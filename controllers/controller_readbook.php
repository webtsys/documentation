<?php

function ReadBook()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $arr_cache_jscript;
	
	settype($_GET['IdBook'], 'integer');
	settype($_GET['IdDocumentation'], 'integer');
	settype($_SESSION['show_menu_var'], 'integer');

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('documentation'));

	load_model('documentation');
	load_libraries(array('utilities/parent_utilities'));

	load_lang('documentation');
	
	//load jquery
	
	$arr_cache_jscript[]='jquery.min.js';
	
	/*
	$('#sucess_buy_'+idproduct).show(500);

	$('#loading_buy_'+idproduct).hide(2000);
	*/

	$title_book='';
	
	$cont_index.=ob_get_contents();
	
	ob_clean();
	
	$query=$model['book']->select('where IdBook='.$_GET['IdBook'], array('IdBook', 'title'));
	
	list($idbook, $title_book)=webtsys_fetch_row($query);
	
	settype($idbook, 'integer');
	
	if($idbook>0)
	{
	
		list($arr_list_father, $arr_cat)=obtain_parent_list('documentation', 'title', 'parent', 'where idbook='.$_GET['IdBook'].' order by position ASC');
			
		$url_doc=make_fancy_url($base_url, 'documentation', 'readbook', $title_book, array('IdBook' => $_GET['IdBook']) );
		
		/*
		Javascript for 
		*/
		
		?>
		<div id="show_menu"></div><p id="show_menu_error" style="display:none;"></div>
		<?php
			
		recursive_list('documentation', $arr_cat, $arr_list_father, 0, $url_doc, $arr_perm=array());
		
		$query=$model['documentation']->select('where documentation.idbook='.$_GET['IdBook'].' and documentation.IdDocumentation='.$_GET['IdDocumentation']);
		
		$arr_doc=webtsys_fetch_array($query);
		
		settype($arr_doc['IdDocumentation'], 'integer');
		
		if($arr_doc['IdDocumentation']>0)
		{
		
			?>
			<h1><?php echo $arr_doc['title']; ?></h1>
			<p></p>
			<?php echo $arr_doc['content']; ?>
			<?php
			//print_r($arr_list_father);
			//Here, Prev - Home - Next
			//Check son, if son, next is son, if not son, next if next in array, if not next in array, next for father.
			
			$next_link=$lang['documentation']['end_book'];
			
			$pos_arr_list=array_search($arr_doc['IdDocumentation'], $arr_list_father[$arr_doc['parent']]);
			
			if( isset($arr_list_father[$arr_doc['IdDocumentation']]) )
			{
			
				reset($arr_list_father[$arr_doc['IdDocumentation']]);
				
				$iddocumentation=current($arr_list_father[$arr_doc['IdDocumentation']]);
				
				$query=$model['documentation']->select('where documentation.IdDocumentation='.$iddocumentation, array('title'));
				
				list($title_next)=webtsys_fetch_row($query);
			
				$next_link='<a href="'.make_fancy_url($base_url, 'documentation', 'readbook', $title_next, array('IdBook' => $_GET['IdBook'], 'IdDocumentation' => $iddocumentation) ).'">'.$title_next.'</a>';
			
			}
			else
			if( count( $arr_list_father[$arr_doc['parent']] ) == ($pos_arr_list+1))
			{
				//Is the last in the docs with the same parent...
				//Obtain next from parent...
				
				//print_r($arr_list_father[$arr_doc['parent']]);
				//Need obtain id for parent of my parent...
				
				$query=$model['documentation']->select('where IdDocumentation='.$arr_doc['parent'], array('parent'));
				
				list($idparent)=webtsys_fetch_row($query);
				settype($idparent, 'integer');
				$pos_arr_list_parent=array_search($arr_doc['parent'], $arr_list_father[$idparent]);
				
				if($pos_arr_list_parent!==false)
				{
				
					$next_to_parent=$pos_arr_list_parent+1;
					
					if(isset($arr_list_father[$idparent][$next_to_parent]))
					{
					
						$query=$model['documentation']->select('where documentation.IdDocumentation='.$arr_list_father[$idparent][$next_to_parent], array('title'));
					
						list($title_next)=webtsys_fetch_row($query);
						
						$next_link='<a href="'.make_fancy_url($base_url, 'documentation', 'readbook', $title_next, array('IdBook' => $_GET['IdBook'], 'IdDocumentation' => $arr_list_father[$idparent][$next_to_parent]) ).'">'.$title_next.'</a>';
						
					}
				}
			
			}
			else
			if( count( $arr_list_father[$arr_doc['parent']] ) > ($pos_arr_list+1))
			{
			
				$next_to_parent=$pos_arr_list+1;
				
				$query=$model['documentation']->select('where documentation.IdDocumentation='.$arr_list_father[$arr_doc['parent']][$next_to_parent], array('title'));
				
				list($title_next)=webtsys_fetch_row($query);
					
				$next_link='<a href="'.make_fancy_url($base_url, 'documentation', 'readbook', $title_next, array('IdBook' => $_GET['IdBook'], 'IdDocumentation' => $arr_list_father[$arr_doc['parent']][$next_to_parent]) ).'">'.$title_next.'</a>';
			
			}
			
			//Now prev link
			
			//Check if prev with same parent, if yes, check if this prev have son, if have son, prev is the last son.
			
			$set_first_chapter=$_SESSION['show_menu_var'];
			
			$prev_link=' <a href="'.make_fancy_url($base_url, 'documentation', 'readbook', $title_book, array('IdBook' => $_GET['IdBook'])).'">'.$lang['documentation']['first_chapter_book'].'</a>';
			
			if(($pos_arr_list)>0)
			{
			
				//$prev_link='hava chapter last';
				
				$pos_arr_list_prev=$pos_arr_list-1;
				
				$iddocumentation_prev=$arr_list_father[$arr_doc['parent']][$pos_arr_list_prev];
				
				$query=$model['documentation']->select('where documentation.IdDocumentation='.$iddocumentation_prev, array('title'));
				
				list($title_prev)=webtsys_fetch_row($query);
				
				$prev_link=' <a href="'.make_fancy_url($base_url, 'documentation', 'readbook', $title_prev, array('IdBook' => $_GET['IdBook'], 'IdDocumentation' => $iddocumentation_prev)).'">'.$title_prev.'</a>';
				
				//If prev with same parent have son is last son
				
				if( isset( $arr_list_father[$iddocumentation_prev] ) )
				{
				
					//$prev_link='hava chapter last son chapter';
					
					//print_r($arr_list_father[$iddocumentation_prev]);
					
					end($arr_list_father[$iddocumentation_prev]);
					
					$iddocumentation_prev=current($arr_list_father[$iddocumentation_prev]);
					
					$query=$model['documentation']->select('where documentation.IdDocumentation='.$iddocumentation_prev, array('title'));
				
					list($title_prev)=webtsys_fetch_row($query);
					
					$prev_link=' <a href="'.make_fancy_url($base_url, 'documentation', 'readbook', $title_prev, array('IdBook' => $_GET['IdBook'], 'IdDocumentation' => $iddocumentation_prev)).'">'.$title_prev.'</a>';
				
				}
				
				//$set_first_chapter=0;
			
			}
			else if($arr_doc['parent']>0 && ($pos_arr_list)==0)
			{
			
				//$prev_link='hava chapter last son';
				
				$query=$model['documentation']->select('where documentation.IdDocumentation='.$arr_doc['parent'], array('title'));
				
				list($title_prev)=webtsys_fetch_row($query);
				
				$prev_link=' <a href="'.make_fancy_url($base_url, 'documentation', 'readbook', $title_prev, array('IdBook' => $_GET['IdBook'], 'IdDocumentation' => $arr_doc['parent'])).'">'.$title_prev.'</a>';
				
				//$set_first_chapter=0;
				
			
			}
			
			
			echo '<hr /><p>'.$prev_link.' - <a href="'.make_fancy_url($base_url, 'documentation', 'readbook', $title_book, array('IdBook' => $_GET['IdBook'])).'">'.$lang['common']['home'].'</a> - '.$next_link.'</p>';
		
		}
	
	}
	else
	{
	
		echo '<p>'.$lang['documentation']['no_exists_book'].'</p>';
	
	}
	
	/*
	?>
	<ul>
		<?php
		while(list($idbook, $title)=webtsys_fetch_row($query))
		{
		
			$url_opt=make_fancy_url($base_url, 'documentation', 'readbook', $title, array('IdBook' => $idbook) );
		
			?>
			<li><a href="<?php echo $url_opt; ?>"><?php echo $title; ?></a></li>
			<?php
		
		}
		
		?>
	</ul>
	<?php*/
	
	
	
	$cont_books=ob_get_contents();
	
	ob_clean();
	
	echo load_view(array($title_book, $cont_books), 'content');
	
	?>
	<script language="Javascript">
		
		function show_menu(show_var, time)
		{
			
			//$('#list_ul').hide();
			
			//alert($('#list_ul').css('display'));
		
			if(show_var==0)
			{
			
				$("#show_menu").html('<a href="#" onclick="show_menu(1, 500)"><?php echo $lang['documentation']['show_menu']; ?></a>');
				$('#list_ul').hide(time);
			
			}
			else
			if(show_var==1)
			{
			
				$("#show_menu").html('<a href="#" onclick="show_menu(0, 500)"><?php echo $lang['documentation']['hidden_menu']; ?></a>');
				$('#list_ul').show(time);
			
			}
			
			set_show_menu_ajax(show_var);
			
			return false;
			
		}
		
		function set_show_menu_ajax(show_menu_var)
		{
		
			$.ajax({
				url: "<?php echo make_fancy_url($base_url, 'documentation/ajax', 'setsessionmenu', 'setsessionmenu', array() ); ?>show_menu_var/"+show_menu_var,
				type: "GET",
				dataType: "json",
				error: function(data){
					
					$("#show_menu_error").show();
					$("#show_menu_error").html('<?php echo $lang['documentation']['error_cannot_access_to_ajax']; ?>');
				}
			});
		
		}
		
		show_menu(<?php echo $set_first_chapter; ?>, 0);
	</script>
	<?php

	$cont_index.=ob_get_contents();

	ob_end_clean();

	echo load_view(array($title_book, $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>
