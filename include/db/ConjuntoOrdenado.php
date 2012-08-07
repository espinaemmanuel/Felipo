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




class ConjuntoOrdenado {

	private $_tabla;
	private $_db;
	private $_camposRestringidos = array();
	
	public function __construct(DbConnection $db, $tabla){
		$this->_db = $db;
		$this->_tabla = $tabla;
	}
	
	public function restringirValor($campo, $valor){
		$this->_camposRestringidos[$campo] = $valor;
	}
	
	private function mover($id, $dir){
		$mov = array();
		$mov['subir']['signo'] = '>';
		$mov['bajar']['signo'] = '<';
		$mov['subir']['orden'] = 'orden';
		$mov['bajar']['orden'] = 'orden desc';
		
		$filtros = array();
		$inspector = $this->_db->getInspector();
		foreach($this->_camposRestringidos as $campo => $valor){
			if($inspector->existe($this->_tabla, $campo)){
				$valor = $inspector->esTexto($this->_tabla, $campo)?"'$valor'":$valor;
				array_push($filtros, "$campo = $valor");
			}
		}
		
		$this->_db->beginTransaction();
		
		$registro = $this->_db->getPorId($this->_tabla, $id);				
		//Obtener el registro en la posicion de destino
		array_push($filtros, 'orden '.$mov[$dir]['signo'].' '.$registro['orden']);
		$destino = $this->_db->getTodos($this->_tabla, array(
				'where' => implode(' AND ', $filtros),
				'order' => $mov[$dir]['orden'],
				'limit' => 1
		));
		
		if(count($destino)){
			$destino = $destino[0];
			$this->_db->update($this->_tabla, $registro['id'], array('orden' => $destino['orden']));
			$this->_db->update($this->_tabla, $destino['id'], array('orden' => $registro['orden']));			
		}
		
		$this->_db->commit();
		
		return $this->_db->getTodos($this->_tabla, array('where' => sprintf('id in (%d, %d)', $registro['id'], $destino['id'])));
		 
	}
	
	public function subirRegistro($id){
		return $this->mover($id, 'subir');
	}
	
	public function bajarRegistro($id){
		return $this->mover($id, 'bajar');
	}
}

?>