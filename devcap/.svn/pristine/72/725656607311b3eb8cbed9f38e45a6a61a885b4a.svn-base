<?php

namespace app\controllers;

use Yii;
use app\models\TblProyectos;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\tblBitComentariosSeguimientoProy;
use app\models\tblcandidatos;
use app\models\tblvacantes;
use app\models\SubirArchivo;
use app\models\TblBitUnidadNegocioAsig;
use app\models\TblCatTarifas;
use app\models\TblTipoProyecto;
use app\models\TblCatDireccion;
use app\models\TblCatSubdireccion;
use app\models\TblClientes;
use app\models\TblFaseProyecto;
use app\models\TblCatUnidadesNegocio;
use app\models\TblCatFabricaDesarrollo;
use app\models\TblCatEstatusProyectos;
use app\models\TblCatAplicativo;
use app\models\tblcatubicaciones;
use app\models\tblcatcontactos;
use app\models\TblDocumentos;
use app\models\TblFacturas;
use app\models\TblFolios;
use app\models\TblFase;
use app\models\TblEmpleados;
use app\models\TblCatTipoProyecto;
use app\models\TblGrupoFases;
use app\models\tblproyectosfases;
use app\models\TblDocumentosProyectos;
use app\models\TblProyectosEquipoAsignado;
use app\models\TblProyectosPeriodos;
use app\models\tblPerfilEmpleados;
use app\models\TblCatCriterio;
use app\models\TblCatDominio;
use app\models\TblCatTecnologiaProyectos;
use app\models\TblBitBlsDocs;


class ProyectosController extends Controller
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


    public function actionIndex()
    {
        $request = Yii::$app->request;
        $tamanio_pagina= 20; 

        $modelResponsableOP = (new \yii\db\Query)
        ->select([
        "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
        'e.PK_EMPLEADO',
        ])
        ->from('tbl_empleados e')

        ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
        ->where(['p.FK_AREA'=>'5'])->andwhere(['or',['p.FK_ESTATUS_RECURSO'=>'3'],['p.FK_ESTATUS_RECURSO'=>'5']])
        ->orderBy('nombre_emp DESC')->all();   
        $urlview = Url::to(["proyectos/view"]);
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
            $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['IN','PK_UNIDAD_NEGOCIO',$unidadesNegocioValidas])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }else{
            $unidadNegocioEmpleado = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            $unidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->select(['PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO'])->where(['PK_UNIDAD_NEGOCIO' => $unidadNegocioEmpleado])->asArray()->all(),'PK_UNIDAD_NEGOCIO','DESC_UNIDAD_NEGOCIO');
        }

        if (Yii::$app->request->isAjax) 
        {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $nombre         =(!empty($post['nombre']))? "'".trim($post['nombre'])."'":'NULL'; 
            $folio          =(!empty($post['folio']))? "'".trim($post['folio'])."'":'NULL'; 
            $contacto       =(!empty($post['contacto']))? trim($post['contacto']):'NULL'; 
            $faseProyecto   =(!empty($post['faseProyecto']))? trim($post['faseProyecto']):'NULL'; 
            $liderfase      =(!empty($post['liderfase']))? trim($post['liderfase']):'NULL';
            $datosEstatus   =(!empty($post['datosEstatus']))? trim($post['datosEstatus']):'NULL'; 
            $numdocumento   =(!empty($post['numdocumento']))? "'".trim($post['numdocumento'])."'":'NULL'; 
            $responsableop  =(!empty($post['responsableop']))? trim($post['responsableop']):'NULL'; 
            //$fk_unidad_negocio  =(!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):''; 
            $pagina         =(!empty($post['page']))? trim($post['page']):'';

            if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $fk_unidad_negocio =(!empty($post['unidadNegocio']))? "'".trim($post['unidadNegocio'])."'":'NULL';
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $fk_unidad_negocio = '';
                foreach ($unidadesNegocioValidas as $key => $value) {
                    $fk_unidad_negocio .= $value.',';
                }
                $fk_unidad_negocio = trim($fk_unidad_negocio,',');
                $fk_unidad_negocio = "'".((!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):$fk_unidad_negocio)."'";
            }else{
                $fk_unidad_negocio = "'".puser_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO']."'";
            }

            if(empty($pagina)){
                $pagina=0;
            }else{
                $pagina= $pagina-1;
            }

            $connection = \Yii::$app->db;
            $total_registros = $connection->createCommand("CALL SP_PROYECTOS_INDEX_SELECT($nombre,$folio,$contacto,$faseProyecto,$liderfase,$datosEstatus,$numdocumento,$responsableop,$fk_unidad_negocio,NULL,NULL)")->queryAll();
            $total_registros = $total_registros[0]['CANTIDAD'];
            $total_paginas= ceil($total_registros/$tamanio_pagina);
            if($total_registros<=$tamanio_pagina){
                $pagina=0;
            }
            $limit = $tamanio_pagina;
            $offset = $pagina*$tamanio_pagina;
            $html='';

            $datosProyectos = [];
            if($total_registros > 0){
                $datosProyectos = $connection->createCommand("CALL SP_PROYECTOS_INDEX_SELECT($nombre,$folio,$contacto,$faseProyecto,$liderfase,$datosEstatus,$numdocumento,$responsableop,$fk_unidad_negocio,$limit,$offset)")->queryAll();
            }

            $connection->close();

            $bgColor1 = '#FFFFFF';
            $bgColor2 = '#F5F5F5';
            $contador = 0;
            $html='';
            foreach ($datosProyectos as $array) {
                $i = 0;
                $fasesProyecto = '';
                    if ($array['FASE1'] != '')
                    {
                        $fasesProyecto.= 'AF-';
                    }
                    if ($array['FASE2'] != '')
                    {
                        $fasesProyecto.= 'DD-';
                    }
                    if ($array['FASE3'] != '')
                    {
                        $fasesProyecto.= 'CO-';
                    }
                    if ($array['FASE4'] != '')
                    {
                        $fasesProyecto.= 'PU-';
                    }
                    $fasesProyecto= trim($fasesProyecto, '-');
                $contador++;
                
                if($contador%2==0){
                    $bgColor = $bgColor2;
                } else {
                    $bgColor = $bgColor1;
                }

                $fechaFinValidada = $array['FIN_DE_PROYECTO'] != '1969-12-31' ? $array['FIN_DE_PROYECTO'] : ''; 

                $html.= '<tr>';
                    $url = $urlview."?id=".$array['PK_PROYECTO'];

                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['FOLIO']."</td>";
                    $html.= "
                            <td style='background-color: $bgColor; text-align: center;' class='border-top name'>
                             <a href= '$url' class = 'refProspectos'>
                            ".$array['NOMBRE_PROYECTO']."                               
                            </a>   
                        ";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['NOMBRE_CORTO']."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['RESPONSABLE_OP']."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['LIDER_PROYECTO']."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['ESTATUS']."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$fasesProyecto."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['CONTACTO']."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['ODC']."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['AP']."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['FACTURA']."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['FASE_EN_EJECUCION']."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['HORAS_TOTALES']."</td>";
                    $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['SOLICITUD']."</td>";
                    if($fechaFinValidada != ''){
                        $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".transform_date($fechaFinValidada, 'd/m/Y')."</td>";
                    }else{
                        $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'></td>";
                    }
                    /*$html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['CONTACTO']."</td>";*/
                //  $html.= "<td style='background-color: $bgColor; $pointer' class='border-top'>$comentariosCancelacion</td>";
                $html.= '</tr>';
                $i++;    
            }

            $cantProyectos = count($datosProyectos);
            if($cantProyectos == 0){
                $html.="<tr>";
                    $html.='<td colspan="14" rowspan="3" style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.="</tr>";
            }

            return [
                'data'=>$html,
                'total_paginas'=>$total_paginas,
                'pagina'=>$pagina,
                'total_registros'=>$total_registros,
                'datosProyectos'=>$datosProyectos,
                'post'=>$post,
            ];
        }
        else{

            return $this->render('index', [
                'modelResponsableOP' => $modelResponsableOP,
                'unidadNegocio' => $unidadNegocio,

            ]);
        }
    }

    public function actionIndex_asociarbolsa()
    {
        if (Yii::$app->request->isAjax) 
        {
            $num_registros_empleado = 0;
            $data = Yii::$app->request->post();
            $filtroProyectos = '-1';
            $post=null;
            parse_str($data['data'],$post);
            
            $nombre = (isset($post['nombre']))?$post['nombre']:'';
            $fabrica_desarrollo   = (isset($post['fabrica_desarrollo']))?$post['fabrica_desarrollo']:'';
            $razon_social   = (isset($post['razon_social']))?$post['razon_social']:'';
            $pagina       = (!empty($post['page']))? trim($post['page']):'';
            $bolsa_asignacion= (!empty($post['bolsa_asignacion']))?$post['bolsa_asignacion']:[];
            foreach ($bolsa_asignacion as $key => $value) {
                $filtroProyectos = $filtroProyectos.','.$value;
            }
            
            $stringFiltro = '';
            $stringNombre = '';
            $stringEmpresa = '';
            $stringFabricaDesarrollo = '';
            
            if($nombre){
                $stringFiltro = ' AND tp.DESC_PROYECTO LIKE "%'.$nombre.'%" OR tp.NOMBRE_CORTO_PROYECTO LIKE "%'.$nombre.'%"';
            }
            if($fabrica_desarrollo){
                $stringFiltro = ' AND tp.FK_FABRICA_DESARROLLO IN ('.$fabrica_desarrollo.')';
            }
            if($razon_social){
                $stringFiltro = $stringFiltro.' AND tp.FK_UNIDAD_NEGOCIO IN ('.$razon_social.')';
            }

            $tamanio_pagina=9;
            $pagina       =(!empty($data['pagina']))? trim($data['pagina']):'';
            $connection = \Yii::$app->db;
            $dataProyectos =  $connection->createCommand("select  
                                tp.PK_PROYECTO,
                                tf.FOLIO,
                                tp.NOMBRE_CORTO_PROYECTO,
                                tp.desc_proyecto AS DESC_PROYECTO,
                                tp.TARIFA,
                                tp.FK_TARIFA,
                                tbc.NOMBRE_CLIENTE,
                                tbc.HORAS_ASIGNACION,
                                tu.INICIALES AS RESPONSABLE_OP,
                                tp.FK_FABRICA_DESARROLLO,
                                tp.FK_UNIDAD_NEGOCIO,
                                FASE1.FECHA_INI_COMPROMISO_PLAN AS FECHA_INI_FASE1,
                                FASE1.FECHA_FIN_COMPROMISO_PLAN AS FECHA_FIN_FASE1,
                                FASE2.FECHA_INI_COMPROMISO_PLAN AS FECHA_INI_FASE2,
                                FASE3.FECHA_FIN_COMPROMISO_PLAN AS FECHA_FIN_FASE2,
                                FASE3.FECHA_INI_COMPROMISO_PLAN AS FECHA_INI_FASE3,
                                FASE3.FECHA_FIN_COMPROMISO_PLAN AS FECHA_FIN_FASE3,
                                FASE4.FECHA_INI_COMPROMISO_PLAN AS FECHA_INI_FASE4,
                                FASE4.FECHA_FIN_COMPROMISO_PLAN AS FECHA_FIN_FASE4,
                                tcef.DESC_ESTATUS_FASE,
                                CONCAT(tcc.NOMBRE_CONTACTO,' ',tcc.APELLIDO_PAT,' ',tcc.APELLIDO_MAT) AS CONTACTO
                                FROM
                                tbl_proyectos tp
                                LEFT JOIN tbl_usuarios tu
                                ON tu.fk_empleado = tp.FK_RESPONSABLE_OP
                                LEFT JOIN tbl_cat_contactos tcc
                                ON tp.FK_CONTACTO = tcc.PK_CONTACTO
                                LEFT JOIN tbl_folios tf
                                ON tp.FK_FOLIO = tf.PK_FOLIO
                                LEFT JOIN tbl_proyectos_fases FASE1
                                ON tp.PK_PROYECTO = FASE1.FK_PROYECTO and FASE1.FK_CAT_FASE = 1
                                LEFT JOIN tbl_proyectos_fases FASE2
                                ON tp.PK_PROYECTO = FASE2.FK_PROYECTO and FASE2.FK_CAT_FASE = 2
                                LEFT JOIN tbl_proyectos_fases FASE3
                                ON tp.PK_PROYECTO = FASE3.FK_PROYECTO and FASE3.FK_CAT_FASE = 3
                                LEFT JOIN tbl_proyectos_fases FASE4
                                ON tp.PK_PROYECTO = FASE4.FK_PROYECTO and FASE4.FK_CAT_FASE = 4
                                LEFT JOIN tbl_cat_estatus_fases tcef
                                ON FASE1.FK_ESTATUS_FASE = tcef.PK_ESTATUS_FASE
                                LEFT JOIN tbl_clientes tbc
                                ON tp.FK_CLIENTE = tbc.PK_CLIENTE
                                INNER JOIN tbl_cat_fabrica_desarrollo tcfd
                                ON tcfd.PK_FABRICA = tp.FK_FABRICA_DESARROLLO
                                INNER JOIN tbl_cat_razon_social tcrs
                                ON tcrs.PK_RAZON_SOCIAL = tp.FK_UNIDAD_NEGOCIO
                                WHERE tp.PK_PROYECTO NOT IN (".$filtroProyectos.") ".$stringFiltro."
                                group by tp.PK_PROYECTO")->queryAll();
            
            $connection->close();    

            $query_cont = count($dataProyectos);
            if($query_cont<$tamanio_pagina)
            {
                $pagina=1;
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return[
                'data' => $data,
                'post' => $post,
                'filtroProyectos' => $filtroProyectos,
                'bolsa_asignacion' => $bolsa_asignacion,
                'dataProyectos' => $dataProyectos,
                'pagina'        => $pagina,
                'total_paginas' => ceil($query_cont / $tamanio_pagina),
                'total_registros' => $query_cont,
            ];
            
        }
    }

    public function actionPeriodos_bolsa()
    {
        $post= [];
        $post= Yii::$app->request->post();

        $connection = \Yii::$app->db;
        $periodosProyectos =  $connection->createCommand("select 
                                tpf.FK_PROYECTO,
                                tpp.PK_PROYECTO_PERIODO,
                                tpp.FK_DOCUMENTO_ODC,
                                tdpodc.NUM_DOCUMENTO AS NUM_ODC,
                                tdpodc.NUM_REFERENCIA AS NUMREF_ODC,
                                tdphde.NUM_DOCUMENTO AS NUM_HDE,
                                tdphde.NUM_REFERENCIA AS NUMREF_HDE,
                                tpp.FK_BOLSA,
                                tpp.FECHA_INI,
                                tpp.FECHA_FIN,
                                tcf.DESC_FASE,
                                tpp.HORAS_ODC,
                                tpp.HORAS_HDE,
                                tpp.HORAS_FACTURA,
                                tpf.ESFUERZO_COMPROMISO_PLAN,
                                tp.FK_TARIFA,
                                tp.TARIFA as TARIFA_PROYECTO
                                from tbl_proyectos_periodos tpp
                                left join tbl_documentos_proyectos tdpodc
                                on tpp.FK_DOCUMENTO_ODC = tdpodc.PK_DOCUMENTO
                                left join tbl_documentos_proyectos tdphde
                                on tpp.FK_DOCUMENTO_HDE = tdphde.PK_DOCUMENTO
                                inner join tbl_proyectos_fases tpf
                                on tpp.FK_PROYECTO_FASE = tpf.PK_PROYECTO_FASE
                                inner join tbl_proyectos tp
                                on tp.PK_PROYECTO = tpf.FK_PROYECTO
                                inner join tbl_cat_fase tcf
                                on tpf.FK_CAT_FASE = tcf.PK_CAT_FASE
                                where tpf.FK_PROYECTO = ".$post['id_asignacion']."
                                and tpp.FECHA_FIN < DATE_ADD(NOW(), INTERVAL 30 DAY)
                                order by tpp.FECHA_INI asc")->queryAll();

        $connection->close();
       \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $res = [
            'periodosProyectos'=>$periodosProyectos,
            'post'=>$post
        ];
        return $res;
    }

    public function actionView($id)
    {

        $model = $this->findModel($id);
        $modelResponsableOP = TblEmpleados::find()->where(['PK_EMPLEADO' => $model->FK_RESPONSABLE_OP])->limit(1)->one();
        $modelUnidadNegocio = TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO' => $model->FK_UNIDAD_NEGOCIO])->limit(1)->one();
        $modelFabricaDesarrollo = TblCatFabricaDesarrollo::find()->where(['PK_FABRICA' => $model->FK_FABRICA_DESARROLLO])->limit(1)->one();
        $modelClientes = TblClientes::find()->where(['PK_CLIENTE' => $model->FK_CLIENTE])->limit(1)->one();
        $modelAplicativo = TblCatAplicativo::find()->where(['PK_APLICATIVO' => $model->FK_APLICATIVO])->limit(1)->one();
        $modelEstatusProyecto = TblCatEstatusProyectos::find()->where(['PK_ESTATUS_PROY' => $model->FK_ESTATUS_PROY])->limit(1)->one();
        $modelCriterio = TblCatCriterio::find()->where(['PK_CRITERIO' => $model->FK_CRITERIO])->limit(1)->one();
        $modelDominio   = TblCatDominio::find()->where(['PK_DOMINIO' => $model->FK_DOMINIO])->limit(1)->one();
        $modelTecnologia  = TblCatTecnologiaProyectos::find()->where(['PK_TECNOLOGIA' => $model->FK_TECNOLOGIA])->limit(1)->one();
        $modelFolio =  TblFolios::findOne($model->FK_FOLIO);
        $tarifas = TblCatTarifas::find()->all();
        $faseAutMostrar = '';

        if(!empty($model->FK_CONTACTO_PM)){
            $modelContactoPM = TblCatContactos::find()->where(['PK_CONTACTO' => $model->FK_CONTACTO_PM])->limit(1)->one();
        }
        if(!empty($model->FK_CONTACTO_DIRECTOR)){
            $modelContactoDirector = TblCatContactos::find()->where(['PK_CONTACTO' => $model->FK_CONTACTO_DIRECTOR])->limit(1)->one();
        }
        $modelContacto = TblCatContactos::find()->where(['PK_CONTACTO' => $model->FK_CONTACTO])->limit(1)->one();
        if(!empty($model->FK_DIRECCION)){
            $modelDireccion = TblCatDireccion::find()->where(['PK_DIRECCION' => $model->FK_DIRECCION])->limit(1)->one();
        }
        if(!empty($model->FK_SUBDIRECCION)){
            $modelSubDireccion = TblCatSubdireccion::find()->where(['PK_SUBDIRECCION' => $model->FK_SUBDIRECCION])->limit(1)->one();
        }
        if(!empty($model->FK_CRITERIO)){
            $modelCriterio = TblCatCriterio::find()->where(['PK_CRITERIO' => $model->FK_CRITERIO])->limit(1)->one();
        }
        if(!empty($model->FK_DOMINIO)){
            $modelDominio   = TblCatDominio::find()->where(['PK_DOMINIO' => $model->FK_DOMINIO])->limit(1)->one();
        }
        if(!empty($model->FK_TECNOLOGIA)){
            $modelTecnologia  = TblCatTecnologiaProyectos::find()->where(['PK_TECNOLOGIA' => $model->FK_TECNOLOGIA])->limit(1)->one();
        }
        $modelTipoProyecto = TblCatTipoProyecto::find()->where(['PK_TIPO_PROYECTO' => $model->FK_TIPO_PROYECTO])->limit(1)->one();
        $modelUbicacion = TblCatUbicaciones::find()->where(['PK_UBICACION' => $model->FK_UBICACION])->limit(1)->one();
        //$modelFasesHabilitar = TblProyectosFases::find()->select(['FK_CAT_FASE'])->where(['FK_PROYECTO' => $model->PK_PROYECTO])->asArray()->column();
        $modelFasesHabilitar = TblProyectosFases::find()->select(['FK_CAT_FASE', 'FECHA_INI_COMPROMISO_PLAN', 'FECHA_FIN_COMPROMISO_PLAN'])->where(['FK_PROYECTO' => $model->PK_PROYECTO])->asArray()->all();

        $datosFolio = TblFolios::find()->select(['NOMBRE_CORTO_FOLIO'])->where(['PK_FOLIO' => $model->FK_FOLIO])->limit(1)->asArray()->one();
        $model->FK_RESPONSABLE_OP = $modelResponsableOP->NOMBRE_EMP.' '.$modelResponsableOP->APELLIDO_PAT_EMP.' '.$modelResponsableOP->APELLIDO_MAT_EMP;
        $model->FK_UNIDAD_NEGOCIO = $modelUnidadNegocio->DESC_UNIDAD_NEGOCIO;
        $model->FK_FABRICA_DESARROLLO = $modelFabricaDesarrollo->DESC_FABRICA;
        $model->FK_CLIENTE = $modelClientes->NOMBRE_CLIENTE;
        $model->FK_APLICATIVO = $modelAplicativo->DESC_APLICATIVO;
        $model->FK_ESTATUS_PROY = $modelEstatusProyecto->DESC_ESTATUS_PROY;
        if(!empty($model->FK_CONTACTO_PM)){
            $model->FK_CONTACTO_PM = $modelContactoPM->NOMBRE_CONTACTO.' '.$modelContactoPM->APELLIDO_PAT.' '.$modelContactoPM->APELLIDO_MAT;
        }else{
            $model->FK_CONTACTO_PM = "NO DEFINIDO";
        }

        if(!empty($model->FK_CONTACTO_DIRECTOR)){
            $model->FK_CONTACTO_DIRECTOR = $modelContactoDirector->NOMBRE_CONTACTO.' '.$modelContactoDirector->APELLIDO_PAT.' '.$modelContactoDirector->APELLIDO_MAT; 
        }else{
            $model->FK_CONTACTO_DIRECTOR = "NO DEFINIDO";
        }

        $model->FK_CONTACTO = $modelContacto->NOMBRE_CONTACTO.' '.$modelContacto->APELLIDO_PAT.' '.$modelContacto->APELLIDO_MAT; 
        if(!empty($model->FK_DIRECCION)){
            $model->FK_DIRECCION = $modelDireccion->NOMBRE_DIRECCION;
        }else{
            $model->FK_DIRECCION = "NO DEFINIDO";
        }

        if(!empty($model->FK_SUBDIRECCION)){
            $model->FK_SUBDIRECCION = $modelSubDireccion->NOMBRE_SUBDIRECCION;
        }else{
            $model->FK_SUBDIRECCION = "NO DEFINIDO";
        }

        if(!empty($model->FK_CRITERIO)){
            $model->FK_CRITERIO = $modelCriterio->DESC_CRITERIO;
        }else{
            $model->FK_CRITERIO = "NO DEFINIDO";
        }

        if(!empty($model->FK_DOMINIO)){
            $model->FK_DOMINIO = $modelDominio->DESC_DOMINIO;
        }else{
            $model->FK_DOMINIO = "NO DEFINIDO";
        }

        if(!empty($model->FK_TECNOLOGIA)){
            $model->FK_TECNOLOGIA = $modelTecnologia->DESC_TECNOLOGIA;
        }else{
            $model->FK_TECNOLOGIA = "NO DEFINIDO";
        }
       

        $model->FK_TIPO_PROYECTO = $modelTipoProyecto->DESC_TIPO_PROYECTO;
        $model->FK_UBICACION = $modelUbicacion->DESC_UBICACION;
        $modeloFases = new tblproyectosfases();
        $modeloProyectoEquipoAsignado = new TblProyectosEquipoAsignado();
        $arrayEquipoAsignado = '';
        $connection = \Yii::$app->db;
        //$transaction = $connection->beginTransaction();//Inicio del funcionamiento de la 'Transacción' con su respectivo try/catch
        
        //try{
            $modeloDocumentosEstCom = new TblDocumentosProyectos();
            $modeloDocumentosEstInt = new TblDocumentosProyectos();
            $modeloDocumentosPPComercial = new TblDocumentosProyectos();
            $modeloDocumentosPPInterno = new TblDocumentosProyectos();

            $modelSubirDocEstCom = new SubirArchivo();
            $modelSubirDocEstCom->extensions = 'doc, docx, xls, xlsx, mpp';
            $modelSubirDocEstCom->noRequired = true;

            $modelSubirDocEstInt = new SubirArchivo();
            $modelSubirDocEstInt->extensions = 'doc, docx, xls, xlsx, mpp';
            $modelSubirDocEstInt->noRequired = true;

            $modelSubirDocPPComercial = new SubirArchivo();
            $modelSubirDocPPComercial->extensions = 'doc, docx, xls, xlsx, mpp';
            $modelSubirDocPPComercial->noRequired = true;

            $modelSubirDocPPInterno = new SubirArchivo();
            $modelSubirDocPPInterno->extensions = 'doc, docx, xls, xlsx, mpp';
            $modelSubirDocPPInterno->noRequired = true;

            /*Documentos ODC, HDE Y FACTURA*/
            $modelSubirDocODC = new SubirArchivo();
            $modelSubirDocODC->extensions = 'pdf, docx';
            $modelSubirDocODC->noRequired = true;
            $modelDocumentoODC = new TblDocumentosProyectos();

            $modelSubirDocHDE = new SubirArchivo();
            $modelSubirDocHDE->extensions = 'pdf, docx';
            $modelSubirDocHDE->noRequired = true;
            $modelDocumentoHDE = new TblDocumentosProyectos();

            $modelPeriodos = new TblProyectosPeriodos();

            /*Apartado para Documentos*/
            if ($modeloFases->load(Yii::$app->request->post()) || $modeloProyectoEquipoAsignado->load(Yii::$app->request->post())) {
                
                $dataget = Yii::$app->request->get();
                $data = Yii::$app->request->post(); 
                //$data = Yii::$app->request->post();
                
                $consultaFase =  $connection->createCommand("select * 
                                        FROM tbl_proyectos_fases tf
                                        WHERE tf.FK_PROYECTO = ".$dataget['id']." AND tf.FK_CAT_FASE = ".$data['fk_fase_actual'])->queryOne();
                
                ////////////////////////////////////////////////////////////////////////////////////////////
                //                                 PESTAÑA PLANEACION                                     //
                ////////////////////////////////////////////////////////////////////////////////////////////
                if($data['formulario_pestania'] == 'formulario_planeacion'){

                    $modelSubirDocEstCom->file = UploadedFile::getInstance($modelSubirDocEstCom, '[10]file');
                    if (!empty($modelSubirDocEstCom->file)) {
                        $fechaHoraHoyEstCom = date('YmdHis');
                        $rutaGuardadoEstCom = '../uploads/ProyectosDocumentos/';
                        $nombreFisicoEstCom = $fechaHoraHoyEstCom.'_'.quitar_acentos(utf8_decode($modelSubirDocEstCom->file->basename));
                        $nombreBDEstCom = quitar_acentos(utf8_decode($modelSubirDocEstCom->file->basename));
                        $extensionEstCom = $modelSubirDocEstCom->upload($rutaGuardadoEstCom,$nombreFisicoEstCom);
                        $rutaDocEstCom = '/uploads/ProyectosDocumentos/';
                        $pk_documento_estcom='';
                    }else{
                        $pk_documento_estcom= (isset($data['pk_documento_estcom'])&&!empty($data['pk_documento_estcom']))?$data['pk_documento_estcom']:'';
                    }

                    $modelSubirDocEstInt->file = UploadedFile::getInstance($modelSubirDocEstInt, '[11]file');
                    if (!empty($modelSubirDocEstInt->file)) {
                        $fechaHoraHoyEstInt = date('YmdHis');
                        $rutaGuardadoEstInt = '../uploads/ProyectosDocumentos/';
                        $nombreFisicoEstInt = $fechaHoraHoyEstInt.'_'.quitar_acentos(utf8_decode($modelSubirDocEstInt->file->basename));
                        $nombreBDEstInt = quitar_acentos(utf8_decode($modelSubirDocEstInt->file->basename));
                        $extensionEstInt = $modelSubirDocEstInt->upload($rutaGuardadoEstInt,$nombreFisicoEstInt);
                        $rutaDocEstInt = '/uploads/ProyectosDocumentos/';
                        $pk_documento_estint='';
                    }else{
                        $pk_documento_estint= (isset($data['pk_documento_estint'])&&!empty($data['pk_documento_estint']))?$data['pk_documento_estint']:'';
                    }

                    $modelSubirDocPPComercial->file = UploadedFile::getInstance($modelSubirDocPPComercial, '[12]file');
                    if (!empty($modelSubirDocPPComercial->file)) {
                        $fechaHoraHoyPPComercial = date('YmdHis');
                        $rutaGuardadoPPComercial = '../uploads/ProyectosDocumentos/';
                        $nombreFisicoPPComercial = $fechaHoraHoyPPComercial.'_'.quitar_acentos(utf8_decode($modelSubirDocPPComercial->file->basename));
                        $nombreBDPPComercial = quitar_acentos(utf8_decode($modelSubirDocPPComercial->file->basename));
                        $extensionPPComercial = $modelSubirDocPPComercial->upload($rutaGuardadoPPComercial,$nombreFisicoPPComercial);
                        $rutaDocPPComercial = '/uploads/ProyectosDocumentos/';
                        $pk_documento_ppcomercial='';
                    }else{
                        $pk_documento_ppcomercial= (isset($data['pk_documento_ppcomercial'])&&!empty($data['pk_documento_ppcomercial']))?$data['pk_documento_ppcomercial']:'';
                    }

                    $modelSubirDocPPInterno->file = UploadedFile::getInstance($modelSubirDocPPInterno, '[13]file');
                    if (!empty($modelSubirDocPPInterno->file)) {
                        $fechaHoraHoyPPInterno = date('YmdHis');
                        $rutaGuardadoPPInterno = '../uploads/ProyectosDocumentos/';
                        $nombreFisicoPPInterno = $fechaHoraHoyPPInterno.'_'.quitar_acentos(utf8_decode($modelSubirDocPPInterno->file->basename));
                        $nombreBDPPInterno = quitar_acentos(utf8_decode($modelSubirDocPPInterno->file->basename));
                        $extensionPPInterno = $modelSubirDocPPInterno->upload($rutaGuardadoPPInterno,$nombreFisicoPPInterno);
                        $rutaDocPPInterno = '/uploads/ProyectosDocumentos/';
                        $pk_documento_ppinterno='';
                    }else{
                        $pk_documento_ppinterno= (isset($data['pk_documento_ppinterno'])&&!empty($data['pk_documento_ppinterno']))?$data['pk_documento_ppinterno']:'';
                    }
                    
                    /*$consultaFase =  $connection->createCommand("select * 
                                        FROM tbl_proyectos_fases tf
                                        WHERE tf.FK_PROYECTO = ".$dataget['id']." AND tf.FK_CAT_FASE = ".$data['fk_fase_actual'])->queryOne();*/
                    
                    if($data['postTypePlaneacion'] == 'create' && !$consultaFase){

                        $modeloFases->FK_CAT_FASE = $data['fk_fase_actual'];
                        $modeloFases->FK_PROYECTO = $dataget['id'];
                        $modeloFases->FECHA_INI_COMPROMISO_PLAN = transform_date($data['TblProyectosFases']['FECHA_INI_COMPROMISO_PLAN'],'Y-m-d');
                        $modeloFases->FECHA_FIN_COMPROMISO_PLAN = transform_date($data['TblProyectosFases']['FECHA_FIN_COMPROMISO_PLAN'],'Y-m-d');
                        $modeloFases->ESFUERZO_COMPROMISO_PLAN = $data['TblProyectosFases']['ESFUERZO_COMPROMISO_PLAN'];
                        $modeloFases->FECHA_INI_COMPROMISO_PLAN_I = $data['TblProyectosFases']['FECHA_INI_COMPROMISO_PLAN_I'] != '' ? transform_date($data['TblProyectosFases']['FECHA_INI_COMPROMISO_PLAN_I'],'Y-m-d') : null;
                        $modeloFases->FECHA_FIN_COMPROMISO_PLAN_I = $data['TblProyectosFases']['FECHA_FIN_COMPROMISO_PLAN_I'] != '' ? transform_date($data['TblProyectosFases']['FECHA_FIN_COMPROMISO_PLAN_I'],'Y-m-d') : null;
                        $modeloFases->ESFUERZO_COMPROMISO_PLAN_I = isset($data['TblProyectosFases']['ESFUERZO_COMPROMISO_PLAN_I']) ? $data['TblProyectosFases']['ESFUERZO_COMPROMISO_PLAN_I'] : null;
                        $modeloFases->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modeloFases->save(false);

                        //Se consulta a la fase recién creada para agregarle los fk's de documentos faltantes, se guarda primero parte del regisro para poder obtener la PK_PROYECTO_FASE y en base a esta relacionar los documentos ingresados por el usuario.
                        $modeloFaseCreada =  $connection->createCommand("select * 
                                        FROM tbl_proyectos_fases tf
                                        WHERE tf.FK_PROYECTO = ".$dataget['id']." AND tf.FK_CAT_FASE = ".$data['fk_fase_actual'])->queryOne();

                        //Se crea nueva instancia sobre la variable '$modeloFases' para modificar la fase recien creada y agregarle los documentos dados de alta.
                        $modeloFases = tblproyectosfases::findOne($modeloFaseCreada['PK_PROYECTO_FASE']);

                        if(!empty($modelSubirDocEstCom->file)){
                            $modeloDocumentosEstCom->NOMBRE_DOCUMENTO = $nombreBDEstCom.'.'.$extensionEstCom;
                            $modeloDocumentosEstCom->RUTA_DOCUMENTO = $rutaDocEstCom.$nombreFisicoEstCom.'.'.$extensionEstCom;
                            $modeloDocumentosEstCom->FK_PROYECTO_FASE = $modeloFases['PK_PROYECTO_FASE'];
                            $modeloDocumentosEstCom->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modeloDocumentosEstCom->save(false);
                        }

                        if(!empty($modelSubirDocEstInt->file)){
                            $modeloDocumentosEstInt->NOMBRE_DOCUMENTO = $nombreBDEstInt.'.'.$extensionEstInt;
                            $modeloDocumentosEstInt->RUTA_DOCUMENTO = $rutaDocEstInt.$nombreFisicoEstInt.'.'.$extensionEstInt;
                            $modeloDocumentosEstInt->FK_PROYECTO_FASE = $modeloFases['PK_PROYECTO_FASE'];
                            //$modeloDocumentosEstInt->FK_PROYECTO = $dataget['id'];
                            //$modeloDocumentosEstInt->FK_CAT_FASE = $data['fk_fase_actual'];
                            $modeloDocumentosEstInt->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modeloDocumentosEstInt->save(false);
                        }

                        if(!empty($modelSubirDocPPComercial->file)){
                            $modeloDocumentosPPComercial->NOMBRE_DOCUMENTO = $nombreBDPPComercial.'.'.$extensionPPComercial;
                            $modeloDocumentosPPComercial->RUTA_DOCUMENTO = $rutaDocPPComercial.$nombreFisicoPPComercial.'.'.$extensionPPComercial;
                            $modeloDocumentosPPComercial->FK_PROYECTO_FASE = $modeloFases['PK_PROYECTO_FASE'];
                            //$modeloDocumentosPPComercial->FK_PROYECTO = $dataget['id'];
                            //$modeloDocumentosPPComercial->FK_CAT_FASE = $data['fk_fase_actual'];
                            $modeloDocumentosPPComercial->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modeloDocumentosPPComercial->save(false);
                        }

                        if(!empty($modelSubirDocPPInterno->file)){
                            $modeloDocumentosPPInterno->NOMBRE_DOCUMENTO = $nombreBDPPInterno.'.'.$extensionPPInterno;
                            $modeloDocumentosPPInterno->RUTA_DOCUMENTO = $rutaDocPPInterno.$nombreFisicoPPInterno.'.'.$extensionPPInterno;
                            $modeloDocumentosPPInterno->FK_PROYECTO_FASE = $modeloFases['PK_PROYECTO_FASE'];
                            //$modeloDocumentosPPInterno->FK_PROYECTO = $dataget['id'];
                            //$modeloDocumentosPPInterno->FK_CAT_FASE = $data['fk_fase_actual'];
                            $modeloDocumentosPPInterno->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modeloDocumentosPPInterno->save(false);
                        }
                        
                        $modeloFases->FK_DOC_EST_COM = $modeloDocumentosEstCom->PK_DOCUMENTO;
                        $modeloFases->FK_DOC_EST_INT = $modeloDocumentosEstInt->PK_DOCUMENTO;
                        $modeloFases->FK_DOC_PLAN_PROY_COM = $modeloDocumentosPPComercial->PK_DOCUMENTO;
                        $modeloFases->FK_DOC_PLAN_PROY_INT = $modeloDocumentosPPInterno->PK_DOCUMENTO;
                        $modeloFases->save(false);

                        $this->redirect(['proyectos/view','id'=>$dataget['id']]);
                   
                    }else{
                        
                        /*$consultaFase =  $connection->createCommand("select * 
                                            FROM tbl_proyectos_fases tf
                                            WHERE tf.FK_PROYECTO = ".$dataget['id']." AND tf.FK_CAT_FASE = ".$data['fk_fase_actual'])->queryOne();*/
                        
                        $modeloFases = tblproyectosfases::findOne($consultaFase['PK_PROYECTO_FASE']);
                        
                        if(!empty($modelSubirDocEstCom->file)){
                            $modeloDocumentosEstCom->NOMBRE_DOCUMENTO = $nombreBDEstCom.'.'.$extensionEstCom;
                            $modeloDocumentosEstCom->RUTA_DOCUMENTO = $rutaDocEstCom.$nombreFisicoEstCom.'.'.$extensionEstCom;
                            $modeloDocumentosEstCom->FK_PROYECTO_FASE = $modeloFases['PK_PROYECTO_FASE'];
                            $modeloDocumentosEstCom->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modeloDocumentosEstCom->save(false);
                            $modeloFases->FK_DOC_EST_COM = $modeloDocumentosEstCom->PK_DOCUMENTO;
                        }

                        if(!empty($modelSubirDocEstInt->file)){
                            $modeloDocumentosEstInt->NOMBRE_DOCUMENTO = $nombreBDEstInt.'.'.$extensionEstInt;
                            $modeloDocumentosEstInt->RUTA_DOCUMENTO = $rutaDocEstInt.$nombreFisicoEstInt.'.'.$extensionEstInt;
                            $modeloDocumentosEstInt->FK_PROYECTO_FASE = $modeloFases['PK_PROYECTO_FASE'];
                            $modeloDocumentosEstInt->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modeloDocumentosEstInt->save(false);
                            $modeloFases->FK_DOC_EST_INT = $modeloDocumentosEstInt->PK_DOCUMENTO;
                        }

                        if(!empty($modelSubirDocPPComercial->file)){
                            $modeloDocumentosPPComercial->NOMBRE_DOCUMENTO = $nombreBDPPComercial.'.'.$extensionPPComercial;
                            $modeloDocumentosPPComercial->RUTA_DOCUMENTO = $rutaDocPPComercial.$nombreFisicoPPComercial.'.'.$extensionPPComercial;
                            $modeloDocumentosPPComercial->FK_PROYECTO_FASE = $modeloFases['PK_PROYECTO_FASE'];
                            $modeloDocumentosPPComercial->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modeloDocumentosPPComercial->save(false);
                            $modeloFases->FK_DOC_PLAN_PROY_COM = $modeloDocumentosPPComercial->PK_DOCUMENTO;
                        }

                        if(!empty($modelSubirDocPPInterno->file)){
                            $modeloDocumentosPPInterno->NOMBRE_DOCUMENTO = $nombreBDPPInterno.'.'.$extensionPPInterno;
                            $modeloDocumentosPPInterno->RUTA_DOCUMENTO = $rutaDocPPInterno.$nombreFisicoPPInterno.'.'.$extensionPPInterno;
                            $modeloDocumentosPPInterno->FK_PROYECTO_FASE = $modeloFases['PK_PROYECTO_FASE'];
                            $modeloDocumentosPPInterno->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modeloDocumentosPPInterno->save(false);
                            $modeloFases->FK_DOC_PLAN_PROY_INT = $modeloDocumentosPPInterno->PK_DOCUMENTO;
                        }
                        var_dump($data['TblProyectosFases']['FECHA_INI_COMPROMISO_PLAN']);
                        $modeloFases->FECHA_INI_COMPROMISO_PLAN = !empty($data['TblProyectosFases']['FECHA_INI_COMPROMISO_PLAN'])
                            ? transform_date($data['TblProyectosFases']['FECHA_INI_COMPROMISO_PLAN'],'Y-m-d') : '';
                        $modeloFases->FECHA_FIN_COMPROMISO_PLAN =  !empty($data['TblProyectosFases']['FECHA_FIN_COMPROMISO_PLAN'])
                            ? transform_date($data['TblProyectosFases']['FECHA_FIN_COMPROMISO_PLAN'],'Y-m-d') : null;
                        $modeloFases->ESFUERZO_COMPROMISO_PLAN =  !empty($data['TblProyectosFases']['ESFUERZO_COMPROMISO_PLAN'])
                            ? $data['TblProyectosFases']['ESFUERZO_COMPROMISO_PLAN'] : null;
                        $modeloFases->FECHA_INI_COMPROMISO_PLAN_I =  !empty($data['TblProyectosFases']['FECHA_INI_COMPROMISO_PLAN_I'])
                            ? transform_date($data['TblProyectosFases']['FECHA_INI_COMPROMISO_PLAN_I'],'Y-m-d') : null;
                        $modeloFases->FECHA_FIN_COMPROMISO_PLAN_I =  !empty($data['TblProyectosFases']['FECHA_FIN_COMPROMISO_PLAN_I'])
                            ? transform_date($data['TblProyectosFases']['FECHA_FIN_COMPROMISO_PLAN_I'],'Y-m-d') : null;
                        $modeloFases->ESFUERZO_COMPROMISO_PLAN_I =  !empty($data['TblProyectosFases']['ESFUERZO_COMPROMISO_PLAN_I'])
                            ? $data['TblProyectosFases']['ESFUERZO_COMPROMISO_PLAN_I'] : null;
                        $modeloFases->save(false);

                        $faseAutMostrar = $data['fk_fase_actual'];
                    }


                ////////////////////////////////////////////////////////////////////////////////////////////
                //                             PESTAÑA ASIGNACION DE EQUIPO                               //
                ////////////////////////////////////////////////////////////////////////////////////////////
                }else if($data['formulario_pestania'] == 'formulario_asignacion_equipo'){
                    //Se inserta un nuevo registro en BD al llenar el formulario correspondiente en pantalla y ejecutar el botón 'AGREGAR'
                    /*$consultaFase =  $connection->createCommand("select * 
                                        FROM tbl_proyectos_fases tf
                                        WHERE tf.FK_PROYECTO = ".$dataget['id']." AND tf.FK_CAT_FASE = ".$data['fk_fase_actual'])->queryOne();*/
                    
                    if($data['postTypeAsignacionEquipo'] != "AGREGAR"){
                        //$this->redirect(['proyectos/view','id'=>$dataget['id']]);
                        //Se eliminan los registros seleccionados[el ejecutar el botón 'ELIMINAR'] en pantalla al ejecutar el botón 'GUARDAR'
                        
                        if(isset($data['FKS_ELIMINAR'])){
                            foreach($data['FKS_ELIMINAR'] as $index => $value){
                                $modeloProyectoEquipoAsignado = TblProyectosEquipoAsignado::find()->where(['PK_PROYECTO_EQUIPO_ASIGNADO' => $value])->one();
                                
                                $consultaEmpleadoProyectos =  $connection->createCommand("select pea.FK_EMPLEADO 
                                    FROM tbl_proyectos_equipo_asignado pea 
                                    WHERE pea.FK_EMPLEADO = ".$modeloProyectoEquipoAsignado->FK_EMPLEADO)->queryAll();

                                $num_registros_empleado = count($consultaEmpleadoProyectos);

                                $consultaProyectoFase =  $connection->createCommand("select * 
                                    FROM tbl_proyectos tp
                                    inner join tbl_proyectos_fases tpf
                                    on tp.PK_PROYECTO = tpf.FK_PROYECTO
                                    WHERE tpf.PK_PROYECTO_FASE = ".$modeloProyectoEquipoAsignado->FK_PROYECTO_FASE)->queryOne();

                                $modelUnidadNegocioOrigen = TblBitUnidadNegocioAsig::find()->where(['FK_REGISTRO' => '0'])->andwhere(['FK_EMPLEADO'=>$modeloProyectoEquipoAsignado->FK_EMPLEADO])->andwhere(['MODULO_ORIGEN'=>'EMPLEADOS'])->orderBy('PK_UNIDAD_NEGOCIO_ASIG DESC')->limit(1)->one();

                                /* Si existe un sólo registro del empleado en la tabla de proyectos_equipo_asignado se cambia el estatus del recurso a disponible porque quiere decir que ya no esta en ninguna otra fase de ningún proyecto.
                                Si existe más de un registro no se cambia el estatus ya que este empleado existe registrado en el equipo de alguna otra fase del mismo o de otro proyecto. */
                                if($num_registros_empleado == 1){
                                    //Se cambia el estatus del empleado que se esta eliminando del equipo en la fase del proyecto a 'Disponible'
                                    $modelPerfilEmpleados = tblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $modeloProyectoEquipoAsignado->FK_EMPLEADO])->one();
                                    $modelPerfilEmpleados->FK_ESTATUS_RECURSO = 3;
                                    $modelPerfilEmpleados->save(false);
                                    user_log_bitacora_estatus_empleado($modelPerfilEmpleados->FK_EMPLEADO, $modelPerfilEmpleados->FK_ESTATUS_RECURSO);
                                    user_log_bitacora_unidad_negocio($modelPerfilEmpleados->FK_EMPLEADO,$modelUnidadNegocioOrigen['FK_UNIDAD_NEGOCIO'],$consultaProyectoFase['PK_PROYECTO']);
                                }
                                
                                $modeloProyectoEquipoAsignado->delete();

                            }
                        }
                        
                        //Se modifican los registros que quedan en pantalla al ejecutar el botón 'GUARDAR'
                        if(isset($data['PORC_TRABAJO'])){
                            
                            foreach($data['PORC_TRABAJO'] as $index => $value){
                                $modeloProyectoEquipoAsignado = TblProyectosEquipoAsignado::find()->where(['PK_PROYECTO_EQUIPO_ASIGNADO' => $index])->one();
                                $modeloProyectoEquipoAsignado->PORC_TRABAJO = $data['PORC_TRABAJO'][$index];
                                $modeloProyectoEquipoAsignado->FECHA_ESTIMADA_INICIO = !empty($data['fecha_estimada_inicio'][$index]) ? transform_date($data['fecha_estimada_inicio'][$index],'Y-m-d') : null;
                                $modeloProyectoEquipoAsignado->FECHA_ESTIMADA_LIBERACION = !empty($data['fecha_estimada_liberacion'][$index]) ? transform_date($data['fecha_estimada_liberacion'][$index],'Y-m-d') : null;
                                $modeloProyectoEquipoAsignado->FECHA_REAL_INICIO = !empty($data['fecha_real_inicio'][$index]) ? transform_date($data['fecha_real_inicio'][$index],'Y-m-d') : null;
                                $modeloProyectoEquipoAsignado->FECHA_REAL_LIBERACION = !empty($data['fecha_real_liberacion'][$index]) ? transform_date($data['fecha_real_liberacion'][$index],'Y-m-d') : null;
                                $modeloProyectoEquipoAsignado->save(false);
                            }
                        }
                        $modeloFases = tblproyectosfases::find()->where(['FK_PROYECTO' => $dataget['id']])->andWhere(['FK_CAT_FASE' => $data['fk_fase_actual']])->one();
                        $modeloFases->URL_TEAM_DASHBOARD = $data['TblProyectosFases']['URL_TEAM_DASHBOARD'];
                        $modeloFases->save(false);
                        //dd($modelPerfilEmpleados);

                    }

                    $faseAutMostrar = $data['fk_fase_actual'];
                ////////////////////////////////////////////////////////////////////////////////////////////
                //                                  PESTAÑA SEGUIMIENTO                                   //
                ////////////////////////////////////////////////////////////////////////////////////////////
                }else if($data['formulario_pestania'] == 'formulario_seguimiento'){

                $modeloFases = tblproyectosfases::findOne($consultaFase['PK_PROYECTO_FASE']);
                
                $modeloFases->FK_ESTATUS_FASE = $data['estatusFase'];
                $modeloFases->FECHA_INI_REAL = !empty($data['TblProyectosFases']['FECHA_INI_REAL']) ? transform_date($data['TblProyectosFases']['FECHA_INI_REAL'],'Y-m-d') : null;
                $modeloFases->FECHA_FIN_REAL = !empty($data['TblProyectosFases']['FECHA_FIN_REAL']) ? transform_date($data['TblProyectosFases']['FECHA_FIN_REAL'],'Y-m-d') : null;
                $modeloFases->ESFUERZO_REAL = $data['TblProyectosFases']['ESFUERZO_REAL'];
                $modeloFases->AVANCE_REAL = $data['TblProyectosFases']['AVANCE_REAL'];
                $modeloFases->AVANCE_COMPROMETIDO = $data['TblProyectosFases']['AVANCE_COMPROMETIDO'];
                $modeloFases->COSTO_ACTUAL = $data['TblProyectosFases']['COSTO_ACTUAL'] != ''?$data['TblProyectosFases']['COSTO_ACTUAL']:0.00;
                //$modeloFases->COMENTARIOS = $data['comentariosFactura'];
                $modeloFases->save(false);
                if(!empty($data['PORC_TRABAJO'])){

                    foreach($data['PORC_TRABAJO'] as $index => $value){
                        $modeloProyectoEquipoAsignado = TblProyectosEquipoAsignado::find()->where(['PK_PROYECTO_EQUIPO_ASIGNADO' => $index])->one();
                        $modeloProyectoEquipoAsignado->PORC_TRABAJO = $value;
                        $modeloProyectoEquipoAsignado->save(false);
                    }

                    foreach($data['HORAS_INVERTIDAS'] as $index => $value){
                        $modeloProyectoEquipoAsignado = TblProyectosEquipoAsignado::find()->where(['PK_PROYECTO_EQUIPO_ASIGNADO' => $index])->one();
                        $modeloProyectoEquipoAsignado->HORAS_INVERTIDAS = $value;
                        $modeloProyectoEquipoAsignado->save(false);
                    }
                    foreach($data['COSTO_PROYECTO_AL_MOMENTO'] as $index => $value){
                        $modeloProyectoEquipoAsignado = TblProyectosEquipoAsignado::find()->where(['PK_PROYECTO_EQUIPO_ASIGNADO' => $index])->one();
                        $modeloProyectoEquipoAsignado->COSTO_PROYECTO_AL_MOMENTO = $value;
                        $modeloProyectoEquipoAsignado->save(false);
                    }
                }

                if($data['comentariosSeguimientoProyecto'] != null){
                    $modelBitComentariosSeguimientoProy = new tblBitComentariosSeguimientoProy();
                    $modelBitComentariosSeguimientoProy->FK_PROYECTO_FASE = $consultaFase['PK_PROYECTO_FASE'];
                    $modelBitComentariosSeguimientoProy->COMENTARIOS = $data['comentariosSeguimientoProyecto'];
                    $modelBitComentariosSeguimientoProy->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelBitComentariosSeguimientoProy->FECHA_REGISTRO = date('Y-m-d H:i:s'); 
                    $modelBitComentariosSeguimientoProy->save(false);
                }

                $faseAutMostrar = $data['fk_fase_actual'];
                /////////////////////////////////////////////////////////////////////////////////////////////
                //                               PESTAÑA CARGAR DOCUMENTO                                 //
                ////////////////////////////////////////////////////////////////////////////////////////////
                }else if($data['formulario_pestania'] == 'formulario_cargar_documentos'){
                }else{
                    var_dump("Oops! Ocurrio un problema con su petición");
                    false;
                }

                
                $connection->close();
                return $this->redirect(['proyectos/view','id'=>$id]);
                }
                
                //$transaction->commit();//Si todo el proceso es correcto se ejectua la sentencia commit para guardar todas las operaciones en la base de datos.

            /*}catch(\Exception $e){
                $transaction->rollBack();//Si sucede algún error durante la operación, se ejecuta la sentencia rollback para evitar que hagan modificaciones en base de datos.
                throw $e;
            }//FIN del funcionamiento de la 'Transacción' con su respectivo try/catch
            */

            /*$datosFaseActual =  $connection->createCommand("select * 
                                        FROM tbl_proyectos_fases tf
                                        WHERE tf.FK_PROYECTO = ".$dataget['id']." AND tf.FK_CAT_FASE = ".$data['fk_fase_actual'])->queryOne();*/

        $connection->close();
        return $this->render('view', [
            'model' => $model,
            'modelFasesHabilitar' => $modelFasesHabilitar,
            'idFolio' => $datosFolio['NOMBRE_CORTO_FOLIO'],
            'modelSubirDocEstCom' => $modelSubirDocEstCom,
            'modelSubirDocEstInt' => $modelSubirDocEstInt,
            'modelSubirDocPPComercial' => $modelSubirDocPPComercial,
            'modelSubirDocPPInterno' => $modelSubirDocPPInterno,
            'modeloProyectoEquipoAsignado'=> $modeloProyectoEquipoAsignado,
            'arrayEquipoAsignado' => $arrayEquipoAsignado,
            'modelSubirDocODC' => $modelSubirDocODC,
            'modelDocumentoODC' => $modelDocumentoODC,
            'modelSubirDocHDE' => $modelSubirDocHDE,
            'modelDocumentoHDE' => $modelDocumentoHDE,
            'modelPeriodos' => $modelPeriodos,
            'cat_tarifas' => $tarifas,
            'faseAutMostrar' => $faseAutMostrar,
            'modelFolio'  => $modelFolio,

        ]);
    }


    public function actionCreate()
    {
        
        $model = new TblProyectos();
        $modelFaseProyectos =  new TblFaseProyecto();
        $modelFolio =  new TblFolios();
        $datosDireccion   = ArrayHelper::map(TblCatDireccion::find()->orderBy('NOMBRE_DIRECCION')->asArray()->all(), 'PK_DIRECCION', 'NOMBRE_DIRECCION');
        $fk_folio = '';
        if(isset($_GET['fk_folio']))
        {
            $fk_folio = $_GET['fk_folio'];
            $modelFolio = TblFolios::findOne($fk_folio);

        }
        if ($fk_folio != '')
        {

            $idProyecto = (new \yii\db\Query())
            ->select(["distinct(PK_FOLIO)",
            "FK_CLIENTE",
            "DESC_FOLIO"
            ])
            ->from('tbl_folios')
            ->where(['PK_FOLIO'=>$fk_folio ])
            ->one();

            $model->FK_CLIENTE = $idProyecto['FK_CLIENTE'];
            $model->DESC_PROYECTO = $idProyecto['DESC_FOLIO'];
            $model->FK_FOLIO = $idProyecto['PK_FOLIO'];
        }
        
        $modelResponsableOP = (new \yii\db\Query)
                        ->select([
                            "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
                            'e.PK_EMPLEADO',
                        ])
                        ->from('tbl_empleados e')
                        
                        ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
                        ->where(['p.FK_AREA'=>'5'])->andwhere(['or',['p.FK_ESTATUS_RECURSO'=>'3'],['p.FK_ESTATUS_RECURSO'=>'5']])
                        ->orderBy('nombre_emp DESC')->all();

        if ($model->load(Yii::$app->request->post())){
            $data = Yii::$app->request->post();
            $model->FK_ESTATUS_PROY = 1;
            $model->FECHA_REGISTRO = date('Y-m-d H:i:s');
            $model->save(false);

            $numFasesProy = 1;//Contador para conocer el número de fases que se iteran y por lo tanto se crearán para el proyecto.
            $estatusFase = 1;//Sólo la primer fase del proyecto nacera con estatus de 'ESTIMACION' (valor 1);
           if(count($data['fase']) > 0){
                foreach ($data['fase'] as $key => $value) {
                    if($value == 1){
                        $liderFase = $data['select2analisis'];
                    }elseif($value == 2){
                        $liderFase = $data['select2diseno'];
                    }elseif($value == 3){
                        $liderFase = $data['select2construccion'];
                    }elseif($value == 4){
                        $liderFase = $data['select2pruebas'];
                    }

                    if($numFasesProy == 1){
                        $estatusFase = 1; //Estatus de la fase 'ESTIMACION'
                    }else{
                        $estatusFase = 9; //Estatus de la fase 'PENDIENTE'
                    }

                        $modelFaseProyectos =  new TblProyectosFases();
                        $modelFaseProyectos->FK_CAT_FASE = $value;
                        $modelFaseProyectos->FK_PROYECTO = $model->PK_PROYECTO;
                        $modelFaseProyectos->FK_ESTATUS_FASE = $estatusFase;
                        $modelFaseProyectos->FK_LIDER_PROYECTO = $liderFase;
                        $modelFaseProyectos->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelFaseProyectos->save(false);

                        $numFasesProy = $numFasesProy + 1;
                }
            }

                if($model->FK_TARIFA != NULL )
                {                                 
                $descTarifa = TblCatTarifas::find()->where(['PK_CAT_TARIFA'=>$model->FK_TARIFA])->asArray()->one()['TARIFA'];    
                $model->TARIFA = $descTarifa;
                }
                else
                {
                //dd('Entro Aca');
                //dd($data['TblProyectos']['TARIFA']);
                $model->TARIFA = $data['TblProyectos']['TARIFA'];
                } 
                //$descTarifa = TblCatTarifas::find()->where(['PK_CAT_TARIFA'=>$model->FK_TARIFA])->asArray()->one()['TARIFA'];    
                //$model->TARIFA = $descTarifa;
                $model->FECHA_REGISTRO = date('Y-m-d H:i:s');
                $model->save(false);
        
            return $this->redirect(['create', 'action'=>'insert']);
                } 
                else 
                {
                         return $this->render('create', [
                        'model' => $model,
                        'modelFaseProyectos' => $modelFaseProyectos,
                        'modelResponsableOP'  => $modelResponsableOP,
                        'modelFolio'  => $modelFolio,
                            ]);
                }

    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelAplicativo = TblCatAplicativo::findOne($model->FK_APLICATIVO);
        $modelContactoPM = TblCatContactos::findOne($model->FK_CONTACTO_PM);
        $modelContactoDirector = TblCatContactos::findOne($model->FK_CONTACTO_DIRECTOR);
        $modelContacto = TblCatContactos::findOne($model->FK_CONTACTO);
        $arrayFases = TblProyectosFases::find()->select('FK_CAT_FASE')->where(['FK_PROYECTO' => $id])->column();
        $modelFases = TblProyectosFases::find()->where(['FK_PROYECTO' => $id])->all();
        $modelDireccion = TblCatDireccion::findOne($model->FK_DIRECCION);
        $modelSubDireccion = TblCatSubdireccion::findOne($model->FK_SUBDIRECCION);
        $modelTarifa = TblCatTarifas::findOne($model->FK_TARIFA);
        $modelCriterio = TblCatCriterio::findOne($model->FK_CRITERIO);
        $modelDominio   = TblCatDominio::findOne($model->FK_DOMINIO);
        $modelTecnologia  = TblCatTecnologiaProyectos::findOne($model->FK_TECNOLOGIA);
        $modelFolio =  TblFolios::findOne($model->FK_FOLIO);

        foreach($modelFases as $key => $array){
            //$arrayEmpleados = TblEmpleados::find()->where(['PK_EMPLEADO' => $array['FK_LIDER_PROYECTO']])->limit(1)->asArray()->one();
            //$extra['NOMBRE_LIDER_FASE'][$array['FK_FASE']] = $arrayEmpleados['NOMBRE_EMP'].' '.$arrayEmpleados['APELLIDO_PAT_EMP'].' '.$arrayEmpleados['APELLIDO_MAT_EMP'];
            $extra['NOMBRE_LIDER_FASE'][$array['FK_CAT_FASE']] = $array['FK_LIDER_PROYECTO'];
        }
        $extra['DESC_APLICATIVO'] = isset($modelAplicativo)?$modelAplicativo->DESC_APLICATIVO:'';
        $extra['CONTACTO_PM'] = isset($modelContactoPM)?($modelContactoPM->NOMBRE_CONTACTO.' '.$modelContactoPM->APELLIDO_PAT.' '.$modelContactoPM->APELLIDO_MAT):'';
        $extra['CONTACTO_DIRECTOR'] = isset($modelContactoDirector)?($modelContactoDirector->NOMBRE_CONTACTO.' '.$modelContactoDirector->APELLIDO_PAT.' '.$modelContactoDirector->APELLIDO_MAT):'';
        $extra['CONTACTO'] = isset($modelContacto)?($modelContacto->NOMBRE_CONTACTO.' '.$modelContacto->APELLIDO_PAT.' '.$modelContacto->APELLIDO_MAT):'';
        $extra['FK_DIRECCION'] = isset($modelDireccion)?$modelDireccion->NOMBRE_DIRECCION:'';
        $extra['FK_SUBDIRECCION'] = isset($modelSubDireccion)?$modelSubDireccion->NOMBRE_SUBDIRECCION:'';
        $extra['FK_TARIFA'] = isset($modelTarifa)?($modelTarifa['TARIFA'].' '.$modelTarifa['DESC_TARIFA']):'';
        $extra['FK_CRITERIO'] = isset($modelCriterio)?$modelCriterio->DESC_CRITERIO:'';
        $extra['FK_DOMINIO'] = isset($modelDominio)?$modelDominio->DESC_DOMINIO:'';
        $extra['FK_TECNOLOGIA'] = isset($modelTecnologia)?$modelTecnologia->DESC_TECNOLOGIA:'';
        $fk_folio = '';
       
        if(isset($_GET['fk_folio']))
        {
            $fk_folio = $_GET['fk_folio'];

        }
        if ($fk_folio != '')
        {
            $idProyecto = (new \yii\db\Query())
            ->select(["distinct(PK_FOLIO)",
            "FK_CLIENTE",
            "DESC_FOLIO"
            ])
            ->from('tbl_folios')
            ->where(['PK_FOLIO'=>$fk_folio ])
            ->one();

            $model->FK_CLIENTE = $idProyecto['FK_CLIENTE'];
            $model->DESC_PROYECTO = $idProyecto['DESC_FOLIO'];
            $model->FK_FOLIO = $idProyecto['PK_FOLIO'];

        }
        
        $modelResponsableOP = (new \yii\db\Query)
                        ->select([
                            "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
                            'e.PK_EMPLEADO',
                        ])
                        ->from('tbl_empleados e')
                        
                        ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
                        ->where(['p.FK_AREA'=>'5'])->andwhere(['or',['p.FK_ESTATUS_RECURSO'=>'3'],['p.FK_ESTATUS_RECURSO'=>'5']])
                        ->orderBy('nombre_emp DESC')->all();


        if ($model->load(Yii::$app->request->post())){
            $data = Yii::$app->request->post();
            if(count($data['fase']) > 0){
                foreach ($data['fase'] as $key => $value) {
                    if($value == 1){
                        $liderFase = $data['select2analisis'];
                    }elseif($value == 2){
                        $liderFase = $data['select2diseno'];
                    }elseif($value == 3){
                        $liderFase = $data['select2construccion'];
                    }elseif($value == 4){
                        $liderFase = $data['select2pruebas'];
                    }
                    if(!in_array($value, $arrayFases)){//Valida si la fase ya habia sido creada
                        $modelProyectosFase =  new TblProyectosFases();
                        $modelProyectosFase->FK_CAT_FASE = $value;
                        $modelProyectosFase->FK_PROYECTO = $model->PK_PROYECTO;
                        $modelProyectosFase->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelProyectosFase->FK_LIDER_PROYECTO = $liderFase;
                        $modelProyectosFase->save(false);

                    }else{ //Si la fase ya habia sido creada anteriormente verifica si cambio el id de lider
                        $modelProyectosFase = TblProyectosFases::find()->where(['FK_PROYECTO' => $id, 'FK_CAT_FASE' => $value])->limit(1)->one();
                        if($modelProyectosFase->FK_LIDER_PROYECTO != $liderFase){
                            $modelProyectosFase->FK_LIDER_PROYECTO = $liderFase;
                            $modelProyectosFase->save(false);
                        }
                    }
                }

            }
            //$descTarifa = TblCatTarifas::find()->where(['PK_CAT_TARIFA'=>$model->FK_TARIFA])->asArray()->one()['TARIFA'];    
            //$model->TARIFA = $descTarifa;
            if($model->FK_TARIFA != NULL )
                {

                $descTarifa = TblCatTarifas::find()->where(['PK_CAT_TARIFA'=>$model->FK_TARIFA])->asArray()->one()['TARIFA'];    
                $model->TARIFA = $descTarifa;
                }
                else
                {

                $model->TARIFA =    $data['TblProyectos']['TARIFA'];
                $model->FK_TARIFA = $data['TblProyectos']['FK_TARIFA'];
                } 
            $model->FECHA_REGISTRO = date('Y-m-d H:i:s');
            $model->FK_ESTATUS_PROY = $data['TblProyectos']['FK_ESTATUS_PROY'];
            /*$model->FK_CRITERIO = $data['TblCatCriterio']['FK_CRITERIO'];
            $model->FK_DOMINIO = $data['TblCatDominio']['FK_DOMINIO'];
            $model->FK_TECNOLOGIA = $data['TblCatTecnologiaProyectos']['FK_TECNOLOGIA'];
            */
            $model->save(false);

            //Verificar si se elimino alguna fase
            /*foreach ($arrayFases as $key => $value) {
                if(!in_array($value, $data['fase'])){//Valida si la fase que esta dada de alta, se mando en el post como activa
                    TblProyectosEquipoAsignado::deleteAll('FK_PROYECTO='.$id.' AND FK_CAT_FASE='.$value);
                    tblproyectosfases::deleteAll('FK_PROYECTO='.$id.' AND FK_CAT_FASE='.$value);
                    $modelFaseProyecto = TblFaseProyecto::find()->andWhere(['FK_PROYECTO'=>$id, 'FK_FASE'=>$value])->one();
                    $modelFaseProyecto->delete();
                }
            }*/
            return $this->redirect(['update', 'action'=>'save','id'=>$id]);
        }else{
            return $this->render('update', [
                'model' => $model,
                'modelResponsableOP'  => $modelResponsableOP,
                'extra' => $extra,
                'arrayFases' => $arrayFases,
                'modelFolio'  => $modelFolio,
                ]);
        }
    }


    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionPlaneacion()
    {
        if (Yii::$app->request->isAjax) 
        {


            $data = Yii::$app->request->post();

            $connection = \Yii::$app->db;

            $planeacionCampos =  $connection->createCommand("select tf.*, ef.DESC_ESTATUS_FASE, cf.DESC_FASE
                                        FROM tbl_proyectos_fases tf LEFT JOIN tbl_cat_estatus_fases ef ON tf.FK_ESTATUS_FASE = ef.PK_ESTATUS_FASE
                                        LEFT JOIN tbl_cat_fase cf ON tf.FK_CAT_FASE = cf.PK_CAT_FASE
                                        WHERE tf.FK_PROYECTO = ".$data['idProyecto']." AND tf.FK_CAT_FASE = ".$data['idFase']." 
                                        AND tf.PK_PROYECTO_FASE = (SELECT MAX(tf2.PK_PROYECTO_FASE) 
                                                          FROM tbl_proyectos_fases tf2
                                                          WHERE tf2.FK_PROYECTO = ".$data['idProyecto']." AND tf2.FK_CAT_FASE = ".$data['idFase'].")")->queryOne();

            
            $doc_est_com = $connection->createCommand("select * 
                                        FROM tbl_documentos_proyectos tdp
                                        WHERE tdp.PK_DOCUMENTO = (SELECT MAX(tf.FK_DOC_EST_COM) 
                                                                   FROM tbl_proyectos_fases tf 
                                                                   WHERE tf.FK_PROYECTO = ".$data['idProyecto']." AND tf.FK_CAT_FASE = ".$data['idFase'].")")->queryOne(); 
                                        
            $doc_est_int = $connection->createCommand("select * 
                                        FROM tbl_documentos_proyectos tdp
                                        WHERE tdp.PK_DOCUMENTO = (SELECT MAX(tf.FK_DOC_EST_INT) 
                                                                   FROM tbl_proyectos_fases tf 
                                                                   WHERE tf.FK_PROYECTO = ".$data['idProyecto']." AND tf.FK_CAT_FASE = ".$data['idFase'].")")->queryOne(); 
            
            $doc_plan_proy_com = $connection->createCommand("select * 
                                        FROM tbl_documentos_proyectos tdp
                                        WHERE tdp.PK_DOCUMENTO = (SELECT MAX(tf.FK_DOC_PLAN_PROY_COM) 
                                                                   FROM tbl_proyectos_fases tf
                                                                   WHERE tf.FK_PROYECTO = ".$data['idProyecto']." AND tf.FK_CAT_FASE = ".$data['idFase'].")")->queryOne(); 
            
            $doc_plan_proy_int = $connection->createCommand("select * 
                                        FROM tbl_documentos_proyectos tdp
                                        WHERE tdp.PK_DOCUMENTO = (SELECT MAX(tf.FK_DOC_PLAN_PROY_INT) 
                                                                   FROM tbl_proyectos_fases tf 
                                                                   WHERE tf.FK_PROYECTO = ".$data['idProyecto']." AND tf.FK_CAT_FASE = ".$data['idFase'].")")->queryOne(); 

            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return[
                    'data' => $data,
                    'planeacionCampos' => $planeacionCampos,
                    'doc_est_com' => $doc_est_com,
                    'doc_est_int' => $doc_est_int,
                    'doc_plan_proy_com' => $doc_plan_proy_com,
                    'doc_plan_proy_int' => $doc_plan_proy_int,
                ];

        }
    }

    public function actionAsignacion_equipo()
    {
        if (Yii::$app->request->isAjax) 
        {
            $data = Yii::$app->request->post();

            $connection = \Yii::$app->db;

            $asignacionEquipoCampos =  $connection->createCommand("select 
                                        tf.PK_PROYECTO_FASE,
                                        tf.URL_TEAM_DASHBOARD 
                                        FROM tbl_proyectos_fases tf
                                        WHERE tf.FK_PROYECTO = ".$data['idProyecto']." AND tf.FK_CAT_FASE = ".$data['idFase'])->queryOne();

            $arrayEquipoAsignado    =  $connection->createCommand("select
                                        tpea.PK_PROYECTO_EQUIPO_ASIGNADO,
                                        tpea.FK_PROYECTO_FASE,
                                        tpea.FK_EMPLEADO, 
                                        tpea.PORC_TRABAJO,
                                        tpea.FK_ROL,
                                        tpea.FECHA_ESTIMADA_INICIO,
                                        tpea.FECHA_ESTIMADA_LIBERACION,
                                        tpea.FECHA_REAL_INICIO,
                                        tpea.FECHA_REAL_LIBERACION,
                                        tpea.HORAS_INVERTIDAS,
                                        tpea.COSTO_PROYECTO_AL_MOMENTO,
                                        te.NOMBRE_EMP,
                                        te.APELLIDO_PAT_EMP,
                                        te.APELLIDO_MAT_EMP,
                                        tpr.DESC_ROL
                                        FROM tbl_proyectos_equipo_asignado tpea
                                        INNER JOIN tbl_empleados te
                                        ON te.PK_EMPLEADO = tpea.FK_EMPLEADO
                                        INNER JOIN tbl_proyectos_roles tpr
                                        ON tpr.PK_ROL = tpea.FK_ROL
                                        WHERE tpea.FK_PROYECTO_FASE =".$asignacionEquipoCampos['PK_PROYECTO_FASE'])->queryAll();

            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return[
                    'data' => $data,
                    'asignacionEquipoCampos' => $asignacionEquipoCampos,
                    'arrayEquipoAsignado' => $arrayEquipoAsignado,
                ];

        }
    }

    public function actionSeguimiento()
    {
        if (Yii::$app->request->isAjax) 
        {
            $data = Yii::$app->request->post();

            $connection = \Yii::$app->db;

            $seguimientoCampos =  $connection->createCommand("select 
                                        tf.PK_PROYECTO_FASE,
                                        tf.FK_PROYECTO, 
                                        tf.FK_CAT_FASE,
                                        tf.FK_ESTATUS_FASE,
                                        tf.FECHA_INI_REAL,
                                        tf.FECHA_FIN_REAL,
                                        tf.ESFUERZO_REAL,
                                        tf.AVANCE_REAL,
                                        tf.FECHA_INI_COMPROMISO_PLAN,
                                        tf.FECHA_FIN_COMPROMISO_PLAN,
                                        tf.ESFUERZO_COMPROMISO_PLAN,
                                        tf.AVANCE_COMPROMETIDO,
                                        tf.COSTO_ACTUAL
                                        FROM tbl_proyectos_fases tf
                                        WHERE tf.FK_PROYECTO = ".$data['idProyecto']." AND tf.FK_CAT_FASE = ".$data['idFase'])->queryOne();

            $seguimientoEquipoAsignado =  $connection->createCommand("select
                                        tpea.PK_PROYECTO_EQUIPO_ASIGNADO,
                                        tpea.FK_PROYECTO_FASE,
                                        tpea.FK_EMPLEADO,
                                        tpe.PK_PERFIL, 
                                        tpea.PORC_TRABAJO,
                                        tpea.FK_ROL,
                                        tpea.FECHA_ESTIMADA_INICIO,
                                        tpea.FECHA_ESTIMADA_LIBERACION,
                                        tpea.FECHA_REAL_INICIO,
                                        tpea.FECHA_REAL_LIBERACION,
                                        tpea.HORAS_INVERTIDAS,
                                        tpea.COSTO_PROYECTO_AL_MOMENTO,
                                        te.NOMBRE_EMP,
                                        te.APELLIDO_PAT_EMP,
                                        te.APELLIDO_MAT_EMP,
                                        tpe.TARIFA,
                                        tpr.DESC_ROL
                                        FROM tbl_proyectos_equipo_asignado tpea
                                        INNER JOIN tbl_empleados te
                                        ON te.PK_EMPLEADO = tpea.FK_EMPLEADO
                                        INNER JOIN tbl_perfil_empleados tpe
                                        ON tpe.FK_EMPLEADO = te.PK_EMPLEADO
                                        INNER JOIN tbl_proyectos_roles tpr
                                        ON tpr.PK_ROL = tpea.FK_ROL
                                        WHERE tpea.FK_PROYECTO_FASE =".$seguimientoCampos['PK_PROYECTO_FASE'])->queryAll();
            
            $seguimientoHistorico =  $connection->createCommand("select
                                        tbcsp.PK_COMENTARIOS_SEGUIMIENTO_PROY,
                                        tbcsp.FK_PROYECTO_FASE,
                                        tbcsp.COMENTARIOS,
                                        tbcsp.FECHA_REGISTRO,
                                        tu.PK_USUARIO,
                                        tu.NOMBRE_COMPLETO
                                        FROM tbl_bit_comentarios_seguimiento_proy tbcsp
                                        INNER JOIN tbl_usuarios tu
                                        ON tu.PK_USUARIO = tbcsp.FK_USUARIO
                                        WHERE tbcsp.FK_PROYECTO_FASE =".$seguimientoCampos['PK_PROYECTO_FASE'])->queryAll();

            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return[
                    'data' => $data,
                    'seguimientoCampos' => $seguimientoCampos,
                    'seguimientoEquipoAsignado' => $seguimientoEquipoAsignado,
                    'seguimientoHistorico' => $seguimientoHistorico,
                ];

        }
    }

    public function actionAgregar()
    {
        if (Yii::$app->request->isAjax) 
        {
            $totalPorcentajeProyectos = 0;
            $data = Yii::$app->request->post();
            
            $connection = \Yii::$app->db;
            $consultaFase =  $connection->createCommand("select * 
                                        FROM tbl_proyectos_fases tf
                                        WHERE tf.FK_PROYECTO = ".$data['pkProyecto']." AND tf.FK_CAT_FASE = ".$data['fk_fase_actual'])->queryOne();
            
            $existeEmpleado =  $connection->createCommand("select * 
                                        FROM tbl_proyectos_equipo_asignado pea
                                        WHERE pea.FK_PROYECTO_FASE = ".$consultaFase['PK_PROYECTO_FASE']." and pea.FK_EMPLEADO = ".$data['fkEmp'])->queryOne();
            $consultaProyecto =  $connection->createCommand("select * 
                                        FROM tbl_proyectos tp
                                        WHERE tp.PK_PROYECTO = ".$data['pkProyecto'])->queryOne();

            if(!$existeEmpleado){
                $modeloProyectoEquipoAsignado = new TblProyectosEquipoAsignado();
                
                $modeloProyectoEquipoAsignado->FK_PROYECTO_FASE = $consultaFase['PK_PROYECTO_FASE'];
                $modeloProyectoEquipoAsignado->FK_EMPLEADO = $data['fkEmp'];
                $modeloProyectoEquipoAsignado->PORC_TRABAJO = $data['porc_trabajo'];
                $modeloProyectoEquipoAsignado->FK_ROL = $data['rol'];
                $modeloProyectoEquipoAsignado->FECHA_ESTIMADA_INICIO = transform_date($data['fecha_est_inicio'],'Y-m-d');
                $modeloProyectoEquipoAsignado->FECHA_ESTIMADA_LIBERACION = transform_date($data['fecha_est_liberacion'],'Y-m-d');
                $modeloProyectoEquipoAsignado->FECHA_REAL_INICIO = null;
                $modeloProyectoEquipoAsignado->FECHA_REAL_LIBERACION = null;
                $modeloProyectoEquipoAsignado->FECHA_REGISTRO = date('Y-m-d H:i:s');
                $modeloProyectoEquipoAsignado->save(false);

                //Se cambia el estatus del empleado que se esta agregando al equipo en la fase del proyecto a 'En Proyecto'
                $modelPerfilEmpleados = tblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $data['fkEmp']])->one();
                $modelPerfilEmpleados->FK_ESTATUS_RECURSO = 1;
                $modelPerfilEmpleados->FK_UNIDAD_NEGOCIO = $consultaProyecto['FK_UNIDAD_NEGOCIO'];
                $modelPerfilEmpleados->save(false);
                user_log_bitacora_estatus_empleado($modelPerfilEmpleados->FK_EMPLEADO, $modelPerfilEmpleados->FK_ESTATUS_RECURSO);

                /*$consultaEmpleadoProyectos =  $connection->createCommand("select 
                            pea.FK_EMPLEADO,
                            pea.PORC_TRABAJO,
                            pea.FECHA_ESTIMADA_INICIO,
                            pea.FECHA_ESTIMADA_LIBERACION,
                            pea.FECHA_REAL_INICIO,
                            pea.FECHA_REAL_LIBERACION
                            FROM tbl_proyectos_equipo_asignado pea 
                            WHERE pea.FK_EMPLEADO = ".$data['fkEmp'])->queryAll();

                $num_registros_empleado = count($consultaEmpleadoProyectos);*/

                /* Si existe un sólo registro del empleado en la tabla de proyectos_equipo_asignado se cambia la unidad de negocio del recurso a la quetenga el primer o unico proyecto en el que se le asignó, si se asigna a otros proyectos no sufrirá modificaciónes hasta que vuelva a estar sólo en uno.
                Si existe más de un registro no se cambia la unidad de negocio ya que este empleado se registro primero en el equipo de algun otro proyecto. */
                if($data['num_registros_empleado'] == 0){
                    user_log_bitacora_unidad_negocio($data['fkEmp'],$consultaProyecto['FK_UNIDAD_NEGOCIO'],$data['pkProyecto']);
                }

                $totalPorcentajeProyectos = $data['totalPorcentajeProyectos'] + $data['porc_trabajo'];
                
            }else{
                $totalPorcentajeProyectos = $data['totalPorcentajeProyectos'] + $data['porc_trabajo'];
            }

            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return[
                'data' => $data,
                'existeEmpleado' => $existeEmpleado,
                'totalPorcentajeProyectos' => $totalPorcentajeProyectos,
            ];
            
        }
    }

    public function actionObtener_porc_empleado()
    {
        if (Yii::$app->request->isAjax) 
        {
            $num_registros_empleado = 0;
            $consultaEmpleadoProyectos = '';
            $totalPorcentajeProyectos = 0;
            
            $data = Yii::$app->request->post();
            $connection = \Yii::$app->db;
            $consultaEmpleadoProyectos =  $connection->createCommand("select 
                        pea.FK_EMPLEADO,
                        sum(pea.PORC_TRABAJO) as PORC_TRABAJO,
                        pea.FK_PROYECTO_FASE,
                        tp.PK_PROYECTO,
                        tp.DESC_PROYECTO,
                        pea.FECHA_ESTIMADA_INICIO,
                        pea.FECHA_ESTIMADA_LIBERACION,
                        pea.FECHA_REAL_INICIO,
                        pea.FECHA_REAL_LIBERACION
                        FROM tbl_proyectos_equipo_asignado pea
                        left join tbl_proyectos_fases tpf
                        on tpf.PK_PROYECTO_FASE = pea.FK_PROYECTO_FASE
                        left join tbl_proyectos tp
                        on tp.PK_PROYECTO = tpf.FK_PROYECTO
                        WHERE pea.FK_EMPLEADO = ".$data['fkEmp']."
                        GROUP BY tp.PK_PROYECTO")->queryAll();

            $num_registros_empleado = count($consultaEmpleadoProyectos);

            $hoy = date('Y-m-d');
            
            foreach ($consultaEmpleadoProyectos as $key => $value) {
                if(!empty($value['FECHA_FIN_REAL'])){
                    $fecha_fin_real = transform_date($value['FECHA_FIN_REAL'],'Y-m-d');
                    $fecha_fin_real >= $hoy 
                    ? $totalPorcentajeProyectos = $totalPorcentajeProyectos+$value['PORC_TRABAJO'] 
                    : $totalPorcentajeProyectos = $totalPorcentajeProyectos;
                }else{
                    $fecha_estimada_liberacion = transform_date($value['FECHA_ESTIMADA_LIBERACION'],'Y-m-d');
                    $fecha_estimada_liberacion >= $hoy 
                    ? $totalPorcentajeProyectos = $totalPorcentajeProyectos+$value['PORC_TRABAJO'] 
                    : $totalPorcentajeProyectos = $totalPorcentajeProyectos;
                }   
            }

            $totalPorcentajeProyectos = $totalPorcentajeProyectos+$data['porc_trabajo'];

            $connection->close();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return[
                'num_registros_empleado' => $num_registros_empleado,
                'consultaEmpleadoProyectos' => $consultaEmpleadoProyectos,
                'totalPorcentajeProyectos' => $totalPorcentajeProyectos,
            ];
        }
    }

    public function actionObtener_datos_folio()
    {
        if (Yii::$app->request->isAjax) 
        {
            $data = Yii::$app->request->post();
            $FK_FOLIO= $data['FK_FOLIO'];

            if($FK_FOLIO != '' && $FK_FOLIO >0)
            {
                $modelFolios = TblFolios::find()->where(['PK_FOLIO' => $FK_FOLIO])->limit(1)->one();
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return[
                    'FK_FOLIO' => $FK_FOLIO,
                    'desc_proyecto' => $modelFolios->DESC_FOLIO,
                    'fk_cliente' => $modelFolios->FK_CLIENTE,                   
                ];
            }
            
        }
    }

    public function actionPeriodos() 
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $opcion = (isset($data['opcion']) == 'contabilidad') ? $data['opcion'] : '';

            $connection = \Yii::$app->db;
            /*if ($opcion == 'contabilidad') {
                //Consulta para obtener periodos facturables, que se ocupan en el modulo de contabilidad.
                $periodos = $connection->createCommand("SELECT PP.*, DP.NUM_DOCUMENTO AS NUM_DOCUMENTO_ODC, DPHDE.NUM_DOCUMENTO AS NUM_DOCUMENTO_HDE
                    FROM tbl_proyectos_periodos PP 
                    LEFT JOIN tbl_documentos_proyectos DP ON PP.FK_DOCUMENTO_ODC = DP.PK_DOCUMENTO
                    LEFT JOIN tbl_documentos_proyectos DPHDE ON PP.FK_DOCUMENTO_HDE = DPHDE.PK_DOCUMENTO
                    WHERE PP.FK_PROYECTO_FASE = ".$data['data']." AND PP.FK_DOCUMENTO_ODC IS NOT NULL
                    AND ((PP.FK_DOCUMENTO_HDE IS NOT NULL AND PP.FK_DOCUMENTO_FACTURA IS NULL) OR (PP.FK_DOCUMENTO_FACTURA IS NULL AND PP.FACTURA_PROVISION = 1)) ORDER BY PP.PK_PROYECTO_PERIODO")->queryAll();
            } else {*/
                //Consulta para obtener todos los periodos de la fase.
                $periodos =  $connection->createCommand("SELECT PP.*,
                        DP.NUM_DOCUMENTO AS NUM_DOCUMENTO_ODC, DPHDE.NUM_DOCUMENTO AS NUM_DOCUMENTO_HDE,
                        DPB.NUM_DOCUMENTO AS NUM_DOCUMENTO_ODC_BOLSA, DPHDEB.NUM_DOCUMENTO AS NUM_DOCUMENTO_HDE_BOLSA,
                        FAC.NUM_DOCUMENTO AS NUM_DOCUMENTO_FACTURA, XML.NUM_DOCUMENTO AS NUM_DOCUMENTO_XML,
                        DP.NUM_REFERENCIA, FACTURA.FECHA_INGRESO_BANCO, DPHDE.NUM_REFERENCIA AS NUM_REFERENCIA_HDE
                    FROM tbl_proyectos_periodos PP 
                        LEFT JOIN tbl_documentos_proyectos DP ON PP.FK_DOCUMENTO_ODC = DP.PK_DOCUMENTO
                        LEFT JOIN tbl_documentos DPB ON PP.FK_DOCUMENTO_ODC = DPB.PK_DOCUMENTO
                        LEFT JOIN tbl_documentos_proyectos DPHDE ON PP.FK_DOCUMENTO_HDE = DPHDE.PK_DOCUMENTO
                        LEFT JOIN tbl_documentos DPHDEB ON PP.FK_DOCUMENTO_HDE = DPHDEB.PK_DOCUMENTO
                        LEFT JOIN tbl_documentos_proyectos FAC ON PP.FK_DOCUMENTO_FACTURA = FAC.PK_DOCUMENTO
                        LEFT JOIN tbl_documentos_proyectos XML ON PP.FK_DOCUMENTO_FACTURA_XML = XML.PK_DOCUMENTO
                        LEFT JOIN tbl_facturas FACTURA ON PP.FK_DOCUMENTO_FACTURA = FACTURA.FK_DOC_FACTURA
                    WHERE PP.FK_PROYECTO_FASE = ".$data['data']." ORDER BY PP.PK_PROYECTO_PERIODO")->queryAll();

            //}
            $connection->close();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $periodos;
        }
    }

    public function actionGetdocumentos() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            if ($data['TIPO'] == 'odc') {
                if($data['ES_BOLSA'] == 'true') {
                    $modelDocumento = TblDocumentos::findOne($data['FK_DOCUMENTO_ODC']);
                } else {
                    $modelDocumento = TblDocumentosProyectos::findOne($data['FK_DOCUMENTO_ODC']);
                }
            } elseif ($data['TIPO'] == 'hde') {
                if($data['ES_BOLSA'] == 'true') {
                    $modelDocumento = TblDocumentos::findOne($data['FK_DOCUMENTO_HDE']);
                } else {
                    $modelDocumento = TblDocumentosProyectos::findOne($data['FK_DOCUMENTO_HDE']);
                }
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['documento' => $modelDocumento,
                    'es_bolsa' => $data['ES_BOLSA']];
        }
    }

    public function actionDocumento_hde() {

        $modelSubirDocHDE = new SubirArchivo();
        $modelSubirDocHDE->extensions = 'pdf, docx';
        $modelSubirDocHDE->noRequired = true;
        $bandera = true;

        $modelDocumentoHDE = new TblDocumentosProyectos();
        $seguimiento = new TblBitComentariosSeguimientoProy();

        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();

            if ($data['postTypeHDE'] == 'create') {
                $modelSubirDocHDE->file = UploadedFile::getInstance($modelSubirDocHDE, '[15]file');
                if (!empty($modelSubirDocHDE->file)) {
                    $fechaHoraHoyHDE = date('YmdHis');
                    $rutaGuardadoHDE = '../uploads/ProyectosDocumentos/';
                    $nombreFisicoHDE = $fechaHoraHoyHDE.'_'.quitar_acentos(utf8_decode($modelSubirDocHDE->file->basename));
                    $nombreBDHDE = quitar_acentos(utf8_decode($modelSubirDocHDE->file->basename));
                    $extensionHDE = $modelSubirDocHDE->upload($rutaGuardadoHDE,$nombreFisicoHDE);
                    $rutaDocHDE = '/uploads/ProyectosDocumentos/';
                    $pk_documento_hde='';

                    $modelDocumentoHDE->NOMBRE_DOCUMENTO = $nombreBDHDE.'.'.$extensionHDE;
                    $modelDocumentoHDE->RUTA_DOCUMENTO = $rutaDocHDE.$nombreFisicoHDE.'.'.$extensionHDE;
                    $modelDocumentoHDE->FK_PROYECTO_FASE = $data['TblDocumentosProyectos']['FK_PROYECTO_FASE'];
                    $modelDocumentoHDE->FK_PROYECTO_PERIODO = $data['TblDocumentosProyectos']['FK_PROYECTO_PERIODO'];
                    $modelDocumentoHDE->FK_TIPO_DOCUMENTO = $data['TblDocumentosProyectos']['FK_TIPO_DOCUMENTO'];
                    $modelDocumentoHDE->NUM_DOCUMENTO = $data['TblDocumentosProyectos']['NUM_DOCUMENTO'];
                    $modelDocumentoHDE->NUM_REFERENCIA = $data['TblDocumentosProyectos']['NUM_REFERENCIA'];
                    $modelDocumentoHDE->TARIFA = $data['TblProyectosPeriodos']['TARIFA_HDE'];
                    $modelDocumentoHDE->HORAS = $data['txtHoras_HDE'];
                    $modelDocumentoHDE->MONTO = $data['txtMonto_HDE'];
                    $modelDocumentoHDE->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelDocumentoHDE->save(false);

                } else {
                    if ($data['TblDocumentosProyectos']['NUM_REFERENCIA']) {
                        $modelDocumentoHDE->NOMBRE_DOCUMENTO = '';
                        $modelDocumentoHDE->RUTA_DOCUMENTO = '';
                        $modelDocumentoHDE->FK_PROYECTO_FASE = $data['TblDocumentosProyectos']['FK_PROYECTO_FASE'];
                        $modelDocumentoHDE->FK_PROYECTO_PERIODO = $data['TblDocumentosProyectos']['FK_PROYECTO_PERIODO'];
                        $modelDocumentoHDE->FK_TIPO_DOCUMENTO = $data['TblDocumentosProyectos']['FK_TIPO_DOCUMENTO'];
                        $modelDocumentoHDE->NUM_REFERENCIA = $data['TblDocumentosProyectos']['NUM_REFERENCIA'];
                        $modelDocumentoHDE->TARIFA = $data['TblProyectosPeriodos']['TARIFA_HDE'];
                        $modelDocumentoHDE->HORAS = $data['txtHoras_HDE'];
                        $modelDocumentoHDE->MONTO = $data['txtMonto_HDE'];
                        $modelDocumentoHDE->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelDocumentoHDE->save(false);

                        $bandera = false;
                    }
                }

                $modelPeriodo = TblProyectosPeriodos::findOne($data['TblProyectosPeriodos']['PK_PROYECTO_PERIODO']);
                $modelPeriodo->FK_DOCUMENTO_HDE = $modelDocumentoHDE->PK_DOCUMENTO;
                $modelPeriodo->TARIFA_HDE = $data['TblProyectosPeriodos']['TARIFA_HDE'];
                $modelPeriodo->HORAS_HDE = $data['txtHoras_HDE'];
                $modelPeriodo->MONTO_HDE = $data['txtMonto_HDE'];
                $modelPeriodo->FK_CAT_TARIFA = $data['TblProyectosPeriodos']['FK_CAT_TARIFA'];
                $modelPeriodo->save(false);
            } else {

                $modelSubirDocHDE->file = UploadedFile::getInstance($modelSubirDocHDE, '[15]file');
                $modelPeriodo = TblProyectosPeriodos::findOne($data['TblProyectosPeriodos']['PK_PROYECTO_PERIODO']);
                $documento_viejo = TblDocumentosProyectos::findOne($modelPeriodo->FK_DOCUMENTO_HDE);

                //Cambio Bolsa Numero de referencia
                if($data['es_bolsa'] == 'true' && $data['TblDocumentosProyectos']['NUM_REFERENCIA']) {
                    $documento_bolsa = TblDocumentos::findOne($modelPeriodo->FK_DOCUMENTO_HDE);
                    $documento_bolsa->NUM_REFERENCIA = $data['TblDocumentosProyectos']['NUM_REFERENCIA'];
                    $documento_bolsa->save(false);
                } elseif ($modelSubirDocHDE->file == null && $data['es_bolsa'] == 'false') {
                    $documento_viejo->NUM_DOCUMENTO = $data['TblDocumentosProyectos']['NUM_DOCUMENTO'];
                    $documento_viejo->NUM_REFERENCIA = $data['TblDocumentosProyectos']['NUM_REFERENCIA'];
                    $documento_viejo->TARIFA = $data['TblProyectosPeriodos']['TARIFA_HDE'];
                    $documento_viejo->HORAS = $data['txtHoras_HDE'];
                    $documento_viejo->MONTO = $data['txtMonto_HDE'];
                    $documento_viejo->save(false);
                } else {
                    $documento_viejo->FK_PROYECTO_PERIODO = null;
                    $documento_viejo->save(false);

                    //Se crea un nuevo ducumento
                    if (!empty($modelSubirDocHDE->file)) {
                        $fechaHoraHoyHDE = date('YmdHis');
                        $rutaGuardadoHDE = '../uploads/ProyectosDocumentos/';
                        $nombreFisicoHDE = $fechaHoraHoyHDE.'_'.quitar_acentos(utf8_decode($modelSubirDocHDE->file->basename));
                        $nombreBDHDE = quitar_acentos(utf8_decode($modelSubirDocHDE->file->basename));
                        $extensionHDE = $modelSubirDocHDE->upload($rutaGuardadoHDE,$nombreFisicoHDE);
                        $rutaDocHDE = '/uploads/ProyectosDocumentos/';
                        $pk_documento_hde='';

                        $modelDocumentoHDE->NOMBRE_DOCUMENTO = $nombreBDHDE.'.'.$extensionHDE;
                        $modelDocumentoHDE->RUTA_DOCUMENTO = $rutaDocHDE.$nombreFisicoHDE.'.'.$extensionHDE;
                        $modelDocumentoHDE->FK_PROYECTO_FASE = $data['TblDocumentosProyectos']['FK_PROYECTO_FASE'];
                        $modelDocumentoHDE->FK_PROYECTO_PERIODO = $data['TblDocumentosProyectos']['FK_PROYECTO_PERIODO'];
                        $modelDocumentoHDE->FK_TIPO_DOCUMENTO = $data['TblDocumentosProyectos']['FK_TIPO_DOCUMENTO'];
                        $modelDocumentoHDE->NUM_DOCUMENTO = $data['TblDocumentosProyectos']['NUM_DOCUMENTO'];
                        $modelDocumentoHDE->NUM_REFERENCIA = $data['TblDocumentosProyectos']['NUM_REFERENCIA'];
                        $modelDocumentoHDE->TARIFA = $data['TblProyectosPeriodos']['TARIFA_HDE'];
                        $modelDocumentoHDE->HORAS = $data['txtHoras_HDE'];
                        $modelDocumentoHDE->MONTO = $data['txtMonto_HDE'];
                        $modelDocumentoHDE->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelDocumentoHDE->save(false);

                        $modelPeriodo->FK_DOCUMENTO_HDE = $modelDocumentoHDE->PK_DOCUMENTO;
                    }
                }

                $modelPeriodo->TARIFA_HDE = $data['TblProyectosPeriodos']['TARIFA_HDE'];
                $modelPeriodo->HORAS_HDE = $data['txtHoras_HDE'];
                $modelPeriodo->MONTO_HDE = $data['txtMonto_HDE'];
                $modelPeriodo->FK_CAT_TARIFA = $data['TblProyectosPeriodos']['FK_CAT_TARIFA'];
                $modelPeriodo->save(false);
            }

            if ($bandera) {
                $comentario = '';
                $comentario .= ($data['postTypeHDE'] == 'create') ? 'Se Creo un documento de Hoja de Entrada_' : 'Modificaci&oacute;n de documento de Hoja de Entrada_';
                $comentario .= $modelDocumentoHDE->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($modelPeriodo->FECHA_INI))).date('Y',strtotime($modelPeriodo->FECHA_INI));

                $seguimiento->COMENTARIOS = $comentario;
                $seguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                $seguimiento->FK_PROYECTO_FASE = $data['TblDocumentosProyectos']['FK_PROYECTO_FASE'];
                $seguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                $seguimiento->save(false);
            }

            return $this->redirect(['view', 'id' => $data['PK_PROYECTO_HDE']]);

        }
    }

    public function actionDocumento_odc() {

        $modelSubirDocODC = new SubirArchivo();
        $modelSubirDocODC->extensions = 'pdf, docx';
        $modelSubirDocODC->noRequired = true;
        $bandera = true;

        $modelDocumentoODC = new TblDocumentosProyectos();
        $seguimiento = new TblBitComentariosSeguimientoProy();

        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();

            if ($data['postTypeODC'] == 'create') {
                $modelSubirDocODC->file = UploadedFile::getInstance($modelSubirDocODC, '[14]file');
                if (!empty($modelSubirDocODC->file)) {
                    $fechaHoraHoyODC = date('YmdHis');
                    $rutaGuardadoODC = '../uploads/ProyectosDocumentos/';
                    $nombreFisicoODC = $fechaHoraHoyODC.'_'.quitar_acentos(utf8_decode($modelSubirDocODC->file->basename));
                    $nombreBDODC = quitar_acentos(utf8_decode($modelSubirDocODC->file->basename));
                    $extensionODC = $modelSubirDocODC->upload($rutaGuardadoODC,$nombreFisicoODC);
                    $rutaDocODC = '/uploads/ProyectosDocumentos/';
                    $pk_documento_odc='';

                    $modelDocumentoODC->NOMBRE_DOCUMENTO = $nombreBDODC.'.'.$extensionODC;
                    $modelDocumentoODC->RUTA_DOCUMENTO = $rutaDocODC.$nombreFisicoODC.'.'.$extensionODC;
                    $modelDocumentoODC->FK_PROYECTO_FASE = $data['TblDocumentosProyectos']['FK_PROYECTO_FASE'];
                    $modelDocumentoODC->FK_PROYECTO_PERIODO = $data['TblDocumentosProyectos']['FK_PROYECTO_PERIODO'];
                    $modelDocumentoODC->FK_TIPO_DOCUMENTO = $data['TblDocumentosProyectos']['FK_TIPO_DOCUMENTO'];
                    $modelDocumentoODC->FK_RAZON_SOCIAL = $data['TblDocumentosProyectos']['FK_RAZON_SOCIAL'];
                    $modelDocumentoODC->FECHA_DOCUMENTO = transform_date($data['TblDocumentosProyectos']['FECHA_DOCUMENTO'], 'Y-m-d');
                    $modelDocumentoODC->NUM_DOCUMENTO = $data['TblDocumentosProyectos']['NUM_DOCUMENTO'];
                    $modelDocumentoODC->NUM_SP = $data['TblDocumentosProyectos']['NUM_SP'];
                    $modelDocumentoODC->NUM_REFERENCIA = $data['TblDocumentosProyectos']['NUM_REFERENCIA'];
                    $modelDocumentoODC->TARIFA = isset($data['cambio']) ? $data['TblProyectosPeriodos']['TARIFA_ODC'] : $data['mask_tarifa_odc'];
                    $modelDocumentoODC->HORAS = $data['txtHoras'];
                    $modelDocumentoODC->MONTO = $data['txtMonto'];
                    $modelDocumentoODC->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelDocumentoODC->save(false);
                } else {
                    if ($data['TblDocumentosProyectos']['NUM_REFERENCIA']) {
                        $modelDocumentoODC->NOMBRE_DOCUMENTO = '';
                        $modelDocumentoODC->RUTA_DOCUMENTO = '';
                        $modelDocumentoODC->FK_PROYECTO_FASE = $data['TblDocumentosProyectos']['FK_PROYECTO_FASE'];
                        $modelDocumentoODC->FK_PROYECTO_PERIODO = $data['TblDocumentosProyectos']['FK_PROYECTO_PERIODO'];
                        $modelDocumentoODC->FK_TIPO_DOCUMENTO = $data['TblDocumentosProyectos']['FK_TIPO_DOCUMENTO'];
                        $modelDocumentoODC->FK_RAZON_SOCIAL = $data['TblDocumentosProyectos']['FK_RAZON_SOCIAL'];
                        $modelDocumentoODC->FECHA_DOCUMENTO = ($data['TblDocumentosProyectos']['FECHA_DOCUMENTO'] != '') ? transform_date($data['TblDocumentosProyectos']['FECHA_DOCUMENTO'], 'Y-m-d') : null;
                        $modelDocumentoODC->NUM_DOCUMENTO = $data['TblDocumentosProyectos']['NUM_DOCUMENTO'];
                        $modelDocumentoODC->NUM_SP = $data['TblDocumentosProyectos']['NUM_SP'];
                        $modelDocumentoODC->NUM_REFERENCIA = $data['TblDocumentosProyectos']['NUM_REFERENCIA'];
                        $modelDocumentoODC->TARIFA = isset($data['cambio']) ? $data['TblProyectosPeriodos']['TARIFA_ODC'] : $data['mask_tarifa_odc'];
                        $modelDocumentoODC->HORAS = $data['txtHoras'];
                        $modelDocumentoODC->MONTO = $data['txtMonto'];
                        $modelDocumentoODC->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelDocumentoODC->save(false);

                        $bandera = false;
                    }
                }
                
                $modelPeriodo = TblProyectosPeriodos::findOne($data['TblProyectosPeriodos']['PK_PROYECTO_PERIODO']);
                $modelPeriodo->FK_DOCUMENTO_ODC = $modelDocumentoODC->PK_DOCUMENTO;
                $modelPeriodo->FACTURA_PROVISION = $data['TblProyectosPeriodos']['FACTURA_PROVISION'];
                $modelPeriodo->TARIFA_ODC = isset($data['cambio']) ? $data['TblProyectosPeriodos']['TARIFA_ODC'] : $data['mask_tarifa_odc'];
                $modelPeriodo->HORAS_ODC = $data['txtHoras'];
                $modelPeriodo->MONTO_ODC = $data['txtMonto'];
                $modelPeriodo->FK_CAT_TARIFA = $data['TblProyectosPeriodos']['FK_CAT_TARIFA'];
                $modelPeriodo->save(false);

            } else {
                
                $modelSubirDocODC->file = UploadedFile::getInstance($modelSubirDocODC, '[14]file');
                $modelPeriodo = TblProyectosPeriodos::findOne($data['TblProyectosPeriodos']['PK_PROYECTO_PERIODO']);
                $documento_viejo = TblDocumentosProyectos::findOne($modelPeriodo->FK_DOCUMENTO_ODC);

                //Cambio Bolsa Numero de referencia
                if($data['es_bolsa'] == 'true' && $data['TblDocumentosProyectos']['NUM_REFERENCIA']) {
                    $documento_bolsa = TblDocumentos::findOne($modelPeriodo->FK_DOCUMENTO_ODC);
                    $documento_bolsa->NUM_REFERENCIA = $data['TblDocumentosProyectos']['NUM_REFERENCIA'];
                    $documento_bolsa->save(false);
                } elseif ($modelSubirDocODC->file == null && $data['es_bolsa'] == 'false') {
                    $documento_viejo->FK_RAZON_SOCIAL = $data['TblDocumentosProyectos']['FK_RAZON_SOCIAL'];
                    $documento_viejo->FECHA_DOCUMENTO = transform_date($data['TblDocumentosProyectos']['FECHA_DOCUMENTO'], 'Y-m-d');
                    $documento_viejo->NUM_DOCUMENTO = $data['TblDocumentosProyectos']['NUM_DOCUMENTO'];
                    $documento_viejo->NUM_SP = $data['TblDocumentosProyectos']['NUM_SP'];
                    $documento_viejo->NUM_REFERENCIA = $data['TblDocumentosProyectos']['NUM_REFERENCIA'];
                    $documento_viejo->TARIFA = isset($data['cambio']) ? $data['TblProyectosPeriodos']['TARIFA_ODC'] : $data['mask_tarifa_odc'];
                    $documento_viejo->HORAS = $data['txtHoras'];
                    $documento_viejo->MONTO = $data['txtMonto'];
                    $documento_viejo->save(false);
                } else {
                    $documento_viejo->FK_PROYECTO_PERIODO = null;
                    $documento_viejo->save(false);

                    //Se crea un nuevo ducumento
                    if (!empty($modelSubirDocODC->file)) {
                        $fechaHoraHoyODC = date('YmdHis');
                        $rutaGuardadoODC = '../uploads/ProyectosDocumentos/';
                        $nombreFisicoODC = $fechaHoraHoyODC.'_'.quitar_acentos(utf8_decode($modelSubirDocODC->file->basename));
                        $nombreBDODC = quitar_acentos(utf8_decode($modelSubirDocODC->file->basename));
                        $extensionODC = $modelSubirDocODC->upload($rutaGuardadoODC,$nombreFisicoODC);
                        $rutaDocODC = '/uploads/ProyectosDocumentos/';
                        $pk_documento_odc='';

                        $modelDocumentoODC->NOMBRE_DOCUMENTO = $nombreBDODC.'.'.$extensionODC;
                        $modelDocumentoODC->RUTA_DOCUMENTO = $rutaDocODC.$nombreFisicoODC.'.'.$extensionODC;
                        $modelDocumentoODC->FK_PROYECTO_FASE = $data['TblDocumentosProyectos']['FK_PROYECTO_FASE'];
                        $modelDocumentoODC->FK_PROYECTO_PERIODO = $data['TblDocumentosProyectos']['FK_PROYECTO_PERIODO'];
                        $modelDocumentoODC->FK_TIPO_DOCUMENTO = $data['TblDocumentosProyectos']['FK_TIPO_DOCUMENTO'];
                        $modelDocumentoODC->FK_RAZON_SOCIAL = $data['TblDocumentosProyectos']['FK_RAZON_SOCIAL'];
                        $modelDocumentoODC->FECHA_DOCUMENTO = ($data['TblDocumentosProyectos']['FECHA_DOCUMENTO'] != '') ? transform_date($data['TblDocumentosProyectos']['FECHA_DOCUMENTO'], 'Y-m-d') : null;
                        $modelDocumentoODC->NUM_DOCUMENTO = $data['TblDocumentosProyectos']['NUM_DOCUMENTO'];
                        $modelDocumentoODC->NUM_SP = $data['TblDocumentosProyectos']['NUM_SP'];
                        $modelDocumentoODC->NUM_REFERENCIA = $data['TblDocumentosProyectos']['NUM_REFERENCIA'];
                        $modelDocumentoODC->TARIFA = isset($data['cambio']) ? $data['TblProyectosPeriodos']['TARIFA_ODC'] : $data['mask_tarifa_odc'];
                        $modelDocumentoODC->HORAS = $data['txtHoras'];
                        $modelDocumentoODC->MONTO = $data['txtMonto'];
                        $modelDocumentoODC->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelDocumentoODC->save(false);

                        $modelPeriodo->FK_DOCUMENTO_ODC = $modelDocumentoODC->PK_DOCUMENTO;
                    }
                }

                $modelPeriodo->FACTURA_PROVISION = $data['TblProyectosPeriodos']['FACTURA_PROVISION'];
                $modelPeriodo->TARIFA_ODC = isset($data['cambio']) ? $data['TblProyectosPeriodos']['TARIFA_ODC'] : $data['mask_tarifa_odc'];
                $modelPeriodo->HORAS_ODC = $data['txtHoras'];
                $modelPeriodo->MONTO_ODC = $data['txtMonto'];
                $modelPeriodo->FK_CAT_TARIFA = $data['TblProyectosPeriodos']['FK_CAT_TARIFA'];
                $modelPeriodo->save(false);

            }

            if ($bandera) {
                $comentario = '';
                $comentario .= ($data['postTypeODC'] == 'create') ? 'Se Creo un documento de Orden de Compra_' : 'Modificaci&oacute;n de documento de Orden de Compra_';
                $comentario .= $modelDocumentoODC->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($modelPeriodo->FECHA_INI))).date('Y',strtotime($modelPeriodo->FECHA_INI));

                $seguimiento->COMENTARIOS = $comentario;
                $seguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                $seguimiento->FK_PROYECTO_FASE = $data['TblDocumentosProyectos']['FK_PROYECTO_FASE'];
                $seguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                $seguimiento->save(false);
            }

            return $this->redirect(['view', 'id' => $data['PK_PROYECTO_ODC']]);
        }
    }

    public function actionCreateperiodo() {

        $model = new TblProyectosPeriodos();
        $retorno = array('fail' => true);
        $bandera = true;

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $idProyectoPeriodo = ($post['tipo_periodo'] == 'update') ? $post['idProyectoPeriodo'] : '';

            /*Verificar si existen periodos*/
            $periodos = TblProyectosPeriodos::find()->where(['FK_PROYECTO_FASE' => $post['fk_proyecto_fase'] ])->all();
            $fase = tblproyectosfases::findOne($post['fk_proyecto_fase']);

            $nueva_fecha_ini = transform_date($post['fechaInicio'],'Y-m-d');
            $nueva_fecha_fin = transform_date($post['fechaFin'],'Y-m-d');

            $fechaInicio = $fase->FECHA_INI_COMPROMISO_PLAN;
            $fechaFinal  = $fase->FECHA_FIN_COMPROMISO_PLAN;

            if($fechaInicio == null || $fechaFinal == null){
                $retorno['msj'] = 'No es posible generar este periodo ya que no se han registrado fechas compromiso en la fase.';
                $bandera = false;
            }else{
                if ($periodos) {
                    foreach ($periodos as $key) {
                        if ($idProyectoPeriodo != '') {
                            if ($key->PK_PROYECTO_PERIODO == $idProyectoPeriodo) {
                                continue;
                            }
                        }
                        if ($nueva_fecha_ini >= $key->FECHA_INI && $nueva_fecha_ini <= $key->FECHA_FIN) {
                            $retorno['msj'] = 'La fecha inicio incluye fechas que ya est&aacute;n definidas en otro periodo, favor de Verificarlo';
                            $bandera = false;
                            break;
                        } else if ($nueva_fecha_fin >= $key->FECHA_INI && $nueva_fecha_fin <= $key->FECHA_FIN) {
                            $retorno['msj'] = 'La fecha inicio incluye fechas que ya est&aacute;n definidas en otro periodo, favor de Verificarlo';
                            $bandera = false;
                            break;
                        }
                    }
                }

                if ($nueva_fecha_ini < $fase->FECHA_INI_COMPROMISO_PLAN) {
                    $retorno['msj'] = 'La fecha inicio es menor a la fecha inicio compromiso plan de la fase';
                    $bandera = false;
                }

                if ($nueva_fecha_fin > $fase->FECHA_FIN_COMPROMISO_PLAN) {
                    $retorno['msj'] = 'La fecha fin es mayor a la fecha fin compromiso plan de la fase';
                    $bandera = false;
                }
            }

            if ($bandera) {
                if ($idProyectoPeriodo != '') {
                    $model = TblProyectosPeriodos::findOne($idProyectoPeriodo);
                } else {
                    $model->FECHA_REGISTRO = date('Y-m-d H:i:s');
                }

                $model->FK_PROYECTO_FASE = $post['fk_proyecto_fase'];
                $model->FECHA_INI = $nueva_fecha_ini;
                $model->FECHA_FIN = $nueva_fecha_fin;
                $model->TARIFA_ODC = $post['tarifa'];
                $model->HORAS_ODC = $post['horas'];
                $model->MONTO_ODC = $post['monto'];
                $model->FK_CAT_TARIFA = ($post['fk_cat_tarifa'] ? $post['fk_cat_tarifa'] : null); 

                $add = $model->save(false);

                if ($add) {
                    $retorno['fail'] = false;
                    $retorno['msj'] = ($idProyectoPeriodo != '') ? 'El periodo fue modificado Exitosamente' : 'El periodo fue creado Exitosamente';
                }
            }


            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $retorno;
        }
    }

    public function actionGetdocumentos_factura(){
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            $modelDocumentoFACTURA = ($data['FK_DOCUMENTO_FACTURA']) ? TblDocumentosProyectos::findOne($data['FK_DOCUMENTO_FACTURA']) : null;
            $modeloDocumentoXML = ($data['FK_DOCUMENTO_FACTURA_XML']) ? TblDocumentosProyectos::findOne($data['FK_DOCUMENTO_FACTURA_XML']) : null;
            $modelDocumentoODC = ($data['ES_BOLSA'] == 'true') ? TblDocumentos::findOne($data['FK_DOCUMENTO_ODC']) : TblDocumentosProyectos::findOne($data['FK_DOCUMENTO_ODC']);
            $modelDocumentoHDE = ($data['ES_BOLSA'] == 'true') ? TblDocumentos::findOne($data['FK_DOCUMENTO_HDE']) : TblDocumentosProyectos::findOne($data['FK_DOCUMENTO_HDE']);
            $modeloFactura = TblFacturas::find()->where(['=','FK_PROYECTO_DOC_FACTURA', $data['FK_DOCUMENTO_FACTURA']])->limit(1)->one();
            $resultado = array();

            $connection = \Yii::$app->db;

            //Bolsas De proyectos
            if ($data['ES_BOLSA'] == 'true') {
                $bloque_bolsas = TblBitBlsDocs::find()->where(['FK_BOLSA' => $data['FK_BOLSA']])->andWhere(['IS NOT','FK_PROYECTO_PERIODOS',null])->all();

                if($bloque_bolsas) {
                    foreach ($bloque_bolsas as $key) {
                        $bloque_periodos = explode(',', $key['FK_PROYECTO_PERIODOS']);
                        unset($bloque_periodos[count($bloque_periodos)-1]);
                        if (in_array($data['FK_PERIODO'], $bloque_periodos)) {
                            for($i = 0; $i < count($bloque_periodos); $i++) {
                                $resultado[] = (new \yii\db\Query())
                                    ->select([
                                        'P.DESC_PROYECTO AS desc_proyecto', 'PP.PK_PROYECTO_PERIODO AS pk_proyecto_periodo', 'PP.HORAS_ODC AS horas',
                                        'PP.MONTO_ODC AS monto', 'CONCAT(DATE_FORMAT(PP.FECHA_INI, \'%d/%m/%Y\')," al ",DATE_FORMAT(PP.FECHA_FIN, \'%d/%m/%Y\')) AS periodo',
                                        'FAC.NUM_DOCUMENTO AS factura'
                                    ])
                                    ->from('tbl_proyectos_periodos PP')
                                    ->join('LEFT JOIN','tbl_documentos_proyectos FAC', 'PP.FK_DOCUMENTO_FACTURA = FAC.PK_DOCUMENTO')
                                    ->join('INNER JOIN','tbl_proyectos_fases PF', 'PP.FK_PROYECTO_FASE = PF.PK_PROYECTO_FASE')
                                    ->join('INNER JOIN','tbl_proyectos P', 'PF.FK_PROYECTO = P.PK_PROYECTO')
                                    ->Where(['PP.PK_PROYECTO_PERIODO'=>$bloque_periodos[$i]])
                                    ->andWhere(['PP.FK_BOLSA' => $data['FK_BOLSA']])->one();

                            }
                            break;
                        }
                    }
                } else {

                    $fks_proyectos =  $connection->createCommand("select DISTINCT tp.PK_PROYECTO
                                    from tbl_proyectos tp
                                    left join tbl_proyectos_fases tpf
                                    on tp.PK_PROYECTO = tpf.FK_PROYECTO
                                    left join tbl_proyectos_periodos tpp
                                    on tpf.PK_PROYECTO_FASE = tpp.FK_PROYECTO_FASE
                                    where tpp.FK_BOLSA = ".$data['FK_BOLSA'])->queryAll();
                    $pks_documentos_proyectos =  $connection->createCommand("select
                                    td.PK_DOCUMENTO,
                                    tp.PK_PROYECTO
                                    from tbl_proyectos tp
                                    inner join tbl_proyectos_fases tpf
                                    on tp.PK_PROYECTO = tpf.FK_PROYECTO
                                    inner join tbl_proyectos_periodos tpp
                                    on tpp.FK_PROYECTO_FASE = tpf.PK_PROYECTO_FASE
                                    inner join tbl_documentos td
                                    on td.PK_DOCUMENTO = tpp.FK_DOCUMENTO_ODC
                                    where tpp.FK_BOLSA = ".$data['FK_BOLSA'])->queryAll();

                    $periodosProyectos = array();
                    $contPeriodos = 0;
                    foreach ($fks_proyectos as $key => $value) {
                        $sqlPeriodos =  $connection->createCommand("select
                                    tp.PK_PROYECTO,
                                    tp.DESC_PROYECTO,
                                    tp.NOMBRE_CORTO_PROYECTO,
                                    tpf.PK_PROYECTO_FASE,
                                    tcf.DESC_FASE,
                                    tpp.PK_PROYECTO_PERIODO,
                                    tpp.FK_BOLSA,
                                    tpp.*,
                                    td.PK_DOCUMENTO,
                                    td.NUM_DOCUMENTO,
                                    tf.PK_FACTURA,
                                    tf.FECHA_ENTREGA_CLIENTE,
                                    tf.FK_DOC_FACTURA,
                                    tf.FECHA_INGRESO_BANCO,
                                    tf.CONTACTO_ENTREGA,
                                    tf.FK_ESTATUS
                                    from tbl_proyectos_periodos tpp
                                    inner join tbl_proyectos_fases tpf
                                    on tpp.FK_PROYECTO_FASE = tpf.PK_PROYECTO_FASE
                                    inner join tbl_proyectos tp
                                    on tp.PK_PROYECTO = tpf.FK_PROYECTO
                                    left join tbl_documentos td
                                    on tpp.FK_DOCUMENTO_FACTURA = td.PK_DOCUMENTO
                                    left join tbl_facturas tf
                                    on tf.FK_PROYECTO_PERIODO = tpp.PK_PROYECTO_PERIODO and tf.FK_ESTATUS <> 3
                                    inner join tbl_cat_fase tcf
                                    on tcf.PK_CAT_FASE = tpf.FK_CAT_FASE
                                    where tp.PK_PROYECTO = '".$data['PK_PROYECTO']."'
                                    order by tpp.FECHA_INI")->queryAll();

                        $periodosProyectos[$contPeriodos] = $sqlPeriodos;
                        $contPeriodos = $contPeriodos+1;
                    }

                    $arr_periodos= '';
                    $contProyectosPintados = 0;
                    $it = 0;
                    $repetidos = array();
                    foreach ($fks_proyectos as $key => $value) {
                        $cont=1;
                        foreach ($periodosProyectos as $key2 => $value2) {
                            foreach ($value2 as $key3 => $value3) {
                                $imprimir = true;
                                foreach ($pks_documentos_proyectos as $doc => $val_doc) {
                                    if($value['PK_PROYECTO']==$val_doc['PK_PROYECTO']&&$val_doc['PK_DOCUMENTO']==$value3['FK_DOCUMENTO_ODC']){
                                        $imprimir = true;
                                        break;
                                    }else{
                                        $imprimir= false;
                                    }
                                }
                                if($imprimir){
                                    if(isset($value3['NUM_DOCUMENTO'])){
                                        $arr_periodos.=$value3['PK_PROYECTO_PERIODO'].",";
                                    }

                                    if (!in_array($value3['PK_PROYECTO_PERIODO'], $repetidos)) {
                                        $repetidos[] = $value3['PK_PROYECTO_PERIODO'];
                                        //$resultado[$it]['pk_proyecto'] = $value['PK_PROYECTO'];
                                        $resultado[$it]['desc_proyecto'] = $value3['DESC_PROYECTO'];
                                        $resultado[$it]['pk_proyecto_periodo'] = $value3['PK_PROYECTO_PERIODO'];
                                        $resultado[$it]['periodo'] = $cont.' - '.transform_date($value3['FECHA_INI'],'d/m/Y').' al '.transform_date($value3['FECHA_FIN'],'d/m/Y');
                                        $resultado[$it]['horas'] = $value3['HORAS_ODC'];
                                        $resultado[$it]['monto'] = $value3['MONTO_ODC'];
                                        $resultado[$it]['factura'] = (isset($value3['NUM_DOCUMENTO'])?$value3['NUM_DOCUMENTO']:"");
                                        $resultado[$it]['pagado'] = (isset($value3['FECHA_INGRESO_BANCO'])?transform_date($value3['FECHA_INGRESO_BANCO'],'d/m/Y'):'');
                                        $it++;
                                    }
                                }
                                $cont++;
                            }
                            $cont=1;
                            $contProyectosPintados++;
                        }
                    }

                }
            }

            $periodos = $connection->createCommand("SELECT PP.*, DP.NUM_DOCUMENTO AS NUM_DOCUMENTO_ODC, DP.FK_RAZON_SOCIAL, DPHDE.NUM_DOCUMENTO AS NUM_DOCUMENTO_HDE,
                    FAC.NUM_DOCUMENTO AS NUM_DOCUMENTO_FACTURA, XML.NUM_DOCUMENTO AS NUM_DOCUMENTO_XML
                    FROM tbl_proyectos_periodos PP 
                    LEFT JOIN tbl_documentos_proyectos DP ON PP.FK_DOCUMENTO_ODC = DP.PK_DOCUMENTO
                    LEFT JOIN tbl_documentos_proyectos DPHDE ON PP.FK_DOCUMENTO_HDE = DPHDE.PK_DOCUMENTO
                    LEFT JOIN tbl_documentos_proyectos FAC ON PP.FK_DOCUMENTO_FACTURA = FAC.PK_DOCUMENTO
                    LEFT JOIN tbl_documentos_proyectos XML ON PP.FK_DOCUMENTO_FACTURA_XML = XML.PK_DOCUMENTO
                    WHERE PP.FK_PROYECTO_FASE = ".$data['FK_PROYECTO_FASE']." ORDER BY PP.PK_PROYECTO_PERIODO")->queryAll();
            $connection->close();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['modeloFactura' => $modeloFactura,
                    'periodos' => $periodos,
                    'xml' => $modeloDocumentoXML,
                    'factura' => $modelDocumentoFACTURA,
                    'odc' => $modelDocumentoODC,
                    'hde' => $modelDocumentoHDE,
                    'proyectos_bolsa' => $resultado,
                    'post' => $data];
        }
    }

    public function actionDeleteajax() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $model = TblProyectosPeriodos::find()->where(['PK_PROYECTO_PERIODO' => $data['id_periodo']])->limit(1)->one();
            $model->delete();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $data;
        }
    }

    public function actionGetautomaticos() {
        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            //Consulta de dados
            $fase = tblproyectosfases::findOne($post['FK_PROYECTO_FASE']);
            $periodos = TblProyectosPeriodos::find()->where(['FK_PROYECTO_FASE' => $post['FK_PROYECTO_FASE']])->all();

            /*Declaracion de variables*/
            $meses = array('01', '03', '05', '07', '08', '10', '12');
            $suma = 1;
            $fechas = '';
            $dias = 0;
            $diasSiguientes = 0;
            /*Fin Declaracion de variables*/

            /*Asignacion de fechas*/
            $fechaInicio = $fase->FECHA_INI_COMPROMISO_PLAN;
            $fechaFinal  = $fase->FECHA_FIN_COMPROMISO_PLAN;

            //Valida que las fechas no vengan nulas en caso contrario no continua con el proceso
            if($fechaInicio != null && $fechaFinal != null){
                if ($periodos) {
                    $fechaInicio = $periodos[(count($periodos) - 1)]['FECHA_FIN'];
                    if($fechaInicio < $fase->FECHA_FIN_COMPROMISO_PLAN) {
                        $fachaini = strtotime ( '+1 day' , strtotime ( $fechaInicio ) ) ;
                        $fechaInicio = date ( 'Y-m-d' , $fachaini );
                    } else {
                        $suma = 0;
                    }
                }
                /*Fin Asignacion de fechas*/

                /*Calculo de Periodos*/
                $fechaIni = explode('-', $fechaInicio);
                $fechaFin = explode('-', $fechaFinal);

                if ($fechaFin[0] > $fechaIni[0]) {
                    $rango = (12 - $fechaIni[1]) + ($fechaFin[1] + $suma);
                } else {
                    $rango = ($fechaFin[1] - $fechaIni[1] + $suma);
                }
                /*Fin Calculo de Periosos*/

                /*Generacion fechas de los periodos*/
                for ($i = 0; $i < $rango; $i++) {

                    if ($fechaIni[2] == '01') {
                        /*Calculo Inicio*/
                        $tempDate = explode('-', $fechaInicio);
                        if (in_array($tempDate[1], $meses)) {
                            $diasSiguientes = 30;
                        } elseif ($tempDate[1] == '02') {
                            if ( ($tempDate[0] % 4 == 0) && ( ($tempDate[0] % 100 != 0) || ($tempDate[0] % 400 == 0) )) {
                                $diasSiguientes = 28;
                            }
                            else {
                                $diasSiguientes = 27;
                            }
                        } else {
                            $diasSiguientes = 29;
                        }
                        /*Generacion de periodos*/
                        $fachaini = strtotime ( '+'.$diasSiguientes.' day' , strtotime ( $fechaInicio ) ) ;
                        $fachaFin = date ( 'Y-m-d' , $fachaini );
                        $fechas[$i]['inicio'] = $fechaInicio;
                        $fechas[$i]['final'] = $fachaFin;
                        //$fechas[$i] = $fechaInicio.'#'.$fachaFin;

                        $fachaini = strtotime ( '+1 day' , strtotime ( $fachaFin ) ) ;
                        $fachaFin = date ( 'Y-m-d' , $fachaini );
                        /*Fin Generacion de periodos*/
                        $fechaInicio = $fachaFin;
                        /*Fin Calculo Inicio*/
                    } else {
                        /*Calculo Inicio*/

                        $tempDate = explode('-', $fechaInicio);
                        /*Genearcion de periodos menores a un mes*/
                        if (in_array($tempDate[1], $meses)) {
                            if ($tempDate[2] < 31) {
                                $diasSiguientes = 31 - $tempDate[2];
                            }
                        } elseif ($tempDate[1] == '02') {
                            if ( ($tempDate[0] % 4 == 0) && ( ($tempDate[0] % 100 != 0) || ($tempDate[0] % 400 == 0) )) {
                                if ($tempDate[2] < 29) {
                                    $diasSiguientes = 29 - $tempDate[2];
                                }
                            }
                            else {
                                if ($tempDate[2] < 28) {
                                    $diasSiguientes = 28 - $tempDate[2];
                                }
                            }
                        } else {
                            if ($tempDate[2] < 30) {
                                $diasSiguientes = 30 - $tempDate[2];
                            }
                        }
                        /*Generacion de periodos*/
                        $fachaini = strtotime ( '+'.$diasSiguientes.' day' , strtotime ( $fechaInicio ) ) ;
                        $fachaFin = date ( 'Y-m-d' , $fachaini );
                        $fechas[$i]['inicio'] = $fechaInicio;
                        $fechas[$i]['final'] = $fachaFin;
                        //$fechas[$i] = $fechaInicio.'#'.$fachaFin;

                        $fachaini = strtotime ( '+1 day' , strtotime ( $fachaFin ) ) ;
                        $fachaFin = date ( 'Y-m-d' , $fachaini );
                        /*Fin Generacion de periodos*/
                        $fechaInicio = $fachaFin;
                        /*Fin Genearcion de periodos menores a un mes*/
                    }

                }
            }else{
                $rango = null;
            }
            /*Fin Generacion fechas de los periodos*/

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['fase' => $fase, 'post' => $post, 'periodos' => $periodos, 'fechas' => $fechas, 'rango' => $rango];
        }
    }

    public function actionObtener_datos_grupos()
    {
        if (Yii::$app->request->isAjax) 
        {
            $data = Yii::$app->request->post();
            $FK_GRUPO= $data['FK_GRUPO'];
           if($FK_GRUPO != '' && $FK_GRUPO >0)
                    {
                        $modelGrupoFases = TblGrupoFases::find()->where(['FK_GRUPO' => $FK_GRUPO])->asArray()->all();
                    }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;                    
        return[
        'FK_GRUPO' => $FK_GRUPO,
        //Aplico porque necesito regresar un array , no las propiedas o elementos del Modelo.
        'fases' => $modelGrupoFases,             
          ];
        }
    }

    protected function findModel($id)
    {
        if (($model = TblProyectos::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

