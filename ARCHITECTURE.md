# Architecture Baseline

## Product Direction

SmartCast Tickets is being built as a serious commercial SaaS platform for event organizers in Ghana. The architecture source document requires support for multi-tenancy, guest and account-based purchasing, Mobile Money and card payments, QR tickets, offline-capable gate scanning, organizer settlements, and financial reconciliation.

The project should be built as a modular monolith on Laravel, with clear domain boundaries and thin controllers.

## Current Framework Baseline

- Laravel 12.62.0
- PHP requirement in the scaffold: `^8.2`
- Current local PHP runtime: `8.2.12`
- Target runtime from product specification: `PHP 8.3+`
- Default scaffold packages only

## Architectural Decisions

### 1. Modular Monolith

Business logic should live in actions, services, domain classes, and policies. Controllers should coordinate requests and responses only.

Recommended `app/` structure for this project:

```text
app/
├── Actions/
│   ├── Admin/
│   ├── Auth/
│   ├── Organizations/
│   └── Users/
├── Contracts/
├── Data/
├── Domain/
│   ├── Audit/
│   ├── Auth/
│   ├── Customers/
│   ├── Organizations/
│   ├── Settings/
│   └── Users/
├── Enums/
├── Events/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Auth/
│   │   └── Organizer/
│   ├── Middleware/
│   └── Requests/
├── Jobs/
├── Listeners/
├── Models/
├── Notifications/
├── Policies/
├── Providers/
├── Queries/
├── Repositories/
├── Rules/
├── Services/
├── Support/
└── ValueObjects/
```

### 2. Shared-Database Multi-Tenancy

Phase 1 should use shared-database tenancy with `organization_id` on organizer-owned records. That choice is efficient and compatible with the product, but it creates a hard requirement for disciplined scoping.

Guard rails for Phase 1:

- Scope organizer data at the query and policy layers
- Centralize current-organization resolution
- Never trust hidden form fields for tenant ownership
- Add cross-tenant authorization tests early
- Separate platform-admin routes from organizer routes

### 3. Role Separation

Phase 1 should establish two distinct permission surfaces:

- Platform roles: super admin, support, compliance, finance, operations
- Organizer roles: owner, admin, finance, support, scanner manager, team member

The first implementation should use a proven role/permission package rather than homegrown permission tables.

### 4. Phone-First Identity

The architecture document treats phone numbers as first-class identifiers. Even before customer checkout is built, Phase 1 should prepare for this by:

- Storing normalized phone numbers
- Using a value object or normalization service
- Creating reusable validation rules
- Keeping email optional where the flow allows

### 5. Auditability

Important actions in Phase 1 should create audit records:

- Organization registration
- Organizer approval or suspension
- Team invitation acceptance
- KYC submission and review
- Role changes
- Sensitive settings changes

### 6. Admin Surface Layout

The spec explicitly rejects a full SPA admin. The preferred Phase 1 shape is:

- Blade layouts for public, admin, and organizer areas
- Bootstrap 5 for layout and forms
- Alpine.js for lightweight interactivity
- Livewire only where it provides clear value

## Phase 1 Domain Priorities

Build these areas first:

1. Authentication and session foundation
2. Organizations and tenancy
3. Team members and roles
4. Organizer KYC and document handling
5. Settings and admin review flows
6. Audit logging and test coverage

Do not start payments, tickets, USSD, scanner, refunds, or settlements until the above is stable.

## Package Direction For Phase 1

Recommended package categories to evaluate before implementation:

- Authentication starter: Laravel Breeze or Jetstream with Blade-first preference
- Roles and permissions: Spatie Laravel Permission
- Activity logging: Spatie Laravel Activitylog
- Media or file handling for KYC documents: native Laravel storage first, add a package only if requirements outgrow it
- Data objects or DTO support: optional, not mandatory in Phase 1

Package selection should happen only after confirming Laravel 12 and PHP 8.3 compatibility.

## Risks To Manage Early

- Cross-tenant data leaks through careless queries
- Environment drift from the PHP 8.3 target
- KYC file upload security and retention handling
- Overbuilding later-phase concepts before the foundation is tested
- Permission sprawl without a clear role matrix

## Phase 1 Exit Criteria

Phase 1 is not done until all of the following are true:

- Fresh installation works
- Migrations and seeders run successfully
- Authentication works
- Organizer registration works
- Platform admin approval flow works
- Suspended organizers are blocked correctly
- Team invitations and role enforcement work
- Cross-tenant tests pass
- Audit records are produced for important actions
- Documentation reflects the implemented foundation

