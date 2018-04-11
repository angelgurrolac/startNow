<?php
/*************************************************************************
** Nombre:  HardwareController
** Desc:    Controlador del modulo Hardware.
**          Permite realizar operaciones sobre las siguientes acciones
**          actionIndex, actionAsociarRecurso, actionDelete, actionView
**          actionUpdate, actionCreate
** Autor:
** Version: 1.0
** Fecha:   05/09/2016
**************************
** Historial de cambios
**************************
** #    Fecha       Autor       Motivo de la Revisión
** --   --------    ------- ------------------------------------
** 01   05/10/2016  JAC     Se realizan correcciones por incidencias.
**************************************************************************/

namespace app\controllers;

use Yii;
use app\models\TblHardware;
use app\models\TblEmpleados;
use app\models\TblCatTipoEquipo;
use app\models\HardwareBusqueda;
use app\models\TblIngresoGarantias;
use app\models\TblHardwareEmpleados;

use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use yii\widgets\Pjax;


/**
 * HardwareController implements the CRUD actions for TblHardware model.
 */
class HardwareController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all TblHardware models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tamanio_pagina = 5;

        if (Yii::$app->request->isAjax) {
            // Variable utilizada en la consulta
            $connection = \Yii::$app->db;

            // Se concentra la información obtenida del REQUEST en la variable $data
            $data = Yii::$app->request->post();
            $post = null;

            parse_str($data['data'], $post);
            $filtro = array();

            // Declaracion de variables en base al Request
            $filtro['Equipo']       =        (!empty($post['EQUIPO']))          ?   trim($post['EQUIPO'])           :   '';
            $filtro['Marca']        =        (!empty($post['MARCA']))           ?   trim($post['MARCA'])            :   '';
            $filtro['Modelo']       =        (!empty($post['MODELO']))          ?   trim($post['MODELO'])           :   '';
            $filtro['No_Serie']     =        (!empty($post['NO_SERIE']))        ?   trim($post['NO_SERIE'])         :   '';
            $filtro['Recurso']      =        (!empty($post['RECURSO']))         ?   trim($post['RECURSO'])          :   '';
            $filtro['Codigo_Eisei'] =        (!empty($post['CODIGO_EISEI']))    ?   trim($post['CODIGO_EISEI'])     :   '';
            $filtro['Proveedor']    =        (!empty($post['PROVEEDOR']))       ?   trim($post['PROVEEDOR'])        :   '';
            $filtro['Estatus']      =        (!empty($post['ESTATUS']))         ?   trim($post['ESTATUS'])          :   '';
            $filtro['Tipo_Equipo']  =        (!empty($post['TIPO_EQUIPO']))     ?   trim($post['TIPO_EQUIPO'])      :   '';
            $filtro['pagina']       =        (!empty($post['page']))            ?   trim($post['page'])             :   '';
            $filtro['FechaIni']     =        (!empty($post['COMPRAFECHAINI']))  ?   transform_date(trim($post['COMPRAFECHAINI'])) :   '';
            $filtro['FechaFin']     =        (!empty($post['COMPRAFECHAFIN']))  ?   transform_date(trim($post['COMPRAFECHAFIN'])) :   '';

            // Se obtiene el número de registro en base al número de página del grid
            $pagina = (empty($filtro['pagina'])) ? 0 : $filtro['pagina'];
            $aPartirDe = ($pagina == 1) ? 0 : ($pagina * $tamanio_pagina) - $tamanio_pagina;

            // Consulta general
            $query = strtr("CALL SP_ERTSEL_HWR_ObtenerHardware('Equipo', 'Marca', 'Modelo', 'No_Serie', 'Codigo_Eisei', 'Proveedor', 'Estatus', 'Tipo_Equipo')", $filtro);
            $datos = $connection->createCommand($query)->queryAll();

            // Se filtra la consulta obtenida en base a los campos de búsqueda Recurso y Fecha_Compra
            $datosFiltrados = array();
            foreach ($datos As $llave => $valor) {
                $cumpleFiltrado = true;
                foreach ($valor As $subLlave => $subValor) {
                    switch ($subLlave):
                        case "Recurso":
                            if ($filtro['Recurso'] !== '' && $cumpleFiltrado) {
                                $cumpleFiltrado = (stripos($subValor, $filtro['Recurso']) !== false) ? true : false;
                            }
                            break;
                        case "Fecha_Compra":
                            if ($cumpleFiltrado) {
                                $subValor =  transform_date($subValor);
                                if ($filtro['FechaIni'] !== '') {
                                    if ($filtro['FechaFin'] !== '') {
                                        $cumpleFiltrado = ($subValor >= $filtro['FechaIni'] && $subValor <= $filtro['FechaFin']) ? true : false;
                                    } else {
                                        $cumpleFiltrado = ($subValor >= $filtro['FechaIni'] ) ? true : false;
                                    }
                                } elseif ($filtro['FechaFin'] !== '') {
                                        $cumpleFiltrado = ($subValor <= $filtro['FechaFin']) ? true : false;
                                }
                            }
                            break;
                        default:
                            break;
                    endswitch;

                    if (!$cumpleFiltrado) {
                        break 1;
                    }
                }

                if ($cumpleFiltrado) {
                    array_push($datosFiltrados, $valor);
                }
            }

            $total_registros = count($datosFiltrados);
            $total_paginas = ($total_registros > 0) ? ceil($total_registros/$tamanio_pagina) : 1;

            // Se filtra la consulta obtenida en base al número elementos por página
            $pagina = ($total_registros <= $tamanio_pagina) ? $pagina=0 : $pagina;
            $datosPorPagina = array_slice($datosFiltrados, $aPartirDe, $tamanio_pagina);

            // Se declara variable que almacenara el HTML generado.
            $html = "";

            // Bucle que recorre la información filtrada de la Consulta general
            $contadorHardware = 0;
            $contadorRegistro = $aPartirDe;

            foreach ($datosPorPagina As $d) {
                $contadorHardware += 1;
                $contadorRegistro += 1;

                /*
                *   Se obtiene la información relacionada a la garantía en el grid.
                *
                */

                // Declaración de variables
                $diasGarantia = 0;

                // Definición de variables
                $now = time();
                $fechaInicial = $d['Fecha_Compra'];

                // Se obtienen los días de garantía en base a los meses
                if($d['Garantia']<1){
                    $meses=-1;
                }
                else{
                    $meses=$d['Garantia'];
                }
                $mesesGarantia = "+" .floor($meses). " month";
                $fechaFinGarantia = date('Y/m/d', strtotime($mesesGarantia, strtotime($fechaInicial)));
                $diasGarantia = intval((strtotime($fechaFinGarantia) - strtotime($fechaInicial))/60/60/24);

                // Se obtienen los días transcurridos a partir de una fecha
                $diasTranscurridos = intval(($now - strtotime($fechaInicial))/60/60/24);

                // Se obtienen los días restantes
                $diasRestantes = $diasGarantia - $diasTranscurridos;

                // Se obtiene el porcentage de días restantes
                $diasRestantesPorcent = floor(100 - (($diasTranscurridos / $diasGarantia) * 100));

                // Se establecen valores a 0 en caso de que los días resultantes sean negativos.
                $diasRestantes = ($diasRestantes < 0) ? 0 : $diasRestantes;
                if($meses>0){
                    $diasRestantesPorcent = ($diasRestantesPorcent < 0) ? 0 : $diasRestantesPorcent;
                }else{
                    $diasRestantesPorcent = 0;
                }
                

                /*
                *   Se envia la información de la consulta a la variable $val
                *   se utiliza facilitar la concatenación con strings que
                *   representan el HTML.
                */
                $val['_PK_Hardware_'] = $d['PK_Hardware'];
                $val['_FK_ESTATUS_RECURSO'] = $d['FK_ESTATUS_RECURSO'];
                $val['_Fecha_Compra_'] = $d['Fecha_Compra'];
                $val['_Equipo_'] = $d['Equipo'];
                $val['_DESC_Tipo_Equipo_'] = $d['DESC_Tipo_Equipo'];
                $val['_Marca_'] = $d['DESC_Marca'];
                $val['_Codigo_Eisei_'] = $d['Codigo_Eisei'];
                $val['_NombreLicencia_'] = $d['NombreLicencia'];
                $val['_Recurso_'] = $d['Recurso'];
                $val['_Modelo_'] = $d['Modelo'];
                $val['_No_Serie_'] = $d['No_Serie'];
                $val['_DESC_Estatus_Hardware_'] = $d['DESC_Estatus_Hardware'];
                $val['_DESC_Proveedor_'] = $d['DESC_Proveedor'];
                $val['_Costo_'] = $d['Costo'];
                $val['_Garantia_'] = $d['Garantia'];
                $val['|'] = "<br/>- ";
                $val['_DiasRestantes_'] = $diasRestantes;
                $val['_DiasRestantesPorcent_'] = $diasRestantesPorcent;

                /*
                *   Se define HTML del grid
                *
                */
                //  Variables que representan elementos HTML
                $tr =       strtr("<tr id='_PK_Hardware_'>",$val);
                $tr_ =      "</tr>";
                $td =       "<td style='border: 0px; text-align: center; background-color:";
                $tdScrl =   "<td style='border: 0px; text-align: left; background-color:";
                $tdStat =   "<td name='DESC_Estatus_Hardware' style='border: 0px; text-align: left; background-color:";
                $tdRcrs =   "<td name='Tabla_Recurso' class='estado-".$d['FK_ESTATUS_RECURSO']."' style='border: 0px; text-align: left; background-color:";
                $td_ =      "</td>";
                $bgColor1 = "#FFFFFF"."'>";
                $bgColor2 = "#F5F5F5"."'>";
                $checkBoxHardware = strtr("<input type='checkbox' name='_PK_Hardware_' value='_PK_Hardware_' />",$val);
                $guionMedio = (count($d['NombreLicencia']) > 0 ) ? '- ' : '';

                // Variables que representan el valor obtenido de la tabla + formato HTML
                $equipoHtml = "<a href='".Url::to(['hardware/view'])."/".$d['PK_Hardware']."'>".$d['Equipo']."</a>";
                $fechaCompraHtml = date('d/m/Y', strtotime($d['Fecha_Compra']));
                $nombreLicenciaHtml = "<div class='scrollbar'><div class='force-overflow'>".$guionMedio.strtr($d['NombreLicencia'], $val)."</div></div>";
                $costoEquipoHtml = "<div style='text-align: right'>$".number_format(floatval($d['Costo']), 2)."</div>";
                $labelDiasRestantes = strtr("Días Restantes<br/>_DiasRestantes_<br/>", $val);
                $labelDiasRestantes .= strtr("<div class='progress'>", $val);
                $labelDiasRestantes .= strtr("<div class='progress-bar' role='progressbar' aria-valuenow='_DiasRestantesPorcent_' aria-valuemin='0' aria-valuemax='100' style='width:_DiasRestantesPorcent_%'>", $val);
                $labelDiasRestantes .= strtr("<span class='sr-only'>_DiasRestantesPorcent_% Complete</span></div></div>", $val);

                // Logica de color de celda
                $bgColor = ($contadorHardware % 2 == 0) ? $bgColor2 : $bgColor1;

                /*
                *   Grid
                *   Se concatenan las variables definidas para formar el Grid
                */
                $campoRecursoGrid = '';
                if($d['DESC_Estatus_Hardware'] != 'Asociado'){
                    $campoRecursoGrid = 'SIN ASOCIAR';
                }

                $html .= $tr;

                $html .= $td.$bgColor.      $contadorRegistro.                  $td_;
                $html .= $td.$bgColor.      $checkBoxHardware.                  $td_;
                $html .= $td.$bgColor.      $fechaCompraHtml.                   $td_;
                $html .= $td.$bgColor.      $equipoHtml.                        $td_;
                $html .= $td.$bgColor.      $d['DESC_Tipo_Equipo'].             $td_;
                $html .= $td.$bgColor.      $d['DESC_Marca'].                   $td_;
                $html .= $td.$bgColor.      $d['Codigo_Eisei'].                 $td_;
                $html .= $tdScrl.$bgColor.  $nombreLicenciaHtml.                $td_;
                $html .= $tdRcrs.$bgColor.  ($d['DESC_Estatus_Hardware'] == 'Asociado' ? $d['Recurso'] : $campoRecursoGrid) . $td_;
                $html .= $td.$bgColor.      $d['Modelo'].                       $td_;
                $html .= $td.$bgColor.      $d['No_Serie'].                     $td_;
                $html .= $tdStat.$bgColor.  $d['DESC_Estatus_Hardware'].        $td_;
                //$html .= $td.$bgColor.      $d['DESC_Proveedor'].               $td_;
                $html .= $td.$bgColor.      $costoEquipoHtml.                   $td_;
                $html .= $td.$bgColor.      $labelDiasRestantes.                $td_;

                $html .= $tr_;
                // fin del grid
            }


            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'data' => $html,
                'total_paginas' => $total_paginas,
                'pagina' => $pagina,
                'total_registros' => $total_registros,
            );

            return $res;
        } else {
             return $this->render('index', [
                'total_paginas' => 0,
            ]);
        }
    }

    /**
     * Asocia un recurso a un hardware.
     * @return mixed
     */
    public function actionAsociarecurso()
    {
        $tamanio_pagina = 6;

        if (Yii::$app->request->isAjax) {
            // Variable utilizada en la consulta
            $connection = \Yii::$app->db;

            // Se concentra la información obtenida del REQUEST en la variable $data
            $data = Yii::$app->request->post();
            $post = null;
            parse_str($data['data'], $post);

            if($post['guardar_asociacion']) {

                $now = date_create(strtotime(time()));
                $now = date_format($now, "Y/m/d");

                $modHardware = new TblHardware();
                $resHardware = TblHardware::find()->Where(['PK_Hardware'=>$post['id']])->asArray()->one();
                $modHardware->attributes = $resHardware;

                $modEmpleado = new TblEmpleados();
                $resEmpleado = TblEmpleados::find()->Where(['PK_Empleado'=>$post['id_recurso']])->asArray()->one();
                $modEmpleado->attributes = $resEmpleado;

                // Alta de registro
                $query = "CALL `SP_ERTINS_HWR_RegistrarHardwareEmpleados`('$modHardware->PK_HARDWARE', '$modEmpleado->PK_EMPLEADO', '$now')";
                $datos = $connection->createCommand($query)->execute();

                // Validación de resultado.
                if ($datos > 0) {
                    $descripcionBitacora = "FK_HARDWARE=$modHardware->PK_HARDWARE,FK_EMPLEADO=$modEmpleado->PK_EMPLEADO,FECHA_REGISTRO=$now";
                    user_log_bitacora_soporte($descripcionBitacora,'Asociar Recurso a Hardware', $modHardware->PK_HARDWARE);

                    return true;
                } else {
                    return false;
                }
            }


            // Declaracion de variables para el filtro en base al Request
            $filtro = array();
            $filtro['Recurso']      =   (!empty($post['RECURSO']))         ?   trim($post['RECURSO'])          :   '';
            $filtro['pagina']       =   (!empty($post['page']))            ?   trim($post['page'])             :   '';

            // Se obtiene el número de registro en base al número de página del grid
            $pagina = (empty($filtro['pagina'])) ? 0 : $filtro['pagina'];
            $aPartirDe = ($pagina == 1) ? 0 : ($pagina * $tamanio_pagina) - $tamanio_pagina;

            // Consulta general
            $query = strtr("CALL `SP_ERTSEL_EMP_ObtenerInformacionEmpleado`('Recurso')", $filtro);
            $datos = $connection->createCommand($query)->queryAll();

            $total_registros = count($datos);
            $total_paginas = ($total_registros > 0) ? ceil($total_registros/$tamanio_pagina) : 1;

            // Se filtra la consulta obtenida en base al número elementos por página
            $pagina = ($total_registros <= $tamanio_pagina) ? $pagina=0 : $pagina;
            $datosPorPagina = array_slice($datos, $aPartirDe, $tamanio_pagina);


            // Se declara variable que almacenara el HTML generado.
            $html = "";

            foreach ($datosPorPagina As $d) {
                /*
                *   Se obtiene la información relacionada al [Tiempo en empresa] en el grid.
                *
                */

                // Definición de variables
                $now = date_create(strtotime(time()));
                $fechaInicial = date_create($d['Fecha_Ingreso']);

                // Se obtienen los días transcurridos
                $intervalo = date_diff($fechaInicial, $now);

                // Se obtienen los años
                $tiempoTranscurrido = array();
                $tiempoTranscurrido['_anios'] = ($intervalo->format('%y') > 0) ? $intervalo->format('%y') : '';
                $tiempoTranscurrido['_aniosTexto'] = '';

                // Se obtienen los meses
                $tiempoTranscurrido['_meses'] = ($intervalo->format('%m') > 0) ? $intervalo->format('%m') : '';
                $tiempoTranscurrido['_mesesTexto'] = '';

                // Se obtiene el año en texto
                if ($tiempoTranscurrido['_anios'] !== '') {
                    $tiempoTranscurrido['_aniosTexto'] = ($tiempoTranscurrido['_anios'] == 1) ? 'año' : 'años';
                }

                // Se obtiene el mes en texto
                if ($tiempoTranscurrido['_meses'] !== '') {
                    $tiempoTranscurrido['_mesesTexto'] = ($tiempoTranscurrido['_meses'] == 1) ? 'mes' : 'meses';
                }

                $tiempoTranscurrido['_dias'] = '';
                $tiempoTranscurrido['_diasTexto'] = '';

                // Si el año es igual a 0, se mostrarán los días
                if ( $tiempoTranscurrido['_anios'] == '' ) {
                    $tiempoTranscurrido['_dias'] = ($intervalo->format('%d') > 0) ? $intervalo->format('%d') : '';
                    $tiempoTranscurrido['_diasTexto'] = '';

                    if ($tiempoTranscurrido['_dias'] != '') {
                        $tiempoTranscurrido['_diasTexto'] = ($tiempoTranscurrido['_dias'] == 1) ? 'día' : 'días';
                    }
                }

                // Concatenación de Tiempo en empresa
                $tiempoTrabajando = strtr("_anios _aniosTexto _meses _mesesTexto _dias _diasTexto", $tiempoTranscurrido);

                /*
                *   Se define HTML del grid
                *
                */
                //  Variables que representan elementos HTML
                $div =          "<div>";
                $div_ =         "</div>";
                $label =        "<label class='campos-label'>";
                $label_ =       "</label>";
                $pColor =       "<p class='font-regular' style='color: #0090E8'>";
                $p =            "<p class='font-regular'>";
                $p_ =           "</p>";
                $empleado =     $d['Nombre_Emp']." ".$d['Apellido_Pat_Emp']." ".$d['Apellido_Mat_Emp'];
                $checkbox =     "<input type='checkbox' name='".$d['PK_Empleado']."' id='".$d['PK_Empleado']."' value='".$empleado."' />";
                $filaEmpleado = "<div style='margin: 0px 5px 0px 0px;'>".$checkbox."</td><td>"."<div class='form-group font-bold'>".$empleado.$div_;

                /*
                *   Grid
                *   Se concatenan las variables definidas para formar el Grid
                */
                $html .=    "<div class='contenedor-item col-lg-4 col-md-6 col-sm-12 col-xs-12><div class='row'>";
                $html .=        "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>";
                $html .=            "<div>&nbsp;</div>";
                $html .=            "<table><tr><td>";
                $html .=                $filaEmpleado;
                $html .=                "</td></tr>";
                $html .=                "<tr><td></td><td>";
                $html .=                $p.$d['Desc_Puesto'].$p_;
                $html .=                $label."Tiempo en empresa".$label_;
                $html .=                $p.$tiempoTrabajando.$p_;
                $html .=            "</td></tr></table>";
                $html .=        "<div class='clear'></div>";
                $html .=    "</div></div>";

                // fin del grid
            }


            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'data' => $html,
                'total_paginas' => $total_paginas,
                'pagina' => $pagina,
                'total_registros' => $total_registros,
            );

            return $res;
        }

        else if (Yii::$app->request->isPost) {

             return $this->render('index', [
                'total_paginas' => 0,
            ]);

        } else {

            $get = Yii::$app->request->get();

            $modelo = new TblHardware();
            $resultado = TblHardware::find()->Where(['PK_Hardware'=>$get['id']])->asArray()->one();
            $modelo->attributes = $resultado;

             return $this->render('asociarecurso', [
                'total_paginas' => 0,
                'modelo' => $modelo,
            ]);
        }
    }


/**
     * Displays a single tblvacantes model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model                       = $this->findModel($id);
       // $model->PK_HARDWARE    = tblHardware::find()->where(['PK_Hardware' => $model->PK_HARDWARE])->limit(1)->one();
       $model->EQUIPO               = tblHardware::find()->where(['Equipo' => $model->EQUIPO])->limit(1)->one();
       $model->FK_MARCA             = tblHardware::find()->where(['FK_Marca' => $model->FK_MARCA])->limit(1)->one();
       $model->MODELO               = tblHardware::find()->where(['Modelo' => $model->MODELO])->limit(1)->one();
       $model->NO_SERIE             = tblHardware::find()->where(['No_Serie' => $model->NO_SERIE])->limit(1)->one();
       $model->FECHA_COMPRA         = tblHardware::find()->where(['Fecha_Compra' => $model->FECHA_COMPRA])->limit(1)->one();
       $model->GARANTIA             = tblHardware::find()->where(['Garantia' => $model->GARANTIA])->limit(1)->one();
       $model->CODIGO_EISEI         = tblHardware::find()->where(['Codigo_Eisei' => $model->CODIGO_EISEI])->limit(1)->one();
       $model->NO_TICKET            = tblHardware::find()->where(['No_Ticket' => $model->NO_TICKET])->limit(1)->one();
       $model->NO_FACTURA           = tblHardware::find()->where(['No_Factura' => $model->NO_FACTURA])->limit(1)->one();
       $model->COSTO                = tblHardware::find()->where(['Costo' => $model->COSTO])->limit(1)->one();
       $model->ESPECIFICACIONES     = tblHardware::find()->where(['Especificaciones' => $model->ESPECIFICACIONES])->limit(1)->one();
       
      // $model->DESC_Estatus_Hardware       = TblCatEstatusHardware::find()->where(['DESC_Estatus_Hardware' => $model->DESC_Estatus_Hardware])->limit(1)->one();
      //Models DUMMY
      
      // Desc: Solo se usan para modificar valores de las FK de los models que se mandan a la vista
      //$modelGenero = TblCatGenero::find()->where(['PK_GENERO' => $model->FK_GENERO_EMP])->limit(1)->one();
      //$modelEstatus = TblCatEstatusHardware::find()->where(['PK_Estatus_Hardware' => $model->FK_Estatus_Hardware])->limit(1)->one();
      
      //Sustitucion de los valores de las FK, por sus descripciones y formateo de valores
      //$model->FK_GENERO_EMP = $modelGenero->DESC_GENERO;
      //$model->FK_Estatus_Hardware = $modelEstatus->DESC_Estatus_Hardware;
       
        $modelEstatus= (new \yii\db\Query())
                            ->select(['es.DESC_Estatus_Hardware','es.PK_Estatus_Hardware'])
                            ->from('tbl_cat_estatus_hardware as es')
                            ->join('INNER JOIN','tbl_hardware as hw',
                                'es.PK_Estatus_Hardware = hw.FK_Estatus_Hardware')
                            ->where(['hw.PK_Hardware'=>$id])                          
                            ->all();
        
                            
        $modelTipoEquipo= (new \yii\db\Query())
                            ->select(['cte.DESC_Tipo_Equipo'])
                            ->from('tbl_cat_tipo_equipo as cte')
                            ->join('INNER JOIN','tbl_hardware as hw',
                                'cte.PK_Tipo_Equipo = hw.FK_Tipo_Equipo')
                            ->where(['hw.PK_Hardware'=>$id])
                           // ->orderBy('es.DESC_Tipo_Equipo ASC')                          
                            ->all();                    
                            //dd($modelEstatus);
                            
        $modelProveedor=    (new \yii\db\Query())
                            ->select(['cp.DESC_Proveedor'])
                            ->from('tbl_cat_Proveedor as cp')
                            ->join('INNER JOIN','tbl_hardware as hw',
                                'cp.PK_Proveedor = hw.FK_Proveedor')
                            ->where(['hw.PK_Hardware'=>$id])                                         
                            ->all();      
        $modelMarca=    (new \yii\db\Query())
                            ->select(['cm.DESC_Marca'])
                            ->from('tbl_cat_Marcas as cm')
                            ->join('INNER JOIN','tbl_hardware as hw',
                                'cm.PK_Marca = hw.FK_Marca')
                            ->where(['hw.PK_Hardware'=>$id])                                         
                            ->all();                
       //traer nombre del empleado
      // $modelNombre= (new \yii\db\Query())
                           // ->select(['em.nombre_emp, em.apellido_pat_emp, em.apellido_mat_emp'])
                           // ->from('tbl_empleados as em')
                           // ->join('INNER JOIN','tbl_hardware_empleados as he',
                               // 'em.PK_EMPLEADO = he.FK_EMPLEADO')
                            // ->join('INNER JOIN','tbl_hardware as hw',
                                  // 'he.FK_Hardware = hw.PK_Hardware')
                           // ->where(['hw.PK_Hardware'=>$id])                                                  
                           // ->all();
          $connection = \Yii::$app->db;
          $modelNombre = $connection->createCommand("CALL SP_ERTSEL_HWR_ObtenerNombreEmpleadoCompleto($id)")->queryAll();

        return $this->render('view', [
            'model' => $model,
             'modelEstatus' => $modelEstatus,
             'modelTipoEquipo' => $modelTipoEquipo,
             'modelProveedor' => $modelProveedor,
             'modelMarca' => $modelMarca,
             'modelNombre' => $modelNombre
        ]);
    }

     /**
     * Creates a new TblHardware model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TblHardware();

        if ($model->load(Yii::$app->request->post())) {
            $model->FK_ESTATUS_HARDWARE = 1;
            $model->FECHA_COMPRA = transform_date($model->FECHA_COMPRA,'Y-m-d');
            $model->GARANTIA = transform_date($model->GARANTIA,'Y-m-d');
            $model->save(true);

            $descripcionBitacora = 'PK_HARDWARE='.$model->PK_HARDWARE.', FECHA_ELIMINACION='.$model->FECHA_COMPRA.', ALTA HARDWARE';
            user_log_bitacora_soporte($descripcionBitacora,'ALTA HARDWARE', $model->PK_HARDWARE);
             return $this->redirect(['index']);

        } else {
            return $this->render('create_hardware', [
                'model' => $model,
            ]);
        }
    }

        public function actionCreate2($id)
    {
         $model = new TblHardware();
         $model = $this->findModel($id);
         $modelTblIngresoGarantias = new TblIngresoGarantias();
                if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio ='';
        }else{
            $unidadNegocio = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
        }
        
        if ($modelTblIngresoGarantias->load(Yii::$app->request->post())) {
            $modelTblIngresoGarantias->FK_HARDWARE = $model->PK_HARDWARE;
            $modelTblIngresoGarantias->FECHA_INGRESO_GARANTIA = transform_date($modelTblIngresoGarantias->FECHA_INGRESO_GARANTIA,'Y-m-d');
            $model->FK_ESTATUS_HARDWARE = 3;
            $model->save(true);   
            $modelTblIngresoGarantias->save(true);

            $descripcionBitacora = 'PK_HARDWARE='.$modelTblIngresoGarantias->FK_HARDWARE.', FECHA_ALTA_GARANTIA='.$modelTblIngresoGarantias->FECHA_INGRESO_GARANTIA.', ALTA GARANTIA';
            user_log_bitacora_soporte($descripcionBitacora,'ALTA GARANTIA', $modelTblIngresoGarantias->FK_HARDWARE);

                Yii::$app->session->setFlash('success', 'La licencia ha sido creada.');

            return $this->redirect(['index']);

        }else{
            $connection = \Yii::$app->db;
            $model2 = $connection->createCommand("CALL SP_ERTSEL_HWR_ObtenerNombreEmpleado($id)")->queryAll();
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->PK_HARDWARE]);
                } else {
                  return $this->render('create_IngresoGarantia', [
                        'model' => $model,
                       'model2' => $model2,
                       'modelTblIngresoGarantias' => $modelTblIngresoGarantias,
                      ]);
                     }
            }
    }



    /**
      * Metodo Update
      * Permite modificar un registro de equipo de computo.
      *
      * @author  Angel Gonzalez
      *
      * @since 1.0
      *
      * @param int    $id ID del hardware (PK_HARDWARE).
      * @return view    Si se accede por post regresa la vista index, de lo contrario regresa la vista update.
      */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model ->FECHA_COMPRA = transform_date($model->FECHA_COMPRA,'d/m/Y');
        $model ->GARANTIA = transform_date($model->GARANTIA,'d/m/Y');

        if ($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post();
            $HardwareEmpleadoss = new TblHardwareEmpleados();
            $HardwareEmpleadoss->load(Yii::$app->request->post());
            if ($model->FK_ESTATUS_HARDWARE == 1)
            {
              $HardwareEmpleados = TblHardwareEmpleados::find()->where(['FK_Hardware' => $id])->one();

              if ($HardwareEmpleados)
              {
                $HardwareEmpleados->delete();
              }
            }
            if ($model->FK_ESTATUS_HARDWARE == 2)
            {
                //dd($data['PK_EMPLEADO']);
                $HardwareEmpleados = TblHardwareEmpleados::find()->where(['FK_Hardware' => $id])->one();

              if ($HardwareEmpleados)
              {
                $HardwareEmpleados->FK_EMPLEADO = $HardwareEmpleadoss->FK_EMPLEADO;
                $HardwareEmpleados->save(true);
              }
              else
              {
                $now = date_create(strtotime(time()));
                $now = date_format($now, "Y/m/d");
                $HardwareEmpleado = new TblHardwareEmpleados();

                    $HardwareEmpleado->FK_HARDWARE = $id;
                    $HardwareEmpleado->FK_EMPLEADO = $HardwareEmpleadoss->FK_EMPLEADO;
                    $HardwareEmpleado->FECHA_REGISTRO = $now;

                    $HardwareEmpleado->save(true);
              }
            }

            $model->FECHA_COMPRA = transform_date($model->FECHA_COMPRA,'Y-m-d');
            $model->GARANTIA = transform_date($model->GARANTIA,'Y-m-d');
            if ($model->save())
            {
              return $this->redirect(['index']);
            }
        } else {


            $HardwareEmpleados = TblHardwareEmpleados::find()->where(['FK_Hardware' => $id])->one();

            $modelResponsableOP = (new \yii\db\Query)
            ->select([
            "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
            'e.PK_Empleado',
            ])
            ->from('tbl_empleados e')
            ->all();

            if ($HardwareEmpleados)
            {
              $Empleado = TblEmpleados::find()->where(['PK_Empleado' => $HardwareEmpleados->FK_EMPLEADO])->one();
              $recurso = "$Empleado->NOMBRE_EMP $Empleado->APELLIDO_PAT_EMP $Empleado->APELLIDO_MAT_EMP";

            } else {
              $recurso = NULL;
            $HardwareEmpleados = new TblHardwareEmpleados();
            }


            return $this->render('update', [
                            'model' => $model,
                            'recurso' => $recurso,
                            'modelResponsableOP' => $modelResponsableOP,
                            'HardwareEmpleados' => $HardwareEmpleados,
            ]);

        }
    }

    /**
     * Deletes an existing TblHardware model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        $model = new TblHardware();

        if (Yii::$app->request->isPost) {
            // Variable de conexión
            $connection = \Yii::$app->db;

            // Se concentra la información obtenida del REQUEST en la variable $post
            $post = Yii::$app->request->post();
            $post['FECHA_ELIMINACION'] = date('Y/m/d');

            // Consulta de registro
            $resultado = TblHardware::find()->Where(['PK_Hardware'=>$post['id']])->asArray()->one();
            $model->attributes = $resultado;

            // Actualización de registro
            $query = strtr("CALL SP_ERTUPD_HWR_ActualizarHardware('$model->PK_HARDWARE', 'FECHA_ELIMINACION', 'MOTIVO_ELIMINACION')", $post);
            $datos = $connection->createCommand($query)->execute();

            // Validación de resultado.
            if ($datos > 0) {
                $descripcionBitacora = 'PK_HARDWARE='.$model->PK_HARDWARE.', FECHA_ELIMINACION='.$post['FECHA_ELIMINACION'].', MOTIVO_ELIMINACION='.$post['MOTIVO_ELIMINACION'];
                user_log_bitacora_soporte($descripcionBitacora,'Modificar Hardware', $model->PK_HARDWARE);

                return $this->redirect(['hardware/equiposeliminados']);
            }
        } else {
            return $this->redirect(['index']);
        }
    }


    /**
       * Retorna la vista de los equipos eliminados
       * @return mixed
       */
      public function actionEquiposeliminados()
      {
          //Se conecta a la base de datos del sistema
          $connection = \Yii::$app->db;
          
          //Si la peticion es por ajax
          if (Yii::$app->request->isAjax) {

            //Se obtienen los valores que vienen por post
            $data = Yii::$app->request->post();
            $post = null;
            parse_str($data['data'], $post);

            $vEquipo          = empty($post['Equipo'])  ? "null" : "'" . $post['Equipo'] . "'";
            $vMarca           = empty($post['Marca'])   ? "null" : "'" . $post['Marca']  . "'";
            $vModelo          = empty($post['Modelo'])  ? "null" : "'" . $post['Modelo'] . "'";
            $vNo_serie        = empty($post['No_Serie'])  ? "null" : "'" . $post['No_Serie'] . "'";
            $vCodigo_Eisei    = empty($post['Codigo'])  ? "null" : "'" . $post['Codigo'] . "'";

            //Se define $datos y se llena con los valores que regresa el Store Procedure 'CALL SP_CONSULTA_EQUIPOS_ELIMINADOS' con los parametros obtenidos de Equipo, Marca, Modelo, Codigo
            $datos = $connection->createCommand("CALL SP_CONSULTA_EQUIPOS_ELIMINADOS(" . $vEquipo . "," . $vMarca . "," . $vModelo . "," . $vNo_serie . "," . $vCodigo_Eisei . ")")->queryAll();

            //Se define el retorno de tipo JSON
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            //retorna un arreglo con los valores de $datos
            return array(
                'data' => $datos
            );

          } else {
            //De lo contrario; retorna la vista indexEquiposEliminados
            return $this->render('indexequiposeliminados', [

            ]);
          }
      }

      public function actionValidar_duplicidad()
      {
        if (Yii::$app->request->isAjax){
            $data = Yii::$app->request->post();
            
            $numSerieRepetido = false;
            $codEiseiRepetido = false;
            $numSerie = $data['numSerie'];
            $codEisei = $data['codEisei'];
            //$idHardware = $data['idHardware'];

            if(strlen($numSerie) > 0){
                $modelHardwareNoSerie = tblHardware::find()->where(['NO_SERIE' => $numSerie])->all();
                if(count($modelHardwareNoSerie)>0){
                $numSerieRepetido = true;
                }
            }
            
            if(strlen($codEisei) > 0){
                $modelHardwareCodEisei = tblHardware::find()->where(['CODIGO_EISEI' => $codEisei])->all();
                if(count($modelHardwareCodEisei)>0){
                    $codEiseiRepetido = true;
                }
            }
                
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'numSerieRepetido' => $numSerieRepetido,
                'codEiseiRepetido' => $codEiseiRepetido,
                //'idHardware' => $idHardware
            ];
        }
    }

    /*Se agrega método para los Equipos Asignados a Bajas*/
      public function actionEquiposasignadosbajas()
      {
          $tamanio_pagina = 5;

          if (Yii::$app->request->isAjax) {
              // Variable utilizada en la consulta
              $connection = \Yii::$app->db;

              // Se concentra la información obtenida del REQUEST en la variable $data
              $data = Yii::$app->request->post();
              $post = null;

              parse_str($data['data'], $post);
              $filtro = array();

              // Declaracion de variables en base al Request
              $filtro['Equipo']       =        (!empty($post['EQUIPO']))          ?   trim($post['EQUIPO'])           :   '';
              $filtro['Marca']        =        (!empty($post['MARCA']))           ?   trim($post['MARCA'])            :   '';
              $filtro['Modelo']       =        (!empty($post['MODELO']))          ?   trim($post['MODELO'])           :   '';
              $filtro['No_Serie']     =        (!empty($post['NO_SERIE']))        ?   trim($post['NO_SERIE'])         :   '';
              $filtro['Recurso']      =        (!empty($post['RECURSO']))         ?   trim($post['RECURSO'])          :   '';
              $filtro['Codigo_Eisei'] =        (!empty($post['CODIGO_EISEI']))    ?   trim($post['CODIGO_EISEI'])     :   '';
              $filtro['Proveedor']    =        (!empty($post['PROVEEDOR']))       ?   trim($post['PROVEEDOR'])        :   '';
              $filtro['Estatus']      =        (!empty($post['ESTATUS']))         ?   trim($post['ESTATUS'])          :   '';
              $filtro['Tipo_Equipo']  =        (!empty($post['TIPO_EQUIPO']))     ?   trim($post['TIPO_EQUIPO'])      :   '';
              $filtro['pagina']       =        (!empty($post['page']))            ?   trim($post['page'])             :   '';
              $filtro['FechaIni']     =        (!empty($post['COMPRAFECHAINI']))  ?   transform_date(trim($post['COMPRAFECHAINI'])) :   '';
              $filtro['FechaFin']     =        (!empty($post['COMPRAFECHAFIN']))  ?   transform_date(trim($post['COMPRAFECHAFIN'])) :   '';

              // Se obtiene el número de registro en base al número de página del grid
              $pagina = (empty($filtro['pagina'])) ? 0 : $filtro['pagina'];
              $aPartirDe = ($pagina == 1) ? 0 : ($pagina * $tamanio_pagina) - $tamanio_pagina;

              // Consulta general
              $query = strtr("CALL SP_ERTSEL_HWR_ObtenerAsignadosBajas('Equipo', 'Marca', 'Modelo', 'No_Serie', 'Codigo_Eisei', 'Proveedor', 'Estatus', 'Tipo_Equipo')", $filtro);
              $datos = $connection->createCommand($query)->queryAll();

              // Se filtra la consulta obtenida en base a los campos de búsqueda Recurso y Fecha_Compra
              $datosFiltrados = array();
              foreach ($datos As $llave => $valor) {
                  $cumpleFiltrado = true;
                  foreach ($valor As $subLlave => $subValor) {
                      switch ($subLlave):
                          case "Recurso":
                              if ($filtro['Recurso'] !== '' && $cumpleFiltrado) {
                                  $cumpleFiltrado = (stripos($subValor, $filtro['Recurso']) !== false) ? true : false;
                              }
                              break;
                          case "Fecha_Compra":
                              if ($cumpleFiltrado) {
                                  $subValor =  transform_date($subValor);
                                  if ($filtro['FechaIni'] !== '') {
                                      if ($filtro['FechaFin'] !== '') {
                                          $cumpleFiltrado = ($subValor >= $filtro['FechaIni'] && $subValor <= $filtro['FechaFin']) ? true : false;
                                      } else {
                                          $cumpleFiltrado = ($subValor >= $filtro['FechaIni'] ) ? true : false;
                                      }
                                  } elseif ($filtro['FechaFin'] !== '') {
                                          $cumpleFiltrado = ($subValor <= $filtro['FechaFin']) ? true : false;
                                  }
                              }
                              break;
                          default:
                              break;
                      endswitch;

                      if (!$cumpleFiltrado) {
                          break 1;
                      }
                  }

                  if ($cumpleFiltrado) {
                      array_push($datosFiltrados, $valor);
                  }
              }

              $total_registros = count($datosFiltrados);
              $total_paginas = ($total_registros > 0) ? ceil($total_registros/$tamanio_pagina) : 1;

              // Se filtra la consulta obtenida en base al número elementos por página
              $pagina = ($total_registros <= $tamanio_pagina) ? $pagina=0 : $pagina;
              $datosPorPagina = array_slice($datosFiltrados, $aPartirDe, $tamanio_pagina);

              // Se declara variable que almacenara el HTML generado.
              $html = "";

              // Bucle que recorre la información filtrada de la Consulta general
              $contadorHardware = 0;
              $contadorRegistro = $aPartirDe;

              foreach ($datosPorPagina As $d) {
                  $contadorHardware += 1;
                  $contadorRegistro += 1;

                  /*
                  *   Se obtiene la información relacionada a la garantía en el grid.
                  *
                  */

                  // Declaración de variables
                  $diasGarantia = 0;

                  // Definición de variables
                  $now = time();
                  $fechaInicial = $d['Fecha_Compra'];

                  // Se obtienen los días de garantía en base a los meses
                  if($d['Garantia']<1){
                      $meses=-1;
                  }
                  else{
                      $meses=$d['Garantia'];
                  }
                  $mesesGarantia = "+" .floor($meses). " month";
                  $fechaFinGarantia = date('Y/m/d', strtotime($mesesGarantia, strtotime($fechaInicial)));
                  $diasGarantia = intval((strtotime($fechaFinGarantia) - strtotime($fechaInicial))/60/60/24);

                  // Se obtienen los días transcurridos a partir de una fecha
                  $diasTranscurridos = intval(($now - strtotime($fechaInicial))/60/60/24);

                  // Se obtienen los días restantes
                  $diasRestantes = $diasGarantia - $diasTranscurridos;

                  // Se obtiene el porcentage de días restantes
                  $diasRestantesPorcent = floor(100 - (($diasTranscurridos / $diasGarantia) * 100));

                  // Se establecen valores a 0 en caso de que los días resultantes sean negativos.
                  $diasRestantes = ($diasRestantes < 0) ? 0 : $diasRestantes;
                  if($meses>0){
                      $diasRestantesPorcent = ($diasRestantesPorcent < 0) ? 0 : $diasRestantesPorcent;
                  }else{
                      $diasRestantesPorcent = 0;
                  }


                  /*
                  *   Se envia la información de la consulta a la variable $val
                  *   se utiliza facilitar la concatenación con strings que
                  *   representan el HTML.
                  */
                  $val['_PK_Hardware_'] = $d['PK_Hardware'];
                  $val['_FK_ESTATUS_RECURSO'] = $d['FK_ESTATUS_RECURSO'];
                  $val['_Fecha_Compra_'] = $d['Fecha_Compra'];
                  $val['_Equipo_'] = $d['Equipo'];
                  $val['_DESC_Tipo_Equipo_'] = $d['DESC_Tipo_Equipo'];
                  $val['_Marca_'] = $d['DESC_Marca'];
                  $val['_Codigo_Eisei_'] = $d['Codigo_Eisei'];
                  $val['_NombreLicencia_'] = $d['NombreLicencia'];
                  $val['_Recurso_'] = $d['Recurso'];
                  $val['_Modelo_'] = $d['Modelo'];
                  $val['_No_Serie_'] = $d['No_Serie'];
                  $val['_DESC_Estatus_Hardware_'] = $d['DESC_Estatus_Hardware'];
                  $val['_DESC_Proveedor_'] = $d['DESC_Proveedor'];
                  $val['_Costo_'] = $d['Costo'];
                  $val['_Garantia_'] = $d['Garantia'];
                  $val['|'] = "<br/>- ";
                  $val['_DiasRestantes_'] = $diasRestantes;
                  $val['_DiasRestantesPorcent_'] = $diasRestantesPorcent;

                  /*
                  *   Se define HTML del grid
                  *
                  */
                  //  Variables que representan elementos HTML
                  $tr =       strtr("<tr id='_PK_Hardware_'>",$val);
                  $tr_ =      "</tr>";
                  $td =       "<td style='border: 0px; text-align: center; background-color:";
                  $tdScrl =   "<td style='border: 0px; text-align: left; background-color:";
                  $tdStat =   "<td name='DESC_Estatus_Hardware' style='border: 0px; text-align: left; background-color:";
                  $tdRcrs =   "<td name='Tabla_Recurso' class='estado-".$d['FK_ESTATUS_RECURSO']."' style='border: 0px; text-align: left; background-color:";
                  $td_ =      "</td>";
                  $bgColor1 = "#FFFFFF"."'>";
                  $bgColor2 = "#F5F5F5"."'>";
                  $checkBoxHardware = strtr("<input type='checkbox' name='_PK_Hardware_' value='_PK_Hardware_' />",$val);
                  $guionMedio = (count($d['NombreLicencia']) > 0 ) ? '- ' : '';

                  // Variables que representan el valor obtenido de la tabla + formato HTML
                  $equipoHtml = "<a href='".Url::to(['hardware/view'])."/".$d['PK_Hardware']."'>".$d['Equipo']."</a>";
                  $fechaCompraHtml = date('d/m/Y', strtotime($d['Fecha_Compra']));
                  $nombreLicenciaHtml = "<div class='scrollbar'><div class='force-overflow'>".$guionMedio.strtr($d['NombreLicencia'], $val)."</div></div>";
                  $costoEquipoHtml = "<div style='text-align: right'>$".number_format(floatval($d['Costo']), 2)."</div>";
                  $labelDiasRestantes = strtr("Días Restantes<br/>_DiasRestantes_<br/>", $val);
                  $labelDiasRestantes .= strtr("<div class='progress'>", $val);
                  $labelDiasRestantes .= strtr("<div class='progress-bar' role='progressbar' aria-valuenow='_DiasRestantesPorcent_' aria-valuemin='0' aria-valuemax='100' style='width:_DiasRestantesPorcent_%'>", $val);
                  $labelDiasRestantes .= strtr("<span class='sr-only'>_DiasRestantesPorcent_% Complete</span></div></div>", $val);

                  // Logica de color de celda
                  $bgColor = ($contadorHardware % 2 == 0) ? $bgColor2 : $bgColor1;

                  /*
                  *   Grid
                  *   Se concatenan las variables definidas para formar el Grid
                  */
                  $campoRecursoGrid = '';
                  if($d['DESC_Estatus_Hardware'] != 'Asociado'){
                      $campoRecursoGrid = 'SIN ASOCIAR';
                  }

                  $html .= $tr;

                  $html .= $td.$bgColor.      $contadorRegistro.                  $td_;
                  $html .= $td.$bgColor.      $checkBoxHardware.                  $td_;
                  $html .= $td.$bgColor.      $fechaCompraHtml.                   $td_;
                  $html .= $td.$bgColor.      $equipoHtml.                        $td_;
                  $html .= $td.$bgColor.      $d['DESC_Tipo_Equipo'].             $td_;
                  $html .= $td.$bgColor.      $d['DESC_Marca'].                   $td_;
                  $html .= $td.$bgColor.      $d['Codigo_Eisei'].                 $td_;
                  $html .= $tdScrl.$bgColor.  $nombreLicenciaHtml.                $td_;
                  $html .= $tdRcrs.$bgColor.  ($d['DESC_Estatus_Hardware'] == 'Asociado' ? $d['Recurso'] : $campoRecursoGrid) . $td_;
                  $html .= $td.$bgColor.      $d['Modelo'].                       $td_;
                  $html .= $td.$bgColor.      $d['No_Serie'].                     $td_;
                  $html .= $tdStat.$bgColor.  $d['DESC_Estatus_Hardware'].        $td_;
                  //$html .= $td.$bgColor.      $d['DESC_Proveedor'].               $td_;
                  $html .= $td.$bgColor.      $costoEquipoHtml.                   $td_;
                  $html .= $td.$bgColor.      $labelDiasRestantes.                $td_;

                  $html .= $tr_;
                  // fin del grid
              }


              \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
              $res = array(
                  'data' => $html,
                  'total_paginas' => $total_paginas,
                  'pagina' => $pagina,
                  'total_registros' => $total_registros,
              );

              return $res;
          } else {
               return $this->render('equiposasignadosbajas', [
                  'total_paginas' => 0,
              ]);
          }
      }

    /**
     * Finds the TblHardware model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblHardware the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblHardware::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
