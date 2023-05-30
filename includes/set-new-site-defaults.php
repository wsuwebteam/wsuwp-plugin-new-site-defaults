<?php namespace WSUWP\Plugin\New_Site_Defaults;

class Set_New_Site_Defaults {


	public static function on_wp_initialize_site( $new_site, $args ) {

		$site_id    = $new_site->blog_id;
		$site_title = $args['title'];

		// Switch to the new site for configuration.
		switch_to_blog( $site_id );

		// Update the default category name to "General" from "Uncategorized".
		wp_update_term(
			1,
			'category',
			array(
				'name' => 'General',
				'slug' => 'general',
			)
		);

		// Delete the first comment auto-created by WordPress during install.
		wp_delete_comment( 1, true );

		// Update the title of the first post created by WordPress, "Hello World".
		wp_update_post(
			array(
				'ID'           => 1,
				'post_title'   => apply_filters( 'wsuwp_first_title', 'Sample Post' ),
				'post_content' => apply_filters( 'wsuwp_first_post_content', '' ),
				'post_status'  => 'draft',
			)
		);

		// Update the title of the first page created by WordPress, "Sample Page".
		wp_update_post(
			array(
				'ID'           => 2,
				'post_title'   => apply_filters( 'wsuwp_first_page_title', $site_title ),
				'post_content' => apply_filters( 'wsuwp_first_page_content', self::get_homepage_content() ),
			)
		);

		// Add a new "News" page to be used for showing posts.
		$news_post_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => 'News',
				'post_status'  => 'publish',
				'post_content' => 'This is a placeholder page for news items. Editing is not recommended.',
			)
		);

		// Set the "Page on Front" setting by default.
		update_option( 'show_on_front', 'page' );

		// Set the page on front to the home page.
		update_option( 'page_on_front', 2 );

		// Set the page for posts to the News page.
		update_option( 'page_for_posts', (int) $news_post_id );

		// Set "For each post in a feed, include" to "Excerpt".
		update_option( 'rss_use_excerpt', 1 );

		// Set "Discourage search engines from indexing this site" to checked.
		update_option( 'blog_public', 0 );

		// Set default discussion settings.
		update_option( 'default_pingback_flag', 0 );
		update_option( 'default_ping_status', 0 );
		update_option( 'default_comment_status', 0 );
		update_option( 'comment_registration', 1 );
		update_option( 'comment_moderation', 1 );
		update_option( 'comment_previously_approved', 0 );
		update_option( 'show_avatars', 0 );

		// Set block as the default editor.
		update_option( 'classic-editor-replace', 'block' );

		// Disallow editors from swtiching editor type.
		update_option( 'classic-editor-allow-users', 'disallow' );

		// Set permalink structure to day and name.
		update_option( 'permalink_structure', '/%year%/%monthnum%/%day%/%postname%/' );

		// Create main navigation menu.
		$main_menu_id   = wp_create_nav_menu( 'Main Navigation' );
		$footer_menu_id = wp_create_nav_menu( 'Footer Menu' );
		wp_update_nav_menu_item(
			$main_menu_id,
			0,
			array(
				'menu-item-title'   => $site_title,
				'menu-item-classes' => 'home',
				'menu-item-url'     => home_url( '/' ),
				'menu-item-status'  => 'publish',
			)
		);

		// Set menu locatitons.
		$locations         = get_theme_mod( 'nav_menu_locations' );
		$locations['site'] = $main_menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		// Set a default, but filtered site description for WSU sites.
		update_option( 'blogdescription', apply_filters( 'wsuwp_install_site_description', '' ) );

		// Set a default, but filtered timezone for Pacific time.
		update_option( 'timezone_string', apply_filters( 'wsuwp_install_default_timezone_string', 'America/Los_Angeles' ) );

		// Revert back to current site.
		restore_current_blog();

		// Flush the rewrite rules immediately after site creation.
		delete_blog_option( $site_id, 'rewrite_rules' );

	}

	private static function get_homepage_content() {
		ob_start();
		?>
		<!-- wp:paragraph -->
		<p>This home page was automatically created with your new site. As soon as this page is edited, this introduction will be replaced with the content you save.</p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph -->
		<p>You can login to your dashboard at <a href="<?php echo esc_url( admin_url() ); ?>"><?php echo esc_url( admin_url() ); ?></a>.</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading -->
		<h2>Getting started</h2>
		<!-- /wp:heading -->

		<!-- wp:list -->
		<ul>
			<li>Verify your site’s title and tagline description in <a href="<?php echo esc_url( admin_url( 'options-general.php' ) ); ?>">General Settings</a>.</li>
			<li>Use the <a href="<?php echo esc_url( admin_url( 'customizer.php' ) ); ?>">Customizer</a> to modify WSU Spine options and customize several parts of your site.</li>
			<li>Add <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">Pages</a> and modify <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">Menus</a> to begin building out your site.</li>
			<li>Add <a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">Posts</a> to share updates on your work with the world.</li>
			<li>Join the <a href="https://wsu-web.slack.com/signup">WSU Web Slack</a> team to discuss your site with the WSU web community.</li>
			<li>Attend <a href="https://web.wsu.edu/open-lab/">Open Labs</a> on Friday mornings to do the same in person.</li>
			<li>Subscribe to posts on <a href="https://web.wsu.edu/">web.wsu.edu</a> to receive updates on the web at WSU.</li>
		</ul>
		<!-- /wp:list -->
		<?php
		$page_content = ob_get_contents();
		ob_end_clean();

		return trim( $page_content );
	}

	public static function init() {

		add_action( 'wp_initialize_site', array( __CLASS__, 'on_wp_initialize_site' ), 11, 2 );

	}


}

Set_New_Site_Defaults::init();
