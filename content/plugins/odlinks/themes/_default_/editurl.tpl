{include file='header.tpl'}
<P>
<div class="odl_container">
  
  <h3>{$odl_lang.ODL_SUBMITSITE}</h3>
  <div class="odl_editform">
    {if $error}<font color="red"><b>{$error}</b></font><hr>{/if}

    {$odl_lang.ODL_REQUIRED_FIELDS}
    {if $title}<br>{$odl_lang.ODL_CATEGORIES} {$title}{/if}
    {if $description}<br>{$description}{/if}
    <form method="post" id="odl_addlink_post" name="odl_addlink_post" onsubmit="this.sub.disabled=true;this.sub.value='{$odl_lang.ODL_POSTED}';" action="{$odl_post_link}">
    <input type="hidden" name="odlinkspost_topic" value="yes" />
    <table border=0 cellpadding=5 cellspacing=5 width="90%">
      <tr bgcolor="#F4F4F4">
      <td class="odl_label_right">{$odl_lang.ODL_URL} </td>
      <td><input type="text" name="odlinksdata[url]" value="{$url}" size="40"></td>
      </tr>
      <tr>
      <td class="odl_label_right">{$odl_lang.ODL_TITLE} </td>
      <td><input type="text" name="odlinksdata[title]" value="{$title}" size="40"><br />
      <span class ="odl_smallTxt">Maximum 50 characters</span></td>
      </tr>
      <tr bgcolor="#F4F4F4">
      {$descriptionHtml}</p>
      <tr>
      <td class="odl_label_right">{$odl_lang.ODL_CATEGORIY} </td>
      <td>
      <select name="odlinksdata[category]">{$categoryList}</select><br />
      <span class ="odl_smallTxt">{$odl_lang.ODL_CATNOTE}</span></td>
      </tr>
      <tr bgcolor="#F4F4F4">
      <td class="odl_label_right">{$odl_lang.ODL_EMAIL} </td>
      <td><input type="text" name="odlinksdata[email]" value="{$email}" size="40">
      <br /><span class ="odl_smallTxt">{$odl_lang.ODL_EMAILNOTE}</span></td>
      </tr>
      {$confirm}
      <tr bgcolor="#F4F4F4"><td></td><td bgcolor="#F4F4F4"><p>{$odl_lang.ODL_PAGENOTE}<BR>
      <input type=submit value="{$odl_lang.ODL_UPDATE}" name=odlinksdata[add]></p></td></tr>
   </table>
   {php}do_action('odlinkspost_topic_above_submit');{/php}
   <p>&nbsp;</p>
   </form>
   {if $googlebtn}{$googleAd}{/if}
</div>
<HR />
<div class="odl_main-content">
{include file='footer.tpl'}
