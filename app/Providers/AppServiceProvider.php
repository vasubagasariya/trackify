<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Account;
use DB;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
         View::composer('*', function ($view) {
        $accounts = DB::table('accounts as a')
            ->leftJoin('transactions as t', 't.account_id', '=', 'a.id')
            ->selectRaw('
                a.id,
                a.name,
                a.type,
                COALESCE(a.current_balance, a.opening_balance) as current_balance,
                a.opening_balance,
                SUM(CASE WHEN t.credit_debit = "Debit" THEN t.amount ELSE 0 END) as total_debit,
                SUM(CASE WHEN t.credit_debit = "Credit" THEN t.amount ELSE 0 END) as total_credit
            ')
            ->groupBy('a.id','a.name','a.type','a.current_balance','a.opening_balance')
            ->orderBy('a.name')
            ->get();

        // send to views
        $view->with('sidebarAccounts', $accounts);
    });
    }
}
