<?php 
    if(!is_user_logged_in()) {
        wp_redirect( home_url(), 404 ); exit;
    }
    the_post();
?><!DOCTYPE html>
<html>
    <head>
        <title>Facture Maison en Jeu - <?php the_title() ?></title>
        <style>
            @media screen, print{
                header, footer, section{display:block;}
                header{ position:relative; height:440px}
                body{ margin: 1.5cm; width: 21cm;}
                header .description{position: absolute; right: 0; text-align: center; width: 49%;}
                header .emmeteur{width: 49%;}
                header .emmeteur h2{display:none}
                header .recepteur{width: 49%;bottom: 0; position: absolute; right: 0;}
                section.facture{clear:both}
                .tampon{float:right}
                .info{float:left}
                .produits{clear:both; border: thin solid; padding: 0.5cm;}
                .produits dd{text-align: right;}
                .reglement {margin: 12pt 0;text-align: right;}
                footer .vcard{border:thin solid; font-size: x-small;}
                footer .org{clear:both; display:block; font-size: small; font-weight: bold;}
                footer .vcard div{float:left}
                footer .vcard .clear{clear:both; float:none}
            }
        </style>
    </head>
    <body>
        <header>
            <div class="description">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/images/logo-chat.png" alt="logo" />
                <h1>Facture</h1>
            </div>
            <div class="emmeteur vcard">
                <h2 class="fn org">Maison en Jeu</h2>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/images/maisonenjeu.png" alt="Maison en Jeu"/>
                <p>Association loi 1901 n&deg;0723011413</p>
                <p>D&eacute;claration au JO du 24 avril 2004</p>
            </div>
            <?php 
                $the_bar = array_shift(get_the_terms($post->ID, 'bar'));
                $bar_metas = get_term_meta($the_bar->term_id, 'bar', true);
            ?>
            <div class="recepteur vcard">
                <h2 class="fn org"><?php echo $the_bar->name ?></h2>
                <div class="adr">
                    <?php echo $bar_metas['adresse'] ?>
                </div>
            </div>
        </header>
        <section class="facture">
            <?php $metas = get_post_meta($post->ID, 'facture', true) ?>
            <div class="num">facture num&eacute;ro <?php printf('%s/%03d', get_the_date('ymd'), $metas['num']) ?></div>
            <div class="info">r&eacute;f&eacute;rent asso : <?php the_author() ?></div>
            <div class="tampon">au <span class="location">Mans</span>, le <abbr id="date" title=""><?php echo get_the_date('j F Y') ?></abbr></div>
            <dl class="produits">
                <dt>Forfait soir&eacute;e jeux</dt><dd>25&euro;</dd>
                <dt>Soit un total de</dt><dd>25&euro;</dd>
            </dl>
            <div class="reglement">R&egrave;glement &agrave; effectuer en fin de prestation</div>
        </section>
        <footer>
            <div class="RIB">
            </div>
            <div class="vcard">
                <a class="fn org url" href="http://maisonenjeu.asso.fr/">Maison en Jeu</a>
                <div class="adr">
                    <span class="type">Adresse postale : </span>
                    <span class="street-address">54, rue des Fontenelles</span>
                    <span class="postal-code">72000</span>
                    <span class="locality">Le Mans</span>
                </div>
                <div class="adr">
                     -- 
                    <span class="type">Si&egrave;ge social : </span>
                    <span class="street-address">39, rue de Sarg&eacute;</span>
                    <span class="postal-code">72000</span>
                    <span class="locality">Le Mans</span>
                </div>
                <div class="tel">
                    <span class="type">T&eacute;l. : </span>02 43 88 76 22
                </div>
                <div> -- Email: 
                    <span class="email">maisonenjeu@gmail.com</span>
                </div>
                <div class="clear"></div>
            </div>
        </footer>
    </body>
</html>
