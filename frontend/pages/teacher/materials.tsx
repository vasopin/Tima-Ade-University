import { Upload, BookOpen } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'
import { resourceRows } from '../../src/data/teacher'

export default function TeacherMaterialsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['teacher']}>
        <div className="space-y-6">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Course Materials</h1>
              <p className="text-sm text-slate-500">Shared lecture slides, reading lists, and lab instructions.</p>
            </div>
            <Button size="sm" className="bg-teal-600 text-white hover:bg-teal-700"><Upload className="mr-2 h-4 w-4" /> Upload material</Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Uploaded Resources</CardTitle>
              <CardDescription>Shared course files for students</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {resourceRows.map((r) => (
                  <div key={r.title} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div className="flex items-center gap-3">
                      <BookOpen className="h-5 w-5 text-teal-600" />
                      <div>
                        <div className="font-bold text-slate-900">{r.title}</div>
                        <div className="text-xs text-slate-500">{r.type} · Uploaded {r.date}</div>
                      </div>
                    </div>
                    <Button variant="outline" size="sm">Manage</Button>
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
