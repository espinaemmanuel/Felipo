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



/**
 * Valida el largo del texto del campo. Si el campo no existe no devuelve errores.
 *
 */
class EqualToFieldValidator extends Validador {
	
	private $fieldName;
	private $otherField;
	
	public function __construct($fieldName, $otherField){
		$this->fieldName = $fieldName;
		$this->otherField = $otherField;
	}
	
	protected function validar(array $campos){
		$errors = array();
		if(!array_key_exists($this->fieldName, $campos) || !$campos[$this->fieldName])
			return $errors;
			
		if(!array_key_exists($this->otherField, $campos) || !$campos[$this->otherField])
			return $errors;
				
		if($campos[$this->fieldName] != $campos[$this->otherField])
			array_push($errors, new ErrorValidacion($this->fieldName, "Este campo debe ser igual a $this->otherField"));
		
		return $errors;			
	}
}

class EqualToFieldValidatorFactory extends ValidatorFactory {
	
	public function getValidator($field, $attribs){
		return new EqualToFieldValidator($field, $attribs['constraints'][EqualToFieldValidatorFactory::getName()]['field']);
	}
	
	public static function getName() {
		return 'EqualToField';
	}
}