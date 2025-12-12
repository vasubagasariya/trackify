<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-transaction-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const thatForm = this; // <-- **ADD THIS LINE**

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete <strong>Transaction</strong>. This action cannot be undone.`,
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
                        Swal.fire({
                            title: 'Deleting...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            showConfirmButton: false
                        });
                        thatForm.submit(); // now thatForm is defined
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Cancelled',
                            text: 'Transaction is safe.',
                            timer: 800,
                            showConfirmButton: false,
                            position: 'center'
                        });
                    }
                });
            });
        });
    });
</script>
