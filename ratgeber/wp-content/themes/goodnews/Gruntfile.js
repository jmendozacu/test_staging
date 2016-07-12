module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
	concat: {
		options: {
			separator: ';'
		},
 
		foundation: {
			
		    src: [
		    		'bower_components/foundation/js/foundation/foundation.js',
					'bower_components/foundation/js/foundation/foundation.accordion.js',
					'bower_components/foundation/js/foundation/foundation.alert.js',
					'bower_components/foundation/js/foundation/foundation.dropdown.js',
					'bower_components/foundation/js/foundation/foundation.equalizer.js',
					'bower_components/foundation/js/foundation/foundation.offcanvas.js',
					'bower_components/foundation/js/foundation/foundation.orbit.js',
					'bower_components/foundation/js/foundation/foundation.interchange.js',
					//'bower_components/foundation/js/foundation/foundation.reveal.js',
					'bower_components/foundation/js/foundation/foundation.tab.js',
					//'bower_components/foundation/js/foundation/foundation.joyride.js',
					'bower_components/foundation/js/foundation/foundation.tooltip.js',
					'assets/js/foundation/topbar.extend.js'
					//'bower_components/foundation/js/foundation/foundation.topbar.js'
		    	 ],
		    dest: 'bower_components/foundation/js/foundation.js'
		    
		},
		vendors_js: {
		    
		    src: [
		    		'assets/vendors/chosen/chosen.jquery.js',
					'assets/vendors/stroll-js/stroll.js',
					'assets/vendors/sticky-sidebar/js/theia-sticky-sidebar.js',
					'assets/vendors/special-scroll/jquery.special-scroll.js',		    		
					'assets/vendors/jquery-nicescroll/jquery.nicescroll.js',
					'assets/vendors/jquery-inview/jquery.inview.js',
					'assets/vendors/jquery-scrollTo/jquery.scrollTo.js',
					'assets/vendors/jquery-parallax/jquery.parallax.js',
					'assets/vendors/jquery-autotype/jquery.autotype.js',
					'assets/vendors/jquery-easing/jquery.easing.1.3.js',
					'assets/vendors/jquery-cookie/jquery.cookie.js',
					'assets/vendors/owl/owl.carousel.js',
					'assets/vendors/twitter/jquery.tweet.js',
					'assets/vendors/retina/retina.js',
		    	 ],
		    dest: 'assets/js/vendors.js'
		    
		},
		vendors_css: {
		    
		    src: [
					'assets/vendors/stroll-js/css/stroll.css',
					'assets/vendors/hover-effect/hover-effect.css',
					'assets/vendors/animate/animate.css'
		    	 ],
		    dest: 'assets/css/vendors.css'
		    
		}  

    },
    uglify: {
      foundation: {
        files: {
          'bower_components/foundation/js/foundation.min.js': ['<%= concat.foundation.dest %>']
	    }
	  },
	  modernizr: {
        files: {
          'bower_components/modernizr/modernizr.min.js': ['bower_components/modernizr/modernizr.js']
	    }
	  },
	  vendors_js: {
	  	files: {
	      'assets/js/vendors.min.js': ['<%= concat.vendors_js.dest %>']
	    }
	  },
	  theme: {
	  	files: {
	      'assets/js/theme.min.js': ['assets/js/theme.js']
	    }
	  }  
	},
	jsvalidate: {
		options:{
		  globals: {},
		  esprimaOptions: {},
		  verbose: false
		},
		targetName:{
		  files:{
		    src:['<%=jshint.all%>']
		  }
		}
	},
	jshint: {
		options: {
			//undef: true,
			//unused: true,
			predef: [ "MY_GLOBAL" ],
			eqeqeq: true,
			eqnull: true,
			browser: true,
			globals: {
	        	jQuery: true
	      	},
	    },
		all: ['assets/js/theme.js']
	},   
    scsslint: {
		allFiles: [
		  'assets/scss/**/*.scss',
		],
		options: {
		  bundleExec: true,
		  config: '.scss-lint.yml',
		  reporterOutput: 'scss-lint-report.xml',
		  colorizeOutput: true
		},
	},
    sass: {
      options: {
        includePaths: ['bower_components/foundation/scss']
      },
      dist: {
        options: {
          outputStyle: 'compressed'
        },
        files: {
          'assets/css/style.min.css': 'assets/scss/style.scss'
        }        
      },
      dev: {
        files: {
          'assets/css/style.css': 'assets/scss/style.scss'
        }        
      }
    },
	cssmin: {
	  vendors_css:{
	    src: 'assets/css/vendors.css',
	    dest: 'assets/css/vendors.min.css'
	  }
	},
	touch: {
		options: {
		  force: true,
		  mtime: true
		},
		src: ['assets/scss'],
	},
    watch: {
      grunt: { 
      	files: ['Gruntfile.js'],
      	tasks: ['concat', 'uglify'] 
      },
      sass: {
        files: 'assets/scss/**/*.scss',
        tasks: ['sass', 'touch']
      },
      concat: {
        files: 'bower_components/foundation/js/foundation/*.js',
        tasks: ['concat']
      },
      uglify: {
        files: 'assets/js/{vendors,theme}.js',
        tasks: ['uglify']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-jsvalidate');  
  grunt.loadNpmTasks('grunt-scss-lint');
  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-touch');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('build', ['jsvalidate', 'jshint','sass','concat','cssmin','uglify']);
  grunt.registerTask('default', ['build','touch','watch']);
}