<?
  include "function.php";
  //variables globales para enviar correo---------------------------------
  $direccion_pagina=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];//127.0.0.1/voluntarios/index.php
  $email_emisor="turboasm@hotmail.com";

  conectar();//conectar a la base de datos;
  //validar datos de entrada
  if(!empty($_POST)){     $_in = $_POST;}
  elseif(!empty($_GET)){  $_in = $_GET; }
  else{                   $_in = "";    }
  list($resultado,$registrar,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip)=validar($_in);

  $resultado=1;
  $registrar="enviar";
  $nombre="fgfg";
  $apellidos="dfsaf";
  $correo="alex.rugby@gmail.com";
  
  if($resultado==1){
    switch ($registrar){
      case "enviar": $resultado=enviarcorreo($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp); break;
      case "grabar": $resultado=      grabar($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip); break;
    }
  }
  vistas($resultado,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp);
  die ();


function validar($_in){
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
    if(empty($_in["registrar"])){
      $registrar=""; $ok=2;
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
        if(ValidaMail($_in["correo"]) && strlen($_in["correo"])<64){       $correo = $_in["correo"];       
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


/*function crip($i,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip){
  $x=$correo.'^#/\3,!';
  switch ($i){
    //encripta---------------------------------------------
    case  1: $crip=md5($x); break;
    //desencripta-------------------------------------------
//    case -1: $crip=base64_decode($crip);   break;
    //valida (true/false)-----------------------------------
    case  0: $crip=(base64_encode($x)==$crip); break;
  }
  return $crip;
}*/




function enviarcorreo($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp){
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
  $crip=crip_alta(1,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip);

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
  echo "--$mail--";
  if($mail){ $resultado=3; //correo enviado
  }else{     $resultado="Ocurrio un fallo enviado el correo"; }  

  $c="--------------------------------------------------------------------";
  echo "$correo<br>$c<br>\n$asunto<br>$c<br>\n$cuerpo<br>$c<br>\n$headers<br>$c<br>\n";
  return "$resultado";
}



function grabar($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip){
  //encriptacion de control------------------------------------------------------
  if(crip_alta(0,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip)){$resultado=$crip."-".$correo; return $resultado;}

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
      $resultado=5;
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
      $resultado=4;
    }
  } 
  return $resultado;
}




function vistas($resultado,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp){
  //Inicializacion de variables-----------------------------------------
  $especialidades="";
  $disponibilidad="";

  //lista de especialidades----------------------------------------------
  $i=0;
  $sql="select id,nombre from lista_especialidades;"; 
  $result= mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    if(count($esp)>$i){if($row[0]==$esp[$i]){$i+=1; $select="selected";}else{$select="";}}else{$select="";}
    $especialidades.="<OPTION value=$row[0] $select>$row[1]</OPTION>\n";
  }

  //lista de disponibilidad-----------------------------------------------
  $i=0;
  $sql="select id,nombre from lista_disponibilidad;";
  $result= mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    if(count($dis)>$i){if($row[0]==$dis[$i]){$i+=1; $select="selected";}else{$select="";}}else{$select="";}
    $disponibilidad.="<OPTION value=$row[0] $select>$row[1]</OPTION>\n";
  }


  cabecera();
  echo "    <h1>Voluntarios en pruebas</h1>\n";
  switch ($resultado){
    //Se validaron los datos pero no se implemento un uso de registrar
    case 1: $resultado="Algo ha fallado1"; break; 
    //Llegaron datos pero sin boton de registrar
    case 2: $resultado="Algo ha fallado2"; break;
    //Correo enviado con exito
    case 3: $resultado="Correo enviado con exito"; break;
    //Datos guardados con exito
    case 4: $resultado="Datos guardados con exito"; break;
    case 5: $resultado="Datos actualizados con exito"; break;
//    case 6: $resultado="Voluntario borrado con exito"; break;
  }
//  echo "<h3>$nombre,$apellidos,$correo,$telefono,$codigo,$comentario<h3>";
?>
    <h3><?=$resultado?></h3>
    <FORM action="index.php" method="POST"> 
      <table><tr><td colspan=2>Voluntarios
        <tr><td>Nombre<td><input type=text size="32" maxlength="32" name=nombre value='<?=htmlentities($nombre)?>'>
        <tr><td>Apellidos<td><input type=text size="32" maxlength="32" name=apellidos value='<?=htmlentities($apellidos)?>'>
        <tr><td>Correo<td><input type=text size="32" maxlength="64" name=correo value='<?=htmlentities($correo)?>'>
        <tr><td>telefono<td><input type=text size="16" maxlength="16" name=telefono value='<?=$telefono?>'>
        <tr><td>Codigo postal<td><input type=text size="5" maxlength="5" name=codigo value='<?=$codigo?>'>
        <tr><td>Especialidad<td>
          <SELECT multiple size="4" name="lista_especialidades[]" >
            <?=$especialidades?>
         </SELECT>
        <tr><td>Disponibilidad<td>
          <SELECT multiple size="3" name="lista_disponibilidad[]">
            <?=$disponibilidad?>
          </SELECT>
        <tr><td>Comentario<td><textarea cols=40 rows=6 name="comentario"><?=htmlentities($comentario)?></textarea>
        <tr><td><td><input type=submit value=enviar name=registrar>
      </table>
    </form>
<?
  cabecera_fin();
}

/*
function ValidaMail($pMail) {//copiado de http://www.desarrolloweb.com/articulos/990.php
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
  echo "\n</div>\n<body>\n</html>\n";
}
*/
?>
