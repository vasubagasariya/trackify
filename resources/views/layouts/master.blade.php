<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('resources/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/adminlte/dist/css/adminlte.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/fontawesome-free/css/all.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('adminlte/css/custome.css') }}"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> --}}

    {{-- <link rel="stylesheet" href="{{asset('adminlte\css\custome.css')}}"> --}}
<style>
     /* Backdrop blur */
 .modal-backdrop.show {
   backdrop-filter: blur(50px);
   background-color: rgba(0, 0, 0, 0.45);
 }

 /* Smooth animation */
 .modal.fade .modal-dialog {
   transform: translateY(-18px) scale(.97);
   transition: all .25s ease;
   opacity: 0;
 }

 .modal.show .modal-dialog {
   transform: translateY(0) scale(1);
   opacity: 1;
 }

 /* Card style modal */
 .modal-content {
   border-radius: 14px;
   border: 1px solid #e7e7e7;
   box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
   background: #ffffff;
 }

 /* Header light gradient */
 .modal-header.custom-header {
   background: linear-gradient(90deg, #ffffff, #f7f7f8);
   border-bottom: 1px solid #ededed;
   padding: .8rem 1rem;
 }

 .modal-title {
   font-weight: 600;
   font-size: 1rem;
   color: #222;
 }

 .modal-body.custom-body {
   background: #ffffff;
   color: #222;
   padding: 1rem 1.3rem;
 }

 /* Inputs */
 .modal-body .form-control,
 .modal-body .form-select {
   background: #fafafa;
   border: 1px solid #d8d8d8;
   border-radius: 8px;
   color: #333;
   box-shadow: none;
 }

 .modal-body .form-select {
   width: 100%;
   border-radius: 8px;
   padding: 10px;
   border: 1px solid #d0d0d0;
   background-color: #fafafa;
 }

 .modal-body .form-control::placeholder {
   color: #9f9f9f;
 }

 /* Smaller modal */
 @media (min-width: 576px) {
   .modal-dialog.modal-sm {
     max-width: 480px;
   }
 }

 .swal2-border-radius {
   border-radius: 12px;
 }

 .btn-group {
   gap: 10px;
 }

 /* optional: active button style */
 .month-button.active {
   background-color: #007bff;
   color: #fff;
   border-color: #007bff;
 }

 .pie-block {
  width: 100%;
  max-width: 520px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 10px;
  border-radius: 8px;
}

/* header (title / total) */
.pie-block .pie-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 14px;
  font-weight: 600;
}

/* content row: canvas + legend */
.pie-block .pie-content {
  display: flex;
  gap: 12px;
  align-items: flex-start;
}

/* ********** IMPORTANT: make canvas square ********** */
/* Give fixed square size (adjust px if needed) */
.pie-block .pie-canvas {
  width: 180px !important;
  height: 180px !important;
  flex: 0 0 auto;
  display: block;
  padding: 0 !important;
  margin: 0 !important;
  box-sizing: border-box;
  flex-shrink: 0;
}

/* legend area */
.pie-block .pie-legend {
  flex: 1 1 auto;
  min-width: 160px;
  max-width: 320px;
  overflow-y: auto;
  padding-left: 6px;
}

/* mobile: smaller square and stacked layout */
@media (max-width: 768px) {
  .pie-block .pie-content {
    flex-direction: column;
    align-items: center;
  }
  .pie-block .pie-canvas {
    width: 180px !important;
    height: 180px !important;
  }
  .pie-block .pie-legend {
    width: 100%;
    max-width: none;
  }
}

/* small visual helpers */
.legend-left { display:flex; align-items:center; gap:8px; }
.legend-color-box { width:12px; height:12px; border-radius:2px; }
</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">

        @include('includes.navbar')
        @include('includes.sidebar')

        <div class="content-wrapper">
            <section class="content pt-3">
                @yield('content')
            </section>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- <script src="/path/to/jquery.min.js"></script>
    <script src="/path/to/bootstrap.bundle.min.js"></script>
    <script src="/path/to/adminlte.min.js"></script> --}}

    @include('sweetalert::alert')

</body>

</html>
