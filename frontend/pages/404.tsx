import Link from 'next/link'

export default function Custom404() {
  return (
    <div className="flex min-h-screen flex-col items-center justify-center p-6 text-center">
      <h1 className="text-4xl font-bold text-slate-900">404</h1>
      <p className="mt-2 text-sm text-slate-600">Page Not Found</p>
      <Link href="/" className="mt-4 rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white">
        Return to Home
      </Link>
    </div>
  )
}
