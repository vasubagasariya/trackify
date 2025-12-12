{{-- Edit Transaction Modal --}}
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header custom-header">
                <h5 class="modal-title">Update Transaction</h5>
                <button type="button" class="btn btn-sm border-0 bg-transparent p-1" data-bs-dismiss="modal">
                    <i class="fas fa-times fa-lg text-secondary"></i>
                </button>
            </div>

            <div class="modal-body custom-body">
                <!-- form action will be set dynamically from the edit button data-action -->
                <form id="transactionEditForm" action="#" method="post">
                    @csrf
                    @method('POST') {{-- your route uses POST for update in routes file; change if needed --}}

                    <div class="mb-3">
                        <label class="form-label">Account</label>
                        <select id="edit_account_id" name="account_id" class="form-select">
                            <option value="">-- Select account --</option>
                            @foreach (\App\Models\Account::all() as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input id="edit_amount" type="number" step="0.01" name="amount" class="form-control"
                            placeholder="1000.00">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Credit / Debit</label>
                        <select id="edit_credit_debit" name="credit_debit" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="Debit">Debit</option>
                            <option value="Credit">Credit</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input id="edit_category" type="text" name="category" class="form-control"
                            placeholder="e.g. travel">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input id="edit_description" type="text" name="description" class="form-control"
                            placeholder="optional">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction Date</label>
                        <input id="edit_transaction_date" type="date" name="transaction_date" class="form-control"
                            min="1900-01-01" max="2099-12-31">
                    </div>

                    <button id="editTxBtn" type="submit" class="btn btn-primary w-100 mt-2">
                        <span id="editTxSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                        <span id="editTxText"><i class="fas fa-save me-1"></i> Update</span>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModalEl = document.getElementById('editTransactionModal');
        const editForm = document.getElementById('transactionEditForm');

        // When any edit button is clicked, bootstrap will open modal; we hook into 'show.bs.modal' to populate
        document.querySelectorAll('.edit-transaction-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // fill inputs from data-* attributes
                const action = this.dataset.action || '#';
                const account_id = this.dataset.account_id || '';
                const amount = this.dataset.amount || '';
                const credit_debit = this.dataset.credit_debit || '';
                const category = this.dataset.category || '';
                const description = this.dataset.description || '';
                const transaction_date = this.dataset.transaction_date || '';

                editForm.action = action;
                editForm.querySelector('[name="account_id"]').value = account_id;
                editForm.querySelector('[name="amount"]').value = amount;
                editForm.querySelector('[name="credit_debit"]').value = credit_debit;
                editForm.querySelector('[name="category"]').value = category;
                editForm.querySelector('[name="description"]').value = description;
                editForm.querySelector('[name="transaction_date"]').value = transaction_date;

                // clear previous validation states if any
                editForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                    'is-invalid'));
                editForm.querySelectorAll('.invalid-feedback').forEach(fb => {
                    fb.textContent = '';
                    fb.classList.add('d-none');
                });
            });
        });

        // same client-side validation & spinner logic as Create
        const btn = document.getElementById('editTxBtn');
        const spinner = document.getElementById('editTxSpinner');
        const txt = document.getElementById('editTxText');

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

        function validateField(formEl, name) {
            const el = formEl.querySelector('[name="' + name + '"]');
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

        ['account_id', 'amount', 'credit_debit', 'category', 'transaction_date'].forEach(field => {
            const el = editForm.querySelector('[name="' + field + '"]');
            if (!el) return;
            const ev = (el.tagName.toLowerCase() === 'select' || el.type === 'date') ? 'change' :
                'input';
            el.addEventListener(ev, () => validateField(editForm, field));
            el.addEventListener('blur', () => validateField(editForm, field));
        });

        editForm.addEventListener('submit', function(e) {
            let ok = true;
            Object.keys(validators).forEach(k => {
                if (!validateField(editForm, k)) ok = false;
            });
            if (!ok) {
                e.preventDefault();
                btn.disabled = false;
                spinner.classList.add('d-none');
                txt.innerHTML = '<i class="fas fa-save me-1"></i> Update';
                return false;
            }
            btn.disabled = true;
            spinner.classList.remove('d-none');
            txt.textContent = ' Saving...';
        });
    });
</script>
