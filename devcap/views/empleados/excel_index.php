<?php
session_start();
extract($_POST);
// Archivo donde se ejecutara query y se descargara excel
$filename = "Empleados ".date('Y-m-d H:i:s').".xls";
$numeroRegistros = "";

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set("America/Mexico_City");

if (PHP_SAPI == 'cli')
  die('This example should only be run from a Web Browser');

/** Include PHPExcel */

require_once dirname(__FILE__) . '\..\..\vendor\PHPExcel\Classes\PHPExcel.php';
if(empty($unidadNegocio) && $_SESSION['usuario']['IS_SUPER_ADMIN']!=1 && !isset($_SESSION['usuario']['IS_ADMIN_UNIDAD_NEGOCIO'])) {
    if(isset($_SESSION['usuario']['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty($_SESSION['usuario']['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
        $unidadesValidas = '';
        foreach ($_SESSION['usuario']['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'] as $key => $value) {
            $unidadesValidas .= $value.',';
        }
        $unidadNegocio = trim($unidadesValidas,',');
    }else{
        $unidadNegocio = $_SESSION['usuario']['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
    }
}


$conn = mysqli_connect("127.0.0.1", "root", "", "erteisei_devcap_DES") or die('<tr><td>No se pudo conectar: ' . mysqli_connect_error()."</td></tr>");

if($_SESSION['usuario']['IS_SUPER_ADMIN']==1 || in_array(5, ($_SESSION['usuario']['ROLES']))){
    $query="
SELECT 
    CONCAT(e.NOMBRE_EMP,' ', e.APELLIDO_PAT_EMP,' ', e.APELLIDO_MAT_EMP) NOMBRE,
    un.DESC_UNIDAD_NEGOCIO,
    er.DESC_ESTATUS_RECURSO,
    p.DESC_PUESTO,
    pe.COSTO_RECURSO,
    pe.TARIFA,
    razon_so.DESC_RAZON_SOCIAL,
    rz.DESC_UBICACION,
    u.DESC_UBICACION,
    ad.NOMBRE_ADMINISTRADORA,
    pe.ID_EMP_ADMINISTRADORA,
    DATE_FORMAT(pe.FECHA_INGRESO, '%d/%m/%Y') FECHA_INGRESO,
    CASE 
        WHEN pe.FK_ESTATUS_RECURSO IN (4,6) THEN (SELECT DATE_FORMAT(MAX(bit2.FECHA_BAJA), '%d/%m/%Y')
                                                                    FROM tbl_bit_comentarios_empleados bit2
                                                                    WHERE bit2.FK_EMPLEADO = e.PK_EMPLEADO)
        ELSE ''
    END FECHA_BAJA,
    cont.DESC_TIPO_CONTRATO,
    dura.DESC_DURACION,
    serv.DESC_TIPO_SERVICIO,
    area.DESC_AREA,
    IFNULL(rank.DESC_RANK_TECNICO,'') DESC_RANK_TECNICO,
    pe.SUELDO_NETO,
    pe.SUELDO_DIARIO,
    pe.APORTACION_IMSS,
    pe.ISR,
    pe.APORTACION_INFONAVIT,
    gen.DESC_GENERO,
    e.LUGAR_NAC_EMP,
    DATE_FORMAT(e.FECHA_NAC_EMP, '%d/%m/%Y') FECHA_NAC_EMP,
    e.RFC_EMP,
    e.NACIONALIDAD_EMP,
    e.CURP_EMP,
    e.EMAIL_EMP,
    e.EMAIL_INTERNO,
    e.EMAIL_ASIGNADO,
    e.NSS_EMP,
    d.CALLE,
    d.COLONIA,
    d.CP,
    estado.DESC_ESTADO,
    muni.DESC_MUNICIPIO,
    d.TELEFONO,
    d.CELULAR,
    d.TEL_EMERGENCIA,
    asig.PK_ASIGNACION,
    cl.NOMBRE_CLIENTE,
    asig.TARIFA TARIFA_ASIGNACION
FROM tbl_empleados e
    LEFT JOIN tbl_perfil_empleados pe
    ON e.PK_EMPLEADO = pe.FK_EMPLEADO
    LEFT JOIN tbl_cat_puestos p
    ON pe.FK_PUESTO = p.PK_PUESTO
    LEFT JOIN tbl_cat_ubicacion_razon_social  rz
    ON pe.FK_UBICACION = rz.PK_UBICACION_RAZON_SOCIAL
    LEFT JOIN tbl_domicilios d
    ON e.FK_DOMICILIO = d.PK_DOMICILIO
    LEFT JOIN tbl_cat_estatus_recursos er
    ON pe.FK_ESTATUS_RECURSO = er.PK_ESTATUS_RECURSO
    LEFT JOIN tbl_cat_ubicaciones u
    ON pe.FK_UBICACION_FISICA = u.PK_UBICACION
    LEFT JOIN tbl_cat_unidades_negocio un
    ON pe.FK_UNIDAD_NEGOCIO = un.PK_UNIDAD_NEGOCIO
    LEFT JOIN tbl_cat_administradoras ad 
    ON pe.FK_ADMINISTRADORA = ad.PK_ADMINISTRADORA
    LEFT JOIN tbl_cat_tipo_contratos cont 
    ON pe.FK_CONTRATO = cont.PK_TIPO_CONTRATO
    LEFT JOIN tbl_cat_duracion_tipo_servicios dura
    ON pe.FK_DURACION_CONTRATO = dura.PK_DURACION
    LEFT JOIN tbl_cat_tipo_servicios serv 
    ON pe.FK_TIPO_SERVICIO = serv.PK_TIPO_SERVICIO
    LEFT JOIN tbl_cat_areas area
    ON pe.FK_AREA = area.PK_AREA
    LEFT JOIN tbl_cat_rank_tecnico rank
    ON pe.FK_RANK_TECNICO = rank.PK_RANK_TECNICO
    LEFT JOIN tbl_cat_genero gen 
    ON e.FK_GENERO_EMP = gen.PK_GENERO
    LEFT JOIN tbl_cat_estados estado
    ON d.FK_ESTADO = estado.PK_ESTADO AND d.FK_PAIS = estado.FK_PAIS
    LEFT JOIN tbl_cat_municipios muni
    ON d.FK_MUNICIPIO = muni.PK_MUNICIPIO AND d.FK_ESTADO = muni.FK_ESTADO AND d.FK_PAIS = muni.FK_PAIS
    LEFT JOIN tbl_cat_razon_social razon_so
    ON pe.FK_RAZON_SOCIAL = razon_so.PK_RAZON_SOCIAL 
    LEFT JOIN tbl_asignaciones asig
    ON e.PK_EMPLEADO= asig.FK_EMPLEADO and asig.FK_ESTATUS_ASIGNACION = 2 
    LEFT JOIN tbl_clientes cl
    ON asig.FK_CLIENTE= cl.PK_CLIENTE   

        ";
        if($_POST){
            $query.="WHERE 1 ";
            if($nombre){
                $query.=" AND e.NOMBRE_EMP LIKE '%$nombre%'";
            }
            if($aPaterno){
                $query.=" AND e.APELLIDO_PAT_EMP LIKE '%$aPaterno%'   ";
            }
            if($idPuesto){
                $query.=" AND pe.FK_PUESTO = $idPuesto ";
            }
            if($unidadNegocio){
                $query.=" AND pe.FK_UNIDAD_NEGOCIO IN ($unidadNegocio)";
            }
            if($idUbicacion){
                $query.=" AND pe.FK_UBICACION = $idUbicacion";
            }
            if($administradora){
                $query.=" AND pe.FK_ADMINISTRADORA = $administradora";
            }
            if($idUbicacionFisica){
                $query.=" AND pe.FK_UBICACION_FISICA = $idUbicacionFisica";
            }
            if($estatusEmpleado){
                if($estatusEmpleado==4 || $estatusEmpleado== 6){
                    $query.=" AND pe.FK_ESTATUS_RECURSO IN (4,6)";
                } else {
                    $query.=" AND pe.FK_ESTATUS_RECURSO IN ($estatusEmpleado)";
                }
            }else{
                $query.=" AND pe.FK_ESTATUS_RECURSO NOT IN (4,6)";
            }
            if($ingresoFechaIni&&$ingresoFechaFin){
                $query.=" AND pe.FECHA_INGRESO between '".transform_date($ingresoFechaIni,'Y-m-d')."' and '".transform_date($ingresoFechaFin,'Y-m-d')."'";
            }
        }
        $query.=" order by e.APELLIDO_PAT_EMP ASC, e.NOMBRE_EMP ASC, e.APELLIDO_MAT_EMP ASC";

    $result = $conn->query($query) or die('Consulta fallida: ' . mysqli_connect_error());
    $numeroRegistros = $result->num_rows;
    $table = '';
        $table.= "<tr>";
        $table.= '<th>DETALLE DE EMPLEADOS</th>';
        $table.= "</tr>";
        $table.= "<tr>";
            $table.= '<td>PERFIL DEL EMPLEADO</td>';
            $table.= '<td>DATOS PERSONALES</td>';
            $table.= '<td>DOMICILIO ACTUAL</td>';
            $table.= '<td>ASIGNACION</td>';
        $table.= "</tr>";
        $table.= "<tr>";
            $table.= '<td>NOMBRE DEL EMPLEADO</td>';
            $table.= '<td>UNIDAD DE NEGOCIO</td>';
            $table.= '<td>ESTATUS</td>';
            $table.= '<td>PUESTO</td>';
            $table.= '<td>COSTO DEL RECURSO</td>';
            $table.= '<td>TARIFA</td>';
            $table.= '<td>RAZON SOCIAL</td>';
            $table.= '<td>UBICACION FISICA</td>';
            $table.= '<td>ADMINISTRADORA</td>';
            $table.= '<td>ID EMPLEADO ADMISTRADORA</td>';
            $table.= '<td>FECHA DE INGRESO</td>';
            $table.= '<td>FECHA DE BAJA</td>';
            $table.= '<td>TIPO DE CONTRATO</td>';
            $table.= '<td>DURACION DEL CONTRATO</td>';
            $table.= '<td>TIPO DE SERVICIO</td>';
            $table.= '<td>AREA</td>';
            $table.= '<td>RANK TECNICO</td>';
            $table.= '<td>SUELDO NETO</td>';
            $table.= '<td>SUELDO DIARIO</td>';
            $table.= '<td>APORTACION IMSS</td>';
            $table.= '<td>ISR</td>';
            $table.= '<td>DESCUENTO INFONAVIT</td>';
            $table.= '<td>GENERO</td>';
            $table.= '<td>LUGAR DE NACIMIENTO</td>';
            $table.= '<td>FECHA DE NACIMIENTO</td>';
            $table.= '<td>RFC</td>';
            $table.= '<td>NACIONALIDAD</td>';
            $table.= '<td>CURP</td>';
            $table.= '<td>CORREO ELECTRONICO</td>';
            $table.= '<td>CORREO INTERNO</td>';
            $table.= '<td>CORREO CLIENTE</td>';
            $table.= '<td>NSS</td>';
            $table.= '<td>CALLE Y NUMERO</td>';
            $table.= '<td>COLONIA</td>';
            $table.= '<td>CODIGO POSTAL</td>';
            $table.= '<td>ESTADO</td>';
            $table.= '<td>MUNICIPIO</td>';
            $table.= '<td>TELEFONO</td>';
            $table.= '<td>CELULAR</td>';
            $table.= '<td>TELEFONO DE EMERGENCIA</td>';
            $table.= '<td>ID ASIGNACION</td>';
            $table.= '<td>CLIENTE</td>';
            $table.= '<td>TARIFA</td>';
        $table.= "</tr>";
    while ($line = $result->fetch_array(MYSQLI_ASSOC)) {
        $table.= "<tr>";
        foreach ($line as $key => $value) {
            $table.= "<td>".$value."</td>";
        }            
        $table.= "</tr>";
    }

    $tmpfile = tempnam(sys_get_temp_dir(), 'html');
    file_put_contents($tmpfile, $table);

    $objPHPExcel     = new PHPExcel();
    $excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
    $excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
    $objPHPExcel->getActiveSheet()->setTitle('Empleados'); // Change sheet's title if you want

    $objPHPExcel->getActiveSheet()
        ->getStyle('A1:AQ3')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('103D66');

    $styleArray = array(
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => 'FFFFFF'),
            'size'  => 11,
            'name'  => 'Calibri'
        ),
        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:AQ1');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:V2');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('W2:AF2');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('AG2:AN2');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('AO2:AQ2');
    $objPHPExcel->getActiveSheet()->getCell('A2')->setValue('PERFIL DEL EMPLEADO');
    $objPHPExcel->getActiveSheet()->getCell('W2')->setValue('DATOS PERSONALES');
    $objPHPExcel->getActiveSheet()->getCell('AG2')->setValue('DOMICILIO ACTUAL');
    $objPHPExcel->getActiveSheet()->getCell('AO2')->setValue('ASIGNACION');
    $objPHPExcel->getActiveSheet()->getStyle('A1:AQ3')->applyFromArray($styleArray);
    for($col = 'A'; $col !== 'AQ'; $col++) {
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension($col)
            ->setAutoSize(true);

        if($col == 'K' || $col == 'L' || $col == 'Y'){
            for($i = 4; $i < $numeroRegistros+4; $i++){
              $date = $objPHPExcel->getActiveSheet()->getCell($col.$i)->getValue();
              $date = str_replace('/', '-', $date);
              $datef = strtotime($date);
              $dateValue = PHPExcel_Shared_Date::PHPToExcel($datef);
                if($dateValue != null){
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue($col.$i, $dateValue);

                    $objPHPExcel->getActiveSheet()
                                ->getStyle($col.$i)
                                ->getNumberFormat()
                                ->setFormatCode('dd/mm/yyyy');
                }
            }
        }
    }
} else {
    $query="
        SELECT 
            e.NOMBRE_EMP,
            e.PK_EMPLEADO,
            e.APELLIDO_PAT_EMP,
            e.APELLIDO_MAT_EMP,
            e.CURP_EMP,
            d.CP,
            p.DESC_PUESTO,
            rz.DESC_UBICACION,
            DATE_FORMAT(pe.FECHA_INGRESO, '%d/%m/%Y') FECHA_INGRESO,
            d.CELULAR,
            er.DESC_ESTATUS_RECURSO,
            pe.FK_ESTATUS_RECURSO,
            e.FECHA_NAC_EMP,
            e.FOTO_EMP,
            u.DESC_UBICACION DESC_UBICACION_FISICA,
            un.DESC_UNIDAD_NEGOCIO DESC_UNIDAD_NEGOCIO,
            CASE 
                WHEN pe.FK_ESTATUS_RECURSO IN (4,6) THEN (SELECT DATE_FORMAT(MAX(bit2.FECHA_BAJA), '%d/%m/%Y')
                                                                            FROM tbl_bit_comentarios_empleados bit2
                                                                            WHERE bit2.FK_EMPLEADO = e.PK_EMPLEADO)
                ELSE ''
            END FECHA_BAJA
        FROM 
            tbl_empleados e
        LEFT JOIN tbl_perfil_empleados pe
            ON e.PK_EMPLEADO = pe.FK_EMPLEADO
        LEFT JOIN tbl_cat_puestos p
            ON pe.FK_PUESTO = p.PK_PUESTO
        LEFT JOIN tbl_cat_ubicacion_razon_social  rz
            ON pe.FK_UBICACION = rz.PK_UBICACION_RAZON_SOCIAL
        LEFT JOIN tbl_domicilios d
            ON e.FK_DOMICILIO = d.PK_DOMICILIO
        LEFT JOIN tbl_cat_estatus_recursos er
            ON pe.FK_ESTATUS_RECURSO = er.PK_ESTATUS_RECURSO
        LEFT JOIN tbl_cat_ubicaciones u
            ON pe.FK_UBICACION_FISICA = u.PK_UBICACION
        LEFT JOIN tbl_cat_unidades_negocio un
            ON pe.FK_UNIDAD_NEGOCIO = un.PK_UNIDAD_NEGOCIO
        ";
        if($_POST){
            $query.="WHERE 1 ";
            if($nombre){
                $query.=" AND e.NOMBRE_EMP LIKE '%$nombre%'";
            }
            if($aPaterno){
                $query.=" AND e.APELLIDO_PAT_EMP LIKE '%$aPaterno%'   ";
            }
            if($idPuesto){
                $query.=" AND p.DESC_PUESTO = $idPuesto";
            }
            if($unidadNegocio){
                $query.=" AND pe.FK_UNIDAD_NEGOCIO IN ($unidadNegocio)";
            }
            if($idUbicacion){
                $query.=" AND pe.FK_UBICACION = $idUbicacion";
            }
            if($administradora){
                $query.=" AND pe.FK_ADMINISTRADORA = $administradora";
            }
            if($idUbicacionFisica){
                $query.=" AND pe.FK_UBICACION_FISICA = $idUbicacionFisica";
            }
            if($estatusEmpleado){
                if($estatusEmpleado==4 || $estatusEmpleado== 6){
                    $query.=" AND pe.FK_ESTATUS_RECURSO IN (4,6)";
                } else {
                    $query.=" AND pe.FK_ESTATUS_RECURSO IN ($estatusEmpleado)";
                }
            }else{
                $query.=" AND pe.FK_ESTATUS_RECURSO NOT IN (4,6)";
            }
            if($ingresoFechaIni&&$ingresoFechaFin){
                $query.=" AND pe.FECHA_INGRESO between ".transform_date($ingresoFechaIni,'Y-m-d')." and ".transform_date($ingresoFechaFin,'Y-m-d')."";
            }
        }
        $query.=" order by e.APELLIDO_PAT_EMP ASC, e.NOMBRE_EMP ASC, e.APELLIDO_MAT_EMP ASC";

    $result = $conn->query($query) or die('Consulta fallida: ' . mysqli_connect_error());
    $numeroRegistros = $result->num_rows;
    $table='';
        $table.= "<tr>";
            $table.= "<th>ID</th>";
            $table.= "<th>NOMBRE</th>";
            $table.= "<th>APELLIDO PATERNO</th>";
            $table.= "<th>APELLIDO MATERNO</th>";
            $table.= "<th>CURP</th>";
            $table.= "<th>CP</th>";
            $table.= "<th>PUESTO</th>";
            $table.= "<th>UBICACION</th>";
            $table.= "<th>FECHA INGRESO</th>";
            $table.= "<th>FECHA BAJA</th>";
            $table.= "<th>CELULAR</th>";
            $table.= "<th>ESTATUS</th>";
            $table.= "<th>FECHA DE NACIMIENTO</th>";
            $table.= "<th>UBICACION FISICA</th>";
            $table.= "<th>UNIDAD DE NEGOCIO</th>";
        $table.= "</tr>";
    while ($line = $result->fetch_array(MYSQLI_ASSOC)) {
        $table.= "<tr>";
            $table.= "<td>".$line['PK_EMPLEADO']."</td>";
            $table.= "<td>".$line['NOMBRE_EMP']."</td>";
            $table.= "<td>".$line['APELLIDO_PAT_EMP']."</td>";
            $table.= "<td>".$line['APELLIDO_MAT_EMP']."</td>";
            $table.= "<td>".$line['CURP_EMP']."</td>";
            $table.= "<td>".$line['CP']."</td>";
            $table.= "<td>".$line['DESC_PUESTO']."</td>";
            $table.= "<td>".$line['DESC_UBICACION']."</td>";
            $table.= "<td>".transform_date($line['FECHA_INGRESO'],'d/m/Y')."</td>";
            $table.= "<td>".$line['FECHA_BAJA']."</td>";
            $table.= "<td>".$line['CELULAR']."</td>";
            $table.= "<td>".$line['DESC_ESTATUS_RECURSO']."</td>";
            $table.= "<td>".transform_date($line['FECHA_NAC_EMP'],'d/m/Y')."</td>";
            $table.= "<td>".$line['DESC_UBICACION_FISICA']."</td>";
            $table.= "<td>".$line['DESC_UNIDAD_NEGOCIO']."</td>";
        $table.= "</tr>";
    }

    $tmpfile = tempnam(sys_get_temp_dir(), 'html');
    file_put_contents($tmpfile, $table);

    $objPHPExcel     = new PHPExcel();
    $excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
    $excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
    $objPHPExcel->getActiveSheet()->setTitle('Empleados'); // Change sheet's title if you want

    $objPHPExcel->getActiveSheet()
        ->getStyle('A1:P1')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('103D66');

    $styleArray = array(
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => 'FFFFFF'),
            'size'  => 11,
            'name'  => 'Calibri'
        ),
        'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

    $objPHPExcel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);

    for($col = 'A'; $col !== 'AO'; $col++) {
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension($col)
            ->setAutoSize(true);

        if($col == 'K' || $col == 'L' || $col == 'Y'){
            for($i = 4; $i < $numeroRegistros+4; $i++){
              $date = $objPHPExcel->getActiveSheet()->getCell($col.$i)->getValue();
              $date = str_replace('/', '-', $date);
              $datef = strtotime($date);
              $dateValue = PHPExcel_Shared_Date::PHPToExcel($datef);
                if($dateValue != null){
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue($col.$i, $dateValue);

                    $objPHPExcel->getActiveSheet()
                                ->getStyle($col.$i)
                                ->getNumberFormat()
                                ->setFormatCode('dd/mm/yyyy');
                }
            }
        }
    }
}
    

mysqli_close($conn);

function transform_date($date,$format='Y-m-d'){
    $date = str_replace('/', '-', $date);
    $newDate = date($format, strtotime($date));
    return $newDate;
}

//cerrar el resulset
$result->close();

unlink($tmpfile); // delete temporary file because it isn't needed anymore
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
header('Cache-Control: max-age=0');

// Creates a writer to output the $objPHPExcel's content
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$writer->save('php://output');
exit;
?>