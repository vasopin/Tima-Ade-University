import { CheckSquare, Plus } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'
import { assignmentRows } from '../../src/data/teacher'

export default function TeacherAssignmentsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Assignments</h1>
              <p className="text-sm text-slate-500">Create, manage, and review coursework assignments.</p>
            </div>
            <Button size="sm" className="bg-teal-600 text-white hover:bg-teal-700"><Plus className="mr-2 h-4 w-4" /> Create assignment</Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Active Coursework</CardTitle>
              <CardDescription>Open assignments and submission deadlines</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {assignmentRows.map((a) => (
                  <div key={a.title} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div>
                      <div className="font-bold text-slate-900">{a.title}</div>
                      <div className="text-xs text-slate-500">{a.course} · Due {a.due}</div>
                    </div>
                    <div className="text-xs font-semibold text-teal-700">{a.submissions} Submissions</div>
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
