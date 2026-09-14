import Image from 'next/image'

export default function Footer(){
  return (
    <footer className="bg-slate-950 text-white py-14 md:py-16">
      <div className="container-xl grid gap-8 md:grid-cols-4">
        <div>
          <div className="flex items-center gap-3">
            <div className="flex-shrink-0">
              <Image src="/images/tima-ade-university-logo.png" alt="Tima-Ade University logo" width={1288} height={1221} className="h-14 w-14 shrink-0 object-contain" />
            </div>
            <div>
              <div className="font-semibold tracking-[-0.03em]">Tima-Ade University</div>
              <div className="text-sm text-slate-300">Gabiley, Somaliland</div>
            </div>
          </div>

          <p className="mt-4 max-w-xs text-sm leading-6 text-slate-400">
            Leading university focused on research, innovation, and student success worldwide.
          </p>
        </div>

        <div>
          <h4 className="mb-4 text-sm font-semibold uppercase tracking-[0.16em] text-slate-300">Quick Links</h4>
          <ul className="space-y-3 text-sm text-slate-300">
            <li><a className="transition hover:text-white">About</a></li>
            <li><a className="transition hover:text-white">Academics</a></li>
            <li><a className="transition hover:text-white">Admissions</a></li>
            <li><a className="transition hover:text-white">Contact</a></li>
          </ul>
        </div>

        <div>
          <h4 className="mb-4 text-sm font-semibold uppercase tracking-[0.16em] text-slate-300">Resources</h4>
          <ul className="space-y-3 text-sm text-slate-300">
            <li><a className="transition hover:text-white">Student Portal</a></li>
            <li><a className="transition hover:text-white">LMS</a></li>
            <li><a className="transition hover:text-white">Library</a></li>
            <li><a className="transition hover:text-white">Careers</a></li>
          </ul>
        </div>

        <div>
          <h4 className="mb-4 text-sm font-semibold uppercase tracking-[0.16em] text-slate-300">Contact</h4>
          <div className="space-y-2 text-sm text-slate-300">
            <div>123 University Ave, City, Country</div>
            <div>admissions@timaade.edu</div>
            <div>+1 (555) 123-4567</div>
          </div>

          <div className="mt-5">
            <form>
              <label htmlFor="newsletter" className="text-sm text-slate-300">Subscribe</label>
              <div className="mt-2 flex">
                <input id="newsletter" type="email" placeholder="Email address" className="w-full rounded-l-full border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-primary-300 focus:outline-none" />
                <button className="rounded-r-full bg-primary-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary-400">Subscribe</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div className="container-xl mt-10 flex flex-col gap-4 border-t border-slate-800 pt-6 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
        <div>© {new Date().getFullYear()} Tima-Ade University. All rights reserved.</div>
        <div className="flex gap-5">
          <a className="transition hover:text-white">Privacy</a>
          <a className="transition hover:text-white">Terms</a>
          <a className="transition hover:text-white">Security</a>
        </div>
      </div>
    </footer>
  )
}
