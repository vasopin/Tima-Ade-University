import { useMemo, useState } from 'react'
import {
  Area,
  AreaChart,
  CartesianGrid,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from 'recharts'
import {
  AlertTriangle,
  Bell,
  BookOpen,
  CalendarDays,
  CheckCircle2,
  ChevronRight,
  CreditCard,
  Eye,
  GraduationCap,
  Mail,
  MessageCircle,
  PhoneCall,
  School,
  Star,
  TrendingUp,
  Users,
} from 'lucide-react'
import { parentChildren } from '../../data/parent'
import { Badge } from '../ui/badge'
import { Button } from '../ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card'

const statusTone: Record<string, 'success' | 'warning' | 'danger' | 'default' | 'info'> = {
  'On track': 'success',
  'Needs support': 'warning',
  Excellent: 'success',
  Submitted: 'success',
  Pending: 'warning',
  Late: 'danger',
  Scheduled: 'info',
  Ready: 'success',
  Completed: 'success',
  Paid: 'success',
  Partial: 'warning',
}

/* ─── attendance threshold alert ─────────────────────────────────────── */
function AttendanceAlert({ rate }: { rate: string }) {
  const pct = parseFloat(rate)
  if (pct >= 80) return null
  return (
    <div className="flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
      <AlertTriangle className="h-4 w-4 shrink-0" />
      Attendance is below 80% threshold — contact the school office.
    </div>
  )
}

/* ─── monitoring stat card ───────────────────────────────────────────── */
function MonitorStat({ label, value, sub, icon: Icon, accent }: {
  label: string; value: string; sub: string; icon: React.ComponentType<{ className?: string }>; accent: string
}) {
  return (
    <div className={`rounded-2xl border p-4 ${accent}`}>
      <div className="flex items-center justify-between gap-2">
        <span className="text-xs font-semibold uppercase tracking-wide opacity-70">{label}</span>
        <Icon className="h-4 w-4 opacity-60" />
      </div>
      <div className="mt-2 text-2xl font-bold">{value}</div>
      <div className="mt-0.5 text-xs opacity-70">{sub}</div>
    </div>
  )
}

export default function ParentDashboard() {
  const [selectedChildId, setSelectedChildId] = useState(parentChildren[0]?.id ?? '')

  const selectedChild = useMemo(
    () => parentChildren.find((child) => child.id === selectedChildId) ?? parentChildren[0],
    [selectedChildId]
  )

  const [assignmentFilter, setAssignmentFilter] = useState<'All' | 'Pending' | 'Submitted' | 'Late'>('All')

  const filteredAssignments = selectedChild?.assignments.filter((a) =>
    assignmentFilter === 'All' ? true : a.status === assignmentFilter
  ) ?? []

  if (!selectedChild) {
    return (
      <div className="rounded-3xl border border-dashed border-slate-200 bg-white p-8 shadow-sm">
        <h1 className="text-xl font-bold text-slate-900">No child profile found</h1>
        <p className="mt-2 text-sm text-slate-600">This parent account does not have a linked child record.</p>
      </div>
    )
  }

  return (
    <div className="space-y-6">

      {/* ── HEADER: Parent Monitoring Banner ─────────────────────────────── */}
      <div className="rounded-3xl bg-gradient-to-r from-amber-600 via-amber-500 to-orange-500 p-6 shadow-soft">
        <div className="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
          <div>
            <div className="mb-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-amber-100">
              Parent / Guardian Portal
            </div>
            <h1 className="text-2xl font-bold tracking-tight text-white">Family Monitoring Center</h1>
            <p className="mt-1 text-sm text-amber-100">
              Monitor your child&apos;s academic progress, attendance, fees, and school communications.
            </p>
          </div>
          <div className="flex flex-wrap gap-2.5">
            <Button size="sm" className="bg-white/15 border border-white/25 text-white hover:bg-white/25">
              <Mail className="mr-2 h-3.5 w-3.5" />
              Message school
            </Button>
            <Button size="sm" className="bg-white text-amber-700 hover:bg-amber-50">
              <PhoneCall className="mr-2 h-3.5 w-3.5" />
              Contact teacher
            </Button>
          </div>
        </div>
      </div>

      {/* ── CHILD SELECTOR (large, prominent) ────────────────────────────── */}
      <section className="rounded-3xl border border-amber-200 bg-amber-50 p-5">
        <div className="mb-4 flex items-center gap-2">
          <Users className="h-4 w-4 text-amber-600" />
          <h2 className="text-sm font-semibold uppercase tracking-[0.18em] text-amber-700">
            Select child to monitor
          </h2>
        </div>
        <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
          {parentChildren.map((child) => {
            const isSelected = child.id === selectedChild.id
            const attendancePct = parseFloat(child.attendance)
            return (
              <button
                key={child.id}
                type="button"
                onClick={() => setSelectedChildId(child.id)}
                className={[
                  'group relative rounded-2xl border-2 p-4 text-left transition-all',
                  isSelected
                    ? 'border-amber-500 bg-white shadow-md ring-2 ring-amber-200'
                    : 'border-transparent bg-white hover:border-amber-300 hover:shadow-sm',
                ].join(' ')}
              >
                {isSelected && (
                  <div className="absolute right-3 top-3 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500">
                    <CheckCircle2 className="h-3.5 w-3.5 text-white" />
                  </div>
                )}
                <div className="flex items-center gap-3">
                  <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-base font-bold text-amber-700">
                    {child.name.split(' ').map((p: string) => p[0]).slice(0, 2).join('')}
                  </div>
                  <div>
                    <div className="font-semibold text-slate-900">{child.name}</div>
                    <div className="text-xs text-slate-500">{child.grade}</div>
                  </div>
                </div>
                <div className="mt-3 grid grid-cols-2 gap-2 text-xs">
                  <div className="rounded-lg bg-slate-50 px-2 py-1.5">
                    <div className="text-slate-400">GPA</div>
                    <div className="font-bold text-slate-800">{child.gpa}</div>
                  </div>
                  <div className={`rounded-lg px-2 py-1.5 ${attendancePct < 80 ? 'bg-red-50' : 'bg-slate-50'}`}>
                    <div className="text-slate-400">Attend.</div>
                    <div className={`font-bold ${attendancePct < 80 ? 'text-red-600' : 'text-slate-800'}`}>{child.attendance}</div>
                  </div>
                </div>
                <Badge variant={statusTone[child.status] ?? 'default'} className="mt-2">{child.status}</Badge>
              </button>
            )
          })}
        </div>
      </section>

      {/* ── ATTENDANCE ALERT ─────────────────────────────────────────────── */}
      <AttendanceAlert rate={selectedChild.attendance} />

      {/* ── MONITORING STRIP (3 KPIs) ─────────────────────────────────── */}
      <section className="grid gap-4 sm:grid-cols-3" id="overview">
        <MonitorStat
          label="Current GPA"
          value={selectedChild.gpa}
          sub={`${selectedChild.currentTerm} · ${selectedChild.status}`}
          icon={GraduationCap}
          accent="border-amber-200 bg-amber-50 text-amber-900"
        />
        <MonitorStat
          label="Attendance"
          value={selectedChild.attendance}
          sub="This semester"
          icon={CalendarDays}
          accent={parseFloat(selectedChild.attendance) < 80
            ? 'border-red-200 bg-red-50 text-red-900'
            : 'border-emerald-200 bg-emerald-50 text-emerald-900'}
        />
        <MonitorStat
          label="Outstanding fees"
          value={selectedChild.fees.filter((f) => f.status !== 'Paid').length > 0 ? 'Unpaid items' : 'All clear'}
          sub={`${selectedChild.fees.filter((f) => f.status !== 'Paid').length} pending`}
          icon={CreditCard}
          accent={selectedChild.fees.some((f) => f.status !== 'Paid')
            ? 'border-orange-200 bg-orange-50 text-orange-900'
            : 'border-emerald-200 bg-emerald-50 text-emerald-900'}
        />
      </section>

      {/* ── QUICK PARENT ACTIONS ─────────────────────────────────────────── */}
      <div className="flex flex-wrap gap-2.5">
        {[
          { label: "View results",          icon: Eye,          href: '#results'     },
          { label: "View attendance",       icon: CalendarDays, href: '#attendance'  },
          { label: "Check fees",            icon: CreditCard,   href: '#fees'        },
          { label: "Contact teacher",       icon: PhoneCall,    href: '#messages'    },
          { label: "School announcements",  icon: Bell,         href: '#messages'    },
        ].map((a) => {
          const Icon = a.icon
          return (
            <a key={a.label} href={a.href}
              className="flex items-center gap-2 rounded-xl border border-amber-200 bg-white px-4 py-2.5 text-sm font-medium text-amber-700 shadow-sm transition-all hover:bg-amber-50 hover:border-amber-300">
              <Icon className="h-4 w-4" />
              {a.label}
            </a>
          )
        })}
      </div>

      {/* ── ACADEMIC PERFORMANCE + CHILD PROFILE ──────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1.4fr_0.6fr]">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Academic performance</CardTitle>
            <CardDescription>GPA progression — {selectedChild.name}</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="h-[240px] w-full">
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={selectedChild.performanceTrend}>
                  <defs>
                    <linearGradient id="parentGrad" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="5%" stopColor="#d97706" stopOpacity={0.3} />
                      <stop offset="95%" stopColor="#d97706" stopOpacity={0.02} />
                    </linearGradient>
                  </defs>
                  <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                  <XAxis dataKey="month" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <YAxis domain={[3, 4.2]} tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <Tooltip contentStyle={{ borderRadius: 12, borderColor: '#e2e8f0' }} />
                  <Area type="monotone" dataKey="score" stroke="#d97706" strokeWidth={3} fill="url(#parentGrad)" name="GPA" />
                </AreaChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>

        <Card className="border-amber-200 bg-amber-50 shadow-sm">
          <CardHeader>
            <CardTitle className="text-amber-900">Child profile</CardTitle>
            <CardDescription className="text-amber-700">Key details — {selectedChild.name}</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {[
                { label: 'Class',        value: selectedChild.grade        },
                { label: 'Advisor',      value: selectedChild.advisor      },
                { label: 'Next meeting', value: selectedChild.profile?.nextMeeting ?? 'TBD' },
                { label: 'GPA',          value: selectedChild.gpa          },
                { label: 'Attendance',   value: selectedChild.attendance   },
              ].map((row) => (
                <div key={row.label}
                  className="flex items-center justify-between rounded-xl border border-amber-200 bg-white px-3 py-2.5 text-sm">
                  <span className="text-slate-500">{row.label}</span>
                  <span className="font-semibold text-slate-800">{row.value}</span>
                </div>
              ))}
            </div>
            <Button size="sm" className="mt-4 w-full bg-amber-600 text-white hover:bg-amber-700">
              <PhoneCall className="mr-2 h-4 w-4" />
              Contact advisor
            </Button>
          </CardContent>
        </Card>
      </section>

      {/* ── GRADES + ATTENDANCE HISTORY ────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1fr_1fr]" id="results">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle>Grades &amp; results</CardTitle>
              <CardDescription>Most recent term — read-only view</CardDescription>
            </div>
            <Badge variant="info">{selectedChild.currentTerm}</Badge>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-2.5">
              {selectedChild.results.map((result) => (
                <div key={result.course}
                  className="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
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

        <Card className="border-slate-200 bg-white shadow-sm" id="attendance">
          <CardHeader>
            <CardTitle>Attendance history</CardTitle>
            <CardDescription>Weekly attendance — {selectedChild.name}</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="h-[220px] w-full">
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={selectedChild.attendanceTrend}>
                  <defs>
                    <linearGradient id="parentAttGrad" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="5%" stopColor="#0f172a" stopOpacity={0.2} />
                      <stop offset="95%" stopColor="#0f172a" stopOpacity={0.02} />
                    </linearGradient>
                  </defs>
                  <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                  <XAxis dataKey="week" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <YAxis domain={[75, 100]} tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <Tooltip contentStyle={{ borderRadius: 12, borderColor: '#e2e8f0' }} formatter={(v: number | string) => [`${v}%`, 'Attendance']} />
                  <Area type="monotone" dataKey="rate" stroke="#0f172a" strokeWidth={2.5} fill="url(#parentAttGrad)" />
                </AreaChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── ASSIGNMENTS VIEW (read-only parent perspective) ─────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]" id="assignments">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-3">
            <div>
              <CardTitle>Assignment status</CardTitle>
              <CardDescription>Homework and submissions — monitoring view</CardDescription>
            </div>
            <div className="flex flex-wrap gap-1.5">
              {(['All', 'Pending', 'Submitted', 'Late'] as const).map((opt) => (
                <button
                  key={opt}
                  type="button"
                  onClick={() => setAssignmentFilter(opt)}
                  className={[
                    'rounded-full border px-2.5 py-1 text-xs font-medium transition-colors',
                    assignmentFilter === opt
                      ? 'border-amber-500 bg-amber-50 text-amber-700'
                      : 'border-slate-200 bg-white text-slate-600',
                  ].join(' ')}
                >
                  {opt}
                </button>
              ))}
            </div>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {filteredAssignments.length ? filteredAssignments.map((assignment) => (
                <div key={`${assignment.title}-${assignment.course}`}
                  className="rounded-2xl border border-slate-200 p-4">
                  <div className="flex items-center justify-between gap-3">
                    <div>
                      <div className="font-semibold text-slate-900">{assignment.title}</div>
                      <div className="mt-0.5 text-xs text-slate-500">{assignment.course} · Due {assignment.due}</div>
                    </div>
                    <Badge variant={statusTone[assignment.status] ?? 'default'}>{assignment.status}</Badge>
                  </div>
                  <div className="mt-2 text-sm text-slate-600">
                    Score: <span className="font-medium text-slate-800">{assignment.score}</span>
                  </div>
                </div>
              )) : (
                <div className="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center">
                  <div className="text-sm font-medium text-slate-500">No assignments match this filter</div>
                </div>
              )}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Upcoming exams</CardTitle>
            <CardDescription>Assessment calendar — read-only</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-2.5">
              {selectedChild.exams.map((exam) => (
                <div key={`${exam.title}-${exam.course}`}
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
      </section>

      {/* ── FEE PAYMENT + MESSAGES ────────────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]" id="fees">
        <Card className="border-orange-200 bg-orange-50 shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle className="text-orange-900">Fees &amp; payments</CardTitle>
              <CardDescription className="text-orange-700">Tuition and school costs</CardDescription>
            </div>
            <Button size="sm" className="bg-amber-600 text-white hover:bg-amber-700">
              <CreditCard className="mr-2 h-4 w-4" />
              Pay now
            </Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {selectedChild.fees.map((fee) => (
                <div key={fee.item} className="rounded-2xl border border-orange-200 bg-white p-3.5">
                  <div className="flex items-center justify-between gap-3">
                    <span className="font-medium text-slate-800">{fee.item}</span>
                    <Badge variant={statusTone[fee.status] ?? 'default'}>{fee.status}</Badge>
                  </div>
                  <div className="mt-1.5 text-sm text-slate-600">Due {fee.due} · Paid {fee.paid}</div>
                  <div className="mt-0.5 text-xs font-semibold text-orange-700">Balance: {fee.balance}</div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm" id="messages">
          <CardHeader>
            <CardTitle>Messages &amp; announcements</CardTitle>
            <CardDescription>From teachers and school administration</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {selectedChild.messages.map((msg) => (
                <div key={`${msg.from}-${msg.subject}`}
                  className="flex items-start gap-3 rounded-xl border border-slate-200 px-3 py-3">
                  <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-[10px] font-bold text-amber-700">
                    {msg.from.split(' ').map((p: string) => p[0]).slice(0,2).join('')}
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between">
                      <span className="font-medium text-slate-800">{msg.from}</span>
                      <span className="text-xs text-slate-400">{msg.time}</span>
                    </div>
                    <div className="mt-0.5 text-sm text-slate-600">{msg.subject}</div>
                  </div>
                </div>
              ))}
              <button className="flex w-full items-center justify-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 py-2.5 text-sm font-medium text-amber-700 hover:bg-amber-100 transition-colors">
                <MessageCircle className="h-4 w-4" />
                Message teacher
              </button>
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── SCHOOL EVENTS + ANNOUNCEMENTS ────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>School events</CardTitle>
            <CardDescription>Upcoming campus and family activities</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {selectedChild.events.map((event) => (
                <div key={`${event.title}-${event.date}`}
                  className="flex items-start gap-3 rounded-xl border border-slate-200 px-4 py-3">
                  <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100">
                    <CalendarDays className="h-4 w-4 text-amber-600" />
                  </div>
                  <div>
                    <div className="font-medium text-slate-800">{event.title}</div>
                    <div className="text-xs text-slate-500">{event.date} · {event.audience}</div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Announcements</CardTitle>
            <CardDescription>School-wide and class-specific updates</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {selectedChild.announcements.map((item) => (
                <div key={item.title}
                  className="rounded-2xl border border-slate-200 p-4">
                  <div className="font-medium text-slate-800">{item.title}</div>
                  <div className="mt-1 text-sm text-slate-500">{item.audience}</div>
                  <div className="mt-1.5 text-xs text-amber-700 font-medium">{item.date}</div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </section>

    </div>
  )
}
