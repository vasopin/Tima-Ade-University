import { Building2, Plus, Globe } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'
import { Button } from '../../src/components/ui/button'
import { universityRows } from '../../src/data/super-admin'

const statusTone: Record<string, 'success' | 'warning' | 'danger' | 'default'> = {
  Active: 'success',
  Pending: 'warning',
  Suspended: 'danger',
}

export default function SuperAdminUniversitiesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['super-admin']}>
        <div className="space-y-6">
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Universities</h1>
              <p className="text-sm text-slate-500">Manage registered university campuses and platform deployments.</p>
            </div>
            <Button size="sm" className="bg-crimson-700 text-white hover:bg-crimson-800">
              <Building2 className="mr-2 h-4 w-4" /> Add university
            </Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>University Portfolio</CardTitle>
              <CardDescription>Overview of all active institutions on the platform</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 text-slate-500">
                      <th className="pb-3 font-medium">Institution</th>
                      <th className="pb-3 font-medium">Location</th>
                      <th className="pb-3 font-medium">Students</th>
                      <th className="pb-3 font-medium">Teachers</th>
                      <th className="pb-3 font-medium">Status</th>
                      <th className="pb-3 font-medium">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    {universityRows.map((campus) => (
                      <tr key={campus.name} className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50">
                        <td className="py-3 font-semibold text-slate-900">{campus.name}</td>
                        <td className="py-3 text-slate-600">{campus.location}</td>
                        <td className="py-3 text-slate-600">{campus.students.toLocaleString()}</td>
                        <td className="py-3 text-slate-600">{campus.teachers}</td>
                        <td className="py-3"><Badge variant={statusTone[campus.status] ?? 'default'}>{campus.status}</Badge></td>
                        <td className="py-3">
                          <button className="text-xs font-semibold text-crimson-700 hover:underline">Manage →</button>
                        </td>
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
