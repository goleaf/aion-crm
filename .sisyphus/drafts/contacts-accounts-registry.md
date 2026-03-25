# Draft: Contacts & Accounts Registry

## Requirements (confirmed)
- Goal: Central registry of all people and organizations you interact with.
- Account fields: account_id, name, industry, type, website, phone, email, billing_address, shipping_address, annual_revenue, employee_count, owner_id, parent_account_id, created_at, tags.
- Contact fields: contact_id, first_name, last_name, email, phone, mobile, job_title, department, account_id, owner_id, lead_source, do_not_contact, birthday, preferred_channel, notes.
- Relationship: Contact → Account (Many-to-One).
- Relationship targets noted: Contact → Deals, Contact → Activities, Contact → Cases.

## Technical Decisions
- Delivery scope: Domain + API + UI in this release.
- Integration depth: Full integration now for Contact links to Deals, Activities, and Cases.
- Test strategy baseline: TDD-aligned planning with unit/functional/integration coverage.
- Deduplication scope: Include full duplicate detection + merge workflows now.
- Validation strictness: Pragmatic required set; non-critical fields remain nullable.
- Enum governance: Admin-configurable taxonomy (DB-managed), not hard-coded fixed enums.
- Account hierarchy behavior: Link-only parent-child relation (no roll-up/inheritance automation).
- UI scope detail: Full CRUD + merge UX for Accounts and Contacts.
- Full integration interpretation: Build Deals, Activities, and Cases domains now in the same release.
- Module topology: Split into dedicated modules (Accounts, Contacts, Deals, Activities, Cases) rather than extending only CRM module.
- Test workflow: Strict TDD (Red-Green-Refactor).
- Merge survivorship default: User-selected primary record wins by default; manual overrides for conflicting scalar values.
- Taxonomy scope: Admin-configurable values are global across the system.

## Research Findings
- Test framework confirmed: PHPUnit 12 + ParaTest + sqlite in-memory (`composer.json`, `phpunit.xml.dist`).
- Test architecture confirmed: `UnitTestCase` + `FunctionalTestCase`; integration tests use `FunctionalTestCase` conventions (`tests/Support/TestCases/*`).
- Suite/naming conventions confirmed: `*UnitTest.php`, `*FunctionalTest.php`, `*IntegrationTest.php` with mirrored directories (`phpunit.xml.dist`).
- Representative references captured: `tests/App/Modules/Auth/Actions/LoginActionFunctionalTest.php`, `tests/Integration/Http/Api/Auth/LoginIntegrationTest.php`.
- Module/transport patterns confirmed: Action + DataTransferObjects + invokable controllers + FormRequest + routes in `app/Http/Api/routes/v1.php` (references: `app/Modules/Auth/*`, `app/Http/Api/Auth/*`, `app/Http/Api/routes/v1.php`).
- Domain baseline confirmed: no pre-existing account/contact tables; this is net-new CRM domain surface.
- UI pattern confirmed: Livewire 4 + Blade page wrappers + Tailwind 4; routes in `app/Http/Web/routes/web.php`; components in `app/Livewire/{Domain}` and `resources/views/livewire/{domain}`.
- Integration baseline confirmed: Deals/Activities/Cases domains do not currently exist (no models, migrations, routes); only CRM foundation assets are present (`app/Modules/CRM/*`).

## Open Questions
- None currently blocking plan generation.

## Scope Boundaries
- INCLUDE: Account + Contact models/logic, API transport, UI surface, and full links into Deals/Activities/Cases.
- EXCLUDE: Advanced account-hierarchy roll-up behavior unless explicitly requested.
