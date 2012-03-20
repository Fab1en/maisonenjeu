<?php 
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
?>
