# TODO: Chapter Filter Functionality for Super Admin Dashboard

## Overview
Implement comprehensive chapter filtering functionality across all admin/dashboard features for super admin access. This allows super admins to view and manage data from any chapter in the system.

## Current State Analysis

### What Exists
- ✅ Chapter filtering already implemented in regular admin components (82 components)
- ✅ Pattern: URL-based filtering using `?chapter=name` parameter
- ✅ `FilterByChapter` trait available for query scoping
- ✅ `AdminChapters` middleware for access control
- ✅ Super admin dashboard with conclave (chapter) management
- ✅ All major models have `chapter_id` foreign key

### What's Missing
- ❌ Centralized chapter selector in super admin dashboard
- ❌ Super admin routes to access regular admin features with chapter override
- ❌ Middleware to allow super admin chapter switching
- ❌ UI components for chapter selection/switching
- ❌ Dashboard overview showing stats across all chapters or filtered by chapter

---

## Implementation Tasks

### Phase 1: Infrastructure Setup

#### 1.1 Create Super Admin Chapter Access Middleware
**File**: `app/Http/Middleware/SuperAdminChapterAccess.php`

**Requirements**:
- Allow super admin to access any chapter's data
- Override normal chapter restrictions for super admin role
- Maintain security - verify user has super-admin role
- Pass selected chapter to request context

**Tasks**:
- [ ] Create middleware file
- [ ] Implement role checking (super-admin only)
- [ ] Add chapter validation logic
- [ ] Register middleware in `app/Http/Kernel.php`
- [ ] Test middleware with various user roles

---

#### 1.2 Create Chapter Selector Component
**File**: `resources/views/livewire/admin/superadmin/components/chapter-selector.blade.php`

**Requirements**:
- Dropdown/select list showing all chapters
- Display current selected chapter
- Update URL parameter on selection
- Persist selection across page navigation
- Show "All Chapters" option

**Tasks**:
- [ ] Create Livewire component class
- [ ] Fetch all chapters from database
- [ ] Implement chapter selection logic
- [ ] Add URL state management with `#[Url]`
- [ ] Create Blade template with TallStackUI select
- [ ] Style component to match existing UI
- [ ] Add wire:navigate support

**Acceptance Criteria**:
- Dropdown shows all chapter names
- Selection updates URL: `?chapter=ChapterName`
- Selection persists on page reload
- Works with Livewire navigation

---

#### 1.3 Update Super Admin Layout
**File**: `resources/views/components/layouts/app.blade.php` or create new super admin layout

**Requirements**:
- Include chapter selector in header/sidebar
- Show selected chapter prominently
- Add quick stats for selected chapter
- Maintain existing navigation structure

**Tasks**:
- [ ] Identify current layout file for super admin
- [ ] Add chapter selector component to layout
- [ ] Update navigation to preserve chapter parameter
- [ ] Add visual indicator of selected chapter
- [ ] Test responsive design

---

### Phase 2: Dashboard Overview

#### 2.1 Enhanced Super Admin Dashboard
**File**: `resources/views/livewire/admin/superadmin/dashboard.blade.php`

**Requirements**:
- Show overview stats for selected chapter or all chapters
- Display key metrics: members, events, academy students, reports, etc.
- Recent activities from selected chapter
- Quick links to filtered admin features
- Comparison view (optional): compare chapters side-by-side

**Tasks**:
- [ ] Update dashboard component to accept chapter filter
- [ ] Create stats cards (total members, active events, etc.)
- [ ] Add filtering logic for all statistics
- [ ] Implement "All Chapters" aggregated view
- [ ] Add recent activities section
- [ ] Create quick action links with chapter parameter
- [ ] Add data visualization (charts/graphs) per chapter
- [ ] Test performance with large datasets

**Statistics to Display**:
- Total Members
- Active Events
- Academy Students
- Pending Reports
- Financial Overview (givings)
- Appointments (pending/completed)
- Prayer Requests
- Transport Requests

---

### Phase 3: Super Admin Routes to Admin Features

#### 3.1 Create Super Admin Feature Routes
**File**: `routes/super_admin_route.php`

**Requirements**:
- Routes to access all admin dashboard features
- Apply super admin middleware + chapter access middleware
- Maintain existing URL structure with super-admin prefix
- Preserve chapter parameter across navigation

**Tasks**:
- [ ] Add routes for Members management
  - `super-admin/members?chapter=X`
  - `super-admin/members/create?chapter=X`
  - `super-admin/members/{id}/edit?chapter=X`

- [ ] Add routes for Events management
  - `super-admin/events?chapter=X`
  - `super-admin/events/create?chapter=X`
  - `super-admin/events/{id}/edit?chapter=X`
  - `super-admin/events/{id}/registrations?chapter=X`
  - `super-admin/events/gallery?chapter=X`

- [ ] Add routes for Believers Academy
  - `super-admin/academy?chapter=X`
  - `super-admin/academy/classes?chapter=X`
  - `super-admin/academy/students?chapter=X`

- [ ] Add routes for Teams
  - `super-admin/teams?chapter=X`
  - `super-admin/teams/create?chapter=X`

- [ ] Add routes for Appointments
  - `super-admin/appointments?chapter=X`
  - `super-admin/appointments/settings?chapter=X`

- [ ] Add routes for Finance
  - `super-admin/finance?chapter=X`
  - `super-admin/finance/givings?chapter=X`

- [ ] Add routes for Reports
  - `super-admin/reports?chapter=X`
  - `super-admin/reports/create?chapter=X`

- [ ] Add routes for Prayer Requests
  - `super-admin/prayer-requests?chapter=X`

- [ ] Add routes for Sermons
  - `super-admin/sermons?chapter=X`

- [ ] Add routes for Transport
  - `super-admin/transport?chapter=X`

- [ ] Add routes for Partnerships
  - `super-admin/partnerships?chapter=X`

- [ ] Add routes for Other Features
  - `super-admin/testimonies?chapter=X`
  - `super-admin/missions?chapter=X`
  - `super-admin/properties?chapter=X`
  - `super-admin/medicals?chapter=X`

**Route Pattern**:
```php
Route::middleware(['super-admin', 'super-admin-chapter-access'])->group(function () {
    // Feature routes here
});
```

---

### Phase 4: Component Adjustments

#### 4.1 Verify Existing Components Support Chapter Filter
**Locations**: `resources/views/livewire/admin/dashboard/**/*.blade.php`

**Requirements**:
- Ensure all components handle `?chapter=X` parameter
- Components should work for both regular admin and super admin
- Authorization checks should account for super admin role

**Tasks**:
- [ ] Audit each admin component for chapter filter support
- [ ] List components that need updates
- [ ] Update components missing chapter filtering

**Components to Verify** (82 total):
- [ ] Members components (5 files)
- [ ] Events components (6 files)
- [ ] Believers Academy components (4 files)
- [ ] Teams components (4 files)
- [ ] Appointments components (5 files)
- [ ] Finance components (4 files)
- [ ] Reports components (5 files)
- [ ] Prayer Requests (2 files)
- [ ] Sermons (1 file)
- [ ] Transport (2 files)
- [ ] Partnerships (1 file)
- [ ] Other features (remaining files)

---

#### 4.2 Update Authorization Logic
**Files**: Various Livewire components

**Requirements**:
- Allow super admin to bypass chapter restrictions
- Maintain security for regular admins
- Check both role and chapter access

**Tasks**:
- [ ] Identify authorization checks in components
- [ ] Add super admin exception to checks
- [ ] Update `mount()` methods
- [ ] Update policy classes if needed
- [ ] Test authorization with different roles

**Pattern to Implement**:
```php
public function mount()
{
    $isSuperAdmin = auth()->user()->hasRole('super-admin');

    if (!$isSuperAdmin) {
        // Regular admin: check chapter ownership
        $this->authorize('view', $this->resource);
    }

    // Super admin: allow access to any chapter
}
```

---

### Phase 5: Navigation Updates

#### 5.1 Super Admin Navigation Menu
**File**: `resources/views/partials/team-based-menu.blade.php` or create new super admin menu

**Requirements**:
- Show all admin features in sidebar
- Preserve chapter parameter in all links
- Highlight active section
- Organize by feature category

**Tasks**:
- [ ] Create/update super admin navigation partial
- [ ] Group features by category (Members, Events, Academy, etc.)
- [ ] Add chapter parameter to all navigation links
- [ ] Implement active state highlighting
- [ ] Add icons for each section
- [ ] Test navigation flow

**Navigation Structure**:
```
SUPER ADMIN MENU
├── Dashboard
├── Chapter Management
│   ├── All Chapters
│   ├── Create Chapter
│   └── Chapter Settings
├── [CHAPTER SELECTOR]
├── Members (filtered by chapter)
├── Events (filtered by chapter)
├── Academy (filtered by chapter)
├── Teams (filtered by chapter)
├── Finance (filtered by chapter)
├── Reports (filtered by chapter)
├── Appointments (filtered by chapter)
├── Prayer Requests (filtered by chapter)
├── Sermons (filtered by chapter)
├── Transport (filtered by chapter)
└── Settings
```

---

### Phase 6: UI/UX Enhancements

#### 6.1 Chapter Context Indicator
**Files**: Various views

**Requirements**:
- Visual indicator showing current chapter context
- Breadcrumb showing: Super Admin > Chapter Name > Feature
- Color coding or badge system
- Clear "All Chapters" mode indicator

**Tasks**:
- [ ] Design chapter indicator component
- [ ] Add to layout header
- [ ] Implement breadcrumb system
- [ ] Style with TallStackUI components
- [ ] Test visibility on all pages

---

#### 6.2 Chapter Switching Experience
**Requirements**:
- Smooth transition when switching chapters
- Preserve current page when switching
- Loading indicators during data fetch
- Confirmation for unsaved changes

**Tasks**:
- [ ] Add loading states to chapter selector
- [ ] Implement page preservation logic
- [ ] Add Livewire wire:loading indicators
- [ ] Create unsaved changes detection
- [ ] Add confirmation modal if needed

---

### Phase 7: Data Aggregation Features

#### 7.1 "All Chapters" View
**Requirements**:
- Show aggregated data from all chapters
- Sortable/filterable table with chapter column
- Export functionality
- Summary statistics

**Tasks**:
- [ ] Create aggregated query logic
- [ ] Add chapter name column to tables
- [ ] Implement sorting by chapter
- [ ] Add export feature (CSV/PDF)
- [ ] Create summary cards
- [ ] Optimize query performance

---

#### 7.2 Chapter Comparison Dashboard
**File**: `resources/views/livewire/admin/superadmin/chapter-comparison.blade.php`

**Requirements** (Optional - Future Enhancement):
- Side-by-side chapter comparison
- Key metrics comparison
- Visual charts/graphs
- Filter by date range

**Tasks**:
- [ ] Create comparison component
- [ ] Design comparison layout
- [ ] Implement multi-chapter data fetching
- [ ] Add chart library (Chart.js or similar)
- [ ] Create comparison views for:
  - [ ] Member growth
  - [ ] Event attendance
  - [ ] Financial giving
  - [ ] Academy enrollment
  - [ ] Report submission rates

---

### Phase 8: Testing & Quality Assurance

#### 8.1 Functional Testing
**Tasks**:
- [ ] Test chapter selector functionality
- [ ] Test all routes with chapter parameter
- [ ] Test authorization (super admin vs regular admin)
- [ ] Test data filtering accuracy
- [ ] Test "All Chapters" aggregation
- [ ] Test navigation with chapter persistence
- [ ] Test create/edit operations in chapter context

---

#### 8.2 Security Testing
**Tasks**:
- [ ] Verify super admin role requirement
- [ ] Test unauthorized access attempts
- [ ] Validate chapter parameter sanitization
- [ ] Check for SQL injection vulnerabilities
- [ ] Test cross-chapter data leakage
- [ ] Verify middleware chain execution

---

#### 8.3 Performance Testing
**Tasks**:
- [ ] Test dashboard load time with multiple chapters
- [ ] Test aggregated queries performance
- [ ] Optimize N+1 query issues
- [ ] Add database indexing if needed
- [ ] Test with large datasets
- [ ] Implement caching where appropriate

---

#### 8.4 User Acceptance Testing
**Tasks**:
- [ ] Create test scenarios for super admin users
- [ ] Document user workflow
- [ ] Gather feedback on UX
- [ ] Test on different screen sizes
- [ ] Verify browser compatibility
- [ ] Check accessibility compliance

---

### Phase 9: Documentation

#### 9.1 Technical Documentation
**Tasks**:
- [ ] Document middleware functionality
- [ ] Document routing structure
- [ ] Create API documentation for chapter filtering
- [ ] Document component updates
- [ ] Add code comments

---

#### 9.2 User Documentation
**Tasks**:
- [ ] Create super admin user guide
- [ ] Document chapter switching workflow
- [ ] Create video tutorials (optional)
- [ ] Add inline help text
- [ ] Create FAQ section

---

### Phase 10: Deployment & Rollout

#### 10.1 Database Migrations
**Tasks**:
- [ ] Review existing chapter-related migrations
- [ ] Create new migrations if needed
- [ ] Test migrations on staging
- [ ] Prepare rollback plan

---

#### 10.2 Configuration
**Tasks**:
- [ ] Update .env variables if needed
- [ ] Configure middleware priorities
- [ ] Set permissions in config
- [ ] Update route caching

---

#### 10.3 Deployment Checklist
**Tasks**:
- [ ] Run migrations on production
- [ ] Clear route cache: `php artisan route:clear`
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Clear view cache: `php artisan view:clear`
- [ ] Test super admin access
- [ ] Monitor error logs
- [ ] Verify performance metrics

---

## Technical Specifications

### Middleware Stack
```php
Route::middleware([
    'auth',
    'verified',
    'super-admin',  // Existing
    'super-admin-chapter-access'  // New
])->group(function () {
    // Routes
});
```

### Component Pattern
```php
use Livewire\Attributes\Url;

class FeatureComponent extends Component
{
    #[Url]
    public ?string $chapter = null;

    public ?int $chapterId = null;

    public function mount()
    {
        if ($this->chapter) {
            $this->chapterId = Chapter::where('name', $this->chapter)
                ->firstOrFail()
                ->id;
        }
    }

    public function rows()
    {
        return Model::query()
            ->when($this->chapterId, fn($q) =>
                $q->where('chapter_id', $this->chapterId)
            )
            ->paginate();
    }
}
```

### URL Structure
```
# Super Admin accessing chapter-specific data
/super-admin/dashboard?chapter=Lagos
/super-admin/members?chapter=Lagos
/super-admin/events?chapter=Abuja

# Super Admin viewing all chapters
/super-admin/dashboard  (no chapter param = all chapters)
/super-admin/members    (shows all members with chapter column)
```

---

## Dependencies

### Required Packages (Already Installed)
- Laravel Livewire v3
- TallStackUI
- Spatie Laravel-Permission

### Optional Enhancements
- Laravel Excel (for exports)
- Chart.js or Laravel Charts (for visualizations)

---

## Estimated Timeline

| Phase | Estimated Time | Priority |
|-------|---------------|----------|
| Phase 1: Infrastructure | 2-3 days | High |
| Phase 2: Dashboard | 2 days | High |
| Phase 3: Routes | 1 day | High |
| Phase 4: Components | 3-4 days | High |
| Phase 5: Navigation | 1 day | Medium |
| Phase 6: UI/UX | 2 days | Medium |
| Phase 7: Aggregation | 2-3 days | Low |
| Phase 8: Testing | 2-3 days | High |
| Phase 9: Documentation | 1-2 days | Medium |
| Phase 10: Deployment | 1 day | High |

**Total Estimated Time**: 17-24 days

---

## Success Metrics

- [ ] Super admin can select any chapter from dropdown
- [ ] All 82 admin dashboard features accessible with chapter filter
- [ ] Chapter filter persists across navigation
- [ ] "All Chapters" view shows aggregated data
- [ ] Authorization properly restricts regular admins
- [ ] Performance: Dashboard loads in < 2 seconds
- [ ] Zero security vulnerabilities
- [ ] 100% of existing features work with chapter filter

---

## Risks & Mitigation

| Risk | Impact | Mitigation |
|------|--------|------------|
| Performance issues with aggregated queries | High | Implement caching, optimize queries, add indexes |
| Breaking existing admin functionality | High | Thorough testing, feature flags, gradual rollout |
| Authorization bypass vulnerabilities | Critical | Security audit, middleware testing, role verification |
| Complex component updates | Medium | Start with simple components, create reusable patterns |
| User confusion with chapter switching | Medium | Clear UI indicators, user training, documentation |

---

## Notes

- Prioritize Phase 1-4 for MVP (Minimum Viable Product)
- Phase 7 (Aggregation) can be future enhancement
- Reuse existing patterns from current admin components
- Maintain backward compatibility with regular admin access
- Consider creating a demo/sandbox chapter for testing

---

## Questions to Resolve

1. Should super admin be able to create/edit data in any chapter?
2. Should there be an audit log of super admin chapter access?
3. Should chapter switching require confirmation?
4. Should some features be restricted even for super admin?
5. How should we handle chapter-specific settings access?
6. Should there be a "recent chapters" quick access list?
7. Should chapter selection be saved in user preferences?

---

## Reference Files

**Key Files to Reference During Implementation**:
- `/app/Http/Middleware/AdminChapters.php` - Existing chapter access middleware
- `/app/Traits/FilterByChapter.php` - Filtering trait pattern
- `/routes/super_admin_route.php` - Super admin routes
- `/resources/views/livewire/admin/dashboard/members/index.blade.php` - Example chapter filtering
- `/resources/views/livewire/admin/dashboard/event/index.blade.php` - Example status + chapter filtering
- `/app/Models/Chapter.php` - Chapter model
- `/app/Models/ChapterSetting.php` - Chapter settings model

---

**Last Updated**: 2025-11-15
**Status**: Planning Phase
**Assigned To**: Development Team
**Priority**: High
