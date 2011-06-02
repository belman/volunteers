<?php
//mysql -h localhost -u root -p
//contraseña mysql="sol"

$link = mysql_connect('localhost', 'root', 'sol') or die('No se pudo conectar: ' . mysql_error());
//----------------------------------------------------------------------------------------------------
$sql="create database volunteers;";
mysql_query($sql) or die ('Fallo al crear base de datos: '.mysql_error()."\n");
mysql_select_db('volunteers') or die('No se pudo seleccionar la base de datos'."\n");

//Crear tabla
//tipo=
//-----------------------------------------------------------------------------------------------------
$crtable="create table ";
$basico=" (id int NOT NULL AUTO_INCREMENT PRIMARY KEY,fecha datetime,fedit datetime,nombre varchar(32), ";

$sql = $crtable." volunteers ".$basico."apellidos varchar(64),correo varchar(64), codigo int,telefono varchar(16),comentario varchar(1024),correo_ok int);";
mysql_query($sql) or die ('Creacion fallida1: '.$sql." ".mysql_error()."\n");

$sql = $crtable." lista_especialidades (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, nombre varchar(32));"; 
mysql_query($sql) or die ('Creacion fallida2: '.mysql_error());

$sql = $crtable." lista_disponibilidad (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, nombre varchar(32));"; 
mysql_query($sql) or die ('Creacion fallida3: '.mysql_error());

$sql = $crtable." disponibilidad (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, fecha datetime,lista int, id_user int);"; 
mysql_query($sql) or die ('Creacion fallida4: '.mysql_error());

$sql = $crtable." especialidades (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, fecha datetime,lista int, id_user int);"; 
mysql_query($sql) or die ('Creacion fallida5: '.mysql_error());

$sql = $crtable." bajas (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, fecha datetime, correo varchar(64), comentario varchar(1024));";
mysql_query($sql) or die ('Creacion fallida6: '.mysql_error());

//-------------------------------------------------------------------------------------------------------
$sql = "insert into lista_especialidades (nombre) values ('Administracion');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Logistica');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Telecomunicaciones');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Legal');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Marketing');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Comunicacion');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Recursos humanos');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Sanidad');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Traduccion');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Diseño grafico y web');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Programacion');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Artes plasticas');"; mysql_query($sql);
$sql = "insert into lista_especialidades (nombre) values ('Audiovisuales');"; mysql_query($sql);
//-------------------------------------------------------------------------------------------------------
$sql = "insert into lista_disponibilidad (nombre) values ('Dia');"; mysql_query($sql);
$sql = "insert into lista_disponibilidad (nombre) values ('Tarde');"; mysql_query($sql);
$sql = "insert into lista_disponibilidad (nombre) values ('Noche');"; mysql_query($sql);
//$sql = "insert into user (fecha,nombre) values (current_timestamp,'Invitado');"; mysql_query($sql);
//$sql = "insert into user (fecha,nombre) values (current_timestamp,'Alex');"; mysql_query($sql);


//propietario VARCHAR(20),
//    -> especie VARCHAR(20), sexo CHAR(1), nacimiento DATE,
//    -> fallecimento DATE);
//Query OK, 0 rows affected (0.02 sec)

?>
