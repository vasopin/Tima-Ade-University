import { CalendarCheck } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { examRows } from '../../src/data/student'
import { Badge } from '../../src/components/ui/badge'

export default function StudentExamsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Exam Schedule</h1>
            <p className="text-sm text-slate-500">Upcoming midterms, final exam hall seats, and schedules.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Assessments Calendar</CardTitle>
              <CardDescription>Scheduled examinations</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {examRows.map((e) => (
                  <div key={e.title} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div>
                      <div className="font-bold text-slate-900">{e.title}</div>
                      <div className="text-xs text-slate-500">{e.course} · {e.date}</div>
                    </div>
                    <Badge variant="info">{e.status}</Badge>
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
