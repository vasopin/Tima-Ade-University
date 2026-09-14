import { CalendarDays, MapPin, Users } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

const events = [
  { title: 'Annual Sports Day', date: 'Sep 20, 2025', location: 'Main School Field', audience: 'All Students & Parents', status: 'Upcoming' },
  { title: 'Science Fair Exhibition', date: 'Oct 5, 2025', location: 'School Hall', audience: 'SS2 & SS3 Students', status: 'Upcoming' },
  { title: 'Inter-House Music Competition', date: 'Oct 18, 2025', location: 'Auditorium', audience: 'All Students', status: 'Upcoming' },
  { title: 'Graduation Ceremony', date: 'Nov 10, 2025', location: 'Main Hall', audience: 'SS3 Graduating Class', status: 'Upcoming' },
  { title: 'Prize Giving Day', date: 'Nov 15, 2025', location: 'School Grounds', audience: 'All Students & Parents', status: 'Upcoming' },
]

export default function ParentEventsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">School Events</h1>
            <p className="text-sm text-slate-500">Upcoming school events, ceremonies, and activities you and your child can attend.</p>
          </div>

          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {events.map((ev, i) => (
              <Card key={i} className="border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <CardContent className="p-5">
                  <div className="mb-3 inline-block rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                    {ev.status}
                  </div>
                  <h3 className="text-base font-bold text-slate-900">{ev.title}</h3>
                  <div className="mt-3 space-y-1.5 text-sm text-slate-600">
                    <div className="flex items-center gap-2">
                      <CalendarDays className="h-3.5 w-3.5 text-slate-400" />
                      {ev.date}
                    </div>
                    <div className="flex items-center gap-2">
                      <MapPin className="h-3.5 w-3.5 text-slate-400" />
                      {ev.location}
                    </div>
                    <div className="flex items-center gap-2">
                      <Users className="h-3.5 w-3.5 text-slate-400" />
                      {ev.audience}
                    </div>
                  </div>
                  <button className="mt-4 w-full rounded-lg border border-amber-200 bg-amber-50 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100 transition-colors">
                    Mark as Attending
                  </button>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
