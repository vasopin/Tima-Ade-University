import type { Config } from 'tailwindcss'

const config: Config = {
  darkMode: ['class'],
  content: ['./pages/**/*.{js,ts,jsx,tsx}', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        navy: {
          900: '#0f172a',
          800: '#1e293b',
          700: '#334155',
          600: '#475569'
        },
        crimson: {
          900: '#4c0519',
          800: '#881337',
          700: '#9f1239',
          600: '#be123c',
          500: '#e11d48'
        },
        teal: {
          900: '#134e4a',
          800: '#115e59',
          700: '#0f766e',
          600: '#0d9488',
          500: '#14b8a6',
          400: '#2dd4bf',
          100: '#ccfbf1',
          50:  '#f0fdfa',
        },
        violet: {
          900: '#2e1065',
          800: '#3b0764',
          700: '#6d28d9',
          600: '#7c3aed',
          500: '#8b5cf6',
          400: '#a78bfa',
          100: '#ede9fe',
          50:  '#f5f3ff',
        },
        amber: {
          900: '#78350f',
          800: '#92400e',
          700: '#b45309',
          600: '#d97706',
          500: '#f59e0b',
          400: '#fbbf24',
          100: '#fef3c7',
          50:  '#fffbeb',
        },
        indigo: {
          900: '#1e1b4b',
          800: '#312e81',
          700: '#3730a3',
          600: '#4338ca',
          500: '#6366f1',
          400: '#818cf8',
          100: '#e0e7ff',
          50:  '#eef2ff',
        }
      },
      boxShadow: {
        soft: '0 10px 30px -12px rgba(15, 23, 42, 0.25)',
      }
    }
  },
  plugins: []
}

export default config
