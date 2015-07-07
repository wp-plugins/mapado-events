<?php
/**
 * Class MapadoPublicAuth
 * For public area
 */
Class MapadoPublicAuth extends MapadoPlugin {

	private $token;

	private $event_displayed	= false;

	/* To do the API call once on event single page */
	protected $current_event;



	function __construct () {
		$this->setDatas();
		$this->setToken();

		add_action( 'the_content', array(&$this, 'eventPage'), 15 );
		add_action( 'the_title', array(&$this, 'eventPageTitle'), 15 );
		add_filter( 'wp_title', array(&$this, 'eventWpTitle'), 10, 15 );

		add_action( 'wp_enqueue_scripts', array(&$this, 'enqueuePublicStyle'), 15 );

		/* Lists display */
		add_shortcode( 'mapado_list', array(&$this, 'eventsPage')  );
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
		wp_enqueue_style( 'mapado-card', MAPADO_PLUGIN_URL . 'assets/mapado_card.css', false, '0.1.6' );
	}

	/**
	 * Show list of events
	 * @param shortcode attributes
	 * @return list html
	 */
	public function eventsPage ( $atts ) {
		global $wp_query, $post;

		if ( is_page() && in_the_loop() && !empty($this->imported_lists) && (false !== $uuid = array_search($post->post_name, $this->imported_lists)) && empty($wp_query->query_vars['mapado_event']) ) {
			/* Check token validity */
			if ( !$client = $this->getClient($this->token) )
				return 'Accès non autorisé, vérifiez vos identifiants Mapado.';

			/* Pagination */
			$page		= 1;
			$perpage	= 10;
			if ( !empty($this->settings['perpage']) ) {
				$perpage = $this->settings['perpage'];
			}

			/* Sort */
			$sort	= '';
			if ( !empty($this->settings['list_sort']) ) {
				$sort = $this->settings['list_sort'];
			}

			/* Display past events */
			$past_events	= 'soon';
			if ( !empty($this->settings['past_events']) )
				$past_events = 'all';

			if ( !empty($wp_query->query_vars['paged']) ) {
				$page	= $wp_query->query_vars['paged'];
			}

			$start		= ($page * $perpage) - $perpage;
			$params		= array(
				'image_sizes'	=> array('100x150', '150x225', '200x300', '150x100', '225x150', '300x200', '150x150', '225x225', '300x300', '500x120', '500x200', '500x280'),
				'offset'		=> $start,
				'limit'			=> $perpage,
				'list'			=> $uuid,
				'sort'			=> $sort,
				'when'			=> $past_events
			);
			$results	= $client->activity->findBy( $params );

			$pagination	= array(
				'perpage'	=> $perpage,
				'page'		=> $page,
				'nb_pages'	=> ceil( $results->getTotalHits() / $perpage )
			);

			/* Card design */
			$card_thumb_design = $this->getCardThumbDesign();
			$card_column_max = $this->settings->getValue('card_column_max');
			$card_template = new MapadoMicroTemplate($this->settings->getValue('card_template'));
			
			ob_start();
			
			MapadoUtils::template( 'events_list', array(
				'uuid'				=> $uuid,
				'list_slug'			=> $post->post_name,
				'events'			=> $results,
				'pagination'		=> $pagination,
				'card_column_max'	=> $card_column_max,
				'card_thumb_design'	=> $card_thumb_design,
				'card_template'     => $card_template
			));

			$html	 = ob_get_contents();

			ob_end_clean();

			return $html;
		}
	}

	/**
	 * Filtering WP title for event single page
	 * @param original title
	 * @return filtered title
	 */
	public function eventWpTitle ( $title, $sep ) {
		global $post, $wp_query;

		if ( is_page() && !empty($this->imported_lists) && (false !== $uuid = array_search($post->post_name, $this->imported_lists)) && !empty($wp_query->query_vars['mapado_event']) ) {
			$params					= array( 'image_sizes' => array('700x250') );
			$this->current_event	= $this->getActivity( $wp_query->query_vars['mapado_event'], $this->token, $params );

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

		if ( is_page() && in_the_loop() && !empty($this->imported_lists) && (false !== $uuid = array_search($post->post_name, $this->imported_lists)) && !empty($this->current_event) ) {
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

		if ( is_page() && in_the_loop() && !empty($this->imported_lists) && (false !== $uuid = array_search($post->post_name, $this->imported_lists)) && !empty($wp_query->query_vars['mapado_event']) ) {
			if ( empty($this->current_event) )
				return 'Accès non autorisé, vérifiez vos identifiants Mapado.';

			if ( empty($this->event_displayed) )
				$this->event_displayed = true;
			else
				return $content;

			$thumbs	= $this->current_event->getImageUrlList();

			ob_start();

			MapadoUtils::template( 'event_single', array(
				'event'		=> $this->current_event,
				'thumbs'	=> $thumbs,
				'display_map'	=> $this->settings->getValue('display_map')
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

	/**
	 * Calculate the thumb size in card listing according to admin settings
	 */
	protected function getCardThumbDesign () {
		$card_thumb_position_type = 'side';
		$card_thumb_position_side = $this->settings->getValue('card_thumb_position');
		$card_thumb_orientation = $this->settings->getValue('card_thumb_orientation');
		$card_thumb_size = $this->settings->getValue('card_thumb_size');
		
		$card_thumb_ratio	= 2;
		if ( $card_thumb_size == 'm' ) {
			$card_thumb_ratio	= 1;
		} else if ($card_thumb_size == 's' ) {
			$card_thumb_ratio	= 0;
		}
		
		if ( $card_thumb_position_side == 'top' ) {
			$card_thumb_position_type = 'bandeau';
			$card_thumb_dimensions = array( 500, (120 + $card_thumb_ratio * 80) );
		} else {
			$card_thumb_dimensions	= array( 100, 150 );

			if ( $card_thumb_orientation == 'landscape' ) {
				$card_thumb_dimensions	= array( 150, 100 );
			} else if ($card_thumb_orientation == 'square') {
				$card_thumb_dimensions	= array( 150, 150 );
			}

			foreach ( $card_thumb_dimensions as $dimension => $val ) {
				$card_thumb_dimensions[$dimension]	= $val * ( 1 + 0.5*$card_thumb_ratio );
			}
		}
		
		return array(
			'position_type'		=> $card_thumb_position_type,
			'position_side'		=> $card_thumb_position_side,
			'orientation'	=> $card_thumb_orientation,
			'size'			=> $card_thumb_size,
			'dimensions'	=> implode('x', $card_thumb_dimensions)
		);
	}
}