# 🔐 Security & Data Integrity Rules

## 🗄️ Database Safety
- **Prepared Statements**: Use PDO prepared statements for ALL queries.
- **No Concatenation**: Never inject variables directly into SQL strings.

## 🛡️ Input/Output Protection
- **XSS**: Always use `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` for output.
- **CSRF**: Check `csrf_token` on every POST and AJAX request.
- **Sanitization**: Trim and sanitize all user-provided data before processing.

## 🔑 Session & Auth
- **Regeneration**: Call `session_regenerate_id(true)` after login.
- **Fingerprinting**: Verify User-Agent against the session to prevent hijacking.
- **Password**: Use `password_hash()` and `password_verify()` only.

## 📁 File Security
- **Validation**: Check real MIME types (finfo).
- **Renaming**: Randomize filenames on upload to prevent overwriting or execution.
