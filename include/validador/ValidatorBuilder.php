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




class ValidatorBuilder {
	
	private $_validatorFactories = array();
	
	public function __construct(){
		$this->_validatorFactories[RequiredValidatorFactory::getName()] = new RequiredValidatorFactory();
		$this->_validatorFactories[EmailValidatorFactory::getName()] = new EmailValidatorFactory();
		$this->_validatorFactories[EqualToFieldValidatorFactory::getName()] = new EqualToFieldValidatorFactory();
		$this->_validatorFactories[LimitedTextValidatorFactory::getName()] = new LimitedTextValidatorFactory();
	}
	
	public function buildValidator($fullPath){
		$json = file_get_contents($fullPath);
		$json = json_decode($json, true);
		
		$v = new ValidadorCompuesto();		
		foreach($json['fields'] as $fieldName => $fieldAttrib){
			$fieldValidator = $this->buildFieldValiator($fieldName, $fieldAttrib);
			$v->agregar($fieldValidator);
		}
		return $v;
	}
	
	protected function buildFieldValiator($fieldName, $fieldAttrib){
		
		if(!array_key_exists('constraints', $fieldAttrib)){
			return null;
		}
		
		$constraints = $fieldAttrib['constraints'];
		
		$lastVal = null;
		$val = null;
		
		foreach($constraints as $constName => $constParams){
			if(!array_key_exists($constName, $this->_validatorFactories)){
				throw new ValidatorBuilderException($constName.' is not a valid constraint name');
			}
			
			$factory = $this->_validatorFactories[$constName];
			$newVal = $factory->getValidator($fieldName, $fieldAttrib);
			
			if(is_null($val)){
				$val = $newVal;
			}
			
			if(!is_null($lastVal)){
				$lastVal->siguiente($newVal);
			}
			
			$lastVal = $newVal;
		}
		
		return $val;
	}
}

class ValidatorBuilderException extends Exception{
	
}

?>