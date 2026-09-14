import { CheckSquare, Upload } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { assignmentRows } from '../../src/data/student'
import { Badge } from '../../src/components/ui/badge'
import { Button } from '../../src/components/ui/button'

export default function StudentAssignmentsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Assignments &amp; Homework</h1>
              <p className="text-sm text-slate-500">Track pending homework deadlines and submit coursework files.</p>
            </div>
            <Button size="sm" className="bg-violet-600 text-white hover:bg-violet-700"><Upload className="mr-2 h-4 w-4" /> Submit assignment</Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Coursework Deadlines</CardTitle>
              <CardDescription>Active and submitted coursework</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {assignmentRows.map((a) => (
                  <div key={a.title} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div>
                      <div className="font-bold text-slate-900">{a.title}</div>
                      <div className="text-xs text-slate-500">{a.course} · Due {a.due}</div>
                    </div>
                    <div className="flex items-center gap-3">
                      <div className="text-xs text-slate-500">Score: <span className="font-semibold text-slate-900">{a.score}</span></div>
                      <Badge variant={a.status === 'Submitted' ? 'success' : 'warning'}>{a.status}</Badge>
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
