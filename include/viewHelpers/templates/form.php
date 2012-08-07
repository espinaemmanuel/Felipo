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
?>
<form id="<?= $form['id'] ?>" action="<?= $form['controller'] ?>" method="<?= $form['method'] ?>" class="<?= $form['class'] ?>" name="<?= $form['name'] ?>">
	<ul>
	<?php foreach($form['fields'] as $fieldName => $attrib) {?>
			<?php switch ($attrib['type']) { 
			 		case 'hidden': ?>
					<input type="hidden" name="<?= $fieldName ?>" id="<?= $fieldName ?>" value="<?= $attrib['defaultValue'] ?>" />
			<?php 		break;
					case 'password': ?>
					<li>
					<label for="<?= $fieldName ?>"><?= $attrib['label'] ?></label>
					<input type="password" name="<?= $fieldName ?>" id="<?= $fieldName ?>" value="<?= $attrib['defaultValue'] ?>" />
					</li>
					
			<?php 		break;
					default: ?>
					<li>
					<label for="<?= $fieldName ?>"><?= $attrib['label'] ?></label>
					<input type="text" name="<?= $fieldName ?>" id="<?= $fieldName ?>" value="<?= $attrib['defaultValue'] ?>" />
					</li>
			<?php 	} ?>
	<?php } ?>
	</ul>
	<input type="submit" value="<?= $form['submitValue'] ?>" />
	<input type="hidden" name="a" value="<?= $form['action'] ?>"/>
</form>