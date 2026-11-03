@extends('layouts.app')
@section('title','Edit Product')
@section('content')
<div class="mb-4"><a href="{{ route('products.show',$product) }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Product details</a><h1 class="page-title mt-2">Edit {{ $product->name }}</h1><p class="text-muted">Update the inventory or expiry information.</p></div><div class="card p-4"><form method="POST" action="{{ route('products.update',$product) }}">@csrf @method('PUT') @include('products._form')<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('products.show',$product) }}" class="btn btn-light">Cancel</a><button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Update Product</button></div></form></div>
@endsection
