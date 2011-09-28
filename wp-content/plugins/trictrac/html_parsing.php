<?php
/**
 * TricTrac - html_parsing.php
 * Plugin
 *
 * Manage a game library hosted by TricTrac website (www.trictrac.net)
 *
 * @author Fabien Quatravaux
 * @link http://www.maisonenjeu.asso.fr/
 * @version 1.0.00
 * @package pluginloader
 */
 
/**
* Call Tric Trac remote page and parse HTML
* to extract game library data.
* Loop on following pages if needed.
*
* @return An array containing game library data
* Each line contains data indexed by keyword.
*/
function trictrac_getCollection(){
  $pglen=20;
  $done=0;
  $data=trictrac_getCollection1Page(0);
  
  // remove 'total' entry from data array
  $total=$data['total'];
  $result=array_slice($data,0,-1);
  
  while ($total>$done+$pglen)
  {
	$done+=$pglen;
	$data=trictrac_getCollection1Page($done);
	// merge with previous results
	$result=array_merge($result,array_slice($data,0,-1));
  }
	
  return $result;
}

/**
* Call Tric Trac remote page and parse HTML
* to extract game library data. 
* Only works for one HTML page.
*
* @param $start The index from where to start
* @return An array containing game library data
* Each line contains data indexed by keyword.
* One more information is indexed by 'total' 
* and contains the number of games in the library.
*/
function trictrac_getCollection1Page($start){
  global $plugin_cf, $plugin_tx, $u, $s, $sn;
  $o="";
  $url="http://www.trictrac.net/index.php3";
  $url.="?id=jeux";
  $url.="&rub=ludoperso";
  $url.="&inf=liste";
  $url.="&deb=$start";
  $url.="&choix=".$plugin_cf['trictrac']['id-trictrac'];
  
  /* 
  *	1: dans sa colec
  *	2: йchange
  *	3: cherche
  *	4: vend ou йchange
  * 5: va se procurer
  *	6: recherche
  */
  $url.="&choix1=1";
  
  $trictrac=file_get_html($url);
  $data=array();
  $table_list=$trictrac->find('table', 16);
  foreach($table_list->find('table') as $i=>$t) {
	$j=($i-$i%2)/2;
	if($i%2==0) {
	  $data[$j]=array();
	  preg_match("/&jeu=(\d+)/",$t->find('tr',0)->find('a',0)->href,$matches);
	  $data[$j]['id_trictrac']=$matches[1];
	  $matches=null;
	  $data[$j]['vign']="http://www.trictrac.net/".$t->find('tr',0)->find('img',0)->src;
	  $data[$j]['infos']=$t->find('tr',0)->next_sibling()->find('p',0)->innertext;
	  if ( preg_match("/inventaire\s*[\d\/]+\s*:\s*([^:]+)\s*,*\s*йtat\s*:\s*([\wйиащ]+)/",$data[$j]['infos'],$matches))
	  {
		$data[$j]['etat']=$matches[2];
		$data[$j]['inventaire']=$matches[1];
		if(sizeof($data[$j]['inventaire']) > 0)
		  $data[$j]['complet']=(substr($data[$j]['inventaire'],0,7)=='complet'?'complet':'incomplet');
	  }
	}else{
	  $data[$j]['nom']=$t->find('tr',1)->find('b',0)->innertext;
	}
  }
  
  // is there more games on the next page ?
  preg_match("/(\d+) rйponse\(s\)/",$table_list->find('div', 2.5*($i+1))->find('b a',0)->innertext,$matches);
  $data['total']=$matches[1];
  
  return $data;
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
  global $plugin_cf, $plugin_tx;
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

?>