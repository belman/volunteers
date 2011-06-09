function validarEmail(valor) {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3,4})+$/.test(valor)){
  //alert("La dirección de email " + valor + " es correcta.");
  } else {
    alert("La dirección de email es incorrecta.");
  }
}

function actualizar(menu,extra) {
  var xmlhttp = new Array();
  delete xmlhttp[menu];
  if (window.XMLHttpRequest) {
    xmlhttp[menu] = new XMLHttpRequest();
  }else{
    xmlhttp[menu] = new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp[menu].onreadystatechange = function() {
    if (xmlhttp[menu].readyState == 4) {
      document.getElementById(menu).innerHTML = xmlhttp[menu].responseText;
    }
  }
  xmlhttp[menu].open("GET", extra, true);
  xmlhttp[menu].send(null);
}

function inscribir(form){
  var nombre     = "&nombre="     + escape(form.nombre.value); 
  var apellidos  = "&apellidos="  + escape(form.apellidos.value);
  var correo     = "&correo="     + escape(form.correo.value);
  var telefono   = "&telefono="   + escape(form.telefono.value);
  var codigo     = "&codigo="     + escape(form.codigo.value);
  var comentario = "&comentario=" + escape(form.comentario.value);
  var espe       = "&esp=";
  var disp       = "&dis=";
  for(i=0; i<form.lista_especialidades.options.length; i++){
    if(      form.lista_especialidades.options[i].selected == true){
      if(espe == "&esp="){ espe = espe + (i+1);}
      else{                espe = espe + "," + (i+1);}
    }
  }
  for(i=0; i<form.lista_disponibilidad.options.length; i++){
    if(      form.lista_disponibilidad.options[i].selected == true){
      if( disp == "&dis="){ disp = disp + (i+1);}
      else{                 disp = disp + "," + (i+1);}
    }
  }
  extra="inscripcion.php?registrar=enviar" + nombre + apellidos + correo + telefono + codigo + comentario + espe + disp;
/*  alert(extra);*/
  actualizar("tabla_inscripcion_validar", extra);
}


function baja(form){
  var correo     = "&correo="     + escape(form.correo.value);
  var comentario = "&comentario=" + escape(form.comentario.value);
  var extra = "baja.php?registrar=enviar%20baja" + correo + comentario;
/*  alert(extra);*/
  actualizar("tabla_baja_validar", extra);
}

function busca2(form){
  var codigo     = "&codigo="     + escape(form.codigo.value);
  var espe       = "&esp=";
  var disp       = "&dis=";
  for(i=0; i<form.lista_especialidades.options.length; i++){
    if(      form.lista_especialidades.options[i].selected == true){
      if(espe == "&esp="){ espe = espe + (i+1);}
      else{                espe = espe + "," + (i+1);}
    }
  }
  for(i=0; i<form.lista_disponibilidad.options.length; i++){
    if(      form.lista_disponibilidad.options[i].selected == true){
      if( disp == "&dis="){ disp = disp + (i+1);}
      else{                 disp = disp + "," + (i+1);}
    }
  }
  extra="busqueda.php?buscar=buscar" + codigo + espe + disp;
//  alert(extra);
  actualizar("tabla_buscar_validar", extra);
}

function busqueda(form){
alert("hola");
/*  var codigo     = "&codigo="     + escape(form.codigo.value);
  var espe       = "&esp=";
  var disp       = "&dis=";
  for(i=0; i<form.lista_especialidades.options.length; i++){
    if(      form.lista_especialidades.options[i].selected == true){
      if(espe == "&esp="){ espe = espe + (i+1);}
      else{                espe = espe + "," + (i+1);}
    }
  }
  for(i=0; i<form.lista_disponibilidad.options.length; i++){
    if(      form.lista_disponibilidad.options[i].selected == true){
      if( disp == "&dis="){ disp = disp + (i+1);}
      else{                 disp = disp + "," + (i+1);}
    }
  }
  extra="index.php?registrar=enviar" + codigo + espe + disp;
  alert(extra);*/
/*  actualizar("tabla_buscar_validar", extra);*/
}

function hola(){
  alert("hola mundo");
}

function current_form(current_form) {
  var error_message =""
  // añadimos propiedades a select multiples
  current_form["lista_especialidades"].field_name = "ANIMALES";
  // maximo de elementos seleccionados permitidos
  current_form["lista_especialidades"].max_selected = 3;
  // minimo  permitidos
  current_form["lista_especialidades"].min_selected = 2;
  // si por ejemplo queremos que el usario seleccion una opcion lo hariamos así
/*  current_form["multiple_select1"].field_name = "COCHES";
  current_form["multiple_select1"].max_selected = 1;
  current_form["multiple_select1"].min_selected = 1;*/
  // recorremos todo los campos

  for(var ctr1 = 0; field_m_select = current_form[ctr1];ctr1++){
    // si es un select multiple y hemos añadido las propiedades el campo es obligatorio
    if(field_m_select.type == "select-multiple" && field_m_select.max_selected){
      (function(){
        var cuantos = 0;
        for (var ctr = 0; opt = field_m_select.options[ctr]; ctr++) {
          if (opt.selected) cuantos ++
        }
        if (cuantos > field_m_select.max_selected || cuantos < field_m_select.min_selected ){
          if(field_m_select.max_selected == field_m_select.min_selected){
            error_message += "En el campo " + field_m_select.field_name + " debe seleccionar " + field_m_select.min_selected + 
			 (field_m_select.min_selected > 1 ?" opciones ":" opción")+".";					
          }else{
            error_message += "En el campo " + field_m_select.field_name + " debe seleccionar un minimo de " + field_m_select.min_selected + 
			" y un máximo de " + field_m_select.max_selected + (field_m_select.max_selected > 1 ? " opciones":" opción")+ ".\n";
          }
        }
      })(field_m_select);
    }
  }
  // Si el mensaje no está vacío mostramos el error
  if(error_message != "") alert("ERROR\n\n" + error_message)
  else alert("Enviamos el formulario.")  
}



