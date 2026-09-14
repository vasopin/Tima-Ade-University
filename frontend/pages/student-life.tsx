'use client'

import { useRef, useEffect } from 'react'
import Link from 'next/link'
import { ChevronRight, Users, Heart, Music, Zap, ArrowRight, Trophy } from 'lucide-react'
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

export default function StudentLifePage() {
  return (
    <PublicLayout title="Student Life" description="Explore campus life at Tima-Ade University. Join clubs, participate in events, and build lifelong connections.">
      {/* Hero Section */}
      <div className="relative min-h-80 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 pt-24 text-white">
        <div className="container-xl py-16">
          <Reveal>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">Student Life at Tima-Ade</h1>
          </Reveal>
          <Reveal className="mt-4">
            <p className="text-lg text-primary-100 max-w-2xl">
              Experience a vibrant campus community with clubs, sports, cultural events, and more.
            </p>
          </Reveal>
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="container-xl py-4 text-sm text-slate-600 flex items-center gap-2">
        <Link href="/" className="hover:text-primary-700 transition">Home</Link>
        <ChevronRight size={16} />
        <span className="text-primary-700 font-medium">Student Life</span>
      </div>

      {/* What to Expect */}
      <section className="py-16 bg-gradient-to-b from-white to-slate-50">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">What to Expect on Campus</h2>
          </Reveal>

          <div className="grid md:grid-cols-3 gap-8">
            {[
              {
                icon: Users,
                title: 'Community & Belonging',
                description: 'Build meaningful friendships and connect with a diverse community of students from around the world.',
              },
              {
                icon: Heart,
                title: 'Personal Growth',
                description: 'Develop leadership skills, discover your passions, and create memories that last a lifetime.',
              },
              {
                icon: Zap,
                title: 'Opportunities',
                description: 'Engage in clubs, sports, cultural events, volunteer work, and professional development activities.',
              },
            ].map((item, i) => (
              <Reveal key={i}>
                <div className="bg-white rounded-2xl border border-slate-200 p-8 hover:shadow-lg transition">
                  <item.icon className="text-primary-700 mb-4" size={40} />
                  <h3 className="text-xl font-bold text-slate-900 mb-3">{item.title}</h3>
                  <p className="text-slate-600">{item.description}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Student Organizations */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Student Organizations</h2>
            <p className="text-lg text-slate-600 mt-4">
              Join one of our 100+ student clubs and organizations
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[
              { name: 'Student Government Association', members: 250, category: 'Leadership' },
              { name: 'Science and Technology Club', members: 180, category: 'Academic' },
              { name: 'International Students Association', members: 320, category: 'Cultural' },
              { name: 'Debate and Forensics Team', members: 45, category: 'Competition' },
              { name: 'Environmental Sustainability Club', members: 150, category: 'Service' },
              { name: 'Arts and Culture Society', members: 200, category: 'Cultural' },
              { name: 'Business & Entrepreneurship Club', members: 210, category: 'Professional' },
              { name: 'Photography & Film Society', members: 120, category: 'Arts' },
              { name: 'Community Volunteer Network', members: 280, category: 'Service' },
            ].map((org, i) => (
              <Reveal key={i}>
                <div className="bg-gradient-to-br from-slate-50 to-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition">
                  <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-100 text-primary-700 text-xs font-semibold mb-3">
                    {org.category}
                  </div>
                  <h3 className="text-lg font-bold text-slate-900 mb-2">{org.name}</h3>
                  <div className="text-sm text-slate-600">~{org.members} members</div>
                </div>
              </Reveal>
            ))}
          </div>

          <Reveal className="text-center mt-12">
            <Link href="/clubs" className="inline-flex items-center gap-2 bg-primary-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-800 transition">
              Browse All Clubs <ArrowRight size={20} />
            </Link>
          </Reveal>
        </div>
      </section>

      {/* Sports & Recreation */}
      <section className="py-16 bg-gradient-to-b from-slate-50 to-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Sports & Recreation</h2>
            <p className="text-lg text-slate-600 mt-4">
              Compete in varsity sports or play recreationally
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 gap-8">
            <Reveal>
              <div className="bg-white rounded-2xl border border-slate-200 p-8">
                <Trophy className="text-primary-700 mb-4" size={40} />
                <h3 className="text-xl font-bold text-slate-900 mb-3">Varsity Sports</h3>
                <p className="text-slate-600 mb-4">
                  Compete at the highest level with fully funded varsity teams.
                </p>
                <ul className="space-y-2 text-slate-600 text-sm mb-6">
                  <li>• Football & Soccer</li>
                  <li>• Basketball & Volleyball</li>
                  <li>• Tennis & Racquetball</li>
                  <li>• Track & Field</li>
                  <li>• Swimming & Diving</li>
                </ul>
                <Link href="/sports" className="text-primary-700 font-semibold hover:text-primary-800">
                  Learn more →
                </Link>
              </div>
            </Reveal>

            <Reveal>
              <div className="bg-white rounded-2xl border border-slate-200 p-8">
                <Heart className="text-primary-700 mb-4" size={40} />
                <h3 className="text-xl font-bold text-slate-900 mb-3">Intramural Programs</h3>
                <p className="text-slate-600 mb-4">
                  Participate in recreational sports for fun and fitness.
                </p>
                <ul className="space-y-2 text-slate-600 text-sm mb-6">
                  <li>• Basketball & Soccer Leagues</li>
                  <li>• Fitness Classes & Yoga</li>
                  <li>• Outdoor Recreation</li>
                  <li>• Climbing Wall & Gym</li>
                  <li>• Swimming Facilities</li>
                </ul>
                <Link href="/intramurals" className="text-primary-700 font-semibold hover:text-primary-800">
                  Learn more →
                </Link>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* Student Services */}
      <section className="py-16 bg-white border-y border-slate-200">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Support & Services</h2>
            <p className="text-lg text-slate-600 mt-4">
              We&apos;re here to help you succeed
            </p>
          </Reveal>

          <div className="grid md:grid-cols-3 gap-6">
            {[
              {
                title: 'Academic Support',
                services: ['Tutoring Services', 'Writing Center', 'Study Groups', 'Peer Mentoring'],
              },
              {
                title: 'Wellness & Health',
                services: ['Health Services', 'Counseling Services', 'Mental Health Support', 'Wellness Programs'],
              },
              {
                title: 'Career Services',
                services: ['Career Counseling', 'Resume Building', 'Job Listings', 'Internship Placement'],
              },
            ].map((service, i) => (
              <Reveal key={i}>
                <div className="bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl border border-primary-200 p-6">
                  <h3 className="text-lg font-bold text-primary-900 mb-4">{service.title}</h3>
                  <ul className="space-y-2">
                    {service.services.map((s, j) => (
                      <li key={j} className="flex items-start gap-2 text-primary-700 text-sm">
                        <span className="text-primary-900 font-bold mt-0.5">•</span>
                        <span>{s}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Events & Activities */}
      <section className="py-16 bg-gradient-to-b from-slate-50 to-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Events & Activities</h2>
            <p className="text-lg text-slate-600 mt-4">
              Year-round programming and campus events
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 gap-6">
            {[
              { month: 'September', event: 'Orientation & Welcome Week', type: 'Annual' },
              { month: 'October', event: 'Homecoming Celebration', type: 'Annual' },
              { month: 'November', event: 'Cultural Festival', type: 'Annual' },
              { month: 'December', event: 'Winter Holiday Events', type: 'Monthly' },
              { month: 'February', event: 'Leadership Conference', type: 'Annual' },
              { month: 'April', event: 'Spring Music Festival', type: 'Annual' },
            ].map((event, i) => (
              <Reveal key={i}>
                <div className="bg-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition">
                  <div className="flex items-start justify-between mb-3">
                    <div>
                      <div className="text-sm font-semibold text-primary-700 uppercase">{event.month}</div>
                      <h3 className="text-lg font-bold text-slate-900">{event.event}</h3>
                    </div>
                    <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                      {event.type}
                    </span>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Housing & Dining */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Housing & Dining</h2>
          </Reveal>

          <div className="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto">
            <Reveal>
              <div className="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200 p-8">
                <h3 className="text-xl font-bold text-slate-900 mb-3">Residential Living</h3>
                <p className="text-slate-600 mb-4">
                  On-campus housing available for all students. Choose from traditional dorms, suite-style, or apartment-style living.
                </p>
                <ul className="space-y-2 text-sm text-slate-600 mb-6">
                  <li>✓ 24/7 Security & Support</li>
                  <li>✓ High-Speed Internet</li>
                  <li>✓ Community Programs</li>
                  <li>✓ Furnished Rooms</li>
                </ul>
                <Link href="/housing" className="text-primary-700 font-semibold hover:text-primary-800">
                  Apply for Housing →
                </Link>
              </div>
            </Reveal>

            <Reveal>
              <div className="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200 p-8">
                <h3 className="text-xl font-bold text-slate-900 mb-3">Dining Services</h3>
                <p className="text-slate-600 mb-4">
                  Multiple dining locations offering diverse cuisine options to meet all dietary needs.
                </p>
                <ul className="space-y-2 text-sm text-slate-600 mb-6">
                  <li>✓ Main Dining Hall</li>
                  <li>✓ Café & Quick Service</li>
                  <li>✓ Dietary Accommodations</li>
                  <li>✓ Meal Plans Available</li>
                </ul>
                <Link href="/dining" className="text-primary-700 font-semibold hover:text-primary-800">
                  View Menus & Plans →
                </Link>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-16 md:py-24 bg-primary-900 text-white">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">Ready to Join Our Community?</h2>
            <p className="text-lg text-primary-100 max-w-2xl mx-auto mb-8">
              Experience campus life firsthand. Schedule a campus visit or apply now.
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <Link href="/admissions" className="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
                Apply Now <ArrowRight size={20} />
              </Link>
              <Link href="/campus-visit" className="inline-flex items-center gap-2 border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white/10 transition">
                Schedule a Visit <ArrowRight size={20} />
              </Link>
            </div>
          </div>
        </Reveal>
      </section>
    </PublicLayout>
  )
}
