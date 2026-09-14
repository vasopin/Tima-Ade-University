import { PenLine } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { submissionRows } from '../../src/data/teacher'
import { Badge } from '../../src/components/ui/badge'

export default function TeacherSubmissionsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Grading Queue</h1>
            <p className="text-sm text-slate-500">Student submissions awaiting review and grading.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Submissions Review</CardTitle>
              <CardDescription>Items pending evaluation</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {submissionRows.map((s, idx) => (
                  <div key={idx} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div>
                      <div className="font-bold text-slate-900">{s.student}</div>
                      <div className="text-xs text-slate-500">{s.assignment}</div>
                    </div>
                    <div className="flex items-center gap-2">
                      <Badge variant="warning">{s.status}</Badge>
                      <button className="rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white">Grade now →</button>
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
