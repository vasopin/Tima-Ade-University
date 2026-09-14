'use client'

import { useRef, useEffect } from 'react'
import Link from 'next/link'
import Image from 'next/image'
import { ArrowRight, ChevronRight, Users, BookOpen, Award, Sparkles } from 'lucide-react'
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

export default function AboutPage() {
  return (
    <PublicLayout title="About Tima-Ade University" description="Learn about our university's mission, vision, values, and history of excellence in education.">
      {/* Hero Section */}
      <div className="relative min-h-96 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 pt-24 text-white">
        <div className="container-xl py-20">
          <Reveal>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">About Tima-Ade University</h1>
          </Reveal>
          <Reveal className="mt-4">
            <p className="text-lg text-primary-100 max-w-2xl">
              Committed to excellence in education, research, and student development since our founding.
            </p>
          </Reveal>
          <Reveal className="mt-8">
            <div className="flex flex-wrap gap-4">
              <Link href="#mission" className="inline-flex items-center gap-2 bg-white text-primary-900 px-6 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
                Our Mission <ArrowRight size={20} />
              </Link>
              <Link href="#leadership" className="inline-flex items-center gap-2 border-2 border-white px-6 py-3 rounded-full font-semibold hover:bg-white/10 transition">
                Leadership Team <ArrowRight size={20} />
              </Link>
            </div>
          </Reveal>
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="container-xl py-4 text-sm text-slate-600 flex items-center gap-2">
        <Link href="/" className="hover:text-primary-700 transition">Home</Link>
        <ChevronRight size={16} />
        <span className="text-primary-700 font-medium">About</span>
      </div>

      {/* Mission Section */}
      <section id="mission" className="py-20 md:py-28 bg-gradient-to-b from-white to-slate-50">
        <div className="container-xl">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <Reveal>
              <div>
                <h2 className="text-3xl md:text-4xl font-bold text-slate-900 mb-6">Our Mission</h2>
                <p className="text-lg text-slate-600 mb-4 leading-relaxed">
                  Tima-Ade University is dedicated to providing transformative education that empowers students to become leaders and innovators in their fields. We foster an environment of intellectual curiosity, critical thinking, and practical application of knowledge.
                </p>
                <p className="text-lg text-slate-600 leading-relaxed">
                  Our mission is to develop educated individuals who contribute meaningfully to society, advance human knowledge through research, and uphold the highest standards of academic excellence.
                </p>
              </div>
            </Reveal>
            <Reveal>
              <div className="bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl p-8 border border-primary-200">
                <BookOpen size={48} className="text-primary-700 mb-6" />
                <h3 className="text-2xl font-bold text-primary-900 mb-4">Excellence in Education</h3>
                <p className="text-primary-700">
                  We are committed to delivering world-class education that combines theoretical knowledge with practical skills, preparing students for success in an ever-changing global landscape.
                </p>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* Vision & Values Section */}
      <section className="py-20 md:py-28 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Vision & Core Values</h2>
            <p className="text-lg text-slate-600 max-w-2xl mx-auto">
              Guiding principles that define who we are and what we stand for
            </p>
          </Reveal>

          <div className="grid md:grid-cols-3 gap-8">
            <Reveal>
              <div className="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 border border-blue-200 hover:shadow-lg transition">
                <Sparkles className="text-blue-600 mb-4" size={40} />
                <h3 className="text-xl font-bold text-slate-900 mb-3">Vision</h3>
                <p className="text-slate-700">
                  To be a leading institution of higher learning recognized globally for academic excellence, innovative research, and the transformative impact of our graduates on society.
                </p>
              </div>
            </Reveal>

            <Reveal>
              <div className="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 border border-green-200 hover:shadow-lg transition">
                <Award className="text-green-600 mb-4" size={40} />
                <h3 className="text-xl font-bold text-slate-900 mb-3">Core Values</h3>
                <ul className="text-slate-700 space-y-2">
                  <li>• <strong>Integrity</strong> - Honesty and ethical conduct</li>
                  <li>• <strong>Excellence</strong> - Highest standards in all endeavors</li>
                  <li>• <strong>Inclusivity</strong> - Diversity and respect for all</li>
                  <li>• <strong>Innovation</strong> - Forward-thinking approaches</li>
                </ul>
              </div>
            </Reveal>

            <Reveal>
              <div className="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8 border border-purple-200 hover:shadow-lg transition">
                <Users className="text-purple-600 mb-4" size={40} />
                <h3 className="text-xl font-bold text-slate-900 mb-3">Community</h3>
                <p className="text-slate-700">
                  We believe in fostering a vibrant community where students, faculty, and staff collaborate to create meaningful educational experiences and advance human knowledge through research and service.
                </p>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* History Section */}
      <section className="py-20 md:py-28 bg-slate-50">
        <div className="container-xl">
          <Reveal className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Our History</h2>
            <p className="text-lg text-slate-600 max-w-2xl mx-auto">
              A legacy of academic achievement and innovation
            </p>
          </Reveal>

          <div className="max-w-3xl mx-auto">
            <Reveal>
              <div className="space-y-8">
                <div className="flex gap-6">
                  <div className="text-primary-700 font-bold text-lg min-w-24">2010</div>
                  <div>
                    <h3 className="text-xl font-bold text-slate-900 mb-2">University Founded</h3>
                    <p className="text-slate-600">Tima-Ade University was established with a vision to provide quality education in the region.</p>
                  </div>
                </div>

                <div className="flex gap-6">
                  <div className="text-primary-700 font-bold text-lg min-w-24">2012</div>
                  <div>
                    <h3 className="text-xl font-bold text-slate-900 mb-2">First Accreditation</h3>
                    <p className="text-slate-600">Received official accreditation from regional and national educational bodies.</p>
                  </div>
                </div>

                <div className="flex gap-6">
                  <div className="text-primary-700 font-bold text-lg min-w-24">2015</div>
                  <div>
                    <h3 className="text-xl font-bold text-slate-900 mb-2">Expansion Phase</h3>
                    <p className="text-slate-600">Expanded academic programs and established research centers of excellence.</p>
                  </div>
                </div>

                <div className="flex gap-6">
                  <div className="text-primary-700 font-bold text-lg min-w-24">2020</div>
                  <div>
                    <h3 className="text-xl font-bold text-slate-900 mb-2">Digital Transformation</h3>
                    <p className="text-slate-600">Embraced digital learning and innovation to serve students globally.</p>
                  </div>
                </div>

                <div className="flex gap-6">
                  <div className="text-primary-700 font-bold text-lg min-w-24">2024</div>
                  <div>
                    <h3 className="text-xl font-bold text-slate-900 mb-2">Global Recognition</h3>
                    <p className="text-slate-600">Recognized internationally for research excellence and student success.</p>
                  </div>
                </div>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* Quick Stats */}
      <section className="py-20 md:py-28 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">By the Numbers</h2>
          </Reveal>

          <div className="grid md:grid-cols-4 gap-6">
            {[
              { number: '2,850+', label: 'Active Students', icon: Users },
              { number: '140+', label: 'Faculty Members', icon: Award },
              { number: '38', label: 'Academic Programs', icon: BookOpen },
              { number: '99.4%', label: 'Success Rate', icon: Sparkles },
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

      {/* CTA Section */}
      <section className="py-16 md:py-24 bg-primary-900 text-white">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">Join Our Community</h2>
            <p className="text-lg text-primary-100 max-w-2xl mx-auto mb-8">
              Discover how Tima-Ade University can help you achieve your academic and professional goals.
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <Link href="/admissions" className="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
                Apply Now <ArrowRight size={20} />
              </Link>
              <Link href="/contact" className="inline-flex items-center gap-2 border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white/10 transition">
                Contact Us <ArrowRight size={20} />
              </Link>
            </div>
          </div>
        </Reveal>
      </section>
    </PublicLayout>
  )
}
