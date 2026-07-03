# ApexPlanet Task 2 - Basic CRUD Application

## Objective
Develop a simple PHP + MySQL web application to perform CRUD operations with basic user authentication.

## Features
- User registration and login (with password hashing)
- Session-based authentication
- Create, Read, Update, Delete blog posts

## Database
- Database name: `blogapp`
- Tables: `users` (id, username, password), `posts` (id, title, content, created_at)

## Files
- `db.php` - Database connection
- `register.php` - User registration
- `login.php` - User login
- `logout.php` - Logout / session destroy
- `posts.php` - View all posts (Read), Delete
- `create.php` - Add new post (Create)
- `edit.php` - Update existing post

## How to Run
1. Import the database schema (users, posts tables) into MySQL as `blogapp`
2. Place project folder in `htdocs`
3. Start Apache & MySQL via XAMPP
4. Visit `localhost/blogapp/register.php` to create an account, then log in

## Task 3 - Advanced Features
- Added search functionality (search posts by title or content)
- Added pagination (5 posts per page)
- Improved UI using Bootstrap 5 (CDN)
- Styled login, register, create, edit, and posts pages