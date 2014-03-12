<?php


require 'lib/Slim/Slim.php';
require 'lib/simplehtmldom15/simple_html_dom.php';



define("SIN_UBICACION", "todos");
define("SIN_EXPERIENCIA", 1000);


\Slim\Slim::registerAutoloader();


\Slim\Route::setDefaultConditions(array(
    //'perfil' => '[a-zA-Z]{3,}',
    //'ubicacion' => '[a-zA-Z_áéíóúñ]{3,}',
    'experiencia' => '[0-9]{1,}'
));



$app = new \Slim\Slim();



//EJEMPLO: http://applicatte.hol.es/empleate/buscar/ingeniero/meta/100
$app->get('/buscar/:perfil/:ubicacion/:experiencia', 

	  function ($perfil,$ubicacion,$experiencia) use ($app) {

	  	$perfil = quitar_tildes($perfil);
	  	$ubicacion = quitar_tildes($ubicacion);


		$html = file_get_html('http://colombianostrabajando.sena.edu.co/spe/servlet/BusquedaOferente?palabra_clave='. urlencode($perfil) .'&accion=buscar');

		$resultados = array();

		foreach($html->find('table[class=tablaRegistros]') as $tabla)
		{	
		   $i = 0;

		   foreach($tabla->find('tr') as $tr)
		   {
		   	   
		   	   foreach($tr->find('td') as $td)
		        	$resultados[$i][] = utf8_encode($td->innertext);

			   $i++;
		   }
		}



		//Quitamos columnas que no queramos en este caso la del
		//enlace que apunta  a los detalles
		for($i=0; $i <= count($resultados); $i++)
		{
			unset($resultados[$i][5]);
			//unset($resultados[$i][2]);
			//unset($resultados[$i][3]);	
		}

	
		//echo urlencode('Facativá');
		//echo 'vale2:' . utf8_encode($ubicacion);
		//$cadenaa = 'ingeniero de sistemas Facatativá y computación';
		//echo 'vale:' . stripos(utf8_encode($resultados[2][4]),$ubicacion);
		//print_r($resultados[2][4]);
		//exit('<br/>tewrmino');

		$filtrados = array();
		$totalElementos = count($resultados);

		
		if($totalElementos>0 && isset($resultados[1][3]))
		{
			for($i=1; $i <= $totalElementos; $i++)
			{
				//no filtramos
				if($experiencia == SIN_EXPERIENCIA && $ubicacion == SIN_UBICACION)
				{	
					$filtrados = $resultados;
					break;
				}
				else if($experiencia != SIN_EXPERIENCIA && $ubicacion != SIN_UBICACION)
				{
					
						if($resultados[$i][3] <= $experiencia && stripos(quitar_tildes($resultados[$i][4]), $ubicacion) !== false)
							$filtrados[$i] = $resultados[$i];

				}
				else if($experiencia != SIN_EXPERIENCIA)
				{

						if($resultados[$i][3] <= $experiencia)
							$filtrados[$i] = $resultados[$i];
				}
				else
				{
					if(stripos(quitar_tildes($resultados[$i][4]), $ubicacion) !== false)
							$filtrados[$i] = $resultados[$i];
				}

			}
		}
		
		unset($resultados);

		//PARA MOSTRAR JSON
	    
	    $app->response()->header("Content-Type", "application/json");
        echo json_encode($filtrados);
         
        
        //PARA MOSTRAR TABLE HTML
/*		 $i=1;
		 echo "<table>";
		 foreach ($filtrados as $fila) {
			
		 	echo "<tr>";
		 	echo "<td>#<td/><td>" . $i . "<td/>";
		 	echo "<td>ID:<td/><td>" . $fila[0] . "<td/>";
		 	echo "<td>OCUPACION:<td/><td>" . $fila[1] . "<td/>";
		 	echo "<td>DESCRIPCION:<td/><td>" . $fila[2] . "<td/>";
		 	echo "<td>EXPERIENCIA:<td/><td>" . $fila[3] . "<td/>";
		 	echo "<td>UBICACION:<td/><td>" . $fila[4] . "<td/>";
		 	echo "<td>ENLACE DETALLES:<td/><td>" . $fila[5] . "<td/>";
		 	echo "</tr>";


		 	$i++;
		 }
		 echo "</table>";*/
		 
    });




//EJEMPLO: http://applicatte.hol.es/empleate/verificar/1544320
$app->get('/verificar/:codigo', 

	  function ($codigo) use ($app) {
	    
	  	$i = 0;


	  	try {
		   

	  		$html = file_get_html('http://colombianostrabajando.sena.edu.co/spe/servlet/BusquedaOferente?accion=vervct&vct_id='. $codigo);

			foreach($html->find('table[class=fondoColorPrincipal]') as $tabla)
				$i++;

			} catch (Exception $e) {
			    $i = 0;
			}


		//PARA MOSTRAR JSON
	    $app->response()->header("Content-Type", "application/json");
        echo json_encode($i);
         
     
		 
});




//EJEMPLO: http://applicatte.hol.es/empleate/verexterno/1544320
// http://localhost/empleate/empleate/Empleate-servidor/verexterno/1544320789

$app->get('/verexterno/:codigo', 

	  function ($codigo) use ($app) {

	  	$i = 0;


	  	try {
		   
	  		$html = file_get_html('http://colombianostrabajando.sena.edu.co/spe/servlet/BusquedaOferente?accion=vervct&vct_id='. $codigo);

			foreach($html->find('table[class=fondoColorPrincipal]') as $tabla)
				$i++;

			} catch (Exception $e) {
			    $i = 0;
			}

			if($i == 0)
				echo '<h1>Empleo ya expiro.</h1>';
			else
				$app->redirect('http://colombianostrabajando.sena.edu.co/spe/servlet/BusquedaOferente?accion=vervct&vct_id=' . $codigo);


});





//EJEMPLO: http://applicatte.hol.es/empleate/resultadosporcorreo
// http://localhost/empleate/empleate/Empleate-servidor/resultadosporcorreo
$app->post('/resultadosporcorreo/', 

	  function () use ($app) {

	  
	  //$data = $app->request->post('data');	
	  $data = json_decode(urldecode($app->request->post('data')));


	  date_default_timezone_set('America/Bogota');


	  //$fp = fopen("ejemplo.txt","a");
	  //fwrite($fp, $data[0] . PHP_EOL . PHP_EOL);
	  //fclose($fp);


	  //$data = array();
	  //$data[] = "andresgarcia@misena.edu.co";
	  //$data[] = array("codigotutor@gmail.com", "perfil andres", 1625714,1632884,1630720);
	  //$data[] = array("codigotutor@hotmail.com", "perfil de monica", 1625714,1632884,1630720);
	  //$data[] = array("", "perfil de hermano", 1625714,1632884,1630720);


	  require_once('class.phpmailer.php');
	  $mail = new PHPMailer();


		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "ssl";
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465;
		$mail->Username = "appempleate@gmail.com";
		$mail->Password = "select395992";
	    $mail->SetFrom('appempleate@gmail.com', 'Empleate');
	  


	  for($i=1; $i<count($data);$i++)
	  {
	  	  $usuario = $data[$i];
	  	  $mail->ClearAllRecipients();
	  	  $correos = array();


	  	  $mail->Subject = utf8_decode("Empleate, encontro nuevos empleos para tí (" . $usuario[1] . ') - (' . date("d-m-Y") . ')');

	  	  $cuerpo = "<h2>Empleate, ¡el empleo que te busca!</h2>";
	  	  $cuerpo = $cuerpo . "<h2>Lista de empleos nuevos:</h2>";

	
          if($data[0] != "")
          {	
          	  $mail->AddAddress($data[0]);
          	  $correos[] = $data[0];
          }


	  	  if($usuario[0] != "")
	  	  {	
	  	  	  $mail->AddAddress($usuario[0]);
	  	  	  $correos[] = $usuario[0];
	  	  }
	  		
	  	  
	  	  for($j=2; $j<count($usuario); $j++)
	  	  {
            $cuerpo = $cuerpo . '<a href="http://colombianostrabajando.sena.edu.co/spe/servlet/BusquedaOferente?accion=vervct&vct_id=' . $usuario[$j] .'" target="_new">Enlace al Servicio Publico de Empleo SENA</a><br/><br/>';
	  	  }


	  	  $mail->MsgHTML($cuerpo);
		  $mail->Send();

	  	  //echo "inicio<br/>";
	  	  //echo print_r($correos);
	  	  //echo $mail->Subject;
		  //echo $cuerpo;
		  //echo "<br/>fin<br/><br/>";

	  }	

			//if(!$mail->Send()) {
			//		echo "Error al enviar: " . $mail->ErrorInfo;
			//} else {
			//		echo "Mensaje enviado!";
			//}

	  $app->response->setStatus(200);

});






function quitar_tildes($cadena) {
$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
$texto = str_replace($no_permitidas, $permitidas ,$cadena);
return $texto;
}


$app->run();


?>
