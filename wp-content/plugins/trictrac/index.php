<?php
/**
 * TricTrac - main index.php
 * Plugin
 *
 * Manage a game library hosted by TricTrac website (www.trictrac.net)
 *
 * @author Fabien Quatravaux
 * @link http://www.maisonenjeu.asso.fr/
 * @version 1.0.00
 * @package pluginloader
 */
 
 // extension needed to parse TricTrac HTML page
 include_once('includes/simple_html_dom.php');
 // other file for this plugin
 include_once('html_parsing.php');
 if($plugin_cf['trictrac']['data-cache']=="true")
	include_once('read_write_data.php');
	
/**
 * Check if PLUGINLOADER is calling and die if not
 */
if(!defined('PLUGINLOADER')) {
	die('Plugin '. basename(dirname(__FILE__)) . ' requires a newer version of the Pluginloader. No direct access.');
}

// $cf  - CMSimple config
// $h   - heading text
// $hc  - heading current number.
// $l   - heading sub-category level
// $s   - active heading

/**
* Main function used to display either the complete library,
* or the details for one game (if 'gid' var is set).
* @return The HTML content to display.
*/
function trictrac_display(){
  global $gid, $plugin_cf;
  initvar('gid');
  if (isset($gid) and $gid!='')
	return trictrac_formatGameHTML(trictrac_getGame($gid));
  else{
	if($plugin_cf['trictrac']['data-cache']=="true")
	  return trictrac_formatCollectionHTML(trictrac_readCollection());
	else
	  return trictrac_formatCollectionHTML(trictrac_getCollection());
  }
}

/**
* Format the game library data in HTML.
* Merge HTML template defined in config file with library data.
* @param $games Array containing game library data.
* Each line contains data indexed by the keywords defined in config file.
* @return The HTML content to display.
*/
function trictrac_formatCollectionHTML($games=array()){
  global $plugin_cf, $plugin_tx, $sn,$s,$u;
  
  $o.="<ol class=\"ludo\">";
  
  // build game list
  foreach($games as $game){
	// replace #LINKTODESC# by a link to the page that describes the game
	$game['linktodesc']=$sn.'?'.$u[$s]."&gid=".$game['id_trictrac'];
	
	$o.="<li>";
	$temp=$plugin_cf['trictrac']['html-template'];
	// loop on keyword list
	foreach (preg_split("/,/",$plugin_cf['trictrac']['keywords']) as $keyword){
	  // replace the keyword by its value
	  $temp=preg_replace('/\#'.strtoupper($keyword).'\#/',$game[$keyword],$temp);
	}
	$o.=$temp;
	$o.="</li>";
  }
  
  $o.="</ol>";
  
  // credit link
  // FIXME : ajouter &qui=Name pour avoir le nom sur la page TricTrac
  $o.="<div class=\"credits\">liste et informations hébergées par 
	<a href=\"http://www.trictrac.net/index.php3?id=jeux&rub=ludoperso&inf=liste&choix=".$plugin_cf['trictrac']['id-trictrac']."
	\">Tric Trac</a>
  </div>";
  
  return $o;
}

/**
* Format the game data in HTML.
* Merge HTML template defined in config file with game data.
* @param $game Array containing game data.
* Data are indexed by keywords defined in config file.
* @return The HTML content to display.
*/
function trictrac_formatGameHTML($game=array()){
  global $plugin_cf, $plugin_tx, $sn, $s, $u, $h;
  
  // replace #BACK# by a link to the main page
  $game['back']="$sn?{$u[$s]}";
  
  $o="<div class=\"ficheJeu\">";
  $temp=$plugin_cf['trictrac']['html-game-template'];
  // loop on keyword list
  foreach (preg_split("/,/",$plugin_cf['trictrac']['keywords']) as $keyword){
	// replace the keyword by its value
	$temp=preg_replace('/\#'.strtoupper($keyword).'\#/',$game[$keyword],$temp);
  }
  $o.=$temp;
  
  // credit link
  $o.="<div class=\"credits\">liste et informations hébergées par 
  <a href=\"http://www.trictrac.net/index.php3?id=jeux&rub=detail&inf=detail&jeu=".$game['id_trictrac']."
  \">Tric Trac</a></div>";
  
  return $o;
}

function trictrac_listCollectionXLS($games=array()){
    global $plugin_cf;
    $keywords=array_intersect(preg_split("/,/",$plugin_cf['trictrac']['keywords']),
                                array('nom','etat','inventaire'));
    
    $o="";
    foreach ($keywords as $keyword) $o.=$keyword."\t"; 
    // build game list
    foreach($games as $game){
        $first=true;
        // loop on keyword list
        foreach ($keywords as $keyword){
            if ($first) {
                $o.="\n";
                $first=false;
            } else $o.="\t";
            if (isset($game[$keyword])) 
                $o.=preg_replace('/\n/','',html_entity_decode($game[$keyword]));
        }
    }
    
    header('Content-type: application/xls');
    header('Content-Disposition: attachment; filename="ludotheque.xls"');
    echo $o;
    exit;
}

?>