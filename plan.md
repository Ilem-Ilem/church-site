# Church Site Recovery Plan

Generated: 2026-02-06

## Snapshot Observations (From Current Repo)
- The repo contains multiple, conflicting status documents: `what_is_left.md` says major gaps remain, while `IMPLEMENTATION_STATUS.txt` claims everything is complete.
- The docs referenced by `IMPLEMENTATION_STATUS.txt` do not exist in the repo (e.g., `IMPLEMENTATION_COMPLETE.md`, `CHANGES_MADE.md`).
- There are multiple feature lists and status files, but no single “source of truth.”
- There is a mix of legacy naming (`BeliversAcademy`) and corrected naming (`BelieversAcademy`) in code; both exist in `app/Models`.
- The project is large (Laravel + Livewire/Volt + Vue + Bootstrap/Tailwind) with many features and likely partial implementations.

## Goal
Make the project stable, consistent, testable, and deployable with a clear, verified feature set and documentation.

## Plan

### Phase 0 — Inventory & Baseline (1–2 days)
Deliverables:
- A single “source of truth” inventory of features, routes, and modules.
- A reproducible local setup checklist.

Tasks:
1. Catalog all modules and major flows (public, admin, super-admin).
2. Build a route inventory and map routes to controllers/components and views.
3. Confirm migrations reflect current models and intended schema.
4. Identify empty or placeholder components/views and list them.
5. Produce a baseline “works/doesn’t work/unknown” table by manual smoke test.

Exit Criteria:
- A short, accurate baseline report (no conflicting status claims).

### Phase 1 — Stabilize the Core (2–4 days)
Deliverables:
- Clean, consistent domain naming and data models.
- Known-good local dev setup.

Tasks:
1. Resolve naming inconsistencies:
   - Decide the official model naming: `BelieversAcademy` vs `BeliversAcademy`.
   - Standardize route naming and slug consistency.
2. Verify schema:
   - Ensure `academy_classes` and `student_classes` tables match feature expectations.
   - Fix mismatches (e.g., `study_material` vs `study_materials`).
3. Clean repo metadata:
   - Remove stale or misleading status files OR update them to reflect reality.
4. Confirm `.env` configuration and queue, mail, storage drivers.

Exit Criteria:
- Migrations are aligned with models and route names.
- No internal naming mismatch causes runtime errors.

### Phase 2 — Finish Critical Feature Gaps (4–7 days)
Deliverables:
- Academy flows and certificate generation functional end-to-end.
- Required notifications working.

Tasks:
1. Believers Academy:
   - Verify enrollment flow, class scheduling, student tracking.
   - Complete certificate generation (PDF creation, storage, retrieval).
2. Notifications:
   - Implement or validate event, prayer request, partnership, academy enrollment notifications.
3. File handling:
   - Validate uploads for sermons, reports, event galleries (size/mime checks).

Exit Criteria:
- Academy enrollment → class completion → certificate flow works.
- Notifications trigger for all required workflows.

### Phase 3 — Super Admin Gaps (5–10 days)
Deliverables:
- Functional super-admin user management and roles/permissions.
- System analytics and activity logs view.

Tasks:
1. System-wide user management:
   - List/search/filter users across conclaves.
   - Bulk actions (activate/deactivate/export).
2. Functions management:
   - Implement CRUD and assign functions to users/teams.
3. Roles & permissions:
   - Admin UI for role and permission management (Spatie).
4. Activity/audit logs:
   - Add super-admin views and filters.
5. System health dashboard:
   - Queue status, storage health, error log access.

Exit Criteria:
- Super-admin can manage users, roles, and system-level settings without direct DB access.

### Phase 4 — Testing & QA (Ongoing, 3–7 days)
Deliverables:
- A meaningful test suite covering critical paths.
- A QA checklist for manual regression tests.

Tasks:
1. Add feature tests for:
   - Auth (register/login/reset).
   - Academy flow and certificates.
   - Events registration and galleries.
   - Admin CRUD operations.
2. Add validation tests for file uploads and form inputs.
3. Manual QA checklist for all public and admin routes.

Exit Criteria:
- Tests cover the top 10 business-critical flows.
- Failing tests represent known, tracked issues only.

### Phase 5 — Documentation & Release (2–3 days)
Deliverables:
- Clear README and deployment guide.
- Accurate system documentation.

Tasks:
1. Write or update:
   - README (setup, commands, env).
   - Deployment steps.
   - Feature list and known gaps.
2. Add a simple changelog and “status snapshot.”

Exit Criteria:
- Anyone can set up, test, and deploy from documentation alone.

## Prioritized Risk Areas
1. Conflicting docs and missing status files.
2. Model/route naming mismatches.
3. Super-admin missing core functionality.
4. File uploads and storage validation.
5. Missing automated tests.

## Suggested First Actions
1. Confirm the authoritative feature set.
2. Decide the canonical naming for academy-related models/routes.
3. Produce the baseline “works/doesn’t work/unknown” matrix.

