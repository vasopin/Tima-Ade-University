import { useMemo, useState } from 'react'
import { AreaChart, Area, ResponsiveContainer, XAxis, YAxis, Tooltip, PieChart, Pie, Cell } from 'recharts'
import { Button } from '../ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '../ui/card'
import { useAuth } from '../../context/AuthContext'

const sampleStudents = [
  { id: 'S001', name: 'Amina Yusuf', gpa: 3.8, attendance: 95, university: 'Tima-Ade University', department: 'Computer Science', class: 'CS-2024' },
  { id: 'S002', name: 'John Mensah', gpa: 3.2, attendance: 88, university: 'Tima-Ade University', department: 'Computer Science', class: 'CS-2024' },
  { id: 'S003', name: 'Lina Ahmed', gpa: 2.9, attendance: 72, university: 'Tima-Ade University', department: 'Business', class: 'BUS-2024' },
  { id: 'S004', name: 'Kwame Opoku', gpa: 3.5, attendance: 90, university: 'Tima-Ade University', department: 'Mathematics', class: 'MA-2024' },
]

const enrollmentTrend = [
  { month: 'Jan', students: 120 },
  { month: 'Feb', students: 128 },
  { month: 'Mar', students: 132 },
  { month: 'Apr', students: 140 },
  { month: 'May', students: 150 },
  { month: 'Jun', students: 148 },
  { month: 'Jul', students: 160 },
]

const feeByDept = [
  { name: 'Computer Science', value: 45000, color: '#0f172a' },
  { name: 'Business', value: 28000, color: '#7c3aed' },
  { name: 'Mathematics', value: 19000, color: '#0891b2' },
]

export default function ReportsPage() {
  const { user } = useAuth()
  const [from, setFrom] = useState<string>('2026-01-01')
  const [to, setTo] = useState<string>('2026-12-31')
  const [university, setUniversity] = useState<string>('All')
  const [department, setDepartment] = useState<string>('All')

  const filteredStudents = useMemo(() => sampleStudents.filter((s) => (university === 'All' || s.university === university) && (department === 'All' || s.department === department)), [university, department])

  const exportCsv = () => {
    const rows = filteredStudents.map((s) => ({ id: s.id, name: s.name, gpa: s.gpa, attendance: s.attendance, university: s.university, department: s.department }))
    const csv = [Object.keys(rows[0] || {}).join(','), ...rows.map((r) => Object.values(r).join(','))].join('\n')
    const blob = new Blob([csv], { type: 'text/csv' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `report_students_${university}_${department}.csv`
    a.click()
    URL.revokeObjectURL(url)
  }

  return (
    <div className="space-y-6 p-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Reports & Analytics</h1>
          <p className="text-sm text-slate-500">Accessible reports depend on your role: {user?.role}</p>
        </div>
        <div className="flex items-center gap-3">
          <Button onClick={exportCsv} className="bg-crimson-600 text-white">Export students</Button>
        </div>
      </div>

      <section className="grid gap-6 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Enrollment trend</CardTitle>
            <CardDescription>Students enrolled over time</CardDescription>
          </CardHeader>
          <CardContent className="h-56">
            <ResponsiveContainer width="100%" height="100%">
              <AreaChart data={enrollmentTrend}>
                <defs>
                  <linearGradient id="grad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#f97316" stopOpacity={0.3} />
                    <stop offset="95%" stopColor="#f97316" stopOpacity={0} />
                  </linearGradient>
                </defs>
                <XAxis dataKey="month" />
                <YAxis />
                <Tooltip />
                <Area type="monotone" dataKey="students" stroke="#f97316" fill="url(#grad)" />
              </AreaChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Fees by department</CardTitle>
            <CardDescription>Collected fees (sample)</CardDescription>
          </CardHeader>
          <CardContent className="h-56">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie data={feeByDept} dataKey="value" nameKey="name" innerRadius={40} outerRadius={80} paddingAngle={4}>
                  {feeByDept.map((e) => <Cell key={e.name} fill={e.color} />)}
                </Pie>
                <Tooltip formatter={(v:any) => [`${v}`, 'Amount']} />
              </PieChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </section>

      <section>
        <Card>
          <CardHeader>
            <CardTitle>Student academic report</CardTitle>
            <CardDescription>List and basic stats — filter by university/department</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-3 mb-4">
              <label className="text-sm">From</label>
              <input type="date" value={from} onChange={(e) => setFrom(e.target.value)} className="rounded border px-2 py-1" />
              <label className="text-sm">To</label>
              <input type="date" value={to} onChange={(e) => setTo(e.target.value)} className="rounded border px-2 py-1" />

              <select value={university} onChange={(e) => setUniversity(e.target.value)} className="rounded border px-2 py-1">
                <option>All</option>
                <option>Tima-Ade University</option>
                <option>Tima-Ade University</option>
              </select>

              <select value={department} onChange={(e) => setDepartment(e.target.value)} className="rounded border px-2 py-1">
                <option>All</option>
                <option>Computer Science</option>
                <option>Business</option>
                <option>Mathematics</option>
              </select>
            </div>

            <div className="overflow-x-auto">
              <table className="min-w-full text-left text-sm">
                <thead>
                  <tr className="border-b">
                    <th className="py-2">ID</th>
                    <th className="py-2">Name</th>
                    <th className="py-2">GPA</th>
                    <th className="py-2">Attendance</th>
                    <th className="py-2">University</th>
                    <th className="py-2">Department</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredStudents.map((s) => (
                    <tr key={s.id} className="border-b">
                      <td className="py-2">{s.id}</td>
                      <td className="py-2">{s.name}</td>
                      <td className="py-2">{s.gpa}</td>
                      <td className="py-2">{s.attendance}%</td>
                      <td className="py-2">{s.university}</td>
                      <td className="py-2">{s.department}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
      </section>
    </div>
  )
}
