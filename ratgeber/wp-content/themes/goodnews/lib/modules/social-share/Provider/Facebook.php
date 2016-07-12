<?php

/**
 * Facebook
 *
 */
class Facebook
{
	const ID = 'facebook';
    const NAME = 'Facebook';
    const SHARE_URL = 'http://www.facebook.com/sharer/sharer.php?u=%s';
    const API_URL = 'http://graph.facebook.com/?id=%s';
    const COLOR = '#3b5998';

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
        return sprintf(self::SHARE_URL, urlencode($url));
    }

    /**
     * {@inheritDoc}
     */
    public function getShares($url)
    {
	    $data = @file_get_contents(sprintf(self::API_URL, $url));
	    $count = 0;
	    
	    if(!empty($data)) {
       		$data = json_decode($data);
       		
       		if(isset($data->likes)) {
	       		$count += intval(isset($data->likes));
       		}
       		
       		if(isset($data->shares)) {
	       		$count += intval(isset($data->shares));
       		}
	   	}
	   	
	   	return $count;	
    }
}
