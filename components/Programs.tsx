"use client"

import { useState } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import Image from 'next/image'

const samplePrograms = [
  { id: 1, name: 'B.Sc. Computer Science', type: 'Undergraduate', faculty: 'School of Computing', duration: '3 Years', img: '/images/home/tech.jpg' },
  { id: 2, name: 'MBA in Business Analytics', type: 'Graduate', faculty: 'School of Business', duration: '2 Years', img: '/images/home/classroom.jpg' },
  { id: 3, name: 'PhD in Materials Science', type: 'Doctoral', faculty: 'Engineering', duration: '4+ Years', img: '/images/home/lab.jpg' },
  { id: 4, name: 'MSc Data Science', type: 'Graduate', faculty: 'School of Computing', duration: '1.5 Years', img: '/images/home/tech.jpg' },
  { id: 5, name: 'Professional Certificate in UX', type: 'Professional', faculty: 'Continuing Education', duration: '6 Months', img: '/images/home/community.jpg' },
]

const filters = ['All','Undergraduate','Graduate','Doctoral','Professional']

export default function Programs() {
  const [filter, setFilter] = useState('All')

  const filtered = samplePrograms.filter(p => filter === 'All' || p.type === filter)

  return (
    <section>
      <div className="flex items-center justify-between mb-6">
        <h3 className="h2">Featured Academic Programs</h3>
        <div className="flex gap-2">
          {filters.map(f => (
            <button key={f} onClick={() => setFilter(f)} className={`px-3 py-2 rounded-md ${filter === f ? 'bg-primary-500 text-white' : 'bg-white border'}`}>{f}</button>
          ))}
        </div>
      </div>

      <div className="grid md:grid-cols-3 gap-6">
        <AnimatePresence mode="popLayout">
          {filtered.map((p) => (
            <motion.div key={p.id} layout initial={{ opacity: 0, y: 8 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -6 }} transition={{ duration: 0.3 }} className="bg-white rounded-xl shadow-card overflow-hidden">
              <div className="h-40 bg-slate-100 relative">
                <Image src={p.img} alt={`${p.name}`} fill style={{ objectFit: 'cover' }} />
              </div>
              <div className="p-4">
                <div className="text-sm text-slate-500">{p.faculty}</div>
                <div className="font-semibold mt-1">{p.name}</div>
                <div className="text-sm text-slate-500 mt-2">{p.duration}</div>
                <div className="mt-4 flex gap-2">
                  <a className="px-3 py-2 rounded-md bg-primary-500 text-white">View Program</a>
                  <a className="px-3 py-2 rounded-md border">Brochure</a>
                </div>
              </div>
            </motion.div>
          ))}
        </AnimatePresence>
      </div>
    </section>
  )
}
