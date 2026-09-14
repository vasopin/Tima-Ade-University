import { FileText, Download } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'
import { Button } from '../../src/components/ui/button'
import { auditRows } from '../../src/data/super-admin'

const statusTone: Record<string, 'success' | 'warning' | 'danger' | 'default'> = {
  Success: 'success',
  Warning: 'warning',
  Failed: 'danger',
}

export default function SuperAdminAuditPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['super-admin']}>
        <div className="space-y-6">
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Audit Logs</h1>
              <p className="text-sm text-slate-500">Immutable administrative action trail across all university tenants.</p>
            </div>
            <Button size="sm" variant="outline"><Download className="mr-2 h-4 w-4" /> Export CSV</Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>System Audit Trail</CardTitle>
              <CardDescription>Most recent administrative actions</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 text-slate-500">
                      <th className="pb-3 font-medium">Actor</th>
                      <th className="pb-3 font-medium">Action</th>
                      <th className="pb-3 font-medium">Timestamp</th>
                      <th className="pb-3 font-medium">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    {auditRows.map((audit) => (
                      <tr key={`${audit.actor}-${audit.timestamp}`} className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50">
                        <td className="py-3 font-medium text-slate-800">{audit.actor}</td>
                        <td className="py-3 text-slate-600">{audit.action}</td>
                        <td className="py-3 text-slate-600">{audit.timestamp}</td>
                        <td className="py-3"><Badge variant={statusTone[audit.status] ?? 'default'}>{audit.status}</Badge></td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </CardContent>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
