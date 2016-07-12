<?php

/**
 * StumbleUpon
 *
 */
class StumbleUpon
{
    const ID = 'stumbleupon';
    const NAME = 'StumbleUpon';
    const SHARE_URL = 'http://www.stumbleupon.com/badge/?%s';
    const API_URL = 'http://www.stumbleupon.com/services/1.01/badge.getinfo?url=%s';
    const COLOR = '#eb4924';

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
		    
        	$data = json_decode($data, true);
			return isset($data['result']['views']) ? intval($data['result']['views']) : 0;
		}
		
		return 0;	
    }
}
