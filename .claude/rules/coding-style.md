# VeloCMS Coding Style

## PHP

- `declare(strict_types=1)` at top of every PHP file
- PHP 8.2+ features are allowed and encouraged
- Full type declarations on all method parameters and return types
- PSR-4 namespaces: `VeloCMS\Modules\{Name}\Controllers\{Name}Controller`
- 4-space indentation, no tabs
- Max line length: 120 characters

## Security (Non-Negotiable)

- All POST: `Auth::verifyCsrf();` as **first line**
- All output: `e($value)` — never raw echo of user data
- All DB: PDO prepared statements — never string interpolation
- Passwords: `password_hash(PASSWORD_DEFAULT)` / `password_verify()`
- Admin controllers: `$this->requireAuth()` in constructor

## Naming

| Entity | Convention | Example |
|--------|-----------|---------|
| Classes | PascalCase | `BlogController` |
| Methods | camelCase | `getBySlug()` |
| Variables | camelCase | `$blogPost` |
| Constants | SCREAMING_SNAKE | `MAX_PER_PAGE` |
| DB tables | snake_case + prefix | `velocms_blog_posts` |
| DB columns | snake_case | `created_at`, `title_en` |
| Lang keys | dot.notation | `blog.headline` |

## Views

- Always `$this->extend('layouts/admin')` or `layouts/frontend`
- Always `e()` for user data
- Always `t('key')` for UI strings — no hardcoded German text
- Always `localized($row, 'field')` for content fields
- Always `csrf_field()` inside every `<form method="POST">`
