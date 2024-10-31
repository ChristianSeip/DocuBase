# DocuBase

## Description

DocuBase is documentation software for managing and publishing articles.
The software offers basic and advanced features to manage categories, items, users and permissions via an Admin Control Panel (ACP).
This project is suitable for both internal documentation and publicly accessible knowledge bases.

## Installation

### System Requirements

* PHP Version 8.2 or higher
* MariaDB 10.11 or higher
* For an installation with 1,000 users, 10,000 articles, and 100 categories, 200 MB of free space is recommended

## Installation steps

1. Clone repository:

`git clone https://github.com/ChristianSeip/DocuBase.git`

2. Install dependencies:

`composer install`

3. Adapt the .env.example to your needs and save it in the main directory as .env

4. Create database and basic data:

`php bin/console doctrine:database:create`

`php bin/console doctrine:migrations:migrate`

`php bin/console doctrine:fixtures:load`

5. Install assets:

`php bin/console assets:install`

Start server:

`php -S localhost:8000 -t public`

6. The application should now be accessible at http://127.0.0.1:8000.

### Login

During the installation, an admin account was automatically created with which you can log in:

User: admin@example.com

Password: admin

_The data should be changed for your own security._

## Basic installation information

### User Roles
* Admin
* User
* Guest

The user roles are protected and are required for proper operation. The rights can of course be freely adjusted. You are also free to create any additional roles you want.

### Guest-Account
The guest account should under no circumstances be deleted. It is not possible to log in with it, but it is used internally for users who are not logged in.

### Dummy-Artikel

At http://127.0.0.1:8000/article/1/welcome you will find a short article that will introduce you to the first steps of your new DocuBase.

