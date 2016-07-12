jQuery(function($) {

	$(document).ready(function() {
	
		xt_advms_init();

	});
	$( document ).ajaxComplete(function() {
	
	  	xt_advms_init();
	});
	
	function xt_advms_init() {
		
		target = '.adv-multiselect';
		
		if(advms_vars.is_admin) {
			target += ',.wp-admin select[multiple="multiple"]';
		}
		
		$(target).not('.skip-adv-multiselect').multiSelect({
		  selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Filter'>",
		  selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Filter'>",
		  afterInit: function(ms){
		    var that = this,
		        $selectableSearch = that.$selectableUl.prev(),
		        $selectionSearch = that.$selectionUl.prev(),
		        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
		        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
		
		    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
		    .on('keydown', function(e){
		      if (e.which === 40){
		        that.$selectableUl.focus();
		        return false;
		      }
		    });
		
		    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
		    .on('keydown', function(e){
		      if (e.which == 40){
		        that.$selectionUl.focus();
		        return false;
		      }
		    });
		    
		    $(that).fadeIn();
		  },
		  afterSelect: function(){
		    this.qs1.cache();
		    this.qs2.cache();
		  },
		  afterDeselect: function(){
		    this.qs1.cache();
		    this.qs2.cache();
		  }
		});
		
	}
});