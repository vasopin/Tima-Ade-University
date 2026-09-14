import { CalendarDays } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

const todaySchedule = [
  { time: '08:00', course: 'Data Structures 301', venue: 'LT-4', status: 'done',    students: 42 },
  { time: '10:00', course: 'Algorithms II',        venue: 'LT-2', status: 'active',  students: 38 },
  { time: '12:00', course: 'Database Systems',     venue: 'Lab-3',status: 'upcoming',students: 30 },
  { time: '14:00', course: 'Office Hours',         venue: 'Rm-11',status: 'upcoming',students: null },
]

export default function TeacherTimetablePage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Teaching Timetable</h1>
            <p className="text-sm text-slate-500">Weekly teaching schedule, lecture halls, and lab sessions.</p>
          </div>

          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            {todaySchedule.map((s) => (
              <Card key={s.time} className="border-slate-200 bg-white p-4 shadow-sm">
                <div className="text-xs font-bold text-slate-400">{s.time}</div>
                <div className="mt-2 font-bold text-slate-900">{s.course}</div>
                <div className="mt-1 text-xs text-slate-500">{s.venue} {s.students ? `· ${s.students} students` : ''}</div>
              </Card>
            ))}
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
