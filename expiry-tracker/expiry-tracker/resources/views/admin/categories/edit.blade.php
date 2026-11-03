@extends('layouts.app')
@section('title','Edit Category')
@section('content')
<div class="mb-4"><a href="{{ route('admin.categories.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Categories</a><h1 class="page-title mt-2">Edit category</h1></div><div class="card p-4"><form method="POST" action="{{ route('admin.categories.update',$category) }}">@csrf @method('PUT') @include('admin.categories._form')<div class="d-flex justify-content-end gap-2"><a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a><button class="btn btn-success">Save Changes</button></div></form></div>
@endsection
