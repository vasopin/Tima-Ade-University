import {
  CartesianGrid,
  Line,
  LineChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from 'recharts'
import {
  Activity,
  AlertTriangle,
  Building2,
  CheckCircle2,
  ChevronRight,
  Database,
  Download,
  Globe,
  KeyRound,
  Lock,
  RefreshCw,
  Server,
  Settings2,
  ShieldAlert,
  ShieldCheck,
  Terminal,
  TrendingUp,
  UserCog,
  Users,
  Wifi,
  XCircle,
  Zap,
} from 'lucide-react'
import { Badge } from '../ui/badge'
import { Button } from '../ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card'
import { auditRows, universityRows, userRows } from '../../data/super-admin'

/* ─── mock platform-level data ──────────────────────────────────────── */
const systemHealth = [
  { label: 'API Gateway',   status: 'Operational', icon: Wifi,     uptime: '99.97%' },
  { label: 'Database',      status: 'Operational', icon: Database, uptime: '99.99%' },
  { label: 'Auth Service',  status: 'Operational', icon: KeyRound, uptime: '100%'   },
  { label: 'File Storage',  status: 'Review',      icon: Server,   uptime: '98.42%' },
]

const platformKpis = [
  { label: 'Total Universities', value: '14',    delta: '+2 this quarter', icon: Building2,    color: 'text-crimson-600',  bg: 'bg-crimson-50'   },
  { label: 'Platform Users',     value: '82,410', delta: '+4.8% MoM',     icon: Users,        color: 'text-slate-700',    bg: 'bg-slate-100'    },
  { label: 'Active Sessions',    value: '3,291',  delta: 'right now',     icon: Activity,     color: 'text-emerald-700',  bg: 'bg-emerald-50'   },
  { label: 'Security Events',    value: '7',      delta: 'last 24 h',     icon: ShieldAlert,  color: 'text-amber-700',    bg: 'bg-amber-50'     },
  { label: 'System Uptime',      value: '99.8%',  delta: 'rolling 30-day', icon: Zap,         color: 'text-sky-700',      bg: 'bg-sky-50'       },
  { label: 'Total Students',     value: '61,204', delta: '+12% vs last yr', icon: Users,      color: 'text-violet-700',   bg: 'bg-violet-50'    },
]

const growthTrend = [
  { month: 'Feb', users: 74200, universities: 12 },
  { month: 'Mar', users: 76100, universities: 12 },
  { month: 'Apr', users: 77300, universities: 13 },
  { month: 'May', users: 78900, universities: 13 },
  { month: 'Jun', users: 80100, universities: 14 },
  { month: 'Jul', users: 81500, universities: 14 },
  { month: 'Aug', users: 82410, universities: 14 },
]

const securityEvents = [
  { event: 'Failed login spike — Lagos Campus',  severity: 'High',   time: '14 min ago',   icon: ShieldAlert },
  { event: 'Role escalation attempt blocked',      severity: 'High',   time: '1 h ago',      icon: Lock        },
  { event: 'API rate-limit exceeded — API key #4', severity: 'Medium', time: '3 h ago',      icon: Terminal    },
  { event: 'Password reset flood — Abuja node',   severity: 'Medium', time: '6 h ago',      icon: ShieldAlert },
  { event: 'Successful audit export by AdminX',   severity: 'Info',   time: '9 h ago',      icon: CheckCircle2 },
]

const roleRows = [
  { name: 'Super Admin',       users: 3,      permissions: 24, color: 'bg-crimson-600' },
  { name: 'University Admin',  users: 14,     permissions: 18, color: 'bg-slate-700'   },
  { name: 'Teacher',           users: 1840,   permissions: 9,  color: 'bg-sky-600'     },
  { name: 'Student',           users: 61204,  permissions: 5,  color: 'bg-emerald-600' },
  { name: 'Parent',            users: 12030,  permissions: 4,  color: 'bg-amber-600'   },
]

const severityColor: Record<string, string> = {
  High:   'text-red-600   bg-red-50   border-red-200',
  Medium: 'text-amber-700 bg-amber-50 border-amber-200',
  Info:   'text-slate-500 bg-slate-50 border-slate-200',
}

/* ─── status helpers ─────────────────────────────────────────────────── */
const statusTone: Record<string, 'success' | 'warning' | 'danger' | 'default' | 'info'> = {
  Active: 'success',
  Pending: 'warning',
  Suspended: 'danger',
  Operational: 'success',
  Review: 'info',
  Maintenance: 'warning',
  Success: 'success',
  Warning: 'warning',
}

export default function SuperAdminDashboard() {
  return (
    <div className="space-y-6">

      {/* ── HEADER: Command Center ──────────────────────────────────────── */}
      <div className="relative overflow-hidden rounded-3xl bg-slate-950 p-6 shadow-soft">
        {/* decorative grid lines */}
        <div className="pointer-events-none absolute inset-0 opacity-[0.04]"
          style={{ backgroundImage: 'linear-gradient(#fff 1px,transparent 1px),linear-gradient(90deg,#fff 1px,transparent 1px)', backgroundSize: '32px 32px' }} />

        <div className="relative flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
          <div>
            <div className="mb-3 inline-flex items-center gap-2 rounded-full border border-crimson-700/40 bg-crimson-900/30 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-crimson-300">
              <Globe className="h-3.5 w-3.5" />
              Platform Control Center
            </div>
            <h1 className="text-3xl font-bold tracking-tight text-white">Super Admin</h1>
            <p className="mt-1.5 max-w-xl text-sm text-slate-400">
              Platform-wide analytics, system health, university portfolio, security events, and access control.
            </p>
          </div>

          <div className="flex flex-wrap gap-2.5">
            <Button size="sm" className="border border-white/10 bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white">
              <RefreshCw className="mr-2 h-3.5 w-3.5" />
              Refresh
            </Button>
            <Button size="sm" className="border border-white/10 bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white">
              <Download className="mr-2 h-3.5 w-3.5" />
              Export report
            </Button>
            <Button size="sm" className="bg-crimson-700 text-white hover:bg-crimson-800">
              <Settings2 className="mr-2 h-3.5 w-3.5" />
              System config
            </Button>
          </div>
        </div>

        {/* System Health Strip */}
        <div className="relative mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
          {systemHealth.map((item) => {
            const Icon = item.icon
            const isOk = item.status === 'Operational'
            return (
              <div key={item.label}
                className="flex items-center gap-3 rounded-2xl border border-white/8 bg-white/5 px-4 py-3">
                <div className={`flex h-9 w-9 items-center justify-center rounded-xl ${isOk ? 'bg-emerald-500/15' : 'bg-amber-500/15'}`}>
                  <Icon className={`h-4 w-4 ${isOk ? 'text-emerald-400' : 'text-amber-400'}`} />
                </div>
                <div className="min-w-0">
                  <div className="text-xs font-medium text-slate-400">{item.label}</div>
                  <div className="flex items-center gap-1.5">
                    {isOk
                      ? <CheckCircle2 className="h-3 w-3 text-emerald-400" />
                      : <AlertTriangle className="h-3 w-3 text-amber-400" />}
                    <span className={`text-xs font-semibold ${isOk ? 'text-emerald-300' : 'text-amber-300'}`}>{item.status}</span>
                    <span className="text-xs text-slate-600">·</span>
                    <span className="text-xs text-slate-500">{item.uptime}</span>
                  </div>
                </div>
              </div>
            )
          })}
        </div>
      </div>

      {/* ── PLATFORM KPI STRIP ─────────────────────────────────────────── */}
      <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        {platformKpis.map((kpi) => {
          const Icon = kpi.icon
          return (
            <Card key={kpi.label} className="border-slate-200 bg-white shadow-sm">
              <CardContent className="p-4">
                <div className="flex items-center justify-between gap-2">
                  <span className="text-xs font-medium text-slate-500">{kpi.label}</span>
                  <div className={`flex h-7 w-7 items-center justify-center rounded-lg ${kpi.bg}`}>
                    <Icon className={`h-3.5 w-3.5 ${kpi.color}`} />
                  </div>
                </div>
                <div className="mt-3 text-2xl font-bold tracking-tight text-slate-900">{kpi.value}</div>
                <div className="mt-1 text-xs text-slate-500">{kpi.delta}</div>
              </CardContent>
            </Card>
          )
        })}
      </section>

      {/* ── GROWTH CHART + SECURITY EVENTS ──────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1.6fr_1fr]" id="analytics">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-4">
            <div>
              <CardTitle>Platform growth</CardTitle>
              <CardDescription>Total users and university count over 7 months</CardDescription>
            </div>
            <Badge variant="info">Live</Badge>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="h-[280px] w-full">
              <ResponsiveContainer width="100%" height="100%">
                <LineChart data={growthTrend}>
                  <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                  <XAxis dataKey="month" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <YAxis yAxisId="left" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <YAxis yAxisId="right" orientation="right" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} domain={[10, 16]} />
                  <Tooltip contentStyle={{ borderRadius: 12, borderColor: '#e2e8f0', fontSize: 12 }} />
                  <Line yAxisId="left" type="monotone" dataKey="users" stroke="#9f1239" strokeWidth={2.5} dot={false} name="Users" />
                  <Line yAxisId="right" type="monotone" dataKey="universities" stroke="#0f172a" strokeWidth={2} strokeDasharray="4 2" dot={false} name="Universities" />
                </LineChart>
              </ResponsiveContainer>
            </div>
            <div className="mt-3 flex items-center gap-6 text-xs text-slate-500">
              <span className="flex items-center gap-1.5"><span className="inline-block h-2.5 w-5 rounded-full bg-crimson-700" /> Total users</span>
              <span className="flex items-center gap-1.5"><span className="inline-block h-0 w-5 border-t-2 border-dashed border-slate-800" /> Universities</span>
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm" id="security">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle>Security events</CardTitle>
              <CardDescription>Platform-wide alerts, last 24 hours</CardDescription>
            </div>
            <div className="flex h-7 w-7 items-center justify-center rounded-full bg-red-50">
              <ShieldAlert className="h-4 w-4 text-red-500" />
            </div>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {securityEvents.map((evt) => {
                const Icon = evt.icon
                return (
                  <div key={evt.event} className={`flex items-start gap-3 rounded-xl border p-3 text-sm ${severityColor[evt.severity]}`}>
                    <Icon className="mt-0.5 h-4 w-4 shrink-0" />
                    <div className="min-w-0 flex-1">
                      <div className="font-medium leading-snug">{evt.event}</div>
                      <div className="mt-0.5 text-xs opacity-70">{evt.time}</div>
                    </div>
                    <span className="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide border">{evt.severity}</span>
                  </div>
                )
              })}
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── UNIVERSITY TABLE ─────────────────────────────────────────────── */}
      <Card className="border-slate-200 bg-white shadow-sm" id="universities">
        <CardHeader className="flex flex-row items-center justify-between gap-2">
          <div>
            <CardTitle>University portfolio</CardTitle>
            <CardDescription>All registered institutions and operational status</CardDescription>
          </div>
          <div className="flex items-center gap-2">
            <Button variant="outline" size="sm">
              <Building2 className="mr-2 h-4 w-4" />
              Add university
            </Button>
            <Button variant="ghost" size="sm">View all <ChevronRight className="ml-1 h-4 w-4" /></Button>
          </div>
        </CardHeader>
        <CardContent className="p-6 pt-0">
          <div className="overflow-x-auto">
            <table className="min-w-full text-left text-sm">
              <thead>
                <tr className="border-b border-slate-200">
                  <th className="pb-3 font-medium text-slate-500">Institution</th>
                  <th className="pb-3 font-medium text-slate-500">Location</th>
                  <th className="pb-3 font-medium text-slate-500">Students</th>
                  <th className="pb-3 font-medium text-slate-500">Teachers</th>
                  <th className="pb-3 font-medium text-slate-500">Status</th>
                  <th className="pb-3 font-medium text-slate-500">Action</th>
                </tr>
              </thead>
              <tbody>
                {universityRows.map((campus) => (
                  <tr key={campus.name} className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50/60 transition-colors">
                    <td className="py-3">
                      <div className="flex items-center gap-3">
                        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-xs font-bold text-white shrink-0">
                          {campus.name.split(' ').map(w => w[0]).slice(0,2).join('')}
                        </div>
                        <span className="font-semibold text-slate-900">{campus.name}</span>
                      </div>
                    </td>
                    <td className="py-3 text-slate-600">{campus.location}</td>
                    <td className="py-3 text-slate-600">{campus.students.toLocaleString()}</td>
                    <td className="py-3 text-slate-600">{campus.teachers}</td>
                    <td className="py-3"><Badge variant={statusTone[campus.status] ?? 'default'}>{campus.status}</Badge></td>
                    <td className="py-3">
                      <button className="text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors">Manage →</button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>

      {/* ── USER MANAGEMENT + ROLES ──────────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]" id="users">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle>User management</CardTitle>
              <CardDescription>Latest accounts and access states across the platform</CardDescription>
            </div>
            <Button variant="outline" size="sm">
              <UserCog className="mr-2 h-4 w-4" />
              Invite user
            </Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="overflow-x-auto">
              <table className="min-w-full text-left text-sm">
                <thead>
                  <tr className="border-b border-slate-200 text-slate-500">
                    <th className="pb-3 font-medium">Name</th>
                    <th className="pb-3 font-medium">Role</th>
                    <th className="pb-3 font-medium">Status</th>
                    <th className="pb-3 font-medium">Last login</th>
                  </tr>
                </thead>
                <tbody>
                  {userRows.map((user) => (
                    <tr key={user.email} className="border-b border-slate-100 last:border-b-0">
                      <td className="py-3">
                        <div className="flex items-center gap-2.5">
                          <div className="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-slate-700 to-slate-900 text-[10px] font-bold text-white shrink-0">
                            {user.name.split(' ').map((p: string) => p[0]).slice(0,2).join('')}
                          </div>
                          <div>
                            <div className="font-medium text-slate-800">{user.name}</div>
                            <div className="text-xs text-slate-500">{user.email}</div>
                          </div>
                        </div>
                      </td>
                      <td className="py-3 text-slate-600">{user.role}</td>
                      <td className="py-3"><Badge variant={statusTone[user.status] ?? 'default'}>{user.status}</Badge></td>
                      <td className="py-3 text-slate-600">{user.lastLogin}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm" id="roles">
          <CardHeader>
            <CardTitle>Roles &amp; permissions</CardTitle>
            <CardDescription>Access coverage by role group</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-4">
              {roleRows.map((role) => (
                <div key={role.name}>
                  <div className="mb-1.5 flex items-center justify-between text-sm">
                    <div className="flex items-center gap-2">
                      <span className={`h-2 w-2 rounded-full ${role.color}`} />
                      <span className="font-medium text-slate-800">{role.name}</span>
                    </div>
                    <div className="text-right">
                      <span className="text-xs text-slate-500">{role.users.toLocaleString()} users · {role.permissions} perms</span>
                    </div>
                  </div>
                  <div className="h-2 overflow-hidden rounded-full bg-slate-100">
                    <div className={`h-full rounded-full ${role.color} opacity-80`} style={{ width: `${(role.permissions / 24) * 100}%` }} />
                  </div>
                </div>
              ))}
            </div>
            <button className="mt-5 flex w-full items-center justify-center gap-1.5 rounded-xl border border-slate-200 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
              <ShieldCheck className="h-4 w-4" />
              Manage permissions
            </button>
          </CardContent>
        </Card>
      </section>

      {/* ── AUDIT LOGS ──────────────────────────────────────────────────── */}
      <Card className="border-slate-200 bg-white shadow-sm" id="audit">
        <CardHeader className="flex flex-row items-center justify-between gap-2">
          <div>
            <CardTitle>Audit log</CardTitle>
            <CardDescription>Most recent administrative actions — platform-wide</CardDescription>
          </div>
          <Button variant="ghost" size="sm">
            <TrendingUp className="mr-2 h-4 w-4" />
            Full audit trail
          </Button>
        </CardHeader>
        <CardContent className="p-6 pt-0">
          <div className="overflow-x-auto">
            <table className="min-w-full text-left text-sm">
              <thead>
                <tr className="border-b border-slate-200 text-slate-500">
                  <th className="pb-3 font-medium">Actor</th>
                  <th className="pb-3 font-medium">Action</th>
                  <th className="pb-3 font-medium">Target</th>
                  <th className="pb-3 font-medium">Timestamp</th>
                  <th className="pb-3 font-medium">Status</th>
                </tr>
              </thead>
              <tbody>
                {auditRows.map((audit) => (
                  <tr key={`${audit.actor}-${audit.timestamp}`} className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50/60 transition-colors">
                    <td className="py-3 font-medium text-slate-800">{audit.actor}</td>
                    <td className="py-3 text-slate-600">{audit.action}</td>
                    <td className="py-3 text-slate-500 text-xs">—</td>
                    <td className="py-3 text-slate-600">{audit.timestamp}</td>
                    <td className="py-3"><Badge variant={statusTone[audit.status] ?? 'default'}>{audit.status}</Badge></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>

    </div>
  )
}
