<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <title>Maison en Jeu</title>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
        <?php echo wp_head(); ?>
    </head>
    <body>
  	<div id="page">
		<div id="header">
			<h1 id="logo"><?php bloginfo( 'name' ); ?></h1>
		</div><!-- /#header -->
		
		<div id="nav">
			<?php wp_page_menu(array('show_home' => true)) ?>
		</div><!-- /#nav -->
		
		<div id="content">
			<div id="texte">
				<?php if (have_posts()) while (have_posts()) : the_post(); ?>
				    <h1><?php the_title() ?></h1>
				    <?php the_content(); ?>
				<?php endwhile; ?>
			</div><!-- /#texte -->
			<div id="sidebar">
				<?php echo gCalendar_nextEvent() ?>
			</div><!-- /#sidebar -->
			<hr id="end-content" /> 
		</div><!-- /#content -->
		
		<div id="footer">
		    <ul>
		        <li><a href="http://www.wordpress-fr.net/" title="Fi�rement propuls� par Wordpress">WordPress</a></li>
		        <li><?php wp_loginout(); ?></li>
		    </ul>
		</div><!-- /#footer -->
	</div><!-- /#page -->
	<?php wp_footer(); ?>
  </body>
</html>
