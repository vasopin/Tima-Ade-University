import { Bell, Check, Info, AlertTriangle } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'

const notifications = [
  { title: 'New University Registered', text: 'St. Jude Tech joined the platform', time: '10 min ago', type: 'info' },
  { title: 'Database Backup Completed', text: 'Automated nightly snapshot created', time: '2 h ago', type: 'success' },
  { title: 'Storage Capacity Alert', text: 'File storage partition at 84% capacity', time: '5 h ago', type: 'warning' },
]

export default function SuperAdminNotificationsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['super-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Notifications</h1>
            <p className="text-sm text-slate-500">System alerts, infrastructure status, and admin notifications.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Platform Broadcasts</CardTitle>
              <CardDescription>Alert history and notifications</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {notifications.map((n) => (
                  <div key={n.title} className="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                    <Bell className="h-5 w-5 text-crimson-700 shrink-0 mt-0.5" />
                    <div className="flex-1">
                      <div className="font-semibold text-slate-900">{n.title}</div>
                      <div className="text-sm text-slate-600 mt-0.5">{n.text}</div>
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
