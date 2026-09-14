import { FileBarChart2 } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function UniversityAdminReportsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Institutional Reports</h1>
            <p className="text-sm text-slate-500">Operational analytics, enrollment summaries, and accreditation reports.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Report Downloads</h2>
            <p className="text-sm text-slate-500 mt-1">Enrollment Audit Q2 (PDF) · Faculty Workload Analysis (CSV) · Financial Summary (PDF)</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
