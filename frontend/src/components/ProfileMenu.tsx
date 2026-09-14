import { useRouter } from 'next/router'
import { LogOut, UserCircle2 } from 'lucide-react'
import { useAuth, type Role } from '../context/AuthContext'

const roleLinks: Record<Role, string> = {
  'super-admin': '/super-admin',
  'university-admin': '/university-admin',
  teacher: '/teacher',
  student: '/student',
  parent: '/parent',
  guest: '/',
}

export default function ProfileMenu() {
  const { user, signOut, signIn } = useAuth()
  const router = useRouter()

  const handleSignIn = async (role: Role) => {
    await signIn(role)
    router.push(roleLinks[role])
  }

  return (
    <div className="relative">
      {!user ? (
        <div className="flex max-w-full flex-wrap justify-end gap-2">
          <button aria-label="Sign in as Super Admin" onClick={() => handleSignIn('super-admin')} className="rounded-xl bg-slate-900 px-2 py-2 text-xs font-medium text-white hover:bg-slate-800 sm:px-3 sm:text-sm dark:bg-slate-100 dark:text-slate-900">
            <span className="sm:hidden">Admin</span><span className="hidden sm:inline">Super Admin</span>
          </button>
          <button aria-label="Sign in as University Admin" onClick={() => handleSignIn('university-admin')} className="rounded-xl border border-slate-200 bg-white px-2 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 sm:px-3 sm:text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            <span className="sm:hidden">University</span><span className="hidden sm:inline">University Admin</span>
          </button>
          <button aria-label="Sign in as Teacher" onClick={() => handleSignIn('teacher')} className="rounded-xl border border-slate-200 bg-white px-2 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 sm:px-3 sm:text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            Teacher
          </button>
          <button aria-label="Sign in as Student" onClick={() => handleSignIn('student')} className="rounded-xl border border-slate-200 bg-white px-2 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 sm:px-3 sm:text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            Student
          </button>
          <button aria-label="Sign in as Parent" onClick={() => handleSignIn('parent')} className="rounded-xl border border-slate-200 bg-white px-2 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 sm:px-3 sm:text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            Parent
          </button>
        </div>
      ) : (
        <div className="flex items-center gap-3">
          <div className="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-900">
            <UserCircle2 className="h-4 w-4 text-slate-500" />
            <span className="max-w-[8rem] truncate sm:max-w-[12rem]">{user.name}</span>
          </div>
          <button onClick={signOut} className="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            <LogOut className="h-4 w-4" />
            Sign out
          </button>
        </div>
      )}
    </div>
  )
}
