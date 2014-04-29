<?php
/*
Plugin Name: Trictrac
Description: Manage a game library hosted by TricTrac website (www.trictrac.net) 
Version: 1.0
Author: Fabien Quatravaux
Author URI: http://fab1en.github.com/
Text Domain: trictrac
*/

define('SCRIPT_DEBUG', true);


if(is_admin()){
    add_filter( 'post_mime_types', 'trictrac_add_game_mime_type');
    function trictrac_add_game_mime_type($mimes){
        $mimes['game/trictrac'] = array(
            'Jeux Trictrac',
            'Gérer les jeux Trictrac',
            _n_noop( 'Jeu <span class="count">(%s)</span>', 'Jeux <span class="count">(%s)</span>' )
        );
        
        return $mimes;
    }
    
    add_filter('wp_mime_type_icon', 'trictrac_game_icon', 10, 3);
    function trictrac_game_icon($icon, $mime, $post_id){
        if($mime == 'game/trictrac') return plugins_url('img/default.png', __FILE__);
        return $icon;
    }
    
} // is_admin

add_action('wp_print_styles', 'trictrac_add_stylesheet');
function trictrac_add_stylesheet(){
    wp_enqueue_style('trictrac', plugins_url('trictrac.css',__FILE__));
}

add_shortcode('ludotheque', 'trictrac_formatCollectionHTML');
/**
* Format the game library data in HTML.
* Merge HTML template defined in config file with library data.
* @param $games Array containing game library data.
* Each line contains data indexed by the keywords defined in config file.
* @return The HTML content to display.
*/
function trictrac_formatCollectionHTML(){

  $ludo = '<ol class="ludo">';
  
  // build game list
  $games = get_posts(array(
    'post_type' => 'attachment',
    'post_mime_type' => 'game/trictrac',
    'numberposts' => -1,
    'orderby' => 'post_title',
    'order' => 'ASC',
  ));
  
  foreach($games as $game) :
    $metas =  get_post_meta($game->ID, "atmedia", true);
    $ludo .= '<li>';
	$ludo .= '<a href="'.get_permalink($game->ID).'"><img src="'.$metas['miniature'].'" /><span class="nom">'.$game->post_title.'</span></a>';
	$ludo .= '</li>';
  endforeach;
  
  $ludo .= '</ol>';
  
  $ludo .= '<div class="credits">liste et informations hébergées par 
	<a href="http://www.trictrac.net">Tric Trac</a>
  </div>';
  
  return $ludo;
}

function the_game(){
    
}

/**
 * The Gallery shortcode.
 *
 * This implements the functionality of the Gallery Shortcode for displaying
 * WordPress images on a post.
 *
 * @since 2.5.0
 *
 * @param array $attr Attributes of the shortcode.
 * @return string HTML content to display gallery.
 */
function trictrac_gallery_shortcode($attr) {
    return '';
    
	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) )
			$attr['orderby'] = 'post__in';
		$attr['include'] = $attr['ids'];
	}

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => ''
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$icontag = tag_escape($icontag);
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) )
		$itemtag = 'dl';
	if ( ! isset( $valid_tags[ $captiontag ] ) )
		$captiontag = 'dd';
	if ( ! isset( $valid_tags[ $icontag ] ) )
		$icontag = 'dt';

	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';
	if ( apply_filters( 'use_default_gallery_style', true ) )
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
		</style>
		<!-- see gallery_shortcode() in wp-includes/media.php -->";
	$size_class = sanitize_html_class( $size );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
			<{$icontag} class='gallery-icon'>
				$link
			</{$icontag}>";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= '<br style="clear: both" />';
	}

	$output .= "
			<br style='clear: both;' />
		</div>\n";

	return $output;
}

