<?php
/**
 * Elementor section custom controls
 *
 * @package xts
 */

use Elementor\Controls_Stack;
use Elementor\Plugin;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_add_section_class_if_content_width' ) ) {
	/**
	 * Add class to section is content with is set.
	 *
	 * @since 1.0.0
	 *
	 * @param object $widget Element.
	 */
	function woodmart_add_section_class_if_content_width( $widget ) {
		$settings = $widget->get_settings_for_display();

		if ( isset( $settings['content_width'] ) && isset( $settings['content_width']['size'] ) && ! $settings['content_width']['size'] ) {
			$widget->add_render_attribute( '_wrapper', 'class', 'wd-negative-gap' );
		}
	}
}
if ( ! function_exists( 'woodmart_section_negative_gap' ) ) {
	/**
	 * Section negative gap fix.
	 *
	 * @since 1.0.0
	 */
	function woodmart_section_negative_gap() {
		if ( 'enabled' === woodmart_get_opt( 'negative_gap', 'enabled' ) ) {
			add_action( 'elementor/frontend/section/before_render', 'woodmart_add_section_class_if_content_width', 10 );
		}
	}

	add_action( 'init', 'woodmart_section_negative_gap', 100 );
}

if ( ! function_exists( 'woodmart_add_section_full_width_control' ) ) {
	/**
	 * Section full width option
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function woodmart_add_section_full_width_control( $element ) {
		$element->start_controls_section(
			'wd_extra_layout',
			[
				'label' => esc_html__( '[XTemos] Layout', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			]
		);

		$element->add_control(
			'wd_section_stretch',
			[
				'label'        => esc_html__( 'Section stretch CSS', 'woodmart' ),
				'description'  => esc_html__( 'Enable this option instead of native Elementor\'s one to stretch section with CSS and not with JS.', 'woodmart' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => '',
				'options'      => array(
					''                => esc_html__( 'Disabled', 'woodmart' ),
					'stretch'         => esc_html__( 'Stretch section', 'woodmart' ),
					'stretch-content' => esc_html__( 'Stretch section and content', 'woodmart' ),
				),
				'render_type'  => 'template',
				'prefix_class' => 'wd-section-',
			]
		);

		$element->end_controls_section();
	}

	add_action( 'elementor/element/section/section_layout/after_section_end', 'woodmart_add_section_full_width_control' );
}

if ( ! function_exists( 'woodmart_override_section_margin_control' ) ) {
	/**
	 * Add custom fonts to theme group
	 *
	 * @since 1.0.0
	 *
	 * @param Controls_Stack $control_stack The control.
	 */
	function woodmart_override_section_margin_control( $control_stack ) {
		$control = Plugin::instance()->controls_manager->get_control_from_stack( $control_stack->get_unique_name(), 'margin' );

		if ( is_wp_error( $control ) ) {
			return;
		}

		$control['allowed_dimensions'] = [ 'top', 'right', 'bottom', 'left' ];
		$control['placeholder']        = [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		];
		$control['selectors']          = [
			'{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		];

		$control_stack->update_responsive_control( 'margin', $control );
	}

	add_action( 'elementor/element/section/section_advanced/before_section_end', 'woodmart_override_section_margin_control', 10, 2 );
}

if ( ! function_exists( 'woodmart_section_before_render' ) ) {
	/**
	 * Section before render.
	 *
	 * @since 1.0.0
	 *
	 * @param object $widget Element.
	 */
	function woodmart_section_before_render( $widget ) {
		$settings = $widget->get_settings_for_display();

		if ( isset( $settings['wd_animation'] ) && $settings['wd_animation'] ) {
			woodmart_enqueue_inline_style( 'animations' );
			woodmart_enqueue_js_script( 'animations' );
			woodmart_enqueue_js_library( 'waypoints' );
		}
	}

	add_action( 'elementor/frontend/section/before_render', 'woodmart_section_before_render', 10 );
}

if ( ! function_exists( 'woodmart_add_section_custom_controls' ) ) {
	/**
	 * Column section controls.
	 *
	 * @since 1.0.0
	 *
	 * @param Controls_Stack $element The control.
	 */
	function woodmart_add_section_custom_controls( $element ) {
		$element->start_controls_section(
			'wd_extra_advanced',
			[
				'label' => esc_html__( '[XTemos] Extra', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		/**
		 * Animations
		 */
		woodmart_get_animation_map( $element );

		$element->end_controls_section();
	}

	add_action( 'elementor/element/section/section_advanced/after_section_end', 'woodmart_add_section_custom_controls' );
}
