"use client"

import Link from 'next/link'
import Image from 'next/image'
import { motion } from 'framer-motion'

export default function AcademicsHero() {
  return (
    <section className="relative overflow-hidden pt-28 pb-18 md:pt-32">
      <div className="absolute inset-0">
        <Image
          src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80"
          alt="Academic lecture hall with students and faculty"
          fill
          priority
          className="object-cover"
        />
        <div className="absolute inset-0 bg-slate-950/55" />
        <div className="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-900/70 to-primary-700/40" />
      </div>

      <div className="container-xl relative z-10">
        <motion.div
          initial={{ opacity: 0, y: 16 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, ease: 'easeOut' }}
          className="max-w-3xl"
        >
          <nav aria-label="Breadcrumb" className="mb-6 text-sm text-slate-200/90">
            <div className="flex items-center gap-2">
              <Link href="/" className="hover:text-white transition-colors">Home</Link>
              <span>→</span>
              <span className="text-white">Academics</span>
            </div>
          </nav>

          <p className="mb-4 text-sm uppercase tracking-[0.25em] text-blue-100/90">Academic excellence</p>
          <h1 className="h1 text-white">Academic Programs Built for Tomorrow.</h1>
          <p className="mt-5 max-w-xl text-base md:text-lg text-slate-200">
            From foundational undergraduate study to advanced research and professional practice, Tima-Ade University empowers learners with ambitious, relevant education.
          </p>
        </motion.div>
      </div>
    </section>
  )
}
