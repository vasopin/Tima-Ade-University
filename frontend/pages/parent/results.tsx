import { GraduationCap } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { parentChildren } from '../../src/data/parent'

export default function ParentResultsPage() {
  const child = parentChildren[0]
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Child Grades &amp; Results</h1>
            <p className="text-sm text-slate-500">Read-only term marks and assessment results for {child.name}.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Academic Results</CardTitle>
              <CardDescription>Term marks breakdown</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {child.results.map((r) => (
                  <div key={r.course} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div>
                      <div className="font-bold text-slate-900">{r.course}</div>
                      <div className="text-xs text-slate-500">{r.term}</div>
                    </div>
                    <div className="text-right">
                      <div className="font-semibold text-slate-900">{r.score}</div>
                      <div className="text-xs font-bold text-amber-700">{r.grade}</div>
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
