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




class RenderHelper {
	
	public static function generarSelect(array $elementos, $idSelect = null, $campoValor = 'valor', $campoId = 'id', $idChecked = null){
		$id = (is_null($idSelect) & strlen($idSelect)>0)?'':"name=$idSelect id=$idSelect";
		$salida = "<select $id>\n%s</select>";
		$opciones = '';
		foreach($elementos as $e){
			$key = $e[$campoId];
			$valor = $e[$campoValor];
			$checked = $idChecked==$key?'checked ':'';
			$opciones .= "<option value=$key $checked>$valor</option>\n";		
		}
		
		return sprintf($salida, $opciones);
	}

}