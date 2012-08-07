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




class ActiveRecord implements SerializableArray{
	
	protected $_idField;
	protected $_tableName;
	protected $_connection;
	protected $_persistent;
	
	public function __construct(DbConnection $connection = null){
		if(is_null($connection)){
			$connection = ConnectionManager::getInstance()->get();
		}
		
		$this->_connection = $connection;
		$this->_tableName = static::tableName();
		$this->_idField = static::idField();
	}
	
	public static function tableName(){
		return preg_replace('/_AR/', '', get_called_class());
	}
	
	public static function idField(){
		return 'id'.static::tableName();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id_or_array
	 * @return ActiveRecord
	 */
	public static function loadById($id_or_array) {
		$id = $id_or_array;
		if (is_array ( $id_or_array )) {
			if (! array_key_exists ( static::idField(), $id_or_array )) {
				return null;
			} else {
				$id = $id_or_array [static::idField()];
			}		
		}
		
		$connection = ConnectionManager::getInstance()->get();
				
		$record = $connection->getPorId(static::tableName(), $id, static::idField());
		
		if(!$record)
			return null;
			
		$classname = get_called_class();
		$result = new $classname;
		$result->loadArrayAsAttributes($record);
		$result->isPersistent(true);
		
		return $result;		
	}
	
	public static function selector(){
		return new ActiveRecordSelector(get_called_class());
	}
	
	protected function getConnection(){
		if(!$this->_connection){
			$this->_connection = ConnectionManager::getInstance()->get();
		}
		
		return $this->_connection;		
	}
	
	public function loadArrayAsAttributes(Array $a){
		$i = $this->_connection->getInspector();
		foreach($a as $k => $v){
			if($i->existe($this->_tableName, $k) && !$i->esTexto($this->_tableName, $k)){
				$v = floatval($v);
			}			
			$this->$k = $v;
		}		
	}
	
	public function isPersistent($val = null){
		if(!is_null($val)){
			$this->_persistent = $val?true:false;
		}
		
		return $this->_persistent;		
	}
	
	public function getAttributesAsArray() {
		
		$attrib = get_object_vars ( $this );
		
		foreach ( $attrib as $k => $v ) {
			if (preg_match ( '/^_/', $k )) {
				unset ( $attrib [$k] );
			}
		}
		
		return $attrib;
	}
	
	public function save(){
		if($this->isPersistent()){
			$this->update();
		} else {
			$this->insert();
		}		
	}
	
	protected function insert(){
		$conn = $this->getConnection();
		
		$id = $conn->insertar($this->_tableName, $this->getAttributesAsArray());
		
		if(!$this->getId()){
			$this->setId($id);			
		}
		
		$this->isPersistent(true);
		
	}
	
	protected function update(){
		$conn = $this->getConnection();
		$conn->update($this->_tableName, $this->getId(), $this->getAttributesAsArray(), $this->_idField);	
	}
	
	public function delete(){
		$conn = $this->getConnection();
		$conn->borrar($this->_tableName, $this->getId(), $this->_idField);
		$this->isPersistent(false);		
	}
	
	public function setId($id){
		$idField = $this->_idField;
		$this->$idField = $id;
	}
	
	public function getId(){
		$idField = $this->_idField;
		return $this->$idField;
	}
}

?>