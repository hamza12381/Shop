<?php
/**
 * wishlist for content loop
 *
 * @package   Auxin Shop
 * @author    averta [averta.net]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product, $auxshp_wishlist;

// Set wishlist status
$wishlist_class = 'available-add';
if ( $auxshp_wishlist->in_wishlist( $product->get_id() ) ) {
    $wishlist_class = 'available-remove';
}

if ( auxin_is_true( auxin_get_option( 'product_wishlist_use_ti_plugin', false ) ) ) {
    echo do_shortcode( "[ti_wishlists_addtowishlist loop=yes]" );
} else { ?>
    <div class="auxshp-wishlist-wrapper">
        <span class="auxshp-wishlist <?php echo esc_attr( $wishlist_class ); ?>" data-auxshp-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-auxshp-verify_nonce="<?php echo wp_create_nonce( 'remove_wishlist-' . $product->get_id() ); ?>">
            <span class="auxshp-sw-icon auxicon-heart-2 auxshp-wishlist-icon"></span>
        </span>
    </div>
<?php 
}
