import { CalendarCheck } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { attendanceTrend } from '../../src/data/student'

export default function StudentAttendancePage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Attendance Breakdown</h1>
            <p className="text-sm text-slate-500">Track attendance percentage and lecture presence by course.</p>
          </div>

          <div className="grid gap-4 sm:grid-cols-3">
            <Card className="border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs text-slate-500 font-semibold uppercase">Overall Attendance</div>
              <div className="text-2xl font-bold text-slate-900 mt-2">87.5%</div>
              <div className="text-xs text-emerald-600 font-semibold mt-1">Good standing</div>
            </Card>
            <Card className="border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs text-slate-500 font-semibold uppercase">Classes Attended</div>
              <div className="text-2xl font-bold text-slate-900 mt-2">42 / 48</div>
              <div className="text-xs text-slate-500 mt-1">Total sessions</div>
            </Card>
            <Card className="border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs text-slate-500 font-semibold uppercase">Absences</div>
              <div className="text-2xl font-bold text-slate-900 mt-2">6</div>
              <div className="text-xs text-amber-600 font-semibold mt-1">Allowed: 10</div>
            </Card>
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
