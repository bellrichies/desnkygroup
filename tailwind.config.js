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
          // Brand colors
          // Primary: corporate purple — primary buttons (light bg), links, eyebrows, active states.
          primary: '#6c3483',
          'primary-50': '#f3edf7',
          'primary-200': '#d8c4e4', // light tint for accent text/links on dark backgrounds
          'primary-700': '#58296b', // hover / darker shade
          // Secondary: brand green — supporting CTAs, success, HSE/Agro, and CTAs on dark backgrounds.
          secondary: '#008000',
          'secondary-50': '#e6f2e6',
          'secondary-700': '#006b00',
          // Neutrals / dark anchor (deep aubergine — hero overlays, dark sections)
          dark: '#1d1228',
          darker: '#150d1d', // footer (true dark theme)
          ink: '#1f1626', // body text
          muted: '#6b6675', // secondary text
          surface: '#f7f5fa', // light section surface (faint brand tint)

          // Sector accents — reuse the two brand hues + dark anchor (differentiated by iconography).
          sector: {
            engineering: '#6c3483', // primary (purple)
            energy: '#008000', // secondary (green)
            procurement: '#1d1228', // dark anchor
            hse: '#008000', // secondary (green)
            ict: '#6c3483', // primary (purple)
            agro: '#008000', // secondary (green)
          },

          // Legacy aliases — remapped to the new brand so existing classes keep working.
          // navy → dark anchor · blue → primary · gold → on-dark light accent · green → secondary.
          navy: '#1d1228',
          blue: '#6c3483',
          gold: '#d8c4e4',
          green: '#008000',
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
      borderRadius: {
        md: '0.5rem',
        lg: '0.75rem',
      },
      boxShadow: {
        card: '0 12px 30px rgba(29, 18, 40, 0.10)',
        lg: '0 20px 40px rgba(29, 18, 40, 0.14)',
      },
      transitionTimingFunction: {
        brand: 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
      transitionDuration: {
        fast: '150ms',
        base: '220ms',
        slow: '400ms',
      },
      zIndex: {
        header: '50',
        dropdown: '40',
        modal: '60',
        toast: '70',
      },
      keyframes: {
        'fade-up': {
          '0%': { opacity: '0', transform: 'translateY(16px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        'fade-in': {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
      },
      animation: {
        'fade-up': 'fade-up 0.5s cubic-bezier(0.4, 0, 0.2, 1) both',
        'fade-in': 'fade-in 0.4s cubic-bezier(0.4, 0, 0.2, 1) both',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
};
