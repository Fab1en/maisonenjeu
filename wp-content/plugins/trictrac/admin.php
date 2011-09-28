<?php
/**
 * TricTrac - module admin
 * Plugin
 *
 *  Admin-interface for configuring the plugin
 *  via the standard-functions of pluginloader.
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
initvar('trictrac');
if ($trictrac) {
	$admin= isset($_POST['admin']) ? $_POST['admin'] : $admin = isset($_GET['admin']) ? $_GET['admin'] : '';
	$action= isset($_POST['action']) ? $_POST['action'] : $action = isset($_GET['action']) ? $_GET['action'] : '';
	
	// Detect the foldername of the plugin.
	$plugin=basename(dirname(__FILE__),"/");
	
	// Parameter "ON"  shows the Plugin Main Tab.
	// Blank "" or "OFF" does not show the Plugin Main Tab.
	$o .= print_plugin_admin('ON');
	
	// Main page shown when the Main Tab is clicked.
	if ($admin == 'plugin_main') {
		$acturl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?&" . $plugin . "&admin=plugin_main&action=plugin_text";
		$o .= plugin_admin_common($action, $admin, $plugin);
	}
	// First page when loading the plugin.
	if($admin == '') {
	   	$o .= "\n".'<div class="plugintext"><div class="plugineditcaption">'.ucfirst(str_replace('_',' ',$plugin)).'</div></div>'. tag('br');
		$o .= '<a href="'.$sn.'?&amp;'.$plugin.'&amp;action=refresh_cache">'.$plugin_tx['trictrac']['update-cache'].'</a>'.tag("br");
        $o .= '<a href="'.$sn.'?&amp;'.$plugin.'&amp;action=list_xls">XLS</a>'.tag("br");
		if($action == 'refresh_cache'){
		  $o .= trictrac_writeCollection();
		  $o .= $plugin_tx['trictrac']['cache-updated'].tag("br");
		} else if ($action == 'list_xls'){
            if($plugin_cf['trictrac']['data-cache']=="true")
                trictrac_listCollectionXLS(trictrac_readCollection());
            else
                trictrac_listCollectionXLS(trictrac_getCollection());
        }
	}
	
	

	// CONFIG, HELP, LANGUAGE, STYLESHEET (CSS) PAGE
	//
	// If config-, help-, language or stylesheet file are found
	// tabs and pages content are created automatically.
	if ($admin <> 'plugin_main') {
		$hint=ARRAY();
		$hint['mode_donotshowvarnames'] = FALSE;

		/*
		* Handle reading and writing of plugin files (e.g. en.php, config.php, stylesheet.css)
		* 
		* Arguments:
		* 
		* 	- STRING	$action:	'plugin_edit'				- edit variables (e.g. config variables)
		* 											'plugin_text'				-	edit text (e.g. stylesheet, plain text messages)
		* 											'plugin_save'				- save variables
		* 											'plugin_textsave'		- save text
		* 
		* 	- STRING	$admin:		'plugin_config'			- edit Plugin Config
		* 											'plugin_language'		- edit Plugin Language
		*												'plugin_main'				- show/edit Plugin Main
		* 											'plugin_stylesheet'	- edit Plugin Stylesheet
		* 
		* 	- STRING	$plugin		'<PLUGIN_NAME>'     - The name of the plugin (use $plugin whenever possible)
		* 
		* 	- ARRAY		$hint			['cf_variable_name']	- Show description/hint for variables
		* 																							You may set hints either in the language files like $plugin_tx['example_plugin']['cf_my_name']
		* 																							or define them directly like this: $hint['cf_my_name'] = 'test: my hint';
		* 
		* 											['mode_donotshowvarnames'] - Do not show hints (TRUE) or show hints (FALSE)	
		* 
		* 
		*/
		$o.=plugin_admin_common($action, $admin, $plugin, $hint);
	}

} // if plugin

?>