import { CalendarDays } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function StudentCalendarPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Academic Calendar</h1>
            <p className="text-sm text-slate-500">Important academic term dates, holidays, and registration windows.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Semester Schedule</h2>
            <p className="text-sm text-slate-500 mt-1">Course Add/Drop Deadline: Aug 30 · Midterm Exam Week: Sep 15-20 · Fall Break: Oct 12-16</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
