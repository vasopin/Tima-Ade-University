"use client"

import { useRef, useState } from 'react'
import { motion } from 'framer-motion'
import Image from 'next/image'
import useFocusTrap from './hooks/useFocusTrap'

const images = [
  { src: '/images/home/classroom.jpg', alt: 'Modern classroom' },
  { src: '/images/home/lab.jpg', alt: 'Research laboratory' },
  { src: '/images/home/library.jpg', alt: 'Library interior' },
  { src: '/images/home/community.jpg', alt: 'Student center' },
  { src: '/images/home/campus.jpg', alt: 'Sports facility' },
  { src: '/images/home/tech.jpg', alt: 'Technology lab' }
]

export default function Facilities(){
  const [open, setOpen] = useState<number | null>(null)
  const modalRef = useRef<HTMLDivElement | null>(null)
  useFocusTrap(modalRef, () => setOpen(null), open !== null)

  return (
    <section>
      <h3 className="h2 mb-6">Campus & Facilities</h3>
      <div className="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
        {images.map((img, i) => (
          <motion.button
            key={i}
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.99 }}
            onClick={() => setOpen(i)}
            className="group block rounded-xl overflow-hidden shadow-card"
            aria-label={`Open image ${img.alt}`}
          >
            <div className="h-48 overflow-hidden relative">
              <Image src={img.src} alt={img.alt} fill style={{ objectFit: 'cover' }} className="group-hover:scale-105 transition-transform" />
            </div>
            <div className="p-3 bg-white">
              <div className="text-sm font-semibold">{img.alt}</div>
              <div className="text-sm text-slate-500">Explore our modern facilities.</div>
            </div>
          </motion.button>
        ))}
      </div>

      {/* Lightbox */}
      {open !== null && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60" role="dialog" aria-modal="true" onClick={() => setOpen(null)}>
          <div ref={modalRef} onClick={(e) => e.stopPropagation()} className="max-w-4xl w-full p-4">
            <button onClick={() => setOpen(null)} className="mb-4 text-white">Close</button>
            <div className="w-full rounded-lg shadow-lg overflow-hidden relative h-[60vh]">
              <Image src={images[open].src} alt={images[open].alt} fill style={{ objectFit: 'cover' }} />
            </div>
          </div>
        </div>
      )}
    </section>
  )
}
