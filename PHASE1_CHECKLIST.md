# Phase 1 Checklist

## Objective

Deliver a stable foundation for SmartCast Tickets before building commercial flows like payments, ticket issuance, USSD, and scanning.

## Environment

- Upgrade local PHP from 8.2.12 to 8.3+
- Confirm MySQL 8 and Redis availability
- Add `.env.example` values that match the planned services
- Decide whether local development uses Redis immediately or starts with database-backed queues and cache

## Repository Foundation

- Replace the generic Laravel README with project documentation
- Create architecture and database planning docs
- Initialize Git if version history will begin from this scaffold
- Configure baseline code style and test conventions

## Package Selection

- Choose a Blade-first auth starter
- Evaluate and install a role/permission package
- Evaluate and install an activity/audit logging package
- Document package decisions and why they fit Laravel 12

## Application Structure

- Create the modular `app/` directory structure
- Define naming conventions for actions, services, policies, and queries
- Split route files by public, admin, auth, and organizer concerns
- Add middleware strategy for platform and organizer access

## Authentication And Identity

- Implement authentication foundation
- Add phone number fields and normalization support
- Add OTP request storage and verification flow foundation
- Keep email-based admin access supported

## Organizations And Tenancy

- Create organizations table and model
- Create organization membership table and model
- Add current-organization resolution strategy
- Add organization-aware authorization checks
- Add suspended and pending organization states
- Build organizer registration flow
- Build organizer approval flow for platform admins

## Team And Roles

- Define platform roles
- Define organizer roles
- Implement role assignment flows
- Add team invitation flow
- Add invitation acceptance flow
- Prevent unauthorized role elevation

## KYC Foundation

- Create KYC submission and document tables
- Add secure file upload handling
- Build organizer submission flow
- Build platform review flow
- Restrict paid-event publishing and settlements until approval status exists, even if those features ship later

## Settings And Auditability

- Add scoped settings storage
- Add audit log storage and recording
- Record critical actor, organization, entity, and request metadata
- Mask sensitive fields in logs

## UI Foundation

- Create public layout
- Create platform admin layout
- Create organizer layout
- Ensure mobile responsiveness and accessible form behavior

## Tests

- Fresh install and migration test
- Authentication test
- Organizer registration test
- Admin approval test
- Suspended organizer access-block test
- Team invite and membership test
- Cross-tenant access-denial tests
- KYC upload and review tests
- Audit record creation tests

## Definition Of Done

- Fresh setup works end to end
- Migrations and seeders succeed
- Authentication works
- Platform admin can log in
- Organizer can register
- Organizer approval flow works
- Suspended organizer protections work
- Team membership and roles work
- Cross-tenant tests pass
- KYC submissions are stored securely
- Audit logs are created for critical actions
- README and architecture docs match the implemented foundation
