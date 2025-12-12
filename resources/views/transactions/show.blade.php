@extends('layouts.master')
@section('title', 'Transactions')

@section('content')
    <div class="container-fluid">

        <div class="card">

            <div class="card-header d-flex align-items-center">
                <h3 class="card-title">Transactions</h3>

                <div class="card-tools ml-auto">
                    <!-- Open create modal (instead of going to separate page) -->
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTransactionModal">
                        <i class="fas fa-plus-circle me-1"></i> Create New Transaction
                    </button>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped table-valign-middle">
                    <thead class="bg-dark">
                        <tr>
                            {{-- helper to build sort link --}}
                            @php
                                // current request params
                                $currentSort = request()->get('sort', 'transaction_date');
                                $currentDir =
                                    strtolower(request()->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

                                // helper closure to render icon
                                $sortIcon = function ($col) use ($currentSort, $currentDir) {
                                    if ($currentSort === $col) {
                                        return $currentDir === 'asc'
                                            ? ' <i class="fas fa-caret-up"></i>'
                                            : ' <i class="fas fa-caret-down"></i>';
                                    }
                                    return '';
                                };

                                // helper to compute next direction
                                $nextDir = function ($col) use ($currentSort, $currentDir) {
                                    if ($currentSort === $col) {
                                        return $currentDir === 'asc' ? 'desc' : 'asc';
                                    }
                                    return 'desc'; // default when switching column -> show newest first (desc)
                                };
                            @endphp

                            <th>
                                <a href="{{ route('transactions.show', array_merge(request()->except(['page']), ['sort' => 'id', 'direction' => $nextDir('id')])) }}"
                                    class="text-white text-decoration-none">
                                    ID{!! $sortIcon('id') !!}
                                </a>
                            </th>

                            <th>
                                <a href="{{ route('transactions.show', array_merge(request()->except(['page']), ['sort' => 'account', 'direction' => $nextDir('account')])) }}"
                                    class="text-white text-decoration-none">
                                    Account{!! $sortIcon('account') !!}
                                </a>
                            </th>

                            <th>
                                <a href="{{ route('transactions.show', array_merge(request()->except(['page']), ['sort' => 'amount', 'direction' => $nextDir('amount')])) }}"
                                    class="text-white text-decoration-none">
                                    Amount{!! $sortIcon('amount') !!}
                                </a>
                            </th>

                            <th>
                                <a href="{{ route('transactions.show', array_merge(request()->except(['page']), ['sort' => 'type', 'direction' => $nextDir('type')])) }}"
                                    class="text-white text-decoration-none">
                                    Type{!! $sortIcon('type') !!}
                                </a>
                            </th>

                            <th>
                                <a href="{{ route('transactions.show', array_merge(request()->except(['page']), ['sort' => 'category', 'direction' => $nextDir('category')])) }}"
                                    class="text-white text-decoration-none">
                                    Category{!! $sortIcon('category') !!}
                                </a>
                            </th>

                            <th>
                                <a href="{{ route('transactions.show', array_merge(request()->except(['page']), ['sort' => 'description', 'direction' => $nextDir('description')])) }}"
                                    class="text-white text-decoration-none">
                                    Description{!! $sortIcon('description') !!}
                                </a>
                            </th>

                            <th>
                                <a href="{{ route('transactions.show', array_merge(request()->except(['page']), ['sort' => 'transaction_date', 'direction' => $nextDir('transaction_date')])) }}"
                                    class="text-white text-decoration-none">
                                    Transaction Date{!! $sortIcon('transaction_date') !!}
                                </a>
                            </th>

                            <th>
                                <a href="{{ route('transactions.show', array_merge(request()->except(['page']), ['sort' => 'remaining_balance', 'direction' => $nextDir('remaining_balance')])) }}"
                                    class="text-white text-decoration-none">
                                    Remaining Balance{!! $sortIcon('remaining_balance') !!}
                                </a>
                            </th>

                            <th style="width:120px">Actions</th>
                        </tr>
                    </thead>


                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>

                                <td>
                                    <strong>{{ $transaction->account->name }}</strong>
                                    <div class="text-muted small">Acc ID: {{ $transaction->account->id }}</div>
                                </td>

                                <td>
                                    <span class="fw-bold">
                                        ₹ {{ number_format($transaction->amount, 2) }}
                                    </span>
                                </td>

                                <td style="display: flex; gap: 10px;">
                                    <span class="action_buton">
                                        @if ($transaction->credit_debit === 'Credit')
                                            <span class="badge bg-success">Credit</span>
                                        @else
                                            <span class="badge bg-danger">Debit</span>
                                        @endif
                                    </span>

                                    <span class="action_buton">
                                        @if ($transaction->account->name == 'Cash')
                                            <span class="badge bg-success">Cash</span>
                                        @else
                                            <span class="badge bg-secondary">Online</span>
                                        @endif
                                    </span>
                                </td>

                                <td>{{ $transaction->category }}</td>

                                <td>{{ \Illuminate\Support\Str::limit($transaction->description, 40) }}</td>

                                <td>{{ $transaction->transaction_date }}</td>

                                <td>₹ {{ number_format($transaction->remaining_balance, 2) }}</td>

                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- EDIT: open edit modal and pass data via data- attributes -->
                                        <button type="button" class="btn btn-sm btn-warning edit-transaction-btn"
                                            title="Edit" data-bs-toggle="modal" data-bs-target="#editTransactionModal"
                                            data-action="{{ route('transactions.update', $transaction->id) }}"
                                            data-id="{{ $transaction->id }}"
                                            data-account_id="{{ $transaction->account_id }}"
                                            data-amount="{{ $transaction->amount }}"
                                            data-credit_debit="{{ $transaction->credit_debit }}"
                                            data-category="{{ $transaction->category }}"
                                            data-description="{{ $transaction->description }}"
                                            data-transaction_date="{{ $transaction->transaction_date }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('transactions.delete', $transaction->id) }}" method="POST"
                                            class="d-inline-block delete-form" data-name="{{ $transaction->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- include create & edit modal partials -->
    @include('layouts.pagination', ['items' => $transactions])
    @include('transactions.create')
    @include('transactions.edit')
    @include('layouts.delete')
@endsection
