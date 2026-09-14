import { ReactNode, useState } from 'react'
import Link from 'next/link'
import { useRouter } from 'next/router'
import {
  GraduationCap,
  Phone,
  Mail,
  MapPin,
  Menu,
  X,
  ArrowRight,
  ShieldCheck,
  ChevronRight,
  Sparkles,
  BookOpen,
  Users,
  Compass,
  FileText,
  Lock,
  Layers,
} from 'lucide-react'
import { useAuth } from '../context/AuthContext'
import UniversityLogo from '../components/UniversityLogo'

interface PublicLayoutProps {
  children: ReactNode
  title?: string
  description?: string
}

export default function PublicLayout({ children, title, description }: PublicLayoutProps) {
  const router = useRouter()
  const { user } = useAuth()
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  const navLinks = [
    { href: '/', label: 'Home' },
    { href: '/about', label: 'About' },
    { href: '/leadership', label: 'Leadership' },
    { href: '/academics', label: 'Academics' },
    { href: '/research', label: 'Research' },
    { href: '/student-life', label: 'Student Life' },
    { href: '/library', label: 'Library' },
    { href: '/scholarships', label: 'Scholarships' },
    { href: '/news', label: 'News' },
    { href: '/events', label: 'Events' },
    { href: '/careers', label: 'Careers' },
    { href: '/alumni', label: 'Alumni' },
    { href: '/contact', label: 'Contact' },
  ]

  const getDashboardUrl = () => {
    if (!user) return '/login'
    return `/${user.role}/overview`
  }

  return (
    <div className="min-h-screen bg-slate-50 text-slate-900 font-sans selection:bg-rose-500 selection:text-white">
      {/* ── TOP ANNOUNCEMENT & UTILITY BAR ──────────────────────────── */}
      <div className="bg-slate-950 text-slate-300 border-b border-white/10 text-xs py-2 px-4 sm:px-6 lg:px-8">
        <div className="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-2">
          <div className="flex flex-wrap items-center justify-center md:justify-start gap-4">
            <span className="inline-flex items-center gap-1.5 text-slate-300 font-medium">
              <MapPin className="h-3.5 w-3.5 text-rose-500 shrink-0" />
              Gabiley, Somaliland
            </span>
            <span className="hidden sm:inline text-slate-600">•</span>
            <a href="tel:+25216500000" className="hidden sm:inline-flex items-center gap-1.5 hover:text-white transition-colors">
              <Phone className="h-3.5 w-3.5 text-rose-500 shrink-0" />
              +252 (650) 00000
            </a>
            <span className="hidden lg:inline text-slate-600">•</span>
            <a href="mailto:admissions@tima-ade.edu" className="hidden lg:inline-flex items-center gap-1.5 hover:text-white transition-colors">
              <Mail className="h-3.5 w-3.5 text-rose-500 shrink-0" />
              admissions@tima-ade.edu
            </a>
          </div>

          <div className="flex flex-wrap items-center justify-center gap-2 sm:gap-3 md:justify-end">
            <span className="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-2.5 py-0.5 text-[11px] font-semibold text-rose-400 border border-rose-500/20">
              <Sparkles className="h-3 w-3" /> Admissions 2026-2027 Open
            </span>
            {user ? (
              <Link
                href={getDashboardUrl()}
                className="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-2.5 py-1 text-white text-[11px] font-semibold hover:bg-rose-700 transition-colors shadow-sm"
              >
                <ShieldCheck className="h-3 w-3" />
                <span>Dashboard ({user.name})</span>
              </Link>
            ) : (
              <Link
                href="/login"
                className="inline-flex items-center gap-1.5 rounded-lg bg-white/10 hover:bg-white/20 px-2.5 py-1 text-white text-[11px] font-semibold transition-colors"
              >
                <Lock className="h-3 w-3 text-rose-400" />
                <span>Portal Sign In</span>
              </Link>
            )}
          </div>
        </div>
      </div>

      {/* ── MAIN STICKY NAVIGATION HEADER ──────────────────────────── */}
      <header className="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-20">
            {/* Brand Logo */}
            <UniversityLogo
              size="md"
              showText
              href="/"
              className="group"
              imageClassName="h-12 w-12 shrink-0 object-contain"
            />

            {/* Desktop Navigation Links */}
            <nav className="hidden lg:flex items-center gap-1">
              {navLinks.map((link) => {
                const isActive = router.pathname === link.href || (link.href !== '/' && router.pathname.startsWith(link.href))
                return (
                  <Link
                    key={link.href}
                    href={link.href}
                    className={[
                      'px-3.5 py-2 rounded-xl text-sm font-semibold transition-all',
                      isActive
                        ? 'bg-rose-50 text-rose-700 shadow-xs ring-1 ring-rose-200'
                        : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100',
                    ].join(' ')}
                  >
                    {link.label}
                  </Link>
                )
              })}
            </nav>

            {/* Right Action Buttons */}
            <div className="hidden lg:flex items-center gap-3">
              <Link
                href="/login"
                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-800 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-xs"
              >
                <Lock className="h-4 w-4 text-rose-600" />
                <span>Portals</span>
              </Link>
              <Link
                href="/admissions"
                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-sm font-bold text-white shadow-md shadow-rose-600/20 hover:shadow-lg hover:shadow-rose-600/30 transition-all"
              >
                <span>Apply Now</span>
                <ArrowRight className="h-4 w-4" />
              </Link>
            </div>

            {/* Mobile Menu Toggle Button */}
            <div className="flex lg:hidden items-center gap-2">
              <Link
                href="/login"
                className="px-3 py-1.5 rounded-lg bg-slate-100 text-xs font-bold text-slate-800"
              >
                Portals
              </Link>
              <button
                type="button"
                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                className="p-2 rounded-xl text-slate-700 hover:bg-slate-100 focus:outline-none"
                aria-label="Toggle navigation menu"
              >
                {mobileMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
              </button>
            </div>
          </div>
        </div>

        {/* Mobile Dropdown Menu */}
        {mobileMenuOpen && (
          <div className="lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-2 shadow-xl animate-in slide-in-from-top-2 duration-200">
            {navLinks.map((link) => {
              const isActive = router.pathname === link.href
              return (
                <Link
                  key={link.href}
                  href={link.href}
                  onClick={() => setMobileMenuOpen(false)}
                  className={[
                    'block px-4 py-2.5 rounded-xl text-base font-semibold',
                    isActive ? 'bg-rose-50 text-rose-700 font-bold' : 'text-slate-700 hover:bg-slate-50',
                  ].join(' ')}
                >
                  {link.label}
                </Link>
              )
            })}
            <div className="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2">
              <Link
                href="/login"
                onClick={() => setMobileMenuOpen(false)}
                className="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800"
              >
                <Lock className="h-4 w-4 text-rose-600" />
                <span>Portal Login</span>
              </Link>
              <Link
                href="/admissions"
                onClick={() => setMobileMenuOpen(false)}
                className="flex items-center justify-center gap-2 py-2.5 rounded-xl bg-rose-600 text-sm font-bold text-white"
              >
                <span>Apply</span>
                <ArrowRight className="h-4 w-4" />
              </Link>
            </div>
          </div>
        )}
      </header>

      {/* ── PAGE MAIN CONTENT ──────────────────────────────────────── */}
      <main id="main-content" className="flex-1">
        {children}
      </main>

      {/* ── UNIFIED UNIVERSITY FOOTER ───────────────────────────────── */}
      <footer className="bg-slate-950 text-slate-300 border-t border-slate-800 pt-16 pb-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-10 pb-12 border-b border-slate-800">
            {/* Brand column */}
            <div className="lg:col-span-2 space-y-4">
              <UniversityLogo
                size="lg"
                showText
                href="/"
                className="text-white"
                imageClassName="h-14 w-14 shrink-0 object-contain"
              />
              <p className="text-sm text-slate-400 max-w-sm leading-relaxed">
                Tima-Ade University is an accredited, world-class academic institution empowering scholars from Grades 9 through 12 and higher secondary streams with rigorous STEM discovery, classical humanities, and ethical leadership.
              </p>
              <div className="pt-2 flex flex-wrap gap-2 text-xs text-slate-400">
                <span className="rounded-md bg-white/5 border border-white/10 px-2.5 py-1 text-slate-300 font-medium">
                  ✓ Fully Accredited
                </span>
                <span className="rounded-md bg-white/5 border border-white/10 px-2.5 py-1 text-slate-300 font-medium">
                  ✓ STEM Certified
                </span>
                <span className="rounded-md bg-white/5 border border-white/10 px-2.5 py-1 text-slate-300 font-medium">
                  ✓ Global Admissions
                </span>
              </div>
            </div>

            {/* Quick Navigation */}
            <div>
              <h3 className="text-xs font-bold uppercase tracking-widest text-rose-400 mb-4">University</h3>
              <ul className="space-y-2.5 text-sm">
                <li><Link href="/" className="hover:text-white transition-colors">Home</Link></li>
                <li><Link href="/about" className="hover:text-white transition-colors">About &amp; Heritage</Link></li>
                <li><Link href="/leadership" className="hover:text-white transition-colors">Leadership</Link></li>
                <li><Link href="/academics" className="hover:text-white transition-colors">Academic Programs</Link></li>
                <li><Link href="/research" className="hover:text-white transition-colors">Research &amp; Innovation</Link></li>
                <li><Link href="/faculty" className="hover:text-white transition-colors">Faculty Directory</Link></li>
                <li><Link href="/admissions" className="hover:text-white transition-colors">Admissions 2026</Link></li>
                <li><Link href="/notices" className="hover:text-white transition-colors">Official Noticeboard</Link></li>
              </ul>
            </div>

            {/* Resources & Community */}
            <div>
              <h3 className="text-xs font-bold uppercase tracking-widest text-rose-400 mb-4">Resources &amp; Community</h3>
              <ul className="space-y-2.5 text-sm">
                <li><Link href="/student-life" className="hover:text-white transition-colors">Student Life &amp; Clubs</Link></li>
                <li><Link href="/library" className="hover:text-white transition-colors">Library Services</Link></li>
                <li><Link href="/scholarships" className="hover:text-white transition-colors">Scholarships &amp; Aid</Link></li>
                <li><Link href="/news" className="hover:text-white transition-colors">News &amp; Updates</Link></li>
                <li><Link href="/events" className="hover:text-white transition-colors">Events &amp; Calendar</Link></li>
                <li><Link href="/careers" className="hover:text-white transition-colors">Career Opportunities</Link></li>
                <li><Link href="/alumni" className="hover:text-white transition-colors">Alumni Network</Link></li>
                <li><Link href="/contact" className="hover:text-white transition-colors">Contact &amp; Visit</Link></li>
              </ul>
            </div>

            {/* Role Portals */}
            <div>
              <h3 className="text-xs font-bold uppercase tracking-widest text-rose-400 mb-4">Portals &amp; Systems</h3>
              <ul className="space-y-2.5 text-sm">
                <li>
                  <Link href="/super-admin/overview" className="inline-flex items-center gap-1.5 hover:text-white transition-colors text-slate-300">
                    <span className="h-1.5 w-1.5 rounded-full bg-rose-500" />
                    Super Admin Control
                  </Link>
                </li>
                <li>
                  <Link href="/university-admin/overview" className="inline-flex items-center gap-1.5 hover:text-white transition-colors text-slate-300">
                    <span className="h-1.5 w-1.5 rounded-full bg-indigo-500" />
                    University Admin
                  </Link>
                </li>
                <li>
                  <Link href="/teacher/overview" className="inline-flex items-center gap-1.5 hover:text-white transition-colors text-slate-300">
                    <span className="h-1.5 w-1.5 rounded-full bg-teal-500" />
                    Teacher Workspace
                  </Link>
                </li>
                <li>
                  <Link href="/student/overview" className="inline-flex items-center gap-1.5 hover:text-white transition-colors text-slate-300">
                    <span className="h-1.5 w-1.5 rounded-full bg-violet-500" />
                    Student Academic Home
                  </Link>
                </li>
                <li>
                  <Link href="/parent/overview" className="inline-flex items-center gap-1.5 hover:text-white transition-colors text-slate-300">
                    <span className="h-1.5 w-1.5 rounded-full bg-amber-500" />
                    Parent Monitoring Center
                  </Link>
                </li>
                <li className="pt-2">
                  <Link href="/login" className="text-xs font-bold text-rose-400 hover:text-rose-300 underline">
                    Switch Role Demo Account →
                  </Link>
                </li>
              </ul>
            </div>

            {/* Campus Contact Info */}
            <div>
              <h3 className="text-xs font-bold uppercase tracking-widest text-rose-400 mb-4">Contact Campus</h3>
              <div className="space-y-3 text-sm text-slate-400">
                <div className="flex items-start gap-2.5">
                  <MapPin className="h-4 w-4 text-rose-500 mt-0.5 shrink-0" />
                  <span>Gabiley, Somaliland</span>
                </div>
                <div className="flex items-center gap-2.5">
                  <Phone className="h-4 w-4 text-rose-500 shrink-0" />
                  <span>+1 (555) 234-5678</span>
                </div>
                <div className="flex items-center gap-2.5">
                  <Mail className="h-4 w-4 text-rose-500 shrink-0" />
                  <span>info@timaade.edu</span>
                </div>
                <div className="pt-3">
                  <Link
                    href="/admissions"
                    className="inline-block w-full text-center py-2 px-3 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors"
                  >
                    Enroll for 2026 Term
                  </Link>
                </div>
              </div>
            </div>
          </div>

          {/* Bottom Bar */}
          <div className="pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <div>
              © {new Date().getFullYear()} Tima-Ade University Management Suite. All rights reserved.
            </div>
            <div className="flex items-center gap-6">
              <Link href="/about" className="hover:text-slate-300">Institutional Policy</Link>
              <Link href="/admissions" className="hover:text-slate-300">Admission Guidelines</Link>
              <Link href="/notices" className="hover:text-slate-300">Academic Circulars</Link>
              <Link href="/login" className="hover:text-slate-300">Portal Security</Link>
            </div>
          </div>
        </div>
      </footer>
    </div>
  )
}
