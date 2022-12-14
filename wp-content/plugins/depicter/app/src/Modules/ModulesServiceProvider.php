<?php

namespace Depicter\Modules;

use Depicter\Modules\Elementor\SliderWidget;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Register widgets.
 */
class ModulesServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		// Nothing to register.
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {

		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) { 
			add_action( 'elementor/widgets/register', [$this, 'registerElementorWidgets'] );
		} else {
			add_action( 'elementor/widgets/widgets_registered', [$this, 'registerElementorWidgets'] );
		}

		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueueEditorScripts'] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_elementor_widget_script'] );

		add_action( 'init', [ $this, 'create_depicter_block_init'] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_gutenberg_widget_scripts'] );

		add_action( 'init', [ $this, 'load_beaver_module' ] );
		add_action( 'init', [ $this, 'load_wpbakery_module' ] );
		add_action( 'init', [ $this, 'load_divi_module' ] );
	}

	/**
	 * Register Elementor widgets.
	 *
	 * @return void
	 */
	public function registerElementorWidgets() {
		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) { 
			\Elementor\Plugin::instance()->widgets_manager->register( new SliderWidget() );
		} else {
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new SliderWidget() );
		}
	}

	/**
	 * load required script for elementor widget in elementor editor env
	 */
	public function load_elementor_widget_script() {
		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			\Depicter::front()->assets()->enqueueScripts('widget');
		}
	}

	public function enqueueEditorScripts() {
		\Depicter::core()->assets()->enqueueScript(
			'depicter-admin',
			\Depicter::core()->assets()->getUrl() . '/resources/scripts/admin/index.js',
			['jquery'],
			true
		);

		wp_localize_script( 'depicter-admin', 'depicterParams',[
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'editorUrl' => \Depicter::editor()->getEditUrl('1'),
			'token' => \Depicter::csrf()->getToken( \Depicter\Security\CSRF::EDITOR_ACTION ),
			'publishedText' => esc_html__( 'Published', 'depicter' )
		]);
	}

	public function load_gutenberg_widget_scripts() {

		$current_screen = get_current_screen();
		if ( !$current_screen->is_block_editor() ) {
			return;
		}

		$list = [
			[
				'id' => "0",
				'name' => __( 'Select Slider', 'depicter' )
			]
		];
		$list = array_merge( $list, \Depicter::app()->documentRepository()->getList(['name','id']) );
		if ( !empty( $list ) ) {
			foreach ( $list as $key => $item ) {
				$list[ $key ]['label'] = $item['name'];
				unset( $list[ $key ]['name'] );

				$list[ $key ]['value'] = $item['id'];
				unset( $list[ $key ]['id'] );
			}
		}
		wp_localize_script( 'wp-block-editor', 'depicterSliders',[
			'list' => $list
		]);

	}

	public function create_depicter_block_init() {
		register_block_type( __DIR__ . '/Gutenberg/build', [
			'render_callback' => [ $this, 'renderGutenbergBlock' ]
		] );
	}

	public function renderGutenbergBlock( $blockAttributes ) {

		if ( !empty( $blockAttributes['id'] ) ) {
			$id = (int) $blockAttributes['id'];
			return depicter( $id, ['echo' => false ] );
		} else {
			echo esc_html__( 'Slider ID required', 'depicter' );
		}

	}


	/**
	 * Load beaver builder module
	 *
	 * @return void
	 */
	public function load_beaver_module() {
		if ( class_exists( '\FLBuilder' ) ) {
			require_once 'Beaver/module.php';
		}
	}

	/**
	 * Load WPBakery module
	 *
	 * @return void
	 */
	public function load_wpbakery_module() {
		require_once 'WPBakery/module.php';
	}

	/**
	 * Load divi module
	 *
	 * @return void
	 */
	public function load_divi_module() {
		if ( class_exists( 'ET_Builder_Plugin' ) ) {
			require_once 'Divi/depicter-divi.php';
		}
	}
}
