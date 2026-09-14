import { GraduationCap, Plus } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'

const programs = [
  { code: 'BSCS', title: 'BSc Computer Science', duration: '4 Years', enrolled: 840 },
  { code: 'BSEE', title: 'BSc Electrical Engineering', duration: '4 Years', enrolled: 620 },
  { code: 'MBA',  title: 'Master of Business Administration', duration: '2 Years', enrolled: 310 },
]

export default function UniversityAdminProgramsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Academic Programs</h1>
              <p className="text-sm text-slate-500">Degree programs and curriculum specifications.</p>
            </div>
            <Button size="sm" className="bg-indigo-600 text-white hover:bg-indigo-700"><Plus className="mr-2 h-4 w-4" /> New program</Button>
          </div>

          <div className="grid gap-4 sm:grid-cols-3">
            {programs.map((p) => (
              <Card key={p.code} className="border-slate-200 bg-white shadow-sm">
                <CardHeader>
                  <CardTitle className="text-base">{p.title}</CardTitle>
                  <CardDescription>{p.code} · {p.duration}</CardDescription>
                </CardHeader>
                <CardContent className="text-xs text-slate-600">
                  <div>Enrolled Students: <span className="font-semibold text-slate-900">{p.enrolled}</span></div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
