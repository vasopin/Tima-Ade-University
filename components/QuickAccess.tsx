"use client"

import { motion } from 'framer-motion'
import { BookOpen, Clipboard, Users, Library, GraduationCap, Award, Home } from 'lucide-react'

const items = [
  { key: 'admissions', title: 'Admissions', icon: Clipboard, desc: 'Apply, requirements & deadlines' },
  { key: 'programs', title: 'Academic Programs', icon: BookOpen, desc: 'Undergraduate, graduate & doctoral' },
  { key: 'portal', title: 'Student Portal', icon: Users, desc: 'Login for students & faculty' },
  { key: 'lms', title: 'LMS', icon: Library, desc: 'Course materials & learning' },
  { key: 'library', title: 'Library', icon: Home, desc: 'Catalogs & research materials' },
  { key: 'research', title: 'Research', icon: GraduationCap, desc: 'Centers & publications' },
  { key: 'scholarships', title: 'Scholarships', icon: Award, desc: 'Funding & awards' },
  { key: 'campus', title: 'Campus Life', icon: Home, desc: 'Clubs, sports & events' },
]

export default function QuickAccess() {
  return (
    <motion.div
      initial={{ opacity: 0, y: 12 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, amount: 0.2 }}
      transition={{ duration: 0.45, ease: 'easeOut' }}
      className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
    >
      {items.map((it, idx) => {
        const Icon = it.icon
        return (
          <motion.a
            key={it.key}
            href={`/${it.key}`}
            whileHover={{ y: -4, scale: 1.01 }}
            whileTap={{ scale: 0.99 }}
            transition={{ type: 'spring', stiffness: 280, damping: 20 }}
            className="group block rounded-2xl border border-slate-200 bg-white p-4 shadow-[0_12px_30px_rgba(15,23,42,0.04)] hover:border-primary-200 hover:shadow-[0_18px_38px_rgba(29,60,122,0.08)]"
            style={{ animationDelay: `${idx * 80}ms` }}
          >
            <div className="flex items-start gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-accent text-white shadow-[0_12px_24px_rgba(93,92,230,0.2)]">
                <Icon size={20} />
              </div>
              <div className="min-w-0">
                <div className="text-base font-semibold text-slate-900">{it.title}</div>
                <div className="mt-1 text-sm leading-6 text-slate-500">{it.desc}</div>
              </div>
            </div>
          </motion.a>
        )
      })}
    </motion.div>
  )
}
