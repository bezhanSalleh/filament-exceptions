/** @type {import('tailwindcss').Config} */

module.exports = {
    content: ["./resources/views/**/*.blade.php", "./src/**/*.php"],
    darkMode: "class",
    // important: ".filament-addons",
    theme: {
        extend: {},
    },
    plugins: [require("@tailwindcss/typography")],
    corePlugins: {
        preflight: false,
    },
};
