import { Menu, Search } from 'lucide-react'
import { useAuth } from '../context/AuthContext'
import ThemeToggle from './ThemeToggle'
import ProfileMenu from './ProfileMenu'
import NotificationDropdown from './NotificationDropdown'

export default function TopNav({ onMenuToggle }: { onMenuToggle: () => void }) {
  const { user } = useAuth()

  return (
    <header className="flex min-h-20 flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white/90 px-4 py-3 backdrop-blur-sm dark:border-slate-800 dark:bg-slate-950/80 sm:px-6">
      <div className="flex items-center gap-3">
        <button type="button" onClick={onMenuToggle} aria-label="Open navigation menu" className="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 lg:hidden dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
          <Menu className="h-4 w-4" />
        </button>

        <div className="relative hidden md:block">
          <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
          <input
            type="text"
            placeholder="Search students, reports..."
            className="h-10 w-72 rounded-xl border border-slate-200 bg-slate-50 pl-9 text-sm text-slate-700 outline-none ring-0 transition focus:border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
          />
        </div>
      </div>

      <div className="flex min-w-0 flex-wrap items-center justify-end gap-2 sm:gap-3">
        <button className="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-2 text-sm font-medium text-slate-700 sm:px-3 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
          <span className="hidden sm:inline">Quick add</span>
        </button>

        <NotificationDropdown />
        <ThemeToggle />
        <ProfileMenu />
      </div>
    </header>
  )
}
