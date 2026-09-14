/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./pages/**/*.{js,ts,jsx,tsx}", "./components/**/*.{js,ts,jsx,tsx}", "./app/**/*.{js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f5f7ff',
          100: '#e6edff',
          500: '#1f3b8a',
          700: '#172a5a'
        },
        accent: '#4f46e5',
        glass: 'rgba(255,255,255,0.06)'
      },
      boxShadow: {
        card: '0 6px 18px rgba(15,23,42,0.08)'
      }
    }
  },
  plugins: []
};