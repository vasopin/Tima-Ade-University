import { CalendarDays } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'

const calendarEvents = [
  { date: 'Aug 18', day: 'Mon', event: 'Second Term Begins', type: 'term', color: 'bg-blue-100 text-blue-700' },
  { date: 'Aug 22', day: 'Fri', event: 'Parent-Teacher Meeting', type: 'meeting', color: 'bg-amber-100 text-amber-700' },
  { date: 'Sep 5', day: 'Fri', event: 'Mid-Term Break Starts', type: 'holiday', color: 'bg-emerald-100 text-emerald-700' },
  { date: 'Sep 12', day: 'Mon', event: 'Classes Resume', type: 'term', color: 'bg-blue-100 text-blue-700' },
  { date: 'Sep 20', day: 'Sat', event: 'Sports Day', type: 'event', color: 'bg-violet-100 text-violet-700' },
  { date: 'Oct 1', day: 'Wed', event: 'Independence Day (No School)', type: 'holiday', color: 'bg-emerald-100 text-emerald-700' },
  { date: 'Oct 15', day: 'Wed', event: 'Second Term Exams Begin', type: 'exam', color: 'bg-red-100 text-red-700' },
  { date: 'Oct 30', day: 'Thu', event: 'Second Term Ends', type: 'term', color: 'bg-blue-100 text-blue-700' },
]

export default function ParentCalendarPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Academic Calendar</h1>
            <p className="text-sm text-slate-500">Key term dates, holidays, exams, and school events for this academic year.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Upcoming Events</CardTitle>
              <CardDescription>School calendar for the current academic session</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {calendarEvents.map((ev, i) => (
                  <div key={i} className="flex items-center gap-4 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <div className="w-16 shrink-0 text-center">
                      <div className="text-xs text-slate-500 font-medium">{ev.day}</div>
                      <div className="text-sm font-bold text-slate-900">{ev.date}</div>
                    </div>
                    <div className="flex-1 text-sm font-medium text-slate-800">{ev.event}</div>
                    <span className={`rounded-full px-2.5 py-0.5 text-xs font-semibold ${ev.color}`}>{ev.type}</span>
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
