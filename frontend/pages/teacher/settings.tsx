import { Settings } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function TeacherSettingsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Workspace Settings</h1>
            <p className="text-sm text-slate-500">Class notification preferences, office hours availability, and grading defaults.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Preferences</h2>
            <p className="text-sm text-slate-500 mt-1">Email notifications on assignment submissions: Enabled · Office Hours: Tue/Thu 14:00-16:00</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
