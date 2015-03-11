<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <?php echo wp_head(); ?>
    </head>
    <body>
  	<div id="page">
		<?php get_header() ?>
		
		<div id="content">
			<div id="texte"><div class="article">
				<a class="permalien" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ) ?>">
					<h2>Dans le lot de <?php the_author_meta( 'display_name' ); ?> (<?php echo $wp_query->found_posts ?>)</h2>
				</a>
				<?php if (have_posts()) : ?>
					<ol class="ludo">
						<?php while (have_posts()) : the_post(); ?>
						<li>
							<a href="<?php the_permalink() ?>">
								<?php echo wp_get_attachment_image( $post->ID, 'thumbnail', true ); ?>
								<span class="nom"><?php the_title() ?></span>
							</a>
						</li>
						<?php endwhile; ?>
					</ol>
					<div class="credits">Informations fournies par <a href="http://www.twikin.fr/" alt="Twikin">
						<img src="<?php echo get_stylesheet_directory_uri() . '/images/Logo-Twikin-Blue.png'; ?>"/>
					</a></div>
				<?php endif; ?>
			</div></div><!-- /#texte -->
			
			<?php get_template_part('sidebar', 'right') ?>
			<hr id="end-content" /> 
		</div><!-- /#content -->
		
		<?php get_footer() ?>
		
	</div><!-- /#page -->
	<?php wp_footer(); ?>
  </body>
</html>
