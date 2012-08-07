<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */




class FormHelper {
	
	private $_formDescription;
	
	public function __construct($formFile){
		$formDescription = file_get_contents($formFile);
		$formDescription = json_decode($formDescription, true);
		
		$this->_formDescription = $this->preProcessForm($formDescription);
	}
	
	protected function preProcessForm($formDescription){
		return $formDescription;
	}
	
	public function toString(){
		global $form;
		$form = $this->_formDescription;
		Loader::getInstance()->includeFile('include/viewHelpers/templates/form.php');
		unset($form);
	}
	
	public static function drawForm($formFile){
		$fh = new FormHelper($formFile);
		$html = $fh->toString();
		echo $html;		
	}

}

?>