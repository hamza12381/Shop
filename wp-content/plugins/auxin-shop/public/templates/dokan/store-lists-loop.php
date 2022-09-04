<?php
/**
 * Store List Loop Template
 *
 * This template can be overridden by copying it to yourtheme/dokan/store-lists-loop.php.
 *
 * HOWEVER, on occasion Dokan will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		Averta
 * @package 	Dokan/Templates
 * @version     1.0
*/


$default_banner_url = DOKAN_PLUGIN_ASSEST . '/images/default-store-banner.png'; 
$column_media_width = auxin_get_content_column_width( 3, 15 );
$image_size         = array( 'width' => $column_media_width, 'height' => $column_media_width * 0.56 );
$wrapper_classname  = 'aux-row aux-de-col3 aux-tb-col2 aux-mb-col1';
$output = '';

?>
<div class="aux-shop-vendors-archive aux-widget-latest-vendors <?php echo esc_attr( $wrapper_classname );?>">
    <?php
        foreach ( $sellers['users'] as $seller ) {
            $store_info   = dokan_get_store_info( $seller->ID );
            $banner      = $store_info['banner'] ? auxin_get_the_responsive_attachment( $store_info['banner'], array( 'size' => $image_size) ): sprintf( '<img class = "aux-vendor-banner-img" src = "%s" width = "%s" height = "%s"/>', $default_banner_url, $image_size['width'], $image_size['height'] ) ;
            $name        = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) :  __( 'N/A', 'auxin-shop' );
            $address     = dokan_get_seller_short_address( $seller->ID );
            $is_featured = get_user_meta( $seller->ID, 'dokan_feature_seller', true );


            $user_rating = dokan_get_seller_rating( $seller->ID );
            $rating        = is_numeric( $user_rating['rating'] ) ? $user_rating['rating'] : 0 ;

            $rating_html  = '<div class="aux-rating-box aux-star-rating">';
            $rating_html .= '<span class="aux-star-rating-avg" style="width: ' . ( $rating / 5 ) * 100 .'%">';
            $rating_html .= '</span>';
            $rating_html .= '</div>';

            $user_url  = dokan_get_store_url( $seller->ID );

            $output .= '<div class="aux-vendor-col aux-col">';
                $output .= '<div class="aux-vendor-box">';
                $output .= sprintf( '<span class="aux-vendor-featured">%s</span>', __( 'Featured', 'auxin-shop' ) ) ;
                $output .= '<div class="aux-vendor-image">'; 
                    $output .= sprintf( '<div class="aux-vendor-banner">%s</div>', $banner );
                    $output .= sprintf( '<div class="aux-vendor-avatar">%s</div>', get_avatar( $seller->ID, 90 ) ) ;
                $output .= '</div>';
                $output .= '<div class="aux-vendor-info">';
                    $output .= sprintf('<h3 class="aux-vendor-name"><a href="%s">%s</a></h3>' , $user_url, $name );
                    $output .= sprintf('<div class="aux-vendor-address">%s</div>' , $address ) ;
                    $output .= sprintf('<div class="aux-vendor-phone">%s</div>' , $store_info['phone'] ) ;
                $output .= '</div>';

                $output .= '<div class="aux-vendor-footer">';
                    $output .= $rating_html ;
                    $output .= sprintf( '<a href="%s" class="aux-vendor-url">%s<i class="auxicon-chevron-right-2"></i></a>', $user_url ,  __( 'View Store', 'auxin-shop' ) ) ;
                $output .= '</div>';
                

                $output .= '</div>';
            $output .= '</div>';
        }

        echo $output;

    ;?>
</div>
<?php

    $user_count   = $sellers['count'];
    $num_of_pages = ceil( $user_count / $limit );

    echo auxin_the_paginate_nav( $args = array(
        'total_pages' => $num_of_pages ,
        'css_class'   => esc_attr( auxin_get_option('product_index_pagination_skin') )
    ));