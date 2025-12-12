{{-- Create Transfer Modal --}}
<div class="modal fade" id="createTransferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header custom-header">
                <h5 class="modal-title">Create Transfer</h5>
                <button type="button" class="btn btn-sm border-0 bg-transparent p-1" data-bs-dismiss="modal">
                    <i class="fas fa-times fa-lg text-secondary"></i>
                </button>
            </div>

            <div class="modal-body custom-body">
                <form id="transferCreateForm" action="{{ route('transfers.store') }}" method="post">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">From Account</label>
                        <select id="from_account" name="from_account" class="form-select @error('from_account') is-invalid @enderror">
                            <option value="">-- Select account --</option>
                            @foreach($data as $d)
                                <option value="{{ $d->id }}" {{ old('from_account') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                        @error('from_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">To Account</label>
                        <select id="to_account" name="to_account" class="form-select @error('to_account') is-invalid @enderror">
                            <option value="">-- Select account --</option>
                            @foreach($data as $d)
                                <option value="{{ $d->id }}" {{ old('to_account') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                        @error('to_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input id="amount" type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" placeholder="1000.00">
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input id="description" type="text" name="description" value="{{ old('description') }}" class="form-control @error('description') is-invalid @enderror" placeholder="optional">
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transfer Date</label>
                        <input id="transfer_date" type="date" name="transfer_date" value="{{ old('transfer_date') }}" class="form-control @error('transfer_date') is-invalid @enderror">
                        @error('transfer_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button id="createTransferBtn" type="submit" class="btn btn-primary w-100 mt-2">
                        <span id="createTransferSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="createTransferText"><i class="fas fa-check me-1"></i> Add Transfer</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('createTransferModal')?.addEventListener('shown.bs.modal', function() {
    this.querySelector('select[name="from_account"]').focus();
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('transferCreateForm');
    if (!form) return;
    const btn = document.getElementById('createTransferBtn');
    const spinner = document.getElementById('createTransferSpinner');
    const txt = document.getElementById('createTransferText');

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

    function validateField(name) {
        const el = form.querySelector('[name="'+name+'"]');
        if (!el) return true;
        const val = el.value;
        const rule = validators[name];
        if (!rule) return true;
        if (val === '' || val === null || (typeof val === 'string' && val.trim() === '')) { showError(el, rule.messageEmpty); return false; }
        if (rule.messageInvalid && !rule.test(val)) { showError(el, rule.messageInvalid); return false; }
        clearError(el); return true;
    }

    ['from_account','to_account','amount','transfer_date'].forEach(field => {
        const el = form.querySelector('[name="'+field+'"]');
        if (!el) return;
        const ev = (el.tagName.toLowerCase() === 'select' || el.type === 'date') ? 'change' : 'input';
        el.addEventListener(ev, () => validateField(field));
        el.addEventListener('blur', () => validateField(field));
    });

    form.addEventListener('submit', function(e) {
        let ok = true;
        ['from_account','to_account','amount','transfer_date'].forEach(k => { if (!validateField(k)) ok = false; });
        if (!ok) {
            e.preventDefault();
            btn.disabled = false;
            spinner.classList.add('d-none');
            txt.innerHTML = '<i class="fas fa-check me-1"></i> Add Transfer';
            return false;
        }
        btn.disabled = true;
        spinner.classList.remove('d-none');
        txt.textContent = ' Saving...';
    });
});
</script>

{{-- If server-side validation failed, auto-open modal and keep errors visible --}}
@if ($errors->any() && old())
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('createTransferModal'));
    modal.show();
});
</script>
@endif
