import { useEffect } from 'react'
import { useRouter } from 'next/router'

export default function UniversityAdminRedirect() {
  const router = useRouter()
  useEffect(() => {
    router.replace('/university-admin/overview')
  }, [router])
  return null
}
