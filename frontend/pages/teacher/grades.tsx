import { GraduationCap } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { gradeRows } from '../../src/data/teacher'

export default function TeacherGradesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Gradebook</h1>
            <p className="text-sm text-slate-500">Student grade records and official mark entry.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Recorded Grades</CardTitle>
              <CardDescription>Course marks breakdown</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 text-slate-500">
                      <th className="pb-3 font-medium">Student</th>
                      <th className="pb-3 font-medium">Course</th>
                      <th className="pb-3 font-medium">Grade</th>
                      <th className="pb-3 font-medium">Score</th>
                    </tr>
                  </thead>
                  <tbody>
                    {gradeRows.map((g, idx) => (
                      <tr key={idx} className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50">
                        <td className="py-3 font-semibold text-slate-900">{g.student}</td>
                        <td className="py-3 text-slate-600">{g.course}</td>
                        <td className="py-3 text-teal-700 font-bold">{g.grade}</td>
                        <td className="py-3 text-slate-600">{g.score}</td>
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
