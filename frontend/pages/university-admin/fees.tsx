import { CreditCard } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

const feeCategories = [
  { category: 'Tuition Fees', collected: '$4.2M', target: '$4.8M', pct: 87.5 },
  { category: 'Hostel Accommodation', collected: '$1.1M', target: '$1.2M', pct: 91.6 },
  { category: 'Library & Labs', collected: '$320K', target: '$350K', pct: 91.4 },
]

export default function UniversityAdminFeesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Student Fees</h1>
            <p className="text-sm text-slate-500">Tuition fee collection status, outstanding balances, and payment receipts.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Fee Collection Breakdown</CardTitle>
              <CardDescription>Collection progress for current academic year</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0 space-y-4">
              {feeCategories.map((f) => (
                <div key={f.category} className="rounded-xl border border-slate-200 p-4">
                  <div className="flex justify-between text-sm font-semibold text-slate-900">
                    <span>{f.category}</span>
                    <span>{f.collected} / {f.target}</span>
                  </div>
                  <div className="mt-2 h-2.5 rounded-full bg-slate-100 overflow-hidden">
                    <div className="h-full bg-indigo-600" style={{ width: `${f.pct}%` }} />
                  </div>
                </div>
              ))}
            </CardContent>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
