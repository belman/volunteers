<?
  include("function.php");
  include("modelo.volunteers.php");
  include("vista.php");

  //variables globales para enviar correo---------------------------------
  $direccion_pagina=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];//127.0.0.1/voluntarios/index.php
  $email_emisor="turboasm@hotmail.com";

  mysql_conectar();//conectar a la base de datos;
  //validar datos de entrada
  if(!empty($_POST)){     $_in = $_POST;}
  elseif(!empty($_GET)){  $_in = $_GET; }
  else{                   $_in = "";    }
  list($resultado,$registrar,$correo,$comentario,$crip)=baja_validar($_in);
  if($resultado==1){
    $resultado="La llamada de registro no es valida 1";
    switch ($registrar){
      case "enviar baja": 
        $resultado=baja_enviar($correo,$comentario); 
        if($resultado==1){ vista_validacion("Correo de baja enviado con exito");}
        else{              vista_error($resultado);}
        break;
      case "baja":        
        $resultado=mysql_baja($correo,$comentario,$crip); 
        if($resultado==2){ vista_validacion("Voluntario borrado con exito");}
        else{              vista_error($resultado);}
        break;
      default : break;
    }
  }else{echo "<h3>$resultado</h3>\n";}
  //vistas($resultado,$correo,$comentario);
  die ();


function baja_validar($_in){
  //Inicializando valores--------------------------------------------------
  $resultado="";
  $registrar="";
  $correo="";
  $comentario="";
  $crip="";
  $resultado="";
  //-------------------------------------------------------------------------
  //validando datos recibidos y enviar correo
  //------------------------------------------------------------------------
  if(!empty($_in)){
    $ok=1;
    $ko="";
    if(empty($_in["registrar"])){
      $registrar=""; $ok=0; $ko="La llamada de registro no es valida 2";
    }else{   
      $registrar = $_in["registrar"];   
      //correo---------------------------------------------------------------
      if(!empty($_in["correo"])){ 
        if(function_validamail($_in["correo"]) && strlen($_in["correo"])<64){       $correo = $_in["correo"];       
        }else{                                                             $correo = ""; $ok=0; $ko.="El correo no es valido ".$_in['correo']."<br>\n";}
      }else{                                                               $correo = ""; $ok=0; $ko.="Introducca su correo<br>\n";}
      //comentario-----------------------------------------------------------
      if(!empty($_in["comentario"])){
        if(strlen($_in["comentario"])<1024){                              $comentario = $_in["comentario"];   }
      }else{                                                              $comentario = "";}
      //codigo encriptado de validacion
      if(!empty($_in['crip'])){                                                $crip=$_in['crip'];        
      }else{                                                                   $crip="";}
    }
  }else{$ok=0; $ko="No se han introducido datos";}
  //----------------------------------------------------------------------
  //----------------------------------------------------------------------
  if($ok==0){
    $resultado=$ko;
  }else{
    $resultado=$ok;
  }
  return array($resultado,$registrar,$correo,$comentario,$crip);
}



function baja_enviar($correo,$comentario){
  //inicializacion de variables------------------------------------
  global $direccion_pagina;
  global $email_emisor;

  //codigo encrptado----------------------------------------------
  $crip=function_crip_baja(1,$correo,$crip);

  //link de validacion-------------------------------------------
  $validar ="?registrar=baja&correo=".urlencode($correo)."&crip=".urlencode($crip)."&comentario=".urlencode($comentario);

  //asunto---------------------------------------------------------
  $asunto ="Validacion en la inscripcion de la pagina de voluntarios";
 
  //cuerpo---------------------------------------------------------
  $cuerpo ="<html>\n<head>\n";
  $cuerpo.="<title>Validacion en la baja de voluntarios</title>\n";
  $cuerpo.="</head>\n<body>\n<h1>Hola amigos!</h1>\n";
  $cuerpo.="<b>Bienvenidos a voluntarios toma la plaza</b>.<br> Ha pedido la baja de voluntarion:<br>\n";
  $cuerpo.="Su correo: $correo <br>\n ";
  $cuerpo.="Comentario: $comentario <br>\n ";
  $cuerpo.="Para validar los datos debes de pinchar en el siguiente link.";
  $cuerpo.="<a href='".$direccion_pagina.$validar."'>link</a><br>\n";
  $cuerpo.=$direccion_pagina.$validar."</body>\n</html>";
  
  //para el envío en formato HTML
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
  $headers .= "From: Baja de prueba de voluntarios <$email_emisor>\r\n";
  $headers .= "Reply-To: $email_emisor\r\n";
  $headers .= "Return-path: $email_emisor\r\n";
  //$headers .= "Cc:  $email_emisor\r\n";
  //$headers .= "Bcc: $email_emisor\r\n";

  if(mail($correo,$asunto,$cuerpo,$headers)){ $resultado=1; //correo enviado
  }else{                                      $resultado="Ocurrio un fallo enviado el correo"; }  

  return "$resultado";
}


/*
function baja_grabar($correo,$comentario,$crip){
  $correo   = mysql_real_escape_string($correo);
  $comentario=mysql_real_escape_string($comentario);

  //encriptacion de control------------------------------------------------------
  if(!function_crip_baja(0,$correo,$crip)){
    $resultado="Ha ocurrido un error durante la baja la validacion no coincide<br>\n"; 
    //$resultado.="<br>\n$crip<br>\n".crip_baja(0,$correo,$comentario,$crip)."<br>\n";
  }else{
    //Inicializacion de variables---------------------------------------------------
    $correo   = mysql_real_escape_string($correo);
    $comentario=mysql_real_escape_string($comentario);
    //comprobacion de existencia de correo -----------------------------------------
    $sql="select * from volunteers where correo='$correo';";
    $result=mysql_query($sql);
    $row = mysql_fetch_array($result);
    if(!$row[0]>0){
      $resultado="El usuario no existia";
    }else{
      //actualizacion----------------------------------------------------------------
      $id_user=$row[0];
      $sql0 ="delete from volunteers where id=$id_user";
      $sql1 ="DELETE FROM especialidades WHERE id_user='$id_user';";
      $sql2 ="DELETE FROM disponibilidad WHERE id_user='$id_user';";
      $sql3 ="insert into bajas (fecha,correo,comentario) values (current_timestamp,'$correo','$comentario');";
      if(!mysql_query($sql0)){
        $resultado="Ocurrio un error en la actualizacion de la base de datos-0-".mysql_error();
      }elseif(!mysql_query($sql1)){
        $resultado="Ocurrio un error en la actualizacion de la base de datos-1-".mysql_error();
      }elseif(!mysql_query($sql2)){
        $resultado="Ocurrio un error en la actualizacion de la base de datos-2-".mysql_error();
      }elseif(!mysql_query($sql3)){
        $resultado="Ocurrio un error en la actualizacion de la base de datos-3-".mysql_error();
      }else{
        $resultado=2;
      }
    }
  }
  return $resultado;
}
*/


/*
function vistas($resultado,$correo,$comentario){
  cabecera();
  echo "    <h1>Voluntarios en pruebas</h1>\n";
  switch ($resultado){
    //Correo enviado con exito
    case 1: echo "<h3>Correo de baja enviado con exito</h3>"; break;
    //Datos guardados con exito
    case 2: echo "<h3>Voluntario borrado con exito</h3>"; break;
    default:
?>
    <h3><?=$resultado?></h3>
    <h3>Darse de baja</h3>
    <FORM action="baja.php" method="POST">
      <table><tr><td colspan=2>Darse de baja
        <tr><td>Correo<td><input type=text size="32" maxlength="64" name=correo value='<?=htmlentities($correo)?>'>
        <tr><td>Comentario<td><textarea cols=40 rows=6 name="comentario"><?=htmlentities($comentario)?></textarea>
        <tr><td><td><input type=submit value='enviar baja' name=registrar>
      </table>
    </form>
<?
  }
  cabecera_fin();
}
*/



