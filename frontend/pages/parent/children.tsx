import { Users } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { parentChildren } from '../../src/data/parent'
import { Badge } from '../../src/components/ui/badge'

export default function ParentChildrenPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">My Children</h1>
            <p className="text-sm text-slate-500">Linked student profiles under guardian monitoring.</p>
          </div>

          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {parentChildren.map((child) => (
              <Card key={child.id} className="border-amber-200 bg-white p-5 shadow-sm">
                <div className="flex items-center gap-3">
                  <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 font-bold text-amber-700">
                    {child.name.split(' ').map((p) => p[0]).slice(0, 2).join('')}
                  </div>
                  <div>
                    <div className="font-bold text-slate-900">{child.name}</div>
                    <div className="text-xs text-slate-500">{child.grade}</div>
                  </div>
                </div>
                <div className="mt-4 grid grid-cols-2 gap-2 text-xs">
                  <div className="rounded-lg bg-slate-50 p-2">
                    <div className="text-slate-400">GPA</div>
                    <div className="font-bold text-slate-800">{child.gpa}</div>
                  </div>
                  <div className="rounded-lg bg-slate-50 p-2">
                    <div className="text-slate-400">Attendance</div>
                    <div className="font-bold text-slate-800">{child.attendance}</div>
                  </div>
                </div>
                <Badge variant="success" className="mt-3">{child.status}</Badge>
              </Card>
            ))}
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
