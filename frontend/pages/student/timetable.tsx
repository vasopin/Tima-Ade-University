import { CalendarDays } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { timetableRows } from '../../src/data/student'

export default function StudentTimetablePage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">My Timetable</h1>
            <p className="text-sm text-slate-500">Weekly lecture timetable, class times, and venues.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Class Schedule</CardTitle>
              <CardDescription>Current term weekly schedule</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {timetableRows.map((t, idx) => (
                  <div key={idx} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div className="flex items-center gap-3">
                      <div className="rounded-lg bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-700">{t.day}</div>
                      <div>
                        <div className="font-semibold text-slate-900">{t.course}</div>
                        <div className="text-xs text-slate-500">{t.time} · {t.venue} · {t.teacher}</div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
