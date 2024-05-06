@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="container">
                    <h2 class="text-center mb-3">Withdrawal Transactions</h2>
                    <table class="table" id="withdraw-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($withdrawals as $withdrawal)
                                <tr>
                                    <td>{{ $withdrawal->created_at }}</td>
                                    <td>${{ $withdrawal->amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <a href="{{ route('transaction.index') }}" class="btn btn-secondary">&lArr; Back to Dashboard</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="container">
                    <h2 class="text-center mb-3">Withdraw Funds</h2>
                    <div class="text-center">
                        <button class="btn btn-primary">Current Balance: ${{$currentBalance}}</button>
                    </div>

                    <form method="POST" action="{{ route('transaction.withdrawal') }}">
                        @csrf
                        <div class="form-group">
                            <label for="amount">Amount to Withdraw</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">&uArr;Withdraw</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#withdraw-table').DataTable({
                paging: true,
            });
        });
    </script>
@endsection


