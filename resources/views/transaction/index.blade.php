@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h2 class="text-start">Transaction History</h2>
            <div>
                <btn class="btn btn-success text-end">{{ $accountType }} Account</btn>
                <btn class="btn btn-primary">Current Balance: ${{ $currentBalance }}</btn>
            </div>
        </div>
        
        <div class="my-2 text-center">
            <a href="{{ route('transaction.deposit') }}" class="btn btn-success">&dArr;Deposit</a>
            <a href="{{ route('transaction.withdrawal') }}" class="btn btn-primary">&uArr;Withdraw</a>
        </div>
        <table class="table" id="transactions-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction Type</th>
                    <th>Amount</th>
                    <th>Fee</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->date }}</td>
                        <td>{{ $transaction->transaction_type }}</td>
                        <td>${{ $transaction->amount }}</td>
                        @if($transaction->fee && $transaction->fee != 0)
                        <td>${{ $transaction->fee }}</td>
                        @else
                        <th>&#10060; No Fees</th
                        @endif
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            $('#transactions-table').DataTable({
                paging: true,
            });
        });
    </script>
@endsection
