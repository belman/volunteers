<?
function conectar(){
  $link = mysql_connect('localhost', 'root', 'sol') or die('No se pudo conectar: ' . mysql_error());
  mysql_select_db('volunteers') or die('No se pudo seleccionar la base de datos');
}

function crip_baja($i,$correo,$crip){
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
}

function crip_alta($i,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip){
  //funcion de validacion
  $x=md5($correo.'^#/\3,!');
  switch ($i){
    //encripta---------------------------------------------
    case  1: $crip=$x; break;
    //desencripta-------------------------------------------
    //case -1: $crip=base64_decode($crip);   break;
    //valida (true/false)-----------------------------------
    case  0: if($x==$crip){$crip=true;}else{$crip=false;}; break;
  }
  return $crip;
}



function ValidaMail($pMail) {//copiado de http://www.desarrolloweb.com/articulos/990.php
  if (ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$", $pMail ) ) {
    return true;
  }else{
    return false;
  }
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
