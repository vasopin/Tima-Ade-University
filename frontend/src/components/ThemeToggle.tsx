import React, { useEffect, useState } from 'react'

export default function ThemeToggle(){
  const [mode, setMode] = useState<'light'|'dark'>('light')

  useEffect(()=>{
    const m = (document.documentElement.getAttribute('data-theme') as 'light'|'dark') || 'light'
    setMode(m)
  },[])

  const toggle = ()=>{
    const next = mode === 'light' ? 'dark' : 'light'
    setMode(next)
    if(next === 'dark'){
      document.documentElement.classList.add('dark')
    }else{
      document.documentElement.classList.remove('dark')
    }
  }

  return (
    <button aria-label="Toggle theme" onClick={toggle} className="p-2 rounded bg-gray-100 dark:bg-gray-800">
      {mode === 'light' ? '🌞' : '🌙'}
    </button>
  )
}
