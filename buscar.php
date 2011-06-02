<?
/*
arreglar nombre y correo anti sql injection




*/
  conectar();
  cabecera();
  principal($_POST);
  cabecera_fin();

function principal($_POST){
  $codigo=htmlentities($_POST["codigo"]);
  $esp=$_POST["lista_especialidad"];
  $dis=$_POST["lista_disponibilidad"];
  $buscar=$_POST["buscar"];
  $r_espe=$_POST["r_espe"];
  $r_dis =$_POST["r_dis"];
  $i=0;
  $especialidad="";
  $disponibilidad="";
  $lista_esp="";
  $lista_dis="";
  
//creacion de Selectores y preparacion de sql
  $sql="select id,nombre from lista_especialidades;";
  $result= mysql_query($sql) or die(mysql_error());
  while ($row = mysql_fetch_array($result)) {
    if($row[0]==$esp[$i]){$i+=1; $select="selected";}else{$select="";}
    $especialidad.="             <OPTION value=$row[0] $select>$row[1]</OPTION>\n";
  }
  $i=0;
  $sql="select id,nombre from lista_disponibilidad;";
  $result= mysql_query($sql) or die(mysql_error());
  while ($row = mysql_fetch_array($result)) {
    if($row[0]==$dis[$i]){$i+=1; $select="selected";}else{$select="";}
    $disponibilidad.="              <OPTION value=$row[0] $select>$row[1]</OPTION>\n";
  }


  menu_busqueda($especialidad,$disponibilidad,$codigo);

  $tabla="";
  if($buscar!=""){
    //validacion de codigo
    if($codigo!="" and is_numeric($codigo)){$codigo=" codigo like '$codigo%' and ";}else{$codigo="";}
    //validacion de especialidades
    foreach ($esp as $esp2){
      if(is_numeric($esp2)){
        if($lista_esp==""){$lista_esp="and (b.lista=$esp2";}else{$lista_esp.= " or b.lista=$esp2 ";}
      }
    }
    if($lista_esp!=""){$lista_esp.=") ";}
    //validacion de disponibilidad
    foreach ($dis as $dis2){
      if(is_numeric($dis2)){
        if($lista_dis==""){$lista_dis =" and (b.lista=$dis2";}else{$lista_dis.=" or b.lista=$dis2 ";}
      }
    }
    if($lista_dis!=""){$lista_dis.=" ) ";}
    //generacion del sql
    $sql ="select * from volunteers where $codigo id in ";
    $sql.="(select a.id_user from disponibilidad a, especialidades b where a.id_user=b.id_user $lista_esp $lista_dis);";
    $result= mysql_query($sql);
    echo "$sql<br>\n";
    while ($row = mysql_fetch_array($result)) {
      $tabla.="\n<tr><td rowspan=2>".$row['id']."<td>".$row['nombre']."<td>".$row['apellidos']."<td>".$row['correo']."<td>";
      $tabla.=$row['codigo']."<td>".$row['telefono']."<td>";
//select * from volunteers v where v.id in (select t1.id_user from (select t.id_user, count(*) from (select d.id_user from disponibilidad d where d.lista=1 or d.lista=2)) t) group by t1.id_user having count(*)=2) t1, (select t2.id_user from (select t3.id_user, count(*) from (select e.id_user from especialidades e where (e.lista=1 or e.lista=2 or b.lista=3)) t3) group by t2.id_user having count(*)=3) t2 where t1.id_user=t2.id_user);

      $sql2="select nombre from lista_especialidades where id in (select lista from especialidades where id_user=$row[0]);";
      $sql3="select nombre from lista_disponibilidad where id in (select lista from disponibilidad where id_user=$row[0]);";
      $result2=mysql_query($sql2);
      $result3=mysql_query($sql3);
      while ($row3 = mysql_fetch_array($result3)) {
        $tabla.="$row3[0]<br>\n";
      }
      $tabla.="<td>";
      while ($row2 = mysql_fetch_array($result2)) {
        $tabla.="$row2[0]<br>\n";
      }
      $tabla.="<tr><td colspan=6>Comentario:".$row['comentario'];
    }
  }
?>
<div name=resultados>
  <table><?=$tabla?>
  </table>
</div>

<?  
}
function menu_busqueda($especialidad,$disponibilidad,$codigo){
//echo "\n<br>$disponibilidad-$especialidad<br>\n";
?>
<form action="" method="POST">
  <table><tr><td colspan=3>Menu de busqueda de voluntarios
    <tr><td>Codigo postal<td colspan=2><input type=text size="5" maxlength="5" name=codigo value=<?=$codigo?>>
    <tr><td>Especialidad<td> <SELECT multiple size="4" name="lista_especialidad[]" ><?=$especialidad?> </SELECT>
      <td><input checked type=radio name=r_espe value=OR>Or<br><input type=radio name=r_espe value=AND>And
    <tr><td>Disponibilidad<td><SELECT multiple size="3" name="lista_disponibilidad[]"><?=$disponibilidad?></SELECT>
      <td><input checked type=radio name=r_dis  value=OR>Or<br><input type=radio name=r_dis  value=AND>And
    <tr><td><td colspan=2><input name=buscar value=buscar type=submit>
  </table>
</form>
<?
}
function conectar(){
  $link = mysql_connect('localhost', 'root', 'sol') or die('No se pudo conectar: ' . mysql_error());
  mysql_select_db('volunteers') or die('No se pudo seleccionar la base de datos');
}
function cabecera() {
  echo "<html>\n<head>\n  <style>\n    table,tr,td{\n      border: 1px solid black;\n    }\n  </style>\n</head>\n<body>\n<div name=cuerpo>\n";
}

function cabecera_fin() {
  echo "\n</div>\n<body>\n</html>\n";
}

?>
