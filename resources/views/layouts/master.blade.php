<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/css/custome.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> --}}

    {{-- <link rel="stylesheet" href="{{asset('adminlte\css\custome.css')}}"> --}}

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
    <script src="{{ asset('adminlte/js/adminlte.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- <script src="/path/to/jquery.min.js"></script>
    <script src="/path/to/bootstrap.bundle.min.js"></script>
    <script src="/path/to/adminlte.min.js"></script> --}}

    @include('sweetalert::alert')

</body>

</html>
