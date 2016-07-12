<?php

/**
 * LinkedIn
 *
 */
class LinkedIn
{
    const ID = 'linkedin';
    const NAME = 'LinkedIn';
    const SHARE_URL = 'http://www.linkedin.com/shareArticle?%s';
    const API_URL = 'http://www.linkedin.com/countserv/count/share?url=%s&format=json';
    const COLOR = '#0976b4';

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
     * Gets the share link for the URL
     *
     * @param array  $options This provider supports the following options:<pre>
     *                        title: the title
     *                        summary: the summary
     *                        source: the source
     *                        </pre>
     * @param string $url
     * @param array  $options
     *
     * @return string
     */
    public function getLink($url, array $options = array())
    {
        $options['mini'] = 'true';
        $options['url'] = $url;

        return sprintf(self::SHARE_URL, http_build_query($options));
    }

    /**
     * {@inheritDoc}
     */
    public function getShares($url)
    {
	    $data = @file_get_contents(sprintf(self::API_URL, $url));
		
		if(!empty($data->count)) {
        	return intval($data->count);
        }
        
        return 0;	
    }
}
