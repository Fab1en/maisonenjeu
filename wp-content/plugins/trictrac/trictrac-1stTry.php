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

add_action( 'init', 'trictrac_init');
function trictrac_init(){
    register_post_type( 'jeu',
		array(
			'labels' => array(
				'name' => _x( 'Jeu', 'nom général du type de page', 'trictrac' ),
                'singular_name' => _x('Jeu', 'nom singulier du type de page', 'trictrac'),
                'add_new' => _x('Ajouter', 'jeu', 'trictrac'),
                'add_new_item' => _x('Ajouter un jeu', 'jeu', 'trictrac'),
                'edit_item' => _x('Editer le jeu', 'jeu', 'trictrac'),
                'new_item' => _x('Nouveau jeu', 'jeu', 'trictrac'),
                'view_item' => _x('Voir le jeu', 'jeu', 'trictrac'),
                'search_items' => _x('Chercher dans les jeux', 'jeu', 'trictrac'),
                'not_found' =>  _x('Aucun jeu trouvé', 'jeu', 'trictrac'),
                'not_found_in_trash' => _x('Aucun jeu trouvé dans la poubelle', 'jeu', 'trictrac'), 
                'parent_item_colon' => '',
                'menu_name' => _x('Ludothèque', 'jeu', 'trictrac')

			),
		'public' => true,
		'hierarchical' => true, 
		'description' => _x('jeu', 'description', 'trictrac'),
		'menu_icon' => plugins_url( '' , __FILE__ ),
		'supports' => array('title', 'editor', 'page-attributes', 'comments'),
		)
	);
}
