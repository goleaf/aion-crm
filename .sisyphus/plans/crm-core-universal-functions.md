# CRM Core Universal Functions & Features

## TL;DR
> **Summary**: Build the CRM as a sequenced modular program on top of the current Laravel app, starting with a durable customer graph and ownership model rather than attempting the full universal CRM in one implementation wave.
> **Deliverables**:
> - CRM foundation modules and conventions
> - Accounts, Contacts, Activities, Leads, Deals, Email & Communications, Campaigns, Cases, Products, Quotes, Reports, Dashboards, and Integration seams in defined rollout order
> - Livewire/Tailwind admin UI slices aligned with the existing repo structure
> - Test coverage for API, Livewire, and action-level behavior per module
> **Effort**: XL
> **Parallel**: YES - 5 waves
> **Critical Path**: Foundation/roles/teams → Accounts/Contacts → Activities → Leads → Deals → Email/Communications → Campaigns → Cases/Products/Quotes → Dashboards/Reports/Integrations

## Context
### Original Request
Build a universal CRM covering Contacts & Accounts, Leads, Deals, Activities, Email & Communications, Products, Quotes, Marketing Campaigns, Support Cases, Reports & Dashboards, Users/Roles/Permissions, and Integrations, following the supplied fields and relations.

## CRM Module Breakdown
| Module | Purpose | Core Fields | Primary Relations |
|--------|---------|-------------|-------------------|
| Accounts | Central registry of companies/organizations | `account_id`, `name`, `industry`, `type`, `website`, `phone`, `email`, `billing_address`, `shipping_address`, `annual_revenue`, `employee_count`, `owner_id`, `parent_account_id`, `tags`, timestamps | belongs to owner `User`; may belong to parent `Account`; has many `Contacts`, `Deals`, `Cases`, `Activities` |
| Contacts | Registry of individual people | `contact_id`, `first_name`, `last_name`, `email`, `phone`, `mobile`, `job_title`, `department`, `account_id`, `owner_id`, `lead_source`, `do_not_contact`, `birthday`, `linkedin_url`, `preferred_channel` | belongs to `Account`; belongs to owner `User`; links to `Deals`, `Activities`, `Cases`, `Emails` |
| Leads | Intake/qualification records before conversion | `lead_id`, `first_name`, `last_name`, `company`, `email`, `phone`, `lead_source`, `status`, `score`, `rating`, `campaign_id`, `owner_id`, conversion target ids/timestamps, `description` | belongs to owner `User`; may belong to `Campaign`; converts into `Account`, `Contact`, and optional `Deal` |
| Deals / Opportunities | Sales pipeline and revenue tracking | `deal_id`, `name`, `account_id`, `contact_id`, `owner_id`, `stage`, `amount`, `currency`, `probability`, `expected_revenue`, `close_date`, `deal_type`, `pipeline_id`, `lost_reason`, `source` | belongs to `Account`; may belong to primary `Contact`; belongs to owner `User`; has many `Activities`, `Quotes`, `Products` via line items |
| Activities | Shared interaction log for work and communication events | `activity_id`, `type`, `subject`, `description`, `status`, `priority`, `due_date`, `duration_minutes`, `outcome`, related subject link fields, `owner_id`, attendees | belongs to owner `User`; attaches to `Account`, `Contact`, `Lead`, `Deal`, `Case`; may have attendees `Users`/contacts |
| Email & Communications | First-class communication history and threading | `email_id`, `from`, `to`, `cc`, `bcc`, `subject`, `body_html`, `body_text`, `sent_at`, `opened_at`, `clicked_at`, `status`, `thread_id`, `template_id` | links to `Contact`, `Deal`, `Case`, optional `Campaign`; may use `Template`; appears in communication timelines |
| Products | Catalog of sellable items/services | `product_id`, `name`, `sku`, `description`, `unit_price`, `currency`, `category`, `tax_rate`, `active`, `recurring`, `billing_frequency` | used by `Deals` and `Quotes` through line items |
| Quotes | Formal pricing documents derived from pipeline work | `quote_id`, `name`, `deal_id`, `contact_id`, `status`, `valid_until`, `line_items`, `subtotal`, `discount`, `tax`, `total`, `notes`, `signed_at` | belongs to `Deal`; may belong to `Contact`; contains `Products` snapshots via quote items |
| Campaigns | Marketing planning, attribution, and ROI tracking | `campaign_id`, `name`, `type`, `status`, `start_date`, `end_date`, `budget`, `actual_cost`, `target_audience`, `expected_leads`, `actual_leads`, `revenue_generated`, `owner_id` | belongs to owner `User`; has many `Leads`; may link to `Contacts` and `Emails` |
| Cases | Post-sale support and service workflow | `case_id`, `title`, `description`, `status`, `priority`, `type`, `contact_id`, `account_id`, `owner_id`, `sla_deadline`, `first_response_at`, `resolved_at`, `satisfaction_score`, `channel` | belongs to `Account`; links to one-or-more `Contacts`; belongs to owner `User`; has many `Activities` and `Emails` |
| Reports | Structured analytics and exportable metrics | `report_id`, `name`, `type`, `module`, `filters`, `group_by`, `metrics`, `date_range`, `scheduled`, `owner_id`, `shared_with` | derives from `Deals`, `Leads`, `Activities`, `Cases`, `Quotes`, `Campaigns`; belongs to owner `User`; may be shared with many users |
| Dashboards | User-facing KPI and summary views | dashboard cards/widgets for pipeline, conversion, activities, cases, campaigns, quote totals | consumes `Reports`/read models and links users into operational lists/details |
| Users / Roles / Permissions / Teams | Access control, assignment, and visibility | `user_id`, `name`, `email`, `role_id`, `team_id`, `is_active`, `last_login`, `quota`; role capabilities such as `can_view`, `can_create`, `can_edit`, `can_delete`, `can_export`, `record_visibility` | `User` owns/gets assigned Accounts, Contacts, Leads, Deals, Activities, Campaigns, Cases, Reports; belongs to `Team`; uses `Role` visibility rules |
| Integrations | External-system connection seams and sync contracts | integration credentials/config, external ids, webhook/event payload contracts, import/export mappings | connects CRM entities to email/calendar/ERP/payments/help desk/chat/no-code tools without making them core records |


### Interview Summary
- The request is being treated as a whole-program CRM plan, not a one-shot implementation.
- Current repo baseline only contains `Auth`, `Users`, and `Shared` modules; no CRM domain exists yet.
- Livewire 4 and Tailwind 4 are available, so the first CRM slice can include UI, but only as thin vertical pages.
- Default applied: `single-workspace first, SaaS later`, meaning v1 must preserve a future tenant seam without introducing tenant tables in the first slice.
- Default applied: sales pipeline comes before support, while email sync automation and external third-party adapters are deferred until the core customer graph stabilizes; first-party email logging, campaigns, dashboards, and integration seams still belong in the program plan.

### Metis Review (gaps addressed)
- Guard against scope creep by separating CRM program waves from the first executable slice.
- Pin ownership, visibility, and UI test selectors before expanding modules.
- Keep Activities as shared infrastructure, but avoid unconstrained polymorphism.
- Defer reporting and integrations until lifecycle/state models stop moving.

## Work Objectives
### Core Objective
Create a decision-complete implementation plan for a modular CRM program that fits the current Laravel architecture and can be executed incrementally without redesigning core entities later.

### Deliverables
- CRM foundation blueprint with module boundaries
- Sequenced implementation tasks for each major CRM capability
- Test strategy per module using existing repo conventions
- Livewire/Tailwind page strategy for CRM lists, forms, and detail pages
- Explicit deferral list for non-v1 capabilities
- Coverage for every major CRM module named in the original request, even where rollout is deferred to later waves

### Definition of Done (verifiable conditions with commands)
- Each implemented wave has matching action, integration, and Livewire tests runnable with `php artisan test --compact <target>`.
- Web UI pages for CRM modules build successfully with `npm run build`.
- Modified PHP and Blade files show zero new diagnostics via LSP checks on touched files.
- Final wave includes full app test run with `php artisan test --compact`.
- Evidence for all task-level QA scenarios is stored under `.sisyphus/evidence/`.

### Must Have
- Reuse existing module pattern: `app/Modules/{Domain}` and `app/Http/{Api|Web|Livewire?}`-style transport.
- Preserve `Shared\Models\User` as the global actor.
- Introduce CRM entities in dependency order, not by spec order.
- Include explicit ownership and permission guardrails before module expansion.
- Include UI data-testid conventions so Playwright/manual QA remains deterministic.

### Must NOT Have
- No one-shot build of all CRM modules in a single implementation wave.
- No premature email sync, campaign automation, ERP/payment/helpdesk/chat integrations in early waves.
- No generic “everything morphs to everything” parent model for activities or cases.
- No tenant tables in v1, but no schema choices that block future `workspace_id` introduction.
- No reporting/dashboard work before core entities and lifecycle states stabilize.

## Verification Strategy
> ZERO HUMAN INTERVENTION — all verification is agent-executed.
- Test decision: tests-after using existing PHPUnit functional + integration patterns, plus Livewire functional tests where UI logic is added
- QA policy: Every task includes agent-executed scenarios with concrete routes, selectors, and commands
- Evidence: `.sisyphus/evidence/task-{N}-{slug}.{ext}`
- Known baseline note: `composer phpstan` currently has unrelated optional-package failures in auth/logging areas; CRM work must introduce zero new diagnostics in touched files and must not worsen the existing baseline

## Execution Strategy
### Parallel Execution Waves
> Target: 5-8 tasks per wave. Shared dependencies are extracted into Wave 1 so later modules can proceed in parallel.

Wave 1: CRM foundations, ownership/visibility rules, reusable UI/test primitives

Wave 2: Accounts, Contacts, and cross-linking customer graph

Wave 3: Activities, Leads, and Deal/Pipeline core

Wave 4: Cases, Products, Quotes, Email & Communications

Wave 5: Campaigns, Dashboards/Reports, integration seams, final hardening

### Dependency Matrix (full, all tasks)
| Task | Depends On |
|------|------------|
| 1 | — |
| 2 | 1 |
| 3 | 1 |
| 4 | 1, 2, 3 |
| 5 | 2, 3, 4 |
| 6 | 2, 3, 4 |
| 7 | 5, 6 |
| 8 | 5, 6, 7 |
| 9 | 5, 6, 7 |
| 10 | 5, 6, 7 |
| 11 | 5, 6, 9 |
| 12 | 5, 6, 7, 8, 9, 10 |
| 13 | 5, 8, 9, 12 |
| 14 | 2, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13 |
| 15 | 14 |

### Agent Dispatch Summary
| Wave | Task Count | Categories |
|------|------------|------------|
| 1 | 4 | unspecified-high, writing, visual-engineering |
| 2 | 2 | unspecified-high, visual-engineering |
| 3 | 3 | unspecified-high, visual-engineering |
| 4 | 3 | unspecified-high, visual-engineering |
| 5 | 3 | deep, unspecified-high, visual-engineering |

## TODOs
> Implementation + Test = ONE task. Never separate.
> EVERY task MUST have: Agent Profile + Parallelization + QA Scenarios.

- [ ] 1. Establish CRM foundation conventions

  **What to do**: Create the shared CRM foundation needed by every later module: enums for ownership/visibility/status primitives, address value objects, tag handling shape, money/currency conventions, UUID policy, `data-testid` naming convention, and a future-tenant seam note in schema/contracts. Add a small internal CRM foundation module or shared CRM support area rather than scattering helpers across domains.
  **Must NOT do**: Do not create tenant/workspace tables in v1. Do not create business entities yet. Do not add reporting/integration logic.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: cross-cutting backend design with real downstream impact
  - Skills: [`laravel-best-practices`] — maintain module and config conventions
  - Omitted: [`scaffold-module`] — the work spans multiple modules, not one isolated module scaffold

  **Parallelization**: Can Parallel: NO | Wave 1 | Blocks: 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15 | Blocked By: —

  **References**:
  - Pattern: `app/Modules/Users/DataTransferObjects/CreateUserData.php` — canonical readonly DTO style
  - Pattern: `app/Modules/Users/Actions/CreateUserAction.php` — single-entry action style
  - Pattern: `app/Providers/AppServiceProvider.php` — global runtime conventions and strictness
  - Pattern: `bootstrap/providers.php` — provider registration pattern
  - Pattern: `tests/Support/TestCases/FunctionalTestCase.php` — testing baseline for app-level work
  - External: `https://docs.espocrm.com/user-guide/sales-management/#accounts` — account/contact centrality in mature CRM sequencing

  **Acceptance Criteria**:
  - [ ] CRM foundation code exists in a single coherent location and is reused by later CRM modules
  - [ ] UUID, ownership, address, money, tags, and `data-testid` conventions are encoded in code/config/tests, not only comments
  - [ ] Touched files pass LSP diagnostics with zero new errors

  **QA Scenarios**:
  ```
  Scenario: Foundation test suite passes
    Tool: Bash
    Steps: Run `php artisan test --compact tests/App/Modules/CRM` or the exact new CRM foundation tests
    Expected: Exit code 0 and all CRM foundation tests pass
    Evidence: .sisyphus/evidence/task-1-crm-foundation.txt

  Scenario: Future tenant seam is preserved
    Tool: Bash
    Steps: Search generated migrations/models/contracts for hard-coded single-company assumptions that prevent adding `workspace_id`; capture matching lines and review expected seam markers
    Expected: No schema choice forces destructive tenant retrofits; seam markers/config are present
    Evidence: .sisyphus/evidence/task-1-crm-foundation-seam.txt
  ```

  **Commit**: YES | Message: `feat(crm-foundation): add shared crm primitives` | Files: `app/Modules/CRMFoundation/**`, `config/**`, `tests/App/Modules/CRMFoundation/**`

- [ ] 2. Add users, teams, roles, and record-visibility foundation

  **What to do**: Introduce CRM-facing roles/permissions for `Admin`, `Manager`, `Rep`, and `Viewer`, plus a concrete v1 `Team` model and membership/assignment rules that make `own`, `team`, and `all` visibility decision-complete. Define owner reassignment/deactivated-user handling and the simplest single-workspace user-management flow while preserving a future tenant seam.
  **Must NOT do**: Do not build full enterprise ACL or multi-level org charts beyond what later CRM modules need. Do not mix CRM module business rules into authorization primitives.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: authorization foundation affects every query and policy
  - Skills: [`laravel-best-practices`] — align policies, config, and model ownership with Laravel patterns
  - Omitted: [`create-dto-action`] — not the dominant concern here

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15 | Blocked By: 1

  **References**:
  - Pattern: `app/Modules/Shared/Models/User.php` — global actor model to extend, not replace
  - Pattern: `config/auth.php` — current auth guard/provider baseline
  - Pattern: `app/Http/Web/routes/web.php` — current web-side authenticated route decisions
  - External: `https://docs.espocrm.com/administration/roles-management/#overview` — mature CRM role baseline

  **Acceptance Criteria**:
  - [ ] Roles, teams, and record-visibility semantics exist in code and tests
  - [ ] CRM queries can filter by owner/visibility without ad-hoc per-module rules
  - [ ] Team-scoped visibility and deactivated-user reassignment rules are explicit and tested
  - [ ] Future tenant seam remains additive rather than breaking

  **QA Scenarios**:
  ```
  Scenario: Ownership and visibility rules are enforced
    Tool: Bash
    Steps: Run the new policy/authorization tests for CRM ownership and visibility
    Expected: Admin/Manager/Rep/Viewer cases pass with correct allow/deny behavior
    Evidence: .sisyphus/evidence/task-2-crm-roles.txt

  Scenario: Team visibility is enforced
    Tool: Playwright / Bash
    Steps: Authenticate as a rep in Team A, request a record owned by Team B, then request a record owned by Team A but another rep
    Expected: Team B record is blocked; Team A record follows the configured `team` visibility rule
    Evidence: .sisyphus/evidence/task-2-crm-teams-ui.txt
  ```

  **Commit**: YES | Message: `feat(crm-foundation): add crm roles teams and visibility rules` | Files: `app/Modules/**`, `config/**`, `tests/**`

- [ ] 3. Build reusable CRM UI shell and table primitives

  **What to do**: Add the minimum reusable Livewire/Tailwind UI primitives for CRM pages: authenticated CRM layout shell, page header, flash/error area, filter bar, empty state, and a reusable table pattern that supports deterministic `data-testid` selectors. This is the only reusable UI foundation required before module-specific pages.
  **Must NOT do**: Do not build a full component design system. Do not implement module business logic in the shell layer.

  **Recommended Agent Profile**:
  - Category: `visual-engineering` — Reason: reusable UI shell/table pattern with Tailwind + Livewire pages
  - Skills: [] — existing repo patterns are enough
  - Omitted: [`brainstorming`] — architecture has already been decided

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15 | Blocked By: 1

  **References**:
  - Pattern: `resources/views/components/layout/app.blade.php` — existing app shell baseline
  - Pattern: `resources/views/pages/login.blade.php` — thin page wrapper pattern
  - Pattern: `resources/views/livewire/users/users-table-page.blade.php` — starter table/list rendering baseline
  - Pattern: `app/Livewire/Users/UsersTablePage.php` — simple page component shape

  **Acceptance Criteria**:
  - [ ] CRM pages can render inside a shared shell without duplicating layout markup
  - [ ] Table/list pages expose stable `data-testid` selectors for filters, rows, columns, and actions
  - [ ] Shell/table primitive tests pass and the frontend builds successfully

  **QA Scenarios**:
  ```
  Scenario: CRM shell renders with stable selectors
    Tool: Playwright
    Steps: Open a seeded CRM page, assert presence of `[data-testid="crm-page-header"]`, `[data-testid="crm-filter-bar"]`, and `[data-testid="crm-table"]`
    Expected: All selectors render exactly once and page loads without console errors caused by the shell
    Evidence: .sisyphus/evidence/task-3-crm-ui-shell.png

  Scenario: Frontend assets compile
    Tool: Bash
    Steps: Run `npm run build`
    Expected: Exit code 0 and manifest/assets are produced
    Evidence: .sisyphus/evidence/task-3-crm-ui-shell-build.txt
  ```

  **Commit**: YES | Message: `feat(crm-ui): add crm shell and table primitives` | Files: `resources/views/**`, `app/Livewire/**`, `tests/**`

- [ ] 4. Create Accounts module

  **What to do**: Add the `Accounts` domain with the supplied universal CRM fields adapted for v1: UUID id, company identity, type, industry, contact channels, billing/shipping address value objects, annual revenue, employee count, owner, parent account, tags, and timestamps. Implement CRUD, list/detail pages, API transport, and tests.
  **Must NOT do**: Do not couple accounts to leads/deals/cases yet. Do not add tenant/workspace foreign keys in v1.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: first major CRM business module with backend + transport + UI
  - Skills: [`laravel-best-practices`, `scaffold-module`] — match module and transport conventions
  - Omitted: [`create-dto-action`] — useful but secondary to the full module scaffold

  **Parallelization**: Can Parallel: NO | Wave 1 | Blocks: 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15 | Blocked By: 1, 2, 3

  **References**:
  - Pattern: `app/Modules/Users/Actions/CreateUserAction.php` — basic action style
  - Pattern: `app/Http/Api/Auth/Controllers/RegisterWithCredentialsController.php` — thin controller + request + action flow
  - Pattern: `app/Http/Api/routes/v1.php` — API route registration baseline
  - External: `https://docs.suitecrm.com/user/core-modules/accounts/` — account centrality and related modules

  **Acceptance Criteria**:
  - [ ] Accounts can be created, listed, viewed, updated, and soft-deleted or safely archived per chosen repo convention
  - [ ] Accounts enforce owner assignment and parent-account integrity rules
  - [ ] API and Livewire/web flows are covered by tests

  **QA Scenarios**:
  ```
  Scenario: Create and view an account
    Tool: Playwright
    Steps: Log in, open `/crm/accounts`, click `[data-testid="accounts-create-button"]`, fill `Acme Holdings`, select owner/type/industry, save, open created row
    Expected: Account detail page shows saved company fields and owner
    Evidence: .sisyphus/evidence/task-4-accounts-create.png

  Scenario: Parent account validation rejects invalid self-link
    Tool: Bash / Playwright
    Steps: Attempt to set an account as its own parent via API or form submission
    Expected: Validation fails with a clear error and no invalid relationship is stored
    Evidence: .sisyphus/evidence/task-4-accounts-parent-error.txt
  ```

  **Commit**: YES | Message: `feat(crm-accounts): add account registry module` | Files: `app/Modules/Accounts/**`, `app/Http/**/Accounts/**`, `resources/views/**`, `tests/**`

- [ ] 5. Create Contacts module

  **What to do**: Add the `Contacts` domain with the supplied person-level CRM fields: identity, email/phone/mobile, job title, department, linked account, owner, lead source, do-not-contact, birthday, LinkedIn URL, preferred channel, and timestamps. Implement CRUD, searchable lists, detail views, and tests.
  **Must NOT do**: Do not collapse leads into contacts. Do not support many-to-many account affiliations in v1 unless the existing requirements absolutely force it.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: core customer graph entity with strong downstream dependencies
  - Skills: [`laravel-best-practices`, `scaffold-module`] — keep structure consistent with the repo and with Accounts
  - Omitted: [`create-dto-action`] — included naturally as part of the module pattern

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: 6, 7, 8, 9, 10, 11, 12, 13, 14, 15 | Blocked By: 2, 3, 4

  **References**:
  - Pattern: `app/Modules/Shared/Models/User.php` — owner relationship target
  - Pattern: `app/Modules/Users/DataTransferObjects/CreateUserData.php` — DTO baseline
  - Pattern: `tests/App/Modules/Users/Actions/CreateUserActionFunctionalTest.php` — action-level test style
  - External: `https://docs.suitecrm.com/user/core-modules/contacts/` — contact semantics in mature CRM

  **Acceptance Criteria**:
  - [ ] Contacts can be created with or without an account depending on the chosen validation rule
  - [ ] Contacts respect `do_not_contact` and preferred channel field validation
  - [ ] Contact search/list/detail flows are tested in API and UI layers

  **QA Scenarios**:
  ```
  Scenario: Create a contact linked to an account
    Tool: Playwright
    Steps: Log in, open `/crm/contacts`, click `[data-testid="contacts-create-button"]`, enter `Ada Lovelace`, `ada@example.com`, select account `Acme Holdings`, save
    Expected: Contact row appears and detail page shows linked account and owner
    Evidence: .sisyphus/evidence/task-5-contacts-create.png

  Scenario: Do-not-contact blocks contactable workflows
    Tool: Bash / Playwright
    Steps: Mark a contact as `do_not_contact`, trigger the minimal allowed contact action or validation test
    Expected: Restricted workflow is blocked or clearly marked unavailable per v1 rules
    Evidence: .sisyphus/evidence/task-5-contacts-dnc.txt
  ```

  **Commit**: YES | Message: `feat(crm-contacts): add contact registry module` | Files: `app/Modules/Contacts/**`, `app/Http/**/Contacts/**`, `resources/views/**`, `tests/**`

- [ ] 6. Add account-contact graph and customer detail timeline surface

  **What to do**: Wire the customer graph between Accounts and Contacts, including account detail screens that show linked contacts and contact detail screens that show their parent account. Add the first timeline placeholder area so Activities can attach cleanly in the next task.
  **Must NOT do**: Do not introduce leads/deals/cases into the detail pages yet. Do not build universal timeline aggregation across every future module in this task.

  **Recommended Agent Profile**:
  - Category: `visual-engineering` — Reason: cross-entity detail/list UI and navigation
  - Skills: [] — relies on the shell/table primitives already established
  - Omitted: [`scaffold-module`] — this is composition over new module creation

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: 7, 8, 9, 10, 11, 12, 13, 14, 15 | Blocked By: 2, 3, 4, 5

  **References**:
  - Pattern: `resources/views/pages/users.blade.php` — page wrapper style
  - Pattern: `app/Livewire/Users/UsersTablePage.php` — simple page component baseline
  - External: `https://docs.espocrm.com/user-guide/sales-management/#accounts` — account as central node for related entities

  **Acceptance Criteria**:
  - [ ] Account detail pages list linked contacts
  - [ ] Contact detail pages expose parent-account context
  - [ ] Timeline placeholder/region is present with stable selectors for future activity attachment

  **QA Scenarios**:
  ```
  Scenario: Account detail shows linked contacts
    Tool: Playwright
    Steps: Open `/crm/accounts/{id}`, inspect `[data-testid="account-contacts-table"]`, click a linked contact row
    Expected: Linked contacts are listed and navigation to the contact detail succeeds
    Evidence: .sisyphus/evidence/task-6-account-contact-graph.png

  Scenario: Contact detail preserves account context
    Tool: Playwright
    Steps: Open a contact detail page and click `[data-testid="contact-account-link"]`
    Expected: Navigation returns to the correct account detail and no broken links exist
    Evidence: .sisyphus/evidence/task-6-contact-account-link.png
  ```

  **Commit**: YES | Message: `feat(crm-core): connect accounts and contacts` | Files: `app/Modules/Accounts/**`, `app/Modules/Contacts/**`, `resources/views/**`, `tests/**`

- [ ] 7. Build Activities module as constrained shared timeline infrastructure

  **What to do**: Implement `Activities` as the shared interaction log for notes, tasks, calls, meetings, and basic email placeholders, with a constrained linking model that can attach to Accounts, Contacts, Leads, Deals, and Cases later. Include owner, status, priority, due date, duration, outcome, and attendees where v1 needs them.
  **Must NOT do**: Do not use unconstrained `morphTo` for arbitrary parents. Do not split every activity subtype into separate modules in v1. Do not implement full email syncing here.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: shared infrastructure with multiple future dependents
  - Skills: [`laravel-best-practices`] — protect query shape and relationship design
  - Omitted: [`scaffold-module`] — the main challenge is modeling, not scaffolding

  **Parallelization**: Can Parallel: NO | Wave 3 | Blocks: 8, 9, 10, 11, 12, 13, 14, 15 | Blocked By: 2, 3, 5, 6

  **References**:
  - Pattern: `app/Modules/Auth/Providers/AuthRelationshipsServiceProvider.php` — example of adding cross-model relations via providers where needed
  - External: `https://docs.espocrm.com/user-guide/activities-and-calendar/#activities-calendar` — shared activity semantics
  - External: `https://docs.suitecrm.com/user/core-modules/tasks/` — activity/task parent linkage precedent

  **Acceptance Criteria**:
  - [ ] Activities can attach to Accounts and Contacts in v1 using the chosen constrained link pattern
  - [ ] Activity lists can filter by type, status, owner, and due date
  - [ ] Account/contact timelines render activities in reverse chronological order

  **QA Scenarios**:
  ```
  Scenario: Log a call against an account
    Tool: Playwright
    Steps: Open account detail, click `[data-testid="activity-create-button"]`, choose `Call`, enter subject/outcome/due date, save
    Expected: New call appears at top of `[data-testid="activity-timeline"]`
    Evidence: .sisyphus/evidence/task-7-activities-account-call.png

  Scenario: Invalid activity parent type is rejected
    Tool: Bash
    Steps: Submit an activity link payload with an unsupported parent type through the lowest-level test or API path
    Expected: Validation/domain exception blocks persistence
    Evidence: .sisyphus/evidence/task-7-activities-invalid-parent.txt
  ```

  **Commit**: YES | Message: `feat(crm-activities): add shared activity timeline` | Files: `app/Modules/Activities/**`, `app/Http/**/Activities/**`, `resources/views/**`, `tests/**`

- [ ] 8. Add Leads module with duplicate handling and explicit conversion flow

  **What to do**: Implement `Leads` as intake records distinct from Contacts. Include source, status, score, rating, owner, campaign pointer placeholder, description, and conversion workflow that creates/selects Account + Contact and optionally a Deal. Add duplicate detection rules before conversion.
  **Must NOT do**: Do not merge leads into contacts via a status flag. Do not auto-create deals without an explicit conversion rule. Do not build marketing automation.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: first lifecycle workflow with conversion semantics and duplicate control
  - Skills: [`laravel-best-practices`, `create-dto-action`] — conversion workflow benefits from strong DTO/action discipline
  - Omitted: [`scaffold-module`] — conversion behavior is more important than scaffold speed

  **Parallelization**: Can Parallel: YES | Wave 3 | Blocks: 9, 10, 11, 12, 13, 14, 15 | Blocked By: 2, 3, 5, 6, 7

  **References**:
  - Pattern: `app/Modules/Auth/Actions/RegisterUserAction.php` — cross-module orchestration precedent
  - Pattern: `app/Modules/Auth/Actions/ContinueWithOAuthAction.php` — orchestration + linking precedent
  - External: `https://docs.suitecrm.com/user/core-modules/leads/#_converting_a_lead` — lead conversion semantics
  - External: `https://docs.espocrm.com/user-guide/sales-management/#leads` — leads are not the final customer graph

  **Acceptance Criteria**:
  - [ ] Leads can be created, qualified, unqualified, and converted
  - [ ] Conversion can select existing Account/Contact targets or create new ones
  - [ ] Duplicate detection prevents accidental customer-graph duplication during conversion

  **QA Scenarios**:
  ```
  Scenario: Convert a qualified lead into account and contact
    Tool: Playwright
    Steps: Create lead `Grace Hopper`, click `[data-testid="lead-convert-button"]`, choose `Create Account`, `Create Contact`, confirm conversion
    Expected: Lead status becomes converted and linked Account/Contact detail pages exist
    Evidence: .sisyphus/evidence/task-8-leads-convert.png

  Scenario: Duplicate email warning blocks naive conversion
    Tool: Playwright / Bash
    Steps: Create a lead with an email matching an existing contact, attempt conversion without resolving duplicate selection
    Expected: Conversion is blocked or forced through explicit duplicate resolution flow
    Evidence: .sisyphus/evidence/task-8-leads-duplicate.txt
  ```

  **Commit**: YES | Message: `feat(crm-leads): add lead intake and conversion` | Files: `app/Modules/Leads/**`, `app/Http/**/Leads/**`, `resources/views/**`, `tests/**`

- [ ] 9. Build Deals / Opportunities / Pipeline core

  **What to do**: Implement `Deals` with account, primary contact, owner, stage, amount, currency, probability, expected revenue calculation, expected close date, deal type, pipeline, source inheritance, lost reason, and activity linkage. Add list, detail, stage transitions, and forecasting-safe state handling.
  **Must NOT do**: Do not implement quote/catalog behavior in this task. Do not build custom forecasting dashboards yet. Do not skip stage/history considerations if stage changes matter for later reporting.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: sales pipeline is the first high-value workflow module after lead conversion
  - Skills: [`laravel-best-practices`, `create-dto-action`] — deal transitions should be explicit and tested
  - Omitted: [`scaffold-module`] — lifecycle logic dominates

  **Parallelization**: Can Parallel: YES | Wave 3 | Blocks: 11, 12, 13, 14, 15 | Blocked By: 2, 3, 5, 6, 7

  **References**:
  - External: `https://docs.espocrm.com/user-guide/sales-management/#opportunities` — opportunity/deal semantics tied to account graph
  - External: `https://docs.suitecrm.com/user/core-modules/opportunities/` — common pipeline stage model
  - Pattern: `app/Modules/Auth/Responses/SessionLoginResponse.php` — example of computed response shaping where needed

  **Acceptance Criteria**:
  - [ ] Deals can be created and linked to an Account with an optional primary Contact
  - [ ] Stage/probability changes recompute expected revenue consistently
  - [ ] Closed lost deals require a lost reason if that rule is chosen for v1

  **QA Scenarios**:
  ```
  Scenario: Advance a deal through pipeline stages
    Tool: Playwright
    Steps: Create deal `Acme Expansion`, change stage from Prospecting to Proposal to Closed Won using `[data-testid="deal-stage-select"]`
    Expected: Stage updates persist and expected revenue reflects current probability
    Evidence: .sisyphus/evidence/task-9-deals-stage-flow.png

  Scenario: Closed lost without reason is rejected
    Tool: Playwright / Bash
    Steps: Attempt to move a deal to `Closed Lost` without setting a lost reason
    Expected: Validation or domain rule prevents the transition
    Evidence: .sisyphus/evidence/task-9-deals-lost-reason.txt
  ```

  **Commit**: YES | Message: `feat(crm-deals): add pipeline core module` | Files: `app/Modules/Deals/**`, `app/Http/**/Deals/**`, `resources/views/**`, `tests/**`

- [ ] 10. Build Cases / Support module

  **What to do**: Implement `Cases` as a support-domain module linked explicitly to Account and Contact(s), with status, priority, type, owner, SLA timestamps, satisfaction score placeholder, and channel. Add list/detail views and activity linkage.
  **Must NOT do**: Do not make Cases a generic polymorphic “caseable” record. Do not integrate external help desks or email ingestion yet.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: service-domain workflow with SLA-sensitive state transitions
  - Skills: [`laravel-best-practices`, `scaffold-module`] — solid transport/domain boundary needed
  - Omitted: [`create-dto-action`] — useful but not primary driver

  **Parallelization**: Can Parallel: YES | Wave 4 | Blocks: 12, 13, 14, 15 | Blocked By: 5, 6, 7, 8

  **References**:
  - External: `https://docs.espocrm.com/user-guide/case-management/#overview` — case dependence on account/contact context
  - Pattern: `app/Modules/Auth/Models/MagicLink.php` — stateful model example with lifecycle semantics

  **Acceptance Criteria**:
  - [ ] Cases can be created against an Account and one-or-more Contacts per chosen v1 model
  - [ ] SLA-related fields validate correctly and support state transitions
  - [ ] Case detail page exposes linked activities and contact/account context

  **QA Scenarios**:
  ```
  Scenario: Create and resolve a support case
    Tool: Playwright
    Steps: Open `/crm/cases`, create case `Billing issue`, assign account/contact, set priority, move status to Resolved
    Expected: Case detail shows resolution state, owner, and linked customer context
    Evidence: .sisyphus/evidence/task-10-cases-resolve.png

  Scenario: Case creation without customer context fails if required by v1 rule
    Tool: Bash / Playwright
    Steps: Submit a case without the minimum required account/contact linkage
    Expected: Validation/domain rule rejects the record
    Evidence: .sisyphus/evidence/task-10-cases-validation.txt
  ```

  **Commit**: YES | Message: `feat(crm-cases): add support case module` | Files: `app/Modules/Cases/**`, `app/Http/**/Cases/**`, `resources/views/**`, `tests/**`

- [ ] 11. Build Products catalog and Quotes module

  **What to do**: Implement `Products` as a reusable catalog and `Quotes` as deal-linked documents with line items, discounts, taxes, totals, status, valid-until, notes, and signature timestamp placeholder. Snapshot quote item price/name/tax so later product edits do not mutate historical quotes.
  **Must NOT do**: Do not connect to payments, invoicing, or ERP. Do not rely on live product values after quote issuance.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: catalog + quoting has strong data-integrity requirements
  - Skills: [`laravel-best-practices`, `create-dto-action`] — calculations and snapshots need explicit boundaries
  - Omitted: [`scaffold-module`] — price snapshot rules are the hard part

  **Parallelization**: Can Parallel: YES | Wave 4 | Blocks: 14, 15 | Blocked By: 5, 6, 8, 9, 10

  **References**:
  - External: `https://docs.espocrm.com/user-guide/products/#products` — catalog role in CRM workflows
  - External: `https://docs.espocrm.com/user-guide/quotes/#quotes` — quote dependence on deal/customer context

  **Acceptance Criteria**:
  - [ ] Products can be created and activated/deactivated
  - [ ] Quotes can be created from deals with line items and correct subtotal/tax/total calculations
  - [ ] Quote item snapshots remain stable after product catalog changes

  **QA Scenarios**:
  ```
  Scenario: Create a quote from a deal with two line items
    Tool: Playwright
    Steps: Open a deal, click `[data-testid="quote-create-button"]`, add two products, quantities, and a discount, then save
    Expected: Quote detail shows correct subtotal, tax, and total plus linked deal/contact context
    Evidence: .sisyphus/evidence/task-11-quotes-create.png

  Scenario: Product price change does not mutate existing quote snapshot
    Tool: Bash / Playwright
    Steps: Create quote, update product unit price, reload quote detail
    Expected: Existing quote line items retain original snapshot values
    Evidence: .sisyphus/evidence/task-11-quotes-snapshot.txt
  ```

  **Commit**: YES | Message: `feat(crm-quotes): add products and quoting module` | Files: `app/Modules/Products/**`, `app/Modules/Quotes/**`, `app/Http/**`, `resources/views/**`, `tests/**`

- [ ] 12. Build Email & Communications module

  **What to do**: Implement first-party CRM email/communications tracking as a concrete module: email records, sender/recipient metadata, cc/bcc, subject, body_html/body_text, send/delivery/open/click status fields, thread grouping, contact/deal/case linkage, and template support for manually initiated CRM emails. Include communication timeline rendering and manual outbound send logging; keep provider sync as a later integration concern.
  **Must NOT do**: Do not build Gmail/Outlook two-way sync, mailbox ingestion daemons, or provider webhooks in this task. Do not hide email records inside generic activities without first-class communication storage.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: real CRM communication domain with state and linkage requirements
  - Skills: [`laravel-best-practices`, `create-dto-action`] — explicit state transitions and DTOs are important
  - Omitted: [`scaffold-module`] — communication lifecycle design is the hard part

  **Parallelization**: Can Parallel: YES | Wave 4 | Blocks: 13, 14, 15 | Blocked By: 5, 6, 7, 8, 9, 10

  **References**:
  - External: `https://docs.espocrm.com/user-guide/emails/` — CRM email logging/thread semantics
  - External: `https://docs.suitecrm.com/user/core-modules/emails/` — email module expectations in mature CRM
  - Pattern: `app/Modules/Auth/Notifications/MagicLinkNotification.php` — existing mail-related baseline in repo

  **Acceptance Criteria**:
  - [ ] Email records are first-class CRM entities with thread/grouping and status fields
  - [ ] Communications can be linked to contacts and at least one business record type (deal or case) in v1
  - [ ] Manual outbound email flows and templates are test-covered without requiring external sync providers

  **QA Scenarios**:
  ```
  Scenario: Log and view a manual outbound email
    Tool: Playwright
    Steps: Open a contact or deal detail page, click `[data-testid="email-compose-button"]`, fill subject/body/template, send or save-log
    Expected: Email record appears in the communication timeline with correct recipients and status
    Evidence: .sisyphus/evidence/task-12-email-log.png

  Scenario: Thread grouping keeps related emails together
    Tool: Bash / Playwright
    Steps: Create two linked email records sharing a thread id, then load the communication timeline or query service
    Expected: Records render in a single grouped thread or are query-grouped by thread id as specified
    Evidence: .sisyphus/evidence/task-12-email-thread.txt
  ```

  **Commit**: YES | Message: `feat(crm-email): add communications module` | Files: `app/Modules/Emails/**`, `app/Http/**/Emails/**`, `resources/views/**`, `tests/**`

- [ ] 13. Build Marketing Campaigns module

  **What to do**: Implement `Campaigns` as a first-class CRM module with name, type, status, date range, budget, actual cost, target audience, expected leads, actual leads, revenue-generated placeholder/read-model hook, owner, and lead/contact associations. Define campaign attribution rules for leads and later communication linkage.
  **Must NOT do**: Do not build full marketing automation, sequence builders, ad-platform sync, or external campaign providers in this task. Do not leave Campaigns as a mere foreign-key placeholder on Leads.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: campaign entity and attribution semantics influence leads and reporting
  - Skills: [`laravel-best-practices`, `create-dto-action`] — attribution and lifecycle fields need clear boundaries
  - Omitted: [`scaffold-module`] — domain rules matter more than scaffolding speed

  **Parallelization**: Can Parallel: YES | Wave 5 | Blocks: 14 | Blocked By: 5, 8, 9, 12

  **References**:
  - External: `https://docs.espocrm.com/user-guide/campaigns/` — campaign semantics and responses
  - External: `https://docs.suitecrm.com/user/core-modules/campaigns/` — campaign/lead attribution baseline

  **Acceptance Criteria**:
  - [ ] Campaigns can be created, listed, activated, paused, and completed
  - [ ] Leads can be attributed to campaigns with explicit ownership and source tracking
  - [ ] Campaign metrics have a defined path into later reporting even if some metrics remain placeholders initially

  **QA Scenarios**:
  ```
  Scenario: Create a campaign and attribute leads to it
    Tool: Playwright
    Steps: Open `/crm/campaigns`, create `Spring Outreach`, then create or update leads to reference that campaign
    Expected: Campaign detail shows attributed leads and basic planned vs actual lead counts
    Evidence: .sisyphus/evidence/task-13-campaigns-create.png

  Scenario: Campaign state transitions are enforced
    Tool: Bash / Playwright
    Steps: Attempt invalid transitions such as moving a completed campaign back to planned if disallowed by v1 rules
    Expected: Transition rules are enforced and tested
    Evidence: .sisyphus/evidence/task-13-campaigns-status.txt
  ```

  **Commit**: YES | Message: `feat(crm-campaigns): add marketing campaigns module` | Files: `app/Modules/Campaigns/**`, `app/Http/**/Campaigns/**`, `resources/views/**`, `tests/**`

- [ ] 14. Add dashboards, reporting read-models, and integration seams

  **What to do**: Implement the minimum reporting, dashboard, and integration-ready foundation after the core lifecycle modules exist. Add read-model/query services for standard CRM metrics (pipeline, conversion, activity summary, campaign ROI baseline, case resolution, quote totals), build user-facing dashboard pages/cards/widgets for those metrics, and create webhook/event seams plus import/export/external-id contracts for future integrations without implementing third-party adapters.
  **Must NOT do**: Do not build Gmail/Outlook sync, Mailchimp/HubSpot/Stripe/Zapier adapters, or full drag-and-drop dashboard customization in this task. Do not let reports query raw transactional joins everywhere.

  **Recommended Agent Profile**:
  - Category: `deep` — Reason: analytics and integration seams require careful boundaries after core modules settle
  - Skills: [`laravel-best-practices`] — protect performance and event/query design
  - Omitted: [`scaffold-module`] — foundation work is analytical rather than scaffold-driven

  **Parallelization**: Can Parallel: NO | Wave 5 | Blocks: 15 | Blocked By: 2, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13

  **References**:
  - External: `https://docs.suitecrm.com/user/advanced-modules/reports/` — reports come after operational entities
  - External: `https://docs.espocrm.com/user-guide/reports/#reports` — reporting built atop stable entity data
  - External: `https://docs.espocrm.com/user-guide/dashboards/` — dashboard surface for user-facing KPI delivery
  - Pattern: `config/webhooks.php` — existing redirect/webhook-style config precedent

  **Acceptance Criteria**:
  - [ ] Standard CRM report queries/read models exist for the agreed baseline metrics
  - [ ] User-facing dashboards exist for agreed KPI cards/widgets and are backed by the reporting layer
  - [ ] Integration events/webhook/import-export/external-id contracts exist without external adapter implementations
  - [ ] Reporting tests prove metrics use stable lifecycle fields from earlier modules

  **QA Scenarios**:
  ```
  Scenario: Pipeline and conversion reports compute correctly
    Tool: Bash
    Steps: Seed deterministic CRM fixtures, run report/read-model tests for pipeline totals and lead conversion counts
    Expected: Report outputs match expected fixture values exactly
    Evidence: .sisyphus/evidence/task-14-reporting-metrics.txt

  Scenario: Dashboard renders KPI cards from reporting layer
    Tool: Playwright
    Steps: Open `/crm/dashboard`, assert KPI cards for pipeline total, open activities, open cases, and campaign/lead baseline metrics
    Expected: Dashboard cards render deterministic values from seeded fixtures and link to their target lists
    Evidence: .sisyphus/evidence/task-14-dashboard-kpis.png

  Scenario: Integration seam emits internal event without external side effects
    Tool: Bash
    Steps: Trigger a CRM lifecycle event that should publish a webhook/integration payload or export contract and assert fake bus/event output
    Expected: Internal event contract is emitted and no external network calls occur
    Evidence: .sisyphus/evidence/task-14-integration-seams.txt
  ```

  **Commit**: YES | Message: `feat(crm-reporting): add dashboards reporting and integration seams` | Files: `app/Modules/Reports/**`, `app/Modules/Dashboards/**`, `app/Modules/Integrations/**`, `config/**`, `resources/views/**`, `tests/**`

- [ ] 15. Harden CRM seed graph, fixtures, and end-to-end smoke coverage

  **What to do**: Replace demo-only CRM data with a coherent seeded graph spanning Accounts, Contacts, Activities, Leads, Deals, Cases, Products, and Quotes so that development, QA, and smoke tests operate on realistic relationships. Add end-to-end smoke flows for the major CRM paths.
  **Must NOT do**: Do not let seed data become the only source of truth for tests; keep factories and focused fixtures authoritative. Do not mix demo-seed concerns into domain logic.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: cross-module fixture graph and smoke coverage
  - Skills: [`laravel-best-practices`] — keep factories/seeders/tests disciplined
  - Omitted: [`scaffold-module`] — this is cross-domain stabilization work

  **Parallelization**: Can Parallel: NO | Wave 5 | Blocks: Final Verification | Blocked By: 14

  **References**:
  - Pattern: `database/seeders/DatabaseSeeder.php` — central seeder entrypoint
  - Pattern: `database/seeders/DemoUsersSeeder.php` — current demo seeding baseline to replace/extend
  - Pattern: `database/factories/**` — factory organization baseline

  **Acceptance Criteria**:
  - [ ] A coherent CRM seed graph exists for local/dev QA
  - [ ] Factories cover all CRM modules with meaningful states where needed
  - [ ] Smoke tests cover account/contact/activity, lead conversion, deal progression, case resolution, quote creation, campaign attribution, email timeline, and dashboard rendering

  **QA Scenarios**:
  ```
  Scenario: Seeded CRM graph boots cleanly
    Tool: Bash
    Steps: Run fresh migrate + seed in a clean database, then execute the CRM smoke suite
    Expected: Seeding succeeds and smoke tests pass without manual setup
    Evidence: .sisyphus/evidence/task-15-crm-seed-smoke.txt

  Scenario: Seeded UI paths are navigable end-to-end
    Tool: Playwright
    Steps: Log in as a seeded CRM user, navigate through seeded account, contact, deal, case, quote, campaign, email, and dashboard pages using deterministic selectors
    Expected: Every seeded route loads and linked records navigate correctly
    Evidence: .sisyphus/evidence/task-15-crm-seed-ui.png
  ```

  **Commit**: YES | Message: `test(crm): add seed graph and smoke coverage` | Files: `database/seeders/**`, `database/factories/**`, `tests/**`

## Final Verification Wave (MANDATORY — after ALL implementation tasks)
> 4 review agents run in PARALLEL. ALL must APPROVE. Present consolidated results to user and get explicit "okay" before completing.
> **Do NOT auto-proceed after verification. Wait for user's explicit approval before marking work complete.**
> **Never mark F1-F4 as checked before getting user's okay.** Rejection or user feedback -> fix -> re-run -> present again -> wait for okay.
- [ ] F1. Plan Compliance Audit — oracle
- [ ] F2. Code Quality Review — unspecified-high
- [ ] F3. Real Manual QA — unspecified-high (+ playwright if UI)
- [ ] F4. Scope Fidelity Check — deep

## Commit Strategy
- Commit once per task or tightly related pair of tasks inside the same wave.
- Use `feat(crm-<domain>): ...` for new CRM modules.
- Use `refactor(crm-foundation): ...` only for foundation changes that do not change behavior.
- Do not mix unrelated wave work in the same commit.

## Success Criteria
- CRM modules are introduced in dependency-safe order and follow the existing modular Laravel pattern.
- First executable slice produces a useful CRM core with Accounts, Contacts, and Activities.
- Later modules reuse the ownership, linking, and UI/test primitives established in Wave 1.
- Deferred capabilities remain explicitly deferred until their prerequisites exist.
