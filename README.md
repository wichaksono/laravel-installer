# Laravel Installer

This repository contains a **web-based installer** for Laravel applications.  
It automates the initial setup process including system requirement checks, database configuration, migrations, and administrator account creation, all through a user-friendly interface.

Visit: [https://neon.web.id](https://neon.web.id)

---

## Table of Contents

- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
- [File Structure](#file-structure)
- [Customization](#customization)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

---

## Features

- **Automated Setup**: Step-by-step installation wizard.  
- **System Requirements Check**: Verifies PHP version and required extensions (`bcmath`, `curl`, `json`, `mbstring`, `pdo`, etc.).  
- **Database Configuration**: Collects credentials and updates the `.env` file automatically.  
- **Database Migration & Seeding**: Runs `php artisan migrate:fresh` and `php artisan db:seed`.  
- **Application Key Generation**: Creates and updates a new `APP_KEY` automatically.  
- **Administrator Account Creation**: Simple form for creating the first admin account.  
- **Session-based State Management**: Stores installation state and errors.  
- **Redirects**: Redirects to the main app after installation is complete.  

---

## Prerequisites

Ensure the following before running the installer:

- **PHP**: Version 8.3 or higher  
- **Web Server**: Apache / Nginx configured for Laravel  
- **Database Server**: MySQL, PostgreSQL, or SQLite accessible by the web server  
- **File Permissions**: Web server user must have write access to the project root and `.env`  

**Required PHP extensions:**

`bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `json`,  
`mbstring`, `openssl`, `pcre`, `pdo`, `session`, `tokenizer`, `xml`

---

## Installation

1. **Clone Repository** into your Laravel project (e.g., `public/installer`):

```bash
   git clone https://github.com/your-username/your-installer-repo.git public/installer
````

2. **Ensure Artisan Access**
   The installer executes Artisan commands using `exec()`.
   Verify that `exec()` is enabled and PHP can run `php artisan`.

3. **File Placement**

   * `public/installer/controller.php`
   * `public/installer/index.php`
   * `public/installer/views/` (HTML templates for each step)

   `index.php` acts as the installer entry point.

---

## Usage

1. **Access the Installer**
   Open in browser:

   ```
   http://your-app-domain.com/installer
   ```

2. **Follow the Steps**

   * **Welcome** → Initial screen
   * **Requirements** → Check system requirements
   * **Database Setup** → Enter database credentials and run migrations
   * **Administrator Account** → Create the first admin account
   * **Finish** → Confirm installation and redirect to app

   Once complete, the installer redirects to `/` if `.env` exists, preventing re-installation.

---

## File Structure

Installer core files:

```
├── public/
│   ├── installer/
│   │   ├── controller.php       # Core logic
│   │   ├── index.php            # Entry point & router
│   │   ├── views/               # Templates for each step
│   │   │   ├── welcome.html.php
│   │   │   ├── requirements.html.php
│   │   │   ├── database.html.php
│   │   │   ├── administrator.html.php
│   │   │   └── finish.html.php
```

**Key functions in `controller.php`:**

* `run_artisan_command()` → Execute Artisan commands
* `generate_app_key_and_update_env()` → Generate new APP\_KEY
* `update_env_database_config()` → Update `.env` with DB credentials
* `check_requirements()` → Verify system requirements
* `handle_database_setup()` → Run migrations and seeders
* `handle_administrator_creation()` → Create admin account

---

## Customization

* **Views** → Modify files in `public/installer/views/` to change UI
* **Logic** → Edit `controller.php` to add/remove steps or commands

---

## Troubleshooting

* **"Failed to connect to the database"**
  → Check host, port, username, password, and ensure DB server is running

* **"Failed to update .env file"**
  → Likely permission issue; ensure web server can write to `.env`

* **"Failed to run database migrations"**
  → Check DB credentials and PHP permission to run Artisan commands

---

## Contributing

Contributions are welcome. Please open issues or submit pull requests.

---

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).

