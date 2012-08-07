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




class ActiveRecordResource extends RecursoRest {
	
	protected $_activeRecord_Classname;
	
	public function __construct($controller){
		//Load resource
		parent::__construct($controller);
		
		$this->_activeRecord_Classname = get_class($this).'_AR';
		$pathRecurso = 'app/' . $this->_controller->getModuleName() . '/model/' . $this->_activeRecord_Classname . '.php';
		if (! Loader::getInstance ()->fileExists ( $pathRecurso ))
				throw new UnknownModelException ( $this->_activeRecord_Classname );
				
		Loader::getInstance()->requireOnce($pathRecurso);
	}
	
	protected function extractId(Request  $request){
		$uri_parts = preg_split('/\//', $request->getUri(), null, PREG_SPLIT_NO_EMPTY);
		if(count($uri_parts)<4){
			return null;
		}else {
			return urldecode($uri_parts[3]);
		}		
	}
	
	public function get(Request  $request){
		$id = $this->extractId($request);
		if(!is_null($id)){
			$this->resource = $this->loadById($id);			
		} else {
			$this->getMultiple($request);
		}
	}
	
	protected function getFiltros(Request  $request){
		$filtros = array();
		$_filtros = $request->getParam('f');
		if(is_array($_filtros)){
			array_merge($filtros, $_filtros);
		} else if(!is_null($_filtros)){
			array_push($filtros, $_filtros);
		}
		
		$return  = array();
		foreach($filtros as $filtro){
			$matches = array();
			if(preg_match('/^(.*)(:)(.*)$/', $filtro, $matches)){
				if($matches[2] == ':'){
					$return[$matches[1]] = $matches[3];
				}
			}			
		}
		
		return $return;
	}
	
	protected function getMultiple(Request  $request){
		$s = $this->getSelector();
		
		foreach($this->getFiltros($request) as $filtro => $valor){
			$s->filter($filtro, $valor, ActiveRecordSelector::EQUAL);
		}

		$this->resources = new SerializableArrayWrapper($s->records());
		$this->numResources = count($this->resources);
	}
	
	protected function loadById($id){
		$name = $this->_activeRecord_Classname;
		$result = call_user_func_array(array($name, "loadById"), array($id));
		return $result;
	}
	
	protected function getSelector(){
		$name = $this->_activeRecord_Classname;
		$selector = call_user_func_array(array($name, "selector"), array());
		return $selector;	
	}
	
	public function post(Request  $request){
		$id = $this->extractId($request);
		if(!is_null($id)){
			$this->postUpdate($request, $id);
		} else {
			$this->postInsert($request);
		}
	}
	
	protected function postInsert(Request  $request){
		$ar = new $this->_activeRecord_Classname;
		$ar->loadArrayAsAttributes($request->post());
		$ar->save();
		$this->resource = $ar;		
	}
	
	protected function postUpdate(Request  $request, $id){
		$ar = $this->loadById($id);
		$ar->loadArrayAsAttributes($request->post());
		$ar->save();
		$this->resource = $ar;		
	}
	
	public function delete(Request  $request){
		$id = $this->extractId($request);		
		$ar = $this->loadById($id);
		
		if(is_null($id)){
			$this->resource = null;
			return;
		}
			
		$ar->delete();
		$this->resource = $ar;		
	}

}

?>