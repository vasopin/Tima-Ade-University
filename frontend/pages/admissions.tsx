import { useState } from 'react'
import Link from 'next/link'
import {
  Sparkles,
  CheckCircle2,
  Send,
  Calendar,
  FileText,
  HelpCircle,
  Clock,
  ArrowRight,
  ShieldCheck,
  Building2,
  GraduationCap,
  Users,
} from 'lucide-react'
import PublicLayout from '../src/layouts/PublicLayout'

export default function AdmissionsPage() {
  const [formData, setFormData] = useState({
    studentName: '',
    dob: '',
    gender: 'Male',
    grade: 'Grade 9',
    parentName: '',
    parentEmail: '',
    parentPhone: '',
    address: '',
    prevSchool: '',
    prevGpa: '',
    interests: 'STEM Honors',
    notes: '',
  })

  const [isSubmitting, setIsSubmitting] = useState(false)
  const [submittedId, setSubmittedId] = useState<string | null>(null)

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    setIsSubmitting(true)

    // Simulate online processing
    setTimeout(() => {
      const randomRef = 'TAU-ADM-2026-' + Math.floor(1000 + Math.random() * 9000)
      setSubmittedId(randomRef)
      setIsSubmitting(false)
    }, 800)
  }

  return (
    <PublicLayout>
      {/* ── HEADER BANNER ───────────────────────────────────────────── */}
      <section className="bg-slate-950 text-white py-16 lg:py-24 border-b border-slate-800 relative overflow-hidden">
        <div className="absolute top-0 right-1/4 w-96 h-96 bg-rose-600/20 rounded-full blur-3xl pointer-events-none" />
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
          <div className="max-w-3xl">
            <span className="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-3 py-1 text-xs font-semibold text-rose-400 border border-rose-500/20 mb-4">
              <Sparkles className="h-3.5 w-3.5" /> 2026-2027 Academic Session
            </span>
            <h1 className="text-4xl sm:text-5xl font-black font-serif tracking-tight text-white leading-tight">
              Admissions &amp; Enrollment
            </h1>
            <p className="mt-4 text-lg text-slate-300 leading-relaxed">
              Join our diverse, ambitious community of scholars. Learn about admission criteria, key deadlines, and submit your online application.
            </p>
          </div>
        </div>
      </section>

      {/* ── ADMISSIONS ROADMAP & FORM SECTION ──────────────────────── */}
      <section className="py-20 bg-slate-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
            {/* Left Column — Process & Checklist */}
            <div className="lg:col-span-5 space-y-8">
              <div>
                <span className="text-xs font-bold uppercase tracking-widest text-rose-700">
                  Step-by-Step Roadmap
                </span>
                <h2 className="text-2xl sm:text-3xl font-black font-serif text-slate-950 tracking-tight mt-1">
                  How to Apply
                </h2>
              </div>

              {/* Steps */}
              <div className="space-y-6">
                {[
                  {
                    step: '01',
                    title: 'Submit Online Application',
                    desc: 'Complete the student inquiry form with academic background, contact details, and grade level selection.',
                  },
                  {
                    step: '02',
                    title: 'Diagnostic Assessment & Campus Visit',
                    desc: 'Prospective scholars participate in an interactive problem-solving diagnostic and family campus walk with faculty chairs.',
                  },
                  {
                    step: '03',
                    title: 'Admission Offer & Portal Activation',
                    desc: 'Accepted scholars receive an official admission docket and instant access credentials to their Student & Parent portals.',
                  },
                ].map((s) => (
                  <div key={s.step} className="flex gap-4 p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-700 font-serif font-black text-lg border border-rose-200">
                      {s.step}
                    </div>
                    <div>
                      <h3 className="text-base font-bold text-slate-900">{s.title}</h3>
                      <p className="text-xs text-slate-600 mt-1 leading-relaxed">{s.desc}</p>
                    </div>
                  </div>
                ))}
              </div>

              {/* Required Documents Card */}
              <div className="rounded-3xl border border-slate-200 bg-white p-7 shadow-xs space-y-4">
                <div className="flex items-center gap-2 text-rose-700 font-bold text-sm">
                  <FileText className="h-4 w-4" />
                  <span>Required Checklist for Registration</span>
                </div>
                <ul className="space-y-2 text-xs text-slate-600">
                  <li className="flex items-center gap-2">
                    <CheckCircle2 className="h-4 w-4 text-emerald-600 shrink-0" />
                    <span>Official academic transcripts from previous 2 school years</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <CheckCircle2 className="h-4 w-4 text-emerald-600 shrink-0" />
                    <span>Copy of student birth certificate / national ID</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <CheckCircle2 className="h-4 w-4 text-emerald-600 shrink-0" />
                    <span>Two recent passport-sized student photographs</span>
                  </li>
                  <li className="flex items-center gap-2">
                    <CheckCircle2 className="h-4 w-4 text-emerald-600 shrink-0" />
                    <span>One recommendation letter from a former teacher or counselor</span>
                  </li>
                </ul>
              </div>
            </div>

            {/* Right Column — Online Form */}
            <div className="lg:col-span-7">
              <div className="rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 shadow-lg">
                {submittedId ? (
                  <div className="text-center py-12 space-y-6">
                    <div className="h-16 w-16 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto">
                      <CheckCircle2 className="h-10 w-10" />
                    </div>
                    <div className="space-y-2">
                      <h3 className="text-2xl font-black font-serif text-slate-900">
                        Application Received Successfully!
                      </h3>
                      <p className="text-sm text-slate-600 max-w-md mx-auto">
                        Thank you for applying to Tima-Ade University. Your application has been logged under reference number:
                      </p>
                      <div className="inline-block rounded-xl bg-slate-900 text-rose-400 font-mono font-bold text-lg px-4 py-2 border border-slate-800">
                        {submittedId}
                      </div>
                    </div>

                    <div className="rounded-2xl bg-slate-50 border border-slate-100 p-5 text-left text-xs text-slate-600 space-y-2">
                      <div>
                        <strong>Applicant:</strong> {formData.studentName} ({formData.grade})
                      </div>
                      <div>
                        <strong>Parent Contact:</strong> {formData.parentEmail} · {formData.parentPhone}
                      </div>
                      <div>
                        <strong>Next Step:</strong> Our Admissions Directorate will contact you within 2 business days regarding your campus assessment appointment.
                      </div>
                    </div>

                    <div className="pt-2 flex justify-center gap-3">
                      <button
                        type="button"
                        onClick={() => setSubmittedId(null)}
                        className="px-6 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50"
                      >
                        Submit Another Form
                      </button>
                      <Link
                        href="/"
                        className="px-6 py-2.5 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700"
                      >
                        Return to Homepage
                      </Link>
                    </div>
                  </div>
                ) : (
                  <form onSubmit={handleSubmit} className="space-y-6">
                    <div>
                      <h3 className="text-2xl font-black font-serif text-slate-900">
                        Online Admission Inquiry Form
                      </h3>
                      <p className="text-xs text-slate-500 mt-1">
                        Fill in all required fields to commence the 2026-2027 enrollment procedure.
                      </p>
                    </div>

                    {/* Student Info */}
                    <div className="space-y-4 pt-2">
                      <div className="text-xs font-bold uppercase tracking-wider text-rose-700 border-b border-slate-100 pb-1">
                        1. Scholar Information
                      </div>

                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                          <label className="block text-xs font-bold text-slate-700 mb-1">
                            Student Full Name *
                          </label>
                          <input
                            type="text"
                            required
                            placeholder="e.g. Eleanor Vance"
                            value={formData.studentName}
                            onChange={(e) => setFormData({ ...formData, studentName: e.target.value })}
                            className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-bold text-slate-700 mb-1">
                            Target Grade Level *
                          </label>
                          <select
                            value={formData.grade}
                            onChange={(e) => setFormData({ ...formData, grade: e.target.value })}
                            className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                          >
                            <option value="Grade 9">Grade 9 (Freshman Foundation)</option>
                            <option value="Grade 10">Grade 10 (Sophomore STEM)</option>
                            <option value="Grade 11">Grade 11 (Junior Pre-College)</option>
                            <option value="Grade 12">Grade 12 (Senior Honors Capstone)</option>
                          </select>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                          <label className="block text-xs font-bold text-slate-700 mb-1">
                            Date of Birth *
                          </label>
                          <input
                            type="date"
                            required
                            value={formData.dob}
                            onChange={(e) => setFormData({ ...formData, dob: e.target.value })}
                            className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-bold text-slate-700 mb-1">
                            Gender *
                          </label>
                          <select
                            value={formData.gender}
                            onChange={(e) => setFormData({ ...formData, gender: e.target.value })}
                            className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                          >
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    {/* Parent Info */}
                    <div className="space-y-4 pt-2">
                      <div className="text-xs font-bold uppercase tracking-wider text-rose-700 border-b border-slate-100 pb-1">
                        2. Parent / Guardian Contact
                      </div>

                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                          <label className="block text-xs font-bold text-slate-700 mb-1">
                            Parent / Guardian Name *
                          </label>
                          <input
                            type="text"
                            required
                            placeholder="e.g. Dr. Robert Vance"
                            value={formData.parentName}
                            onChange={(e) => setFormData({ ...formData, parentName: e.target.value })}
                            className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-bold text-slate-700 mb-1">
                            Parent Email Address *
                          </label>
                          <input
                            type="email"
                            required
                            placeholder="parent@example.com"
                            value={formData.parentEmail}
                            onChange={(e) => setFormData({ ...formData, parentEmail: e.target.value })}
                            className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                          />
                        </div>
                      </div>

                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                          <label className="block text-xs font-bold text-slate-700 mb-1">
                            Parent Phone Number *
                          </label>
                          <input
                            type="tel"
                            required
                            placeholder="+1 (555) 000-0000"
                            value={formData.parentPhone}
                            onChange={(e) => setFormData({ ...formData, parentPhone: e.target.value })}
                            className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-bold text-slate-700 mb-1">
                            Previous School &amp; GPA
                          </label>
                          <input
                            type="text"
                            placeholder="e.g. Cambridge Academy (3.9 GPA)"
                            value={formData.prevSchool}
                            onChange={(e) => setFormData({ ...formData, prevSchool: e.target.value })}
                            className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                          />
                        </div>
                      </div>
                    </div>

                    {/* Submit button */}
                    <div className="pt-4">
                      <button
                        type="submit"
                        disabled={isSubmitting}
                        className="w-full py-3.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-bold text-sm shadow-md shadow-rose-900/30 transition-all flex items-center justify-center gap-2 cursor-pointer"
                      >
                        {isSubmitting ? (
                          <span>Processing Application...</span>
                        ) : (
                          <>
                            <Send className="h-4 w-4" />
                            <span>Submit Formal Admission Inquiry</span>
                          </>
                        )}
                      </button>
                    </div>
                  </form>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>
    </PublicLayout>
  )
}
