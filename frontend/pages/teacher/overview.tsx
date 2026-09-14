import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import TeacherDashboard from '../../src/components/dashboard/TeacherDashboard'

export default function TeacherOverviewPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <TeacherDashboard />
      </ProtectedRoute>
    </SharedLayout>
  )
}
