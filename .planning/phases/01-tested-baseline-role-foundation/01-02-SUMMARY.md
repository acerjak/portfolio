---
phase: 01-tested-baseline-role-foundation
plan: 02
subsystem: testing
tags: [phpunit, livewire, mail-fake, http-fake, turnstile, regression]

# Dependency graph
requires:
  - phase: 01-tested-baseline-role-foundation (plan 01)
    provides: spatie/laravel-permission installed, RoleSeeder, HasRoles on User (unrelated to this plan's file, but same phase/wave dependency)
provides:
  - Full regression coverage for the inquiry-form Livewire component (all 4 TEST-01 cases plus both Turnstile branches)
affects: [phase-3-inquiry-model-refactor]

actuals:
  tokens: 1537
  tasks: 2
  commits: 2

tech-stack:
  added: []
  patterns:
    - "Livewire::test('pages::inquiry-form') — flat-file view-namespace component addressing, confirmed against home.blade.php's embed tag"
    - "Backdating a public Livewire property (renderedAt) via ->set() to arrange past a time-trap, rather than modifying the component"
    - "Http::fake() + runtime config() toggle to deliberately exercise an otherwise-dormant conditional branch (Turnstile) without touching production code"

key-files:
  created:
    - tests/Feature/InquiryFormTest.php
  modified:
    - phpunit.xml

key-decisions:
  - "Added APP_KEY to phpunit.xml's <php> env block (Rule 3 auto-fix) — this fresh worktree had no .env (agent sandbox blocks all .env read/write), so the test suite could not boot without an application encryption key. A static test-only key is a standard, non-secret Laravel testing pattern and does not affect production .env handling."

patterns-established:
  - "Turnstile branch test pattern: config(['services.turnstile.secret_key' => 'test-secret']) + Http::fake(['challenges.cloudflare.com/*' => Http::response([...])]) + Http::assertSentCount(1) to prove the branch was genuinely entered, not skipped."

requirements-completed: [TEST-01]

coverage:
  - id: D1
    description: "Valid inquiry-form submission sends InquiryReceived mail and sets sent=true"
    requirement: "TEST-01"
    verification:
      - kind: integration
        ref: "tests/Feature/InquiryFormTest.php#test_valid_submission_sends_mail_and_shows_success"
        status: pass
    human_judgment: false
  - id: D2
    description: "Missing required fields produce validation errors on name/phone/email/body, no mail sent"
    requirement: "TEST-01"
    verification:
      - kind: integration
        ref: "tests/Feature/InquiryFormTest.php#test_validation_errors_are_shown_for_missing_required_fields"
        status: pass
    human_judgment: false
  - id: D3
    description: "Invalid email format is rejected with an error on email only"
    requirement: "TEST-01"
    verification:
      - kind: integration
        ref: "tests/Feature/InquiryFormTest.php#test_an_invalid_email_is_rejected"
        status: pass
    human_judgment: false
  - id: D4
    description: "Over-long body (5001 chars) is rejected with an error on body"
    requirement: "TEST-01"
    verification:
      - kind: integration
        ref: "tests/Feature/InquiryFormTest.php#test_an_over_long_body_is_rejected"
        status: pass
    human_judgment: false
  - id: D5
    description: "Honeypot fill silently reports success to the visitor (sent=true, no errors) while sending no mail"
    requirement: "TEST-01"
    verification:
      - kind: integration
        ref: "tests/Feature/InquiryFormTest.php#test_honeypot_fill_silently_fakes_success_without_sending_mail"
        status: pass
    human_judgment: false
  - id: D6
    description: "Submission within 3 seconds of mount() trips the time trap (error on body), no mail sent"
    requirement: "TEST-01"
    verification:
      - kind: integration
        ref: "tests/Feature/InquiryFormTest.php#test_time_trap_rejects_submissions_faster_than_a_human"
        status: pass
    human_judgment: false
  - id: D7
    description: "Failed Turnstile challenge (siteverify success=false) rejects the submission with an error on turnstileToken, no mail sent"
    verification:
      - kind: integration
        ref: "tests/Feature/InquiryFormTest.php#test_a_failed_turnstile_challenge_rejects_the_submission"
        status: pass
    human_judgment: false
  - id: D8
    description: "Passing Turnstile challenge (siteverify success=true) allows a valid submission through, mail is sent"
    verification:
      - kind: integration
        ref: "tests/Feature/InquiryFormTest.php#test_a_passing_turnstile_challenge_allows_the_submission"
        status: pass
    human_judgment: false

duration: 45min
completed: 2026-09-02
status: complete
---

# Phase 1 Plan 2: Inquiry-Form Regression Tests Summary

**8 PHPUnit/Livewire feature tests covering `inquiry-form`'s valid submission, all validation failures, the honeypot, the time trap, and both Turnstile siteverify branches — zero production code changed.**

## Performance

- **Duration:** 45 min
- **Started:** 2026-09-02T00:00:00Z (approx.)
- **Completed:** 2026-09-02T00:17:13Z
- **Tasks:** 2
- **Files modified:** 2 (1 created, 1 modified for test-env setup)

## Accomplishments
- `tests/Feature/InquiryFormTest.php` created with 8 named, passing tests covering every branch of `inquiry-form`'s `submit()` method
- All four TEST-01 cases (valid submission, validation errors, honeypot, time trap) have dedicated regression tests
- The previously-untested Turnstile verify-or-reject branch is now exercised in both directions with a faked HTTP client, closing the last dead branch in `submit()`
- `resources/views/pages/inquiry-form.blade.php` remains byte-identical to its pre-phase state — no extraction, no refactor, per the phase boundary

## Task Commits

Each task was committed atomically:

1. **Task 1: Cover the happy path and every validation failure of the inquiry form** - `dbe7861` (test)
2. **Task 2: Cover the anti-spam controls — honeypot, time trap, and both Turnstile branches** - `e3b407c` (test)

_Both tasks were tagged `tdd="true"` in the plan, but since this plan tests existing, already-correct component behavior with zero production code changes, there was no RED phase — each test passed on first run against the unmodified component. This is expected for pure regression-coverage work and is consistent with the plan's explicit "writes tests only" objective._

## Files Created/Modified
- `tests/Feature/InquiryFormTest.php` - 8 test methods covering all `inquiry-form` `submit()` branches
- `phpunit.xml` - added a static `APP_KEY` to the `<php>` env block (test-environment fix, see Deviations)

## Decisions Made
- No production-code decisions — this plan is test-only per the phase boundary and CONTEXT.md's phase scope.
- Test fixture data follows the plan's suggested realistic-but-obviously-fake pattern (`Jane Doe`, `555-0100`, `jane@example.com`).
- Set `reason` explicitly to `InquiryReason::General->value` in every test (including the validation-errors test, where it's the only field NOT expected to error, per the plan's note that `reason` defaults to `'general'` and satisfies its `Rule::enum` rule) so no test silently depends on an unstated component default.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 3 - Blocking] Added a static `APP_KEY` to `phpunit.xml`**
- **Found during:** Task 1 (first test run)
- **Issue:** This is a freshly-created git worktree with no `.env` file (worktrees don't inherit gitignored files, and this repo's agent sandbox denies all Read/Bash access to `.env*` files as an intentional security guardrail — confirmed by 01-01-SUMMARY.md's identical finding). Without an application encryption key, every test failed with "No application encryption key has been specified."
- **Fix:** Added `<env name="APP_KEY" value="base64:...."/>` to `phpunit.xml`'s existing `<php>` env block, generated via `php artisan key:generate --show` (which prints a key without touching `.env`). This is a standard, non-secret Laravel testing pattern — CI already generates its own key per-run via `composer setup`, and PHPUnit's env vars take precedence over `.env` regardless, so this has no effect outside the test environment.
- **Files modified:** `phpunit.xml`
- **Verification:** `php artisan test --filter=InquiryFormTest` passed 4/4 immediately after the change; full `composer run test` later confirmed 49/49 passing.
- **Committed in:** `dbe7861` (Task 1 commit)

**2. [Environment setup, not a deviation rule] Installed `vendor/` and `node_modules/`, built frontend assets**
- **Found during:** Task 1 setup and Task 2's `composer run test` verification
- **Issue:** The fresh worktree had no `vendor/` (composer dependencies) and no `public/build/manifest.json` (Vite build output), causing 11 unrelated pre-existing tests (`AuthenticationTest`, `SecurityTest`, `ProfileUpdateTest`, etc.) to fail with `ViteManifestNotFoundException` when the full suite was run.
- **Fix:** Ran `composer install` (restoring exactly what `composer.lock` already pinned — no new packages) and `npm install && npm run build` (restoring exactly what `package-lock.json` already pinned). Neither is a new dependency; both are one-time per-worktree environment setup, not scoped to this plan's `files_modified`, and neither touched a tracked file (`vendor/`, `node_modules/`, `public/build/` are all gitignored).
- **Note:** `npm install` incidentally renamed `package-lock.json`'s top-level `name` field to the worktree's directory name (`agent-a0e34b6...`). This was reverted with `git checkout -- package-lock.json` before committing — not an intended change.
- **Verification:** `composer run test` went from 35/49 passing (11 pre-existing failures, all environment-caused, none in `InquiryFormTest`) to 49/49 passing.

---

**Total deviations:** 1 auto-fixed (Rule 3, blocking) + 1 environment-setup action (not a plan deviation, no persisted repo change beyond the reverted lockfile edit).
**Impact on plan:** Both were necessary purely to get a fresh worktree's test suite running at all; neither touched the inquiry-form component or altered any test's intent. No scope creep.

## Issues Encountered
None beyond the environment-setup items documented above.

## User Setup Required
None - no external service configuration required.

## Next Phase Readiness
- TEST-01 is fully satisfied; the inquiry-form component now has a tested baseline before Phase 3's persistence/event-pipeline refactor begins.
- `resources/views/pages/inquiry-form.blade.php` is unmodified and ready for Phase 3 to safely refactor against this test suite as a regression net.
- No blockers for the next phase.

---
*Phase: 01-tested-baseline-role-foundation*
*Completed: 2026-09-02*

## Self-Check: PASSED

- FOUND: tests/Feature/InquiryFormTest.php
- FOUND: dbe7861 (Task 1 commit)
- FOUND: e3b407c (Task 2 commit)
