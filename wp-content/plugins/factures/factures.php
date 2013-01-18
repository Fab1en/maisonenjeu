<?php
/*
Plugin Name: Factures
Plugin URI: http://cabyres.com
Description: Gestion des factures pour Maison en Jeu
Version: 0.1
Author: Fabien Quatravaux
Author URI: http://cabyres.com
Text Domain: factures
*/

register_activation_hook(__FILE__, 'factures_install'); 
function factures_install() {
    // crée le numéro de facture courant
	add_option('factures', array(
	    'courant' => 0
	));
}


add_action( 'init', 'factures_create_objects');
function factures_create_objects() {
	
	// les bars (clients)
	register_taxonomy('bar', 'facture', array(
		'labels' => array(
			'name' => 'Bars',
            'singular_name' => 'Bar',
            'add_new' => 'Ajouter',
            'add_new_item' => 'Ajouter un bar',
            'edit_item' => 'Éditer le bar',
            'update_item' => 'Mettre à jour le bar',
            'new_item' => 'Nouveau bar',
            'new_item_name' => 'Nom du nouveau bar',
            'view_item' => 'Voir le bar',
            'search_items' => 'Chercher dans les bars',
            'popular_items' => 'Bars les plus utilisés',
            'separate_items_with_commas' => '',
		    'add_or_remove_items' => 'Ajouter ou retirer des bars',
		    'choose_from_most_used' => 'Choisir parmi les bars les plus utilisés',
            'all_items' => 'Tous les bars',
            'not_found' => 'Aucun bar trouvé',
            'not_found_in_trash' => 'Aucun bar trouvé dans la poubelle',
            'parent_item' => null,
            'parent_item_colon' => null,
            'menu_name' => 'Bars'
		),
		'hierarchical' => true,
	));
	
	register_post_type( 'facture',
		array(
			'labels' => array(
				'name' => 'Factures',
                'singular_name' => 'Facture',
                'add_new' => 'Ajouter',
                'add_new_item' => 'Ajouter une facture',
                'edit_item' => 'Editer la facture',
                'new_item' => 'Nouvelle facture',
                'view_item' => 'Voir la facture',
                'search_items' => 'Chercher dans les factures',
                'not_found' =>  'Aucune facture trouvée',
                'not_found_in_trash' => 'Aucune facture trouvée dans la poubelle',
                'parent_item_colon' => '',
                'menu_name' => 'Factures',
			),
		'public' => true,
		'hierarchical' => false, 
		'description' => 'les factures',
		'menu_icon' => plugins_url( 'images/facture.png', __FILE__ ),
		'supports' => array('title', 'author'),
		'taxonomies' => array('bar'),
		)
	);
}

add_action( 'post_updated', 'factures_calc_num', 10, 2);
function factures_calc_num($post_ID, $post){
    if($post->post_type == 'facture') {
        $metas = get_post_meta($post_ID, 'facture', true);
        if(!$metas['num']) {
            $opt = get_option('factures');
            $metas['num'] = ++$opt['courant'];
            update_option('factures', $opt);
            add_post_meta($post_ID, 'facture', $metas);
        }
    }
}

if ( is_admin() ) {

    add_action('admin_init', 'factures_admin_init');
    function factures_admin_init(){
        // page pour gérer les options
        add_settings_section('factures', 'Factures', 'factures_section', 'general');
        function factures_section(){}
        add_settings_field('factures', 'Numéro de facture courant', 'factures_options', 'general', 'factures');
        function factures_options(){
            $opt = get_option('factures');
            echo '<input id="factures[courant]" name="factures[courant]" value="'.$opt["courant"].'" />';
        }
        register_setting('general', 'factures');
    }
    
    // info supplémentaires sur les bars : adresse et gérant
    add_action( 'bar_add_form_fields', 'factures_add_bar_form_fields');
    function factures_add_bar_form_fields($taxonomy){
        ?>
        <div class="form-field">
	        <label for="gerant">Gérant</label>
	        <input type="text" name="bar[gerant]" id="gerant" />
	        <p>Nom du gérant</p>
	    </div>
	    <div class="form-field">
	        <label for="adresse">Adresse</label>
	        <input type="text" name="bar[adresse]" id="adresse" />
	        <p>Adresse de l'établissement</p>
	    </div>
	    <div class="form-field">
	        <label for="siret">Siret</label>
	        <input type="text" name="bar[siret]" id="siret" />
	        <p>N° SIRET de l'établissement</p>
	    </div>
	    <?php
	}
	
    add_action( 'bar_edit_form_fields', 'factures_edit_bar_form_fields', 10, 2);
    function factures_edit_bar_form_fields($tag, $taxonomy){
        
        $metas = get_term_meta($tag->term_id, 'bar', true);
        if(!$metas) $metas= array('gerant' => '', 'adresse' => '', 'siret' => '');
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="gerant">Gérant</label></th>
            <td><input type="text" name="bar[gerant]" id="gerant" value="<?php echo $metas['gerant'] ?>" />
            <p class="description">Nom du gérant</p></td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="adresse">Adresse</label></th>
            <td><input type="text" name="bar[adresse]" id="adresse" value="<?php echo $metas['adresse'] ?>" />
            <p class="description">Adresse de l'établissement</p></td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="adresse">Siret</label></th>
            <td><input type="text" name="bar[siret]" id="siret" value="<?php echo $metas['siret'] ?>" />
            <p class="description">N° SIRET de l'établissement</p></td>
        </tr>
	    <?php
    }
    
    add_action( 'edit_bar', 'factures_edit_bar');
    function factures_edit_bar($term_id){
        update_term_meta($term_id, 'bar', $_POST['bar']);
    }
    
    add_action( 'create_bar', 'factures_create_bar');
    function factures_create_bar($term_id){
        update_term_meta($term_id, 'bar', $_POST['bar']);
    }
    
    add_action( 'add_meta_boxes', 'factures_pdf_generation_box' );
    function factures_pdf_generation_box(){
        add_meta_box( 
            'factures_pdfgen',
            'fichier PDF',
            'factures_inner_pdfgen',
            'facture'
        );
    }
    function factures_inner_pdfgen($post){
        // Use nonce for verification
        wp_nonce_field( plugin_basename( __FILE__ ), 'factures_pdfgen_nonce' );
        
        $pdfs = get_children(array(
            'post_parent' => $post->ID,
            'post_type' => 'attachment',
            'post_mime_type' => 'application/pdf',
            'post_status' => 'inherit',
        ));
        
        if(sizeof($pdfs)){
            $pdf = array_shift($pdfs);
            ?>
                <a class="pdf" href="<?php echo wp_get_attachment_url($pdf->ID) ?>">
                    <?php echo wp_get_attachment_image($pdf->ID, 'thumbnail', 1) ?>
                    <?php echo $pdf->post_title ?>
                </a>
                <div class="infos">
                    <label><input type="checkbox"/>Envoyée</label>
                    <label><input type="checkbox"/>Payée</label>
                    <label><input type="checkbox"/>Encaissée</label>
                </div>
                <div class="ajax-button">
                    <img src="<?php echo admin_url('images/wpspin_light.gif') ?>" class="ajax-loading" id="ajax-loading" alt="">
                    <button id="genpdf" data-id="<?php echo $post->ID ?>">Regénérer le PDF</button>
                </div>
            <?php
        } else { 
            ?>
                <img src="<?php echo admin_url('images/wpspin_light.gif') ?>" class="ajax-loading" id="ajax-loading" alt="">
                <button id="genpdf" data-id="<?php echo $post->ID ?>">Générer le PDF</button>
            <?php
        }
        
        
    }
    
    add_action('admin_print_scripts-post.php', 'factures_add_script');
    function factures_add_script($page){
        wp_enqueue_script('factures', plugins_url( 'js/factures.js' , __FILE__ ));
    }
    
    add_action('admin_print_styles-post.php', 'factures_add_style');
    function factures_add_style($page){
        wp_enqueue_style('factures', plugins_url( 'css/factures.css' , __FILE__ ));
    }
    
    add_action('wp_ajax_factures_genpdf', 'factures_ajax_genpdf');
    function factures_ajax_genpdf(){
    
        $facture_id = $_GET['id'];
        
        $the_bar = array_shift(get_the_terms($facture_id, 'bar'));
        $bar_metas = get_term_meta($the_bar->term_id, 'bar', true);
        $facture_metas = get_post_meta($facture_id, 'facture', true);
        $facture = get_post($facture_id);
        $author = get_userdata($facture->post_author);
        $num = sprintf('%s/%03d', mysql2date('ymd',$facture->post_date), $facture_metas['num']);
        
        $content = '
            <style>
                @media screen, print{
                    #header{ position:relative;}
                    body{ margin: 1.5cm; width: 21cm;}
                    #header .description{position: absolute; right: 0; text-align: center; width: 49%;}
                    #header .description img{width: 80%};
                    #header .emmeteur{width: 49%;}
                    #header .recepteur{width: 49%;bottom: 0; position: absolute; right: 0;}
                    .tampon{text-align:right;}
                    .produits{border: thin solid; padding: 20px; list-style:none;}
                    .produits .montant{text-align: right;}
                    .reglement {margin: 12pt 0;text-align: right;}
                    #footer .vcard{border:thin solid;}
                    #footer .org{clear:both; display:block; font-weight: bold;}
                    
                    .spacer{height:200px;}
                    .description .spacer{height: 50px;}
                }
            </style>
            
            <div id="header">
                <div class="description">
                    <div class="spacer"></div>
                    <img src="http://maisonenjeu.asso.fr/wordpress/wp-content/themes/maisonenjeu/images/logo-chat.png" alt="logo" />
                    <h1>Facture</h1>
                </div>
                <div class="emmeteur vcard">
                    <img src="http://maisonenjeu.asso.fr/wordpress/wp-content/themes/maisonenjeu/images/maisonenjeu.png" alt="Maison en Jeu"/>
                    <p>Association loi 1901 n&deg;0723011413</p>
                    <p>D&eacute;claration au JO du 24 avril 2004</p>
                </div>
                
                <div class="spacer"></div>
                
                <div class="recepteur vcard">
                    <h2 class="fn org">'.$the_bar->name.'</h2>
                    <div class="adr">'.$bar_metas['adresse'].'</div>
                </div>
            </div>
            <div class="facture">
                <div class="num">facture num&eacute;ro '.$num.'</div>
                <div class="info">r&eacute;f&eacute;rent asso : '.$author->display_name.'</div>
                <div class="tampon">au <span class="location">Mans</span>, le <span id="date" title="">'.mysql2date('j F Y',$facture->post_date).'</span></div>
                <ul class="produits">
                    <li><div>Forfait soir&eacute;e jeux</div><div class="montant">25&euro;</div></li>
                    <li><div>Soit un total de</div><div class="montant">25&euro;</div></li>
                </ul>
                <div class="reglement">R&egrave;glement &agrave; effectuer en fin de prestation</div>
            </div>
            <div id="footer">
                <div class="vcard">
                    <div><a class="org" href="http://maisonenjeu.asso.fr/">Maison en Jeu</a></div>
                    <div class="adr">
                        <span class="type">Adresse postale : </span>
                        <span class="street-address">54, rue des Fontenelles</span>
                        <span class="postal-code">72000</span>
                        <span class="locality">Le Mans</span>
                    </div>
                    <div class="adr">
                        <span class="type">Si&egrave;ge social : </span>
                        <span class="street-address">39, rue de Sarg&eacute;</span>
                        <span class="postal-code">72000</span>
                        <span class="locality">Le Mans</span>
                    </div>
                    <div class="tel">
                        <span class="type">T&eacute;l. : </span>02 43 88 76 22
                    </div>
                    <div> Email: 
                        <span class="email">maisonenjeu@gmail.com</span>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        ';
        
        require_once(dirname(__FILE__).'/html2pdf/html2pdf.class.php');
        try {
            // convertir la facture en PDF
            $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', array(20, 20, 20, 20));
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->writeHTML($content);

            // sauvegarder en document attaché
            $upload_dir = wp_upload_dir();
            $filename = "facture_".str_replace('/', '-', $num).".pdf";
            $html2pdf->Output($upload_dir['path'].'/'.$filename, 'F');
            $attachment = wp_insert_attachment(array(
                'guid' => $upload_dir['url'].'/'.$filename, 
                'post_mime_type' => 'application/pdf',
                'post_title' => 'Facture '.$facture->post_title.' (n° '.$num.')',
                'post_content' => '',
                'post_status' => 'inherit'
            ), $upload_dir['path'].'/'.$filename, $_GET['id']);
            
            echo $attachment;
            
        } catch(HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
        
        die();
    }
    
} // endif is_admin

?>
