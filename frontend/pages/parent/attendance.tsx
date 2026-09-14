import { CalendarDays } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { parentChildren } from '../../src/data/parent'

export default function ParentAttendancePage() {
  const child = parentChildren[0]
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Child Attendance</h1>
            <p className="text-sm text-slate-500">Weekly attendance presence and threshold alert status for {child.name}.</p>
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            <Card className="border-amber-200 bg-amber-50 p-5 shadow-sm">
              <div className="text-xs font-semibold text-amber-700 uppercase">Attendance Rate</div>
              <div className="text-2xl font-bold text-amber-950 mt-2">{child.attendance}</div>
              <div className="text-xs text-amber-700 mt-1">Monitored for current term</div>
            </Card>
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
