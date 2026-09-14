import { User } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { parentChildren } from '../../src/data/parent'

export default function ParentChildProfilePage() {
  const child = parentChildren[0]
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Child Profile &amp; Advisor</h1>
            <p className="text-sm text-slate-500">Academic advisor details and child enrollment summary.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">{child.name}</h2>
            <p className="text-sm text-slate-500 mt-1">Class: {child.grade} · Academic Advisor: {child.advisor}</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
