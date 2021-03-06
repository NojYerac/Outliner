<?php
require_once('config.php');
require_once('db.php');
require_once('node.php');

if (isset($_REQUEST['outline_id'])) {
	$outline_id = htmlentities($_REQUEST['outline_id']);

	$outline = get_one_document('outlines', array('_id' => new MongoId($outline_id)));
	$title = $outline['title'];
	$root = array(
		'tag' => 'ul',
		'id' => 'outline_root_' . $outline_id,
		'class' => 'outline_root'
	);

	foreach ($outline['nodes'] as $node_id) {
		$root['child_nodes'][] = node2array($node_id);
	}

} else {
	$title = "Outlines";
	$outlines = get_all_documents('outlines', array());
	$links = array();
	foreach($outlines as $outline) {
		$outline_id = htmlentities($outline['_id']);
		$link = array(
			'tag' => 'a',
			'class' => 'outline_link',
			'id' => 'outline_link_' . $outline_id,
			'text' => htmlentities($outline['title']),
			'href' => '?outline_id=' . $outline_id
		);
		$metadata = array(
			'tag' => 'input',
			'type' => 'hidden',
			'value' => htmlentities(JSON_encode($outline)),
			'id' => 'outline_metadata_' . $outline_id,
			'class' => 'outline_metadata'
		);
		$clone_button = array(
			'tag' => 'button',
			'text' => 'Clone',
			'id' => 'clone_button_' . $outline_id,
			'class' => 'clone_button'
		);
		$delete_button = array(
			'tag' => 'button',
			'text' => 'Delete',
			'id' => 'delete_button_' . $outline_id,
			'class' => 'delete_button'
		);
		$buttons = array(
			'tag' => 'div',
			'id' => 'button_div_' . $outline_id,
			'class' => 'button_div',
			'child_nodes' => array($clone_button, $delete_button)
		);
		$links[] = array(
			'tag' => 'li',
			'class' => 'outline_link_li',
			'id' => 'outline_link_li_' . $outline_id,
			'child_nodes' => array($link, $metadata, $buttons)
		);
	}
	$root = array(
		'tag' => 'ul',
		'child_nodes' => $links,
		'id' => 'link_list',
		'class' => 'link_list'
	);
}

$head = array(
	'tag' => 'head',
	'child_nodes' => array(
		array(
			'tag' => 'title',
			'text' => $title
		),
		array(
			'tag' => 'link',
			'rel' => 'stylesheet',
			'type' => 'text/css',
			'href' => '/css/default.css'
		),
		array(
			'tag' => 'script',
			'src' => '/js/default.js',
			'type' => 'text/javascript'
		)
	)
);

$header = array(
	'tag' => 'div',
	'id' => 'header_div',
	'class' => 'header_placeholder',
	'child_nodes' => array(
		array(
			'tag' => 'div',
			'class' => 'header',
			'child_nodes' => array(
				array(
					'tag' => 'h1',
					'text' => $title
				)
			)
		)
	)
);

$body = array(
	'tag' => 'body',
	'child_nodes' => array(
		$header,
		$root,
		array(
			'tag' => 'script',
			'src' => '/js/outline.js',
			'type' => 'text/javascript'
		)

	)
);

$html = array(
	'tag' => 'html',
	'child_nodes' => array(
		$head,
		$body
	)
);

$decl = "<!DOCTYPE html>\n";
header('Content-type: text/html; charset=utf-8');
echo($decl . array2tag($html));
?>
