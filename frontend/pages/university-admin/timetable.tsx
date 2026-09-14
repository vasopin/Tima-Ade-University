import { CalendarDays } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { timetableRows } from '../../src/data/university-admin'

export default function UniversityAdminTimetablePage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Campus Timetable</h1>
            <p className="text-sm text-slate-500">Master schedule of lecture halls, labs, and class sessions.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Master Schedule</CardTitle>
              <CardDescription>Timetable allocation by day and venue</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {timetableRows.map((t, idx) => (
                  <div key={idx} className="flex items-center justify-between rounded-xl border border-slate-200 p-3.5">
                    <div className="flex items-center gap-3">
                      <div className="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{t.day}</div>
                      <div>
                        <div className="font-semibold text-slate-900">{t.course}</div>
                        <div className="text-xs text-slate-500">{t.start} - {t.end} · {t.venue}</div>
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
