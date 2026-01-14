/**
 * Gruntfile for local_skill5 plugin.
 *
 * This Gruntfile uses the same Rollup/Babel/Terser configuration as Moodle core
 * to generate AMD modules that are compatible with Moodle's CI checks.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

module.exports = function(grunt) {
    const path = require("path");

    // Load NPM tasks.
    grunt.loadNpmTasks("grunt-rollup");
    grunt.loadNpmTasks("grunt-contrib-watch");

    /**
     * Rename function for AMD build output (minified).
     * Converts amd/src/file.js to amd/build/file.min.js
     *
     * @param {string} destPath The destination path.
     * @param {string} srcPath The source path.
     * @returns {string} The renamed path.
     */
    const babelRenameMin = function(destPath, srcPath) {
        destPath = srcPath.replace("amd/src", "amd/build");
        destPath = destPath.replace(/\.js$/, ".min.js");
        return destPath;
    };

    /**
     * Rename function for AMD build output (non-minified).
     * Converts amd/src/file.js to amd/build/file.js
     *
     * @param {string} destPath The destination path.
     * @param {string} srcPath The source path.
     * @returns {string} The renamed path.
     */
    const babelRename = function(destPath, srcPath) {
        destPath = srcPath.replace("amd/src", "amd/build");
        return destPath;
    };

    // Babel transform function using @babel/core.
    const babelTransform = require("@babel/core").transform;
    const babel = (options = {}) => {
        return {
            name: "babel",
            transform: (code, id) => {
                grunt.log.debug("Transforming " + id);
                options.filename = id;
                const transformed = babelTransform(code, options);
                return {
                    code: transformed.code,
                    map: transformed.map
                };
            }
        };
    };

    // Terser plugin for minification.
    const terser = require("rollup-plugin-terser").terser;

    // Grunt configuration.
    grunt.initConfig({
        rollup: {
            // Non-minified build (for development/debugging).
            dev: {
                options: {
                    format: "esm",
                    dir: "output",
                    sourcemap: true,
                    treeshake: false,
                    context: "window",
                    plugins: [
                        babel({
                            sourceMaps: true,
                            comments: true,
                            compact: false,
                            plugins: [
                                "transform-es2015-modules-amd-lazy",
                                "system-import-transformer"
                            ],
                            presets: [
                                ["@babel/preset-env", {
                                    modules: false,
                                    useBuiltIns: false
                                }]
                            ]
                        })
                    ]
                },
                files: [{
                    expand: true,
                    src: ["amd/src/*.js", "amd/src/**/*.js"],
                    rename: babelRename
                }]
            },
            // Minified build (for production).
            dist: {
                options: {
                    format: "esm",
                    dir: "output",
                    sourcemap: true,
                    treeshake: false,
                    context: "window",
                    plugins: [
                        babel({
                            sourceMaps: true,
                            comments: false,
                            compact: false,
                            plugins: [
                                "transform-es2015-modules-amd-lazy",
                                "system-import-transformer"
                            ],
                            presets: [
                                ["@babel/preset-env", {
                                    modules: false,
                                    useBuiltIns: false
                                }]
                            ]
                        }),
                        terser({
                            // Do not mangle variables (matches Moodle config).
                            mangle: false
                        })
                    ]
                },
                files: [{
                    expand: true,
                    src: ["amd/src/*.js", "amd/src/**/*.js"],
                    rename: babelRenameMin
                }]
            }
        },
        watch: {
            amd: {
                files: ["amd/src/*.js", "amd/src/**/*.js"],
                tasks: ["amd"]
            }
        }
    });

    // Register tasks.
    grunt.registerTask("amd", ["rollup:dev", "rollup:dist"]);
    grunt.registerTask("default", ["amd"]);

    // Stylelint task (empty for plugins without SCSS).
    grunt.registerTask("stylelint", []);
};
