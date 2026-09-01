# Codebase Concerns

**Analysis Date:** 2026-09-01

## Tech Debt

**Hardcoded Fallback Email in Config:**
- Issue: `config/inquiry.php` line 15 has a hardcoded email address (`amandacojerean@gmail.com`) as the default fallback when `INQUIRY_TO_EMAIL` env var is not set
- Files: `config/inquiry.php`
- Impact: Personal email address is exposed in version control; difficult to change without code deployment; production inquiries could go to wrong address if env not configured
- Fix approach: Remove the hardcoded email entirely; make `INQUIRY_TO_EMAIL` required in `.env.example` with documentation that inquiries will fail silently (or throw error) if not configured

**Business Logic in View File:**
- Issue: The inquiry form submission logic (validation, spam checking, mail sending) lives in `resources/views/pages/inquiry-form.blade.php` as an anonymous Livewire component (lines 10-95), not in a proper action or service class
- Files: `resources/views/pages/inquiry-form.blade.php` (210 lines including logic and markup)
- Impact: Hard to test in isolation; mixed concerns (presentation + business logic); duplicated validation logic if form changes; difficult to reuse logic for API endpoints
- Fix approach: Extract validation rules to `app/Concerns/InquiryValidationRules.php`; create `app/Actions/SubmitInquiry.php` for form submission logic; keep view file for presentation only

**Placeholder Tests Not Removed:**
- Issue: Two placeholder test files still exist that don't provide meaningful coverage: `tests/Unit/ExampleTest.php` (test that true is true) and `tests/Feature/ExampleTest.php` (test that home route returns 200)
- Files: `tests/Unit/ExampleTest.php`, `tests/Feature/ExampleTest.php`
- Impact: Inflates test count; gives false sense of security; placeholder tests are hard-coded and don't catch actual regressions
- Fix approach: Delete both files; ensure actual feature tests replace them (inquiry form, project listing, auth flows)

**Weak Time-Based Bot Detection:**
- Issue: Inquiry form uses 3-second minimum submission time to detect bots (`resources/views/pages/inquiry-form.blade.php` line 57), but this is a single weak signal
- Files: `resources/views/pages/inquiry-form.blade.php` lines 56-61
- Impact: Sophisticated bots can wait 3+ seconds; honeypot + time trap alone insufficient without CAPTCHA (Turnstile is optional, not required)
- Fix approach: Make Turnstile required, not optional; consider rate limiting by IP (Laravel already supports this); log suspected bot attempts for monitoring

---

## Test Coverage Gaps

**Inquiry Form Completely Untested:**
- What's not tested: The entire contact form submission flow (validation, honeypot, time-trap, Turnstile verification, mail sending, success/error states)
- Files: `resources/views/pages/inquiry-form.blade.php` (no corresponding test in `tests/`)
- Risk: Production inquiries could silently fail; validation could be bypassed; emails sent with incorrect data; form could be spam-attacked without detection
- Priority: **High** - This is the primary public-facing feature

**Mail Sending Untested:**
- What's not tested: `app/Mail/InquiryReceived.php` envelope/content generation; email formatting; proper address handling
- Files: `app/Mail/InquiryReceived.php` (no test in `tests/`)
- Risk: Emails could be formatted incorrectly, sent to wrong address, fail silently in production
- Priority: **High** - Blocks user communication

**Project Listing Not Tested:**
- What's not tested: Project query ordering (is_featured DESC, sort_order ASC) might be wrong; pagination not tested; filtering/searching not tested if implemented
- Files: `app/Http/Controllers/HomeController.php` (minimal test coverage)
- Risk: Featured projects may not sort correctly; sort_order column never used
- Priority: **Medium** - Core feature but only 2-test coverage

**User Enum (InquiryReason) Not Tested:**
- What's not tested: `app/Enums/InquiryReason.php` label() method is untested; if labels change, UI might break
- Files: `app/Enums/InquiryReason.php` (no test)
- Risk: Label display could break; typo in label not caught
- Priority: **Low** - Small enum but should be tested

---

## Security Considerations

**Inquiry Email Address Exposed:**
- Risk: Hardcoded fallback email in `config/inquiry.php` is exposed in version control; could receive spam if repository is public or leaked
- Files: `config/inquiry.php` line 15
- Current mitigation: `.env` override possible, but fallback is unsafe
- Recommendations: Remove hardcoded email; require env var; validate email format at config load time

**Optional CAPTCHA Allows Spam:**
- Risk: Turnstile CAPTCHA is optional (`resources/views/pages/inquiry-form.blade.php` line 186); if config not set, form is vulnerable to automated submission
- Files: `resources/views/pages/inquiry-form.blade.php` lines 63-75, lines 186-196
- Current mitigation: Honeypot and 3-second time trap (weak)
- Recommendations: Make Turnstile required; implement stricter rate limiting; consider requiring auth for inquiry submission

**No Rate Limiting on Inquiry Submission:**
- Risk: No per-IP or per-email rate limiting on inquiry form; attacker can spam unlimited inquiries
- Files: `resources/views/pages/inquiry-form.blade.php` (no rate limiting middleware)
- Current mitigation: Only honeypot + time-trap
- Recommendations: Add rate limiter middleware; limit to 5 inquiries per hour per IP; log submission attempts

**Debug Mode Toggle Exposed:**
- Risk: `config/app.php` line 42 defaults to `APP_DEBUG=false`, but if accidentally set to true in production, stack traces leak sensitive paths and configuration
- Files: `config/app.php`
- Current mitigation: Environment variable required, defaults safely
- Recommendations: Add production environment check to force debug=false; consider CI check

**Email Exposure in Mail Log:**
- Risk: If mail is logged (Laravel logs all mail in debug mode), user email addresses submitted via inquiry form appear in logs
- Files: `app/Mail/InquiryReceived.php` (not sensitive itself, but logging config could expose it)
- Current mitigation: Mail logging depends on Laravel config
- Recommendations: Ensure `MAIL_MAILER=log` never used in production; audit mail configuration in CI

---

## Known Bugs

**Project Sort Order Column Unused:**
- Symptoms: `sort_order` column in `projects` table (migration line 27) exists but `HomeController` doesn't use it for secondary sort (only is_featured primary sort)
- Files: `database/migrations/2026_08_31_055858_create_projects_table.php` line 27; `app/Http/Controllers/HomeController.php` lines 17-19
- Trigger: View home page; projects with same `is_featured` value appear in database insertion order, not `sort_order`
- Workaround: Manually insert projects in desired order

---

## Performance Bottlenecks

**N+1 Query Risk in Project Listing:**
- Problem: If Project model had relationships (user, tags, etc.), `HomeController` query doesn't eager-load them
- Files: `app/Http/Controllers/HomeController.php` line 16
- Cause: Simple `Project::query()` without `->with()` clauses
- Improvement path: Add relationships to Project; use `eager loading` in controller: `Project::with(['user', 'tags'])->orderByDesc('is_featured')`

**No Pagination on Project Listing:**
- Problem: All projects loaded into memory; if portfolio grows to 1000+ projects, response time degrades
- Files: `app/Http/Controllers/HomeController.php` lines 16-19
- Cause: Using `->get()` instead of `->paginate()`
- Improvement path: Implement pagination: `Project::paginate(12)` or lazy load via Livewire component

---

## Fragile Areas

**Inquiry Form Livewire Component:**
- Files: `resources/views/pages/inquiry-form.blade.php`
- Why fragile: Business logic mixed with presentation; validation spread across lines 39-75; multiple state variables (`$name`, `$email`, `$reason`, etc.) that must stay in sync with form fields; Turnstile callback uses global `window.onTurnstileVerified`
- Safe modification: Extract to `app/Livewire/SubmitInquiry.php` class; move validation to dedicated Rules concern; test form state independently from view
- Test coverage: 0 tests; need 8-10 test cases (valid submission, validation failures, honeypot, time-trap, Turnstile failure, success redirect, mail assertion)

**FortifyServiceProvider Configuration:**
- Files: `app/Providers/FortifyServiceProvider.php` lines 46-55 (view bindings using magic route names)
- Why fragile: If a Fortify view route name changes in Fortify core, this provider silently fails to bind it; rate limiting keys are hardcoded and not configurable
- Safe modification: Add validation to ensure all route names exist; make rate limits configurable via config; add logging to detect binding failures
- Test coverage: 0 unit tests for provider; only feature tests via integration tests

**Rate Limiting Configuration:**
- Files: `app/Providers/FortifyServiceProvider.php` lines 62-78
- Why fragile: Rate limits hardcoded (5/min for login, 5/min for 2FA, 10/min for passkeys); no way to adjust without code change; if under attack, must redeploy
- Safe modification: Move rate limits to config file; make configurable per environment; add real-time adjustment via cache
- Test coverage: Limited by integration tests only

---

## Scaling Limits

**No Database Indexing Strategy:**
- Current capacity: Unknown; likely handles <10k projects fine, but no indexes defined
- Files: `database/migrations/2026_08_31_055858_create_projects_table.php` (no `->index()` or `->unique()` beyond `slug`)
- Limit: Query performance degrades as projects table grows; filtering by `category` or `is_featured` becomes slow
- Scaling path: Add indexes: `$table->index('is_featured')`, `$table->index('category')`, `$table->index('slug')`; consider separate admin query for sorting

**Single Inquiry Email Address:**
- Current capacity: All inquiries go to single `INQUIRY_TO_EMAIL`; if email address bounces or is unavailable, inquiries are lost
- Limit: No failover; no retry logic; no dead letter queue
- Scaling path: Store inquiries in database table before sending email; retry failed sends; add fallback email; implement inquiry dashboard

**No Message Queue for Mail:**
- Current capacity: Mail sends synchronously; if Turnstile/email server is slow, form submission blocks user
- Limit: Form could timeout under load; user experience degrades
- Scaling path: Enable `QUEUE_CONNECTION=redis/sqs`; move mail to queue; implement async mail sending

---

## Dependencies at Risk

**Livewire Blaze Component Library:**
- Risk: `livewire/blaze` (v1.0) is very new, still may have breaking changes; limited community resources
- Files: `composer.json` line 17
- Impact: If Blaze is abandoned or has critical bugs, auth UI components break; settings pages depend on Blaze components
- Migration plan: Components are built in Blade files; could migrate to Flux (stable) or custom Blade components if needed

**Flux (Livewire UI Kit) v2:**
- Risk: `livewire/flux` (v2.13.1) is recent; may have breaking updates
- Files: `composer.json` line 18; settings pages use Flux components
- Impact: Dashboard and settings pages depend on Flux; UI could break with major version update
- Migration plan: Maintain composer.lock pin; subscribe to Flux releases; test major updates in CI before deploying

**Laravel Fortify with Passkeys:**
- Risk: Passkey support is new in Fortify (v1.37.2); security implications not fully audited by community
- Files: `composer.json` line 14; `app/Models/User.php` line 15
- Impact: Passkey implementation could have vulnerabilities; WebAuthn standard still evolving
- Migration plan: Monitor Fortify security advisories; test passkey flow regularly; have fallback auth method

**Tailwind CSS v4:**
- Risk: Tailwind 4.0 is relatively new; oxide compiler (Rust-based) may have edge cases
- Files: `package.json` line 14; build depends on Tailwind v4 features
- Impact: Build could fail; CSS could be generated incorrectly
- Migration plan: Keep `tailwindcss` pinned to known-good version; test build in CI

---

## Missing Critical Features

**No Admin Panel for Project Management:**
- Problem: Projects can only be managed via direct database access or Laravel Tinker; no admin UI for CRUD operations
- Blocks: Portfolio owners can't add/edit/delete projects without SSH/database access; no sorting UI for `sort_order`
- Impact: Adding a new project requires code deployment or direct DB manipulation
- Solution path: Build Filament admin panel (aligns with Laravel ecosystem); create Project resource with sort, search, filter

**No Inquiry Storage/Dashboard:**
- Problem: Inquiries are sent via email only; no persistence; no way to track, reply, or follow up
- Blocks: Can't view inquiry history; can't categorize inquiries; can't reply from app
- Impact: Inquiries could be lost if email fails; can't analyze inquiry trends
- Solution path: Create `inquiries` table; store inquiry before/after sending email; build inquiry dashboard

**No Rate Limiting Monitoring:**
- Problem: Rate limiter is configured but not monitored; no alerts if user is throttled
- Blocks: Can't detect brute force attacks in real time
- Impact: Silent attacks; no visibility into login attempts
- Solution path: Log rate limit hits; send alert when IP throttled repeatedly; implement honeypot for admin panel

---

## Architectural Concerns

**View File as Livewire Component:**
- Issue: `resources/views/pages/inquiry-form.blade.php` is an inline Livewire component class (lines 1-95) rather than a separate class in `app/Livewire/`
- Impact: Harder to find; harder to test; view/logic not separated; can't type-hint in other code
- Fix: Move component class to `app/Livewire/SubmitInquiry.php`; update view to `resources/views/livewire/submit-inquiry.blade.php`

**No Service Container Usage:**
- Issue: Mail, config, validation accessed directly via facades (`Mail::to()`, `config()`) rather than injected
- Impact: Harder to mock in tests; tightly coupled to Laravel framework; difficult to swap implementations
- Fix: Inject `MailerContract`, `ConfigRepository` into component; use dependency injection

---

*Concerns audit: 2026-09-01*
