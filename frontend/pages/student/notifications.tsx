import { Bell } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { notificationRows } from '../../src/data/student'

export default function StudentNotificationsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Notifications</h1>
            <p className="text-sm text-slate-500">Student announcements, assignment reminders, and exam alerts.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Recent Alerts</CardTitle>
              <CardDescription>Notifications history</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {notificationRows.map((n, idx) => (
                  <div key={idx} className="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                    <Bell className="h-5 w-5 text-violet-600 shrink-0 mt-0.5" />
                    <div className="flex-1">
                      <div className="font-bold text-slate-900">{n.title}</div>
                      <div className="text-xs text-slate-400 mt-1">{n.time}</div>
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
