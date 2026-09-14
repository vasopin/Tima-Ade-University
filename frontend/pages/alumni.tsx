'use client'

import { useRef, useEffect } from 'react'
import Link from 'next/link'
import { ChevronRight, Users, Award, Heart, ArrowRight } from 'lucide-react'
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

const alumniStories = [
  {
    id: 1,
    name: 'Dr. Amina Hassan',
    year: '2015',
    title: 'Medical Director, International Health Organization',
    story: 'From dedicated student to global healthcare leader, Dr. Hassan credits her time at Tima-Ade for developing the leadership skills and ethical grounding that define her career.',
    achievement: 'Leading health initiatives in 15 African countries',
  },
  {
    id: 2,
    name: 'Eng. Mohammed Ali',
    year: '2012',
    title: 'Founder & CEO, Tech Innovation Solutions',
    story: 'Mohammed founded a successful tech startup that now employs over 200 people. His entrepreneurial journey began with a research project at the university.',
    achievement: 'Built a $50M valued company',
  },
  {
    id: 3,
    name: 'Prof. Zainab Mohammed',
    year: '2010',
    title: 'Professor of Environmental Science',
    story: 'Returning to her alma mater as faculty, Prof. Mohammed is mentoring the next generation of environmental scientists and conducting groundbreaking research.',
    achievement: 'Published 40+ peer-reviewed papers',
  },
  {
    id: 4,
    name: 'Ms. Sarah Johnson',
    year: '2018',
    title: 'Communications Manager, Global NGO',
    story: 'Sarah uses her university education to drive positive social change through strategic communications for a leading international non-profit organization.',
    achievement: 'Managing campaigns impacting millions',
  },
]

export default function AlumniPage() {
  return (
    <PublicLayout title="Alumni Community" description="Connect with Tima-Ade University alumni network. Celebrate achievements, share stories, and stay engaged with the community.">
      {/* Hero Section */}
      <div className="relative min-h-96 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 pt-24 text-white">
        <div className="container-xl py-20">
          <Reveal>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">Our Alumni Community</h1>
          </Reveal>
          <Reveal className="mt-4">
            <p className="text-lg text-primary-100 max-w-2xl">
              Our graduates are making a difference around the world. Join the Tima-Ade alumni network and stay connected with our thriving community.
            </p>
          </Reveal>
          <Reveal className="mt-8">
            <Link href="#get-involved" className="inline-flex items-center gap-2 bg-white text-primary-900 px-6 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
              Get Involved <ArrowRight size={20} />
            </Link>
          </Reveal>
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="container-xl py-4 text-sm text-slate-600 flex items-center gap-2">
        <Link href="/" className="hover:text-primary-700 transition">Home</Link>
        <ChevronRight size={16} />
        <span className="text-primary-700 font-medium">Alumni</span>
      </div>

      {/* Alumni Stats */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Tima-Ade Alumni By The Numbers</h2>
          </Reveal>

          <div className="grid md:grid-cols-4 gap-6">
            {[
              { number: '8,500+', label: 'Alumni Worldwide', icon: Users },
              { number: '85%', label: 'Career Success Rate', icon: Award },
              { number: '25+', label: 'Years of Excellence', icon: Heart },
              { number: '120+', label: 'Countries Represented', icon: ArrowRight },
            ].map((stat, i) => (
              <Reveal key={i}>
                <div className="text-center p-8 bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl border border-primary-200">
                  <stat.icon className="text-primary-700 mx-auto mb-4" size={40} />
                  <div className="text-3xl font-bold text-primary-900 mb-2">{stat.number}</div>
                  <div className="text-slate-700 font-medium">{stat.label}</div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Alumni Stories Section */}
      <section className="py-16 bg-gradient-to-b from-slate-50 to-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Alumni Success Stories</h2>
            <p className="text-lg text-slate-600 mt-4">
              Inspiring journeys of our graduates making an impact globally
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 gap-8">
            {alumniStories.map((alumni) => (
              <Reveal key={alumni.id}>
                <article className="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl transition">
                  {/* Header */}
                  <div className="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-8 text-white">
                    <div className="text-sm font-semibold text-primary-200 mb-2">Class of {alumni.year}</div>
                    <h3 className="text-xl font-bold mb-1">{alumni.name}</h3>
                    <p className="text-primary-100">{alumni.title}</p>
                  </div>

                  {/* Content */}
                  <div className="p-6">
                    <p className="text-slate-600 mb-4 leading-relaxed">{alumni.story}</p>
                    
                    <div className="bg-primary-50 rounded-lg p-4 border border-primary-200">
                      <div className="text-sm text-primary-700 font-semibold">Key Achievement</div>
                      <p className="text-slate-700 font-medium mt-1">{alumni.achievement}</p>
                    </div>

                    <Link href={`/alumni/${alumni.id}`} className="inline-flex items-center gap-2 text-primary-700 font-semibold hover:gap-3 transition mt-4">
                      Read Full Story <ArrowRight size={16} />
                    </Link>
                  </div>
                </article>
              </Reveal>
            ))}
          </div>

          <Reveal className="text-center mt-12">
            <Link href="/alumni/stories" className="inline-flex items-center gap-2 bg-primary-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-800 transition">
              View More Stories <ArrowRight size={20} />
            </Link>
          </Reveal>
        </div>
      </section>

      {/* Alumni Benefits Section */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Alumni Benefits & Services</h2>
            <p className="text-lg text-slate-600 mt-4">
              Exclusive benefits for members of the Tima-Ade alumni community
            </p>
          </Reveal>

          <div className="grid md:grid-cols-3 gap-6">
            {[
              {
                title: 'Networking Events',
                description: 'Connect with fellow alumni at exclusive networking events, seminars, and conferences throughout the year.',
                icon: Users,
              },
              {
                title: 'Career Services',
                description: 'Access job boards, career coaching, and professional development resources to advance your career.',
                icon: Award,
              },
              {
                title: 'Lifelong Learning',
                description: 'Participate in continuing education programs and webinars featuring expert speakers and thought leaders.',
                icon: Heart,
              },
              {
                title: 'Alumni Directory',
                description: 'Connect with classmates through our secure alumni directory and stay updated with class reunions.',
                icon: Users,
              },
              {
                title: 'Campus Access',
                description: 'Enjoy continuing access to campus facilities, library resources, and recreational amenities.',
                icon: Award,
              },
              {
                title: 'Mentorship Programs',
                description: 'Give back by mentoring current students or benefit from guidance from senior alumni in your field.',
                icon: Heart,
              },
            ].map((benefit, i) => (
              <Reveal key={i}>
                <div className="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200 p-6 hover:shadow-lg transition">
                  <benefit.icon className="text-primary-700 mb-4" size={40} />
                  <h3 className="text-lg font-bold text-slate-900 mb-2">{benefit.title}</h3>
                  <p className="text-slate-600">{benefit.description}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Get Involved Section */}
      <section id="get-involved" className="py-16 bg-gradient-to-b from-primary-50 to-white border-y border-primary-200">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Get Involved</h2>
            <p className="text-lg text-slate-600 mt-4">
              There are many ways to stay connected and give back to the Tima-Ade community
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto">
            <Reveal>
              <div className="bg-white rounded-xl border-2 border-primary-300 p-8">
                <h3 className="text-2xl font-bold text-slate-900 mb-3">Volunteer</h3>
                <p className="text-slate-600 mb-4">
                  Share your expertise and experience by mentoring students, speaking at events, or supporting university initiatives.
                </p>
                <Link href="/alumni/volunteer" className="text-primary-700 font-semibold inline-flex items-center gap-2 hover:gap-3 transition">
                  Learn More <ArrowRight size={18} />
                </Link>
              </div>
            </Reveal>

            <Reveal>
              <div className="bg-white rounded-xl border-2 border-primary-300 p-8">
                <h3 className="text-2xl font-bold text-slate-900 mb-3">Donate</h3>
                <p className="text-slate-600 mb-4">
                  Support scholarships, research initiatives, and campus improvements through tax-deductible contributions.
                </p>
                <Link href="/alumni/donate" className="text-primary-700 font-semibold inline-flex items-center gap-2 hover:gap-3 transition">
                  Contribute Now <ArrowRight size={18} />
                </Link>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* Alumni Events */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Upcoming Alumni Events</h2>
          </Reveal>

          <div className="space-y-4 max-w-2xl mx-auto">
            {[
              {
                date: 'September 15, 2024',
                title: 'Alumni Networking Breakfast',
                location: 'Main Campus',
              },
              {
                date: 'October 5, 2024',
                title: 'Homecoming Weekend',
                location: 'Campus-wide',
              },
              {
                date: 'October 20, 2024',
                title: 'Career Development Workshop',
                location: 'Virtual',
              },
              {
                date: 'November 10, 2024',
                title: 'Annual Alumni Gala',
                location: 'Grand Ballroom',
              },
            ].map((event, i) => (
              <Reveal key={i}>
                <div className="bg-gradient-to-r from-slate-50 to-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition">
                  <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                      <div className="text-sm text-primary-700 font-semibold">{event.date}</div>
                      <h3 className="text-lg font-bold text-slate-900 mt-1">{event.title}</h3>
                      <p className="text-slate-600 text-sm">{event.location}</p>
                    </div>
                    <Link href="#" className="inline-flex items-center justify-center h-10 w-10 rounded-full bg-primary-100 text-primary-700 hover:bg-primary-200 transition flex-shrink-0">
                      <ArrowRight size={20} />
                    </Link>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>

          <Reveal className="text-center mt-8">
            <Link href="/alumni/events" className="inline-flex items-center gap-2 text-primary-700 font-semibold hover:gap-3 transition">
              View All Events <ArrowRight size={20} />
            </Link>
          </Reveal>
        </div>
      </section>

      {/* Join/Register Section */}
      <section className="py-16 md:py-24 bg-primary-900 text-white">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">Join the Alumni Network</h2>
            <p className="text-lg text-primary-100 max-w-2xl mx-auto mb-8">
              Connect with classmates, stay updated with university news, and access exclusive alumni benefits.
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <Link href="/alumni/register" className="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
                Register / Login <ArrowRight size={20} />
              </Link>
              <Link href="/contact" className="inline-flex items-center gap-2 border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white/10 transition">
                Contact Alumni Office <ArrowRight size={20} />
              </Link>
            </div>
          </div>
        </Reveal>
      </section>
    </PublicLayout>
  )
}
