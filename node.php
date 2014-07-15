<?php
require_once('config.php');
require_once('db.php');

function array2tag(array $array,string $tag=Null) {
	$attributes = '';
	if (isset($array['css'])) {
		if (count($array['css']) > 0) {
			$style = '';
			foreach ($array['css'] as $key => $value) {
				$style .= htmlentities($key) . ':' . htmlentities($value) . ';';
			}
			$attributes .= ' style="' . $style . '" ';
		}
		unset($array['css']);
	}
	if (isset($array['tag']) && $tag === Null) {
		$tag = $array['tag'];
		unset($array['tag']);
	}
	if ($tag != Null && is_string($tag)) {
		$tag = htmlentities(preg_replace('/[\/\s\\\]+/', '', $tag)); //]/'
	} else {
		throw(new Exception("Ambiguous tag"));
	}
	if (isset($array['text'])) {
		if (is_string($array['text'])) {
			$text_content = htmlentities($array['text']);
			unset($array['text']);
		} else {
			throw(new Exception("value of 'text' must be a string"));
		}
		unset($array['text']);
	} else {
		$text_content = '';
	}
	$child_nodes = '';
	if (isset($array['child_nodes'])) {
		if (is_array($array['child_nodes']) && count($array['child_nodes']) > 0) {
			foreach($array['child_nodes'] as $child_node) {
				$child_nodes .= $child_node?array2tag($child_node):'';
			}
			unset($array['child_nodes']);
		} else {
			throw(new Exception("value of 'child_nodes' must be an array"));
		}
	}
	if ($child_nodes || $text_content || preg_match('/^(span|script)$/i', $tag)) {
		$innerHTML = $text_content . $child_nodes;
	}
	if (isset($array['_id'])) {
		if (!isset($array['id'])) {
			$array['id'] = (string)$array['_id'];
		}
		unset($array['_id']);
	}
	foreach ($array as $attribute => $value) {
		$attributes .= ' ' . htmlentities($attribute) . '="' . htmlentities($value) . '"';
	}
	return "<$tag$attributes" . (isset($innerHTML)?">$innerHTML</$tag>":"/>");
}

function node2array($node_id) {
	$node = get_one_document('nodes', array('_id' => new MongoId($node_id)));
	if (!$node) {throw(new Exception($node_id));}
	$array = array(
		'tag' => 'li',
		'class' => 'node_li',
		'id' => "node_li_" . $node_id
	);
	$array['child_nodes'] = array(
		array_merge(
			array(
				'tag' => 'span',
				'id' => 'node_title_' . $node_id,
				'class' => 'node_title'
			),
			$node['title']
		),
		array_merge(
			array(
				'tag' => 'span',
				'id' => 'node_note_' . $node_id,
				'class' => 'node_note'
			),
			$node['note']
		),
		array(
			'tag' => 'div',
			'id' => 'node_subdoc_' . $node_id,
			'class' => 'node_subdoc hidden',
			'child_nodes' => array(
				array(
					'text' => $node['subdoc'],
					'tag' => 'pre',
					'class' => 'node_subdoc'
				)
			)
		)
	);
	/*$child_nodes[] = array(
		'tag' => 'li',
		'id' => 'add_child_node_' . $node_id,
		'class' => 'add_child_node'
	);*/
	if (isset($node['nodes']) && count($node['nodes']) > 0) {
		foreach ($node['nodes'] as $sub_node_id) {
			$child_nodes[] = node2array($sub_node_id);
		}
		$array['child_nodes'][] = array(
			'tag' => 'ul',
			'id' => 'node_ul_' . $node_id,
			'class' => 'node_ul',
			'child_nodes' => $child_nodes
		);
	}

	return $array;
}

?>
