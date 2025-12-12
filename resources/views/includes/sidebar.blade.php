{{-- resources/views/partials/sidebar.blade.php  (या जहां भी तुमने रखा है) --}}
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
        <span class="brand-text font-weight-light">Trackify</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2" aria-label="Main navigation">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- Accounts --}}
                @php
                    $accs = $sidebarAccounts ?? collect();
                @endphp

                <li class="nav-item dropdown nav-item-accounts" id="nav-accounts-dropdown">
                    <a href="{{ route('accounts.show') }}"
                        class="nav-link d-flex justify-content-between align-items-center" id="accountsDropdownToggle"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        <span><i class="nav-icon fas fa-wallet me-1"></i> Accounts</span>
                        <i class="fas fa-angle-down small ms-1"></i>
                    </a>

                    {{-- Dropdown panel (hidden by default) --}}
                    <div class="accounts-dropdown-panel shadow-sm rounded" style="display:none;" role="menu"
                        aria-labelledby="accountsDropdownToggle">
                        <div class="accounts-dropdown-inner p-2">
                            @if ($accs->isEmpty())
                                <div class="text-muted small px-2 py-2">No accounts yet. <a href="javascript:void(0)"
                                        id="openCreateFromDropdown">Create</a></div>
                            @else
                                @foreach ($accs as $acc)
                                    <a href="{{ route('accounts.show') }}#acc-{{ $acc->id }}"
                                        class="d-flex align-items-center accounts-dropdown-item p-2 rounded">
                                        <div class="acc-avatar me-2">
                                            <div class="bg-secondary rounded-circle text-center text-white"
                                                style="width:36px;height:36px;line-height:36px;font-weight:600;">
                                                {{ strtoupper(substr($acc->name, 0, 1)) }}
                                            </div>
                                        </div>

                                        <div class="acc-meta flex-grow-1" style="min-width:0;">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="text-truncate"
                                                    style="max-width:160px; font-weight:600; color:#fff;">
                                                    {{ \Illuminate\Support\Str::limit($acc->name, 26) }}
                                                </div>
                                                <div class="text-end ms-2" style="min-width:100px;">
                                                    <div class="fw-bold">₹
                                                        {{ number_format($acc->current_balance ?? 0, 2) }}</div>
                                                    <small
                                                        class="{{ ($acc->current_balance ?? 0) < 0 ? 'text-danger' : 'text-success' }}">{{ ($acc->current_balance ?? 0) < 0 ? 'Overdrawn' : 'Available' }}</small>
                                                </div>
                                            </div>

                                            <div class="small text-muted mt-1">
                                                Spent: ₹{{ number_format($acc->total_debit ?? 0, 2) }} · Income:
                                                ₹{{ number_format($acc->total_credit ?? 0, 2) }}
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </li>


                {{-- Transactions --}}
                <li class="nav-item">
                    <a href="{{ route('transactions.show') }}"
                        class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>
                            Transactions
                            @if (isset($pendingTransactionsCount) && $pendingTransactionsCount > 0)
                                <span class="right badge badge-danger">{{ $pendingTransactionsCount }}</span>
                            @endif
                        </p>
                    </a>
                </li>

                {{-- Transfers --}}
                <li class="nav-item">
                    <a href="{{ route('transfers.show') }}"
                        class="nav-link {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-paper-plane"></i>
                        <p>Transfers</p>
                    </a>
                </li>

                {{-- Logout --}}
                <li class="nav-item mt-1">
                    <a href="{{ route('logout') }}" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const toggle = document.getElementById('accountsDropdownToggle');
            const panel = document.querySelector('.accounts-dropdown-panel');
            const wrapper = document.getElementById('nav-accounts-dropdown');

            if (!toggle || !panel || !wrapper) return;

            // toggle on click
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
            });

            // hide when clicking outside
            document.addEventListener('click', function(e) {
                if (!wrapper.contains(e.target)) {
                    panel.style.display = 'none';
                }
            });

        });
    </script>

</aside>
