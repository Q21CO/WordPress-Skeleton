      
      {if ($addurl_link)}<h3><img src="{$odl_images}/images/addtopic.jpg">{$addurl_link}</h3>{/if}
      {if $new_links}
        <h3>{$odl_lang.ODL_LAST} {$linksNum} {$odl_lang.ODL_POSTED}</h3>
        {foreach from=$new_links item=item key=key}
          <div style="padding:0;margin:0"><a href="{$item.url}" target=_blank rel="nofollow">{$item.title}</a>&nbsp;<span class="odl_smallTxt">({$item.category}&nbsp;[{$item.date}])</span></div>
        {/foreach}
      {/if}

      <h3>{$odl_lang.ODL_LEGEND}</h3>
      <span class ="odl_smallTxt">{$odl_lang.ODL_CATEGORIES} {$categories_total}, {$odl_lang.ODL_LINKS} {$links_total}</span>
      {$odlFbLike}
      {$rssLink}
   </div><!--main-content-->
</div><!--odl_container-->