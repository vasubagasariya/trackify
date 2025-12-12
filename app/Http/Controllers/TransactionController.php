<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Services\BalanceService;
use RealRashid\SweetAlert\Facades\Alert;

class TransactionController extends Controller
{
    // display transactions page
    // app/Http/Controllers/TransactionController.php

    function show(Request $request)
    {
        // allowed sort columns (map friendly names to DB columns)
        $allowed = [
            'id' => 'transactions.id',
            'account' => 'accounts.name',        // special: requires join
            'amount' => 'transactions.amount',
            'type' => 'transactions.credit_debit',
            'category' => 'transactions.category',
            'description' => 'transactions.description',
            'transaction_date' => 'transactions.transaction_date',
            'remaining_balance' => 'transactions.remaining_balance'
        ];

        // get requested sort & direction (default: transaction_date desc)
        $sort = $request->get('sort', 'transaction_date');
        $dir = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        // fallback if invalid
        if (!array_key_exists($sort, $allowed)) {
            $sort = 'transaction_date';
        }
        $sortColumn = $allowed[$sort];

        // base query: join accounts when sorting by account name OR always join to allow account name search/sort
        $query = Transaction::query()->select('transactions.*')->with('account');

        // if sorting by account name, join accounts table
        if ($sort === 'account') {
            $query->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                ->orderBy('accounts.name', $dir)
                ->orderBy('transactions.id', 'desc'); // stable secondary order
            // since we joined, fetch transactions with account relationship: re-load -> not necessary here because select transactions.* used and with('account') will eager load
        } else {
            $query->orderBy($sortColumn, $dir)
                ->orderBy('transactions.id', 'desc'); // stable secondary order
        }

        // optional: pagination (recommended) e.g. ->paginate(25)
        $transactions = $query->paginate(10)->appends($request->except('page'));

        return view('transactions.show', compact('transactions'));
    }


    // store
    function store(Request $req)
    {
        $req->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'credit_debit' => 'required|in:Debit,Credit',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date|date_format:Y-m-d'
        ], [
            'account_id.required' => 'Please select an account',
            'account_id.exists' => 'Selected account not found',
            'amount.required' => 'Please enter amount',
            'amount.numeric' => 'Amount must be numeric',
            'amount.min' => 'Amount must be at least 0.01',
            'credit_debit.required' => 'Please choose Credit or Debit',
            'credit_debit.in' => 'Invalid transaction type',
            'category.required' => 'Please enter category',
            'category.string' => 'Category must be text',
            'description.string' => 'Description must be text',
            'transaction_date.required' => 'Please choose transaction date',
            'transaction_date.date' => 'Invalid date',
            'transaction_date.date_format' => 'Invalid date format (use YYYY-MM-DD)'
        ]);

        $account = Account::findOrFail($req->account_id);

        $lastTransaction = Transaction::where('account_id', $req->account_id)->latest('id')->first();
        $balance = $lastTransaction ? $lastTransaction->remaining_balance : $account->opening_balance;

        if ($req->input('credit_debit') == 'Debit') {
            $remaining = $balance - $req->input('amount');
        } else {
            $remaining = $balance + $req->input('amount');
        }

        Transaction::create([
            'account_id' => $req->account_id,
            'amount' => $req->amount,
            'credit_debit' => $req->input('credit_debit'),
            'category' => $req->category,
            'description' => $req->description,
            'transaction_date' => $req->transaction_date,
            'remaining_balance' => $remaining
        ]);

        BalanceService::updateCurrentBalance();
        Alert::success('Success!', 'Transaction created successfully!');

        return redirect()->route('transactions.show')->with('success', 'Transaction added');
    }

    // update
    function update(Request $req, $id)
    {
        $req->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'credit_debit' => 'required|in:Debit,Credit',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date|date_format:Y-m-d'
        ], [
            // same messages as store...
            'account_id.required' => 'Please select an account',
            'account_id.exists' => 'Selected account not found',
            'amount.required' => 'Please enter amount',
            'amount.numeric' => 'Amount must be numeric',
            'amount.min' => 'Amount must be at least 0.01',
            'credit_debit.required' => 'Please choose Credit or Debit',
            'credit_debit.in' => 'Invalid transaction type',
            'category.required' => 'Please enter category',
            'transaction_date.required' => 'Please choose transaction date',
            'transaction_date.date_format' => 'Invalid date format (use YYYY-MM-DD)'
        ]);

        $transaction = Transaction::findOrFail($id);

        $oldAccountId = $transaction->account_id;

        // update transaction
        $transaction->account_id = $req->account_id;
        $transaction->amount = $req->amount;
        $transaction->credit_debit = $req->credit_debit;
        $transaction->category = $req->category;
        $transaction->description = $req->description;
        $transaction->transaction_date = $req->transaction_date;
        $transaction->save();

        // Recalculate remaining_balance for old account transactions
        $account = Account::findOrFail($oldAccountId);
        $balance = $account->opening_balance;
        $transactions = Transaction::where('account_id', $oldAccountId)->orderBy('id')->get();
        foreach ($transactions as $t) {
            if ($t->credit_debit == 'Debit')
                $balance -= $t->amount;
            else
                $balance += $t->amount;
            $t->remaining_balance = $balance;
            $t->save();
        }

        // If account changed, recalc for new account as well
        if ($oldAccountId != $req->account_id) {
            $account = Account::findOrFail($req->account_id);
            $balance = $account->opening_balance;
            $transactions = Transaction::where('account_id', $req->account_id)->orderBy('id')->get();
            foreach ($transactions as $t) {
                if ($t->credit_debit == 'Debit')
                    $balance -= $t->amount;
                else
                    $balance += $t->amount;
                $t->remaining_balance = $balance;
                $t->save();
            }
        }

        BalanceService::updateCurrentBalance();
        Alert::success('Success!', 'Transaction updated successfully!');
        return redirect()->route('transactions.show')->with('success', 'Transaction updated');
    }

    // delete
    function delete($id)
    {
        Transaction::where('id', $id)->delete();
        BalanceService::updateCurrentBalance();
        Alert::success('Deleted!', 'Transaction deleted successfully.');
        return redirect()->route('transactions.show');
    }
}
