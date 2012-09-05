<?php
	require_once(TOOLKIT . '/class.administrationpage.php');

	Class contentExtensionCkeditorJs extends AdministrationPage
	{
		function build()
		{
			if(!$this->canAccessPage()){
				$this->_Parent->customError(E_USER_ERROR, __('Access Denied'), __('You are not authorised to access this page.'));
				exit();
			}

			header('Content-Type: text/javascript');
			$css = Symphony::Configuration()->get('styles', 'ckeditor');
			$lines = explode("\n", $css);
			// h3.groen-blok { color: #363636; background: #a3cf5e; }
			// h3.zwart-blok { color: #fff; background: #363636; }
			$js = 'var ckeditor_styles = [';
			$rules = array();
			foreach($lines as $line)
			{
				if(!empty($line))
				{
					$a = explode('{', $line);
					$selector = trim($a[0]);
					$b = explode('.', $selector);
					$element = $b[0];
					$className = $b[1];
					// {name: 'Groen Blok', element: 'h3', attributes: {class: 'groen-blok'}}
					$c = explode('-', $className);
					$name = '';
					foreach($c as $d) { $name .= ucfirst($d).' '; }
					$rules[] = '{\'name\': \''.trim($name).'\', \'element\': \''.$element.'\', \'attributes\': {\'class\': \''.$className.'\'}}';
				}
			}
			$js .= implode(',', $rules).']';
			die($js);
		}
	}
