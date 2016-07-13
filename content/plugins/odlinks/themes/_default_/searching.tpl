<div class="odl_list" style="margin-bottom:40px">
  <div class="odl_list_top" style="min-height:200px;">
     {$odl_lang.ODL_SUBMITTING_NOTE}
     <p>{$odl_lang.ODL_SUBMITTING_TEXT}{*$odl_top_description*}</p>
     {$odl_lang.ODL_CHOOSE_NOTE}
     <div style="float:left;"><!--a href="{$odl_imgURL}"> {$top_image}</a></div-->
     <form style="margin-left:550px;margin-top:5px;min-width:260px" method="post" id="odl_search_post" name="odl_search_post" action="{$odl_search_link}">
         <input type="text" name="search_terms" size="23" value="{$search_terms}"><input type="submit" value="Search">
         <p>
         <input type="radio" value="links" name="type" style="margin:0 5px 0 0;">{$odl_lang.ODL_LINKS}
         <input type="radio" value="desc" name="type" style="margin:0 5px 0 15px;">{$odl_lang.ODL_DESC}
         <input type="radio" value="all" name="type" checked style="margin:0 5px 0 15px;">{$odl_lang.ODL_ALL}
         </p>
     </form>
     {*if ($addurl_link)}<h3><img src="{$odl_images}/images/addtopic.jpg" style="padding-right:5px">{$addurl_link}</h3>{/if*}
  </div>
</div>