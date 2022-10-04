<?php
/*
Plugin Name: WP Store Locator Extender
Description: Taps into WP Store Locator and allows for use of shortcode to display grids of location data. Shortcode Example: [store_loc state="AZ"]
Version: 1.0
Author: David Hinojosa
Author URI: http://www.davidhinojosa.com
*/

 function my_error_notice() {
    ?>
    <div class="error notice">
        <p><?php _e( 'The WP Store Locator Plugin Is Not Active', 'my_plugin_textdomain' ); ?></p>
    </div>
    <?php
}

function convert_state($name, $name_type = null){
	$states = array(
'Alabama'=>'AL',
'Alaska'=>'AK',
'Arizona'=>'AZ',
'Arkansas'=>'AR',
'California'=>'CA',
'Colorado'=>'CO',
'Connecticut'=>'CT',
'Delaware'=>'DE',
'Florida'=>'FL',
'Georgia'=>'GA',
'Hawaii'=>'HI',
'Idaho'=>'ID',
'Illinois'=>'IL',
'Indiana'=>'IN',
'Iowa'=>'IA',
'Kansas'=>'KS',
'Kentucky'=>'KY',
'Louisiana'=>'LA',
'Maine'=>'ME',
'Maryland'=>'MD',
'Massachusetts'=>'MA',
'Michigan'=>'MI',
'Minnesota'=>'MN',
'Mississippi'=>'MS',
'Missouri'=>'MO',
'Montana'=>'MT',
'Nebraska'=>'NE',
'Nevada'=>'NV',
'New Hampshire'=>'NH',
'New Jersey'=>'NJ',
'New Mexico'=>'NM',
'New York'=>'NY',
'North Carolina'=>'NC',
'North Dakota'=>'ND',
'Ohio'=>'OH',
'Oklahoma'=>'OK',
'Oregon'=>'OR',
'Pennsylvania'=>'PA',
'Rhode Island'=>'RI',
'South Carolina'=>'SC',
'South Dakota'=>'SD',
'Tennessee'=>'TN',
'Texas'=>'TX',
'Utah'=>'UT',
'Vermont'=>'VT',
'Virginia'=>'VA',
'Washington'=>'WA',
'West Virginia'=>'WV',
'Wisconsin'=>'WI',
'Wyoming'=>'WY');

	if(isset($name_type) && !empty($name_type)){
		$states = array_flip($states);
		// array_flip($states);
	}

	return $states[$name];

}

function lovetap($atts){
if(is_plugin_active( 'wp-store-locator/wp-store-locator.php' )){

?>

<style>
	<?php include './style.css'; ?>
</style>

<?php	

$a = shortcode_atts(array('state' => ''), $atts);

	if($a['state']){
		if(strlen($a['state']) > 2){
			$state1 = convert_state($a['state']);
			$state2 = $a['state'];
		} else{
			$state1 = convert_state($a['state'], 1);
			$state2 = $a['state'];
		}

	}

	$args = array( 
		'post_type' => 'wpsl_stores',
		'numberposts' => -1,
		'meta_query' => array( 
			'relation' => 'OR',
			 array( 
			 	'key' => 'wpsl_state',
				'value' => $state1,
				'compare' => '=',
			 ),
			 array( 
		 		'key' => 'wpsl_state',
				'compare' => '=',
				'value' => $state2,
			)
		)
	);

	$args['post_type'] = 'wpsl_stores';
	
	$test = get_posts( $args );

	foreach($test as $post){
		$ids[$post->ID] = get_post_meta($post->ID);
	}


	$body = '';
	foreach($ids as $post_ID => $locationInfo){
	/*
		if($locationInfo['wpsl_state'][0] === 'AZ'){
			$body .= 
		}
	*/
	$body .= '<div class="little-grid">';
	
	if(!empty($locationInfo['wpsl_url'][0])){
		$body .= '<a class="loc-title" href="'. $locationInfo['wpsl_url'][0] .'">' . get_the_title($post_ID) . '</a>';
	} else {
		$body .= get_the_title($post_ID);
	}

	if(!empty($locationInfo['wpsl_address'][0])){
		$body .= '<br>';
		$body .= $locationInfo['wpsl_address'][0];
	}

	if(!empty($locationInfo['wpsl_address2'][0])){ 
	    $body .= '<br>';
		$body .= $locationInfo['wpsl_address2'][0];
	}

	if(!empty($locationInfo['wpsl_city'][0]) && (!empty($locationInfo['wpsl_state'][0]))){
		$body .= '<br>';
		$body .= $locationInfo['wpsl_city'][0] . ' ' . $locationInfo['wpsl_state'][0] . ' ' . $locationInfo['wpsl_zip'][0];
	}

	if(!empty($locationInfo['wpsl_country'][0])){
		$body .= '<br>';
		$body .= $locationInfo['wpsl_country'][0];
	}

	if(!empty($locationInfo['wpsl_phone'][0])){
		$body .= '<br>';
		$body .= '<strong>Phone: </strong>' . '<a href="tel:' . $locationInfo['wpsl_phone'][0] . '">'. $locationInfo['wpsl_phone'][0] . '</a>';
	}
	
	if(!empty($locationInfo['wpsl_email'][0])){
		$body .= '<br>';
		$body .= '<strong>Email: </strong>' . '<a href="mailto:' . $locationInfo['wpsl_email'][0] . '">'. $locationInfo['wpsl_email'][0] . '</a>';
	}

	$body .= '</div>';
	//var_dump($ids);
	}

	return $body;

	} else {
   
		add_action( 'admin_notices', 'my_error_notice' );

	}
}

add_shortcode( 'store_loc', 'lovetap' );
?>
