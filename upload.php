<?php
require_once('config.php');
require_once('db.php');
require_once('node.php');

if (isset($_FILES)) {
	foreach($_FILES as $file) {
		var_dump($file);
		if ($file['name'][0] === 'content.xml') {
			$uri = 'compress.zlib://' . $file['tmp_name'][0];
			$xml = new XMLReader();
			$xml->open($uri);
			while ( $xml->read() ) {
				echo($xml->name . "<br/>");
			}
		}
	}
}


$file_input_label = array(
	'tag' => 'label',
	'text' => 'File: ',
	'for' => 'file[]'
);

$file_input = array(
	'tag' => 'input',
	'type' => 'file',
	'multiple' => 'multiple',
	'name' => 'file[]'
);

$submit = array(
	'tag' => 'input',
	'type' => 'submit',
	'value' => 'Upload',
);

$form = array(
	'tag' => 'form',
	'method' => 'post',
	'enctype' => 'multipart/form-data',
	'child_nodes' => array(
		$file_input_label,
		$file_input,
		$submit
	)
);

echo array2tag($form);

?>
