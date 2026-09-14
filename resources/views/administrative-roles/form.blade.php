@extends('layouts.app')

@section('title', isset($user) ? 'Edit ' . $roleModel->name : 'Add ' . $roleModel->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route($role . '.index') }}">{{ $roleModel->name }} Management</a></li>
    <li class="breadcrumb-item active">{{ isset($user) ? 'Edit' : 'Add' }}</li>
@endsection

@section('content')
<div class="container-fluid px-0"><div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4">
    <h3 class="fw-bold mb-4">{{ isset($user) ? 'Edit ' . $roleModel->name : 'Add ' . $roleModel->name }}</h3>
    <form method="POST" action="{{ isset($user) ? route($role . '.update', [$role => $role, 'user' => $user]) : route($role . '.store') }}" class="row g-3">
        @csrf @if(isset($user)) @method('PUT') @endif
        <div class="col-md-6"><label class="form-label" for="name">Full name</label><input id="name" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>@error('name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label" for="email">Email address</label><input id="email" type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>@error('email')<div class="text-danger small">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label" for="phone">Phone number</label><input id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label" for="status">Account status</label><select id="status" name="status" class="form-select" required>@foreach(['active','pending','inactive','suspended'] as $status)<option value="{{ $status }}" @selected(old('status', $user->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label" for="password">{{ isset($user) ? 'New password (optional)' : 'Initial password' }}</label><input id="password" type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }} minlength="8">@error('password')<div class="text-danger small">{{ $message }}</div>@enderror</div>
        <div class="col-12 d-flex gap-2"><a href="{{ route($role . '.index') }}" class="btn btn-outline-secondary">Cancel</a><button class="btn btn-primary">{{ isset($user) ? 'Update' : 'Create' }} {{ $roleModel->name }}</button></div>
    </form>
</div></div></div>
@endsection