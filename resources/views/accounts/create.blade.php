{{-- create modal --}}
<div class="modal fade" id="createAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header custom-header">
                <h5 class="modal-title">Create New Account</h5>
                <button type="button" class="btn btn-sm border-0 bg-transparent p-1" data-bs-dismiss="modal">
                    <i class="fas fa-times fa-lg text-secondary"></i>
                </button>

            </div>

            <div class="modal-body custom-body">
                <form action="{{ route('accounts.store') }}" method="POST" id="modalCreateAccountForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" placeholder="Enter name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror">
                            <option value="Cash" {{ old('type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Bank" {{ old('type') == 'Bank' ? 'selected' : '' }}>Bank</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Opening Balance</label>
                        <input type="number" step="0.01" id="opening_balance" name="opening_balance"
                            value="{{ old('opening_balance') }}"
                            class="form-control @error('opening_balance') is-invalid @enderror" placeholder="1000.00">
                        @error('opening_balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Opening Date</label>
                        <input type="date" id="opening_date" name="opening_date" value="{{ old('opening_date') }}"
                            class="form-control @error('opening_date') is-invalid @enderror" min="1900-01-01"
                            max="2099-12-31">
                        @error('opening_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2" id="createBtn">
                        <span id="createSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                        <span id="createText"><i class="fas fa-check me-1"></i> Add Account</span>
                    </button>

                </form>
            </div>

        </div>
    </div>
</div>
<script>
    // Auto focus first input
    document.getElementById('createAccountModal').addEventListener('shown.bs.modal', function() {
        this.querySelector('input[name="name"]').focus();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('modalCreateAccountForm');
        const btn = document.getElementById('createBtn');
        const spinner = document.getElementById('createSpinner');
        const text = document.getElementById('createText');

        // validation rules:
        const validators = {
            name: {
                test: value => value.trim().length > 0 && /^[A-Za-z\s]+$/.test(value.trim()),
                messageEmpty: 'Please Enter you account name',
                messageInvalid: 'Account name should contain only alphabetical values'
            },
            type: {
                test: value => value !== null && value !== '',
                messageEmpty: 'type of account is compulsory'
            },
            opening_balance: {
                test: value => value !== '' && !isNaN(value),
                messageEmpty: 'Please Enter Opening Balance',
                messageInvalid: 'Balance should contain only numbers'
            },
            opening_date: {
                test: value => value !== '',
                messageEmpty: 'please choose opening date of your account'
            }
        };

        // helper: show field error (uses nearest .invalid-feedback sibling or creates one)
        function showError(inputEl, message) {
            inputEl.classList.add('is-invalid');
            // try to find an existing .invalid-feedback in the same form-group
            let fb = inputEl.parentElement.querySelector('.invalid-feedback');
            if (!fb) {
                fb = document.createElement('div');
                fb.className = 'invalid-feedback';
                inputEl.parentElement.appendChild(fb);
            }
            fb.textContent = message;
            fb.classList.remove('d-none');
        }

        function clearError(inputEl) {
            inputEl.classList.remove('is-invalid');
            const fb = inputEl.parentElement.querySelector('.invalid-feedback');
            if (fb) {
                fb.textContent = '';
                fb.classList.add('d-none');
            }
        }

        // validate single field by name -> returns true if valid
        function validateFieldByName(name) {
            const input = form.querySelector('[name="' + name + '"]');
            if (!input) return true; // nothing to validate
            const val = input.value;
            const rule = validators[name];
            if (!rule) return true;

            if (val === '' || val === null || (typeof val === 'string' && val.trim() === '')) {
                showError(input, rule.messageEmpty);
                return false;
            }

            if (rule.messageInvalid && !rule.test(val)) {
                showError(input, rule.messageInvalid);
                return false;
            }

            // valid
            clearError(input);
            return true;
        }

        // validate all fields -> returns true if all valid
        function validateAll() {
            let ok = true;
            for (const name in validators) {
                const v = validateFieldByName(name);
                if (!v) ok = false;
            }
            return ok;
        }

        // attach real-time listeners
        ['name', 'type', 'opening_balance', 'opening_date'].forEach(fieldName => {
            const el = form.querySelector('[name="' + fieldName + '"]');
            if (!el) return;
            // on input for text/number, on change for select/date
            const ev = (el.tagName.toLowerCase() === 'select' || el.type === 'date') ? 'change' :
                'input';
            el.addEventListener(ev, () => validateFieldByName(fieldName));
            // also on blur validate
            el.addEventListener('blur', () => validateFieldByName(fieldName));
        });

        // intercept submit
        form.addEventListener('submit', function(e) {
            // first run client-side validation
            const ok = validateAll();
            if (!ok) {
                // prevent actual submit and do NOT show spinner
                e.preventDefault();
                // ensure button is not disabled or showing spinner
                btn.disabled = false;
                spinner.classList.add('d-none');
                text.innerHTML = '<i class="fas fa-check me-1"></i> Add Account';
                // keep modal open (it already is)
                return false;
            }

            btn.disabled = true;
            spinner.classList.remove('d-none');
            text.textContent = ' Saving...';

        });
    });
</script>
{{-- auto-open modal if server-side validation failed --}}
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = new bootstrap.Modal(document.getElementById('createAccountModal'));
            modal.show();
        });
    </script>
@endif
