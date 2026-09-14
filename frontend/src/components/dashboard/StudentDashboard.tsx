import {
  Bar,
  BarChart,
  CartesianGrid,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from 'recharts'
import {
  Bell,
  BookOpen,
  CalendarDays,
  CheckCircle2,
  Clock,
  CreditCard,
  FileText,
  GraduationCap,
  LayoutGrid,
  Mail,
  TrendingUp,
  Upload,
  User,
} from 'lucide-react'
import { Badge } from '../ui/badge'
import { Button } from '../ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card'
import {
  assignmentRows,
  attendanceTrend,
  courseRows,
  examRows,
  feeRows,
  gpaTrend,
  materialRows,
  messageRows,
  notificationRows,
  resultsRows,
  studentStats,
  submissionRows,
  timetableRows,
} from '../../data/student'

/* ─── deadline countdown strip ─────────────────────────────────────── */
const deadlines = [
  { title: 'Database Assignment',  course: 'CS301',   due: '2 days',  type: 'assignment', urgency: 'high'   },
  { title: 'Midterm Exam',         course: 'MTH201',  due: '5 days',  type: 'exam',       urgency: 'medium' },
  { title: 'Project Proposal',     course: 'CS401',   due: '1 week',  type: 'assignment', urgency: 'low'    },
  { title: 'Algorithms Quiz',      course: 'CS201',   due: '2 weeks', type: 'quiz',       urgency: 'low'    },
]

const urgencyStyle: Record<string, string> = {
  high:   'border-red-200   bg-red-50   text-red-800',
  medium: 'border-amber-200 bg-amber-50 text-amber-800',
  low:    'border-violet-200 bg-violet-50 text-violet-800',
}
const dueStyle: Record<string, string> = {
  high:   'bg-red-100   text-red-700',
  medium: 'bg-amber-100 text-amber-700',
  low:    'bg-violet-100 text-violet-600',
}

const statusTone: Record<string, 'success' | 'warning' | 'danger' | 'default' | 'info'> = {
  Open: 'info',
  Submitted: 'success',
  'Needs review': 'warning',
  Ready: 'success',
  Scheduled: 'info',
  Partial: 'warning',
  Paid: 'success',
  Pending: 'warning',
  Late: 'danger',
}

/* ─── circular gauge ─────────────────────────────────────────────────── */
function CircleGauge({ value, label, color, max = 4.0 }: {
  value: number; label: string; color: string; max?: number
}) {
  const radius = 38
  const circ = 2 * Math.PI * radius
  const pct = Math.min(value / max, 1)
  const dash = circ * pct

  return (
    <div className="flex flex-col items-center gap-2">
      <svg width="100" height="100" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r={radius} fill="none" stroke="#e2e8f0" strokeWidth="8" />
        <circle
          cx="50" cy="50" r={radius} fill="none"
          stroke={color} strokeWidth="8"
          strokeLinecap="round"
          strokeDasharray={`${dash} ${circ}`}
          strokeDashoffset={circ * 0.25}
          style={{ transition: 'stroke-dasharray 0.6s ease' }}
        />
        <text x="50" y="50" textAnchor="middle" dominantBaseline="central"
          fontSize="18" fontWeight="700" fill="#0f172a">{value}</text>
      </svg>
      <span className="text-xs font-medium text-slate-500">{label}</span>
    </div>
  )
}

export default function StudentDashboard() {
  return (
    <div className="space-y-6">

      {/* ── HERO: Personal Welcome Card ──────────────────────────────────── */}
      <div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-violet-700 via-violet-600 to-indigo-700 p-6 shadow-soft">
        {/* decorative circles */}
        <div className="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/5" />
        <div className="pointer-events-none absolute -bottom-12 -left-12 h-48 w-48 rounded-full bg-white/5" />

        <div className="relative flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
          <div className="flex items-center gap-4">
            <div className="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-2 ring-white/20 text-2xl font-bold text-white">
              S
            </div>
            <div>
              <div className="text-[11px] font-semibold uppercase tracking-[0.22em] text-violet-200">
                BSc Computer Science · Year 3
              </div>
              <h1 className="mt-0.5 text-2xl font-bold text-white">Welcome back, Student!</h1>
              <p className="mt-1 text-sm text-violet-200">
                Stay on top of your studies. You have assignments due soon.
              </p>
            </div>
          </div>

          {/* GPA + Attendance gauges */}
          <div className="flex items-center gap-6 xl:gap-8">
            <CircleGauge value={3.74} label="Current GPA" color="#a78bfa" max={4.0} />
            <CircleGauge value={87} label="Attendance %" color="#34d399" max={100} />
          </div>
        </div>

        {/* quick stats strip */}
        <div className="relative mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
          {studentStats.slice(0, 4).map((stat) => (
            <div key={stat.title} className="rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
              <div className="text-xs text-violet-200">{stat.title}</div>
              <div className="mt-1 text-xl font-bold text-white">{stat.value}</div>
              <div className="mt-0.5 text-xs text-violet-300">{stat.trend}</div>
            </div>
          ))}
        </div>
      </div>

      {/* ── QUICK ACTIONS ────────────────────────────────────────────────── */}
      <div className="flex flex-wrap gap-2.5">
        {[
          { label: 'View timetable',      icon: CalendarDays, href: '#timetable'   },
          { label: 'Submit assignment',   icon: Upload,       href: '#assignments'  },
          { label: 'View results',        icon: GraduationCap,href: '#results'      },
          { label: 'Open materials',      icon: BookOpen,     href: '#materials'    },
          { label: 'Check attendance',    icon: CheckCircle2, href: '#attendance'   },
          { label: 'Pay fees',            icon: CreditCard,   href: '#fees'         },
        ].map((a) => {
          const Icon = a.icon
          return (
            <a key={a.label} href={a.href}
              className="flex items-center gap-2 rounded-xl border border-violet-200 bg-violet-50 px-4 py-2.5 text-sm font-medium text-violet-700 transition-colors hover:bg-violet-100 hover:border-violet-300">
              <Icon className="h-4 w-4" />
              {a.label}
            </a>
          )
        })}
      </div>

      {/* ── DEADLINE COUNTDOWN STRIP ─────────────────────────────────────── */}
      <section>
        <div className="mb-3 flex items-center gap-2">
          <Clock className="h-4 w-4 text-violet-600" />
          <h2 className="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Upcoming deadlines</h2>
        </div>
        <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
          {deadlines.map((d) => (
            <div key={d.title} className={`rounded-2xl border p-4 ${urgencyStyle[d.urgency]}`}>
              <div className="flex items-start justify-between gap-2">
                <span className="text-xs font-semibold uppercase tracking-wide opacity-60">{d.type}</span>
                <span className={`rounded-full px-2 py-0.5 text-[10px] font-bold uppercase ${dueStyle[d.urgency]}`}>
                  {d.due}
                </span>
              </div>
              <div className="mt-2 font-semibold leading-snug">{d.title}</div>
              <div className="mt-1 text-xs opacity-70">{d.course}</div>
            </div>
          ))}
        </div>
      </section>

      {/* ── TODAY'S TIMETABLE + ATTENDANCE ─────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1fr_0.9fr]" id="timetable">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-4">
            <div>
              <CardTitle>Today&apos;s timetable</CardTitle>
              <CardDescription>Classes scheduled for today</CardDescription>
            </div>
            <Button variant="outline" size="sm" className="border-violet-200 text-violet-700 hover:bg-violet-50">
              <CalendarDays className="mr-2 h-4 w-4" />
              Full calendar
            </Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-2.5">
              {timetableRows.map((row) => (
                <div key={`${row.day}-${row.course}`}
                  className="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-2.5">
                  <div className="w-12 shrink-0 text-center rounded-lg bg-violet-50 px-1 py-1.5">
                    <div className="text-[10px] font-semibold uppercase text-violet-500">{row.day}</div>
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="font-medium text-slate-800 truncate">{row.course}</div>
                    <div className="text-xs text-slate-500">{row.time} · {row.venue}</div>
                    <div className="text-xs text-violet-600">{row.teacher}</div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm" id="attendance">
          <CardHeader>
            <CardTitle>Attendance by course</CardTitle>
            <CardDescription>Your attendance rate this semester</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="h-[240px] w-full">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={gpaTrend}>
                  <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                  <XAxis dataKey="term" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <YAxis domain={[3, 4]} tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <Tooltip contentStyle={{ borderRadius: 12, borderColor: '#e2e8f0' }} />
                  <Bar dataKey="gpa" radius={[8, 8, 0, 0]} fill="#7c3aed" name="GPA" />
                </BarChart>
              </ResponsiveContainer>
            </div>
            <div className="mt-3 text-center text-xs text-slate-500">GPA trend by term</div>
          </CardContent>
        </Card>
      </section>

      {/* ── MY COURSES ───────────────────────────────────────────────────── */}
      <section id="courses">
        <div className="mb-3 flex items-center justify-between">
          <h2 className="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">My courses</h2>
          <Button variant="ghost" size="sm" className="text-violet-700 hover:bg-violet-50">
            <BookOpen className="mr-2 h-4 w-4" />
            Course hub
          </Button>
        </div>
        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
          {courseRows.map((course) => (
            <Card key={course.code} className="border-violet-100 bg-white shadow-sm hover:shadow-md transition-shadow">
              <CardContent className="p-4">
                <div className="flex items-start justify-between gap-2">
                  <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-sm font-bold text-violet-700">
                    {course.code.slice(0, 2)}
                  </div>
                  <Badge variant="info">{course.next}</Badge>
                </div>
                <div className="mt-3 font-semibold text-slate-900 leading-snug">{course.title}</div>
                <div className="mt-0.5 text-xs text-slate-500">{course.code} · {course.instructor}</div>
                <div className="mt-3">
                  <div className="mb-1 flex justify-between text-xs font-medium text-slate-500">
                    <span>Progress</span>
                    <span>{course.progress}%</span>
                  </div>
                  <div className="h-2 overflow-hidden rounded-full bg-slate-100">
                    <div className="h-full rounded-full bg-gradient-to-r from-violet-500 to-violet-700 transition-all" style={{ width: `${course.progress}%` }} />
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </section>

      {/* ── ASSIGNMENTS + RESULTS/GPA ─────────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]" id="assignments">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle>Assignments</CardTitle>
              <CardDescription>Pending coursework and recent submissions</CardDescription>
            </div>
            <Button variant="outline" size="sm" className="border-violet-200 text-violet-700 hover:bg-violet-50">
              <Upload className="mr-2 h-4 w-4" />
              Submit
            </Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {assignmentRows.map((assignment) => (
                <div key={assignment.title} className="rounded-2xl border border-slate-200 p-4">
                  <div className="flex items-center justify-between gap-3">
                    <div>
                      <div className="font-semibold text-slate-900">{assignment.title}</div>
                      <div className="mt-0.5 text-xs text-slate-500">{assignment.course} · Due {assignment.due}</div>
                    </div>
                    <Badge variant={statusTone[assignment.status] ?? 'default'}>{assignment.status}</Badge>
                  </div>
                  <div className="mt-2 flex items-center justify-between text-sm text-slate-600">
                    <span>Score: <span className="font-medium text-slate-800">{assignment.score}</span></span>
                    {assignment.status === 'Submitted' && <CheckCircle2 className="h-4 w-4 text-emerald-500" />}
                    {assignment.status === 'Open' && (
                      <button className="rounded-lg bg-violet-600 px-3 py-1 text-xs font-semibold text-white hover:bg-violet-700 transition-colors">
                        Submit now
                      </button>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm" id="results">
          <CardHeader>
            <CardTitle>Latest results</CardTitle>
            <CardDescription>Recent marks and course grades</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {resultsRows.map((result) => (
                <div key={result.course} className="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                  <div>
                    <div className="font-medium text-slate-800">{result.course}</div>
                    <div className="text-xs text-slate-500">{result.term}</div>
                  </div>
                  <div className="text-right">
                    <div className="font-semibold text-slate-900">{result.score}</div>
                    <div className={`text-xs font-semibold ${result.grade.startsWith('A') ? 'text-emerald-600' : result.grade.startsWith('B') ? 'text-teal-600' : 'text-amber-600'}`}>
                      {result.grade}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── EXAMS + FEE STATUS ───────────────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Upcoming exams</CardTitle>
            <CardDescription>Academic evaluation schedule</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {examRows.map((exam) => (
                <div key={exam.title}
                  className="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                  <div>
                    <div className="font-medium text-slate-800">{exam.title}</div>
                    <div className="text-xs text-slate-500">{exam.course} · {exam.date}</div>
                  </div>
                  <Badge variant={statusTone[exam.status] ?? 'default'}>{exam.status}</Badge>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm" id="fees">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle>Fee status</CardTitle>
              <CardDescription>Tuition and payment balances</CardDescription>
            </div>
            <Button size="sm" className="bg-violet-600 text-white hover:bg-violet-700">
              <CreditCard className="mr-2 h-4 w-4" />
              Pay now
            </Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {feeRows.map((row) => (
                <div key={row.item} className="rounded-2xl border border-slate-200 p-3.5">
                  <div className="flex items-center justify-between gap-3">
                    <span className="font-medium text-slate-800">{row.item}</span>
                    <Badge variant={statusTone[row.status] ?? 'default'}>{row.status}</Badge>
                  </div>
                  <div className="mt-1.5 text-sm text-slate-600">Due {row.due} · Paid {row.paid}</div>
                  <div className="mt-0.5 text-xs font-medium text-slate-500">Balance: {row.balance}</div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── LEARNING MATERIALS + NOTIFICATIONS ──────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Learning materials</CardTitle>
            <CardDescription>Recent resources and uploaded notes</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-2.5">
              {materialRows.map((material) => (
                <div key={material.title} className="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-2.5">
                  <BookOpen className="h-4 w-4 shrink-0 text-violet-500" />
                  <div className="flex-1 min-w-0">
                    <div className="font-medium text-slate-800 truncate">{material.title}</div>
                    <div className="text-xs text-slate-500">{material.type} · {material.updated}</div>
                  </div>
                  <button className="text-xs font-medium text-violet-700 hover:underline shrink-0">Open</button>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Notifications &amp; messages</CardTitle>
            <CardDescription>Latest updates for your account</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {notificationRows.slice(0, 3).map((n) => (
                <div key={n.title} className="flex items-center gap-3 rounded-xl border border-violet-100 bg-violet-50 px-3 py-2.5 text-sm">
                  <Bell className="h-4 w-4 shrink-0 text-violet-500" />
                  <div className="flex-1">
                    <div className="text-slate-700">{n.title}</div>
                    <div className="text-xs text-slate-400">{n.time}</div>
                  </div>
                </div>
              ))}
              <div className="mt-1 border-t border-slate-200 pt-3 space-y-2.5">
                {messageRows.slice(0, 2).map((msg) => (
                  <div key={`${msg.from}-${msg.subject}`} className="flex items-start gap-2.5 rounded-xl border border-slate-200 px-3 py-2.5">
                    <Mail className="h-4 w-4 shrink-0 text-slate-400 mt-0.5" />
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center justify-between">
                        <span className="text-sm font-medium text-slate-800">{msg.from}</span>
                        <span className="text-xs text-slate-400">{msg.time}</span>
                      </div>
                      <div className="text-xs text-slate-500 truncate">{msg.subject}</div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

    </div>
  )
}
