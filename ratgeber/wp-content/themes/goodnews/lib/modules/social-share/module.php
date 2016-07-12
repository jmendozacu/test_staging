<?php	
/**
 * SocialShare
 *
 */
 
class XT_SocialShare
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var array
     */
    protected $providers = array();

    /**
     * @var false|array
     */
    protected $selected = false;
        
    /**
     * @var array
     */
    protected $toUpdate = array();
 
     /**
     * @var array
     */
    protected $skins = array();
       
    /**
     * @var string
     */
    protected $show_title = true; 
    
         
    /**
     * @var string
     */
    protected $title = 'Share';     
      
       
    /**
     * @var string
     */
    protected $skin = 'default';
 
	 /**
     * @var bool
     *
     * horizontal|vertical
     */   
    protected $layout = 'horizontal'; 
        
     /**
     * @var string
     *
     * small|medium|large
     */   
    protected $size = 'medium';
    
     /**	
     * @var string
     *
     * left|right|center
     */   
    protected $align = 'left';  

     /**
     * @var bool
     */   
    protected $is_fullwidth = false; 
 
 
     /**
     * @var bool
     */   
    protected $radius = false; 
  
     /**
     * @var bool
     */   
    protected $rounded = false; 
    
          
     /**
     * @var bool
     */   
    protected $show_names = false; 
    	    
     /**
     * @var bool
     */   
    protected $show_shares = false; 
    
     /**
     * @var bool
     */   
    protected $show_total_shares = false; 

	
                
    /**
     * @param Cache $cache
     */
    public function __construct($params = array(), $selected = array())
    {
	    $this->setParams($params);
	    $this->setSelected($selected);
	    
	    add_action('wp_enqueue_scripts', array(&$this,'enqueue_styles'), 1);
	    
	    $this->registerProviders();
	    $this->registerSkins();
    }

        
    public function setParams($params = array()) {
	    
	    foreach($params as $key => $value) {
		    
		    if(isset($this->$key)) {
			    $this->$key = $value;
		    }
		    
	    }
 
    }

    public function setSelected($providers = array()) {
	    
	    if(!empty($providers)) {
	    	$this->selected = $providers;
	    	$this->providers = array();
	    	$this->registerProviders();
	    }
    }

	public function getSkins() {
		
		return $this->skins;
	}
	
	public function getProviders() {
		
		return $this->providers;
	}
	
	public function getProvidersIds() {

		return array_keys($this->providers);
	}
	
	
	public function getProvidersNames() {
		
		$providers = array();
		foreach($this->providers as $providerID => $provider) {
			$providers[$providerID] = $provider['provider']->getName();
		}
		return $providers;
	}
	
	
    public function enqueue_styles() {
	    
	     wp_register_style('xt_socialshare_styles', XT_MOD_URL.'/social-share/skins/'.$this->skin.'.css');
		 wp_enqueue_style('xt_socialshare_styles');
    }

	public function registerSkins() {
		
	    $skins = glob(dirname(__FILE__).'/skins/*.css');
	   
        foreach($skins as $skin) {
	        $skin_id = str_replace('.css', '', basename($skin));
	        $this->skins[$skin_id] = ucfirst($skin_id);
        }
       
	}
	
	
	public function registerProviders() {
		
		if(empty($this->selected)) {
		    $providers = glob(dirname(__FILE__).'/Provider/*.php');
		    
	        foreach($providers as $provider) {
		        include_once($provider);
		        $providerClass = str_replace('.php', '', basename($provider));
		        $this->registerProvider(new $providerClass);
	        }
        }else{
	        foreach($this->selected as $provider) {
		        
		        $providerPath = dirname(__FILE__).'/Provider/'.$provider.'.php';
		        if(file_exists($providerPath)) {
		       	 	include_once($providerPath);
			        $this->registerProvider(new $provider);
			    } 
	        }
        }
		
	}
	
	
    /**
     * Registers a provider
     *
     * @param ProviderInterface $provider
     * @param int|\DateInterval $lifeTime Life time in seconds or a \DateInterval instance
     */
    public function registerProvider($provider, $lifeTime = 3600)
    {
        if (!$lifeTime instanceof \DateInterval) {
            $lifeTime = new \DateInterval(sprintf('PT%dS', $lifeTime));
        }

        $this->providers[$provider->getId()] = array('provider' => $provider, 'lifeTime' => $lifeTime);
    }

    /**
     * Gets the color for the given provider
     *
     * @param  string            $providerID
     * @param  string            $url
     * @param  array             $options
     * @throws \RuntimeException
     * @return string
     */
    public function getName($providerID)
    {
        $this->checkProvider($providerID);

        return  $this->providers[$providerID]['provider']->getName($providerID);
    }
    
    /**
     * Gets the color for the given provider
     *
     * @param  string            $providerID
     * @param  string            $url
     * @param  array             $options
     * @throws \RuntimeException
     * @return string
     */
    public function getColor($providerID)
    {
        $this->checkProvider($providerID);

        return  $this->providers[$providerID]['provider']->getColor($providerID);
    }
    
    /**
     * Gets the sharing links for the given provider and url
     *
     * @param  string            $providerID
     * @param  string            $url
     * @param  array             $options
     * @throws \RuntimeException
     * @return string
     */
    public function getLink($providerID, $url, array $options = array())
    {
        $this->checkProvider($providerID);

        return  $this->providers[$providerID]['provider']->getLink($url, $options);
    }

    /**
     * Gets the number of share of the given URL on the given provider
     *
     * @param  string            $providerID
     * @param  string            $url
     * @param  boolean           $delayUpdate
     * @throws \RuntimeException
     * @return int
     */
    public function getShares($providerID, $url, $delayUpdate = false)
    {
        $this->checkProvider($providerID);

        $id = $this->getId($providerID, $url);
        $lifeTime = $this->providers[$providerID]['lifeTime'];
        $now = new \DateTime();

        $dataFromCache = wp_cache_get($id, 'socialshare');
        $shares = isset($dataFromCache[0]) ? $dataFromCache[0] : false;
        $expired = isset($dataFromCache[1]) && $dataFromCache[1]->add($lifeTime) < $now;

        if (!$delayUpdate && (false === $shares || $expired)) {
            $shares = $this->providers[$providerID]['provider']->getShares($url);

            wp_cache_add($id, array($shares, $now), 'socialshare');
            
        } else {
            if ($delayUpdate && (false === $shares || $expired)) {
                $this->toUpdate[$providerID][] = $url;
            }

            $shares = intval($shares);
        }

        return $shares;
    }

    /**
     * Updates delayed URLs
     */
    public function update()
    {
        $now = new \DateTime();

        foreach ($this->toUpdate as $providerID => $urls) {
            foreach ($urls as $url) {
                $shares = $this->providers[$providerID]['provider']->getShares($url);

                wp_cache_add($this->getId($providerID, $url), array($shares, $now), 'socialshare');
            }
        }
    }
    
    
    public function render($url, $config = array(), $return = false) {
	    
	    
	    // Wrap Classes
		$position = xt_option('xtss_position');
	    $wrap_classes = array();
	    $wrap_classes[] = 'skin-'.$this->skin;
	    $wrap_classes[] = 'layout-'.$this->layout;
	    $wrap_classes[] = 'size-'.$this->size;
	    $wrap_classes[] = 'align-'.$this->align;

	    if($this->is_fullwidth && $this->layout == 'vertical')
	     	$wrap_classes[] = 'fullwidth';

	    if($this->show_names)
	     	$wrap_classes[] = 'showing-names';
	     	
	    if($this->show_shares)
	     	$wrap_classes[] = 'showing-shares';
	     	
	    if($this->show_total_shares)
	     	$wrap_classes[] = 'showing-total-shares'; 	
	     		     		     		     	
	    if(!empty($this->extraClass))
	     	$wrap_classes[] = $this->extraClass;	
	     	

		$wrap_classes = implode(" ", $wrap_classes);
		// ----
		
		
	
		// Inner Classes
		$inner_classes = array();
		$inner_classes[] = 'xtss-transition';
		$inner_classes = implode(" ", $inner_classes);
		// ----
		
		
		// Item Classes
		$item_classes = array();
		$item_classes[] = 'xtss-transition';
		
	    if($this->radius)
	     	$item_classes[] = 'radius';
	     	
	    if($this->rounded)
	     	$item_classes[] = 'round'; 	
	     		
		$item_classes = implode(" ", $item_classes);
		// ----
				
		
		$items = '';

		$providerShares = 0;
		$providerTotalShares = 0;
		
		foreach($this->providers as $providerID => $provider) {

			$options = array();
			if(!empty($config[$providerID])) {
				$options = $config[$providerID];
			}
			
			$providerName = $this->getName($providerID);
			$providerLink = $this->getLink($providerID, $url, $options);
			$providerColor = $this->getColor($providerID);

			if(!empty($options["link_class"])) {
				$item_classes .= ' '.$options["link_class"];
			}

			$providerNameOutput = '';
			if($this->show_names)
				$providerNameOutput = '<span class="xtss-name">'.$providerName.'</span>';
	     	
	     				
			$providerSharesOutput = '';
			
			if($this->show_shares || $this->show_total_shares) {
				$providerShares = $this->getShares($providerID, $url);
				$providerTotalShares += $providerShares;
				$providerSharesOutput = '<span class="xtss-shares xtss-transition">'.$providerShares.'</span>';
			}
			
			$items .=  '
			<a class="xtss-'.$providerID.' '.$item_classes.'" style="background-color:'.$providerColor.';" target="_blank" href="'.esc_attr($providerLink).'" title="'.__('Share on', XT_TEXT_DOMAIN).' '.$providerName.'">
				<span class="icon fa fa-'.esc_attr($providerID).' xtss-transition"></span>
				'.$providerNameOutput.'
				'.$providerSharesOutput.'
			</a>';
    
		}


		$output = '<div class="xtss-wrap '.$wrap_classes.'">';
		$output .= '	<div class="xtss '.$inner_classes.'">';

		if($this->show_title) {
			$output .= '<div class="xtss-title">'.$this->title.'</div>';
		}
		if($this->show_total_shares) {
			$output .= '<span class="xtss-total-shares xtss-transition">
							<span class="xtss-total-count">'.$providerTotalShares.'</span>
							<span class="xtss-total-text">'.__('Shares', XT_TEXT_DOMAIN).'</span>
						</span>';
		}
					
		$output .= 		'<span class="xtss-social-networks">'.$items.'</span>';
				
		$output .=  '	</div>';
		$output .=  '</div>';
    
		if($return) {
			return $output;
		}
		
		echo ($output);
    }

    /**
     * Checks if the provider is registered
     *
     * @param  string            $providerID
     * @throws \RuntimeException
     */
    private function checkProvider($providerID)
    {
        if (!isset($this->providers[$providerID])) {
            throw new \RuntimeException(sprintf('Unknown provider "%s".', $providerID));
        }
    }

    /**
     * Gets the ID corresponding to this provider name and URL
     *
     * @param  string $providerID
     * @param  string $url
     * @return string
     */
    private function getId($providerID, $url)
    {
        return sprintf('%s_%s', $providerID, $url);
    }
}

$XT_SocialShare = new XT_SocialShare();