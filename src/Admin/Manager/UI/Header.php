<?php
/**
 * Header UI component for WikiPress admin pages.
 *
 * @package WikiPress
 * @subpackage Admin\Manager\UI
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\UI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Header {
	/**
	 * Renders the header for WikiPress admin pages.
	 *
	 * @return void
	 */
	public static function render(): void {
		$links = [
					[ 
					'label' => __( 'Documentation', 'wikipress' ), 
					'url' => 'https://trilb.dev/collection/web-extension/wordpress/wikipress' 
					],
					[ 
					'label' => __( 'Community', 'wikipress' ), 
					'url' => 'https://trilb.dev/community' 
					],
					[ 
					'label' => __( 'Extensions', 'wikipress' ), 
					'url' => 'https://trilb.dev/extensions' 
					],
					[ 
					'label' => __( 'Support', 'wikipress' ), 
					'url' => 'https://trilb.dev/support' 
					],
					[ 
					'label' => __( 'Roadmap', 'wikipress' ), 
					'url' => 'https://trilb.dev/roadmap' 
					],
					[ 
					'label' => __( 'Account', 'wikipress' ), 
					'url' => 'https://trilb.dev/account' 
					],
			];
		?>
		<header class="wikipress-header border-bottom">
			<nav class="navbar navbar-expand-lg" aria-label="<?php esc_attr_e( 'WikiPress header navigation', 'wikipress' ); ?>">
				<div class="container-fluid wikipress-shell px-3 px-lg-4">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress' ) ); ?>">
						<img class="navbar-brand d-flex align-items-center gap-2" src="<?php echo esc_url( WIKIPRESS_ASSETS_URL . '/Images/Logo/WikiPress-Logo-full-length.svg' ); ?>" alt="" />
					</a>
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#wikipress-header-menu" aria-controls="wikipress-header-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle header navigation', 'wikipress' ); ?>">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="wikipress-header-menu">
						<ul class="navbar-nav ms-auto align-items-lg-start gap-lg-1">
							<?php foreach ( $links as $link ) : ?>
								<li class="nav-item"><a class="nav-link" href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $link['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</nav>
		</header>
		<?php
	}
}
