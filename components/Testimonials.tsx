"use client"

import { useState } from 'react'
import { motion, AnimatePresence } from 'framer-motion'

const data = [
  { id: 1, name: 'Aisha Khan', role: 'Alumni', quote: 'Tima-Ade University shaped my career and opened global doors.' },
  { id: 2, name: 'Dr. Miguel S.', role: 'Faculty', quote: 'Our research ecosystem supports curiosity and impact.' },
  { id: 3, name: 'Priya Patel', role: 'Student', quote: 'Supportive faculty and vibrant campus life.' },
]

export default function Testimonials(){
  const [idx, setIdx] = useState(0)
  const next = () => setIdx((i) => (i + 1) % data.length)
  const prev = () => setIdx((i) => (i - 1 + data.length) % data.length)

  return (
    <div className="mt-6 bg-white p-4 rounded-xl shadow-card">
      <h4 className="font-semibold mb-3">Testimonials</h4>
      <div className="relative">
        <AnimatePresence mode="wait">
          <motion.div key={data[idx].id} initial={{ opacity: 0, x: 12 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: -12 }} transition={{ duration: 0.4 }} className="min-h-[96px]">
            <blockquote className="text-slate-700">“{data[idx].quote}”</blockquote>
            <div className="mt-3 text-sm text-slate-500">— {data[idx].name}, {data[idx].role}</div>
          </motion.div>
        </AnimatePresence>

        <div className="absolute right-0 top-0 flex gap-2">
          <button onClick={prev} aria-label="Previous" className="p-2 rounded bg-slate-100">‹</button>
          <button onClick={next} aria-label="Next" className="p-2 rounded bg-slate-100">›</button>
        </div>
      </div>
    </div>
  )
}
