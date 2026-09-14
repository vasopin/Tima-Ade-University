import { BookOpen } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { parentChildren } from '../../src/data/parent'

export default function ParentCoursesPage() {
  const child = parentChildren[0]
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Child Courses</h1>
            <p className="text-sm text-slate-500">Enrolled subjects and learning modules for {child.name}.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Enrolled Subjects</h2>
            <p className="text-sm text-slate-500 mt-1">Data Structures 301 · Linear Algebra · Database Systems</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
