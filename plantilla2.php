<?
  //crear/rehacer funcion validar para posible POST y GET entrada array salida por list
  conectar();//conectar a la base de datos;
   $resultado=registra2($_POST);
  vistas($_POST,$resultado);
  die ();


function validar($_in);
  //-------------------------------------------------------------------------
  //validando datos recibidos y enviar correo
  //------------------------------------------------------------------------
  if(empty($_POST)){echo "hola post";}
  if(empty($_GET)){echo "no get";}
  echo "nada";
  if(!empty($_POST)){
    $ok=1;
    $ko="";
    if(empty($_POST["registrar"])){
      $registrar=""; $ok=2;
    }else{   
      $registrar = $_POST["registrar"];   
      //nombre---------------------------------------------------------------
      if(!empty($_POST["nombre"])){
        if($_POST["nombre"]=""){                                              $nombre = ""; $ok=0; $ko.="Intruducca su nombre<br>\n";
        }else{
          if(strlen($_POST["nombre"])>2 && strlen($_POST["nombre"])<32){      $nombre = $_POST["nombre"];       
          }else{                                                              $nombre = ""; $ok=0; $ko.="El nombre no es valido<br>\n";  }
        }                                                                     
      }else{    $nombre=""; $ok=0; $ko.="Intruducca su nombre<br>\n";  }
      //apellidos------------------------------------------------------------
      if(!empty($_POST["apellidos"])){
        if($_POST["apellidos"]=""){                                            $apellidos = ""; $ok=0; $ko.="Intruducca sus apellidos<br>\n";
        }else{
          if(strlen($_POST["apellidos"])>2 && strlen($_POST["apellidos"])<32){ $apellidos = $_POST["apellidos"];       
          }else{                                                               $apellidos = ""; $ok=0; $ko.="Los apellidos no son validos<br>\n";  }
        }                                                                     
      }else{    $nombre=""; $ok=0; $ko.="Intruducca sus apellidos<br>\n";  }
      //correo---------------------------------------------------------------
      if(!empty($_POST["correo"])){ 
        if(ValidarMail($_POST["correo"])){                                     $correo = $_POST["correo"];       
        }else{                                                                 $correo = ""; $ok=0; $ko.="El correo no es valido<br>\n";}
      }else{                                                                   $correo = ""; $ok=0; $ko.="Introducca su correo<br>\n";}
      //telefono-------------------------------------------------------------
      if(!empty($_POST["telefono"])){
        if(is_numeric($_POST["telefono"]) && $_POST["telefono"]>99999999 && $_POST["telefono"]<10000000000000000){
                                                                               $telefono = $_POST["telefono"];     
        }else{                                                                 $telefono = ""; $ok=0; $ko.="El telefono es incorrecto<br>\n";}
      }else{                                                                   $telefono = ""; $ok=0; $ko.="Introducca su telefono<br>\n";}
      //codigo---------------------------------------------------------------
      if(!empty($_POST["codigo"])){
        if(is_numeric($_POST["codigo"]) && $_POST["codigo"]>9999 && $_POST["codigo"]<100000){
                                                                               $codigo = $_POST["codigo"];       
        }else{                                                                 $codigo = ""; $ok=0; $ko.="El codigo postal no es valido<br>\n";}
      }else{                                                                   $codigo = ""; $ok=0; $ko.="Introducca su codigo postal<br>\n";}
      //comentario-----------------------------------------------------------
      if(!empty($_POST["comentario"])){                                        $comentario = $_POST["comentario"];   
      }else{                                                                   $comentario = "";}
      //especialidades-------------------------------------------------------
      if(!empty($_POST["lista_especialidades"])){ 
        $i=0;
        foreach($_POST["lista_especialidades"] as $esp2){
          if(is_numeric($esp2)){                                               $esp[$i]=$esp2; $i+=1; 
          }else{                                                               $esp = ""; $ok=0; $ko.="entrada ilegal de datos $esp2<br>\n";}
        }
      }else{                                                                   $esp = ""; $ok=0; $ko.="Introducca sus especialidades<br>\n";}
      //disponibilidad------------------------------------------------------
      if(!empty($_POST["lista_disponibilidad"])){
        $i=0;
        foreach($_POST["lista_disponibilidad"] as $dis2){
          if(is_numeric($dis2)){                                               $dis[$i]=$dis2; $i+=1; 
          }else{                                                               $dis = ""; $ok=0; $ko.="entrada ilegal de datos $esp2<br>\n";}
        }
      }else{                                                                   $dis = ""; $ok=0; $ko.="Introducca sus disponibilidad<br>\n";}
      //----------------------------------------------------------------------
      //enviar correo
      //----------------------------------------------------------------------
      if($ok==1){
//        enviarcorreo($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$esp,$dis);
        $resultado=1;
      }else{
        $resultado=$ko;
      }
    }
  }
  return array();
}



function registra2($_POST){
  if(empty($_POST["registrar"])){
    $resultado="";
  }else{
    //validando datos recibidos
    $nombre=$_POST["nombre"];
    $apellidos=$_POST["apellidos"];
    $correo=$_POST["correo"];
    $telefono=$_POST["telefono"];
    $codigo=$_POST["codigo"];
    $comentario=$_POST["comentario"];
    $esp=$_POST["lista_especialidad"];
    $dis=$_POST["lista_disponibilidad"];
    $ok=0;
    $ko="";

    if(strlen($nombre)>2 && strlen($nombre)<32){$ok+=1;}else{$ko.="El nombre no es correcto $nombre<br>\n";}
    if(strlen($apellidos)>3 && strlen($apellidos)<64){$ok+=1;}else{$ko.="Los apellidos no son correcto $apellidos<br>\n";}
    if(strlen($correo)>7 && strlen($correo)<64 && ValidaMail($correo)){$ok+=1;}else{$ko.="El correo no es valido $correo<br>\n";}
    if(is_numeric($telefono) && $telefono>99999999 && $telefono<10000000000000000){$ok+=1;}else{$ko.="El telefono no es correcto $telefono<br>\n";}
    if(is_numeric($codigo) && $codigo>9999 && $codigo<100000){$ok+=1;}else{$ko.="El codigo postal no es correcto $codigo<br>\n";}
    if($esp[0]>0){$ok+=1;}else{$ko.="Se debe seleccionar alguna especialidad<br>\n";}
    if($dis[0]>0){$ok+=1;}else{$ko.="Se debe seleccionar alguna especialidad<br>\n";}

    foreach ($dis as $dis2){
      if(!is_numeric($dis2)){$ok=0; $ko.="entrada ilegal de datos $dis2<br>\n";}
    }
    foreach ($esp as $esp2){
      if(!is_numeric($esp2)){$ok=0; $ko.="entrada ilegal de datos $esp2<br>\n";}
    }
    //Validacion final y preparacion de la query
    if($ok!=7){
      $resultado="Los datos estan incompletos, rellenelos correctamente y de le a enviar\n<br>$ko";
    }else{
 //     enviarcorreo($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$esp,$dis);
      $resultado=1;
      //comprobacion de correo repetido, 
//      $sql="select * from volunteers where correo='$correo';";
//      $result=mysql_query($sql);
//      $row = mysql_fetch_array($result);
/*      if($row[0]>0){$resultado="El correo ya existe<input type=button name=editar value=editar onclick=''>";}else{
/*      if($row[0]>0){$resultado="El correo ya existe<input type=button name=editar value=editar onclick=''>";}else{


        $sql ="insert into volunteers (fecha,nombre,apellidos,correo,telefono,codigo,comentario) values ";
        $sql.="(current_timestamp,'$nombre','$apellidos','$correo','$telefono','$codigo','$comentario');";
        //echo "$sql<br>\n";
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
          $resultado=1;
        }
      }*/
    }
  }
  return $resultado;
}



function encriptar($correo){
  $validar=base64_encode($correo);
  return $validar;
}


function desencriptar($validar){
  $correo=base64_decode($validar);
  return $correo;
}


function enviarcorreo($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$esp,$dis){
  $espe="";
  $disp="";
  if(!empty($esp)){
    $sql="select * from lista_especialidades where id=0";
    foreach ($esp as $esp2){
      if(is_numeric($esp2)){$sql.=" or id=$esp2"; $espe=$esp2.",";}
    }
  }
  $result= mysql_query($sql) or die(mysql_error());
  $especialidad="";
  while ($row = mysql_fetch_array($result)) {
    $especialidades.=" ".$row['lista'];
  }

  if(!empty($dis)){
    $sql="select * from lista_disponibilidad where id=0";
    foreach ($dis as $dis2){
      if(is_numeric($dis)){$sql.=" or id=$dis2"; $disp=$dis2.",";}
    }
  }
  $result= mysql_query($sql) or die(mysql_error());
  $disponibilidad="";
  while ($row = mysql_fetch_array($result)) {
    $disponibilidad.=" ".$row['lista'];
  }
  $validar ="index.php?correo='$correo'&crip='".encriptar($correo)."'&nombre='$nombre'&apellidos=$apellidos&codigo=$codigo";
  $validar.="&telefono=$telefono&esp=$espe&dis=$disp&comentario='$comentario'";
//    $correo = "";
  $headers="";
  $asunto ="Validacion en la inscripcion de la pagina de voluntarios";
  $cuerpo ="<html>\n<head>\n";
  $cuerpo.="<title>Validacion en la inscripcion de voluntarios</title>\n";
  $cuerpo.="</head>\n<body>\n<h1>Hola amigos!</h1>\n";
  $cuerpo.="<p><b>Bienvenidos a voluntarios toma la plaza</b>. Se ha suscrito a la pagina de voluntarios sus datos son:\n";
  $cuerpo.=" $nombre $apellidos \n Su correo: $correo \n Codigo Postal: $codigo \n Telefono: $telefono \n Disponibilidad: $disponibilidad ";
  $cuerpo.="Especialidades: $especialidades\n Comentario: $comentario \n Para validar los datos debes de pinchar en el siguiente link.</p>";
  $cuerpo.="<a href='127.0.0.1:/voluntarios/index.php'>index</a>\n</body>\n</html>";

  //para el envío en formato HTML
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
  //dirección del remitente
  $headers .= "From: alejandro magno <turboasm@hotmail.com>\r\n";
  //dirección de respuesta, si queremos que sea distinta que la del remitente
  $headers .= "Reply-To: turboasm@hotmail.com\r\n";
  //ruta del mensaje desde origen a destino
  $headers .= "Return-path: turboasm@hotmailcom\r\n";
  //direcciones que recibián copia
  $headers .= "Cc: turboasm@hotmail.com\r\n";
  //direcciones que recibirán copia oculta
  $headers .= "Bcc: turboasm@hotmail.com\r\n";

  mail($correo,$asunto,$cuerpo,$headers); 
}

function registro($_POST){
  if($_POST["registrar"]==""){
    $resultados="";
  }else{
    $nombre=mysql_real_escape_string(htmlentities($_POST["nombre"]));
    $apellidos=mysql_real_escape_string(htmlentities($_POST["apellidos"]));
    $correo=mysql_real_escape_string(htmlentities($_POST["correo"]));
    $telefono=$_POST["telefono"];
    $codigo=$_POST["codigo"];
    $comentario=mysql_real_escape_string(htmlentities($_POST["comentario"]));
    $esp=$_POST["lista_especialidad"];
    $dis=$_POST["lista_disponibilidad"];
    $ok=0;
    $ko="";
//comprobacion de usuario.
    if(strlen($nombre)>2 && strlen($nombre)<32){$ok+=1;}else{$ko.="El nombre no es correcto $nombre<br>\n";}
    if(strlen($apellidos)>3 && strlen($apellidos)<64){$ok+=1;}else{$ko.="Los apellidos no son correcto $apellidos<br>\n";}
    if(strlen($correo)>7 && strlen($correo)<64 && ValidaMail($correo)){$ok+=1;}else{$ko.="El correo no es valido $correo<br>\n";}
    if(is_numeric($telefono) && $telefono>99999999 && $telefono<10000000000000000){$ok+=1;}else{$ko.="El telefono no es correcto $telefono<br>\n";}
    if(is_numeric($codigo) && $codigo>9999 && $codigo<100000){$ok+=1;}else{$ko.="El codigo postal no es correcto $codigo<br>\n";}
    if($esp[0]>0){$ok+=1;}else{$ko.="Se debe seleccionar alguna especialidad<br>\n";}
    if($dis[0]>0){$ok+=1;}else{$ko.="Se debe seleccionar alguna especialidad<br>\n";}
    
    foreach ($dis as $dis2){
      if(!is_numeric($dis2)){$ok=0; $ko.="entrada ilegal de datos $dis2<br>\n";}
    } 
    foreach ($esp as $esp2){
      if(!is_numeric($esp2)){$ok=0; $ko.="entrada ilegal de datos $esp2<br>\n";}
    } 
    if($ok!=7){
      $resultado="Los datos estan incompletos, rellenelos correctamente y de le a enviar\n<br>$ko";
    }else{  
      //comprobacion de correo repetido, 
      $sql="select * from volunteers where correo='$correo';";
      $result=mysql_query($sql);
      $row = mysql_fetch_array($result);
      if($row[0]>0){$resultado="El correo ya existe<input type=button name=editar value=editar onclick=''>";}else{

        $sql ="insert into volunteers (fecha,nombre,apellidos,correo,telefono,codigo,comentario) values ";
        $sql.="(current_timestamp,'$nombre','$apellidos','$correo','$telefono','$codigo','$comentario');";
        //echo "$sql<br>\n";
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
          $resultado=1;
        }
      } 
    }
  }
  return $resultado;
}


function vistas($_POST,$resultado){
  $nombre=$_POST["nombre"];
  $apellidos=$_POST["apellidos"];
  $correo=$_POST["correo"];
  $telefono=$_POST["telefono"];
  $codigo=$_POST["codigo"];
  $comentario=($_POST["comentario"]);
  $esp=$_POST["lista_especialidad"];
  $dis=$_POST["lista_disponibilidad"];

  $sql="select id,nombre from lista_especialidades;";
  $result= mysql_query($sql) or die(mysql_error());
  $i=0;
  $especialidad="";
  while ($row = mysql_fetch_array($result)) {
    if($row[0]==$esp[$i]){$i+=1; $select="selected";}else{$select="";}
    $especialidad.="             <OPTION value=$row[0] $select>$row[1]</OPTION>\n";
  }

  $sql="select id,nombre from lista_disponibilidad;";
  $result= mysql_query($sql) or die(mysql_error());
  $disponibilidad="";
  $i=0;
  while ($row = mysql_fetch_array($result)) {
    if($row[0]==$dis[$i]){$i+=1; $select="selected";}else{$select="";}
    $disponibilidad.="              <OPTION value=$row[0] $select>$row[1]</OPTION>\n";
  }


  cabecera();
  echo "    <h1>Voluntarios en pruebas</h1>\n";
  if ($resultado==1){
    echo "    <h3>A continuacion se esta enviando un correo de confirmacion, puede tardar unos segundos<br>\nMuchas gracias<br>\n</h3>";
    if(enviarcorreo($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$esp,$dis)){
      echo "    <h3>El correo ha sido enviado con exito</h3>";
    }else{
      echo "    <h3>Ocurrio un error al enviar el mensaje</h3>";
    }
  }else{
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
          <SELECT multiple size="4" name="lista_especialidad[]" >
            <?=$especialidad?>
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
  }
  cabecera_fin();
}

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

?>
