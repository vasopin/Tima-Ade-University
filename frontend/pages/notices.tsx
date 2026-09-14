import { useState } from 'react'
import Link from 'next/link'
import {
  Sparkles,
  Search,
  Calendar,
  ChevronRight,
  Filter,
  FileText,
  Bell,
  ArrowRight,
  Download,
} from 'lucide-react'
import PublicLayout from '../src/layouts/PublicLayout'
import { universityNotices, UniversityNotice } from '../src/data/public-site'

export default function NoticesPage() {
  const [selectedCategory, setSelectedCategory] = useState<string>('All')
  const [searchQuery, setSearchQuery] = useState('')

  const categories = ['All', 'Academic', 'Exams', 'Events', 'Admissions', 'General']

  const filteredNotices = universityNotices.filter((n) => {
    const matchesCategory = selectedCategory === 'All' || n.category === selectedCategory
    const matchesSearch =
      n.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
      n.summary.toLowerCase().includes(searchQuery.toLowerCase()) ||
      n.author.toLowerCase().includes(searchQuery.toLowerCase())
    return matchesCategory && matchesSearch
  })

  return (
    <PublicLayout>
      {/* ── HEADER BANNER ───────────────────────────────────────────── */}
      <section className="bg-slate-950 text-white py-16 lg:py-24 border-b border-slate-800 relative overflow-hidden">
        <div className="absolute top-0 right-1/4 w-96 h-96 bg-amber-600/20 rounded-full blur-3xl pointer-events-none" />
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
          <div className="max-w-3xl">
            <span className="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-3 py-1 text-xs font-semibold text-rose-400 border border-rose-500/20 mb-4">
              <Sparkles className="h-3.5 w-3.5" /> Campus Bulletins &amp; Circulars
            </span>
            <h1 className="text-4xl sm:text-5xl font-black font-serif tracking-tight text-white leading-tight">
              Official Noticeboard
            </h1>
            <p className="mt-4 text-lg text-slate-300 leading-relaxed">
              Stay informed with latest university announcements, examination schedules, academic calendars, and institutional circulars.
            </p>
          </div>
        </div>
      </section>

      {/* ── SEARCH & CATEGORY FILTER ────────────────────────────────── */}
      <section className="py-6 bg-white border-b border-slate-200 sticky top-20 z-30 shadow-xs">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row items-center justify-between gap-4">
            {/* Search Input */}
            <div className="relative w-full md:w-96">
              <Search className="absolute left-3.5 top-3 h-4 w-4 text-slate-400" />
              <input
                type="text"
                placeholder="Search circulars, exams, events..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full h-10 pl-10 pr-4 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-900 outline-none focus:border-rose-500 focus:bg-white transition-all shadow-2xs"
              />
            </div>

            {/* Category Filter Pills */}
            <div className="flex flex-wrap gap-1.5 w-full md:w-auto">
              {categories.map((cat) => (
                <button
                  key={cat}
                  type="button"
                  onClick={() => setSelectedCategory(cat)}
                  className={[
                    'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all',
                    selectedCategory === cat
                      ? 'bg-rose-600 text-white shadow-sm'
                      : 'bg-slate-100 text-slate-700 hover:bg-slate-200',
                  ].join(' ')}
                >
                  {cat}
                </button>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* ── NOTICES GRID ────────────────────────────────────────────── */}
      <section className="py-16 bg-slate-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {filteredNotices.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {filteredNotices.map((notice) => (
                <div
                  key={notice.id}
                  className="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm hover:shadow-xl hover:border-slate-300 transition-all flex flex-col justify-between group"
                >
                  <div>
                    <div className="flex items-center justify-between mb-3">
                      <span className="rounded-lg bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1 text-xs font-bold">
                        {notice.category}
                      </span>
                      <span className="text-xs text-slate-400 font-medium flex items-center gap-1">
                        <Calendar className="h-3.5 w-3.5" />
                        {notice.publishedDate}
                      </span>
                    </div>

                    <h3 className="text-lg font-bold text-slate-900 mb-2 leading-snug group-hover:text-rose-700 transition-colors">
                      <Link href={`/notices/${notice.id}`}>{notice.title}</Link>
                    </h3>

                    <p className="text-xs text-slate-600 leading-relaxed mb-4 line-clamp-3">
                      {notice.summary}
                    </p>
                  </div>

                  <div className="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span className="text-[11px] text-slate-400 font-medium truncate max-w-[180px]">
                      {notice.author}
                    </span>
                    <Link
                      href={`/notices/${notice.id}`}
                      className="inline-flex items-center gap-1 text-xs font-bold text-rose-700 hover:text-rose-800"
                    >
                      <span>Read Bulletin</span>
                      <ChevronRight className="h-3.5 w-3.5" />
                    </Link>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-20 bg-white rounded-3xl border border-slate-200 p-12 space-y-4">
              <Bell className="h-12 w-12 text-slate-300 mx-auto" />
              <h3 className="text-xl font-bold text-slate-800">No circulars found</h3>
              <p className="text-sm text-slate-500">No bulletins match your current category filter or search query.</p>
              <button
                type="button"
                onClick={() => {
                  setSelectedCategory('All')
                  setSearchQuery('')
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
