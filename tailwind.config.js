/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      backgroundImage: {
        'login': "url('/images/login.webp')",
        'register': "url('/images/register.webp')",
        'landing': "url('/images/landing.webp')",
      },
      background: {
        'primary-red': '#D57275',
        'primary-white': '#F5F5F5',
        'primary-black': '#0D1321',
      },
      colors: {
        'primary-red': '#D57275',
        'primary-white': '#F5F5F5',
        'primary-black': '#0D1321',
        'secondary-black': '#637587',
      }
    },
  },
  plugins: [],
}