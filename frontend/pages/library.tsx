'use client'

import { useRef, useEffect } from 'react'
import Link from 'next/link'
import { ChevronRight, BookOpen, Globe, Clock, MapPin, Phone, Mail, ArrowRight, Search, Database } from 'lucide-react'
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

export default function LibraryPage() {
  return (
    <PublicLayout title="University Library" description="Explore Tima-Ade University's comprehensive library resources, collections, databases, and services.">
      {/* Hero Section */}
      <div className="relative min-h-96 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 pt-24 text-white">
        <div className="container-xl py-20">
          <Reveal>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">University Library</h1>
          </Reveal>
          <Reveal className="mt-4">
            <p className="text-lg text-primary-100 max-w-2xl">
              Your gateway to knowledge. Access millions of resources, databases, and research materials.
            </p>
          </Reveal>
          <Reveal className="mt-8">
            <div className="flex flex-wrap gap-4">
              <Link href="#catalog" className="inline-flex items-center gap-2 bg-white text-primary-900 px-6 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
                Search Catalog <ArrowRight size={20} />
              </Link>
              <Link href="#databases" className="inline-flex items-center gap-2 border-2 border-white px-6 py-3 rounded-full font-semibold hover:bg-white/10 transition">
                Browse Databases <ArrowRight size={20} />
              </Link>
            </div>
          </Reveal>
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="container-xl py-4 text-sm text-slate-600 flex items-center gap-2">
        <Link href="/" className="hover:text-primary-700 transition">Home</Link>
        <ChevronRight size={16} />
        <span className="text-primary-700 font-medium">Library</span>
      </div>

      {/* Library Stats */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Our Collections</h2>
          </Reveal>

          <div className="grid md:grid-cols-4 gap-6">
            {[
              { number: '2.5M+', label: 'Physical Books', icon: BookOpen },
              { number: '500K+', label: 'E-Journals', icon: Globe },
              { number: '1.2M+', label: 'E-Books', icon: Database },
              { number: '150+', label: 'Databases', icon: Search },
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

      {/* Catalog Search Section */}
      <section id="catalog" className="py-16 bg-gradient-to-b from-slate-50 to-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Search Our Catalog</h2>
            <p className="text-lg text-slate-600 mt-4">
              Find books, journals, and other resources in our comprehensive library catalog
            </p>
          </Reveal>

          <Reveal>
            <form className="max-w-2xl mx-auto">
              <div className="flex gap-3">
                <div className="flex-1 relative">
                  <Search className="absolute left-4 top-3.5 text-slate-400" size={20} />
                  <input
                    type="text"
                    placeholder="Search by title, author, ISBN, or keyword..."
                    className="w-full pl-12 pr-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 transition text-slate-900"
                  />
                </div>
                <button className="px-6 py-3 bg-primary-700 text-white rounded-lg font-semibold hover:bg-primary-800 transition flex-shrink-0">
                  Search
                </button>
              </div>
              <div className="mt-4 flex flex-wrap gap-2">
                <button type="button" className="px-4 py-2 bg-slate-100 text-slate-700 rounded-full text-sm hover:bg-slate-200 transition">Advanced Search</button>
                <button type="button" className="px-4 py-2 bg-slate-100 text-slate-700 rounded-full text-sm hover:bg-slate-200 transition">My Account</button>
                <button type="button" className="px-4 py-2 bg-slate-100 text-slate-700 rounded-full text-sm hover:bg-slate-200 transition">Renewals</button>
              </div>
            </form>
          </Reveal>
        </div>
      </section>

      {/* Library Services */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Library Services</h2>
          </Reveal>

          <div className="grid md:grid-cols-3 gap-8">
            {[
              {
                title: 'Reference Services',
                description: 'Expert librarians available to assist with research, citing sources, and finding information.',
                icon: BookOpen,
              },
              {
                title: 'Research Support',
                description: 'Personalized research consultations, database training, and literature review assistance.',
                icon: Database,
              },
              {
                title: 'Interlibrary Loan',
                description: 'Access materials from partner institutions when not available in our collection.',
                icon: Globe,
              },
              {
                title: 'Study Spaces',
                description: 'Quiet study areas, collaborative spaces, and group study rooms available for students.',
                icon: BookOpen,
              },
              {
                title: 'Technology Support',
                description: 'Computer access, printing services, scanning, and technology assistance.',
                icon: Database,
              },
              {
                title: 'Instruction Programs',
                description: 'Information literacy classes, database workshops, and research methodology sessions.',
                icon: Globe,
              },
            ].map((service, i) => (
              <Reveal key={i}>
                <div className="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200 p-6 hover:shadow-lg transition">
                  <service.icon className="text-primary-700 mb-4" size={40} />
                  <h3 className="text-lg font-bold text-slate-900 mb-2">{service.title}</h3>
                  <p className="text-slate-600">{service.description}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Databases Section */}
      <section id="databases" className="py-16 bg-gradient-to-b from-slate-50 to-white border-y border-slate-200">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Access Databases</h2>
            <p className="text-lg text-slate-600 mt-4">
              Comprehensive access to academic journals, research databases, and digital resources
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[
              { name: 'JSTOR', category: 'Interdisciplinary', coverage: '8,000+ journals' },
              { name: 'ProQuest', category: 'Academic', coverage: 'Dissertations & theses' },
              { name: 'EBSCOhost', category: 'Multidisciplinary', coverage: '10,000+ publications' },
              { name: 'Web of Science', category: 'Citation', coverage: 'Research analytics' },
              { name: 'Scopus', category: 'Indexing', coverage: 'Scientific research' },
              { name: 'Academic Search Complete', category: 'General', coverage: '6,400+ journals' },
            ].map((db, i) => (
              <Reveal key={i}>
                <div className="bg-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition">
                  <h3 className="text-lg font-bold text-slate-900 mb-2">{db.name}</h3>
                  <div className="space-y-2 text-sm text-slate-600">
                    <div><strong>Category:</strong> {db.category}</div>
                    <div><strong>Coverage:</strong> {db.coverage}</div>
                  </div>
                  <Link href="#" className="inline-flex items-center gap-2 text-primary-700 font-semibold mt-4 hover:gap-3 transition text-sm">
                    Access Database <ArrowRight size={14} />
                  </Link>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Operating Hours & Contact */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Visit Us</h2>
          </Reveal>

          <div className="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto">
            <Reveal>
              <div className="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200 p-8">
                <h3 className="text-xl font-bold text-slate-900 mb-6">Main Library</h3>
                
                <div className="space-y-4">
                  <div className="flex items-start gap-3">
                    <MapPin className="text-primary-700 flex-shrink-0 mt-1" size={20} />
                    <div>
                      <div className="font-semibold text-slate-900">Location</div>
                      <p className="text-slate-600 text-sm">Central Campus, Building A, 123 University Ave</p>
                    </div>
                  </div>

                  <div className="flex items-start gap-3">
                    <Clock className="text-primary-700 flex-shrink-0 mt-1" size={20} />
                    <div>
                      <div className="font-semibold text-slate-900">Hours</div>
                      <div className="text-slate-600 text-sm">
                        <p>Mon-Fri: 7:00 AM - 10:00 PM</p>
                        <p>Sat: 9:00 AM - 6:00 PM</p>
                        <p>Sun: 12:00 PM - 8:00 PM</p>
                      </div>
                    </div>
                  </div>

                  <div className="flex items-start gap-3">
                    <Phone className="text-primary-700 flex-shrink-0 mt-1" size={20} />
                    <div>
                      <div className="font-semibold text-slate-900">Phone</div>
                      <p className="text-slate-600 text-sm">+1 (555) 123-4567</p>
                    </div>
                  </div>

                  <div className="flex items-start gap-3">
                    <Mail className="text-primary-700 flex-shrink-0 mt-1" size={20} />
                    <div>
                      <div className="font-semibold text-slate-900">Email</div>
                      <p className="text-slate-600 text-sm">library@timaade.edu</p>
                    </div>
                  </div>
                </div>
              </div>
            </Reveal>

            <Reveal>
              <div className="bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl border border-primary-200 p-8">
                <h3 className="text-xl font-bold text-primary-900 mb-6">Need Help?</h3>
                
                <p className="text-primary-700 mb-6 leading-relaxed">
                  Our librarians are ready to assist you with research, database access, and information literacy. Contact us or visit the reference desk during library hours.
                </p>

                <div className="space-y-3">
                  <Link href="/contact" className="block w-full text-center bg-primary-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-primary-800 transition">
                    Contact Us
                  </Link>
                  <button className="w-full bg-white text-primary-700 px-4 py-2 rounded-lg font-semibold hover:bg-slate-100 transition">
                    Schedule Research Consultation
                  </button>
                </div>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* Resource Guides */}
      <section className="py-16 bg-slate-50">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Research Guides & Resources</h2>
          </Reveal>

          <div className="grid md:grid-cols-3 gap-6">
            {[
              { title: 'Citation Styles', description: 'Learn APA, MLA, Chicago, and other citation formats.' },
              { title: 'Research Strategies', description: 'Guides for conducting effective literature reviews and research.' },
              { title: 'Subject Guides', description: 'Curated resources for specific subject areas and disciplines.' },
              { title: 'Database Tutorials', description: 'Video tutorials and guides for accessing online databases.' },
              { title: 'Remote Access', description: 'Information about accessing library resources off-campus.' },
              { title: 'Digital Literacy', description: 'Resources for developing information and technology skills.' },
            ].map((guide, i) => (
              <Reveal key={i}>
                <Link href="#" className="block bg-white rounded-xl border border-slate-200 p-6 hover:shadow-lg hover:border-primary-300 transition">
                  <h3 className="text-lg font-bold text-slate-900 mb-2">{guide.title}</h3>
                  <p className="text-slate-600 text-sm">{guide.description}</p>
                </Link>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-16 md:py-24 bg-primary-900 text-white">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">Start Your Research Journey</h2>
            <p className="text-lg text-primary-100 max-w-2xl mx-auto mb-8">
              With millions of resources at your fingertips, discover what our library can offer for your academic success.
            </p>
            <Link href="#catalog" className="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
              Search Catalog <ArrowRight size={20} />
            </Link>
          </div>
        </Reveal>
      </section>
    </PublicLayout>
  )
}
