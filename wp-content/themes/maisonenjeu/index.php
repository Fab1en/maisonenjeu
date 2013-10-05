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
				<?php if (have_posts()) while (have_posts()) : the_post(); ?>
				    <div class="article">
				        <a class="permalien" href="<?php the_permalink() ?>"><h2><?php the_title() ?></h1></a>
				        <?php the_content(); ?>
				    </div>
				<?php endwhile; ?>
			</div><!-- /#texte -->
			
			<?php get_template_part('sidebar', 'right') ?>
			<hr id="end-content" /> 
		</div><!-- /#content -->
		
		<?php get_footer() ?>
		
	</div><!-- /#page -->
	<?php wp_footer(); ?>
  </body>
</html>
