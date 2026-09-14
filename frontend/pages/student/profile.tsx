import { User } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function StudentProfilePage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Student Profile</h1>
            <p className="text-sm text-slate-500">Personal academic details, major, and student ID card.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Demo Student</h2>
            <p className="text-sm text-slate-500 mt-1">Student ID: STU-84920 · Major: BSc Computer Science · Year: 3</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
