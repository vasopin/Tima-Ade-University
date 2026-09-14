import { Users } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { classRows } from '../../src/data/teacher'

export default function TeacherClassesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">My Classes</h1>
            <p className="text-sm text-slate-500">Active class sections, enrollment counts, and next session details.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Assigned Classes</CardTitle>
              <CardDescription>Current semester class sections</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 text-slate-500">
                      <th className="pb-3 font-medium">Class Section</th>
                      <th className="pb-3 font-medium">Enrolled Students</th>
                      <th className="pb-3 font-medium">Avg Attendance</th>
                      <th className="pb-3 font-medium">Next Session</th>
                    </tr>
                  </thead>
                  <tbody>
                    {classRows.map((row) => (
                      <tr key={row.name} className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50">
                        <td className="py-3 font-bold text-slate-900">{row.name}</td>
                        <td className="py-3 text-slate-600">{row.students} students</td>
                        <td className="py-3 text-teal-700 font-semibold">{row.attendance}</td>
                        <td className="py-3 text-slate-600">{row.next}</td>
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
