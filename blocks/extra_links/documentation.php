<?php

global $lang;

load_lang('documentation');

$select_page[]=$lang['translations']['translations'];
$select_page[]='optgroup';

$select_page[]=$lang['documentation']['documentation'];
$select_page[]=make_fancy_url($base_url, 'documentation', 'index', 'documentation', $arr_data=array());

$select_page[]='';
$select_page[]='end_optgroup';

?>
