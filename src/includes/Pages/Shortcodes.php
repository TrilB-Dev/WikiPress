<?php
/**
 * Shortcodes class for defining and registering built-in WikiPress shortcodes.
 *
 * @package WikiPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace WikiPress\Includes\Pages;

use WikiPress\Includes\Core\Shortcodes as ShortcodeCore;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcodes class for defining and registering built-in WikiPress shortcodes.
 *
 * @package WikiPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
final class Shortcodes {
    /**
     * Register all built-in WikiPress shortcodes.
     *
     * @return void
     * @since 1.0.0
     */
    public static function register(): void {
        foreach ( self::definitions() as $tag => $config ) {
            ShortcodeCore::create( (string) $tag, (array) $config );
        }
    }

    /**
     * Return the built-in WikiPress shortcode definitions.
     *
     * @return array<string, array<string, mixed>>
     * @since 1.0.0
     */
    public static function definitions(): array {
        $definitions = array(
            'wikipress_alert' => array(
                'callback'   => array( self::class, 'render_alert' ),
                'attributes' => array(
                    'type'  => 'info',
                    'title' => '',
                    'class' => '',
                ),
                'description' => __( 'Display a styled alert box', 'wikipress' ),
                'category'    => 'content',
                'enclosing'   => true,
                'tinymce'     => false,
            ),
            'wikipress_button' => array(
                'callback'   => array( self::class, 'render_button' ),
                'attributes' => array(
                    'label'  => __( 'Learn more', 'wikipress' ),
                    'url'    => '#',
                    'style'  => 'primary',
                    'target' => '',
                    'rel'    => '',
                    'class'  => '',
                ),
                'description' => __( 'Render a styled button link', 'wikipress' ),
                'category'    => 'content',
                'enclosing'   => false,
                'tinymce'     => false,
            ),
            'wikipress_badge' => array(
                'callback'   => array( self::class, 'render_badge' ),
                'attributes' => array(
                    'text'  => __( 'New', 'wikipress' ),
                    'style' => 'primary',
                    'class' => '',
                ),
                'description' => __( 'Render a simple badge label', 'wikipress' ),
                'category'    => 'content',
                'enclosing'   => false,
                'tinymce'     => false,
            ),
        );

        return apply_filters( 'wikipress_shortcode_definitions', $definitions );
    }

    /**
     * Render a styled alert shortcode.
     *
     * @param array<string, mixed> $attributes Shortcode attributes.
     * @param string|null $content Enclosed content.
     * @param string $tag Shortcode tag.
     * @return string
     * @since 1.0.0
     */
    public static function render_alert( array $attributes = array(), $content = null, string $tag = '' ): string {
        $attributes = shortcode_atts(
            array(
                'type'  => 'info',
                'title' => '',
                'class' => '',
            ),
            $attributes,
            $tag
        );

        $type    = in_array( strtolower( (string) $attributes['type'] ), array( 'success', 'warning', 'error', 'info' ), true ) ? strtolower( (string) $attributes['type'] ) : 'info';
        $title   = trim( (string) $attributes['title'] );
        $message = trim( (string) ( is_string( $content ) ? $content : '' ) );
        $class   = trim( (string) $attributes['class'] );

        $output = '<div class="wikipress-alert wikipress-alert-' . esc_attr( $type );
        if ( '' !== $class ) {
            $output .= ' ' . esc_attr( $class );
        }
        $output .= '" role="alert">';

        if ( '' !== $title ) {
            $output .= '<strong>' . esc_html( $title ) . '</strong> ';
        }

        $output .= wp_kses_post( $message );
        $output .= '</div>';

        return $output;
    }

    /**
     * Render a styled button shortcode.
     *
     * @param array<string, mixed> $attributes Shortcode attributes.
     * @param string|null $content Enclosed content.
     * @param string $tag Shortcode tag.
     * @return string
     * @since 1.0.0
     */
    public static function render_button( array $attributes = array(), $content = null, string $tag = '' ): string {
        $attributes = shortcode_atts(
            array(
                'label'  => __( 'Learn more', 'wikipress' ),
                'url'    => '#',
                'style'  => 'primary',
                'target' => '',
                'rel'    => '',
                'class'  => '',
            ),
            $attributes,
            $tag
        );

        $link_text = trim( (string) $attributes['label'] );
        $url       = esc_url( (string) $attributes['url'] );
        $style     = in_array( strtolower( (string) $attributes['style'] ), array( 'primary', 'secondary', 'success', 'danger' ), true ) ? strtolower( (string) $attributes['style'] ) : 'primary';
        $target    = trim( (string) $attributes['target'] );
        $rel       = trim( (string) $attributes['rel'] );
        $class     = trim( (string) $attributes['class'] );

        $extra = '';
        if ( '' !== $target ) {
            $extra .= ' target="' . esc_attr( $target ) . '"';
        }
        if ( '' !== $rel ) {
            $extra .= ' rel="' . esc_attr( $rel ) . '"';
        }
        if ( '' !== $class ) {
            $extra .= ' class="' . esc_attr( $class ) . '"';
        }

        return '<a href="' . $url . '" class="wikipress-button wikipress-button-' . esc_attr( $style ) . '"' . $extra . '>' . esc_html( $link_text ) . '</a>';
    }

    /**
     * Render a badge shortcode.
     *
     * @param array<string, mixed> $attributes Shortcode attributes.
     * @param string|null $content Enclosed content.
     * @param string $tag Shortcode tag.
     * @return string
     * @since 1.0.0
     */
    public static function render_badge( array $attributes = array(), $content = null, string $tag = '' ): string {
        $attributes = shortcode_atts(
            array(
                'text'  => __( 'New', 'wikipress' ),
                'style' => 'primary',
                'class' => '',
            ),
            $attributes,
            $tag
        );

        $text  = trim( (string) $attributes['text'] );
        $style = in_array( strtolower( (string) $attributes['style'] ), array( 'primary', 'secondary', 'success', 'warning', 'danger' ), true ) ? strtolower( (string) $attributes['style'] ) : 'primary';
        $class = trim( (string) $attributes['class'] );

        $output = '<span class="wikipress-badge wikipress-badge-' . esc_attr( $style );
        if ( '' !== $class ) {
            $output .= ' ' . esc_attr( $class );
        }
        $output .= '">' . esc_html( $text ) . '</span>';

        return $output;
    }
}

