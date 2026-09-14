"use client"

import { useEffect, useRef, useState } from 'react'
import Link from 'next/link'
import Image from 'next/image'
import { ChevronDown, Menu, Search, X } from 'lucide-react'
import { motion, AnimatePresence } from 'framer-motion'
import useFocusTrap from './hooks/useFocusTrap'

const navItems = [
  { href: '/', label: 'Home' },
  { href: '/academics', label: 'Programs' },
  { href: '/facilities', label: 'E-Campus' },
  { href: '/admissions', label: 'Admission' },
  { href: '/notice', label: 'Student Life' },
  { href: '/contact', label: 'Contact' },
]

export default function Navbar() {
  const [scrolled, setScrolled] = useState(false)
  const [open, setOpen] = useState(false)
  const panelRef = useRef<HTMLDivElement | null>(null)
  useFocusTrap(panelRef, () => setOpen(false), open)

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24)
    onScroll()
    window.addEventListener('scroll', onScroll)
    return () => window.removeEventListener('scroll', onScroll)
  }, [])

  return (
    <>
      <header
        className={`fixed w-full z-40 transition-all duration-300 ${
          scrolled
            ? 'bg-white/80 backdrop-blur-xl border-b border-slate-200/80 shadow-[0_12px_30px_rgba(15,23,42,0.08)]'
            : 'bg-transparent'
        }`}
        role="banner"
      >
        <nav className="container-xl flex items-center justify-between py-3 lg:py-4">
          <div className="flex items-center gap-4">
            <Link href="/" className="flex items-center gap-3 group" aria-label="Tima-Ade University home">
              <Image src="/images/tima-ade-university-logo.png" alt="Tima-Ade University logo" width={1288} height={1221} className="h-12 w-12 shrink-0 object-contain" />
              <div className="hidden md:block">
                <div className={`font-semibold tracking-[-0.03em] ${scrolled ? 'text-slate-900' : 'text-white'}`}>
                  Tima-Ade University
                </div>
                <div className={`text-xs ${scrolled ? 'text-slate-500' : 'text-slate-200'}`}>
                  Gabiley, Somaliland
                </div>
              </div>
            </Link>
          </div>

          <div className="hidden lg:flex items-center gap-6">
            <Link
              href="/"
              className={`text-sm font-medium ${scrolled ? 'text-slate-700 hover:text-primary-700' : 'text-slate-100 hover:text-white'} transition-colors`}
            >
              Home
            </Link>
            <div className="group relative">
              <Link
                href="/about"
                className={`flex items-center gap-1 text-sm font-medium ${scrolled ? 'text-slate-700 hover:text-primary-700' : 'text-slate-100 hover:text-white'} transition-colors`}
              >
                About <ChevronDown size={14} aria-hidden="true" />
              </Link>
              <div className="invisible absolute left-1/2 top-full z-50 mt-4 w-56 -translate-x-1/2 translate-y-1 opacity-0 transition-all group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                <div className="border border-slate-200 bg-white p-2 shadow-xl">
                  <Link href="/about" className="block px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700">About Tima-Ade</Link>
                  <Link href="/about/board" className="block px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700">Board of Directors</Link>
                  <Link href="/about/campuses" className="block px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700">Campuses</Link>
                </div>
              </div>
            </div>
            {navItems.slice(1).map(({ href, label }) => (
              <Link
                key={label}
                href={href}
                className={`text-sm font-medium ${scrolled ? 'text-slate-700 hover:text-primary-700' : 'text-slate-100 hover:text-white'} transition-colors`}
              >
                {label}
              </Link>
            ))}
            <button
              className={`flex items-center justify-center h-10 w-10 rounded-full border transition ${
                scrolled
                  ? 'border-slate-200 bg-white text-slate-700 hover:border-primary-200 hover:text-primary-700'
                  : 'border-white/25 bg-white/10 text-white hover:bg-white/16'
              }`}
              aria-label="Search"
            >
              <Search size={16} />
            </button>
            <Link
              href="/portal"
              className={`rounded-full border px-4 py-2 text-sm font-semibold transition ${
                scrolled
                  ? 'border-primary-200 bg-white text-primary-700 hover:bg-primary-50'
                  : 'border-white/30 bg-white/10 text-white hover:bg-white/15'
              }`}
            >
              Student Portal
            </Link>
            <Link
              href="/apply"
              className="rounded-full bg-primary-500 px-4 py-2 text-sm font-semibold text-white shadow-[0_12px_25px_rgba(29,60,122,0.28)] transition hover:-translate-y-0.5 hover:bg-primary-700"
            >
              Apply Now
            </Link>
          </div>

          <div className="lg:hidden flex items-center gap-3">
            <button
              onClick={() => setOpen(true)}
              aria-label="Open menu"
              className={`flex h-10 w-10 items-center justify-center rounded-full border ${
                scrolled
                  ? 'border-slate-200 bg-white text-slate-700'
                  : 'border-white/30 bg-white/10 text-white'
              }`}
            >
              <Menu size={20} />
            </button>
          </div>
        </nav>
      </header>

      <AnimatePresence>
        {open && (
          <motion.aside
            initial={{ x: '100%' }}
            animate={{ x: 0 }}
            exit={{ x: '100%' }}
            transition={{ type: 'spring', stiffness: 300, damping: 30 }}
            className="fixed inset-0 z-50 lg:hidden"
            aria-modal="true"
            role="dialog"
          >
            <div className="absolute inset-0 bg-slate-900/60" onClick={() => setOpen(false)} />
            <div ref={panelRef} className="relative ml-auto h-full w-80 overflow-auto bg-white p-6 shadow-2xl" tabIndex={-1}>
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <Image src="/images/tima-ade-university-logo.png" alt="Tima-Ade University logo" width={1288} height={1221} className="h-12 w-12 shrink-0 object-contain" />
                  <div className="font-semibold text-slate-900">Tima-Ade University</div>
                </div>
                <button onClick={() => setOpen(false)} aria-label="Close menu" className="rounded-full p-2 text-slate-700 hover:bg-slate-100">
                  <X />
                </button>
              </div>

              <hr className="my-4 border-slate-200" />

              <ul className="space-y-2">
                {navItems.slice(0, 1).map(({ href, label }) => (
                  <li key={label}>
                    <Link href={href} onClick={() => setOpen(false)} className="block rounded-xl px-3 py-2.5 text-base font-medium text-slate-700 hover:bg-slate-100 hover:text-primary-700">
                      {label}
                    </Link>
                  </li>
                ))}
                <li>
                  <Link href="/about" onClick={() => setOpen(false)} className="block rounded-xl px-3 py-2.5 text-base font-semibold text-primary-700 hover:bg-primary-50">
                    About Tima-Ade
                  </Link>
                  <div className="ml-3 border-l border-slate-200 pl-3">
                    <Link href="/about/board" onClick={() => setOpen(false)} className="block rounded-xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-primary-700">Board of Directors</Link>
                    <Link href="/about/campuses" onClick={() => setOpen(false)} className="block rounded-xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-primary-700">Campuses</Link>
                  </div>
                </li>
                {navItems.slice(1).map(({ href, label }) => (
                  <li key={label}>
                    <Link
                      href={href}
                      onClick={() => setOpen(false)}
                      className="block rounded-xl px-3 py-2.5 text-base font-medium text-slate-700 hover:bg-slate-100 hover:text-primary-700"
                    >
                      {label}
                    </Link>
                  </li>
                ))}
              </ul>

              <div className="mt-6 space-y-3">
                <Link href="/portal" onClick={() => setOpen(false)} className="block rounded-full border border-primary-200 bg-primary-50 px-4 py-2.5 text-center font-semibold text-primary-700 hover:bg-primary-100">
                  Student Portal
                </Link>
                <Link href="/apply" onClick={() => setOpen(false)} className="block rounded-full bg-primary-500 px-4 py-2.5 text-center font-semibold text-white shadow-[0_12px_25px_rgba(29,60,122,0.28)] hover:bg-primary-700">
                  Apply Now
                </Link>
              </div>
            </div>
          </motion.aside>
        )}
      </AnimatePresence>
    </>
  )
}
