import Link from 'next/link'
import { ArrowRight, CheckCircle2 } from 'lucide-react'
import Navbar from './Navbar'
import Footer from './Footer'

export function PageIntro({ eyebrow, title, description }: { eyebrow: string; title: string; description: string }) {
  return (
    <section className="bg-primary-900 pb-16 pt-32 text-white md:pb-20 md:pt-40">
      <div className="container-xl">
        <div className="max-w-3xl">
          <div className="eyebrow">{eyebrow}</div>
          <h1 className="mt-6 text-4xl font-bold leading-tight tracking-[-0.04em] md:text-6xl">{title}</h1>
          <p className="mt-6 max-w-2xl text-base leading-8 text-slate-200 md:text-lg">{description}</p>
        </div>
      </div>
    </section>
  )
}

export function SectionHeading({ eyebrow, title, description }: { eyebrow: string; title: string; description?: string }) {
  return (
    <div className="max-w-2xl">
      <p className="text-xs font-bold uppercase tracking-[0.2em] text-primary-500">{eyebrow}</p>
      <h2 className="mt-3 text-3xl font-bold tracking-[-0.04em] text-slate-950 md:text-4xl">{title}</h2>
      {description && <p className="mt-4 leading-7 text-slate-600">{description}</p>}
    </div>
  )
}

export function AboutPage({ children }: { children: React.ReactNode }) {
  return (
    <>
      <Navbar />
      <main className="overflow-hidden">{children}</main>
      <Footer />
    </>
  )
}

export function CallToAction({ title, description, href = '/apply', label = 'Begin your journey' }: { title: string; description: string; href?: string; label?: string }) {
  return (
    <section className="bg-primary-900 py-16 text-white md:py-20">
      <div className="container-xl flex flex-col gap-8 md:flex-row md:items-center md:justify-between">
        <div className="max-w-2xl">
          <h2 className="text-3xl font-bold tracking-[-0.04em] md:text-4xl">{title}</h2>
          <p className="mt-4 leading-7 text-slate-200">{description}</p>
        </div>
        <Link href={href} className="inline-flex shrink-0 items-center justify-center gap-2 bg-white px-5 py-3 text-sm font-bold text-primary-900 hover:bg-primary-50">
          {label} <ArrowRight size={16} aria-hidden="true" />
        </Link>
      </div>
    </section>
  )
}

export function DetailList({ items }: { items: string[] }) {
  return (
    <ul className="space-y-3">
      {items.map((item) => (
        <li key={item} className="flex gap-3 text-sm leading-6 text-slate-600">
          <CheckCircle2 size={18} className="mt-0.5 shrink-0 text-primary-500" aria-hidden="true" />
          <span>{item}</span>
        </li>
      ))}
    </ul>
  )
}
