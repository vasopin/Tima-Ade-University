import { Megaphone, Plus } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'
import { announcementRows } from '../../src/data/teacher'

export default function TeacherAnnouncementsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Class Announcements</h1>
              <p className="text-sm text-slate-500">Post notices and updates for students in your classes.</p>
            </div>
            <Button size="sm" className="bg-teal-600 text-white hover:bg-teal-700"><Plus className="mr-2 h-4 w-4" /> New announcement</Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Posted Announcements</CardTitle>
              <CardDescription>Recent class broadcasts</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {announcementRows.map((a) => (
                  <div key={a.title} className="rounded-xl border border-slate-200 p-4">
                    <div className="font-bold text-slate-900">{a.title}</div>
                    <div className="mt-1 text-xs text-slate-500">{a.audience} · Posted {a.date}</div>
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
