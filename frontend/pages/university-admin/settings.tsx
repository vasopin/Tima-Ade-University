import { Settings } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function UniversityAdminSettingsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Campus Settings</h1>
            <p className="text-sm text-slate-500">Configure campus details, semester dates, and academic parameters.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Campus Configuration</h2>
            <p className="text-sm text-slate-500 mt-1">Campus Name: Tima-Ade University Main Campus · Current Term: Fall 2026</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
