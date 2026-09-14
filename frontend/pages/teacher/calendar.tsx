import { CalendarDays } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function TeacherCalendarPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Academic Calendar</h1>
            <p className="text-sm text-slate-500">Key semester dates, grade submission deadlines, and holidays.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Semester Schedule</h2>
            <p className="text-sm text-slate-500 mt-1">Midterm Exams: Sep 15-20 · Grade Submission Deadline: Oct 5 · Term End: Dec 15</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
