module.exports = {
    content: ["./**/*.html"],
    content: ["./**/*.php"],
    theme: {
        extend: {
            colors: {
                synthwave: {
                    light: '#e0aaff',
                    mid: '#7209b7',
                    dark: '#3a0ca3',
                    accent: '#4cc9f0',
                },
            },
            boxShadow: {
                black: '0 4px 10px rgba(0, 0, 0, 0.2)', // Ombre noire par défaut
                synthwave: '3px 3px 12px rgba(112, 8, 183, 0.3), 2px 4px 17px rgba(76, 201, 240, 0.2)',
            },
            animation: {
                'gradient-move': 'gradientMove 3s infinite',
            },
            keyframes: {
                gradientMove: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
            },
        },
    },
    plugins: [],
}
