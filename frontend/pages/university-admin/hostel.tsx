import { DoorClosed } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function UniversityAdminHostelPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Hostel Management</h1>
            <p className="text-sm text-slate-500">Student dormitories, room allocations, and warden assignments.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Hostel Accommodation Summary</h2>
            <p className="text-sm text-slate-500 mt-1">Total Capacity: 2,400 Beds | Occupied: 2,180 Beds (90.8% Occupancy)</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
