const colors = require('tailwindcss/colors');
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    './vendor/filament/**/*.blade.php',
    './resources/views/filament/**/*.blade.php',
  ],
  darkMode: 'class',
  safelist: ['max-w-8xl'],
  theme: {
    extend: {
      colors: {
        white: '#fff',
        black: '#000',
        transparent: 'transparent',
        danger: colors.rose,
        primary: colors.violet,
        success: colors.green,
        warning: colors.amber,
        gray: colors.gray,
        blue: colors.blue,
        orange: colors.orange,
      },
      maxWidth: {
        '8xl': '88rem',
      },
      fontFamily: {
        sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
};
