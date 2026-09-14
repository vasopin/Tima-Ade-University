import { useEffect } from 'react'
import { useRouter } from 'next/router'

export default function TeacherRedirect() {
  const router = useRouter()
  useEffect(() => {
    router.replace('/teacher/overview')
  }, [router])
  return null
}
