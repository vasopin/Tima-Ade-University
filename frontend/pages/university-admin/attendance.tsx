import { ClipboardCheck } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function UniversityAdminAttendancePage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Attendance Statistics</h1>
            <p className="text-sm text-slate-500">Campus-wide student and faculty attendance records.</p>
          </div>

          <div className="grid gap-4 sm:grid-cols-3">
            <Card className="border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs text-slate-500 font-semibold uppercase">Overall Campus Attendance</div>
              <div className="text-2xl font-bold text-slate-900 mt-2">91.4%</div>
              <div className="text-xs text-emerald-600 font-semibold mt-1">Above target threshold</div>
            </Card>
            <Card className="border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs text-slate-500 font-semibold uppercase">Faculty Attendance</div>
              <div className="text-2xl font-bold text-slate-900 mt-2">96.8%</div>
              <div className="text-xs text-emerald-600 font-semibold mt-1">Excellent</div>
            </Card>
            <Card className="border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs text-slate-500 font-semibold uppercase">Flagged Absences</div>
              <div className="text-2xl font-bold text-slate-900 mt-2">42</div>
              <div className="text-xs text-amber-600 font-semibold mt-1">Requires follow-up</div>
            </Card>
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
