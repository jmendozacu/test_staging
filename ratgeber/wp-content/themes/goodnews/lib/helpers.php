<?php
/**
 * Insert an array into another array before/after a certain key
 *
 * @param array $array The initial array
 * @param array $pairs The array to insert
 * @param string $key The certain key
 * @param string $position Wether to insert the array before or after the key
 * @return array
 */


function xt_array_insert ( $array, $pairs, $key, $position = 'after' )
{
	$key_pos = array_search( $key, array_keys($array) );

	if ( 'after' == $position )
		$key_pos++;

	if ( false !== $key_pos ) {
		$result = array_slice( $array, 0, $key_pos );
		$result = array_merge( $result, $pairs );
		$result = array_merge( $result, array_slice( $array, $key_pos ) );
	}
	else {
		$result = array_merge( $array, $pairs );
	}

	return $result;
}



/**
* Hex 2 RGB values
*/

function xt_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return implode(",", $rgb); // returns the rgb values separated by commas
   // return $rgb; // returns an array with the rgb values
}

function xt_hex2rgba($color, $opacity = false) {

	$default = '';

	//Return default if no color provided
	if(empty($color))
          return $default; 

	//Sanitize $color if "#" is provided 
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity && !is_array($opacity)){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;

        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
}


/**
* setting a random id
*/

function xt_random_id($id_length) {
	$random_id_length = $id_length; 
	$rnd_id = crypt(uniqid(rand(),1)); 
	$rnd_id = strip_tags(stripslashes($rnd_id)); 
	$rnd_id = str_replace(".","",$rnd_id); 
	$rnd_id = strrev(str_replace("/","",$rnd_id)); 
	$rnd_id = str_replace(range(0,9),"",$rnd_id); 
	$rnd_id = substr($rnd_id,0,$random_id_length); 
	$rnd_id = strtolower($rnd_id);  

	return $rnd_id;
}


/**
 * trims text to a space then adds ellipses if desired
 * @param string $input text to trim
 * @param int $length in characters to trim to
 * @param bool $ellipses if ellipses (...) are to be added
 * @param bool $strip_html if html tags are to be stripped
 * @return string 
 */
function xt_trim_text($input, $length = 80, $ellipses = true, $strip_html = true) {
    //strip tags, if desired
    if ($strip_html) {
        $input = strip_tags($input);
    }
  
    //no need to trim, already shorter than trim length
    if (strlen($input) <= $length) {
        return $input;
    }
  
    //find last space within length
    $last_space = strrpos(substr($input, 0, $length), ' ');
    $trimmed_text = substr($input, 0, $last_space);
  
    //add ellipses (...)
    if ($ellipses) {
        $trimmed_text .= '...';
    }
  
    return $trimmed_text;
}

function xt_trim_words($input, $limit, $trimmed_text = "...") {
	
	$output = explode(' ', $input, $limit);
	if (count($output) >= $limit) {
		array_pop($output);
		$output = implode(" ",$output).$trimmed_text;
	} else {
		$output = implode(" ",$output);
	}	
	$output = preg_replace('`\[[^\]]*\]`','',$output);
	$output = strip_tags($output, '<a><code><b><strong>');
	
	return $output;
}

function xt_get_user_ip() {

	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		
	} else {
		
		$ip = $_SERVER['REMOTE_ADDR'];
	
	}
	
	return apply_filters( 'xt_get_user_ip', $ip );
}

function xt_time_elapsed($date)
{

	$ptime = strtotime($date);
    $etime = time() - $ptime;

    if ($etime < 1)
    {
        return '0 '.__('seconds ago', XT_TEXT_DOMAIN);
    }

    $a = array( 12 * 30 * 24 * 60 * 60  =>  __('year', XT_TEXT_DOMAIN),
                30 * 24 * 60 * 60       =>  __('month', XT_TEXT_DOMAIN),
                24 * 60 * 60            =>  __('day', XT_TEXT_DOMAIN),
                60 * 60                 =>  __('hour', XT_TEXT_DOMAIN),
                60                      =>  __('minute', XT_TEXT_DOMAIN),
                1                       =>  __('second', XT_TEXT_DOMAIN)
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' '.__('ago', XT_TEXT_DOMAIN);
        }
    }
}

function xt_time_elapsed_DE($date)
{

	$ptime = strtotime($date);
    $etime = time() - $ptime;

    if ($etime < 1)
    {
        return __('ago', XT_TEXT_DOMAIN).'0 '.__('seconds ago', XT_TEXT_DOMAIN);
    }

    $a = array( 12 * 30 * 24 * 60 * 60  =>  __('year', XT_TEXT_DOMAIN),
                30 * 24 * 60 * 60       =>  __('month', XT_TEXT_DOMAIN),
                24 * 60 * 60            =>  __('day', XT_TEXT_DOMAIN),
                60 * 60                 =>  __('hour', XT_TEXT_DOMAIN),
                60                      =>  __('minute', XT_TEXT_DOMAIN),
                1                       =>  __('second', XT_TEXT_DOMAIN)
                );

    // $a = array( 365 * 24 * 60 * 60  =>  __('year', XT_TEXT_DOMAIN),

    $a_plural = array( __('year', XT_TEXT_DOMAIN)   => 'Jahren',
                       __('month', XT_TEXT_DOMAIN)  => 'Monaten',
                       __('day', XT_TEXT_DOMAIN)    => 'Tagen',
                       __('hour', XT_TEXT_DOMAIN)   => 'Studen',
                       __('minute', XT_TEXT_DOMAIN) => 'Minuten',
                       __('second', XT_TEXT_DOMAIN) => 'Sekunden'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return __('ago', XT_TEXT_DOMAIN) . ' ' . $r . ' '   . ($r > 1 ? $a_plural[$str] : $str);
            // return __('ago', XT_TEXT_DOMAIN) . ' ' . $r . ' '  . $str . ($r > 1 ? $a_plural[$str] : $str);
        }
    }
}


function xt_64_decode($string) {
	
	$decode = "base64"."_"."decode";
	$decode($string);
	return $string;
}

function xt_64_encode($string) {
	
	$encode = "base64"."_"."encode";
	$encode($string);
	return $string;
}

/**
 * Retrieves the response from the specified URL using one of PHP's outbound request facilities.
 *
 * @params	$url	The URL of the feed to retrieve.
 * @returns			The response from the URL; null if empty.
 */
function xt_get_url_contents( $url ) {

	$response = null;

	// First, we try to use wp_remote_get
	$response = wp_remote_get( $url );
	if( is_wp_error( $response ) ) {

		// And if that doesn't work, then we'll try curl
		$curl = curl_init( $url );
	
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HEADER, 0 );
		curl_setopt( $curl, CURLOPT_USERAGENT, '' );
		curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	
		$response = curl_exec( $curl );
		if( 0 !== curl_errno( $curl ) || 200 !== curl_getinfo( $curl, CURLINFO_HTTP_CODE ) ) {
			$response = null;
		} // end if
		curl_close( $curl );

		$url_data = parse_url($url);

		if( null == $response && !empty($url_data['scheme']) && $url_data['scheme'] != 'https') {
			
			// If that doesn't work, then we'll try file_get_contents
			$response = file_get_contents( $url );
			if( false == $response ) {

				$response = 0;
			}
				
		} // end if/else

	} // end if

	// If the response is an array, it's coming from wp_remote_get,
	// so we just want to capture to the body index for json_decode.
	if( is_array( $response ) ) {
		$response = $response['body'];
	} // end if/else

	return $response;

} // end get_response



function xt_recursive_array_search( $needle, $haystack ) 
{
    foreach( $haystack as $key => $value ) 
    {
        $current_key = $key;
        if( 
            $needle === $value 
            OR ( 
                is_array( $value )
                && xt_recursive_array_search( $needle, $value ) !== false 
            )
        ) 
        {
            return $current_key;
        }
    }
    return false;
}

