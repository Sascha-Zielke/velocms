# VeloCMS Git Workflow

## Branches

| Branch | Purpose | Deploy |
|--------|---------|--------|
| `main` | Production | Auto-deploy via GitHub Actions |
| `staging` | Testing before merge | Manual / auto to staging VPS |

Never push broken code to `main`.

## Commit Format

```
{type}({scope}): {short description}

Types: feat / fix / docs / refactor / test / chore
Scope: module name or core area

Examples:
feat(blog): add category filter to admin list
fix(auth): resolve session fixation on login
docs(readme): update installation steps
refactor(core): extract TranslationService
test(blog): add softDelete unit test
chore(deps): update phpunit to 11.x
```

## Session End Protocol (Mandatory)

At the end of EVERY coding session:

```bash
# 1. Stage all changes
git add -A

# 2. Commit with descriptive message
git commit -m "feat(blog): scaffold Blog module with admin CRUD"

# 3. Push
git push

# 4. Update session files
# Edit RESUME.md — what was done, what's next
# Append to SESSION_LOG.md — date, summary, open issues
```

Never end a session without committing. GitHub Actions will auto-deploy `main` to production.

## GitHub Actions Deploy

Push to `main` → GitHub Actions → SSH to VPS → pull + restart PHP-FPM

VPS: `velocms@95.217.185.113:22`

## Before Starting a New Feature

```bash
git pull          # Get latest
git status        # Ensure clean working tree
# Read CLAUDE_START.md and RESUME.md
```
