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
            tasks: ['svgmin', 'svgstore'],
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
                src : ['css/*.min.css', '**/*.php', '**/*.js', '**/*.svg', '!node_modules'],
            },
            options: {
                watchTask: true,
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
                dest: 'SVG/compressed',
            }],
        },
    },
    svgstore: {
        options: {
            prefix : 'icon-', // This will prefix each ID
            svg: { // will add and overide the the default xmlns="http://www.w3.org/2000/svg" attribute to the resulting SVG
                viewBox : '0 0 100 100',
                xmlns: 'http://www.w3.org/2000/svg',
            },
        },
        default: {
            files: {
                'SVG/icons.min.svg': ['SVG/compressed/*.svg'],
            },
        },
    },
    uglify: {
        custom: {
            files: {
                'js/exhibitor-backend.min.js': ['js/exhibitor-backend.js'],
                'js/woocommerce.min.js': ['js/woocommerce.js'],
            },
        },
    },
  });

    grunt.loadNpmTasks('grunt-svgmin');
    grunt.loadNpmTasks('grunt-svgstore');
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
