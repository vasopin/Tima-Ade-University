import { Bell } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function UniversityAdminNotificationsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Notifications</h1>
            <p className="text-sm text-slate-500">Administrative alerts, pending approvals, and system notifications.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">System Notifications</h2>
            <p className="text-sm text-slate-500 mt-1">3 pending student transfers · 14 exam timetables awaiting review</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
