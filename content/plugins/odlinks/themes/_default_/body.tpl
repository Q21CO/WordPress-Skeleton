{include file='header.tpl'}

<div class="odl_container">
  <div class="odl_editform">{if $error}<b>{$error}</b><hr>{/if}</div>
    <div class="odl_main-content">
    {if ($cat_id != 0) && ($cat_desc <> "")}{$odl_lang.ODL_DESC}<br />{$cat_desc}{/if}
    {if $categories}
      <h3>{$odl_lang.ODL_CATS}</h3>
        <table border="0" width="100%" cellspacing="2" cellpadding="2"><tr>
        {foreach from=$categories name=categories item=cat key=cats}
          <td valign="top" width="50%">
            <div style="border-color:#f7b5a7 #f7f5a7 #f7f5e7 #f7b5a7;border-width:1px;border-style:solid;background-color:#f7f5e7;padding-top:5px;padding-left:5px;height:40px;font: bold 18px 'Source Sans Pro',Helvetica, sans-serif;"><img style="padding-right:5px" src="{$odl_images}/images/folder.gif">{$cat.cat_link}<span class="odl_smallRed">({$cat.c_links})</span></div>
            {if $subcategories}
                {assign var=cnt value=0}
                {foreach from=$subcategories item=sub key=subs name=subcount}
                    {if $sub.c_parent==$cat.c_id}
                      {if $cnt is div by 2}
                        <div style="padding:0 0 0 14px;float:left;">&nbsp;{$sub.c_path}<span class="odl_smallGreen">({$sub.c_links})</span></div>
                      {else}
                        <div style="border-left:1px solid #f7b5a7;padding:0 0 0 234px;">&nbsp;{$sub.c_path}<span class="odl_smallGreen">({$sub.c_links})</span></div>
                      {/if}
                      {assign var=cnt value=$cnt+1}
                    {/if}
                    {foreachelse}
                    <div style="width:100%;">&nbsp;{$odl_lang.ODL_NOTFOUND}</div>
                  {/foreach}
            {/if}
          </td>
          {if $smarty.foreach.categories.iteration is div by 2}
            </tr><tr>
          {elseif not $smarty.foreach.categories.last}
          {/if}
        {/foreach}
        </tr></table>
    {/if}
      <BR />
      {if $links}
       {*
       <h3>{$navigation_link}<div class="odl_smallTxt">({$links|@count})</div></h3>
       {$odl_navigation_description}<br /><br />
       *}
       {foreach from=$links item=item key=key}
        <div class="odl_viewlink">
          <div style="padding:10px;min-width:135px;float:left">{$item.img}</div>
          <div style="margin-left:140px;padding-top:5px;">
            <p style="background-color: #f7f5e7;font: normal 20px 'Source Sans Pro', Helvetica, sans-serif;"><a href="{$item.url}" target=_blank rel="nofollow">{$item.title}</a>&nbsp;<span class ="odl_smallTxt">{$odl_lang.ODL_ADDED}{$item.date}</span></p>
            {if $item.description <> ""}{$item.description}{/if}
            <div style="padding:10px; 0">
             <img style="padding-right:3px" src="{$odl_images}/images/favourite.gif"><a href="javascript:addbookmark('{$item.url}','{$item.title}');" style="font: normal 16px "Source Sans Pro", Helvetica, sans-serif;">{$odl_lang.ODL_ADDFOVOURITE}</a>
             <img style="padding-right:3px;padding-left:15px;" src="{$odl_images}/images/refer.gif">{$item.sendlink}
             <img style="padding-left:15px;" src="{$odl_images}/images/{$item.rank_img}">{$item.rank_txt}
             {if $item.editlink}
                <img style="padding-right:3px;padding-left:15px;" src="{$odl_images}/images/edit.gif">{$item.editlink}
                <img style="padding-right:3px;padding-left:15px;" src="{$odl_images}/images/delete.gif">{$item.deletelink}
             {/if}
            </div>
          </div>
        </div>
       {/foreach}
       <p class="odl_pageNavi">{$navigation_page}</p>
     {/if}
	 <hr style="height: 1px; border: 0px solid #f7b5a7; border-top-width: 2px;" />
     {$mycarousel}
{include file='footer.tpl'}
