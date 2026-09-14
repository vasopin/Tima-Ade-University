import { Upload, BookOpen } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { materialRows } from '../../src/data/student'
import { Button } from '../../src/components/ui/button'

export default function StudentMaterialsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Learning Materials</h1>
            <p className="text-sm text-slate-500">Download course lecture slides, notes, and reading handouts.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Resource Library</CardTitle>
              <CardDescription>Uploaded course materials</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {materialRows.map((m) => (
                  <div key={m.title} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div className="flex items-center gap-3">
                      <BookOpen className="h-5 w-5 text-violet-600" />
                      <div>
                        <div className="font-bold text-slate-900">{m.title}</div>
                        <div className="text-xs text-slate-500">{m.type} · Updated {m.updated}</div>
                      </div>
                    </div>
                    <Button variant="outline" size="sm">Download</Button>
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
