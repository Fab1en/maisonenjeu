        <div id="header">
			<h1 id="logo"><?php bloginfo( 'name' ); ?></h1>
		</div><!-- /#header -->
		
		<div id="nav">
			<?php wp_page_menu(array('show_home' => true)) ?>
			
			<div class="rubriques">
			    <h2>Rubriques</h2>
			    <ul>
			        <?php wp_list_categories(array('title_li' => '')); ?> 
			    </ul>
			</div>
			
			<?php dynamic_sidebar('gauche') ?>
		</div><!-- /#nav -->
