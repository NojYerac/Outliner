<?php
require_once('../config.php');
require_once('../db.php');

if (!isset($_REQUEST['target']) || !isset($_REQUEST['target_id']) ||
	!in_array($_REQUEST['target'], array('outlines', 'nodes')) ||
	!preg_match('/^[0-9a-f]{24}$/', $_REQUEST['target_id']) ||
	($_REQUEST['target'] === 'nodes' && (
		!isset($_REQUEST['parent']) || !isset($_REQUEST['parent_id']) ||
		!in_array($_REQUEST['parent'], array('outlines', 'nodes')) ||
		!preg_match('/^[0-9a-f]{24}$/', $_REQUEST['parent_id']))
	)
) {
	var_dump($_REQUEST);
	throw(new Exception('Bad/Missing target parameter'));
}

function clone_node($node_id, $parent, $parent_id, $changes=array()) {
	$new_node = get_one_document('nodes', array('_id' => new MongoId($node_id)));
	unset($new_node['_id']);
	foreach ($changes as $key => $value) {
		if (!isset($new_node[$key])) {throw(new Exception('Illegal key'));}
		$new_node[$key] = $value;
	}
	$child_nodes = $new_node['nodes'];
	unset($new_node['nodes']);
	$new_node_id = (string)insert_one_document('nodes', $new_node);
	modify_one_document($parent, array('_id' => new MongoId($parent_id)), array('$push' => array('nodes' => $new_node_id)));
	foreach ($child_nodes as $child_node) {
		$new_node['children'][] = clone_node($child_node, 'nodes', $new_node_id);
	}
	return $new_node;
}

function clone_outline($outline_id, $changes=array()) {
	$new_outline = get_one_document('outlines', array('_id' => new MongoId($outline_id)));
	unset($new_outline['_id']);
	foreach ($changes as $key => $value) {
		if (!isset($new_outline[$key])) {throw(new Exception('Illegal key'));}
		$new_outline[$key] = $value;
	}
	$child_nodes = $new_outline['nodes'];
	unset($new_outline['nodes']);
	$new_outline_id = (string)insert_one_document('outlines', $new_outline);
	foreach($child_nodes as $child_node) {
		$new_outline['children'][] = clone_node($child_node, 'outlines', $new_outline_id);
	}
	return $new_outline;
}

$target = $_REQUEST['target'];
$target_id = $_REQUEST['target_id'];
if ($target === 'node') {
	$parent = $_REQUEST['parent'];
	$parent_id = $_REQUEST['parent_id'];
	$result = clone_node($target_id, $parent, $parent_id);
} else {
	$result = clone_outline($target_id);
}

header('Content-type: application/json; charset=utf-8');
echo JSON_encode($result);

?>
