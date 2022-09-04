<?php
/**
 * Dokan Seller Single product tab Template
 *
 * @since 2.4
 *
 * @package dokan
 */


?>

<div class="aux-single-product-vendor-info">
    <div class="aux-single-product-vendor-info-img"> <?php
        echo get_avatar( $author->ID, 225, '', $store_info['store_name'] );
    ?></div>
    <div class="aux-single-product-vendor-info-box">

        <?php if ( !empty( $store_info['store_name'] ) ) { ?>
            <h4 class="aux-single-product-vendor-info-name"><?php echo esc_html( $store_info['store_name'] ) ?></h4>
        <?php } ?>

        <div class="aux-single-product-vendor-info-rating">
         <?php dokan_get_readable_seller_rating( $author->ID ); ?>
        </div>

        <div class="aux-single-product-vendor-info-link">
            <?php printf( '<a href="%s">%s</a>', dokan_get_store_url( $author->ID ), $author->display_name ); ?>
        </div>

        <?php if ( !empty( $store_info['address'] ) ) { ?>
            <div class="aux-single-product-vendor-info-address">
                <span><?php _e('Address:', 'auxin-shop' );?></span>
                <?php echo dokan_get_seller_address( $author->ID ) ?>
            </div>
        <?php } ?>


    </div>
</div>