import { useEffect } from 'react'
import { useRouter } from 'next/router'

export default function StudentRedirect() {
  const router = useRouter()
  useEffect(() => {
    router.replace('/student/overview')
  }, [router])
  return null
}
