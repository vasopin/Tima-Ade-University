import { ShieldAlert, Lock, Terminal, CheckCircle2 } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'
import { Badge } from '../../src/components/ui/badge'

const securityEvents = [
  { event: 'Failed login spike — Lagos Campus',  severity: 'High',   time: '14 min ago',   icon: ShieldAlert },
  { event: 'Role escalation attempt blocked',      severity: 'High',   time: '1 h ago',      icon: Lock        },
  { event: 'API rate-limit exceeded — API key #4', severity: 'Medium', time: '3 h ago',      icon: Terminal    },
  { event: 'Password reset flood — Abuja node',   severity: 'Medium', time: '6 h ago',      icon: ShieldAlert },
  { event: 'Successful audit export by AdminX',   severity: 'Info',   time: '9 h ago',      icon: CheckCircle2 },
]

const severityColor: Record<string, string> = {
  High:   'text-red-600 bg-red-50 border-red-200',
  Medium: 'text-amber-700 bg-amber-50 border-amber-200',
  Info:   'text-slate-500 bg-slate-50 border-slate-200',
}

export default function SuperAdminSecurityPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['super-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Security Events</h1>
            <p className="text-sm text-slate-500">Real-time platform security alerts, authentication events, and anomaly logs.</p>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>Recent Security Incidents</CardTitle>
                <CardDescription>Automated security event log (last 24 hours)</CardDescription>
              </div>
              <Badge variant="danger">5 Active Alerts</Badge>
            </CardHeader>
            <CardContent className="p-6 pt-0">
              <div className="space-y-3">
                {securityEvents.map((evt) => {
                  const Icon = evt.icon
                  return (
                    <div key={evt.event} className={`flex items-start gap-3 rounded-2xl border p-4 text-sm ${severityColor[evt.severity]}`}>
                      <Icon className="mt-0.5 h-5 w-5 shrink-0" />
                      <div className="min-w-0 flex-1">
                        <div className="font-semibold">{evt.event}</div>
                        <div className="mt-0.5 text-xs opacity-70">{evt.time}</div>
                      </div>
                      <span className="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide border">{evt.severity}</span>
                    </div>
                  )
                })}
              </div>
            </CardContent>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
