module.exports = {
  content: [
    './views/**/*.php',
    './admin/**/*.php',
    './public/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#FAEEDA',
          100: '#FFF3E8',
          600: '#E87722',
          700: '#C4601A',
        },
        gray: {
          50: '#F9F8F6',
          100: '#F1EFE8',
          200: '#D3D1C7',
          400: '#888780',
          600: '#5F5E5A',
          900: '#1A1A1A',
        },
      },
      fontFamily: {
        sans: ['Sarabun', 'Noto Sans Thai', 'sans-serif'],
      },
    },
  },
};

