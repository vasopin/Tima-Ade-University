import { UserPlus } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Button } from '../../src/components/ui/button'

const funnel = [
  { stage: 'Applied',   count: 540,  color: 'bg-indigo-600' },
  { stage: 'Screened',  count: 381,  color: 'bg-indigo-500' },
  { stage: 'Invited',   count: 245,  color: 'bg-sky-600'    },
  { stage: 'Offered',   count: 178,  color: 'bg-teal-600'   },
  { stage: 'Enrolled',  count: 127,  color: 'bg-emerald-600'},
]

export default function UniversityAdminAdmissionsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Admissions Pipeline</h1>
            <p className="text-sm text-slate-500">Applicant tracking, screening stages, and enrollment conversions.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Current Intake Funnel</CardTitle>
              <CardDescription>Applications by stage</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-4">
                {funnel.map((f) => (
                  <div key={f.stage}>
                    <div className="flex justify-between text-sm mb-1 font-medium text-slate-700">
                      <span>{f.stage}</span>
                      <span>{f.count} applicants</span>
                    </div>
                    <div className="h-3 rounded-full bg-slate-100 overflow-hidden">
                      <div className={`h-full ${f.color}`} style={{ width: `${(f.count / 540) * 100}%` }} />
                    </div>
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
