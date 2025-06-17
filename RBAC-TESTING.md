# Laravel Todo App with Role-Based Access Control (RBAC)

This is a simple Todo application enhanced with authentication and Role-Based Access Control (RBAC).

## Features

- User Authentication (Login/Registration)
- Two-Factor Authentication via Email
- User Profile Management
- Role-Based Access Control
  - Administrator Role: Manages users and permissions
  - User Role: Manages their own todo items based on permissions
- Permissions System (CRUD operations)

## Setup Instructions

1. Clone the repository
2. Install dependencies:
```
composer install
npm install
```

3. Set up the environment:
```
cp .env.example .env
php artisan key:generate
```

4. Configure the database in `.env`
5. Run migrations and seeders:
```
php artisan migrate --seed
```

6. Compile frontend assets:
```
npm run dev
```

7. Start the server:
```
php artisan serve
```

## Default Users

After running the seeders, the following users will be created:

1. **Administrator**
   - Email: admin@example.com
   - Password: password
   - Role: Administrator
   - Permissions: Create, Retrieve, Update, Delete

2. **Regular User**
   - Email: user@example.com
   - Password: password
   - Role: User
   - Permissions: Create, Retrieve

3. **Test User**
   - Email: test@example.com
   - Password: password
   - Role: User
   - Permissions: Create, Retrieve

## Testing RBAC Implementation

1. **Login as Administrator** (admin@example.com)
   - You will be redirected to the Admin Dashboard
   - From here you can:
     - View all users
     - View user's todos
     - Toggle user activation status
     - Delete users
     - Manage user permissions

2. **Login as Regular User** (user@example.com)
   - You will be redirected to the Todo List page
   - You should only see buttons for actions you have permission to perform
   - By default, you can:
     - View your todo list (Retrieve)
     - Create new todo items (Create)
   - You cannot:
     - Edit todo items (Update)
     - Delete todo items (Delete)

3. **Try different permission combinations**
   - Login as Admin
   - Change a user's permissions
   - Login as that user to see the effect of permission changes

## Permission Logic

- If a user has the "Create" permission, they will see the "Add Todo" button
- If a user has the "Update" permission, they will see the "Edit" button for each todo
- If a user has the "Delete" permission, they will see the "Delete" button for each todo
- All users with the "Retrieve" permission can view their todo list
