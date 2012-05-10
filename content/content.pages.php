<?php
	require_once(TOOLKIT . '/class.administrationpage.php');
    require_once(TOOLKIT . '/class.entrymanager.php');

	Class contentExtensionCkeditorPages extends AdministrationPage
	{

		function build()
		{
			if(!$this->canAccessPage()){
				$this->_Parent->customError(E_USER_ERROR, __('Access Denied'), __('You are not authorised to access this page.'));
				exit();
			}

            // The pages:
            $tree = array();
			$tree[] = array('name'=>'', 'items'=>$this->buildTree());

			echo json_encode($tree);
			die();
		}
		
		private function buildTree($parent = null, $indent = 0)
		{
			if($parent == null)
			{
				$results = PageManager::fetch(true, array(), array('`parent` IS NULL'), '`sortorder` ASC');
			} else {
				$results = PageManager::fetch(true, array(), array('`parent` = '.$parent), '`sortorder` ASC');
			}
			$tree = array();
			foreach($results as $result)
			{
				// Check if the page should be shown:
				if(!in_array('ck_hide', $result['type']))
				{
					$prefix = '';
					$info = array('handle'=>$result['handle'], 'path'=>$result['path']);
					if($result['path'] == null)
					{
						$info['url'] = '/'.$result['handle'].'/';
						$info['title'] = $result['title'];
					} else {
						$info['url'] = '/'.$result['path'].'/'.$result['handle'].'/';
						for($i = 0; $i < $indent; $i++)
						{
							$prefix .= ' '; // Please note: this might look like an empty space (nbsp) but it's an em space (emsp).
							// This was necessary because &nbsp; kept showing as plain text in the dropdown.
						}

						$info['title'] = $prefix.' › '.General::sanitize($result['title']);
					}
					$tree[] = $info;

					// Check if there are templates for this page:
					$tree = array_merge($tree, $this->checkTemplates($result['id'], $prefix.' ')); // also an emsp

					// Get the children:
					$children = $this->buildTree($result['id'], $indent + 1);

					// Join arrays:
					$tree = array_merge($tree, $children);
				}
			}
			
			return $tree;
		}

		private function checkTemplates($pageId, $prefix = '')
		{
            // Link templates:
            $templates = Symphony::Database()->fetch(
				sprintf('SELECT * FROM `tbl_ckeditor_link_templates` WHERE `page_id` = %d;', $pageId)
			);

			$entryTree = array();

            foreach($templates as $template)
            {
				$section = SectionManager::fetch($template['section_id']);
				$entries = EntryManager::fetch(null, $template['section_id']);
                $fields  = $section->fetchFields();
                foreach($entries as $entry)
                {
                    $link    = $template['link'];
                    // Replace the ID:
                    $link = str_replace('{$id}', $entry->get('id'), $link);
                    $data = $entry->getData();

                    foreach($fields as $field)
                    {
                        // Replace the placeholders with the value:
                        // Check if the field has a 'handle':
                        $testData = $field->processRawFieldData('test', $field->__OK__);
                        if(isset($testData['handle']))
                        {
                            $link = str_replace('{$'.$field->get('element_name').'}', $data[$field->get('id')]['handle'], $link);
                        }
                    }

					$entryTree[] = array(
						 'handle' => $data[$field->get('id')]['handle'],
						 'path' => '',
						 'url' => $link,
						 'title' => $prefix.' › '.General::sanitize($data[$template['field_id']]['value'])
					 );

                }
            }

			return $entryTree;
		}
	}
