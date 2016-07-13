{*
 * $Revision: $
 * Description: Wordpress odlinks
 * Header_Templates
*}

<div style="border:1px dotted #cdcdcd; text-align:center; padding:5px; margin:10px 0;">

<center>
{literal}
<!--
add the "google_ad_client" to your correct adsense
or remove it completely if you want.
-->

<script type="text/javascript">
var getLocation = function(href) {
    var l = document.createElement("a");
    l.href = href;
    return l
}
</script>
{/literal}
</center>
</div>

{include file='searching.tpl'}

<div class="odl_head">
  {if ($navigation_link)}
     <h3><b>{$odl_main_link}</b>: {$navigation_link}</h3>
  {/if}
  {if $cat_id > "0"}
    {if ($odl_navigation_description)}
      <span class ="odl_navigation_description">{$odl_navigation_description}</span>
    {/if}
  {/if}
  {if ($addurl_link)}
    <h3><span class="odl_head_addurl"><img src="{$odl_images}/images/addtopic.jpg">{$addurl_link}</span></h3>
  {/if}
</div>

