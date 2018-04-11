<?php
session_start();

$eliminarRegistroBD = $_POST['eliminarRegistroBD'];
$rutaArchivo = $_POST['rutaArchivo'];


unlink($rutaArchivo);

if($eliminarRegistroBD == 1){
	// Conectando, seleccionando la base de datos
	$conn = mysqli_connect("127.0.0.1", "root", "", "erteisei_devcap_DES") or die('<tr><td>No se pudo conectar: ' . mysql_error()."</td></tr>");

	//Se hace consulta para saber el ID del documento recien insertado
    $query = "SELECT PK_DOCUMENTO, FK_EMPLEADO
        FROM tbl_documentos_empleados
        WHERE RUTA_DOCUMENTO = '".substr($rutaArchivo, 5)."'
        LIMIT 1";
    $result = $conn->query($query) or die('Consulta fallida: ' . mysql_error());
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
            $PK_DOCUMENTO = $row['PK_DOCUMENTO'];
            $FK_EMPLEADO = $row['FK_EMPLEADO'];
        }
    }
    
    //Inserta la bitacora
    $PK_USUARO = $_SESSION['usuario']['PK_USUARIO'];
    $descOperacion="PK_EMPLEADO=".$FK_EMPLEADO.",PK_DOCUMENTO=".$PK_DOCUMENTO;
    $query = "insert into tbl_bitacora(DESC_OPERACION, TIPO_OPERACION, FK_USUARIO, REGISTRO_AFECTADO, FECHA_OPERACION, MODULO) values ('$descOperacion','Baja de Documento',$PK_USUARO,$PK_DOCUMENTO,now(),'empleados/cambiar_documentos')"; 
    $result = $conn->query($query) or die('Consulta fallida: ' . mysql_error());

    //Elimina Documento
	$query = "delete from tbl_documentos_empleados where RUTA_DOCUMENTO = '".substr($rutaArchivo, 5)."'";  
	$result = $conn->query($query) or die('Consulta fallida: ' . mysql_error());
	mysqli_close($conn);
}	

echo $rutaArchivo;
?>