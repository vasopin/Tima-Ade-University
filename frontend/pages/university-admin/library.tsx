import { Library } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function UniversityAdminLibraryPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Library System</h1>
            <p className="text-sm text-slate-500">Book catalog, digital journals, and library circulation management.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Library Assets Summary</h2>
            <p className="text-sm text-slate-500 mt-1">Total Titles: 45,200 | Active Borrows: 1,840 | Digital Subscriptions: 18</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
