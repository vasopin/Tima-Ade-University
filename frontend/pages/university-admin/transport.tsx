import { Bus } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function UniversityAdminTransportPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Campus Transport</h1>
            <p className="text-sm text-slate-500">Bus routes, fleet maintenance, and driver schedules.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Transport Fleet Overview</h2>
            <p className="text-sm text-slate-500 mt-1">Active Buses: 14 | Operational Routes: 8 | Daily Commuters: 1,450</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
