import React, { createContext, useContext, useEffect, useState, ReactNode } from 'react'

export type Role = 'super-admin' | 'university-admin' | 'teacher' | 'student' | 'parent' | 'guest'

export type NotificationItem = {
  id: string
  title: string
  body?: string
  role: Role | 'all'
  read: boolean
  createdAt: string
  meta?: Record<string, any>
}

type NotificationsContextType = {
  notifications: NotificationItem[]
  unreadCount: number
  markRead: (id: string) => Promise<void>
  markAllRead: () => Promise<void>
  pushNotification: (n: Omit<NotificationItem, 'id' | 'read' | 'createdAt'>) => Promise<void>
  getForRole: (role: Role) => NotificationItem[]
}

const NotificationsContext = createContext<NotificationsContextType | undefined>(undefined)

const sampleNotifications: NotificationItem[] = [
  { id: 'n1', title: 'Assignment due soon', body: 'Assignment for CS201 due in 2 days', role: 'student', read: false, createdAt: new Date().toISOString(), meta: { course: 'CS201' } },
  { id: 'n2', title: 'New submission', body: 'A student submitted Assignment 3', role: 'teacher', read: false, createdAt: new Date().toISOString(), meta: { course: 'CS201' } },
  { id: 'n3', title: 'Fee payment received', body: 'Payment received from parent for ST-2041', role: 'university-admin', read: false, createdAt: new Date().toISOString() },
  { id: 'n4', title: 'Exam timetable published', body: 'Final exam timetable is live', role: 'all', read: false, createdAt: new Date().toISOString() },
]

const API_BASE = process.env.NEXT_PUBLIC_API_URL || ''
const api = (path: string) => (API_BASE ? `${API_BASE}${path}` : path)

export const NotificationsProvider = ({ children }: { children: ReactNode }) => {
  const [notifications, setNotifications] = useState<NotificationItem[]>([])

  useEffect(() => {
    // Try fetching from backend; fall back to sample data
    ;(async () => {
      try {
        const res = await fetch(api('/api/notifications'), { credentials: 'include' })
        if (res.ok) {
          const data = await res.json()
          // Map backend fields to frontend NotificationItem shape
          const mapped = data.map((n: any) => ({
            id: String(n.id),
            title: n.title,
            body: n.body || '',
            role: (n.role as Role) || 'all',
            read: !!n.read,
            createdAt: n.created_at || n.createdAt || new Date().toISOString(),
            meta: n.meta || {},
          }))
          setNotifications(mapped)
          return
        }
      } catch (e) {
        // ignore — fall back
      }
      setNotifications(sampleNotifications)
    })()
  }, [])

  const markRead = async (id: string) => {
    try {
      await fetch(api(`/api/notifications/${id}/read`), { method: 'POST', credentials: 'include' })
    } catch (e) {
      // ignore
    }
    setNotifications((prev) => prev.map((n) => (n.id === id ? { ...n, read: true } : n)))
  }

  const markAllRead = async () => {
    try {
      await fetch(api('/api/notifications/read-all'), { method: 'POST', credentials: 'include' })
    } catch (e) {
      // ignore
    }
    setNotifications((prev) => prev.map((n) => ({ ...n, read: true })))
  }

  const pushNotification = async (n: Omit<NotificationItem, 'id' | 'read' | 'createdAt'>) => {
    try {
      const res = await fetch(api('/api/notifications'), {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(n),
      })
      if (res.ok) {
        const created = await res.json()
        const item: NotificationItem = {
          id: String(created.id),
          title: created.title,
          body: created.body || '',
          role: (created.role as Role) || 'all',
          read: !!created.read,
          createdAt: created.created_at || new Date().toISOString(),
          meta: created.meta || {},
        }
        setNotifications((prev) => [item, ...prev])
        return
      }
    } catch (e) {
      // ignore
    }

    const newItem: NotificationItem = {
      id: 'n' + Math.random().toString(36).slice(2, 9),
      read: false,
      createdAt: new Date().toISOString(),
      ...n,
    }
    setNotifications((prev) => [newItem, ...prev])
  }

  const getForRole = (role: Role) => {
    return notifications.filter((n) => n.role === 'all' || n.role === role)
  }

  const unreadCount = notifications.filter((n) => !n.read).length

  return (
    <NotificationsContext.Provider value={{ notifications, unreadCount, markRead, markAllRead, pushNotification, getForRole }}>
      {children}
    </NotificationsContext.Provider>
  )
}

export const useNotifications = () => {
  const ctx = useContext(NotificationsContext)
  if (!ctx) throw new Error('useNotifications must be used within NotificationsProvider')
  return ctx
}
