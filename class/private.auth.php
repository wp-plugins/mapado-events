<?php
/**
 * Class MapadoPrivateAuth
 * For admin area
 */
Class MapadoPrivateAuth extends MapadoPlugin {
	
	private $auth, $token;



	function __construct () {
		$this->setAuth();
		$this->setToken();
		$this->setDatas();

		add_action( 'admin_menu', array(&$this, 'adminMenu') );

		add_action( 'wp_ajax_ajaxGetUserLists', array(&$this, 'ajaxGetUserLists') );
		add_action( 'wp_ajax_ajaxUpdateListSettings', array(&$this, 'ajaxUpdateListSettings') );

		add_action( 'admin_enqueue_scripts', array(&$this, 'enqueueScriptsStyle') );
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
		$this->token	= new \Mapado\Sdk\Model\AccessToken();
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
		$notification	= array();

		/* API Settings submit */
		if ( !empty($_POST['mapado_settings_submit']) ) {
			$api = $auth = true;

			$api_settings	= array(
				'id'		=> $_POST['mapado_api_id'],
				'secret'	=> $_POST['mapado_api_secret']
			);

			/* Check if API settings have been changed */
			if ( empty($this->api) || (!empty($this->api) && ($this->api['id'] != $_POST['mapado_api_id'] || $this->api['secret'] != $_POST['mapado_api_secret'])) )
				$api	= update_option( parent::API_WP_INDEX, $api_settings );

			/* Check if auth key have been changed */
			if ( !isset($this->auth) || (isset($this->auth) && $this->auth != $_POST['mapado_api_auth']) )
				$auth	= update_option( parent::AUTH_WP_INDEX, $_POST['mapado_api_auth'] );

			/* Refresh access, auth & token */
			$this->setAccess();
			$this->setAuth();
			$this->setToken();
			
			/* Something went wrong */
			if ( !$api || !$auth ) {
				$notification	= array(
					'state'	=> 'error',
					'text'	=> 'Il y a eu un problème'
				);
			}
			/* Success */
			else {
				$notification	= array(
					'state'	=> 'updated',
					'text'	=> 'Réglages enregistrés'
				);
			}
		}

		/* Additional settings page submit */
		if ( !empty($_POST['mapado_add_settings_submit']) ) {
			if ( isset($_POST['mapado_widget']) )
				$this->settings['widget']	= true;
			else
				$this->settings['widget']	= false;

			$settings	= update_option( parent::SETTINGS_WP_INDEX, $this->settings );

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

		MapadoUtils::template( 'admin/settings', array(
			'notification'	=> $notification,
			'api'			=> $this->api,
			'auth'			=> $this->auth,
			'settings'		=> $this->settings
		));
	}

	/**
	 * AJAX
	 * Get user lists
	 */
	function ajaxGetUserLists () {
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
	function ajaxUpdateListSettings () {
		/* Add a list */
		if ( $_POST['mapado_action'] == 'import' ) {
			if ( empty($this->imported_lists) )
				$this->imported_lists	= array();

			$page	= wp_insert_post(array(
				'post_title'		=> $_POST['title'],
				'post_name'			=> $_POST['slug'],
				'post_content'		=> 'LISTE MAPADO',
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
				$this->imported_lists[$_POST['uuid']]	= $_POST['slug'];
		}
		/* Delete a list */
		else if ( $_POST['mapado_action'] == 'delete' ) {
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

}