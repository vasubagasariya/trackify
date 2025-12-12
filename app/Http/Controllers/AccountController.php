<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\BalanceService;
use RealRashid\SweetAlert\Facades\Alert;


class AccountController extends Controller
{
    // display accounts page
    function show()
    {
        BalanceService::updateCurrentBalance();
        $accounts = Account::all();
        return view('accounts.show', compact('accounts'));
    }

    // create page stores
    function store(Request $req)
    {

        $req->validate([
            'name' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'type' => 'required',
            'opening_balance' => 'required | numeric',
            'opening_date' => 'required'
        ], [
            'name.required' => "Please Enter you account name",
            'name.regex' => 'Acoount name shoul be contain only alphabetical values',
            'type.required' => 'type of account is coumpalasary',
            'opening_balance.required' => 'Please Enter Opening Balance',
            'opening_balance.numeric' => 'Balance should contain only numbers',
            'opening_date.required' => 'please choose opening date of your account'
        ]);
        Account::create($req->all());
        if ($req->wantsJson() || $req->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
                'redirect' => route('accounts.show') // अगर चाहो redirect के लिए
            ]);
        }
        Alert::success('Success!', 'Account created successfully!');
        return redirect()->route('accounts.show');
    }

    //update data
    function update(Request $req, $name)
    {
        $req->validate([
            'name' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'type' => 'required',
            'opening_balance' => 'required|numeric',
            'opening_date' => 'required|date_format:Y-m-d'
        ], [
            'name.required' => "Please Enter your account name",
            'name.regex' => 'Account name should contain only alphabetical values',
            'type.required' => 'Type of account is compulsory',
            'opening_balance.required' => 'Please Enter Opening Balance',
            'opening_balance.numeric' => 'Balance should contain only numbers',
            'opening_date.required' => 'Please choose opening date of your account',
            'opening_date.date_format' => 'Invalid date format (use YYYY-MM-DD)'
        ]);

        $account = Account::where('name', $name)->firstOrFail();
        $account->name = $req->name;
        $account->type = $req->type;
        $account->opening_balance = $req->opening_balance;
        $account->opening_date = $req->opening_date;

        $account->save();
        // Account::update();
        Alert::success('Success!', 'Account updated successfully!');
        return redirect()->route('accounts.show');
    }

    //delete
    function delete($name)
    {
        Account::where('name', $name)->delete();
        Alert::success('Deleted!', 'Account deleted successfully.');
        return redirect()->route('accounts.show');
    }
}
