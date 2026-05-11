# Database & Schema Rules

## Naming Conventions
- Tables use plural names, e.g. `projects`, `participants`.
- Columns use snake_case, e.g. `project_id`, `first_name`.
- Primary key is `id` with AUTO_INCREMENT.
- Foreign keys use `<singular_table_name>_id`, e.g. `project_id`.

## Constraints
- Main tables should include `created_at` and `updated_at` where practical.
- Use foreign keys to preserve core relationships.
- Current schema v1 uses hard delete unless a module explicitly implements soft delete later.

## Performance
- Add indexes for frequently searched columns such as `access_token`, `project_id`, and `verify_token`.
- Use `utf8mb4` for Thai text.

## Exam Access Codes
- `participants.access_token` stores the participant exam access code.
- The value must be a unique 6-digit numeric string, e.g. `042817`.
- Generate with `random_int(0, 999999)`, pad left to 6 digits, and check uniqueness before insert/update.
- Do not use long hex strings for participant exam access. Long secure tokens are reserved for certificate verification tokens.
