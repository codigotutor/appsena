

    var idUsuario = 0;
    var eliminarId = 0;


    var app = {
        /**
        * Se crea el objeto global de la aplicación.
        * @name initialize
        * @event 
        */
        initialize: function() {
            this.bindEvents();
        },
        /**
         * Función encargada de registrar los eventos que utilizara la aplicación.
         * @function
         */
        bindEvents: function() {
            document.addEventListener('deviceready', this.onDeviceReady, false);
        },
        /**
         * Evento ejecutado por el ambiente phonegap indicando que todo esta cargado y listo para usarse.
         * @name onDeviceReady
         * @event
         */
        onDeviceReady: function() {
            app.receivedEvent('deviceready');
        },
        /**
         * Función que propaga y uestra en consola cuando todo la inicialización esta lista..
         * @function
         */
        receivedEvent: function(id) {
            console.log('Evento recibido: ' + id);
        }
    };






        function createDb(tx) {
            tx.executeSql("DROP TABLE IF EXISTS usuarios");
            tx.executeSql("CREATE TABLE IF NOT EXISTS usuarios (id INTEGER PRIMARY KEY, nombre VARCHAR( 80 )  NOT NULL, apellidos VARCHAR( 80 )  NOT NULL);");
            //tx.executeSql("INSERT INTO [perfil] ([experiencia], [ubicacion], [claves], [nombre], [correo], [total]) VALUES ('70', 'bogota', 'sistemas', 'sistemas andres', 'andresgarcia@misena.edu.co','0');");
            //tx.executeSql("INSERT INTO [perfil] ([experiencia], [ubicacion], [claves], [nombre], [total]) VALUES ('30', 'meta', 'ingeniero', 'ingeniero monica', '0');");
        }




    $('#indice').bind('pageinit', function(event) {
            

        var db = window.openDatabase("empleate", "1.0", "DB Empleate", 1000000);
        db.transaction(createDb, mensajeError);
            
    });









$('#nuevo').bind('pagebeforeshow', function(event) {


                $.ajax({
                   type: "GET",
                   async: false,
                   url: "http://www.fiestascuyabras.hol.es/empleate_servidor/index3.php/get/"+idUsuario,
                   success: function(data){

                    $("#detallesUsuario").html('');
                    $("#detallesUsuario").append( 
                                                  "<p>" + data.id + "<br>" 
                                                        + data.nombre + "<br>" 
                                                        + data.apellidos + "<br>" + "</p>");
                    

                   },
                   error: function () {
                        alert("Error");
                   } 

                });

        });







function mensajeError(error) {
    console.log(error);
}



$("#eliminarBtn").click(function() {
   
    $.ajax({
        type: "GET",
        async: false,
        url: "http://www.fiestascuyabras.hol.es/empleate_servidor/index3.php/delete/"+eliminarId,
                   success: function(data){
                   

                    if(data)
                        alert("Con exito!!");
                    else
                        alert("No se pudo");


                   },
                   error: function () {
                        alert("Error");
                   } 

                });

});



 
$("#testLink").click(function(e) {
            e.preventDefault();

            $.ajax({
                   type: "GET",
                   async: false,
                   url: "http://www.fiestascuyabras.hol.es/empleate_servidor/index3.php/getall",
                   success: function(data){

                    var items = new Array();


                    //llenamos la lista de datos remotos
                    for (var i = 0; i < data.length; i++) {

                        //items[i] = new Array(data[i].id, data[i].nombre, data[i].apellidos);
                        $("#listarItemsRemotos").append('<li class="ui-li-has-alt" data-name="'+ parseInt(data[i].id) +'"><a href="#nuevo" data-transition="slide" data-role="button" class="ui-btn">' 
                                                  + "<h4>" + data[i].nombre + "</h4>"
                                                  + "<p>Apellidos: "  + data[i].apellidos + "</p>" 
                                                  + "</a>" 
                                                  + '<a href="#"  data-name="'+  parseInt(data[i].id) +'" class="delete ui-btn ui-btn-icon-notext ui-icon-delete" data-rel="popup" data-icon="delete" data-position-to="window" data-role="button" data-inline="true" title="Eliminar" data-transition="pop"></a>'
                                          +"</li>");
                    }


                    $('#listarItemsRemotos').children('li').bind('click', function(e) {
                        idUsuario = parseInt($(this).attr('data-name'));
                    });



                    $(".delete").on( "click", function() {
                        eliminarId = parseInt($(this).attr('data-name'));       
                        $.mobile.changePage( "#confirmacionEliminar", { role: "dialog" } );
                    });



                    //llenamos la lista de datos remotos
                    for (var i = 0; i < data.length; i++) {
                        llenarBD(data[i].id,data[i].nombre,data[i].apellidos);
                    }


                    mostrarListaLocal();


                   },
                   error: function () {
                        alert("Error");
                   } 

                });

        });
 





 function llenarBD(id, nombre, apellidos){

    var db = window.openDatabase("empleate", "1.0", "DB Empleate", 1000000);

    var sqlTxt = "INSERT INTO usuarios(id,nombre,apellidos) VALUES (?, ?, ?)";
    db.transaction(function(tx) {tx.executeSql(sqlTxt,[id,nombre,apellidos])}, mensajeError);

 }





 function mostrarListaLocal() {

    var db = window.openDatabase("empleate", "1.0", "DB Empleate", 1000000);
    db.transaction(mostrarListaLocalDB, mensajeError);

}




 function mostrarListaLocalDB(tx){


    tx.executeSql('SELECT * FROM usuarios', [], function (tx, results) {
     
   
       var len = results.rows.length, i;
       

       for (i = 0; i < len; i++){
          

         $("#listarItemsLocales").append('<li class="ui-li-has-alt" data-name="'+ parseInt(results.rows.item(i).id) +'"><a href="#nuevo" data-transition="slide" data-role="button" class="ui-btn">' 
                                                  + "<h4>" + results.rows.item(i).nombre + "</h4>"
                                                  + "<p>Apellidos: "  + results.rows.item(i).apellidos + "</p>" 
                                                  + "</a>" 
                                                  + '<a href="#"  data-name="'+  parseInt(results.rows.item(i).id) +'" class="delete ui-btn ui-btn-icon-notext ui-icon-delete" data-rel="popup" data-icon="delete" data-position-to="window" data-role="button" data-inline="true" title="Eliminar" data-transition="pop"></a>'
                                          +"</li>");
       }



       });


}


