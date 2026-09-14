'use client'

export const dynamic = 'force-dynamic'

import { useEffect, useMemo, useRef, useState } from 'react'
import Link from 'next/link'
import Image from 'next/image'
import { motion, useAnimation } from 'framer-motion'
import {
  BookOpen,
  GraduationCap,
  Sparkles,
  ArrowRight,
  CheckCircle2,
  Search,
  Award,
  Layers,
  FileText,
  Clock,
  Compass,
  Users,
  Globe,
  Briefcase,
  ChevronRight,
  ChevronDown,
} from 'lucide-react'
import PublicLayout from '../src/layouts/PublicLayout'
import {
  academicPrograms,
  facultyDirectory,
  universityNotices,
  universityStats,
  campusPillars,
} from '../src/data/public-site'

// Static Stat Card Component
function StatCard({ s }: { s: any }) {
  return (
    <div
      className="rounded-2xl border border-slate-200 p-6 bg-slate-50"
    >
      <div className="text-sm text-slate-500">{s.label}</div>
      <div className="text-2xl font-bold text-slate-900 mt-2">{s.value}</div>
      <div className="text-xs text-slate-500 mt-1">{s.subtext}</div>
    </div>
  )
}

export default function AcademicsPage() {
  const [selectedGrade, setSelectedGrade] = useState('all')
  const [searchQuery, setSearchQuery] = useState('')

  // For Program Explorer filters
  const [filterGrade, setFilterGrade] = useState<string>('all')
  const [filterDepartment, setFilterDepartment] = useState<string>('all')
  const [filterSubject, setFilterSubject] = useState<string>('all')

  // Flatten all subjects from programs (used for subject filters and search)
  const allSubjects = useMemo(
    () =>
      academicPrograms.flatMap((prog) =>
        prog.subjects.map((subj) => ({
          ...subj,
          grade: prog.grade,
          leadTeacher: prog.leadTeacher,
          programId: prog.id,
          programName: prog.name,
        }))
      ),
    []
  )

  const departments = useMemo(() => {
    const set = new Set(facultyDirectory.map((f) => f.department))
    return Array.from(set)
  }, [])

  // Helper: find faculty by program lead teacher (best-effort using name match)
  function findFacultyForProgram(leadTeacher: string) {
    const match = facultyDirectory.find((f) => leadTeacher.includes(f.name) || f.name.includes(leadTeacher.split(',')[0]))
    return match || null
  }

  // Program Explorer - filtered programs
  const explorerPrograms = useMemo(() => {
    return academicPrograms.filter((p) => {
      if (filterGrade !== 'all' && p.id !== filterGrade) return false
      if (filterSubject !== 'all') {
        const found = p.subjects.find((s) => s.code === filterSubject || s.name === filterSubject)
        if (!found) return false
      }
      if (filterDepartment !== 'all') {
        const faculty = findFacultyForProgram(p.leadTeacher)
        if (!faculty || faculty.department !== filterDepartment) return false
      }
      return true
    })
  }, [filterGrade, filterDepartment, filterSubject])

  // Preserve existing page behavior: filteredPrograms for grade tabs & subject search
  const filteredPrograms =
    selectedGrade === 'all' ? academicPrograms : academicPrograms.filter((p) => p.id === selectedGrade)

  const filteredSubjects = allSubjects.filter((s) => {
    const matchesSearch =
      s.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      s.code.toLowerCase().includes(searchQuery.toLowerCase()) ||
      s.grade.toLowerCase().includes(searchQuery.toLowerCase())
    const matchesGrade = selectedGrade === 'all' || s.grade.toLowerCase().replace(' ', '-') === selectedGrade
    return matchesSearch && matchesGrade
  })

  // Counters animation (uses universityStats - only uses provided data)
  const countersRef = useRef<HTMLDivElement | null>(null)
  const [countersVisible, setCountersVisible] = useState(false)

  useEffect(() => {
    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) setCountersVisible(true)
        })
      },
      { threshold: 0.3 }
    )
    if (countersRef.current) obs.observe(countersRef.current)
    return () => obs.disconnect()
  }, [])

  // Simple numeric animator for stats that contains digits (moved to component level)
  // See AnimatedStatCard component above

  // Scroll to program explorer anchor
  function scrollToExplorer() {
    const el = document.getElementById('program-explorer')
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }

  return (
    <PublicLayout>
      {/* ── PREMIUM HERO (enhanced, animated) ───────────────────────── */}
      <section className="relative bg-gradient-to-b from-slate-900 via-slate-900/95 to-slate-800 text-white overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 relative z-10">
          {/* Breadcrumb */}
          <nav className="text-sm text-slate-300 mb-4" aria-label="Breadcrumb">
            <ol className="flex items-center gap-2">
              <li>
                <Link href="/" className="hover:underline text-slate-300">
                  Home
                </Link>
                <span className="mx-2 text-slate-500">/</span>
              </li>
              <li className="font-semibold text-white">Academics</li>
            </ol>
          </nav>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div>
              <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black font-serif leading-tight tracking-tight">
                Academic Excellence &amp; Programs
              </h1>
              <p className="mt-4 text-lg text-slate-300 max-w-2xl">
                A curated portfolio of rigorous curricula, capstone research, and faculty-led programs designed to prepare scholars for global leadership and advanced study.
              </p>

              <div className="mt-6 flex flex-wrap items-center gap-3">
                <button
                  onClick={scrollToExplorer}
                  className="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
                >
                  <GraduationCap className="w-4 h-4" /> Explore Programs
                </button>
                <Link
                  href="/admissions"
                  className="inline-flex items-center gap-2 px-4 py-3 rounded-xl border border-white/20 hover:bg-white/5 text-white font-medium"
                >
                  Apply Now
                </Link>
                <Link href="/contact" className="ml-2 text-sm text-slate-300 hover:underline">
                  Contact Admissions <ArrowRight className="inline-block w-3 h-3 ml-1" />
                </Link>
              </div>

              {/* campus pillars */}
              <div className="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
                {campusPillars.slice(0, 4).map((p) => (
                  <div key={p.title} className={`rounded-lg p-3 border ${p.accent} bg-white/5`}> 
                    <div className="text-xs font-semibold text-white">{p.title}</div>
                    <div className="text-xs text-slate-300 mt-1">{p.description}</div>
                  </div>
                ))}
              </div>
            </div>

            <div
              className="relative h-64 sm:h-80 lg:h-96 rounded-2xl overflow-hidden bg-slate-900/30"
            >
              {/* Replaceable campus image - easy to swap with institution image */}
              <Image
                src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=1600&auto=format&fit=crop"
                alt="Campus building with academic atmosphere"
                fill
                sizes="(min-width:1024px) 50vw, 100vw"
                className="object-cover"
                priority={false}
              />

              <div className="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent" />
            </div>
          </div>
        </div>
      </section>

      {/* ── ACADEMIC OVERVIEW (Undergrad/Postgrad/Doctoral/Continuing) ── */}
      <section className="py-12 bg-white border-b border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-bold">Academic Overview</h2>
            <p className="text-sm text-slate-500">Explore program types and pathways</p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {[
              {
                key: 'undergrad',
                title: 'Undergraduate Studies',
                icon: <BookOpen className="w-5 h-5 text-rose-600" />,
                desc: 'Full degree and diploma pathways focused on breadth and depth across disciplines.',
                count: universityStats.find((s) => s.label.includes('Academic Programs'))?.value,
              },
              {
                key: 'postgrad',
                title: 'Postgraduate & Honors',
                icon: <Award className="w-5 h-5 text-blue-600" />,
                desc: 'Capstone mentorships, advanced seminars, and research-led coursework for scholars.',
              },
              {
                key: 'doctoral',
                title: 'Doctoral & Research',
                icon: <Layers className="w-5 h-5 text-emerald-600" />,
                desc: 'Faculty-guided research tracks and specialized labs (see Research & Innovation).',
              },
              {
                key: 'continuing',
                title: 'Professional & Continuing Education',
                icon: <Clock className="w-5 h-5 text-amber-600" />,
                desc: 'Short courses, executive programs, and certificate series for lifelong learners.',
              },
            ].map((card) => (
              <div
                key={card.key}
                className="rounded-2xl border border-slate-200 p-6 bg-slate-50"
              >
                <div className="flex items-start gap-3">
                  <div className="shrink-0 bg-white/5 rounded-md p-2">{card.icon}</div>
                  <div>
                    <div className="text-sm font-semibold text-slate-900">{card.title}</div>
                    <div className="text-xs text-slate-500 mt-1">{card.desc}</div>
                    {card.count && <div className="mt-3 text-xs font-bold text-rose-700">{card.count} Programs</div>}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── PROGRAM EXPLORER ───────────────────────────────────────── */}
      <section id="program-explorer" className="py-12 bg-slate-50 border-b border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-2xl font-bold">Program Explorer</h2>
            <p className="text-sm text-slate-500">Search, filter, and discover programs</p>
          </div>

          {/* Filters */}
          <div className="flex flex-col lg:flex-row gap-3 items-start lg:items-center mb-6">
            <div className="flex gap-2 flex-wrap">
              <select
                value={filterGrade}
                onChange={(e) => setFilterGrade(e.target.value)}
                className="px-3 py-2 rounded-xl border bg-white text-sm"
              >
                <option value="all">All Levels</option>
                {academicPrograms.map((p) => (
                  <option key={p.id} value={p.id}>
                    {p.grade}
                  </option>
                ))}
              </select>

              <select
                value={filterDepartment}
                onChange={(e) => setFilterDepartment(e.target.value)}
                className="px-3 py-2 rounded-xl border bg-white text-sm"
              >
                <option value="all">All Departments</option>
                {departments.map((d) => (
                  <option key={d} value={d}>
                    {d}
                  </option>
                ))}
              </select>

              <select
                value={filterSubject}
                onChange={(e) => setFilterSubject(e.target.value)}
                className="px-3 py-2 rounded-xl border bg-white text-sm"
              >
                <option value="all">All Subjects</option>
                {allSubjects.map((s) => (
                  <option key={`${s.programId}-${s.code}`} value={s.code}>
                    {s.code} — {s.name}
                  </option>
                ))}
              </select>
            </div>

            <div className="ml-auto w-full lg:w-auto">
              <div className="relative">
                <Search className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
                <input
                  type="search"
                  aria-label="Search programs"
                  placeholder="Search programs, keywords..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="w-full lg:w-80 pl-9 pr-3 h-10 rounded-xl border bg-white text-sm"
                />
              </div>
            </div>
          </div>

          {/* Programs grid */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {explorerPrograms
              .filter((p) => p.name.toLowerCase().includes(searchQuery.toLowerCase()) || p.description.toLowerCase().includes(searchQuery.toLowerCase()) || searchQuery === '')
              .map((p) => {
                const faculty = findFacultyForProgram(p.leadTeacher)
                return (
                  <article
                    key={p.id}
                    className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                  >
                    <div className="flex items-start gap-3">
                      <div className="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center text-rose-600 text-xl font-bold">{p.grade.split(' ')[1] || 'G'}</div>
                      <div className="flex-1">
                        <h3 className="text-lg font-bold text-slate-900">{p.name}</h3>
                        <p className="text-xs text-slate-500 mt-1">{p.description}</p>

                        <div className="mt-3 flex items-center justify-between">
                          <div className="text-xs text-slate-600">
                            <div>{p.grade}</div>
                            <div className="text-slate-500">{faculty ? `${faculty.department}` : 'Faculty information'}</div>
                          </div>
                          <div className="flex items-center gap-2">
                            <Link href={`#${p.id}`} className="text-sm font-semibold text-rose-600 hover:underline">
                              View Program
                            </Link>
                            <Link href="/admissions" className="text-sm px-3 py-1 rounded-full border bg-rose-50 text-rose-600">
                              Apply
                            </Link>
                          </div>
                        </div>
                      </div>
                    </div>
                  </article>
                )
              })}

            {/* Empty state */}
            {explorerPrograms.length === 0 && (
              <div className="col-span-full rounded-2xl border border-slate-200 bg-white p-8 text-center">
                <h3 className="text-lg font-bold">No programs match your filters</h3>
                <p className="text-sm text-slate-500 mt-2">Try adjusting filters or clear the search to discover more programs.</p>
                <div className="mt-4">
                  <button
                    onClick={() => {
                      setFilterDepartment('all')
                      setFilterGrade('all')
                      setFilterSubject('all')
                      setSearchQuery('')
                    }}
                    className="px-4 py-2 rounded-xl border text-sm"
                  >
                    Reset Filters
                  </button>
                </div>
              </div>
            )}
          </div>
        </div>
      </section>

      {/* ── EXISTING GRADE LEVEL BREAKDOWN (preserved & given anchor ids) ── */}
      <section className="py-16 bg-slate-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
          {filteredPrograms.map((prog) => (
            <div
              id={prog.id}
              key={prog.id}
              className="rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 shadow-sm space-y-6"
            >
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                <div>
                  <span className="inline-block rounded-lg bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1 text-xs font-bold mb-2">
                    {prog.grade} Curriculum
                  </span>
                  <h2 className="text-2xl sm:text-3xl font-black font-serif text-slate-950">
                    {prog.name}
                  </h2>
                  <p className="text-sm text-slate-600 mt-1 max-w-2xl">{prog.description}</p>
                </div>
                <div className="text-start md:text-end space-y-1">
                  <div className="text-xs text-slate-500 font-medium">
                    Faculty Chair:{' '}
                    <strong className="text-slate-900">{prog.leadTeacher}</strong>
                  </div>
                  <div className="text-xs text-slate-500 font-medium">
                    Active Cohort Sections:{' '}
                    <span className="font-semibold text-rose-700">{prog.sections.length}</span>
                  </div>
                </div>
              </div>

              {/* Sections list */}
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                {prog.sections.map((sec, idx) => (
                  <div key={idx} className="rounded-xl border border-slate-200 bg-slate-50/80 p-3 text-xs font-semibold text-slate-800 flex items-center gap-2">
                    <CheckCircle2 className="h-4 w-4 text-rose-600 shrink-0" />
                    <span>{sec}</span>
                  </div>
                ))}
              </div>

              {/* Course Catalog Table */}
              <div className="space-y-3 pt-2">
                <h3 className="text-xs font-bold uppercase tracking-wider text-slate-500">
                  Assigned Disciplines &amp; Credit Allocations ({prog.subjects.length} Courses)
                </h3>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                  {prog.subjects.map((subj) => (
                    <div
                      key={subj.code}
                      className="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs hover:border-rose-300 hover:shadow-sm transition-all"
                    >
                      <div className="flex items-center justify-between text-[11px] font-bold text-rose-700 mb-1">
                        <span>{subj.code}</span>
                        <span className="rounded bg-slate-100 text-slate-600 px-1.5 py-0.5">{subj.credits} Credits</span>
                      </div>
                      <div className="text-sm font-bold text-slate-900 leading-snug">{subj.name}</div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* ── COURSE DIRECTORY SEARCH RESULTS (IF SEARCH ACTIVE) ─────── */}
      {searchQuery && (
        <section className="py-12 bg-white border-t border-slate-200">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <h3 className="text-lg font-bold text-slate-900">
              Matching Courses for “{searchQuery}” ({filteredSubjects.length} found)
            </h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              {filteredSubjects.map((s, idx) => (
                <div key={idx} className="rounded-2xl border border-slate-200 p-4 bg-slate-50">
                  <div className="flex justify-between items-center text-xs font-bold text-rose-700 mb-1">
                    <span>{s.code}</span>
                    <span className="text-slate-500">{s.grade}</span>
                  </div>
                  <div className="text-base font-bold text-slate-900">{s.name}</div>
                  <div className="text-xs text-slate-500 mt-2">Chair: {s.leadTeacher}</div>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* ── FACULTIES & SCHOOLS */}
      <section className="py-12 bg-white border-b border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-2xl font-bold">Faculties &amp; Schools</h2>
            <p className="text-sm text-slate-500">Explore our academic leadership and departments</p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {facultyDirectory.map((f) => (
              <div
                key={f.id}
                className="rounded-2xl border border-slate-200 overflow-hidden bg-white"
              >
                <div className="relative h-36 w-full">
                  <Image src={f.avatar} alt={`${f.name} - ${f.department}`} fill className="object-cover" sizes="(min-width:1024px) 33vw, 100vw" />
                </div>
                <div className="p-4">
                  <div className="text-sm font-semibold text-slate-900">{f.name}</div>
                  <div className="text-xs text-slate-500">{f.title}</div>
                  <div className="text-xs text-slate-600 mt-2">Department: {f.department}</div>
                  <div className="mt-3 flex items-center justify-between">
                    <Link href="/faculty" className="text-sm text-rose-600 font-semibold">
                      Explore Faculty
                    </Link>
                    <Link href="#program-explorer" className="text-sm text-slate-500 hover:underline">
                      View Programs <ChevronRight className="inline-block w-4 h-4 ml-1" />
                    </Link>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── RESEARCH & INNOVATION (highlights from notices) */}
      <section className="py-12 bg-slate-50 border-b border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-2xl font-bold">Research &amp; Innovation</h2>
            <p className="text-sm text-slate-500">Research centers, innovation wings, and ongoing projects</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            {universityNotices
              .filter((n) => /innovation|robot|quantum|research/i.test(n.title + ' ' + n.summary))
              .slice(0, 3)
              .map((n) => (
                <article
                  key={n.id}
                  className="rounded-2xl border border-slate-200 bg-white p-5"
                >
                  <div className="text-xs text-rose-600 font-bold mb-1">{n.category}</div>
                  <div className="text-lg font-semibold text-slate-900">{n.title}</div>
                  <div className="text-xs text-slate-500 mt-2">{n.publishedDate}</div>
                  <p className="text-sm text-slate-600 mt-3">{n.summary}</p>
                  <div className="mt-4">
                    <Link href="#" className="text-rose-600 font-semibold text-sm">
                      Learn more
                    </Link>
                  </div>
                </article>
              ))}

            {/* Fallback panel */}
            {universityNotices.filter((n) => /innovation|robot|quantum|research/i.test(n.title + ' ' + n.summary)).length === 0 && (
              <div className="rounded-2xl border border-slate-200 bg-white p-6 text-center">
                <div className="text-lg font-semibold">Research updates coming soon</div>
                <div className="text-sm text-slate-500 mt-2">Visit the Research Hub or contact the Office of Research for details.</div>
              </div>
            )}
          </div>
        </div>
      </section>

      {/* ── ACADEMIC EXCELLENCE (animated stats using provided data) */}
      <section className="py-12 bg-white border-b border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" ref={countersRef}>
            {universityStats.map((s) => (
              <StatCard key={s.label} s={s} />
            ))}
          </div>
        </div>
      </section>

      {/* ── STUDENT ACADEMIC JOURNEY */}
      <section className="py-12 bg-slate-50 border-b border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-2xl font-bold mb-4">Student Academic Journey</h2>
          <div className="hidden lg:flex items-center gap-6">
            {['Discover', 'Apply', 'Enroll', 'Learn', 'Research', 'Graduate'].map((step, idx) => (
              <div key={step} className="flex-1 text-center bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div className="text-3xl font-black text-rose-600">{idx + 1}</div>
                <div className="text-sm font-semibold mt-2">{step}</div>
              </div>
            ))}
          </div>

          <div className="lg:hidden space-y-3">
            {['Discover', 'Apply', 'Enroll', 'Learn', 'Research', 'Graduate'].map((step, idx) => (
              <div key={step} className="rounded-2xl border border-slate-200 bg-white p-4">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold">{idx + 1}</div>
                  <div className="text-sm font-semibold">{step}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── INTERNATIONAL & INDUSTRY CONNECTIONS (if any) */}
      <section className="py-12 bg-white border-b border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-2xl font-bold">International &amp; Industry Connections</h2>
            <p className="text-sm text-slate-500">Partnerships, exchanges, and internships</p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div className="rounded-2xl border border-slate-200 p-6 bg-slate-50">
              <div className="text-sm font-semibold">Exchange &amp; Study Abroad</div>
              <div className="text-xs text-slate-500 mt-2">Learn about our exchange opportunities via the International Office and student portal.</div>
            </div>
            <div className="rounded-2xl border border-slate-200 p-6 bg-slate-50">
              <div className="text-sm font-semibold">Industry Partnerships</div>
              <div className="text-xs text-slate-500 mt-2">Information on corporate collaborations and internship placements is available from Career Services.</div>
            </div>
            <div className="rounded-2xl border border-slate-200 p-6 bg-slate-50">
              <div className="text-sm font-semibold">Professional Development</div>
              <div className="text-xs text-slate-500 mt-2">Short courses and executive education offerings for working professionals.</div>
            </div>
          </div>
        </div>
      </section>

      {/* ── FAQ (accessible accordion) */}
      <section className="py-12 bg-slate-50 border-b border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-2xl font-bold mb-4">Frequently Asked Questions</h2>
          <div className="space-y-3">
            {[
              {
                q: 'What programs are available?',
                a: 'Programs across grade tiers are listed above in the Program Explorer and grade breakdown. Use filters to narrow by subject or department.',
              },
              {
                q: 'How long are programs?',
                a: 'Program durations vary by level; specific course credit and duration details are available on each program card and within the student portal.',
              },
              {
                q: 'What degree levels are offered?',
                a: 'The site presents pathways across foundational, honors, and research levels. For degree/granting information contact Admissions.',
              },
              {
                q: 'Where can I find the academic calendar?',
                a: 'Official semester dates, registration, and examination periods are published via the Student Portal and Registrar notices (see Notices).',
              },
            ].map((item, idx) => (
              <AccordionItem key={idx} question={item.q} answer={item.a} />
            ))}
          </div>
        </div>
      </section>

      {/* ── PREMIUM FINAL CTA */}
      <section className="py-16 bg-rose-600 text-white">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl font-black mb-3">Find the Program That Builds Your Future.</h2>
          <p className="text-sm opacity-90 mb-6">Discover rigorous curricula, world-class faculty, and research opportunities tailored to ambitious scholars.</p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <button onClick={scrollToExplorer} className="px-6 py-3 rounded-xl bg-white text-rose-600 font-bold">Explore Programs</button>
            <Link href="/admissions" className="px-6 py-3 rounded-xl border border-white/30 text-white">Apply Now</Link>
            <Link href="/contact" className="px-6 py-3 rounded-xl text-white bg-white/10">Contact Admissions</Link>
          </div>
        </div>
      </section>
    </PublicLayout>
  )
}

// Small accessible accordion component used above
function AccordionItem({ question, answer }: { question: string; answer: string }) {
  const [open, setOpen] = useState(false)
  return (
    <div className="rounded-xl border border-slate-200 bg-white overflow-hidden">
      <button
        className="w-full text-left p-4 flex items-center justify-between"
        aria-expanded={open}
        onClick={() => setOpen((v) => !v)}
      >
        <span className="text-sm font-semibold text-slate-900">{question}</span>
        <span className="text-slate-500">{open ? <ChevronDown className="w-4 h-4" /> : <ChevronRight className="w-4 h-4" />}</span>
      </button>
      <div className={`px-4 pb-4 text-sm text-slate-600 ${open ? 'block' : 'hidden'}`}>{answer}</div>
    </div>
  )
}




