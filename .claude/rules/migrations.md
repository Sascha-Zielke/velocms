# VeloCMS Migration Rules

## File Naming

```
modules/{Name}/migrations/
├── 001_create_{name}_table.php
├── 002_add_{column}_to_{name}.php
└── 003_rename_{old}_to_{new}_in_{table}.php
```

- 3-digit zero-padded prefix
- Descriptive name: action + target
- Always `up()` and `down()` methods

## Standard Table Template

```sql
CREATE TABLE IF NOT EXISTS velocms_{name} (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    -- your columns here --
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at  DATETIME NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Translatable Content Columns

For any user-facing text that should be available in EN:

```sql
title       VARCHAR(255) NOT NULL,
title_en    VARCHAR(255) NULL,
content     LONGTEXT NULL,
content_en  LONGTEXT NULL,
manual_override_en BOOLEAN NOT NULL DEFAULT 0
```

## Rules

1. **Never edit an existing migration** — always create a new one
2. **Always implement `down()`** — rollback must work
3. **Test `down()` locally** before pushing
4. **Run migrations on staging first**, then production

## Running

```bash
php velocms migrate          # Run all pending migrations
php velocms migrate:rollback # Roll back last batch
php velocms migrate:status   # Show migration status
```
