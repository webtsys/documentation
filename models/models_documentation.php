<?php

$model['book']=new Webmodel('book');
$model['book']->components['title']=new CharField(255);
$model['book']->components['title']->required=1;

$model['documentation']=new Webmodel('documentation');

$model['documentation']->components['title']=new CharField(255);
$model['documentation']->components['title']->required=1;
$model['documentation']->components['content']=new TextHTMLField();
$model['documentation']->components['content']->required=1;
$model['documentation']->components['parent']=new ParentField('documentation');
$model['documentation']->components['idbook']=new ForeignKeyField('book');
$model['documentation']->components['idbook']->required=1;
$model['documentation']->components['position']=new IntegerField();

$arr_module_insert['documentation']=array('name' => 'documentation', 'admin' => 1, 'admin_script' => array('documentation', 'documentation'), 'load_module' => '', 'order_module' => 0, 'required' => 0, 'app_index' => 1);

$arr_module_remove['documentation']=array('documentation');

?>