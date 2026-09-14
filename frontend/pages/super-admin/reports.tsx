import { FileBarChart2, Download, Calendar } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'

const reports = [
  { name: 'Global Platform Usage Q2', format: 'PDF', date: '2026-08-01', size: '4.2 MB' },
  { name: 'University Onboarding Audit', format: 'CSV', date: '2026-07-28', size: '1.8 MB' },
  { name: 'Security Incident Summary July', format: 'PDF', date: '2026-08-05', size: '2.1 MB' },
]

export default function SuperAdminReportsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['super-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Platform Reports</h1>
            <p className="text-sm text-slate-500">System-wide reports, financial compliance summaries, and export logs.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Generated Reports</CardTitle>
              <CardDescription>Available system export files for download</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {reports.map((r) => (
                  <div key={r.name} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div className="flex items-center gap-3">
                      <FileBarChart2 className="h-5 w-5 text-crimson-700" />
                      <div>
                        <div className="font-semibold text-slate-900">{r.name}</div>
                        <div className="text-xs text-slate-500">{r.format} · {r.date} · {r.size}</div>
                      </div>
                    </div>
                    <Button size="sm" variant="outline"><Download className="mr-2 h-3.5 w-3.5" /> Download</Button>
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
