import { CartesianGrid, Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts'
import { Activity, Download, RefreshCw, Zap } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'
import { Button } from '../../src/components/ui/button'

const growthTrend = [
  { month: 'Feb', users: 74200, universities: 12 },
  { month: 'Mar', users: 76100, universities: 12 },
  { month: 'Apr', users: 77300, universities: 13 },
  { month: 'May', users: 78900, universities: 13 },
  { month: 'Jun', users: 80100, universities: 14 },
  { month: 'Jul', users: 81500, universities: 14 },
  { month: 'Aug', users: 82410, universities: 14 },
]

export default function SuperAdminAnalyticsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['super-admin']}>
        <div className="space-y-6">
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Growth Analytics</h1>
              <p className="text-sm text-slate-500">Platform user adoption, university onboarding, and system performance metrics.</p>
            </div>
            <div className="flex gap-2">
              <Button size="sm" variant="outline"><RefreshCw className="mr-2 h-3.5 w-3.5" /> Refresh</Button>
              <Button size="sm" className="bg-crimson-700 text-white hover:bg-crimson-800"><Download className="mr-2 h-3.5 w-3.5" /> Export analytics</Button>
            </div>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>Platform Growth Trend</CardTitle>
                <CardDescription>Active user registrations and connected campuses over time</CardDescription>
              </div>
              <Badge variant="info">Live Stream</Badge>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="h-[340px] w-full">
                <ResponsiveContainer width="100%" height="100%">
                  <LineChart data={growthTrend}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                    <XAxis dataKey="month" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                    <YAxis yAxisId="left" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                    <YAxis yAxisId="right" orientation="right" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} domain={[10, 16]} />
                    <Tooltip contentStyle={{ borderRadius: 12, borderColor: '#e2e8f0' }} />
                    <Line yAxisId="left" type="monotone" dataKey="users" stroke="#9f1239" strokeWidth={2.5} dot={false} name="Total Users" />
                    <Line yAxisId="right" type="monotone" dataKey="universities" stroke="#0f172a" strokeWidth={2} strokeDasharray="4 2" dot={false} name="Universities" />
                  </LineChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>

          <div className="grid gap-4 sm:grid-cols-3">
            <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs font-semibold text-slate-500 uppercase tracking-wide">Monthly Active Users</div>
              <div className="mt-2 text-2xl font-bold text-slate-900">82,410</div>
              <div className="mt-1 text-xs text-emerald-600 font-semibold">+4.8% vs last month</div>
            </div>
            <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs font-semibold text-slate-500 uppercase tracking-wide">Avg Session Time</div>
              <div className="mt-2 text-2xl font-bold text-slate-900">24.6 min</div>
              <div className="mt-1 text-xs text-emerald-600 font-semibold">+1.2 min vs last week</div>
            </div>
            <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs font-semibold text-slate-500 uppercase tracking-wide">Peak Concurrency</div>
              <div className="mt-2 text-2xl font-bold text-slate-900">5,890</div>
              <div className="mt-1 text-xs text-slate-500">Recorded 10:30 AM</div>
            </div>
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
