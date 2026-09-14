import { BookOpen } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { courseRows } from '../../src/data/student'

export default function StudentCoursesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Enrolled Courses</h1>
            <p className="text-sm text-slate-500">Active subjects, syllabus progress, and course instructors.</p>
          </div>

          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {courseRows.map((c) => (
              <Card key={c.code} className="border-slate-200 bg-white shadow-sm p-5">
                <div className="font-bold text-slate-900">{c.title}</div>
                <div className="text-xs text-slate-500">{c.code} · {c.instructor}</div>
                <div className="mt-3">
                  <div className="flex justify-between text-xs font-semibold text-slate-500 mb-1">
                    <span>Progress</span>
                    <span>{c.progress}%</span>
                  </div>
                  <div className="h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div className="h-full bg-violet-600" style={{ width: `${c.progress}%` }} />
                  </div>
                </div>
              </Card>
            ))}
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
