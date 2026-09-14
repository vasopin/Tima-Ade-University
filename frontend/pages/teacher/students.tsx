import { UserCheck } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { studentRows } from '../../src/data/teacher'
import { Badge } from '../../src/components/ui/badge'

export default function TeacherStudentsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Student Roster</h1>
            <p className="text-sm text-slate-500">Class rosters, performance indicators, and support flags.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Students Directory</CardTitle>
              <CardDescription>Academic standing for students in your courses</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 text-slate-500">
                      <th className="pb-3 font-medium">Student</th>
                      <th className="pb-3 font-medium">ID</th>
                      <th className="pb-3 font-medium">Performance Score</th>
                      <th className="pb-3 font-medium">Attendance</th>
                      <th className="pb-3 font-medium">Flag</th>
                    </tr>
                  </thead>
                  <tbody>
                    {studentRows.map((s) => (
                      <tr key={s.id} className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50">
                        <td className="py-3 font-semibold text-slate-900">{s.name}</td>
                        <td className="py-3 text-slate-600">{s.id}</td>
                        <td className="py-3 text-slate-600 font-semibold">{s.performance}</td>
                        <td className="py-3 text-slate-600">{s.attendance}</td>
                        <td className="py-3">
                          {parseFloat(s.performance) < 65 ? <Badge variant="danger">At risk</Badge> : <Badge variant="success">Normal</Badge>}
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
