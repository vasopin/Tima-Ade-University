import { Bell, CheckCircle2, AlertCircle, Info } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

const notifications = [
  { type: 'alert', icon: AlertCircle, color: 'text-red-600 bg-red-50', title: 'Attendance Alert', body: 'Your child missed 2 classes this week. Attendance is now at 78%.', time: '2h ago' },
  { type: 'info', icon: Info, color: 'text-blue-600 bg-blue-50', title: 'Exam Schedule Published', body: 'The second-semester examination timetable has been published.', time: '5h ago' },
  { type: 'success', icon: CheckCircle2, color: 'text-emerald-600 bg-emerald-50', title: 'Fee Payment Confirmed', body: 'Your payment of ₦85,000 for second-term fees has been received.', time: '1d ago' },
  { type: 'info', icon: Bell, color: 'text-amber-600 bg-amber-50', title: 'Parent-Teacher Meeting', body: 'A parent-teacher meeting is scheduled for Friday, Aug 22 at 10 AM.', time: '2d ago' },
  { type: 'info', icon: Info, color: 'text-blue-600 bg-blue-50', title: 'Assignment Submission', body: "Your child submitted the Mathematics assignment on time.", time: '3d ago' },
]

export default function ParentNotificationsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Notifications</h1>
            <p className="text-sm text-slate-500">Alerts, reminders, and updates about your child&apos;s school activities.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>All Notifications</CardTitle>
                <CardDescription>Recent alerts and updates from the school</CardDescription>
              </div>
              <button className="text-xs font-semibold text-amber-600 hover:underline">Mark all read</button>
            </CardHeader>
            <CardContent className="p-0">
              <div className="divide-y divide-slate-100">
                {notifications.map((n, i) => {
                  const Icon = n.icon
                  return (
                    <div key={i} className="flex items-start gap-4 px-6 py-4 hover:bg-slate-50">
                      <div className={`mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl ${n.color}`}>
                        <Icon className="h-4 w-4" />
                      </div>
                      <div className="flex-1 min-w-0">
                        <div className="flex items-center justify-between">
                          <div className="text-sm font-semibold text-slate-900">{n.title}</div>
                          <div className="text-xs text-slate-400 shrink-0 ml-2">{n.time}</div>
                        </div>
                        <div className="text-sm text-slate-600 mt-0.5">{n.body}</div>
                      </div>
                    </div>
                  )
                })}
              </div>
            </CardContent>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
