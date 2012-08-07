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




class ActiveRecordSelector {
	
	const LIKE = ' like ';
	const EQUAL = ' = ';
	
	protected $_recordName;	
	protected $_tableName;
	
	protected $_filters = array();
	protected $_offset = 0;
	protected $_limit = null;
	protected $_conn;
	
	public function __construct($recordName, $connName = null){
		$this->_recordName = $recordName;
		$this->_tableName = call_user_func_array(array($this->_recordName, "tableName"), array());
		
		$this->_conn = ConnectionManager::getInstance()->get($connName);
	}
	
	public function filter($field, $value, $comparator = self::EQUAL){
		array_push($this->_filters, array('field' => $field,
											  'value' => $value,
											  'comparator' => $comparator));
		return $this;
	}
	
	public function offset($offset){
		$this->_offset = intval($offset);
		
		return $this;
	}
	
	public function limit($limit){
		$this->_limit = intval($limit);
		
		return $this;
	}
	
	public function records(){
		
		$where = array();
		
		foreach($this->_filters as $filter){
			$prepValue = $this->_conn->prepararValor($this->_tableName, $filter['field'], $filter['value']);
			
			if($prepValue == null){
				//Notify omission
				continue;
			}
			array_push($where, $filter['field'].$filter['comparator'].$prepValue);
		}
		
		$where = join(' AND ', $where);
		
		if(!strlen(trim($where))){
			$where = null;
		}
		
		$params = array();
		$params['where'] = $where;
		$params['offset'] = $this->_offset;
		$params['limit'] = $this->_limit;
		
		$records = $this->_conn->getTodos($this->_tableName, $params);

		$activeRecords = array();
		foreach($records as $r){
			$ar = new $this->_recordName();
			$ar->loadArrayAsAttributes($r);
			$ar->isPersistent(true);
			
			array_push($activeRecords, $ar);
		}
		
		return $activeRecords;
	}
}

?>