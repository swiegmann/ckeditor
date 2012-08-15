<?php
	require_once(TOOLKIT . '/class.administrationpage.php');

	Class contentExtensionCkeditorCss extends AdministrationPage
	{
		function build()
		{
			if(!$this->canAccessPage()){
				$this->_Parent->customError(E_USER_ERROR, __('Access Denied'), __('You are not authorised to access this page.'));
				exit();
			}

			header('Content-Type: text/css');
			die(Symphony::Configuration()->get('styles', 'ckeditor'));
		}
	}
