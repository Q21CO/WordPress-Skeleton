{include file='header.tpl'}
<P>
<div class="odl_container">
  <div class="odl_deleteform">
    {if $error}<font color="red"><b>{$error}</b></font><hr>{/if}

    {$topTitle}
    {if $title}<br>{$odl_lang.ODL_CATEGORIES} {$title}{/if}
    {if $description}<br>{$description}{/if}
    <form method="post" id="odl_dellink_post" name="odl_dellink_post" onsubmit="this.sub.disabled=true;this.sub.value='{$odl_lang.ODL_POSTED}';" action="{$odl_post_link}">
    <input type="hidden" name="odlinkspost_topic" value="yes" />
    <table border=0 cellpadding=5 cellspacing=5 width="90%">
      <tr bgcolor="#F4F4F4">
      <td class="odl_label_right">{$odl_lang.ODL_URL} </td>
      <td>{$url}</td>
      </tr>
      <tr>
      <td class="odl_label_right">{$odl_lang.ODL_TITLE} </td>
      <td>{$title}<br />
      </tr>
      <tr bgcolor="#F4F4F4"><td class="odl_label_right"></td><td><p>{$description}</p></td><tr>
      <span class ="odl_smallTxt">{$odl_lang.ODL_CATNOTE}</span></td>
      </tr>
      <tr bgcolor="#F4F4F4">
      <td class="odl_label_right">{$odl_lang.ODL_EMAIL} </td>
      <td>{$email}</td>
      </tr>
      {$confirm}
      <tr bgcolor="#F4F4F4"><td></td><td bgcolor="#F4F4F4"><p><input type=submit value="Delete Site" name=odlinksdata[add]></p></td></tr>
   </table>
   {php}do_action('odlinkspost_topic_above_submit');{/php}
   <p>&nbsp;</p>
   </form>
   {if $googlebtn}{$googleAd}{/if}
</div>
<HR />
<div class="odl_main-content">
{include file='footer.tpl'}
