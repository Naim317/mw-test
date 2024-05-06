@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="container">
                    <h2 class="text-center mb-3">Deposited Transactions</h2>
                    <table class="table" id="deposit-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deposits as $deposit)
                                <tr>
                                    <td>{{ $deposit->date }}</td>
                                    <td>${{ $deposit->amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <a href="{{ route('transaction.index') }}" class="btn btn-secondary">&lArr; Back to Dashboard</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="container">
                    <h2 class="text-center">Deposit Funds</h2>
                    <form method="POST" action="{{ route('transaction.deposit') }}">
                        @csrf
                        <div class="form-group">
                            <label for="amount">Amount to Deposit</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-success mt-2">&dArr;Deposit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#deposit-table').DataTable({
                paging: true,
            });
        });
    </script>
@endsection

