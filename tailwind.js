const {colors} = require('tailwindcss/defaultTheme');

module.exports = {
    purge: [
        './**/*.vue',
    ],
    future: {
        removeDeprecatedGapUtilities: true,
    },
    theme: {
        extend: {
            spacing: {
                '72': '18rem',
                '84': '21rem',
                '96': '24rem',
            },
        },
        colors: {
            black: colors.black,
            white: colors.white,
            gray: colors.gray,
            disabled: {
                darker: '#8D8D8F',
                lighter: '#e8e8e8'
            },
            brand: {
                lighter: '#149739',
                default: '#006950',
            },
            button: {
                light: '#fff',
                text: '#616663'
            },
            map: {
                static: '#DCEDF5',
            },
            content: {
                background: 'rgba(247,247,247,1)',
                body: 'rgba(20,20,20,1)',
            },
            icon: {
                pdf: 'rgb(211,52,68)',
                word: 'rgb(44,112,170)',
                excel: 'rgb(54,120,70)',
                ppt: 'rgb(218,100,62)',
                image: 'rgb(91,174,173)',
                video: 'rgb(95,119,191)',
            }
        }
    },
    variants: {},
    plugins: []
};
