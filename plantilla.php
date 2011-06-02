<?
  conectar();
  $resultado=registro($_POST);
  vistas($_POST,$resultado);


function correo($_POST){
  if(1==2){
    $destinatario = "pepito@desarrolloweb.com";
    $asunto = "Este mensaje es de prueba";
    $cuerpo = '
<html>
<head>
   <title>Prueba de correo</title>
</head>
<body>
<h1>Hola amigos!</h1>
<p>
<b>Bienvenidos a mi correo electrónico de prueba</b>. Estoy encantado de tener tantos lectores. Este cuerpo del mensaje es del artículo de envío de mails por PHP. Habría que cambiarlo para poner tu propio cuerpo. Por cierto, cambia también las cabeceras del mensaje.
</p>
</body>
</html>
';

    //para el envío en formato HTML
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    //dirección del remitente
    $headers .= "From: Miguel Angel Alvarez <pepito@desarrolloweb.com>\r\n";
    //dirección de respuesta, si queremos que sea distinta que la del remitente
    $headers .= "Reply-To: mariano@desarrolloweb.com\r\n";
    //ruta del mensaje desde origen a destino
    $headers .= "Return-path: holahola@desarrolloweb.com\r\n";
    //direcciones que recibián copia
    $headers .= "Cc: maria@desarrolloweb.com\r\n";
    //direcciones que recibirán copia oculta
    $headers .= "Bcc: pepe@pepe.com,juan@juan.com\r\n";
    mail($destinatario,$asunto,$cuerpo,$headers); 
  }
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
  $nombre=htmlentities($_POST["nombre"]);
  $apellidos=htmlentities($_POST["apellidos"]);
  $correo=htmlentities($_POST["correo"]);
  $telefono=$_POST["telefono"];
  $codigo=$_POST["codigo"];
  $comentario=htmlentities($_POST["comentario"]);
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
    echo "    <h3>La informcaion del voluntario ha sido guardada correctamente<br>\nMuchas gracias<br>\n</h3>";
  }else{
?>
    <h3><?=$resultado?></h3>
    <FORM action="" method="POST"> 
      <table><tr><td colspan=2>Voluntarios
        <tr><td>Nombre<td><input type=text size="32" maxlength="32" name=nombre value='<?=$nombre?>'>
        <tr><td>Apellidos<td><input type=text size="32" maxlength="32" name=apellidos value='<?=$apellidos?>'>
        <tr><td>Correo<td><input type=text size="32" maxlength="64" name=correo value='<?=$correo?>'>
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
        <tr><td>Comentario<td><textarea cols=40 rows=6 name="comentario"><?=$comentario?></textarea>
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
