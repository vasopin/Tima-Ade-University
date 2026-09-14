'use client'

import { useRef, useEffect, useState } from 'react'
import Link from 'next/link'
import { ChevronRight, Briefcase, Clock, MapPin, Users, ArrowRight, Search } from 'lucide-react'
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

const jobOpenings = [
  {
    id: 1,
    title: 'Assistant Professor - Computer Science',
    department: 'School of Technology',
    type: 'Faculty',
    location: 'Main Campus',
    posted: '2024-08-10',
    description: 'Join our dynamic computer science department and contribute to our innovative curriculum and research initiatives.',
  },
  {
    id: 2,
    title: 'Senior Research Engineer',
    department: 'Research Center',
    type: 'Research',
    location: 'Main Campus',
    posted: '2024-08-08',
    description: 'Lead cutting-edge research projects in artificial intelligence and machine learning.',
  },
  {
    id: 3,
    title: 'Administrative Manager',
    department: 'Administration',
    type: 'Administrative',
    location: 'Main Campus',
    posted: '2024-08-05',
    description: 'Manage operations and coordinate administrative functions for university departments.',
  },
  {
    id: 4,
    title: 'Lecturer - Business Administration',
    department: 'School of Business',
    type: 'Faculty',
    location: 'Main Campus',
    posted: '2024-07-28',
    description: 'Teach business courses and mentor students in professional development.',
  },
  {
    id: 5,
    title: 'Library Systems Specialist',
    department: 'Library Services',
    type: 'Technical',
    location: 'Main Campus',
    posted: '2024-07-20',
    description: 'Manage and maintain library technology systems and digital resources.',
  },
  {
    id: 6,
    title: 'Student Affairs Coordinator',
    department: 'Student Services',
    type: 'Administrative',
    location: 'Main Campus',
    posted: '2024-07-15',
    description: 'Support student programs and coordinate student life initiatives.',
  },
]

const jobTypes = ['All', 'Faculty', 'Research', 'Administrative', 'Technical']

export default function CareersPage() {
  const [selectedType, setSelectedType] = useState('All')
  const [searchTerm, setSearchTerm] = useState('')

  const filteredJobs = jobOpenings.filter((job) => {
    const matchesType = selectedType === 'All' || job.type === selectedType
    const matchesSearch = job.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         job.department.toLowerCase().includes(searchTerm.toLowerCase())
    return matchesType && matchesSearch
  })

  return (
    <PublicLayout title="Careers" description="Explore career opportunities at Tima-Ade University. Join our dynamic team of educators, researchers, and professionals.">
      {/* Hero Section */}
      <div className="relative min-h-80 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 pt-24 text-white">
        <div className="container-xl py-16">
          <Reveal>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">Careers at Tima-Ade University</h1>
          </Reveal>
          <Reveal className="mt-4">
            <p className="text-lg text-primary-100 max-w-2xl">
              Join our community of educators, researchers, and professionals dedicated to excellence in higher education.
            </p>
          </Reveal>
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="container-xl py-4 text-sm text-slate-600 flex items-center gap-2">
        <Link href="/" className="hover:text-primary-700 transition">Home</Link>
        <ChevronRight size={16} />
        <span className="text-primary-700 font-medium">Careers</span>
      </div>

      {/* Why Work With Us Section */}
      <section className="py-16 bg-gradient-to-b from-white to-slate-50">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Why Work With Us</h2>
            <p className="text-lg text-slate-600 mt-4">
              Be part of an institution committed to academic excellence and professional growth
            </p>
          </Reveal>

          <div className="grid md:grid-cols-3 gap-8">
            {[
              {
                title: 'Professional Growth',
                description: 'Access to training, professional development programs, and mentorship opportunities.',
                icon: Users,
              },
              {
                title: 'Competitive Benefits',
                description: 'Comprehensive health insurance, retirement plans, and work-life balance initiatives.',
                icon: Briefcase,
              },
              {
                title: 'Innovation Culture',
                description: 'Work in an environment that fosters creativity, collaboration, and intellectual pursuit.',
                icon: ArrowRight,
              },
            ].map((benefit, i) => (
              <Reveal key={i}>
                <div className="bg-white rounded-2xl border border-slate-200 p-8 hover:shadow-lg transition">
                  <benefit.icon className="text-primary-700 mb-4" size={40} />
                  <h3 className="text-xl font-bold text-slate-900 mb-3">{benefit.title}</h3>
                  <p className="text-slate-600">{benefit.description}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Job Openings Section */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900 mb-8">Open Positions</h2>

            {/* Search and Filter */}
            <div className="flex flex-col gap-6">
              <div className="relative">
                <Search className="absolute left-4 top-3.5 text-slate-400" size={20} />
                <input
                  type="text"
                  placeholder="Search positions..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full pl-12 pr-4 py-3 rounded-lg border border-slate-200 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 transition"
                />
              </div>

              <div className="flex flex-wrap gap-3">
                {jobTypes.map((type) => (
                  <button
                    key={type}
                    onClick={() => setSelectedType(type)}
                    className={`px-4 py-2 rounded-full font-medium transition ${
                      selectedType === type
                        ? 'bg-primary-700 text-white'
                        : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                    }`}
                  >
                    {type}
                  </button>
                ))}
              </div>
            </div>
          </Reveal>

          {/* Job Listings */}
          {filteredJobs.length > 0 ? (
            <div className="space-y-4">
              {filteredJobs.map((job) => (
                <Reveal key={job.id}>
                  <article className="group bg-gradient-to-r from-slate-50 to-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition">
                    <div className="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                      <div className="flex-1">
                        {/* Type Badge */}
                        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-100 text-primary-700 text-xs font-semibold mb-3">
                          {job.type}
                        </div>

                        {/* Title */}
                        <h3 className="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-700 transition">
                          {job.title}
                        </h3>

                        {/* Department */}
                        <p className="text-slate-600 mb-3">{job.department}</p>

                        {/* Details */}
                        <div className="flex flex-wrap gap-4 text-sm text-slate-600">
                          <div className="flex items-center gap-1">
                            <MapPin size={16} className="text-primary-700" />
                            {job.location}
                          </div>
                          <div className="flex items-center gap-1">
                            <Clock size={16} className="text-primary-700" />
                            Posted {new Date(job.posted).toLocaleDateString()}
                          </div>
                        </div>

                        {/* Description */}
                        <p className="text-slate-600 mt-3">{job.description}</p>
                      </div>

                      {/* CTA Button */}
                      <Link href={`/careers/${job.id}`} className="md:ml-4 flex-shrink-0 inline-flex items-center justify-center h-10 w-10 rounded-full bg-primary-100 text-primary-700 hover:bg-primary-200 transition">
                        <ArrowRight size={20} />
                      </Link>
                    </div>
                  </article>
                </Reveal>
              ))}
            </div>
          ) : (
            <Reveal>
              <div className="text-center py-12">
                <p className="text-lg text-slate-600">No positions found matching your criteria.</p>
              </div>
            </Reveal>
          )}
        </div>
      </section>

      {/* Application Process */}
      <section className="py-16 bg-slate-50 border-y border-slate-200">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">How to Apply</h2>
          </Reveal>

          <div className="max-w-2xl mx-auto space-y-6">
            {[
              { step: '1', title: 'Browse Positions', description: 'Review our current job openings to find the role that matches your qualifications and interests.' },
              { step: '2', title: 'Prepare Application', description: 'Gather your CV, cover letter, and any required documents as specified in the job posting.' },
              { step: '3', title: 'Submit Online', description: 'Complete the online application form and upload your documents through our careers portal.' },
              { step: '4', title: 'Interview Process', description: 'Selected candidates will be invited for interviews with department heads and HR personnel.' },
            ].map((item, i) => (
              <Reveal key={i}>
                <div className="flex gap-6 bg-white p-6 rounded-xl border border-slate-200">
                  <div className="flex-shrink-0 w-10 h-10 rounded-full bg-primary-700 text-white flex items-center justify-center font-bold">
                    {item.step}
                  </div>
                  <div>
                    <h3 className="text-lg font-bold text-slate-900 mb-2">{item.title}</h3>
                    <p className="text-slate-600">{item.description}</p>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Employee Benefits */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Employee Benefits & Perks</h2>
          </Reveal>

          <div className="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
            {[
              'Comprehensive Health Insurance',
              'Retirement & Pension Plans',
              'Professional Development',
              'Flexible Work Arrangements',
              'Annual Leave & Holidays',
              'Tuition Assistance Programs',
              'On-Campus Parking',
              'Wellness Programs',
              'Disability Coverage',
              'Life Insurance',
              'Commuter Benefits',
              'Library & IT Access',
            ].map((benefit, i) => (
              <Reveal key={i}>
                <div className="flex items-center gap-3 p-4 bg-slate-50 rounded-lg">
                  <div className="flex-shrink-0 w-6 h-6 rounded-full bg-primary-700 text-white flex items-center justify-center text-sm font-bold">✓</div>
                  <span className="font-medium text-slate-900">{benefit}</span>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Contact Section */}
      <section className="py-16 md:py-24 bg-primary-900 text-white">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">Have Questions About Working With Us?</h2>
            <p className="text-lg text-primary-100 max-w-2xl mx-auto mb-8">
              Contact our Human Resources department for more information about careers at Tima-Ade University.
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <a href="mailto:careers@timaade.edu" className="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
                careers@timaade.edu
              </a>
              <Link href="/contact" className="inline-flex items-center gap-2 border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white/10 transition">
                Contact HR <ArrowRight size={20} />
              </Link>
            </div>
          </div>
        </Reveal>
      </section>
    </PublicLayout>
  )
}
