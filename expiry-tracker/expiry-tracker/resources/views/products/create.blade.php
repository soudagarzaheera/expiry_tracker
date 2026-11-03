@extends('layouts.app')
@section('title','Add Product')
@section('content')
<div class="mb-4"><a href="{{ route('products.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Products</a><h1 class="page-title mt-2">Add product</h1><p class="text-muted">Expiry status will be calculated automatically.</p></div><div class="card p-4"><form method="POST" action="{{ route('products.store') }}">@csrf @include('products._form')<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('products.index') }}" class="btn btn-light">Cancel</a><button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Save Product</button></div></form></div>
@endsection
