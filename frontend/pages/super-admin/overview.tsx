import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import SuperAdminDashboard from '../../src/components/dashboard/SuperAdminDashboard'

export default function SuperAdminOverviewPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['super-admin']}>
        <SuperAdminDashboard />
      </ProtectedRoute>
    </SharedLayout>
  )
}
