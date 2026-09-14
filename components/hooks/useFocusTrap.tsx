"use client"

import { useEffect } from 'react'

export default function useFocusTrap(containerRef: React.RefObject<HTMLElement | null>, onClose?: () => void, active = true) {
  useEffect(() => {
    if (!active) return
    const el = containerRef.current
    if (!el) return

    const focusableSelectors = [
      'a[href]', 'button:not([disabled])', 'textarea', 'input', 'select', '[tabindex]:not([tabindex="-1"])'
    ].join(',')

    const focusable = Array.from(el.querySelectorAll<HTMLElement>(focusableSelectors))
    const previous = document.activeElement as HTMLElement | null

    if (focusable.length) {
      focusable[0].focus()
    } else {
      el.tabIndex = -1
      el.focus()
    }

    function handleKey(e: KeyboardEvent) {
      if (e.key === 'Escape') {
        e.preventDefault()
        onClose?.()
        return
      }
      if (e.key === 'Tab') {
        if (focusable.length === 0) {
          e.preventDefault()
          return
        }
        const first = focusable[0]
        const last = focusable[focusable.length - 1]
        if (e.shiftKey) {
          if (document.activeElement === first) {
            e.preventDefault()
            last.focus()
          }
        } else {
          if (document.activeElement === last) {
            e.preventDefault()
            first.focus()
          }
        }
      }
    }

    document.addEventListener('keydown', handleKey)

    return () => {
      document.removeEventListener('keydown', handleKey)
      try { previous?.focus() } catch (e) {}
    }
  }, [containerRef, onClose, active])
}
