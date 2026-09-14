'use client'

import { useRef, useEffect, useState } from 'react'
import Link from 'next/link'
import { ChevronRight, Calendar, User, ArrowRight, Search, Tag } from 'lucide-react'
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

const newsArticles = [
  {
    id: 1,
    title: 'Tima-Ade University Launches New Research Center for Technology Innovation',
    category: 'Research',
    date: '2024-08-15',
    author: 'Dr. Sarah Johnson',
    excerpt: 'The university has inaugurated a state-of-the-art research center dedicated to advancing technology innovation and addressing real-world challenges.',
    content: 'The university has inaugurated a state-of-the-art research center dedicated to advancing technology innovation and addressing real-world challenges.',
    featured: true,
  },
  {
    id: 2,
    title: 'Record Number of Students Achieve Distinction in Annual Academic Awards',
    category: 'Academic',
    date: '2024-08-10',
    author: 'Prof. Marcus Chen',
    excerpt: 'A record 45 students received academic distinction awards, highlighting the university\'s commitment to excellence and student achievement.',
    content: 'A record 45 students received academic distinction awards, highlighting the university\'s commitment to excellence and student achievement.',
    featured: true,
  },
  {
    id: 3,
    title: 'Tima-Ade Hosts International Conference on Sustainable Development',
    category: 'Events',
    date: '2024-08-05',
    author: 'Dr. Elena Rodriguez',
    excerpt: 'Leaders and scholars from around the world gathered to discuss sustainable development goals and collaborative solutions.',
    content: 'Leaders and scholars from around the world gathered to discuss sustainable development goals and collaborative solutions.',
    featured: false,
  },
  {
    id: 4,
    title: 'Student Achievement: Local Team Wins National Science Competition',
    category: 'Student Life',
    date: '2024-07-28',
    author: 'James Mitchell',
    excerpt: 'Three Tima-Ade students took first place in the National Science Olympiad, earning recognition for their innovative solutions.',
    content: 'Three Tima-Ade students took first place in the National Science Olympiad, earning recognition for their innovative solutions.',
    featured: false,
  },
  {
    id: 5,
    title: 'Partnership Announcement: Tima-Ade Collaborates with Global Tech Leader',
    category: 'Partnerships',
    date: '2024-07-20',
    author: 'Dr. Michael Anderson',
    excerpt: 'The university has signed a partnership agreement to enhance research capabilities and provide internship opportunities for students.',
    content: 'The university has signed a partnership agreement to enhance research capabilities and provide internship opportunities for students.',
    featured: false,
  },
  {
    id: 6,
    title: 'New Scholarship Program Aims to Support Underprivileged Students',
    category: 'Scholarships',
    date: '2024-07-15',
    author: 'Dr. Patricia White',
    excerpt: 'The university has launched a new scholarship program with $5 million in funding to make education more accessible.',
    content: 'The university has launched a new scholarship program with $5 million in funding to make education more accessible.',
    featured: false,
  },
]

const categories = ['All', 'Academic', 'Research', 'Events', 'Student Life', 'Partnerships', 'Scholarships']

export default function NewsPage() {
  const [selectedCategory, setSelectedCategory] = useState('All')
  const [searchTerm, setSearchTerm] = useState('')

  const filteredNews = newsArticles.filter((article) => {
    const matchesCategory = selectedCategory === 'All' || article.category === selectedCategory
    const matchesSearch = article.title.toLowerCase().includes(searchTerm.toLowerCase()) || 
                         article.excerpt.toLowerCase().includes(searchTerm.toLowerCase())
    return matchesCategory && matchesSearch
  })

  const featuredArticles = filteredNews.filter((article) => article.featured).slice(0, 2)
  const regularArticles = filteredNews.filter((article) => !article.featured)

  return (
    <PublicLayout title="News & Updates" description="Stay updated with the latest news, announcements, and achievements from Tima-Ade University.">
      {/* Hero Section */}
      <div className="relative min-h-80 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 pt-24 text-white">
        <div className="container-xl py-16">
          <Reveal>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">University News</h1>
          </Reveal>
          <Reveal className="mt-4">
            <p className="text-lg text-primary-100 max-w-2xl">
              Stay updated with the latest announcements, achievements, and events from Tima-Ade University.
            </p>
          </Reveal>
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="container-xl py-4 text-sm text-slate-600 flex items-center gap-2">
        <Link href="/" className="hover:text-primary-700 transition">Home</Link>
        <ChevronRight size={16} />
        <span className="text-primary-700 font-medium">News</span>
      </div>

      {/* Search and Filter Section */}
      <section className="py-12 bg-white border-b border-slate-200">
        <div className="container-xl">
          <Reveal>
            <div className="flex flex-col md:flex-row gap-6">
              {/* Search */}
              <div className="flex-1 relative">
                <Search className="absolute left-4 top-3.5 text-slate-400" size={20} />
                <input
                  type="text"
                  placeholder="Search news..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full pl-12 pr-4 py-3 rounded-lg border border-slate-200 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 transition"
                />
              </div>
            </div>
          </Reveal>

          {/* Categories */}
          <Reveal className="mt-6">
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
          </Reveal>
        </div>
      </section>

      {/* Featured Articles */}
      {featuredArticles.length > 0 && (
        <section className="py-16 bg-gradient-to-b from-white to-slate-50">
          <div className="container-xl">
            <Reveal className="mb-12">
              <h2 className="text-2xl md:text-3xl font-bold text-slate-900">Featured Stories</h2>
            </Reveal>

            <div className="grid md:grid-cols-2 gap-8">
              {featuredArticles.map((article) => (
                <Reveal key={article.id}>
                  <article className="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl transition">
                    <div className="aspect-video bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center group-hover:from-primary-200 group-hover:to-primary-300 transition">
                      <div className="text-center">
                        <div className="text-6xl font-bold text-primary-200">{article.id}</div>
                      </div>
                    </div>
                    <div className="p-6">
                      <div className="flex items-center gap-2 mb-3">
                        <Tag size={16} className="text-primary-700" />
                        <span className="text-sm font-semibold text-primary-700">{article.category}</span>
                      </div>
                      <h3 className="text-xl font-bold text-slate-900 mb-3 group-hover:text-primary-700 transition">
                        {article.title}
                      </h3>
                      <p className="text-slate-600 mb-4 line-clamp-2">{article.excerpt}</p>
                      <div className="flex items-center justify-between text-sm text-slate-500">
                        <div className="flex items-center gap-4">
                          <div className="flex items-center gap-1">
                            <Calendar size={16} />
                            {new Date(article.date).toLocaleDateString()}
                          </div>
                          <div className="flex items-center gap-1">
                            <User size={16} />
                            {article.author}
                          </div>
                        </div>
                      </div>
                      <Link href={`/news/${article.id}`} className="inline-flex items-center gap-2 mt-4 text-primary-700 font-semibold hover:gap-3 transition">
                        Read More <ArrowRight size={16} />
                      </Link>
                    </div>
                  </article>
                </Reveal>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* All News Articles */}
      <section className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="mb-12">
            <h2 className="text-2xl md:text-3xl font-bold text-slate-900">
              {selectedCategory === 'All' ? 'All News' : `${selectedCategory} News`}
            </h2>
          </Reveal>

          {regularArticles.length > 0 ? (
            <div className="space-y-6">
              {regularArticles.map((article) => (
                <Reveal key={article.id}>
                  <article className="group bg-gradient-to-r from-slate-50 to-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition">
                    <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                      <div className="flex-1">
                        <div className="flex items-center gap-3 mb-2">
                          <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-primary-100 text-primary-700 text-xs font-semibold">
                            <Tag size={12} />
                            {article.category}
                          </span>
                          <span className="text-sm text-slate-500 flex items-center gap-1">
                            <Calendar size={14} />
                            {new Date(article.date).toLocaleDateString()}
                          </span>
                        </div>
                        <h3 className="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-700 transition">
                          {article.title}
                        </h3>
                        <p className="text-slate-600 mb-3">{article.excerpt}</p>
                        <div className="flex items-center gap-1 text-sm text-slate-500">
                          <User size={14} />
                          By {article.author}
                        </div>
                      </div>
                      <Link href={`/news/${article.id}`} className="inline-flex items-center justify-center h-10 w-10 rounded-full bg-primary-100 text-primary-700 hover:bg-primary-200 transition flex-shrink-0">
                        <ArrowRight size={20} />
                      </Link>
                    </div>
                  </article>
                </Reveal>
              ))}
            </div>
          ) : (
            <Reveal>
              <div className="text-center py-16">
                <p className="text-lg text-slate-600">No news articles found matching your criteria.</p>
              </div>
            </Reveal>
          )}
        </div>
      </section>

      {/* Newsletter CTA */}
      <section className="py-16 md:py-24 bg-primary-50 border-y border-primary-200">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Stay Updated</h2>
            <p className="text-lg text-slate-600 max-w-2xl mx-auto mb-8">
              Subscribe to our newsletter to receive the latest news, announcements, and updates from Tima-Ade University.
            </p>
            <form className="max-w-md mx-auto flex gap-3">
              <input
                type="email"
                placeholder="Enter your email"
                className="flex-1 px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:outline-none transition"
              />
              <button className="px-6 py-3 bg-primary-700 text-white rounded-lg font-semibold hover:bg-primary-800 transition">
                Subscribe
              </button>
            </form>
          </div>
        </Reveal>
      </section>
    </PublicLayout>
  )
}
