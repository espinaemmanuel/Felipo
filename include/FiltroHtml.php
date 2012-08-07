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




require_once 'Zend/Filter.php';
require_once 'Zend/Filter/StripTags.php';
require_once 'Zend/Filter/StringTrim.php';

class FiltroHtml {
	
	private $_listaTags;
	
	public function __construct(array $listaTags = null) {
		if ($listaTags === null) {
			$this->_listaTags = array (
										'a' => array ('href', 'target', 'name' ),
										'b' => array (),
										'strong' => array (),
										'em' => array (),
										'i' => array (),
										'ul' => array (),
										'u' => array (),
										'li' => array (),
										'ol' => array (),
										'p' => array (),
										'br' => array ()
			 );
			 		
		} else {
			$this->_listaTags = $listaTags;
		}
	}
	
	public function filtrar($html) {
		
		$chain = new Zend_Filter ( );
		$chain->addFilter ( new Zend_Filter_StripTags ( $this->_listaTags ) );
		$chain->addFilter ( new Zend_Filter_StringTrim ( ) );
		
		//Eliminar tags que a veces introduce Word		
		$html = eregi_replace('<w:WordDocument>.*</w:WordDocument>', '', $html);
		
		//Eliminar los estilos
		$html = eregi_replace('<style>.*</style>', '', $html);
			
		$html = $chain->filter ( $html );
		
		//Eliminar las repeticiones de saltos de linea y de tabs
		$html = eregi_replace("[\t]", '', $html);		
		$html = eregi_replace("( ?\r\n ?)+", "\r\n", $html);
		
		$tmp = $html;
		
		while ( 1 ) {
			$html = preg_replace ( '/(<[^>]*)javascript:([^>]*>)/i', '$1$2', $html );
			
			if ($html == $tmp)
				break;
			$tmp = $html;
		}
		return $html;
	}

}