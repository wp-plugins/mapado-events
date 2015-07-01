<?php
/**
 * Class MapadoPrivateAuth
 * For admin area
 */
Class MapadoPrivateAuth extends MapadoPlugin {
	
	private $auth;
	private $token;
	private $param_list = array( 'perpage', 'card_thumb_position', 'card_thumb_orientation', 'card_thumb_size', 'card_column_max', 'list_sort', 'past_events' );

	function __construct () {
		$this->setAuth();
		$this->setToken();
		$this->setDatas();

		add_action( 'admin_menu', array(&$this, 'adminMenu') );

		add_action( 'wp_ajax_ajaxGetUserLists', array(&$this, 'ajaxGetUserLists') );
		add_action( 'wp_ajax_ajaxUpdateListSettings', array(&$this, 'ajaxUpdateListSettings') );

		add_action( 'admin_enqueue_scripts', array(&$this, 'enqueueScriptsStyle') );

		add_action( 'admin_init', array(&$this, 'pluginCheck') );

		/* Plugin settings link */
		add_filter( 'plugin_action_links_' . $this->plugin_basename, array(&$this, 'settingsPluginLink'), 10, 2 );
	}



	/**
	 * Get the private auth key from WP settings
	 */
	private function setAuth () {
		$this->auth	= get_option( parent::AUTH_WP_INDEX );
	}

	/**
	 * Generate token from auth key
	 */
	private function setToken () {
		$this->token = new \Mapado\Sdk\Model\AccessToken();
		$this->token->setAccessToken( $this->auth );
	}



	/**
	 * Enqueue JS & CSS files in mapado admin settings page
	 */
	public function enqueueScriptsStyle ( $hook ) {
		/* If not mapado settings page */
		if ( $hook != 'settings_page_mapado_settings' )
			return;

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'mapado_admin_script', MAPADO_PLUGIN_URL . 'assets/admin.js', '0.1.6' );

		wp_register_style( 'mapado_admin_css', MAPADO_PLUGIN_URL . 'assets/admin.css', false, '0.1.6' );
		wp_enqueue_style( 'mapado_admin_css' );
	}

	/**
	 * Check plugin version
	 */
	public function pluginCheck () {
		$plugin_datas	= get_plugin_data( plugin_dir_path( dirname( __FILE__ ))  . 'mapado.php' );

		/* Force flush rewrite if the plugin has been updated */
		if ( isset($this->settings['mapado_version']) && $plugin_datas['Version'] != $this->settings['mapado_version'] ) {
			$this->flushRules( true );

			/* Update last plugin version on this site */
			$this->settings['mapado_version']	= $plugin_datas['Version'];
			$this->settings->update();
		}
	}

	/**
	 * Adding mapado in admin settings menu
	 */
	public function adminMenu () {
		add_submenu_page(
			'options-general.php', 
			'Mapado plugin', 
			'Paramètres mapado', 
			'manage_options', 
			'mapado_settings', 
			array( &$this, 'adminSettings' )
		);
	}

	/**
	 * Admin settings page
	 */
	public function adminSettings () {
		/* Values for items per page params */
		$notification = array();

		/* API Settings submit */
		if ( !empty($_POST['mapado_settings_submit']) ) {
			$api = $auth = true;

			$api_settings	= array(
				'id'		=> $_POST['mapado_api_id'],
				'secret'	=> $_POST['mapado_api_secret']
			);

			/* Check if API settings have been changed */
			if ( empty($this->api) || (!empty($this->api) && ($this->api['id'] != $_POST['mapado_api_id'] || $this->api['secret'] != $_POST['mapado_api_secret'])) ) {
				$api	= update_option( parent::API_WP_INDEX, $api_settings );
			}

			/* Check if auth key have been changed */
			if ( !isset($this->auth) || (isset($this->auth) && $this->auth != $_POST['mapado_api_auth']) ) {
				$auth = update_option( parent::AUTH_WP_INDEX, $_POST['mapado_api_auth'] );
			}

			/* Refresh access, auth & token */
			$this->setAccess();
			$this->setAuth();
			$this->setToken();
			
			/* Something went wrong */
			if ( !$api || !$auth ) {
				$notification = array(
					'state'	=> 'error',
					'text'	=> 'Il y a eu un problème'
				);
			}
			/* Success */
			else {
				$notification = array(
					'state'	=> 'updated',
					'text'	=> 'Réglages enregistrés'
				);
			}
		}

		/* Additional settings page submit */
		if ( !empty($_POST['mapado_add_settings_submit']) ) {
			if ( isset($_POST['mapado_widget']) ) {
				$this->settings['widget'] = true;
			} else {
				$this->settings['widget'] = false;
			}

			foreach ( $this->param_list as $param ) {
				$this->settings[$param] = $_POST['mapado_' . $param];
			}

			$settings = $this->settings->update();

			/* Something went wrong */
			if ( !$settings ) {
				$notification	= array(
					'state'	=> 'error',
					'text'	=> 'Il y a eu un problème'
				);
			}
			/* Success */
			else {
				$notification	= array(
					'state'	=> 'updated',
					'text'	=> 'Paramètres supplémentaires enregistrés'
				);
			}
		}
		
		$vars = array(
			'notification'	=> $notification,
			'api'			=> $this->api,
			'auth'			=> $this->auth,
			'settings'		=> $this->settings
		);

		foreach ( $this->param_list as $param ) {
			$vars[$param]	= $this->settings->getDefinition( $param );
		}

		MapadoUtils::template( 'admin/settings', $vars);
	}

	/**
	 * AJAX
	 * Get user lists
	 */
	public function ajaxGetUserLists () {
		$userUuid		= $this->getUser( $this->token )->getUuid();
		$user_lists 	= $this->getClient( $this->token )->userList->findByUserUuid( $userUuid );

		MapadoUtils::template( 'admin/user_lists', array(
			'user_lists'		=> $user_lists,
			'imported_lists'	=> $this->imported_lists
		));

		exit;
	}

	/**
	 * AJAX
	 * Save a user list settings
	 */
	public function ajaxUpdateListSettings () {
		global $wpdb;
		
		/* Add a list */
		if ( $_POST['mapado_action'] == 'import' ) {
			if ( empty($this->imported_lists) )
				$this->imported_lists	= array();

			/* Slugify list slug */
			$slug	= sanitize_title( $_POST['slug'] );

			/* Check if slug already exist */
			if ( $wpdb->get_row("SELECT post_name FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $slug . "'", 'ARRAY_A') ) {
				echo json_encode(array(
					'state' => 'error',
					'msg'	=> "L'identifiant est déjà utilisé"
				));

				exit;
			}

			$page	= wp_insert_post(array(
				'post_title'		=> $_POST['title'],
				'post_name'			=> $slug,
				'post_content'		=> '[mapado_list]',
				'post_status'		=> 'publish',
				'post_type'     	=> 'page',
				'post_author'		=> 1,
				'comment_status'	=> 'closed'
			), false );

			if ( empty($page) ) {
				echo json_encode(array(
					'state' => 'error',
					'msg'	=> 'Problème lors de la création'
				));
			}
			else
				$this->imported_lists[$_POST['uuid']]	= $slug;
		}
		/* Delete a list & the associate page */
		else if ( $_POST['mapado_action'] == 'delete' ) {
			$page	= get_page_by_path( $_POST['slug'] );
			wp_delete_post( $page->ID, true );

			unset( $this->imported_lists[$_POST['uuid']] );
		}

		/* Something went wrong */
		if ( !update_option('mapado_user_lists', $this->imported_lists) ) {
			echo json_encode(array(
				'state' => 'error',
				'msg'	=> 'Erreur lors de la mise à jour'
			));
		}
		/* Success */
		else {
			echo json_encode(array(
				'state' => 'updated',
				'msg'	=> 'Listes mises à jour'
			));
		}

		exit;
	}

	/**
	 * Adding plugin settings link in extensions list
	 * @param array of existing links
	 * @param plugin basename
	 * @return array of links updated
	 */
	public function settingsPluginLink ( $links, $file ) {
		array_unshift( $links, '<a href="' . admin_url( 'options-general.php?page=mapado_settings' ) . '">Réglages</a>' );

		return $links;
	}

}