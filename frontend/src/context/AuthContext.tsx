import React, { createContext, useContext, useState, ReactNode, useEffect } from 'react'

export type Role = 'super-admin' | 'university-admin' | 'teacher' | 'student' | 'parent' | 'guest'

export type User = {
  id: string
  name: string
  email: string
  role: Role
  phone?: string
  avatar?: string
}

type AuthContextType = {
  user: User | null
  loading: boolean
  signIn: (role: Role) => Promise<void>
  loginWithCredentials: (email: string, password: string) => Promise<User>
  signOut: () => Promise<void>
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

const API_BASE = process.env.NEXT_PUBLIC_API_URL || ''
const api = (path: string) => (API_BASE ? `${API_BASE}${path}` : path)

export const AuthProvider = ({ children }: { children: ReactNode }) => {
  const [user, setUser] = useState<User | null>(null)
  const [loading, setLoading] = useState<boolean>(true)

  const loginWithCredentials = async (email: string, password: string) => {
      // Acquire CSRF cookie for Laravel/Sanctum
      await fetch(api('/sanctum/csrf-cookie'), {
        method: 'GET',
        credentials: 'include',
      }).catch(() => {})

      // Read XSRF token cookie and include it in the header (fetch doesn't do this automatically)
      const getCookie = (name: string) => {
        if (typeof document === 'undefined') return ''
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
        return match ? decodeURIComponent(match[2]) : ''
      }

      const xsrf = getCookie('XSRF-TOKEN')

      const body = new URLSearchParams({ email, password })
      const loginRes = await fetch(api('/login'), {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json',
          ...(xsrf ? { 'X-XSRF-TOKEN': xsrf } : {}),
        },
        body: body.toString(),
      })

      if (!loginRes.ok) {
        let message = 'The provided credentials were rejected.'
        try {
          const data = await loginRes.json()
          message = data.message || data.errors?.email?.[0] || message
        } catch (_) {}
        throw new Error(message)
      }

      // Fetch user profile from API
      const userRes = await fetch(api('/api/user'), {
        method: 'GET',
        credentials: 'include',
        headers: { Accept: 'application/json' },
      })

      if (userRes.ok) {
        const data = await userRes.json()
        const authenticatedUser = {
          id: String(data.id),
          name: data.name,
          email: data.email,
          role: data.role as Role,
          phone: data.phone,
          avatar: data.avatar,
        }
        setUser(authenticatedUser)
        if (typeof window !== 'undefined') {
          localStorage.setItem('moon_user', JSON.stringify(authenticatedUser))
        }
        return authenticatedUser
      }

      throw new Error('The authenticated user profile could not be loaded.')
  }

  const signIn = async (_role: Role) => {
    throw new Error('Select a role and sign in with your assigned university credentials.')
  }

  const signOut = async () => {
    try {
      // Include XSRF token when available
      const getCookie = (name: string) => {
        if (typeof document === 'undefined') return ''
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
        return match ? decodeURIComponent(match[2]) : ''
      }
      const xsrf = getCookie('XSRF-TOKEN')
      await fetch(api('/logout'), {
        method: 'POST',
        credentials: 'include',
        headers: xsrf ? { 'X-XSRF-TOKEN': xsrf } : {},
      }).catch(() => {})
    } finally {
      if (typeof window !== 'undefined') {
        localStorage.removeItem('moon_user')
      }
      setUser(null)
    }
  }

  useEffect(() => {
    // Try to restore user session on mount
    ;(async () => {
      try {
        const res = await fetch(api('/api/user'), {
          credentials: 'include',
          headers: { Accept: 'application/json' },
        })
        if (res.ok) {
          const data = await res.json()
          const u: User = {
            id: String(data.id),
            name: data.name,
            email: data.email,
            role: data.role as Role,
            phone: data.phone,
            avatar: data.avatar,
          }
          setUser(u)
          if (typeof window !== 'undefined') {
            localStorage.setItem('moon_user', JSON.stringify(u))
          }
          setLoading(false)
          return
        }
      } catch (_) {
        if (typeof window !== 'undefined') {
          localStorage.removeItem('moon_user')
        }
      }

      setLoading(false)
    })()
  }, [])

  return (
    <AuthContext.Provider value={{ user, loading, signIn, loginWithCredentials, signOut }}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used within AuthProvider')
  return ctx
}
