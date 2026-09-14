import { BookOpen } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { courseRows } from '../../src/data/student'

export default function StudentSubjectsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Subject Breakdown</h1>
            <p className="text-sm text-slate-500">Detailed subject syllabi and credit hour distribution.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Current Semester Subjects</CardTitle>
              <CardDescription>Academic registration overview</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {courseRows.map((c) => (
                  <div key={c.code} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div>
                      <div className="font-bold text-slate-900">{c.title} ({c.code})</div>
                      <div className="text-xs text-slate-500">Instructor: {c.instructor}</div>
                    </div>
                    <div className="text-xs font-semibold text-violet-700">{c.next}</div>
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
