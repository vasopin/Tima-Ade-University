import { CalendarDays } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function UniversityAdminEventsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Campus Events</h1>
            <p className="text-sm text-slate-500">University convocations, academic conferences, and student galas.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Upcoming Events Calendar</h2>
            <p className="text-sm text-slate-500 mt-1">Annual Research Symposium (Aug 24) · Orientation Week (Sep 1) · Convocation (Oct 12)</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
