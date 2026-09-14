import Link from 'next/link'
import {
  Bar,
  BarChart,
  CartesianGrid,
  Cell,
  Pie,
  PieChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from 'recharts'
import {
  AlertCircle,
  ArrowRight,
  BookOpen,
  Building2,
  CalendarCheck,
  CalendarDays,
  ChevronRight,
  ClipboardList,
  CreditCard,
  DollarSign,
  Download,
  FileBarChart2,
  GraduationCap,
  LayoutGrid,
  Megaphone,
  Plus,
  School,
  TrendingUp,
  UserPlus,
  Users,
} from 'lucide-react'
import { Badge } from '../ui/badge'
import { Button } from '../ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card'
import {
  admissionsRows,
  attendanceTrend,
  departmentRows,
  enrollmentTrend,
  examRows,
  financeRows,
  overviewStats,
  programRows,
  reportRows,
  resultsSummary,
  studentRows,
  teacherRows,
  timetableRows,
} from '../../data/university-admin'

/* ─── mock operational data ─────────────────────────────────────────── */
const operationalStrip = [
  { label: 'Total Students',     value: '8,241',  icon: Users,          accent: 'bg-indigo-50 text-indigo-700',  border: 'border-indigo-200' },
  { label: 'Total Teachers',     value: '312',    icon: School,         accent: 'bg-sky-50    text-sky-700',     border: 'border-sky-200'    },
  { label: 'Departments',        value: '18',     icon: Building2,      accent: 'bg-violet-50 text-violet-700', border: 'border-violet-200' },
  { label: 'Active Programs',    value: '54',     icon: GraduationCap,  accent: 'bg-teal-50   text-teal-700',   border: 'border-teal-200'   },
  { label: 'Pending Admissions', value: '127',    icon: ClipboardList,  accent: 'bg-amber-50  text-amber-700',  border: 'border-amber-200'  },
]

const feeDonut = [
  { name: 'Tuition',   value: 68, fill: '#4338ca' },
  { name: 'Hostel',    value: 48, fill: '#0d9488' },
  { name: 'Library',   value: 88, fill: '#0f172a' },
  { name: 'Transport', value: 53, fill: '#d97706' },
]

const admissionFunnel = [
  { stage: 'Applied',   count: 540,  color: 'bg-indigo-600' },
  { stage: 'Screened',  count: 381,  color: 'bg-indigo-500' },
  { stage: 'Invited',   count: 245,  color: 'bg-sky-600'    },
  { stage: 'Offered',   count: 178,  color: 'bg-teal-600'   },
  { stage: 'Enrolled',  count: 127,  color: 'bg-emerald-600'},
]

const urgentNotices = [
  { text: '14 exam timetables not yet published',    type: 'warn'  },
  { text: 'Semester registration closes in 3 days',  type: 'warn'  },
  { text: '23 fee defaults — action required',        type: 'danger'},
  { text: 'New program accreditation submitted',      type: 'info'  },
]

const noticeColors = {
  warn:   'bg-amber-50 border-amber-200 text-amber-800',
  danger: 'bg-red-50   border-red-200   text-red-800',
  info:   'bg-indigo-50 border-indigo-200 text-indigo-800',
}

const statusTone: Record<string, 'success' | 'warning' | 'danger' | 'default' | 'info'> = {
  Active: 'success',
  'On Leave': 'warning',
  Probation: 'danger',
  Approved: 'success',
  Pending: 'warning',
  'In Progress': 'info',
  Healthy: 'success',
  'On schedule': 'success',
  Delayed: 'warning',
  Scheduled: 'info',
  Ready: 'success',
  Enabled: 'success',
}

export default function UniversityAdminDashboard() {
  return (
    <div className="space-y-6">

      {/* ── HEADER: University Identity Bar ────────────────────────────── */}
      <div className="rounded-3xl bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 p-6 shadow-soft">
        <div className="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
          <div className="flex items-start gap-4">
            <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/20">
              <School className="h-7 w-7 text-white" />
            </div>
            <div>
              <div className="mb-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-300">
                University Operations Center
              </div>
              <h1 className="text-2xl font-bold tracking-tight text-white">Tima-Ade University — Main Campus</h1>
              <p className="mt-1 text-sm text-indigo-200">
                Manage students, faculty, academics, admissions, finance, and campus operations.
              </p>
            </div>
          </div>

          <div className="flex flex-wrap gap-2.5">
            <Button size="sm" className="bg-white/10 border border-white/15 text-slate-200 hover:bg-white/20">
              <Download className="mr-2 h-3.5 w-3.5" />
              Export metrics
            </Button>
            <Button size="sm" className="bg-white/10 border border-white/15 text-slate-200 hover:bg-white/20">
              <Megaphone className="mr-2 h-3.5 w-3.5" />
              Post announcement
            </Button>
            <Button size="sm" className="bg-indigo-500 text-white hover:bg-indigo-400">
              <UserPlus className="mr-2 h-3.5 w-3.5" />
              Add student
            </Button>
          </div>
        </div>

        {/* Operational 5-metric strip */}
        <div className="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
          {operationalStrip.map((item) => {
            const Icon = item.icon
            return (
              <div key={item.label} className={`flex items-center gap-3 rounded-2xl border bg-white/8 px-4 py-3 ${item.border}`}>
                <div className={`flex h-9 w-9 items-center justify-center rounded-xl ${item.accent} bg-white/20`}>
                  <Icon className="h-4 w-4 text-white" />
                </div>
                <div>
                  <div className="text-xs text-indigo-200">{item.label}</div>
                  <div className="text-lg font-bold text-white">{item.value}</div>
                </div>
              </div>
            )
          })}
        </div>
      </div>

      {/* ── QUICK ACTIONS ROW ──────────────────────────────────────────── */}
      <div className="flex flex-wrap gap-3">
        {[
          { label: 'Add student',       icon: UserPlus,      href: '#students' },
          { label: 'Schedule exam',     icon: CalendarCheck, href: '#academics'},
          { label: 'Generate report',   icon: FileBarChart2, href: '#reports'  },
          { label: 'Manage timetable',  icon: CalendarDays,  href: '#academics'},
          { label: 'Fee collection',    icon: CreditCard,    href: '#finance'  },
          { label: 'New program',       icon: Plus,          href: '#academics'},
        ].map((action) => {
          const Icon = action.icon
          return (
            <a key={action.label} href={action.href}
              className="flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-medium text-indigo-700 transition-colors hover:bg-indigo-100 hover:border-indigo-300">
              <Icon className="h-4 w-4" />
              {action.label}
            </a>
          )
        })}
      </div>

      {/* ── ADMISSIONS FUNNEL + ENROLLMENT CHART ─────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1fr_1.2fr]" id="analytics">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Admissions pipeline</CardTitle>
            <CardDescription>Current semester applicant funnel by stage</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {admissionFunnel.map((stage) => {
                const pct = Math.round((stage.count / 540) * 100)
                return (
                  <div key={stage.stage}>
                    <div className="mb-1.5 flex items-center justify-between text-sm">
                      <span className="font-medium text-slate-700">{stage.stage}</span>
                      <span className="tabular-nums text-slate-500">{stage.count.toLocaleString()} <span className="text-slate-400">({pct}%)</span></span>
                    </div>
                    <div className="h-3 overflow-hidden rounded-full bg-slate-100">
                      <div className={`h-full rounded-full ${stage.color} transition-all`} style={{ width: `${pct}%` }} />
                    </div>
                  </div>
                )
              })}
            </div>
            <div className="mt-5 rounded-2xl border border-indigo-200 bg-indigo-50 p-3 text-sm text-indigo-700">
              <span className="font-semibold">127</span> applicants have enrolled this intake.{' '}
              <Link href="/university-admin/admissions" className="underline underline-offset-2">Review pending →</Link>
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-4">
            <div>
              <CardTitle>Monthly enrollment trend</CardTitle>
              <CardDescription>New students registered per month</CardDescription>
            </div>
            <Badge variant="info">This year</Badge>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="h-[260px] w-full">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={enrollmentTrend}>
                  <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                  <XAxis dataKey="month" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <YAxis tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <Tooltip contentStyle={{ borderRadius: 12, borderColor: '#e2e8f0' }} />
                  <Bar dataKey="students" radius={[8, 8, 0, 0]} fill="#4338ca" />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── URGENT NOTICES + FEE COLLECTION RING ─────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]" id="finance">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle>Urgent notices</CardTitle>
              <CardDescription>Items requiring administrative action today</CardDescription>
            </div>
            <AlertCircle className="h-5 w-5 text-amber-500" />
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {urgentNotices.map((n) => (
                <div key={n.text} className={`flex items-center justify-between rounded-xl border px-4 py-3 text-sm font-medium ${noticeColors[n.type as keyof typeof noticeColors]}`}>
                  <span>{n.text}</span>
                  <ChevronRight className="h-4 w-4 opacity-60 shrink-0" />
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Fee collection</CardTitle>
            <CardDescription>Collection progress by category</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="flex items-center justify-center">
              <div className="h-[180px] w-[180px]">
                <ResponsiveContainer width="100%" height="100%">
                  <PieChart>
                    <Pie data={feeDonut} dataKey="value" nameKey="name" innerRadius={52} outerRadius={82} paddingAngle={3}>
                      {feeDonut.map((entry) => (
                        <Cell key={entry.name} fill={entry.fill} />
                      ))}
                    </Pie>
                    <Tooltip formatter={(v: number) => [`${v}%`, 'Collected']} />
                  </PieChart>
                </ResponsiveContainer>
              </div>
            </div>
            <div className="mt-3 space-y-2">
              {feeDonut.map((item) => (
                <div key={item.name} className="flex items-center justify-between text-sm">
                  <div className="flex items-center gap-2">
                    <span className="h-2.5 w-2.5 rounded-full" style={{ backgroundColor: item.fill }} />
                    <span className="text-slate-600">{item.name}</span>
                  </div>
                  <span className="font-semibold text-slate-800">{item.value}%</span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── DEPARTMENT TABLE ─────────────────────────────────────────────── */}
      <Card className="border-slate-200 bg-white shadow-sm" id="academics">
        <CardHeader className="flex flex-row items-center justify-between gap-2">
          <div>
            <CardTitle>Departments &amp; faculties</CardTitle>
            <CardDescription>Department strength, program count, and operational status</CardDescription>
          </div>
          <Button variant="ghost" size="sm">
            Overview <ArrowRight className="ml-1 h-4 w-4" />
          </Button>
        </CardHeader>
        <CardContent className="p-6 pt-0">
          <div className="overflow-x-auto">
            <table className="min-w-full text-left text-sm">
              <thead>
                <tr className="border-b border-slate-200">
                  <th className="pb-3 font-medium text-slate-500">Department</th>
                  <th className="pb-3 font-medium text-slate-500">Head</th>
                  <th className="pb-3 font-medium text-slate-500">Programs</th>
                  <th className="pb-3 font-medium text-slate-500">Status</th>
                </tr>
              </thead>
              <tbody>
                {departmentRows.map((dept) => (
                  <tr key={dept.name} className="border-b border-slate-100 last:border-b-0 hover:bg-indigo-50/40 transition-colors">
                    <td className="py-3">
                      <div className="flex items-center gap-3">
                        <div className="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-[10px] font-bold text-indigo-700">
                          {dept.name.split(' ').map((w: string) => w[0]).slice(0,2).join('')}
                        </div>
                        <span className="font-semibold text-slate-900">{dept.name}</span>
                      </div>
                    </td>
                    <td className="py-3 text-slate-600">{dept.head}</td>
                    <td className="py-3 text-slate-600">{dept.programs} programs</td>
                    <td className="py-3"><Badge variant={statusTone[dept.status] ?? 'default'}>{dept.status}</Badge></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>

      {/* ── STUDENTS + TEACHERS ──────────────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]" id="students">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle>Student management</CardTitle>
              <CardDescription>Recent enrolments and academic status</CardDescription>
            </div>
            <Button variant="outline" size="sm">
              <Users className="mr-2 h-4 w-4" />
              Add student
            </Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="mb-4">
              <input type="text" placeholder="Search students…"
                className="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100" />
            </div>
            <div className="overflow-x-auto">
              <table className="min-w-full text-left text-sm">
                <thead>
                  <tr className="border-b border-slate-200 text-slate-500">
                    <th className="pb-3 font-medium">Student</th>
                    <th className="pb-3 font-medium">ID</th>
                    <th className="pb-3 font-medium">Faculty</th>
                    <th className="pb-3 font-medium">Status</th>
                  </tr>
                </thead>
                <tbody>
                  {studentRows.map((student) => (
                    <tr key={student.id} className="border-b border-slate-100 last:border-b-0">
                      <td className="py-3">
                        <div className="font-medium text-slate-800">{student.name}</div>
                        <div className="text-xs text-slate-500">{student.year}</div>
                      </td>
                      <td className="py-3 text-slate-600">{student.id}</td>
                      <td className="py-3 text-slate-600">{student.faculty}</td>
                      <td className="py-3"><Badge variant={statusTone[student.status] ?? 'default'}>{student.status}</Badge></td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm" id="teachers">
          <CardHeader>
            <CardTitle>Faculty overview</CardTitle>
            <CardDescription>Teaching staff and current assignments</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {teacherRows.map((lecturer) => (
                <div key={lecturer.name} className="rounded-2xl border border-slate-200 p-3.5">
                  <div className="flex items-start justify-between gap-3">
                    <div>
                      <div className="font-semibold text-slate-900">{lecturer.name}</div>
                      <div className="mt-1 text-xs text-slate-500">{lecturer.department}</div>
                    </div>
                    <Badge variant={statusTone[lecturer.status] ?? 'default'}>{lecturer.status}</Badge>
                  </div>
                  <div className="mt-2 text-xs text-indigo-700 font-medium">{lecturer.course}</div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── TIMETABLE + EXAMS ────────────────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Academic timetable</CardTitle>
            <CardDescription>Scheduled sessions this week</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-2.5">
              {timetableRows.slice(0, 5).map((slot) => (
                <div key={`${slot.day}-${slot.course}`}
                  className="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-2.5">
                  <div className="w-14 shrink-0 text-center">
                    <div className="rounded-lg bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">{slot.day}</div>
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="font-medium text-slate-800 truncate">{slot.course}</div>
                    <div className="text-xs text-slate-500">{slot.start} – {slot.end} · {slot.venue}</div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Exams &amp; assessments</CardTitle>
            <CardDescription>Scheduled exams and readiness status</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-2.5">
              {examRows.map((exam) => (
                <div key={exam.exam}
                  className="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-2.5">
                  <div>
                    <div className="font-medium text-slate-800">{exam.exam}</div>
                    <div className="text-xs text-slate-500">{exam.course}</div>
                  </div>
                  <Badge variant={statusTone[exam.status] ?? 'default'}>{exam.status}</Badge>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── ACADEMIC RESULTS SUMMARY ─────────────────────────────────────── */}
      <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" id="reports">
        {resultsSummary.map((stat) => (
          <Card key={stat.label} className="border-indigo-100 bg-indigo-50 shadow-sm">
            <CardContent className="p-5">
              <div className="flex items-center justify-between gap-2">
                <div className="text-sm font-medium text-indigo-700">{stat.label}</div>
                <TrendingUp className="h-4 w-4 text-indigo-400" />
              </div>
              <div className="mt-3 text-2xl font-bold text-indigo-900">{stat.value}</div>
              <div className="mt-1.5 inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-600">
                ↑ {stat.delta}
              </div>
            </CardContent>
          </Card>
        ))}
      </section>

    </div>
  )
}
