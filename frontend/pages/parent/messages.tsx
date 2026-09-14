import { MessageCircle, Send } from 'lucide-react'
import SharedLayout from '../../src/layouts/SharedLayout'
import ProtectedRoute from '../../src/components/ProtectedRoute'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../src/components/ui/card'

const messages = [
  { from: 'Mr. James Okon', role: 'Class Teacher', subject: 'Mid-term performance update', date: 'Aug 14', preview: 'Your child has shown improvement in mathematics...' },
  { from: 'Dr. Amina Yusuf', role: 'Physics Teacher', subject: 'Lab safety reminder', date: 'Aug 12', preview: 'Please ensure your ward brings their lab coat...' },
  { from: 'Administration', role: 'School Office', subject: 'Fee payment reminder', date: 'Aug 10', preview: 'This is a reminder that second-term fees are due...' },
  { from: 'Mrs. Grace Eze', role: 'Form Master', subject: 'Parent-teacher meeting', date: 'Aug 8', preview: 'We are pleased to invite you to our upcoming parent...' },
]

export default function ParentMessagesPage() {
  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={['parent']}>
        <div className="space-y-6">
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-2xl font-bold tracking-tight text-slate-900">Messages</h1>
              <p className="text-sm text-slate-500">Communication from teachers, faculty, and school administration.</p>
            </div>
            <button className="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">
              <Send className="h-4 w-4" /> New Message
            </button>
          </div>

          <Card className="border-slate-200 bg-white shadow-sm">
            <CardHeader>
              <CardTitle>Inbox</CardTitle>
              <CardDescription>Messages from teachers and school administration</CardDescription>
            </CardHeader>
            <CardContent className="p-0">
              <div className="divide-y divide-slate-100">
                {messages.map((msg, i) => (
                  <div key={i} className="flex items-start gap-4 px-6 py-4 hover:bg-slate-50 cursor-pointer">
                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700 font-bold text-sm">
                      {msg.from.split(' ').map(w => w[0]).slice(0, 2).join('')}
                    </div>
                    <div className="min-w-0 flex-1">
                      <div className="flex items-center justify-between">
                        <div className="font-semibold text-slate-900 text-sm">{msg.from}</div>
                        <div className="text-xs text-slate-400">{msg.date}</div>
                      </div>
                      <div className="text-xs text-amber-600 font-medium">{msg.role}</div>
                      <div className="text-sm font-medium text-slate-700 mt-0.5">{msg.subject}</div>
                      <div className="text-xs text-slate-500 truncate">{msg.preview}</div>
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
