<?php
	require_once(TOOLKIT . '/class.htmlpage.php');
	require_once(TOOLKIT . '/class.sectionmanager.php');
	require_once(TOOLKIT . '/class.entrymanager.php');

	Class contentExtensionCkeditorFilebrowserajax extends HTMLPage
	{
		function __construct(&$parent){
			parent::__construct($parent);			
		}
		
		function build($context)
		{
			$this->setTitle('Symphony - File Browser for CKEditor');
			
			if(!Administration::instance()->isLoggedIn()){
				$this->_Parent->customError(E_USER_ERROR, __('Access Denied'), __('You are not authorised to access this page.'));
				exit();
			}
			
			$this->addElementToHead(new XMLElement('meta', NULL, array('http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8')), 0);
			$this->addHeaderToPage('Content-Type', 'text/html; charset=UTF-8');
			
			## Build the form
			
			$form = Widget::Form(Administration::instance()->getCurrentPageURL(), 'post');
			
			// Get the section:
			if(isset($_GET['id'])) {
				$sectionID = intval($_GET['id']);
				$sectionManager = new SectionManager($this);
				$section = $sectionManager->fetch($sectionID);
				if($section != false)
				{
					$table = new XMLElement('table');
					
					// Show the entries of this section:
					$columns = $section->fetchVisibleColumns();
					$headers = new XMLElement('tr');
					$fieldIDs = array();
					foreach($columns as $column)
					{
						// The correct order:
						array_push($fieldIDs, $column->get('id'));
						$headers->appendChild(new XMLElement('th', $column->get('label')));
					}
					$table->appendChild($headers);
					
					// Add rows:
					$entryManager = new EntryManager($this);
					$entries = $entryManager->fetch(null, $sectionID);
					foreach($entries as $entry)
					{
						$fileFound = false;
						$row = new XMLElement('tr');
						$data = $entry->getData();
						
						foreach($fieldIDs as $id)
						{
							$info = $data[$id];
							if(in_array($id, $fieldIDs)) {
								$attributes = array();
								if(isset($info['file'])) {
									$value = '<a href="/workspace'.$info['file'].'">/workspace'.$info['file'].'</a>';
									// check mime:
									if($info['mimetype'] == 'image/jpeg' ||
									   $info['mimetype'] == 'image/jpg' ||
									   $info['mimetype'] == 'image/png' ||
									   $info['mimetype'] == 'image/gif')
									{
										$attributes['class'] = 'image';
									}									
									$fileFound = true;
								} elseif(isset($info['value'])) {
									$value = $info['value'];									
								} elseif(isset($info['handle'])) {
									$value = $info['handle'];
								} else {
									$value = '<em>no value found</em>';
								}
								$row->appendChild(new XMLElement('td', $value, $attributes));
							}
						}
						if($fileFound) {
							$table->appendChild($row);
						}
					}
					
					$form->appendChild(new XMLElement('a', __('create new'), array('href'=>'/symphony/publish/'.$section->get('handle').'/new/', 'class'=>'create button')));
					$form->appendChild(new XMLElement('h3', $section->get('name')));
					$form->appendChild($table);
					$form->appendChild(new XMLElement('div', '', array('id'=>'thumb')));
				}
			}
			
			$this->Body->appendChild($form);
			
		}
	}
?>