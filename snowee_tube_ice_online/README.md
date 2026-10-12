# SNOWEE TUBE ICE — Online PHP/MySQL Version

A separate deployment-ready copy of the SNOWEE Tube Ice management system. This package does **not** connect to or modify your existing XAMPP project or `BSIT3D_DB` database.

## Important hosting note

This is a conventional PHP + MySQL application. GitHub stores the source code, but GitHub Pages and standard Vercel static hosting do not execute PHP or provide a MySQL database. Deploy this ZIP to a PHP/MySQL host (for example, a cPanel host with PHP and MySQL/MariaDB). You can still keep the code in GitHub and deploy through your host's Git integration or upload the files using its File Manager.

## Included modules

- Admin login and logout
- Dashboard summary
- Agent management
- Inventory releases to agents (1 KG, 5 KG, 20 KG packaging)
- Customer deliveries
- Sales records (created from deliveries)
- Remittance records
- SQL database schema and one-time web setup
- Responsive navy / teal / gold UI

## Before deploying

1. Create a **new empty online database** and database user in your hosting control panel.
2. Upload this project's files to the host.
3. Configure database credentials in `config.php` (or set the environment variables listed below).
4. Open `https://YOUR-DOMAIN/setup.php` and create the first admin account.
5. Sign in, then **delete `setup.php` from the server immediately**.
6. Use HTTPS and a strong, unique admin password.

The setup page creates the tables in the online database only. It does not connect to your local computer or your XAMPP database unless you deliberately enter those credentials (do not do that).

## Database configuration

The app reads these environment variables if your host supports them:
- `SNOWEE_DB_HOST`
- `SNOWEE_DB_NAME`
- `SNOWEE_DB_USER`
- `SNOWEE_DB_PASS`

If environment variables are not available, edit the defaults at the top of `config.php`. Do not commit real production credentials to a public GitHub repository. For public GitHub repositories, use environment variables or upload a host-specific `config.php` privately after deployment.

## GitHub upload (beginner steps)

1. Extract `snowee-tube-ice-online.zip` on your computer.
2. Sign in at https://github.com and create a new repository, for example `snowee-tube-ice-online`.
3. Choose **Private** if you do not want the source code public. Do not include passwords or real database credentials.
4. In the new repository, choose **Add file → Upload files**.
5. Open the extracted project folder and upload its files and folders (upload the contents, not the ZIP itself).
6. Commit the upload.

## Deploy to PHP/MySQL hosting

1. Buy/activate hosting that explicitly supports PHP 8.1+ and MySQL or MariaDB.
2. In the hosting control panel, create a new database and database user, then grant that user all privileges on that database.
3. Open File Manager and upload the project files to the web root (often `public_html`), or use the host's Git deployment feature.
4. Set database credentials in `config.php` or host environment variables. Keep the database password private.
5. Visit `/setup.php`, create the admin account, and then delete `setup.php`.
6. Visit your domain and log in.

## Safe testing checklist

- Test with a new online database, never your existing `BSIT3D_DB`.
- Create a test agent and release stock.
- Record a customer delivery and check the sales total.
- Record a remittance and confirm the balance due changes.
- Test on a phone and desktop.
- Back up the online database before entering real business data.

## Local preview without XAMPP

You do not need XAMPP to deploy online. For a local preview, a PHP runtime and MySQL-compatible database are still needed; this package does not include a database server. The target is PHP/MySQL hosting.
