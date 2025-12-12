<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trackify — Login</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Local: <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        .login-left {
            background: linear-gradient(135deg,#0f1724 0%, #1f2937 100%);
            color: #fff;
        }
        .login-left .brand {
            font-weight: 700;
            font-size: 1.3rem;
        }
        .login-card {
            border-radius: 12px;
            overflow: hidden;
        }
        .input-group-text { background: transparent; border-right: 0; }
        .form-control { border-left: 0; }
        .toggle-btn { border-left: 0; }
        @media (max-width: 767px) {
            .login-left { padding: 2rem 1rem; text-align: center; }
        }
    </style>
</head>
<body class="hold-transition">

<div class="login-page d-flex align-items-center" style="min-height:100vh; background:#f4f6f9;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10">
                <div class="card login-card shadow-lg">
                    <div class="row g-0">
                        <!-- Left illustration -->
                        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center login-left p-4">
                            <div class="text-center px-3">
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                                     class="img-fluid mb-3" alt="login image" style="max-height:260px;">
                                <h3 class="brand">Welcome back to <strong>Trackify</strong></h3>
                                <p class="text-muted" style="color:#cbd5e1 !important">Securely manage your accounts & transactions</p>
                            </div>
                        </div>

                        <!-- Form area -->
                        <div class="col-12 col-md-6">
                            <div class="card-body p-4 p-md-5">
                                <h4 class="mb-4">Sign in to your account</h4>

                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                <form method="post" action="{{ route('login.check') }}">
                                    @csrf

                                    {{-- Username (name same as before) --}}
                                    <div class="mb-3">
                                        <label for="form3Example3" class="form-label">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text"
                                                   id="form3Example3"
                                                   name="username"
                                                   class="form-control @error('username') is-invalid @enderror"
                                                   placeholder="Enter username"
                                                   value="{{ old('username') }}"
                                                   required autofocus>
                                        </div>
                                        @error('username')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Password (name same as before) --}}
                                    <div class="mb-3">
                                        <label for="form3Example4" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password"
                                                   id="form3Example4"
                                                   name="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   placeholder="Enter password"
                                                   required>
                                            <button type="button" class="btn btn-outline-secondary toggle-btn" id="togglePassword" title="Show / Hide password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                                            <label class="form-check-label" for="remember"> Remember me </label>
                                        </div>
                                        <a href="#!" class="small">Forgot password?</a>
                                    </div>

                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-sign-in-alt me-2"></i> Login
                                        </button>
                                    </div>

                                    <div class="text-center">
                                        <small class="text-muted">Don't have an account? <a href="#">Create one</a></small>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div> <!-- /.card -->
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<!-- Local versions if available:
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/js/adminlte.min.js') }}"></script>
-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('togglePassword');
    const pwd = document.getElementById('form3Example4');
    if(toggle && pwd){
        toggle.addEventListener('click', function () {
            const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
            pwd.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
});
</script>
</body>
</html>
