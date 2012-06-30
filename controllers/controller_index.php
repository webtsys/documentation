<?php

function Index()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data;

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('documentation'));

	load_model('documentation');
	//load_libraries(array('form_date', 'form_time', 'pages'));

	load_lang('documentation');
	
	$cont_index.=ob_get_contents();
	
	ob_clean();
	
	$query=$model['book']->select('', array('IdBook', 'title'));
	
	?>
	<ul>
		<?php
		while(list($idbook, $title)=webtsys_fetch_row($query))
		{

			$title=I18nField::show_formatted($title);
		
			$url_opt=make_fancy_url($base_url, 'documentation', 'readbook', $title, array('IdBook' => $idbook) );
			
			?>
			<li><a href="<?php echo $url_opt; ?>"><?php echo $title; ?></a></li>
			<?php
		
		}
		
		?>
	</ul>
	<?php
	
	$cont_books=ob_get_contents();
	
	ob_clean();

	echo load_view(array($lang['documentation']['books'], $cont_books), 'content');

	$cont_index.=ob_get_contents();

	ob_end_clean();

	echo load_view(array($lang['documentation']['books'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>
