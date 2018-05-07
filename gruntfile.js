module.exports = function (grunt) {
	grunt.initConfig({
		watch: {
			javascript: {
				files: "src/js/*",
				tasks: ['uglify'],
			},
			styles: {
				files: "src/scss/*",
				tasks: ['sass', 'postcss'],
			},
			svg: {
				files: "src/images/**/*.svg",
				tasks: ['svgmin'],
			},
//			php: {
//				files: "**/*.php",
//				tasks: ['phpcs'],
//			}
		},

		sass: {
			dev: {
				files: {
					"dist/css/style.min.css" : "src/scss/style.scss",
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
					src : ['dist/**/*', '**/*.php', '**/*.html', '!node_modules'],
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
					cwd: 'src/images/svg/',
					src: '*.svg',
					dest: 'dist/images/svg/',
				}],
			},
		},

		phpcs: {
			plugin_files: {
				src: ['**/*.php'],
			},
			options: {
				bin: '/usr/local/bin/phpcs',
				standard: 'WordPress-Extra',
			}
		},

		uglify: {
			options: {
				sourceMap: true
			},
			custom: {
				files: {
					'dist/js/exhibitor-backend.min.js': ['src/js/exhibitor-backend.js'],
					'dist/js/popups.min.js': ['src/js/popups.js'],
					'dist/js/price-sheets.min.js': ['src/js/price-sheets.js'],
					'dist/js/robly-lists.min.js': ['src/js/robly-lists.js'],
					'dist/js/woocommerce.min.js': ['src/js/woocommerce.js'],
					'dist/js/workshop-filter.min.js': ['src/js/workshop-filter.js'],
				},
			},
		},

	});

	grunt.loadNpmTasks('grunt-svgmin');
	grunt.loadNpmTasks('grunt-sass');
	grunt.loadNpmTasks('grunt-phpcs');
	grunt.loadNpmTasks('grunt-postcss');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-browser-sync');
	grunt.registerTask('default', [
		'browserSync',
		'watch',
	]);
};
