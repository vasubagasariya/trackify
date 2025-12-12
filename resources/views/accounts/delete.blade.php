<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-account-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // default submit रोको

                const accName = this.dataset.name || 'this account';
                const thatForm = this;

                // SweetAlert2 confirmation — package look जैसा
                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete <strong>${accName}</strong>. This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        popup: 'swal2-border-radius'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // show loading toast/modal while submitting
                        Swal.fire({
                            title: 'Deleting...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            showConfirmButton: false
                        });
                        // submit the form (will trigger server delete route)
                        thatForm.submit();
                    } else {
                        // optional small toast for cancel
                        Swal.fire({
                            icon: 'info',
                            title: 'Cancelled',
                            text: 'Account is safe.',
                            timer: 1200,
                            showConfirmButton: false,
                            position: 'center'
                        });
                    }
                });
            });
        });
    });
</script>
