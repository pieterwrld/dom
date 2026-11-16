/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    '../../**/*.php',
    '../js/**/*.js',
  ],
  theme: {
    fontFamily: {
      sans: ['Geist', 'Inter', 'system-ui', '-apple-system', 'sans-serif'],
      mono: ['Geist Mono', 'SF Mono', 'monospace'],
    },
    extend: {
      colors: {
        bg: '#FFFFFF',
        'bg-off': '#FAFAF9',
        fg: '#0A0A0A',
        accent: '#9CA3AF',
        'accent-2': '#D1D5DB',
        surface: '#F5F5F4',
        'surface-2': '#EBEBEA',
        border: '#E5E5E4',
        muted: '#71717A',
        urgency: '#EF4444',
        success: '#22C55E',
        'bf-dark': '#0A0A0A',
      },
      borderRadius: {
        sm: '6px',
        md: '10px',
        lg: '16px',
      },
      fontSize: {
        '2xs': ['11px', '1.4'],
        xs: ['12px', '1.5'],
        sm: ['13px', '1.5'],
        base: ['15px', '1.6'],
        lg: ['18px', '1.5'],
        xl: ['20px', '1.4'],
        '2xl': ['24px', '1.3'],
        '3xl': ['32px', '1.2'],
        '4xl': ['40px', '1.1'],
        '5xl': ['48px', '1.05'],
        '6xl': ['64px', '1'],
      },
      spacing: {
        4.5: '18px',
        13: '52px',
        15: '60px',
        18: '72px',
        22: '88px',
      },
      letterSpacing: {
        label: '0.06em',
      },
      animation: {
        'silver-shimmer': 'silverShimmer 6s ease infinite',
        marquee: 'marquee 30s linear infinite',
      },
      keyframes: {
        silverShimmer: {
          '0%': { backgroundPosition: '0% 50%' },
          '50%': { backgroundPosition: '100% 50%' },
          '100%': { backgroundPosition: '0% 50%' },
        },
        marquee: {
          to: { transform: 'translateX(-50%)' },
        },
      },
    },
  },
  plugins: [],
};
