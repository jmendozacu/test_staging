<?php

/**
 * Twitter
 *
 */
class Twitter
{
    const ID = 'twitter';
    const NAME = 'Twitter';
    const SHARE_URL = 'http://twitter.com/intent/tweet?%s';
    const API_URL = 'http://cdn.api.twitter.com/1/urls/count.json?url=%s';
    const COLOR = '#1fc1f1';

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
		    
        	$data = json_decode($data);
			return intval($data->count);
		}
		
		return 0;	
    }
}
