import { Megaphone } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { parentChildren } from '../../src/data/parent'

export default function ParentAnnouncementsPage() {
  const child = parentChildren[0]
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">School Announcements</h1>
            <p className="text-sm text-slate-500">Official broadcasts for parents and guardians of {child.name}.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>School Notices</CardTitle>
              <CardDescription>Important campus updates</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {child.announcements.map((a, idx) => (
                  <div key={idx} className="rounded-xl border border-slate-200 p-4">
                    <div className="font-bold text-slate-900">{a.title}</div>
                    <div className="text-xs text-slate-500 mt-1">{a.audience} · Posted {a.date}</div>
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
