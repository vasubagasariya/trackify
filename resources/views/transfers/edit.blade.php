{{-- Edit Transfer Modal --}}
<div class="modal fade" id="editTransferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header custom-header">
                <h5 class="modal-title">Update Transfer</h5>
                <button type="button" class="btn btn-sm border-0 bg-transparent p-1" data-bs-dismiss="modal">
                    <i class="fas fa-times fa-lg text-secondary"></i>
                </button>
            </div>

            <div class="modal-body custom-body">
                <form id="transferEditForm" action="#" method="post">
                    @csrf
                    {{-- your routes use POST for update; if you change to PUT then add @method('PUT') --}}
                    
                    <div class="mb-3">
                        <label class="form-label">From Account</label>
                        <select id="edit_from_account" name="from_account" class="form-select">
                            <option value="">-- Select account --</option>
                            @foreach($data as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">To Account</label>
                        <select id="edit_to_account" name="to_account" class="form-select">
                            <option value="">-- Select account --</option>
                            @foreach($data as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input id="edit_amount" type="number" step="0.01" name="amount" class="form-control" placeholder="1000.00">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input id="edit_description" type="text" name="description" class="form-control" placeholder="optional">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transfer Date</label>
                        <input id="edit_transfer_date" type="date" name="transfer_date" class="form-control">
                    </div>

                    <button id="editTransferBtn" type="submit" class="btn btn-primary w-100 mt-2">
                        <span id="editTransferSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="editTransferText"><i class="fas fa-save me-1"></i> Update</span>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('transferEditForm');
    const editBtn = document.getElementById('editTransferBtn');
    const editSpinner = document.getElementById('editTransferSpinner');
    const editTxt = document.getElementById('editTransferText');

    // when edit button clicked, fill modal inputs
    document.querySelectorAll('.edit-transfer-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action || '#';
            const from_account = this.dataset.from_account || '';
            const to_account = this.dataset.to_account || '';
            const amount = this.dataset.amount || '';
            const description = this.dataset.description || '';
            const transfer_date = this.dataset.transfer_date || '';

            editForm.action = action;
            editForm.querySelector('[name="from_account"]').value = from_account;
            editForm.querySelector('[name="to_account"]').value = to_account;
            editForm.querySelector('[name="amount"]').value = amount;
            editForm.querySelector('[name="description"]').value = description;
            editForm.querySelector('[name="transfer_date"]').value = transfer_date;

            // clear validation states
            editForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            editForm.querySelectorAll('.invalid-feedback').forEach(fb => { fb.textContent=''; fb.classList.add('d-none'); });
        });
    });

    const validators = {
        from_account: { test: v => v !== '' && v !== null, messageEmpty: 'Please select source account' },
        to_account: { test: v => v !== '' && v !== null, messageEmpty: 'Please select destination account' },
        amount: { test: v => v !== '' && !isNaN(v) && Number(v) > 0, messageEmpty: 'Please enter amount', messageInvalid: 'Invalid amount' },
        transfer_date: { test: v => v !== '' , messageEmpty: 'Please choose transfer date' }
    };

    function showError(el, msg) {
        el.classList.add('is-invalid');
        let fb = el.parentElement.querySelector('.invalid-feedback');
        if (!fb) { fb = document.createElement('div'); fb.className = 'invalid-feedback'; el.parentElement.appendChild(fb); }
        fb.textContent = msg; fb.classList.remove('d-none');
    }
    function clearError(el) {
        el.classList.remove('is-invalid');
        const fb = el.parentElement.querySelector('.invalid-feedback');
        if (fb) { fb.textContent=''; fb.classList.add('d-none'); }
    }

    function validateField(formEl, name) {
        const el = formEl.querySelector('[name="'+name+'"]');
        if (!el) return true;
        const val = el.value;
        const rule = validators[name];
        if (!rule) return true;
        if (val === '' || val === null || (typeof val === 'string' && val.trim() === '')) { showError(el, rule.messageEmpty); return false; }
        if (rule.messageInvalid && !rule.test(val)) { showError(el, rule.messageInvalid); return false; }
        clearError(el); return true;
    }

    ['from_account','to_account','amount','transfer_date'].forEach(field => {
        const el = editForm.querySelector('[name="'+field+'"]');
        if (!el) return;
        const ev = (el.tagName.toLowerCase() === 'select' || el.type === 'date') ? 'change' : 'input';
        el.addEventListener(ev, () => validateField(editForm, field));
        el.addEventListener('blur', () => validateField(editForm, field));
    });

    editForm.addEventListener('submit', function(e) {
        let ok = true;
        ['from_account','to_account','amount','transfer_date'].forEach(k => { if (!validateField(editForm, k)) ok = false; });
        if (!ok) {
            e.preventDefault();
            editBtn.disabled = false;
            editSpinner.classList.add('d-none');
            editTxt.innerHTML = '<i class="fas fa-save me-1"></i> Update';
            return false;
        }
        editBtn.disabled = true;
        editSpinner.classList.remove('d-none');
        editTxt.textContent = ' Saving...';
    });
});
</script>
