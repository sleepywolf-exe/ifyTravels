/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.{html,js}"],
  theme: {
    extend: {
      colors: {
        primary: '#0A6CF1',       // Ocean Blue
        secondary: '#FF7A18',     // Sunset Orange
        accent: '#1ABC9C',        // Tropical Green
        background: '#F9FAFB',    // Soft White
        charcoal: '#1F2937',      // Charcoal Text
      },
      fontFamily: {
        heading: ['"Plus Jakarta Sans"', 'sans-serif'],
        body: ['"Plus Jakarta Sans"', 'sans-serif'],
        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
