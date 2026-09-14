import Image from 'next/image'
import { Building2, FlaskConical, Library, MapPin, Users } from 'lucide-react'
import { AboutPage, CallToAction, PageIntro, SectionHeading } from '../../../components/AboutPageShell'

const campuses = [
  { name: 'Main campus details to be confirmed', location: 'Gabiley, Somaliland', image: '/images/home/campus.jpg', overview: 'The university is based in Gabiley, Somaliland. Official campus overview information will be published when confirmed.', facilities: 'Official facilities list to be confirmed by the university' },
  { name: 'Additional campus details to be confirmed', location: 'Location to be confirmed', image: '/images/home/tech.jpg', overview: 'Information about any additional Tima-Ade University campus will be published after official confirmation.', facilities: 'Official facilities list to be confirmed by the university' },
]

const highlights = [{ icon: Building2, label: 'Academic buildings' }, { icon: Library, label: 'Central library' }, { icon: FlaskConical, label: 'Science laboratories' }, { icon: Users, label: 'Student facilities' }]

export default function Campuses() {
  return (
    <AboutPage>
      <PageIntro eyebrow="Places to learn and belong" title="Our campuses" description="Tima-Ade University’s campuses are designed to make learning practical, community-centered, and connected to the wider world." />
      <section className="py-16 md:py-24"><div className="container-xl"><SectionHeading eyebrow="Campus locations" title="Spaces designed for possibility" description="From focused study to collaboration and student life, each Tima-Ade location supports the full university experience." /><div className="mt-12 grid gap-6 lg:grid-cols-3">{campuses.map((campus) => <article key={campus.name} className="overflow-hidden border border-slate-200 bg-white shadow-card"><div className="relative h-56"><Image src={campus.image} alt={campus.name} fill sizes="(max-width: 1024px) 100vw, 33vw" className="object-cover" /></div><div className="p-6"><div className="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-primary-500"><MapPin size={14} aria-hidden="true" /> {campus.location}</div><h2 className="mt-4 text-2xl font-bold tracking-[-0.03em] text-slate-950">{campus.name}</h2><p className="mt-4 text-sm leading-7 text-slate-600">{campus.overview}</p><div className="mt-5 border-t border-slate-200 pt-5"><p className="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Facilities</p><p className="mt-2 text-sm leading-6 text-slate-700">{campus.facilities}</p></div></div></article>)}</div></div></section>
      <section className="bg-slate-100 py-16 md:py-20"><div className="container-xl grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-center"><div><SectionHeading eyebrow="Campus highlights" title="Everything needed to do your best work" description="Well-considered facilities create room for focused study, meaningful connection, and ambitious ideas." /></div><div className="grid grid-cols-2 gap-4">{highlights.map(({ icon: Icon, label }) => <div key={label} className="border border-slate-200 bg-white p-5"><Icon className="text-primary-500" aria-hidden="true" /><p className="mt-5 font-bold text-slate-900">{label}</p></div>)}</div></div></section>
      <section className="border-b border-slate-200 bg-white py-14"><div className="container-xl flex flex-col gap-5 md:flex-row md:items-center md:justify-between"><div><p className="text-xs font-bold uppercase tracking-[0.2em] text-primary-500">Plan a visit</p><h2 className="mt-2 text-2xl font-bold text-slate-950">Campus visit information</h2></div><div className="text-sm leading-7 text-slate-600"><p className="flex items-center gap-2"><MapPin size={16} className="text-primary-500" /> Gabiley, Somaliland</p><p className="mt-1">Contact details to be confirmed by the university</p></div></div></section>
      <CallToAction title="See where your next chapter begins" description="Connect with our admissions team to arrange a campus visit or learn more about studying at Tima-Ade." />
    </AboutPage>
  )
}
