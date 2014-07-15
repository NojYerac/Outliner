<?php
require_once('../config.php');
require_once('../db.php');

$parent_node_id = $_POST['parent_node_id'];

$child_node = array(
		'title' => array(
				'css' => array(
					'color' => '#000'
				),
			'text' => 'New node'
		),
		'note' => array(
			'css' => array(
					'color' => '#000'
			),
			'text' => ''
		),
		'subdoc' => '',
		'nodes' => array()
);

$child_id = insert_one_document('nodes', $child_node);
$child_node['id'] = (string)$child_id;
modify_one_document(
		'nodes',
		array('_id' => new MongoId($parent_node_id)),
		array('$push' => array('nodes' => $child_node['id']))
);

header('Content-type: application/json; charset=utf-8');
echo JSON_encode($child_node);
?>
