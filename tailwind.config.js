const plugin = require('tailwindcss/plugin')

/** @type {import('tailwindcss').Config} */
module.exports = {
        content: [
            './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
            './storage/framework/views/*.php',
            './resources/views/**/*.blade.php',
            './resources/js/**/*.vue',
            './resources/js/**/**/*.vue',
            './resources/js/**/**/**/**/*.vue',
            './resources/js/Pages/Admin/Permission/*.vue',
            './resources/js/**/*.js',
            './node_modules/@protonemedia/inertiajs-tables-laravel-query-builder/**/*.{js,vue}',
        ],
        darkMode: 'class', // or 'media' or 'class'
        theme: {
            fontFamily: {
                iransans: ['IRANSansFaDigits', 'sans'],
            },
            asideScrollbars: {
                light: 'light',
                gray: 'gray'
            },
            extend: {
                colors: {
                    'reddish': {
                        light: '#fda4af',
                        DEFAULT: '#e11d48',
                        dark: '#9f1239',
                    },
                    'purple': {
                        light: '#d8b4fe',
                        DEFAULT: '#9333ea',
                        dark: '#6b21a8'
                    },
                    'green': {
                        light: '#86efac',
                        DEFAULT: '#16a34a',
                        dark: '#166534'
                    },
                    'gray': {
                        light: '#e4e4e7',
                        DEFAULT: '#a1a1aa',
                        dark: '#52525b'
                    }
                },
                zIndex: {
                    '-1': '-1'
                },
                flexGrow: {
                    5: '5'
                },
                maxHeight: {
                    'screen-menu': 'calc(100vh - 3.5rem)',
                    modal: 'calc(100vh - 160px)'
                },
                transitionProperty: {
                    position: 'right, left, top, bottom, margin, padding',
                    textColor: 'color'
                },
                keyframes: {
                    'fade-out': {
                        from: { opacity: 1 },
                        to: { opacity: 0 }
                    },
                    'fade-in': {
                        from: { opacity: 0 },
                        to: { opacity: 1 }
                    }
                },
                animation: {
                    'fade-out': 'fade-out 250ms ease-in-out',
                    'fade-in': 'fade-in 250ms ease-in-out'
                },
                
            }
        },
        plugins: [
                require('@tailwindcss/forms'),
                plugin(function({ matchUtilities, theme }) {
                        matchUtilities({
                                    'aside-scrollbars': value => {
                                            const track = value === 'light' ? '100' : '900'
                                            const thumb = value === 'light' ? '300' : '600'
                                            const color = value === 'light' ? 'gray' : value

                                            return {
                                                scrollbarWidth: 'thin',
                                                scrollbarColor: `${theme(`colors.${color}.${thumb}`)} ${theme(`colors.${color}.${track}`)}`,
              '&::-webkit-scrollbar': {
                width: '8px',
                height: '8px'
              },
              '&::-webkit-scrollbar-track': {
                backgroundColor: theme(`colors.${color}.${track}`)
              },
              '&::-webkit-scrollbar-thumb': {
                borderRadius: '0.25rem',
                backgroundColor: theme(`colors.${color}.${thumb}`)
              }
            }
          }
        },
        { values: theme('asideScrollbars') }
      )
    }),
    require('@tailwindcss/line-clamp')
  ]
}