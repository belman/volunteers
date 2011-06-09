<?php
function mysql_conectar(){
  $link = mysql_connect('localhost', 'root', 'sol') or die('No se pudo conectar: ' . mysql_error());
  mysql_select_db('volunteers') or die('No se pudo seleccionar la base de datos');
  return $link;
}


function mysql_lista_esp(){
  $sql="select id,nombre from lista_especialidades;"; 
  $result= mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    $lista_esp[$row['id']]=$row['nombre'];
  }
  return($lista_esp);
}



function mysql_lista_dis(){
  $sql="select id,nombre from lista_disponibilidad;";
  $result= mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    $lista_dis[$row['id']]=$row['nombre'];
  }
  return($lista_dis);
}




function mysql_listar_esp($esp){
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
  return array($especialidades,$espe);
}
function mysql_listar_dis($dis){
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
  return array($disponibilidad,$disp);
}

function mysql_inscripcion($nombre,$apellidos,$correo,$telefono,$codigo,$comentario,$dis,$esp,$crip){
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
//------------------------------------------------------------------------------------------------------------------------------------
function mysql_baja($correo,$comentario,$crip){
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


//-------------------------------------------------------------------------------------------------------------------------------
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
