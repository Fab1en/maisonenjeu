<?php
/**
 * TricTrac - read_write_data.php
 * Plugin
 *
 * Manage a game library hosted by TricTrac website (www.trictrac.net)
 * Module to cache data in a local file to improve performances.
 *
 * @author Fabien Quatravaux
 * @link http://www.maisonenjeu.asso.fr/
 * @version 1.0.00
 * @package pluginloader
 */

/**
* Read data from remote Tric Trac server, than
* write it into a local file.
*/
function trictrac_writeCollection(){
  global $pth;
  $data=trictrac_getCollection();
  $dbdir=$pth['folder']['plugins'].'trictrac/content/';
  $dbfile=$dbdir.'collection.db.php';
  
  // create directory if absent
  if (! file_exists($dbdir)) mkdir($dbdir);
  
  // first delete old data
  if (file_exists($dbfile)) unlink($dbfile);
  
  // then write new data
  foreach($data as $i=>$game){
	PluginWriteFile($dbfile, 
	  PluginPrepareConfigData("data[$i]",trictrac_escape($game)), 
	  FALSE, TRUE);
  }
  
  // Make the file readable, writable and executable by anybody.
  chmod($dbfile, 0777);
}

/**
* Replace characters data that are interpreted 
* as php code but should not.
* 
* @param $in The input string containing forbidden characters
* @return A safe string 
*/
function trictrac_escape($in){
  $out=$in;
  $out = preg_replace('/\$/','&#36;',$out);
  $out = preg_replace('/"/','\\"',$out);
  return $out;
}

/**
* Read the game library from local file to improve perfomances.
*
* @return An array containing game library data extracted form local file
* Each line contains data indexed by keyword.
*/
function trictrac_readCollection(){
  global $pth;
  // read file content
  require_once($pth['folder']['plugins'].'trictrac/content/collection.db.php');
  return $data;
}

?>