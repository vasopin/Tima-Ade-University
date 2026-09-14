'use client'

import { useRef, useEffect, useState } from 'react'
import Link from 'next/link'
import { ChevronRight, Beaker, Users, Award, TrendingUp, ArrowRight } from 'lucide-react'
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

const researchCenters = [
  {
    id: 1,
    name: 'Center for Advanced Technology Research',
    description: 'Pioneering research in AI, machine learning, and computational sciences.',
    focus: 'Technology & Innovation',
    researchers: 45,
    projects: 12,
  },
  {
    id: 2,
    name: 'Institute for Environmental Studies',
    description: 'Research on sustainability, climate change, and environmental conservation.',
    focus: 'Environmental Science',
    researchers: 28,
    projects: 8,
  },
  {
    id: 3,
    name: 'Biomedical Research Laboratory',
    description: 'Cutting-edge research in medicine, biotechnology, and life sciences.',
    focus: 'Health Sciences',
    researchers: 35,
    projects: 10,
  },
  {
    id: 4,
    name: 'Center for Social Innovation',
    description: 'Research addressing social challenges and community development.',
    focus: 'Social Sciences',
    researchers: 22,
    projects: 6,
  },
  {
    id: 5,
    name: 'Digital Humanities Research Group',
    description: 'Interdisciplinary research combining technology with humanities.',
    focus: 'Humanities & Technology',
    researchers: 18,
    projects: 5,
  },
  {
    id: 6,
    name: 'Materials Science Laboratory',
    description: 'Research in advanced materials and nanotechnology applications.',
    focus: 'Physical Sciences',
    researchers: 32,
    projects: 9,
  },
]

const recentPublications = [
  {
    title: 'Advances in Machine Learning for Healthcare Applications',
    authors: 'Dr. Sarah Johnson et al.',
    year: 2024,
    journal: 'International Journal of AI Research',
  },
  {
    title: 'Sustainable Urban Development: A Comprehensive Framework',
    authors: 'Prof. Michael Chen et al.',
    year: 2024,
    journal: 'Journal of Environmental Studies',
  },
  {
    title: 'Novel Approaches to Antibiotic Resistance',
    authors: 'Dr. Elena Rodriguez et al.',
    year: 2023,
    journal: 'Nature Biomedical Sciences',
  },
  {
    title: 'Digital Transformation in Education: Challenges and Opportunities',
    authors: 'Prof. James Mitchell et al.',
    year: 2023,
    journal: 'Higher Education Review',
  },
]

export default function ResearchPage() {
  return (
    <PublicLayout title="Research & Innovation" description="Explore Tima-Ade University's groundbreaking research initiatives, centers, and innovations.">
      {/* Hero Section */}
      <div className="relative min-h-96 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 pt-24 text-white">
        <div className="container-xl py-20">
          <Reveal>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">Research & Innovation</h1>
          </Reveal>
          <Reveal className="mt-4">
            <p className="text-lg text-primary-100 max-w-2xl">
              Driving innovation and advancing human knowledge through cutting-edge research across multiple disciplines.
            </p>
          </Reveal>
          <Reveal className="mt-8">
            <Link href="#research-centers" className="inline-flex items-center gap-2 bg-white text-primary-900 px-6 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
              Explore Research Centers <ArrowRight size={20} />
            </Link>
          </Reveal>
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="container-xl py-4 text-sm text-slate-600 flex items-center gap-2">
        <Link href="/" className="hover:text-primary-700 transition">Home</Link>
        <ChevronRight size={16} />
        <span className="text-primary-700 font-medium">Research</span>
      </div>

      {/* Research Stats */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Research Impact</h2>
          </Reveal>

          <div className="grid md:grid-cols-4 gap-6">
            {[
              { number: '180+', label: 'Active Projects', icon: Beaker },
              { number: '300+', label: 'Researchers', icon: Users },
              { number: '45+', label: 'Publications Yearly', icon: Award },
              { number: '$15M+', label: 'Funding Annually', icon: TrendingUp },
            ].map((stat, i) => (
              <Reveal key={i}>
                <div className="text-center p-6 bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl border border-primary-200">
                  <stat.icon className="text-primary-700 mx-auto mb-4" size={40} />
                  <div className="text-3xl font-bold text-primary-900 mb-2">{stat.number}</div>
                  <div className="text-slate-700 font-medium">{stat.label}</div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Research Overview */}
      <section className="py-16 bg-gradient-to-b from-slate-50 to-white">
        <div className="container-xl">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <Reveal>
              <div>
                <h2 className="text-3xl md:text-4xl font-bold text-slate-900 mb-6">Our Research Mission</h2>
                <p className="text-lg text-slate-600 mb-4 leading-relaxed">
                  Tima-Ade University is committed to conducting high-impact research that addresses real-world challenges and contributes to the advancement of knowledge across multiple disciplines.
                </p>
                <p className="text-lg text-slate-600 mb-6 leading-relaxed">
                  Our research initiatives focus on innovation, interdisciplinary collaboration, and creating tangible solutions to global problems.
                </p>
                <div className="space-y-3">
                  <div className="flex items-start gap-3">
                    <div className="flex-shrink-0 w-6 h-6 rounded-full bg-primary-700 text-white flex items-center justify-center text-sm font-bold">✓</div>
                    <div>
                      <h4 className="font-semibold text-slate-900">Interdisciplinary Collaboration</h4>
                      <p className="text-slate-600 text-sm">Breaking boundaries across departments and disciplines</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-3">
                    <div className="flex-shrink-0 w-6 h-6 rounded-full bg-primary-700 text-white flex items-center justify-center text-sm font-bold">✓</div>
                    <div>
                      <h4 className="font-semibold text-slate-900">Societal Impact</h4>
                      <p className="text-slate-600 text-sm">Creating solutions that benefit communities globally</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-3">
                    <div className="flex-shrink-0 w-6 h-6 rounded-full bg-primary-700 text-white flex items-center justify-center text-sm font-bold">✓</div>
                    <div>
                      <h4 className="font-semibold text-slate-900">Innovation Support</h4>
                      <p className="text-slate-600 text-sm">Providing resources and infrastructure for groundbreaking work</p>
                    </div>
                  </div>
                </div>
              </div>
            </Reveal>

            <Reveal>
              <div className="bg-gradient-to-br from-primary-100 to-primary-200 rounded-2xl p-8 border border-primary-300 h-full flex items-center justify-center">
                <div className="text-center">
                  <Beaker size={80} className="text-primary-700 mx-auto mb-4 opacity-50" />
                  <h3 className="text-2xl font-bold text-primary-900">Innovation at Scale</h3>
                  <p className="text-primary-700 mt-2">Building tomorrow&apos;s solutions today</p>
                </div>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* Research Centers */}
      <section id="research-centers" className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Research Centers & Institutes</h2>
            <p className="text-lg text-slate-600 mt-4 max-w-2xl mx-auto">
              Dedicated teams of experts driving innovation across multiple research domains
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {researchCenters.map((center) => (
              <Reveal key={center.id}>
                <article className="group bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200 p-6 hover:shadow-xl transition">
                  <div className="flex items-start justify-between mb-4">
                    <div className="flex-1">
                      <h3 className="text-lg font-bold text-slate-900 mb-2 group-hover:text-primary-700 transition">
                        {center.name}
                      </h3>
                      <p className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-100 text-primary-700 text-xs font-semibold">
                        {center.focus}
                      </p>
                    </div>
                  </div>

                  <p className="text-slate-600 mb-4">{center.description}</p>

                  <div className="grid grid-cols-2 gap-3 mb-4 p-3 bg-slate-50 rounded-lg">
                    <div>
                      <div className="text-2xl font-bold text-primary-700">{center.researchers}</div>
                      <div className="text-xs text-slate-600">Researchers</div>
                    </div>
                    <div>
                      <div className="text-2xl font-bold text-primary-700">{center.projects}</div>
                      <div className="text-xs text-slate-600">Active Projects</div>
                    </div>
                  </div>

                  <Link href={`/research/${center.id}`} className="inline-flex items-center gap-2 text-primary-700 font-semibold hover:gap-3 transition">
                    Learn More <ArrowRight size={16} />
                  </Link>
                </article>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Recent Publications */}
      <section className="py-16 bg-gradient-to-b from-slate-50 to-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Recent Publications</h2>
            <p className="text-lg text-slate-600 mt-4">
              Latest research publications from our faculty and researchers
            </p>
          </Reveal>

          <div className="max-w-3xl mx-auto space-y-6">
            {recentPublications.map((pub, i) => (
              <Reveal key={i}>
                <article className="group bg-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition">
                  <h3 className="text-lg font-bold text-slate-900 mb-2 group-hover:text-primary-700 transition">
                    {pub.title}
                  </h3>
                  <p className="text-slate-600 mb-3">{pub.authors}</p>
                  <div className="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                    <span className="font-medium">{pub.journal}</span>
                    <span>•</span>
                    <span>{pub.year}</span>
                  </div>
                </article>
              </Reveal>
            ))}
          </div>

          <Reveal className="text-center mt-12">
            <Link href="/research/publications" className="inline-flex items-center gap-2 bg-primary-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-800 transition">
              View All Publications <ArrowRight size={20} />
            </Link>
          </Reveal>
        </div>
      </section>

      {/* Funding Opportunities */}
      <section className="py-16 bg-primary-50 border-y border-primary-200">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-2xl md:text-3xl font-bold text-slate-900 mb-4">Funding Opportunities</h2>
            <p className="text-lg text-slate-600 max-w-2xl mx-auto mb-8">
              Explore available grants, fellowships, and research funding opportunities for your projects.
            </p>
            <Link href="/research/funding" className="inline-flex items-center gap-2 bg-primary-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-800 transition">
              Explore Funding <ArrowRight size={20} />
            </Link>
          </div>
        </Reveal>
      </section>

      {/* CTA Section */}
      <section className="py-16 md:py-24 bg-primary-900 text-white">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">Join Our Research Community</h2>
            <p className="text-lg text-primary-100 max-w-2xl mx-auto mb-8">
              Collaborate with leading researchers and contribute to cutting-edge innovations.
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <Link href="/admissions" className="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
                Learn More <ArrowRight size={20} />
              </Link>
              <Link href="/contact" className="inline-flex items-center gap-2 border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white/10 transition">
                Contact Research Office <ArrowRight size={20} />
              </Link>
            </div>
          </div>
        </Reveal>
      </section>
    </PublicLayout>
  )
}
