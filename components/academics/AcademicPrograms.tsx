"use client"

import { useState } from 'react'
import Image from 'next/image'
import Link from 'next/link'
import { motion, AnimatePresence } from 'framer-motion'

const programs = [
  { id: 1, title: 'B.Sc. Computer Science', type: 'Undergraduate', faculty: 'Faculty of Computing', duration: '3 Years', image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80' },
  { id: 2, title: 'MBA in Business Analytics', type: 'Graduate', faculty: 'Faculty of Business', duration: '2 Years', image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80' },
  { id: 3, title: 'PhD in Sustainable Engineering', type: 'Doctoral', faculty: 'Faculty of Engineering', duration: '4 Years', image: 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=1200&q=80' },
  { id: 4, title: 'Professional Certificate in UX', type: 'Professional', faculty: 'Continuing Education', duration: '6 Months', image: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80' },
  { id: 5, title: 'MSc Data Science', type: 'Graduate', faculty: 'Faculty of Computing', duration: '18 Months', image: 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1200&q=80' },
  { id: 6, title: 'BA in Education Leadership', type: 'Undergraduate', faculty: 'Faculty of Education', duration: '4 Years', image: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80' }
]

const filters = ['All', 'Undergraduate', 'Graduate', 'Doctoral', 'Professional']

export default function AcademicPrograms() {
  const [filter, setFilter] = useState('All')
  const visible = filter === 'All' ? programs : programs.filter((program) => program.type === filter)

  return (
    <section className="py-16 md:py-20 bg-slate-50">
      <div className="container-xl">
        <div className="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
          <div className="max-w-2xl">
            <p className="text-sm font-semibold uppercase tracking-[0.2em] text-primary-500">Programs</p>
            <h2 className="h2 mt-3">Programs designed for ambition, flexibility, and impact.</h2>
          </div>

          <div className="flex flex-wrap gap-2">
            {filters.map((item) => (
              <button
                key={item}
                onClick={() => setFilter(item)}
                className={`rounded-full px-4 py-2 text-sm font-medium border transition ${
                  filter === item ? 'bg-primary-500 border-primary-500 text-white shadow' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'
                }`}
                aria-pressed={filter === item}
              >
                {item}
              </button>
            ))}
          </div>
        </div>

        <AnimatePresence mode="popLayout">
          <div className="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            {visible.map((program, index) => (
              <motion.article
                key={program.id}
                layout
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -8 }}
                transition={{ duration: 0.3, delay: index * 0.04 }}
                className="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-card"
              >
                <div className="relative h-52 w-full">
                  <Image src={program.image} alt={program.title} fill className="object-cover" />
                </div>
                <div className="p-6">
                  <div className="text-xs uppercase tracking-[0.18em] text-primary-500">{program.type}</div>
                  <h3 className="mt-3 text-xl font-semibold text-slate-900">{program.title}</h3>
                  <div className="mt-2 text-sm text-slate-500">{program.faculty}</div>
                  <div className="mt-4 flex items-center justify-between text-sm text-slate-600">
                    <span>Duration</span>
                    <span className="font-medium text-slate-800">{program.duration}</span>
                  </div>
                  <div className="mt-5 flex gap-3">
                    <Link href="/admissions" className="inline-flex items-center justify-center rounded-md bg-primary-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-primary-700">
                      View Program
                    </Link>
                    <button className="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300">
                      Brochure
                    </button>
                  </div>
                </div>
              </motion.article>
            ))}
          </div>
        </AnimatePresence>
      </div>
    </section>
  )
}
