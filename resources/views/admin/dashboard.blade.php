@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Admin Dashboard</h2>
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

                    <h3>User Management</h3>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role ? $user->role->role_name : 'No Role' }}</td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.user.todos', $user->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-tasks"></i> View Todos
                                            </a>

                                            <a href="{{ route('admin.permissions', $user->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-key"></i> Permissions
                                            </a>

                                            <form action="{{ route('admin.user.toggle', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @if($user->is_active ?? true)
                                                    <button type="submit" class="btn btn-secondary btn-sm">
                                                        <i class="fas fa-ban"></i> Deactivate
                                                    </button>
                                                @else
                                                    <button type="submit" name="activate" value="1" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> Activate
                                                    </button>
                                                @endif
                                            </form>

                                            <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
