module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concat: {
            options: {
                separator: ';'
            },
            ui: {
                src: [
                    'resources/assets/js/sharp.ui.js',
                    'resources/assets/js/sharp.conditional_display.js'
                ],
                dest: 'resources/assets/<%= pkg.name %>.ui.js'
            },
            advancedsearch: {
                src: [
                    'resources/assets/bower_components/selectize/dist/js/selectize.js',
                    'resources/assets/js/advancedsearch/sharp.adv.js',
                    'resources/assets/js/advancedsearch/sharp.adv.tags.js'
                ],
                dest: 'resources/assets/<%= pkg.name %>.advancedsearch.js'
            },
            form: {
                src: [
                    // Tags, ref
                    'resources/assets/bower_components/microplugin/src/microplugin.js',
                    'resources/assets/bower_components/sifter/sifter.js',
                    'resources/assets/bower_components/selectize/dist/js/selectize.js',
                    // Date
                    'resources/assets/bower_components/datetimepicker/jquery.datetimepicker.js',
                    // Markdown
                    'resources/assets/bower_components/mirrormark/dist/js/mirrormark.package.js',
                    // Upload
                    'resources/assets/bower_components/jquery-file-upload/js/jquery.iframe-transport.js',
                    'resources/assets/bower_components/jquery-file-upload/js/jquery.fileupload.js',
                    // Image crop
                    'resources/assets/bower_components/imgareaselect/jquery.imgareaselect.dev.js',
                    // Sharp
                    'resources/assets/js/sharp.form.js',
                    'resources/assets/js/sharp.date.js',
                    'resources/assets/js/sharp.embed.js',
                    'resources/assets/js/sharp.markdown.js',
                    'resources/assets/js/sharp.tags.js',
                    'resources/assets/js/sharp.ref.js',
                    'resources/assets/js/sharp.refSublistItem.js',
                    'resources/assets/js/sharp.upload.js',
                    'resources/assets/js/sharp.imagecrop.js',
                    'resources/assets/js/sharp.list.js'
                ],
                dest: 'resources/assets/<%= pkg.name %>.form.js'
            }
        },

        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
            },
            dist: {
                files: {
                    'resources/assets/<%= pkg.name %>.ui.min.js': ['<%= concat.ui.dest %>'],
                    'resources/assets/<%= pkg.name %>.advancedsearch.min.js': ['<%= concat.advancedsearch.dest %>'],
                    'resources/assets/<%= pkg.name %>.form.min.js': ['<%= concat.form.dest %>']
                }
            }
        },

        less: {
            development: {
                options: {
                    paths: ["resources/assets/less"]
                },
                files: {
                    "public/css/sharp.css": "resources/assets/less/main.less"
                }
            }
        },

        cssmin: {
            target: {
                files: {
                    'public/css/sharp.min.css': [
                        'resources/assets/bower_components/mirrormark/dist/css/mirrormark.package.css',
                        'resources/assets/sharp.css'
                    ]
                }
            }
        },

        watch: {
            js: {
                files: [
                    '<%= concat.ui.src %>',
                    '<%= concat.advancedsearch.src %>',
                    '<%= concat.form.src %>'
                ],
                tasks: ['concat', 'uglify']
            },

            css: {
                files: ['resources/assets/less/**/*.less'],
                tasks: ['less']
            },

            mincss: {
                files: ['resources/assets/sharp.css'],
                tasks: ['cssmin']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('default', ['concat', 'uglify', 'less', 'mincss']);

};