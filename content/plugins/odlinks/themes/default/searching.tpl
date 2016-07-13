<div class="odl_list">
  <div class="odl_list_top">
    <table width="100%">
      <tr>
        <td valign="top" style="text-align: left;" width="100%" colspan=2>
          {$odl_lang.ODL_SUBMITTING_NOTE}
          <p>{$odl_lang.ODL_SUBMITTING_TEXT}{*$odl_top_description*}</p>
        </td>
      </tr>
      <tr>
      <td valign="top"><a href="{$odl_imgURL}"> {$top_image}</a></td>
      <td>
        <div class="odl_search">
          <form method="post" id="odl_search_post" name="odl_search_post" action="{$odl_search_link}">
          <table border=0>
          <tr>
            <td><input type="text" name="search_terms" size="15" value="{$search_terms}"><input type="submit" value="Search"><br></td>
          </tr>
          <tr>
            <td align="left">
             {$odl_lang.ODL_LINKS}<input type="radio" value="links" name="type">&nbsp;
             {$odl_lang.ODL_DESC}<input type="radio" value="desc" name="type">&nbsp;
             {$odl_lang.ODL_ALL}<input type="radio" value="all" name="type" checked><br>
             <div style="margin:20px 0;padding-top:20px;">
             {$odl_lang.ODL_CHOOSE_NOTE}
             {if ($addurl_link)}<h3><img src="{$odl_images}/images/addtopic.jpg" style="margin-left:-5px">{$addurl_link}</h3>{/if}
             </div>
            </td>
          </tr>
          </table>
          </form>
        </div>
      </td>
      </tr>
    </table>
   </div>
</div>