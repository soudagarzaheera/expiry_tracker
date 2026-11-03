<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Expiry Tracker') · Smart Expiry Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root{--brand:#166534;--brand-dark:#0f3d25;--ink:#17231c;--muted:#65756b;--canvas:#f5f8f6}
        body{background:var(--canvas);color:var(--ink);font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;min-height:100vh}.navbar-brand{font-weight:800;letter-spacing:-.03em}.brand-mark{width:35px;height:35px;display:inline-grid;place-items:center;border-radius:10px;background:#dcfce7;color:var(--brand)}
        .navbar{box-shadow:0 1px 0 rgba(20,55,34,.09)}.nav-link{font-weight:600}.nav-link.active,.nav-link:hover{color:var(--brand)!important}.page-wrap{padding-top:2rem;padding-bottom:3rem}.page-title{font-weight:800;letter-spacing:-.035em}.text-muted{color:var(--muted)!important}
        .card{border:0;border-radius:16px;box-shadow:0 8px 30px rgba(24,64,40,.07)}.stat-card{overflow:hidden;position:relative}.stat-icon{width:46px;height:46px;border-radius:13px;display:grid;place-items:center;font-size:1.3rem}.table> :not(caption)>*>*{padding:.9rem .75rem;vertical-align:middle}.table thead th{font-size:.76rem;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);background:#f8faf9;border-bottom:0}
        .btn{border-radius:10px;font-weight:650}.btn-success{background:var(--brand);border-color:var(--brand)}.btn-success:hover{background:var(--brand-dark);border-color:var(--brand-dark)}.form-control,.form-select{border-radius:10px;padding:.68rem .8rem;border-color:#d8e2dc}.form-control:focus,.form-select:focus{border-color:#22c55e;box-shadow:0 0 0 .2rem rgba(34,197,94,.12)}
        .badge{font-weight:700;padding:.47em .72em}.empty-state{padding:3.5rem 1rem;text-align:center}.empty-state i{font-size:2.5rem;color:#93a49a}.hero{background:radial-gradient(circle at 84% 18%,rgba(74,222,128,.35),transparent 29%),linear-gradient(135deg,#0f3d25,#166534 58%,#15803d);color:#fff;border-radius:0 0 36px 36px;min-height:570px;display:flex;align-items:center}.hero-panel{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.22);backdrop-filter:blur(12px);border-radius:22px}.feature-icon{width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:#dcfce7;color:var(--brand);font-size:1.3rem}
        @media print{.no-print,.navbar{display:none!important}.page-wrap{padding:0}.card{box-shadow:none;border:1px solid #ddd}body{background:#fff}}
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white sticky-top no-print"><div class="container"><a class="navbar-brand d-flex align-items-center gap-2" href="{{ auth()->check() ? route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard') : route('home') }}"><span class="brand-mark"><i class="bi bi-hourglass-split"></i></span> Expiry Tracker</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="mainNav">
@auth
<ul class="navbar-nav me-auto ms-lg-4"><li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard','admin.dashboard') ? 'active' : '' }}" href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard') }}">Dashboard</a></li><li class="nav-item"><a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a></li><li class="nav-item"><a class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}" href="{{ route('alerts.index') }}">Alerts @if($navAlertCount)<span class="badge rounded-pill bg-danger">{{ $navAlertCount }}</span>@endif</a></li><li class="nav-item"><a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">Reports</a></li>@if(auth()->user()->isAdmin())<li class="nav-item dropdown"><a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') && !request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">Admin</a><ul class="dropdown-menu"><li><a class="dropdown-item" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>Manage Users</a></li><li><a class="dropdown-item" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags me-2"></i>Categories</a></li></ul></li>@endif</ul>
<div class="dropdown"><button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}</button><ul class="dropdown-menu dropdown-menu-end"><li><span class="dropdown-item-text small text-muted">{{ ucfirst(auth()->user()->role) }} · {{ auth()->user()->email }}</span></li><li><hr class="dropdown-divider"></li><li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></form></li></ul></div>
@else
<div class="ms-auto d-flex gap-2"><a href="{{ route('login') }}" class="btn btn-outline-success">Login</a><a href="{{ route('register') }}" class="btn btn-success">Create account</a></div>
@endauth
</div></div></nav>
@hasSection('full') @yield('full') @else
<main class="container page-wrap">@if(session('success'))<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif @if($errors->any())<div class="alert alert-danger"><strong>Please fix the following:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif @yield('content')</main>
@endif
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script><script>document.querySelectorAll('[data-confirm]').forEach(el=>el.addEventListener('click',e=>{if(!confirm(el.dataset.confirm))e.preventDefault()}));</script>@stack('scripts')
</body></html>
