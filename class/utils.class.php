<?php
/**
 * Class MapadoUtils
 * Utility functions
 */
Class MapadoUtils {
	/**
	 * Mapado template call
	 * @param path file in 'templates' folder
	 * @param variables to send to the template
	 */
	static function template ( $file, $vars = array() ) {
		require MAPADO_PLUGIN_PATH . 'templates/' . $file . '.php';
	}


	/**
	 * Build user list url based on WP permalink settings
	 * @param string user list uuid
	 * @param array of mapado pages ids
	 * @param array of imported lists
	 * @return string url
	 */
	static function getUserListUrl ( $slug, $page = 1 ) {
		$url	= user_trailingslashit( get_permalink(get_page_by_path($slug)) );

		/* Rewrite url */
		if ( get_option('permalink_structure') != '' && $page > 1 )
				$url	.= 'page/' . $page;
		/* Classic url */
		else if ( get_option('permalink_structure') == '' && $page > 1 )
			add_query_arg( 'page', $page );

		return $url;
	}

	/**
	 * Build single event url based on WP permalink settings
	 * @param string event uuid
	 * @param array of mapado pages ids
	 * @return string url
	 */
	static function getEventUrl ( $event_uuid, $list_slug  ) {
		$url	= user_trailingslashit( get_permalink(get_page_by_path($list_slug)) );

		/* Rewrite url */
		if ( get_option('permalink_structure') != '' )
			$url	= user_trailingslashit( $url ) . $event_uuid;
		/* Classic url */
		else
			$url	= add_query_arg( 'event', $event_uuid, $url );

		return $url;
	}
}