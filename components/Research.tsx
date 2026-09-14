"use client"

import { motion } from 'framer-motion'

export default function Research(){
  const items = [
    { title: 'Center for AI', desc: 'Interdisciplinary research in machine intelligence.', cta: 'Explore Research' },
    { title: 'Materials Innovation Lab', desc: 'Leading research in sustainable materials.', cta: 'Learn More' },
    { title: 'Publications', desc: 'Latest journals & publications from faculty.', cta: 'Browse' },
  ]

  return (
    <section>
      <div className="flex items-center justify-between mb-6">
        <h3 className="h2">Research & Innovation</h3>
        <a className="px-3 py-2 rounded-md bg-primary-500 text-white">Explore Research</a>
      </div>

      <div className="grid md:grid-cols-3 gap-6">
        {items.map((it, idx) => (
          <motion.div key={idx} initial={{ opacity:0,y:12 }} animate={{ opacity:1,y:0 }} transition={{ delay: idx * 0.08 }} className="bg-white p-6 rounded-xl shadow-card">
            <h4 className="font-semibold">{it.title}</h4>
            <p className="text-slate-500 mt-2">{it.desc}</p>
            <div className="mt-4">
              <a className="text-primary-700 font-semibold">{it.cta} →</a>
            </div>
          </motion.div>
        ))}
      </div>
    </section>
  )
}
