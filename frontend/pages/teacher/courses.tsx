import { BookOpen } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { courseRows } from '../../src/data/teacher'

export default function TeacherCoursesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">My Courses</h1>
            <p className="text-sm text-slate-500">Taught courses, syllabus completion, and learning materials.</p>
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            {courseRows.map((c) => (
              <Card key={c.code} className="border-slate-200 bg-white shadow-sm p-5">
                <div className="font-bold text-slate-900 text-lg">{c.title}</div>
                <div className="text-xs text-slate-500">{c.code} · {c.term}</div>
                <div className="mt-4">
                  <div className="flex justify-between text-xs text-slate-500 font-semibold mb-1">
                    <span>Syllabus Completion</span>
                    <span>{c.completion}%</span>
                  </div>
                  <div className="h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div className="h-full bg-teal-600" style={{ width: `${c.completion}%` }} />
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
