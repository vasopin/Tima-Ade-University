import { Megaphone } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

export default function UniversityAdminAnnouncementsPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['university-admin']}>
        <div className="space-y-6">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">Campus Announcements</h1>
            <p className="text-sm text-slate-500">Official university broadcasts to students, faculty, and parents.</p>
          </div>
          <Card className="border-slate-200 bg-white shadow-sm p-6">
            <h2 className="text-lg font-bold text-slate-900">Active Notices</h2>
            <p className="text-sm text-slate-500 mt-1">1. Semester Registration Deadline Extended to Friday<br/>2. Campus Library Extended Hours for Midterms</p>
          </Card>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
