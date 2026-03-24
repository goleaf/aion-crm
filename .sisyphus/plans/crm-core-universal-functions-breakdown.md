# CRM — Core Universal Functions & Features

This artifact is the direct planning breakdown of the universal CRM scope. It is intentionally structured as a module-by-module reference covering purpose, fields, and relationships.

## 1. Contacts & Accounts

### Account (Company / Organization)

**Purpose:** Central registry of all organizations the CRM interacts with.

| Field | Type | Description |
|---|---|---|
| `account_id` | UUID | Primary key |
| `name` | String | Legal company name |
| `industry` | Enum | Sector such as Tech, Finance, Retail |
| `type` | Enum | Customer, Partner, Prospect, Vendor |
| `website` | URL | Company website |
| `phone` | String | Main phone |
| `email` | String | General email |
| `billing_address` | Object | Street, City, State, ZIP, Country |
| `shipping_address` | Object | Separate delivery address |
| `annual_revenue` | Decimal | Financial size indicator |
| `employee_count` | Integer | Company size |
| `owner_id` | FK → User | Assigned sales rep |
| `parent_account_id` | FK → Account | For subsidiaries and branches |
| `created_at` | Datetime | Record creation |
| `tags` | Array | Custom labels |

**Primary Relations:**
- Account → User (many-to-one owner)
- Account → Account (many-to-one parent)
- Account → Contacts (one-to-many)
- Account → Deals (one-to-many)
- Account → Activities (one-to-many / linked)
- Account → Cases (one-to-many)

### Contact (Individual Person)

**Purpose:** Central registry of people connected to accounts and CRM workflows.

| Field | Type | Description |
|---|---|---|
| `contact_id` | UUID | Primary key |
| `first_name` | String | First name |
| `last_name` | String | Last name |
| `email` | String | Primary email |
| `phone` | String | Direct line |
| `mobile` | String | Cell number |
| `job_title` | String | Role at company |
| `department` | String | Department |
| `account_id` | FK → Account | Linked company |
| `owner_id` | FK → User | CRM owner |
| `lead_source` | Enum | How they found you |
| `do_not_contact` | Boolean | GDPR / opt-out flag |
| `birthday` | Date | Relationship building |
| `linkedin_url` | URL | Social profile |
| `preferred_channel` | Enum | Email, Phone, SMS |

**Primary Relations:**
- Contact → Account (many-to-one)
- Contact → User (many-to-one owner)
- Contact → Deals (one-to-many / linked)
- Contact → Activities (one-to-many / linked)
- Contact → Cases (one-to-many / linked)
- Contact → Emails (one-to-many / linked)

## 2. Leads

**Purpose:** Capture and qualify potential customers before conversion into durable CRM records.

| Field | Type | Description |
|---|---|---|
| `lead_id` | UUID | Primary key |
| `first_name` | String | First name |
| `last_name` | String | Last name |
| `company` | String | Employer name |
| `email` | String | Email |
| `phone` | String | Phone |
| `lead_source` | Enum | Web form, Cold call, Referral, Social, Ad |
| `status` | Enum | New, Contacted, Qualified, Unqualified, Converted |
| `score` | Integer | Lead scoring 0–100 |
| `rating` | Enum | Hot, Warm, Cold |
| `campaign_id` | FK → Campaign | Origin campaign |
| `owner_id` | FK → User | Assigned rep |
| `converted` | Boolean | Has been converted |
| `converted_to_contact_id` | FK → Contact | Conversion target |
| `converted_to_deal_id` | FK → Deal | Conversion target |
| `converted_at` | Datetime | Conversion timestamp |
| `description` | Text | Notes from first contact |

**Primary Relations:**
- Lead → Campaign (many-to-one)
- Lead → User (many-to-one owner)
- Lead → Contact (conversion target)
- Lead → Deal (optional conversion target)

## 3. Deals / Opportunities / Pipeline

**Purpose:** Track revenue opportunities through pipeline stages until won or lost.

| Field | Type | Description |
|---|---|---|
| `deal_id` | UUID | Primary key |
| `name` | String | Deal title |
| `account_id` | FK → Account | Customer company |
| `contact_id` | FK → Contact | Primary contact |
| `owner_id` | FK → User | Sales rep |
| `stage` | Enum | Prospecting → Qualification → Proposal → Negotiation → Closed Won/Lost |
| `amount` | Decimal | Expected deal value |
| `currency` | Enum | USD, EUR, etc. |
| `probability` | Integer | Win chance percentage |
| `expected_revenue` | Computed | `amount × probability` |
| `close_date` | Date | Expected close |
| `deal_type` | Enum | New Business, Renewal, Upsell |
| `pipeline_id` | FK → Pipeline | Pipeline membership |
| `lost_reason` | Enum | Price, Competitor, No budget, etc. |
| `products` | FK → Products[] | Line items |
| `source` | Enum | Source inherited from lead or direct origin |
| `created_at` | Datetime | Record creation |

**Primary Relations:**
- Deal → Account (many-to-one)
- Deal → Contact (many-to-one)
- Deal → User (many-to-one owner)
- Deal → Pipeline (many-to-one)
- Deal → Products (many-to-many via line items)
- Deal → Activities (one-to-many / linked)
- Deal → Quotes (one-to-many)

## 4. Activities

**Purpose:** Log and schedule every interaction such as calls, emails, meetings, tasks, notes, and SMS.

| Field | Type | Description |
|---|---|---|
| `activity_id` | UUID | Primary key |
| `type` | Enum | Call, Email, Meeting, Task, Note, SMS |
| `subject` | String | Short title |
| `description` | Text | Full details or outcome |
| `status` | Enum | Planned, Completed, Cancelled |
| `priority` | Enum | Low, Normal, High |
| `due_date` | Datetime | Deadline or scheduled time |
| `duration_minutes` | Integer | For calls/meetings |
| `outcome` | Text | What happened |
| `related_to_type` | Enum | Deal, Contact, Account, Lead, Case |
| `related_to_id` | Polymorphic/typed FK | Linked record |
| `owner_id` | FK → User | Responsible person |
| `attendees` | FK → Users[] | Meeting participants |

**Primary Relations:**
- Activity → Account / Contact / Deal / Lead / Case (linked subject)
- Activity → User (many-to-one owner)
- Activity → Users (many-to-many attendees)

## 5. Email & Communications

**Purpose:** Track outbound/inbound communication history, email threads, and templates.

| Field | Type | Description |
|---|---|---|
| `email_id` | UUID | Primary key |
| `from` | String | Sender address |
| `to` | Array | Recipient list |
| `cc` | Array | CC list |
| `bcc` | Array | BCC list |
| `subject` | String | Email subject |
| `body_html` | HTML | Rich content |
| `body_text` | Text | Plain text fallback |
| `sent_at` | Datetime | Send time |
| `opened_at` | Datetime | Tracking pixel hit |
| `clicked_at` | Datetime | Link click tracking |
| `status` | Enum | Draft, Sent, Delivered, Bounced, Opened |
| `thread_id` | String | Gmail/Outlook thread grouping |
| `contact_id` | FK → Contact | Linked person |
| `deal_id` | FK → Deal | Linked opportunity |
| `template_id` | FK → Template | If from template |

**Primary Relations:**
- Email → Contact (many-to-one)
- Email → Deal (many-to-one)
- Email → Case (optional many-to-one)
- Email → Campaign (optional linkage)
- Email → Template (many-to-one)

## 6. Products & Catalog

**Purpose:** Define sellable products and services for use in deals and quotes.

| Field | Type | Description |
|---|---|---|
| `product_id` | UUID | Primary key |
| `name` | String | Product name |
| `sku` | String | Stock keeping unit |
| `description` | Text | What it is |
| `unit_price` | Decimal | Default price |
| `currency` | Enum | Currency |
| `category` | String | Product family |
| `tax_rate` | Decimal | Applicable tax percentage |
| `active` | Boolean | Available for sale |
| `recurring` | Boolean | Subscription product |
| `billing_frequency` | Enum | Monthly, Annual |

**Primary Relations:**
- Product → Deals (many-to-many via line items)
- Product → Quotes (many-to-many via quote items)

## 7. Quotes & Proposals

**Purpose:** Generate formal pricing and proposal documents from deal work.

| Field | Type | Description |
|---|---|---|
| `quote_id` | UUID | Primary key |
| `name` | String | Quote title |
| `deal_id` | FK → Deal | Parent opportunity |
| `contact_id` | FK → Contact | Sent to |
| `status` | Enum | Draft, Sent, Accepted, Rejected, Expired |
| `valid_until` | Date | Expiry date |
| `line_items` | Array | Products with qty, price, discount |
| `subtotal` | Computed | Before tax |
| `discount` | Decimal | Percentage or fixed amount |
| `tax` | Computed | Tax total |
| `total` | Computed | Final amount |
| `notes` | Text | Terms and conditions |
| `signed_at` | Datetime | e-Signature timestamp |

**Primary Relations:**
- Quote → Deal (many-to-one)
- Quote → Contact (many-to-one)
- Quote → Products (many-to-many via line items)

## 8. Marketing Campaigns

**Purpose:** Plan, execute, and measure marketing efforts tied to leads, contacts, and communication.

| Field | Type | Description |
|---|---|---|
| `campaign_id` | UUID | Primary key |
| `name` | String | Campaign name |
| `type` | Enum | Email, Event, Social, Ads, Cold Call |
| `status` | Enum | Planned, Active, Completed, Paused |
| `start_date` | Date | Start date |
| `end_date` | Date | End date |
| `budget` | Decimal | Allocated spend |
| `actual_cost` | Decimal | Real spend |
| `target_audience` | Text | Segment description |
| `expected_leads` | Integer | Goal |
| `actual_leads` | Integer | Generated |
| `revenue_generated` | Computed | Via linked deals |
| `owner_id` | FK → User | Campaign manager |

**Primary Relations:**
- Campaign → User (many-to-one owner)
- Campaign → Leads (one-to-many)
- Campaign → Contacts (many-to-many / linked audience)
- Campaign → Emails (one-to-many / linked communications)

## 9. Support & Cases

**Purpose:** Track post-sale issues, requests, and service workflows.

| Field | Type | Description |
|---|---|---|
| `case_id` | UUID | Primary key |
| `title` | String | Issue summary |
| `description` | Text | Full problem detail |
| `status` | Enum | Open, In Progress, Pending, Resolved, Closed |
| `priority` | Enum | Low, Medium, High, Critical |
| `type` | Enum | Bug, Feature Request, Question, Complaint |
| `contact_id` | FK → Contact | Reporter |
| `account_id` | FK → Account | Company |
| `owner_id` | FK → User | Assigned agent |
| `sla_deadline` | Datetime | SLA breach time |
| `first_response_at` | Datetime | SLA tracking |
| `resolved_at` | Datetime | Resolution timestamp |
| `satisfaction_score` | Integer | CSAT 1–5 |
| `channel` | Enum | Email, Phone, Chat, Portal |

**Primary Relations:**
- Case → Contact (many-to-one)
- Case → Account (many-to-one)
- Case → User (many-to-one owner)
- Case → Activities (one-to-many / linked)
- Case → Emails (one-to-many / linked)

## 10. Reports & Dashboards

### Reports

**Purpose:** Give users structured analytics and scheduled output over CRM data.

| Field | Type | Description |
|---|---|---|
| `report_id` | UUID | Primary key |
| `name` | String | Report name |
| `type` | Enum | Table, Bar, Line, Funnel, Pie, KPI Card |
| `module` | Enum | Deals, Contacts, Activities, Cases, etc. |
| `filters` | JSON | Applied conditions |
| `group_by` | String | Grouping dimension |
| `metrics` | Array | Sum, Count, Average, etc. |
| `date_range` | Enum | This month, Quarter, Custom |
| `scheduled` | Boolean | Auto email report |
| `owner_id` | FK → User | Creator |
| `shared_with` | FK → Users[] | Access list |

**Primary Relations:**
- Report → User (many-to-one owner)
- Report → Users (many-to-many shared recipients)
- Report derives from Deals, Leads, Activities, Cases, Quotes, Campaigns, Accounts, Contacts

### Dashboards

**Purpose:** Present KPI cards and summary widgets for CRM operations.

| Field / Element | Type | Description |
|---|---|---|
| `dashboard_id` | UUID / logical id | Dashboard identifier if persisted |
| `name` | String | Dashboard name |
| `widgets` | Array | KPI cards and summary widgets |
| `layout` | JSON | Widget placement configuration |
| `owner_id` | FK → User | Dashboard owner |
| `shared_with` | FK → Users[] | Shared access list |

**Primary Relations:**
- Dashboard → User (many-to-one owner)
- Dashboard → Users (many-to-many shared viewers)
- Dashboard → Reports / read models (consumes metrics)

## 11. Users, Roles, Permissions, and Teams

**Purpose:** Control who can view, create, edit, delete, export, and own CRM records.

### User
| Field | Type | Description |
|---|---|---|
| `user_id` | UUID | Primary key |
| `name` | String | Full name |
| `email` | String | Login email |
| `role_id` | FK → Role | Admin, Manager, Rep, Viewer |
| `team_id` | FK → Team | Sales region/team |
| `is_active` | Boolean | Account enabled |
| `last_login` | Datetime | Activity check |
| `quota` | Decimal | Monthly/quarterly target |

### Role / Permissions
| Field | Description |
|---|---|
| `can_view` | See records |
| `can_create` | Add new records |
| `can_edit` | Modify records |
| `can_delete` | Remove records |
| `can_export` | Download data |
| `record_visibility` | Own / Team / All |

### Team
| Field | Type | Description |
|---|---|---|
| `team_id` | UUID | Primary key |
| `name` | String | Team or region name |
| `manager_user_id` | FK → User | Team manager |
| `description` | Text | Team description |
| `is_active` | Boolean | Whether the team is active |
| `created_at` | Datetime | Creation timestamp |

**Primary Relations:**
- User → Role (many-to-one)
- User → Team (many-to-one)
- Team → Users (one-to-many)
- Team → Accounts / Contacts / Leads / Deals / Cases / Reports / Dashboards (visibility scope via team membership)
- User owns or is assigned Accounts, Contacts, Leads, Deals, Activities, Campaigns, Cases, Reports, Dashboards

## 12. Integrations

**Purpose:** Connect CRM records and events to external systems.

| Field | Type | Description |
|---|---|---|
| `integration_id` | UUID | Primary key |
| `name` | String | Integration name |
| `type` | Enum | Email, Calendar, VoIP, ERP, Marketing, Payments, Help Desk, Chat, Webhook, No-Code |
| `provider` | String | Provider such as Gmail, Outlook, Stripe, Zendesk |
| `status` | Enum | Draft, Connected, Syncing, Disabled, Error |
| `direction` | Enum | Import, Export, Bidirectional |
| `external_account_id` | String | Remote system account identifier |
| `credentials_reference` | String / Secret Ref | Pointer to encrypted credentials or secret store entry |
| `config` | JSON | Provider-specific configuration |
| `webhook_secret` | String / Secret Ref | Secret used for webhook verification |
| `last_synced_at` | Datetime | Last successful sync |
| `last_error_at` | Datetime | Last failed sync timestamp |
| `owner_id` | FK → User | Internal owner of the integration |
| `is_active` | Boolean | Whether the integration is enabled |
| `created_at` | Datetime | Creation timestamp |

**Primary Relations:**
- Integration → User (many-to-one owner)
- Integration → Accounts / Contacts / Leads / Deals / Activities / Emails / Cases / Quotes / Campaigns / Reports (one-to-many through external-id mappings, sync jobs, webhook events, or import/export records)
- Integration → External system records (logical relation through `external_account_id` and per-entity mapping tables)

### Common Integration Targets

| Integration Type | Primary Purpose |
|---|---|
| Email (Gmail/Outlook) | Two-way sync of emails and calendar |
| Calendar | Meetings synced to activities |
| Phone / VoIP | Click-to-call, auto-log calls |
| ERP | Sync invoices, inventory, orders |
| Marketing tools | Mailchimp, HubSpot, Marketo |
| Payments | Stripe, PayPal for deal payments |
| Help Desk | Zendesk, Freshdesk for cases |
| Chat | Intercom, Drift for live leads |
| Webhooks / API | Custom integrations |
| Zapier / Make | No-code automation |

## Universal Cross-Module Relations Map

`Campaign -> Lead -> Contact -> Account`

`Contact -> Activity`

`Account -> Contact -> Deal`

`Deal -> Quote / Products`

`Contact -> Case`

`Contact + Deal + Case + Campaign -> Emails`

`All major entities -> Reports & Dashboards`

`All major entities -> User owner / assignee under Role + Team visibility`
