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

function remove_node($node_id, $parent, $parent_id) {
	$node = get_one_document('nodes', array('_id' => new MongoId($node_id)));
	$removed['node'] = $node_id;
	$child_nodes = $node['nodes'];
	delete_one_document('nodes', array('_id' => $node['_id']));
	if ($parent) {
		modify_one_document(
			$parent,
			array('_id' => new MongoId($parent_id)),
			array('$pull' => array('nodes' => $new_node_id))
		);
	}
	foreach ($child_nodes as $child_node) {
		$removed['children'][] = remove_node($child_node, false, false);
	}
	return $removed;
}

function remove_outline($outline_id) {
	$outline = get_one_document('outlines', array('_id' => new MongoId($outline_id)));
	$removed['outline'] = $outline_id;
	$child_nodes = $outline['nodes'];
	delete_one_document('outlines', array('_id' => $outline['_id']));
	foreach($child_nodes as $child_node) {
		$removed['children'][] = remove_node($child_node, false, false);
	}
	return $removed;
}

$target = $_REQUEST['target'];
$target_id = $_REQUEST['target_id'];
if ($target === 'node') {
	$parent = $_REQUEST['parent'];
	$parent_id = $_REQUEST['parent_id'];
	$result['removed'] = remove_node($target_id, $parent, $parent_id);
} else {
	$result['removed'] = remove_outline($target_id);
}

header('Content-type: application/json; charset=utf-8');
echo JSON_encode($result);

?>
