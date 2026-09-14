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
        <div className="flex items-center gap-2">
          <button onClick={() => handleSignIn('super-admin')} className="rounded-xl bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900">
            Super Admin
          </button>
          <button onClick={() => handleSignIn('university-admin')} className="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            University Admin
          </button>
          <button onClick={() => handleSignIn('teacher')} className="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            Teacher
          </button>
          <button onClick={() => handleSignIn('student')} className="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            Student
          </button>
          <button onClick={() => handleSignIn('parent')} className="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            Parent
          </button>
        </div>
      ) : (
        <div className="flex items-center gap-3">
          <div className="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-900">
            <UserCircle2 className="h-4 w-4 text-slate-500" />
            <span>{user.name}</span>
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
