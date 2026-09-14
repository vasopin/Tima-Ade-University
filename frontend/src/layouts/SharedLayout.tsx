import { ReactNode, useEffect, useState } from 'react'
import { useRouter } from 'next/router'
import Sidebar from '../components/Sidebar'
import TopNav from '../components/TopNav'
import Breadcrumbs from '../components/Breadcrumbs'

const getBreadcrumbs = (pathname: string): string[] => {
  const parts = pathname.split('/').filter(Boolean)
  if (parts.length === 0) return ['Home']
  const format = (s: string) => s.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')
  return parts.map(format)
}

export default function SharedLayout({ children }: { children: ReactNode }) {
  const router = useRouter()
  const [mobileNavOpen, setMobileNavOpen] = useState(false)
  const breadcrumbItems = getBreadcrumbs(router.pathname)

  useEffect(() => {
    const handleKeyDown = (event: KeyboardEvent) => {
      if (event.key === 'Escape') setMobileNavOpen(false)
    }

    document.addEventListener('keydown', handleKeyDown)
    return () => document.removeEventListener('keydown', handleKeyDown)
  }, [])

  return (
    <div className="min-h-screen bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-50">
      <div className="flex min-h-screen">
        <Sidebar mobileOpen={mobileNavOpen} onMobileClose={() => setMobileNavOpen(false)} />

        <div className="flex min-h-screen flex-1 flex-col">
          <TopNav onMenuToggle={() => setMobileNavOpen((open) => !open)} />

          <div className="px-4 pb-8 pt-4 sm:px-6 lg:px-8">
            <div className="mb-6">
              <Breadcrumbs items={breadcrumbItems} />
            </div>
            <main>{children}</main>
          </div>
        </div>
      </div>
    </div>
  )
}
