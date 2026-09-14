import '../styles/globals.css'
import type { AppProps } from 'next/app'
import Head from 'next/head'

function MyApp({ Component, pageProps }: AppProps) {
  return (
    <>
      <Head>
        <title>Tima-Ade University — Empowering Minds</title>
        <meta name="description" content="Tima-Ade University — a world-class enterprise university." />
        <meta name="viewport" content="width=device-width,initial-scale=1" />
      </Head>

      <Component {...pageProps} />
    </>
  )
}

export default MyApp
