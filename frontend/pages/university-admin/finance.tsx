import { Wallet } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function UniversityAdminFinancePage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">University Finance</h1>
            <p className="text-sm text-slate-500">Campus budget allocation, revenue Streams, and operational expenses.</p>
          </div>

          <div className="grid gap-4 sm:grid-cols-3">
            <Card className="border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs text-slate-500 font-semibold uppercase">Total Revenue</div>
              <div className="text-2xl font-bold text-slate-900 mt-2">$5.62M</div>
              <div className="text-xs text-emerald-600 font-semibold mt-1">+8.2% YOY</div>
            </Card>
            <Card className="border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs text-slate-500 font-semibold uppercase">Operational Costs</div>
              <div className="text-2xl font-bold text-slate-900 mt-2">$3.84M</div>
              <div className="text-xs text-slate-500 mt-1">On budget</div>
            </Card>
            <Card className="border-slate-200 bg-white p-5 shadow-sm">
              <div className="text-xs text-slate-500 font-semibold uppercase">Net Operating Surplus</div>
              <div className="text-2xl font-bold text-slate-900 mt-2">$1.78M</div>
              <div className="text-xs text-emerald-600 font-semibold mt-1">Healthy reserve</div>
            </Card>
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
