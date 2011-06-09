<?
  include "modelo.volunteers.php";
  include "function.php";
  include "vista.php";

  mysql_conectar();

  if(!empty($_POST)){     $_in = $_POST; }
  elseif(!empty($_GET)){  $_in = $_GET; }
  else{                   $_in = "";    }
  list($resultado,$buscar,$codigo,$esp,$dis,$r_esp,$r_dis)=buscar_validar($_in);

  if($resultado==1){
    $busqueda=mysql_busqueda($codigo,$esp,$dis,$r_esp,$r_dis);
    vista_busqueda($busqueda);
  }else{
    vista_error($resultado);
  }
  die("\n");

function buscar_validar($_in){
  //Inicializando valores--------------------------------------------------
  $resultado="";
  $buscar="";
  $codigo="";
  $dis="";
  $esp="";
  $r_espe="";
  $r_dis ="";

  //-------------------------------------------------------------------------
  //validando datos recibidos y enviar correo
  //------------------------------------------------------------------------
  $ok=1;
  $ko="";
  if(!empty($_in)){
    //buscar---------------------------------------------------------------
    if(!isset($_in["buscar"])){                                          $ok=0; $ko.="La llamada de busqueda no es valida(buscar)<br>\n";}
    else{
      if($_in["buscar"]=="buscar"){                                      $buscar = $_in["buscar"];}
      else{                                                              $ok=0; $ko.="La llamada de busqueda no es valida<br>\n";}
      //codigo---------------------------------------------------------------
      if(!isset($_in["codigo"])){                                        $ok=0; $ko.="La llamada de busqueda no es valida(codigo)<br>\n";}
      else{
        if((is_numeric($_in["codigo"]) && $_in["codigo"]<100000)){       $codigo = $_in["codigo"];
        }else{                                                           $codigo = "";} 
      }
      //especialidades-------------------------------------------------------
      if(isset($_in["esp"])){$_in["lista_especialidades"]=explode(",",$_in["esp"]);}
      if(empty($_in["lista_especialidades"])){}
      else{
        $i=0;
        foreach($_in["lista_especialidades"] as $esp2){
          if(is_numeric($esp2)){                                         $esp[$i]=$esp2; $i+=1;
          }elseif($esp2==""){}else{                                       $esp = ""; $ok=0; $ko.="entrada ilegal de datos $esp2<br>\n"; break;}
        }
      }
      //disponibilidad------------------------------------------------------
      if(isset($_in["dis"])){$_in["lista_disponibilidad"]=explode(",",$_in["dis"]);}
      if(empty($_in["lista_disponibilidad"])){}
      else{
        $i=0;
        foreach($_in["lista_disponibilidad"] as $dis2){
          if(is_numeric($dis2)){                                         $dis[$i]=$dis2; $i+=1;
          }elseif($dis2==""){}else{                                      $dis = ""; $ok=0; $ko.="entrada ilegal de datos $dis2<br>\n"; break;}
        }
      }
      //r_esp-----------------------------------------------------------------
      if(isset($in["r_esp"])){
        $r_esp=$_in["r_esp"];
      }
      //r_dis-----------------------------------------------------------------
      if(isset($in["r_dis"])){
        $r_dis=$_in["r_dis"];
      }
      //----------------------------------------------------------------------
      //----------------------------------------------------------------------
    }
  }
  if($ok==1){ $resultado=$ok;}
  else{$resultado=$ko;}
  return array($resultado,$registrar,$codigo,$esp,$dis,$r_esp,$r_dis);

}

?>
