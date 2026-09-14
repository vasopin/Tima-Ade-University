import { useRouter } from 'next/router'
import Link from 'next/link'
import {
  ArrowLeft,
  Calendar,
  User,
  Share2,
  Download,
  FileText,
  Bell,
  ChevronRight,
  Sparkles,
} from 'lucide-react'
import PublicLayout from '../../src/layouts/PublicLayout'
import { universityNotices } from '../../src/data/public-site'

export default function SingleNoticePage() {
  const router = useRouter()
  const { id } = router.query

  const notice =
    universityNotices.find((n) => n.id === id) ||
    universityNotices.find((n) => n.id === 'notice-1') ||
    universityNotices[0]

  const relatedNotices = universityNotices.filter((n) => n.id !== notice.id).slice(0, 3)

  return (
    <PublicLayout>
      <div className="bg-slate-50 py-12">
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          {/* Back link */}
          <div>
            <Link
              href="/notices"
              className="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-rose-700 transition-colors"
            >
              <ArrowLeft className="h-4 w-4" />
              <span>Back to All Circulars</span>
            </Link>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {/* Main Circular Content */}
            <div className="lg:col-span-8 space-y-6">
              <article className="rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 shadow-sm space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                  <span className="rounded-lg bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1 text-xs font-bold">
                    {notice.category} Bulletin
                  </span>
                  <div className="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
                    <Calendar className="h-3.5 w-3.5" />
                    <span>{notice.publishedDate}</span>
                  </div>
                </div>

                <h1 className="text-2xl sm:text-3xl font-black font-serif text-slate-950 leading-snug">
                  {notice.title}
                </h1>

                <div className="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-600">
                  <div className="h-8 w-8 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                    MC
                  </div>
                  <div>
                    <div className="font-bold text-slate-900">{notice.author}</div>
                    <div className="text-[11px] text-slate-400">Official Directorate Notice</div>
                  </div>
                </div>

                <div className="prose prose-slate max-w-none text-sm text-slate-700 leading-relaxed space-y-4 pt-2">
                  <p className="font-semibold text-base text-slate-900 leading-normal">
                    {notice.summary}
                  </p>
                  <p>{notice.content}</p>
                  <p>
                    For inquiries regarding this bulletin, authorized students, parents, and faculty members may consult their respective dashboard notification feeds or contact the Registrar Desk at{' '}
                    <a href="mailto:registrar@timaade.edu" className="text-rose-700 font-bold hover:underline">
                      registrar@timaade.edu
                    </a>
                    .
                  </p>
                </div>

                {/* Attachment / Download simulation */}
                <div className="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                  <div className="flex items-center gap-3">
                    <div className="h-10 w-10 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center border border-rose-200 shrink-0">
                      <FileText className="h-5 w-5" />
                    </div>
                    <div>
                      <div className="text-xs font-bold text-slate-900">Official_Circular_Docket.pdf</div>
                      <div className="text-[11px] text-slate-400">PDF Document · 1.4 MB</div>
                    </div>
                  </div>
                  <button
                    type="button"
                    onClick={() => alert('Downloading official circular document...')}
                    className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors"
                  >
                    <Download className="h-3.5 w-3.5 text-rose-400" />
                    <span>Download PDF</span>
                  </button>
                </div>
              </article>
            </div>

            {/* Right Column — Related Circulars */}
            <div className="lg:col-span-4 space-y-6">
              <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h3 className="text-sm font-bold text-slate-900 uppercase tracking-wider">
                  Related Circulars
                </h3>
                <div className="space-y-3">
                  {relatedNotices.map((rn) => (
                    <Link
                      key={rn.id}
                      href={`/notices/${rn.id}`}
                      className="block p-3.5 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-rose-50 hover:border-rose-200 transition-colors group"
                    >
                      <div className="flex items-center justify-between text-[10px] text-slate-400 font-bold mb-1">
                        <span className="text-rose-700 uppercase">{rn.category}</span>
                        <span>{rn.publishedDate}</span>
                      </div>
                      <h4 className="text-xs font-bold text-slate-900 group-hover:text-rose-700 leading-snug line-clamp-2">
                        {rn.title}
                      </h4>
                    </Link>
                  ))}
                </div>

                <div className="pt-2">
                  <Link
                    href="/notices"
                    className="block text-center py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-800"
                  >
                    View Noticeboard Archive
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </PublicLayout>
  )
}
