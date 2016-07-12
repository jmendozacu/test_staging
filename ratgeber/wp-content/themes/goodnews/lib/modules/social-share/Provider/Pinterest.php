<?php

/**
 * Pinterest
 *
 */
class Pinterest
{
    const ID = 'pinterest';
    const NAME = 'Pinterest';
    const SHARE_URL = 'http://www.pinterest.com/pin/create/button/?%s';
    const API_URL = 'http://api.pinterest.com/v1/urls/count.json?url=%s';
    const COLOR = '#cc2127';

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return self::ID;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getColor()
    {
        return self::COLOR;
    }

    /**
     * {@inheritDoc}
     */
    public function getLink($url, array $options = array())
    {
        $options['url'] = $url;

        return sprintf(self::SHARE_URL, http_build_query($options));
    }

    /**
     * {@inheritDoc}
     */
    public function getShares($url)
    {
	    $data = @file_get_contents(sprintf(self::API_URL, urlencode($url)));
	    
	    if(!empty($data)) {
		    
        	$data = json_decode(preg_replace('/^receiveCount\((.*)\)$/', "\\1", $data));
			return intval($data->count);
			
		}else{
			return 0;
		}	
    }
}
