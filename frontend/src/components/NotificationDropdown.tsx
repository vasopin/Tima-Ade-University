import { Bell } from 'lucide-react'
import { useState } from 'react'
import Link from 'next/link'
import { useAuth } from '../context/AuthContext'
import { useNotifications } from '../context/NotificationsContext'

export default function NotificationDropdown() {
  const { user } = useAuth()
  const { getForRole, unreadCount, markRead, markAllRead } = useNotifications()
  const [open, setOpen] = useState(false)

  const role = (user?.role as any) ?? 'guest'
  const items = getForRole(role)

  return (
    <div className="relative">
      <button
        type="button"
        onClick={() => setOpen((value) => !value)}
        className="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
        aria-label="Notifications"
      >
        <Bell className="h-4 w-4" />
        {unreadCount > 0 && (
          <span className="absolute -right-1 -top-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-crimson-600 px-1 text-[10px] font-semibold text-white">
            {unreadCount}
          </span>
        )}
      </button>

      {open && (
        <div className="absolute right-0 z-20 mt-2 w-[calc(100vw-2rem)] max-w-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
          <div className="flex items-center justify-between border-b border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:text-slate-200">
            <span>Notifications</span>
            <div className="flex items-center gap-2">
              <button onClick={() => markAllRead()} className="text-xs text-slate-500 hover:underline">Mark all read</button>
              <Link href="/notifications" className="text-xs text-crimson-600 hover:underline">View center</Link>
            </div>
          </div>

          <ul>
            {items.length === 0 && (
              <li className="px-4 py-4 text-sm text-slate-500">No notifications</li>
            )}

            {items.slice(0, 6).map((item) => (
              <li key={item.id} className={`flex items-start gap-3 border-b border-slate-100 px-4 py-3 text-sm text-slate-600 last:border-b-0 dark:border-slate-800 dark:text-slate-300 ${item.read ? '' : 'bg-slate-50'}`}>
                <div className="flex-1">
                  <div className="flex items-center justify-between">
                    <div className="font-medium text-slate-800">{item.title}</div>
                    <div className="text-[11px] text-slate-400">{new Date(item.createdAt).toLocaleString()}</div>
                  </div>
                  {item.body && <div className="mt-1 text-xs text-slate-600">{item.body}</div>}
                  <div className="mt-2 flex items-center gap-2">
                    {!item.read && (
                      <button className="text-xs text-crimson-600 hover:underline" onClick={() => markRead(item.id)}>Mark read</button>
                    )}
                    <Link href={`/reports?from=notification&n=${item.id}`} className="text-xs text-slate-500 hover:underline">Open</Link>
                  </div>
                </div>
              </li>
            ))}

            {items.length > 6 && (
              <li className="px-4 py-3 text-center text-sm text-slate-500">More in the notification center</li>
            )}
          </ul>
        </div>
      )}
    </div>
  )
}
