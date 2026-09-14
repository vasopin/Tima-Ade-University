import Link from 'next/link'
import { useRouter } from 'next/router'
import {
  Activity,
  AlertOctagon,
  AlertTriangle,
  BarChart3,
  Bell,
  BookOpen,
  Building2,
  Bus,
  CalendarCheck,
  CalendarDays,
  CheckSquare,
  ClipboardCheck,
  CreditCard,
  DoorClosed,
  FileBarChart2,
  FileText,
  Globe,
  GraduationCap,
  Home,
  KeyRound,
  LayoutDashboard,
  Library,
  LogOut,
  Mail,
  Megaphone,
  MessageCircle,
  MessageSquare,
  PenLine,
  PhoneCall,
  School,
  Server,
  Settings,
  ShieldCheck,
  Star,
  TrendingUp,
  Upload,
  User,
  UserCheck,
  UserCog,
  UserPlus,
  Users,
  Wallet,
  X,
} from 'lucide-react'
import { useAuth } from '../context/AuthContext'
import UniversityLogo from './UniversityLogo'

/* ─── Dedicated Role Navigation Routes ────────────────────────────────── */
export const navItemsByRole = {
  /* Super Admin — 10 dedicated routes */
  'super-admin': [
    { href: '/super-admin/overview',      label: 'Platform overview', icon: LayoutDashboard },
    { href: '/super-admin/analytics',     label: 'Growth analytics',  icon: BarChart3       },
    { href: '/super-admin/users',         label: 'User management',   icon: Users           },
    { href: '/super-admin/universities',    label: 'Universities',    icon: Building2       },
    { href: '/super-admin/roles',         label: 'Roles & access',    icon: KeyRound        },
    { href: '/super-admin/security',      label: 'Security events',   icon: AlertOctagon    },
    { href: '/super-admin/audit',         label: 'Audit log',         icon: FileText        },
    { href: '/super-admin/reports',       label: 'Reports',           icon: FileBarChart2   },
    { href: '/super-admin/notifications', label: 'Notifications',     icon: Bell            },
    { href: '/super-admin/settings',      label: 'System settings',   icon: Settings        },
  ],

  /* University Admin — 22 dedicated routes */
  'university-admin': [
    { href: '/university-admin/overview',      label: 'University overview', icon: LayoutDashboard },
    { href: '/university-admin/students',      label: 'Students',            icon: Users           },
    { href: '/university-admin/teachers',      label: 'Teachers / Faculty',  icon: School          },
    { href: '/university-admin/departments',   label: 'Departments',         icon: Building2       },
    { href: '/university-admin/faculties',     label: 'Faculties',           icon: Building2       },
    { href: '/university-admin/programs',      label: 'Programs',            icon: GraduationCap   },
    { href: '/university-admin/courses',       label: 'Courses / Subjects',  icon: BookOpen        },
    { href: '/university-admin/admissions',    label: 'Admissions',          icon: UserPlus        },
    { href: '/university-admin/attendance',    label: 'Attendance',          icon: ClipboardCheck  },
    { href: '/university-admin/timetable',     label: 'Timetable',           icon: CalendarDays    },
    { href: '/university-admin/exams',         label: 'Exams',               icon: CalendarCheck   },
    { href: '/university-admin/results',       label: 'Results',             icon: TrendingUp      },
    { href: '/university-admin/fees',          label: 'Fees',                icon: CreditCard      },
    { href: '/university-admin/finance',       label: 'Finance',             icon: Wallet          },
    { href: '/university-admin/library',       label: 'Library',             icon: Library         },
    { href: '/university-admin/hostel',        label: 'Hostel',              icon: DoorClosed      },
    { href: '/university-admin/transport',     label: 'Transport',           icon: Bus             },
    { href: '/university-admin/events',        label: 'Events',              icon: CalendarDays    },
    { href: '/university-admin/announcements', label: 'Announcements',       icon: Megaphone       },
    { href: '/university-admin/reports',       label: 'Reports',             icon: FileBarChart2   },
    { href: '/university-admin/notifications', label: 'Notifications',       icon: Bell            },
    { href: '/university-admin/settings',      label: 'Settings',            icon: Settings        },
  ],

  /* Teacher — 17 dedicated routes */
  teacher: [
    { href: '/teacher/overview',      label: 'Teacher overview',   icon: Star          },
    { href: '/teacher/classes',       label: 'My classes',         icon: Users         },
    { href: '/teacher/courses',       label: 'My courses',         icon: BookOpen      },
    { href: '/teacher/students',      label: 'Students',           icon: UserCheck     },
    { href: '/teacher/attendance',    label: 'Attendance',         icon: ClipboardCheck},
    { href: '/teacher/assignments',   label: 'Assignments',        icon: CheckSquare   },
    { href: '/teacher/submissions',   label: 'Submissions',        icon: PenLine       },
    { href: '/teacher/exams',         label: 'Exams',              icon: CalendarCheck },
    { href: '/teacher/grades',        label: 'Grades',             icon: GraduationCap },
    { href: '/teacher/timetable',     label: 'Timetable',          icon: CalendarDays  },
    { href: '/teacher/materials',     label: 'Course materials',   icon: Upload        },
    { href: '/teacher/announcements',  label: 'Announcements',     icon: Megaphone     },
    { href: '/teacher/messages',      label: 'Messages',           icon: MessageSquare },
    { href: '/teacher/notifications', label: 'Notifications',      icon: Bell          },
    { href: '/teacher/calendar',      label: 'Academic calendar',  icon: CalendarDays  },
    { href: '/teacher/profile',       label: 'Profile',            icon: User          },
    { href: '/teacher/settings',      label: 'Settings',           icon: Settings      },
  ],

  /* Student — 16 dedicated routes */
  student: [
    { href: '/student/overview',      label: 'Student overview',   icon: LayoutDashboard },
    { href: '/student/profile',       label: 'My profile',         icon: User            },
    { href: '/student/courses',       label: 'My courses',         icon: BookOpen        },
    { href: '/student/subjects',      label: 'Subjects',           icon: BookOpen        },
    { href: '/student/timetable',     label: 'Timetable',          icon: CalendarDays    },
    { href: '/student/attendance',    label: 'Attendance',         icon: CalendarCheck   },
    { href: '/student/assignments',   label: 'Assignments',        icon: CheckSquare     },
    { href: '/student/exams',         label: 'Exams',              icon: CalendarCheck   },
    { href: '/student/results',       label: 'Results',            icon: GraduationCap   },
    { href: '/student/gpa',           label: 'GPA & progress',     icon: TrendingUp      },
    { href: '/student/materials',     label: 'Learning materials', icon: Upload        },
    { href: '/student/fees',          label: 'Fees & payments',    icon: CreditCard      },
    { href: '/student/messages',      label: 'Messages',           icon: Mail            },
    { href: '/student/notifications', label: 'Notifications',      icon: Bell            },
    { href: '/student/calendar',      label: 'Academic calendar',  icon: CalendarDays    },
    { href: '/student/settings',      label: 'Settings',           icon: Settings        },
  ],

  /* Parent — 18 dedicated routes */
  parent: [
    { href: '/parent/overview',      label: 'Parent overview',     icon: LayoutDashboard },
    { href: '/parent/children',      label: 'My children',         icon: Users           },
    { href: '/parent/profile',       label: 'Child profile',       icon: User            },
    { href: '/parent/attendance',    label: 'Attendance',          icon: CalendarDays    },
    { href: '/parent/performance',   label: 'Academic performance',icon: TrendingUp      },
    { href: '/parent/results',       label: 'Grades & results',    icon: GraduationCap   },
    { href: '/parent/courses',       label: 'Courses',             icon: BookOpen        },
    { href: '/parent/timetable',     label: 'Timetable',           icon: CalendarDays    },
    { href: '/parent/assignments',   label: 'Assignments',        icon: ClipboardCheck  },
    { href: '/parent/exams',         label: 'Exams',               icon: CalendarCheck   },
    { href: '/parent/fees',          label: 'Fees & payments',     icon: CreditCard      },
    { href: '/parent/announcements', label: 'School announcements',icon: Megaphone       },
    { href: '/parent/messages',      label: 'Messages',            icon: PhoneCall       },
    { href: '/parent/notifications', label: 'Notifications',       icon: Bell            },
    { href: '/parent/calendar',      label: 'Academic calendar',   icon: CalendarDays    },
    { href: '/parent/events',        label: 'Events',              icon: CalendarDays    },
    { href: '/parent/profile-settings', label: 'Profile',          icon: User            },
    { href: '/parent/settings',      label: 'Settings',            icon: Settings        },
  ],
}

/* ─── Role-specific sidebar accent colors ─────────────────────────────── */
const roleAccent: Record<string, { active: string; badge: string; dot: string }> = {
  'super-admin':     { active: 'bg-crimson-700/20 text-white ring-1 ring-crimson-500/40',    badge: 'bg-crimson-600', dot: 'bg-crimson-500' },
  'university-admin':{ active: 'bg-indigo-700/20  text-white ring-1 ring-indigo-500/40',     badge: 'bg-indigo-600',  dot: 'bg-indigo-400'  },
  teacher:           { active: 'bg-teal-700/20    text-white ring-1 ring-teal-500/40',       badge: 'bg-teal-600',    dot: 'bg-teal-400'    },
  student:           { active: 'bg-violet-700/20  text-white ring-1 ring-violet-500/40',     badge: 'bg-violet-600',  dot: 'bg-violet-400'  },
  parent:            { active: 'bg-amber-700/20   text-white ring-1 ring-amber-500/40',      badge: 'bg-amber-600',   dot: 'bg-amber-400'   },
}

/* ─── Role display labels ─────────────────────────────────────────────── */
const roleDisplay: Record<string, string> = {
  'super-admin':      'Super Admin',
  'university-admin': 'University Admin',
  teacher:            'Teacher',
  student:            'Student',
  parent:             'Parent / Guardian',
}

/* ─── Role subtitle lines ─────────────────────────────────────────────── */
const roleSubtitle: Record<string, string> = {
  'super-admin':      'Platform Control',
  'university-admin': 'Operations Center',
  teacher:            'Teaching Workspace',
  student:            'Academic Portal',
  parent:             'Monitoring Center',
}

export default function Sidebar({ mobileOpen, onMobileClose }: { mobileOpen: boolean; onMobileClose: () => void }) {
  const { user, signOut } = useAuth()
  const router = useRouter()
  const role = (user?.role ?? 'super-admin') as keyof typeof navItemsByRole
  const navItems = navItemsByRole[role] ?? navItemsByRole['super-admin']
  const accent = roleAccent[role] ?? roleAccent['super-admin']

  return (
    <>
      {mobileOpen && <button type="button" aria-label="Close navigation menu" onClick={onMobileClose} className="fixed inset-0 z-40 bg-slate-950/60 lg:hidden" />}
    <aside className={["fixed inset-y-0 left-0 z-50 flex h-screen w-[min(18rem,calc(100vw-2rem))] shrink-0 -translate-x-full flex-col bg-slate-950 text-slate-100 shadow-2xl transition-transform duration-200 lg:static lg:z-auto lg:h-screen lg:w-72 lg:translate-x-0 lg:shadow-none", mobileOpen ? 'translate-x-0' : ''].join(' ')}>
      {/* Logo */}
      <Link href="/" className="flex h-20 items-center border-b border-white/10 px-5 group hover:bg-white/5 transition-colors">
        <UniversityLogo
          size="md"
          showText
          href="/"
          className="text-white"
          imageClassName="h-12 w-12 shrink-0 object-contain"
        />
      </Link>
      <button type="button" onClick={onMobileClose} aria-label="Close navigation menu" className="absolute right-3 top-5 inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-300 hover:bg-white/10 hover:text-white lg:hidden">
        <X className="h-5 w-5" />
      </button>

      {/* User card with role accent */}
      <div className="mx-4 mt-4 rounded-2xl border border-white/10 bg-white/5 p-3">
        <div className="flex items-center gap-3">
          <div className={`flex h-11 w-11 items-center justify-center rounded-full text-sm font-semibold text-white ${accent.badge}`}>
            {user?.name?.split(' ').map((part) => part[0]).slice(0, 2).join('') || 'MC'}
          </div>
          <div className="min-w-0">
            <div className="truncate text-sm font-semibold text-white">{user?.name || 'Demo User'}</div>
            <div className="flex items-center gap-1.5 mt-0.5">
              <span className={`h-1.5 w-1.5 rounded-full ${accent.dot}`} />
              <div className="truncate text-[11px] text-slate-400">{roleSubtitle[role] ?? 'Portal'}</div>
            </div>
          </div>
        </div>
        <div className="mt-2.5 rounded-lg bg-white/5 px-2.5 py-1.5 text-center text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">
          {roleDisplay[role] ?? 'User'}
        </div>
      </div>

      {/* Navigation */}
      <nav className="mt-6 flex-1 space-y-1 px-3 overflow-y-auto">
        {navItems.map(({ href, label, icon: Icon }) => {
          const isActive = router.pathname === href

          return (
            <Link
              key={href}
              href={href}
              onClick={onMobileClose}
              className={[
                'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors',
                isActive ? accent.active : 'text-slate-400 hover:bg-white/5 hover:text-white',
              ].join(' ')}
            >
              <Icon className="h-4 w-4 shrink-0" />
              <span>{label}</span>
            </Link>
          )
        })}
      </nav>

      {/* Footer Navigation & Sign out */}
      <div className="border-t border-white/10 p-3 space-y-1">
        <Link
          href="/"
          className="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-300 transition-colors hover:bg-white/5 hover:text-white"
        >
          <Globe className="h-4 w-4 text-rose-400" />
          <span>Public Website</span>
        </Link>
        <button
          type="button"
          onClick={signOut}
          className="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400 transition-colors hover:bg-white/5 hover:text-white"
        >
          <LogOut className="h-4 w-4" />
          <span>Sign out</span>
        </button>
      </div>
    </aside>
    </>
  )
}
