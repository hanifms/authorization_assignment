# Laravel Todo App - Role-Based Access Control (RBAC) Implementation

## Requirements Implemented

1.  Added authorization layer requiring login before accessing Todo pages
2.  Implemented RBAC to redirect users based on roles:
   - Regular users to Todo management pages
   - Administrators to admin dashboard with user management
3.  Created UserRoles table with:
   - RoleID
   - UserID
   - RoleName
   - Description
4.  Created RolePermissions table with:
   - PermissionID
   - RoleID
   - Description (Create, Retrieve, Update, Delete)
5.  Implemented UI that shows/hides buttons based on permissions
   - Users with Create permission see "Add Todo" button
   - Users with Update permission see edit buttons
   - Users with Delete permission see delete buttons

## Implementation Details

### Database Structure

#### User Roles Table
Located at `database/migrations/2025_06_17_000000_create_user_roles_table.php`

```php
Schema::create('user_roles', function (Blueprint $table) {
    $table->id('role_id');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->string('role_name');
    $table->text('description')->nullable();
    $table->timestamps();
    
    // A user can have only one role
    $table->unique('user_id');
});
```

#### Role Permissions Table
Located at `database/migrations/2025_06_17_000001_create_role_permissions_table.php`

```php
Schema::create('role_permissions', function (Blueprint $table) {
    $table->id('permission_id');
    $table->foreignId('role_id')->constrained('user_roles', 'role_id')->onDelete('cascade');
    $table->enum('description', ['Create', 'Retrieve', 'Update', 'Delete']);
    $table->timestamps();
    
    // A role can have a specific permission only once
    $table->unique(['role_id', 'description']);
});
```

### Key Models

#### UserRole Model (`app/Models/UserRole.php`)
```php
public function permissions()
{
    return $this->hasMany(RolePermission::class, 'role_id', 'role_id');
}

public function hasPermission(string $permission): bool
{
    return $this->permissions()->where('description', $permission)->exists();
}
```

#### User Model (`app/Models/User.php`)
```php
public function role()
{
    return $this->hasOne(UserRole::class, 'user_id');
}

public function hasPermission(string $permission): bool
{
    return $this->role && $this->role->hasPermission($permission);
}

public function isAdmin(): bool
{
    return $this->hasRole('Administrator');
}
```

### Middleware

#### RoleMiddleware (`app/Http/Middleware/RoleMiddleware.php`)
Restricts routes based on user roles:
```php
public function handle(Request $request, Closure $next, string $role)
{
    // Check if user has the required role
    if (!Auth::user()->hasRole($role)) {
        return redirect()->route('home')->with('error', 'You do not have permission to access this page.');
    }
    return $next($request);
}
```

#### PermissionMiddleware (`app/Http/Middleware/PermissionMiddleware.php`)
Restricts actions based on user permissions:
```php
public function handle(Request $request, Closure $next, $permission)
{
    if (!Auth::user()->hasPermission($permission)) {
        return redirect()->back()->with('error', 'You do not have permission to perform this action.');
    }
    return $next($request);
}
```

### Routes

Routes are protected with middleware to enforce RBAC:
```php
// Todo routes with permission middleware
Route::middleware('auth')->group(function () {
    Route::get('/todo', [TodoController::class, 'index'])
        ->middleware('permission:Retrieve')
        ->name('todo.index');
    
    Route::get('/todo/create', [TodoController::class, 'create'])
        ->middleware('permission:Create')
        ->name('todo.create');
    // Other routes...
});

// Admin routes protected by role middleware
Route::middleware(['auth', 'role:Administrator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    // Other admin routes...
});
```

### Views

The Todo list view shows/hides buttons based on permissions:
```blade
<!-- resources/views/todo/list.blade.php -->
@if(Auth::user()->hasPermission('Create'))
    <a href="{{ route('todo.create') }}" class="btn btn-primary btn-sm">Add Todo</a>
@endif

<!-- In table rows -->
@if(Auth::user()->hasPermission('Update'))
    <a href="{{ route('todo.edit', $todo->id) }}" class="btn btn-sm btn-primary">Edit</a>
@endif

@if(Auth::user()->hasPermission('Delete'))
    <form action="{{ route('todo.destroy', $todo->id) }}" method="POST" class="d-inline">
        <!-- Delete form -->
    </form>
@endif
```

## TL;DR - How It Works

1. **Authentication Flow**: Login → Role Check → Redirect to appropriate page
   - Admins go to Dashboard
   - Users go to Todo list

2. **Authorization System**:
   - **Roles**: Administrator or User role assigned to each user
   - **Permissions**: Create, Retrieve, Update, Delete permissions linked to roles
   - **UI Elements**: Buttons and links appear only when user has proper permissions

3. **Admin Functions**:
   - View all users and their todos
   - Toggle user activation status
   - Delete users
   - Manage user permissions

4. **User Experience**:
   - Users only see UI elements for actions they can perform
   - Permission-less actions are hidden or disabled
   - Clear error messages explain why actions are denied


## demo showcase

https://github.com/user-attachments/assets/9162fe3b-55f9-40b7-80de-f683b280469b

