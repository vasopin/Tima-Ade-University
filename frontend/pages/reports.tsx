import SharedLayout from '../src/layouts/SharedLayout'
import ProtectedRoute from '../src/components/ProtectedRoute'
import ReportsPage from '../src/components/reports/ReportsPage'

export default function ReportsRoute() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={["super-admin", "university-admin", "teacher", "student", "parent"]}>
        <ReportsPage />
      </ProtectedRoute>
    </SharedLayout>
  )
}
