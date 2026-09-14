import '../styles/globals.css'
import React from 'react'

export const metadata = {
  title: 'Tima-Ade University — Empowering Minds',
  description: 'A world-class enterprise university.'
}

export default function RootLayout({ children }: { children: React.ReactNode }){
  return (
    <html lang="en">
      <body>
        {children}
      </body>
    </html>
  )
}
