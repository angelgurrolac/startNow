<?php
    session_start();
    
    //Obtener nombre del archivo quitandole la extension
    $postNombreArchivo = $_POST['nombreArchivo'];
    $nombreArchivo = quitar_acentos(utf8_decode($postNombreArchivo));
    //$nombreArchivo = $_POST['nombreArchivo'];

    $idTempArchivos = $_POST['idTempArchivos'];//Es el id del empleado
    $ruta = $_POST['ruta'];
    $posPunto = strpos($_FILES['file']['name'], '.');
    $extension = substr($_FILES['file']['name'], $posPunto+1);
    $guardarRegistroBD = $_POST['guardarRegistroBD'];


    if ( 0 < $_FILES['file']['error'] ) {
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
        move_uploaded_file($_FILES['file']['tmp_name'], $ruta.$idTempArchivos.'_'.$nombreArchivo.'.'.$extension);
    }

    echo $ruta.$idTempArchivos.'_'.$nombreArchivo.'.'.$extension;

    if($guardarRegistroBD==1){
        $fechaHoy = date('Y-m-d');
        $rutaBD = substr($ruta, 5).$idTempArchivos.'_'.$nombreArchivo.'.'.$extension;
        // Conectando, seleccionando la base de datos
        $conn = mysqli_connect("127.0.0.1", "root", "", "erteisei_devcap_DES") or die('<tr><td>No se pudo conectar: ' . mysql_error()."</td></tr>");
        //Inserta el documento
        $query = "insert into tbl_documentos_empleados(NOMBRE_DOCUMENTO,RUTA_DOCUMENTO,FECHA_CREACION,FK_BITACORA,FK_EMPLEADO) values ('$nombreArchivo.$extension','$rutaBD','$fechaHoy',0,'$idTempArchivos')"; 
        $result = $conn->query($query) or die('Consulta fallida: ' . mysql_error());
        
        //Se hace consulta para saber el ID del documento recien insertado
        $query = "SELECT MAX(PK_DOCUMENTO) PK_DOCUMENTO
            FROM tbl_documentos_empleados
            WHERE FK_EMPLEADO = $idTempArchivos";
        $result = $conn->query($query) or die('Consulta fallida: ' . mysql_error());
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $PK_DOCUMENTO = $row['PK_DOCUMENTO'];
            }
        }
        
        //Inserta la bitacora
        $PK_USUARO = $_SESSION['usuario']['PK_USUARIO'];
        $descOperacion="PK_EMPLEADO=".$idTempArchivos.",PK_DOCUMENTO=".$PK_DOCUMENTO;
        $query = "insert into tbl_bitacora(DESC_OPERACION, TIPO_OPERACION, FK_USUARIO, REGISTRO_AFECTADO, FECHA_OPERACION, MODULO) values ('$descOperacion','Alta de Documento',$PK_USUARO,$PK_DOCUMENTO,now(),'empleados/cambiar_documentos')"; 
        $result = $conn->query($query) or die('Consulta fallida: ' . mysql_error());
        mysqli_close($conn);
    }

    function quitar_acentos($string){
        //$cadena = $string;
        $no_permitidas = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $permitidas =    'AAAAAAAcEEEEIIIIDNOOOOOOUUUUYDBaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $cadena = utf8_decode(strtr($string, utf8_decode($no_permitidas), $permitidas));
        
        return $cadena;
    }
?>
