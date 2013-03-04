<?php

	require_once('default.ckeditor.php');

	Class formatter{{HANDLE}} extends DefaultTextFormatter {
		public function about(){
			$about = parent::about();
			$about['name'] = 'CKEditor : {{NAME}}';
			return $about;
		}
	}

