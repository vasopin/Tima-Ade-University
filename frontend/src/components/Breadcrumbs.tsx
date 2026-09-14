import Link from 'next/link'
import { ChevronRight } from 'lucide-react'

export default function Breadcrumbs({ items }: { items: string[] }) {
  return (
    <nav className="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
      <Link href="/" className="font-medium text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">
        Home
      </Link>
      {items.map((item, index) => (
        <div key={item} className="flex items-center gap-2">
          <ChevronRight className="h-4 w-4 text-slate-400" />
          <span className={index === items.length - 1 ? 'font-semibold text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400'}>
            {item}
          </span>
        </div>
      ))}
    </nav>
  )
}
