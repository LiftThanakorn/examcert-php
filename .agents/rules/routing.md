# 🌐 Routing & Stability Rules

## 🛤️ Path Management
- **Front Controller**: All logic must flow through `index.php`.
- **Base URL**: Always use the `BASE_URL` constant for links (`<?= BASE_URL ?>/...`).
- **No Absolute Paths**: Never leak or use absolute server paths in the frontend or public logs.

## 🛡️ Forbidden (403) Prevention
- **Apache Config**: `.htaccess` must disable `Indexes` and `MultiViews`.
- **Trailing Slashes**: The router in `index.php` must handle (trim) trailing slashes to prevent directory access attempts.
- ** Laragon/Windows**: URI detection must correctly handle subdirectories (e.g., `/examcert-php/`).

## 🚫 Constraints
- Do NOT create physical directories in the public root that clash with route names (e.g., don't create an `/admin` folder if `/admin` is a route).
