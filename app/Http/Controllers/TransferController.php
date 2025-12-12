<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transfer;
use App\Services\BalanceService;
use RealRashid\SweetAlert\Facades\Alert;

class TransferController extends Controller
{
    function show(){
        $transfers = Transfer::with(['fromAccount', 'toAccount'])->get();
        $data = Account::all();
        return view('transfers.show',compact('transfers','data'));
    }

    function store(Request $req){
        $req->validate([
            'from_account' => 'required|exists:accounts,id|different:to_account',
            'to_account' => 'required|exists:accounts,id|different:from_account',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'transfer_date' => 'required|date|date_format:Y-m-d'
        ], [
            'from_account.required' => 'Please select source account',
            'from_account.exists' => 'Source account not found',
            'from_account.different' => 'From and To accounts must be different',
            'to_account.required' => 'Please select destination account',
            'to_account.exists' => 'Destination account not found',
            'to_account.different' => 'From and To accounts must be different',
            'amount.required' => 'Please enter amount',
            'amount.numeric' => 'Amount must be numeric',
            'amount.min' => 'Amount must be greater than zero',
            'transfer_date.required' => 'Please choose transfer date',
            'transfer_date.date_format' => 'Invalid date format (use YYYY-MM-DD)'
        ]);

        $from = Account::findOrFail($req->from_account);
        if($from->current_balance < $req->amount){
            return back()->withErrors(['amount' => 'Insufficient balance in source account'])->withInput();
        }

        Transfer::create($req->all());
        BalanceService::updateCurrentBalance();
        Alert::success('Success!', 'Transfer created successfully!');
        return redirect()->route('transfers.show');
    }



    function update(Request $req,$id){
        $req->validate([
            'from_account' => 'required|exists:accounts,id|different:to_account',
            'to_account' => 'required|exists:accounts,id|different:from_account',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'transfer_date' => 'required|date|date_format:Y-m-d'
        ], [
            // same messages as store...
            'from_account.required' => 'Please select source account',
            'from_account.exists' => 'Source account not found',
            'from_account.different' => 'From and To accounts must be different',
            'to_account.required' => 'Please select destination account',
            'to_account.exists' => 'Destination account not found',
            'amount.required' => 'Please enter amount',
            'amount.numeric' => 'Amount must be numeric',
            'amount.min' => 'Amount must be greater than zero',
            'transfer_date.required' => 'Please choose transfer date',
            'transfer_date.date_format' => 'Invalid date format (use YYYY-MM-DD)'
        ]);

        $transfer = Transfer::findOrFail($id);

        // optional: check if new from_account has enough balance when changed
        $fromAccount = Account::findOrFail($req->from_account);
        if($fromAccount->current_balance < $req->amount){
            return back()->withErrors(['amount' => 'Insufficient balance in source account'])->withInput();
        }

        $transfer->from_account = $req->from_account;
        $transfer->to_account = $req->to_account;
        $transfer->amount = $req->amount;
        $transfer->description = $req->description;
        $transfer->transfer_date = $req->transfer_date;
        $transfer->save();

        BalanceService::updateCurrentBalance();
        Alert::success('Success!', 'Transfer updated successfully!');
        return redirect()->route('transfers.show');
    }

    function delete($id){
        Transfer::findOrFail($id)->delete();
        BalanceService::updateCurrentBalance();
        Alert::success('Deleted!', 'Transfer deleted successfully.');
        return redirect()->route('transfers.show');
    }
}
