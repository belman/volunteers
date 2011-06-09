<?
  include "modelo.volunteers.php";
  include "function.php";
  include "vista.php";

  mysql_conectar();

  if(!empty($_POST)){     $_in = $_POST; }
  elseif(!empty($_GET)){  $_in = $_GET; }
  else{                   $_in = "";    }
  list($resultado,$buscar,$codigo,$esp,$dis,$r_esp,$r_dis)=buscar_validar($_in);

//foreach($esp as $esp2){echo $esp2;}
//foreach($dis as $dis2){echo $dis2;}
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

function vista_busqueda($busqueda){
  if(is_array($busqueda)){
    vista_busqueda_thead();
    foreach ($busqueda as $voluntario){
      vista_busqueda_voluntario($voluntario);
      echo "hola";
    }
    vista_busqueda_tfoot();
  }else{
    echo "<h3>Sin resultados</h3>\n";
  }

}
function vista_busqueda_voluntario($voluntario){
  $lista_esp="";
  $lista_dis="";
  list($id,$fecha,$fedit,$nombre,$apellidos,$correo,$codigo,$telefono,$comentario,$esp,$dis,$busqueda_esp,$busqueda_dis)=$voluntario;
  foreach($busqueda_esp as $esp2){
    $lista_esp.=$esp2."<br>\n";
  }
  foreach($busqueda_dis as $dis2){
    $lista_dis.=$dis2."<br>\n";
  }
?>
        <tr><td rowspan=2><?=$id?><td><?=$nombre?><td><?=$apellidos?><td><?=$correo?><td><?=$codigo?><td><?=$telefono?><td><?=$lista_esp?><td><?=$lista_dis?>
        <tr><td colspan=7>Comentario:<?=$comentario?>
<?
}

function vista_busqueda_thead(){
  echo "      <table>\n";
}
function vista_busqueda_tfoot(){
  echo "      </table>\n";
}

function mysql_busqueda($codigo,$esp,$dis,$r_esp,$r_dis){
//select * from volunteers v where v.id in (select t1.id_user from (select t.id_user, count(*) from (select d.id_user from disponibilidad d where d.lista=1 or d.lista=2)) t) group by t1.id_user having count(*)=2) t1, (select t2.id_user from (select t3.id_user, count(*) from (select e.id_user from especialidades e where (e.lista=1 or e.lista=2 or b.lista=3)) t3) group by t2.id_user having count(*)=3) t2 where t1.id_user=t2.id_user);

  //inicializacion-------------------------------------------
  $busqueda="";
  $busqueda_esp="";
  $busqueda_dis="";

  //validacion de codigo
  if($codigo!="" and is_numeric($codigo)){$codigo=" codigo like '$codigo%' and ";}else{$codigo="";}
  //preparacion sql de especialidades-------------------------
//  if($r_esp==0){
    foreach ($esp as $esp2){
      if(is_numeric($esp2)){
        if($lista_esp==""){$lista_esp="and (b.lista=$esp2";}else{$lista_esp.= " or b.lista=$esp2 ";}
      }
    }
    if($lista_esp!=""){$lista_esp.=") ";}
//  }else{}

  //preparacion sql de disponibilidad---------------------------
//  if($r_dis==0){
    foreach ($dis as $dis2){
    if(is_numeric($dis2)){
        if($lista_dis==""){$lista_dis =" and (a.lista=$dis2";}else{$lista_dis.=" or a.lista=$dis2 ";}
      }
    }
    if($lista_dis!=""){$lista_dis.=" ) ";}
//  }else{}

  //generacion del sql----------------------------------------------
  $i=0;
  $sql ="select * from volunteers where $codigo id in ";
  $sql.="(select a.id_user from disponibilidad a, especialidades b where a.id_user=b.id_user $lista_esp $lista_dis);";
  $result= mysql_query($sql);
echo $sql;
  while ($row = mysql_fetch_array($result)) {
    $sql2="select id,nombre from lista_especialidades where id in (select lista from especialidades where id_user=$row[0]);";
    $sql3="select id,nombre from lista_disponibilidad where id in (select lista from disponibilidad where id_user=$row[0]);";
    $result2=mysql_query($sql2);
    $result3=mysql_query($sql3);
    $j=0;
    while ($row2 = mysql_fetch_array($result2)) {
      $esp[$j]=$row2[0];
      $busqueda_esp[$j]=$row2[1]; 
      $j=$j+1;
    }
    $j=0;
    while ($row3 = mysql_fetch_array($result3)) {
      $dis[$j]=$row3[0];
      $busqueda_dis[$j]=$row3[1]; 
      $j=$j+1;
    }
    $busqueda[$i]=array($row['id'],$row['fecha'],$row['fedit'],$row['nombre'],$row['apellidos'],$row['correo'],$row['codigo'],$row['telefono'], 
                        $row['comentario'],$esp,$dis,$busqueda_esp,$busqueda_dis);
    $i=$i+1;
  }
  return $busqueda;
}
?>
