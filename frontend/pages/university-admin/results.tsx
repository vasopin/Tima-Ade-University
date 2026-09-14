import { TrendingUp } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { resultsSummary } from '../../src/data/university-admin'

export default function UniversityAdminResultsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Academic Results</h1>
            <p className="text-sm text-slate-500">Semester grades, GPA distributions, and transcript approvals.</p>
          </div>

          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {resultsSummary.map((r) => (
              <Card key={r.label} className="border-indigo-100 bg-indigo-50 p-5 shadow-sm">
                <div className="text-sm font-semibold text-indigo-700">{r.label}</div>
                <div className="text-2xl font-bold text-indigo-950 mt-2">{r.value}</div>
                <div className="text-xs text-indigo-600 mt-1">↑ {r.delta}</div>
              </Card>
            ))}
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
