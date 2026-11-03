@extends('layouts.app')
@section('title','Verify Email')
@section('content')
<div class="row justify-content-center py-5"><div class="col-md-7"><div class="card p-5 text-center"><span class="feature-icon mx-auto mb-3"><i class="bi bi-envelope-check"></i></span><h2 class="page-title">Verify your email</h2><p class="text-muted">A verification link has been sent using Laravel’s log mailer. For this local demo, open <code>storage/logs/laravel.log</code>, copy the verification URL, and open it in your browser.</p><div class="d-flex justify-content-center gap-2"><form method="POST" action="{{ route('verification.send') }}">@csrf<button class="btn btn-success">Send another link</button></form><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-secondary">Logout</button></form></div></div></div></div>
@endsection
