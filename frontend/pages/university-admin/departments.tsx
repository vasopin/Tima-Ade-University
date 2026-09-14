import { Building2, Plus } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'
import { Button } from '../../src/components/ui/button'
import { departmentRows } from '../../src/data/university-admin'

export default function UniversityAdminDepartmentsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Academic Departments</h1>
              <p className="text-sm text-slate-500">Configure academic departments, heads of department, and program counts.</p>
            </div>
            <Button size="sm" className="bg-indigo-600 text-white hover:bg-indigo-700">
              <Plus className="mr-2 h-4 w-4" /> Create department
            </Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Departments Overview</CardTitle>
              <CardDescription>Academic units and program offerings</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 text-slate-500">
                      <th className="pb-3 font-medium">Department</th>
                      <th className="pb-3 font-medium">Head of Department</th>
                      <th className="pb-3 font-medium">Programs Offered</th>
                      <th className="pb-3 font-medium">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    {departmentRows.map((dept) => (
                      <tr key={dept.name} className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50">
                        <td className="py-3 font-semibold text-slate-900">{dept.name}</td>
                        <td className="py-3 text-slate-600">{dept.head}</td>
                        <td className="py-3 text-slate-600">{dept.programs} programs</td>
                        <td className="py-3"><Badge variant="success">{dept.status}</Badge></td>
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
