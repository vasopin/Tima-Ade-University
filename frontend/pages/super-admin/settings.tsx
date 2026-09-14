import { Settings, Save, Shield } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'

export default function SuperAdminSettingsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['super-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">System Settings</h1>
            <p className="text-sm text-slate-500">Global environment configuration, API keys, and platform settings.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Environment Configuration</CardTitle>
              <CardDescription>System parameters and API defaults</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0 space-y-4">
              <div>
                <label className="block text-sm font-medium text-slate-700">Platform Title</label>
                <input type="text" defaultValue="Tima-Ade University Platform" className="mt-1 w-full max-w-md rounded-xl border border-slate-200 p-2 text-sm outline-none focus:border-crimson-600" />
              </div>
              <div>
                <label className="block text-sm font-medium text-slate-700">API Rate Limit (req/min)</label>
                <input type="number" defaultValue="1000" className="mt-1 w-full max-w-md rounded-xl border border-slate-200 p-2 text-sm outline-none focus:border-crimson-600" />
              </div>
              <div className="pt-2">
                <Button size="sm" className="bg-crimson-700 text-white hover:bg-crimson-800"><Save className="mr-2 h-4 w-4" /> Save settings</Button>
              </div>
            </CardContent>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
