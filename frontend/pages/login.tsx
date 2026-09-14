import { useState } from 'react'
import Link from 'next/link'
import { useRouter } from 'next/router'
import {
  GraduationCap,
  Lock,
  Mail,
  ShieldCheck,
  Building2,
  School,
  Users,
  ArrowRight,
  CheckCircle2,
  AlertCircle,
} from 'lucide-react'
import PublicLayout from '../src/layouts/PublicLayout'
import { useAuth, Role } from '../src/context/AuthContext'
import UniversityLogo from '../src/components/UniversityLogo'

export default function LoginPage() {
  const router = useRouter()
  const { loginWithCredentials, user } = useAuth()

  const [selectedRole, setSelectedRole] = useState<Role>('super-admin')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)
  const [errorMsg, setErrorMsg] = useState<string | null>(null)

  const roleCredentials: Record<Role, { email: string; name: string; title: string; icon: any; color: string; desc: string }> = {
    'super-admin': {
      email: '',
      name: 'Super Admin',
      title: 'Platform Control Center',
      icon: ShieldCheck,
      color: 'bg-rose-50 text-rose-700 border-rose-200 ring-rose-500',
      desc: 'Full platform governance, university management, audit logs, and security controls.',
    },
    'university-admin': {
      email: '',
      name: 'University Admin',
      title: 'University Operations Center',
      icon: Building2,
      color: 'bg-indigo-50 text-indigo-700 border-indigo-200 ring-indigo-500',
      desc: 'Campus admissions, classroom rosters, teacher allocation, exams, and fee collections.',
    },
    teacher: {
      email: '',
      name: 'Teacher',
      title: 'Daily Teaching Workspace',
      icon: School,
      color: 'bg-teal-50 text-teal-700 border-teal-200 ring-teal-500',
      desc: 'Class schedule, live attendance marking, assignment grading, and student progress.',
    },
    student: {
      email: '',
      name: 'Student',
      title: 'Personal Academic Home',
      icon: GraduationCap,
      color: 'bg-violet-50 text-violet-700 border-violet-200 ring-violet-500',
      desc: 'Courses, homework submissions, exam schedule, GPA results, and learning materials.',
    },
    parent: {
      email: '',
      name: 'Parent / Guardian',
      title: 'Child Monitoring Center',
      icon: Users,
      color: 'bg-amber-50 text-amber-700 border-amber-200 ring-amber-500',
      desc: 'Child monitoring center.',
    },
    guest: {
      email: '',
      name: 'Guest',
      title: 'Guest Access',
      icon: Users,
      color: 'bg-gray-50 text-gray-700 border-gray-200 ring-gray-500',
      desc: 'Public preview mode.',
    },
  }

  const handleRoleSelect = (r: Role) => {
    setSelectedRole(r)
    setEmail('')
    setPassword('')
    setErrorMsg(null)
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    setErrorMsg(null)

    try {
      const authenticatedUser = await loginWithCredentials(email, password)
      router.push(`/${authenticatedUser.role}/overview`)
    } catch (e: any) {
      setErrorMsg(e.message || 'Login failed')
    } finally {
      setLoading(false)
    }
  }

  const currentRoleInfo = roleCredentials[selectedRole]
  const CurrentIcon = currentRoleInfo.icon

  return (
    <PublicLayout>
      <div className="min-h-[calc(100vh-200px)] py-16 bg-slate-900 flex items-center justify-center px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div className="absolute top-1/4 left-1/3 w-[500px] h-[500px] bg-rose-600/15 rounded-full blur-[140px] pointer-events-none" />
        <div className="absolute bottom-1/4 right-1/3 w-[500px] h-[500px] bg-indigo-600/15 rounded-full blur-[140px] pointer-events-none" />

        <div className="max-w-4xl w-full grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch relative">
          {/* Left Column — Role selector and guide */}
          <div className="lg:col-span-5 rounded-3xl bg-slate-950 border border-white/10 p-7 flex flex-col justify-between text-white space-y-6">
            <div>
              <div className="mb-4">
                <UniversityLogo
                  size="sm"
                  showText
                  href="/"
                  className="text-white"
                  imageClassName="h-10 w-10 shrink-0 object-contain"
                />
              </div>
              <h1 className="text-2xl font-bold">Select Your Role Portal</h1>
              <p className="text-slate-400 text-sm">
                Choose your role to access your dashboard.
              </p>

              {/* Role selector buttons */}
              <div className="space-y-2 mt-5">
                {(['super-admin', 'university-admin', 'teacher', 'student', 'parent'] as Role[]).map((r) => {
                  const info = roleCredentials[r]
                  const Icon = info.icon
                  const isSelected = selectedRole === r

                  return (
                    <button
                      key={r}
                      type="button"
                      onClick={() => handleRoleSelect(r)}
                      className={[
                        'w-full flex items-center justify-between p-3 rounded-2xl border text-left transition-all cursor-pointer',
                        isSelected
                          ? 'bg-white/10 border-rose-500 text-white shadow-sm ring-1 ring-rose-500/50'
                          : 'bg-white/5 border-white/5 text-slate-400 hover:text-white hover:bg-white/10',
                      ].join(' ')}
                    >
                      <div className="flex items-center gap-3 min-w-0">
                        <div className={`flex h-8 w-8 shrink-0 items-center justify-center rounded-xl ${isSelected ? 'bg-rose-600 text-white' : 'bg-white/10 text-slate-300'}`}>
                          <Icon className="h-4 w-4" />
                        </div>
                        <div className="min-w-0">
                          <div className="text-xs font-bold text-white truncate">{info.name}</div>
                          <div className="text-[10px] text-slate-400 truncate">{info.title}</div>
                        </div>
                      </div>
                      <span className="text-[10px] font-bold text-rose-400 shrink-0 ml-2">Select</span>
                    </button>
                  )
                })}
              </div>
            </div>

          </div>

          {/* Right Column — Credential Login Card */}
          <div className="lg:col-span-7 rounded-3xl bg-white p-8 sm:p-10 shadow-2xl border border-slate-200 flex flex-col justify-between">
            <form onSubmit={handleSubmit} className="space-y-5">
              <div>
                <div className="inline-block rounded-lg bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-0.5 text-xs font-bold mb-2">
                  {currentRoleInfo.name} Portal
                </div>
                <h3 className="text-2xl font-black font-serif text-slate-900">
                  Sign In with Credentials
                </h3>
                <p className="text-xs text-slate-500 mt-1">
                  {currentRoleInfo.desc}
                </p>
              </div>

              {errorMsg && (
                <div className="rounded-xl bg-red-50 border border-red-200 p-3 text-xs text-red-700 flex items-center gap-2">
                  <AlertCircle className="h-4 w-4 shrink-0" />
                  <span>{errorMsg}</span>
                </div>
              )}

              <div className="space-y-4">
                <div>
                  <label className="block text-xs font-bold text-slate-700 mb-1">
                    Email Address
                  </label>
                  <div className="relative">
                    <Mail className="absolute left-3.5 top-3 h-4 w-4 text-slate-400" />
                    <input
                      type="email"
                      required
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder="name@school.com"
                      className="w-full h-10 pl-10 pr-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50 font-medium"
                    />
                  </div>
                </div>

                <div>
                  <div className="flex justify-between items-center mb-1">
                    <label className="block text-xs font-bold text-slate-700">
                      Password
                    </label>
                    <span className="text-[11px] text-rose-700 font-semibold cursor-pointer" onClick={() => setPassword('password')}>
                      Default: password
                    </span>
                  </div>
                  <div className="relative">
                    <Lock className="absolute left-3.5 top-3 h-4 w-4 text-slate-400" />
                    <input
                      type="password"
                      required
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      placeholder="••••••••"
                      className="w-full h-10 pl-10 pr-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50 font-medium"
                    />
                  </div>
                </div>
              </div>

              <div className="pt-2">
                <button
                  type="submit"
                  disabled={loading}
                  className="w-full py-3.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-bold text-sm shadow-md shadow-rose-900/30 transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                  {loading ? (
                    <span>Authenticating...</span>
                  ) : (
                    <>
                      <span>Enter {currentRoleInfo.name} Portal</span>
                      <ArrowRight className="h-4 w-4" />
                    </>
                  )}
                </button>
              </div>
            </form>

            <div className="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 mt-6">
              <span>New applicant or parent?</span>
              <Link href="/admissions" className="font-bold text-rose-700 hover:underline">
                Apply for Admission →
              </Link>
            </div>
          </div>
        </div>
      </div>
    </PublicLayout>
  )
}
