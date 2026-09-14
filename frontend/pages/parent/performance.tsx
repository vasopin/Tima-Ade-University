import { TrendingUp } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { parentChildren } from '../../src/data/parent'
import { Area, AreaChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts'

export default function ParentPerformancePage() {
  const child = parentChildren[0]
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Academic Performance Trend</h1>
            <p className="text-sm text-slate-500">GPA progression chart and performance monitoring for {child.name}.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>GPA Progression</CardTitle>
              <CardDescription>Term-by-term performance trend</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="h-[280px] w-full">
                <ResponsiveContainer width="100%" height="100%">
                  <AreaChart data={child.performanceTrend}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                    <XAxis dataKey="month" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                    <YAxis domain={[3, 4.2]} tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                    <Tooltip contentStyle={{ borderRadius: 12, borderColor: '#e2e8f0' }} />
                    <Area type="monotone" dataKey="score" stroke="#d97706" fill="#fef3c7" strokeWidth={3} name="GPA" />
                  </AreaChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
