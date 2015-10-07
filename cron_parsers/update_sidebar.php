<?php
include(dirname(__FILE__).'/../wp-load.php');
ob_start();
?>
<div class="right2">

<!--/* OpenX Javascript Tag v2.8.7 */-->

<script type='text/javascript'><!--//<![CDATA[
   var m3_u = (location.protocol=='https:'?'https://adv.advstatus.ru/www/delivery/ajs.php':'http://adv.advstatus.ru/www/delivery/ajs.php');
   var m3_r = Math.floor(Math.random()*99999999999);
   if (!document.MAX_used) document.MAX_used = ',';
   document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
   document.write ("?zoneid=3");
   document.write ('&amp;cb=' + m3_r);
   if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
   document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));
   document.write ("&amp;loc=" + escape(window.location));
   if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
   if (document.context) document.write ("&context=" + escape(document.context));
   if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
   document.write ("'><\/scr"+"ipt>");
//]]>--></script><noscript><a href='http://adv.advstatus.ru/www/delivery/ck.php?n=a7bfd748&amp;cb=65111115' target='_blank'><img src='http://adv.advstatus.ru/www/delivery/avw.php?zoneid=3&amp;cb=2132111&amp;n=a7bfd748' border='0' alt='' /></a></noscript>

        <br /><br />
        <br />

        <div class="zag2"><h2>Форекс статьи</h2></div>
        <table width="302" border="0" cellspacing="0" cellpadding="0" class="tabl1 bord">
            <tr>
            <td>
            <?php $postslist = get_posts('numberposts=3&category=4842,1822,9,5,1320,832,639,2284,301,7,1786,1994,1933,1787,2321,823,6');
            foreach ($postslist as $post) : setup_postdata($post); ?>
                <img src="<?php echo p75GetThumbnail(get_the_ID(), 89, 68, ""); ?>" />
                    <a href="<?php the_permalink(); ?>">
                <?php $anonce=get_post_meta($post->ID, 'anonce', true); if($anonce) { echo $anonce;} else { echo the_title();} ?>
                    </a>
                <div style="height: 15px;" class="clear"></div>
		<?php endforeach; ?>
		</td>
            </tr>
        </table>


        <br /><br />

                    <div class="clear2"></div>


                    <div class="block2 wid">
                        <div class="zag2"><h2>Промо, бонусы и акции</h2></div>
                        <div class="block1">
                            <ul>
            <?php $postslist = get_posts('numberposts=3&category=10');
                    foreach ($postslist as $post) : setup_postdata($post); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
            <?php echo (get_post_meta($post->ID, 'company', true)); ?></li>
<?php endforeach; ?>
                    </ul>
                    <div class="more2"><a href="/promo">Еще акции</a></div>
                </div>
            </div><br /><br />

<!--/* OpenX Javascript Tag v2.8.7 */-->

<script type='text/javascript'><!--//<![CDATA[
   var m3_u = (location.protocol=='https:'?'https://adv.advstatus.ru/www/delivery/ajs.php':'http://adv.advstatus.ru/www/delivery/ajs.php');
   var m3_r = Math.floor(Math.random()*99999999999);
   if (!document.MAX_used) document.MAX_used = ',';
   document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
   document.write ("?zoneid=4");
   document.write ('&amp;cb=' + m3_r);
   if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
   document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));
   document.write ("&amp;loc=" + escape(window.location));
   if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
   if (document.context) document.write ("&context=" + escape(document.context));
   if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
   document.write ("'><\/scr"+"ipt>");
//]]>--></script><noscript><a href='http://adv.advstatus.ru/www/delivery/ck.php?n=ae407d11&amp;cb=32132355454' target='_blank'><img src='http://adv.advstatus.ru/www/delivery/avw.php?zoneid=4&amp;cb=3215654321321&amp;n=ae407d11' border='0' alt='' /></a></noscript>
            
            <div class="block wid">
                <div class="zag2"><h2>Биржевое интервью</h2></div>
                <div class="block1">



        <?php $postslist = get_posts('numberposts=1&category=9');
                        foreach ($postslist as $post) : setup_postdata($post); ?>
                            <div class="blo2" style="width:100%;"><a href="<?php the_permalink(); ?>"><img src="<?php echo p75GetThumbnail(get_the_ID(), 94, 91, ""); ?>" /></a>
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br /><br />
        <?php echo (get_post_meta($post->ID, 'author', true)); ?></div>
<?php endforeach; ?>


        <div class="more2"><a href="/inter">Еще интервью</a></div>
    </div>
</div><br /><br />


<div class="col1 wid">
    <?php if (function_exists('get_siterating_top_sites')): $siterating_top = get_siterating_top_sites(5); ?>
    <div class="zag">Рейтинг Форекс сайтов</div>
    <table width="99%" border="0" cellspacing="0" cellpadding="0">
        <tr valign="bottom">
            <td><br />Название ресурса</td>
            <td align="right"><br />hits</td>
        </tr>
        <?php foreach ($siterating_top as $site):?>
        <tr valign="bottom">
            <td>
                <a href="/go.php?url=http://<?php echo $site->url; ?>">http://<?php echo $site->url; ?></a>
                <br />
                <?php echo $site->sitename; ?>
            </td>
            <td align="right"><?php echo $site->hits; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div class="all3"><a href="/siteraiting/">Добавить сайт</a></div>
    <?php endif; ?>
    <br /><br />


    <?php if (isset($journalDistributors) && is_object($journalDistributors) && get_class($journalDistributors) == 'journalDistributors'): $jd_sites = $journalDistributors->getSites(0,5); ?>
    <div class="zag2"><h2>Рейтинг партнеров</h2></div>
    <table width="302" border="0" cellspacing="0" cellpadding="0">
        <tr valign="bottom">
            <td><br />Название ресурса</td>
            <td align="right"><br />hits</td>
        </tr>
        <?php foreach ($jd_sites as $site):?>
        <tr valign="bottom">
            <td>
                <a href="/go.php?url=<?php echo $site->url; ?>"><?php echo $site->url; ?></a>
                <br />
                <?php echo $site->desc; ?>
            </td>
            <td align="right"><?php echo $site->dwall; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div class="all3"><a href="/distributors/cp/#add_site">Стать партнером</a></div>
    <br /><br />
    <?php endif; ?>

		<div class="zag">Рассылки трейдера</div>
        <div class="opros">
            <form action=http://subscribe.ru/member/quick method=post target=_top>
                  <INPUT type=hidden value=quick name=action>
                <input name="grp" type="checkbox" checked="checked" value="fin.forex.fxfft" />
                Журнал ForTrader.ru - анонсы выпусков<br />
                <input name="grp" type="checkbox" checked="checked" value="" />
                Ежедневные новости рынка и политики<br />
                <input name="grp" type="checkbox" checked="checked" value="fin.forex.fortrader" />
                Аналитика рынка Форекс (Forex)<br />
                <input name="grp" type="checkbox" checked="checked" value="http://subscribe.ru/catalog/fin.forex.fxta" />
                Торговые индикаторы, скриты и утилиты<br />
                <input name="grp" type="checkbox" checked="checked" value="http://subscribe.ru/catalog/fin.forex.fxsignals" />
                Forex - эксперты, советники MQL<br />
                <input name="grp" type="checkbox" checked="checked" value="http://subscribe.ru/catalog/fin.forex.fxstrateg" />
                Forex - Торговые стратегии<br />
                <input name="grp" type="checkbox" checked="checked" value="http://subscribe.ru/catalog/fin.rate.techanalisys" />
                Forex - популярные статьи о трейдинге<br />
        </div>
        <input class="inp" name="email" type="text" /><input class="sub2" value="OK!" name="submit" type="submit" />
        </form>
</div>
</div>
<div class="clear2"></div>
<?php if (function_exists('get_fxnow_top_brokers')): ?>
        <a class="all2" href="/ratetopbrokers/">Все брокеры</a>
        <div class="zag">Рейтинг биржевых брокеров</div>
        <ul class="partners">
    <?php $brokers = get_fxnow_top_brokers(5); ?>
    <?php foreach ($brokers as $entry): ?>
            <li class="nom"><?php echo $entry->sort ?>.</li>
            <li>
                <a href="<?php echo '/' . get_option('fxnow_rating_url') . '/' . preg_replace('/[^a-z\-]+/Ui', '', str_replace(' ', '-', $entry->CompNameComp)) . '-' . $entry->idcomp . '/' ?>">
                    <img src="<?php echo $entry->img ?>" alt="<?php echo $entry->CompNameComp ?>" />
                    <br /><?php echo $entry->CompNameComp ?>
                </a>
            </li>
    <?php endforeach; ?>
        </ul>
<?php endif; ?>
<?

$memcache->set(md5('wp-fortrader-sidebar'), ob_get_contents(), 3600);
ob_end_clean();

?>