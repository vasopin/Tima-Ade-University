import { KeyRound, ShieldCheck, Lock } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'

const roleRows = [
  { name: 'Super Admin',       users: 3,      permissions: 24, color: 'bg-crimson-600' },
  { name: 'University Admin',  users: 14,     permissions: 18, color: 'bg-slate-700'   },
  { name: 'Teacher',           users: 1840,   permissions: 9,  color: 'bg-sky-600'     },
  { name: 'Student',           users: 61204,  permissions: 5,  color: 'bg-emerald-600' },
  { name: 'Parent',            users: 12030,  permissions: 4,  color: 'bg-amber-600'   },
]

export default function SuperAdminRolesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['super-admin']}>
        <div className="space-y-6">
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Roles &amp; Access Control</h1>
              <p className="text-sm text-slate-500">Configure system permissions, security scopes, and role hierarchies.</p>
            </div>
            <Button size="sm" className="bg-crimson-700 text-white hover:bg-crimson-800">
              <ShieldCheck className="mr-2 h-4 w-4" /> Add custom role
            </Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Role Coverage</CardTitle>
              <CardDescription>Permission scopes mapped across system roles</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-5">
                {roleRows.map((role) => (
                  <div key={role.name} className="rounded-2xl border border-slate-200 p-4">
                    <div className="flex items-center justify-between text-sm">
                      <div className="flex items-center gap-2">
                        <span className={`h-3 w-3 rounded-full ${role.color}`} />
                        <span className="font-bold text-slate-900">{role.name}</span>
                      </div>
                      <div className="text-xs text-slate-500">{role.users.toLocaleString()} users · {role.permissions} active permissions</div>
                    </div>
                    <div className="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-100">
                      <div className={`h-full rounded-full ${role.color}`} style={{ width: `${(role.permissions / 24) * 100}%` }} />
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
