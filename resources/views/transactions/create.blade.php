{{-- Create Transaction Modal --}}
<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header custom-header">
                <h5 class="modal-title">Create Transaction</h5>
                <button type="button" class="btn btn-sm border-0 bg-transparent p-1" data-bs-dismiss="modal">
                    <i class="fas fa-times fa-lg text-secondary"></i>
                </button>
            </div>

            <div class="modal-body custom-body">
                <form id="transactionCreateForm" action="{{ route('transactions.store') }}" method="post">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Account</label>
                        <select id="account_id" name="account_id"
                            class="form-select @error('account_id') is-invalid @enderror">
                            <option value="">-- Select account --</option>
                            @foreach (\App\Models\Account::all() as $d)
                                <option value="{{ $d->id }}" {{ old('account_id') == $d->id ? 'selected' : '' }}
                                    {{ old('account_id') ? '' : ($d->name == 'BOB' ? 'selected' : '') }}>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input id="amount" type="number" step="0.01" name="amount" value="{{ old('amount') }}"
                            class="form-control @error('amount') is-invalid @enderror" placeholder="1000.00">
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Credit / Debit</label>
                        <select id="credit_debit" name="credit_debit"
                            class="form-select @error('credit_debit') is-invalid @enderror">
                            <option value="">-- Select --</option>
                            <option value="Debit" {{ old('credit_debit') == 'Debit' ? 'selected' : '' }}
                                {{ old('credit_debit') ? '' : 'selected' }}>Debit</option>
                            <option value="Credit" {{ old('credit_debit') == 'Credit' ? 'selected' : '' }}>Credit
                            </option>
                        </select>
                        @error('credit_debit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input id="category" type="text" name="category" value="{{ old('category') }}"
                            class="form-control @error('category') is-invalid @enderror" placeholder="e.g. travel">
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input id="description" type="text" name="description" value="{{ old('description') }}"
                            class="form-control @error('description') is-invalid @enderror" placeholder="optional">
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction Date</label>
                        <input id="transaction_date" type="date" name="transaction_date"
                            value="{{ old('transaction_date') }}"
                            class="form-control @error('transaction_date') is-invalid @enderror" min="1900-01-01"
                            max="2099-12-31">
                        @error('transaction_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button id="createTxBtn" type="submit" class="btn btn-primary w-100 mt-2">
                        <span id="createTxSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                        <span id="createTxText"><i class="fas fa-check me-1"></i> Add Transaction</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('createTransactionModal')?.addEventListener('shown.bs.modal', function() {
        this.querySelector('select[name="account_id"]').focus();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('transactionCreateForm');
        if (!form) return;
        const btn = document.getElementById('createTxBtn');
        const spinner = document.getElementById('createTxSpinner');
        const txt = document.getElementById('createTxText');

        const validators = {
            account_id: {
                test: v => v !== '' && v !== null,
                messageEmpty: 'Please select an account'
            },
            amount: {
                test: v => v !== '' && !isNaN(v) && Number(v) >= 0.01,
                messageEmpty: 'Please enter amount',
                messageInvalid: 'Invalid amount'
            },
            credit_debit: {
                test: v => v === 'Debit' || v === 'Credit',
                messageEmpty: 'Choose Credit or Debit'
            },
            category: {
                test: v => v.trim().length > 0,
                messageEmpty: 'Please enter category'
            },
            transaction_date: {
                test: v => v !== '',
                messageEmpty: 'Please choose transaction date'
            }
        };

        function showError(el, msg) {
            el.classList.add('is-invalid');
            let fb = el.parentElement.querySelector('.invalid-feedback');
            if (!fb) {
                fb = document.createElement('div');
                fb.className = 'invalid-feedback';
                el.parentElement.appendChild(fb);
            }
            fb.textContent = msg;
            fb.classList.remove('d-none');
        }

        function clearError(el) {
            el.classList.remove('is-invalid');
            const fb = el.parentElement.querySelector('.invalid-feedback');
            if (fb) {
                fb.textContent = '';
                fb.classList.add('d-none');
            }
        }

        function validateField(name) {
            const el = form.querySelector('[name="' + name + '"]');
            if (!el) return true;
            const val = el.value;
            const rule = validators[name];
            if (!rule) return true;
            if (val === '' || val === null || (typeof val === 'string' && val.trim() === '')) {
                showError(el, rule.messageEmpty);
                return false;
            }
            if (rule.messageInvalid && !rule.test(val)) {
                showError(el, rule.messageInvalid);
                return false;
            }
            clearError(el);
            return true;
        }

        Object.keys(validators).forEach(field => {
            const el = form.querySelector('[name="' + field + '"]');
            if (!el) return;
            const ev = (el.tagName.toLowerCase() === 'select' || el.type === 'date') ? 'change' :
                'input';
            el.addEventListener(ev, () => validateField(field));
            el.addEventListener('blur', () => validateField(field));
        });

        form.addEventListener('submit', function(e) {
            let ok = true;
            Object.keys(validators).forEach(k => {
                if (!validateField(k)) ok = false;
            });
            if (!ok) {
                e.preventDefault();
                btn.disabled = false;
                spinner.classList.add('d-none');
                txt.innerHTML = '<i class="fas fa-check me-1"></i> Add Transaction';
                return false;
            }
            btn.disabled = true;
            spinner.classList.remove('d-none');
            txt.textContent = ' Saving...';
        });
    });
</script>
