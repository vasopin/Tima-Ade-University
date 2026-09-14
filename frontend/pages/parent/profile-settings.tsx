import { User, Mail, Phone, MapPin, Edit2 } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function ParentProfileSettingsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Profile</h1>
            <p className="text-sm text-slate-500">Manage your personal details and contact information.</p>
          </div>

          <div className="grid gap-6 lg:grid-cols-3">
            {/* Avatar card */}
            <Card className="border-slate-200 bg-white shadow-sm lg:col-span-1">
              <CardContent className="flex flex-col items-center p-8">
                <div className="flex h-24 w-24 items-center justify-center rounded-full bg-amber-100 text-3xl font-bold text-amber-700">
                  DP
                </div>
                <div className="mt-4 text-center">
                  <div className="text-lg font-bold text-slate-900">Demo Parent</div>
                  <div className="text-sm text-slate-500">Parent / Guardian</div>
                  <div className="mt-1 inline-block rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Active</div>
                </div>
                <button className="mt-6 flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                  <Edit2 className="h-3.5 w-3.5" /> Change Photo
                </button>
              </CardContent>
            </Card>

            {/* Info card */}
            <Card className="border-slate-200 bg-white shadow-sm lg:col-span-2">
              <CardHeader>
                <CardTitle>Personal Information</CardTitle>
              </CardHeader>
              <CardContent className="p-6 pt-0 space-y-4">
                {[
                  { icon: User, label: 'Full Name', value: 'Demo Parent' },
                  { icon: Mail, label: 'Email Address', value: 'parent@school.com' },
                  { icon: Phone, label: 'Phone Number', value: '+234 800 000 0000' },
                  { icon: MapPin, label: 'Home Address', value: '12 Victoria Island, Lagos, Nigeria' },
                ].map(({ icon: Icon, label, value }) => (
                  <div key={label} className="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <Icon className="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                    <div>
                      <div className="text-xs font-medium text-slate-500">{label}</div>
                      <div className="text-sm font-semibold text-slate-900">{value}</div>
                    </div>
                  </div>
                ))}
                <button className="w-full rounded-xl bg-amber-600 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">
                  Save Changes
                </button>
              </CardContent>
            </Card>
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
