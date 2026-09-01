# Amanda Cojerean — Portfolio: Demos & Client Access

## What This Is

A personal Laravel/Livewire portfolio site showcasing Amanda's UCI Coding Bootcamp and solo projects. This milestone adds a "Demos" section: original, from-scratch recreations of the *type* of features Amanda has built professionally (appointment booking, a filterable calendar) — built with zero company code, data, or branding so they demonstrate real skill without any IP/confidentiality risk. Access is gated behind accounts Amanda personally creates for prospective clients, managed through a new Filament admin panel.

## Core Value

Prospective clients can experience real, interactive demonstrations of Amanda's professional-caliber work through a controlled-access system — without exposing any proprietary code, data, or branding, and without any public URL revealing the demos exist beyond the homepage teaser.

## Business Context

- **Customer**: Prospective freelance/employment clients who see the portfolio and want proof of professional-level work
- **Revenue model**: Indirect — the portfolio drives freelance and employment opportunities; demos are a trust-building/conversion tool, not monetized directly
- **Success metric**: Inquiries with reason "Demo access" that convert into a client account created via `ClientResource`
- **Strategy notes**: See `docs/specs/demos-page.md` for the full agreed spec

## Requirements

### Validated

- ✓ Public homepage — Hero, Projects (seeded `Project` model), About, Contact sections — existing
- ✓ `inquiry-form` Livewire component — honeypot + time-trap + optional Cloudflare Turnstile spam protection; currently emails directly via the `InquiryReceived` mailable (no persistence yet) — existing
- ✓ Authenticated account area — Fortify-based login/register/password-reset/2FA/passkeys, profile & security settings — existing
- ✓ Naming already migrated ahead of this milestone: `contact-form` → `inquiry-form`, `ContactReason` → `InquiryReason`, `ContactFormSubmitted` → `InquiryReceived` (Mail), `config/contact.php` → `config/inquiry.php`

### Active

- [ ] **Feature test coverage for `inquiry-form`** — the one existing custom form with no test coverage today; establish this as the clean baseline before new work begins
- [ ] Roles via `spatie/laravel-permission` — `founder` / `admin` / `client`
- [ ] Filament `^5.0` admin panel (required for Livewire `^4.1` compatibility — v3/v4 only support Livewire `^3.5`), panel access restricted to `founder`/`admin`
- [ ] `ProjectResource` — manage existing `Project` model; add `SoftDeletes` migration (doesn't have one today)
- [ ] `Inquiry` model + migration (name, company, phone, email, reason, body, status enum, `reviewed_at`, soft deletes) and `InquiryResource`
- [ ] `App\Events\InquiryReceived` event + listener — replaces the direct `Mail::send()` call currently inline in `inquiry-form`, reusing the existing mailable
- [ ] `UserResource` (all users, any role) and `ClientResource` (scoped to `client` role, auto-assigns role + generates password on create) — `ClientResource` supersedes the old "Artisan command" idea entirely
- [ ] Homepage "Demos" section (teaser cards, styled like Projects) + nav link to `/demos`
- [ ] Auth-gated `/demos`, `/demos/appointments`, `/demos/calendar` — no logged-out-friendly content on any of them; guests redirect to `/contact?from=demos`
- [ ] Standalone `/contact` page (not in nav) reusing `inquiry-form`; conditional banner/headline based on `?from=demos` flag
- [ ] `InquiryReason::DemoAccess` case, defaulted on the `/contact?from=demos` flow
- [ ] Appointment booking demo — `DemoBooking` model, 15-min/1-hour duration picker, duration-aware slot availability, 9am–5pm Mon–Fri, open-ended booking window, pre-seeded taken slots
- [ ] Calendar demo — `DemoEvent` model, 8 categories, ~40–50 seeded events across a 3-month window, live filter + search-as-you-type
- [ ] Demo GIFs for homepage teaser cards (same pattern as existing project GIFs in `public/images/projects/`)

### Out of Scope

- Public self-registration (`/register` flow disabled) — Amanda is the only one who creates accounts, via Filament
- Artisan command for user creation — replaced entirely by `ClientResource`
- A dedicated public `/demos` gate page with real content — superseded; the homepage is the *only* public surface allowed to mention demos at all
- Apple Calendar / CalDAV sync — future consideration only; don't design anything today that would preclude it later

## Context

- Existing Laravel 13 / Livewire 4.1 / Tailwind 4 / Fortify portfolio site — see `.planning/codebase/` for the full architecture, stack, conventions, testing, and concerns maps
- `docs/specs/demos-page.md` is the authoritative, already-agreed spec for this milestone — treat it as source of truth over any paraphrase here
- Full spec text also carries three standing project rules that apply to *all* future work, not just this milestone: reuse-before-building (reconsider naming when a component is reused in a new context), every admin-managed model gets a Filament resource, and comprehensive rename sweeps (grep the whole repo — code, docs, comments, copy — in the same pass as any rename)

## Constraints

- **Tech stack**: `filament/filament:^5.0` specifically (not v3/v4) — only v5 requires/supports Livewire `^4.1`, which this project already runs; verified live against Packagist
- **Access model**: No public self-registration; every account (staff or client) is created by Amanda through Filament
- **IP/confidentiality**: Demos must be original recreations only — zero company code, data, branding, or identifiable proprietary patterns
- **Security**: Every admin-managed model gets `SoftDeletes`; force-delete (permanent removal) is `founder`-only, never `admin`

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Filament `^5.0`, not v3/v4 | Only v5 supports Livewire `^4.1`, which this project already runs | — Pending |
| Redirect to `/contact` instead of a public `/demos` gate page | Even a contentless gate page still reveals demos exist at a dedicated public URL; homepage should be the only public surface that mentions demos | — Pending |
| `spatie/laravel-permission` for roles | Gives `assignRole`/`hasRole` + route/Blade helpers for free, room for granular permissions later, instead of a plain role column | — Pending |
| `ClientResource` (not an Artisan command) for client onboarding | Keeps account creation inside the admin UI Amanda already uses; auto-assigns the role and generates a password in one step | — Pending |
| Feature-test `inquiry-form` before starting new work | Establishes a clean, tested baseline so phases don't accumulate cleanup debt | — Pending |

## Evolution

This document evolves at phase transitions and milestone boundaries.

**After each phase transition** (via `/gsd-transition`):
1. Requirements invalidated? → Move to Out of Scope with reason
2. Requirements validated? → Move to Validated with phase reference
3. New requirements emerged? → Add to Active
4. Decisions to log? → Add to Key Decisions
5. "What This Is" still accurate? → Update if drifted

**After each milestone** (via `/gsd-complete-milestone`):
1. Full review of all sections
2. Core Value check — still the right priority?
3. Audit Out of Scope — reasons still valid?
4. Update Context with current state

---
*Last updated: 2026-09-01 after initialization*
