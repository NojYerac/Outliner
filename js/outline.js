(function() {

function addStyle(elem, styles) {
		var styleString = '';
		for (var key in styles) {
				styleString += key + ':' + styles[key] + ';';
		}
		elem.setAttribute('style', styleString);
}

function editNodeDialog(id) {
		var dialogBox = document.createElement('div');
		dialogBox.id = 'edit_node_dialog';
		dialogBox.className = 'feature-box center';
		var titleText = document.createElement('input');
		titleText.id = 'title_text';
		titleText.name = 'title_text';
		titleText.className = 'edit_node';
		var titleTextLabel = document.createElement('label');
		titleTextLabel.setAttribute('for', 'text_title');
		titleTextLabel.appendChild(document.createTextNode('Title: '));
		dialogBox.appendChild(titleTextLabel);
		dialogBox.appendChild(titleText);
		var titleColor = document.createElement('input');
		titleColor.id = 'title_color';
		titleColor.name = 'title_color';
		titleColor.type = 'color';
		titleColor.addEventListener(
						'change',
						function() {
								titleText.style.color = this.value;
						}
				);
		dialogBox.appendChild(titleColor);
		dialogBox.appendChild(document.createElement('br'));
		var noteText = document.createElement('input');
		noteText.id = 'note_text';
		noteText.name = 'note_text';
		noteText.className = 'edit_node';
		var noteTextLabel = document.createElement('label');
		noteTextLabel.setAttribute('for', 'text_note');
		noteTextLabel.appendChild(document.createTextNode('Note: '));
		dialogBox.appendChild(noteTextLabel);
		dialogBox.appendChild(noteText);
		var noteColor = document.createElement('input');
		noteColor.id = 'note_color';
		noteColor.name = 'note_color';
		noteColor.type = 'color';
		noteColor.addEventListener(
						'change',
						function() {
								noteText.style.color = this.value;
						}
				);
		dialogBox.appendChild(noteColor);
		dialogBox.appendChild(document.createElement('br'));
		var subDocText = document.createElement('textarea');
		subDocText.setAttribute('cols', '80');
		subDocText.setAttribute('rows', '20');
		dialogBox.appendChild(subDocText);
		dialogBox.appendChild(document.createElement('br'));
		var saveButton = document.createElement('button');
		saveButton.appendChild(document.createTextNode('Save'));
		saveButton.addEventListener(
				'click',
				function() {
					var params = 'node_id=' + id +
						'&title[text]=' + titleText.value +
						'&title[css][color]=' + titleColor.value +
						'&note[text]=' +noteText.value +
						'&note[css][color]=' + noteColor.value +
						'&subdoc=' + subDocText.value;
					var xhr = new XMLHttpRequest();
					xhr.addEventListener(
							'load',
							function() {
									var resp = JSON.parse(xhr.responseText);
									alert(resp);
							}
							);
					xhr.open(
							'POST',
							'api/edit_node.php',
							true
							);
					xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
					xhr.send(params);
					window.requestAnimationFrame(
									function(x) {
											dialogBox.parentNode.removeChild(dialogBox);
									}
									);
				},
				false
				);
		dialogBox.appendChild(saveButton);
		var cancleButton = document.createElement('button');
		cancleButton.appendChild(document.createTextNode('Cancle'));
		cancleButton.addEventListener(
				'click',
				function() {
					window.requestAnimationFrame(
							function(x) {
									dialogBox.parentNode.removeChild(dialogBox);
							}
					)
				}
				);
		dialogBox.appendChild(cancleButton);
		window.requestAnimationFrame(
						function(x) {document.body.appendChild(dialogBox);}
						);
		var xhr = new XMLHttpRequest();
		xhr.addEventListener(
			'load',
			function() {
				var resp = JSON.parse(xhr.responseText);
				window.requestAnimationFrame(
					function(x) {
						titleText.value = resp['title']['text'];
						titleColor.value = resp['title']['css']['color'];
						noteText.value = resp['note']['text'];
						noteColor.value = resp['note']['css']['color'];
						subDocText.value = resp['subdoc'];
					}
				);
			}
		);
		xhr.open(
			'POST',
			'api/get_node.php',
			true
		);
		xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhr.send('node_id=' + id);
}

var linkList = document.getElementById('link_list');
if (linkList) {
	linkList.addEventListener(
		'click',
		function(event) {
			var target = event.target;
			if (target.className === 'clone_button') {
				var params = "target=outlines&target_id=" +
					target.id.slice(-24);
				xhr = new XMLHttpRequest();
				xhr.addEventListener(
					'load',
					function() {
						var resp = JSON.parse(xhr.responseText);
						var outlineId = resp['_id']['$id'];
						var outlineTitle = resp['title'];
						var linkLi = document.createElement('li');
						linkLi.id = 'outline_link_li_' + outlineId;
						linkLi.className = 'outline_link_li';
						var linkA = document.createElement('a');
						linkA.id = 'outline_link_' +outlineId;
						linkA.className = 'outline_link';
						linkA.appendChild(document.createTextNode(outlineTitle));
						linkA.href = '?outline_id=' + outlineId;
						linkLi.appendChild(linkA);
						var metaData = document.createElement('input');
						metaData.type = 'hidden';
						metaData.id = 'outline_metadata_' + outlineId;
						metaData.className = 'outline_metadata';
						metaData.value = xhr.responseText;
						linkLi.appendChild(metaData);
						var buttonDiv = document.createElement('div');
						buttonDiv.id = 'button_div_' + outlineId;
						buttonDiv.className = 'button_div';
						var cloneButton = document.createElement('button');
						cloneButton.id = 'clone_button_' + outlineId;
						cloneButton.className = 'clone_button';
						cloneButton.appendChild(document.createTextNode('Clone'));
						buttonDiv.appendChild(cloneButton);
						var deleteButton = document.createElement('button');
						deleteButton.id = 'delete_button_' + outlineId;
						deleteButton.className = 'delete_button';
						deleteButton.appendChild(document.createTextNode('Delete'));
						buttonDiv.appendChild(deleteButton);
						linkLi.appendChild(buttonDiv);
						window.requestAnimationFrame(
							function() {
								linkList.appendChild(linkLi);
							}
						);
					}
				);
				xhr.open(
					'POST',
					'api/clone.php',
					true
				);
				xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xhr.send(params);
			} else if (target.className === 'delete_button') {
				var params = "target=outlines&target_id=" + target.id.slice(-24);
				xhr = new XMLHttpRequest();
				xhr.addEventListener(
					'load',
					function() {
						var resp = JSON.parse(xhr.responseText);
						var outlineId = resp['removed']['outline'];
						var deletedLi = document.getElementById(
							'outline_link_li_' + outlineId
							);
						window.requestAnimationFrame(
							function(x) {
								deletedLi.parentNode.removeChild(deletedLi);
							}
						);
					}
				);
				xhr.open(
					'POST',
					'api/remove.php',
					true
				);
				xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xhr.send(params);
			}
		},
		false
	);
}

var outlineRoot = document.getElementsByClassName('outline_root')[0];
if (outlineRoot) {
outlineRoot.addEventListener(
		'click',
		function(event) {
			var target = event.target;
			if (target.className === 'add_child_node') {
				var parentNodeId = target.id.slice(-24);
				var params = 'parent_node_id=' + parentNodeId;
				xhr = new XMLHttpRequest();
				xhr.addEventListener(
					'load',
					function() {
						var resp = JSON.parse(xhr.responseText);
						var newLi = document.createElement('li');
						newLi.id = 'node_li_' + resp['id'];
						newLi.className = 'node_li';
						var newLiTitle = document.createElement('span');
						newLiTitle.id = 'node_title_' + resp['id'];
						newLiTitle.className = 'node_title';
						newLiTitle.appendChild(
								document.createTextNode(resp['title']['text'])
								);
						var styles = resp['title']['css'];
						addStyle(newLiTitle, styles);
						newLi.appendChild(newLiTitle);
						var newLiNote = document.createElement('span');
						newLiNote.id = 'node_note_' + resp['id'];
						newLiNote.className = 'node_note';
						newLiNote.appendChild(
								document.createTextNode(resp['note']['text'])
								);
						styles = resp['note']['css'];
						addStyle(newLiNote, styles);
						newLi.appendChild(newLiNote);
						var newLiSubdoc = document.createElement('div');
						newLiSubdoc.id = 'node_subdoc_' + resp['id'];
						newLiSubdoc.className = 'node_subdoc hidden';
						newLi.appendChild(newLiSubdoc);
						var subdocPre = document.createElement('pre');
						subdocPre.className = 'node_subdoc';
						subdocPre.appendChild(document.createTextNode(resp['subdoc']));
						newLiSubdoc.appendChild(subdocPre);
						var newLiUl = document.createElement('ul');
						newLiUl.id = 'node_ul_' + resp['id'];
						newLiUl.className = 'node_ul';
						newLi.appendChild(newLiUl);
						var newLiAddNode = document.createElement('li');
						newLiAddNode.id = 'add_child_node_' + resp['id'];
						newLiAddNode.className = 'add_child_node';
						newLiUl.appendChild(newLiAddNode);
						window.requestAnimationFrame(
							function(x) {
								target.parentNode.insertBefore(newLi, target);
							}
						);
					}
				);
				xhr.open(
					'POST',
					'api/add_child_node.php',
					true
				);
				xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xhr.send(params);
			} else if (target.className === 'node_title') {
					var targetId = target.id.slice(-24);
					editNodeDialog(targetId);
			}
		},
		false
	);
}
})();
