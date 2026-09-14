import { Mail } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { messageRows } from '../../src/data/student'

export default function StudentMessagesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Messages &amp; Inbox</h1>
            <p className="text-sm text-slate-500">Communications from course instructors and academic advisors.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Direct Messages</CardTitle>
              <CardDescription>Inbox messages</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {messageRows.map((m, idx) => (
                  <div key={idx} className="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                    <Mail className="h-5 w-5 text-violet-600 shrink-0 mt-0.5" />
                    <div className="flex-1">
                      <div className="flex justify-between">
                        <span className="font-bold text-slate-900">{m.from}</span>
                        <span className="text-xs text-slate-400">{m.time}</span>
                      </div>
                      <div className="text-sm text-slate-600 mt-1">{m.subject}</div>
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
