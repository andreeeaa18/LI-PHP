# LI: PHP Blog

Individual assignment for the **PHP** course at university: a small blog and social platform where users publish stories, like and comment on them, report inappropriate content, and play two mini-games.

Built with plain **PHP**, **MySQL** and vanilla **JavaScript**, running on XAMPP. There are no frameworks.

## Features

- **Authentication:** sign up and log in, with sessions. Passwords are hashed with bcrypt (`password_hash` / `password_verify`).
- **Stories:** create, read, edit and delete posts with a title, content and an optional image URL. The home page lists every story, and each user has a profile page with their own.
- **Likes:** toggle a like on a post, with a live counter.
- **Comments:** threaded comments with replies.
- **Reports:** users can report a post, and each user can report a given post once.
- **Roles:** `user` and `admin`. Authors can delete their own posts, and admins can delete any post.
- **Mini-games:**
  - **Typing game:** type a text drawn from `data/typing_texts.json`.
  - **Drawing game:** paint on a pixel grid and save drawings to the database.

## Tech

PHP (mysqli with prepared statements), MySQL / MariaDB, HTML, CSS and vanilla JavaScript (`fetch` calls to the JSON API).

## Project structure

```
index.php, login.php, profile.php, games.php   pages
api/            JSON endpoints: auth, posts, comments, likes, reports, drawings
includes/       db connection, auth/session helpers, navigation
assets/         css, js and icons
data/           typing_texts.json
setup.php       creates the database, tables and demo data
database.sql    the same schema and demo data as plain SQL
```

## Database

Six tables: `users`, `posts`, `likes`, `comments` (self-referencing `parent_id` for replies), `reports` and `drawings`. Foreign keys use `ON DELETE CASCADE`.

## Running it locally

1. Install [XAMPP](https://www.apachefriends.org) and start **Apache** and **MySQL**.
2. Put this folder in `htdocs` as **`LI`**, so the app lives at `http://localhost/LI/`. The paths in the code expect that name.
3. Open `http://localhost/LI/setup.php`. It creates the `li_blog` database, the tables and some demo content. (Alternatively, import `database.sql` in phpMyAdmin.)
4. Open `http://localhost/LI/login.php`.

Database settings are in `includes/db.php` (XAMPP defaults: user `root`, empty password, database `li_blog`).

### Demo accounts

Created by the setup. All three use the password **`password`**.

| Username | Role |
| --- | --- |
| Admin | admin |
| Alice | user |
| Bob | user |

These are for local testing only. If you ever deploy this somewhere, delete them and use a real database user instead of `root`.
