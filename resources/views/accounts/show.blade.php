@extends('layouts.master')
@section('title', 'Accounts')

@section('content')
    <div class="container-fluid">
        {{-- index file or table  --}}
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title">Accounts</h3>
                <div class="card-tools ml-auto">
                    <a class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAccountModal">
                        <i class="fas fa-plus-circle me-1"></i> Create New Account
                    </a>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped table-valign-middle">
                    <thead class="bg-dark">
                        <tr>
                            <th style="width:60px">ID</th>
                            <th>Name</th>
                            <th style="width:140px">Type</th>
                            <th style="width:150px">Opening Balance</th>
                            <th style="width:120px">Expense</th>
                            <th style="width:160px">Current Balance</th>
                            <th style="width:140px">Opening Date</th>
                            <th style="width:140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accounts as $d)
                            <tr>
                                <td>{{ $d->id }}</td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <span class="avatar circle bg-secondary text-white"
                                                style="width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%; margin-right:5px;">
                                                {{ strtoupper(substr($d->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>{{ $d->name }}</strong>
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if (strtolower($d->type) == 'cash' || strtolower($d->type) == 'cashbook')
                                        <span class="badge bg-success">{{ $d->type }}</span>
                                    @elseif(strtolower($d->type) == 'bank')
                                        <span class="badge bg-info">{{ $d->type }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $d->type }}</span>
                                    @endif
                                </td>

                                <td>₹ {{ number_format($d->opening_balance, 2) }}</td>
                                <td><b>₹ {{ number_format($d->expence ?? 0, 2) }}</b></td>
                                <td>
                                    <b><span class="fw-bold">₹ {{ number_format($d->current_balance ?? 0, 2) }}</span></b>
                                    @if (isset($d->current_balance))
                                        @if ($d->current_balance < 0)
                                            <small class="text-danger d-block">Overdrawn</small>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $d->opening_date }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Actions">
                                        <!-- Replace existing edit link with this -->
                                        <div class="action_butttons">

                                            <button type="button" class="btn btn-sm btn-warning edit-account-btn"
                                                title="Edit" data-bs-toggle="modal" data-bs-target="#editAccountModal"
                                                data-action="{{ route('accounts.update', $d->name) }}"
                                                data-name="{{ $d->name }}" data-type="{{ $d->type }}"
                                                data-opening_balance="{{ $d->opening_balance }}"
                                                data-opening_date="{{ $d->opening_date }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>

                                        <div class="action_butttons">
                                            <form action="{{ route('accounts.delete', $d->name) }}" method="POST"
                                                class="d-inline-block delete-form" data-name="{{ $d->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No accounts found. <a href="{{ route('accounts.create') }}">Create one</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                <div class="float-start text-muted">
                    Showing {{ $accounts->count() }} account(s)
                </div>
                <div class="float-end">
                </div>
            </div>
        </div>

        {{-- create modal --}}
        @include('accounts.create')

        {{-- Update modal --}}
        @include('accounts.edit')

        {{-- Delete script --}}
        @include('layouts.delete')
    </div>

@endsection
