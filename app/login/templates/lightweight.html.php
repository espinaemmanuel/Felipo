<?php global $r; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Iuristantum Web</title>
<link rel="stylesheet" type="text/css" href="/inicial/css/personalizacion.css"   />

</head>

<body>

<h1>Login</h1>

<?php if(count($r->errores) > 0) {
	foreach($r->errores as $error){
		echo sprintf("<p class=\"mensaje\">%s</p>", $error);
	}	
}?> 

<?php if($r->validLogin) { ?>
<div>Se ha logueado con exito en el sistema</div>
<?php } else {
	FormHelper::drawForm(Loader::getInstance()->getFullPath('app/inicial/form/lightweightLogin.json')); 
}?>
<div class="olvido">* Olvidó su contraseña? Haga clic <a href="/inicial/olvidoPassword" target="_top">aquí</a> para recuperarla</div>   	

</body>
</html>

        