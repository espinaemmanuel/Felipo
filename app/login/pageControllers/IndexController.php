<?php

class IndexController extends LoginController{
	
	protected function cargarDatos(){
		
		if($this->request->getParam('lightweight')){
			$this->template = 'app/login/templates/lightweight.html.php';
		}
		
		parent::cargarDatos();
	}
			
}