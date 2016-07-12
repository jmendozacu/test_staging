<?php
/**
 * Loop Rating
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' )
	return;
?>

<?php if ( $rating_html = $product->get_rating_html() ) : ?>
	<?php echo $rating_html; ?>
<?php else: ?>
	<div class="star-rating" title="<?php echo __("Not yet rated", 'woocommerce'); ?>"><span style="width:0%"><strong class="rating">0</strong> out of 5</span></div>	
<?php endif; ?>