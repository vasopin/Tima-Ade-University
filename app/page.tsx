import Navbar from '../components/Navbar'
import Hero from '../components/Hero'
import QuickAccess from '../components/QuickAccess'
import Stats from '../components/Stats'
import Programs from '../components/Programs'
import Facilities from '../components/Facilities'
import Research from '../components/Research'
import CampusLife from '../components/CampusLife'
import News from '../components/News'
import NoticeBoard from '../components/NoticeBoard'
import Testimonials from '../components/Testimonials'
import Partners from '../components/Partners'
import CTA from '../components/CTA'
import Footer from '../components/Footer'

export default function Home(){
  return (
    <>
      <Navbar />
      <main className="overflow-x-hidden">
        <Hero />
        <section className="relative z-10 -mt-10 pb-8 md:-mt-12">
          <div className="container-xl">
            <QuickAccess />
          </div>
        </section>

        <section className="py-12 md:py-16 bg-transparent">
          <div className="container-xl">
            <Stats />
          </div>
        </section>

        <section className="py-12">
          <div className="container-xl">
            <Programs />
          </div>
        </section>

        <section className="py-12 bg-slate-50">
          <div className="container-xl">
            <Facilities />
          </div>
        </section>

        <section className="py-12">
          <div className="container-xl">
            <Research />
          </div>
        </section>

        <section className="py-12 bg-slate-50">
          <div className="container-xl">
            <CampusLife />
          </div>
        </section>

        <section className="py-12">
          <div className="container-xl grid lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2">
              <News />
            </div>
            <aside>
              <NoticeBoard />
              <Testimonials />
            </aside>
          </div>
        </section>

        <section className="py-12 bg-white">
          <div className="container-xl">
            <Partners />
          </div>
        </section>

        <CTA />
      </main>

      <Footer />
    </>
  )
}
