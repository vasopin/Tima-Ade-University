import SharedLayout from '../src/layouts/SharedLayout'
import ProtectedRoute from '../src/components/ProtectedRoute'
import { useNotifications } from '../src/context/NotificationsContext'
import { useAuth } from '../src/context/AuthContext'
import { useMemo } from 'react'

export default function NotificationsPage() {
  const { user } = useAuth()
  const { getForRole, markRead, markAllRead, notifications } = useNotifications()
  const role = (user?.role as any) ?? 'guest'
  const items = getForRole(role)

  const unread = useMemo(() => items.filter((i) => !i.read).length, [items])

  const exportCsv = () => {
    const rows = items.map((it) => ({ id: it.id, title: it.title, body: it.body || '', role: it.role, read: it.read ? 'read' : 'unread', createdAt: it.createdAt }))
    const csv = [Object.keys(rows[0] || {}).join(','), ...rows.map((r) => Object.values(r).map((v) => `"${String(v).replace(/"/g, '""')}"`).join(','))].join('\n')
    const blob = new Blob([csv], { type: 'text/csv' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `notifications_${role}.csv`
    a.click()
    URL.revokeObjectURL(url)
  }

  return (
    <SharedLayout>
      <ProtectedRoute allowedRoles={["super-admin", "university-admin", "teacher", "student", "parent"]}>
        <div className="space-y-6 p-6">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold">Notification Center</h1>
              <p className="text-sm text-slate-500">Role: {user?.role}</p>
            </div>
            <div className="flex items-center gap-3">
              <button onClick={() => markAllRead()} className="rounded-lg border px-3 py-2 text-sm">Mark all read</button>
              <button onClick={exportCsv} className="rounded-lg bg-crimson-600 px-3 py-2 text-sm text-white">Export CSV</button>
            </div>
          </div>

          <div className="space-y-3">
            {items.length === 0 && <div className="text-sm text-slate-500">No notifications for your role.</div>}

            {items.map((n) => (
              <div key={n.id} className={`rounded-lg border p-4 ${n.read ? 'bg-white' : 'bg-slate-50'}`}>
                <div className="flex items-start justify-between gap-3">
                  <div>
                    <div className="font-semibold">{n.title}</div>
                    {n.body && <div className="mt-1 text-sm text-slate-600">{n.body}</div>}
                    <div className="mt-2 text-xs text-slate-400">{new Date(n.createdAt).toLocaleString()}</div>
                  </div>
                  <div className="flex flex-col items-end gap-2">
                    {!n.read && <button className="text-sm text-crimson-600" onClick={() => markRead(n.id)}>Mark read</button>}
                    <div className="text-xs text-slate-400">Role: {n.role}</div>
                  </div>
                </div>
              </div>
            ))}
          </div>

          <div className="text-sm text-slate-500">Total: {items.length} • Unread: {unread}</div>
        </div>
      </ProtectedRoute>
    </SharedLayout>
  )
}
