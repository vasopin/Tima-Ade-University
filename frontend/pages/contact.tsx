import { useState } from 'react'
import Link from 'next/link'
import {
  MapPin,
  Phone,
  Mail,
  Clock,
  Send,
  Sparkles,
  CheckCircle2,
  Building,
  HelpCircle,
} from 'lucide-react'
import PublicLayout from '../src/layouts/PublicLayout'

export default function ContactPage() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    subject: 'Admissions Inquiry',
    message: '',
  })
  const [submitted, setSubmitted] = useState(false)
  const [isSubmitting, setIsSubmitting] = useState(false)

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    setIsSubmitting(true)
    setTimeout(() => {
      setSubmitted(true)
      setIsSubmitting(false)
    }, 600)
  }

  return (
    <PublicLayout>
      {/* ── HEADER BANNER ───────────────────────────────────────────── */}
      <section className="bg-slate-950 text-white py-16 lg:py-24 border-b border-slate-800 relative overflow-hidden">
        <div className="absolute top-0 right-1/4 w-96 h-96 bg-rose-600/20 rounded-full blur-3xl pointer-events-none" />
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
          <div className="max-w-3xl">
            <span className="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-3 py-1 text-xs font-semibold text-rose-400 border border-rose-500/20 mb-4">
              <Sparkles className="h-3.5 w-3.5" /> Connect With Our Team
            </span>
            <h1 className="text-4xl sm:text-5xl font-black font-serif tracking-tight text-white leading-tight">
              Contact &amp; Campus Location
            </h1>
            <p className="mt-4 text-lg text-slate-300 leading-relaxed">
              We are here to answer your questions regarding admissions, academic curriculum, campus tours, and enrollment.
            </p>
          </div>
        </div>
      </section>

      {/* ── CONTACT GRID SECTION ────────────────────────────────────── */}
      <section className="py-20 bg-slate-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
            {/* Left Column — Contact Details */}
            <div className="lg:col-span-5 space-y-6">
              <div>
                <span className="text-xs font-bold uppercase tracking-widest text-rose-700">
                  Campus Directory
                </span>
                <h2 className="text-2xl sm:text-3xl font-black font-serif text-slate-950 tracking-tight mt-1">
                  Get in Touch
                </h2>
              </div>

              <div className="space-y-4">
                <div className="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-start gap-4">
                  <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                    <MapPin className="h-5 w-5" />
                  </div>
                  <div>
                    <h3 className="text-sm font-bold text-slate-900">Campus Address</h3>
                    <p className="text-xs text-slate-600 mt-0.5 leading-relaxed">
                      Gabiley, Somaliland
                    </p>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-start gap-4">
                  <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                    <Phone className="h-5 w-5" />
                  </div>
                  <div>
                    <h3 className="text-sm font-bold text-slate-900">Telephone Lines</h3>
                    <p className="text-xs text-slate-600 mt-0.5 leading-relaxed">
                      Main Switchboard: +1 (555) 234-5678<br />
                      Admissions Desk: +1 (555) 234-5679
                    </p>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-start gap-4">
                  <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                    <Mail className="h-5 w-5" />
                  </div>
                  <div>
                    <h3 className="text-sm font-bold text-slate-900">Email Inquiries</h3>
                    <p className="text-xs text-slate-600 mt-0.5 leading-relaxed">
                      General: info@timaade.edu<br />
                      Admissions: admissions@timaade.edu<br />
                      Registrar: registrar@timaade.edu
                    </p>
                  </div>
                </div>

                <div className="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-start gap-4">
                  <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                    <Clock className="h-5 w-5" />
                  </div>
                  <div>
                    <h3 className="text-sm font-bold text-slate-900">Administration Hours</h3>
                    <p className="text-xs text-slate-600 mt-0.5 leading-relaxed">
                      Monday – Friday: 8:00 AM – 5:00 PM EST<br />
                      Saturday: 9:00 AM – 1:00 PM (By Appointment)
                    </p>
                  </div>
                </div>
              </div>
            </div>

            {/* Right Column — Message Form */}
            <div className="lg:col-span-7">
              <div className="rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 shadow-lg">
                {submitted ? (
                  <div className="text-center py-12 space-y-4">
                    <div className="h-16 w-16 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto">
                      <CheckCircle2 className="h-10 w-10" />
                    </div>
                    <h3 className="text-2xl font-black font-serif text-slate-900">
                      Message Sent Successfully!
                    </h3>
                    <p className="text-sm text-slate-600 max-w-md mx-auto">
                      Thank you for contacting Tima-Ade University. An administrative officer will reply to <strong>{formData.email}</strong> within 24 business hours.
                    </p>
                    <div className="pt-4">
                      <button
                        type="button"
                        onClick={() => {
                          setSubmitted(false)
                          setFormData({ name: '', email: '', phone: '', subject: 'Admissions Inquiry', message: '' })
                        }}
                        className="px-6 py-2.5 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700"
                      >
                        Send Another Inquiry
                      </button>
                    </div>
                  </div>
                ) : (
                  <form onSubmit={handleSubmit} className="space-y-5">
                    <div>
                      <h3 className="text-2xl font-black font-serif text-slate-900">
                        Send an Official Message
                      </h3>
                      <p className="text-xs text-slate-500 mt-1">
                        Please fill out the form below and our admissions or administrative team will assist you.
                      </p>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                      <div>
                        <label className="block text-xs font-bold text-slate-700 mb-1">
                          Your Full Name *
                        </label>
                        <input
                          type="text"
                          required
                          placeholder="e.g. John Doe"
                          value={formData.name}
                          onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                          className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                        />
                      </div>
                      <div>
                        <label className="block text-xs font-bold text-slate-700 mb-1">
                          Email Address *
                        </label>
                        <input
                          type="email"
                          required
                          placeholder="john@example.com"
                          value={formData.email}
                          onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                          className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                        />
                      </div>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div>
                        <label className="block text-xs font-bold text-slate-700 mb-1">
                          Phone Number
                        </label>
                        <input
                          type="tel"
                          placeholder="+1 (555) 000-0000"
                          value={formData.phone}
                          onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                          className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                        />
                      </div>
                      <div>
                        <label className="block text-xs font-bold text-slate-700 mb-1">
                          Inquiry Department *
                        </label>
                        <select
                          value={formData.subject}
                          onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                          className="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                        >
                          <option value="Admissions Inquiry">Admissions Directorate</option>
                          <option value="Academic Curriculum">Academic Deans Office</option>
                          <option value="Campus Tour Request">Campus Visit Coordination</option>
                          <option value="Tuition & Fees">Bursary &amp; Tuition Desk</option>
                          <option value="General Question">General Administration</option>
                        </select>
                      </div>
                    </div>

                    <div>
                      <label className="block text-xs font-bold text-slate-700 mb-1">
                        Message / Question *
                      </label>
                      <textarea
                        required
                        rows={4}
                        placeholder="Please write your inquiry or message in detail..."
                        value={formData.message}
                        onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                        className="w-full p-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 outline-none focus:border-rose-500 focus:bg-slate-50"
                      />
                    </div>

                    <button
                      type="submit"
                      disabled={isSubmitting}
                      className="w-full py-3.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-bold text-sm shadow-md shadow-rose-900/30 transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                      {isSubmitting ? (
                        <span>Sending Message...</span>
                      ) : (
                        <>
                          <Send className="h-4 w-4" />
                          <span>Send Message to Administration</span>
                        </>
                      )}
                    </button>
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
