'use client'

import { useRef, useEffect, useState } from 'react'
import Link from 'next/link'
import { ChevronRight, Calendar, MapPin, Clock, Users, ArrowRight, Search } from 'lucide-react'
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

const upcomingEvents = [
  {
    id: 1,
    title: 'Annual Science & Technology Symposium',
    date: '2024-09-15',
    time: '09:00 AM',
    location: 'Main Campus Auditorium',
    category: 'Academic',
    description: 'Join leading researchers and industry experts for presentations on cutting-edge technology and innovation.',
    attendees: 500,
    image: 'Science',
  },
  {
    id: 2,
    title: 'Career Fair 2024',
    date: '2024-09-20',
    time: '10:00 AM',
    location: 'Convention Hall',
    category: 'Career',
    description: 'Meet with top employers and explore career opportunities in various fields.',
    attendees: 300,
    image: 'Career',
  },
  {
    id: 3,
    title: 'Student Leadership Conference',
    date: '2024-09-25',
    time: '08:30 AM',
    location: 'Virtual & Main Campus',
    category: 'Student Life',
    description: 'Develop leadership skills and network with peers from across the university.',
    attendees: 200,
    image: 'Leadership',
  },
  {
    id: 4,
    title: 'International Research Workshop',
    date: '2024-10-01',
    time: '02:00 PM',
    location: 'Research Building, Room 101',
    category: 'Research',
    description: 'Collaborative workshop on emerging research methodologies and best practices.',
    attendees: 150,
    image: 'Research',
  },
  {
    id: 5,
    title: 'Alumni Networking Dinner',
    date: '2024-10-05',
    time: '06:00 PM',
    location: 'Grand Ballroom',
    category: 'Alumni',
    description: 'Reconnect with fellow alumni and celebrate the university\'s achievements.',
    attendees: 400,
    image: 'Alumni',
  },
  {
    id: 6,
    title: 'Sports Day & Athletic Championships',
    date: '2024-10-10',
    time: '08:00 AM',
    location: 'Sports Complex',
    category: 'Sports',
    description: 'Annual sports competition featuring various athletic disciplines and team events.',
    attendees: 1000,
    image: 'Sports',
  },
]

const categories = ['All', 'Academic', 'Career', 'Student Life', 'Research', 'Alumni', 'Sports', 'Cultural']

export default function EventsPage() {
  const [selectedCategory, setSelectedCategory] = useState('All')
  const [searchTerm, setSearchTerm] = useState('')

  const filteredEvents = upcomingEvents.filter((event) => {
    const matchesCategory = selectedCategory === 'All' || event.category === selectedCategory
    const matchesSearch = event.title.toLowerCase().includes(searchTerm.toLowerCase())
    return matchesCategory && matchesSearch
  })

  return (
    <PublicLayout title="Events & Conferences" description="Discover upcoming events, conferences, and activities at Tima-Ade University.">
      {/* Hero Section */}
      <div className="relative min-h-80 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 pt-24 text-white">
        <div className="container-xl py-16">
          <Reveal>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">Events & Conferences</h1>
          </Reveal>
          <Reveal className="mt-4">
            <p className="text-lg text-primary-100 max-w-2xl">
              Explore academic conferences, networking events, seminars, and activities happening at our university.
            </p>
          </Reveal>
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="container-xl py-4 text-sm text-slate-600 flex items-center gap-2">
        <Link href="/" className="hover:text-primary-700 transition">Home</Link>
        <ChevronRight size={16} />
        <span className="text-primary-700 font-medium">Events</span>
      </div>

      {/* Search and Filter Section */}
      <section className="py-12 bg-white border-b border-slate-200">
        <div className="container-xl">
          <Reveal>
            <div className="flex flex-col gap-6">
              {/* Search */}
              <div className="relative">
                <Search className="absolute left-4 top-3.5 text-slate-400" size={20} />
                <input
                  type="text"
                  placeholder="Search events..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full pl-12 pr-4 py-3 rounded-lg border border-slate-200 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 transition"
                />
              </div>

              {/* Categories */}
              <div className="flex flex-wrap gap-3">
                {categories.map((cat) => (
                  <button
                    key={cat}
                    onClick={() => setSelectedCategory(cat)}
                    className={`px-4 py-2 rounded-full font-medium transition ${
                      selectedCategory === cat
                        ? 'bg-primary-700 text-white'
                        : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                    }`}
                  >
                    {cat}
                  </button>
                ))}
              </div>
            </div>
          </Reveal>
        </div>
      </section>

      {/* Events Grid */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          {filteredEvents.length > 0 ? (
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
              {filteredEvents.map((event) => (
                <Reveal key={event.id}>
                  <article className="group bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl transition">
                    {/* Event Image Placeholder */}
                    <div className="aspect-video bg-gradient-to-br from-primary-200 to-primary-400 flex items-center justify-center group-hover:from-primary-300 group-hover:to-primary-500 transition">
                      <div className="text-center text-white">
                        <Calendar size={48} className="mx-auto mb-2 opacity-50" />
                        <div className="font-bold">{event.image}</div>
                      </div>
                    </div>

                    <div className="p-6">
                      {/* Category Badge */}
                      <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-100 text-primary-700 text-xs font-semibold mb-3">
                        {event.category}
                      </div>

                      {/* Title */}
                      <h3 className="text-lg font-bold text-slate-900 mb-3 line-clamp-2 group-hover:text-primary-700 transition">
                        {event.title}
                      </h3>

                      {/* Description */}
                      <p className="text-sm text-slate-600 mb-4 line-clamp-2">
                        {event.description}
                      </p>

                      {/* Event Details */}
                      <div className="space-y-2 mb-4 text-sm text-slate-600">
                        <div className="flex items-center gap-2">
                          <Calendar size={16} className="text-primary-700 flex-shrink-0" />
                          <span>{new Date(event.date).toLocaleDateString()}</span>
                        </div>
                        <div className="flex items-center gap-2">
                          <Clock size={16} className="text-primary-700 flex-shrink-0" />
                          <span>{event.time}</span>
                        </div>
                        <div className="flex items-center gap-2">
                          <MapPin size={16} className="text-primary-700 flex-shrink-0" />
                          <span className="line-clamp-1">{event.location}</span>
                        </div>
                        <div className="flex items-center gap-2">
                          <Users size={16} className="text-primary-700 flex-shrink-0" />
                          <span>~{event.attendees} expected attendees</span>
                        </div>
                      </div>

                      {/* CTA Button */}
                      <Link href={`/events/${event.id}`} className="inline-flex items-center gap-2 text-primary-700 font-semibold hover:gap-3 transition">
                        Learn More <ArrowRight size={16} />
                      </Link>
                    </div>
                  </article>
                </Reveal>
              ))}
            </div>
          ) : (
            <Reveal>
              <div className="text-center py-16">
                <p className="text-lg text-slate-600">No events found matching your criteria.</p>
              </div>
            </Reveal>
          )}
        </div>
      </section>

      {/* Calendar Section */}
      <section className="py-16 bg-slate-50 border-y border-slate-200">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-2xl md:text-3xl font-bold text-slate-900 mb-4">Event Calendar</h2>
            <p className="text-lg text-slate-600 max-w-2xl mx-auto mb-8">
              View the complete academic and event calendar for the year. Stay informed about important dates and deadlines.
            </p>
            <Link href="/calendar" className="inline-flex items-center gap-2 bg-primary-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-800 transition">
              View Full Calendar <ArrowRight size={20} />
            </Link>
          </div>
        </Reveal>
      </section>

      {/* CTA Section */}
      <section className="py-16 md:py-24 bg-primary-900 text-white">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">Have an Event Idea?</h2>
            <p className="text-lg text-primary-100 max-w-2xl mx-auto mb-8">
              Get in touch with our events team to learn about hosting or sponsoring an event at Tima-Ade University.
            </p>
            <Link href="/contact" className="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
              Contact Us <ArrowRight size={20} />
            </Link>
          </div>
        </Reveal>
      </section>
    </PublicLayout>
  )
}
