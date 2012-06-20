<?php

function SetSessionMenu()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $arr_cache_jscript;
	
	settype($_GET['IdBook'], 'integer');
	settype($_GET['IdDocumentation'], 'integer');

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('documentation'));

	load_model('documentation');
	load_libraries(array('utilities/parent_utilities'));

	load_lang('documentation');
	
	$field_setmenu=new BooleanField();
	
	$show_menu_var=$field_setmenu->check($_GET['show_menu_var']);
	
	$_SESSION['show_menu_var']=$show_menu_var;
	
	echo json_encode(array('success' => 1, 'show_menu_var' => $show_menu_var));
	
	die;
	
}

?>