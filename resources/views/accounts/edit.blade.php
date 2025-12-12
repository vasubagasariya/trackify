<div class="modal fade" id="editAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header custom-header">
                <h5 class="modal-title">Update Account</h5>
                <button type="button" class="btn btn-sm border-0 bg-transparent p-1" data-bs-dismiss="modal">
                    <i class="fas fa-times fa-lg text-secondary"></i>
                </button>
            </div>

            <div class="modal-body custom-body">
                <form action="#" method="POST" id="modalEditAccountForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input id="edit_name" type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror" placeholder="Enter name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select id="edit_type" name="type" class="form-select @error('type') is-invalid @enderror">
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Opening Balance</label>
                        <input id="edit_opening_balance" type="number" step="0.01" name="opening_balance"
                            class="form-control @error('opening_balance') is-invalid @enderror" placeholder="1000.00">
                        @error('opening_balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Opening Date</label>
                        <input id="edit_opening_date" type="date" name="opening_date"
                            class="form-control @error('opening_date') is-invalid @enderror">
                        @error('opening_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2" id="editBtn">
                        <span id="editSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                        <span id="editText"><i class="fas fa-save me-1"></i> Update</span>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // when clicking any edit button, fill modal form
        document.querySelectorAll('.edit-account-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const action = this.dataset.action;
                const name = this.dataset.name || '';
                const type = this.dataset.type || '';
                const opening_balance = this.dataset.opening_balance || '';
                const opening_date = this.dataset.opening_date || '';

                const form = document.getElementById('modalEditAccountForm');
                form.action = action;

                document.getElementById('edit_name').value = name;
                document.getElementById('edit_type').value = type;
                document.getElementById('edit_opening_balance').value = opening_balance;
                document.getElementById('edit_opening_date').value = opening_date;

                // clear previous invalid states if any
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                    'is-invalid'));
                form.querySelectorAll('.invalid-feedback').forEach(fb => {
                    // keep server-side shown feedback for errors; optional reset
                    if (fb.dataset.keep !== '1') {
                        fb.classList.add('d-none');
                        fb.textContent = '';
                    }
                });
            });
        });

        // client-side validators (mirror create validators)
        const editForm = document.getElementById('modalEditAccountForm');
        const editBtn = document.getElementById('editBtn');
        const editSpinner = document.getElementById('editSpinner');
        const editText = document.getElementById('editText');

        const validators = {
            name: {
                test: value => value.trim().length > 0 && /^[A-Za-z\s]+$/.test(value.trim()),
                messageEmpty: 'Please enter account name',
                messageInvalid: 'Account name should contain only alphabetical values'
            },
            type: {
                test: value => value !== null && value !== '',
                messageEmpty: 'Type of account is compulsory'
            },
            opening_balance: {
                test: value => value !== '' && !isNaN(value),
                messageEmpty: 'Please Enter Opening Balance',
                messageInvalid: 'Balance should contain only numbers'
            },
            opening_date: {
                test: value => value !== '',
                messageEmpty: 'Please choose opening date of your account',
                messageInvalid: 'Invalid date format'
            }
        };

        function showError(inputEl, message) {
            inputEl.classList.add('is-invalid');
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

        function validateFieldByName(formEl, name) {
            const input = formEl.querySelector('[name="' + name + '"]');
            if (!input) return true;
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
            clearError(input);
            return true;
        }

        function validateAll(formEl) {
            let ok = true;
            for (const name in validators) {
                const v = validateFieldByName(formEl, name);
                if (!v) ok = false;
            }
            return ok;
        }

        // realtime listeners for edit form
        ['name', 'type', 'opening_balance', 'opening_date'].forEach(fieldName => {
            const el = editForm.querySelector('[name="' + fieldName + '"]');
            if (!el) return;
            const ev = (el.tagName.toLowerCase() === 'select' || el.type === 'date') ? 'change' :
                'input';
            el.addEventListener(ev, () => validateFieldByName(editForm, fieldName));
            el.addEventListener('blur', () => validateFieldByName(editForm, fieldName));
        });

        // submit interception for edit form
        editForm.addEventListener('submit', function(e) {
            const ok = validateAll(editForm);
            if (!ok) {
                e.preventDefault();
                editBtn.disabled = false;
                editSpinner.classList.add('d-none');
                if (editText) editText.innerHTML = '<i class="fas fa-save me-1"></i> Update';
                return false;
            }
            // pass -> show spinner + allow submit
            editBtn.disabled = true;
            editSpinner.classList.remove('d-none');
            editText.textContent = ' Saving...';
            // normal submit proceeds
        });

        // If update validation failed server-side and page reloaded with errors,
        // auto-open modal and fill fields from old() (so errors show inside modal)
        @if ($errors->any() && session('_old_input'))
            var old = @json(session('_old_input'));
            // set form action from a guessable route if you store id/name in old input,
            // else you may need to set a default action or pass it via session.
            // Here we try to open modal and fill from old inputs.
            document.addEventListener('DOMContentLoaded', function() {
                var editModal = new bootstrap.Modal(document.getElementById('editAccountModal'));
                // fill fields
                if (old.name) document.getElementById('edit_name').value = old.name;
                if (old.type) document.getElementById('edit_type').value = old.type;
                if (old.opening_balance) document.getElementById('edit_opening_balance').value = old
                    .opening_balance;
                if (old.opening_date) document.getElementById('edit_opening_date').value = old
                    .opening_date;
                editModal.show();
            });
        @endif

    });
</script>
