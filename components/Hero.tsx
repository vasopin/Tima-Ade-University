"use client"

import { motion } from 'framer-motion'
import Link from 'next/link'
import Image from 'next/image'
import { ArrowRight, Play } from 'lucide-react'

export default function Hero() {
  return (
    <section className="relative flex min-h-[760px] items-center overflow-hidden bg-slate-950">
      <div className="absolute inset-0">
        <Image
          src="/images/home/campus.jpg"
          alt="Campus at golden hour"
          fill
          style={{ objectFit: 'cover' }}
          priority
        />
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(93,92,230,0.30),_transparent_30%),linear-gradient(90deg,rgba(8,15,33,0.82)_0%,rgba(15,23,42,0.62)_40%,rgba(15,23,42,0.2)_100%)]" />
        <div className="absolute inset-0 bg-gradient-to-r from-slate-950/75 via-slate-900/35 to-transparent" />
      </div>

      <div className="container-xl relative z-10 py-24 md:py-28 lg:py-32">
        <div className="grid items-center gap-10 lg:grid-cols-[1.2fr_0.8fr]">
          <motion.div
            initial={{ opacity: 0, y: 16 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.08, duration: 0.7, ease: 'easeOut' }}
            className="max-w-2xl"
          >
            <p className="eyebrow">Accredited • Globally Connected • Student Focused</p>

            <h1 className="mt-6 text-white text-4xl md:text-5xl lg:text-[4.2rem] font-black leading-[0.96] tracking-[-0.06em] max-w-xl">
              Empowering Minds.<br />
              <span className="bg-gradient-to-r from-sky-200 via-white to-indigo-200 bg-clip-text text-transparent">
                Shaping the Future.
              </span>
            </h1>

            <p className="mt-5 max-w-xl text-base leading-7 text-slate-200 md:text-lg">
              A world-class enterprise university combining research excellence with student-centered learning pathways.
              Explore programs, connect with faculty, and advance your career with confidence.
            </p>

            <div className="mt-8 flex flex-wrap items-center gap-4">
              <Link
                href="/programs"
                className="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3.5 text-sm font-semibold text-primary-700 shadow-[0_18px_36px_rgba(12,16,35,0.25)] transition hover:-translate-y-0.5 hover:bg-slate-100"
              >
                Explore Programs
                <ArrowRight size={16} />
              </Link>
              <Link
                href="/apply"
                className="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/8 px-6 py-3.5 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/14"
              >
                <Play size={15} className="fill-current" />
                Apply Now
              </Link>
            </div>

            <div className="mt-8 flex flex-wrap items-center gap-6 text-sm text-slate-200">
              <div>
                <span className="block text-2xl font-bold text-white">25+</span>
                <span className="text-slate-300">Years of Excellence</span>
              </div>
              <div>
                <span className="block text-2xl font-bold text-white">120+</span>
                <span className="text-slate-300">Global Partners</span>
              </div>
            </div>
          </motion.div>

          <motion.div
            className="relative lg:justify-self-end"
            initial={{ opacity: 0, y: 18 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.18, duration: 0.7, ease: 'easeOut' }}
          >
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 max-w-md lg:ml-auto">
              <div className="glass-card rounded-2xl p-4 md:p-5">
                <div className="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Ranking</div>
                <div className="mt-3 text-2xl font-black tracking-[-0.04em] text-slate-900">Top 100</div>
                <div className="mt-2 text-sm leading-6 text-slate-600">Global research and innovation index</div>
              </div>
              <div className="glass-card rounded-2xl p-4 md:p-5">
                <div className="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Programs</div>
                <div className="mt-3 text-2xl font-black tracking-[-0.04em] text-slate-900">120+</div>
                <div className="mt-2 text-sm leading-6 text-slate-600">International pathways and degrees</div>
              </div>
              <div className="glass-card rounded-2xl p-4 md:p-5 sm:translate-y-4">
                <div className="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Scholarships</div>
                <div className="mt-3 text-2xl font-black tracking-[-0.04em] text-slate-900">Merit</div>
                <div className="mt-2 text-sm leading-6 text-slate-600">Need-based support for bright students</div>
              </div>
              <div className="glass-card rounded-2xl p-4 md:p-5">
                <div className="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Campus</div>
                <div className="mt-3 text-2xl font-black tracking-[-0.04em] text-slate-900">Modern</div>
                <div className="mt-2 text-sm leading-6 text-slate-600">Labs, libraries, and world-class facilities</div>
              </div>
            </div>
          </motion.div>
        </div>
      </div>

      <svg className="absolute -bottom-16 right-[-8rem] w-[32rem] opacity-45" viewBox="0 0 600 600" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
        <defs>
          <linearGradient id="g" x1="0" x2="1">
            <stop offset="0" stopColor="#7c3aed" />
            <stop offset="1" stopColor="#1f3b8a" />
          </linearGradient>
        </defs>
        <path d="M0 200 C150 100 450 300 600 200 L600 600 L0 600 Z" fill="url(#g)" opacity="0.18" />
      </svg>
    </section>
  )
}
