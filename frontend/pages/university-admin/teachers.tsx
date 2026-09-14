import { School, UserPlus } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'
import { Button } from '../../src/components/ui/button'
import { teacherRows } from '../../src/data/university-admin'

const statusTone: Record<string, 'success' | 'warning' | 'default'> = {
  Active: 'success',
  'On Leave': 'warning',
}

export default function UniversityAdminTeachersPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Faculty &amp; Teaching Staff</h1>
              <p className="text-sm text-slate-500">Manage academic staff, course assignments, and department allocations.</p>
            </div>
            <Button size="sm" className="bg-indigo-600 text-white hover:bg-indigo-700">
              <UserPlus className="mr-2 h-4 w-4" /> Add faculty member
            </Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Teaching Staff Registry</CardTitle>
              <CardDescription>All active professors, lecturers, and teaching assistants</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {teacherRows.map((t) => (
                  <div key={t.name} className="rounded-2xl border border-slate-200 p-4">
                    <div className="flex items-center justify-between">
                      <div className="font-bold text-slate-900">{t.name}</div>
                      <Badge variant={statusTone[t.status] ?? 'default'}>{t.status}</Badge>
                    </div>
                    <div className="mt-1 text-xs text-slate-500">{t.department}</div>
                    <div className="mt-3 rounded-xl bg-indigo-50 p-2.5 text-xs font-semibold text-indigo-700">
                      Assigned: {t.course}
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
