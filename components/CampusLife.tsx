"use client"

import { motion } from 'framer-motion'
import Image from 'next/image'

const activities = [
  { title: 'Clubs & Societies', img: '/images/home/community.jpg' },
  { title: 'Sports & Wellness', img: '/images/home/campus.jpg' },
  { title: 'Events & Cultural', img: '/images/home/event.jpg' },
  { title: 'Volunteering', img: '/images/home/community.jpg' },
]

export default function CampusLife(){
  return (
    <section>
      <h3 className="h2 mb-6">Campus Life</h3>
      <div className="grid sm:grid-cols-2 gap-6">
        {activities.map((a, i) => (
          <motion.div key={i} initial={{ opacity: 0, y: 8 }} whileInView={{ opacity:1, y:0 }} viewport={{ once:true }} className="rounded-xl overflow-hidden shadow-card relative">
            <div className="w-full h-56 relative">
              <Image src={a.img} alt={a.title} fill style={{ objectFit: 'cover' }} />
            </div>
            <div className="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent text-white">
              <div className="font-semibold">{a.title}</div>
              <div className="text-sm">Engage in student-led activities and events.</div>
            </div>
          </motion.div>
        ))}
      </div>
    </section>
  )
}
