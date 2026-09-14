import { CreditCard } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { parentChildren } from '../../src/data/parent'
import { Badge } from '../../src/components/ui/badge'
import { Button } from '../../src/components/ui/button'

export default function ParentFeesPage() {
  const child = parentChildren[0]
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Fees &amp; Payment Portal</h1>
              <p className="text-sm text-slate-500">Child tuition invoices and online payment status for {child.name}.</p>
            </div>
            <Button size="sm" className="bg-amber-600 text-white hover:bg-amber-700"><CreditCard className="mr-2 h-4 w-4" /> Pay now</Button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Invoices &amp; Balances</CardTitle>
              <CardDescription>Tuition breakdown</CardDescription>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {child.fees.map((f, idx) => (
                  <div key={idx} className="flex items-center justify-between rounded-xl border border-slate-200 p-4">
                    <div>
                      <div className="font-bold text-slate-900">{f.item}</div>
                      <div className="text-xs text-slate-500">Due: {f.due} · Paid: {f.paid}</div>
                    </div>
                    <div className="flex items-center gap-3">
                      <div className="text-xs font-bold text-slate-900">Balance: {f.balance}</div>
                      <Badge variant={f.status === 'Paid' ? 'success' : 'warning'}>{f.status}</Badge>
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
