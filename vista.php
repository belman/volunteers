<?php
//------------------------------------------------------
function vista_error($error){
  echo "<h3>\n".$error."</h3>\n";
}
function vista_validacion($error){
  echo "<h3>\n".$error."</h3>\n";
}
//-----------------------------------------------------------------------------------------------------------------------
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

//--------------------------------------------------------------------------------------------------------------------
function vista_tabla_inscripcion($lista_esp,$lista_dis){
  $especialidades="";
  $disponibilidad="";

  foreach($lista_esp as $n=> $nombre){
    $especialidades.="          <OPTION value=$n>$nombre</OPTION>\n";
  }
  foreach($lista_dis as $n=> $nombre){
    $disponibilidad.="          <OPTION value=$n>$nombre</OPTION>\n";
  }

?>
  <div id="tabla_inscripcion" name="tabla_inscripcion">
    <FORM name="form_inscripcion"> 
      <table><tr><td colspan=2>Voluntarios
        <tr><td>Nombre<td><input type=text size="32" maxlength="32" name=nombre value=''>
        <tr><td>Apellidos<td><input type=text size="32" maxlength="32" name=apellidos value=''>
        <tr><td>Correo<td><input type=text size="32" maxlength="64" name=correo value=''>
        <tr><td>telefono<td><input type=text size="16" maxlength="16" name=telefono value=''>
        <tr><td>Codigo postal<td><input type=text size="5" maxlength="5" name=codigo value=''>
        <tr><td>Especialidad<td>
          <SELECT multiple size="4" name="lista_especialidades" >
            <?=$especialidades?>
         </SELECT>
        <tr><td>Disponibilidad<td>
          <SELECT multiple size="3" name="lista_disponibilidad">
            <?=$disponibilidad?>
          </SELECT>
        <tr><td>Comentario<td><textarea cols=40 rows=6 name="comentario"></textarea>
        <tr><td><td><input type=button value=enviar name=registrar onclick=' inscribir(document.form_inscripcion);'>
      </table>
    </form>
  </div>
  <div id="tabla_inscripcion_validar" name="tabla_inscripcion_validar">
  </div>

<?
}
//-------------------------------------------------------------------------------------------------------------------------
function vista_tabla_baja(){
?>
  <div id="tabla_baja" name="tabla_baja">
    <FORM name="form_baja">
      <table><tr><td colspan=2>Darse de baja
        <tr><td>Correo<td><input type=text size="32" maxlength="64" name=correo>
        <tr><td>Comentario<td><textarea cols=40 rows=6 name="comentario"></textarea>
        <tr><td><td><input type=button value='enviar baja' name=registrar onclick=' baja(document.form_baja);'>
      </table>
    </form>
  </div>
  <div id="tabla_baja_validar" name="tabla_baja_validar">
  </div>

<?
}

function vista_tabla_buscar($lista_esp,$lista_dis){
  $especialidades="";
  $disponibilidad="";

  foreach($lista_esp as $n=> $nombre){
    $especialidades.="          <OPTION value=$n>$nombre</OPTION>\n";
  }
  foreach($lista_dis as $n=> $nombre){
    $disponibilidad.="          <OPTION value=$n>$nombre</OPTION>\n";
  }
?>
  <div id="tabla_buscar" name="tabla_buscar">
    <form name="form_buscar">
      <table><tr><td colspan=3>Menu de busqueda de voluntarios
        <tr><td>Codigo postal<td colspan=2><input type=text size="5" maxlength="5" name=codigo value=>
        <tr><td>Especialidad<td> 
          <SELECT multiple size="4" name="lista_especialidades" ><?="\n".$especialidades?> 
          </SELECT>
          <td><input checked type=radio name=r_espe value=OR>Or<br><input type=radio name=r_espe value=AND>And
        <tr><td>Disponibilidad<td>
          <SELECT multiple size="3" name="lista_disponibilidad"><?="\n".$disponibilidad?>
          </SELECT>
          <td><input checked type=radio name=r_dis  value=OR>Or<br><input type=radio name=r_dis  value=AND>And
        <tr><td><td colspan=2><input name=buscar value=buscar type=button onclick='busca2(document.form_buscar);'>
      </table>
    </form>
  </div>
  <div id="tabla_buscar_validar" name="tabla_buscar_validar">
  </div>

<?
}
//--------------------------------------------------------------------------------------------------------------------------
function vista_cabecera() {
?>
<html>
<head>
  <style>
    table,tr,td{
      border: 1px solid black;
    }
  </style>
  <script language='javascript' type='text/javascript' src='java.js'></script>
</head>
<body>
  <div name=cuerpo>
<?
}

function vista_cabecera_fin() {
  echo "\n</div>\n<body>\n</html>\n";
}

?>
