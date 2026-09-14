import { useEffect } from 'react'
import { useRouter } from 'next/router'

export default function SuperAdminRedirect() {
  const router = useRouter()
  useEffect(() => {
    router.replace('/super-admin/overview')
  }, [router])
  return null
}
