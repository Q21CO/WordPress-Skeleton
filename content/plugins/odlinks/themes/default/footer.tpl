      
      {if ($addurl_link)}<h3><img src="{$odl_images}/images/addtopic.jpg">{$addurl_link}</h3>{/if}
      <p><b>{$odl_main_link}</b>:{$navigation_link}</p>
      {if $new_links}
        <h3>{$odl_lang.ODL_LAST} {$linksNum} {$odl_lang.ODL_POSTED}</h3>
        <table><tr><td width=550>
          <table>
          {foreach from=$new_links item=item key=key}
            <tr><td width="20">

            </td><td><a href="{$item.url}" target=_blank rel="nofollow">{$item.title}</a>&nbsp;
            <span class="odl_smallTxt">({$item.category}&nbsp;[{$item.date}])</span><br /></td></tr>
          {/foreach}
          </table>
          </td><!--td valign="top" align="center" width="135"><img src="{$odl_images}/images/default.jpg" id="imageshow"></td--></tr>
        </table>
      {/if}

      <h3>{$odl_lang.ODL_LEGEND}</h3>
      <span class ="odl_smallTxt">{$odl_lang.ODL_CATEGORIES} {$categories_total}, {$odl_lang.ODL_LINKS} {$links_total}</span>
      {$odlFbLike}
      {$rssLink}
   </div><!--main-content-->
</div><!--odl_container-->