import { Bell } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function TeacherNotificationsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Notifications</h1>
            <p className="text-sm text-slate-500">System alerts, submission reminders, and department notices.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Active Notifications</h2>
            <p className="text-sm text-slate-500 mt-1">8 new submissions pending grading · Faculty meeting scheduled for Thursday</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
