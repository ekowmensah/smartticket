# Database Plan For Phase 1

## Goal

Phase 1 should create only the tables needed for platform foundation, tenancy, onboarding, KYC, permissions, and auditability. Payments, orders, tickets, USSD, scanning, refunds, and settlements belong to later phases.

## Keep From Laravel Scaffold

- `users`
- `cache`
- `jobs`

The default `users` table should be expanded rather than replaced immediately.

## Core Tables To Add In Phase 1

### Organizations

`organizations`

- `id`
- `uuid` or `ulid`
- `name`
- `slug`
- `type`
- `public_email`
- `public_phone`
- `status`
- `approval_status`
- `suspension_reason`
- `timezone`
- `currency_code`
- `country_code`
- `created_by`
- timestamps
- soft deletes if needed later

### Organization Membership

`organization_user`

- `id`
- `organization_id`
- `user_id`
- `role`
- `status`
- `invited_by`
- `invited_at`
- `joined_at`
- timestamps

If a package handles permission pivot data separately, keep this table focused on membership rather than fine-grained permissions.

### Organization Invitations

`organization_invitations`

- `id`
- `organization_id`
- `invited_by`
- `accepted_by` nullable
- `name` nullable
- `email`
- `role`
- `token_hash`
- `expires_at`
- `accepted_at`
- `revoked_at`
- timestamps

Use this to support invitation links and delayed acceptance without granting access before the recipient completes onboarding.

### Organization KYC

`organization_kyc_submissions`

- `id`
- `organization_id`
- `submitted_by`
- `status`
- `reviewed_by`
- `reviewed_at`
- `rejection_reason`
- `business_type`
- `registration_number`
- `tax_identifier`
- `legal_name`
- `contact_name`
- `contact_phone`
- `contact_email`
- `payout_method`
- `payout_account_name`
- `payout_account_number`
- `payout_provider`
- timestamps

### KYC Documents

`organization_documents`

- `id`
- `organization_id`
- `organization_kyc_submission_id`
- `document_type`
- `storage_disk`
- `storage_path`
- `original_name`
- `mime_type`
- `file_size`
- `uploaded_by`
- `verified_at`
- `verified_by`
- timestamps

### OTP Requests

`otp_requests`

- `id`
- `user_id` nullable
- `phone_number`
- `channel`
- `purpose`
- `code_hash`
- `expires_at`
- `consumed_at`
- `attempts`
- `ip_address`
- `user_agent`
- timestamps

Store hashes, never raw OTP values.

### Customers Foundation

`customers`

- `id`
- `uuid` or `ulid`
- `phone_number`
- `email`
- `first_name`
- `last_name`
- `status`
- `last_verified_at`
- timestamps

This is a foundation table only for now. Checkout and ticket ownership belong to later phases.

### Settings

`settings`

- `id`
- `scope`
- `scope_id` nullable
- `key`
- `value` long text or json
- timestamps

Use this for platform and organization configuration rather than scattering config-like values across tables.

### Audit Logs

`audit_logs`

- `id`
- `actor_type`
- `actor_id`
- `organization_id` nullable
- `action`
- `entity_type`
- `entity_id`
- `old_values` json nullable
- `new_values` json nullable
- `ip_address`
- `user_agent`
- `request_id`
- `created_at`

These records should be append-only at the application level.

## User Table Adjustments

`users` should likely gain:

- `phone_number`
- `phone_verified_at`
- `status`
- `last_login_at`
- `last_login_ip`

Email should remain available for platform/admin accounts even if customer identity becomes phone-first.

## Indexing Priorities

Create indexes early on:

- `organizations.slug`
- `organization_user.organization_id + user_id`
- `organization_kyc_submissions.organization_id + status`
- `organization_documents.organization_id + document_type`
- `otp_requests.phone_number + purpose + expires_at`
- `customers.phone_number`
- `settings.scope + scope_id + key`
- `audit_logs.organization_id + created_at`
- `audit_logs.entity_type + entity_id`

## Constraints

- Add foreign keys for all organization-owned tables
- Prefer enums in code with database-safe string columns
- Use decimal types for money later, but do not add financial tables in Phase 1
- Do not store secrets, raw OTPs, or document contents in the database

## Tables Explicitly Deferred

These should not be created in the first implementation pass unless the Phase 1 scope is being expanded deliberately:

- `venues`
- `events`
- `event_sessions`
- `ticket_types`
- `inventory_reservations`
- `checkout_sessions`
- `orders`
- `payments`
- `tickets`
- `gates`
- `scanner_devices`
- `refunds`
- `settlements`
- `ledger_accounts`
- `ledger_transactions`
- `ledger_entries`
