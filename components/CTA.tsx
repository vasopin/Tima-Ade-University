export default function CTA(){
  return (
    <section className="relative overflow-hidden bg-gradient-to-r from-primary-700 via-primary-700 to-accent py-16 text-white">
      <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.18),_transparent_30%)]" />
      <div className="container-xl relative grid items-center gap-8 md:grid-cols-2">
        <div>
          <p className="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">Next steps</p>
          <h2 className="text-3xl font-black tracking-[-0.05em] md:text-4xl">Your future starts here.</h2>
          <p className="mt-3 max-w-xl text-base text-white/85 md:text-lg">
            Apply now or explore programs and contact admissions for personalized guidance that matches your goals.
          </p>
        </div>
        <div className="flex flex-wrap gap-3 md:justify-end">
          <a className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-primary-700 shadow-[0_18px_30px_rgba(15,23,42,0.22)] transition hover:-translate-y-0.5 hover:bg-slate-100">Apply Now</a>
          <a className="rounded-full border border-white/30 bg-white/5 px-6 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/10">Explore Programs</a>
          <a className="rounded-full bg-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/15">Contact Admissions</a>
        </div>
      </div>
    </section>
  )
}
