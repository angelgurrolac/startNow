<?php

namespace app\controllers;

use Yii;
use yii\base\Model;


use yii\db\ActiveQuery;
use yii\db\Query;
use yii\db\Command;
use yii\db\Expression;
use app\models\TblCatUnidadesNegocio;
use app\models\TblCatTipoDocumentos;
use app\models\TblAsignaciones;
use app\models\TblPeriodos;
use app\models\TblFacturas;
use app\models\TblBitComentariosAsignaciones;
use app\models\TblClientes;
use app\models\TblCatUbicaciones;
use app\models\TblCatRazonSocial;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * ClientesController implements the CRUD actions for TblClientes model.
 */
class ReportesController extends Controller
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
     * Lists all TblClientes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $tamanio_pagina= 20;
        
        $datosTipoDocumento = ArrayHelper::map(TblCatTipoDocumentos::find()->select(['PK_TIPO_DOCUMENTO','DESC_TIPO_DOCUMENTO'])->where(['not in','PK_TIPO_DOCUMENTO',[1,5]])->asArray()->all(),'PK_TIPO_DOCUMENTO','DESC_TIPO_DOCUMENTO');
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
            $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }else{
            $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }
        $fk_estatus_asignacion = [2,3,4];
        if (Yii::$app->request->isAjax) {
            //Se recogen variables de entrada
            $data = Yii::$app->request->post();
            $post=null;
            $datos=[];
            parse_str($data['data'],$post);
            if(isset($post['FK_UNIDAD_NEGOCIO']) && !empty($post['FK_UNIDAD_NEGOCIO'])){
                $unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->andFilterWhere(['and',['=','PK_UNIDAD_NEGOCIO',$post['FK_UNIDAD_NEGOCIO']]])->all();
            }elseif((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->all();
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->all();
            }else{
                $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
                $unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->all();
            }
            $filtro_unidad_negocio='';
            foreach($unidades_negocio as $key => $array){
                $filtro_unidad_negocio .= $array['PK_UNIDAD_NEGOCIO'].',';
            }
            $filtro_unidad_negocio = trim($filtro_unidad_negocio,',');
            $filtro_cliente = $post['FK_CLIENTE']!=''?$post['FK_CLIENTE']:'NULL';
            $i=0;
            $anio_actual = $post['año'];
            $html='';

            $años = array();
            $contadorAños = 0;
            $bandera = true;

            $connection = \Yii::$app->db;

            //Consulta de todas
            $años_anteriores =  $connection->createCommand("SELECT
                YEAR(peri.FECHA_INI) ANIO_PERIODO,
                SUM(peri.MONTO) TOTAL_ODC,
                SUM(IF(peri.FK_DOCUMENTO_ODC IS NULL, 0, peri.MONTO)) MONTO_CON_ODC
            FROM tbl_asignaciones asig
                LEFT JOIN tbl_periodos peri
                    ON peri.FK_ASIGNACION = asig.PK_ASIGNACION
                    AND peri.FECHA_INI < IFNULL((SELECT com3.FECHA_FIN
                                                FROM tbl_bit_comentarios_asignaciones com3
                                                WHERE com3.FK_ESTATUS_ASIGNACION = 5
                                                AND com3.FK_ASIGNACION = asig.PK_ASIGNACION
                                                ),'2050-01-01')
            WHERE asig.FK_ESTATUS_ASIGNACION in (2,3,4,5)
            AND peri.FECHA_INI IS NOT NULL
            AND MONTH(peri.FECHA_INI) <= MONTH(CURRENT_DATE())
            AND (SELECT com2.FK_ASIGNACION
                FROM tbl_bit_comentarios_asignaciones com2
                WHERE com2.FK_ESTATUS_ASIGNACION = 6
                AND com2.FK_ASIGNACION = asig.PK_ASIGNACION
                AND com2.FECHA_FIN <= peri.FECHA_INI
                AND com2.FECHA_RETOMADA >= peri.FECHA_FIN
            ) IS NULL AND YEAR(peri.FECHA_INI) <= YEAR(NOW()) GROUP BY
                    asig.FK_UNIDAD_NEGOCIO,
                    asig.FK_CLIENTE,
                    MONTH(peri.FECHA_INI)
                    ORDER BY YEAR(peri.FECHA_INI)")->queryAll();

            foreach ($años_anteriores as $key => $value) {

                $anio = $años_anteriores[$key]['ANIO_PERIODO'];
                $res_odc = floor(($años_anteriores[$key]['MONTO_CON_ODC']>0)?((float)$años_anteriores[$key]['MONTO_CON_ODC']/(float)$años_anteriores[$key]['TOTAL_ODC'])*100:0);

                if (!in_array($anio, $años)) {
                    $años[] = $anio;
                    $contadorAños++;

                    if ($res_odc <= 99 && $anio < date('Y') && $bandera) {
                        $colores[$contadorAños] = '<a href="javascript:void(0);" onclick="consulta_años('.$anio.');"><span style="color:red" class="font-bold">'.$anio.'</span></a>';
                        $bandera = false;
                    } else {
                        $colores[$contadorAños] = '<a href="javascript:void(0);" onclick="consulta_años('.$anio.');"><span style="color:blue" class="font-bold">'.$anio.'</span></a>';
                    }
                }

            }

            //$connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_GENERAL_ASIGNACIONES($anio_actual,'$filtro_unidad_negocio',$filtro_cliente)")->queryAll();
            $connection->close();
            if( count($total_registros) > 0){
                $unidadNegocioAnterior = '';
                $clienteAnterior = '';
                $html2='';
                $html3='';
                /* Totales ODC */
                $contadorMes = 0;
                $cant_asig_ODC = 0;
                $total_asig_ODC = 0;
                $cant_asig_con_ODC = 0;
                $total_asig_con_ODC = 0;
                $cant_asig_sin_ODC = 0;
                $total_asig_sin_ODC = 0;
                /* Totales HDE */
                $cant_asig_HDE = 0;
                $total_asig_HDE = 0;
                $cant_asig_con_HDE = 0;
                $total_asig_con_HDE = 0;
                $cant_asig_sin_HDE = 0;
                $total_asig_sin_HDE = 0;
                /* Totales FACTURACION */
                $cant_asig_pend_facturar = 0;
                $total_asig_pend_facturar = 0;
                $cant_asig_FACTURA = 0;
                $total_asig_FACTURA = 0;
                $cant_asig_fact_pagada = 0;
                $total_asig_fact_pagada = 0;
                $cant_asig_fact_sin_pagar = 0;
                $total_asig_fact_sin_pagar = 0;
                foreach($total_registros as $key => $array){
                    $html3 = '';
                    /* Se verifica si hay cambio en la unidad de negocio */
                    if($array['FK_UNIDAD_NEGOCIO'] != $unidadNegocioAnterior){
                        $html3.="<tr style='background-color:#a4a4a4'><td width='100%'  class='font-bold full-row' colspan='12' >".$array['DESC_UNIDAD_NEGOCIO']."</td></tr>";
                    }
                    /* Se verifica si hay un cambio en el cliente */
                    if($array['FK_CLIENTE'] != $clienteAnterior || $html3!=''){
                        $html3.="<tr style='background-color:#0F1F50;'><td width='100%' style='color:#fff' class='font-bold full-row'  colspan='12' data-pk_cliente=".$array['FK_CLIENTE']." data-nombre_cliente='".$array['NOMBRE_CLIENTE']."'>".$array['NOMBRE_CLIENTE']." (".$array['ASIG_ACTIVAS']." Asig. activas) <div class='icon-chart' style='display:none'></div></td></tr>";
                    }

                    /* Se valida si hubo un cambio de unidad de negocio o cliente*/
                    if($key==0){
                        $html.=$html3;
                    }elseif($html3 != ''){
                        $color = '';
                        $odc_porcentaje = 0;
                        $res_odc = floor(($total_asig_con_ODC>0)?((float)$total_asig_con_ODC/(float)$total_asig_ODC)*100:0);
                        if($res_odc<=49){
                            $color = 'red';
                        }elseif($res_odc>49&&$res_odc<=99){
                            $color = 'yellow';
                        }elseif($res_odc==100){
                            $color = '#84da84';
                        }
                        $html.="<tr class='font-bold full-row row-totales'>";
                            $html.="<td class='td-width-periodo text-center'>TOTALES</td>";
                            $html.="<td class='td-width-total td-odc text-center' >".round($cant_asig_ODC/$contadorMes)." Pm</td>";
                            $html.="<td class='td-width-led td-odc text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                            $html.="<td class='td-width-number td-odc text-right'>$".number_format($total_asig_ODC,2)."</td>";
                            $html.="<td class='td-width-total td-odc text-center' style='background-color: #84da84;'></td>";
                            $html.="<td class='td-width-number td-odc text-right' style='background-color: #84da84;' >$".number_format($total_asig_con_ODC,2)."</td>";
                            $html.="<td class='td-width-total td-odc text-center' style='background-color:rgb(249, 141, 141)' >".$cant_asig_sin_ODC."</td>";
                            $html.="<td class='td-width-number td-odc text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_sin_ODC,2)."</td>";
                            $color = '';
                            $odc_porcentaje = 0;
                            $res_odc = floor(($total_asig_con_HDE>0)?((float)$total_asig_con_HDE/(float)$total_asig_HDE)*100:0);
                            if($res_odc<=49){
                                $color = 'red';
                            }elseif($res_odc>49&&$res_odc<=99){
                                $color = 'yellow';
                            }elseif($res_odc==100){
                                $color = '#84da84';
                            }
                            $html.="<td class='td-width-total td-hde text-center' > </td>";
                            $html.="<td class='td-width-led td-hde text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                            $html.="<td class='td-width-number td-hde text-right'>$".number_format($total_asig_HDE,2)."</td>";
                            $html.="<td class='td-width-total td-hde text-center' style='background-color: #84da84;'></td>";
                            $html.="<td class='td-width-number td-hde text-right' style='background-color: #84da84;'>$".number_format($total_asig_con_HDE,2)."</td>";
                            $html.="<td class='td-width-total td-hde text-center' style='background-color:rgb(249, 141, 141)'>".$cant_asig_sin_HDE."</td>";
                            $html.="<td class='td-width-number td-hde text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_sin_HDE,2)."</td>";
                            $color = '';
                            $odc_porcentaje = 0;
                            $res_odc = floor(($total_asig_fact_pagada>0)?((float)$total_asig_fact_pagada/(float)$total_asig_FACTURA)*100:0);
                            if($res_odc<=49){
                                $color = 'red';
                            }elseif($res_odc>49&&$res_odc<=99){
                                $color = 'yellow';
                            }elseif($res_odc==100){
                                $color = '#84da84';
                            }
                            $html.="<td class=' td-width-total td-factura text-center' style='background-color:orange'>".$cant_asig_pend_facturar."</td>";
                            $html.="<td class=' td-width-number td-factura text-right' style='background-color:orange'>$".number_format($total_asig_pend_facturar,2)."</td>";
                            $html.="<td class=' td-width-total td-factura text-center' > </td>";
                            $html.="<td class=' td-width-led td-factura text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                            $html.="<td class=' td-width-number td-factura text-right'>$".number_format($total_asig_FACTURA,2)."</td>";
                            $html.="<td class=' td-width-total td-factura text-center' style='background-color: #84da84;'></td>";
                            $html.="<td class=' td-width-number td-factura text-right' style='background-color: #84da84;'>$".number_format($total_asig_fact_pagada,2)."</td>";
                            $html.="<td class=' td-width-total td-factura text-center' style='background-color:rgb(249, 141, 141)'>".$cant_asig_fact_sin_pagar."</td>";
                            $html.="<td class=' td-width-number td-factura text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_fact_sin_pagar,2)."</td>";
                        $html.="</tr>";
                        $html.=$html2;
                        $html.="<tr style='background-color:#fff'><td width='100%'  class='font-bold full-row' colspan='12' ><canvas id=\"chart-".$clienteAnterior."\" width=\"1080\" height=\"540\" style=\"display:none\"></canvas></br><a href=\"javascript:void(0)\" style=\"font-size:12px ;display:none \" data-pk_cliente='".$clienteAnterior."' class=\"cerrar_grafica c-".$clienteAnterior."\">(cerrar)</a></td></tr>";
                        $html.=$html3;
                        $html2 = '';
                        /* Totales ODC */
                        $contadorMes = 0;
                        $cant_asig_ODC = 0;
                        $total_asig_ODC = 0;
                        $cant_asig_con_ODC = 0;
                        $total_asig_con_ODC = 0;
                        $cant_asig_sin_ODC = 0;
                        $total_asig_sin_ODC = 0;
                        /* Totales HDE */
                        $cant_asig_HDE = 0;
                        $total_asig_HDE = 0;
                        $cant_asig_con_HDE = 0;
                        $total_asig_con_HDE = 0;
                        $cant_asig_sin_HDE = 0;
                        $total_asig_sin_HDE = 0;
                        /* Totales FACTURACION */
                        $cant_asig_pend_facturar = 0;
                        $total_asig_pend_facturar = 0;
                        $cant_asig_FACTURA = 0;
                        $total_asig_FACTURA = 0;
                        $cant_asig_fact_pagada = 0;
                        $total_asig_fact_pagada = 0;
                        $cant_asig_fact_sin_pagar = 0;
                        $total_asig_fact_sin_pagar = 0;
                    }


                    $color = '';
                    $odc_porcentaje = 0;
                    $res_odc = floor(($array['MONTO_CON_ODC']>0)?((float)$array['MONTO_CON_ODC']/(float)$array['TOTAL_ODC'])*100:0);
                    if($res_odc<=49){
                        $color = 'red';
                    }elseif($res_odc>49&&$res_odc<=99){
                        $color = 'yellow';
                    }elseif($res_odc==100){
                        $color = '#84da84';
                    }

                    $html2.="<tr>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-periodo text-center'>".$array['MES_NOMBRE']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-odc text-center'> ".$array['CANT_ASIG']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-led td-odc text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-number td-odc text-right' >".number_format($array['TOTAL_ODC'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-odc text-center' > ".$array['CANT_ASIG_CON_ODC']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." con-odc td-width-number td-odc text-right' data-valor=".$array['MONTO_CON_ODC']." data-mes=".$array['MES_PERIODO']." >".number_format($array['MONTO_CON_ODC'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-odc text-center' > ".$array['CANT_ASIG_SIN_ODC']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." sin-odc td-width-number td-odc text-right' data-valor=".$array['MONTO_SIN_ODC']."  data-mes=".$array['MES_PERIODO'].">".number_format($array['MONTO_SIN_ODC'],2)."</td>";

                        $color = '';
                        $odc_porcentaje = 0;
                        $res_odc = floor(($array['MONTO_CON_HDE']>0)?((float)$array['MONTO_CON_HDE']/(float)$array['TOTAL_HDE'])*100:0);
                        if($res_odc<=49){
                            $color = 'red';
                        }elseif($res_odc>49&&$res_odc<=99){
                            $color = 'yellow';
                        }elseif($res_odc==100){
                            $color = '#84da84';
                        }

                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-hde text-center'> ".$array['CANT_ASIG_HDE']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-led td-hde text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-number td-hde text-right' >".number_format($array['TOTAL_HDE'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-hde text-center' > ".$array['CANT_ASIG_CON_HDE']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." con-hde td-width-number td-hde text-right' data-valor=".$array['MONTO_CON_HDE']."  data-mes=".$array['MES_PERIODO'].">".number_format($array['MONTO_CON_HDE'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-hde text-center' > ".$array['CANT_ASIG_SIN_HDE']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." sin-hde td-width-number td-hde text-right' data-valor=".$array['MONTO_SIN_HDE']."  data-mes=".$array['MES_PERIODO'].">".number_format($array['MONTO_SIN_HDE'],2)."</td>";

                        $color = '';
                        $odc_porcentaje = 0;
                        $res_odc = floor(($array['TOTAL_ASIG_FACT_PAG']>0)?((float)$array['TOTAL_ASIG_FACT_PAG']/(float)$array['TOTAL_FACT'])*100:0);
                        if($res_odc<=49){
                            $color = 'red';
                        }elseif($res_odc>49&&$res_odc<=99){
                            $color = 'yellow';
                        }elseif($res_odc==100){
                            $color = '#84da84';
                        }
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-factura text-center' > ".
                            ($array['CANT_ASIG_PEND_FACT']!='0'?"<a href='javascript:void(0);' class='ajaxDetallePendienteFacturar'>":"").
                            $array['CANT_ASIG_PEND_FACT'].
                            ($array['CANT_ASIG_PEND_FACT']!='0'?"</a>":'').
                            "<input type='hidden' id='MES' value='".$array['MES_PERIODO']."' >".
                            "<input type='hidden' id='FK_CLIENTE' value='".$array['FK_CLIENTE']."' >".
                            "<input type='hidden' id='FK_UNIDAD_NEGOCIO' value='".$array['FK_UNIDAD_NEGOCIO']."' >".
                            "<input type='hidden' id='ANIO' value='2016'>".
                        "</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." sin-factura td-width-number td-factura text-right' data-valor=".$array['TOTAL_PEND_FACT']." data-mes=".$array['MES_PERIODO'].">".number_format($array['TOTAL_PEND_FACT'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-factura text-center'> ".$array['CANT_ASIG_FACT']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-led td-factura text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-number td-factura text-right' >".number_format($array['TOTAL_FACT'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-factura text-center' > ".$array['CANT_ASIG_FACT_PAG']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." con-factura td-width-number td-factura text-right' data-valor=".$array['TOTAL_ASIG_FACT_PAG']." data-mes=".$array['MES_PERIODO'].">".number_format($array['TOTAL_ASIG_FACT_PAG'],2)."</td>";
                        //$html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-factura text-center' > ".$asignaciones_sin_factura."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-factura text-center' > ".
                            ($array['CANT_ASIG_FACT_SIN_PAG']!='0'?"<a href='javascript:void(0);' class='ajaxDetalleFactura'>":"").
                            $array['CANT_ASIG_FACT_SIN_PAG'].
                            ($array['CANT_ASIG_FACT_SIN_PAG']!='0'?"</a>":'').
                            "<input type='hidden' id='MES' value='".$array['MES_PERIODO']."' >".
                            "<input type='hidden' id='FK_CLIENTE' value='".$array['FK_CLIENTE']."' >".
                            "<input type='hidden' id='FK_UNIDAD_NEGOCIO' value='".$array['FK_UNIDAD_NEGOCIO']."' >".
                            "<input type='hidden' id='ANIO' value='2016' >".
                        "</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." sin-factura td-width-number td-factura text-right' data-valor=".$array['TOTAL_ASIG_FACT_SIN_PAG']." data-mes=".$array['MES_PERIODO'].">".number_format($array['TOTAL_ASIG_FACT_SIN_PAG'],2)."</td>";
                    $html2.="</tr>";

                    /* Totales ODC */
                    $contadorMes++;
                    $cant_asig_ODC += $array['CANT_ASIG'];
                    $total_asig_ODC +=$array['TOTAL_ODC'];
                    $cant_asig_con_ODC +=$array['CANT_ASIG_CON_ODC'];
                    $total_asig_con_ODC +=$array['MONTO_CON_ODC'];
                    $cant_asig_sin_ODC +=$array['CANT_ASIG_SIN_ODC'];
                    $total_asig_sin_ODC +=$array['MONTO_SIN_ODC'];
                    /* Totales HDE */
                    $cant_asig_HDE += $array['CANT_ASIG_HDE'];
                    $total_asig_HDE += $array['TOTAL_HDE'];
                    $cant_asig_con_HDE += $array['CANT_ASIG_CON_HDE'];
                    $total_asig_con_HDE += $array['MONTO_CON_HDE'];
                    $cant_asig_sin_HDE += $array['CANT_ASIG_SIN_HDE'];
                    $total_asig_sin_HDE += $array['MONTO_SIN_HDE'];
                    /* Totales FACTURACION */
                    $cant_asig_pend_facturar += $array['CANT_ASIG_PEND_FACT'];
                    $total_asig_pend_facturar += $array['TOTAL_PEND_FACT'];
                    $cant_asig_FACTURA += $array['CANT_ASIG_FACT'];
                    $total_asig_FACTURA += $array['TOTAL_FACT'];
                    $cant_asig_fact_pagada += $array['CANT_ASIG_FACT_PAG'];
                    $total_asig_fact_pagada += $array['TOTAL_ASIG_FACT_PAG'];
                    $cant_asig_fact_sin_pagar += $array['CANT_ASIG_FACT_SIN_PAG'];
                    $total_asig_fact_sin_pagar += $array['TOTAL_ASIG_FACT_SIN_PAG'];
                    $unidadNegocioAnterior = $array['FK_UNIDAD_NEGOCIO'];
                    $clienteAnterior = $array['FK_CLIENTE'];
                }

                $html.="<tr class='font-bold full-row row-totales'>";
                    $html.="<td class='td-width-periodo text-center'>TOTALES</td>";
                    $html.="<td class='td-width-total td-odc text-center' >".round($cant_asig_ODC/$contadorMes)." Pm</td>";
                    $html.="<td class='td-width-led td-odc text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                    $html.="<td class='td-width-number td-odc text-right'>$".number_format($total_asig_ODC,2)."</td>";
                    $html.="<td class='td-width-total td-odc text-center' style='background-color: #84da84;'></td>";
                    $html.="<td class='td-width-number td-odc text-right' style='background-color: #84da84;' >$".number_format($total_asig_con_ODC,2)."</td>";
                    $html.="<td class='td-width-total td-odc text-center' style='background-color:rgb(249, 141, 141)' >".$cant_asig_sin_ODC."</td>";
                    $html.="<td class='td-width-number td-odc text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_sin_ODC,2)."</td>";
                    $color = '';
                    $odc_porcentaje = 0;
                    $res_odc = floor(($total_asig_con_HDE>0)?((float)$total_asig_con_HDE/(float)$total_asig_HDE)*100:0);
                    if($res_odc<=49){
                        $color = 'red';
                    }elseif($res_odc>49&&$res_odc<=99){
                        $color = 'yellow';
                    }elseif($res_odc==100){
                        $color = '#84da84';
                    }
                    $html.="<td class='td-width-total td-hde text-center' > </td>";
                    $html.="<td class='td-width-led td-hde text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                    $html.="<td class='td-width-number td-hde text-right'>$".number_format($total_asig_HDE,2)."</td>";
                    $html.="<td class='td-width-total td-hde text-center' style='background-color: #84da84;'></td>";
                    $html.="<td class='td-width-number td-hde text-right' style='background-color: #84da84;'>$".number_format($total_asig_con_HDE,2)."</td>";
                    $html.="<td class='td-width-total td-hde text-center' style='background-color:rgb(249, 141, 141)'>".$cant_asig_sin_HDE."</td>";
                    $html.="<td class='td-width-number td-hde text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_sin_HDE,2)."</td>";
                    $color = '';
                    $odc_porcentaje = 0;
                    $res_odc = floor(($total_asig_fact_pagada>0)?((float)$total_asig_fact_pagada/(float)$total_asig_FACTURA)*100:0);
                    if($res_odc<=49){
                        $color = 'red';
                    }elseif($res_odc>49&&$res_odc<=99){
                        $color = 'yellow';
                    }elseif($res_odc==100){
                        $color = '#84da84';
                    }
                    $html.="<td class=' td-width-total td-factura text-center' style='background-color:orange'>".$cant_asig_pend_facturar."</td>";
                    $html.="<td class=' td-width-number td-factura text-right' style='background-color:orange'>$".number_format($total_asig_pend_facturar,2)."</td>";
                    $html.="<td class=' td-width-total td-factura text-center' > </td>";
                    $html.="<td class=' td-width-led td-factura text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                    $html.="<td class=' td-width-number td-factura text-right'>$".number_format($total_asig_FACTURA,2)."</td>";
                    $html.="<td class=' td-width-total td-factura text-center' style='background-color: #84da84;'></td>";
                    $html.="<td class=' td-width-number td-factura text-right' style='background-color: #84da84;'>$".number_format($total_asig_fact_pagada,2)."</td>";
                    $html.="<td class=' td-width-total td-factura text-center' style='background-color:rgb(249, 141, 141)'>".$cant_asig_fact_sin_pagar."</td>";
                    $html.="<td class=' td-width-number td-factura text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_fact_sin_pagar,2)."</td>";
                $html.="</tr>";
                $html.=$html2;
            }else{
                $html.= "<tr>";
                    $html.='<td colspan="24" rowspan="3" style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;background-color: #D0D0D0;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.= "</tr>";
            }
                
            $res = [
                'html'=>$html,
                'post'=>$post,
                'años'=>$colores
            ];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $res;

        }else{
            user_log_bitacora('Consulta: Reporte General de Asignaciones','Consulta: Reporte General de Asignaciones','0');
            return $this->render('index', [
                'total_paginas' => 0,
                'unidadNegocio' => $unidadNegocio,
                'datosTipoDocumento' => $datosTipoDocumento,
            ]);
            
        }
    }

    public function actionIndex_consolidado()
    {
        $request = Yii::$app->request;
        $tamanio_pagina= 20;
        
        $datosTipoDocumento = ArrayHelper::map(TblCatTipoDocumentos::find()->select(['PK_TIPO_DOCUMENTO','DESC_TIPO_DOCUMENTO'])->where(['not in','PK_TIPO_DOCUMENTO',[1,5]])->asArray()->all(),'PK_TIPO_DOCUMENTO','DESC_TIPO_DOCUMENTO');
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
            $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }else{
            $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }
        $fk_estatus_asignacion = [2,3,4];
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            $datos=[];
            parse_str($data['data'],$post);
 
            if(isset($post['FK_UNIDAD_NEGOCIO']) && !empty($post['FK_UNIDAD_NEGOCIO'])){
                $buscar_unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO'])->andFilterWhere(['and',['=','PK_UNIDAD_NEGOCIO',$post['FK_UNIDAD_NEGOCIO']]])->asArray()->all();
            }elseif((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $buscar_unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO'])->asArray()->all();
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $buscar_unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->asArray()->all();
            }else{
                $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
                $buscar_unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->asArray()->all();
            }
               
            foreach($buscar_unidades_negocio as $index => $array){
                $unidades_negocio[] = $array['PK_UNIDAD_NEGOCIO'];
            }

            $i=0;
            $anio_actual=date('Y');

            //Obtiene el total de asignaciones a procesar
            $asignaciones_cliente = TblAsignaciones::find()->select(['count(PK_ASIGNACION) as asignaciones'])
                                                                                    ->andFilterWhere(['and',
                                                                                        ['IN','FK_UNIDAD_NEGOCIO',$unidades_negocio],
                                                                                        ['IN','FK_ESTATUS_ASIGNACION',$fk_estatus_asignacion],
                                                                                        ['=','FK_CLIENTE',$post['FK_CLIENTE']],
                                                                                    ])
                                                                                    ->asArray()
                                                                                    ->one();
            //Se inicializan variables
            $periodos=[];
            $total_periodo=[];

            //Se hace la consulta de las asignaciones a involucrar
            $asignaciones_periodo_cliente = TblAsignaciones::find()
                                                            ->select(['PK_ASIGNACION'])
                                                            ->andFilterWhere(['and',
                                                                ['IN','FK_UNIDAD_NEGOCIO',$unidades_negocio],
                                                                ['IN','FK_ESTATUS_ASIGNACION',$fk_estatus_asignacion],
                                                                ['=','FK_CLIENTE',$post['FK_CLIENTE']],
                                                            ])
                                                            ->asArray()
                                                            ->column();
            //Se empiezan a recorrer los meses
            for ($j=1; $j < 12; $j++) { 
                //Se hace la consulta de los periodos que participan en el mes que va el ciclo
                $consulta_periodos= TblPeriodos::find()
                    ->select(['MONTO','FK_DOCUMENTO_ODC','FK_ASIGNACION','FK_DOCUMENTO_HDE','MONTO_HDE','PK_PERIODO','MONTO_FACTURA','FK_DOCUMENTO_FACTURA','DATE_FORMAT(FECHA_INI, "%d-%m-%Y %H:%i:%s") FECHA_INI','DATE_FORMAT(FECHA_FIN, "%d-%m-%Y %H:%i:%s") FECHA_FIN'])
                    ->where("MONTH(FECHA_INI)= $j AND YEAR(FECHA_INI) = $anio_actual AND MONTH(FECHA_INI)<=".date('m'))
                    ->andWhere(['IN','FK_ASIGNACION',$asignaciones_periodo_cliente])
                    ->asArray()->all();

                $consulta[$j] = $consulta_periodos;
                //Se hace una consulta para obtener las fechas en que una asignacion estuvo detenida
                $consulta_comentarios_asignaciones = TblBitComentariosAsignaciones::find()
                    ->select([
                        'FK_ASIGNACION',
                        'DATE_FORMAT(FECHA_FIN, "%d-%m-%Y %H:%i:%s") FECHA_FIN',
                        'DATE_FORMAT(FECHA_RETOMADA, "%d-%m-%Y %H:%i:%s") FECHA_RETOMADA'
                        ])
                    ->andWhere(['IN','FK_ASIGNACION',$asignaciones_periodo_cliente])
                    ->andWhere(['=','FK_ESTATUS_ASIGNACION', '6'])
                    ->asArray()
                    ->all();

                //Se obtienen los periodos cuyas fechas se traslapan con las fechas de detencion de una asignacion
                $posicionPeriodoEliminar = [];
                foreach ($consulta_periodos as $key_consulta_periodos_2 => $array_consulta_periodos_2) {
                    $fecha_ini_periodo = strtotime($array_consulta_periodos_2['FECHA_INI']);
                    $fecha_fin_periodo = strtotime($array_consulta_periodos_2['FECHA_FIN']);
                    $asignacion_checar = $array_consulta_periodos_2['FK_ASIGNACION'];
                    foreach($consulta_comentarios_asignaciones as $array2){
                        if($array2['FK_ASIGNACION']==$asignacion_checar){
                            $fecha_ini_detenida = strtotime($array2['FECHA_FIN']);
                            $fecha_fin_detenida = strtotime($array2['FECHA_RETOMADA']);
                            if($fecha_ini_periodo >= $fecha_ini_detenida && $fecha_ini_periodo <= $fecha_fin_detenida && $fecha_fin_periodo >= $fecha_ini_detenida && $fecha_fin_periodo <= $fecha_fin_detenida){
                                $posicionPeriodoEliminar[] = $key_consulta_periodos_2;
                            }
                        }
                    }
                }

                //Si se detectaron periodos que traslapan sus fechas con la fecha de detencion de una asignacion, se eliminan
                if(count($posicionPeriodoEliminar) > 0){
                    foreach($posicionPeriodoEliminar as $valor_posicion_eliminar){
                        unset($consulta_periodos[$valor_posicion_eliminar]);
                    }
                }

                //Se reinician los indices del array
                $consulta_periodos = array_values($consulta_periodos);

                //Si existen periodos a consultar
                if($consulta_periodos){
                    $periodos[$j]=$consulta_periodos;
                    if($periodos[$j]){
                        $odc=0;
                        $odc_pagado=0;
                        $odc_faltante=0;
                        
                        $asignaciones_odc=[];
                        $asignaciones_sin_odc=[];
                        $total_odc=0;
                        $hde=0;
                        $hde_pagado=0;
                        $hde_faltante=0;
                        
                        $asignaciones_hde=[];
                        $asignaciones_sin_hde=[];
                        $total_hde=0;
                        $factura=0;
                        $factura_pagado=0;
                        $factura_faltante=0;
                        $pendiente_facturar=0;
                        
                        $asignaciones_factura=[];
                        $asignaciones_sin_factura=[];
                        $asignaciones_pendientes_facturar=[];
                        $total_factura=0;

                        foreach ($periodos[$j] as $index_periodos => $periodo) {
                            $odc+=(float)$periodo['MONTO'];
                            $hde+=(float)$periodo['MONTO'];
                            $factura+=(float)$periodo['MONTO'];

                            if($periodo['FK_DOCUMENTO_ODC']){
                               $odc_pagado+=(float)$periodo['MONTO']; 
                               if(!in_array($periodo['FK_ASIGNACION'], $asignaciones_odc)){
                                    $asignaciones_odc[]=$periodo['FK_ASIGNACION'];
                               }
                            }else{
                               $odc_faltante+=(float)$periodo['MONTO']; 
                               if(!in_array($periodo['FK_ASIGNACION'], $asignaciones_sin_odc)){
                                    $asignaciones_sin_odc[]=$periodo['FK_ASIGNACION'];
                               }
                            }
                            $total_odc+=(float)$periodo['MONTO']; 

                            if($periodo['FK_DOCUMENTO_HDE']){
                                $hde_pagado+=(float)$periodo['MONTO_HDE']; 
                                $total_hde+=(float)$periodo['MONTO_HDE'];
                                 if(!in_array($periodo['FK_ASIGNACION'], $asignaciones_hde)){
                                    $asignaciones_hde[]=$periodo['FK_ASIGNACION'];
                                }
                            }else{
                                $hde_faltante+=(float)$periodo['MONTO']; 
                                $total_hde+=(float)$periodo['MONTO']; 
                                if(!in_array($periodo['FK_ASIGNACION'], $asignaciones_sin_hde)){
                                    $asignaciones_sin_hde[]=$periodo['FK_ASIGNACION'];
                                }
                            }

                            if($periodo['FK_DOCUMENTO_FACTURA']){
                                $consulta_fecha_pago = TblFacturas::find()->where(['FECHA_INGRESO_BANCO'=>null])->andWhere(['FK_PERIODO'=>$periodo['PK_PERIODO']])->limit(1)->one();

                                if(!$consulta_fecha_pago){
                                    $factura_pagado+=(float)$periodo['MONTO_FACTURA']; 
                                    $total_factura+=(float)$periodo['MONTO_FACTURA']; 
                                    if(!in_array($periodo['FK_ASIGNACION'], $asignaciones_factura)){
                                        $asignaciones_factura[]=$periodo['FK_ASIGNACION'];
                                    }
                                    // $pagados++;
                                }else{
                                    $factura_faltante+=(float)$periodo['MONTO_FACTURA']; 
                                    $total_factura+=(float)$periodo['MONTO_FACTURA']; 
                                    if(!in_array($periodo['FK_ASIGNACION'], $asignaciones_sin_factura)){
                                        $asignaciones_sin_factura[]=$periodo['FK_ASIGNACION'];
                                    }
                                    // $faltantes++;
                                }
                            }else{
                                if($periodo['FK_DOCUMENTO_HDE']){
                                    $pendiente_facturar+=(float)$periodo['MONTO']; 
                                    //$pendiente_facturar+=(float)$periodo['MONTO']; 
                                    if(!in_array($periodo['FK_ASIGNACION'], $asignaciones_pendientes_facturar)){
                                        $asignaciones_pendientes_facturar[]=$periodo['FK_ASIGNACION'];
                                    }
                                }
                                /*
                                $factura_faltante+=(float)$periodo['MONTO']; 
                                $total_factura+=(float)$periodo['MONTO']; 
                                if(!in_array($periodo['FK_ASIGNACION'], $asignaciones_sin_factura)){
                                    $asignaciones_sin_factura[]=$periodo['FK_ASIGNACION'];
                                }
                                */
                                // $faltantes++;
                            }
                            
                        }
                        $total_periodo[$j]= [
                            'odc'=>$odc,
                            'odc_pagado'=>$odc_pagado,
                            'odc_faltante'=>$odc_faltante,
                            'total_odc'=>$total_odc,
                            'hde'=>$hde,
                            'hde_pagado'=>$hde_pagado,
                            'hde_faltante'=>$hde_faltante,
                            'total_hde'=>$total_hde,
                            'factura'=>$factura,
                            'factura_pagado'=>$factura_pagado,
                            'factura_faltante'=>$factura_faltante,
                            'total_factura'=>$total_factura,
                            'asignaciones_odc'=>$asignaciones_odc,
                            'asignaciones_sin_odc'=>$asignaciones_sin_odc,
                            'asignaciones_hde'=>$asignaciones_hde,
                            'asignaciones_sin_hde'=>$asignaciones_sin_hde,
                            'asignaciones_factura'=>$asignaciones_factura,
                            'asignaciones_sin_factura'=>$asignaciones_sin_factura,
                            'pendiente_facturar'=>$pendiente_facturar,
                            'asignaciones_pendientes_facturar'=>$asignaciones_pendientes_facturar,
                        ];
                    }
                }
            }
            $arrayMeses = array(
                1 => "Enero",
                2 => "Febrero",
                3 => "Marzo",
                4 => "Abril",
                5 => "Mayo",
                6 => "Junio",
                7 => "Julio",
                8 => "Agosto",
                9 => "Septiembre",
                10 => "Octubre",
                11 => "Noviembre",
                12 => "Diciembre", );
            $total_odc                      =0;
            $odc_pagado                     =0;
            $odc_faltante                   =0;
            $total_hde                      =0;
            $hde_pagado                     =0;
            $hde_faltante                   =0;
            $total_factura                  =0;
            $factura_pagado                 =0;
            $factura_faltante               =0;
            $factura_pendiente              =0;
            $asignaciones_odc               =0;
            $asignaciones_sin_odc           =0;
            $asignaciones_hde               =0;
            $asignaciones_sin_hde           =0;
            $asignaciones_factura           =0;
            $asignaciones_sin_factura       =0;
            $asignaciones_pendientes_facturar = 0;
            $total_asg_odc                  =0;
            $total_asg_hde                  =0;
            $total_asg_factura              =0;
            $total_asignaciones_pendientes_facturar  =0;
            $g_total_asg_odc                =0;
            $g_total_asg_hde                =0;
            $g_total_asg_factura            =0;
            $g_total_asig_pendientes        =0;
            $total_asignaciones_odc         = 0;
            $total_asignaciones_sin_odc     = 0;
            $total_asignaciones_hde         = 0;
            $total_asignaciones_sin_hde     = 0;
            $total_asignaciones_factura     = 0;
            $total_asignaciones_sin_factura = 0;
            $total_asignaciones_pendiente_facturar = 0;
            $sum_total_asg_odc              = 0;
            $cont_mes                       =0;
            $html='';
            $html2='';
            $html3='';
            foreach($total_periodo as $key_periodo => $periodo){
                $asignaciones_odc               +=count($periodo['asignaciones_odc']);
                $asignaciones_sin_odc           +=count($periodo['asignaciones_sin_odc']);
                $total_asg_odc                  += $asignaciones_odc+ $asignaciones_sin_odc;
                $sum_total_asg_odc              +=$total_asg_odc;
                $asignaciones_hde               +=count($periodo['asignaciones_hde']);
                $asignaciones_sin_hde           +=count($periodo['asignaciones_sin_hde']);
                $total_asg_hde                  += $asignaciones_hde+ $asignaciones_sin_hde;
                $asignaciones_factura           +=count($periodo['asignaciones_factura']);
                $asignaciones_sin_factura       +=count($periodo['asignaciones_sin_factura']);
                $asignaciones_pendientes_facturar +=count($periodo['asignaciones_pendientes_facturar']);
                $total_asg_factura              += $asignaciones_factura+ $asignaciones_sin_factura;
                $total_asignaciones_pendientes_facturar  += $asignaciones_pendientes_facturar;
                $total_asignaciones_odc         +=$asignaciones_odc;
                $total_asignaciones_sin_odc     +=$asignaciones_sin_odc;
                $total_asignaciones_hde         +=$asignaciones_hde;
                $total_asignaciones_sin_hde     +=$asignaciones_sin_hde;
                $total_asignaciones_factura     +=$asignaciones_factura;
                $total_asignaciones_sin_factura +=$asignaciones_sin_factura;

                $color = '';
                $odc_porcentaje = 0;
                $res_odc = floor(($periodo['odc_pagado']>0)?((float)$periodo['odc_pagado']/(float)$periodo['total_odc'])*100:0);
                if($res_odc<=49){
                    $color = 'red';
                }elseif($res_odc>49&&$res_odc<=99){
                    $color = 'yellow';
                }elseif($res_odc==100){
                    $color = '#84da84';
                }

                $html2.="<tr>";
                    $html2.="<td class='cliente td-width-periodo text-center'>".$arrayMeses[$key_periodo]."</td>";
                    $html2.="<td class='cliente td-width-total td-odc text-center'> ".$total_asg_odc."</td>";
                    $html2.="<td class='cliente td-width-led td-odc text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                    $html2.="<td class='cliente td-width-number td-odc text-right' >".number_format($periodo['total_odc'],2)."</td>";
                    $html2.="<td class='cliente td-width-total td-odc text-center' > ".$asignaciones_odc."</td>";
                    $html2.="<td class='cliente con-odc td-width-number td-odc text-right' data-valor=".$periodo['odc_pagado']." data-mes=".$key_periodo." >".number_format($periodo['odc_pagado'],2)."</td>";
                    $html2.="<td class='cliente td-width-total td-odc text-center' > ".$asignaciones_sin_odc."</td>";
                    $html2.="<td class='cliente sin-odc td-width-number td-odc text-right' data-valor=".$periodo['odc_faltante']."  data-mes=".$key_periodo.">".number_format($periodo['odc_faltante'],2)."</td>";

                $color = '';
                $odc_porcentaje = 0;
                $res_odc = floor(($periodo['hde_pagado']>0)?((float)$periodo['hde_pagado']/(float)$periodo['total_hde'])*100:0);
                if($res_odc<=49){
                    $color = 'red';
                }elseif($res_odc>49&&$res_odc<=99){
                    $color = 'yellow';
                }elseif($res_odc==100){
                    $color = '#84da84';
                }

                    $html2.="<td class='cliente td-width-total td-hde text-center'> ".$total_asg_hde."</td>";
                    $html2.="<td class='cliente td-width-led td-hde text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                    $html2.="<td class='cliente td-width-number td-hde text-right' >".number_format($periodo['total_hde'],2)."</td>";
                    $html2.="<td class='cliente td-width-total td-hde text-center' > ".$asignaciones_hde."</td>";
                    $html2.="<td class='cliente con-hde td-width-number td-hde text-right' data-valor=".$periodo['hde_pagado']."  data-mes=".$key_periodo.">".number_format($periodo['hde_pagado'],2)."</td>";
                    $html2.="<td class='cliente td-width-total td-hde text-center' > ".$asignaciones_sin_hde."</td>";
                    $html2.="<td class='cliente sin-hde td-width-number td-hde text-right' data-valor=".$periodo['hde_faltante']."  data-mes=".$key_periodo.">".number_format($periodo['hde_faltante'],2)."</td>";

                $color = '';
                $odc_porcentaje = 0;
                $res_odc = floor(($periodo['factura_pagado']>0)?((float)$periodo['factura_pagado']/(float)$periodo['total_factura'])*100:0);
                if($res_odc<=49){
                    $color = 'red';
                }elseif($res_odc>49&&$res_odc<=99){
                    $color = 'yellow';
                }elseif($res_odc==100){
                    $color = '#84da84';
                }
                    $html2.="<td class='cliente td-width-total td-factura text-center' > ".
                        ($asignaciones_pendientes_facturar!='0'?"<a href='javascript:void(0);' class='ajaxDetallePendienteFacturar'>":"").
                        $asignaciones_pendientes_facturar.
                        ($asignaciones_pendientes_facturar!='0'?"</a>":'').
                        "<input type='hidden' id='MES' value='".$key_periodo."' >".
                        /*"<input type='hidden' id='FK_CLIENTE' value='".$cliente[0]['PK_CLIENTE']."' >".
                        "<input type='hidden' id='FK_UNIDAD_NEGOCIO' value='".$value[$key]['PK_UNIDAD_NEGOCIO']."' >".*/
                        "<input type='hidden' id='ANIO' value='2016'>".
                    "</td>";
                    $html2.="<td class='cliente sin-factura td-width-number td-factura text-right' data-valor=".$periodo['pendiente_facturar']." data-mes=".$key_periodo.">".number_format($periodo['pendiente_facturar'],2)."</td>";
                    $html2.="<td class='cliente td-width-total td-factura text-center'> ".$total_asg_factura."</td>";
                    $html2.="<td class='cliente td-width-led td-factura text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                    $html2.="<td class='cliente td-width-number td-factura text-right' >".number_format($periodo['total_factura'],2)."</td>";
                    $html2.="<td class='cliente td-width-total td-factura text-center' > ".$asignaciones_factura."</td>";
                    $html2.="<td class='cliente con-factura td-width-number td-factura text-right' data-valor=".$periodo['factura_pagado']." data-mes=".$key_periodo.">".number_format($periodo['factura_pagado'],2)."</td>";
                    //$html2.="<td class='cliente td-width-total td-factura text-center' > ".$asignaciones_sin_factura."</td>";
                    $html2.="<td class='cliente td-width-total td-factura text-center' > ".
                        ($asignaciones_sin_factura!='0'?"<a href='javascript:void(0);' class='ajaxDetalleFactura'>":"").
                        $asignaciones_sin_factura.
                        ($asignaciones_sin_factura!='0'?"</a>":'').
                        "<input type='hidden' id='MES' value='".$key_periodo."' >".
                        /*"<input type='hidden' id='FK_CLIENTE' value='".$cliente[0]['PK_CLIENTE']."' >".
                        "<input type='hidden' id='FK_UNIDAD_NEGOCIO' value='".$value[$key]['PK_UNIDAD_NEGOCIO']."' >".*/
                        "<input type='hidden' id='ANIO' value='2016' >".
                    "</td>";
                    $html2.="<td class='cliente sin-factura td-width-number td-factura text-right' data-valor=".$periodo['factura_faltante']." data-mes=".$key_periodo.">".number_format($periodo['factura_faltante'],2)."</td>";
                    
                    $total_odc           +=$periodo['total_odc'];
                    $odc_pagado          +=$periodo['odc_pagado'];
                    $odc_faltante        +=$periodo['odc_faltante'];
                    $total_hde           +=$periodo['total_hde'];
                    $hde_pagado          +=$periodo['hde_pagado'];
                    $hde_faltante        +=$periodo['hde_faltante'];
                    $total_factura       +=$periodo['total_factura'];
                    $factura_pagado      +=$periodo['factura_pagado'];
                    $factura_faltante    +=$periodo['factura_faltante'];
                    $factura_pendiente   +=$periodo['pendiente_facturar'];
                    $g_total_asg_odc     +=$total_asg_odc;
                    $g_total_asg_hde     +=$total_asg_hde;
                    $g_total_asg_factura +=$total_asg_factura;
                    $g_total_asig_pendientes += $total_asignaciones_pendientes_facturar;

                    $total_asg_odc            =0;
                    $total_asg_hde            =0;
                    $total_asg_factura        =0;
                    //$total_asignaciones_pendientes_facturar = 0;
                    $asignaciones_odc         =0;
                    $asignaciones_sin_odc     =0;
                    $asignaciones_hde         =0;
                    $asignaciones_sin_hde     =0;
                    $asignaciones_factura     =0;
                    $asignaciones_sin_factura =0;
                    $asignaciones_pendientes_facturar = 0;

                $html2.="</tr>";
                $cont_mes++;
            }

            $color = '';
            $odc_porcentaje = 0;
            $res_odc = floor(($odc_pagado>0)?((float)$odc_pagado/(float)$total_odc)*100:0);
            if($res_odc<=49){
                $color = 'red';
            }elseif($res_odc>49&&$res_odc<=99){
                $color = 'yellow';
            }elseif($res_odc==100){
                $color = '#84da84';
            }
            $html3.="<tr class='font-bold full-row row-totales'>";
                $html3.="<td class='td-width-periodo text-center'>TOTALES</td>";
                // $html3.="<td class='td-odc text-center' > ".$g_total_asg_odc."</td>";
                $html3.="<td class='td-width-total td-odc text-center' >".round($sum_total_asg_odc/$cont_mes)." Pm</td>";
                $html3.="<td class='td-width-led td-odc text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                $html3.="<td class='td-width-number td-odc text-right'>$".number_format($total_odc,2)."</td>";
                // $html3.="<td class='td-width-total td-odc text-center' style='background-color: #84da84;'>".$total_asignaciones_odc."</td>";
                $html3.="<td class='td-width-total td-odc text-center' style='background-color: #84da84;'></td>";
                $html3.="<td class='td-width-number td-odc text-right' style='background-color: #84da84;' >$".number_format($odc_pagado,2)."</td>";
                $html3.="<td class='td-width-total td-odc text-center' style='background-color:rgb(249, 141, 141)' >".$total_asignaciones_sin_odc."</td>";
                $html3.="<td class='td-width-number td-odc text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($odc_faltante,2)."</td>";

            $color = '';
            $odc_porcentaje = 0;
            $res_odc = floor(($hde_pagado>0)?((float)$hde_pagado/(float)$total_hde)*100:0);
            if($res_odc<=49){
                $color = 'red';
            }elseif($res_odc>49&&$res_odc<=99){
                $color = 'yellow';
            }elseif($res_odc==100){
                $color = '#84da84';
            }
                // $html3.="<td class='td-hde text-center' > ".$g_total_asg_hde."</td>";
                $html3.="<td class='td-width-total td-hde text-center' > </td>";
                $html3.="<td class='td-width-led td-hde text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                $html3.="<td class='td-width-number td-hde text-right'>$".number_format($total_hde,2)."</td>";
                // $html3.="<td class='td-width-total td-hde text-center' style='background-color: #84da84;'>".$total_asignaciones_hde."</td>";
                $html3.="<td class='td-width-total td-hde text-center' style='background-color: #84da84;'></td>";
                $html3.="<td class='td-width-number td-hde text-right' style='background-color: #84da84;'>$".number_format($hde_pagado,2)."</td>";
                $html3.="<td class='td-width-total td-hde text-center' style='background-color:rgb(249, 141, 141)'>".$total_asignaciones_sin_hde."</td>";
                $html3.="<td class='td-width-number td-hde text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($hde_faltante,2)."</td>";

            $color = '';
            $odc_porcentaje = 0;
            $res_odc = floor(($factura_pagado>0)?((float)$factura_pagado/(float)$total_factura)*100:0);
            if($res_odc<=49){
                $color = 'red';
            }elseif($res_odc>49&&$res_odc<=99){
                $color = 'yellow';
            }elseif($res_odc==100){
                $color = '#84da84';
            }
                // $html3.="<td class='td-factura text-center' > ".$g_total_asg_factura."</td>";
                $html3.="<td class=' td-width-total td-factura text-center' style='background-color:orange'>".$total_asignaciones_pendientes_facturar."</td>";
                $html3.="<td class=' td-width-number td-factura text-right' style='background-color:orange'>$".number_format($factura_pendiente,2)."</td>";
                $html3.="<td class=' td-width-total td-factura text-center' > </td>";
                $html3.="<td class=' td-width-led td-factura text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                $html3.="<td class=' td-width-number td-factura text-right'>$".number_format($total_factura,2)."</td>";
                // $html3.="<td class=' td-width-total td-factura text-center' style='background-color: #84da84;'>".$total_asignaciones_factura."</td>";
                $html3.="<td class=' td-width-total td-factura text-center' style='background-color: #84da84;'></td>";
                $html3.="<td class=' td-width-number td-factura text-right' style='background-color: #84da84;'>$".number_format($factura_pagado,2)."</td>";
                $html3.="<td class=' td-width-total td-factura text-center' style='background-color:rgb(249, 141, 141)'>".$total_asignaciones_sin_factura."</td>";
                $html3.="<td class=' td-width-number td-factura text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($factura_faltante,2)."</td>";
                
            $html3.="</tr>";
            $html.=$html3.$html2;
            $datos = 'tub';
            //$html = '<tr><td>Si me ejecute</td></tr>';
            $res = [
                'post'=>$post,
                'data'=>$datos,
                'html'=>$html,
            ];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $res;
        }
    }

    public function actionIndex_proyeccion_pagos()
    {
        $request = Yii::$app->request;
        $tamanio_pagina= 20;
        $anio_actual=date('Y');
        $mes_actual=date('n');
        $arrayMeses = array(
            1 => "Enero",
            2 => "Febrero",
            3 => "Marzo",
            4 => "Abril",
            5 => "Mayo",
            6 => "Junio",
            7 => "Julio",
            8 => "Agosto",
            9 => "Septiembre",
            10 => "Octubre",
            11 => "Noviembre",
            12 => "Diciembre", );
        $datosTipoDocumento = ArrayHelper::map(TblCatTipoDocumentos::find()->select(['PK_TIPO_DOCUMENTO','DESC_TIPO_DOCUMENTO'])->where(['not in','PK_TIPO_DOCUMENTO',[1,5]])->asArray()->all(),'PK_TIPO_DOCUMENTO','DESC_TIPO_DOCUMENTO');
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
            $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }else{
            $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }
        $fk_estatus_asignacion = [2,3,4];
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            $datos=[];
            parse_str($data['data'],$post);
            if(isset($post['FK_UNIDAD_NEGOCIO']) && !empty($post['FK_UNIDAD_NEGOCIO'])){
                $unidades_negocio = $post['FK_UNIDAD_NEGOCIO'];
            }elseif((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->asArray()->all();
                $cadenaUni_neg='';
                foreach($unidades_negocio as $arrayUnidadesNeg){
                    $cadenaUni_neg .= $arrayUnidadesNeg['PK_UNIDAD_NEGOCIO'].',';
                }
                $unidades_negocio = trim($cadenaUni_neg,',');
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $cadenaUni_neg='';
                foreach($unidadesNegocioValidas as $valor){
                    $cadenaUni_neg .= $valor.',';
                }
                $unidades_negocio = trim($cadenaUni_neg,',');
            }else{
                $unidades_negocio = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            }
            $FK_CLIENTE =(!empty($post['FK_CLIENTE']))? ("'".trim($post['FK_CLIENTE'])."'"):'null';

            $connection = \Yii::$app->db;
            $datos = $connection->createCommand("CALL SP_REPORTES_INDEX_PROYECCION_PAGOS('$unidades_negocio',$FK_CLIENTE,$anio_actual)")->queryAll();
            $unidadNegocioArray = [];
            $html='';

            if(count($datos) > 0){
                foreach($datos as $arrayDatos){
                    $arrayUnidadNegocio = array(
                                            'FK_UNIDAD_NEGOCIO'=>$arrayDatos['FK_UNIDAD_NEGOCIO'], 
                                            'DESC_UNIDAD_NEGOCIO'=>$arrayDatos['DESC_UNIDAD_NEGOCIO'],
                                            'FK_CLIENTE'=>$arrayDatos['FK_CLIENTE'], 
                                            'NOMBRE_CLIENTE'=>$arrayDatos['NOMBRE_CLIENTE'],
                                            );
                    if(!in_array($arrayUnidadNegocio, $unidadNegocioArray)){
                        $unidadNegocioArray[] = $arrayUnidadNegocio;
                    }
                }
                $unidadNegocioAnterior='';
                
                foreach($unidadNegocioArray as $arrayUN){
                    $html2 = '';
                    $htmlUnidadNegocio = '';
                    if($arrayUN['FK_UNIDAD_NEGOCIO'] != $unidadNegocioAnterior){
                        $htmlUnidadNegocio = "<tr style='background-color:#a4a4a4; width: 960px;'><td style='border: 0px !important;' class='font-bold full-row' colspan='10' >".$arrayUN['DESC_UNIDAD_NEGOCIO']."</td></tr>";
                    }
                    $htmlCliente = "<tr style='background-color:#0F1F50; width: 960px;'><td style='color:#fff;border: 0px !important;' class='font-bold full-row' colspan='10'>".$arrayUN['NOMBRE_CLIENTE']."</td></tr>";
                    $unidadNegocioAnterior = $arrayUN['FK_UNIDAD_NEGOCIO'];
                    $cantODC=0;
                    $cantHDE=0;
                    $cantFACT=0;
                    $cantPAGO=0;
                    $promODC=0;
                    $promHDE=0;
                    $promFACT=0;
                    $promPAGO=0;
                    for ($i=1; $i <=$mes_actual ; $i++) { 
                        $registroEncontrado = false;
                        foreach($datos as $arrayDatos){
                            if($arrayUN['FK_UNIDAD_NEGOCIO']==$arrayDatos['FK_UNIDAD_NEGOCIO'] && $arrayUN['FK_CLIENTE']==$arrayDatos['FK_CLIENTE'] && $i==$arrayDatos['MES_PERIODO_NUM']){
                                $registroEncontrado = true;
                                $arrayMes = $arrayDatos;
                                break;
                            }
                        }
                        if($registroEncontrado){
                            $html2.='<tr>';
                                $html2.='<td class="td-width-periodo text-center">'.$arrayMes['MES_PERIODO'].'</td>';
                                $html2.='<td class="td-width-periodo text-center">'.$arrayMes['TOTAL_ASIGNACIONES'].'</td>';
                                $html2.='<td class="td-width-periodo text-center caja-border-left">'.($arrayMes['CANT_INI_VS_ODC']>0?('<a href="#" onclick="ajaxObtenerDetalle(1,'.$arrayMes['FK_UNIDAD_NEGOCIO'].','.$arrayMes['FK_CLIENTE'].','.$arrayMes['MES_PERIODO_NUM'].','.$arrayMes['ANIO'].');" >'.$arrayMes['CANT_INI_VS_ODC'].'</a>'):$arrayMes['CANT_INI_VS_ODC']).'</td>';
                                $html2.='<td class="td-width-periodo text-center">'.$arrayMes['PROM_INI_VS_ODC'].'</td>';
                                $html2.='<td class="td-width-periodo text-center caja-border-left">'.($arrayMes['CANT_ODC_VS_HDE']>0?('<a href="#" onclick="ajaxObtenerDetalle(2,'.$arrayMes['FK_UNIDAD_NEGOCIO'].','.$arrayMes['FK_CLIENTE'].','.$arrayMes['MES_PERIODO_NUM'].','.$arrayMes['ANIO'].');" >'.$arrayMes['CANT_ODC_VS_HDE'].'</a>'):$arrayMes['CANT_ODC_VS_HDE']).'</td>';
                                $html2.='<td class="td-width-periodo text-center">'.$arrayMes['PROM_ODC_VS_HDE'].'</td>';
                                $html2.='<td class="td-width-periodo text-center caja-border-left">'.($arrayMes['CANT_HDE_VS_FAC']>0?('<a href="#" onclick="ajaxObtenerDetalle(3,'.$arrayMes['FK_UNIDAD_NEGOCIO'].','.$arrayMes['FK_CLIENTE'].','.$arrayMes['MES_PERIODO_NUM'].','.$arrayMes['ANIO'].');" >'.$arrayMes['CANT_HDE_VS_FAC'].'</a>'):$arrayMes['CANT_HDE_VS_FAC']).'</td>';
                                $html2.='<td class="td-width-periodo text-center">'.$arrayMes['PROM_HDE_VS_FAC'].'</td>';
                                $html2.='<td class="td-width-periodo text-center caja-border-left">'.($arrayMes['CANT_FAC_VS_PAGO']>0?('<a href="#" onclick="ajaxObtenerDetalle(4,'.$arrayMes['FK_UNIDAD_NEGOCIO'].','.$arrayMes['FK_CLIENTE'].','.$arrayMes['MES_PERIODO_NUM'].','.$arrayMes['ANIO'].');" >'.$arrayMes['CANT_FAC_VS_PAGO'].'</a>'):$arrayMes['CANT_FAC_VS_PAGO']).'</td>';
                                $html2.='<td class="td-width-periodo text-center">'.$arrayMes['PROM_FAC_VS_PAGO'].'</td>';
                                
                                if($arrayMes['CANT_INI_VS_ODC']>0){
                                    $cantODC++;
                                    $promODC+=$arrayMes['PROM_INI_VS_ODC'];
                                }
                                if($arrayMes['CANT_ODC_VS_HDE']>0){
                                    $cantHDE++;
                                    $promHDE+=$arrayMes['PROM_ODC_VS_HDE'];
                                }
                                if($arrayMes['CANT_HDE_VS_FAC']>0){
                                    $cantFACT++;
                                    $promFACT+=$arrayMes['PROM_HDE_VS_FAC'];
                                }
                                if($arrayMes['CANT_FAC_VS_PAGO']>0){
                                    $cantPAGO++;
                                    $promPAGO+=$arrayMes['PROM_FAC_VS_PAGO'];
                                }
                            $html2.='</tr>';
                        } else {
                            $html2.='<tr>';
                                $html2.='<td class="td-width-periodo text-center">'.$arrayMeses[$i].'</td>';
                                $html2.='<td class="td-width-periodo text-center">0</td>';
                                $html2.='<td class="td-width-periodo text-center caja-border-left">0</td>';
                                $html2.='<td class="td-width-periodo text-center">0</td>';
                                $html2.='<td class="td-width-periodo text-center caja-border-left">0</td>';
                                $html2.='<td class="td-width-periodo text-center">0</td>';
                                $html2.='<td class="td-width-periodo text-center caja-border-left">0</td>';
                                $html2.='<td class="td-width-periodo text-center">0</td>';
                                $html2.='<td class="td-width-periodo text-center caja-border-left">0</td>';
                                $html2.='<td class="td-width-periodo text-center">0</td>';
                            $html2.='</tr>';
                        }
                    }
                    $htmlTotales='<tr class="font-bold full-row row-totales">'.
                                    '<td class="td-width-periodo text-center">TOTALES</td>'.
                                    '<td class="td-width-periodo text-center"></td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-verde">-</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-verde">'.round($cantODC>0?($promODC/$cantODC):'0').'</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-rojo">-</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-rojo">'.round($cantHDE>0?($promHDE/$cantHDE):'0').'</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-naranja">-</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-naranja">'.round($cantFACT>0?($promFACT/$cantFACT):'0').'</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-celeste">-</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-celeste">'.round($cantPAGO>0?($promPAGO/$cantPAGO):'0').'</td>'.
                                '</tr>';
                                ;
                    $html .= $htmlUnidadNegocio.$htmlCliente.$htmlTotales.$html2;
                }
            }
            $connection->close();
            $res = [
                'post'=>$post,
                'data'=>$datos,
                'html'=>$html,
            ];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $res;
        }else{
            user_log_bitacora('Consulta: Reporte de Proyeccion de Pagos Asignaciones','Consulta: Reporte de Proyeccion de Pagos Asignaciones','0');
            return $this->render('index_proyeccion_pagos', [
                'total_paginas' => 0,
                'unidadNegocio' => $unidadNegocio,
                'datosTipoDocumento' => $datosTipoDocumento,
            ]);
            
        }
    }

    public function actionIndex_proyeccion_saldos()
    {
        $request = Yii::$app->request;
        $tamanio_pagina= 20;
        $anio_actual=date('Y');
        $mes_actual=date('n');
        $arrayMeses = array(
            1 => "Enero",
            2 => "Febrero",
            3 => "Marzo",
            4 => "Abril",
            5 => "Mayo",
            6 => "Junio",
            7 => "Julio",
            8 => "Agosto",
            9 => "Septiembre",
            10 => "Octubre",
            11 => "Noviembre",
            12 => "Diciembre", );
        $datosTipoDocumento = ArrayHelper::map(TblCatTipoDocumentos::find()->select(['PK_TIPO_DOCUMENTO','DESC_TIPO_DOCUMENTO'])->where(['not in','PK_TIPO_DOCUMENTO',[1,5]])->asArray()->all(),'PK_TIPO_DOCUMENTO','DESC_TIPO_DOCUMENTO');
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
            $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }else{
            $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }
        $fk_estatus_asignacion = [2,3,4];
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            $datos=[];
            parse_str($data['data'],$post);
            if(isset($post['FK_UNIDAD_NEGOCIO']) && !empty($post['FK_UNIDAD_NEGOCIO'])){
                $unidades_negocio = $post['FK_UNIDAD_NEGOCIO'];
            }elseif((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->asArray()->all();
                $cadenaUni_neg='';
                foreach($unidades_negocio as $arrayUnidadesNeg){
                    $cadenaUni_neg .= $arrayUnidadesNeg['PK_UNIDAD_NEGOCIO'].',';
                }
                $unidades_negocio = trim($cadenaUni_neg,',');
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $cadenaUni_neg='';
                foreach($unidadesNegocioValidas as $valor){
                    $cadenaUni_neg .= $valor.',';
                }
                $unidades_negocio = trim($cadenaUni_neg,',');
            }else{
                $unidades_negocio = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            }
            $FK_CLIENTE =(!empty($post['FK_CLIENTE']))? ("'".trim($post['FK_CLIENTE'])."'"):'null';

            $connection = \Yii::$app->db;
            $datos = $connection->createCommand("CALL SP_REPORTES_INDEX_PROYECCION_SALDOS('$unidades_negocio',$FK_CLIENTE,$anio_actual)")->queryAll();
            $unidadNegocioArray = [];
            $html='';
            if(count($datos) > 0){
                foreach($datos as $arrayDatos){
                    $arrayUnidadNegocio = array(
                                            'FK_UNIDAD_NEGOCIO'=>$arrayDatos['FK_UNIDAD_NEGOCIO'], 
                                            'DESC_UNIDAD_NEGOCIO'=>$arrayDatos['DESC_UNIDAD_NEGOCIO'],
                                            'FK_CLIENTE'=>$arrayDatos['FK_CLIENTE'], 
                                            'NOMBRE_CLIENTE'=>$arrayDatos['NOMBRE_CLIENTE'],
                                            );
                    if(!in_array($arrayUnidadNegocio, $unidadNegocioArray)){
                        $unidadNegocioArray[] = $arrayUnidadNegocio;
                    }
                }
                $unidadNegocioAnterior='';
                
                foreach($unidadNegocioArray as $arrayUN){
                    $html2 = '';
                    $htmlUnidadNegocio = '';
                    if($arrayUN['FK_UNIDAD_NEGOCIO'] != $unidadNegocioAnterior){
                        $htmlUnidadNegocio = "<tr style='background-color:#a4a4a4; width: 960px;'><td style='border: 0px !important;' class='font-bold full-row' colspan='10' >".$arrayUN['DESC_UNIDAD_NEGOCIO']."</td></tr>";
                    }
                    $htmlCliente = "<tr style='background-color:#0F1F50; width: 960px;'><td style='color:#fff;border: 0px !important;' class='font-bold full-row' colspan='10'>".$arrayUN['NOMBRE_CLIENTE']."</td></tr>";
                    $unidadNegocioAnterior = $arrayUN['FK_UNIDAD_NEGOCIO'];
                    $arrayMes = '';
                    $arrayMesProyecto = '';
                    
                    $numFACT = 0;
                    $numPROY = 0;
                    $totNUM = 0;
                    $cantFACT = 0;
                    $cantPROY = 0;
                    
                    $promFACT = 0;
                    $promPROY = 0;
                    $totPROM = 0;
                    $promCantFACT = 0;
                    $promCantPROY = 0;

                    for ($i=1; $i <=$mes_actual ; $i++) { 
                        $registroEncontrado = false;
                        foreach($datos as $arrayDatos){
                            if($arrayUN['FK_UNIDAD_NEGOCIO']==$arrayDatos['FK_UNIDAD_NEGOCIO'] && $arrayUN['FK_CLIENTE']==$arrayDatos['FK_CLIENTE'] && $i==$arrayDatos['MES_PERIODO_NUM'] && $arrayDatos['PK_PERIODO']){
                                $registroEncontrado = true;
                                $arrayMes = $arrayDatos;
                                break;
                            }
                        }
                        foreach($datos as $arrayDatos){
                            if($arrayUN['FK_UNIDAD_NEGOCIO']==$arrayDatos['FK_UNIDAD_NEGOCIO'] && $arrayUN['FK_CLIENTE']==$arrayDatos['FK_CLIENTE'] && $i==$arrayDatos['MES_PERIODO_NUM'] && $arrayDatos['PK_PROYECTO_PERIODO']){
                                $registroEncontrado = true;
                                $arrayMesProyecto = $arrayDatos;
                                break;
                            }
                        }
                        if($registroEncontrado){
                            $html2.='<tr>';
                            $numFACT = ($arrayMes != '' ? $arrayMes['TOTAL_FACTURAS'] : 0);
                            $numPROY = ($arrayMesProyecto != '' ? $arrayMesProyecto['TOTAL_FACTURAS'] : 0);
                            $totNUM = $numFACT + $numPROY;
                            $cantFACT += $numFACT;
                            $cantPROY += $numPROY;
                            $totCantNum = $cantFACT + $cantPROY;

                            $promFACT = ($arrayMes != '' ? $arrayMes['TOTAL_FACTURABLE'] : 0);
                            $promPROY = ($arrayMesProyecto != '' ? $arrayMesProyecto['TOTAL_FACTURABLE'] : 0);
                            $totPROM = $promFACT + $promPROY;
                            $promCantFACT += $promFACT;
                            $promCantPROY += $promPROY;
                            $totPromCant = $promCantFACT + $promCantPROY; 

                                $html2.='<td class="td-width-periodo text-center">'.$arrayMes['MES_PERIODO'].'</td>';
                                $html2.='<td class="td-width-periodo text-center">'.$totNUM.'</td>';
                                $html2.='<td class="td-width-periodo text-center">'."$ ".number_format((float)$totPROM,2).'</td>';
                                
                                if($arrayMes != ''){
                                    $html2.='<td class="td-width-periodo text-center caja-border-left">'.($arrayMes['TOTAL_FACTURAS']>0?('<a href="#" onclick="ajaxObtenerDetalle(5,'.$arrayMes['FK_UNIDAD_NEGOCIO'].','.$arrayMes['FK_CLIENTE'].','.$arrayMes['MES_PERIODO_NUM'].','.$arrayMes['ANIO'].');" >'.$arrayMes['TOTAL_FACTURAS'].'</a>'):0).'</td>';
                                    $html2.='<td class="td-width-periodo text-center">'."$ ".number_format((float)$arrayMes['TOTAL_FACTURABLE'],2).'</td>';
                                }else{
                                    $html2.='<td class="td-width-periodo text-center caja-border-left">'.(0).'</td>';
                                    $html2.='<td class="td-width-periodo text-center">'."$ 0.00".'</td>';
                                }
                                if($arrayMesProyecto != ''){
                                    $html2.='<td class="td-width-periodo text-center caja-border-left">'.($arrayMesProyecto['TOTAL_FACTURAS']>0?('<a href="#" onclick="ajaxObtenerDetalle(6,'.$arrayMesProyecto['FK_UNIDAD_NEGOCIO'].','.$arrayMesProyecto['FK_CLIENTE'].','.$arrayMesProyecto['MES_PERIODO_NUM'].','.$arrayMesProyecto['ANIO'].');" >'.$arrayMesProyecto['TOTAL_FACTURAS'].'</a>'):0).'</td>';
                                    $html2.='<td class="td-width-periodo text-center">'."$ ".number_format((float)$arrayMesProyecto['TOTAL_FACTURABLE'],2).'</td>';
                                }else{
                                    $html2.='<td class="td-width-periodo text-center caja-border-left">'.(0).'</td>';
                                    $html2.='<td class="td-width-periodo text-center">'."$ 0.00".'</td>';
                                }
                               
                            $html2.='</tr>';
                        } else {
                            $html2.='<tr>';
                                $html2.='<td class="td-width-periodo text-center">'.$arrayMeses[$i].'</td>';
                                $html2.='<td class="td-width-periodo text-center">'."$ 0.00".'</td>';
                                $html2.='<td class="td-width-periodo text-center">0</td>';
                                $html2.='<td class="td-width-periodo text-center caja-border-left">0</td>';
                                $html2.='<td class="td-width-periodo text-center">'."$ 0.00".'</td>';
                                $html2.='<td class="td-width-periodo text-center caja-border-left">0</td>';
                                $html2.='<td class="td-width-periodo text-center">'."$ 0.00".'</td>';
                            $html2.='</tr>';
                        }
                    }
                    $htmlTotales='<tr class="font-bold full-row row-totales">'.
                                    '<td class="td-width-periodo text-center">TOTALES</td>'.
                                    '<td class="td-width-periodo text-center">'.$totCantNum.'</td>'.
                                    '<td class="td-width-periodo text-center">'."$ ".number_format((float)$totPromCant,2).'</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-verde">'.$cantFACT.'</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-verde">'."$ ".number_format((float)$promCantFACT,2).'</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-naranja">'.$cantPROY.'</td>'.
                                    '<td class="td-width-periodo text-center fondo-claro-naranja">'."$ ".number_format((float)$promCantPROY,2).'</td>'.
                                '</tr>';
                                ;
                    $html .= $htmlUnidadNegocio.$htmlCliente.$htmlTotales.$html2;
                }
            }
            $connection->close();
            $res = [
                'post'=>$post,
                'data'=>$datos,
                'html'=>$html,
            ];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $res;
        }else{
            user_log_bitacora('Consulta: Reporte de Proyeccion de Saldos','Consulta: Reporte de Proyeccion de Saldos','0');
            return $this->render('index_proyeccion_saldos', [
                'total_paginas' => 0,
                'unidadNegocio' => $unidadNegocio,
                'datosTipoDocumento' => $datosTipoDocumento,
            ]);
            
        }
    }

    public function actionIndex_movimientos(){

        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
            $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }else{
            $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }
        $datosUbicacionFisica = ArrayHelper::map(TblCatUbicaciones::find()->where(['=','PROPIA_CLIENTE','Propia'])->asArray()->all(), 'PK_UBICACION', 'DESC_UBICACION');
        $datosRazonSocial = ArrayHelper::map(TblCatRazonSocial::find()->asArray()->all(), 'PK_RAZON_SOCIAL', 'DESC_RAZON_SOCIAL');
        if (Yii::$app->request->isAjax) {
            //Se recogen los parametros del post
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $FK_UNIDAD_NEGOCIO =(!empty($post['FK_UNIDAD_NEGOCIO']))? ("'".trim($post['FK_UNIDAD_NEGOCIO'])."'"):'null';
            $FK_UBICACION_FISICA =(!empty($post['FK_UBICACION_FISICA']))? ("'".trim($post['FK_UBICACION_FISICA'])."'"):'null';
            $FK_RAZON_SOCIAL =(!empty($post['FK_RAZON_SOCIAL']))? ("'".trim($post['FK_RAZON_SOCIAL'])."'"):'null';
            $FECHA_INI =(!empty($post['ingresoFechaIni']))? ("'".transform_date(trim($post['ingresoFechaIni']),'Y-m-d')."'"):'null';
            $FECHA_FIN =(!empty($post['ingresoFechaFin']))? ("'".transform_date(trim($post['ingresoFechaFin']),'Y-m-d')."'"):'null';

            //Se crea variable de conexion
            $connection = \Yii::$app->db;

            //1.- Se ejecuta el stored procedure que contiene la informacion de las FECHAS DE INGRESO de los empleados
            $tablaIngresos = '';
            $tablaDetalleIngresos = '';
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_MOVIMIENTOS_INGRESOS($FK_UNIDAD_NEGOCIO,$FK_UBICACION_FISICA,$FK_RAZON_SOCIAL,$FECHA_INI,$FECHA_FIN)")->queryAll();
            $cantRegistrosIngreso = count($total_registros);
            if($cantRegistrosIngreso > 0){
                $i=0;
                foreach($total_registros as $array){
                    $i++;
                    //En la pantalla Principal solo se deben de mostrar cuando mucho 12 registros por apartado
                    if($i<=12){
                        $tablaIngresos.='<tr>';
                            $tablaIngresos.='<td><div style="background:url(../..'.$array['FOTO_EMP'].');height: 80px;width: 80px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div></td>';
                            $tablaIngresos.='<td>'.$array['NOMBRE'].'</td>';
                            $tablaIngresos.='<td>'.$array['DESC_PUESTO'].'</td>';
                            $tablaIngresos.='<td>$ '.number_format($array['SUELDO_NETO'],2,'.',',').'</td>';
                            $tablaIngresos.='<td>'.$array['DESC_UNIDAD_NEGOCIO'].'</td>';
                            $tablaIngresos.='<td>'.$array['NOMBRE_ADMINISTRADORA'].'</td>';
                            $tablaIngresos.='<td>'.$array['DESC_TIPO_CONTRATO'].'</td>';
                            $tablaIngresos.='<td>'.$array['DESC_DURACION'].'</td>';
                            $tablaIngresos.='<td>'.transform_date($array['FECHA_INGRESO'],'d/m/Y').'</td>';
                            $tablaIngresos.='<td>'.$array['USUARIO'].'</td>';
                        $tablaIngresos.='</tr>';
                    }
                    $tablaDetalleIngresos.='<tr>';
                        $tablaDetalleIngresos.='<td><div style="background:url(../..'.$array['FOTO_EMP'].');height: 80px;width: 80px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div></td>';
                        $tablaDetalleIngresos.='<td>'.$array['NOMBRE'].'</td>';
                        $tablaDetalleIngresos.='<td>'.$array['DESC_PUESTO'].'</td>';
                        $tablaDetalleIngresos.='<td>$ '.number_format($array['SUELDO_NETO'],2,'.',',').'</td>';
                        $tablaDetalleIngresos.='<td>'.$array['DESC_UNIDAD_NEGOCIO'].'</td>';
                        $tablaDetalleIngresos.='<td>'.$array['NOMBRE_ADMINISTRADORA'].'</td>';
                        $tablaDetalleIngresos.='<td>'.$array['DESC_TIPO_CONTRATO'].'</td>';
                        $tablaDetalleIngresos.='<td>'.$array['DESC_DURACION'].'</td>';
                        $tablaDetalleIngresos.='<td>'.transform_date($array['FECHA_INGRESO'],'d/m/Y').'</td>';
                        $tablaDetalleIngresos.='<td>'.$array['USUARIO'].'</td>';
                    $tablaDetalleIngresos.='</tr>';
                }
            } else {
                $tablaIngresos = "<tr><td colspan='10' class='td-no-results'>NO EXISTEN ELEMENTOS QUE CUENTEN CON LOS CRITERIOS ESPECIFICADOS PARA LOS PARÁMETROS DE BÚSQUEDA</td></tr>";
                $tablaDetalleIngresos = "<tr><td colspan='10' class='td-no-results'>NO EXISTEN ELEMENTOS QUE CUENTEN CON LOS CRITERIOS ESPECIFICADOS PARA LOS PARÁMETROS DE BÚSQUEDA</td></tr>";
            }
            
            //2.- Se ejecuta el stored procedure que contiene la informacion de las ESTATUS de los empleados
            $tablaEstatus = '';
            $tablaDetalleEstatus = '';
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_MOVIMIENTOS_ESTATUS($FK_UNIDAD_NEGOCIO,$FK_UBICACION_FISICA,$FK_RAZON_SOCIAL,$FECHA_INI,$FECHA_FIN)")->queryAll();
            $cantRegistrosEstatus = count($total_registros);
            if($cantRegistrosEstatus > 0){
                $i=0;
                foreach($total_registros as $array){
                    $i++;
                    //En la pantalla Principal solo se deben de mostrar cuando mucho 12 registros por apartado
                    if($i<=12){
                        $tablaEstatus.='<tr>';
                            $tablaEstatus.='<td><div style="background:url(../..'.$array['FOTO_EMP'].');height: 80px;width: 80px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div></td>';
                            $tablaEstatus.='<td>'.$array['NOMBRE'].'</td>';
                            $tablaEstatus.='<td>'.$array['DESC_PUESTO'].'</td>';
                            $tablaEstatus.='<td>'.$array['DESC_UNIDAD_NEGOCIO'].'</td>';
                            $tablaEstatus.='<td>'.transform_date($array['FECHA_INGRESO'],'d/m/Y').'</td>';
                            $tablaEstatus.='<td>'.$array['ESTATUS_ANTERIOR'].'</td>';
                            $tablaEstatus.='<td>'.transform_date($array['FECHA_CAMBIO'],'d/m/Y').'</td>';
                            $tablaEstatus.='<td>'.$array['ESTATUS_ACTUAL'].'</td>';
                            $tablaEstatus.='<td>'.$array['USUARIO'].'</td>';
                        $tablaEstatus.='</tr>';
                    }
                    $tablaDetalleEstatus.='<tr>';
                        $tablaDetalleEstatus.='<td><div style="background:url(../..'.$array['FOTO_EMP'].');height: 80px;width: 80px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div></td>';
                        $tablaDetalleEstatus.='<td>'.$array['NOMBRE'].'</td>';
                        $tablaDetalleEstatus.='<td>'.$array['DESC_PUESTO'].'</td>';
                        $tablaDetalleEstatus.='<td>'.$array['DESC_UNIDAD_NEGOCIO'].'</td>';
                        $tablaDetalleEstatus.='<td>'.transform_date($array['FECHA_INGRESO'],'d/m/Y').'</td>';
                        $tablaDetalleEstatus.='<td>'.$array['ESTATUS_ANTERIOR'].'</td>';
                        $tablaDetalleEstatus.='<td>'.transform_date($array['FECHA_CAMBIO'],'d/m/Y').'</td>';
                        $tablaDetalleEstatus.='<td>'.$array['ESTATUS_ACTUAL'].'</td>';
                        $tablaDetalleEstatus.='<td>'.$array['USUARIO'].'</td>';
                    $tablaDetalleEstatus.='</tr>';
                }
            } else {
                $tablaEstatus = "<tr><td colspan='9' class='td-no-results'>NO EXISTEN ELEMENTOS QUE CUENTEN CON LOS CRITERIOS ESPECIFICADOS PARA LOS PARÁMETROS DE BÚSQUEDA</td></tr>";
                $tablaDetalleEstatus = "<tr><td colspan='9' class='td-no-results'>NO EXISTEN ELEMENTOS QUE CUENTEN CON LOS CRITERIOS ESPECIFICADOS PARA LOS PARÁMETROS DE BÚSQUEDA</td></tr>";
            }

            //3.- Se ejecuta el stored procedure que contiene la informacion de las UNIDADES DE NEGOCIO de los empleados
            $tablaUnidadNegocio = '';
            $tablaDetalleUnidadNegocio = '';
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_MOVIMIENTOS_UNIDAD_NEGOCIO($FK_UNIDAD_NEGOCIO,$FK_UBICACION_FISICA,$FK_RAZON_SOCIAL,$FECHA_INI,$FECHA_FIN)")->queryAll();
            $cantRegistrosUnidadNegocio = count($total_registros);
            if($cantRegistrosUnidadNegocio > 0){
                $i=0;
                foreach($total_registros as $array){
                    $i++;
                    //En la pantalla Principal solo se deben de mostrar cuando mucho 12 registros por apartado
                    if($i<=12){
                        $tablaUnidadNegocio.='<tr>';
                            $tablaUnidadNegocio.='<td><div style="background:url(../..'.$array['FOTO_EMP'].');height: 80px;width: 80px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div></td>';
                            $tablaUnidadNegocio.='<td>'.$array['NOMBRE'].'</td>';
                            $tablaUnidadNegocio.='<td>'.$array['DESC_PUESTO'].'</td>';
                            $tablaUnidadNegocio.='<td>'.$array['UNIDAD_NEGOCIO_ANTERIOR'].'</td>';
                            $tablaUnidadNegocio.='<td>'.$array['UNIDAD_NEGOCIO_ACTUAL'].'</td>';
                            $tablaUnidadNegocio.='<td>'.$array['DESC_UBICACION'].'</td>';
                            $tablaUnidadNegocio.='<td>'.transform_date($array['FECHA_CAMBIO'],'d/m/Y').'</td>';
                            $tablaUnidadNegocio.='<td>'.$array['USUARIO'].'</td>';
                        $tablaUnidadNegocio.='</tr>';
                    }
                    $tablaDetalleUnidadNegocio.='<tr>';
                        $tablaDetalleUnidadNegocio.='<td><div style="background:url(../..'.$array['FOTO_EMP'].');height: 80px;width: 80px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div></td>';
                        $tablaDetalleUnidadNegocio.='<td>'.$array['NOMBRE'].'</td>';
                        $tablaDetalleUnidadNegocio.='<td>'.$array['DESC_PUESTO'].'</td>';
                        $tablaDetalleUnidadNegocio.='<td>'.$array['UNIDAD_NEGOCIO_ANTERIOR'].'</td>';
                        $tablaDetalleUnidadNegocio.='<td>'.$array['UNIDAD_NEGOCIO_ACTUAL'].'</td>';
                        $tablaDetalleUnidadNegocio.='<td>'.$array['DESC_UBICACION'].'</td>';
                        $tablaDetalleUnidadNegocio.='<td>'.transform_date($array['FECHA_CAMBIO'],'d/m/Y').'</td>';
                        $tablaDetalleUnidadNegocio.='<td>'.$array['USUARIO'].'</td>';
                    $tablaDetalleUnidadNegocio.='</tr>';
                }
            } else {
                $tablaUnidadNegocio = "<tr><td colspan='8' class='td-no-results'>NO EXISTEN ELEMENTOS QUE CUENTEN CON LOS CRITERIOS ESPECIFICADOS PARA LOS PARÁMETROS DE BÚSQUEDA</td></tr>";
                $tablaDetalleUnidadNegocio = "<tr><td colspan='8' class='td-no-results'>NO EXISTEN ELEMENTOS QUE CUENTEN CON LOS CRITERIOS ESPECIFICADOS PARA LOS PARÁMETROS DE BÚSQUEDA</td></tr>";
            }

            //4.- Se ejecuta el stored procedure que contiene la informacion de las BAJAS de los empleados
            $tablaBajas = '';
            $tablaDetalleBajas = '';
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_MOVIMIENTOS_BAJAS($FK_UNIDAD_NEGOCIO,$FK_UBICACION_FISICA,$FK_RAZON_SOCIAL,$FECHA_INI,$FECHA_FIN)")->queryAll();
            $cantRegistrosBajas = count($total_registros);
            if($cantRegistrosBajas > 0){
                $i=0;
                foreach($total_registros as $array){
                    $i++;
                    //En la pantalla Principal solo se deben de mostrar cuando mucho 12 registros por apartado
                    if($i<=12){
                        $tablaBajas.='<tr>';
                            $tablaBajas.='<td><div style="background:url(../..'.$array['FOTO_EMP'].');height: 80px;width: 80px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div></td>';
                            $tablaBajas.='<td>'.$array['NOMBRE'].'</td>';
                            $tablaBajas.='<td>'.$array['DESC_PUESTO'].'</td>';
                            $tablaBajas.='<td>$ '.number_format($array['SUELDO_NETO'],2,'.',',').'</td>';
                            $tablaBajas.='<td>'.$array['DESC_UNIDAD_NEGOCIO'].'</td>';
                            $tablaBajas.='<td>'.$array['NOMBRE_ADMINISTRADORA'].'</td>';
                            $tablaBajas.='<td>'.transform_date($array['FECHA_BAJA'],'d/m/Y').'</td>';
                            $tablaBajas.='<td>'.$array['MOTIVO'].'</td>';
                            $tablaBajas.='<td>'.$array['USUARIO'].'</td>';
                        $tablaBajas.='</tr>';
                    }
                    $tablaDetalleBajas.='<tr>';
                        $tablaDetalleBajas.='<td><div style="background:url(../..'.$array['FOTO_EMP'].');height: 80px;width: 80px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div></td>';
                        $tablaDetalleBajas.='<td>'.$array['NOMBRE'].'</td>';
                        $tablaDetalleBajas.='<td>'.$array['DESC_PUESTO'].'</td>';
                        $tablaDetalleBajas.='<td>$ '.number_format($array['SUELDO_NETO'],2,'.',',').'</td>';
                        $tablaDetalleBajas.='<td>'.$array['DESC_UNIDAD_NEGOCIO'].'</td>';
                        $tablaDetalleBajas.='<td>'.$array['NOMBRE_ADMINISTRADORA'].'</td>';
                        $tablaDetalleBajas.='<td>'.transform_date($array['FECHA_BAJA'],'d/m/Y').'</td>';
                        $tablaDetalleBajas.='<td>'.$array['MOTIVO'].'</td>';
                        $tablaDetalleBajas.='<td>'.$array['USUARIO'].'</td>';
                    $tablaDetalleBajas.='</tr>';
                }
            } else {
                $tablaBajas = "<tr><td colspan='9' class='td-no-results'>NO EXISTEN ELEMENTOS QUE CUENTEN CON LOS CRITERIOS ESPECIFICADOS PARA LOS PARÁMETROS DE BÚSQUEDA</td></tr>";
                $tablaDetalleBajas = "<tr><td colspan='9' class='td-no-results'>NO EXISTEN ELEMENTOS QUE CUENTEN CON LOS CRITERIOS ESPECIFICADOS PARA LOS PARÁMETROS DE BÚSQUEDA</td></tr>";
            }

            //5.- Se ejecuta el stored procedure que contiene la informacion de los cambios de SUEDLO de los empleados
            $tablaSueldos = '';
            $tablaDetalleSueldos = '';
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_MOVIMIENTOS_SUELDOS($FK_UNIDAD_NEGOCIO,$FK_UBICACION_FISICA,$FK_RAZON_SOCIAL,$FECHA_INI,$FECHA_FIN)")->queryAll();
            $cantRegistrosSueldos = count($total_registros);
            if($cantRegistrosSueldos > 0){
                $i=0;
                foreach($total_registros as $array){
                    $i++;
                    //En la pantalla Principal solo se deben de mostrar cuando mucho 12 registros por apartado
                    if($i<=12){
                        $tablaSueldos.='<tr>';
                            $tablaSueldos.='<td><div style="background:url(../..'.$array['FOTO_EMP'].');height: 80px;width: 80px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div></td>';
                            $tablaSueldos.='<td>'.$array['NOMBRE'].'</td>';
                            $tablaSueldos.='<td>'.$array['DESC_PUESTO'].'</td>';
                            $tablaSueldos.='<td>'.$array['DESC_UNIDAD_NEGOCIO'].'</td>';
                            $tablaSueldos.='<td>$ '.number_format($array['SUELDO_ANTERIOR'],2,'.',',').'</td>';
                            $tablaSueldos.='<td>$ '.number_format($array['SUELDO_NUEVO'],2,'.',',').'</td>';
                            $tablaSueldos.='<td>'.transform_date($array['QUINCENA_APLICAR'],'d/m/Y').'</td>';
                            $tablaSueldos.='<td>'.$array['USUARIO'].'</td>';
                        $tablaSueldos.='</tr>';
                    }
                    $tablaDetalleSueldos.='<tr>';
                        $tablaDetalleSueldos.='<td><div style="background:url(../..'.$array['FOTO_EMP'].');height: 80px;width: 80px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div></td>';
                        $tablaDetalleSueldos.='<td>'.$array['NOMBRE'].'</td>';
                        $tablaDetalleSueldos.='<td>'.$array['DESC_PUESTO'].'</td>';
                        $tablaDetalleSueldos.='<td>'.$array['DESC_UNIDAD_NEGOCIO'].'</td>';
                        $tablaDetalleSueldos.='<td>$ '.number_format($array['SUELDO_ANTERIOR'],2,'.',',').'</td>';
                        $tablaDetalleSueldos.='<td>$ '.number_format($array['SUELDO_NUEVO'],2,'.',',').'</td>';
                        $tablaDetalleSueldos.='<td>'.transform_date($array['QUINCENA_APLICAR'],'d/m/Y').'</td>';
                        $tablaDetalleSueldos.='<td>'.$array['USUARIO'].'</td>';
                    $tablaDetalleSueldos.='</tr>';
                }
            } else {
                $tablaSueldos = "<tr><td colspan='8' class='td-no-results'>NO EXISTEN ELEMENTOS QUE CUENTEN CON LOS CRITERIOS ESPECIFICADOS PARA LOS PARÁMETROS DE BÚSQUEDA</td></tr>";
                $tablaDetalleSueldos = "<tr><td colspan='8' class='td-no-results'>NO EXISTEN ELEMENTOS QUE CUENTEN CON LOS CRITERIOS ESPECIFICADOS PARA LOS PARÁMETROS DE BÚSQUEDA</td></tr>";
            }
            
            $connection->close();
            $res = [
                'post'=>$post,
                'tablaIngresos' => $tablaIngresos,
                'tablaEstatus' => $tablaEstatus,
                'tablaUnidadNegocio' => $tablaUnidadNegocio,
                'tablaBajas' => $tablaBajas,
                'tablaSueldos' => $tablaSueldos,
                'tablaDetalleIngresos' => $tablaDetalleIngresos,
                'tablaDetalleEstatus' => $tablaDetalleEstatus,
                'tablaDetalleUnidadNegocio' => $tablaDetalleUnidadNegocio,
                'tablaDetalleBajas' => $tablaDetalleBajas,
                'tablaDetalleSueldos' => $tablaDetalleSueldos,
                'cantRegistrosIngreso' => $cantRegistrosIngreso,
                'cantRegistrosEstatus' => $cantRegistrosEstatus,
                'cantRegistrosUnidadNegocio' => $cantRegistrosUnidadNegocio,
                'cantRegistrosBajas' => $cantRegistrosBajas,
                'cantRegistrosSueldos' => $cantRegistrosSueldos,
            ];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $res;
        } else {
            user_log_bitacora('Consulta: Reporte de Movimientos','Consulta: Reporte de Movimientos','0');
            return $this->render('index_movimientos', [
                'total_paginas' => 0,
                'unidadNegocio' => $unidadNegocio,
                'datosUbicacionFisica' => $datosUbicacionFisica,
                'datosRazonSocial' => $datosRazonSocial,
            ]);
        }
    }

    public function actionIndex_actividades(){
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
            $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }else{
            $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }
        $datosUbicacionFisica = ArrayHelper::map(TblCatUbicaciones::find()->where(['=','PROPIA_CLIENTE','Propia'])->asArray()->all(), 'PK_UBICACION', 'DESC_UBICACION');
        $datosRazonSocial = ArrayHelper::map(TblCatRazonSocial::find()->asArray()->all(), 'PK_RAZON_SOCIAL', 'DESC_RAZON_SOCIAL');
        if (Yii::$app->request->isAjax) {
            //Se recogen los parametros del post
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            if(isset($post['FK_UNIDAD_NEGOCIO']) && !empty($post['FK_UNIDAD_NEGOCIO'])){
                $FK_UNIDAD_NEGOCIO = $post['FK_UNIDAD_NEGOCIO'];
            }elseif((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $FK_UNIDAD_NEGOCIO = 'null';
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $cadenaUni_neg='';
                foreach($unidadesNegocioValidas as $valor){
                    $cadenaUni_neg .= $valor.',';
                }
                $FK_UNIDAD_NEGOCIO = trim($cadenaUni_neg,',');
            }else{
                $FK_UNIDAD_NEGOCIO = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            }
            $FK_UBICACION_FISICA =(!empty($post['FK_UBICACION_FISICA']))? ("'".trim($post['FK_UBICACION_FISICA'])."'"):'null';
            $FK_RAZON_SOCIAL =(!empty($post['FK_RAZON_SOCIAL']))? ("'".trim($post['FK_RAZON_SOCIAL'])."'"):'null';
            $FECHA_INI =(!empty($post['ingresoFechaIni']))? ("'".transform_date(trim($post['ingresoFechaIni']),'Y-m-d')."'"):'null';
            $FECHA_FIN =(!empty($post['ingresoFechaFin']))? ("'".transform_date(trim($post['ingresoFechaFin']),'Y-m-d')."'"):'null';

            //Se crea variable de conexion
            $connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_GENERAL_ACTIVIDADES($FECHA_INI,$FECHA_FIN,$FK_UNIDAD_NEGOCIO,$FK_RAZON_SOCIAL,$FK_UBICACION_FISICA)")->queryAll();
            $connection->close();
            foreach ($total_registros as $key => $array) {
                if($array['MODULO']==1){
                    $total_registros[$key]['DESCRIPCION_MODULO'] = 'Capital Humano';
                }elseif($array['MODULO']==2){
                    $total_registros[$key]['DESCRIPCION_MODULO'] = 'Recursos Humanos';
                }elseif($array['MODULO']==3){
                    $total_registros[$key]['DESCRIPCION_MODULO'] = 'Asignaciones';
                }elseif($array['MODULO']==4){
                    $total_registros[$key]['DESCRIPCION_MODULO'] = 'Proyectos';
                }elseif($array['MODULO']==5){
                    $total_registros[$key]['DESCRIPCION_MODULO'] = 'Reportes';
                }elseif($array['MODULO']==6){
                    $total_registros[$key]['DESCRIPCION_MODULO'] = 'Contactos y Clientes';
                }elseif($array['MODULO']==7){
                    $total_registros[$key]['DESCRIPCION_MODULO'] = 'Soporte';
                }elseif($array['MODULO']==8){
                    $total_registros[$key]['DESCRIPCION_MODULO'] = 'Contabilidad';
                }
            }
            $res = [
                'post'=>$post,
                'total_registros'=>$total_registros,
                'sp' => "CALL SP_REPORTES_INDEX_GENERAL_ACTIVIDADES($FECHA_INI,$FECHA_FIN,$FK_UNIDAD_NEGOCIO,$FK_RAZON_SOCIAL,$FK_UBICACION_FISICA)",
            ];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $res;
        } else {
            user_log_bitacora('Consulta: Reporte de Movimientos','Consulta: Reporte de Movimientos','0');
            return $this->render('index_actividades', [
                'total_paginas' => 0,
                'unidadNegocio' => $unidadNegocio,
                'datosUbicacionFisica' => $datosUbicacionFisica,
                'datosRazonSocial' => $datosRazonSocial,
            ]);
        }
    }

    public function actionIndex_paquete_clientes()
    {
        $request = Yii::$app->request;
        $tamanio_pagina= 20;
        
        $datosTipoDocumento = ArrayHelper::map(TblCatTipoDocumentos::find()->select(['PK_TIPO_DOCUMENTO','DESC_TIPO_DOCUMENTO'])->where(['not in','PK_TIPO_DOCUMENTO',[1,5]])->asArray()->all(),'PK_TIPO_DOCUMENTO','DESC_TIPO_DOCUMENTO');
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
            $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }else{
            $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }
        $fk_estatus_asignacion = [2,3,4];
        if (Yii::$app->request->isAjax) {
            //Se recogen variables de entrada
            $data = Yii::$app->request->post();
            $post=null;
            $datos=[];
            parse_str($data['data'],$post);
            if(isset($post['FK_UNIDAD_NEGOCIO']) && !empty($post['FK_UNIDAD_NEGOCIO'])){
                $unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->andFilterWhere(['and',['=','PK_UNIDAD_NEGOCIO',$post['FK_UNIDAD_NEGOCIO']]])->all();
            }elseif((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->all();
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->all();
            }else{
                $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
                $unidades_negocio = TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->all();
            }
            $filtro_unidad_negocio='';
            foreach($unidades_negocio as $key => $array){
                $filtro_unidad_negocio .= $array['PK_UNIDAD_NEGOCIO'].',';
            }
            $filtro_unidad_negocio = trim($filtro_unidad_negocio,',');
            $filtro_cliente = $post['FK_CLIENTE']!=''?$post['FK_CLIENTE']:'NULL';
            $i=0;
            $anio_actual = $post['año'];
            $html='';

            $años = array();
            $contadorAños = 0;
            $bandera = true;

            $connection = \Yii::$app->db;

            //Consulta de todas
            $años_anteriores =  $connection->createCommand("SELECT
                YEAR(peri.FECHA_INI) ANIO_PERIODO,
                SUM(peri.MONTO) TOTAL_ODC,
                SUM(IF(peri.FK_DOCUMENTO_ODC IS NULL, 0, peri.MONTO)) MONTO_CON_ODC
            FROM tbl_asignaciones asig
                LEFT JOIN tbl_periodos peri
                    ON peri.FK_ASIGNACION = asig.PK_ASIGNACION
                    AND peri.FECHA_INI < IFNULL((SELECT com3.FECHA_FIN
                                                FROM tbl_bit_comentarios_asignaciones com3
                                                WHERE com3.FK_ESTATUS_ASIGNACION = 5
                                                AND com3.FK_ASIGNACION = asig.PK_ASIGNACION
                                                ),'2050-01-01')
            WHERE asig.FK_ESTATUS_ASIGNACION in (2,3,4,5)
            AND peri.FECHA_INI IS NOT NULL
            AND MONTH(peri.FECHA_INI) <= MONTH(CURRENT_DATE())
            AND (SELECT com2.FK_ASIGNACION
                FROM tbl_bit_comentarios_asignaciones com2
                WHERE com2.FK_ESTATUS_ASIGNACION = 6
                AND com2.FK_ASIGNACION = asig.PK_ASIGNACION
                AND com2.FECHA_FIN <= peri.FECHA_INI
                AND com2.FECHA_RETOMADA >= peri.FECHA_FIN
            ) IS NULL AND YEAR(peri.FECHA_INI) <= YEAR(NOW()) GROUP BY
                    asig.FK_UNIDAD_NEGOCIO,
                    asig.FK_CLIENTE,
                    MONTH(peri.FECHA_INI)
                    ORDER BY YEAR(peri.FECHA_INI)")->queryAll();

            foreach ($años_anteriores as $key => $value) {

                $anio = $años_anteriores[$key]['ANIO_PERIODO'];
                $res_odc = floor(($años_anteriores[$key]['MONTO_CON_ODC']>0)?((float)$años_anteriores[$key]['MONTO_CON_ODC']/(float)$años_anteriores[$key]['TOTAL_ODC'])*100:0);

                if (!in_array($anio, $años)) {
                    $años[] = $anio;
                    $contadorAños++;

                    if ($res_odc <= 99 && $anio < date('Y') && $bandera) {
                        $colores[$contadorAños] = '<a href="javascript:void(0);" onclick="consulta_años('.$anio.');"><span style="color:red" class="font-bold">'.$anio.'</span></a>';
                        $bandera = false;
                    } else {
                        $colores[$contadorAños] = '<a href="javascript:void(0);" onclick="consulta_años('.$anio.');"><span style="color:blue" class="font-bold">'.$anio.'</span></a>';
                    }
                }

            }

            //$connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_GENERAL_ASIGNACIONES($anio_actual,'$filtro_unidad_negocio',$filtro_cliente)")->queryAll();
            $connection->close();
            if( count($total_registros) > 0){
                $unidadNegocioAnterior = '';
                $clienteAnterior = '';
                $html2='';
                $html3='';
                /* Totales ODC */
                $contadorMes = 0;
                $cant_asig_ODC = 0;
                $total_asig_ODC = 0;
                $cant_asig_con_ODC = 0;
                $total_asig_con_ODC = 0;
                $cant_asig_sin_ODC = 0;
                $total_asig_sin_ODC = 0;
                /* Totales HDE */
                $cant_asig_HDE = 0;
                $total_asig_HDE = 0;
                $cant_asig_con_HDE = 0;
                $total_asig_con_HDE = 0;
                $cant_asig_sin_HDE = 0;
                $total_asig_sin_HDE = 0;
                /* Totales FACTURACION */
                $cant_asig_pend_facturar = 0;
                $total_asig_pend_facturar = 0;
                $cant_asig_FACTURA = 0;
                $total_asig_FACTURA = 0;
                $cant_asig_fact_pagada = 0;
                $total_asig_fact_pagada = 0;
                $cant_asig_fact_sin_pagar = 0;
                $total_asig_fact_sin_pagar = 0;
                foreach($total_registros as $key => $array){
                    $html3 = '';
                    /* Se verifica si hay cambio en la unidad de negocio */
                    if($array['FK_UNIDAD_NEGOCIO'] != $unidadNegocioAnterior){
                        $html3.="<tr style='background-color:#a4a4a4'><td width='100%'  class='font-bold full-row' colspan='12' >".$array['DESC_UNIDAD_NEGOCIO']."</td></tr>";
                    }
                    /* Se verifica si hay un cambio en el cliente */
                    if($array['FK_CLIENTE'] != $clienteAnterior || $html3!=''){
                        $html3.="<tr style='background-color:#0F1F50;'><td width='100%' style='color:#fff' class='font-bold full-row'  colspan='12' data-pk_cliente=".$array['FK_CLIENTE']." data-nombre_cliente='".$array['NOMBRE_CLIENTE']."'>".$array['NOMBRE_CLIENTE']." (".$array['ASIG_ACTIVAS']." Asig. activas) <div class='icon-chart' style='display:none'></div></td></tr>";
                    }

                    /* Se valida si hubo un cambio de unidad de negocio o cliente*/
                    if($key==0){
                        $html.=$html3;
                    }elseif($html3 != ''){
                        $color = '';
                        $odc_porcentaje = 0;
                        $res_odc = floor(($total_asig_con_ODC>0)?((float)$total_asig_con_ODC/(float)$total_asig_ODC)*100:0);
                        if($res_odc<=49){
                            $color = 'red';
                        }elseif($res_odc>49&&$res_odc<=99){
                            $color = 'yellow';
                        }elseif($res_odc==100){
                            $color = '#84da84';
                        }
                        $html.="<tr class='font-bold full-row row-totales'>";
                            $html.="<td class='td-width-periodo text-center'>TOTALES</td>";
                            $html.="<td class='td-width-total td-odc text-center' >".round($cant_asig_ODC/$contadorMes)." Pm</td>";
                            $html.="<td class='td-width-led td-odc text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                            $html.="<td class='td-width-number td-odc text-right'>$".number_format($total_asig_ODC,2)."</td>";
                            $html.="<td class='td-width-total td-odc text-center' style='background-color: #84da84;'></td>";
                            $html.="<td class='td-width-number td-odc text-right' style='background-color: #84da84;' >$".number_format($total_asig_con_ODC,2)."</td>";
                            $html.="<td class='td-width-total td-odc text-center' style='background-color:rgb(249, 141, 141)' >".$cant_asig_sin_ODC."</td>";
                            $html.="<td class='td-width-number td-odc text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_sin_ODC,2)."</td>";
                            $color = '';
                            $odc_porcentaje = 0;
                            $res_odc = floor(($total_asig_con_HDE>0)?((float)$total_asig_con_HDE/(float)$total_asig_HDE)*100:0);
                            if($res_odc<=49){
                                $color = 'red';
                            }elseif($res_odc>49&&$res_odc<=99){
                                $color = 'yellow';
                            }elseif($res_odc==100){
                                $color = '#84da84';
                            }
                            $html.="<td class='td-width-total td-hde text-center' > </td>";
                            $html.="<td class='td-width-led td-hde text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                            $html.="<td class='td-width-number td-hde text-right'>$".number_format($total_asig_HDE,2)."</td>";
                            $html.="<td class='td-width-total td-hde text-center' style='background-color: #84da84;'></td>";
                            $html.="<td class='td-width-number td-hde text-right' style='background-color: #84da84;'>$".number_format($total_asig_con_HDE,2)."</td>";
                            $html.="<td class='td-width-total td-hde text-center' style='background-color:rgb(249, 141, 141)'>".$cant_asig_sin_HDE."</td>";
                            $html.="<td class='td-width-number td-hde text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_sin_HDE,2)."</td>";
                            $color = '';
                            $odc_porcentaje = 0;
                            $res_odc = floor(($total_asig_fact_pagada>0)?((float)$total_asig_fact_pagada/(float)$total_asig_FACTURA)*100:0);
                            if($res_odc<=49){
                                $color = 'red';
                            }elseif($res_odc>49&&$res_odc<=99){
                                $color = 'yellow';
                            }elseif($res_odc==100){
                                $color = '#84da84';
                            }
                            $html.="<td class=' td-width-total td-factura text-center' style='background-color:orange'>".$cant_asig_pend_facturar."</td>";
                            $html.="<td class=' td-width-number td-factura text-right' style='background-color:orange'>$".number_format($total_asig_pend_facturar,2)."</td>";
                            $html.="<td class=' td-width-total td-factura text-center' > </td>";
                            $html.="<td class=' td-width-led td-factura text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                            $html.="<td class=' td-width-number td-factura text-right'>$".number_format($total_asig_FACTURA,2)."</td>";
                            $html.="<td class=' td-width-total td-factura text-center' style='background-color: #84da84;'></td>";
                            $html.="<td class=' td-width-number td-factura text-right' style='background-color: #84da84;'>$".number_format($total_asig_fact_pagada,2)."</td>";
                            $html.="<td class=' td-width-total td-factura text-center' style='background-color:rgb(249, 141, 141)'>".$cant_asig_fact_sin_pagar."</td>";
                            $html.="<td class=' td-width-number td-factura text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_fact_sin_pagar,2)."</td>";
                        $html.="</tr>";
                        $html.=$html2;
                        $html.="<tr style='background-color:#fff'><td width='100%'  class='font-bold full-row' colspan='12' ><canvas id=\"chart-".$clienteAnterior."\" width=\"1080\" height=\"540\" style=\"display:none\"></canvas></br><a href=\"javascript:void(0)\" style=\"font-size:12px ;display:none \" data-pk_cliente='".$clienteAnterior."' class=\"cerrar_grafica c-".$clienteAnterior."\">(cerrar)</a></td></tr>";
                        $html.=$html3;
                        $html2 = '';
                        /* Totales ODC */
                        $contadorMes = 0;
                        $cant_asig_ODC = 0;
                        $total_asig_ODC = 0;
                        $cant_asig_con_ODC = 0;
                        $total_asig_con_ODC = 0;
                        $cant_asig_sin_ODC = 0;
                        $total_asig_sin_ODC = 0;
                        /* Totales HDE */
                        $cant_asig_HDE = 0;
                        $total_asig_HDE = 0;
                        $cant_asig_con_HDE = 0;
                        $total_asig_con_HDE = 0;
                        $cant_asig_sin_HDE = 0;
                        $total_asig_sin_HDE = 0;
                        /* Totales FACTURACION */
                        $cant_asig_pend_facturar = 0;
                        $total_asig_pend_facturar = 0;
                        $cant_asig_FACTURA = 0;
                        $total_asig_FACTURA = 0;
                        $cant_asig_fact_pagada = 0;
                        $total_asig_fact_pagada = 0;
                        $cant_asig_fact_sin_pagar = 0;
                        $total_asig_fact_sin_pagar = 0;
                    }


                    $color = '';
                    $odc_porcentaje = 0;
                    $res_odc = floor(($array['MONTO_CON_ODC']>0)?((float)$array['MONTO_CON_ODC']/(float)$array['TOTAL_ODC'])*100:0);
                    if($res_odc<=49){
                        $color = 'red';
                    }elseif($res_odc>49&&$res_odc<=99){
                        $color = 'yellow';
                    }elseif($res_odc==100){
                        $color = '#84da84';
                    }

                    $html2.="<tr>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-periodo text-center'>".$array['MES_NOMBRE']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-odc text-center'> ".$array['CANT_ASIG']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-led td-odc text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-number td-odc text-right' >".number_format($array['TOTAL_ODC'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-odc text-center' > ".$array['CANT_ASIG_CON_ODC']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." con-odc td-width-number td-odc text-right' data-valor=".$array['MONTO_CON_ODC']." data-mes=".$array['MES_PERIODO']." >".number_format($array['MONTO_CON_ODC'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-odc text-center' > ".$array['CANT_ASIG_SIN_ODC']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." sin-odc td-width-number td-odc text-right' data-valor=".$array['MONTO_SIN_ODC']."  data-mes=".$array['MES_PERIODO'].">".number_format($array['MONTO_SIN_ODC'],2)."</td>";

                        $color = '';
                        $odc_porcentaje = 0;
                        $res_odc = floor(($array['MONTO_CON_HDE']>0)?((float)$array['MONTO_CON_HDE']/(float)$array['TOTAL_HDE'])*100:0);
                        if($res_odc<=49){
                            $color = 'red';
                        }elseif($res_odc>49&&$res_odc<=99){
                            $color = 'yellow';
                        }elseif($res_odc==100){
                            $color = '#84da84';
                        }

                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-hde text-center'> ".$array['CANT_ASIG_HDE']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-led td-hde text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-number td-hde text-right' >".number_format($array['TOTAL_HDE'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-hde text-center' > ".$array['CANT_ASIG_CON_HDE']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." con-hde td-width-number td-hde text-right' data-valor=".$array['MONTO_CON_HDE']."  data-mes=".$array['MES_PERIODO'].">".number_format($array['MONTO_CON_HDE'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-hde text-center' > ".$array['CANT_ASIG_SIN_HDE']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." sin-hde td-width-number td-hde text-right' data-valor=".$array['MONTO_SIN_HDE']."  data-mes=".$array['MES_PERIODO'].">".number_format($array['MONTO_SIN_HDE'],2)."</td>";

                        $color = '';
                        $odc_porcentaje = 0;
                        $res_odc = floor(($array['TOTAL_ASIG_FACT_PAG']>0)?((float)$array['TOTAL_ASIG_FACT_PAG']/(float)$array['TOTAL_FACT'])*100:0);
                        if($res_odc<=49){
                            $color = 'red';
                        }elseif($res_odc>49&&$res_odc<=99){
                            $color = 'yellow';
                        }elseif($res_odc==100){
                            $color = '#84da84';
                        }
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-factura text-center' > ".
                            ($array['CANT_ASIG_PEND_FACT']!='0'?"<a href='javascript:void(0);' class='ajaxDetallePendienteFacturar'>":"").
                            $array['CANT_ASIG_PEND_FACT'].
                            ($array['CANT_ASIG_PEND_FACT']!='0'?"</a>":'').
                            "<input type='hidden' id='MES' value='".$array['MES_PERIODO']."' >".
                            "<input type='hidden' id='FK_CLIENTE' value='".$array['FK_CLIENTE']."' >".
                            "<input type='hidden' id='FK_UNIDAD_NEGOCIO' value='".$array['FK_UNIDAD_NEGOCIO']."' >".
                            "<input type='hidden' id='ANIO' value='2016'>".
                        "</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." sin-factura td-width-number td-factura text-right' data-valor=".$array['TOTAL_PEND_FACT']." data-mes=".$array['MES_PERIODO'].">".number_format($array['TOTAL_PEND_FACT'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-factura text-center'> ".$array['CANT_ASIG_FACT']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-led td-factura text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-number td-factura text-right' >".number_format($array['TOTAL_FACT'],2)."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-factura text-center' > ".$array['CANT_ASIG_FACT_PAG']."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." con-factura td-width-number td-factura text-right' data-valor=".$array['TOTAL_ASIG_FACT_PAG']." data-mes=".$array['MES_PERIODO'].">".number_format($array['TOTAL_ASIG_FACT_PAG'],2)."</td>";
                        //$html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-factura text-center' > ".$asignaciones_sin_factura."</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." td-width-total td-factura text-center' > ".
                            ($array['CANT_ASIG_FACT_SIN_PAG']!='0'?"<a href='javascript:void(0);' class='ajaxDetalleFactura'>":"").
                            $array['CANT_ASIG_FACT_SIN_PAG'].
                            ($array['CANT_ASIG_FACT_SIN_PAG']!='0'?"</a>":'').
                            "<input type='hidden' id='MES' value='".$array['MES_PERIODO']."' >".
                            "<input type='hidden' id='FK_CLIENTE' value='".$array['FK_CLIENTE']."' >".
                            "<input type='hidden' id='FK_UNIDAD_NEGOCIO' value='".$array['FK_UNIDAD_NEGOCIO']."' >".
                            "<input type='hidden' id='ANIO' value='2016' >".
                        "</td>";
                        $html2.="<td class='cliente-".$array['FK_CLIENTE']." sin-factura td-width-number td-factura text-right' data-valor=".$array['TOTAL_ASIG_FACT_SIN_PAG']." data-mes=".$array['MES_PERIODO'].">".number_format($array['TOTAL_ASIG_FACT_SIN_PAG'],2)."</td>";
                    $html2.="</tr>";

                    /* Totales ODC */
                    $contadorMes++;
                    $cant_asig_ODC += $array['CANT_ASIG'];
                    $total_asig_ODC +=$array['TOTAL_ODC'];
                    $cant_asig_con_ODC +=$array['CANT_ASIG_CON_ODC'];
                    $total_asig_con_ODC +=$array['MONTO_CON_ODC'];
                    $cant_asig_sin_ODC +=$array['CANT_ASIG_SIN_ODC'];
                    $total_asig_sin_ODC +=$array['MONTO_SIN_ODC'];
                    /* Totales HDE */
                    $cant_asig_HDE += $array['CANT_ASIG_HDE'];
                    $total_asig_HDE += $array['TOTAL_HDE'];
                    $cant_asig_con_HDE += $array['CANT_ASIG_CON_HDE'];
                    $total_asig_con_HDE += $array['MONTO_CON_HDE'];
                    $cant_asig_sin_HDE += $array['CANT_ASIG_SIN_HDE'];
                    $total_asig_sin_HDE += $array['MONTO_SIN_HDE'];
                    /* Totales FACTURACION */
                    $cant_asig_pend_facturar += $array['CANT_ASIG_PEND_FACT'];
                    $total_asig_pend_facturar += $array['TOTAL_PEND_FACT'];
                    $cant_asig_FACTURA += $array['CANT_ASIG_FACT'];
                    $total_asig_FACTURA += $array['TOTAL_FACT'];
                    $cant_asig_fact_pagada += $array['CANT_ASIG_FACT_PAG'];
                    $total_asig_fact_pagada += $array['TOTAL_ASIG_FACT_PAG'];
                    $cant_asig_fact_sin_pagar += $array['CANT_ASIG_FACT_SIN_PAG'];
                    $total_asig_fact_sin_pagar += $array['TOTAL_ASIG_FACT_SIN_PAG'];
                    $unidadNegocioAnterior = $array['FK_UNIDAD_NEGOCIO'];
                    $clienteAnterior = $array['FK_CLIENTE'];
                }

                $html.="<tr class='font-bold full-row row-totales'>";
                    $html.="<td class='td-width-periodo text-center'>TOTALES</td>";
                    $html.="<td class='td-width-total td-odc text-center' >".round($cant_asig_ODC/$contadorMes)." Pm</td>";
                    $html.="<td class='td-width-led td-odc text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                    $html.="<td class='td-width-number td-odc text-right'>$".number_format($total_asig_ODC,2)."</td>";
                    $html.="<td class='td-width-total td-odc text-center' style='background-color: #84da84;'></td>";
                    $html.="<td class='td-width-number td-odc text-right' style='background-color: #84da84;' >$".number_format($total_asig_con_ODC,2)."</td>";
                    $html.="<td class='td-width-total td-odc text-center' style='background-color:rgb(249, 141, 141)' >".$cant_asig_sin_ODC."</td>";
                    $html.="<td class='td-width-number td-odc text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_sin_ODC,2)."</td>";
                    $color = '';
                    $odc_porcentaje = 0;
                    $res_odc = floor(($total_asig_con_HDE>0)?((float)$total_asig_con_HDE/(float)$total_asig_HDE)*100:0);
                    if($res_odc<=49){
                        $color = 'red';
                    }elseif($res_odc>49&&$res_odc<=99){
                        $color = 'yellow';
                    }elseif($res_odc==100){
                        $color = '#84da84';
                    }
                    $html.="<td class='td-width-total td-hde text-center' > </td>";
                    $html.="<td class='td-width-led td-hde text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                    $html.="<td class='td-width-number td-hde text-right'>$".number_format($total_asig_HDE,2)."</td>";
                    $html.="<td class='td-width-total td-hde text-center' style='background-color: #84da84;'></td>";
                    $html.="<td class='td-width-number td-hde text-right' style='background-color: #84da84;'>$".number_format($total_asig_con_HDE,2)."</td>";
                    $html.="<td class='td-width-total td-hde text-center' style='background-color:rgb(249, 141, 141)'>".$cant_asig_sin_HDE."</td>";
                    $html.="<td class='td-width-number td-hde text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_sin_HDE,2)."</td>";
                    $color = '';
                    $odc_porcentaje = 0;
                    $res_odc = floor(($total_asig_fact_pagada>0)?((float)$total_asig_fact_pagada/(float)$total_asig_FACTURA)*100:0);
                    if($res_odc<=49){
                        $color = 'red';
                    }elseif($res_odc>49&&$res_odc<=99){
                        $color = 'yellow';
                    }elseif($res_odc==100){
                        $color = '#84da84';
                    }
                    $html.="<td class=' td-width-total td-factura text-center' style='background-color:orange'>".$cant_asig_pend_facturar."</td>";
                    $html.="<td class=' td-width-number td-factura text-right' style='background-color:orange'>$".number_format($total_asig_pend_facturar,2)."</td>";
                    $html.="<td class=' td-width-total td-factura text-center' > </td>";
                    $html.="<td class=' td-width-led td-factura text-right' style='color:$color;font-size: 20px;line-height: 12px;'>&#9679;</td>";
                    $html.="<td class=' td-width-number td-factura text-right'>$".number_format($total_asig_FACTURA,2)."</td>";
                    $html.="<td class=' td-width-total td-factura text-center' style='background-color: #84da84;'></td>";
                    $html.="<td class=' td-width-number td-factura text-right' style='background-color: #84da84;'>$".number_format($total_asig_fact_pagada,2)."</td>";
                    $html.="<td class=' td-width-total td-factura text-center' style='background-color:rgb(249, 141, 141)'>".$cant_asig_fact_sin_pagar."</td>";
                    $html.="<td class=' td-width-number td-factura text-right' style='background-color:rgb(249, 141, 141)'>$".number_format($total_asig_fact_sin_pagar,2)."</td>";
                $html.="</tr>";
                $html.=$html2;
            }else{
                $html.= "<tr>";
                    $html.='<td colspan="24" rowspan="3" style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;background-color: #D0D0D0;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.= "</tr>";
            }
                
            $res = [
                'html'=>$html,
                'post'=>$post,
                'años'=>$colores
            ];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $res;

        }else{
            user_log_bitacora('Consulta: Reporte General de Asignaciones','Consulta: Reporte General de Asignaciones','0');
            return $this->render('index', [
                'total_paginas' => 0,
                'unidadNegocio' => $unidadNegocio,
                'datosTipoDocumento' => $datosTipoDocumento,
            ]);
            
        }
    }

    public function actionDetalle_actividades_mensual(){
        if (Yii::$app->request->isAjax) {
            //Se recogen los parametros del post
            $data = Yii::$app->request->post();
            $id_modulo = $data['id_modulo'];
            $connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_ACTIVIDADES_MENSUAL($id_modulo)")->queryAll();
            $connection->close();
            $res = [
                'post'=>$data,
                'total_registros'=>$total_registros,
            ];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $res;

        }
    }

    public function actionObtener_detalle_actividades(){
        if (Yii::$app->request->isAjax) {
            //Se recogen los parametros del post
            $data = Yii::$app->request->post();
            $id_modulo = $data['id_modulo'];
            if(isset($data['unidad_negocio']) && !empty($data['unidad_negocio'])){
                $FK_UNIDAD_NEGOCIO = $data['unidad_negocio'];
            }elseif((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $FK_UNIDAD_NEGOCIO = 'null';
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $cadenaUni_neg='';
                foreach($unidadesNegocioValidas as $valor){
                    $cadenaUni_neg .= $valor.',';
                }
                $FK_UNIDAD_NEGOCIO = trim($cadenaUni_neg,',');
            }else{
                $FK_UNIDAD_NEGOCIO = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            }
            $FK_UBICACION_FISICA =(!empty($data['ubicacion_fisica']))? ("'".trim($data['ubicacion_fisica'])."'"):'null';
            $FK_RAZON_SOCIAL =(!empty($data['razon_social']))? ("'".trim($data['razon_social'])."'"):'null';
            $FECHA_INI =(!empty($data['fecha_ini']))? ("'".transform_date(trim($data['fecha_ini']),'Y-m-d')."'"):'null';
            $FECHA_FIN =(!empty($data['fecha_fin']))? ("'".transform_date(trim($data['fecha_fin']),'Y-m-d')."'"):'null';
            
            $connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_ACTIVIDADES_DETALLE($id_modulo,$FECHA_INI,$FECHA_FIN,$FK_UNIDAD_NEGOCIO,$FK_RAZON_SOCIAL,$FK_UBICACION_FISICA)")->queryAll();
            $connection->close();
            $res = [
                'post'=>$data,
                'total_registros'=>$total_registros,
                'sp' => "CALL SP_REPORTES_INDEX_ACTIVIDADES_DETALLE($id_modulo,$FECHA_INI,$FECHA_FIN,$FK_UNIDAD_NEGOCIO,$FK_RAZON_SOCIAL,$FK_UBICACION_FISICA)",
            ];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $res;
        }
    }

    public function actionClientes(){
        $data = Yii::$app->request->post();
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if(isset($data['fk_unidad_negocio']) && !empty($data['fk_unidad_negocio'])) {
            $clientes = (new \yii\db\Query())
                ->select(['c.PK_CLIENTE','c.NOMBRE_CLIENTE'])
                ->from('tbl_clientes c')
                ->join('inner join','tbl_asignaciones a','a.FK_CLIENTE=c.PK_CLIENTE')
                ->where(['a.FK_UNIDAD_NEGOCIO'=>$data['fk_unidad_negocio']])
                ->orderBy('c.NOMBRE_CLIENTE ASC')
                ->distinct()
                ->all();
            
        }else{
            $clientes = (new \yii\db\Query())
                ->select(['c.PK_CLIENTE','c.NOMBRE_CLIENTE'])
                ->from('tbl_clientes c')
                ->join('inner join','tbl_asignaciones a','a.FK_CLIENTE=c.PK_CLIENTE')
                ->orderBy('c.NOMBRE_CLIENTE ASC')
                ->distinct()
                ->all();
        }

        $res = [
            'clientes'=>$clientes,
        ];
        return $res;
    }

    /**
     * Displays a single TblClientes model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model= $this->findModel($id);
       // var_dump($model);
        $model->FK_GIRO= tblcatgiro::find()->where(['PK_GIRO' => $model->FK_GIRO])->limit(1)->one();

        $domicilio = tbldomicilios::findOne($model->FK_DOMICILIO);
        //var_dump($domicilio);
        $pais      = tblcatpaises::findOne($domicilio->FK_PAIS);
        $estado    = tblcatestados::findOne($domicilio->FK_ESTADO);
        $municipio = tblcatmunicipios::find()->where(['PK_MUNICIPIO'=>$domicilio->FK_MUNICIPIO,'FK_ESTADO'=>$domicilio->FK_ESTADO,'FK_PAIS'=>$domicilio->FK_PAIS])->limit(1)->one();
        $extra['NUM_INTERIOR']   = $domicilio->NUM_INTERIOR;
        $extra['NUM_EXTERIOR']   = $domicilio->NUM_EXTERIOR;
        $extra['PISO']   = $domicilio->PISO;
        $extra['COLONIA']        = $domicilio->COLONIA;
        $extra['FK_MUNICIPIO'] = $municipio->DESC_MUNICIPIO;
        $extra['FK_ESTADO']    = $estado->DESC_ESTADO;
        $extra['CP']             = $domicilio->CP;
        $extra['PAIS']           = $pais->DESC_PAIS;
        return $this->render('view', [
            'model' => $model,
            'extra' => $extra,
            'domicilio' => $domicilio,
            
        ]);
    }

    /**
     * Creates a new TblClientes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new tblclientes();
        $modelDomicilios = new tbldomicilios();

        if(!isset($model, $modelDomicilios)){
            throw new Exception("Error al obtener Datos de form");   
        }

        if ($model->load(Yii::$app->request->post()) && $modelDomicilios->load(Yii::$app->request->post())) {
                $modelDomicilios->save(false);

                $model->FECHA_REGISTRO=date('Y-m-d h:i:s');
                $model->FK_DOMICILIO=$modelDomicilios->PK_DOMICILIO;
                $model->HORAS_ASIGNACION = ($model->HORAS_ASIGNACION == null ? 0 : $model->HORAS_ASIGNACION);
                $model->FK_USUARIO_CREADOR= user_info()['PK_USUARIO'];
                $insert= $model->save(false);

                if($insert){
                    $descripcionBitacora = 'PK_CLIENTE='.$model->PK_CLIENTE.',NOMBRE_CLIENTE='.$model->NOMBRE_CLIENTE.',FK_GIRO='.$model->FK_GIRO.',FK_DOMICILIO='.$model->FK_DOMICILIO;
                    user_log_bitacora($descripcionBitacora,'Alta de Datos Cliente',$model->PK_CLIENTE );            
                }
                //Yii::$app->session->setFlash('success', 'Los datos se han guardado correctamente.');
                // return $this->redirect(['view', 'id' => $model->PK_CLIENTE]);
                return $this->redirect(['create', 'id' => $model->PK_CLIENTE,'action'=>'insert']);
        } else {
            //Yii::$app->session->setFlash('success', 'Error al guardar, intÃ©ntelo de nuevo.');
            return $this->render('create', [
                'model' => $model,
                'modelDomicilios' => $modelDomicilios,
            ]);
        }
    }

    /**
     * Updates an existing TblClientes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $sql = 'select * from tbl_domicilios AS td WHERE td.pk_domicilio = 
                (select tc.fk_domicilio from tbl_clientes tc where tc.pk_cliente ='.$id.')';

        $modelDomicilios = tbldomicilios::findBySql($sql)->limit(1)->one();

        $extra['DESC_MUNICIPIO']     = tblcatestados::findOne($modelDomicilios->FK_ESTADO);
        //$extra['DESC_ESTADO']     = tblcatmunicipios::findOne($modelDomicilios->FK_MUNICIPIO);
        $extra['DESC_ESTADO']  = tblcatmunicipios::find()->where(['PK_MUNICIPIO'=>$modelDomicilios->FK_MUNICIPIO,'FK_ESTADO'=>$modelDomicilios->FK_ESTADO,'FK_PAIS'=>$modelDomicilios->FK_PAIS])->limit(1)->one();

        if (!isset($model, $modelDomicilios)) {
            throw new NotFoundHttpException("El cliente no fue encontrado");
        }

        if ($model->load(Yii::$app->request->post()) && $modelDomicilios->load(Yii::$app->request->post()) ) {
            // $isValid = $model->validate();
            // $isValid = $modelDomicilios->validate() && $isValid;
            // if($isValid){
                $model->save(false);
                $modelDomicilios->save(false);

                $descripcionBitacora = 'PK_CLIENTE='.$model->PK_CLIENTE.',NOMBRE_CLIENTE='.$model->NOMBRE_CLIENTE;
                user_log_bitacora($descripcionBitacora,'ModificaciÃ³n de datos de Cliente',$model->PK_CLIENTE );            

                // Yii::$app->session->setFlash('success', 'El Registro se ha modificado correctamente');
                return $this->redirect(['view', 'id' => $id]);
            // }
        }

        return $this->render('update', [
            'model' => $model,
            'modelDomicilios' => $modelDomicilios,
            'extra' => $extra,
        ]);
    }

    /**
     * Deletes an existing TblClientes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionObtener_detalle_factura(){
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $arrayMeses= array(
                    1 => 'ENERO',
                    2 => 'FEBRERO',
                    3 => 'MARZO',
                    4 => 'ABRIL',
                    5 => 'MAYO',
                    6 => 'JUNIO',
                    7 => 'JULIO',
                    8 => 'AGOSTO',
                    9 => 'SEPTIEMBRE',
                    10 => 'OCTUBRE',
                    11 => 'NOVIEMBRE',
                    12 => 'DICIEMBRE'
                );
            $MES= explode(":", $data['MES']);
            $MES = $MES[0];
            $FK_CLIENTE= explode(":", $data['FK_CLIENTE']);
            $FK_CLIENTE = $FK_CLIENTE[0];
            $FK_UNIDAD_NEGOCIO= explode(":", $data['FK_UNIDAD_NEGOCIO']);
            $FK_UNIDAD_NEGOCIO = $FK_UNIDAD_NEGOCIO[0];
            $ANIO= explode(":", $data['ANIO']);
            $ANIO = $ANIO[0];
            //Se ejecuta SP que trae el detalle de las facturas a consultar
            $connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_REPORTES_DETALLE_FACTURAS($FK_CLIENTE,$FK_UNIDAD_NEGOCIO,$MES,$ANIO)")->queryAll();
            $modelUnidadNegocio = TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO' => $FK_UNIDAD_NEGOCIO])->limit(1)->one();
            $modelCliente = TblClientes::find()->where(['PK_CLIENTE' => $FK_CLIENTE])->limit(1)->one();
            $html='';
            $diasHabiles = $modelCliente->DIAS_LIMITE_PAGO!=null?$modelCliente->DIAS_LIMITE_PAGO:30;
            $fechaHoy = strtotime(date("d-m-Y"));
            foreach($total_registros as $array){
                unset($fechaProximoPago);
                unset($fechaProximoPagoConvertida);
                $url = Url::to(['asignaciones/view','id'=>$array['PK_ASIGNACION']]);
                $html.="<tr>";
                    // PK_ASIGNACION
                    $html.="<td><a href='".$url."' target='_blank'>".$array['PK_ASIGNACION']."</a></td>";
                    
                    //# de factura
                    if($array['FACTURA'] !='' && isset($array['FACTURA'])){
                        $html.="<td>".$array['FACTURA']."</td>";
                    } else {
                        $html.="<td style='color: red;'> SIN FACTURA </td>";
                    }
                    
                    // Monto de Factura
                    if($array['MONTO_FACTURA'] !=''){
                        $html.="<td>$ ".number_format($array['MONTO_FACTURA'],2,'.',',')."</td>";
                    } else {
                        $html.="<td>$ ".number_format($array['MONTO'],2,'.',',')."</td>";
                    }
                    
                    //Fecha entrega a cliente
                    $diasFacturaVencida = 0;
                    if(isset($array['FECHA_ENTREGA_CLIENTE'])){
                        $html.="<td>".$array['FECHA_ENTREGA_CLIENTE']."</td>";
                        $fechaProximoPago = strtotime(str_replace('/', '-', $array['FECHA_ENTREGA_CLIENTE']));
                        $diasActuales = 0;
                         
                        while($diasActuales < $diasHabiles){
                            $fechaProximoPago+= 86400;
                            //if(date('N',$fechaProximoPago)<= 5 ){//Se valida que el dia de la semana no sea sabado ni domingo
                                $diasActuales++;
                            //}
                        }
                        $fechaProximoPagoConvertida = date('d/m/Y',$fechaProximoPago);
                        $arrayFechas[] = array(
                            'proxPago' => $fechaProximoPago,
                            'hoy'      => $fechaHoy,
                            );
                        if($fechaProximoPago < $fechaHoy){
                            while($fechaProximoPago < $fechaHoy){
                                $fechaProximoPago+= 86400;
                                //if(date('N',$fechaProximoPago)<= 5 ){//Se valida que el dia de la semana no sea sabado ni domingo
                                    $diasFacturaVencida++;
                                //}
                            }
                        }
                    } else {
                        $html.="<td></td>";
                    }
                    
                    //Fecha ingreso a banco
                    if(isset($fechaProximoPagoConvertida)){
                        $html.="<td>".$fechaProximoPagoConvertida."</td>";
                    } else {
                        $html.="<td></td>";
                    }

                    //Dias factura vencida
                    if($diasFacturaVencida > 0){
                        $html.="<td>".$diasFacturaVencida."</td>";
                    } else {
                        $html.="<td></td>";
                    }
                $html.="</tr>";
            }
            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return[
                'html' => $html,
                'unidadNeogico' => $modelUnidadNegocio->DESC_UNIDAD_NEGOCIO,
                'cliente' => $modelCliente->NOMBRE_CLIENTE,
                'mes' => $arrayMeses[$MES],
                'diasHabiles' => $diasHabiles,
                'fechaHoy' => $fechaHoy,
                'arrayFechas' => $arrayFechas,
                'hoy' => date("d/m/Y"),
            ];
        }
    }   
    public function actionObtener_detalle_proyeccion_pagos(){
        if (Yii::$app->request->isAjax) {
            //Se recogen los parametros y se inicializan variables
            $data = Yii::$app->request->post();
            $idOperacion = $data['idOperacion'];
            $FK_UNIDAD_NEGOCIO = $data['FK_UNIDAD_NEGOCIO'];
            $FK_CLIENTE = $data['FK_CLIENTE'];
            $MES = $data['MES'];
            $ANIO = $data['ANIO'];
            $html = '';
            $diasPago = '';
            $cliente = '';
            $unidadNegocio = '';
            $arrayMeses= array(
                    1 => 'ENERO',
                    2 => 'FEBRERO',
                    3 => 'MARZO',
                    4 => 'ABRIL',
                    5 => 'MAYO',
                    6 => 'JUNIO',
                    7 => 'JULIO',
                    8 => 'AGOSTO',
                    9 => 'SEPTIEMBRE',
                    10 => 'OCTUBRE',
                    11 => 'NOVIEMBRE',
                    12 => 'DICIEMBRE'
                );
            //Se realiza la conexion y se ejecuta SP
            $connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_AJAX_DETALLE($FK_UNIDAD_NEGOCIO,$FK_CLIENTE,$MES,$ANIO)")->queryAll();

                
            //Se valida que el SP haya traido registros
            if(count($total_registros) > 0){
                //Se valida de que tipo de operacion viene la llamada
                /*****
                Tipo operacion
                1 => ODC
                2 => HDE
                3 => FAC
                4 => PAGO
                ****/
                $diasPago=strtoupper($total_registros[0]['DIAS_LIMITE_PAGO']);
                $cliente=strtoupper($total_registros[0]['NOMBRE_CLIENTE']);
                $unidadNegocio=strtoupper($total_registros[0]['DESC_UNIDAD_NEGOCIO']);
                if($idOperacion==1){
                    foreach($total_registros as $arrayRegistro){
                        $url = Url::to(['asignaciones/view','id'=>$arrayRegistro['FK_ASIGNACION']]);
                        $fechaIniConvertida = transform_date($arrayRegistro['FECHA_INI'],'d/m/Y');
                        $fechaFinConvertida = transform_date($arrayRegistro['FECHA_FIN'],'d/m/Y');
                        $fechaODC= ($arrayRegistro['FECHA_ODC']?transform_date($arrayRegistro['FECHA_ODC'],'d/m/Y'):'');
                        $html.='<tr>';
                            $html.='<td><a target="_blank" href="'.$url.'" >'.($arrayRegistro['DIFF_FECHA_INI_VS_ODC']<0||!$fechaODC?'*':'').$arrayRegistro['FK_ASIGNACION'].'</a></td>';
                            $html.='<td>'.$fechaIniConvertida.' - '.$fechaFinConvertida.'</td>';
                            $html.='<td>'.$fechaIniConvertida.'</td>';
                            $html.='<td>'.$fechaODC.'</td>';
                            $html.='<td>'.($arrayRegistro['DIFF_FECHA_INI_VS_ODC']<0?'0':$arrayRegistro['DIFF_FECHA_INI_VS_ODC']).'</td>';
                        $html.='</tr>';
                    }
                }elseif($idOperacion==2){
                    foreach($total_registros as $arrayRegistro){
                        $url = Url::to(['asignaciones/view','id'=>$arrayRegistro['FK_ASIGNACION']]);
                        $fechaIniConvertida = transform_date($arrayRegistro['FECHA_INI'],'d/m/Y');
                        $fechaFinConvertida = transform_date($arrayRegistro['FECHA_FIN'],'d/m/Y');
                        $fechaODC= ($arrayRegistro['FECHA_ODC']?transform_date($arrayRegistro['FECHA_ODC'],'d/m/Y'):'');
                        $fechaHDE= ($arrayRegistro['FECHA_HDE']?transform_date($arrayRegistro['FECHA_HDE'],'d/m/Y'):'');
                        $html.='<tr>';
                            $html.='<td><a target="_blank" href="'.$url.'" >'.($arrayRegistro['DIFF_ODC_VS_HDE']<0||!$fechaHDE?'*':'').$arrayRegistro['FK_ASIGNACION'].'</a></td>';
                            $html.='<td>'.$fechaIniConvertida.' - '.$fechaFinConvertida.'</td>';
                            $html.='<td>'.$fechaODC.'</td>';
                            $html.='<td>'.$fechaHDE.'</td>';
                            $html.='<td>'.($arrayRegistro['DIFF_ODC_VS_HDE']<0?'0':$arrayRegistro['DIFF_ODC_VS_HDE']).'</td>';
                        $html.='</tr>';
                    }
                }elseif($idOperacion==3){
                    foreach($total_registros as $arrayRegistro){
                        $url = Url::to(['asignaciones/view','id'=>$arrayRegistro['FK_ASIGNACION']]);
                        $fechaIniConvertida = transform_date($arrayRegistro['FECHA_INI'],'d/m/Y');
                        $fechaFinConvertida = transform_date($arrayRegistro['FECHA_FIN'],'d/m/Y');
                        $fechaHDE= ($arrayRegistro['FECHA_HDE']?transform_date($arrayRegistro['FECHA_HDE'],'d/m/Y'):'');
                        $fechaFacturaEntregada= ($arrayRegistro['FECHA_ENTREGA_CLIENTE']?transform_date($arrayRegistro['FECHA_ENTREGA_CLIENTE'],'d/m/Y'):'');
                        $html.='<tr>';
                            $html.='<td><a target="_blank" href="'.$url.'" >'.($arrayRegistro['DIFF_HDE_VS_FAC']<0||!$fechaFacturaEntregada?'*':'').$arrayRegistro['FK_ASIGNACION'].'</a></td>';
                            $html.='<td>'.$fechaIniConvertida.' - '.$fechaFinConvertida.'</td>';
                            $html.='<td>'.$fechaHDE.'</td>';
                            $html.='<td>'.$fechaFacturaEntregada.'</td>';
                            $html.='<td>'.($arrayRegistro['DIFF_HDE_VS_FAC']<0?'0':$arrayRegistro['DIFF_HDE_VS_FAC']).'</td>';
                        $html.='</tr>';
                    }
                }elseif($idOperacion==4){
                    foreach($total_registros as $arrayRegistro){
                        $url = Url::to(['asignaciones/view','id'=>$arrayRegistro['FK_ASIGNACION']]);
                        $fechaIniConvertida = transform_date($arrayRegistro['FECHA_INI'],'d/m/Y');
                        $fechaFinConvertida = transform_date($arrayRegistro['FECHA_FIN'],'d/m/Y');
                        $fechaFacturaEntregada= ($arrayRegistro['FECHA_ENTREGA_CLIENTE']?transform_date($arrayRegistro['FECHA_ENTREGA_CLIENTE'],'d/m/Y'):'');
                        $fechaFacturaPagada= ($arrayRegistro['FECHA_INGRESO_BANCO']?transform_date($arrayRegistro['FECHA_INGRESO_BANCO'],'d/m/Y'):'');
                        $html.='<tr>';
                            $html.='<td><a target="_blank" href="'.$url.'" >'.($arrayRegistro['DIFF_FAC_VS_PAGO']<0||!$fechaFacturaPagada?'*':'').$arrayRegistro['FK_ASIGNACION'].'</a></td>';
                            $html.='<td>'.$fechaIniConvertida.' - '.$fechaFinConvertida.'</td>';
                            $html.='<td>'.$fechaFacturaEntregada.'</td>';
                            $html.='<td>'.$fechaFacturaPagada.'</td>';
                            $html.='<td>'.($arrayRegistro['DIFF_FAC_VS_PAGO']<0?'0':$arrayRegistro['DIFF_FAC_VS_PAGO']).'</td>';
                        $html.='</tr>';
                    }
                }
            }
            
            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return[
                'html' => $html,
                'mes'=>$arrayMeses[$MES],
                'diasPago'=>$diasPago,
                'cliente'=>$cliente,
                'unidadNegocio'=>$unidadNegocio,
                'query' => "CALL SP_REPORTES_INDEX_AJAX_DETALLE($FK_UNIDAD_NEGOCIO,$FK_CLIENTE,$MES,$ANIO)",
            ];
        }
    }
    public function actionObtener_detalle_proyeccion_saldos(){
        if (Yii::$app->request->isAjax) {
            //Se recogen los parametros y se inicializan variables
            $data = Yii::$app->request->post();
            $idOperacion = $data['idOperacion'];
            $FK_UNIDAD_NEGOCIO = $data['FK_UNIDAD_NEGOCIO'];
            $FK_CLIENTE = $data['FK_CLIENTE'];
            $MES = $data['MES'];
            $ANIO = $data['ANIO'];
            $html = '';
            $diasPago = '';
            $cliente = '';
            $unidadNegocio = '';
            $arrayMeses= array(
                    1 => 'ENERO',
                    2 => 'FEBRERO',
                    3 => 'MARZO',
                    4 => 'ABRIL',
                    5 => 'MAYO',
                    6 => 'JUNIO',
                    7 => 'JULIO',
                    8 => 'AGOSTO',
                    9 => 'SEPTIEMBRE',
                    10 => 'OCTUBRE',
                    11 => 'NOVIEMBRE',
                    12 => 'DICIEMBRE'
                );
            //Se realiza la conexion y se ejecuta SP
            $connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_REPORTES_INDEX_AJAX_DETALLE_SALDOS($FK_UNIDAD_NEGOCIO,$FK_CLIENTE,$MES,$ANIO)")->queryAll();

                
            //Se valida que el SP haya traido registros
            if(count($total_registros) > 0){
                //Se valida de que tipo de operacion viene la llamada
                /*****
                Tipo operacion
                5 => FACTURAS ASIGNACIONES
                6 => FACTIRAS PROYECTOS
                ****/
                $diasPago=strtoupper($total_registros[0]['DIAS_LIMITE_PAGO']);
                $cliente=strtoupper($total_registros[0]['NOMBRE_CLIENTE']);
                $unidadNegocio=strtoupper($total_registros[0]['DESC_UNIDAD_NEGOCIO']);
                if($idOperacion==5){
                    foreach($total_registros as $arrayRegistro){
                        $url = Url::to(['asignaciones/view','id'=>$arrayRegistro['FK_ASIGNACION']]);
                        $fechaIniConvertida = transform_date($arrayRegistro['FECHA_INI'],'d/m/Y');
                        $fechaFinConvertida = transform_date($arrayRegistro['FECHA_FIN'],'d/m/Y');
                        $fechaPendientePago = ($arrayRegistro['FECHA_PENDIENTE_PAGO']?transform_date($arrayRegistro['FECHA_PENDIENTE_PAGO'],'d/m/Y'):'');
                        
                        $html.='<tr>';
                            $html.='<td><a target="_blank" href="'.$url.'" >'.$arrayRegistro['FK_ASIGNACION'].'</a></td>';
                            $html.='<td>'.$fechaIniConvertida.' - '.$fechaFinConvertida.'</td>';
                            $html.='<td>'.$fechaPendientePago.'</td>';
                            $html.='<td>'."$ ".number_format((float)$arrayRegistro['TOTAL_FACTURABLE'],2).'</td>';
                        $html.='</tr>';
                    }
                }elseif($idOperacion==6){
                    foreach($total_registros as $arrayRegistro){
                        $url = Url::to(['asignaciones/view','id'=>$arrayRegistro['FK_ASIGNACION']]);
                        $fechaIniConvertida = transform_date($arrayRegistro['FECHA_INI'],'d/m/Y');
                        $fechaFinConvertida = transform_date($arrayRegistro['FECHA_FIN'],'d/m/Y');
                        $fechaPendientePago = ($arrayRegistro['FECHA_PENDIENTE_PAGO']?transform_date($arrayRegistro['FECHA_PENDIENTE_PAGO'],'d/m/Y'):'');
                        $html.='<tr>';
                            $html.='<td><a target="_blank" href="'.$url.'" >'.$arrayRegistro['PK_PROYECTO'].'</a></td>';
                            $html.='<td>'.$fechaIniConvertida.' - '.$fechaFinConvertida.'</td>';
                            $html.='<td>'.$fechaPendientePago.'</td>';
                            $html.='<td>'."$ ".number_format((float)$arrayRegistro['TOTAL_FACTURABLE'],2).'</td>';
                        $html.='</tr>';
                    }
                }
            }
            
            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return[
                'html' => $html,
                'mes'=>$arrayMeses[$MES],
                'diasPago'=>$diasPago,
                'cliente'=>$cliente,
                'unidadNegocio'=>$unidadNegocio,
                'query' => "CALL SP_REPORTES_INDEX_AJAX_DETALLE_SALDOS($FK_UNIDAD_NEGOCIO,$FK_CLIENTE,$MES,$ANIO)",
            ];
        }
    }
    public function actionObtener_detalle_pendiente_facturar(){
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $arrayMeses= array(
                    1 => 'ENERO',
                    2 => 'FEBRERO',
                    3 => 'MARZO',
                    4 => 'ABRIL',
                    5 => 'MAYO',
                    6 => 'JUNIO',
                    7 => 'JULIO',
                    8 => 'AGOSTO',
                    9 => 'SEPTIEMBRE',
                    10 => 'OCTUBRE',
                    11 => 'NOVIEMBRE',
                    12 => 'DICIEMBRE'
                );
            $MES= explode(":", $data['MES']);
            $MES = $MES[0];
            $FK_CLIENTE= explode(":", $data['FK_CLIENTE']);
            $FK_CLIENTE = $FK_CLIENTE[0];
            $FK_UNIDAD_NEGOCIO= explode(":", $data['FK_UNIDAD_NEGOCIO']);
            $FK_UNIDAD_NEGOCIO = $FK_UNIDAD_NEGOCIO[0];
            $ANIO= explode(":", $data['ANIO']);
            $ANIO = $ANIO[0];
            //Se ejecuta SP que trae el detalle de los periodos pendientes de facturar
            $connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_REPORTES_DETALLE_PENDIENTE_FACTURAR($FK_CLIENTE,$FK_UNIDAD_NEGOCIO,$MES,$ANIO)")->queryAll();
            $modelUnidadNegocio = TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO' => $FK_UNIDAD_NEGOCIO])->limit(1)->one();
            $html='';
            $modelCliente = TblClientes::find()->where(['PK_CLIENTE' => $FK_CLIENTE])->limit(1)->one();
            $fechaHoy = strtotime(date("d-m-Y"));
            foreach($total_registros as $array){
                $diasTranscurridos = 0;
                $url = Url::to(['asignaciones/view','id'=>$array['PK_ASIGNACION']]);
                if($array['FECHA_DOCUMENTO'] != null){
                    $fechaHDE = strtotime(str_replace('/', '-', $array['FECHA_DOCUMENTO']));
                    while($fechaHDE < $fechaHoy){
                        $fechaHDE+= 86400;
                        if(date('N',$fechaHDE)<= 5 ){//Se valida que el dia de la semana no sea sabado ni domingo
                            $diasTranscurridos++;
                        }
                    }
                }
                    
                $html.="<tr>";

                    // PK_ASIGNACION
                    $html.="<td><a href='".$url."' target='_blank'>".$array['PK_ASIGNACION']."</a></td>";
                    
                    //# DE HDE
                    if($array['NUM_DOCUMENTO'] !='' && isset($array['NUM_DOCUMENTO'])){
                        $html.="<td>".$array['NUM_DOCUMENTO']."</td>";
                    } else {
                        $html.="<td></td>";
                    }

                    //MONTO HDE
                    if($array['MONTO_HDE'] !='' && isset($array['MONTO_HDE'])){
                        $html.="<td>$ ".number_format($array['MONTO_HDE'],2,'.',',')."</td>";
                    } else {
                        $html.="<td></td>";
                    }

                    //FECHA HDE
                    if($array['FECHA_DOCUMENTO'] !='' && isset($array['FECHA_DOCUMENTO'])){
                        $html.="<td>".$array['FECHA_DOCUMENTO']."</td>";
                    } else {
                        $html.="<td></td>";
                    }

                    //DIAS SIN FACTURAR
                    if($diasTranscurridos > 0){
                        $html.="<td>".$diasTranscurridos."</td>";
                    } else {
                        $html.="<td></td>";
                    }
                    
                $html.="</tr>";
            }
            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return[
                'html' => $html,
                'unidadNeogico' => $modelUnidadNegocio->DESC_UNIDAD_NEGOCIO,
                'cliente' => $modelCliente->NOMBRE_CLIENTE,
                'mes' => $arrayMeses[$MES],
            ];
        }
    }

    /**
     * Finds the TblClientes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblClientes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblClientes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
