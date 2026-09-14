import { CalendarCheck } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'
import { examRows } from '../../src/data/university-admin'

export default function UniversityAdminExamsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Exams &amp; Assessments</h1>
            <p className="text-sm text-slate-500">Examination schedules, hall allocations, and invigilation.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Examination Schedule</CardTitle>
              <CardDescription>Scheduled exams and readiness status</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {examRows.map((e) => (
                  <div key={e.exam} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div>
                      <div className="font-bold text-slate-900">{e.exam}</div>
                      <div className="text-xs text-slate-500">{e.course}</div>
                    </div>
                    <Badge variant="info">{e.status}</Badge>
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
