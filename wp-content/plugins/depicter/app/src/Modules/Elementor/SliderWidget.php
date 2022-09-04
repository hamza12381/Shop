<?php
namespace Depicter\Modules\Elementor;

use Elementor\Plugin;
use Depicter\Document\CSS\Selector;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor 'Slider' widget.
 *
 * Elementor widget that displays an 'Slider' with lightbox.
 *
 * @since 1.0.0
 */
class SliderWidget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve 'Slider' widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'depicter_slider';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve 'Slider' widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Depicter Slider', 'depicter' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Slider widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-slider depicter-badge';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_categories() {
		return ['basic'];
	}

	/**
	 * load dependent styles
	 *
	 * @return array
	 */
	public function get_style_depends() {
		$styles = \Depicter::front()->assets()->registerStyles();
		return array_keys( $styles );
	}

	/**
	 * load dependent scripts
	 *
	 * @return array
	 */
	public function get_script_depends() {
		$scripts = \Depicter::front()->assets()->registerScripts();
		return array_keys( $scripts );
	}

	public function getSlidersList() {
		$list = [
			0 => __( 'Select Slider', 'depicter' )
		];
		$documents = \Depicter::documentRepository()->getList();
		foreach( $documents as $document ) {
			$list[ $document['id'] ] = "[#{$document['id']}]: " . $document['name'];
		}
		return $list;
	}

	/**
	 * Register 'Slider' widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		/*-----------------------------------------------------------------------------------*/
		/*  slider_section
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'slider_section',
			array(
				'label'      => __('Slider', 'depicter' ),
			)
		);

		$this->add_control(
			'slider_id',
			[
				'label'     => __('Select Slider','depicter' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->getSlidersList(),
				'default'   => 0
			]
		);

		$documents = \Depicter::documentRepository()->getList();
		foreach( $documents as $key => $document ) {
			$disabled = 'disabled';
			$markup = '';
			$published = __( 'Published', 'depicter' );
			$noticeText = '';

			if ( \Depicter::documentRepository()->isNotPublished( $document['id'], [ 'status' => 'publish' ] ) ) {
				$noticeText = __( 'This slider is not published yet and will not be visible to the visitors. Open the editor and publish now. This notice is only visible to you.', 'depicter' );
			} elseif ( $document['status'] == 'draft' ) {
				$noticeText = __( 'This slider has unpublished changes. Publish the changes to see the final result.', 'depicter' );
			}

			if ( $document['status'] == 'draft' ) {
				$disabled = '';
				$published = __( 'Publish Slider', 'depicter' );
				$markup .= '<div class="notice-wrapper" style="display: flex; align-items: flex-start;">';
				$markup .= '<span class="depicter-notice-icon" style="color: #F7BA19; padding: 2px 7px; border: 2px solid #F7BA19; border-radius: 50%;margin-right: 5px; font-size: 14px;">!</span><span style="font-family: roboto; font-size: 12px; line-height: 14px;color: #E0E1E5;">' . esc_html( $noticeText ) . '</span>';
				$markup .= '</div>';
			}

			$markup .= '<div class="btns-wrapper" style="display: flex; justify-content: space-between;margin-top: 15px;">';
			$markup .= '<button class="depicter-edit-slider elementor-button" style="background-color: #4499C0; color: #fff; padding: 5px 10px; width:100%; margin-right: 5px;">' . esc_html__( 'Edit Slider', 'depicter' ) . '</button>';
			$markup .= '<button class="depicter-publish-slider elementor-button elementor-button-success" style="padding: 5px 10px; width:100%; margin-left: 5px;" ' . esc_attr( $disabled ) . '><span class="btn-label">' . esc_html( $published ) . '</span><span class="elementor-state-icon" style="display: none;"><i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i></span></button>';
			$markup .= '</div>';
			
			$this->add_control(
				'slider_control_buttons_' . $key,
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => $markup,
					'condition' => [
						'slider_id' => $document['id']
					]
				]
			);
		}
		
		$this->end_controls_section();
	}

	/**
	 * Render image box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings   = $this->get_settings_for_display();

		if ( $settings['slider_id'] ) {
			$args = [];
			if ( Plugin::$instance->editor->is_edit_mode() ) {
				$args = [
					'useCache' => false,
					'status' => ['publish', 'draft'],
				];

				$document = \Depicter::documentRepository()->findById( $settings['slider_id'] )->toArray();

				if ( !empty( $document ) && $document['status'] == 'draft' ) {
					$this->printNoticeScripts();
					$this->printNoticeStyles();
					echo '<div class="depicter-notice-wrapper">';
					echo '<span class="notice-icon">!</span>';

					if ( \Depicter::documentRepository()->isNotPublished( $settings['slider_id'], [ 'status' => 'publish' ] ) ) {
						echo '<span>' . esc_html__('This slider is not published yet and will not be visible to the visitors.', 'depicter' ) . '<br>';
					} else {
						echo '<span>' . esc_html__('This slider has unpublished changes. Publish the changes to see the final result.', 'depicter' ) . '<br>';
					}

					echo '<a href="' . esc_url( \Depicter::editor()->getEditUrl( $settings['slider_id'] ) ) . '" target="_blank">' . esc_html__( 'Open the editor', 'depicter' ) . '</a> ' . esc_html__( 'and publish now.', 'depicter' ) . ' '; 
					echo esc_html__( 'This notice is only visible to you.', 'depicter' ) . '</span><span class="close-icon"></span>';
					echo '</div>';
				}
			}
			echo \Depicter::front()->render()->document( $settings['slider_id'], $args );
		}

	}

	/**
	 * Print scripts required just in elementor editor
	 *
	 * @return void
	 */
	protected function printNoticeScripts() {
		echo '<script type="text/javascript">
			jQuery(".depicter-notice-wrapper .close-icon").on( "click", function () {
				jQuery(this).parents(".depicter-notice-wrapper").hide();
			});

			jQuery(".depicter-notice-wrapper a").on( "click", function () {
				window.open(this.href);
			});
		</script>';
	}

	/**
	 * Print required styles just in elementor editor
	 *
	 * @return void
	 */
	protected function printNoticeStyles() {
		echo '<style>';
		echo '.depicter-notice-wrapper{
			position: absolute;
			top: 20px;
			left: 20px;
			display: flex; 
			align-items: flex-start; 
			background-color: #F7BA19;
			border-radius: 5px;
			padding: 10px;
			box-shadow: 5px 10px 30px #00000026;
			max-width: 600px;
			z-index: 1;
		}';

		echo '.depicter-notice-wrapper span {
			color: black;
			font-size: 12px;
			line-height: 17px;
			font-weight: 600;
		}';

		echo '.depicter-notice-wrapper span.notice-icon {
			background-color: #fff; 
			color: #F7BA19; 
			padding: 2px 8px;
			font-size: 14px;
			border-radius: 50%;
			margin-right: 7px;
		}';

		echo '.depicter-notice-wrapper a {
			color: #0A00FF;
			text-decoration: underline;
		}';

		echo '.depicter-notice-wrapper .close-icon {
			width: 10px;
			height: 10px;
			margin-left: 10px;
			cursor: pointer;
		}';

		echo '.depicter-notice-wrapper .close-icon:before, 
			.depicter-notice-wrapper .close-icon:after {
			width: 2px;
			height: 10px;
			background-color: #fff;
			display: block;
			content: " ";
			position: absolute;
		}';

		echo '.depicter-notice-wrapper .close-icon:before {
			transform: rotate(45deg);
		}';
		echo '.depicter-notice-wrapper .close-icon:after {
			transform: rotate(-45deg);
		}';

		echo '</style>';
	}
}
