/* jshint node:true */
module.exports = function ( grunt ) {
    var path = require( 'path' ),
        SOURCE_DIR = 'src/',
        BUILD_DIR = 'build/',
        //autoprefixer = require( 'autoprefixer' ),
        config = {},
        builds = [ 'admin' ];

    // Load tasks.
    require( 'matchdep' ).filterDev( [ 'grunt-*', '!grunt-legacy-util' ] ).forEach( grunt.loadNpmTasks );

    // Load legacy utils
    grunt.util = require( 'grunt-legacy-util' );

    builds.forEach( function ( build ) {
        var path = SOURCE_DIR + build + '/js';
        config[ build ] = { files: {} };
        config[ build ].files[ path + '/' + build + '.js' ] = [ path + '/src/' + build + '.manifest.js' ];
    } );

    // Project configuration.
    grunt.initConfig( {
        //postcss: {
        //    options: {
        //        processors: [
        //            autoprefixer( {
        //                browsers: [ 'Android >= 2.1', 'Chrome >= 21', 'Explorer >= 7', 'Firefox >= 17', 'Opera >= 12.1', 'Safari >= 6.0' ],
        //                cascade: false
        //            } )
        //        ]
        //    }
        //    //,
        //    //admin: {
        //    //    expand: true,
        //    //    cwd: SOURCE_DIR,
        //    //    dest: SOURCE_DIR,
        //    //    src: 'admin/css/*.css'
        //    //},
        //    //colors: {
        //    //    expand: true,
        //    //    cwd: BUILD_DIR,
        //    //    dest: BUILD_DIR,
        //    //    src: [
        //    //        'wp-admin/css/colors/*/colors.css'
        //    //    ]
        //    //}
        //},
        clean: {
            all: [ BUILD_DIR ],
            dynamic: {
                dot: true,
                expand: true,
                cwd: BUILD_DIR,
                src: []
            }
            //, qunit: [ 'tests/qunit/compiled.html' ]
        },
        copy: {
            files: {
                files: [
                    {
                        dot: true,
                        expand: true,
                        cwd: SOURCE_DIR,
                        src: [
                            '**',
                            '!admin/js/src/**',
                            '!public/js/src/**',
                            '!**/.{svn,git}/**' // Ignore version control directories.
                        ],
                        dest: BUILD_DIR
                    }
                ]
            },
            dynamic: {
                dot: true,
                expand: true,
                cwd: SOURCE_DIR,
                dest: BUILD_DIR,
                src: []
            }
            // ,
            //qunit: {
            //    src: 'tests/qunit/index.html',
            //    dest: 'tests/qunit/compiled.html',
            //    options: {
            //        processContent: function ( src ) {
            //            return src.replace( /(\".+?\/)src(\/.+?)(?:.min)?(.js\")/g, function ( match, $1, $2, $3 ) {
            //                // Don't add `.min` to files that don't have it.
            //                return $1 + 'build' + $2 + ( /jquery$/.test( $2 ) ? '' : '.min' ) + $3;
            //            } );
            //        }
            //    }
            //}
        },
        browserify: config,
        //sass: {
        //    colors: {
        //        expand: true,
        //        cwd: SOURCE_DIR,
        //        dest: BUILD_DIR,
        //        ext: '.css',
        //        src: [ 'wp-admin/css/colors/*/colors.scss' ],
        //        options: {
        //            outputStyle: 'expanded'
        //        }
        //    }
        //},
        //cssmin: {
        //    options: {
        //        'wp-admin': [ 'wp-admin', 'color-picker', 'customize-controls', 'customize-widgets', 'customize-nav-menus', 'ie', 'install', 'login', 'press-this', 'deprecated-*' ]
        //    },
        //    core: {
        //        expand: true,
        //        cwd: SOURCE_DIR,
        //        dest: BUILD_DIR,
        //        ext: '.min.css',
        //        src: [
        //            'wp-admin/css/{<%= cssmin.options["wp-admin"] %>}.css',
        //            'wp-includes/css/*.css'
        //        ]
        //    },
        //    rtl: {
        //        expand: true,
        //        cwd: BUILD_DIR,
        //        dest: BUILD_DIR,
        //        ext: '.min.css',
        //        src: [
        //            'wp-admin/css/{<%= cssmin.options["wp-admin"] %>}-rtl.css',
        //            'wp-includes/css/*-rtl.css'
        //        ]
        //    },
        //    colors: {
        //        expand: true,
        //        cwd: BUILD_DIR,
        //        dest: BUILD_DIR,
        //        ext: '.min.css',
        //        src: [
        //            'wp-admin/css/colors/*/*.css'
        //        ]
        //    }
        //},
        jshint: {
            options: grunt.file.readJSON( '.jshintrc' ),
            grunt: {
                src: [ 'Gruntfile.js' ]
            },
            //tests: {
            //    src: [
            //        'tests/qunit/**/*.js',
            //        '!tests/qunit/vendor/*',
            //        '!tests/qunit/editor/**'
            //    ],
            //    options: grunt.file.readJSON( 'tests/qunit/.jshintrc' )
            //},
            //themes: {
            //    expand: true,
            //    cwd: SOURCE_DIR + 'wp-content/themes',
            //    src: [
            //        'twenty*/**/*.js',
            //        '!twenty{eleven,twelve,thirteen}/**',
            //        // Third party scripts
            //        '!twenty{fourteen,fifteen,sixteen}/js/html5.js'
            //    ]
            //},
            admin: {
                options: {
                    browserify: true
                },
                src: [
                    SOURCE_DIR + 'admin/js/src/**/*.js'
                ]
            }
        },
        //qunit: {
        //    files: [
        //        'tests/qunit/**/*.html',
        //        '!tests/qunit/editor/**'
        //    ]
        //},
        phpunit: {
            'default': {
                cmd: 'phpunit',
                args: [ '-c', 'phpunit.xml' ]
            }
        },
        uglify: {
            options: {
                ASCIIOnly: true
            },
            admin: {
                expand: true,
                cwd: SOURCE_DIR,
                dest: BUILD_DIR,
                ext: '.min.js',
                src: [
                    'admin/js/admin.js'
                ]
            }
        },
        //concat: {
        //    tinymce: {
        //        options: {
        //            separator: '\n',
        //            process: function ( src, filepath ) {
        //                return '// Source: ' + filepath.replace( BUILD_DIR, '' ) + '\n' + src;
        //            }
        //        },
        //        src: [
        //            BUILD_DIR + 'wp-includes/js/tinymce/tinymce.min.js',
        //            BUILD_DIR + 'wp-includes/js/tinymce/themes/modern/theme.min.js',
        //            BUILD_DIR + 'wp-includes/js/tinymce/plugins/*/plugin.min.js'
        //        ],
        //        dest: BUILD_DIR + 'wp-includes/js/tinymce/wp-tinymce.js'
        //    },
        //    emoji: {
        //        options: {
        //            separator: '\n',
        //            process: function ( src, filepath ) {
        //                return '// Source: ' + filepath.replace( BUILD_DIR, '' ) + '\n' + src;
        //            }
        //        },
        //        src: [
        //            BUILD_DIR + 'wp-includes/js/twemoji.min.js',
        //            BUILD_DIR + 'wp-includes/js/wp-emoji.min.js'
        //        ],
        //        dest: BUILD_DIR + 'wp-includes/js/wp-emoji-release.min.js'
        //    }
        //},
        //compress: {
        //    tinymce: {
        //        options: {
        //            mode: 'gzip',
        //            level: 9
        //        },
        //        src: '<%= concat.tinymce.dest %>',
        //        dest: BUILD_DIR + 'wp-includes/js/tinymce/wp-tinymce.js.gz'
        //    }
        //},
        jsvalidate: {
            options: {
                globals: {},
                esprimaOptions: {},
                verbose: false
            },
            build: {
                files: {
                    src: [
                        BUILD_DIR + '{admin,public}/js/**/*.js'
                    ]
                }
            }
        },
        imagemin: {
            all: {
                expand: true,
                cwd: SOURCE_DIR,
                src: [
                    '{admin,public}/images/**/*.{png,jpg,gif,jpeg}'
                ],
                dest: SOURCE_DIR
            }
        },
        _watch: {
            all: {
                files: [
                    SOURCE_DIR + '**',
                    // Ignore version control directories.
                    '!' + SOURCE_DIR + '**/.{svn,git}/**'
                ],
                tasks: [ 'clean:dynamic', 'copy:dynamic' ],
                options: {
                    dot: true,
                    spawn: false,
                    interval: 2000
                }
            },
            config: {
                files: 'Gruntfile.js'
            }
            //,
            //test: {
            //    files: [
            //        'tests/qunit/**',
            //        '!tests/qunit/editor/**'
            //    ],
            //    tasks: [ 'qunit' ]
            //}
        }
    } );

    // Register tasks.

    grunt.renameTask( 'watch', '_watch' );

    grunt.registerTask( 'watch', function () {
        if ( !this.args.length || this.args.indexOf( 'browserify' ) > -1 ) {
            grunt.config( 'browserify.options', {
                browserifyOptions: {
                    debug: true
                },
                watch: true
            } );

            grunt.task.run( 'browserify' );
        }

        grunt.task.run( '_' + this.nameArgs );
    } );

    grunt.registerTask( 'copy:all', [
        'copy:files'
    ] );

    grunt.registerTask( 'build', [
        'clean:all',
        'copy:all',
        'uglify:admin',
        'jsvalidate:build'
    ] );

    // Testing tasks.
    grunt.registerMultiTask( 'phpunit', 'Runs PHPUnit tests.', function () {
        grunt.util.spawn( {
            cmd: this.data.cmd,
            args: this.data.args,
            opts: { stdio: 'inherit' }
        }, this.async() );
    } );

    //grunt.registerTask( 'qunit:compiled', 'Runs QUnit tests on compiled as well as uncompiled scripts.',
    //    [ 'build', 'copy:qunit', 'qunit' ] );

    //grunt.registerTask( 'test', 'Runs all QUnit and PHPUnit tasks.', [ 'qunit:compiled', 'phpunit' ] );

    // Travis CI tasks.
    //grunt.registerTask( 'travis:js', 'Runs Javascript Travis CI tasks.', [ 'jshint:corejs', 'qunit:compiled' ] );
    grunt.registerTask( 'travis:phpunit', 'Runs PHPUnit Travis CI tasks.', 'phpunit' );

    // Default task.
    grunt.registerTask( 'default', [ 'build' ] );

    /*
     * Automatically updates the `:dynamic` configurations
     * so that only the changed files are updated.
     */
    grunt.event.on( 'watch', function ( action, filepath, target ) {
        var src;

        if ( [ 'all', 'browserify' ].indexOf( target ) === -1 ) {
            return;
        }

        src = [ path.relative( SOURCE_DIR, filepath ) ];

        if ( action === 'deleted' ) {
            grunt.config( [ 'clean', 'dynamic', 'src' ], src );
        } else {
            grunt.config( [ 'copy', 'dynamic', 'src' ], src );

            if ( target === 'rtl' ) {
                grunt.config( [ 'dynamic', 'src' ], src );
            }
        }
    } );
};
