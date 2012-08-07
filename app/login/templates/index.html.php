<?php global $r; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Iuristantum Web</title>
<link href="/inicial/css/estilos.css" rel="stylesheet" type="text/css" />
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js'></script>

<script type="text/javascript">
$(document).ready(function(){
	$('#send').click(function(){
		$('#loginForm').submit();
	});
});

</script>

</head>

<body>
	<div id="wrapper">
	<?php Loader::getInstance()->includeFile('app/inicial/templates/header.html.php'); ?>
   	  <div class="caja_form_central">
        <div class="contenido_form_central">
   	      <h2>LOGIN</h2>
           <div class="division"></div>
           <?php if(count($r->errores) > 0) {
				foreach($r->errores as $error){
					echo sprintf("<p class=\"mensaje\">%s</p>", $error);
				}	
			}?>
			<?php FormHelper::drawForm(Loader::getInstance()->getFullPath('app/inicial/form/login.json')); ?> 
			
         </div>
         <div class="olvido">* Olvidó su contraseña? Haga clic <a href="/inicial/olvidoPassword">aquí</a> para recuperarla</div>   	
      </div>
	  <?php Loader::getInstance()->includeFile('app/inicial/templates/footer.html.php'); ?>
</div>
</body>
</html>

        