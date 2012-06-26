<?php

function DocumentationAdmin()
{

	global $lang, $base_url, $model, $base_path;
	
	settype($_GET['op'], 'integer');
	settype($_GET['parent'], 'integer');
	settype($_GET['IdBook'], 'integer');
	settype($_GET['IdDocumentation'], 'integer');

	load_lang('documentation');
	load_libraries(array('generate_admin_ng', 'utilities/parent_utilities'));
	load_model('documentation');
	
	switch($_GET['op'])
	{
	
		default:
		
			?>
			<h3><?php echo $lang['documentation']['edit_books']; ?></h3>
			<?php
		
			$where_sql='';
		
			$url_options=make_fancy_url($base_url, 'admin', 'index', 'admin_docs', array('IdModule' => $_GET['IdModule']));

			$arr_fields=array('title');
			$arr_fields_edit=array();

			generate_admin_model_ng('book', $arr_fields, $arr_fields_edit, $url_options, $options_func='DocumentOptionsListModel', $where_sql, $arr_fields_form=array(), $type_list='Basic');
		
		break;
	
		case 1:
		
			load_libraries(array('forms/selectmodelformbyorder', 'forms/textareabb'));
			
			$query=$model['book']->select('where IdBook='.$_GET['IdBook'], array('title'));
			
			list($title_book)=webtsys_fetch_row($query);
	
			?>
			<h3><?php echo $lang['documentation']['documentation_index']; ?> - <?php echo $title_book;?></h3>
			<?php
			
			$model['documentation']->create_form();
			
			$model['documentation']->forms['content']->form='TextAreaBBForm';
			
			$model['documentation']->forms['idbook']->form='HiddenForm';
			$model['documentation']->forms['idbook']->SetForm($_GET['IdBook']);
			
			//SelectModelFormByOrder($name, $class, $value, $model_name, $identifier_field, $field_parent, $where='')
			$model['documentation']->forms['parent']->form='SelectModelFormByOrder';
			$model['documentation']->forms['parent']->parameters=array('parent', '', $_GET['IdDocumentation'], 'documentation', 'title', 'parent');
			
			list($arr_list_father, $arr_cat)=obtain_parent_list('documentation', 'title', 'parent', 'where idbook='.$_GET['IdBook'].' order by position ASC');
			
			$url_doc=make_fancy_url($base_url, 'admin', 'index', 'edit_son_docs', array('IdModule' => $_GET['IdModule'], 'IdBook' => $_GET['IdBook'], 'op' => 1) );
			
			recursive_list('documentation', $arr_cat, $arr_list_father, 0, $url_doc, $arr_perm=array());
			
			//Now administrator...
			
			$where_sql='where parent='.$_GET['IdDocumentation'].' and idbook='.$_GET['IdBook'];
			
			$url_options=make_fancy_url($base_url, 'admin', 'index', 'admin_docs', array('IdModule' => $_GET['IdModule'], 'IdDocumentation' => $_GET['IdDocumentation'], 'IdBook' => $_GET['IdBook'], 'op' => 1));

			$arr_fields=array('title', 'position');
			$arr_fields_edit=array('title', 'content', 'parent', 'idbook');
			
			//?order_field=position&order_desc=0&search_word=&search_field=IdDocumentation
			
			if(!isset($_GET['search_field']) && !isset($_GET['order_field']))
			{
			
				$_GET['order_field']='position';
			
			}

			generate_admin_model_ng('documentation', $arr_fields, $arr_fields_edit, $url_options, $options_func='ChapterOptionsListModel', $where_sql, $arr_fields_form=array(), $type_list='Basic');
			
			//http://localhost/phangodev/index.php/admin/show/index/edit_son_docs/IdModule/14/IdBook/5/op/1/IdDocumentation/2
			
			echo '<p><a href="'.make_fancy_url($base_url, 'admin', 'index', 'edit_position_docs', array('IdModule' => $_GET['IdModule'], 'IdDocumentation' => $_GET['IdDocumentation'], 'IdBook' => $_GET['IdBook'], 'op' => 2) ).'">'.$lang['documentation']['modify_order_chapters'].'</a></p>';
			
			echo '<p><a href="'.make_fancy_url($base_url, 'admin', 'index', 'edit_son_docs', array('IdModule' => $_GET['IdModule']) ).'">'.$lang['documentation']['go_to_index_books'].'</a></p>';
		
		break;
		
		case 2:
		
			$query=$model['documentation']->select('where IdDocumentation='.$_GET['IdDocumentation'], array('title'));
			
			list($title_doc)=webtsys_fetch_row($query);
			
			if($title_doc=='')
			{
			
				$title_doc=$lang['common']['home'];
			
			}
			
			$url=make_fancy_url($base_url, 'admin', 'index', 'edit_position_docs', array('IdModule' => $_GET['IdModule'], 'IdDocumentation' => $_GET['IdDocumentation'], 'IdBook' => $_GET['IdBook'], 'op' => 2) );
			
			$url_back=make_fancy_url($base_url, 'admin', 'index', 'edit_position_docs', array('IdModule' => $_GET['IdModule'], 'IdDocumentation' => $_GET['IdDocumentation'], 'IdBook' => $_GET['IdBook'], 'op' => 1) );
		
			echo '<h3>'.$lang['documentation']['modify_order_chapters'].' - '.$title_doc.'</h3>';
			
			GeneratePositionModel('documentation', 'title', 'position', $url, $where='where parent='.$_GET['IdDocumentation']);
			
			echo '<p><a href="'.$url_back.'">'.$lang['common']['go_back'].'</a>';
		break;
		
	}

}

function DocumentOptionsListModel($url_options, $model_name, $id)
{

	global $lang, $base_url;

	$arr_options=BasicOptionsListModel($url_options, $model_name, $id);

	$arr_options[]='<a href="'.make_fancy_url($base_url, 'admin', 'index', 'admin_docs', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'IdBook' => $id)).'">'.$lang['documentation']['edit_chapters'].'</a>';

	return $arr_options;

}

function ChapterOptionsListModel($url_options, $model_name, $id)
{

	global $lang, $base_url;

	$arr_options=BasicOptionsListModel($url_options, $model_name, $id);

	$arr_options[]='<a href="'.make_fancy_url($base_url, 'admin', 'index', 'admin_docs', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'IdBook' => $_GET['IdBook'], 'IdDocumentation' => $id)).'">'.$lang['documentation']['edit_son_docs'].'</a>';

	return $arr_options;

}

?>