<?php
/**
 * Class MapadoPublicAuth
 * For public area
 */
Class MapadoPublicAuth extends MapadoPlugin {

	private $token;

	/* To do the API call once on event single page */
	protected $current_event;



	function __construct () {
		$this->setDatas();
		$this->setToken();

		add_action( 'the_content', array(&$this, 'eventsPage'), 14 );
		add_action( 'the_title', array(&$this, 'eventsPageTitle'), 14 );
		add_action( 'the_content', array(&$this, 'eventPage'), 15 );
		add_action( 'the_title', array(&$this, 'eventPageTitle'), 15 );
		add_filter( 'wp_title', array(&$this, 'eventWpTitle'), 10, 15 );

		add_action( 'wp_enqueue_scripts', array(&$this, 'enqueuePublicStyle'), 15 );
	}



	/**
	 * Settings token
	 * Cached or not in WP database
	 */
	private function setToken ( $forceRefresh = false ) {
		$token_cache	= get_option( parent::TOKEN_WP_INDEX );

		/* Get cached token */
		if ( !$forceRefresh && !empty($token_cache) && $token_cache->getExpiresAt()->getTimestamp() > time() ) {
			$this->token	= $token_cache;
		}
		/* Refresh token */
		else if ( !empty($this->api['id']) && !empty($this->api['secret']) ) {
			try {
				$oauth			= \Mapado\Sdk\Oauth::createOauth( $this->api['id'], $this->api['secret'] );
				$this->token	= $oauth->getClientToken();

				update_option( parent::TOKEN_WP_INDEX, $this->token );
			}
			catch ( GuzzleHttp\Exception\ClientException $e ) {
				error_log( $e->getResponse() );
			}
		}
	}



	/**
	 * Enqueue style in public area
	 */
	public function enqueuePublicStyle () {
		wp_enqueue_style( 'mapado-plugin', MAPADO_PLUGIN_URL . 'assets/mapado_plugin.css', false, '0.1.6' );
	}

	/**
	 * Show event informations in post content
	 * @param original content
	 * @return filtered content
	 */
	public function eventsPage ( $content ) {
		global $wp_query, $post;

		if ( is_page() && in_the_loop() && (false !== $uuid = array_search($post->post_name, $this->imported_lists)) ) {
			/* Check token validity */
			if ( !$client = $this->getClient($this->token) )
				return 'Accès non autorisé, vérifiez vos identifiants Mapado.';

			/* Pagination */
			$perpage	= 3;
			$page		= 1;

			if ( !empty($wp_query->query_vars['paged']) )
				$page	= $wp_query->query_vars['paged'];

			$start		= ($page * $perpage) - $perpage;
			$end		= $page * $perpage - 1;
			$params		= array( 'image_sizes' => array('200x250'), 'offset' => $start, 'limit' => $perpage );
			$results	= $client->activity->program( $uuid, $params );

			$pagination	= array(
				'perpage'	=> $perpage,
				'page'		=> $page,
				'nb_pages'	=> ceil( $results->getTotalHits() / $perpage )
			);

			ob_start();

			MapadoUtils::template( 'events_list', array(
				'uuid'			=> $uuid,
				'list_slug'		=> $post->post_name,
				'events'		=> $results,
				'pagination'	=> $pagination
			));

			$content	 = ob_get_contents();

			ob_end_clean();
		}

		return $content;
	}

	/**
	 * Filtering post title for event single page
	 * @param original title
	 * @return filtered title
	 */
	public function eventsPageTitle ( $title ) {
		global $post, $wp_query;

		if ( is_page() && in_the_loop() && (false !== $uuid = array_search($post->post_name, $this->imported_lists)) ) {
			/* Retrieve list uuid with slug */
			foreach ( $this->imported_lists as $uuid => $slug ) {
				if ( $slug == $post->post_name )
					break;
			}

			$activity	= $this->getActivity( $uuid, $this->token );

			if ( !empty($activity) )
				$title		= $activity->getTitle();
		}

		return $title;
	}

	/**
	 * Filtering WP title for event single page
	 * @param original title
	 * @return filtered title
	 */
	public function eventWpTitle ( $title, $sep ) {
		global $post, $wp_query;

		if ( is_page() && $post->ID == $this->settings['activity_page'] ) {
			$params					= array( 'image_sizes' => array('700x250') );
			$this->current_event	= $this->getActivity( $wp_query->query_vars['event'], $this->token, $params );

			if ( !empty($this->current_event) )
				$title	= $this->current_event->getTitle() . ' ' . $sep . ' ' . get_bloginfo( 'name' );
		}

		return $title;
	}

	/**
	 * Filtering post title for event single page
	 * @param original title
	 * @return filtered title
	 */
	public function eventPageTitle ( $title ) {
		global $post, $wp_query;

		if ( is_page() && in_the_loop() && $post->ID == $this->settings['activity_page'] && !empty($this->current_event) ) {
			$title	= $this->current_event->getTitle();
		}

		return $title;
	}

	/**
	 * Filtering post content for event single page
	 * Replace page content by event content
	 * @param original content
	 * @return filtered content
	 */
	public function eventPage ( $content ) {
		global $post, $wp_query;

		if ( is_page() && in_the_loop() && $post->ID == $this->settings['activity_page'] ) {
			if ( empty($this->current_event) )
				return 'Accès non autorisé, vérifiez vos identifiants Mapado.';

			/* To fix the infinite loop with apply_filter on activity description */
			if ( !preg_match('/MAPADO_EVENEMENT/', $content) )
				return $content;

			$thumbs	= $this->current_event->getImageUrlList();

			ob_start();

			MapadoUtils::template( 'event_single', array(
				'event'		=> $this->current_event,
				'thumbs'	=> $thumbs
			));

			$content	 = ob_get_contents();

			ob_end_clean();
		}

		return $content;
	}

	/**
	 * Show activity infos in the widget
	 */
	public function eventWidget () {
		global $post, $wp_query;

		if ( is_page() && $post->ID == $this->settings['activity_page'] && !empty($this->settings['widget']) )
			MapadoUtils::template( 'widget', array('event' => $this->current_event) );
	}
}