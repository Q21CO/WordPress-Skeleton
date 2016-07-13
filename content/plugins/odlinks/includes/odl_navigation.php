<?php

class odl_navigation {

  var $nr_rows=10;
  var $nr_links=10;
  var $all;
  var $params="";

  
  function get_navigation($page){
    return $this->get_html($page);
  }

  function get_html($page){
    $nr_rows  = $this->nr_rows;
    $nr_links = $this->nr_links;
    $all = $this->all; # should be setup by init of the class
    
    if($page==0) $page=1;
    $start = ($page-1)*$nr_rows+1;
    $end = $start + $nr_rows;
    if ($end > $all ) $end = $all;
    $nr_pages = ceil($all / $nr_rows);
    if ($nr_pages <2 ) return "";
    $start_page = (int)(($page) / $nr_links)*$nr_links+1;
    $end_page = $start_page + $nr_links;
    if ( $end_page > $nr_pages) {
      $end_page = $nr_pages+1;
      $mod = 0;
    }else{
      $mod = 1;
    }
    $next_page = $page+1;
    $prev_page = $page-1;
    $pniz = $start_page - 2;
    $nniz = $end_page + 1 - $mod;

    if (strlen($this->params)==0){
      $params =  "?";
    }else{
      $params = "?".$this->params."&";
    }

    $html = "";

    if ($pniz > 0 ){
      $html .= "<span class='odl_bold'><a href={$params}page=$pniz>&lt;&lt;</a></span>";
    }
    if ($prev_page > 0){
      $html .= "<span class='odl_bold'><a href={$params}page=$prev_page>Previous</a></span>";
    }

    for($i = $start_page; $i<$end_page; $i++){
      if ($i == $page){
        $html .= '<span class="current">'. $i.'</span>';
        $current_page = $i;
      }else{
        $html .= "<a href={$params}page=$i >$i</a>&nbsp;\n";
      }
    }

    if ($next_page <= $nr_pages ){
      $html .= "<span class='odl_bold'><a href={$params}page=$next_page>Next</a></span>";
    }
    if ($nniz <= $nr_pages ){
      $html .= "<span class='odl_bold'><a href={$params}page=$nniz>&gt;&gt;</a></span>";
    }
    return array($current_page, $html);
  }

}
