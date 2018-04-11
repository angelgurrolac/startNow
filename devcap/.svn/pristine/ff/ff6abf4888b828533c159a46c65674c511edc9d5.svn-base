<?php
require_once dirname(__FILE__) . '\..\..\vendor\PHPWord\PHPWord-master\src\PhpWord\Autoloader.php';
\PhpOffice\PhpWord\Autoloader::register();

use PhpOffice\PhpWord\TemplateProcessor;

$path = 'C:/wamp64/www/erteisei_devcap_DES/web/plantillas/plantilla.docx';
$templateWord = new TemplateProcessor($path);
$var= $_GET["id"];
$link = mysqli_connect('localhost', 'root', '', 'erteisei_devcap_DES');
$query = "select TBL_EMPLEADOS.APELLIDO_PAT_EMP,
TBL_EMPLEADOS.APELLIDO_MAT_EMP,
TBL_EMPLEADOS.NOMBRE_EMP,
TBL_EMPLEADOS.NSS_EMP,
TBL_EMPLEADOS.RFC_EMP,
TBL_EMPLEADOS.CURP_EMP,
TBL_EMPLEADOS.LUGAR_NAC_EMP,
TBL_EMPLEADOS.FECHA_NAC_EMP,
YEAR(CURDATE())-YEAR(TBL_EMPLEADOS.FECHA_NAC_EMP)  AS EDAD,
TBL_DOMICILIOS.CALLE,
TBL_DOMICILIOS.COLONIA,
TBL_DOMICILIOS.CP,
TBL_CAT_MUNICIPIOS.DESC_MUNICIPIO,
TBL_CAT_ESTADOS.DESC_ESTADO,
TBL_DOMICILIOS.TELEFONO,
TBL_CONTACTOS.NOMBRE_PADRE,
TBL_CONTACTOS.TEL_PADRE,
TBL_CONTACTOS.NOMBRE_MADRE,
TBL_CONTACTOS.TEL_MADRE,
TBL_CONTACTOS.TIPO_SANGRE,
TBL_CONTACTOS.CASO_ACCIDENTE,
TBL_CONTACTOS.TEL_ACCIDENTE,
TBL_BENEFICIARIO.NOMBRE_BEN,
TBL_BENEFICIARIO.RFC_BEN,
TBL_BENEFICIARIO.PARENTESCO_BEN,
TBL_BENEFICIARIO.PORCENTAJE,
TBL_BENEFICIARIO.DOMICILIO,
TBL_CAT_PUESTOS.DESC_PUESTO AS PUESTO,
TBL_PERFIL_EMPLEADOS.SUELDO_NETO AS SUELDO,
DATE_FORMAT(TBL_PERFIL_EMPLEADOS.FECHA_REGISTRO,'%d/%m/%Y') AS FECHA_ANTIGUEDAD,
DATE_FORMAT(DATE_ADD(TBL_PERFIL_EMPLEADOS.FECHA_REGISTRO,INTERVAL 3 DAY),'%d/%m/%Y') AS FECHA_INGRESO
from TBL_EMPLEADOS
LEFT JOIN TBL_PERFIL_EMPLEADOS ON TBL_EMPLEADOS.PK_EMPLEADO = TBL_PERFIL_EMPLEADOS.FK_EMPLEADO
LEFT JOIN TBL_CAT_PUESTOS ON TBL_CAT_PUESTOS.PK_PUESTO = TBL_PERFIL_EMPLEADOS.FK_PUESTO
LEFT JOIN TBL_DOMICILIOS ON TBL_EMPLEADOS.FK_DOMICILIO = TBL_DOMICILIOS.PK_DOMICILIO
LEFT JOIN TBL_CAT_ESTADOS ON TBL_DOMICILIOS.FK_ESTADO = TBL_CAT_ESTADOS.PK_ESTADO
LEFT JOIN TBL_CAT_MUNICIPIOS ON TBL_DOMICILIOS.FK_MUNICIPIO = TBL_CAT_MUNICIPIOS.PK_MUNICIPIO AND TBL_DOMICILIOS.FK_ESTADO = TBL_CAT_MUNICIPIOS.FK_ESTADO
LEFT JOIN TBL_BENEFICIARIO ON TBL_EMPLEADOS.PK_EMPLEADO = TBL_BENEFICIARIO.FK_EMPLEADO
LEFT JOIN TBL_CONTACTOS ON TBL_BENEFICIARIO.FK_EMPLEADO = TBL_CONTACTOS.FK_EMPLEADO
where TBL_EMPLEADOS.PK_EMPLEADO = '+$var+' ";
$query = $link->query($query);
$resultado=$query->fetch_object(); 
$query->close();

$Apellido_paterno = 	$resultado->APELLIDO_PAT_EMP;
$Apellido_materno = 	$resultado->APELLIDO_MAT_EMP;
$Nombre = 				$resultado->NOMBRE_EMP;
$NSS = 					$resultado->NSS_EMP;
$RFC = 					$resultado->RFC_EMP;
$CURP = 				$resultado->CURP_EMP;
$lugar_nac = 			$resultado->LUGAR_NAC_EMP;
$fecha_nacimiento = 	$resultado->FECHA_NAC_EMP;
$edad = 				$resultado->EDAD;
$calle = 				$resultado->CALLE;
$colonia = 				$resultado->COLONIA;
$CP = 					$resultado->CP;
$municipio = 			$resultado->DESC_MUNICIPIO;
$estado = 				$resultado->DESC_ESTADO;
$telefono = 			$resultado->TELEFONO;
$nombre_padre = 		$resultado->NOMBRE_PADRE;
$tel_padre  = 			$resultado->TEL_PADRE;
$nombre_madre = 		$resultado->NOMBRE_MADRE;
$tel_madre = 			$resultado->TEL_MADRE;
$S = 					$resultado->TIPO_SANGRE;
$emergencia = 			$resultado->CASO_ACCIDENTE;
$tel_emergencia = 		$resultado->TEL_ACCIDENTE;
$nombre_beneficiario = 	$resultado->NOMBRE_BEN;
$RFC_BENEF = 			$resultado->RFC_BEN;
$parentesco = 			$resultado->PARENTESCO_BEN;
$porcentaje = 			$resultado->PORCENTAJE;
$domicilio_benef = 		$resultado->DOMICILIO;
$puesto = 				$resultado->PUESTO;
$sueldo = 				$resultado->SUELDO;
$fecha_ant = 			$resultado->FECHA_ANTIGUEDAD;
$fecha_ing = 			$resultado->FECHA_INGRESO;

// --- Asignamos valores a la plantilla
$templateWord->setValue('Apellido_paterno',$Apellido_paterno);
$templateWord->setValue('Apellido_materno',$Apellido_materno);
$templateWord->setValue('Nombre',$Nombre);
$templateWord->setValue('NSS',$NSS);
$templateWord->setValue('RFC',$RFC);
$templateWord->setValue('CURP',$CURP);
$templateWord->setValue('lugar_nac',$lugar_nac);
$templateWord->setValue('fecha_nacimiento',$fecha_nacimiento);
$templateWord->setValue('edad',$edad);
$templateWord->setValue('calle',$calle);
$templateWord->setValue('colonia',$colonia);
$templateWord->setValue('CP',$CP);
$templateWord->setValue('municipio',$municipio);
$templateWord->setValue('estado',$estado);
$templateWord->setValue('telefono',$telefono);
$templateWord->setValue('nombre_padre',$nombre_padre);
$templateWord->setValue('tel_padre',$tel_padre);
$templateWord->setValue('nombre_madre',$nombre_madre);
$templateWord->setValue('tel_madre',$tel_madre);
$templateWord->setValue('S',$S);
$templateWord->setValue('emergencia',$emergencia);
$templateWord->setValue('tel_emergencia',$tel_emergencia);
$templateWord->setValue('nombre_beneficiario',$nombre_beneficiario);
$templateWord->setValue('RFC_BENEF',$RFC_BENEF);
$templateWord->setValue('parentesco',$parentesco);
$templateWord->setValue('porcentaje',$porcentaje);
$templateWord->setValue('domicilio_benef',$domicilio_benef);
$templateWord->setValue('puesto',$puesto);
$templateWord->setValue('sueldo',$sueldo);
$templateWord->setValue('fecha_ant',$fecha_ant);
$templateWord->setValue('fecha_ing',$fecha_ing);

// --- Guardamos el documento
$filename = "REGISTRO"."_".$Nombre."_".$Apellido_paterno."_".$Apellido_materno;
$templateWord->saveAs('$filename.docx');

header("Content-Disposition: attachment; filename=$filename.docx; charset=iso-8859-1");
echo file_get_contents('$filename.docx');
        
?>