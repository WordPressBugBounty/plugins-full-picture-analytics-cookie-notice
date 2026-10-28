<?php

class Fupi_Public_Inline_JS {

	public $plugin_name;
	public $version;
	public $main;
	public $track;
	public $tools;
	public $woo;
	public $gtm;
	public $cook;
	public $track_current_user;
	public $ver;

	private $public;

	public function __construct( $public ) {
		$this->public             = $public;
		$this->plugin_name        = $public->plugin_name;
		$this->version            = $public->version;
		$this->main               = $public->main;
		$this->track              = $public->track;
		$this->tools              = $public->tools;
		$this->woo                = $public->woo;
		$this->gtm                = $public->gtm;
		$this->track_current_user = $public->track_current_user;
		$this->cook               = get_option( 'fupi_cook' );
		$this->ver                = get_option( 'fupi_versions' );
	}

	public function output() {
		$output = $this->get_output();
		$this->public->track_current_user = $this->track_current_user;
		echo $output;
	}

	private function get_output() {

		// GET THE DATA

		global $wp;
		global $post;

		$fp = [
			'atrig' => [],
			'loaded' => [],
			'loading' => [],
			'runtime' => [],
			'events' => [],
			'observers' => [],
			'tools' => [],
			'vars' => [],
			'notice' => [
				'enabled' => false,
			],
		];

		$fpdata = [];

		include_once FUPI_PATH . '/public/modules/main/data-main.php';
		include_once FUPI_PATH . '/public/modules/track/data-track.php';
		include_once FUPI_PATH . '/public/in_head/data-wp.php';
		include FUPI_PATH . '/includes/fupi_modules_data.php';

		// add files to the runtime script

		$fp = apply_filters( 'fupi_modify_fp_object', $fp );
		$fpdata = apply_filters( 'fupi_modify_fpdata_object', $fpdata );

		// OUTPUT THE DATA

		$output = '<!-- FUPI_INLINE_JS --><script id=\'fp_data_js\' class="fupi_no_defer" type="text/javascript" data-no-defer="1" data-no-optimize="1" data-cfasync="false" nowprocket>

			var FP = { \'fns\' : {} },
				fp = ' . json_encode( $fp ) . ',
				fpdata = ' . json_encode( $fpdata ) . ';';
				// fp_nonce = "' . wp_create_nonce('wp_rest'). '";'; // It has to be "wp_rest" This is required!

				$extra_scr = apply_filters( 'fupi_add_js_to_head_data', '' );
				if ( ! empty( $extra_scr ) ) $output .= $extra_scr;

			// add queueing functions
			$output .= '

			FP.manageIframes = ()=>{}; // DO NOT REMOVE for backwards compat
				
			// GTM
			
			window.dataLayer = window.dataLayer || [];

			if ( fp.gtm ) {
				let use_default_dl = ! fp.gtm.datalayer || fp.gtm.datalayer == "default";
				if ( ! use_default_dl ){
					window.fupi_dataLayer = window.fupi_dataLayer || [];
					window.fupi_gtm_gtag = function(){window.fupi_dataLayer.push(arguments);}
				}
			};

			// GTAG

			window.gtag = function(){window.dataLayer.push(arguments);};

			["gtag","fupi_gtm_gtag"].forEach( tag_name => {

				if ( typeof window[tag_name] == "function" ) {

					window[tag_name]( "consent", "default", {
						"analytics_storage": "denied",
						"ad_storage": "denied",
						"ad_user_data": "denied",
						"ad_personalization": "denied",
						"functionality_storage": "denied",
						"personalization_storage": "denied",
						"security_storage": "granted",
					} );
				}
			});

			// MS

			window.uetq = window.uetq || [];

			window.uetq.push( "consent", "default", {
				"ad_storage": "denied"
			});
			';

			if ( 
				! empty( $this->tools['cook'] ) && 
				! empty( $this->cook['scrblk_auto_rules'] ) && 
				in_array( 'pixelman', $this->cook['scrblk_auto_rules'] ) 
			) {
				$output .= '
				window._pmwq = window._pmwq || [];
				window._pmwq.push(function() {
					if ( !! window?.pmw?.consent?.api?.updateSelectively ) {
						pmw.consent.api.updateSelectively({
							statistics: false,
							marketing: false,
							preferences: false,
							necessary: true,
						});
					}
				});
				';
			}

			$output .= '
		</script><!-- FUPI_INLINE_JS_END -->';

		return $output;
	}
}
