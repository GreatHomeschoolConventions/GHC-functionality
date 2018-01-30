module.exports = function (grunt) {
  grunt.initConfig({
    // Watch task config
    watch: {
        styles: {
            files: "css/*.scss",
            tasks: ['sass', 'postcss'],
        },
        svg: {
            files: "SVG/raw/*.svg",
            tasks: ['svgmin'],
        },
        javascript: {
            files: ["js/*.js", "!js/*.min.js"],
            tasks: ['uglify'],
        },
    },
    sass: {
        dev: {
            files: {
                "css/style.min.css" : "css/style.scss",
            }
        }
    },
    postcss: {
        options: {
            map: {
                inline: false,
            },

            processors: [
                require('pixrem')(), // add fallbacks for rem units
                require('autoprefixer')({browsers: 'last 2 versions'}), // add vendor prefixes
                require('cssnano')() // minify the result
            ]
        },
        dist: {
            src: 'css/*.min.css',
        }
    },
    browserSync: {
        dev: {
            bsFiles: {
                src : ['css/*.min.css', '**/*.php', '**/*.js', '**/*.svg', '**/*.html', '!node_modules'],
            },
            options: {
                watchTask: true,
                open: "external",
                host: "andrews-macbook-pro.local",
                proxy: "https://ghc.dev",
                https: {
                    key: "/Users/andrew/github/dotfiles/local-dev.key",
                    cert: "/Users/andrew/github/dotfiles/local-dev.crt",
                }
            },
        },
    },
    svgmin: {
        options: {
            plugins: [
                { removeViewBox: false },
                { removeUselessStrokeAndFill: false },
            ]
        },
        icons: {
            files: [{
                expand: true,
                cwd: 'SVG/raw',
                src: '*.svg',
                dest: 'SVG',
            }],
        },
    },
    uglify: {
        custom: {
            files: {
                'js/exhibitor-backend.min.js': ['js/exhibitor-backend.js'],
                'js/price-sheets.min.js': ['js/price-sheets.js'],
                'js/robly-lists.min.js': ['js/robly-lists.js'],
                'js/woocommerce.min.js': ['js/woocommerce.js'],
                'js/workshop-filter.min.js': ['js/workshop-filter.js'],
            },
        },
    },
  });

    grunt.loadNpmTasks('grunt-svgmin');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-browser-sync');
    grunt.registerTask('default', [
        'browserSync',
        'watch',
    ]);
};
