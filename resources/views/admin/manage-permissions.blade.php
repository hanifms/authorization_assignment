@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Manage Permissions for {{ $user->name }}</h2>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Users</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.permissions.update', $user->id) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label class="font-weight-bold">CRUD Permissions:</label>
                            <div class="mt-2">
                                @foreach($availablePermissions as $permission)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                               value="{{ $permission }}" id="permission_{{ $permission }}"
                                               {{ in_array($permission, $userPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission }}">
                                            {{ $permission }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Permissions</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
