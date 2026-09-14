'use client'

import { useRef, useEffect } from 'react'
import Link from 'next/link'
import { ChevronRight, Award, DollarSign, CheckCircle2, ArrowRight } from 'lucide-react'
import PublicLayout from '../src/layouts/PublicLayout'

function Reveal({ children, className = '' }: { children: React.ReactNode; className?: string }) {
  const ref = useRef<HTMLDivElement | null>(null)
  useEffect(() => {
    const el = ref.current
    if (!el) return
    const prefersReduced = typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches
    if (prefersReduced) {
      el.classList.add('opacity-100', 'translate-y-0')
      return
    }
    el.classList.add('opacity-0', 'translate-y-4')
    const obs = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        el.classList.remove('opacity-0', 'translate-y-4')
        el.classList.add('opacity-100', 'translate-y-0')
        obs.unobserve(el)
      }
    })
    obs.observe(el)
    return () => obs.disconnect()
  }, [])
  return (
    <div ref={ref} className={`transition-all duration-500 ${className}`}>
      {children}
    </div>
  )
}

const scholarshipPrograms = [
  {
    id: 1,
    name: 'Merit-Based Scholarships',
    amount: 'Up to Full Tuition',
    description: 'Awarded to students demonstrating exceptional academic performance and potential.',
    eligibility: 'Minimum 3.5 GPA, excellent entrance exam scores',
    deadline: 'March 31, 2025',
  },
  {
    id: 2,
    name: 'Need-Based Financial Aid',
    amount: 'Varies',
    description: 'Assistance for students demonstrating financial need.',
    eligibility: 'Complete FAFSA, demonstrated financial need',
    deadline: 'February 15, 2025',
  },
  {
    id: 3,
    name: 'International Excellence Award',
    amount: '$10,000 - $25,000/year',
    description: 'For international students showing academic excellence.',
    eligibility: 'Non-citizen international students, 3.7+ GPA',
    deadline: 'April 15, 2025',
  },
  {
    id: 4,
    name: 'First Generation Scholarship',
    amount: 'Up to $15,000/year',
    description: 'Support for first-generation college students.',
    eligibility: 'Neither parent attended 4-year university',
    deadline: 'March 1, 2025',
  },
  {
    id: 5,
    name: 'STEM Excellence Award',
    amount: '$12,000/year',
    description: 'For students pursuing Science, Technology, Engineering, or Math.',
    eligibility: 'STEM major, 3.5+ GPA',
    deadline: 'April 1, 2025',
  },
  {
    id: 6,
    name: 'Leadership Scholarship',
    amount: '$8,000/year',
    description: 'Recognizing significant community and campus leadership.',
    eligibility: 'Demonstrated leadership experience, 3.0+ GPA',
    deadline: 'March 15, 2025',
  },
]

export default function ScholarshipsPage() {
  return (
    <PublicLayout title="Scholarships & Financial Aid" description="Explore scholarship opportunities and financial aid options at Tima-Ade University. Make education affordable.">
      {/* Hero Section */}
      <div className="relative min-h-96 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 pt-24 text-white">
        <div className="container-xl py-20">
          <Reveal>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">Scholarships & Financial Aid</h1>
          </Reveal>
          <Reveal className="mt-4">
            <p className="text-lg text-primary-100 max-w-2xl">
              Make your education affordable. Explore our comprehensive scholarship and financial aid programs.
            </p>
          </Reveal>
          <Reveal className="mt-8">
            <Link href="#scholarships" className="inline-flex items-center gap-2 bg-white text-primary-900 px-6 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
              Explore Opportunities <ArrowRight size={20} />
            </Link>
          </Reveal>
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="container-xl py-4 text-sm text-slate-600 flex items-center gap-2">
        <Link href="/" className="hover:text-primary-700 transition">Home</Link>
        <ChevronRight size={16} />
        <span className="text-primary-700 font-medium">Scholarships</span>
      </div>

      {/* Financial Support Overview */}
      <section className="py-16 bg-gradient-to-b from-white to-slate-50">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Commitment to Affordability</h2>
            <p className="text-lg text-slate-600 mt-4 max-w-2xl mx-auto">
              We are committed to making quality education accessible to all qualified students, regardless of financial circumstances.
            </p>
          </Reveal>

          <div className="grid md:grid-cols-4 gap-6">
            {[
              { number: '$50M+', label: 'Annual Aid Available' },
              { number: '85%', label: 'Students Receive Aid' },
              { number: '100+', label: 'Scholarship Programs' },
              { number: '$2.2B', label: 'Lifetime Value to Alumni' },
            ].map((stat, i) => (
              <Reveal key={i}>
                <div className="text-center p-6 bg-white rounded-2xl border border-primary-200">
                  <DollarSign className="text-primary-700 mx-auto mb-3" size={40} />
                  <div className="text-3xl font-bold text-primary-900 mb-1">{stat.number}</div>
                  <div className="text-slate-700 font-medium text-sm">{stat.label}</div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Scholarship Programs */}
      <section id="scholarships" className="py-16 bg-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Scholarship Programs</h2>
            <p className="text-lg text-slate-600 mt-4">
              Choose the opportunity that matches your achievements and circumstances
            </p>
          </Reveal>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {scholarshipPrograms.map((program) => (
              <Reveal key={program.id}>
                <article className="group bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-200 p-6 hover:shadow-xl transition">
                  {/* Header */}
                  <div className="mb-4">
                    <Award className="text-primary-700 mb-3" size={40} />
                    <h3 className="text-lg font-bold text-slate-900 mb-2">{program.name}</h3>
                  </div>

                  {/* Amount */}
                  <div className="bg-primary-50 rounded-lg p-3 mb-4 border border-primary-200">
                    <div className="text-sm text-primary-700 font-semibold">Award Amount</div>
                    <div className="text-lg font-bold text-primary-900">{program.amount}</div>
                  </div>

                  {/* Description */}
                  <p className="text-slate-600 text-sm mb-4">{program.description}</p>

                  {/* Eligibility */}
                  <div className="mb-4">
                    <div className="text-xs font-semibold text-slate-700 uppercase mb-1">Eligibility</div>
                    <p className="text-sm text-slate-600">{program.eligibility}</p>
                  </div>

                  {/* Deadline */}
                  <div className="mb-4 p-3 bg-amber-50 rounded-lg border border-amber-200">
                    <div className="text-xs font-semibold text-amber-800">Application Deadline</div>
                    <div className="text-sm font-bold text-amber-900">{program.deadline}</div>
                  </div>

                  {/* CTA */}
                  <Link href={`/scholarships/${program.id}`} className="inline-flex items-center gap-2 text-primary-700 font-semibold hover:gap-3 transition text-sm">
                    Learn More <ArrowRight size={16} />
                  </Link>
                </article>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Types of Financial Aid */}
      <section className="py-16 bg-gradient-to-b from-slate-50 to-white">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Types of Financial Aid</h2>
          </Reveal>

          <div className="grid md:grid-cols-2 gap-8">
            {[
              {
                title: 'Grants & Scholarships',
                description: 'Free money that does not need to be repaid. Based on merit, need, or other criteria.',
                benefits: [
                  'No repayment required',
                  'Merit-based and need-based options',
                  'Renewable annually',
                ],
              },
              {
                title: 'Student Loans',
                description: 'Borrowed funds that must be repaid with interest. Available through federal and private lenders.',
                benefits: [
                  'Flexible repayment options',
                  'Low interest federal loans',
                  'Income-driven repayment plans',
                ],
              },
              {
                title: 'Work-Study Programs',
                description: 'Earn money while gaining work experience. On-campus and off-campus opportunities available.',
                benefits: [
                  'Flexible scheduling around classes',
                  'Valuable work experience',
                  'Campus employment benefits',
                ],
              },
              {
                title: 'Tuition Payment Plans',
                description: 'Pay tuition over several months instead of one lump sum.',
                benefits: [
                  'No interest charges',
                  'Reduced financial burden',
                  'Flexible monthly payments',
                ],
              },
            ].map((aid, i) => (
              <Reveal key={i}>
                <div className="bg-white rounded-2xl border border-slate-200 p-8 hover:shadow-lg transition">
                  <h3 className="text-xl font-bold text-slate-900 mb-3">{aid.title}</h3>
                  <p className="text-slate-600 mb-4">{aid.description}</p>
                  <div className="space-y-2">
                    {aid.benefits.map((benefit, j) => (
                      <div key={j} className="flex items-start gap-2">
                        <CheckCircle2 className="text-primary-700 flex-shrink-0 mt-0.5" size={18} />
                        <span className="text-sm text-slate-600">{benefit}</span>
                      </div>
                    ))}
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Application Process */}
      <section className="py-16 bg-white border-y border-slate-200">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">How to Apply</h2>
            <p className="text-lg text-slate-600 mt-4">
              Simple steps to access financial aid and scholarships
            </p>
          </Reveal>

          <div className="max-w-3xl mx-auto space-y-6">
            {[
              {
                step: '1',
                title: 'Complete FAFSA',
                description: 'Fill out the Free Application for Federal Student Aid to determine eligibility for federal and state aid.',
              },
              {
                step: '2',
                title: 'Explore Scholarships',
                description: 'Search our scholarship database and identify opportunities matching your profile and achievements.',
              },
              {
                step: '3',
                title: 'Submit Applications',
                description: 'Complete scholarship applications by the stated deadlines with required supporting documents.',
              },
              {
                step: '4',
                title: 'Receive Award Letter',
                description: 'Once admitted, receive a financial aid package detailing all available aid and payment options.',
              },
              {
                step: '5',
                title: 'Accept & Plan',
                description: 'Accept your aid offers and work with our financial aid office to plan your payments and budget.',
              },
            ].map((item, i) => (
              <Reveal key={i}>
                <div className="flex gap-6 bg-gradient-to-r from-slate-50 to-white p-6 rounded-xl border border-slate-200">
                  <div className="flex-shrink-0 w-12 h-12 rounded-full bg-primary-700 text-white flex items-center justify-center font-bold text-lg">
                    {item.step}
                  </div>
                  <div>
                    <h3 className="text-lg font-bold text-slate-900 mb-2">{item.title}</h3>
                    <p className="text-slate-600">{item.description}</p>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* FAQ Section */}
      <section className="py-16 bg-slate-50">
        <div className="container-xl">
          <Reveal className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-slate-900">Frequently Asked Questions</h2>
          </Reveal>

          <div className="max-w-2xl mx-auto space-y-4">
            {[
              {
                q: 'When should I apply for financial aid?',
                a: 'Submit the FAFSA as early as possible after January 1st. Scholarship deadlines vary, typically ranging from March to May.',
              },
              {
                q: 'Can I receive multiple scholarships?',
                a: 'Yes, you can receive multiple scholarships. However, total aid cannot exceed your cost of attendance.',
              },
              {
                q: 'Do I need to be a citizen to receive aid?',
                a: 'Some aid requires U.S. citizenship, but we offer scholarships for international students. Check specific program requirements.',
              },
              {
                q: 'Can international students receive scholarships?',
                a: 'Yes, we have dedicated scholarships for international students. See our International Excellence Award program.',
              },
            ].map((faq, i) => (
              <Reveal key={i}>
                <details className="group bg-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition cursor-pointer">
                  <summary className="flex items-center justify-between font-bold text-slate-900 text-lg">
                    {faq.q}
                    <span className="text-primary-700 group-open:rotate-180 transition">▼</span>
                  </summary>
                  <p className="text-slate-600 mt-4 leading-relaxed">{faq.a}</p>
                </details>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Contact Section */}
      <section className="py-16 md:py-24 bg-primary-900 text-white">
        <Reveal>
          <div className="container-xl text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">Need Help Financing Your Education?</h2>
            <p className="text-lg text-primary-100 max-w-2xl mx-auto mb-8">
              Our financial aid advisors are ready to help you navigate scholarships, loans, and payment options.
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <Link href="/contact" className="inline-flex items-center gap-2 bg-white text-primary-900 px-8 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
                Contact Financial Aid <ArrowRight size={20} />
              </Link>
              <a href="https://fafsa.ed.gov" target="_blank" rel="noopener noreferrer" className="inline-flex items-center gap-2 border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white/10 transition">
                Complete FAFSA <ArrowRight size={20} />
              </a>
            </div>
          </div>
        </Reveal>
      </section>
    </PublicLayout>
  )
}
