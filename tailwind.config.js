/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig"
  ],
  theme: {
    fontFamily : {
      'primary' : ["'Petit Formal Script', cursive"],
      'secondary' : ["'Alice', serif"],
      'third' : ["'Open Sans', sans-serif"]
    },
    fontSize : {
      'lg' : "1.125rem",
      '2.25xl' : '1.625rem',
      '2.5xl' : '1.75rem',
      '3xl' : "1.875rem",
      '3.5xl': '2rem',
    },
    colors: {
      'primary' : '#171515',
      'secondary' : '#F9F6F0',
      '400' : '#C4AE78',
      '500' : '#6F5B3E',
    },
    extend: {
      blur : {
        sm : '2px',
        md : '4px',
      },
    },
  },
  plugins: [],
}
