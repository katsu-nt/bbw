const mix = require("laravel-mix");
const tailwindcss = require("tailwindcss");
const path = require('path');


mix.webpackConfig({
  stats: {
    children: true,
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "resources/react"),
    },
  },
});

// Biên dịch và nén file CSS
mix.postCss("resources/css/custom.css", "public/css", [
  require("postcss-import"),
  require("tailwindcss"),
  require("postcss-nested"),
  require("autoprefixer"),
]);

mix
  .js("resources/js/app.js", "public/js")
  .postCss("resources/css/app.css", "public/css", [tailwindcss]);

// Copy các tài nguyên khác
mix
  .copyDirectory("resources/fonts", "public/fonts")
  .copyDirectory("resources/images", "public/images");

if (mix.inProduction()) {
  mix.version();
}

//react

mix.js("resources/react/entrypoints/market.js", "public/js").react();

