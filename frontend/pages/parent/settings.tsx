import { Bell, Lock, Moon, Globe, ShieldCheck } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function ParentSettingsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Settings</h1>
            <p className="text-sm text-slate-500">Configure your notification preferences, security, and account settings.</p>
          </div>

          <div className="space-y-4">
            {/* Notifications */}
            <Card className="border-slate-200 bg-white shadow-sm">
              <CardHeader>
                <CardTitle className="flex items-center gap-2 text-base"><Bell className="h-4 w-4 text-amber-600" /> Notification Preferences</CardTitle>
              </CardHeader>
              <CardContent className="p-6 pt-0 space-y-3">
                {['Email notifications for attendance alerts', 'SMS for exam results', 'In-app notifications for fee reminders', 'Push alerts for parent-teacher meetings'].map((pref) => (
                  <label key={pref} className="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 cursor-pointer">
                    <span className="text-sm font-medium text-slate-700">{pref}</span>
                    <div className="h-5 w-9 rounded-full bg-amber-500 relative cursor-pointer">
                      <div className="absolute right-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow" />
                    </div>
                  </label>
                ))}
              </CardContent>
            </Card>

            {/* Security */}
            <Card className="border-slate-200 bg-white shadow-sm">
              <CardHeader>
                <CardTitle className="flex items-center gap-2 text-base"><ShieldCheck className="h-4 w-4 text-emerald-600" /> Security</CardTitle>
              </CardHeader>
              <CardContent className="p-6 pt-0 space-y-3">
                <button className="w-full flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                  <Lock className="h-4 w-4 text-slate-400" /> Change Password
                </button>
                <button className="w-full flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                  <ShieldCheck className="h-4 w-4 text-slate-400" /> Enable Two-Factor Authentication
                </button>
              </CardContent>
            </Card>

            {/* Preferences */}
            <Card className="border-slate-200 bg-white shadow-sm">
              <CardHeader>
                <CardTitle className="flex items-center gap-2 text-base"><Globe className="h-4 w-4 text-blue-600" /> Preferences</CardTitle>
              </CardHeader>
              <CardContent className="p-6 pt-0 space-y-3">
                <div className="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                  <div className="flex items-center gap-3">
                    <Moon className="h-4 w-4 text-slate-400" />
                    <span className="text-sm font-medium text-slate-700">Dark Mode</span>
                  </div>
                  <div className="h-5 w-9 rounded-full bg-slate-200 relative cursor-pointer">
                    <div className="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow" />
                  </div>
                </div>
                <div className="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                  <div className="flex items-center gap-3">
                    <Globe className="h-4 w-4 text-slate-400" />
                    <span className="text-sm font-medium text-slate-700">Language</span>
                  </div>
                  <span className="text-sm text-slate-600 font-semibold">English</span>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
