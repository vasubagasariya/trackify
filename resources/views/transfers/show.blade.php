@extends('layouts.master')
@section('title', 'Transfers')

@section('content')
    <div class="container-fluid">

        <div class="card">

            <div class="card-header d-flex align-items-center">
                <h3 class="card-title">Transfers</h3>

                <div class="card-tools ml-auto">
                    <!-- Create modal trigger -->
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTransferModal">
                        <i class="fas fa-exchange-alt me-1"></i> Create New Transfer
                    </button>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped table-valign-middle">
                    <thead class="bg-dark">
                        <tr>
                            <th>ID</th>
                            <th>From Account</th>
                            <th>To Account</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Transfer Date</th>
                            <th style="width:140px">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transfers as $transfer)
                            <tr>
                                <td>{{ $transfer->id }}</td>

                                <td>
                                    <strong>{{ $transfer->fromAccount->name ?? 'N/A' }}</strong>
                                    <div class="text-muted small">ID: {{ $transfer->from_account ?? '---' }}</div>
                                </td>

                                <td>
                                    <strong>{{ $transfer->toAccount->name ?? 'N/A' }}</strong>
                                    <div class="text-muted small">ID: {{ $transfer->to_account ?? '---' }}</div>
                                </td>

                                <td><span class="fw-bold">₹ {{ number_format($transfer->amount, 2) }}</span></td>

                                <td>{{ \Illuminate\Support\Str::limit($transfer->description, 40) }}</td>

                                <td>{{ $transfer->transfer_date }}</td>

                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Edit: open modal and pass data via data-attributes -->
                                        <button type="button" class="btn btn-sm btn-warning edit-transfer-btn"
                                            data-bs-toggle="modal" data-bs-target="#editTransferModal"
                                            data-action="{{ route('transfers.update', $transfer->id) }}"
                                            data-id="{{ $transfer->id }}" data-from_account="{{ $transfer->from_account }}"
                                            data-to_account="{{ $transfer->to_account }}"
                                            data-amount="{{ $transfer->amount }}"
                                            data-description="{{ $transfer->description }}"
                                            data-transfer_date="{{ $transfer->transfer_date }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Delete: SweetAlert -->
                                        <form action="{{ route('transfers.delete', $transfer->id) }}" method="POST"
                                            class="d-inline-block delete-form"
                                            data-name="{{ $transfer->id }}">
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
                                <td colspan="7" class="text-center text-muted py-4">No transfers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-muted">
                Showing {{ $transfers->count() }} transfer(s)
            </div>

        </div>
    </div>

    {{-- include modals (partials) --}}
    @include('transfers.create')
    @include('transfers.edit')
    @include('layouts.delete')
@endsection
