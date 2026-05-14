# Database Setup

1. Open MySQL Workbench.
2. Connect to your local MySQL server.
3. Open `database.sql`.
4. Run the full script. It creates the `careerconnect` database, tables, and demo records.
5. Open `db.php` and update these values if your MySQL login is different:

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'careerconnect');
define('DB_USER', 'root');
define('DB_PASS', 'Frenik@1954');
```

Demo logins:

- Job seeker: `seeker@example.com` / `123456`
- Job giver: `giver@example.com` / `123456`
