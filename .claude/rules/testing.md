# VeloCMS Testing Rules

## When to Write Tests

- Every new Model public method → unit test
- Every new Controller action → integration test (optional but preferred)
- Every bug fix → regression test

## Test Naming

```
{ClassUnderTest}Test
testGetBySlug_returnsPost_whenSlugExists()
testGetBySlug_returnsNull_whenSlugNotFound()
testCreate_returnsNewId()
testSoftDelete_hidesFromGetAll()
```

## Running Tests

```bash
composer test                        # All tests
vendor/bin/phpunit tests/Unit/       # Unit only
vendor/bin/phpunit --filter Blog     # Module-specific
```

## Test Rules

1. No test depends on another test's state
2. Integration tests use transaction rollback (`START TRANSACTION` / `ROLLBACK`)
3. No hardcoded IDs — always create data in test
4. Mock PDO for unit tests — no real DB required
5. Tests must pass before pushing to `main`

## Coverage Targets

- Models: 80%+ coverage
- Critical paths (auth, CSRF): 100%
- Views: not required (visual testing)
