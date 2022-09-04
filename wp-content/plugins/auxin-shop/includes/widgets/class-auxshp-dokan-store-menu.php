<?php

/**
 * new WordPress Widget format
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */
class AUXSHP_Dokan_Widget_Store_Category extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    public function __construct() {
        $widget_ops = array( 'classname' => 'auxshp-store-menu', 'description' => __( 'Phlox Seller Store Menu', 'auxin-shop' ) );
        parent::__construct( 'auxshp-store-menu', __( 'Phlox: Store Category Menu', 'auxin-shop' ), $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    function widget( $args, $instance ) {

       global $wpdb;

        if ( ! dokan_is_store_page() ) {
            return;
        }

        extract( $args, EXTR_SKIP );

        echo $before_widget;

        $title      = apply_filters( 'widget_title', $instance['title'] );
        $seller_id  = (int) get_query_var( 'author' );

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }


        $categories = get_transient( 'auxshp-store-category-'.$seller_id );

        if ( false === $categories ) {
            $sql = "SELECT t.term_id,t.name, tt.parent FROM $wpdb->terms as t
                    LEFT JOIN $wpdb->term_taxonomy as tt on t.term_id = tt.term_id
                    LEFT JOIN $wpdb->term_relationships AS tr on tt.term_taxonomy_id = tr.term_taxonomy_id
                    LEFT JOIN $wpdb->posts AS p on tr.object_id = p.ID
                    WHERE tt.taxonomy = 'product_cat'
                    AND p.post_type = 'product'
                    AND p.post_status = 'publish'
                    AND p.post_author = $seller_id GROUP BY t.term_id";

            $categories = $wpdb->get_results( $sql );
            set_transient( 'auxshp-store-category-'.$seller_id , $categories );
        }

        $args = array(
            'taxonomy'      => 'product_cat',
            'selected_cats' => ''
        );

        $output = '';

        echo '<ul>';

        foreach ( $categories as $cat => $value ) {
            $store_url = dokan_get_store_url ( $seller_id );
            $url = $store_url . 'section/' . $value->term_id;
            $output .= '<li><a href="' . $url . '">' . $value->name .'</a>';
        }
        
        echo $output;
        echo '</ul>';

        echo $after_widget;
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     **/
    function update( $new_instance, $old_instance ) {

        // update logic goes here
        $updated_instance = $new_instance;
        return $updated_instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => __( 'Category', 'auxin-shop' ),
        ) );

        $title = $instance['title'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'auxin-shop' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }
}
