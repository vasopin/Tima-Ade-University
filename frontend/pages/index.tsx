import { useEffect, useMemo, useRef, useState } from 'react'
import Link from 'next/link'
import { useRouter } from 'next/router'
import {
  GraduationCap,
  Sparkles,
  ArrowRight,
  ShieldCheck,
  Building2,
  School,
  User,
  Users,
  CheckCircle2,
  BookOpen,
  Calendar,
  Award,
  Lock,
  ChevronRight,
  TrendingUp,
  Brain,
  Atom,
  Compass,
  MessageSquare,
  FileText,
  Star,
  Zap,
} from 'lucide-react'
import PublicLayout from '../src/layouts/PublicLayout'
import { useAuth, Role } from '../src/context/AuthContext'
import {
  universityStats,
  academicPrograms,
  universityNotices,
  universityTestimonials,
  campusPillars,
} from '../src/data/public-site'

// Small reveal component using IntersectionObserver for subtle scroll reveal animations.
function Reveal({ children, className = '' }: { children: React.ReactNode; className?: string }) {
  const ref = useRef<HTMLDivElement | null>(null)
  useEffect(() => {
    const el = ref.current
    if (!el) return
    const prefersReduced = typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches
    if (prefersReduced) {
      el.classList.add('opacity-100', 'translate-y-0')
      return
    }
    el.classList.add('opacity-0', 'translate-y-4')
    const obs = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          el.classList.remove('opacity-0', 'translate-y-4')
          el.classList.add('opacity-100', 'translate-y-0', 'transition', 'duration-700', 'ease-out')
          obs.unobserve(el)
        }
      })
    }, { threshold: 0.15 })
    obs.observe(el)
    return () => obs.disconnect()
  }, [])
  return (
    <div ref={ref} className={className}>
      {children}
    </div>
  )
}

// Simple animated number hook (respects prefers-reduced-motion)
function useAnimatedNumber(targetStr: string) {
  const [value, setValue] = useState<string>(targetStr)
  const ref = useRef<number | null>(null)
  useEffect(() => {
    const prefersReduced = typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches
    if (prefersReduced) return setValue(targetStr)
    const match = targetStr.match(/([0-9,]+)/)
    if (!match) return setValue(targetStr)
    const numeric = parseInt(match[1].replace(/,/g, ''), 10)
    if (Number.isNaN(numeric)) return setValue(targetStr)
    const duration = 900
    const start = performance.now()
    const matchStr = match[1]
    function step(ts: number) {
      const pct = Math.min(1, (ts - start) / duration)
      const current = Math.floor(pct * numeric)
      setValue(targetStr.replace(matchStr, current.toLocaleString()))
      if (pct < 1) ref.current = requestAnimationFrame(step)
    }
    ref.current = requestAnimationFrame(step)
    return () => {
      if (ref.current) cancelAnimationFrame(ref.current)
    }
  }, [targetStr])
  return value
}

// Animated Stat Component
function AnimatedStat({ value, label, subtext }: { value: string; label: string; subtext: string }) {
  const animated = useAnimatedNumber(value)
  return (
    <div className="space-y-1">
      <div className="text-3xl sm:text-4xl font-black font-serif text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-amber-200">
        {animated}
      </div>
      <div className="text-sm font-bold text-white">{label}</div>
      <div className="text-xs text-slate-400">{subtext}</div>
    </div>
  )
}

export default function Home() {
  const router = useRouter()
  const { signIn, user } = useAuth()
  const [activeRoleTab, setActiveRoleTab] = useState<Role>('super-admin')
  const [loggingInRole, setLoggingInRole] = useState<string | null>(null)

  const handleQuickLaunch = async (role: Role) => {
    setLoggingInRole(role)
    try {
      await signIn(role)
      router.push(`/${role}/overview`)
    } catch (e) {
      router.push(`/${role}/overview`)
    } finally {
      setLoggingInRole(null)
    }
  }

  const rolePortals: Array<{
    role: Role
    title: string
    subtitle: string
    description: string
    badge: string
    badgeColor: string
    accentColor: string
    icon: any
    features: string[]
  }> = [
    {
      role: 'super-admin',
      title: 'Super Admin Control Center',
      subtitle: 'Platform Governance & Multi-Campus Analytics',
      description: 'Platform health monitoring, university onboarding, security audits, global billing, and role permission control.',
      badge: 'Super Admin',
      badgeColor: 'bg-rose-50 text-rose-700 border-rose-200',
      accentColor: 'from-rose-600 to-rose-800',
      icon: ShieldCheck,
      features: ['System Health Telemetry', 'Multi-Campus Directory', 'Role & Permission Engine', 'Audit Logs & Security'],
    },
    {
      role: 'university-admin',
      title: 'University Operations Center',
      subtitle: 'Campus Administration & Academic Scheduling',
      description: 'Faculty management, student admissions, classroom allocation, examination timetable, fee billing, and campus facilities.',
      badge: 'University Admin',
      badgeColor: 'bg-indigo-50 text-indigo-700 border-indigo-200',
      accentColor: 'from-indigo-600 to-indigo-800',
      icon: Building2,
      features: ['Student & Staff Registry', 'Department & Course Catalog', 'Exams & Result Publication', 'Fee Collection & Finance'],
    },
    {
      role: 'teacher',
      title: 'Teaching Workspace',
      subtitle: 'Instructional Management & Grading',
      description: 'Daily class schedule, real-time attendance marking, assignment creation, submission review, gradebook, and course material uploads.',
      badge: 'Teacher',
      badgeColor: 'bg-teal-50 text-teal-700 border-teal-200',
      accentColor: 'from-teal-600 to-teal-800',
      icon: School,
      features: ['Live Class Timetable', 'Instant Attendance Marker', 'Assignment & Grading Engine', 'Student Progress Analytics'],
    },
    {
      role: 'student',
      title: 'Student Academic Home',
      subtitle: 'Personal Learning & Course Progress',
      description: 'Enrolled courses, daily timetable, upcoming assignment deadlines, examination docket, GPA progress, and fee records.',
      badge: 'Student',
      badgeColor: 'bg-violet-50 text-violet-700 border-violet-200',
      accentColor: 'from-violet-600 to-violet-800',
      icon: GraduationCap,
      features: ['Personal Course Schedule', 'Assignment Submissions', 'Exam Results & GPA Tracker', 'Learning Resource Hub'],
    },
    {
      role: 'parent',
      title: 'Parent Monitoring Center',
      subtitle: 'Child Progress & Direct School Communication',
      description: 'Monitor your ward’s attendance presence, quarterly grades, fee invoices & payment receipts, exam dates, and teacher messages.',
      badge: 'Parent',
      badgeColor: 'bg-amber-50 text-amber-700 border-amber-200',
      accentColor: 'from-amber-600 to-amber-800',
      icon: Users,
      features: ['Child Attendance Tracker', 'Academic Performance Card', 'Online Tuition Payments', 'Direct Teacher Messaging'],
    },
  ]

  const activePortal = rolePortals.find((p) => p.role === activeRoleTab) || rolePortals[0]
  const ActiveIcon = activePortal.icon

  return (
    <PublicLayout>
      {/* ── HERO SECTION ────────────────────────────────────────────── */}
      <section className="relative overflow-hidden bg-slate-950 text-white pt-12 pb-24 lg:pt-20 lg:pb-32">
        {/* Abstract Glow Background Elements */}
        <div className="absolute top-0 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-rose-600/20 rounded-full blur-[140px] pointer-events-none" />
        <div className="absolute bottom-0 right-1/4 translate-x-1/2 translate-y-1/2 w-[500px] h-[500px] bg-indigo-600/15 rounded-full blur-[120px] pointer-events-none" />

        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            {/* Left Content Column */}
            <div className="lg:col-span-7 space-y-6 text-center lg:text-left">
              <div className="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold text-rose-300 border border-white/15 backdrop-blur-md">
                <Sparkles className="h-3.5 w-3.5 text-rose-400 animate-pulse" />
                <span>Admissions Open for Academic Year 2026-2027</span>
              </div>

              <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black font-serif tracking-tight text-white leading-[1.1]">
                Inspiring Intellect.{' '}
                <span className="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 via-rose-300 to-amber-200">
                  Building Character.
                </span>{' '}
                Shaping the Future.
              </h1>

              <p className="text-lg text-slate-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                Welcome to <strong className="text-white">Tima-Ade University</strong> — a premier academic institution fostering discovery, analytical excellence, and innovative student leadership from Grades 9 through 12 and higher secondary programs.
              </p>

              {/* Action buttons */}
              <div className="pt-2 flex flex-wrap items-center justify-center lg:justify-start gap-4">
                <Link
                  href="/admissions"
                  className="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-bold shadow-lg shadow-rose-900/40 hover:scale-[1.02] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-rose-400"
                >
                  <span>Apply for Admission</span>
                  <ArrowRight className="h-4 w-4" />
                </Link>
                <Link
                  href="/academics"
                  className="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl border border-white/20 bg-white/5 hover:bg-white/10 text-white font-semibold backdrop-blur-sm transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/40"
                >
                  <BookOpen className="h-4 w-4 text-rose-400" />
                  <span>Explore Curriculum</span>
                </Link>
              </div>

              {/* Trust Indicators */}
              <div className="pt-6 border-t border-white/10 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-slate-400 font-medium">
                <div className="flex items-center gap-1.5">
                  <CheckCircle2 className="h-4 w-4 text-emerald-400" />
                  <span>Accredited Standards</span>
                </div>
                <div className="flex items-center gap-1.5">
                  <CheckCircle2 className="h-4 w-4 text-emerald-400" />
                  <span>140+ Expert Faculty</span>
                </div>
                <div className="flex items-center gap-1.5">
                  <CheckCircle2 className="h-4 w-4 text-emerald-400" />
                  <span>State-of-the-art STEM Labs</span>
                </div>
              </div>
            </div>

            {/* Right Interactive Portal Launcher */}
            <div className="lg:col-span-5">
              <div className="rounded-3xl border border-white/15 bg-slate-900/90 backdrop-blur-xl p-6 shadow-2xl shadow-black/60 relative">
                <div className="flex items-center justify-between pb-4 border-b border-white/10">
                  <div className="flex items-center gap-2.5">
                    <div className="h-3 w-3 rounded-full bg-rose-500 animate-ping" />
                    <span className="text-xs font-bold uppercase tracking-wider text-rose-400">
                      Unified Portal Access
                    </span>
                  </div>
                  <span className="text-[11px] text-slate-400">5 Distinct Dashboards</span>
                </div>

                {/* Role Switcher Pills */}
                <div className="mt-4 grid grid-cols-3 sm:grid-cols-5 gap-1.5 bg-slate-950/80 p-1.5 rounded-2xl border border-white/10">
                  {(['super-admin', 'university-admin', 'teacher', 'student', 'parent'] as Role[]).map((r) => {
                    const active = activeRoleTab === r
                    const shortName =
                      r === 'super-admin'
                        ? 'Super'
                        : r === 'university-admin'
                        ? 'Admin'
                        : r === 'teacher'
                        ? 'Teacher'
                        : r === 'student'
                        ? 'Student'
                        : 'Parent'
                    return (
                      <button
                        key={r}
                        type="button"
                        onClick={() => setActiveRoleTab(r)}
                        className={[
                          'py-1.5 px-2 rounded-xl text-xs font-bold transition-all text-center truncate focus:outline-none',
                          active
                            ? 'bg-rose-600 text-white shadow-md'
                            : 'text-slate-400 hover:text-white hover:bg-white/5 focus-visible:ring-2 focus-visible:ring-rose-400',
                        ].join(' ')}
                      >
                        {shortName}
                      </button>
                    )
                  })}
                </div>

                {/* Active Role Card Details */}
                <div className="mt-5 space-y-4">
                  <div className="flex items-start gap-3.5">
                    <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-600/20 text-rose-400 border border-rose-500/30">
                      <ActiveIcon className="h-6 w-6" />
                    </div>
                    <div>
                      <span className="inline-block text-[10px] font-bold uppercase tracking-widest text-rose-400 mb-0.5">
                        {activePortal.subtitle}
                      </span>
                      <h2 className="text-lg font-bold text-white leading-tight">
                        {activePortal.title}
                      </h2>
                    </div>
                  </div>

                  <p className="text-xs text-slate-300 leading-relaxed">
                    {activePortal.description}
                  </p>

                  {/* Feature Bullets */}
                  <div className="grid grid-cols-2 gap-2 pt-1">
                    {activePortal.features.map((feat, i) => (
                      <div key={i} className="flex items-center gap-1.5 text-[11px] text-slate-300">
                        <span className="h-1.5 w-1.5 rounded-full bg-rose-500" />
                        <span className="truncate">{feat}</span>
                      </div>
                    ))}
                  </div>

                  {/* Direct Launch Button */}
                  <div className="pt-2">
                    <button
                      type="button"
                      onClick={() => handleQuickLaunch(activeRoleTab)}
                      disabled={loggingInRole !== null}
                      className="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-bold text-sm shadow-lg shadow-rose-900/50 transition-all cursor-pointer"
                    >
                      {loggingInRole === activeRoleTab ? (
                        <span>Launching Portal...</span>
                      ) : (
                        <>
                          <span>Enter {activePortal.badge} Dashboard</span>
                          <ArrowRight className="h-4 w-4" />
                        </>
                      )}
                    </button>
                    <div className="mt-2 text-center text-[11px] text-slate-400">
                      Or visit <Link href="/login" className="text-rose-400 hover:underline">credential sign in</Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ── STATS COUNTER STRIP ────────────────────────────────────── */}
      <section className="bg-slate-900 text-white py-8 border-y border-slate-800 shadow-inner">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
            {universityStats.map((stat, i) => (
              <AnimatedStat key={i} value={stat.value} label={stat.label} subtext={stat.subtext} />
            ))}
          </div>
        </div>
      </section>

      {/* ── CORE ACADEMIC PILLARS ──────────────────────────────────── */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span className="text-xs font-bold uppercase tracking-widest text-rose-700">
              Institutional Excellence
            </span>
            <h2 className="text-3xl sm:text-4xl font-black font-serif text-slate-950 tracking-tight">
              Foundations of Tima-Ade University Distinction
            </h2>
            <p className="text-slate-600 text-base">
              Our educational framework unites rigorous analytical discipline with personal leadership, ethical integrity, and pre-university scholarly mastery.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {campusPillars.map((pillar, i) => {
              const Icon =
                pillar.icon === 'Brain'
                  ? Brain
                  : pillar.icon === 'Atom'
                  ? Atom
                  : pillar.icon === 'ShieldCheck'
                  ? ShieldCheck
                  : Compass
              return (
                <div
                  key={i}
                  className="rounded-3xl border border-slate-200 bg-slate-50/70 p-7 hover:bg-white hover:border-slate-300 hover:shadow-xl transition-all duration-300 flex flex-col group"
                >
                  <div className={`h-12 w-12 rounded-2xl flex items-center justify-center ${pillar.accent} mb-6 shadow-xs group-hover:scale-110 transition-transform`}>
                    <Icon className="h-6 w-6" />
                  </div>
                  <h3 className="text-lg font-bold text-slate-900 mb-2.5">
                    {pillar.title}
                  </h3>
                  <p className="text-sm text-slate-600 leading-relaxed flex-1">
                    {pillar.description}
                  </p>
                  <div className="mt-6 pt-4 border-t border-slate-200">
                    <Link
                      href="/academics"
                      className="inline-flex items-center gap-1.5 text-xs font-bold text-rose-700 hover:text-rose-800"
                    >
                      <span>Explore Programs</span>
                      <ChevronRight className="h-3.5 w-3.5" />
                    </Link>
                  </div>
                </div>
              )
            })}
          </div>
        </div>
      </section>

      {/* ── ACADEMIC PROGRAMS TIER PREVIEW ─────────────────────────── */}
      <section className="py-20 bg-slate-100/70 border-t border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
              <span className="text-xs font-bold uppercase tracking-widest text-rose-700">
                Curriculum Structure
              </span>
              <h2 className="text-3xl sm:text-4xl font-black font-serif text-slate-950 tracking-tight mt-1">
                Grade Tiers &amp; Classrooms
              </h2>
            </div>
            <Link
              href="/academics"
              className="inline-flex items-center gap-2 text-sm font-bold text-rose-700 hover:text-rose-800"
            >
              <span>View Full Course Catalog (38 Subjects)</span>
              <ArrowRight className="h-4 w-4" />
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {academicPrograms.map((prog) => (
              <div
                key={prog.id}
                className="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm hover:shadow-md transition-shadow"
              >
                <div className="flex items-center justify-between mb-3">
                  <span className="rounded-full bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1 text-xs font-bold">
                    {prog.grade}
                  </span>
                  <span className="text-xs text-slate-500 font-medium">
                    {prog.sections.length} Sections Active
                  </span>
                </div>

                <h3 className="text-xl font-bold text-slate-900 mb-2">
                  {prog.name}
                </h3>
                <p className="text-sm text-slate-600 mb-4 leading-relaxed">
                  {prog.description}
                </p>

                <div className="bg-slate-50 rounded-2xl p-4 border border-slate-100 mb-4 space-y-2">
                  <div className="text-xs text-slate-500 font-medium">
                    Lead Faculty Chair:{' '}
                    <strong className="text-slate-900">{prog.leadTeacher}</strong>
                  </div>
                  <div className="text-xs text-slate-500 font-medium">
                    Sections: <span className="text-slate-700">{prog.sections.join(', ')}</span>
                  </div>
                </div>

                <div className="space-y-2">
                  <div className="text-xs font-bold uppercase tracking-wider text-slate-500">
                    Core Course Allocations ({prog.subjects.length}):
                  </div>
                  <div className="flex flex-wrap gap-1.5">
                    {prog.subjects.slice(0, 4).map((s) => (
                      <span
                        key={s.code}
                        className="rounded-lg bg-slate-100 text-slate-800 px-2.5 py-1 text-xs font-medium border border-slate-200"
                      >
                        {s.name} ({s.code})
                      </span>
                    ))}
                    {prog.subjects.length > 4 && (
                      <span className="rounded-lg bg-rose-50 text-rose-700 px-2.5 py-1 text-xs font-bold">
                        +{prog.subjects.length - 4} more
                      </span>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── NOTICEBOARD & BULLETINS STRIP ──────────────────────────── */}
      <section className="py-20 bg-white border-t border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
              <span className="text-xs font-bold uppercase tracking-widest text-rose-700">
                Campus Bulletins
              </span>
              <h2 className="text-3xl sm:text-4xl font-black font-serif text-slate-950 tracking-tight mt-1">
                Latest University Circulars &amp; News
              </h2>
            </div>
            <Link
              href="/notices"
              className="inline-flex items-center gap-2 text-sm font-bold text-rose-700 hover:text-rose-800"
            >
              <span>View All Circulars</span>
              <ArrowRight className="h-4 w-4" />
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {universityNotices.slice(0, 3).map((notice) => (
              <div
                key={notice.id}
                className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-lg transition-all flex flex-col justify-between"
              >
                <div>
                  <div className="flex items-center justify-between mb-3">
                    <span className="rounded-md bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-0.5 text-xs font-bold">
                      {notice.category}
                    </span>
                    <span className="text-xs text-slate-400 font-medium">
                      {notice.publishedDate}
                    </span>
                  </div>

                  <h3 className="text-base font-bold text-slate-900 mb-2 line-clamp-2 hover:text-rose-700 transition-colors">
                    <Link href={`/notices/${notice.id}`}>{notice.title}</Link>
                  </h3>
                  <p className="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-4">
                    {notice.summary}
                  </p>
                </div>

                <div className="pt-4 border-t border-slate-100 flex items-center justify-between">
                  <span className="text-[11px] text-slate-400 truncate">{notice.author}</span>
                  <Link
                    href={`/notices/${notice.id}`}
                    className="text-xs font-bold text-rose-700 hover:text-rose-800 inline-flex items-center gap-1 shrink-0"
                  >
                    <span>Read</span>
                    <ChevronRight className="h-3 w-3" />
                  </Link>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── TESTIMONIALS & VOICES ──────────────────────────────────── */}
      <section className="py-20 bg-slate-950 text-white relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
          <div className="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span className="text-xs font-bold uppercase tracking-widest text-rose-400">
              Community Voices
            </span>
            <h2 className="text-3xl sm:text-4xl font-black font-serif text-white tracking-tight">
              Scholars, Parents &amp; Faculty
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {universityTestimonials.map((t, i) => (
              <div
                key={i}
                className="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md p-7 flex flex-col justify-between"
              >
                <p className="text-sm text-slate-300 italic leading-relaxed mb-6">
                  &ldquo;{t.quote}&rdquo;
                </p>
                <div className="flex items-center gap-3 pt-4 border-t border-white/10">
                  <img
                    src={t.avatar}
                    alt={t.author}
                    className="h-11 w-11 rounded-full object-cover border border-rose-500/40"
                  />
                  <div>
                    <div className="text-sm font-bold text-white">{t.author}</div>
                    <div className="text-xs text-rose-400 font-medium">{t.role}</div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── ADMISSIONS CTA BANNER ───────────────────────────────────── */}
      <section className="py-20 bg-gradient-to-br from-rose-700 via-rose-800 to-slate-950 text-white">
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
          <span className="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold text-rose-200 border border-white/20">
            <Sparkles className="h-3.5 w-3.5 text-amber-300" />
            2026 Academic Term Enrollment
          </span>

          <h2 className="text-3xl sm:text-5xl font-black font-serif tracking-tight leading-tight">
            Begin Your Scholarly Journey at Tima-Ade University
          </h2>

          <p className="text-base sm:text-lg text-rose-100 max-w-2xl mx-auto leading-relaxed">
            Our admissions committee reviews applications on a holistic basis. Join a diverse, ambitious community dedicated to curiosity, character, and academic mastery.
          </p>

          <div className="pt-4 flex flex-wrap items-center justify-center gap-4">
            <Link
              href="/admissions"
              className="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-white text-rose-900 hover:bg-rose-50 font-bold text-base shadow-xl hover:scale-105 transition-all"
            >
              <span>Submit Admission Application</span>
              <ArrowRight className="h-5 w-5 text-rose-700" />
            </Link>
            <Link
              href="/contact"
              className="inline-flex items-center gap-2 px-8 py-4 rounded-2xl border border-white/30 bg-white/10 hover:bg-white/20 text-white font-semibold text-base backdrop-blur-sm transition-all"
            >
              <span>Schedule Campus Tour</span>
            </Link>
          </div>
        </div>
      </section>
    </PublicLayout>
  )
}
