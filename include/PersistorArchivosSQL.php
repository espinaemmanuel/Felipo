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




class PersistorArchivosSQL {
	
	/**
	 * Base de datos
	 *
	 * @var ConexionSql
	 */
	private $db;
	private $tabla = 'archivo';
	
	/**
	 * array que mapea los campos a persistir con los nombres de los campos en la base de datos
	 *
	 * @var unknown_type
	 */
	private $campos = array( 'id' => 'id',
							 'nombre' =>'nombre',
							 'tipo' =>'tipo',
							 'size' =>'size',
							 'fechaCreacion' =>'fechaCreacion',
							 'contenido' =>'contenido_binario');
	
	public function __construct(DbConnection  $db){
		$this->db = $db;		
	}
	
	/**
	 * Persiste el archivo en la base de datos
	 *
	 * @param Archivo $archivo
	 * @return el id del archivo persistido
	 */
	
	public function persistir(Archivo $archivo){
		//Chequear archivo
		
		$fila = $this->db->query('SELECT MAX('.$this->campos['id'].') as max_id from '.$this->tabla);
		$id = $fila[0]['max_id'] + 1;
		
		$query = sprintf('INSERT INTO %s (%s, %s, %s, %s, %s, %s ) VALUES (?,?,?,?,?,?)', $this->tabla,
																				  $this->campos['id'],
																				  $this->campos['nombre'],
																				  $this->campos['tipo'],
																				  $this->campos['size'],
																				  $this->campos['fechaCreacion'],
																				  $this->campos['contenido']);
																				  
		$fila = $this->db->query('SELECT @@max_allowed_packet as maximo');
		$maximo = $fila[0]['maximo']>10485760?10485760:$fila[0]['maximo'];
		
		$base_mysqli = $this->db->getRecurso();
		
		$stmt = $base_mysqli->prepare($query);
		$null = NULL;
		$stmt->bind_param('issisb', $id, $archivo->getNombre(), $archivo->getTipo(), $archivo->getSize(), date('Y-m-d'), $null);
		
		while(!$archivo->eof()){
			$cadena = $archivo->read($maximo);
			$stmt->send_long_data(5, $cadena);			
		}
		
		if(!$stmt->execute()){
			throw new SqlException($stmt->error);
		}
		return $id;	
	}
}