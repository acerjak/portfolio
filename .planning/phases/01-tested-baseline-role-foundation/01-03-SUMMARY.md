---
phase: 01-tested-baseline-role-foundation
plan: 03
subsystem: auth
tags: [fortify, laravel, blade, phpunit, rbac]

# Dependency graph
requires:
  - phase: 01-tested-baseline-role-foundation (plan 01-01)
    provides: spatie/laravel-permission installed, HasRoles on User, RoleSeeder
provides:
  - "/register route permanently unreachable (Fortify registration feature removed)"
  - "/login page free of any dangling sign-up affordance"
  - "RBAC-04 regression coverage via tests/Feature/Roles/RegistrationClosedTest.php"
affects: [phase-02-filament-admin-panel, phase-02-client-resource]

actuals:
  tokens: 1665
  tasks: 2
  commits: 2

tech-stack:
  added: []
  patterns:
    - "Feature removal + dependent-view fix land in the same commit so no commit boundary leaves a page 500-ing"

key-files:
  created:
    - tests/Feature/Roles/RegistrationClosedTest.php
  modified:
    - config/fortify.php
    - resources/views/pages/auth/login.blade.php
    - app/Providers/FortifyServiceProvider.php
  deleted:
    - resources/views/pages/auth/register.blade.php
    - tests/Feature/Auth/RegistrationTest.php

key-decisions:
  - "D-04: deleted Features::registration() from config/fortify.php outright rather than layering a route-level guard over a still-enabled feature"
  - "D-05: deleted the skip-guarded tests/Feature/Auth/RegistrationTest.php rather than leaving it permanently and silently skipped"
  - "D-06: added explicit RegistrationClosedTest as the real RBAC-04 regression check"

patterns-established:
  - "Pattern: when a feature flag removal makes another view's route helper call unresolvable, fix both in the same task/commit — never split them across commit boundaries"

requirements-completed: [RBAC-04]

coverage:
  - id: D1
    description: "GET /register returns 404 — the route is not registered at all"
    requirement: "RBAC-04"
    verification:
      - kind: unit
        ref: "tests/Feature/Roles/RegistrationClosedTest.php#test_register_route_is_unreachable"
        status: pass
      - kind: unit
        ref: "php artisan route:list --json (no name starting with 'register')"
        status: pass
    human_judgment: false
  - id: D2
    description: "GET /login still returns 200 with no sign-up affordance after registration is disabled"
    requirement: "RBAC-04"
    verification:
      - kind: unit
        ref: "tests/Feature/Roles/RegistrationClosedTest.php#test_login_page_still_renders_after_registration_is_disabled"
        status: pass
      - kind: unit
        ref: "tests/Feature/Roles/RegistrationClosedTest.php#test_the_login_page_offers_no_sign_up_link"
        status: pass
    human_judgment: false
  - id: D3
    description: "Orphaned registration view, its Fortify view binding, and the skip-guarded RegistrationTest.php are removed with no stale references left anywhere in tracked source"
    verification:
      - kind: unit
        ref: "composer run test (42/42 passing, 0 skipped, Pint clean, PHPStan level 7 clean)"
        status: pass
      - kind: other
        ref: "grep -rnE \"route\\('register'\\)|register\\.store|registerView|pages::auth\\.register\" app resources routes config tests (0 matches)"
        status: pass
    human_judgment: false

duration: ~15min
completed: 2026-09-01
status: complete
---

# Phase 1 Plan 03: Close Public Registration Summary

**Permanently closed public self-registration by deleting Fortify's `Features::registration()` entry, removing the login page's now-dangling "Sign up" link in the same commit, and sweeping the orphaned registration view/binding/test out of the repo — replaced with a named `RegistrationClosedTest` that proves both `/register` 404s and `/login` still renders.**

## Performance

- **Duration:** ~15 min
- **Completed:** 2026-09-01
- **Tasks:** 2/2
- **Files modified:** 6 (1 created, 3 modified, 2 deleted)

## Accomplishments
- `Features::registration()` removed from `config/fortify.php` — Fortify no longer registers the `register`/`register.store` named routes, confirmed empty via `php artisan route:list --json`
- The unguarded "Don't have an account? Sign up" block removed from `resources/views/pages/auth/login.blade.php` in the exact same commit that removed the feature, so no point in git history has a login page that 500s
- `tests/Feature/Roles/RegistrationClosedTest.php` created with 3 assertions: route unreachable (404), login page still renders (200), no "Sign up" copy visible — the real RBAC-04 regression coverage
- Orphaned `resources/views/pages/auth/register.blade.php`, its `Fortify::registerView()` binding in `FortifyServiceProvider`, and the now-permanently-skip-guarded `tests/Feature/Auth/RegistrationTest.php` all removed
- `app/Actions/Fortify/CreateNewUser.php` and its `Fortify::createUsersUsing(...)` binding deliberately retained (per plan) as a likely dependency for Phase 2's `ClientResource`/`UserResource` work

## Task Commits

Each task was committed atomically:

1. **Task 1: Close the sign-up route and remove the login page's dangling link — one atomic change** - `8a4df8d` (feat)
2. **Task 2: Sweep the now-unreachable registration surface out of the repository** - `81a2756` (chore)

_No TDD RED/GREEN split — task 1 authored config/tests/view together per the plan's explicit ordering constraint (feature removal and dangling-link fix must never straddle a commit boundary)._

## Files Created/Modified
- `tests/Feature/Roles/RegistrationClosedTest.php` - New RBAC-04 regression test (3 methods, no skip guard)
- `config/fortify.php` - Removed `Features::registration()`; the other four feature entries (reset passwords, email verification, two-factor, passkeys) untouched
- `resources/views/pages/auth/login.blade.php` - Removed the unguarded sign-up block (lines 54-57); the guarded `password.request` link above it is untouched
- `app/Providers/FortifyServiceProvider.php` - Removed the `Fortify::registerView(...)` binding; the other six view bindings and `configureActions()`/`configureRateLimiting()` untouched
- `resources/views/pages/auth/register.blade.php` - Deleted (unreachable once the route was unregistered)
- `tests/Feature/Auth/RegistrationTest.php` - Deleted (its `skipUnlessFortifyHas()` guard would otherwise leave it permanently, silently skipped)

## Decisions Made
- Followed CONTEXT.md D-04/D-05/D-06 exactly as locked: delete the feature entry outright (not a route guard), delete the skip-guarded test outright (not leave it skipping), add an explicit 404+200 regression test.
- Scope extended one step past CONTEXT.md's literal wording (config line + test file) to also delete the orphaned view and its Fortify binding — this was pre-authorized in the plan itself as a deliberate application of the project's standing "sweep stale references in one pass" rule, not an improvised deviation.

## Deviations from Plan

None - plan executed exactly as written. The two extra file deletions (view + provider binding) in Task 2 were explicitly specified in the plan's `<action>` block, not discovered mid-execution, so they are not tracked as deviations.

**Environment setup performed (not a plan deviation, required to run any verification in this isolated worktree):** `composer install`, `npm install`, and `npm run build` were run because the worktree had no `vendor/`, `node_modules/`, or `public/build/` — none of these are plan-file changes and none were committed (all are gitignored). `package-lock.json` was regenerated by `npm install` with a trivial 1-line `lockfileVersion`-adjacent diff; left unstaged/uncommitted since it wasn't part of this plan's `files_modified` and reverts harmlessly on the next `npm install`.

## Issues Encountered
- Running feature tests required a temporary `APP_KEY` passed as an environment variable to `php artisan test` (no `.env` exists in this fresh worktree, and per this repo's `.claude/settings.local.json`, agents are correctly denied Read/Bash access to `.env*` files). This only affected local verification commands in this session — no `.env` file was created, read, or committed, and no application code depends on this. Same environmental note as plan 01-01's SUMMARY.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness

- RBAC-04 is fully satisfied: `/register` 404s, `/login` renders cleanly, and the regression test is permanent and unskippable.
- Phase 2's `ClientResource`/`UserResource` work can safely reuse `App\Actions\Fortify\CreateNewUser` — it was deliberately left in place and is unreachable from any public route.
- Full `composer run test` gate is green (42/42 tests, 0 skipped, Pint clean, PHPStan level 7 clean) at the end of this plan.

---
*Phase: 01-tested-baseline-role-foundation*
*Completed: 2026-09-01*

## Self-Check: PASSED

- FOUND: tests/Feature/Roles/RegistrationClosedTest.php
- FOUND: resources/views/pages/auth/register.blade.php correctly deleted
- FOUND: commit 8a4df8d
- FOUND: commit 81a2756
