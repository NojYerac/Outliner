<?php
require_once('../config.php');
require_once('../db.php');

$node_id = $_POST['node_id'];

$node = get_one_document(
		'nodes',
		array('_id' => new MongoId($node_id))
);

header('Content-type: application/json; charset=utf-8');
echo JSON_encode($node);
?>
