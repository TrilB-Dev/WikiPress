<?php
/**
 * Admin functions for native Mod management.
 *
 * @package WikiPress
 */

namespace WikiPress\Includes\Functions\Admin;

use WikiPress\Includes\Core\Schema;
use WikiPress\Includes\Functions\Helpers\AlertHelper;
use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Helpers\LoggerHelper;
use WikiPress\Includes\Functions\Helpers\PermalinkHelper;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use WikiPress\Includes\Functions\Helpers\RequestHelper;
use WikiPress\Includes\Functions\Helpers\DBHelper;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\WPMediaHelper;
use WikiPress\Includes\ModManagement\GroupsManager;
use WikiPress\Includes\ModManagement\ModsManager;
use WikiPress\Includes\ModManagement\ChangelogManager;
use WikiPress\Includes\ModManagement\FileManager;
use WikiPress\Includes\Plugins\FontAwesome\Includes\IconPicker;
use WikiPress\Includes\Plugins\FontAwesome\Includes\Functions\Helpers\FAHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class FunctionsWiki {

	/**
	 * Register the WordPress admin-post actions used by the settings forms.
	 *
	 * @param LoaderHelper $loader Loader instance used to register hooks.
	 * @return void
     * @since 1.0.0
	 */
	public function register_admin_post_hooks( LoaderHelper $loader ): void {
		$loader->register_component(
			$this,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_save_wiki',
					'callback' => 'save_wiki',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_save_wiki_page',
					'callback' => 'save_wiki_page',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_save_tags',
					'callback' => 'save_tags',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_save_categories',
					'callback' => 'save_categories',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_delete_wiki',
					'callback' => 'delete_wiki',
				),
                array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_delete_wiki_page',
					'callback' => 'delete_wiki_page',
				),
                array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_delete_tags',
					'callback' => 'delete_tags',
				),
                array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_delete_categories',
					'callback' => 'delete_categories',
				)
			)
		)->run();
	}
    /**
     * Save a WikiPress form submission.
     *
     * @return void
     * @since 1.0.0
     */
	public function save(): void {
		$action = RequestHelper::key( $_POST, 'action', '' );
		if ( '' === $action ) {
			return;
		}

		if ( in_array( $action, array( 'wikipress_save_wiki' ), true ) ) {
			$this->save_wiki();
		}
		if ( in_array( $action, array( 'wikipress_save_wiki_page' ), true ) ) {
			$this->save_wiki_page();
		}
		if ( 'wikipress_save_tags' === $action ) {
			$this->save_tags();
		}
		if ( 'wikipress_save_categories' === $action ) {
			$this->save_categories();
		}
		if ( 'wikipress_delete_wiki' === $action ) {
			$this->delete_wiki();
		}
		if ( 'wikipress_delete_wiki_page' === $action ) {
			$this->delete_wiki_page();
		}
		if ( 'wikipress_delete_tags' === $action ) {
			$this->delete_tags();
		}
		if ( 'wikipress_delete_categories' === $action ) {
			$this->delete_categories();
		}
	}
	/**
	 * Validate a save nonce for the current request.
	 *
	 * @param string $action Nonce action.
	 * @return void
     * @since 1.0.0
	 */
	private function validate_save_nonce( string $action, ?string $query_arg = null ): void {
		$nonce_name = $query_arg ?? ( $action . '_nonce' );
		check_admin_referer( $action, $nonce_name );
	}
    /**
     * Save a custom WikiPress wiki.
     *
     * @return void
     * @since 1.0.0
     */
    public function save_wiki(): void {
        if ( ! current_user_can( 'wikipress_manager_wiki_edit' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to manage WikiPress wikis.', 'wikipress' ) );
        }

        check_admin_referer( 'wikipress_save_wiki', 'wikipress_wiki_nonce' );

        $wiki_data = isset( $_POST['wikipress_wiki'] ) && is_array( $_POST['wikipress_wiki'] ) ? wp_unslash( $_POST['wikipress_wiki'] ) : array();
        $wiki_id   = absint( $_POST['wikipress_wiki_id'] ?? 0 );
        $title     = isset( $wiki_data['title'] ) ? sanitize_text_field( wp_unslash( $wiki_data['title'] ) ) : '';
        $slug      = isset( $wiki_data['slug'] ) ? sanitize_title( wp_unslash( $wiki_data['slug'] ) ) : '';
        $excerpt   = isset( $wiki_data['excerpt'] ) ? wp_kses_post( wp_unslash( $wiki_data['excerpt'] ) ) : '';

        if ( '' === $title ) {
            $error_redirect = add_query_arg( 'wikipress_notice', 'wiki_missing_title', admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis' ) );
            wp_safe_redirect( $error_redirect );
            exit;
        }

        $postarr = array(
            'ID'           => $wiki_id > 0 ? $wiki_id : 0,
            'post_type'    => 'wikipress_wiki',
            'post_title'   => $title,
            'post_name'    => '' === $slug ? sanitize_title( $title ) : $slug,
            'post_excerpt' => $excerpt,
            'post_status'  => 'publish',
            'post_content' => '',
        );

        $saved_id = wp_insert_post( $postarr, true );
        if ( is_wp_error( $saved_id ) ) {
            $error_redirect = add_query_arg( 'wikipress_notice', 'wiki_error', admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis' ) );
            wp_safe_redirect( $error_redirect );
            exit;
        }

        $redirect = admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis' );
        $redirect = add_query_arg( 'wiki_id', absint( $saved_id ), $redirect );
        $redirect = add_query_arg( 'wikipress_notice', $wiki_id > 0 ? 'wiki_updated' : 'wiki_saved', $redirect );
        wp_safe_redirect( $redirect );
        exit;
    }
    /**
     * Delete a custom WikiPress wiki.
     *
     * @return void
     * @since 1.0.0
     */
    public function delete_wiki(): void {
        if ( ! current_user_can( 'wikipress_manager_wiki_edit' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to manage WikiPress wikis.', 'wikipress' ) );
        }

        check_admin_referer( 'wikipress_delete_wiki', 'wikipress_wiki_nonce' );

        $wiki_id = absint( $_POST['wikipress_wiki_id'] ?? 0 );
        if ( $wiki_id <= 0 ) {
            $redirect = add_query_arg( 'wikipress_notice', 'wiki_missing_id', admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis' ) );
            wp_safe_redirect( $redirect );
            exit;
        }

        $child_pages = get_posts(
            array(
                'post_type'      => 'wikipress_wiki_page',
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'post_parent'    => $wiki_id,
                'fields'         => 'ids',
                'no_found_rows'  => true,
            )
        );

        foreach ( $child_pages as $child_page_id ) {
            wp_delete_post( absint( $child_page_id ), true );
        }

        wp_delete_post( $wiki_id, true );

        $redirect = add_query_arg( 'wikipress_notice', 'wiki_deleted', admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis' ) );
        wp_safe_redirect( $redirect );
        exit;
    }
    /**
     * Save a custom WikiPress wiki page.
     *
     * @return void
     * @since 1.0.0
     */
    public function save_wiki_page(): void {
        if ( ! current_user_can( 'wikipress_manager_wiki_edit' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to manage WikiPress wikis.', 'wikipress' ) );
        }

        check_admin_referer( 'wikipress_save_wiki_page', 'wikipress_wiki_nonce' );

        $page_data = isset( $_POST['wikipress_wiki_page'] ) && is_array( $_POST['wikipress_wiki_page'] ) ? wp_unslash( $_POST['wikipress_wiki_page'] ) : array();
        $wiki_id    = absint( $_POST['wikipress_wiki_id'] ?? ( $page_data['wiki_id'] ?? 0 ) );
        $page_id    = absint( $_POST['wikipress_wiki_page_id'] ?? 0 );
        $title      = isset( $page_data['title'] ) ? sanitize_text_field( wp_unslash( $page_data['title'] ) ) : '';
        $slug       = isset( $page_data['slug'] ) ? sanitize_title( wp_unslash( $page_data['slug'] ) ) : '';
        $excerpt    = isset( $page_data['excerpt'] ) ? wp_kses_post( wp_unslash( $page_data['excerpt'] ) ) : '';
        $content    = isset( $page_data['content'] ) ? wp_kses_post( wp_unslash( $page_data['content'] ) ) : '';

        if ( $wiki_id <= 0 || ! get_post( $wiki_id ) ) {
            $redirect = add_query_arg( 'wikipress_notice', 'wiki_page_missing_wiki', admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis' ) );
            wp_safe_redirect( $redirect );
            exit;
        }

        if ( '' === $title ) {
            $redirect = add_query_arg( 'wikipress_notice', 'wiki_page_missing_title', admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis&wiki_id=' . $wiki_id ) );
            wp_safe_redirect( $redirect );
            exit;
        }

        $postarr = array(
            'ID'           => $page_id > 0 ? $page_id : 0,
            'post_type'    => 'wikipress_wiki_page',
            'post_parent'  => $wiki_id,
            'post_title'   => $title,
            'post_name'    => '' === $slug ? sanitize_title( $title ) : $slug,
            'post_excerpt' => $excerpt,
            'post_content' => $content,
            'post_status'  => 'publish',
        );

        $saved_id = wp_insert_post( $postarr, true );
        if ( is_wp_error( $saved_id ) ) {
            $redirect = add_query_arg( 'wikipress_notice', 'wiki_page_error', admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis&wiki_id=' . $wiki_id ) );
            wp_safe_redirect( $redirect );
            exit;
        }

        $redirect = add_query_arg( 'wikipress_notice', $page_id > 0 ? 'wiki_page_updated' : 'wiki_page_saved', admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis&wiki_id=' . $wiki_id ) );
        wp_safe_redirect( $redirect );
        exit;
    }
    /**
     * Delete a custom WikiPress wiki page.
     *
     * @return void
     * @since 1.0.0
     */
    public function delete_wiki_page(): void {
        if ( ! current_user_can( 'wikipress_manager_wiki_edit' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to manage WikiPress wikis.', 'wikipress' ) );
        }

        check_admin_referer( 'wikipress_delete_wiki_page', 'wikipress_wiki_nonce' );

        $page_id = absint( $_POST['wikipress_wiki_page_id'] ?? 0 );
        if ( $page_id <= 0 ) {
            $redirect = add_query_arg( 'wikipress_notice', 'wiki_page_missing_id', admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis' ) );
            wp_safe_redirect( $redirect );
            exit;
        }

        $wiki_id = absint( get_post_field( 'post_parent', $page_id ) ?: $_POST['wikipress_wiki_id'] ?? 0 );
        wp_delete_post( $page_id, true );

        $redirect = add_query_arg( 'wikipress_notice', 'wiki_page_deleted', admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis' . ( $wiki_id > 0 ? '&wiki_id=' . $wiki_id : '' ) ) );
        wp_safe_redirect( $redirect );
        exit;
    }
    /**
     * Save a custom WikiPress category.
     *
     * @return void
     * @since 1.0.0
     */
    public function save_categories(): void {
        if ( ! current_user_can( 'wikipress_manager_categories_edit' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to manage WikiPress categories.', 'wikipress' ) );
        }

        check_admin_referer( 'wikipress_save_categories', 'wikipress_category_nonce' );

        // Implement the logic to save categories here.
    }
    /**
     * Delete a custom WikiPress category.
     *
     * @return void
     * @since 1.0.0
     */
    public function delete_categories(): void {
        if ( ! current_user_can( 'wikipress_manager_categories_edit' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to manage WikiPress categories.', 'wikipress' ) );
        }

        check_admin_referer( 'wikipress_delete_category', 'wikipress_category_nonce' );

        // Implement the logic to delete a category here.
    }
    /**
     * Handle a tag save request.
     *
     * @return void
     * @since 1.0.0
     */
    public function save_tags(): void {
        if ( ! current_user_can( 'wikipress_manager_tags_edit' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to manage WikiPress tags.', 'wikipress' ) );
        }

        check_admin_referer( 'wikipress_save_tags' );
        $this->redirect_mod_action_result( 'groups', __( 'Tags saved successfully.', 'wikipress' ), true );
    }
    /**
     * Handle a tag delete request.
     *
     * @return void
     * @since 1.0.0
     */
    public function delete_tags(): void {
        if ( ! current_user_can( 'wikipress_manager_tags_edit' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to manage WikiPress tags.', 'wikipress' ) );
        }

        check_admin_referer( 'wikipress_delete_tags' );
        $this->redirect_mod_action_result( 'groups', __( 'Tags deleted successfully.', 'wikipress' ), true );
    }

}

