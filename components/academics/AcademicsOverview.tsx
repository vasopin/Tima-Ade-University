"use client"

import { motion } from 'framer-motion'
import { ArrowRight, BookOpen, Lightbulb, Sparkles } from 'lucide-react'

const highlights = [
  { title: 'Academic Freedom', text: 'Explore interdisciplinary learning that connects theory, practice, and real-world impact.', icon: BookOpen },
  { title: 'Research-led Teaching', text: 'Learn from faculty who are shaping new knowledge and leading innovation across sectors.', icon: Sparkles },
  { title: 'Career Readiness', text: 'Build practical capabilities, industry exposure, and professional competencies from day one.', icon: Lightbulb }
]

export default function AcademicsOverview() {
  return (
    <section className="py-16 md:py-20">
      <div className="container-xl grid lg:grid-cols-2 gap-10 items-center">
        <motion.div
          initial={{ opacity: 0, x: -24 }}
          whileInView={{ opacity: 1, x: 0 }}
          viewport={{ once: true, amount: 0.3 }}
          transition={{ duration: 0.6 }}
        >
          <p className="text-sm font-semibold uppercase tracking-[0.2em] text-primary-500">Why choose us</p>
          <h2 className="h2 mt-4">An academic experience designed to unlock potential.</h2>
          <p className="mt-5 text-base leading-8 text-slate-600">
            Our academic model balances rigorous study with practical application. Students benefit from flexible pathways, faculty mentorship, cutting-edge facilities, and a learning community rooted in curiosity, service, and leadership.
          </p>
          <div className="mt-7 flex items-center gap-2 text-sm font-medium text-primary-700">
            Explore admissions <ArrowRight size={16} />
          </div>
        </motion.div>

        <div className="space-y-5">
          {highlights.map((item, index) => {
            const Icon = item.icon

            return (
              <motion.div
                key={item.title}
                initial={{ opacity: 0, y: 18 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, amount: 0.4 }}
                transition={{ duration: 0.45, delay: index * 0.08 }}
                className="rounded-2xl border border-slate-200 bg-white p-5 shadow-card"
              >
                <div className="flex items-start gap-4">
                  <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 text-primary-500">
                    <Icon size={22} />
                  </div>
                  <div>
                    <h3 className="text-xl font-semibold text-slate-900">{item.title}</h3>
                    <p className="mt-2 text-sm leading-6 text-slate-600">{item.text}</p>
                  </div>
                </div>
              </motion.div>
            )
          })}
        </div>
      </div>
    </section>
  )
}
