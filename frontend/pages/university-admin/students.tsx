import { Users, UserPlus, Search } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'
import { Button } from '../../src/components/ui/button'
import { studentRows } from '../../src/data/university-admin'

const statusTone: Record<string, 'success' | 'warning' | 'danger' | 'default'> = {
  Active: 'success',
  'On Leave': 'warning',
  Probation: 'danger',
}

export default function UniversityAdminStudentsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Student Directory</h1>
              <p className="text-sm text-slate-500">Manage enrolled students, registrations, and academic standing.</p>
            </div>
            <Button size="sm" className="bg-indigo-600 text-white hover:bg-indigo-700">
              <UserPlus className="mr-2 h-4 w-4" /> Add student
            </Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <CardTitle>Enrolled Students</CardTitle>
                <CardDescription>Complete student registry for current academic session</CardDescription>
              </div>
              <div className="relative">
                <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                <input type="text" placeholder="Search student by name or ID..." className="h-9 w-64 rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-xs outline-none focus:border-indigo-500" />
              </div>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 text-slate-500">
                      <th className="pb-3 font-medium">Student</th>
                      <th className="pb-3 font-medium">Student ID</th>
                      <th className="pb-3 font-medium">Faculty / Department</th>
                      <th className="pb-3 font-medium">Year</th>
                      <th className="pb-3 font-medium">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    {studentRows.map((student) => (
                      <tr key={student.id} className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50">
                        <td className="py-3 font-semibold text-slate-900">{student.name}</td>
                        <td className="py-3 text-slate-600">{student.id}</td>
                        <td className="py-3 text-slate-600">{student.faculty}</td>
                        <td className="py-3 text-slate-600">{student.year}</td>
                        <td className="py-3"><Badge variant={statusTone[student.status] ?? 'default'}>{student.status}</Badge></td>
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
