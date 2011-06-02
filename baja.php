<?
  include("function.php");
  //variables globales para enviar correo---------------------------------
  $direccion_pagina=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];//127.0.0.1/voluntarios/index.php
  $email_emisor="turboasm@hotmail.com";

  conectar();//conectar a la base de datos;
  //validar datos de entrada
  if(!empty($_POST)){     $_in = $_POST;}
  elseif(!empty($_GET)){  $_in = $_GET; }
  else{                   $_in = "";    }
  list($resultado,$registrar,$correo,$comentario,$crip)=validar($_in);

  if($resultado==1){
    switch ($registrar){
      case "enviar baja": $resultado=enviar_baja($correo,$comentario); break;
      case "baja":        $resultado=       baja($correo,$comentario,$crip); break;
    }
  }
  vistas($resultado,$correo,$comentario);
  die ();


function validar($_in){
  //Inicializando valores--------------------------------------------------
  $resultado="";
  $registrar="";
  $correo="";
  $comentario="";
  $crip="";
  //-------------------------------------------------------------------------
  //validando datos recibidos y enviar correo
  //------------------------------------------------------------------------
  if(!empty($_in)){
    $ok=1;
    $ko="";
    if(empty($_in["registrar"])){
      $registrar=""; $ok=2;
    }else{   
      $registrar = $_in["registrar"];   
      //correo---------------------------------------------------------------
      if(!empty($_in["correo"])){ 
        if(ValidaMail($_in["correo"]) && strlen($_in["correo"])<64){       $correo = $_in["correo"];       
        }else{                      $ko.=strlen($_in["correo"]);           $correo = ""; $ok=0; $ko.="El correo no es valido ".$_in['correo']."<br>\n";}
      }else{                                                               $correo = ""; $ok=0; $ko.="Introducca su correo<br>\n";}
      //comentario-----------------------------------------------------------
      if(!empty($_in["comentario"])){
        if(strlen($_in["comentario"])<1024){                              $comentario = $_in["comentario"];   }
      }else{                                                              $comentario = "";}
      //codigo encriptado de validacion
      if(!empty($_in['crip'])){                                                $crip=$_in['crip'];        
      }else{                                                                   $crip="";}
      //----------------------------------------------------------------------
      //----------------------------------------------------------------------
      if($ok==0){
        $resultado=$ko;
      }else{
        $resultado=$ok;
      }
    }
  }
  return array($resultado,$registrar,$correo,$comentario,$crip);
}


/*function crip_baja($i,$correo,$crip){
  //funcion de validacion------------------------------------
  $x=md5($correo.'^#/\3,!');
  switch ($i){
    //encripta---------------------------------------------
    case  1: $crip=$x; break;
    //desencripta-------------------------------------------
    //case -1: break;
    //valida (true/false)-----------------------------------
    case  0: if($x==$crip){$crip=true;}else{$crip=false;}; break;
  }
  return $crip;
}*/




function enviar_baja($correo,$comentario){
  //inicializacion de variables------------------------------------
  global $direccion_pagina;
  global $email_emisor;

  //codigo encrptado----------------------------------------------
  $crip=crip_baja(1,$correo,$crip);

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

  if(mail($correo,$asunto,$cuerpo,$headers)){ $resultado=6; //correo enviado
  }else{                                      $resultado="Ocurrio un fallo enviado el correo"; }  

  return "$resultado";
}



function baja($correo,$comentario,$crip){
  $correo   = mysql_real_escape_string($correo);
  $comentario=mysql_real_escape_string($comentario);

  //encriptacion de control------------------------------------------------------
  if(!crip_baja(0,$correo,$crip)){
    $resultado="Ha ocurrido un error durante la baja la validacion no coincide $correo-$comentario<br>\n$crip<br>\n".crip_baja(1,$correo,$comentario,$crip); 
    $resultado.="<br>\n--".crip_baja(0,$correo,$comentario,$crip)."-";
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
        $resultado=7;
      }
    }
  }
  return $resultado;
}




function vistas($resultado,$correo,$comentario){
  cabecera();
  echo "    <h1>Voluntarios en pruebas</h1>\n";
  switch ($resultado){
    //Se validaron los datos pero no se implemento un uso de registrar
    case 1: $resultado="Algo ha fallado1"; break; 
    //Llegaron datos pero sin boton de registrar
    case 2: $resultado="Algo ha fallado2"; break;
    //Correo enviado con exito
    case 6: $resultado="Correo de baja enviado con exito"; break;
    //Datos guardados con exito
    case 7: $resultado="Voluntario borrado con exito"; break;
  }
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
  cabecera_fin();
}



/*function ValidaMail($pMail) {//copiado de http://www.desarrolloweb.com/articulos/990.php
  if (ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$", $pMail ) ) {
    return true;
  }else{
    return false;
  }
} 

function conectar(){
  $link = mysql_connect('localhost', 'root', 'sol') or die('No se pudo conectar: ' . mysql_error());
  mysql_select_db('volunteers') or die('No se pudo seleccionar la base de datos');
}

function cabecera() {
?>
<html>
<head>
  <style>
    table,tr,td{
      border: 1px solid black;
    }
  </style>
  <script>
    function validarEmail(valor) {
      if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3,4})+$/.test(valor)){
        //alert("La dirección de email " + valor + " es correcta.");
      } else {
        alert("La dirección de email es incorrecta.");
      }
    }
  </script>
</head>
<body>
  <div name=cuerpo>
<?
}

function cabecera_fin() {
  echo "\n  </div>\n<body>\n</html>\n";
}*/

?>
