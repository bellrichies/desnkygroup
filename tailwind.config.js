module.exports = {
  content: [
    './app/Views/**/*.php',
    './public/assets/**/*.js',
  ],
  darkMode: 'class',
  theme: {
    screens: {
      sm: '640px',
      md: '768px',
      lg: '1024px',
      xl: '1280px',
      '2xl': '1536px',
    },
    extend: {
      colors: {
        desnky: {
          navy: '#0f2742',
          blue: '#1769aa',
          gold: '#d9a441',
          green: '#207a4c',
          ink: '#172033',
          muted: '#667085',
          surface: '#f6f8fb',
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      spacing: {
        18: '4.5rem',
        22: '5.5rem',
        30: '7.5rem',
      },
      boxShadow: {
        card: '0 12px 30px rgba(15, 39, 66, 0.08)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
};
