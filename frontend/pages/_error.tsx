import Link from 'next/link'
import { NextPageContext } from 'next'

function Error({ statusCode }: { statusCode?: number }) {
  return (
    <div className="flex min-h-screen flex-col items-center justify-center p-6 text-center">
      <h1 className="text-4xl font-bold text-slate-900">{statusCode ? statusCode : 'Error'}</h1>
      <p className="mt-2 text-sm text-slate-600">
        {statusCode === 404
          ? 'This page could not be found.'
          : 'An unexpected error occurred on the server.'}
      </p>
      <Link href="/" className="mt-4 rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white">
        Return to Home
      </Link>
    </div>
  )
}

Error.getInitialProps = ({ res, err }: NextPageContext) => {
  const statusCode = res ? res.statusCode : err ? err.statusCode : 404
  return { statusCode }
}

export default Error
