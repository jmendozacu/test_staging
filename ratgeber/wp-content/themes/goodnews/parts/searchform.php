<form method="get" action="<?php echo home_url('/'); ?>">
	<div class="row collapse">
    	<div class="small-12 columns small-centered transition">
        	<input type="text" name="s" class="search-input" autocomplete="off" value="<?php the_search_query(); ?>" placeholder="<?php echo __('Start searching...', XT_TEXT_DOMAIN); ?>">
        	<a href="#" class="transparent button search-button"><i class="fa fa-search"></i></a>
        	<a href="#" class="transparent search-close-button"><i class="fa fa-times"></i></a>
		</div>
	</div>
</form>