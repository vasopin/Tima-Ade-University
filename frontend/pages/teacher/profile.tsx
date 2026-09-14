import { User } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function TeacherProfilePage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Faculty Profile</h1>
            <p className="text-sm text-slate-500">Personal information, academic department, and teaching rating.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Prof. Demo Teacher</h2>
            <p className="text-sm text-slate-500 mt-1">Department of Computer Science · Rating: 4.8 / 5.0 · 154 Active Students</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
