<?php

namespace App\Services;
// use App\Models\Transaction;
use App\Models\Account;
// use App\Models\Transfer;

class BalanceService
{
    public static function updateCurrentBalance(){
        $accounts = Account::with(['transactions','sentTrasfer','recievedTransfer'])->get();
        foreach($accounts as $account){
            $balance =  $account->opening_balance;
            // $transactions = Transaction::where('account_id',$account->id)->get();
            // $transfers = Transfer::where('from_account',$account->id)->get();
            // $transfers_to = Transfer::where('to_account',$account->id)->get();

            foreach($account->transactions as $transaction){
                if($transaction->credit_debit == 'Credit'){
                    $balance = $balance + $transaction->amount;
                }
                else{
                    $balance = $balance - $transaction->amount;
                }
            }
            
            foreach($account->sentTrasfer as $t){
                $balance = $balance - $t->amount;
            }
            foreach($account->recievedTransfer as $t){
                $balance = $balance + $t->amount;
            }
            
            $account->current_balance = $balance;
            $account->expence = $account->opening_balance - $balance;
            $account->save();

        }
        
    }
}
