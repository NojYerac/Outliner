<?php
require_once('config.php');
require_once('db.php');
require_once('node.php');


if (isset($_FILES)) {
	$file = $_FILES['file'];
	var_dump($file);
	if ($file['name'][0] === 'contents.xml') {
		move_uploaded_file($file['tmp_name'][0], 'upload/' . $file['name'][0]);
	}
}
		/*$uri = 'compress.zlib://' . $file['tmp_name'][0];
		$xml = new XMLReader();
		$xml->open($uri);
		while (true) {
			$node = $xml->expand();
			echo "<p><b>" . get_class($node) . "</b><br/>";
			echo $node->nodeName . "<br/>";
			if ($node->nodeName === '#text') {
				//var_dump($node);
				echo $node->wholeText . "<br/>";
			}
			echo("</p>");
			if (!$xml->read()) {break;}
		}*/

		/*while ($xml->read()) {
			switch ($xml->name) {
				case 'style':
					$style = extract_style_values($xml->expand());
					while($xml->read() && $xml->name !== 'style');
					var_dump($style);
					break;
				case 'lit':
					break;
				default:
					break;
			}*/ /*
		//$node = $xml->expand();
		//$dom = new DOMDocument($node);
			echo '<pre>' . htmlentities($xml->readOuterXML()) . '</pre>';
		}
	}

}*/

function extract_style_values(object $dom) {
	$style = array();


}



$file_input_label = array(
	'tag' => 'label',
	'text' => 'File: ',
	'for' => 'file'
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
