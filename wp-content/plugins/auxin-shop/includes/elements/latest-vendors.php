<?php
/**
 * Advanced Recent Products Element
 *
 * 
 * @package    
 * @license    LICENSE.txt
 * @author     averta <info@averta.net>
 * @link       https://bitbucket.org/averta/
 * @copyright  (c) 2010-2022 averta <info@averta.net>
 */
function auxin_get_latest_vendors_master_array( $master_array ) {

    $master_array['aux_latest_vendors'] = array(
        'name'                    => __("Latest Vendors", 'auxin-shop'  ),
        'auxin_output_callback'   => 'auxin_widget_latest_vendors_callback',
        'base'                    => 'aux_latest_vendors',
        'description'             => __("a Widget for Display Latest Vendors of Your Shop", 'auxin-shop' ),
        'class'                   => 'aux-widget-latest-vendors',
        'show_settings_on_create' => true,
        'weight'                  => 1,
        'category'                => THEME_NAME,
        'is_vc'                   => true,
        'is_widget'               => true,
        'is_shortcode'            => true,
        'group'                   => '',
        'admin_enqueue_js'        => '',
        'admin_enqueue_css'       => '',
        'front_enqueue_js'        => '',
        'front_enqueue_css'       => '',
        'icon'                    => 'aux-element aux-pb-icons-grid',
        'custom_markup'           => '',
        'js_view'                 => '',
        'html_template'           => '',
        'deprecated'              => '',
        'content_element'         => '',
        'as_parent'               => '',
        'as_child'                => '',
        'params'                  => array(
            array(
                'heading'          => __('Title','auxin-shop' ),
                'description'      => __('Recent Vendors title, leave it empty if you don`t need title.', 'auxin-shop'),
                'param_name'       => 'title',
                'type'             => 'textfield',
                'value'            => '',
                'holder'           => 'textfield',
                'class'            => 'title',
                'admin_label'      => false,
                'dependency'       => '',
                'weight'           => '',
                'group'            => '',
                'edit_field_class' => ''
            ),
            array(
                'heading'           => __( 'Number of columns', 'auxin-shop'),
                'description'       => '',
                'param_name'        => 'desktop_cnum',
                'type'              => 'dropdown',
                'def_value'         => '4',
                'holder'            => '',
                'class'             => 'num',
                'value'             => array(
                    '2' => '2', '3' => '3',
                    '4'  => '4', '5' => '5'
                ),
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => 'Layout',
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __( 'Number of rows', 'auxin-shop'),
                'description'       => '',
                'param_name'        => 'rows',
                'type'              => 'dropdown',
                'def_value'         => '2',
                'holder'            => '',
                'class'             => 'num',
                'value'             => array(
                    '2' => '2', '3' => '3',
                    '4'  => '4', '5' => '5'
                ),
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => 'Layout',
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __( 'Number of columns in tablet size', 'auxin-shop'),
                'description'       => '',
                'param_name'        => 'tablet_cnum',
                'type'              => 'dropdown',
                'def_value'         => 'inherit',
                'holder'            => '',
                'class'             => 'num',
                'value'             => array(
                    'inherit' => 'Inherited from larger',
                    '1'  => '1', '2' => '2', '3' => '3',
                    '4'  => '4', '5' => '5',
                ),
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => 'Layout',
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __( 'Number of columns in phone size', 'auxin-shop' ),
                'description'       => '',
                'param_name'        => 'phone_cnum',
                'type'              => 'dropdown',
                'def_value'         => '1',
                'holder'            => '',
                'class'             => 'num',
                'value'             => array(
                    '1' => '1', '2' => '2', '3' => '3'
                ),
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => 'Layout',
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __( 'Extra class name', 'auxin-shop' ),
                'description'       => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'auxin-shop' ),
                'param_name'        => 'extra_classes',
                'type'              => 'textfield',
                'value'             => '',
                'def_value'         => '',
                'holder'            => '',
                'class'             => 'extra_classes',
                'admin_label'       => false,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '',
                'edit_field_class'  => ''
            )
        )
    );

    return $master_array;
}

add_filter( 'auxin_master_array_shortcodes', 'auxin_get_latest_vendors_master_array', 10, 1 );

function auxin_widget_latest_vendors_callback( $atts, $shortcode_content = null ){

    // Defining default attributes
    $default_atts = array(
        'title'              => '',    // header title (required)
        'display_title'      => '',
        'display_badge'      => '',
        'display_avatar'     => '',
        'display_address'    => '',
        'display_phone'      => '',
        'display_rating'     => '',
        'display_url'        => '',
        'display_pagination' => false,
        'desktop_cnum'       => 4,
        'tablet_cnum'        => 'inherit',
        'phone_cnum'         => '1',
        'rows'               => '2',
        'extra_classes'      => '',
        'base'               => 'aux_latest_vendors',
        'base_class'         => 'aux-widget-latest-vendors'
    );

    $result = auxin_get_widget_scafold( $atts, $default_atts, $shortcode_content );
    extract( $result['parsed_atts'] );
    
    $tablet_cnum = ('inherit' == $tablet_cnum  ) ? $desktop_cnum : $tablet_cnum ;
    $phone_cnum  = ('inherit' == $phone_cnum  )  ? $tablet_cnum : $phone_cnum;

    $wrapper_classname = 'aux-row aux-de-col' . $desktop_cnum . ' aux-tb-col' . $tablet_cnum . ' aux-mb-col' . $phone_cnum;
    $default_banner_url = DOKAN_PLUGIN_ASSEST . '/images/default-store-banner.png'; 
    $output    = '';

    ob_start();

    // widget header ------------------------------
    echo $result['widget_header'];
    echo $result['widget_title'];

    $column_media_width = auxin_get_content_column_width( $desktop_cnum, 15 );
    $image_size = array( 'width' => $column_media_width, 'height' => $column_media_width * 0.56 );

    $user_args = array(
        'role'         => 'seller',
        'meta_query'   => array(
            array(
                'key'   => 'dokan_enable_selling',
                'value' => 'yes',
            )
        ),
    );

    $users = new WP_User_Query( $user_args );
    $users =  $users->get_results();

    foreach ( $users as $key => $user ) {

        $user_info   = dokan_get_store_info( $user->ID );
        $banner      = $user_info['banner'] ? auxin_get_the_responsive_attachment( $user_info['banner'], array( 'size' => $image_size) ): sprintf( '<img class = "aux-vendor-banner-img" src = "%s" width = "%s" height = "%s"/>', $default_banner_url, $image_size['width'], $image_size['height'] ) ;
        $name        = isset( $user_info['store_name'] ) ? esc_html( $user_info['store_name'] ) :  __( 'N/A', 'auxin-shop' );
        $address     = dokan_get_seller_short_address( $user->ID );
        $is_featured = get_user_meta( $user->ID, 'dokan_feature_seller', true );


        $user_rating = dokan_get_seller_rating( $user->ID );
        $rating        = is_numeric( $user_rating['rating'] ) ? $user_rating['rating'] : 0 ;

        $rating_html  = '<div class="aux-rating-box aux-star-rating">';
        $rating_html .= '<span class="aux-star-rating-avg" style="width: ' . ( $rating / 5 ) * 100 .'%">';
        $rating_html .= '</span>';
        $rating_html .= '</div>';

        $user_url  = dokan_get_store_url( $user->ID );

        $output .= '<div class="aux-vendor-col aux-col">';
            $output .= '<div class="aux-vendor-box">';
            $output .= auxin_is_true( $is_featured ) && auxin_is_true( $display_badge )  ? sprintf( '<span class="aux-vendor-featured">%s</span>', __( 'Featured', 'auxin-shop' ) ) : '';
            $output .= '<div class="aux-vendor-image">'; 
                $output .= sprintf( '<div class="aux-vendor-banner">%s</div>', $banner );
                $output .= auxin_is_true( $display_avatar ) ? sprintf( '<div class="aux-vendor-avatar">%s</div>', get_avatar( $user->ID, 90 ) ) : '';
            $output .= '</div>';
            $output .= '<div class="aux-vendor-info">';
                $output .= auxin_is_true( $display_title ) ? sprintf('<h3 class="aux-vendor-name"><a href="%s">%s</a></h3>' , $user_url, $name ) : '';
                $output .= auxin_is_true( $display_address ) ? sprintf('<div class="aux-vendor-address">%s</div>' , $address ) : '' ;
                $output .= auxin_is_true( $display_phone ) ? sprintf('<div class="aux-vendor-phone">%s</div>' , $user_info['phone'] ) : '';
            $output .= '</div>';

            if ( auxin_is_true( $display_rating ) ||  auxin_is_true( $display_url) ){
                $output .= '<div class="aux-vendor-footer">';
                    $output .= auxin_is_true( $display_rating ) ? $rating_html : '';
                    $output .= auxin_is_true( $display_url) ? sprintf( '<a href="%s" class="aux-vendor-url">%s<i class="auxicon-chevron-right-2"></i></a>', $user_url ,  __( 'View Store', 'auxin-shop' ) ) : '';
                $output .= '</div>';
            }

            $output .= '</div>';
        $output .= '</div>';
    }

    echo sprintf('<div class="aux-latest-vendors-wrapper %s">%s</div>', $wrapper_classname, $output);
    // widget footer ------------------------------
    
    echo $result['widget_footer'];

    return ob_get_clean();
}
