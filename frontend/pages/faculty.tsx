import { useState } from 'react'
import Link from 'next/link'
import {
  GraduationCap,
  Sparkles,
  Search,
  Mail,
  Phone,
  BookOpen,
  Award,
  Users,
  Filter,
  CheckCircle2,
} from 'lucide-react'
import PublicLayout from '../src/layouts/PublicLayout'
import { facultyDirectory } from '../src/data/public-site'

export default function FacultyPage() {
  const [searchQuery, setSearchQuery] = useState('')
  const [selectedDept, setSelectedDept] = useState('All')

  const departments = [
    'All',
    'Biological & Life Sciences',
    'Mathematics & Computing',
    'Chemistry & Physical Sciences',
    'Humanities & Social Sciences',
    'Physics & Space Sciences',
    'Computer Science',
  ]

  const filteredFaculty = facultyDirectory.filter((f) => {
    const matchesSearch =
      f.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      f.specialization.toLowerCase().includes(searchQuery.toLowerCase()) ||
      f.qualification.toLowerCase().includes(searchQuery.toLowerCase()) ||
      f.department.toLowerCase().includes(searchQuery.toLowerCase())

    const matchesDept = selectedDept === 'All' || f.department === selectedDept
    return matchesSearch && matchesDept
  })

  return (
    <PublicLayout>
      {/* ── HEADER BANNER ───────────────────────────────────────────── */}
      <section className="bg-slate-950 text-white py-16 lg:py-24 border-b border-slate-800 relative overflow-hidden">
        <div className="absolute top-0 right-1/4 w-96 h-96 bg-teal-600/20 rounded-full blur-3xl pointer-events-none" />
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
          <div className="max-w-3xl">
            <span className="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-3 py-1 text-xs font-semibold text-rose-400 border border-rose-500/20 mb-4">
              <Sparkles className="h-3.5 w-3.5" /> Scholars, Mentors &amp; Chairs
            </span>
            <h1 className="text-4xl sm:text-5xl font-black font-serif tracking-tight text-white leading-tight">
              Faculty Directory
            </h1>
            <p className="mt-4 text-lg text-slate-300 leading-relaxed">
              Meet our distinguished faculty of 140+ researchers, authors, and educators dedicated to inspiring analytical rigor and character in every scholar.
            </p>
          </div>
        </div>
      </section>

      {/* ── SEARCH & FILTER STRIP ───────────────────────────────────── */}
      <section className="py-6 bg-white border-b border-slate-200 sticky top-20 z-30 shadow-xs">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row items-center justify-between gap-4">
            {/* Search Input */}
            <div className="relative w-full md:w-96">
              <Search className="absolute left-3.5 top-3 h-4 w-4 text-slate-400" />
              <input
                type="text"
                placeholder="Search by name, specialization, degree..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full h-10 pl-10 pr-4 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-900 outline-none focus:border-rose-500 focus:bg-white transition-all shadow-2xs"
              />
            </div>

            {/* Department Dropdown / Filter */}
            <div className="flex items-center gap-2 w-full md:w-auto">
              <Filter className="h-4 w-4 text-slate-400 shrink-0" />
              <select
                value={selectedDept}
                onChange={(e) => setSelectedDept(e.target.value)}
                className="h-10 px-3.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-800 outline-none focus:border-rose-500 focus:bg-white transition-all w-full md:w-auto"
              >
                {departments.map((d) => (
                  <option key={d} value={d}>
                    {d === 'All' ? 'All Academic Departments' : d}
                  </option>
                ))}
              </select>
            </div>
          </div>
        </div>
      </section>

      {/* ── FACULTY CARDS GRID ──────────────────────────────────────── */}
      <section className="py-16 bg-slate-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {filteredFaculty.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {filteredFaculty.map((f) => (
                <div
                  key={f.id}
                  className="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm hover:shadow-xl hover:border-slate-300 transition-all flex flex-col justify-between"
                >
                  <div className="space-y-4">
                    <div className="flex items-center gap-4">
                      <img
                        src={f.avatar}
                        alt={f.name}
                        className="h-16 w-16 rounded-2xl object-cover border-2 border-rose-500/30 shadow-md shrink-0"
                      />
                      <div className="min-w-0">
                        <h3 className="text-lg font-bold text-slate-950 truncate">{f.name}</h3>
                        <div className="text-xs font-semibold text-rose-700 leading-tight line-clamp-1">{f.title}</div>
                        <div className="text-[11px] text-slate-500 mt-0.5 truncate">{f.department}</div>
                      </div>
                    </div>

                    <div className="rounded-xl bg-rose-50 border border-rose-100 p-2.5 text-xs text-rose-800 font-semibold">
                      Specialization: {f.specialization}
                    </div>

                    <p className="text-xs text-slate-600 leading-relaxed line-clamp-3">
                      {f.bio}
                    </p>

                    <div className="space-y-1.5 text-xs text-slate-500 pt-2 border-t border-slate-100">
                      <div>
                        <strong>Qualifications:</strong> {f.qualification}
                      </div>
                      {f.classTeacherOf && (
                        <div>
                          <strong>Class Lead:</strong>{' '}
                          <span className="font-semibold text-emerald-700">{f.classTeacherOf}</span>
                        </div>
                      )}
                    </div>
                  </div>

                  <div className="pt-6 border-t border-slate-100 flex items-center justify-between mt-6">
                    <a
                      href={`mailto:${f.email}`}
                      className="inline-flex items-center gap-1.5 text-xs font-bold text-rose-700 hover:text-rose-800"
                    >
                      <Mail className="h-3.5 w-3.5" />
                      <span>{f.email}</span>
                    </a>
                    <span className="text-[11px] text-slate-400">{f.phone}</span>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-20 bg-white rounded-3xl border border-slate-200 p-12 space-y-4">
              <Users className="h-12 w-12 text-slate-300 mx-auto" />
              <h3 className="text-xl font-bold text-slate-800">No faculty members found</h3>
              <p className="text-sm text-slate-500">Try adjusting your search criteria or department filter.</p>
              <button
                type="button"
                onClick={() => {
                  setSearchQuery('')
                  setSelectedDept('All')
                }}
                className="px-4 py-2 rounded-xl bg-rose-600 text-white text-xs font-bold"
              >
                Reset Filters
              </button>
            </div>
          )}
        </div>
      </section>
    </PublicLayout>
  )
}
