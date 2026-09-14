import '../styles/globals.css'
import type { AppProps } from 'next/app'
import { AuthProvider } from '../src/context/AuthContext'
import { NotificationsProvider } from '../src/context/NotificationsContext'

export default function App({ Component, pageProps }: AppProps) {
  return (
    <AuthProvider>
      <NotificationsProvider>
        <Component {...pageProps} />
      </NotificationsProvider>
    </AuthProvider>
  )
}
