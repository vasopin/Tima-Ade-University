import Image from 'next/image'
import { Award, Briefcase, Scale } from 'lucide-react'
import { AboutPage, CallToAction, PageIntro, SectionHeading } from '../../../components/AboutPageShell'

const boardMembers = [
  { name: 'Board member profile to be confirmed', title: 'Chair, Board of Directors', background: 'Verified professional background pending', bio: 'The official name, biography, and portrait will be published when confirmed by the university.', image: '/images/home/community.jpg' },
  { name: 'Board member profile to be confirmed', title: 'Deputy Chair', background: 'Verified professional background pending', bio: 'The official name, biography, and portrait will be published when confirmed by the university.', image: '/images/home/campus.jpg' },
  { name: 'Board member profile to be confirmed', title: 'Academic governance representative', background: 'Verified professional background pending', bio: 'The official name, biography, and portrait will be published when confirmed by the university.', image: '/images/home/library.jpg' },
  { name: 'Board member profile to be confirmed', title: 'Community representative', background: 'Verified professional background pending', bio: 'The official name, biography, and portrait will be published when confirmed by the university.', image: '/images/home/tech.jpg' },
]

export default function BoardOfDirectors() {
  return (
    <AboutPage>
      <PageIntro eyebrow="Governance" title="Board of Directors" description="The Tima-Ade University Board of Directors provides strategic oversight, protects the university’s mission, and stewards its future with care and accountability." />
      <section className="py-16 md:py-24">
        <div className="container-xl">
          <SectionHeading eyebrow="Institutional leadership" title="Stewardship with a clear public purpose" description="Our Board brings together experience from education, enterprise, finance, and community leadership. Together, its members safeguard academic quality and guide the university’s responsible development." />
          <div className="mt-12 grid gap-6 md:grid-cols-2">
            {boardMembers.map((member) => <article key={member.name} className="grid overflow-hidden border border-slate-200 bg-white shadow-card sm:grid-cols-[180px_1fr]"><div className="relative min-h-56 sm:min-h-full"><Image src={member.image} alt="" fill sizes="(max-width: 640px) 100vw, 180px" className="object-cover" /></div><div className="p-6 md:p-8"><p className="text-xs font-bold uppercase tracking-[0.16em] text-primary-500">{member.title}</p><h2 className="mt-3 text-2xl font-bold tracking-[-0.03em] text-slate-950">{member.name}</h2><p className="mt-2 text-sm font-semibold text-slate-500">{member.background}</p><p className="mt-5 text-sm leading-7 text-slate-600">{member.bio}</p></div></article>)}
          </div>
        </div>
      </section>
      <section className="border-y border-slate-200 bg-white py-16 md:py-20"><div className="container-xl grid gap-6 md:grid-cols-3"><div className="flex gap-4"><Scale className="shrink-0 text-primary-500" /><div><h3 className="font-bold text-slate-900">Good governance</h3><p className="mt-2 text-sm leading-6 text-slate-600">Transparent decisions aligned to institutional purpose.</p></div></div><div className="flex gap-4"><Award className="shrink-0 text-primary-500" /><div><h3 className="font-bold text-slate-900">Academic quality</h3><p className="mt-2 text-sm leading-6 text-slate-600">Oversight that keeps learning and research at the center.</p></div></div><div className="flex gap-4"><Briefcase className="shrink-0 text-primary-500" /><div><h3 className="font-bold text-slate-900">Long-term stewardship</h3><p className="mt-2 text-sm leading-6 text-slate-600">Careful planning for a resilient university.</p></div></div></div></section>
      <CallToAction title="A community guided by purpose" description="Discover the people, programmes, and places that make Tima-Ade University distinctive." href="/about/campuses" label="Explore our campuses" />
    </AboutPage>
  )
}
