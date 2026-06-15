# SmartCast Tickets

SmartCast Tickets is a multi-tenant event ticketing platform for Ghana. The product vision in this repository targets web sales, USSD purchases, Mobile Money and card payments, QR-code tickets, gate scanning, refunds, promoter and agent sales, and organizer settlements.

This repository is currently at the Phase 1 foundation stage. The Laravel application has been scaffolded, and the immediate focus is establishing the tenancy, authentication, organizer onboarding, KYC, roles, audit logging, and testing foundation before any payment, ticketing, USSD, or scanner work begins.

## Current Baseline

- Framework: Laravel 12.62.0
- PHP: 8.2.12 locally
- Package manager: Composer 2.8.9
- Default database scaffolding: users, cache, jobs
- Architecture source: [SmartTickets Architecture.pdf](./SmartTickets%20Architecture.pdf)

## PHP Baseline

Laravel 12 supports PHP 8.2, so the current local environment can be used for development work. The architecture specification still targets PHP 8.3 or newer, so the deployment baseline should be upgraded before production hardening.

## Database Baseline

The project is configured to use MySQL locally instead of SQLite. Default local settings now assume:

- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=smartticket`
- `DB_USERNAME=root`
- `DB_PASSWORD=` (XAMPP default)

## Phase 1 Scope

Phase 1 delivers the platform foundation only:

- Base application configuration
- Public, platform admin, and organizer layout foundations
- Authentication foundation
- Customer phone-number model foundation
- Roles and permissions
- Shared-database multi-tenancy
- Organization registration and team membership
- Organizer KYC foundation
- Platform administration foundation
- Settings foundation
- Audit logging foundation
- Policies, factories, seeders, and automated tests
- Documentation

See [ARCHITECTURE.md](./ARCHITECTURE.md), [DATABASE.md](./DATABASE.md), and [PHASE1_CHECKLIST.md](./PHASE1_CHECKLIST.md) for the implementation plan.

## Local Setup

1. Install PHP 8.2+ locally (target PHP 8.3+ for deployment), Composer, MySQL 8, and Node.js.
2. Copy `.env.example` to `.env`.
3. Configure database, mail, queue, cache, and session settings.
4. Run `composer install`.
5. Run `php artisan key:generate`.
6. Run `php artisan migrate`.
7. Run `php artisan db:seed`.
8. Run `php artisan test`.

## Planned Application Shape

The target application is a modular monolith with domain boundaries inside `app/`, not a controller-heavy codebase. Planned top-level areas include:

- `app/Actions`
- `app/Contracts`
- `app/Data`
- `app/Domain`
- `app/Enums`
- `app/Events`
- `app/Exceptions`
- `app/Http`
- `app/Jobs`
- `app/Listeners`
- `app/Models`
- `app/Notifications`
- `app/Policies`
- `app/Queries`
- `app/Repositories`
- `app/Rules`
- `app/Services`
- `app/Support`
- `app/ValueObjects`

## Immediate Next Work

- Reconcile the Phase 1 checklist against the implemented foundation and close any remaining gaps
- Decide whether Phase 1 keeps database-backed queues/cache locally or introduces Redis immediately
- Split shared authenticated UI into more explicit platform and organizer layout layers if we want stronger visual separation before Phase 2
- Verify a clean-machine setup from scratch, including migrations, seeding, and test execution
