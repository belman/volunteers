<?
  include "modelo.volunteers.php";
  include "function.php";
  include "vista.php";

  mysql_conectar();//conectar a la base de datos;

  //validar datos de entrada
  if(!empty($_POST)){     $_in = $_POST; }
  elseif(!empty($_GET)){  $_in = $_GET; }
  else{                   $_in = "";    }
  list($resultado,$registrar,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip)=inscripcion_validar($_in);

  if($resultado==1){
    $resultado="La llamada de registro no es valida 1";
    switch ($registrar){
      case "enviar": 
        $resultado=inscripcion_correo($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp); 
        if($resultado==1){    vista_validacion("Correo enviado con exito");}
        else{                 vista_error($resultado);}
        break;
      case "grabar": 
        $resultado=mysql_inscripcion($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip); 
        if($resultado==2){    vista_validacion("Datos actualizados con exito");}
        elseif($resultado==3){vista_validacion("Datos guardados con exito");}
        else{                 vista_error($resultado);}
        break;
      default : break;
    }    
  }else{echo "<h3>$resultado</h3>";}
//  inscripcion_vista($resultado,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp);
  die ();


function inscripcion_validar($_in){
  //Inicializando valores--------------------------------------------------
  $resultado="";
  $registrar="";
  $nombre="";
  $apellidos="";
  $correo="";
  $telefono="";
  $codigo="";
  $comentario="";
  $dis=""; $esp="";
  $crip="";
  //-------------------------------------------------------------------------
  //validando datos recibidos y enviar correo
  //------------------------------------------------------------------------
  if(!empty($_in)){
    $ok=1;
    $ko="";
    $error="";
    if(empty($_in["registrar"])){
      $registrar=""; $resultado="La llamada de registro no es valida 2";
    }else{   
      $registrar = $_in["registrar"];   
      //nombre---------------------------------------------------------------
      if(!empty($_in["nombre"])){
        if($_in["nombre"]==""){                                           $nombre = ""; $ok=0; $ko.="Intruducca su nombre<br>\n";
        }else{
          if(strlen($_in["nombre"])>2 && strlen($_in["nombre"])<32){      $nombre = $_in["nombre"];       
          }else{                                                          $nombre = ""; $ok=0; $ko.="El nombre no es valido ".$_in["nombre"]."<br>\n";  }
        }                                                                     
      }else{                                                              $nombre=""; $ok=0; $ko.="Intruducca su nombre<br>\n";  }
      //apellidos------------------------------------------------------------
      if(!empty($_in["apellidos"])){
        if($_in["apellidos"]==""){                                        $apellidos = ""; $ok=0; $ko.="Intruducca sus apellidos<br>\n";
        }else{
          if(strlen($_in["apellidos"])>2 && strlen($_in["apellidos"])<32){$apellidos = $_in["apellidos"];       
          }else{                                                         $apellidos="";$ok=0;$ko.="Los apellidos son invalidos ".$_in['apellidos']."<br>\n";}
        }                
      }else{                                                              $apellidos =""; $ok=0; $ko.="Intruducca sus apellidos<br>\n";  }
      //correo---------------------------------------------------------------
      if(!empty($_in["correo"])){ 
        if(function_validamail($_in["correo"]) && strlen($_in["correo"])<64){       $correo = $_in["correo"];       
        }else{                      $ko.=strlen($_in["correo"]);           $correo = ""; $ok=0; $ko.="El correo no es valido ".$_in['correo']."<br>\n";}
      }else{                                                               $correo = ""; $ok=0; $ko.="Introducca su correo<br>\n";}
      //telefono-------------------------------------------------------------
      if(!empty($_in["telefono"])){
        if(is_numeric($_in["telefono"]) && $_in["telefono"]>99999999 && $_in["telefono"]<10000000000000000){
                                                                          $telefono = $_in["telefono"];            
        }else{                                                            $telefono = ""; $ok=0; $ko.="El telefono no es valido ".$_in['telefono']."<br>\n";}
      }else{                                                              $telefono = ""; $ok=0; $ko.="Introducca su telefono<br>\n";}
      //codigo---------------------------------------------------------------
      if(!empty($_in["codigo"])){
        if(is_numeric($_in["codigo"]) && $_in["codigo"]>9999 && $_in["codigo"]<100000){
                                                                          $codigo = $_in["codigo"];       
        }else{                                                            $codigo = ""; $ok=0;$ko.="El codigo postal no es valido ".$_in['codigo']."<br>\n";}
      }else{                                                              $codigo = ""; $ok=0; $ko.="Introducca su codigo postal<br>\n";}
      //comentario-----------------------------------------------------------
      if(!empty($_in["comentario"])){
        if(strlen($_in["comentario"])<1024){                              $comentario = $_in["comentario"];   }
      }else{                                                              $comentario = "";}
      //especialidades-------------------------------------------------------
      if(!empty($_in["esp"])){$_in["lista_especialidades"]=explode(",",$_in["esp"]);}
      if(!empty($_in["lista_especialidades"])){ 
        $i=0;
        foreach($_in["lista_especialidades"] as $esp2){
          if(is_numeric($esp2)){                                               $esp[$i]=$esp2; $i+=1; 
          }else{                                                               $esp = ""; $ok=0; $ko.="entrada ilegal de datos $esp2<br>\n"; break;}
        }
      }else{                                                                   $esp = ""; $ok=0; $ko.="Introducca sus especialidades<br>\n";}
      //disponibilidad------------------------------------------------------
      if(!empty($_in["dis"])){$_in["lista_disponibilidad"]=explode(",",$_in["dis"]);}
      if(!empty($_in["lista_disponibilidad"])){
        $i=0;
        foreach($_in["lista_disponibilidad"] as $dis2){
          if(is_numeric($dis2)){                                               $dis[$i]=$dis2; $i+=1; 
          }else{                                                               $dis = ""; $ok=0; $ko.="entrada ilegal de datos $dis2<br>\n"; break;}
        }
      }else{                                                                   $dis = ""; $ok=0; $ko.="Introducca sus disponibilidad<br>\n";}
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
  return array($resultado,$registrar,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip);
}


function inscripcion_correo($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp){
  //inicializacion de variables------------------------------------
  global $direccion_pagina;
  global $email_emisor;

  $especialidades=""; $espe="";
  $disponibilidad=""; $disp="";
  $crip="";

  //Seleccion de especialidades------------------------------------
  $sql="select * from lista_especialidades where id=0";
  foreach($esp as $esp2){
    if(is_numeric($esp2)){$sql.=" or id=$esp2 ";}
  }
  $result= mysql_query($sql.";") ;
  while ($row = mysql_fetch_array($result)) {
    $especialidades.=" ".$row['nombre'];
    if($espe==""){$espe.=$row['id'];}else{$espe.=",".$row['id'];}
  }

  //Seleccion de disponibilidad------------------------------------
  $sql="select * from lista_disponibilidad where id=0";
  foreach($dis as $dis2){
    if(is_numeric($dis2)){$sql.=" or id=$dis2";}
  }
  $result= mysql_query($sql.";");
  while ($row = mysql_fetch_array($result)) {
    $disponibilidad.=" ".$row['nombre'];
    if($disp==""){$disp.=$row['id'];}else{$disp.=",".$row['id'];}
  }

  //codigo encrptado----------------------------------------------
  $crip=function_crip_alta(1,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip);

  //link de validacion-------------------------------------------
  $validar ="?registrar=grabar&correo=".urlencode($correo)."&crip=".urlencode($crip)."&nombre=".urlencode($nombre);
  $validar.="&apellidos=".urlencode($apellidos)."&codigo=$codigo";
  $validar.="&telefono=$telefono&esp=$espe&dis=$disp&comentario=".urlencode($comentario);

  //asunto---------------------------------------------------------
  $asunto ="Validacion en la inscripcion de la pagina de voluntarios";
 
  //cuerpo---------------------------------------------------------
  $cuerpo ="<html>\n<head>\n";
  $cuerpo.="<title>Validacion en la inscripcion de voluntarios</title>\n";
  $cuerpo.="</head>\n<body>\n<h1>Hola amigos!</h1>\n";
  $cuerpo.="<b>Bienvenidos a voluntarios toma la plaza</b>.<br> Se ha suscrito a la pagina de voluntarios sus datos son:<br>\n";
  $cuerpo.=" $nombre $apellidos <br>\n Su correo: $correo <br>\n Codigo Postal: $codigo <br>\n Telefono: $telefono <br>\n ";
  $cuerpo.="Disponibilidad: $disponibilidad <br>\n Especialidades: $especialidades<br>\n Comentario: $comentario <br>\n ";
  $cuerpo.="Para validar los datos debes de pinchar en el siguiente link.";
  $cuerpo.="<a href='http://".$direccion_pagina.$validar."'>link</a><br>\n";
  $cuerpo.=$direccion_pagina.$validar."</body>\n</html>";
  
  //para el envío en formato HTML
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
  $headers .= "From: Prueba de voluntarios <$email_emisor>\r\n";
  $headers .= "Reply-To: $email_emisor\r\n";
  $headers .= "Return-path: $email_emisor\r\n";
  //$headers .= "Cc:  $email_emisor\r\n";
  //$headers .= "Bcc: $email_emisor\r\n";

  $mail=mail($correo,$asunto,$cuerpo,$headers);
  //echo "--$mail--";
  if($mail){ $resultado=1; //correo enviado
  }else{     $resultado="Ocurrio un fallo enviado el correo"; }  

  return "$resultado";
}


/*
function inscripcion_grabar($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip){
  //encriptacion de control------------------------------------------------------
  if(!function_crip_alta(0,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip)){
    $resultado="Ha ocurrido un error durante la baja la validacion no coincide<br>\n"; 
  }else{
    //Inicializacion de variables---------------------------------------------------
    $nombre   = mysql_real_escape_string($nombre);
    $apellidos= mysql_real_escape_string($apellidos);
    $correo   = mysql_real_escape_string($correo);
    $comentario=mysql_real_escape_string($comentario);

    //comprobacion de existencia de correo -----------------------------------------
    $sql="select * from volunteers where correo='$correo';";
    $result=mysql_query($sql);
    $row = mysql_fetch_array($result);
    if($row[0]>0){
      //actualizacion----------------------------------------------------------------
      $id_user=$row[0];
      $sql0 ="update volunteers set fedit=current_timestamp, nombre='$nombre', apellidos='$apellidos', correo='$correo', telefono='$telefono', ";
      $sql0.="codigo='$codigo', comentario='$comentario' where id='$id_user';";
      if(!mysql_query($sql0)){
        $resultado="Ocurrio un error en la actualizacion de la base de datos".mysql_error();
      }else{
        $sql1 ="DELETE FROM especialidades WHERE id_user='$id_user';"; mysql_query($sql1);
        $sql2 ="DELETE FROM disponibilidad WHERE id_user='$id_user';"; mysql_query($sql2);
        foreach ($dis as $dis2){
          $sql ="insert into disponibilidad (fecha,lista,id_user) values (current_timestamp,'$dis2','$id_user');"; mysql_query($sql);
        }
        foreach ($esp as $esp2){
          $sql ="insert into especialidades (fecha,lista,id_user) values (current_timestamp,'$esp2','$id_user');"; mysql_query($sql);
        }
        $resultado=2;
      }
    }else{
      //creacion------------------------------------------------------------------------
      $sql ="insert into volunteers (fecha,fedit,nombre,apellidos,correo,telefono,codigo,comentario) values ";
      $sql.="(current_timestamp,current_timestamp,'$nombre','$apellidos','$correo','$telefono','$codigo','$comentario');";
      if(!mysql_query($sql)){
        $resultado="Ocurrio un error en la query".mysql_error();
      }else{
        $sql="select * from volunteers where correo='$correo';";
        $result=mysql_query($sql);
        $row =  mysql_fetch_array($result);
        $id_user=$row["id"];
        //bucle por cada $esp[0:n] i dis[0:n]
        foreach ($dis as $dis2){
          $sql ="insert into disponibilidad (fecha,lista,id_user) values (current_timestamp,'$dis2','$id_user');"; mysql_query($sql);
        }
        foreach ($esp as $esp2){
          $sql ="insert into especialidades (fecha,lista,id_user) values (current_timestamp,'$esp2','$id_user');"; mysql_query($sql);
        }
        $resultado=3;
      }
    } 
  }
  return $resultado;
}
*/





?>
