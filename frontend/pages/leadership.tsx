'use client'

import { useRef, useEffect } from 'react'
import Link from 'next/link'
import { ChevronRight, Users, Award, ArrowRight } from 'lucide-react'
import PublicLayout from '../src/layouts/PublicLayout'

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
      if (entries[0].isIntersecting) {
        el.classList.remove('opacity-0', 'translate-y-4')
        el.classList.add('opacity-100', 'translate-y-0')
        obs.unobserve(el)
      }
    })
    obs.observe(el)
    return () => obs.disconnect()
  }, [])
  return (
    <div ref={ref} className={`transition-all duration-500 ${className}`}>
      {children}
    </div>
  )
}

const leaders = [
  {
    id: 1,
    name: 'Dr. Hassan Mohamed',
    title: 'President & Vice Chancellor',
    bio: 'Leading vision for academic excellence with over 30 years in higher education.',
    photo: 'HM',
  },
  {
    id: 2,
    name: 'Prof. Amina Ahmed',
    title: 'Provost & Academic Affairs',
    bio: 'Overseeing curriculum development and academic standards across all programs.',
    photo: 'AA',
  },
  {
    id: 3,
    name: 'Dr. Mohamed Ibrahim',
    title: 'Vice Chancellor, Research & Innovation',
    bio: 'Driving research initiatives and fostering innovation across the university.',
    photo: 'MI',
  },
  {
    id: 4,
    name: 'Ms. Fatima Hassan',
    title: 'Vice Chancellor, Student Affairs',
    bio: 'Supporting student success and creating a vibrant campus community.',
    photo: 'FH',
  },
  {
    id: 5,
    name: 'Dr. Abdi Mohamed',
    title: 'Vice Chancellor, Administration',
    bio: 'Managing operational excellence and institutional resources.',
    photo: 'AM',
  },
  {
    id: 6,
    name: 'Prof. Zainab Hassan',
    title: 'Dean, School of Technology',
    bio: 'Leading innovation in technology education and digital transformation.',
    photo: 'ZH',
  },
]

export default function LeadershipPage() {
  return (
    <PublicLayout title="Leadership" description="Meet the visionary leaders guiding Tima-Ade University toward excellence and innovation.">
      {/* Hero Section */}
      <div className="relative min-h-80 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 pt-24 text-white">
        <div className="container-xl py-16">
          <Reveal>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">University Leadership</h1>
          </Reveal>
          <Reveal className="mt-4">
            <p className="text-lg text-primary-100 max-w-2xl">
              Visionary leaders dedicated to advancing academic excellence and institutional growth.
            </p>
          </Reveal>
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="container-xl py-4 text-sm text-slate-600 flex items-center gap-2">
        <Link href="/" className="hover:text-primary-700 transition">Home</Link>
        <ChevronRight size={16} />
        <span className="text-primary-700 font-medium">Leadership</span>
      </div>

      {/* President's Message */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <Reveal>
              <div className="bg-gradient-to-br from-primary-100 to-primary-200 rounded-2xl p-8 border border-primary-300 aspect-square flex items-center justify-center">
                <div className="text-6xl font-bold text-primary-400">HM</div>
              </div>
            </Reveal>

            <Reveal>
              <div>
                <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-100 text-primary-700 text-xs font-semibold mb-4">
                  <Award size={14} />
                  President & Vice Chancellor
                </div>
                <h2 className="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Dr. Hassan Mohamed</h2>
                <p className="text-lg text-slate-600 mb-6 leading-relaxed">
                  Welcome to Tima-Ade University, where we nurture tomorrow&apos;s leaders and innovators. Our commitment to academic excellence, research advancement, and student success drives everything we do.
                </p>
                <p className="text-lg text-slate-600 mb-6 leading-relaxed">
                  As we continue to grow and evolve, we remain dedicated to our core mission: transforming lives through quality education and preparing our graduates to make meaningful contributions to society and the world.
                </p>
                <Link href="/contact" className="inline-flex items-center gap-2 text-primary-700 font-semibold hover:gap-3 transition">
                  Contact President <ArrowRight size={20} />
                </Link>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* Executive Leadership Team */}
      <section className="py-16 bg-gradient-to-b from-slate-50 to-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Executive Leadership Team</h2>
            <p className="text-lg text-slate-600 mt-4">
              Distinguished professionals committed to institutional excellence
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {leaders.map((leader) => (
              <Reveal key={leader.id}>
                <article className="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl transition">
                  {/* Photo Placeholder */}
                  <div className="aspect-square bg-gradient-to-br from-primary-600 to-primary-700 flex items-center justify-center group-hover:from-primary-700 group-hover:to-primary-800 transition">
                    <div className="text-6xl font-bold text-white opacity-60">{leader.photo}</div>
                  </div>

                  {/* Content */}
                  <div className="p-6">
                    <div className="text-sm font-semibold text-primary-700 mb-2 uppercase tracking-wide">{leader.title}</div>
                    <h3 className="text-lg font-bold text-slate-900 mb-2">{leader.name}</h3>
                    <p className="text-slate-600 text-sm leading-relaxed mb-4">{leader.bio}</p>
                    <Link href={`/leadership/${leader.id}`} className="inline-flex items-center gap-2 text-primary-700 font-semibold hover:gap-3 transition text-sm">
                      Full Profile <ArrowRight size={16} />
                    </Link>
                  </div>
                </article>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Governance Structure */}
      <section className="py-16 bg-white border-y border-slate-200">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Governance Structure</h2>
            <p className="text-lg text-slate-600 mt-4">
              Institutional leadership and decision-making bodies
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <Reveal>
              <div className="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200 p-8">
                <Users className="text-primary-700 mb-4" size={40} />
                <h3 className="text-xl font-bold text-slate-900 mb-3">Board of Trustees</h3>
                <p className="text-slate-600 mb-4">
                  Provides strategic guidance, oversight of institutional finances, and governance policy.
                </p>
                <ul className="space-y-2 text-sm text-slate-600 mb-4">
                  <li>• Sets university direction and strategic priorities</li>
                  <li>• Approves major policies and programs</li>
                  <li>• Ensures financial accountability</li>
                  <li>• Represents stakeholder interests</li>
                </ul>
                <Link href="/governance/board" className="text-primary-700 font-semibold hover:text-primary-800 text-sm">
                  Learn more →
                </Link>
              </div>
            </Reveal>

            <Reveal>
              <div className="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200 p-8">
                <Award className="text-primary-700 mb-4" size={40} />
                <h3 className="text-xl font-bold text-slate-900 mb-3">Academic Senate</h3>
                <p className="text-slate-600 mb-4">
                  Faculty leadership body responsible for academic policies and curriculum decisions.
                </p>
                <ul className="space-y-2 text-sm text-slate-600 mb-4">
                  <li>• Develops academic policies</li>
                  <li>• Reviews curriculum proposals</li>
                  <li>• Supports faculty professional development</li>
                  <li>• Promotes academic excellence</li>
                </ul>
                <Link href="/governance/senate" className="text-primary-700 font-semibold hover:text-primary-800 text-sm">
                  Learn more →
                </Link>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* Office Directory */}
      <section className="py-16 bg-gradient-to-b from-slate-50 to-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Administrative Offices</h2>
            <p className="text-lg text-slate-600 mt-4">
              Key offices and their contact information
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[
              { name: 'Office of the President', email: 'president@timaade.edu', phone: '+252-650-0000' },
              { name: 'Provost Office', email: 'provost@timaade.edu', phone: '+252-650-0001' },
              { name: 'Academic Affairs', email: 'academic@timaade.edu', phone: '+252-650-0002' },
              { name: 'Student Affairs', email: 'students@timaade.edu', phone: '+252-650-0003' },
              { name: 'Research Office', email: 'research@timaade.edu', phone: '+252-650-0004' },
              { name: 'Administration', email: 'admin@timaade.edu', phone: '+252-650-0005' },
            ].map((office, i) => (
              <Reveal key={i}>
                <div className="bg-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition">
                  <h3 className="text-lg font-bold text-slate-900 mb-3">{office.name}</h3>
                  <div className="space-y-2 text-sm text-slate-600">
                    <div><strong>Email:</strong> <a href={`mailto:${office.email}`} className="text-primary-700 hover:underline">{office.email}</a></div>
                    <div><strong>Phone:</strong> <a href={`tel:${office.phone}`} className="text-primary-700 hover:underline">{office.phone}</a></div>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-16 md:py-24 bg-primary-900 text-white">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">Connect with University Leadership</h2>
            <p className="text-lg text-primary-100 max-w-2xl mx-auto mb-8">
              Have questions or suggestions? We&apos;d like to hear from you. Contact our offices directly.
            </p>
            <Link href="/contact" className="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
              Get in Touch <ArrowRight size={20} />
            </Link>
          </div>
        </Reveal>
      </section>
    </PublicLayout>
  )
}
