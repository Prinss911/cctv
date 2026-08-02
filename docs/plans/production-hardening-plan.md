# Production Hardening Plan — Bayu CCTV PHP App

## Context

This plan is based on the recent audit of the Bayu CCTV / Bintang CCTV PHP application after frontend/backend optimization and NAS deployment. The app is currently functional and live, but it is **not yet production-hardened**. The main risks are in authentication/reset flows, admin CRUD ordering correctness, URL-based upload security, CSP alignment, proxy-aware session handling, and lack of regression coverage.

This document is meant to be used by a planning/execution agent as the source of truth for phased remediation work.

---

## Objectives

1. Remove immediate takeover and auth-flow risks.
2. Fix admin CRUD correctness issues introduced or exposed by pagination.
3. Eliminate or harden SSRF-capable upload behavior.
4. Make session/cookie security correct behind NAS / reverse proxy deployment.
5. Align CSP with actual runtime features while reducing inline-script dependency.
6. Add enough automated verification to safely redeploy changes.

---

## Non-goals

- Full UI redesign
- Large architectural rewrite
- Replacing the framework/runtime entirely
- Enterprise observability stack rollout
- WAF / network perimeter implementation outside repository scope

---

## Workstreams

### WS-1: Auth & Reset Hardening
Scope:
- seeded/default credential risk
- logout GET → POST
- password reset token hashing
- removal of production token-display paths
- forgot/reset abuse throttling

### WS-2: Admin CRUD Correctness
Scope:
- pagination vs reorder corruption risk
- CSRF rotation vs AJAX reorder
- page validation/clamping
- fetch error handling for admin AJAX

### WS-3: Upload & External Resource Security
Scope:
- URL upload SSRF mitigation or feature removal
- SVG policy
- remote image strategy

### WS-4: Session / CSP / Frontend Hardening
Scope:
- proxy-aware secure cookies
- CSP cleanup
- inline JS removal
- map/embed policy alignment

### WS-5: Testing & Release Verification
Scope:
- auth/reset/upload/access-control coverage
- admin CRUD smoke/E2E verification
- deploy verification checklist

---

## Phase 0 — Documentation & Code Pattern Discovery

### Goal
Collect the exact current patterns from the codebase before changing behavior.

### Source files to consult
- `bootstrap/app.php`
- `app/Helpers/Auth.php`
- `app/Helpers/Csrf.php`
- `app/Helpers/Upload.php`
- `app/Helpers/Router.php`
- `app/Controllers/Admin/AuthController.php`
- `app/Controllers/Admin/PasswordResetController.php`
- `app/Models/PasswordResetModel.php`
- `app/Models/BaseModel.php`
- `public/assets/js/admin.js`
- `resources/views/layouts/main.php`
- `resources/views/layouts/admin.php`
- `resources/views/partials/pagination.php`
- `resources/views/partials/crud-index.php`
- `routes/admin.php`
- `database/seeds/DatabaseSeeder.php`

### What to confirm
- Current CSRF verification behavior for form and JSON requests
- Current login/logout route behavior
- Password reset token generation, storage, and verification
- Reorder flow and its dependency on current-page IDs
- URL upload behavior and call sites
- All remaining inline script and inline event-handler locations
- Current session cookie / HTTPS detection logic

### Deliverables
- Exact file-by-file map of affected auth/reset/upload/reorder/CSP flows
- List of allowed existing helpers/APIs to reuse
- Final decision note for unresolved design forks before implementation begins

### Verification
- Grep all target helpers/controllers/routes before implementing changes
- Confirm all mutating admin routes use auth + CSRF middleware where expected

### Anti-pattern guards
- Do not invent new framework helpers before checking existing ones
- Do not change behavior globally without mapping all call sites first

---

## Phase 1 — Emergency Production Safeguards

### Goal
Close the fastest production takeover and unsafe-flow risks.

### Files
- `database/seeds/DatabaseSeeder.php`
- `app/Controllers/Admin/AuthController.php`
- `resources/views/layouts/admin.php`
- `routes/admin.php`
- `app/Controllers/Admin/PasswordResetController.php`

### Tasks
1. Remove seeded/default admin credential risk.
2. Convert logout from GET to POST.
3. Remove any production path that displays reset links/tokens to the UI.
4. Add explicit production guardrails for dev-only reset behavior.

### Acceptance criteria
- No admin logout via GET remains.
- No `_csrf=` logout URL remains in views.
- No production branch can display a reset token/link.
- Seeder no longer encourages predictable credentials for real environments.

### Verification
- Grep for `/admin/logout` route and confirm POST-only behavior.
- Grep for `_csrf=` in URLs and confirm removal.
- Grep password reset controller for token-display behavior and confirm it is disabled or gated correctly.

### Anti-pattern guards
- Do not keep GET logout as a fallback.
- Do not merely hide reset output in the UI while leaving the backend path active.

---

## Phase 2 — Password Reset Hardening

### Goal
Make password reset safe for production.

### Files
- `app/Models/PasswordResetModel.php`
- `app/Controllers/Admin/PasswordResetController.php`
- password reset migration files in `database/migrations/`
- `app/Helpers/Auth.php` (for throttling pattern reference)

### Tasks
1. Replace plaintext token storage with hashed token storage.
2. Update issue/verify/reset flow to use secure comparison.
3. Invalidate previous tokens for the same user/email.
4. Enforce single-use + expiry + cleanup.
5. Add forgot/reset request throttling.
6. Ensure responses do not leak whether an email exists.

### Acceptance criteria
- Raw reset tokens are no longer stored in the DB.
- Expired or used tokens are rejected.
- Repeated forgot-password abuse is throttled.
- Responses are generic and non-enumerating.

### Verification
- Grep `WHERE token = ?` in reset model and remove raw-token lookup.
- Test valid, invalid, expired, and used token scenarios.
- Test repeated reset requests from same IP/email.

### Anti-pattern guards
- Do not log raw reset tokens.
- Do not leak account existence in error messages.

---

## Phase 3 — Admin CRUD Correctness: Pagination vs Reorder

### Goal
Prevent ordering corruption now that admin lists are paginated.

### Files
- `public/assets/js/admin.js`
- `resources/views/partials/crud-index.php`
- `resources/views/partials/pagination.php`
- paginated admin controllers (`SliderController`, `GalleryController`, `FaqController`, etc.)
- any model implementing `updateSortOrder(...)`

### Tasks
Choose one supported design and implement it consistently:

#### Preferred design
Disable reorder on paginated lists, and move ordering to a dedicated mode/screen if needed.

Implementation tasks:
1. Identify every list that is both paginated and reorderable.
2. Disable drag handle / Sortable behavior when pagination is active.
3. Optionally add a dedicated “manage order” mode that loads all relevant items.
4. Ensure backend reorder endpoints cannot corrupt global ordering from subset-page input.

### Acceptance criteria
- Paginated lists cannot silently corrupt sort order.
- Reorder is either disabled on paginated views or moved to a dedicated safe flow.
- Public/frontend ordering remains stable after admin operations.

### Verification
- Manual test across multiple pages.
- Confirm reorder endpoint no longer uses current-page subset in an unsafe way.

### Anti-pattern guards
- Do not leave drag UI enabled if backend semantics are invalid.
- Do not patch only JS while backend still accepts unsafe reorder input.

---

## Phase 4 — CSRF Rotation vs AJAX Stability

### Goal
Keep CSRF protection while making repeated AJAX actions reliable.

### Files
- `app/Helpers/Csrf.php`
- `app/Middleware/CsrfMiddleware.php`
- `public/assets/js/admin.js`

### Tasks
1. Decide the final token lifecycle policy for AJAX:
   - stable session token with expiry, or
   - rotate token and always return a fresh token in JSON responses.
2. Update AJAX handlers to send token consistently.
3. Update reorder/bulk-action responses to include a fresh token if rotation remains.
4. Add explicit client-side handling for CSRF failures.

### Acceptance criteria
- Multiple reorder/bulk AJAX actions can succeed without page refresh.
- CSRF failure is explicit and recoverable.
- Token state remains in sync between server and browser.

### Verification
- Perform several consecutive AJAX actions in one session.
- Force invalid-token scenario and confirm clear failure handling.

### Anti-pattern guards
- Do not rotate server token without updating the client token.
- Do not return HTML error pages to JSON consumers.

---

## Phase 5 — Upload Hardening & SSRF Mitigation

### Goal
Eliminate SSRF exposure from URL-based upload or reduce it to an acceptable risk.

### Files
- `app/Helpers/Upload.php`
- controllers using `Upload::handleFromUrl(...)`
- `bootstrap/app.php`
- any public-facing views that render uploaded resource URLs

### Tasks
Preferred approach:
1. Disable URL-based upload in production.

If the feature must remain:
2. Add hostname/IP validation against localhost/private/link-local ranges.
3. Add strict redirect limits and network timeouts.
4. Remove or sanitize SVG support for URL mode.
5. Decide whether remote assets are proxied/downloaded locally or explicitly allowlisted.

### Acceptance criteria
- Local/private/internal URLs are rejected.
- SVG risk is removed or tightly controlled.
- Runtime asset policy and CSP are aligned.

### Verification
- Test localhost/private IP URLs and ensure rejection.
- Test legitimate public image URLs under the final policy.

### Anti-pattern guards
- Do not rely only on string regex checks.
- Do not allow remote fetches to resolved private IPs.

---

## Phase 6 — Session & Reverse Proxy Hardening

### Goal
Make cookies and session behavior correct under NAS / reverse proxy HTTPS deployment.

### Files
- `bootstrap/app.php`
- environment/config files relevant to app URL / proxy assumptions

### Tasks
1. Add proxy-aware HTTPS detection.
2. Ensure `Secure` cookie behavior is correct in production.
3. Document the trust model for forwarded headers.
4. Verify HSTS and document-root assumptions.

### Acceptance criteria
- Production HTTPS traffic yields `Secure` session cookies.
- Local HTTP development still works with intended config.
- Reverse proxy assumptions are explicit and testable.

### Verification
- Inspect session cookie attributes in deployed/proxied environment.
- Confirm login/logout still work after proxy-aware changes.

### Anti-pattern guards
- Do not trust arbitrary forwarded headers without a defined proxy boundary.

---

## Phase 7 — CSP Alignment & Inline JS Removal

### Goal
Reduce XSS attack surface and align CSP with real features.

### Files
- `bootstrap/app.php`
- `resources/views/layouts/main.php`
- `resources/views/layouts/admin.php`
- `resources/views/partials/nav.php`
- `resources/views/home/index.php`
- `resources/views/admin/advantages/create.php`
- `resources/views/admin/advantages/edit.php`
- `resources/views/partials/footer.php`

### Tasks
1. Inventory all inline `<script>` and inline event handlers.
2. Move them into `public/assets/js/app.js` or `public/assets/js/admin.js`.
3. Revisit font/map/image behavior after the move.
4. Tighten CSP:
   - reduce or remove `unsafe-inline` for scripts if possible
   - add `frame-src` only if maps/embed remains supported
   - align `img-src` with the final upload/image policy

### Acceptance criteria
- Inline JS/event usage is substantially reduced or eliminated in target areas.
- CSP violations are resolved for supported features.
- Theme toggle, FAQ, admin utilities, and maps/image behavior still work.

### Verification
- Grep for `onclick=`, `onload=`, inline `<script>` in targeted views.
- Check browser console for CSP violations.

### Anti-pattern guards
- Do not widen CSP sources without a documented feature need.
- Do not leave dead inline handlers after moving logic.

---

## Phase 8 — Tests & Release Verification

### Goal
Add enough regression coverage to redeploy with confidence.

### Scope
- auth
- logout POST
- password reset
- upload restrictions
- access control
- admin pagination/reorder behavior
- frontend/admin smoke checks

### Tasks
1. Add backend tests for auth/reset/upload/access-control flows.
2. Add smoke/E2E tests for homepage, admin login, logout, paginated CRUD pages, and ordering behavior.
3. Add deploy verification checklist/script.

### Acceptance criteria
- High-risk flows have executable verification.
- Manual-only verification is no longer the only safety net.

### Verification
- Run backend tests.
- Run frontend/admin smoke checks.
- Record deploy verification outputs.

### Anti-pattern guards
- Do not settle for 200-only tests without behavior assertions.
- Do not skip tests around auth/reset/upload changes.

---

## Final Verification Phase

### Security checklist
- [ ] No seeded default admin credential risk remains.
- [ ] Logout is POST-only.
- [ ] Reset tokens are hashed at rest.
- [ ] Production reset flow never displays raw tokens/links.
- [ ] URL upload is disabled or SSRF-hardened.
- [ ] Session cookie is `Secure` under reverse proxy HTTPS.
- [ ] CSP matches actual supported runtime features.

### Correctness checklist
- [ ] Paginated CRUD views cannot corrupt ordering.
- [ ] AJAX admin actions remain stable across repeated requests.
- [ ] Invalid/out-of-range page inputs behave safely.

### Deployment checklist
- [ ] NAS/container deployment passes smoke checks.
- [ ] Document root points to `public/`.
- [ ] Runtime/session/log artifacts are not web-accessible.

### Regression checklist
- [ ] Auth/reset/upload/access-control tests pass.
- [ ] Admin CRUD smoke tests pass.

### Anti-pattern sweep
- Grep for default passwords, GET logout, `_csrf=` in URLs, raw token lookup, inline handlers/scripts, and unsafe `handleFromUrl(...)` usage that conflicts with the final policy.

---

## Risks

- Pagination/reorder changes can alter admin workflows and may require UX messaging or a dedicated ordering page.
- Reset-token schema changes may require migration handling for existing reset rows.
- CSP tightening may temporarily break supported features if rollout is not verified with browser testing.
- Proxy-aware cookie changes must be tested carefully against the NAS deployment topology.

---

## Rollback Strategy

1. Keep current container image/tag or project copy before changes.
2. Apply changes by phase, not all at once.
3. After each phase, run targeted verification before moving on.
4. If a phase breaks admin auth/reset behavior, rollback only that phase’s code and redeploy.
5. Preserve DB backup before any password-reset schema/data migration.

---

## Open Questions

1. Is password reset required in production right now, or can it be disabled until mail delivery is implemented?
2. Is URL-based upload truly required in production, or can it be removed entirely?
3. Should ordering be available from normal paginated CRUD views, or is a dedicated “manage order” screen acceptable?
4. What reverse proxy / HTTPS topology is used in front of the NAS deployment?
5. Is there any additional production admin protection (VPN, IP allowlist, auth gateway, basic auth, WAF) outside the repo?
