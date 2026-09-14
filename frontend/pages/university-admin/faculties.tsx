import { Building2, Plus } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'

const faculties = [
  { name: 'Faculty of Science & Technology', dean: 'Dr. Arthur Pendelton', depts: 6, students: 3200 },
  { name: 'Faculty of Engineering', dean: 'Prof. Marcus Vance', depts: 4, students: 2400 },
  { name: 'Faculty of Business Administration', dean: 'Dr. Sarah Jenkins', depts: 5, students: 2640 },
]

export default function UniversityAdminFacultiesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Faculties</h1>
              <p className="text-sm text-slate-500">University faculties and dean appointments.</p>
            </div>
            <Button size="sm" className="bg-indigo-600 text-white hover:bg-indigo-700"><Plus className="mr-2 h-4 w-4" /> Add faculty</Button>
          </div>

          <div className="grid gap-4 sm:grid-cols-3">
            {faculties.map((f) => (
              <Card key={f.name} className="border-slate-200 bg-white shadow-sm">
                <CardHeader>
                  <CardTitle className="text-base">{f.name}</CardTitle>
                  <CardDescription>Dean: {f.dean}</CardDescription>
                </CardHeader>
                <CardContent className="text-xs text-slate-600 space-y-1">
                  <div>Departments: <span className="font-semibold text-slate-900">{f.depts}</span></div>
                  <div>Students: <span className="font-semibold text-slate-900">{f.students.toLocaleString()}</span></div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
