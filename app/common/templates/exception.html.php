<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Exception</title>
</head>

<body>
<h1>An exception ocurred</h1>
<h2><?= get_class($r->e).' : '.$r->e->getMessage() ?></h2>
<ul class="stack-trace">
<?php 
$trace = $r->e->getTrace();

foreach($trace as $traceLine){
	$file = Loader::getInstance()->getRelativePath($traceLine['file']);
	$line = $traceLine['line'];
	$function = $traceLine['function'];
	$class = isset($traceLine['class']) ? $traceLine['class'] : '';
	$type = isset($traceLine['type']) ? $traceLine['type'] : '';
	
	$params = array();
	
	foreach($traceLine['args'] as $arg){
		$string = '';
		
		if (!is_object($arg) || method_exists($arg, '__toString')) {
			$string = (string)$arg;
		} else {
			$string = get_class($arg);
		}
		
		array_push($params, $string);
	}
	
	$params = join(', ', $params);
	
	echo "<li>$file:$line : $class $type $function($params) </li>";
}

?>
</ul>
</body>
</html>
