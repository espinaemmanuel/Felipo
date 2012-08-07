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



class SqliteConnector extends DbConnector {
	public function connect(array $connConfig) {
		if ($connConfig ['type'] === 'persistent') {
			$path = $connConfig ['directory'];
			if (! Loader::getInstance ()->fileExists ( $path )) {
				throw new SqlException ( 'Sqlite directory not found' );
			}
			
			$path = Loader::getInstance ()->getFullPath ( $path );
			$dbName = isset($connConfig ['db'])?$connConfig ['db'] : 'default.sq3';
		
			$dsn = "sqlite:$path/$dbName";
				
			return new PDO ( $dsn, null, null, array (
					PDO::ATTR_PERSISTENT => true 
			) );
		}
	}
}

?>