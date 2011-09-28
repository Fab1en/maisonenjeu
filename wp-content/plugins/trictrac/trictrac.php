<?php
/*
Plugin Name: Trictrac
Plugin URI: http://cabyres.com
Description: Manage a game library hosted by TricTrac website (www.trictrac.net) 
Version: 0.1
Author: Fabien Quatravaux
Author URI: http://cabyres.com
Text Domain: trictrac
*/

include_once(plugin_dir_path(__FILE__).'includes/simple_html_dom.php');

add_action('init', 'trictrac_add_map_media', 1);
function trictrac_add_map_media(){
    if (function_exists('atmedia_register_media')){
        atmedia_register_media(array('game' => array(
            'title' => __('Ajouter un jeu'), 
            'icon' => plugins_url( 'images/jeu.png', __FILE__ ),
            'mime_type' => 'game/trictrac',
            'add_new_form' => 'trictrac_add_new_form',
        )));
    }
}

function trictrac_add_new_form(){
    ?><div>
        <p>
            <label for="atmedia_title"><?php _e('Titre', 'trictrac') ?></label>
            <input type="text" id="atmedia_title" name="atmedia_title"></input>
        </p>
        <p>
            <input type="button" value="<?php _e('Chercher', 'trictrac') ?>" class="button" id="viewgamebutton" name="viewgamebutton">
        </p>
        
        <input type="submit" value="<?php _e('Save') ?>" class="button" id="savegamebutton" name="savegamebutton">
        
    </div>
    <?php
}

add_filter('media_upload_tabs', 'trictrac_add_tab');
function trictrac_add_tab($tabs){
    $tabs['trictrac'] = 'Trictrac';
    return $tabs;
}
add_action("media_upload_trictrac", "trictrac_tab_render");
function trictrac_tab_render(){
    return wp_iframe( 'media_trictrac_import_bdd');
}
function media_trictrac_import_bdd(){
    media_upload_header();
    
    /*include_once(plugin_dir_path(__FILE__).'content/collection.db.php');
    foreach($data as $game){
        $id = wp_insert_attachment(array(
	        'post_mime_type' => 'game/trictrac',
	        'post_title' => addslashes(utf8_encode($game['nom'])),
	        'guid' => 'http://www.trictrac.net/index.php3?id=jeux&rub=detail&inf=detail&jeu='.$game['id_trictrac'],
	    ));
	    update_post_meta($id, "atmedia", array(
	        'miniature' => $game['vign'],
	        'inventaire' => addslashes(utf8_encode($game['infos'])),
	        'id_trictrac' => $game['id_trictrac'],
	    ));
	    echo addslashes(utf8_encode($game['nom']))."<br>\n";
    }*/
}

add_action('wp_print_styles', 'trictrac_add_stylesheet');
function trictrac_add_stylesheet(){
    wp_enqueue_style('trictrac', plugins_url('/css/stylesheet.css',__FILE__));
}

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
  
  //print_r($games);
  
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

/**
* Format the game data in HTML.
* Merge HTML template defined in config file with game data.
* @param $game Array containing game data.
* Data are indexed by keywords defined in config file.
* @return The HTML content to display.
*/
function trictrac_formatGameHTML($game=array()){
  
  $o="<div class=\"ficheJeu\">";
  $temp = "<h2 class=\"title\">#NOM#</h2>
    <dl class=\"infosJeu\">
      <dt>auteur</dt><dd>#AUTEUR#</dd>
      <dt>editeur</dt><dd>#EDITEUR#</dd>
      <dt>annee</dt><dd>#ANNEE#</dd>
      <dt>genre</dt><dd>#GENRE#</dd>
      <dt>public</dt><dd>#PUBLIC#</dd>
      <dt>joueurs</dt><dd>#JOUEURS#</dd>
      <dt>duree</dt><dd>#DUREE#</dd>
    </dl>
    <img src=\"#IMG#\"/>
    </div>";
    
  // loop on keyword list
  foreach (preg_split("/,/","nom,id_trictrac,auteur,editeur,annee,genre,public,joueurs,duree,img") as $keyword){
	// replace the keyword by its value
	$temp=preg_replace('/\#'.strtoupper($keyword).'\#/',utf8_encode($game[$keyword]),$temp);
  }
  $o.=$temp;
  
  // credit link
  $o.="<div class=\"credits\">liste et informations hébergées par 
  <a href=\"http://www.trictrac.net/index.php3?id=jeux&rub=detail&inf=detail&jeu=".$game['id_trictrac']."
  \">Tric Trac</a></div>";
  
  return $o;
}

/**
* Call Tric Trac remote page and parse HTML
* to extract game data.
*
* @param $id_trictrac TricTrac game id
* @return An array containing game data
* Data are indexed by keyword.
*/
function trictrac_getGame($id_trictrac){
  $urlFicheJeu="http://www.trictrac.net/index.php3?id=jeux&rub=detail&inf=detail";
  $urlFicheJeu.="&jeu=".$id_trictrac;
  
  $ficheJeu=file_get_html($urlFicheJeu);
  $jeu=array();
  $ficheJeu=$ficheJeu->find('table', 14); 
  if($ficheJeu == "") return "no data";
  $jeu['nom']=$ficheJeu->find('tr', 0)->find('i', 0)->innertext;
  
  $infos=$ficheJeu->find('table', 0);
  if($infos->find('tr', 13) == "") return "wrong ID: $id_trictrac";
  
  $jeu['auteur']=$infos->find('tr', 0)->find('b a', 0)->innertext;
  $jeu['editeur']=$infos->find('tr', 2)->find('b a', 0)->innertext;
  $jeu['annee']=$infos->find('tr', 6)->find('b a', 0)->innertext;
  $jeu['genre']=$infos->find('tr', 7)->find('b a', 0)->innertext;
  $jeu['public']=$infos->find('tr', 8)->find('b a', 0)->innertext;
  $jeu['joueurs']=$infos->find('tr', 11)->find('b font', 0)->innertext;
  $jeu['duree']=$infos->find('tr', 13)->find('b font', 0)->innertext;
  $jeu['img']="http://www.trictrac.net/".$ficheJeu->find('tr', 2)->find('td', 0)->next_sibling()->find('img',0)->src;
  $jeu['id_trictrac']=$id_trictrac;
  
  return $jeu;
}

function the_game(){
    global $post;
    $metas =  get_post_meta($post->ID, "atmedia", true);
    echo trictrac_formatGameHTML(trictrac_getGame($metas['id_trictrac']));
}

add_shortcode('ludotheque', 'trictrac_formatCollectionHTML');

?>
