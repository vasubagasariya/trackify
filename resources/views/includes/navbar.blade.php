<nav class="main-header navbar navbar-expand navbar-dark bg-dark">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <!-- Pushmenu Button -->
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <!-- Dashboard link (optional) -->
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- User Name (Optional) -->
        <li class="nav-item d-flex align-items-center">
            <span class="text-white mr-3">Hello, {{ Auth::user()->name ?? 'User' }}</span>
        </li>

        <!-- Logout -->
        <li class="nav-item">
            <a class="nav-link text-danger" href="{{ route('logout') }}">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</nav>
<script>
    // यह तभी चलाओ जब jquery पहले से लोड हो
    $(function() {
        const $toggle = $('[data-widget="pushmenu"]');

        // अगर adminlte plugin मौजूद है तो वो संभालेगा, वरना fallback
        $toggle.on('click', function(e) {
            // अगर AdminLTE का PushMenu plugin मौजूद है तो allow it
            if (typeof $.fn.pushMenu === 'function' || (window.AdminLTE && AdminLTE.PushMenu)) {
                // let plugin handle it
                return;
            }
            e.preventDefault();
            $('body').toggleClass('sidebar-collapse');
        });
    });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.querySelector('[data-widget="pushmenu"]');
    if (!toggle) return;

    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      document.body.classList.toggle('sidebar-collapse');
    });
  });
</script>
