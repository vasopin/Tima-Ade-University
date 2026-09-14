import { BookOpen, Plus } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'

const courses = [
  { code: 'CS301', title: 'Data Structures & Algorithms', credits: 4, dept: 'Computer Science' },
  { code: 'MTH201', title: 'Linear Algebra & Calculus', credits: 3, dept: 'Mathematics' },
  { code: 'EE101', title: 'Circuit Analysis I', credits: 4, dept: 'Electrical Engineering' },
]

export default function UniversityAdminCoursesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Courses &amp; Subjects</h1>
              <p className="text-sm text-slate-500">Course catalog, credit hours, and subject assignments.</p>
            </div>
            <Button size="sm" className="bg-indigo-600 text-white hover:bg-indigo-700"><Plus className="mr-2 h-4 w-4" /> Add course</Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Course Catalog</CardTitle>
              <CardDescription>Offered subjects for current academic term</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {courses.map((c) => (
                  <div key={c.code} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div>
                      <div className="font-bold text-slate-900">{c.title} ({c.code})</div>
                      <div className="text-xs text-slate-500">{c.dept} · {c.credits} Credits</div>
                    </div>
                    <Button variant="outline" size="sm">Configure</Button>
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
