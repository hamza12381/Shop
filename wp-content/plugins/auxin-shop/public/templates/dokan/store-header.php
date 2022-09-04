<?php

global $wp;

$store_user    = dokan()->vendor->get( get_query_var( 'author' ) );
$store_info    = $store_user->get_shop_info();
$social_info   = $store_user->get_social_profiles();
$store_tabs    = dokan_get_store_tabs( $store_user->get_id() );
$social_fields = dokan_get_social_profile_fields();

$store_address        = dokan_get_seller_short_address( $store_user->get_id(), false );
$dokan_store_time_enabled = isset( $store_info['dokan_store_time_enabled'] ) ? $store_info['dokan_store_time_enabled'] : '';
$store_open_notice        = isset( $store_info['dokan_store_open_notice'] ) && ! empty( $store_info['dokan_store_open_notice'] ) ? $store_info['dokan_store_open_notice'] : __( 'Store Open', 'dokan-lite' );
$store_closed_notice      = isset( $store_info['dokan_store_close_notice'] ) && ! empty( $store_info['dokan_store_close_notice'] ) ? $store_info['dokan_store_close_notice'] : __( 'Store Closed', 'dokan-lite' );
$show_store_open_close    = dokan_get_option( 'store_open_close', 'dokan_general', 'on' );

$general_settings = get_option( 'dokan_general', [] );
$banner_width     = ! empty( $general_settings['store_banner_width'] ) ? $general_settings['store_banner_width'] : 625;

$image_size = array( 'width' => 1200, 'height' => 600 );
$default_banner_url = DOKAN_PLUGIN_ASSEST . '/images/default-store-banner.png'; 


$user_rating = dokan_get_seller_rating( $store_user->get_id() );
$rating        = is_numeric( $user_rating['rating'] ) ? $user_rating['rating'] : 0 ;

$rating_html  = '<div class="aux-rating-box aux-star-rating">';
$rating_html .= '<span class="aux-star-rating-avg" style="width: ' . ( $rating / 5 ) * 100 .'%">';
$rating_html .= '</span>';
$rating_html .= '</div>';


?>

<div class="aux-shop-single-vendor"> 
    <div class="aux-shop-single-vendor-header">
    <?php
        if ( ! empty( $store_info['banner'] ) ) {
            echo auxin_get_the_responsive_attachment( $store_info['banner'], array(  'size' => $image_size ) );
        } else {
            echo sprintf( '<img class = "aux-vendor-banner-img" src = "%s" width = "%s" height = "%s"/>', $default_banner_url, $image_size['width'], $image_size['height'] ) ;
        }
    ?>
    </div>
    <div class="aux-shop-single-vendor-info">
        <div class="aux-shop-single-vendor-img">
            <?php echo get_avatar( $store_user->get_id(), 210, '', $store_user->get_shop_name() ); ?>
        </div>
        <div class="aux-shop-single-vendor-meta">

        <?php if ( ! empty( $store_user->get_shop_name() ) ) { ?>
            <h3 class="aux-shop-single-vendor-name"><?php echo esc_html( $store_user->get_shop_name() ); ?></h3>
        <?php } ?>

            <ul class="aux-shop-single-vendor-info-list">
                <?php if ( isset( $store_address ) && !empty( $store_address ) ) { ?>
                    <li class="aux-shop-single-vendor-address"><i class="aux-ico auxicon-ios-location"></i>
                        <?php echo $store_address; ?>
                    </li>
                <?php } ?>

                <?php if ( !empty( $store_user->get_phone() ) ) { ?>
                    <li class="aux-shop-single-vendor-phone">
                        <i class="aux-ico auxicon-ios-telephone"></i>
                        <a href="tel:<?php echo esc_html( $store_user->get_phone() ); ?>"><?php echo esc_html( $store_user->get_phone() ); ?></a>
                    </li>
                <?php } ?>

                <?php if ( $store_user->show_email() == 'yes' ) { ?>
                    <li class="aux-shop-single-vendor-email">
                        <i class="auxicon-email-mail-streamline"></i>
                        <a href="mailto:<?php echo antispambot( $store_user->get_email() ); ?>"><?php echo antispambot( $store_user->get_email() ); ?></a>
                    </li>
                <?php } ?>

                <?php if ( $rating != 0 ) { ;?>
                <li class="aux-shop-single-vendor-rating">
                    <?php echo $rating_html ;?>
                </li>
                <?php } ;?>

                <?php if ( $show_store_open_close == 'on' && $dokan_store_time_enabled == 'yes') : ?>
                    <li class="daux-shop-single-vendor-open-close">
                        <i class="fa fa-shopping-cart"></i>
                        <?php if ( dokan_is_store_open( $store_user->get_id() ) ) {
                            echo esc_attr( $store_open_notice );
                        } else {
                            echo esc_attr( $store_closed_notice );
                        } ?>
                    </li>
                <?php endif ?>

            </ul>
            
            <div class="aux-single-vendor-btn">
                <ul class="aux-single-vendor-btn-list">
                    <?php do_action( 'dokan_after_store_tabs');?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php if ( $store_tabs ) { ?>
    <div class="aux-single-vendor-tabs">
        <ul class="aux-single-vendor-tabs-list">
            <?php foreach( $store_tabs as $key => $tab ) { 
                
                $current_url = home_url( add_query_arg( array(), $wp->request ) );
                $tab_url     = trim($tab['url'],'/');
                $selected    = $current_url === $tab_url ? 'class="aux-active"': '';
                ?>
                <li <?php echo $selected ;?>><a href="<?php echo esc_url( $tab_url ); ?>"><?php echo $tab['title']; ?></a></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>