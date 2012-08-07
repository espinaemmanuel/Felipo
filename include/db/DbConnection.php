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




class DbConnection {
	/**
	 * 
	 * @var PDO
	 */
	private $_base=null;
	
	/**
	 * 
	 * @var InspectorSql
	 */
	private $_inspectorSql = null;
	
	/**
	 * 
	 * @var boolean
	 */
	private $_transaccionComenzada = false;
	
	/**
	 * 
	 * @var array
	 */
	private $_connectionData;
	private $_nestedTransactions = true;
	private $_nesting = 0;
	
	public static $_connectors = array();
	
	public function __call($name, array $arguments){
		$regs = array();
		if(ereg('^get([A-Z][A-Za-z]*)PorId$', $name, $regs)){
			$resutlado = $this->getPorId($regs[1], $arguments[0]);
			return $resutlado;
		} else if (ereg('^insertar([A-Z][a-z]*)$', $name, $regs)) {
			$resutlado = $this->insertar($regs[1], $arguments[0], $arguments[1]);
			return $resutlado;			
		} else if (ereg('^borrar([A-Z][a-z]*)$', $name, $regs)){
			$this->borrar($regs[1], $arguments[0]);
			return;			
		}
		
		throw new Exception("Metodo inexistente $name");
	}
	
	public function getRecurso(){
		return $this->getConexion();
	}
	
	/**
	 * 
	 * @return InspectorSql el inspector
	 */	
	public function getInspector(){
		if(!$this->_inspectorSql)
			$this->_inspectorSql = new InspectorSql($this);
		return $this->_inspectorSql;
	}
	
	public function __construct(array $connConf) {			
		$this->_connectionData = $connConf;	
	}
	
	public function beginTransaction(){
		if(!$this->_nestedTransactions && $this->_transaccionComenzada)
			throw new SqlException("Se ha tratado de comenzar otra transaccion sin terminar la anterior");
		
		if($this->_nesting == 0){
			if(!$this->getConexion()->autocommit(FALSE))
				throw new SqlException("No se pudo comenzar la transaccion");			
		}

		$this->_nesting += 1;
		$this->_transaccionComenzada = true;			
	}
	
	public function commit(){
		if($this->_nesting == 0)
			throw new SqlException("Se ha tratado de terminar una transaccion que no fue iniciada");
		
		if($this->_nesting == 1){
			$this->getConexion()->commit();
			
			if($this->getConexion()->errno)
				throw new SqlException("Fallo la query: ".$this->getConexion()->error);
			
			if(!$this->getConexion()->autocommit(true))
				throw new SqlException("No se pudo terminar la transaccion");
		}
		
		$this->_nesting -= 1;		
			
		if($this->_nesting == 0)
			$this->_transaccionComenzada = false;
	}
	
	public function rollback(){
		if(!$this->_transaccionComenzada)
			throw new SqlException("Se ha tratado de terminar una transaccion que no fue iniciada");
			
		$this->getConexion()->rollback();
		
		if($this->getConexion()->errno)
			throw new SqlException("Fallo la query: ".$this->getConexion()->error);		
			
		$this->_transaccionComenzada = false;
		
		if(!$this->getConexion()->autocommit(true))
			throw new SqlException("No se pudo terminar la transaccion");		
	}
	
	protected function connect(){
		
		$driver = $this->_connectionData ['driver'];

		if(!array_key_exists($driver, self::$_connectors)){
			throw new DbConnectorNotDefinedException($driver);
		}
		
		$connector = self::$_connectors[$driver];
		
		$this->_base = $connector->connect($this->_connectionData);
		$this->_base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
	}
	
	protected function getConexion(){
		if ($this->_base == null){
			$this->connect();
		}		
		return $this->_base;
	}
	public function query($sql) {
		return $this->getConexion ()->query ( $sql )->fetchAll ();
	}
	
	public function getPorId($tabla, $id, $fieldId = 'id'){
		
		$id = $this->prepararValor($tabla, $fieldId, $id);
	
		$sql = "SELECT * FROM $tabla WHERE $fieldId = $id";
		$resultado = $this->query($sql);
		
		if(count($resultado)>1)
			throw new SqlException("La query devolvio multiples resultados($resultado->num_rows): $tabla, $id");

		if(count($resultado)==0)
			return null;
			
		return $resultado[0];
	}

	public function getTodos($from, $opciones=null){
	
		$select = '*';
		$where = null;
		$order = null;
		$limit = null;
		$offset = null;
		
		if(isset($opciones) && is_array($opciones)){
			if(array_key_exists('select', $opciones))
				$select = $opciones['select'];
			
			if(array_key_exists('where', $opciones))
				$where = $opciones['where'];
				
			if(array_key_exists('order', $opciones))
				$order = $opciones['order'];
				
			if(array_key_exists('offset', $opciones))
				$offset = $opciones['offset'];

			if(array_key_exists('limit', $opciones))
				$limit = $opciones['limit'];
		}
				
		$sql = "SELECT $select FROM $from";
		if($where!=null){
			$sql .= " WHERE $where";
		}
		if($order!=null){
			$sql .= " ORDER BY $order";
		}		
		if($limit!=null){
			$sql .= " LIMIT $limit";
		}
		if($limit!=null && $offset!=null){
			$sql .= " OFFSET $offset";
		}
		
		return $this->query($sql);			
	}
	
	/**
	 * Obtiene informacion sobre una tabla mysql
	 */
	public function getInfoTabla($tabla){
		if(!array_key_exists($tabla, $this->_infoBase))
			$this->_infoBase[$tabla] = $this->query("DESC $tabla");
	}
		
	public function sanitizar($string){
		return $this->getConexion()->real_escape_string($string);
	}
	
	public function prepararValor($tabla, $campo, $valor){
			//Ignorar los campos que no estan en la tabla
			if(!$this->getInspector()->existe($tabla, $campo))
				return null;
			
			if($this->getInspector()->esFecha($tabla, $campo)){
				$valor = $this->sanitizar($valor);
				if(!trim($valor))
					return null;
					
				return "'$valor'";
			}
			//Agregar comillas de ser necesario
			if($this->getInspector()->esTexto($tabla, $campo)){
				$valor = $this->sanitizar($valor);
				return "'$valor'";
			}

			return $valor;
	}
	
	public function update($tabla, $id, array $campos, $idField = 'id'){
		
		$id = $this->prepararValor($tabla, $idField, $id);
		
		$sql = "UPDATE $tabla SET ";
		$arrayModificaciones = array();
		
		foreach($campos as $campo => $valor){
			
			$valor = $this->prepararValor($tabla, $campo, $valor);
			
			if(!is_null($valor)){
				array_push($arrayModificaciones, "$campo = $valor");
			}
		}
		
		$sql .= implode(', ', $arrayModificaciones);
		$sql .= " WHERE $idField=$id";
		
		$this->query($sql);
	}
	
	public function insertar($tabla, array $campos){
			
		$arrayCampos = array();
		$arrayValores = array();
		
		foreach($campos as $campo => $valor){
			
			$valor = $this->prepararValor($tabla, $campo, $valor);
			
			if(!is_null($valor)){
				array_push($arrayCampos, $campo);
				array_push($arrayValores, $valor);				
			}
		}
		
		$campos = implode(', ', $arrayCampos);
		$valores = implode(', ', $arrayValores);
		
		$sql = "INSERT INTO $tabla ($campos) VALUES ($valores)";
		
		$id = null;
		
		$this->beginTransaction();
			$this->query($sql);
			$id = $this->getLastId();
		$this->commit();
		
		return $id;
	}
	
	public function getLastId(){
		return $this->getConexion()->insert_id;
	}
	
	/**
	 * Todos los indices deben ser iguales
	 * 
	 * @param unknown_type $tabla
	 * @param array $campos
	 */
	
	public function insertarMultiple($tabla, array $elementos){
		
		if(!count($elementos))
			return;
		
		$camposTemp = array_keys($elementos[0]);
		$campos = array();
		foreach($camposTemp as $campo){
			if($this->getInspector()->existe($tabla, $campo)){
				array_push($campos, $campo);	
			}				
		}
		
		$registros = array();
		foreach($elementos as $elemento){
			$registro = array();
			foreach($campos as $campo){
				if(!array_key_exists($campo, $elemento))
					throw new SqlException('No son todos los elementos iguales');
				
				$valor = $elemento[$campo];
				$valor = $this->prepararValor($tabla, $campo, $valor);
				if($valor)
					array_push($registro, $valor);
			}
			$registro = '('.implode(', ', $registro).')';
			array_push($registros, $registro);
		}
		$registros = implode(', ', $registros);
		$campos = implode(', ', $campos);
		
		$this->query("INSERT INTO $tabla ($campos) VALUES $registros");
	}
	
	public function borrar($tabla, $id, $idField = 'id'){
		
		if(is_null($id))
			return;
		
		$id = $this->prepararValor($tabla, $idField, $id);
			
		$sql = "DELETE FROM $tabla WHERE $idField = $id";
		
		return $this->query($sql);
	}
}

DbConnection::$_connectors['sqlite'] = new SqliteConnector();
