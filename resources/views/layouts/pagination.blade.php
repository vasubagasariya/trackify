@if ($items instanceof \Illuminate\Pagination\LengthAwarePaginator && $items->hasPages())
    <style>
        .custom-pagination .page-link {
            border-radius: 6px !important;
            margin: 0 4px;
            padding: 6px 14px;
            border: 1px solid #dee2e6;
            color: #333;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .custom-pagination .page-link:hover {
            background: #f0f0f0;
            color: #000;
        }

        .custom-pagination .active > .page-link {
            background: #007bff;
            border-color: #007bff;
            color: #fff !important;
        }

        .custom-pagination .disabled > .page-link {
            background: #e9ecef;
            color: #6c757d;
            border-color: #dee2e6;
        }
    </style>

    <div class="d-flex justify-content-center mt-3 custom-pagination">
        {{ $items->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
@endif
