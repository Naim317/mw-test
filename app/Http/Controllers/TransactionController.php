<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // Fetch all transactions and current balance for the authenticated user
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $currentBalance = $user->balance;
        $accountType = $user->account_type;

        return view('transaction.index', compact('transactions', 'currentBalance', 'accountType'));
    }

    public function showDeposits()
    {
        // Fetch all deposited transactions for the authenticated user
        $user = auth()->user();
        $deposits = Transaction::where('user_id', $user->id)
            ->where('transaction_type', 'Deposit')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('transaction.deposit', compact('deposits'));
    }

    public function deposit(Request $request)
    {
        // Validate the deposit form input
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Update the user's balance by adding the deposited amount
        $user->balance += $request->amount;
        $user->save();

        // Create a deposit transaction
        try{
            Transaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'Deposit',
                'amount' => $request->amount,
                'fee' => $request->fee,
            ]);
    
            return redirect()->route('transaction.index')->with('success', 'Amount Deposit successfull.');
        }
        catch(\Exception $e) {
            // $msg = $e->getMessage();
            return  redirect()->route('transaction.index')->with('fail', "Amount Deposit Failed! Please try again"); 
        } 
    }

    public function showWithdrawals()
    {
        // Fetch all withdrawal transactions for the authenticated user
        $user = auth()->user();
        $currentBalance = $user->balance;
        $withdrawals = Transaction::where('user_id', $user->id)
            ->where('transaction_type', 'Withdrawal')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('transaction.withdrawal', compact('withdrawals', 'currentBalance'));
    }

    public function withdrawal(Request $request)
    {
        // Validate the withdrawal form input
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Calculate the withdrawal fee based on account type
        $accountType = $user->account_type;
        $withdrawalAmount = $request->amount;
        $fee = 0.0;

        // Calculate the total withdrawal amount in the current month
        $currentMonth = now()->format('Y-m');
        $totalWithdrawalThisMonth = Transaction::where('user_id', $user->id)
        ->where('transaction_type', 'Withdrawal')
        ->where('date', 'LIKE', $currentMonth . '%')
        ->sum('amount');
        // dd($totalWithdrawalThisMonth);

        // Check if it's a Friday and Check if the withdrawal amount is less than 1000 and totalWithdrawalThisMonth is less than 5000 then no fee will be added
        if (now()->isFriday() || $withdrawalAmount < 1000 || $totalWithdrawalThisMonth <= 5000 && $withdrawalAmount <= 5000) {
            if($totalWithdrawalThisMonth+$withdrawalAmount <= 5000){
                $finalWithdrawalAmount = $withdrawalAmount;
            }
            else{
                $ExtraWithdrawlWithFees = (($totalWithdrawalThisMonth + $withdrawalAmount) - 5000);
                $fee = $ExtraWithdrawlWithFees * ($accountType === 'Individual' ? 0.015 : 0.025);
                $withdrawalAmount += $fee;
                $finalWithdrawalAmount = $withdrawalAmount;
            }    
        }
        // Check if the user is a Business account and the total withdrawal exceeds 50K then decrease fees
        elseif ($accountType === 'Business' && $totalWithdrawalThisMonth+$withdrawalAmount > 50000){
                $fee = $withdrawalAmount * 0.015;
                $withdrawalAmount += $fee;
                $finalWithdrawalAmount = $withdrawalAmount;
        }

        
        // For Normal case
        else {
            $fee = $withdrawalAmount * ($accountType === 'Individual' ? 0.015 : 0.025);
            $withdrawalAmount += $fee;
            $finalWithdrawalAmount = $withdrawalAmount;
        }

        // Check if the user has sufficient balance for the withdrawal
        if ($user->balance < $finalWithdrawalAmount) {
            return redirect()->route('transaction.index')->with('error', 'Insufficient balance.');
        }

        // Update the user's balance by deducting the withdrawn amount and fee
        $user->balance -= $finalWithdrawalAmount;
        $user->save();

        // Create a withdrawal transaction with the fee
        Transaction::create([
            'user_id' => $user->id,
            'transaction_type' => 'Withdrawal',
            'amount' => $request->amount,
            'fee' => $fee,
        ]);

        return redirect()->route('transaction.index')->with([
            'success' => 'Withdrawal successful.',
            'fee' => $fee,
            'totalWithdrawalThisMonth' => $totalWithdrawalThisMonth,
        ]);
    }

}
