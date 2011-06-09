<?php
  include "function.php";
  include "vista.php";
  include "modelo.volunteers.php";

  mysql_conectar();
  vista_cabecera();
  menu();
  vista_cabecera_fin();
  die();

function menu(){
  $lista_esp=mysql_lista_esp();
  $lista_dis=mysql_lista_dis();
  vista_tabla_inscripcion($lista_esp,$lista_dis);
  vista_tabla_baja();
  vista_tabla_buscar($lista_esp,$lista_dis);
}
?>


