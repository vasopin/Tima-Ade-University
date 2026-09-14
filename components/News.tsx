"use client"

import { motion } from 'framer-motion'

const news = [
  { id: 1, title: 'New Research Grant Awarded', date: '2026-07-12', excerpt: 'Major funding awarded to the AI center to scale research.' },
  { id: 2, title: 'Open Day: Visit Our Campus', date: '2026-08-01', excerpt: 'Register for our open day to meet faculty and students.' },
  { id: 3, title: 'Global Partnerships Expanded', date: '2026-06-20', excerpt: 'New partnerships with universities in Europe and Asia.' },
]

export default function News(){
  return (
    <section>
      <h3 className="h2 mb-4">News & Events</h3>
      <div className="space-y-4">
        {news.map(n => (
          <motion.article key={n.id} initial={{ opacity:0,y:8 }} animate={{ opacity:1,y:0 }} transition={{ duration: .4 }} className="bg-white rounded-xl p-4 shadow-card">
            <div className="flex items-start justify-between">
              <div>
                <div className="font-semibold">{n.title}</div>
                <div className="text-sm text-slate-500">{new Date(n.date).toLocaleDateString()}</div>
                <p className="mt-2 text-slate-600">{n.excerpt}</p>
              </div>
              <div>
                <a className="text-primary-700">Read More →</a>
              </div>
            </div>
          </motion.article>
        ))}
      </div>

      <div className="mt-6">
        <a className="px-4 py-2 rounded-md border">View All News</a>
      </div>
    </section>
  )
}
