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
  AlertCircle,
  BookOpen,
  CheckCircle2,
  ClipboardCheck,
  Clock,
  Edit3,
  FileText,
  GraduationCap,
  MapPin,
  MessageSquare,
  PenSquare,
  Star,
  Upload,
  UserCheck,
  Users,
  Zap,
} from 'lucide-react'
import { Badge } from '../ui/badge'
import { Button } from '../ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card'
import {
  announcementRows,
  assignmentRows,
  classRows,
  courseRows,
  examRows,
  gradeRows,
  messageRows,
  performanceTrend,
  resourceRows,
  studentRows,
  submissionRows,
  teacherStats,
  timetableRows,
} from '../../data/teacher'

/* ─── today's schedule data ─────────────────────────────────────────── */
const todaySchedule = [
  { time: '08:00', course: 'Data Structures 301', venue: 'LT-4', status: 'done',    students: 42 },
  { time: '10:00', course: 'Algorithms II',        venue: 'LT-2', status: 'active',  students: 38 },
  { time: '12:00', course: 'Database Systems',     venue: 'Lab-3',status: 'upcoming',students: 30 },
  { time: '14:00', course: 'Office Hours',         venue: 'Rm-11',status: 'upcoming',students: null },
]

const statusStyle: Record<string, string> = {
  done:     'bg-slate-100 text-slate-400 border-slate-200',
  active:   'bg-teal-500  text-white     border-teal-400 ring-2 ring-teal-200',
  upcoming: 'bg-white     text-slate-700 border-slate-200',
}

const urgentActions = [
  { text: '8 submissions pending grading — Algorithms II',   icon: PenSquare,     cta: 'Grade now', color: 'border-teal-300 bg-teal-50 text-teal-800'   },
  { text: 'Attendance not taken — DB Systems (12:00)',        icon: ClipboardCheck,cta: 'Mark now',  color: 'border-amber-300 bg-amber-50 text-amber-800'  },
  { text: 'Assignment due tomorrow — notify students',        icon: AlertCircle,   cta: 'Remind',    color: 'border-orange-300 bg-orange-50 text-orange-800'},
]

const statusTone: Record<string, 'success' | 'warning' | 'danger' | 'default' | 'info'> = {
  Open: 'success',
  'In review': 'info',
  Submitted: 'success',
  Late: 'warning',
  Pending: 'warning',
  Scheduled: 'info',
  Ready: 'success',
  Draft: 'warning',
  'At risk': 'danger',
  'Needs support': 'warning',
}

const performanceColor = (p: string) => {
  if (!p) return 'text-slate-500'
  const n = parseFloat(p)
  if (n >= 85) return 'text-emerald-600 font-semibold'
  if (n >= 70) return 'text-teal-600 font-semibold'
  return 'text-red-600 font-semibold'
}

export default function TeacherDashboard() {
  const today = new Date().toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long' })

  return (
    <div className="space-y-6">

      {/* ── HEADER: Date + Greeting (no dark banner!) ──────────────────── */}
      <div className="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
          <div className="text-xs font-semibold uppercase tracking-[0.22em] text-teal-600">{today}</div>
          <h1 className="mt-1 text-2xl font-bold tracking-tight text-slate-900">Good morning, Teacher 👋</h1>
          <p className="mt-1 text-sm text-slate-500">
            You have <span className="font-semibold text-teal-700">3 classes</span> and{' '}
            <span className="font-semibold text-amber-600">8 submissions</span> waiting to be graded today.
          </p>
        </div>
        <div className="flex items-center gap-2">
          <div className="flex items-center gap-1.5 rounded-xl border border-teal-200 bg-teal-50 px-3 py-2 text-xs font-semibold text-teal-700">
            <Star className="h-3.5 w-3.5" />
            4.8 student rating
          </div>
          <div className="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600">
            <Users className="h-3.5 w-3.5" />
            154 students
          </div>
        </div>
      </div>

      {/* ── TODAY'S TIMETABLE (horizontal timeline — hero element) ────── */}
      <section id="timetable">
        <div className="mb-3 flex items-center justify-between">
          <h2 className="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Today&apos;s schedule</h2>
          <Button variant="ghost" size="sm" className="text-teal-700 hover:bg-teal-50">View full timetable</Button>
        </div>
        <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
          {todaySchedule.map((slot) => (
            <div key={slot.time}
              className={`relative overflow-hidden rounded-2xl border px-4 py-4 transition-all ${statusStyle[slot.status]}`}>
              {slot.status === 'active' && (
                <div className="absolute right-3 top-3 flex items-center gap-1 rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-bold text-white">
                  <span className="h-1.5 w-1.5 rounded-full bg-white animate-pulse" />
                  NOW
                </div>
              )}
              <div className={`text-xs font-bold ${slot.status === 'active' ? 'text-white/80' : 'text-slate-400'}`}>{slot.time}</div>
              <div className={`mt-2 font-semibold leading-snug ${slot.status === 'active' ? 'text-white' : 'text-slate-800'}`}>{slot.course}</div>
              <div className={`mt-1.5 flex items-center gap-1.5 text-xs ${slot.status === 'active' ? 'text-white/70' : 'text-slate-500'}`}>
                <MapPin className="h-3 w-3" />
                {slot.venue}
                {slot.students && <><span>·</span>{slot.students} students</>}
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* ── URGENT ACTION QUEUE ─────────────────────────────────────────── */}
      <section>
        <div className="mb-3 flex items-center gap-2">
          <Zap className="h-4 w-4 text-amber-500" />
          <h2 className="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Action needed</h2>
        </div>
        <div className="grid gap-3 xl:grid-cols-3">
          {urgentActions.map((action) => {
            const Icon = action.icon
            return (
              <div key={action.text} className={`flex items-center gap-3 rounded-2xl border px-4 py-3.5 ${action.color}`}>
                <Icon className="h-5 w-5 shrink-0 opacity-70" />
                <span className="flex-1 text-sm font-medium leading-snug">{action.text}</span>
                <button className="shrink-0 rounded-lg bg-white/60 px-3 py-1 text-xs font-semibold hover:bg-white/90 transition-colors">
                  {action.cta}
                </button>
              </div>
            )
          })}
        </div>
      </section>

      {/* ── QUICK ACTIONS BAR ───────────────────────────────────────────── */}
      <div className="flex flex-wrap gap-2.5">
        {[
          { label: 'Take attendance',    icon: UserCheck,      id: '#attendance'  },
          { label: 'Create assignment',  icon: Edit3,          id: '#assessments' },
          { label: 'Grade submissions',  icon: ClipboardCheck, id: '#assessments' },
          { label: 'Enter results',      icon: GraduationCap,  id: '#assessments' },
          { label: 'Upload material',    icon: Upload,         id: '#courses'     },
          { label: 'Send message',       icon: MessageSquare,  id: '#messages'    },
        ].map((action) => {
          const Icon = action.icon
          return (
            <a key={action.label} href={action.id}
              className="flex items-center gap-2 rounded-xl border border-teal-200 bg-white px-4 py-2.5 text-sm font-medium text-teal-700 shadow-sm transition-all hover:bg-teal-50 hover:border-teal-300 hover:shadow-none">
              <Icon className="h-4 w-4" />
              {action.label}
            </a>
          )
        })}
      </div>

      {/* ── MY CLASSES + SUBMISSIONS QUEUE ──────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1.4fr_0.6fr]" id="classes">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-4">
            <div>
              <CardTitle>My classes</CardTitle>
              <CardDescription>All active classes, student count, and next session</CardDescription>
            </div>
            <Button variant="outline" size="sm" className="border-teal-200 text-teal-700 hover:bg-teal-50">
              View timetable
            </Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="overflow-x-auto">
              <table className="min-w-full text-left text-sm">
                <thead>
                  <tr className="border-b border-slate-200 text-slate-500">
                    <th className="pb-3 font-medium">Class</th>
                    <th className="pb-3 font-medium">Students</th>
                    <th className="pb-3 font-medium">Attendance</th>
                    <th className="pb-3 font-medium">Next session</th>
                    <th className="pb-3 font-medium">Action</th>
                  </tr>
                </thead>
                <tbody>
                  {classRows.map((row) => (
                    <tr key={row.name} className="border-b border-slate-100 last:border-b-0 hover:bg-teal-50/40 transition-colors">
                      <td className="py-3 font-semibold text-slate-800">{row.name}</td>
                      <td className="py-3 text-slate-600">{row.students}</td>
                      <td className="py-3">
                        <span className={`font-medium ${parseFloat(row.attendance) >= 85 ? 'text-emerald-600' : 'text-amber-600'}`}>
                          {row.attendance}
                        </span>
                      </td>
                      <td className="py-3 text-slate-600">{row.next}</td>
                      <td className="py-3">
                        <button className="rounded-lg border border-teal-200 px-2.5 py-1 text-xs font-medium text-teal-700 hover:bg-teal-50 transition-colors">
                          Attend
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>

        <Card className="border-teal-200 bg-teal-50 shadow-sm">
          <CardHeader>
            <CardTitle className="text-teal-900">Grading queue</CardTitle>
            <CardDescription className="text-teal-700">Submissions awaiting your review</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {submissionRows.map((row) => (
                <div key={`${row.student}-${row.assignment}`}
                  className="rounded-xl border border-teal-200 bg-white p-3 shadow-sm">
                  <div className="flex items-center justify-between gap-2">
                    <div>
                      <div className="font-medium text-slate-800">{row.student}</div>
                      <div className="text-xs text-slate-500">{row.assignment}</div>
                    </div>
                    <Badge variant={statusTone[row.status] ?? 'default'}>{row.status}</Badge>
                  </div>
                  {row.status === 'In review' && (
                    <button className="mt-2 w-full rounded-lg bg-teal-600 py-1.5 text-xs font-semibold text-white hover:bg-teal-700 transition-colors">
                      Grade now →
                    </button>
                  )}
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── COURSES + STUDENT LIST ──────────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1fr_1.1fr]" id="courses">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle>My courses</CardTitle>
              <CardDescription>Syllabus progress and student count</CardDescription>
            </div>
            <Button variant="ghost" size="sm">Manage</Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-4">
              {courseRows.map((course) => (
                <div key={course.code}>
                  <div className="flex items-center justify-between gap-3">
                    <div>
                      <div className="font-semibold text-slate-900">{course.title}</div>
                      <div className="text-xs text-slate-500">{course.code} · {course.term}</div>
                    </div>
                    <span className="rounded-full bg-teal-100 px-2.5 py-0.5 text-xs font-semibold text-teal-700">{course.students} students</span>
                  </div>
                  <div className="mt-2.5 h-2 overflow-hidden rounded-full bg-slate-100">
                    <div className="h-full rounded-full bg-gradient-to-r from-teal-500 to-teal-700 transition-all" style={{ width: `${course.completion}%` }} />
                  </div>
                  <div className="mt-1 text-right text-xs font-medium text-slate-400">{course.completion}% syllabus</div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm" id="students">
          <CardHeader>
            <CardTitle>Student roster</CardTitle>
            <CardDescription>Performance and support indicators per student</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="overflow-x-auto">
              <table className="min-w-full text-left text-sm">
                <thead>
                  <tr className="border-b border-slate-200 text-slate-500">
                    <th className="pb-3 font-medium">Student</th>
                    <th className="pb-3 font-medium">Score</th>
                    <th className="pb-3 font-medium">Attendance</th>
                    <th className="pb-3 font-medium">Flag</th>
                  </tr>
                </thead>
                <tbody>
                  {studentRows.map((student) => (
                    <tr key={student.id} className="border-b border-slate-100 last:border-b-0">
                      <td className="py-3">
                        <div className="font-medium text-slate-800">{student.name}</div>
                        <div className="text-xs text-slate-500">{student.id}</div>
                      </td>
                      <td className={`py-3 ${performanceColor(student.performance)}`}>{student.performance}</td>
                      <td className="py-3 text-slate-600">{student.attendance}</td>
                      <td className="py-3">
                        {student.performance && parseFloat(student.performance) < 65
                          ? <Badge variant="danger">At risk</Badge>
                          : <CheckCircle2 className="h-4 w-4 text-emerald-500" />}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── ATTENDANCE CHART + ASSIGNMENTS ──────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]" id="attendance">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-4">
            <div>
              <CardTitle>Class performance scores</CardTitle>
              <CardDescription>Average exam scores by month</CardDescription>
            </div>
            <Button variant="outline" size="sm" className="border-teal-200 text-teal-700 hover:bg-teal-50">
              <ClipboardCheck className="mr-2 h-4 w-4" />
              Mark attendance
            </Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="h-[240px] w-full">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={performanceTrend}>
                  <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                  <XAxis dataKey="month" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <YAxis tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                  <Tooltip contentStyle={{ borderRadius: 12, borderColor: '#e2e8f0' }} />
                  <Bar dataKey="score" radius={[8, 8, 0, 0]} fill="#0d9488" />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm" id="assessments">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle>Assignments</CardTitle>
              <CardDescription>Open work items and submission tracking</CardDescription>
            </div>
            <Button variant="ghost" size="sm" className="text-teal-700 hover:bg-teal-50">+ New</Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {assignmentRows.map((assignment) => (
                <div key={assignment.title} className="rounded-2xl border border-slate-200 p-3.5">
                  <div className="flex items-center justify-between gap-3">
                    <div>
                      <div className="font-semibold text-slate-900">{assignment.title}</div>
                      <div className="mt-0.5 text-xs text-slate-500">{assignment.course} · Due {assignment.due}</div>
                    </div>
                    <Badge variant={statusTone[assignment.status] ?? 'default'}>{assignment.status}</Badge>
                  </div>
                  <div className="mt-2 flex items-center justify-between text-xs text-slate-500">
                    <span>{assignment.submissions} submissions</span>
                    <button className="text-teal-700 font-medium hover:underline">Review →</button>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── EXAMS + GRADE ENTRY ─────────────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Upcoming exams</CardTitle>
            <CardDescription>Assessment blocks and readiness</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {examRows.map((exam) => (
                <div key={exam.title} className="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
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

        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Grade entry</CardTitle>
            <CardDescription>Current gradebook status and pending entries</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="overflow-x-auto">
              <table className="min-w-full text-left text-sm">
                <thead>
                  <tr className="border-b border-slate-200 text-slate-500">
                    <th className="pb-3 font-medium">Student</th>
                    <th className="pb-3 font-medium">Grade</th>
                    <th className="pb-3 font-medium">Status</th>
                  </tr>
                </thead>
                <tbody>
                  {gradeRows.map((row) => (
                    <tr key={`${row.student}-${row.course}`} className="border-b border-slate-100 last:border-b-0">
                      <td className="py-3">
                        <div className="font-medium text-slate-800">{row.student}</div>
                        <div className="text-xs text-slate-500">{row.course}</div>
                      </td>
                      <td className={`py-3 font-semibold ${performanceColor(row.score.toString())}`}>
                        {row.grade} ({row.score})
                      </td>
                      <td className="py-3"><Badge variant={statusTone[row.status] ?? 'default'}>{row.status}</Badge></td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
      </section>

      {/* ── COURSE MATERIALS + MESSAGES ─────────────────────────────────── */}
      <section className="grid gap-6 xl:grid-cols-[1fr_1fr]" id="messages">
        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between gap-2">
            <div>
              <CardTitle>Course materials</CardTitle>
              <CardDescription>Shared learning resources and uploaded notes</CardDescription>
            </div>
            <Button variant="outline" size="sm" className="border-teal-200 text-teal-700 hover:bg-teal-50">
              <Upload className="mr-2 h-4 w-4" />
              Upload
            </Button>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-2.5">
              {resourceRows.map((resource) => (
                <div key={resource.title} className="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-2.5">
                  <BookOpen className="h-4 w-4 shrink-0 text-teal-600" />
                  <div className="flex-1 min-w-0">
                    <div className="font-medium text-slate-800 truncate">{resource.title}</div>
                    <div className="text-xs text-slate-500">{resource.type} · {resource.date}</div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="border-slate-200 bg-white shadow-sm">
          <CardHeader>
            <CardTitle>Messages</CardTitle>
            <CardDescription>Recent communications from students and administration</CardDescription>
          </CardHeader>
          <CardContent className="p-6 pt-0">
            <div className="space-y-3">
              {messageRows.map((message) => (
                <div key={`${message.from}-${message.subject}`} className="rounded-xl border border-slate-200 p-3">
                  <div className="flex items-center justify-between gap-2">
                    <div className="flex items-center gap-2">
                      <div className="flex h-7 w-7 items-center justify-center rounded-full bg-teal-100 text-[10px] font-bold text-teal-700">
                        {message.from.split(' ').map((p: string) => p[0]).slice(0,2).join('')}
                      </div>
                      <span className="font-medium text-slate-800">{message.from}</span>
                    </div>
                    <span className="text-xs text-slate-400">{message.time}</span>
                  </div>
                  <div className="mt-1.5 text-sm text-slate-600">{message.subject}</div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </section>

    </div>
  )
}
