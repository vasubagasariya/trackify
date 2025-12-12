<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Services\BalanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ensure balances updated
        BalanceService::updateCurrentBalance();

        // get ids for Cash and BOB only
        $cashBobAccountIds = Account::whereIn('name', ['Cash', 'BOB'])->pluck('id')->toArray();

        // totalExpense limited to Cash + BOB (Debit)
        $totalExpense = (float) Transaction::where('credit_debit', 'Debit')
            ->whereIn('account_id', $cashBobAccountIds)
            ->sum('amount');

        // opening balance sum for BOB + Cash
        $bobCashOpeningBalance = (float) Account::whereIn('name', ['BOB', 'Cash'])->sum('opening_balance');

        // Total current balance only for BOB and UCO (combined)
        $totalCurrentBalance = (float) Account::whereIn('name', ['BOB', 'Cash'])->sum('current_balance');

        // totals
        $totalTransfers = Transfer::count();
        $totalIncome = (float) Transaction::where('credit_debit', 'Credit')
            ->whereIn('account_id', $cashBobAccountIds)
            ->sum('amount');

        // recent items
        $recentTransactions = Transaction::with('account')->latest('transaction_date')->limit(6)->get();
        $recentTransfers = Transfer::with(['fromAccount', 'toAccount'])->latest('transfer_date')->limit(6)->get();

        // build month list from transactions (distinct YYYY-MM)
        $months = Transaction::selectRaw("DATE_FORMAT(transaction_date, '%Y-%m') as ym, DATE_FORMAT(transaction_date, '%M %Y') as label")
            ->groupBy('ym', 'label')
            ->orderBy('ym', 'desc')
            ->get();

        // limit to latest 12 months to avoid very long list
        $months = $months->take(12);

        $monthList = $months->mapWithKeys(function ($m) {
            return [$m->ym => $m->label];
        })->toArray();

        $selectedMonth = $request->get('month') ?: (count($monthList) ? array_key_first($monthList) : null);

        // Prepare pie chart data BUT only for Cash + BOB accounts (filter by account_id)
        $monthlyPieData = [];
        foreach ($monthList as $ym => $label) {
            try {
                $start = Carbon::createFromFormat('Y-m', $ym)->startOfMonth()->toDateString();
                $end = Carbon::createFromFormat('Y-m', $ym)->endOfMonth()->toDateString();
            } catch (\Exception $e) {
                // invalid format, skip safely
                $monthlyPieData[$ym] = [];
                continue;
            }

            // aggregate only Debit transactions for Cash and BOB accounts, grouped by category
            $categoryTotals = Transaction::where('credit_debit', 'Debit')
                ->whereIn('account_id', $cashBobAccountIds)
                ->whereBetween('transaction_date', [$start, $end])
                ->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->orderByDesc('total')
                ->get()
                ->pluck('total', 'category')
                ->toArray();

            // ensure numeric totals
            $categoryTotals = array_map(function ($v) {
                return (float) $v;
            }, $categoryTotals);

            $monthlyPieData[$ym] = $categoryTotals;
        }

        // Chart data: last 6 months income vs expense (by transaction_date) - only Cash + BOB
        $lastTransaction = Transaction::orderBy('transaction_date', 'desc')->first();

        if ($lastTransaction) {
            $lastMonth = Carbon::parse($lastTransaction->transaction_date)->startOfMonth();
            $startMonth = $lastMonth->copy()->subMonths(12);
        } else {
            $startMonth = Carbon::now()->startOfMonth()->subMonths(12);
        }

        $labels = [];
        $incomeData = [];
        $expenseData = [];
        for ($i = 0; $i <= 12; $i++) {
            $month = $startMonth->copy()->addMonths($i);
            // return $month;
            $labels[] = $month->format('M Y');

            $start = $month->copy()->startOfMonth()->toDateString();
            $end = $month->copy()->endOfMonth()->toDateString();

            $income = Transaction::where('credit_debit', 'Credit')
                ->whereIn('account_id', $cashBobAccountIds)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            $expense = Transaction::where('credit_debit', 'Debit')
                ->whereIn('account_id', $cashBobAccountIds)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            $incomeData[] = (float) $income;
            $expenseData[] = (float) $expense;
        }

        // --- RNSB specific data ---
        $rnsbAccount = Account::where('name', 'RNSB')->first();
        if ($rnsbAccount) {
            $rnsbId = $rnsbAccount->id;
            $rnsbCurrentBalance = (float) $rnsbAccount->current_balance;
            $rnsbExpense = (float) Transaction::where('credit_debit', 'Debit')
                ->where('account_id', $rnsbId)
                ->sum('amount');
            $rnsbIncome = (float) Transaction::where('credit_debit', 'Credit')
                ->where('account_id', $rnsbId)
                ->sum('amount');

        } else {
            $rnsbId = null;
            $rnsbCurrentBalance = 0.0;
            $rnsbExpense = 0.0;
            $rnsbIncome = 0.0;
        }

        // ------- BOB DETAILS -------
        $bob = Account::where('name', 'BOB')->first();
        if ($bob) {
            $bobId = $bob->id;
            $bobCurrentBalance = (float) $bob->current_balance;
            $bobOpeningBalance = (float) $bob->opening_balance;
            $bobExpense = (float) Transaction::where('credit_debit', 'Debit')
                ->where('account_id', $bobId)
                ->sum('amount');
            $bobIncome = (float) Transaction::where('credit_debit', 'Credit')
                ->where('account_id', $bobId)
                ->sum('amount');
        } else {
            $bobCurrentBalance = 0.0;
            $bobOpeningBalance = 0.0;
            $bobExpense = 0.0;
        }

        // ------- CASH DETAILS -------
        $cash = Account::where('name', 'Cash')->first();
        if ($cash) {
            $cashId = $cash->id;
            $cashCurrentBalance = (float) $cash->current_balance;
            $cashOpeningBalance = (float) $cash->opening_balance;
            $cashExpense = (float) Transaction::where('credit_debit', 'Debit')
                ->where('account_id', $cashId)
                ->sum('amount');
            $cashIncome = (float) Transaction::where('credit_debit', 'Credit')
                ->where('account_id', $cashId)
                ->sum('amount');
        } else {
            $cashCurrentBalance = 0.0;
            $cashOpeningBalance = 0.0;
            $cashExpense = 0.0;
        }

        return view('pages.dashboard', compact(
            'totalTransfers',
            'totalIncome',
            'totalExpense',
            'totalCurrentBalance',
            'recentTransactions',
            'recentTransfers',
            'labels',
            'incomeData',
            'expenseData',
            'monthList',
            'selectedMonth',
            'monthlyPieData',
            'rnsbCurrentBalance',
            'rnsbExpense',
            'rnsbIncome',
            'bobIncome',
            'cashIncome',
            'bobCashOpeningBalance',
            'bobCurrentBalance',
            'bobOpeningBalance',
            'bobExpense',
            'cashCurrentBalance',
            'cashOpeningBalance',
            'cashExpense'
        ));
    }
}
