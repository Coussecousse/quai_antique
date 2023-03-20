/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig"
  ],
  theme: {
    extend: {
      fontFamily : {
        'primary' : ["'Petit Formal Script', cursive"],
        'secondary' : ["'Alice', serif"],
        'third' : ["'Open Sans', sans-serif"]
      },
      colors: {
        'primary' : '#171515',
        'secondary' : '#F9F6F0',
        '400' : '#C4AE78',
        '500' : '#6F5B3E',
        '600' : '#473B29',
      },
      height : {
        'carousel-image' : 'clamp(13.375rem, 7.107rem + 31.339vw, 35.313rem)',
      },
      letterSpacing : {
        "code" : ".875em",
      },
      opacity: {
        '85': '.85',
      },
      blur : {
        sm : '2px',
        md : '4px',
      },
      fontSize : {
        'home-title' : 'clamp(2rem, 1.282rem + 3.59vw, 3.75rem)',
        'home-quai' : 'clamp(2.25rem, 1.224rem + 5.128vw, 4.75rem)',
        'home-carousel' : 'clamp(1rem, 0.857rem + 0.714vw, 1.625rem)',
        'home-title-moving' : 'clamp(2rem, 1.795rem + 1.026vw, 2.5rem)',
        'lg' : "1.125rem",
        'xl' : '1.25rem',
        '1.5xl' : '1.375rem',
        '2xl' : '1.5rem',
        '2.25xl' : '1.625rem',
        '2.5xl' : '1.75rem',
        '3xl' : "1.875rem",
        '3.5xl': '2rem',
      },
      screens : {
        "sm" : "425px",
        "sm-600": "560px",
        "sm-700" : "750px",
        "sm-cardButton" : '708px',
        "md" : "768px",
        "md-cardButton" : "806px",
        "md.5" : "900px",
        "lg" : "992px",
      },
      placeholderColors : {
        'primary' : '#171515',
      }
    },
  },
  plugins: [],
}
