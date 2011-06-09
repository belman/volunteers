<?
//--------------------------------------------------------------------------------------------------------
  $direccion_pagina=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];//127.0.0.1/voluntarios/index.php
  $email_emisor="noreplay.voluntarios@tomalaplaza.net";
//----------------------------------------------------------------------------------------------------------

function function_crip_baja($i,$correo,$crip){
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

function function_crip_alta($i,$nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip){
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



function function_validamail($pMail) {//copiado de http://www.desarrolloweb.com/articulos/990.php
  if (ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$", $pMail ) ) {
    return true;
  }else{
    return false;
  }
} 


?>
