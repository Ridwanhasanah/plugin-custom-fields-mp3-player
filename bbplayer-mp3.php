<?php
/*
  Plugin Name: Pemutar MP3
  Plugin URI: https://www.facebook.com/ridwan.hasanah3
  Description: Plugin Pemuatar Mp3
  Version: 1.0
  Author: Ridwan Hasanah
  Author URI: https://www.facebook.com/ridwan.hasanah3
*/

add_action('add_meta_boxes','rh_mp3_player_meta_box_add' );

function rh_mp3_player_meta_box_add(){

	add_meta_box(
		'rh_custom_fields_mp3_player', //Meta box ID
		'MP3 Player', //Title of the meta box.
		'rh_custom_fields_mp3_player_form', //fucntion utk menampilkan form
		'post', //screen
		'normal', //The context within the screen where the boxes should display. 
		'hight'); //The priority within the context where the boxes should show ('high', 'low').

}

function rh_custom_fields_mp3_player_form(){
	$data = get_post_custom(get_the_ID() );
	if (!is_null($data['rh-custom-fields-mp3-player'])) {
		extract(unserialize($data['rh-custom-fields-mp3-player'][0]));
	}

	wp_nonce_field( 'rh_mp3_player_custom_fields_nonce','rh_mp3_player_nonce' );
	?>
		<label for="url_mp3">Url Mp3</label>
		<input type="text" size="100" name="url_mp3" id="url_mp3" value="<?php echo $url_mp3; ?>"/>
	<?php
}

add_action( 'save_post','rh_custom_fields_mp3_player_simpan');

function rh_custom_fields_mp3_player_simpan(){
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can( 'edit_post' ) ) return;
	if (!isset($_POST['rh_mp3_player_nonce']) || !wp_verify_nonce($_POST['rh_mp3_player_nonce'],'rh_mp3_player_custom_fields_nonce' ) ) return;

	$custom_fields['url_mp3'] = $_POST['url_mp3'];
	update_post_meta(get_the_ID(), 'rh-custom-fields-mp3-player',$custom_fields);
}

add_action('wp_head', 'rh_custom_fields_mp3_player_head' );
add_filter('the_content','rh_custom_fields_mp3_player_tampil' );
add_action('wp_head', 'rh_custom_fields_mp3_player_footer' );

function rh_custom_fields_mp3_player_head(){

	if (is_singular('post' )) {
		$data = get_post_custom(get_the_ID() );


		if (!empty($data['rh-custom-fields-mp3-player'][0])) {
			extract(unserialize($data['rh-custom-fields-mp3-player'][0]));

			if (!empty($url_mp3)) {
				echo '<link rel="stylesheet" href="'.plugin_dir_url(__FILE__).'/css/bbplayer.css">';
			}
		}
	}
}

function rh_custom_fields_mp3_player_tampil($content){

	if (!is_singular('post' )) {
		return $content;
	}

	$data = get_post_custom(get_the_ID());

	if (!empty($data['rh-custom-fields-mp3-player'][0])) {
		
		extract(unserialize($data['rh-custom-fields-mp3-player'][0]));
		if (!empty($url_mp3)) {
			$template   = dirname(__FILE__).'/player.html';
			$mp3_player = '<p>'.file_get_contents($template).'</p>';
			$mp3_player = str_replace('{url_mp3}', $url_mp3, $mp3_player);

			return $mp3_player.$content;
		}else {
			return $content;
		}
	}else {
		return $content;
	}
}

function rh_custom_fields_mp3_player_footer(){
	if (is_singular('post' )) {
		$data = get_post_custom(get_the_ID() );

		if (!empty($data['rh-custom-fields-mp3-player'][0])) {
				extract(unserialize($data['rh-custom-fields-mp3-player'][0]));

				if (!empty($url_mp3)) {
					echo '<script src="'.plugin_dir_url(__FILE__ ).'/js/bbplayer.js"></script>';
				}
			}	
	}
}
?>