<?php 

add_action('wp_head', 'maisonenjeu_head', 0);
function maisonenjeu_head(){ 
?>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php wp_title() ?></title>
    <meta name="viewport" content="width=device-width">
<?php
}

add_action('wp_enqueue_scripts', 'maisonenjeu_print_assets');
function maisonenjeu_print_assets(){
    // stylesheet
    wp_enqueue_style('maisonenjeu', get_stylesheet_directory_uri().'/style.css?v1.1', array());
}

add_action('widgets_init', 'maisonenjeu_sidebars');
function maisonenjeu_sidebars(){
    register_sidebar(array(
        'name' => 'Ardoise',
        'id' => 'ardoise',
        'before_widget' => '<div class="ardoise">',
        'after_widget' => '</div>'
    ));

    register_sidebar(array(
        'name' => 'Cadre à gauche',
        'id' => 'gauche',
        'before_widget' => '<div class="gauche">',
        'after_widget' => '</div>'
    ));
}
?>
