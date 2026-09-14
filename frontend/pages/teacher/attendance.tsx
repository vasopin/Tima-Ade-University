import { ClipboardCheck, UserCheck } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'

export default function TeacherAttendancePage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Attendance Register</h1>
              <p className="text-sm text-slate-500">Record daily class attendance and view historical register entries.</p>
            </div>
            <Button size="sm" className="bg-teal-600 text-white hover:bg-teal-700"><UserCheck className="mr-2 h-4 w-4" /> Mark attendance</Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Today&apos;s Attendance Status</h2>
            <p className="text-sm text-slate-500 mt-1">Data Structures 301: Marked (42/42 Present) · Algorithms II: Pending</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
