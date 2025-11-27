/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    screens: {
      'sm': '640px',
      'md': '768px',
      'lg': '1024px',
      'xl': '1280px',
      '2xl': '1536px',
    },
    extend: {
      colors: {
        'spnr-blue': {
          50: '#E0F2FE',
          100: '#BAE6FD',
          200: '#7DD3FC',
          300: '#38BDF8',
          400: '#3B82F6',
          500: '#06B6D4',
          600: '#1E40AF',
          700: '#1E3A8A',
          800: '#1E40AF',
          900: '#1D4ED8',
        },
        'spnr-gray': {
          400: '#9CA3AF',
          500: '#6B7280',
          600: '#64748B',
          700: '#374151',
          900: '#0F172A',
        },
        'spnr-accent': '#06B6D4',
        'spnr-success': '#10B981',
        'spnr-warning': '#F59E0B',
        'spnr-error': '#EF4444',
      },
      fontFamily: {
        'sans': ['Poppins', 'Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'soft': '0 4px 6px rgba(0, 0, 0, 0.07)',
        'DEFAULT': '0 4px 6px rgba(0, 0, 0, 0.07)',
        'md': '0 8px 16px rgba(0, 0, 0, 0.1)',
        'lg': '0 20px 40px rgba(0, 0, 0, 0.15)',
        'xl': '0 32px 64px rgba(0, 0, 0, 0.2)',
      },
    },
  },
  plugins: [],
}
