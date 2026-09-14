import { Settings } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function StudentSettingsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Student Settings</h1>
            <p className="text-sm text-slate-500">Manage account security, email notifications, and UI preferences.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Account Preferences</h2>
            <p className="text-sm text-slate-500 mt-1">Assignment Due SMS Alerts: Enabled · Email Digest: Weekly</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
