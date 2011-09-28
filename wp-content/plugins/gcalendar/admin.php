<?php
/**
 * Google Calendar - module admin
 * 
 * Admin-interface for configuring the plugin
 * via the standard-functions of pluginloader.
 *
 * @author Fabien Quatravaux
 * @link http://www.maisonenjeu.asso.fr/
 * @version 1.0.00
 * @package pluginloader
 */
/**
 * Check if plugin was called. If so, let the 
 * Loader create and handle the admin-menu 
 */
initvar('gcalendar');
if($gcalendar){
	$admin= isset($_POST['admin']) ? $_POST['admin'] : $admin = isset($_GET['admin']) ? $_GET['admin'] : '';
	$action= isset($_POST['action']) ? $_POST['action'] : $action = isset($_GET['action']) ? $_GET['action'] : '';
	$plugin=basename(dirname(__FILE__),"/");
	
	// Parameter "ON"  shows the Plugin Main Tab.
	// Blank "" or "OFF" does not show the Plugin Main Tab.
	$o .= print_plugin_admin('on');
	if($admin<>'plugin_main'){
		$o .= plugin_admin_common($action,$admin,$plugin);
	}
	if ($admin == 'plugin_main') {
		$acturl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?&" . $plugin . "&admin=plugin_main&action=plugin_text";
		$o .= plugin_admin_common($action, $admin, $plugin);
	}
	if($admin == '') {
	   	$o .= "\n".'<div class="plugintext">';
		$o .= '<div class="plugineditcaption">'.ucfirst(str_replace('_',' ',$plugin)).'</div>';
		$o .= '<div class="pluginCheckConfig">';
		if (function_exists("json_decode")){
			$o .= $plugin_tx['gcalendar']['json-ok']. tag('br');
		}else{
			$o .= $plugin_tx['gcalendar']['json-nok']. tag('br');
			if (version_compare("5.2.0", phpversion()))
			  $o .= 'PHP 5.2.0'.$plugin_tx['gcalendar']['version-required'].phpversion().tag('br');
		}
		if (function_exists("date_format")){
			$o .= $plugin_tx['gcalendar']['date-format-ok']. tag('br');
		}else{
			$o .= $plugin_tx['gcalendar']['date-format-nok']. tag('br');
			if (version_compare("5.2.0", phpversion()))
			  $o .= 'PHP 5.2.0'.$plugin_tx['gcalendar']['version-required'].phpversion().tag('br');
		}
		
		$o .= '</div>';
		$o .= '</div>'. tag('br');
	}
}
?>