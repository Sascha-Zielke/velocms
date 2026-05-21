# VeloCMS Security Rules

These rules are non-negotiable. All code must comply before merge.

## The Big Four

### 1. CSRF — Every POST Handler

```php
public function save(): void
{
    Auth::verifyCsrf(); // FIRST LINE — no exceptions
    // ...
}
```

Every form:
```php
<form method="POST" action="...">
    <?= csrf_field() ?>
```

### 2. XSS — Every Output

```php
// Views — every variable:
<?= e($anyUserData) ?>

// HTML content:
<?= safe_html($post['content']) ?>
```

### 3. SQL Injection — Prepared Statements Always

```php
// Only this:
$stmt = $this->db->prepare("SELECT * FROM t WHERE id = :id");
$stmt->execute([':id' => $id]);

// Never this:
$this->db->query("SELECT * FROM t WHERE id = $id");
```

### 4. Authentication — Every Admin Controller

```php
public function __construct()
{
    parent::__construct();
    $this->requireAuth(); // Always in admin controllers
}
```

## Additional Rules

- Passwords: `password_hash(PASSWORD_DEFAULT)` only
- No secrets in code: use `$_ENV['KEY']`
- Session: `session_regenerate_id(true)` on login
- File uploads: whitelist MIME types, generate safe filenames
- Dynamic SQL columns: whitelist explicitly

## Review Before Merge

Run `velocms-reviewer` agent on all changed files. No critical issues allowed in `main`.
