"use client"

import Image from 'next/image'

export default function Partners(){
  const logos = [
    '/images/university-logo.png',
    '/images/tima-ade-university-logo.png',
    '/images/university-logo.png',
    '/images/tima-ade-university-logo.png',
    '/images/university-logo.png',
  ]

  return (
    <section className="py-8">
      <h3 className="h2 mb-6">Partners & Accreditation</h3>
      <div className="flex flex-wrap items-center gap-6 bg-white p-4 rounded-xl shadow-card">
        {logos.map((l, i) => (
          <div key={i} className="p-2 w-[120px] h-[40px] relative">
            <Image src={l} alt={`Partner ${i+1}`} fill style={{ objectFit: 'contain' }} />
          </div>
        ))}
      </div>
    </section>
  )
}
