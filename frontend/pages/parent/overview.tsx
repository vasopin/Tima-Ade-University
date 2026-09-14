import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import ParentDashboard from '../../src/components/dashboard/ParentDashboard'

export default function ParentOverviewPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <ParentDashboard />
      </ProtectedRoute>
    </SharedLayout>
  )
}
