module.exports = function (grunt){

    grunt.initConfig({
            pkg: grunt.file.readJSON('package.json'),
            cmp: grunt.file.readJSON('composer.json'),
            bumpup: {
                options: {
                    updateProps: {
                        pkg: 'package.json'
                    }
                },
                files: ['package.json', 'composer.json']
            },
            composer: {
                options: {
                    usePhp: true,
                    composerLocation: './composer.phar'
                },
                default: {
                    options: {
                        cwd: '.'
                    }
                }
            },
            compress: {
                main: {
                    options: {
                        archive: 'target/polyshapes-wpplugin.zip',
                        mode: 'zip'
                    },
                    expand: true,
                    src: [
                        'public/css/**',
                        'public/images/**',
                        'public/js/**',
                        'public/patches/',
                        'sql/**',
                        'src/**',
                        'vendor/**',
                        'templates/**',
                        'polyshapes-wpplugin.php',
                        'README.md'
                    ]
                }
            },
            replace: {
                versions: {
                    options: {
                        patterns: [
                            {
                                match: /Version:\s*(.*)/,
                                replacement: 'Version: <%= pkg.version %>'
                            }
                        ]
                    },
                    files: [
                        {
                            expand: true,
                            flatten: true,
                            src: ['README.md', 'polyshapes-wpplugin.php']
                        }
                    ]
                }
            },
            clean: [
                'target',
                'vendor',
                'release.json'
            ]
        }
    );

    // Alias task for release
    grunt.registerTask('release', function (type) {
        grunt.task.run('clean');        // clean previous builds
        type = type ? type : 'patch';     // default release type
        grunt.task.run('bumpup:' + type); // bump up the version
        grunt.task.run('replace');        // replace version number in plugin file and readme
        grunt.task.run('composer:default:install');         // get php dependencies
        grunt.task.run('compress');     // build a release zip
    });

    // Alias task for release with buildmeta suffix support
    grunt.registerTask('release', function (type, build) {
        grunt.task.run('clean');        // clean previous builds
        var bumpParts = ['bumpup'];
        if (type) { bumpParts.push(type); }
        if (build) { bumpParts.push(build); }
        grunt.task.run(bumpParts.join(':')); // bump up the version
        grunt.task.run('replace');        // replace version number in plugin file and readme
        grunt.task.run('composer:default:install');         // get php dependencies
        grunt.task.run('compress');     // build a release zip
    });

    grunt.registerTask('strider:releasefile', function (type) {
        var artifcatId = process.env.STRIDER_ARTIFACT_ID;
        var branch = process.env.STRIDER_BRANCH;
        if(!artifcatId || !branch) {
            grunt.fail.fatal("got no information in environment: STRIDER_ARTIFACT_ID or STRIDER_BRANCH missing!", 1);
        }
        var name =  process.env.STRIDER_PROJECT_NAME;
        var cmp = grunt.config.get("cmp");
        var projectName = name ? name : cmp.name;
        var releaseJson = {
            "name": projectName,
            "version": cmp.version,
            "download_url": cmp.extra.release.baseurl + projectName + "/api/artifact-repository/dl/" + artifcatId + "?branch=" + branch,
            "sections" : {
                "description": cmp.description
            }
        };
        grunt.file.write('release.json', JSON.stringify(releaseJson, null, 2));
    });

    grunt.registerTask('build', ['clean', 'composer:default:install']);
    grunt.registerTask('package', ['default', 'compress']);
    grunt.registerTask('default', ['build']);

    // needed modules
    grunt.loadNpmTasks('grunt-composer');
    grunt.loadNpmTasks('grunt-replace');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-bumpup');
    grunt.loadNpmTasks('grunt-contrib-clean');

}
