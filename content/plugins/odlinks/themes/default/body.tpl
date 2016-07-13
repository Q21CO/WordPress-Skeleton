{include file='header.tpl'}

<P>
<div class="odl_container">
  <div class="odl_editform">{if $error}<b>{$error}</b><hr>{/if}</div>
    <div class="odl_main-content">
    {if ($cat_id != 0) && ($cat_desc <> "")}{$odl_lang.ODL_DESC}<br />{$cat_desc}{/if}
    {if $categories}
      <h3>{$odl_lang.ODL_CATS}</h3></td>
      <table border="0" width="100%" class="odl_tbody" cellspacing="1" cellpadding="1">
      <tr><td width="100%">
        <table border="0" width="99%" cellspacing="3" cellpadding="3"><tr>
        {foreach from=$categories name=categories item=cat key=cats}
          <td valign="top" width="50%">
            <img src="{$odl_images}/images/folder.gif">&nbsp;<b>{$cat.cat_link}</b>&nbsp;<span class="odl_smallRed">({$cat.c_links})</span><br>
            {if $subcategories}
              <table width="99%" border="0">
                {assign var=cnt value=0}
                  {foreach from=$subcategories item=sub key=subs name=subcount}
                    {if $sub.c_parent==$cat.c_id}
                      {if $cnt is div by 2}</tr><tr>{/if}
                      <td width="50%">&nbsp;{$sub.c_path}<span class="odl_smallGreen">({$sub.c_links})</span></td>
                      {assign var=cnt value=$cnt+1}
                    {/if}
                    {foreachelse}
                    <tr><td>{$odl_lang.ODL_NOTFOUND}</td></tr>
                  {/foreach}
              </table>
            {/if}
          </td>
          {if $smarty.foreach.categories.iteration is div by 2}
            </tr><tr>
          {elseif not $smarty.foreach.categories.last}
            <td>&nbsp;</td>
          {/if}
        {/foreach}
        </tr></table>
      </td></tr>
      </table>
    {/if}
      <BR />
      {if $links}
       {*
       <h3>{$navigation_link}<div class="odl_smallTxt">({$links|@count})</div></h3>
       {$odl_navigation_description}<br /><br />
       *}
       {foreach from=$links item=item key=key}
         <div class="odl_viewlink">
          <table width=100%>
           <tr>
             <td valign="top" width="135">
             {$item.img}&nbsp;</td>
             <td valign="top" width=100%>
             <table width=100%>
               <tr width=100%><td width="5px"></td><td><b><a href="{$item.url}" target=_blank rel="nofollow">{$item.title}</a></b>&nbsp;<span class ="odl_smallTxt">{$odl_lang.ODL_ADDED}{$item.date}</span><p>{if $item.description <> ""}{$item.description}{/if}</p></td></tr>
               <tr><td width="5px"></td><td><br><img src="{$odl_images}/images/favourite.gif">
                  <a href="javascript:addbookmark('{$item.url}','{$item.title}');">
                  <font color="#840000">{$odl_lang.ODL_ADDFOVOURITE}</font></a>
                  &nbsp;&nbsp;&nbsp;<img src="{$odl_images}/images/refer.gif">{$item.sendlink}
                  &nbsp;&nbsp;&nbsp;{$item.rank_txt}
                  &nbsp;&nbsp;&nbsp;<img src="{$odl_images}/images/{$item.rank_img}">
                  {if $item.editlink}
                    &nbsp;&nbsp;&nbsp;<img src="{$odl_images}/images/edit.gif">{$item.editlink}
                    &nbsp;&nbsp;&nbsp;<img src="{$odl_images}/images/delete.gif">{$item.deletelink}
                 {/if}
              </td></tr>
            </table>
          </td></tr></table>
         </div>
       {/foreach}
       <p class="odl_pageNavi">{$navigation_page}</p>
     {/if}
     {$mycarousel}
{include file='footer.tpl'}
