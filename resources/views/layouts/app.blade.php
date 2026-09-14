<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tima-Ade University — @yield('title', 'School Management System')</title>
    <meta name="description" content="Tima-Ade University School Management System — Modern, comprehensive school administration platform.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>
        if (window.innerWidth >= 992 && window.localStorage.getItem('moon_sidebar_collapsed') === '1') {
            document.documentElement.classList.add('sidebar-collapsed-preload');
        }
    </script>

    <style>
        /* Print-specific styles to produce clean reports */
        @media print {
            /* Hide navigation, sidebars, overlays, interactive controls */
            .sidebar, .topbar, .sidebar-overlay, .sidebar-toggle-btn, .topbar-toggle,
            .page-footer, .no-print, .btn, form .btn, .dropdown, .pagination,
            .topbar-user, .topbar-btn, .sidebar-user, .nav-badge {
                display: none !important;
            }

            /* Expand main content to full width */
            .main-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .page-content, .page {
                padding: 0.5rem !important;
            }

            /* Ensure readable colors and remove shadows */
            body {
                background: #FFFFFF !important;
                color: #000000 !important;
            }
            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }

            /* Tables should be legible on paper */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 12pt !important;
            }
            table th, table td {
                border: 1px solid #ccc !important;
                padding: 4px 6px !important;
            }

            /* Avoid page breaks inside important elements */
            .card, .report, .table, .page {
                page-break-inside: avoid !important;
            }

            /* Reduce header/footer noise and fix link appearance */
            a[href]:after { content: "" !important; }

            /* Prevent printing selection highlights */
            * { -webkit-print-color-adjust: exact; color-adjust: exact; }
        }
    </style>

    @stack('styles')

    <style>
        /* Accessible focus outlines for keyboard users */
        :focus {
            outline: 3px solid #2563eb; /* blue focus ring */
            outline-offset: 2px;
        }

        /* Consistent button styles */
        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        /* Loading spinner small */
        .spinner-border-sm-custom {
            width: 1rem;
            height: 1rem;
            border-width: .15rem;
        }

        /* Table responsive improvements */
        .table-responsive { overflow-x: auto; }

        /* Improve pagination spacing */
        .pagination { margin: 0.5rem 0; }

        /* Reduced motion preference respect */
        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; animation: none !important; scroll-behavior: auto !important; }
        }
    </style>
</head>
<body class="sidebar-collapsed-md">
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('partials.ai-assistant')

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand-logo">
                <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo" class="brand-logo-image">
                <div class="brand-text">
                    <span class="brand-name">Tima-Ade University</span>
                </div>
            </div>
            <button class="sidebar-toggle-btn" id="sidebarToggleBtn" type="button" title="Toggle Sidebar">
                <i class="bi bi-layout-sidebar-reverse"></i>
            </button>
        </div>

        <div class="sidebar-user">
            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="sidebar-avatar">
            <div class="sidebar-user-info">
                <span class="sidebar-user-name">{{ auth()->user()->name }}</span>
                <span class="sidebar-user-role badge-role-{{ auth()->user()->role->slug ?? 'user' }}">
                    {{ auth()->user()->role->name ?? 'User' }}
                </span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Main Menu</div>

            <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                <i class="bi bi-chat-square-text"></i>
                <span>Tima Chat</span>
            </a>

            <a href="{{ route('public.home') }}" class="nav-link" target="_blank" style="color: #60a5fa;">
                <i class="bi bi-globe"></i>
                <span>Public Website</span>
                <i class="bi bi-box-arrow-up-right ms-auto small"></i>
            </a>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('advising.index') }}" class="nav-link {{ request()->routeIs('advising.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check"></i>
                    <span>Academic Advising</span>
                </a>
                @if(auth()->user()->isSuperAdmin())
                    <div class="nav-label">Super Admin Control</div>
                    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"><i class="bi bi-sliders"></i><span>Global Configuration</span></a>
                    <a href="{{ route('super-admin.roles') }}" class="nav-link {{ request()->routeIs('super-admin.roles') ? 'active' : '' }}"><i class="bi bi-shield-lock"></i><span>RBAC &amp; User Roles</span></a>
                    <a href="{{ route('super-admin.tenants') }}" class="nav-link {{ request()->routeIs('super-admin.tenants') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i><span>Tenant Oversight</span></a>
                    <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i><span>Security Audit Logs</span></a>
                    <a href="{{ route('super-admin.health') }}" class="nav-link {{ request()->routeIs('super-admin.health') ? 'active' : '' }}"><i class="bi bi-heart-pulse"></i><span>System Health</span></a>
                @endif
                <a href="{{ route('admin.live-classes.index') }}" class="nav-link {{ request()->routeIs('admin.live-classes*') ? 'active' : '' }}">
                    <i class="bi bi-broadcast-pin"></i>
                    <span>Live Class Monitoring</span>
                </a>
                <div class="nav-label">Administrative Role Management</div>
                <a href="{{ route(\App\Models\Role::PRESIDENT . '.index') }}" class="nav-link {{ request()->routeIs(\App\Models\Role::PRESIDENT . '.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i><span>President Accounts</span></a>
                <a href="{{ route(\App\Models\Role::CHANCELLOR . '.index') }}" class="nav-link {{ request()->routeIs(\App\Models\Role::CHANCELLOR . '.*') ? 'active' : '' }}"><i class="bi bi-person-badge-fill"></i><span>Chancellor Accounts</span></a>
                <a href="{{ route(\App\Models\Role::DEAN . '.index') }}" class="nav-link {{ request()->routeIs(\App\Models\Role::DEAN . '.*') ? 'active' : '' }}"><i class="bi bi-building"></i><span>Dean Accounts</span></a>
                <a href="{{ route(\App\Models\Role::DEPARTMENT_HEAD . '.index') }}" class="nav-link {{ request()->routeIs(\App\Models\Role::DEPARTMENT_HEAD . '.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i><span>Department Head Accounts</span></a>
                <a href="{{ route(\App\Models\Role::ACADEMIC_ADVISOR . '.index') }}" class="nav-link {{ request()->routeIs(\App\Models\Role::ACADEMIC_ADVISOR . '.*') ? 'active' : '' }}"><i class="bi bi-person-check"></i><span>Academic Advisor Accounts</span></a>
            @endif

            @if(auth()->user()->isDean())
                <div class="nav-label">Faculty Leadership</div>
                <a href="{{ route('dean.welcome') }}" class="nav-link {{ request()->routeIs('dean.welcome') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Welcome</span></a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Faculty Dashboard</span></a>
                <a href="{{ route('dean.departments') }}" class="nav-link {{ request()->routeIs('dean.departments') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i><span>Departments</span></a>
                <a href="{{ route('dean.programs') }}" class="nav-link {{ request()->routeIs('dean.programs') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i><span>Programs</span></a>
                <a href="{{ route('dean.courses') }}" class="nav-link {{ request()->routeIs('dean.courses') ? 'active' : '' }}"><i class="bi bi-book"></i><span>Courses</span></a>
                <a href="{{ route('dean.sections') }}" class="nav-link {{ request()->routeIs('dean.sections') ? 'active' : '' }}"><i class="bi bi-collection-play"></i><span>Course Sections</span></a>
                <a href="{{ route('dean.students') }}" class="nav-link {{ request()->routeIs('dean.students') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Faculty Students</span></a>
                <a href="{{ route('dean.staff') }}" class="nav-link {{ request()->routeIs('dean.staff') ? 'active' : '' }}"><i class="bi bi-person-workspace"></i><span>Faculty Staff</span></a>
                <a href="{{ route('dean.analytics') }}" class="nav-link {{ request()->routeIs('dean.analytics') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i><span>Analytics</span></a>
                <a href="{{ route('dean.reports') }}" class="nav-link {{ request()->routeIs('dean.reports') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i><span>Reports</span></a>
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"><i class="bi bi-person-circle"></i><span>My Profile</span></a>
            @endif

            @if(auth()->user()->isExecutive())
                <div class="nav-label">Executive Oversight</div>
                <a href="{{ route('executive.welcome') }}" class="nav-link {{ request()->routeIs('executive.welcome') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Welcome</span></a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Executive Dashboard</span></a>
                <a href="{{ route('executive.faculties') }}" class="nav-link {{ request()->routeIs('executive.faculties') ? 'active' : '' }}"><i class="bi bi-building"></i><span>Faculties</span></a>
                <a href="{{ route('executive.departments') }}" class="nav-link {{ request()->routeIs('executive.departments') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i><span>Departments</span></a>
                <a href="{{ route('executive.programs') }}" class="nav-link {{ request()->routeIs('executive.programs') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i><span>Programs</span></a>
                <a href="{{ route('executive.students') }}" class="nav-link {{ request()->routeIs('executive.students') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Students</span></a>
                <a href="{{ route('executive.academic-performance') }}" class="nav-link {{ request()->routeIs('executive.academic-performance') ? 'active' : '' }}"><i class="bi bi-graph-up"></i><span>Academic Performance</span></a>
                <a href="{{ route('executive.analytics') }}" class="nav-link {{ request()->routeIs('executive.analytics') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i><span>Analytics</span></a>
                <a href="{{ route('executive.reports') }}" class="nav-link {{ request()->routeIs('executive.reports') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i><span>Reports</span></a>
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"><i class="bi bi-person-circle"></i><span>My Profile</span></a>
            @endif

            @if(auth()->user()->isDepartmentHead())
                <div class="nav-label">Department Oversight</div>
                <a href="{{ route('department-head.welcome') }}" class="nav-link {{ request()->routeIs('department-head.welcome') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Welcome</span></a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Department Dashboard</span></a>
                <a href="{{ route('department-head.programs') }}" class="nav-link {{ request()->routeIs('department-head.programs') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i><span>Programs</span></a>
                <a href="{{ route('department-head.courses') }}" class="nav-link {{ request()->routeIs('department-head.courses') ? 'active' : '' }}"><i class="bi bi-book"></i><span>Courses</span></a>
                <a href="{{ route('department-head.sections') }}" class="nav-link {{ request()->routeIs('department-head.sections') ? 'active' : '' }}"><i class="bi bi-collection-play"></i><span>Sections</span></a>
                <a href="{{ route('department-head.students') }}" class="nav-link {{ request()->routeIs('department-head.students') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Students</span></a>
                <a href="{{ route('department-head.instructors') }}" class="nav-link {{ request()->routeIs('department-head.instructors') ? 'active' : '' }}"><i class="bi bi-person-workspace"></i><span>Instructors</span></a>
                <a href="{{ route('department-head.academic-performance') }}" class="nav-link {{ request()->routeIs('department-head.academic-performance') ? 'active' : '' }}"><i class="bi bi-graph-up"></i><span>Academic Performance</span></a>
                <a href="{{ route('department-head.advising') }}" class="nav-link {{ request()->routeIs('department-head.advising') ? 'active' : '' }}"><i class="bi bi-person-check"></i><span>Advising</span></a>
                <a href="{{ route('department-head.analytics') }}" class="nav-link {{ request()->routeIs('department-head.analytics') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i><span>Analytics</span></a>
                <a href="{{ route('department-head.reports') }}" class="nav-link {{ request()->routeIs('department-head.reports') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i><span>Reports</span></a>
            @endif

            @if(auth()->user()->isAcademicAdvisor())
                <div class="nav-label">Academic Advising</div>
                <a href="{{ route('academic-advisor.welcome') }}" class="nav-link {{ request()->routeIs('academic-advisor.welcome') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Welcome</span></a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Advisor Dashboard</span></a>
                <a href="{{ route('advising.dashboard') }}" class="nav-link {{ request()->routeIs('advising.dashboard') ? 'active' : '' }}"><i class="bi bi-person-check"></i><span>My Advisees</span></a>
                <a href="{{ route('students.student-360.index') }}" class="nav-link {{ request()->routeIs('students.student-360.index') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Student Profiles</span></a>
            @endif

            @if(auth()->user()->isAdmissionsOfficer())
                <div class="nav-label">Admissions</div>
                <a href="{{ route('admissions_officer.welcome') }}" class="nav-link {{ request()->routeIs('admissions_officer.welcome') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Welcome</span></a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                <a href="{{ route('admin.applications') }}" class="nav-link {{ request()->routeIs('admin.applications*') ? 'active' : '' }}"><i class="bi bi-person-lines-fill"></i><span>Applications</span></a>
                <a href="{{ route('admin.applicants') }}" class="nav-link {{ request()->routeIs('admin.applicants*') ? 'active' : '' }}"><i class="bi bi-person-vcard"></i><span>Applicants</span></a>
                <a href="{{ route('admin.documents') }}" class="nav-link {{ request()->routeIs('admin.documents') ? 'active' : '' }}"><i class="bi bi-file-earmark-check"></i><span>Documents</span></a>
                <a href="{{ route('admin.reviews') }}" class="nav-link {{ request()->routeIs('admin.reviews') ? 'active' : '' }}"><i class="bi bi-clipboard-check"></i><span>Reviews</span></a>
                <a href="{{ route('admin.interviews') }}" class="nav-link {{ request()->routeIs('admin.interviews') ? 'active' : '' }}"><i class="bi bi-calendar2-event"></i><span>Interviews</span></a>
                <a href="{{ route('admin.decisions') }}" class="nav-link {{ request()->routeIs('admin.decisions') ? 'active' : '' }}"><i class="bi bi-check2-square"></i><span>Decisions</span></a>
                <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i><span>Reports</span></a>
                <a href="{{ route('admin.communications') }}" class="nav-link {{ request()->routeIs('admin.communications') ? 'active' : '' }}"><i class="bi bi-envelope-paper"></i><span>Applicant Communications</span></a>
                <div class="nav-label">Account</div>
                <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"><i class="bi bi-bell"></i><span>Notifications</span></a>
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"><i class="bi bi-person-circle"></i><span>My Profile</span></a>
            @elseif(auth()->user()->isFinanceOfficer())
                <div class="nav-label">Finance</div>
                <a href="{{ route('finance_officer.welcome') }}" class="nav-link {{ request()->routeIs('finance_officer.welcome') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Welcome</span></a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                <a href="{{ route('finance.index') }}" class="nav-link {{ request()->routeIs('finance.index') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i><span>Finance Overview</span></a>
                <a href="{{ route('fees.index') }}" class="nav-link {{ request()->routeIs('fees.*') ? 'active' : '' }}"><i class="bi bi-cash-stack"></i><span>Fee Payments</span></a>
                <a href="{{ route('fees.structures') }}" class="nav-link {{ request()->routeIs('fees.structures') ? 'active' : '' }}"><i class="bi bi-receipt"></i><span>Fee Structures</span></a>
                <a href="{{ route('fees.create') }}" class="nav-link {{ request()->routeIs('fees.create') ? 'active' : '' }}"><i class="bi bi-file-earmark-plus"></i><span>Student Billing</span></a>
                <a href="{{ route('finance.refunds') }}" class="nav-link {{ request()->routeIs('finance.refunds') ? 'active' : '' }}"><i class="bi bi-arrow-counterclockwise"></i><span>Payments &amp; Refunds</span></a>
                <a href="{{ route('hr.payroll-records.index') }}" class="nav-link {{ request()->routeIs('hr.payroll-*') ? 'active' : '' }}"><i class="bi bi-wallet2"></i><span>Staff Payroll</span></a>
                <a href="{{ route('finance.budget') }}" class="nav-link {{ request()->routeIs('finance.budget') ? 'active' : '' }}"><i class="bi bi-pie-chart"></i><span>Budget Tracking</span></a>
                <a href="{{ route('reports.fees') }}" class="nav-link {{ request()->routeIs('reports.fees') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i><span>Financial Reports</span></a>
                <a href="{{ route('admin.scholarships.index') }}" class="nav-link {{ request()->routeIs('admin.scholarships.*') ? 'active' : '' }}"><i class="bi bi-award"></i><span>Scholarships</span></a>
                <a href="{{ route('finance.audit') }}" class="nav-link {{ request()->routeIs('finance.audit') ? 'active' : '' }}"><i class="bi bi-journal-text"></i><span>Audit History</span></a>
            @elseif(auth()->user()->isRegistrar())
                <div class="nav-label">Registrar</div>
                <a href="{{ route('registrar.welcome') }}" class="nav-link {{ request()->routeIs('registrar.welcome') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Welcome</span></a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}"><i class="bi bi-person-vcard"></i><span>Student Records</span></a>
                <a href="{{ route('registrar.enrollment') }}" class="nav-link {{ request()->routeIs('registrar.enrollment') ? 'active' : '' }}"><i class="bi bi-journal-plus"></i><span>Course Enrollment</span></a>
                <a href="{{ route('registrar.transcripts.index') }}" class="nav-link {{ request()->routeIs('registrar.transcripts.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i><span>Transcripts</span></a>
                <a href="{{ route('registrar.certificates') }}" class="nav-link {{ request()->routeIs('registrar.certificates') ? 'active' : '' }}"><i class="bi bi-award"></i><span>Certificates</span></a>
                <a href="{{ route('registrar.academic-standing.index') }}" class="nav-link {{ request()->routeIs('registrar.academic-standing.*') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i><span>Academic Standing</span></a>
                <a href="{{ route('registrar.graduation.index') }}" class="nav-link {{ request()->routeIs('registrar.graduation.*') ? 'active' : '' }}"><i class="bi bi-check2-square"></i><span>Graduation Clearance</span></a>
                <a href="{{ route('registrar.exam-approvals.index') }}" class="nav-link {{ request()->routeIs('registrar.exam-approvals.*') ? 'active' : '' }}"><i class="bi bi-check2-square"></i><span>Exam Approvals</span></a>
                <a href="{{ route('subjects.index') }}" class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}"><i class="bi bi-journal-bookmark"></i><span>Course Catalog</span></a>
                <a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}"><i class="bi bi-building"></i><span>Class Sections</span></a>
                <a href="{{ route('timetables.index') }}" class="nav-link {{ request()->routeIs('timetables.*') ? 'active' : '' }}"><i class="bi bi-calendar3"></i><span>Schedules</span></a>
                <a href="{{ route('registrar.transfer-credits') }}" class="nav-link {{ request()->routeIs('registrar.transfer-credits') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i><span>Transfer Credits</span></a>
            @elseif(auth()->user()->isHROfficer())
                <div class="nav-label">Human Resources</div>
                <a href="{{ route('hr_officer.welcome') }}" class="nav-link {{ request()->routeIs('hr_officer.welcome') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Welcome</span></a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                <a href="{{ route('hr.employees.index') }}" class="nav-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Employees</span></a>
                <a href="{{ route('hr.recruitment') }}" class="nav-link {{ request()->routeIs('hr.recruitment') ? 'active' : '' }}"><i class="bi bi-person-plus"></i><span>Recruitment</span></a>
                <a href="{{ route('hr.leave-requests.index') }}" class="nav-link {{ request()->routeIs('hr.leave-requests.*') ? 'active' : '' }}"><i class="bi bi-calendar2-check"></i><span>Leave Requests</span></a>
                <a href="{{ route('hr.leave-types.index') }}" class="nav-link {{ request()->routeIs('hr.leave-types.*') ? 'active' : '' }}"><i class="bi bi-list-check"></i><span>Leave Types</span></a>
                <a href="{{ route('hr.attendance') }}" class="nav-link {{ request()->routeIs('hr.attendance') ? 'active' : '' }}"><i class="bi bi-clock-history"></i><span>Attendance</span></a>
                <a href="{{ route('hr.payroll') }}" class="nav-link {{ request()->routeIs('hr.payroll*') ? 'active' : '' }}"><i class="bi bi-wallet2"></i><span>Payroll</span></a>
                <a href="{{ route('hr.performance') }}" class="nav-link {{ request()->routeIs('hr.performance') ? 'active' : '' }}"><i class="bi bi-graph-up"></i><span>Performance Reviews</span></a>
            @elseif(auth()->user()->isLibrarian())
                <div class="nav-label">Library</div>
                <a href="{{ route('librarian.welcome') }}" class="nav-link {{ request()->routeIs('librarian.welcome') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Welcome</span></a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                <a href="{{ route('library.books.index') }}" class="nav-link {{ request()->routeIs('library.books.*') ? 'active' : '' }}"><i class="bi bi-book"></i><span>Book Catalog</span></a>
                <a href="{{ route('library.borrowings.index') }}" class="nav-link {{ request()->routeIs('library.borrowings.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i><span>Borrowings</span></a>
                <a href="{{ route('library.reservations.index') }}" class="nav-link {{ request()->routeIs('library.reservations.*') ? 'active' : '' }}"><i class="bi bi-bookmark"></i><span>Reservations</span></a>
            @endif

            @if(auth()->user()->isTeacher())
                <div class="nav-label">Teacher LMS</div>
                <a href="{{ route('teacher.welcome') }}" class="nav-link {{ request()->routeIs('teacher.welcome') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span>Welcome</span>
                </a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('teacher.classes') }}" class="nav-link {{ request()->routeIs('teacher.classes') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>My Classes</span>
                </a>
                <a href="{{ route('teacher.courses') }}" class="nav-link {{ request()->routeIs('teacher.courses') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark"></i>
                    <span>My Courses</span>
                </a>
                <a href="{{ route('teacher.gradebook') }}" class="nav-link {{ request()->routeIs('teacher.gradebook') ? 'active' : '' }}">
                    <i class="bi bi-table"></i>
                    <span>Gradebook</span>
                </a>
                <a href="{{ route('teacher.discussions.index') }}" class="nav-link {{ request()->routeIs('teacher.discussions.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-square-text"></i>
                    <span>Discussions</span>
                </a>
                <a href="{{ route('teacher.question-bank.index') }}" class="nav-link {{ request()->routeIs('teacher.question-bank.*') ? 'active' : '' }}"><i class="bi bi-question-circle"></i><span>Question Bank</span></a>
                <a href="{{ route('teacher.videos') }}" class="nav-link {{ request()->routeIs('teacher.videos') ? 'active' : '' }}">
                    <i class="bi bi-play-circle"></i>
                    <span>Videos</span>
                </a>
                <a href="{{ route('teacher.materials') }}" class="nav-link {{ request()->routeIs('teacher.materials') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>Materials</span>
                </a>
                <a href="{{ route('teacher.attendance') }}" class="nav-link {{ request()->routeIs('teacher.attendance') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Take Attendance</span>
                </a>
                <a href="{{ route('advising.index') }}" class="nav-link {{ request()->routeIs('advising.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check"></i>
                    <span>Academic Advising</span>
                </a>
                <a href="{{ route('advising.dashboard') }}" class="nav-link {{ request()->routeIs('advising.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer"></i>
                    <span>Advisor Dashboard</span>
                </a>
                <a href="{{ route('teacher.tests.index') }}" class="nav-link {{ request()->routeIs('teacher.tests.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Tests</span>
                </a>
                <a href="{{ route('teacher.live-classes.index') }}" class="nav-link {{ request()->routeIs('teacher.live-classes*') || request()->routeIs('live-classes*') ? 'active' : '' }}">
                    <i class="bi bi-camera-video"></i>
                    <span>Live Classes</span>
                </a>
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Profile</span>
                </a>
            @elseif(auth()->user()->isStudent())
                <div class="nav-label">Student LMS</div>
                <a href="{{ route('student.welcome') }}" class="nav-link {{ request()->routeIs('student.welcome') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span>Welcome</span>
                </a>
                <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('student.courses') }}" class="nav-link {{ request()->routeIs('student.courses') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark"></i>
                    <span>My Courses</span>
                </a>
                <a href="{{ route('student.gradebook') }}" class="nav-link {{ request()->routeIs('student.gradebook') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i>
                    <span>My Gradebook</span>
                </a>
                <a href="{{ route('student.discussions.index') }}" class="nav-link {{ request()->routeIs('student.discussions.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-square-text"></i>
                    <span>Discussions</span>
                </a>
                <a href="{{ route('student.tests.index') }}" class="nav-link {{ request()->routeIs('student.tests.*') ? 'active' : '' }}"><i class="bi bi-ui-checks"></i><span>Quizzes</span></a>
                <a href="{{ route('student.registration') }}" class="nav-link {{ request()->routeIs('student.registration') ? 'active' : '' }}"><i class="bi bi-journal-plus"></i><span>Course Registration</span></a>
                <a href="{{ route('student.schedule') }}" class="nav-link {{ request()->routeIs('student.schedule') ? 'active' : '' }}"><i class="bi bi-calendar3"></i><span>Class Schedule</span></a>
                <a href="{{ route('student.academic-records') }}" class="nav-link {{ request()->routeIs('student.academic-records') ? 'active' : '' }}"><i class="bi bi-file-earmark-bar-graph"></i><span>Academic Records</span></a>
                <a href="{{ route('advising.index') }}" class="nav-link {{ request()->routeIs('advising.*') ? 'active' : '' }}"><i class="bi bi-person-check"></i><span>Academic Advising</span></a>
                <a href="{{ route('student.live-classes') }}" class="nav-link {{ request()->routeIs('student.live-classes') ? 'active' : '' }}">
                    <i class="bi bi-camera-video"></i>
                    <span>Live Classes</span>
                </a>
                <a href="{{ route('student.videos') }}" class="nav-link {{ request()->routeIs('student.videos') ? 'active' : '' }}">
                    <i class="bi bi-play-circle"></i>
                    <span>Videos</span>
                </a>
                <a href="{{ route('student.materials') }}" class="nav-link {{ request()->routeIs('student.materials') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>Materials</span>
                </a>
                <a href="{{ route('student.assignments') }}" class="nav-link {{ request()->routeIs('student.assignments') ? 'active' : '' }}">
                    <i class="bi bi-list-task"></i>
                    <span>Assignments</span>
                </a>
                <a href="{{ route('student.tests.index') }}" class="nav-link {{ request()->routeIs('student.tests.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Tests</span>
                </a>
                <a href="{{ route('student.attendance') }}" class="nav-link {{ request()->routeIs('student.attendance') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Attendance</span>
                </a>
                <a href="{{ route('student.fees') }}" class="nav-link {{ request()->routeIs('student.fees') ? 'active' : '' }}"><i class="bi bi-cash-stack"></i><span>Fees &amp; Payments</span></a>
                <a href="{{ route('student.library') }}" class="nav-link {{ request()->routeIs('student.library') ? 'active' : '' }}"><i class="bi bi-book"></i><span>Library Account</span></a>
                <a href="{{ route('student.announcements') }}" class="nav-link {{ request()->routeIs('student.announcements') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span>Announcements</span>
                </a>
                <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"><i class="bi bi-bell"></i><span>Notifications</span></a>
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Profile</span>
                </a>
            @elseif(auth()->user()->isParent())
                <div class="nav-label">University Portal</div>
                <a href="{{ route('parent.welcome') }}" class="nav-link {{ request()->routeIs('parent.welcome') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span>Welcome</span>
                </a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('parent.children') }}" class="nav-link {{ request()->routeIs('parent.children') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>My Children</span>
                </a>
                <a href="{{ route('parent.profiles') }}" class="nav-link {{ request()->routeIs('parent.profiles') ? 'active' : '' }}"><i class="bi bi-person-vcard"></i><span>Student Profiles</span></a>
                <a href="{{ route('parent.academics') }}" class="nav-link {{ request()->routeIs('parent.academics') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i><span>Academic Progress</span></a>
                <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Attendance Register</span>
                </a>
                <a href="{{ route('attendance.report') }}" class="nav-link {{ request()->routeIs('attendance.report') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i>
                    <span>Attendance Report</span>
                </a>
                <a href="{{ route('parent.finance') }}" class="nav-link {{ request()->routeIs('parent.finance') ? 'active' : '' }}"><i class="bi bi-cash-stack"></i><span>Financial Oversight</span></a>
                <a href="{{ route('exams.results') }}" class="nav-link {{ request()->routeIs('exams.results') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Test Results</span>
                </a>
                <a href="{{ route('public.notices') }}" class="nav-link {{ request()->routeIs('public.notices') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span>Announcements</span>
                </a>
                <a href="{{ route('parent.communications') }}" class="nav-link {{ request()->routeIs('parent.communications') ? 'active' : '' }}"><i class="bi bi-megaphone"></i><span>University Communications</span></a>
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Profile</span>
                </a>
            @elseif(auth()->user()->isStaff())
                <div class="nav-label">University Administration Portal</div>
                <a href="{{ route('staff.welcome') }}" class="nav-link {{ request()->routeIs('staff.welcome') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span>Welcome</span>
                </a>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <div class="nav-label">Notices</div>
                <a href="{{ route('public.notices') }}" class="nav-link {{ request()->routeIs('public.notices') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span>Notices</span>
                </a>
                <a href="{{ route('staff.workspace.communications') }}" class="nav-link {{ request()->routeIs('staff.workspace.communications') ? 'active' : '' }}">
                    <i class="bi bi-broadcast"></i>
                    <span>Communications</span>
                </a>
                <div class="nav-label">Operations</div>
                <a href="{{ route('staff.workspace.time') }}" class="nav-link {{ request()->routeIs('staff.workspace.time*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Attendance &amp; Leave</span>
                </a>
                <a href="{{ route('staff.workspace.support') }}" class="nav-link {{ request()->routeIs('staff.workspace.support*') ? 'active' : '' }}">
                    <i class="bi bi-life-preserver"></i>
                    <span>Student Helpdesk</span>
                </a>
                <a href="{{ route('staff.workspace.maintenance') }}" class="nav-link {{ request()->routeIs('staff.workspace.maintenance*') ? 'active' : '' }}">
                    <i class="bi bi-tools"></i>
                    <span>Maintenance Requests</span>
                </a>
                <a href="{{ route('staff.workspace.hr') }}" class="nav-link {{ request()->routeIs('staff.workspace.hr') ? 'active' : '' }}">
                    <i class="bi bi-person-vcard"></i>
                    <span>My HR Overview</span>
                </a>
                <div class="nav-label">Student Services</div>
                <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Students Directory</span>
                </a>
                <a href="{{ route('students.create') }}" class="nav-link {{ request()->routeIs('students.create') ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i>
                    <span>Add Student</span>
                </a>
                <a href="{{ route('parents.index') }}" class="nav-link {{ request()->routeIs('parents.*') ? 'active' : '' }}">
                    <i class="bi bi-person-heart"></i>
                    <span>Parents & Guardians</span>
                </a>
                <a href="{{ route('parents.create') }}" class="nav-link {{ request()->routeIs('parents.create') ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i>
                    <span>Add Parent</span>
                </a>
                <div class="nav-label">Admissions</div>
                <a href="{{ route('admin.applications') }}" class="nav-link {{ request()->routeIs('admin.applications*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-person"></i>
                    <span>Applications</span>
                </a>
                <div class="nav-label">Profile</div>
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>My Profile</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.applications') }}" class="nav-link {{ request()->routeIs('admin.applications*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-person"></i>
                    <span>Applications</span>
                </a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>User Accounts</span>
                    </a>

                    <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                        <i class="bi bi-briefcase"></i>
                        <span>Staff Management</span>
                    </a>
                    <div class="nav-label">Administrative Roles</div>
                    @foreach([
                        ['admissions_officer.index', 'admissions_officer.*', 'Admissions Officers', 'bi-person-lines-fill'],
                        ['finance_officer.index', 'finance_officer.*', 'Finance Officers', 'bi-cash-stack'],
                        ['registrar.index', 'registrar.*', 'Registrars', 'bi-mortarboard'],
                        ['hr_officer.index', 'hr_officer.*', 'HR Officers', 'bi-people'],
                        ['librarian.index', 'librarian.*', 'Librarians', 'bi-book'],
                    ] as [$routeName, $activePattern, $label, $icon])
                        <a href="{{ route($routeName) }}" class="nav-link {{ request()->routeIs($activePattern) ? 'active' : '' }}"><i class="bi {{ $icon }}"></i><span>{{ $label }}</span></a>
                    @endforeach
                @endif

                <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="bi bi-person-vcard"></i>
                    <span>Students</span>
                    <span class="nav-badge">{{ \App\Models\Student::count() }}</span>
                </a>

                <a href="{{ route('teachers.index') }}" class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                    <i class="bi bi-person-workspace"></i>
                    <span>Teachers</span>
                </a>

                <a href="{{ route('parents.index') }}" class="nav-link {{ request()->routeIs('parents.*') ? 'active' : '' }}">
                    <i class="bi bi-person-heart"></i>
                    <span>Parents & Guardians</span>
                </a>
            @endif

            @if(!auth()->user()->isParent())
                <div class="nav-label">Academics</div>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                <a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>Classes & Sections</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <a href="{{ route('subjects.index') }}" class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i>
                    <span>Subjects</span>
                </a>

                <a href="{{ route('timetables.index') }}" class="nav-link {{ request()->routeIs('timetables.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar3-range"></i>
                    <span>Timetable Schedule</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Daily Attendance Register</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                <a href="{{ route('exams.index') }}" class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i>
                    <span>Examinations</span>
                </a>
            @elseif(auth()->user()->isStudent())
                <a href="{{ route('exams.results') }}" class="nav-link {{ request()->routeIs('exams.results') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Results</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <div class="nav-label">Finance & Billing</div>
                <a href="{{ route('fees.index') }}" class="nav-link {{ request()->routeIs('fees.index') || request()->routeIs('fees.create') || request()->routeIs('fees.show') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i>
                    <span>Fee Collection</span>
                </a>
                <a href="{{ route('fees.structures') }}" class="nav-link {{ request()->routeIs('fees.structures') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff"></i>
                    <span>Fee Structures</span>
                </a>
            @elseif(auth()->user()->isStudent() && auth()->user()->student)
                <div class="nav-label">Finance & Billing</div>
                <a href="{{ route('fees.student', auth()->user()->student) }}" class="nav-link {{ request()->routeIs('fees.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    <span>My Fee Ledger</span>
                </a>
            @endif

            @if(!auth()->user()->isParent() && !auth()->user()->isStaff())
                <a href="{{ route('public.notices') }}" class="nav-link {{ request()->routeIs('public.notices') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span>Notices</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                <div class="nav-label">Reports & Analytics</div>

                <a href="{{ route('reports.attendance') }}" class="nav-link {{ request()->routeIs('reports.attendance') ? 'active' : '' }}">
                    <i class="bi bi-calendar-range"></i>
                    <span>Attendance Report</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('reports.fees') }}" class="nav-link {{ request()->routeIs('reports.fees') ? 'active' : '' }}">
                        <i class="bi bi-pie-chart"></i>
                        <span>Fee Collection Report</span>
                    </a>
                @endif

                <a href="{{ route('reports.academic') }}" class="nav-link {{ request()->routeIs('reports.academic') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Academic Matrix</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isRegistrar())
                <div class="nav-label">Administration</div>
                <a href="{{ route('admin.academics.index') }}" class="nav-link {{ request()->routeIs('admin.academics.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i>
                    <span>Academic Foundation</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.notices.index') }}" class="nav-link {{ request()->routeIs('admin.notices.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span>Notice Board</span>
                </a>
                <a href="{{ route('admin.facilities.index') }}" class="nav-link {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
                    <i class="bi bi-building-gear"></i>
                    <span>Facilities &amp; Resources</span>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Audit Logs</span>
                </a>
                <a href="{{ route('admin.notices.videos') }}" class="nav-link ps-5 {{ request()->routeIs('admin.notices.videos') ? 'active' : '' }}">
                    <i class="bi bi-camera-video"></i>
                    <span>Videos / Media</span>
                </a>

                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>School Settings</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i>
                    <span>My Profile & Security</span>
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link logout-btn">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <script>
        (function () {
            try {
                var sidebar = document.getElementById('sidebar');
                var savedScrollTop = Number.parseFloat(sessionStorage.getItem('tima_ade_sidebar_scroll_top') || '');

                if (sidebar && Number.isFinite(savedScrollTop)) {
                    sidebar.style.scrollBehavior = 'auto';
                    sidebar.scrollTop = savedScrollTop;
                    sidebar.dataset.sidebarScrollRestored = 'true';
                }
            } catch (error) {
                // Storage may be unavailable; the regular app script remains the fallback.
            }
        }());
    </script>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="main-wrapper" id="mainWrapper">

        <!-- Top Navigation Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="topbarToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Home</a>
                        </li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>

            <div class="topbar-right">
                <div class="topbar-search">
                    @include('components.dashboard-search')
                </div>

                <!-- Quick Actions for Admins & Teachers -->
                @if(auth()->user()->isAdmin())
                    <div class="dropdown me-2">
                        <button class="topbar-btn" data-bs-toggle="dropdown" title="Quick Actions">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                            <li><h6 class="dropdown-header">Quick Actions</h6></li>
                            <li><a class="dropdown-item" href="{{ route('students.create') }}"><i class="bi bi-person-plus me-2 text-primary"></i>New Student</a></li>
                            <li><a class="dropdown-item" href="{{ route('teachers.create') }}"><i class="bi bi-person-plus me-2 text-success"></i>New Teacher</a></li>
                            <li><a class="dropdown-item" href="{{ route('parents.create') }}"><i class="bi bi-person-heart me-2 text-warning"></i>New Parent</a></li>
                            <li><a class="dropdown-item" href="{{ route('attendance.create') }}"><i class="bi bi-calendar-plus me-2 text-info"></i>Mark Attendance</a></li>
                            <li><a class="dropdown-item" href="{{ route('fees.create') }}"><i class="bi bi-cash me-2 text-danger"></i>Record Fee Payment</a></li>
                            <li><a class="dropdown-item" href="{{ route('timetables.create') }}"><i class="bi bi-clock me-2 text-secondary"></i>Add Timetable Slot</a></li>
                        </ul>
                    </div>
                @elseif(auth()->user()->isTeacher())
                    <div class="dropdown me-2">
                        <button class="topbar-btn" data-bs-toggle="dropdown" title="Quick Actions">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                            <li><h6 class="dropdown-header">Teacher Actions</h6></li>
                            <li><a class="dropdown-item" href="{{ route('attendance.create') }}"><i class="bi bi-calendar-plus me-2 text-primary"></i>Mark Roll Call</a></li>
                            <li><a class="dropdown-item" href="{{ route('exams.index') }}"><i class="bi bi-journal-check me-2 text-success"></i>Enter Exam Marks</a></li>
                        </ul>
                    </div>
                @endif

                <!-- Notifications Dropdown (user-scoped) -->
                @php
                    $currentUser = auth()->user();
                    $roleSlug = $currentUser->role->slug ?? 'all';

                    // Notifications targeted to this user (or broadcasts for this role/all). We limit results for performance.
                    $notifications = \App\Models\Notification::where(function($q) use ($currentUser, $roleSlug) {
                        $q->where('user_id', $currentUser->id)
                          ->orWhere(function($q2) use ($roleSlug) {
                              $q2->whereNull('user_id')
                                 ->whereIn('role', ['all', $roleSlug]);
                          });
                    })->latest()->take(6)->get();

                    // Count unread for scoped notifications (this includes broadcasts that are unread)
                    $unreadCount = \App\Models\Notification::where(function($q) use ($currentUser, $roleSlug) {
                        $q->where('user_id', $currentUser->id)
                          ->orWhere(function($q2) use ($roleSlug) {
                              $q2->whereNull('user_id')
                                 ->whereIn('role', ['all', $roleSlug]);
                          });
                    })->where('read', false)->count();
                @endphp

                <div class="dropdown me-2" id="notifications">
                    <button type="button" class="topbar-btn position-relative" data-bs-toggle="dropdown" title="Notifications" aria-label="Notifications" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unreadCount }}</span>
                        @endif
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 360px;">
                        <li class="px-3 py-2 d-flex justify-content-between align-items-center">
                            <div class="fw-semibold">Notifications</div>
                            <div class="d-flex gap-2">
                                <form method="POST" action="{{ route('notifications.readAll') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link">Mark all read</button>
                                </form>
                                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link">View all</a>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>

                        @forelse($notifications as $n)
                            <li>
                                <div class="dropdown-item d-flex justify-content-between align-items-start py-2">
                                    <div class="me-2" style="flex:1">
                                        <div class="fw-semibold small text-dark{{ $n->read ? '' : ' text-primary' }}">{{ Str::limit($n->title, 60) }}</div>
                                        <div class="text-muted small">{{ Str::limit($n->body ?? '', 80) }}</div>
                                        <div class="text-muted x-small mt-1" style="font-size:0.72rem">{{ $n->created_at->diffForHumans() }}</div>
                                    </div>

                                    <div class="ms-2 text-end">
                                        @if($n->user_id === auth()->id())
                                            <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Mark read</button>
                                            </form>
                                        @else
                                            <span class="badge bg-secondary">&nbsp;</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li><span class="dropdown-item text-muted small py-3 text-center">No notifications</span></li>
                        @endforelse
                    </ul>
                </div>

                <!-- Current date opens the existing monthly calendar -->
                <button type="button" class="topbar-date" data-bs-toggle="modal" data-bs-target="#dashboardCalendarModal" aria-label="Open monthly calendar">
                    <i class="bi bi-calendar3 me-2" aria-hidden="true"></i>
                    <span>
                        <small class="topbar-date-day">{{ now()->format('l') }}</small>
                        <strong>{{ now()->format('M j, Y') }}</strong>
                    </span>
                </button>

                <!-- User Profile Dropdown -->
                <div class="dropdown ms-2">
                    <button class="topbar-user" data-bs-toggle="dropdown" aria-label="Open user profile menu" aria-expanded="false">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }} avatar" class="topbar-avatar">
                        <span class="d-none d-md-inline">{{ Str::words(auth()->user()->name, 1, '') }}</span>
                        <i class="bi bi-chevron-down ms-1 small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                        <li>
                            <div class="dropdown-header-user">
                                <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="dropdown-avatar">
                                <div>
                                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                    <div class="small text-muted">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show') }}">
                                <i class="bi bi-person me-2 text-primary"></i>My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show') }}#security">
                                <i class="bi bi-shield-lock me-2 text-warning"></i>Change Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="modal fade" id="dashboardCalendarModal" tabindex="-1" aria-labelledby="dashboardCalendarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content dashboard-calendar-modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="dashboardCalendarModalLabel">Monthly calendar</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close monthly calendar"></button>
                    </div>
                    <div class="modal-body">
                        @include('components.dashboard-calendar')
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="page-content">
            @include('partials._alerts')
            @if(request()->routeIs('dashboard') || request()->routeIs('student.dashboard'))
                @include('partials.ai-card')
            @endif
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="page-footer">
            <span>&copy; {{ date('Y') }} Tima-Ade University. All rights reserved.</span>
        </footer>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        (function(){
            'use strict';

            // Confirmation for destructive actions: forms with data-confirm
            document.addEventListener('click', function(e){
                var btn = e.target.closest('button[data-confirm], a[data-confirm]');
                if(btn){
                    var message = btn.getAttribute('data-confirm');
                    if(!confirm(message)){
                        e.preventDefault();
                        return false;
                    }
                }
            });

            document.addEventListener('submit', function(e){
                var form = e.target;

                // If the form has a data-confirm attribute, confirm before submit
                if(form.hasAttribute('data-confirm')){
                    var message = form.getAttribute('data-confirm');
                    if(!confirm(message)){
                        e.preventDefault();
                        return false;
                    }
                }

                // Client-side validation for forms with class needs-validation (Bootstrap pattern)
                if(form.classList && form.classList.contains('needs-validation')){
                    if(!form.checkValidity()){
                        e.preventDefault();
                        e.stopPropagation();
                        form.classList.add('was-validated');
                        // Focus first invalid element
                        var invalid = form.querySelector(':invalid');
                        if(invalid) invalid.focus();
                        return false;
                    }

                    // Show loading state for valid submission
                    var submitBtn = form.querySelector('button[type="submit"]');
                    if(submitBtn){
                        var original = submitBtn.innerHTML;
                        var loading = submitBtn.getAttribute('data-loading-text') || 'Saving...';
                        submitBtn.innerHTML = loading;
                        submitBtn.disabled = true;
                        form.classList.add('loading');

                        // Re-enable after 10s as a fallback
                        setTimeout(function(){
                            // Only reset if still showing loading
                            if(submitBtn && submitBtn.disabled){
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = original;
                                form.classList.remove('loading');
                            }
                        }, 10000);
                    }
                }
            }, true);

            // Initialize DataTables for tables with class .datatable
            function initDataTables(){
                if(window.jQuery && $.fn.dataTable){
                    $('.datatable').each(function(){
                        if(!$.fn.dataTable.isDataTable(this)){
                            $(this).DataTable({
                                responsive: true,
                                lengthChange: true,
                                pageLength: 10,
                                columnDefs: [{ orderable: false, targets: -1 }],
                                language: { search: "Filter:", lengthMenu: "Show _MENU_ entries" }
                            });
                        }
                    });
                }
            }

            // Run once DOM is ready
            if(document.readyState === 'loading'){
                document.addEventListener('DOMContentLoaded', initDataTables);
            } else {
                initDataTables();
            }

            // Accessible skip link focus fix (if present)
            var skip = document.getElementById('skipToContent');
            if(skip){
                skip.addEventListener('click', function(e){
                    var target = document.getElementById('mainWrapper');
                    if(target){ target.setAttribute('tabindex', '-1'); target.focus(); }
                });
            }

        })();
    </script>

    @stack('scripts')
</body>
</html>
