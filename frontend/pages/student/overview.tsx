import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import StudentDashboard from '../../src/components/dashboard/StudentDashboard'

export default function StudentOverviewPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <StudentDashboard />
      </ProtectedRoute>
    </SharedLayout>
  )
}
