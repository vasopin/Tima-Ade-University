"use client"

import { useEffect, useRef, useState } from 'react'
import { motion } from 'framer-motion'

function useInView(elRef: any) {
  const [inView, setInView] = useState(false)
  useEffect(() => {
    if (!elRef.current) return
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) setInView(true)
      })
    }, { threshold: 0.25 })
    obs.observe(elRef.current)
    return () => obs.disconnect()
  }, [elRef])
  return inView
}

function AnimatedNumber({ value }: { value: number }) {
  const ref = useRef<HTMLSpanElement | null>(null)
  const [display, setDisplay] = useState(0)
  useEffect(() => {
    let raf = 0
    const duration = 1200
    const start = performance.now()
    const step = (now: number) => {
      const t = Math.min(1, (now - start) / duration)
      const eased = t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t
      setDisplay(Math.round(eased * value))
      if (t < 1) raf = requestAnimationFrame(step)
    }
    raf = requestAnimationFrame(step)
    return () => cancelAnimationFrame(raf)
  }, [value])
  return <span className="text-3xl md:text-4xl font-black tracking-[-0.06em] text-slate-900">{display.toLocaleString()}</span>
}

export default function Stats() {
  const ref = useRef(null)
  const inView = useInView(ref)

  const statData = [
    { label: 'Students', value: 10000, postfix: '+' },
    { label: 'Programs', value: 120, postfix: '+' },
    { label: 'Faculty', value: 500, postfix: '+' },
    { label: 'Years of Excellence', value: 25, postfix: '+' },
    { label: 'Research Projects', value: 50, postfix: '+' }
  ]

  return (
    <div ref={ref} className="py-8 md:py-10">
      <div className="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <h3 className="h2">University at a glance</h3>
        <p className="max-w-xl text-sm leading-6 text-slate-500 md:text-right">
          A student-first learning environment designed for academic excellence, global opportunity, and meaningful impact.
        </p>
      </div>

      <div className="grid grid-cols-2 gap-4 md:grid-cols-5">
        {statData.map((s) => (
          <motion.div
            key={s.label}
            initial={{ opacity: 0, y: 12 }}
            animate={inView ? { opacity: 1, y: 0 } : {}} 
            transition={{ duration: 0.45 }}
          >
            <div className="premium-card h-full rounded-2xl p-5 text-center md:p-6">
              <div className="flex justify-center">
                {inView ? <AnimatedNumber value={s.value} /> : <span className="text-3xl md:text-4xl font-black tracking-[-0.06em] text-slate-900">0</span>}
                <span className="ml-1 text-xl font-semibold text-primary-700">{s.postfix}</span>
              </div>
              <div className="mt-3 text-sm font-medium text-slate-600">{s.label}</div>
            </div>
          </motion.div>
        ))}
      </div>
    </div>
  )
}
