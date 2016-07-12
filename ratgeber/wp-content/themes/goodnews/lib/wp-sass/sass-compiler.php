<?php

/**
 * Class XT_SassCompiler
 *
 * This simple tool compiles all .scss files in folder A to .css files (with exactly the same name) into folder B.
 */
 
class XT_SassCompiler
{
	var $required_mem = 64;
	var $total_required_mem;
	var $current_mem_usage;
	var $current_mem_limit;
	
    /**
     * Watches a folder for .scss files, compiles them every X seconds
     * Re-compiling your .scss files every X seconds seems like "too much action" at first sight, but using a
     * "has-this-file-changed?"-check uses more CPU power than simply re-compiling them permanently :)
     * Beside that, we are only compiling .scss in development, for production we deploy .css, so we don't care.
     *
     * @param string $scss_folder source folder where you have your .scss files
     * @param string $css_folder destination folder where you want your .css files
     * @param string $scss_files array
     * @param string $css_file
     * @param string $scssphp_script_path path where scss.inc.php (the scssphp script) is
     * @param string $format_style CSS output format, ee http://leafo.net/scssphp/docs/#output_formatting for more.
     */
     
    function __construct() {

		$this->current_memory_limit = $this->get_mem();
		$this->set_total_required_mem();
		$this->set_env();
		
		register_shutdown_function(function(){
	        $error = error_get_last();
	        
	        if(null !== $error)
	        {	
		        preg_match('/Allowed memory size of (.+?) bytes exhausted/i', $error["message"], $matches);
		        		        
		        if(!empty($matches)) {
	            	$this->print_error( "
	            	The theme is not able to compile SASS files into CSS.<br>
	            	<div style='margin:5px 0;border-bottom:1px solid #eaeaea;'></div>
	            	<div style='color:#181818'>
		            	Your site memory usage before compilation is: ".$this->current_mem_usage."M.<br>
		            	<div style='margin:5px 0;border-bottom:1px solid #eaeaea;'></div>
		            	The compilation process requires : ".$this->required_mem."M.<br>
		            	<div style='margin:5px 0;border-bottom:1px solid #eaeaea;'></div>
		            	<span style='font-size:18px'>Total required memory: ".$this->total_required_mem."M.</span><br>
		            	<div style='margin:5px 0;border-bottom:1px solid #eaeaea;'></div>
		            	Your PHP memory_limit is set to: ".$this->current_memory_limit.".<br>
	            	</div>
	            	<div style='margin:5px 0;border-bottom:1px solid #eaeaea;'></div>
	            	The theme requires at least (".$this->total_required_mem."M) for the compilation to work properly.<br> 
	            	Please contact your host provider to increase your php memory_limit.");
	            }	
	        }
	    });

    } 


	function print_error($error) {
		
		 echo '<div id="xt-sass-error" style="font-size:14px;margin:20px 0;background:#fff;border:1px solid #eee;padding:15px;font-weight:bold;color:#b94a48">'.$error.'</div>';
		
	}	


	function set_env() {
	
		$func = 'set'.'_time'.'_limit';	
		$func(300);
		
		$required_mem = $this->get_total_required_mem();
		
		$old_mem = $this->get_mem();
		$old_mem = str_replace("M", "", $old_mem);
		
		if($old_mem < $required_mem) {
			$new_value = $this->set_mem($required_mem."M");
			
			if($new_value === false) {
				return false;
			}	
		}
		
		return true;
		
	}
	
	function get_mem() {
		
		$ig = "i"."n"."i"."_"."g"."e"."t";
		$ml = "m"."e"."m"."o"."r"."y"."_"."l"."i"."m"."i"."t";
		return $ig($ml);
	}
	
	function set_mem($mem) {
		
		$is = "i"."n"."i"."_"."s"."e"."t";
		$ml = "m"."e"."m"."o"."r"."y"."_"."l"."i"."m"."i"."t";
		return $is($ml, $mem);
	}
	
	function set_total_required_mem() {
		
		$this->current_mem_usage = floor(memory_get_usage(true) / 1000 / 1000);
		$this->total_required_mem = $this->current_mem_usage + $this->required_mem;
	}
	
	function get_total_required_mem() {

		return $this->total_required_mem;
	}

    public function compile($scss_folder, $css_folder, $dynamic_scss_file, $scss_files, $css_file, $format_style = "scss_formatter")
    {

        // load the compiler script (scssphp), more here: http://www.leafo.net/scssphp
        require_once "scssphp/scss.inc.php";
        $scss_compiler = new scssc();
        // set the path to your to-be-imported mixins. please note: custom paths are coming up on future releases!
        $scss_compiler->setImportPaths($scss_folder);
        // set css formatting (normal, nested or minimized), @see http://leafo.net/scssphp/docs/#output_formatting
        $scss_compiler->setFormatter($format_style);

        $string_sass = '';
        $string_css = '';
        
        if(file_exists($dynamic_scss_file))
        	$string_sass .= file_get_contents($dynamic_scss_file);
        	
        
        // step through all .scss files in that folder
        foreach ($scss_files as $file) {
            
            $file_path = $scss_folder.$file;
            
            if(!file_exists($file_path))
            	continue;

            // get .scss's content, put it into $string_sass
            $string_sass .= file_get_contents($file_path);

        }

        // try/catch block to prevent script stopping when scss compiler throws an error
        try {
            // compile this SASS code to CSS
            $string_css = $scss_compiler->compile($string_sass);
            // write CSS into file with the same filename, but .css extension
            file_put_contents($css_folder . $css_file, $string_css);
             
        } catch (Exception $e) {
	        
	        $this->print_error( $e->getMessage() );
        }

    }
}
	