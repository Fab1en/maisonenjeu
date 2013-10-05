<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <?php echo wp_head(); ?>
    </head>
    <body>
  	<div id="page">
		<?php get_header() ?>
		
		<div id="content">
			<div id="texte">
				<?php the_post(); ?>
				<?php echo do_shortcode('[game]'); ?>
			</div><!-- /#texte -->
			
			<?php get_template_part('sidebar', 'right') ?>
			<hr id="end-content" /> 
		</div><!-- /#content -->
		
		<?php get_footer() ?>
		
	</div><!-- /#page -->
	<?php wp_footer(); ?>
  </body>
</html>
