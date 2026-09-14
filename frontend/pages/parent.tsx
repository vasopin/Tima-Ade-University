import { useEffect } from 'react'
import { useRouter } from 'next/router'

export default function ParentRedirect() {
  const router = useRouter()
  useEffect(() => {
    router.replace('/parent/overview')
  }, [router])
  return null
}
