import { TrendingUp } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { gpaTrend } from '../../src/data/student'
import { Bar, BarChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts'

export default function StudentGpaPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['student']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">GPA &amp; Academic Progress</h1>
            <p className="text-sm text-slate-500">Cumulative GPA trends and academic standing over semesters.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>GPA Trend</CardTitle>
              <CardDescription>Term-by-term grade point average</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="h-[280px] w-full">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={gpaTrend}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                    <XAxis dataKey="term" tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                    <YAxis domain={[3, 4]} tickLine={false} axisLine={false} tick={{ fill: '#64748b', fontSize: 12 }} />
                    <Tooltip contentStyle={{ borderRadius: 12, borderColor: '#e2e8f0' }} />
                    <Bar dataKey="gpa" radius={[8, 8, 0, 0]} fill="#7c3aed" name="GPA" />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
