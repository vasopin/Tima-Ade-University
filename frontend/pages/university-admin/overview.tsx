import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import UniversityAdminDashboard from '../../src/components/dashboard/UniversityAdminDashboard'

export default function UniversityAdminOverviewPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <UniversityAdminDashboard />
      </ProtectedRoute>
    </SharedLayout>
  )
}
