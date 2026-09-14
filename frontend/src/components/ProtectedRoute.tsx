import React, { ReactNode, useEffect } from 'react'
import { useRouter } from 'next/router'
import { useAuth } from '../context/AuthContext'

export default function ProtectedRoute({ children, allowedRoles }: { children: ReactNode, allowedRoles?: string[] }){
  const { user, loading } = useAuth()
  const router = useRouter()

  useEffect(() => {
    if (loading) return
    if (!user) {
      router.replace('/')
      return
    }
    // Allow Super Admin to access any protected route
    if (allowedRoles && !allowedRoles.includes(user.role) && user.role !== 'super-admin') {
      router.replace('/')
    }
  }, [user, loading, router, allowedRoles])

  if (loading) return null
  if (!user) return null
  if (allowedRoles && !allowedRoles.includes(user.role) && user.role !== 'super-admin') return <div className="p-6">Access denied</div>
  return <>{children}</>
}
