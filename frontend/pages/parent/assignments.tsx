import { ClipboardCheck } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { parentChildren } from '../../src/data/parent'
import { Badge } from '../../src/components/ui/badge'

export default function ParentAssignmentsPage() {
  const child = parentChildren[0]
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Child Assignment Status</h1>
            <p className="text-sm text-slate-500">Read-only homework submission monitoring for {child.name}.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Coursework Submissions</CardTitle>
              <CardDescription>Homework monitoring</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {child.assignments.map((a, idx) => (
                  <div key={idx} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div>
                      <div className="font-bold text-slate-900">{a.title}</div>
                      <div className="text-xs text-slate-500">{a.course} · Due {a.due}</div>
                    </div>
                    <Badge variant={a.status === 'Submitted' ? 'success' : 'warning'}>{a.status}</Badge>
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
