import { UserCog, Plus, Search, Filter } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'
import { Button } from '../../src/components/ui/button'
import { userRows } from '../../src/data/super-admin'

const statusTone: Record<string, 'success' | 'warning' | 'danger' | 'default'> = {
  Active: 'success',
  Pending: 'warning',
  Suspended: 'danger',
}

export default function SuperAdminUsersPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['super-admin']}>
        <div className="space-y-6">
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">User Management</h1>
              <p className="text-sm text-slate-500">Manage user accounts, roles, and status across all universities.</p>
            </div>
            <Button size="sm" className="bg-crimson-700 text-white hover:bg-crimson-800">
              <UserCog className="mr-2 h-4 w-4" /> Invite user
            </Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <CardTitle>Platform Accounts</CardTitle>
                <CardDescription>Directory of all registered platform users</CardDescription>
              </div>
              <div className="flex items-center gap-2">
                <div className="relative">
                  <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                  <input type="text" placeholder="Search user..." className="h-9 w-60 rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-xs outline-none focus:border-crimson-500" />
                </div>
                <Button variant="outline" size="sm"><Filter className="mr-1 h-3.5 w-3.5" /> Filter</Button>
              </div>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 text-slate-500">
                      <th className="pb-3 font-medium">User</th>
                      <th className="pb-3 font-medium">Role</th>
                      <th className="pb-3 font-medium">Status</th>
                      <th className="pb-3 font-medium">Last Login</th>
                      <th className="pb-3 font-medium">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {userRows.map((user) => (
                      <tr key={user.email} className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50">
                        <td className="py-3">
                          <div className="font-semibold text-slate-900">{user.name}</div>
                          <div className="text-xs text-slate-500">{user.email}</div>
                        </td>
                        <td className="py-3 text-slate-600">{user.role}</td>
                        <td className="py-3"><Badge variant={statusTone[user.status] ?? 'default'}>{user.status}</Badge></td>
                        <td className="py-3 text-slate-600">{user.lastLogin}</td>
                        <td className="py-3">
                          <button className="text-xs font-semibold text-crimson-700 hover:underline">Edit</button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </CardContent>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
