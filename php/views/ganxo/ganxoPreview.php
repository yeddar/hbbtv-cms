

<?php
$imageURL = $_GET['imageURL'] ?? 'img/sense-imatge.png';
$title = $_GET['title'] ?? '';
$subtitle = $_GET['subtitle'] ?? '';
?>

<div style="display: flex; justify-content: center; align-items: center;">
    <div class="ganxo-container">
        <img src="img/logo-apunt.png" style="width: 100%; margin-bottom: 15px;" />			
        
        <img id="imatge_promo" src="<?= $imageURL ?>" style="width: 90%; display: block; margin-top:10px; margin-bottom:10px; margin-left: auto; margin-right: auto;" />
        
        <!-- <p style=" font-size: 18px; text-align: center; font-weight: bold; margin-top:15px; margin-bottom: 0; ">1996, <span style="white-space: nowrap">la tragèdia de Biescas</span></p> -->
        
        <p id="titol_promo" class="titol-promo"><?=$title?></p> 
        <p id="subtitol_promo" class="subtitol-promo"><?=$subtitle?></p>
        
        <div style="border-bottom:1px solid white; padding-top: 10px; "></div>
        <h2 style=" font-family: gotham-bold; padding:0;">CONTINGUTS<br>A LA CARTA</h2>
        <p style="font-size: 16px; margin-top: 15px; font-family: gotham-book;"><img src="img/boto-roig.png" style="float: left; margin: 0px 10px 0px 0px; width: 45px; font-weight: bold; "/>Prem el botó roig per a accedir</p>
        <p style="font-weight: bold; font-size: 18px; text-shadow: 0px 0px 10px #000000b3;">www.apuntmedia.es</p>
    </div>
</div>

<style>
            
    /* Fonts */
    @font-face {
        font-family: 'gotham-black';
        src: url(fonts/Gotham-Black.otf);
        font-weight: normal;
        font-style: normal;
        font-variant: normal
    }
    @font-face {
        font-family: 'gotham-bold';
        src: url(fonts/Gotham-Bold.otf);
        font-weight: normal;
        font-style: normal;
        font-variant: normal
    }
    @font-face {
        font-family: 'gotham-light';
        src: url(fonts/Gotham-Light.otf);
        font-weight: normal;
        font-style: normal;
        font-variant: normal
    }
    @font-face {
        font-family: 'gotham-book';
        src: url(fonts/gotham-book.otf);
        font-weight: normal;
        font-style: normal;
        font-variant: normal
    }
    @font-face {
        font-family: 'gotham-thin';
        src: url(fonts/Gotham-Thin.otf);
        font-weight: normal;
        font-style: normal;
        font-variant: normal
    }
    @font-face {
        font-family: 'gotham-bolditalic';
        src: url(fonts/Gotham-BoldItalict.otf);
        font-weight: normal;
        font-style: normal;
        font-variant: normal
    }
    @font-face {
        font-family: 'gotham-medium';
        src: url(fonts/Gotham-Medium.otf);
        font-weight: normal;
        font-style: normal;
        font-variant: normal
    }
    @font-face {
        font-family: 'gotham_blackitalic';
        src: url(fonts/Gotham-BlackItalic.otf);
        font-weight: normal;
        font-style: normal;
        font-variant: normal
    }

    .ganxo-container {
        font-family: 'gotham-medium';
        font-size: 0.8em;
        width: 200px;
        padding: 30px;
        height: 520px;
        /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#000000+0,000000+100&0.65+0,0+100 */
        background: -moz-linear-gradient(top, rgba(0,0,0,0.7) 50%, rgba(0,0,0,0) 100%); /* FF3.6-15 */
        background: -webkit-linear-gradient(top, rgba(0,0,0,0.7) 50%,rgba(0,0,0,0) 100%); /* Chrome10-25,Safari5.1-6 */
        background: linear-gradient(to bottom, rgba(0,0,0,0.7) 50%,rgba(0,0,0,0) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#a6000000', endColorstr='#00000000',GradientType=0 ); /* IE6-9 */
        color: white;
    }

    .titol-promo {
        font-size: 18px; 
        text-align: center; 
        font-weight: bold; 
        margin-top: 5px; 
        margin-bottom: 0;
    }

    .subtitol-promo {
        font-size: 16px; 
        text-align: center; 
        margin-top: 5px;
        margin-bottom: 10px;
    }


</style>
