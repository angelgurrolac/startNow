<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\db\Command;
use yii\db\Expression;
use app\models\TblCatMotivos;
use app\models\TblAsignaciones;
use app\models\tblempleados;
use app\models\tblclientes;
use app\models\tblcatcontactos;
use app\models\tblperfilempleados;
use app\models\tblcatpuestos;
use app\models\TblCatProyectos;
use app\models\tbldocumentos;
use app\models\tblcatestatusasignaciones;
use app\models\TblPeriodos;
use app\models\tblcatestatusrecursos;
use app\models\TblAsignacionesReemplazos;
use app\models\TblCatUbicaciones;
use app\models\TblCatRankTecnico;
use app\models\TblCatUnidadesNegocio;
use app\models\TblBitUnidadNegocioAsig;
use app\models\TblAsignacionesSeguimiento;
use app\models\TblBitUbicacionFisica;
use app\models\TblCatBolsas;
use app\models\SubirArchivo;
use app\models\TblCatRazonSocial;
use app\models\TblUsuarios;
use app\models\TblCatTarifas;
use app\models\TblBitCatTarifas;
use app\models\TblTarifasClientes;
use app\models\TblBitPeriodos;
use app\models\TblBitAsignacionTarifas;
use app\models\TblVacantes;
use app\models\TblBitComentariosFacturas;
use app\models\TblBitComentariosAsignaciones;
use app\models\TblFacturas;
use app\models\TblBitBlsDocs;
use app\models\TblProyectosPeriodos;
use app\models\TblProyectos;
use app\models\TblCatFabricaDesarrollo;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use app\models\TblDocumentosProyectos;

class AsignacionesController extends Controller
{
        public function behaviors()
        {
            return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post'],
                    ],
                ],
            ]);

        }

        public function actionIndex()
        {
            //$model=$this->findModel($id);
            $tamanio_pagina=9;
            if (Yii::$app->request->isAjax)
            {
                $data = Yii::$app->request->post();
                $post=null;
                parse_str($data['data'],$post);
                $nombre      =(!empty($post['nombre']))? trim($post['nombre']):'';
                $documentos  =(!empty($post['documentos']))? trim($post['documentos']):'';
                $NumDoc      =(!empty($post['NumDoc']))? trim($post['NumDoc']):'';
                $proyectos   =(!empty($post['proyectos']))? trim($post['proyectos']):'';
                $estatus     =(!empty($post['estatus']))? trim($post['estatus']):'';
                $cliente     =(!empty($post['cliente']))? trim($post['cliente']):'';
                $contacto    =(!empty($post['contacto']))? trim($post['contacto']):'';
                $ubicaciones =(!empty($post['ubicacion']))? trim($post['ubicacion']):'';
                $empresa     =(!empty($post['empresa']))? trim($post['empresa']):'';
                $bolsa_asignacion= (!empty($post['bolsa_asignacion']))?$post['bolsa_asignacion']:[];

                if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                    $razon_social =(!empty($post['razon_social']))? trim($post['razon_social']):'';
                }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                    $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                    $razon_social = (!empty($post['razon_social']))? trim($post['razon_social']):$unidadesNegocioValidas;
                }else{
                    $razon_social = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
                }

                $fk_responsable_op =(!empty($post['fk_responsable_op']))? trim($post['fk_responsable_op']):'';

                $validadorBandeja = 0;//Si la llamada ajax viene desde la bandeja(ODC,HDE,FACTURA) marca 1, de lo contrario marca 0
                $op1=[];
                $op2=[];
                $op3=[];
                $ids_asignacion_compra =(!empty($post['ids_asignacion_compra']))? trim($post['ids_asignacion_compra']):'';
                if($ids_asignacion_compra){
                    $op1=['not in', 'a.PK_ASIGNACION',explode(',',$ids_asignacion_compra)];
                    $validadorBandeja = 1;
                }
                $ids_hoja_entrada =(!empty($post['ids_hoja_entrada']))? trim($post['ids_hoja_entrada']):'';
                if($ids_hoja_entrada){
                    $op1=['in', 'a.PK_ASIGNACION',explode(',',$ids_hoja_entrada)];
                    $validadorBandeja = 1;
                }

                $ids_factura =(!empty($post['ids_factura']))? trim($post['ids_factura']):'';
                if($ids_factura){
                    //if($ids_factura!=-1){
                        $op1=['in', 'a.PK_ASIGNACION',explode(',',$ids_factura)];
                        $validadorBandeja = 1;
                    //}
                }

                $ids_por_vencer =(!empty($post['ids_por_vencer']))? trim($post['ids_por_vencer']):'';
                if($ids_por_vencer){
                    $op1=['in', 'a.PK_ASIGNACION',explode(',',$ids_por_vencer)];
                }

                $ids_por_cobrar =(!empty($post['ids_por_cobrar']))? trim($post['ids_por_cobrar']):'';
                if($ids_por_cobrar){
                    $op1=['in', 'a.PK_ASIGNACION',explode(',',$ids_por_cobrar)];
                    $validadorBandeja = 1;
                }

                $ids_pendientes_aprobar =(!empty($post['ids_pendientes_aprobar']))? trim($post['ids_pendientes_aprobar']):'';
                if($ids_pendientes_aprobar){
                    $op1=['in', 'a.PK_ASIGNACION',explode(',',$ids_pendientes_aprobar)];
                    $validadorBandeja = 1;
                }

                $ids_detenidas =(!empty($post['ids_detenidas']))? trim($post['ids_detenidas']):'';
                if($ids_detenidas){
                    $op1=['in', 'a.PK_ASIGNACION',explode(',',$ids_detenidas)];
                    $validadorBandeja = 2; //NUEVO VALIDADOR DE BANDEJA PARA LAS ASIGNACIONES DETENIDAS --> CLRR
                }

                $ids_pdts_fecha_entrega_cliente =(!empty($post['ids_pdts_fecha_entrega_cliente']))? trim($post['ids_pdts_fecha_entrega_cliente']):'';
                if($ids_pdts_fecha_entrega_cliente){
                    $op1=['in', 'a.PK_ASIGNACION',explode(',',$ids_pdts_fecha_entrega_cliente)];
                    $validadorBandeja = 0;
                }

                $pagina       =(!empty($data['pagina']))? trim($data['pagina']):'';

                /**
                 * Empieza
                 */

                $query_cont=(new \yii\db\Query())
                                ->select([
                                            'count(distinct (a.PK_ASIGNACION)) as count',
                                            ])
                                ->from('tbl_asignaciones as a')
                                ->join('inner JOIN','tbl_empleados',
                                    'a.fk_empleado = tbl_empleados.PK_empleado')
                                ->join('LEFT JOIN','tbl_perfil_empleados',
                                    'tbl_perfil_empleados.FK_empleado = tbl_empleados.PK_empleado')
                                ->join('LEFT JOIN','tbl_cat_puestos',
                                    'tbl_perfil_empleados.fk_puesto = tbl_cat_puestos.PK_puesto')
                                ->join('LEFT JOIN','tbl_cat_unidades_negocio',
                                    'tbl_perfil_empleados.FK_RAZON_SOCIAL = tbl_cat_unidades_negocio.PK_UNIDAD_NEGOCIO')
                                ->join('LEFT JOIN','tbl_periodos',
                                    'tbl_periodos.FK_Asignacion = a.PK_asignacion')
                                ->join('LEFT JOIN','tbl_documentos',
                                    // 'tbl_documentos.FK_ASIGNACION = tbl_periodos.FK_ASIGNACION and YEAR(tbl_periodos.fecha_ini) = '.date('Y').' AND MONTH(tbl_periodos.fecha_ini) = '.date('m').' and tbl_periodos.fecha_ini = (select max(fecha_ini) from tbl_periodos where fk_asignacion=a.pk_asignacion)')
                                    'tbl_documentos.FK_ASIGNACION = tbl_periodos.FK_ASIGNACION ')
                                ->join('LEFT JOIN','tbl_cat_estatus_asignaciones',
                                   'tbl_cat_estatus_asignaciones.pk_estatus_asignacion = a.FK_ESTATUS_ASIGNACION')
                                ->join('LEFT JOIN','tbl_asignaciones_reemplazos as ar',
                                    'ar.FK_ASIGNACION = a.PK_ASIGNACION')
                                ->join('LEFT JOIN','tbl_empleados emp',
                                    'ar.fk_empleado = emp.PK_empleado')
                                ->andFilterWhere(
                                ['or',
                                    ['LIKE', "CONCAT(tbl_empleados.NOMBRE_EMP,' ', tbl_empleados.APELLIDO_PAT_EMP ,' ',tbl_empleados.APELLIDO_MAT_EMP)", $nombre],
                                    ['LIKE', 'a.NOMBRE', $nombre],
                                    ['LIKE', "CONCAT(emp.NOMBRE_EMP,' ', emp.APELLIDO_PAT_EMP ,' ',emp.APELLIDO_MAT_EMP)", $nombre],
                                ])
                                ->andFilterWhere(
                                ['and',
                                    // ['LIKE', 'tbl_empleados.NOMBRE_EMP', $nombre],
                                    //['=', 'a.FK_ESTATUS_ASIGNACION', $estatus],
                                    ['=', 'a.FK_CLIENTE', $cliente],
                                    ['=', 'tbl_documentos.FK_TIPO_DOCUMENTO', $documentos],
                                    // ['=', 'tbl_documentos.NUM_DOCUMENTO', $NumDoc],
                                    ['=', 'a.FK_PROYECTO', $proyectos],
                                    ['=', 'a.FK_CONTACTO', $contacto],
                                    ['=', 'a.FK_UBICACION', $ubicaciones],
                                    ['IN', 'a.FK_UNIDAD_NEGOCIO', $razon_social],
                                    ['=', 'tbl_perfil_empleados.FK_RAZON_SOCIAL', $empresa],
                                    ['=', 'a.FK_RESPONSABLE_OP', $fk_responsable_op],
                                    $op1,
                                    $op2,
                                    $op3,
                                    ['not in', 'a.PK_ASIGNACION',$bolsa_asignacion]
                                ])
                                ->andFilterWhere(
                                ['and',
                                    ['LIKE', 'tbl_documentos.NUM_DOCUMENTO', $NumDoc],
                                ])
                                ->andFilterWhere(
                                ['and',
                                    (!empty($estatus))?['=', 'a.FK_ESTATUS_ASIGNACION', $estatus]:((!empty($nombre)|| !empty($NumDoc))?['=', 'a.FK_ESTATUS_ASIGNACION', $estatus]:['NOT IN', 'a.FK_ESTATUS_ASIGNACION', [1,3,4,5,6,7,8]]),
                                ])
                                ->one()['count'];
                if($query_cont<$tamanio_pagina)
                {
                    $pagina=1;
                }
                $paginas=$query_cont/$tamanio_pagina;
                if($pagina>$paginas){
                    $pagina=(int)$paginas+1;
                }

                $dataProvider = new ActiveDataProvider([
                    'query'=>(new \yii\db\Query())
                                ->select([
                                            'distinct (a.PK_ASIGNACION)',
                                            'e.NOMBRE_EMP',
                                            'e.APELLIDO_PAT_EMP',
                                            'e.APELLIDO_MAT_EMP',
                                            'e.FOTO_EMP',
                                            'DATE_FORMAT(a.FECHA_INI, \'%d/%m/%Y\') as FECHA_INI',
                                            'DATE_FORMAT(a.FECHA_FIN, \'%d/%m/%Y\') as FECHA_FIN',
                                            'a.TARIFA',
                                            'tbl_cat_puestos.DESC_PUESTO',
                                            'a.MONTO',
                                            'ea.DESC_ESTATUS_ASIGNACION',
                                            'a.FK_ESTATUS_ASIGNACION',
                                            'a.NOMBRE',
                                            'a.fk_empleado',
                                            'cl.alias_cliente',
                                            'cl.NOMBRE_CLIENTE',
                                            'a.FK_USUARIO',
                                            '(
                                                SELECT td2.NUM_DOCUMENTO
                                                    FROM tbl_documentos td2
                                                    INNER join tbl_periodos p2
                                                    on p2.FK_ASIGNACION = td2.FK_ASIGNACION
                                                    and YEAR(
                                                            p2.fecha_ini
                                                        ) = '.date('Y').'
                                                    AND MONTH(
                                                            p2.fecha_ini
                                                    ) = '.date('m').'
                                                    AND td2.FK_TIPO_DOCUMENTO=2
                                                    AND td2.PK_DOCUMENTO= p2.FK_DOCUMENTO_ODC
                                                    where p2.fk_asignacion= a.pk_asignacion
                                                    limit 1
                                            ) as NUM_DOCUMENTO',
                                            'cl.HORAS_ASIGNACION',
                                            'a.FK_CAT_TARIFA',
                                            ])
                                ->from('tbl_asignaciones as a')
                                ->join('inner JOIN','tbl_empleados as e',
                                    'a.fk_empleado = e.PK_empleado')
                                ->join('LEFT JOIN','tbl_perfil_empleados',
                                    'tbl_perfil_empleados.FK_empleado = e.PK_empleado')
                                ->join('LEFT JOIN','tbl_clientes as cl',
                                    'a.FK_CLIENTE = cl.PK_cliente')
                                ->join('LEFT JOIN','tbl_cat_puestos',
                                    'tbl_perfil_empleados.fk_puesto = tbl_cat_puestos.PK_puesto')
                                ->join('LEFT JOIN','tbl_cat_unidades_negocio',
                                    'tbl_perfil_empleados.FK_RAZON_SOCIAL = tbl_cat_unidades_negocio.PK_UNIDAD_NEGOCIO')
                                ->join('LEFT JOIN','tbl_periodos p',
                                    'p.FK_Asignacion = a.PK_asignacion')
                                ->join('LEFT JOIN','tbl_documentos td',
                                    'td.FK_ASIGNACION = p.FK_ASIGNACION ')
                                ->join('LEFT JOIN','tbl_cat_estatus_asignaciones ea',
                                   'ea.pk_estatus_asignacion = a.FK_ESTATUS_ASIGNACION')
                                ->join('LEFT JOIN','tbl_asignaciones_reemplazos as ar',
                                    'ar.FK_ASIGNACION = a.PK_ASIGNACION')
                                ->join('LEFT JOIN','tbl_empleados as e_ar',
                                    'ar.FK_EMPLEADO = e_ar.PK_EMPLEADO')
                                ->andFilterWhere(
                                ['or',
                                    ['LIKE', "CONCAT(e.NOMBRE_EMP,' ', e.APELLIDO_PAT_EMP ,' ',e.APELLIDO_MAT_EMP)", $nombre],
                                    ['LIKE', "CONCAT(e_ar.NOMBRE_EMP,' ', e_ar.APELLIDO_PAT_EMP ,' ',e_ar.APELLIDO_MAT_EMP)", $nombre],
                                    ['LIKE', 'a.NOMBRE', $nombre],
                                ])
                                ->andFilterWhere(
                                ['and',
                                    //['=', 'a.FK_ESTATUS_ASIGNACION', $estatus],
                                    ['=', 'a.FK_CLIENTE', $cliente],
                                    ['=', 'td.FK_TIPO_DOCUMENTO', $documentos],
                                    ['=', 'a.FK_PROYECTO', $proyectos],
                                    ['=', 'a.FK_CONTACTO', $contacto],
                                    ['=', 'a.FK_UBICACION', $ubicaciones],
                                    ['IN', 'a.FK_UNIDAD_NEGOCIO', $razon_social],
                                    ['=', 'tbl_perfil_empleados.FK_RAZON_SOCIAL', $empresa],
                                    ['=', 'a.FK_RESPONSABLE_OP', $fk_responsable_op],
                                    $op1,
                                    $op2,
                                    $op3,
                                    ['not in', 'a.PK_ASIGNACION',$bolsa_asignacion]
                                ])
                                ->andFilterWhere(
                                ['and',
                                    ['LIKE', 'td.NUM_DOCUMENTO', $NumDoc],
                                ])
                                ->andFilterWhere(
                                ['and',
                                    (!empty($estatus))?['=', 'a.FK_ESTATUS_ASIGNACION', $estatus]:((!empty($nombre)|| !empty($NumDoc))?['=', 'a.FK_ESTATUS_ASIGNACION', $estatus]:['NOT IN', 'a.FK_ESTATUS_ASIGNACION', [1,3,4,5]]),
                                ])
                                ->groupBy(['a.PK_ASIGNACION'])
                                ->orderBy('a.FK_ESTATUS_ASIGNACION ASC')
                            ,
                    'pagination' => [
                        'pageSize' => $tamanio_pagina,
                        'page' => $pagina-1,
                    ],
                ]);

                $resultado=$dataProvider->getModels();
                foreach ($resultado as $key => $value) {
                     // $resultado[$key]['FK_ESTATUS_ASIGNACION']=2;
                    /*if($resultado[$key]['FK_ESTATUS_ASIGNACION']!='4'&&$resultado[$key]['FK_ESTATUS_ASIGNACION']!='5'){
                        $date1 = str_replace('/','-',$resultado[$key]['FECHA_INI']);
                        $date1_fin = str_replace('/','-',$resultado[$key]['FECHA_FIN']);
                        $date2 = date('Y-m-d');
                        $fecha_inicio = strtotime($date1);
                        $fecha_fin = strtotime($date1_fin);
                        $actual = strtotime($date2);

                        if($fecha_inicio>$actual){
                            $query= (new \yii\db\Query())->createCommand()->update('tbl_asignaciones',['FK_ESTATUS_ASIGNACION'=>'1'],"PK_ASIGNACION =".$resultado[$key]['PK_ASIGNACION'])->execute();
                            $query= (new \yii\db\Query())->createCommand()->update('tbl_perfil_empleados',['FK_ESTATUS_RECURSO'=>'2'],"FK_EMPLEADO =".$resultado[$key]['fk_empleado'])->execute();
                            $resultado[$key]['FK_ESTATUS_ASIGNACION']='1';
                            $resultado[$key]['DESC_ESTATUS_ASIGNACION']="PENDIENTE";
                        }else
                        if ($fecha_inicio <= $actual){
                            $query= (new \yii\db\Query())->createCommand()->update('tbl_asignaciones',['FK_ESTATUS_ASIGNACION'=>'2'],"PK_ASIGNACION =".$resultado[$key]['PK_ASIGNACION'])->execute();
                            $query= (new \yii\db\Query())->createCommand()->update('tbl_perfil_empleados',['FK_ESTATUS_RECURSO'=>'2'],"FK_EMPLEADO =".$resultado[$key]['fk_empleado'])->execute();
                            $resultado[$key]['FK_ESTATUS_ASIGNACION']='2';
                            $resultado[$key]['DESC_ESTATUS_ASIGNACION']='EN EJECUCI&Oacute;N';
                        }

                        if($fecha_fin<$actual){
                            $query= (new \yii\db\Query())->createCommand()->update('tbl_asignaciones',['FK_ESTATUS_ASIGNACION'=>'3'],"PK_ASIGNACION =".$resultado[$key]['PK_ASIGNACION'])->execute();
                            $resultado[$key]['FK_ESTATUS_ASIGNACION']='3';
                            $resultado[$key]['DESC_ESTATUS_ASIGNACION']="FINALIZADO";
                        }
                        // $resultado[$key]['DESC_ESTATUS_ASIGNACION']="$date1 $date1_fin";

                    }  */
                    $resultado[$key]['MONTO']=number_format((float)$resultado[$key]['MONTO'],2);
                    $resultado[$key]['TARIFA']=number_format((float)$resultado[$key]['TARIFA'],2);
                    /*if($ids_pendientes_aprobar){
                        $resultado[$key]['BANDEJA']=1;
                    }*/
                }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $res = array(
                    'post'        => $post,
                    'pagina'        => $pagina,
                    // 'data'          => $dataProvider->getModels(),
                    'data'          => $resultado,
                    'total_paginas' => ceil($query_cont / $tamanio_pagina),
                    'total_registros' => $query_cont,
                    'cambioLiga' => $validadorBandeja,
                    // 'query' => dataProvider_last_query($dataProvider),
                    'ids_por_cobrar'  => $ids_por_cobrar,
                );

                return $res;
            }
            else{
                $modelResponsableOP = (new \yii\db\Query)
                        ->select([
                            "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
                            'e.PK_EMPLEADO',
                        ])
                        ->from('tbl_empleados e')

                        ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
                        ->where(['p.FK_AREA'=>'5'])->andwhere(['or',['p.FK_ESTATUS_RECURSO'=>'3'],['p.FK_ESTATUS_RECURSO'=>'5']])
                        ->orderBy('nombre_emp DESC')->all();
                return $this->render('index', [
                    'total_paginas' => 0,
                    'total_registros' => '',
                    'modelResponsableOP'  => $modelResponsableOP,
                ]);
            }




            return $this->render('index', [

            ]);

        }

        public function actionView($id)
        {

          $modelSubirDoc = new SubirArchivo();
          $modelSubirDoc->extensions = 'pdf, docx';
          $modelSubirDoc->noRequired = true;

          $modelSubirDocHde = new SubirArchivo();
          $modelSubirDocHde->extensions = 'pdf, docx';
          $modelSubirDocHde->noRequired = true;

          $modeloDocumentos = new TblDocumentos;
          $modeloHde = new TblDocumentos;
          $model= $this->findModel($id);


            //Cargar OdC

            if ($modeloDocumentos->load(Yii::$app->request->post())) {
                $datos = Yii::$app->request->get();
                $data = Yii::$app->request->post();
                $this->guardarDocs($modeloDocumentos, $datos, $data, $modelSubirDoc, $id, $modelSubirDocHde);

            }
            $connection = \Yii::$app->db;
            $sql = $connection->createCommand(" select
                                a.PK_ASIGNACION,
                                e.NOMBRE_EMP,
                                e.PK_EMPLEADO,
                                e.APELLIDO_PAT_EMP,
                                a.NOMBRE,
                                a.TARIFA,
                                a.HORAS,
                                a.FECHA_INI,
                                a.FECHA_FIN,
                                a.MONTO,
                                c.NOMBRE_CLIENTE,
                                c.PK_CLIENTE,
                                c.HORAS_ASIGNACION,
                                c.RFC,
                                a.FK_ESTATUS_ASIGNACION,
                                cont.NOMBRE_CONTACTO,
                                t.PK_CAT_TARIFA,
                                t.DESC_TARIFA
                                from tbl_asignaciones a
                                    left join tbl_clientes c
                                    on a.FK_CLIENTE = c.PK_CLIENTE
                                    inner join tbl_cat_contactos cont
                                    on a.FK_CONTACTO = cont.PK_CONTACTO
                                    left join tbl_empleados e
                                    on a.FK_EMPLEADO = e.PK_EMPLEADO
                                    left join tbl_cat_tarifas t
                                        on t.PK_CAT_TARIFA = a.FK_CAT_TARIFA
                                    where a.PK_ASIGNACION = ".($id))->queryOne();

            $modelUnidadNegocio = $connection->createCommand("select
                                a.PK_ASIGNACION,
                                u.PK_UNIDAD_NEGOCIO,
                                u.DESC_UNIDAD_NEGOCIO
                                from tbl_asignaciones a
                                inner join tbl_cat_unidades_negocio u
                                on a.PK_ASIGNACION =".($id).
                                " and a.FK_UNIDAD_NEGOCIO = u.PK_UNIDAD_NEGOCIO")->queryOne();

            $modelComentariosAsignaciones3= TblBitComentariosAsignaciones::find()->where(['FK_ASIGNACION'=>$sql['PK_ASIGNACION']])->andWhere(['=','FK_ESTATUS_ASIGNACION',5])->orderBy(['FECHA_FIN' => SORT_ASC])->asArray()->all();

            $modelSeguimiento = new TblAsignacionesSeguimiento();
            $modelPeriodo = new TblPeriodos();
            $modelSeguimientoRegistros = (new \yii\db\Query())
                                    ->select("
                                        tbl_asignaciones_seguimiento.COMENTARIOS,
                                        tbl_asignaciones_seguimiento.FECHA_REGISTRO,
                                        tbl_usuarios.NOMBRE_COMPLETO
                                        ")
                                    ->from("tbl_asignaciones_seguimiento")
                                    ->join('LEFT JOIN', 'tbl_usuarios', 'tbl_asignaciones_seguimiento.FK_USUARIO = tbl_usuarios.PK_USUARIO')
                                    ->where(['FK_ASIGNACION'=>$id])
                                    ->orderBy('PK_SEGUIMIENTO DESC')
                                    ->all();

        $datos = Yii::$app->request->get();
        $connection = \Yii::$app->db;

        $sql = $connection->createCommand(" select
                            a.PK_ASIGNACION,
                            e.NOMBRE_EMP,
                            e.PK_EMPLEADO,
                            e.APELLIDO_PAT_EMP,
                            a.NOMBRE,
                            a.TARIFA,
                            a.HORAS,
                            a.FECHA_INI,
                            a.FECHA_FIN,
                            a.MONTO,
                            c.NOMBRE_CLIENTE,
                            c.PK_CLIENTE,
                            c.HORAS_ASIGNACION,
                            c.RFC,
                            a.FK_ESTATUS_ASIGNACION,
                            cont.NOMBRE_CONTACTO,
                            t.PK_CAT_TARIFA,
                            t.DESC_TARIFA
                            from tbl_asignaciones a
                                left join tbl_clientes c
                                on a.FK_CLIENTE = c.PK_CLIENTE
                                inner join tbl_cat_contactos cont
                                on a.FK_CONTACTO = cont.PK_CONTACTO
                                left join tbl_empleados e
                                on a.FK_EMPLEADO = e.PK_EMPLEADO
                                left join tbl_cat_tarifas t
                                    on t.PK_CAT_TARIFA = a.FK_CAT_TARIFA
                                where a.PK_ASIGNACION = ".($datos['id']))->queryOne();


            $cliente = tblclientes::findOne($model->FK_CLIENTE);
            $contacto = tblcatcontactos::find()->where(['PK_CONTACTO'=>$model->FK_CONTACTO])->limit(1)->one();

            $empleado = tblempleados::find()->where(['PK_EMPLEADO'=>$model->FK_EMPLEADO])->limit(1)->one();
            $pefil = tblperfilempleados::find()->where(['FK_EMPLEADO'=>$empleado->PK_EMPLEADO])->limit(1)->one();
            $puesto = tblcatpuestos::find()->where(['PK_PUESTO'=>$pefil->FK_PUESTO])->limit(1)->one();
            $estatus = tblcatestatusasignaciones::find()->where(['PK_ESTATUS_ASIGNACION'=>$model->FK_ESTATUS_ASIGNACION])->limit(1)->one();
            $rankTecnico = TblCatRankTecnico::find()->where(['PK_RANK_TECNICO'=>$pefil->FK_RANK_TECNICO])->limit(1)->one();
            $reemplazos = TblAsignacionesReemplazos::find()->where(['FK_ASIGNACION'=>$model->PK_ASIGNACION])->orderBy(['PK_ASIGNACION_REEMPLAZO' => SORT_DESC])->asArray()->limit(1)->one();
            $modelComentariosAsignaciones2= TblBitComentariosAsignaciones::find()->where(['FK_ASIGNACION'=>$model->PK_ASIGNACION])->andWhere(['=','FK_ESTATUS_ASIGNACION',6])->orderBy(['FECHA_FIN' => SORT_DESC])->asArray()->all();
            $modelComentariosAsignaciones3= TblBitComentariosAsignaciones::find()->where(['FK_ASIGNACION'=>$model->PK_ASIGNACION])->andWhere(['=','FK_ESTATUS_ASIGNACION',5])->orderBy(['FECHA_FIN' => SORT_DESC])->asArray()->all();
            $descTarifa = TblCatTarifas::find()->where(['PK_CAT_TARIFA'=>$model->FK_CAT_TARIFA])->asArray()->one()['DESC_TARIFA'];

            $fue_detenida=false;
            if($reemplazos&&$modelComentariosAsignaciones2){
                $fue_detenida=true;
            }

            $tblreemplazos = (new \yii\db\Query)
                        ->select([
                            "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
                            'tr.FECHA_INICIO',
                            'tr.FECHA_FIN',
                            'p.TARIFA',
                        ])
                        ->from('tbl_asignaciones_reemplazos tr')
                        ->join('inner join', 'tbl_empleados e','e.PK_EMPLEADO =  tr.FK_EMPLEADO')
                        ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
                        ->where(['tr.FK_ASIGNACION'=>$model->PK_ASIGNACION])
                        //->andWhere(['<>','tr.FK_EMPLEADO',$model->FK_EMPLEADO])
                        ->orderBy('PK_ASIGNACION_REEMPLAZO DESC')->all();

            $asignaciones = TblAsignaciones::find()->where(['PK_ASIGNACION'=>$model->PK_ASIGNACION])->asArray()->limit(1)->one();
            $unidaddenegocio = TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO'=>$pefil->FK_UNIDAD_NEGOCIO])->limit(1)->one();

            /* INICIO HRIBI 160316 - Permite mostrar la unidad de negocio actual que esta vinculada a la asignación (tomando como referencia la asignación en la que esta
                                     y el empleado que esta asignado a ella, buscando el ultimo registro con estas coincidencia en TblBitUnidadNegocioAsig), si por alguna razón
                                     no se existe algún registro en la bitacora de unidades_negocio_Asignaciones, entonces toma la unidad de negocio que fue registrada para
                                     el empleado en su perfil. */
            $modelBitUnidadNegocioAsig = TblBitUnidadNegocioAsig::find()->where(['FK_REGISTRO'=>$model->PK_ASIGNACION])->andwhere(['FK_EMPLEADO'=>$empleado->PK_EMPLEADO])->andwhere(['like','MODULO_ORIGEN','asignaciones'])->orderBy(['PK_UNIDAD_NEGOCIO_ASIG' => SORT_DESC])->limit(1)->one();
            //$modelBitUnidadNegocioAsig = TblBitUnidadNegocioAsig::find()->where(['FK_ASIGNACION'=>$model->PK_ASIGNACION])->orderBy('PK_UNIDAD_NEGOCIO_ASIG ASC')->limit(1)->one();
            if(!empty($modelBitUnidadNegocioAsig)&&$model->FK_ESTATUS_ASIGNACION!=7){
                $unidaddenegocio = TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO'=>$modelBitUnidadNegocioAsig->FK_UNIDAD_NEGOCIO])->limit(1)->one();
            }
            else
            /*if($model->FK_ESTATUS_ASIGNACION==7||$model->FK_ESTATUS_ASIGNACION==1){
                $unidaddenegocio = TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO'=>$model->FK_UNIDAD_NEGOCIO])->limit(1)->one();
            }else*/{
                $unidaddenegocio = TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO'=>$pefil->FK_UNIDAD_NEGOCIO])->limit(1)->one();
            }
            /* FIN HRIBI 160316 */

            $modelComentariosAsignaciones = new TblBitComentariosAsignaciones();


            $extra['NOMBRE_CLIENTE']      = $cliente->NOMBRE_CLIENTE;
            $extra['NOMBRE_CONTACTO']     = $contacto->NOMBRE_CONTACTO.' '.$contacto->APELLIDO_PAT;
            $extra['TEL_OFICINA']         = $contacto->TEL_OFICINA;
            $extra['TEL_EXTENSION']       = $contacto->TEL_EXTENSION;
            $extra['TEL_CELULAR']         = $contacto->TEL_CELULAR;
            $extra['EMAIL']               = $contacto->EMAIL;

            $extra['NOMBRE_EMP']          = $empleado->NOMBRE_EMP.' '.$empleado->APELLIDO_PAT_EMP;
            $extra['DESC_PUESTO']         = $puesto->DESC_PUESTO;
            $extra['ESTATUSID']           = $estatus->PK_ESTATUS_ASIGNACION;
            $extra['ESTATUSDESC']         = $estatus->DESC_ESTATUS_ASIGNACION;
            $extra['DESC_RANK_TECNICO']   = $rankTecnico->DESC_RANK_TECNICO;
            $extra['DESC_UNIDAD_NEGOCIO'] = $unidaddenegocio->DESC_UNIDAD_NEGOCIO;
            $extra['CANT_REEMPLAZOS']     = $reemplazos;
            $extra['FUE_DETENIDA']        = ($fue_detenida)?$modelComentariosAsignaciones2:[];
            $extra['ASIGNACIONES']        = $asignaciones;
            $extra['DESC_TARIFA']        = ($descTarifa)?$descTarifa:'';

            /* INICIO HRIBI 240316 - Valida si se muestra la fecha de asignación o la del reemplazo, dependerá de cual sea mayor,
            cuando se hace un reemplazo la fecha_fin de la asignación es actualizada con la del reemplazo independientemente si es mayor o menor
            a la originarl, pero una vez hecho el reemplazo las fechas pueden ser extendidas, si en la modificaición la fecha es mayor a la del reemplazo
            ahora la fecha_fin que mandará será la de la asignación y no la de la tabla de reemplazos. */



              $fecha_fin_a = str_replace('/','-',$model->FECHA_FIN);
              $fecha_fin_asig_original = strtotime($fecha_fin_a);
              if($reemplazos['FECHA_FIN']){
                  $fecha_fin_r = str_replace('/','-',$reemplazos['FECHA_FIN']);
                  $fecha_fin_reemplazo = strtotime($fecha_fin_r);

                  if($fecha_fin_reemplazo>=$fecha_fin_asig_original){
                      $extra['ASIGNACIONES']['FECHA_FIN'] = $reemplazos['FECHA_FIN'];
                      $extra['ASIGNACIONES']['FECHA_INI'] = $reemplazos['FECHA_INICIO'];
                  }else{
                      $asignaciones['FECHA_FIN'] = $fecha_fin_asig_original;
                  }
              }else{
                  $extra['ASIGNACIONES']['FECHA_FIN'] = $asignaciones['FECHA_FIN'];
                  $extra['ASIGNACIONES']['FECHA_INI'] = $asignaciones['FECHA_INI'];
              }
              $extra['reemplazos']=$tblreemplazos;



            /* FIN HRIBI 240316 */

            /* Inicio Cambio de Tarifas */

            $periodos = TblPeriodos::find()->where(['FK_ASIGNACION' => $id])->all();

            $MontoTotal = 0;

             if ($periodos) {
                foreach ($periodos as $key) {
                    $cambio = TblBitAsignacionTarifas::find()->where(['FK_PERIODO' => $key->PK_PERIODO])->limit(1)->one();

                    if($cambio) {
                        $extra['NUEVA_TARIFA'] = $key->TARIFA;
                        break;
                    }

                    /*if ($key->TARIFA != $model->TARIFA) {
                        $extra['NUEVA_TARIFA'] = $key->TARIFA;
                    }*/
                    $MontoTotal = $MontoTotal + $key->MONTO;
                }
                $model->MONTO = $MontoTotal;
            }

            /*else {

                $fechaIni = explode('-', $model->FECHA_INI);
                $fechaFin = explode('-', $model->FECHA_FIN);

                $per = 0;

                if ($fechaFin[0] > $fechaIni[0]) {
                    $per = (($fechaFin[1] - $fechaIni[1]) + 1) * 12;
                } else {
                    $per = ($fechaFin[1] - $fechaIni[1]) + 1;
                }

                $model->MONTO = ($model->HORAS * $model->TARIFA) * $per;
            }*/

            /* Fin Cambio de Tarifas */

            if($modelComentariosAsignaciones3){
                $periodosAsignacion = (new \yii\db\Query())
                        ->select('PK_PERIODO, FECHA_INI, FECHA_FIN, TARIFA')
                        ->from('tbl_periodos')
                        ->where(['FK_ASIGNACION' => $id ])
                        ->andWhere(['<','FECHA_INI',$modelComentariosAsignaciones3[0]['FECHA_FIN'] ])
                        ->orderBy(['FECHA_INI' => SORT_ASC])
                        ->all();

                        $tarifas = '';
                        if ($periodosAsignacion) {
                            foreach ($periodosAsignacion as $key) {
                                $tarifas[$key['PK_PERIODO']] = $key['TARIFA'];
                            }
                        }

            }else{
                $periodosAsignacion = (new \yii\db\Query())
                        ->select('PK_PERIODO, FECHA_INI, FECHA_FIN, TARIFA')
                        ->from('tbl_periodos')
                        ->where(['FK_ASIGNACION' => $id ])
                        ->orderBy(['FECHA_INI' => SORT_ASC])
                        ->all();

                        $tarifas = '';
                        if ($periodosAsignacion) {
                            foreach ($periodosAsignacion as $key) {
                                $tarifas[$key['PK_PERIODO']] = $key['TARIFA'];
                            }
                        }

            }
            $reemplazos = TblAsignacionesReemplazos::find()->where(['FK_ASIGNACION' => $id ])->orderBy(['PK_ASIGNACION_REEMPLAZO' => SORT_DESC])->asArray()->limit(1)->one();
            $cat_tarifas=[];
            //if($sql['DESC_TARIFA']){
                $fks_cat_tarifas = TblTarifasClientes::find()->select(['FK_CAT_TARIFA'])->where(['FK_CLIENTE'=>$sql['PK_CLIENTE']])->column();
                $cat_tarifas= TblCatTarifas::find()->andWhere(['IN','PK_CAT_TARIFA',$fks_cat_tarifas])->asArray()->all();
            //}

            $queryAnios = "(SELECT
                          DISTINCT YEAR(FECHA_INI) AS AÑO
                          FROM
                          tbl_periodos
                          WHERE FK_ASIGNACION = $id
                          ORDER BY AÑO ASC)";

            $anios = $connection->createCommand($queryAnios)->queryAll();

            return $this->render('view', [
                'model' => $model,
                'extra' => $extra,
                'periodosAsignacion' => $periodosAsignacion,
                'sql' => $sql,
                'tarifas' => $tarifas,
                'cliente' => $cliente,
                'contacto' => $contacto,
                'modelComentariosAsignaciones' => $modelComentariosAsignaciones,
                'modelSeguimiento' => $modelSeguimiento,
                'modelPeriodo' => $modelPeriodo,
                'modelSeguimientoRegistros' => $modelSeguimientoRegistros,
                'modelComentariosAsignaciones2' => $modelComentariosAsignaciones2,
                'modelComentariosAsignaciones3' => $modelComentariosAsignaciones3,
                'sql' => $sql,
                'modelSubirDoc' => $modelSubirDoc,
                'cat_tarifas' => $cat_tarifas,
                'periodosAsignacion' => $periodosAsignacion,
                'anios'       => $anios,
                'modeloHde' => $modeloHde,
                'modelSubirDocHde' => $modelSubirDocHde
            ]);

            $connection->close();
        }

        public function actionCreate()
        {
          $model = new TblAsignaciones();
          $modelRemplazo = new TblAsignacionesReemplazos();
          $modelResponsableOP = (new \yii\db\Query)
                        ->select([
                            "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
                            'e.PK_EMPLEADO',
                        ])
                        ->from('tbl_empleados e')

                        ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
                        ->where(['p.FK_AREA'=>'5'])->andwhere(['or',['p.FK_ESTATUS_RECURSO'=>'3'],['p.FK_ESTATUS_RECURSO'=>'5']])
                        ->orderBy('nombre_emp DESC')->all();
            $pkProyecto = 0;

            if (Yii::$app->request->post()) {
                $data = Yii::$app->request->post();

                $unidaddenegocio= $data['fk_unidad_negocio'];

                if(!is_numeric($data['TblAsignaciones']['FK_PROYECTO'])){
                    if(!empty($data['TblAsignaciones']['FK_PROYECTO'])){
                        $pkProyecto = $data['TblAsignaciones']['FK_PROYECTO'];
                        $proyecto = new tblcatproyectos();
                        $proyecto -> NOMBRE_PROYECTO = $data['TblAsignaciones']['FK_PROYECTO'];
                        $proyecto -> FK_CONTACTO = $data['TblAsignaciones']['FK_CONTACTO'];
                        $proyecto -> FECHA_REGISTRO = date('Y-m-d');
                        $proyecto -> save(false);
                        $pkProyecto = $proyecto -> PK_PROYECTO;
                    }else{
                        $pkProyecto = NULL;
                    }
                }
                else {
                    if(!empty($data['TblAsignaciones']['FK_PROYECTO'])){
                        $pkProyecto = $data['TblAsignaciones']['FK_PROYECTO'];
                    }else{
                        $pkProyecto = NULL;
                    }
                }

                /* INICIO SCM 29/06/2016 - Se agre validacion para ver si el empleado asignado tiene empleados a su cargo*/
                if(isset($data['emp_reasignar'])){
                    foreach ($data['emp_reasignar'] as $key => $value) {
                        if($value != ''){
                            $modelPerfilNuevoJefe = tblperfilempleados::find()->where(['FK_EMPLEADO' => $key])->limit(1)->one();
                            $modelPerfilNuevoJefe->FK_JEFE_DIRECTO = $value;
                            $modelPerfilNuevoJefe->save(false);
                        }
                    }
                }
                /* FIN SCM 29/06/2016 */
                foreach ($data['idEmp'] as $key => $idEmpleado) {

                    //Modificaciones al perfil
                    $perfil= tblperfilempleados::find()->where(['FK_EMPLEADO'=>$idEmpleado])->limit(1)->one();

                    $model = new TblAsignaciones();
                    $model -> FECHA_INI = transform_date($data['inicio'][$idEmpleado],'Y-m-d');
                    $model -> FECHA_FIN = transform_date($data['fin'][$idEmpleado],'Y-m-d');
                    $model -> NOMBRE = $data['TblAsignaciones']['NOMBRE'];
                    $model -> FK_CONTACTO = $data['TblAsignaciones']['FK_CONTACTO'];
                    $model -> FK_PROYECTO = $pkProyecto;
                    $model -> FK_UBICACION = $data['TblAsignaciones']['FK_UBICACION'];
                    //SE ASIGNA ID CATALOGO ESTATUS ASIGNACIONES
                    // $model -> FK_ESTATUS_ASIGNACION = 1;
                    $model -> FK_CLIENTE = $data['TblAsignaciones']['FK_CLIENTE'];
                    $model -> FK_RESPONSABLE_OP = $data['TblAsignaciones']['FK_RESPONSABLE_OP'];
                    //SE ASIGNA ID CATALOGO OPERACIONES
                    $model -> FK_OPERACION = 1;
                    $model -> FK_USUARIO = user_info()['PK_USUARIO'];
                    $model -> FK_EMPLEADO = $idEmpleado;
                    $model -> TARIFA = $data['tarifa'][$idEmpleado];
                    $model -> HORAS = $data['periodo'][$idEmpleado];
                    $model -> FK_CAT_TARIFA = (isset($data['pk_cat_tarifa_select'][$idEmpleado])?$data['pk_cat_tarifa_select'][$idEmpleado]:null);
                    $model -> PORC_RENTABILIDAD = $data['rentabilidad'][$idEmpleado];
                    $model -> MONTO = $data['monto'][$idEmpleado];
                    $model -> FECHA_REGISTRO = date('Y-m-d');
                    $model -> FK_UNIDAD_NEGOCIO = $unidaddenegocio;

                    $date1 = str_replace('/','-',$model->FECHA_INI);
                    $date1_fin = str_replace('/','-',$model->FECHA_FIN);
                    $date2 = date('Y-m-d');
                    $fecha_inicio = strtotime($date1);
                    $fecha_fin = strtotime($date1_fin);
                    $actual = strtotime($date2);

                    if($perfil->FK_ESTATUS_RECURSO == 1){
                        $model->FK_ESTATUS_ASIGNACION = 7;
                    } else {
                        if($fecha_inicio>$actual){
                            $model->FK_ESTATUS_ASIGNACION=1;
                        }else
                        if ($fecha_inicio <= $actual&& $fecha_fin >= $actual){
                            $model->FK_ESTATUS_ASIGNACION=2;
                        }else
                        if($fecha_fin<$actual){
                            $model->FK_ESTATUS_ASIGNACION=3;
                        }
                    }
                    $model->save(false);

                    if($model->FK_ESTATUS_ASIGNACION != 7){
                        $modelBitUnidadNegocioAsigN = new TblBitUnidadNegocioAsig();
                        $modelBitUnidadNegocioAsigN->FK_REGISTRO=$model->PK_ASIGNACION;
                        if(!empty(Yii::$app->request->post()['fk_unidad_negocio'])){
                            $modelBitUnidadNegocioAsigN->FK_UNIDAD_NEGOCIO=(Yii::$app->request->post()['fk_unidad_negocio']);
                        }

                        $modelBitUnidadNegocioAsigN->FK_EMPLEADO=$perfil->FK_EMPLEADO;
                        $modelBitUnidadNegocioAsigN->MODULO_ORIGEN = onToy();
                        $modelBitUnidadNegocioAsigN->FK_USUARIO = user_info()['PK_USUARIO'];
                        $modelBitUnidadNegocioAsigN->FECHA_CREACION=date('Y-m-d H:i:s');
                        $modelBitUnidadNegocioAsigN->save(false);

                        if(!empty(Yii::$app->request->post()['fk_unidad_negocio'])){
                            $model->FK_UNIDAD_NEGOCIO = (Yii::$app->request->post()['fk_unidad_negocio']);
                        }

                    }

                    $descripcionBitacora = 'FK_CLIENTE='.$model->FK_CLIENTE.',NOMBRE='.$model->NOMBRE.',FK_UBICACION='.$model->FK_UBICACION.',FK_PROYECTO='.$model->FK_PROYECTO;
                    user_log_bitacora($descripcionBitacora,'Registro de Asignación',$model->PK_ASIGNACION );
                    /* Condición para sólo cuando la asignación tenga estats => En Ejecución se afecte al empleado[REEMPLAZANTE]
                       en su valores de FK_ESTATUS_RECURSO, FK_UBICACION_FISICA y FK_UNIDAD_NEGOCIO */
                    if($model->FK_ESTATUS_ASIGNACION == 2){
                        $perfil->FK_UNIDAD_NEGOCIO = $unidaddenegocio;
                        $perfil->FK_UBICACION_FISICA = $model->FK_UBICACION;
                        $perfil->FK_ESTATUS_RECURSO=2;
                        $perfil->save(false);
                        user_log_bitacora_estatus_empleado($perfil->FK_EMPLEADO,$perfil->FK_ESTATUS_RECURSO);
                        user_log_bitacora_unidad_negocio($perfil->FK_EMPLEADO,$perfil->FK_UNIDAD_NEGOCIO,$model->PK_ASIGNACION);
                        user_log_bitacora_ubicacion_fisica($perfil->FK_EMPLEADO,$perfil->PK_PERFIL,$model->FK_UBICACION,'CLIENTE');
                    }
                    /* FIN HRIBI 16/03/16 */
                }
                return $this->redirect(['create', 'id' => $model->PK_ASIGNACION, 'action'=>'create']);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'modelResponsableOP' => $modelResponsableOP,
                ]);
            }

        }

        public function actionUpdate_asignacion($id)
        {
            //dd(Yii::$app->request->post());
            $model = TblAsignaciones::find()->where(['PK_ASIGNACION' => $id])->limit(1)->one();
            $modelAsignacionModificar = TblAsignaciones::find()->where(['PK_ASIGNACION' => $id])->limit(1)->one();
            $modelCatMotivosAsignaciondetenida  = new TblCatMotivos();
            $modelEmpleadoAsignado = tblempleados::find()->where(['PK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
            $modelPerfilEmpleado = TblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $modelEmpleadoAsignado->PK_EMPLEADO])->limit(1)->one();
            $modelCliente = tblclientes::find()->where(['PK_CLIENTE' => $model->FK_CLIENTE])->limit(1)->one();
            $modelUbicacion = TblCatUbicaciones::find()->where(['PK_UBICACION' => $model->FK_UBICACION])->limit(1)->one();
            $modelContactos = tblcatcontactos::find()->where(['PK_CONTACTO' => $model->FK_CONTACTO])->limit(1)->one();
            $modelProyectos = TblCatProyectos::find()->where(['PK_PROYECTO' => $model->FK_PROYECTO])->limit(1)->one();
            $modelComentariosAsignaciones = new TblBitComentariosAsignaciones(['scenario'=>'DETENER']);
            $modelComentariosAsignacionesCancelar = new TblBitComentariosAsignaciones(['scenario'=>'CANCELAR']);
            $modelVacantes = new TblVacantes();
            $ubicacionFisicaEmp = $modelPerfilEmpleado->FK_UBICACION;
            $estatusActualEmp = $modelPerfilEmpleado->FK_ESTATUS_RECURSO;

            $modelResponsableOP = (new \yii\db\Query)
                        ->select([
                            "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
                            'e.PK_EMPLEADO',
                        ])
                        ->from('tbl_empleados e')

                        ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
                        ->where(['p.FK_AREA'=>'5'])->andwhere(['or',['p.FK_ESTATUS_RECURSO'=>'3'],['p.FK_ESTATUS_RECURSO'=>'5']])
                        ->orderBy('nombre_emp DESC')->all();


            if($model->FK_PROYECTO)
            {
                $modelProyecto = TblCatProyectos::find()->where(['PK_PROYECTO' => $model->FK_PROYECTO])->limit(1)->one();
            }
            else
            {
                $modelProyecto = new TblCatProyectos();
            }

            $model->FECHA_INI= transform_date($model->FECHA_INI,'d/m/Y');
            $model->FECHA_FIN= transform_date($model->FECHA_FIN,'d/m/Y');
            $extra['proyectoDesc']  = $modelProyecto->NOMBRE_PROYECTO;
            $extra['ubicacionDesc'] = $modelUbicacion->DESC_UBICACION;
            $extra['contactoDesc'] = $modelContactos->NOMBRE_CONTACTO.' '.$modelContactos->APELLIDO_PAT;
            $pkProyecto = 0;
            $periodos = TblPeriodos::find()->where(['FK_ASIGNACION' => $id])->all();
            $tarifaAnterior = $model->TARIFA;

            if (Yii::$app->request->post()) {

                $data= Yii::$app->request->post();
                $model->load(Yii::$app->request->post());
                $model->FECHA_INI= transform_date($model->FECHA_INI,'Y-m-d');
                $model->FECHA_FIN= transform_date($model->FECHA_FIN,'Y-m-d');

                if(isset($data['pk_cat_tarifa_select'])){
                    $model->FK_CAT_TARIFA = $data['pk_cat_tarifa_select'];
                    $model->TARIFA = $data['TblPeriodos']['TARIFA'];
                }

                if($model->FK_ESTATUS_ASIGNACION != 4 && $model->FK_ESTATUS_ASIGNACION != 5 && $model->FK_ESTATUS_ASIGNACION != 6 && $estatusActualEmp != 4 && $estatusActualEmp != 6){//Si es diferente de Cerrado, CANCELADO O DETENIDA
                    $date1 = str_replace('/','-',$model->FECHA_INI);
                    $date1_fin = str_replace('/','-',$model->FECHA_FIN);
                    $date2 = date('Y-m-d');
                    $fecha_inicio = strtotime($date1);
                    $fecha_fin = strtotime($date1_fin);
                    $actual = strtotime($date2);

                    if($fecha_inicio>$actual){
                        $model->FK_ESTATUS_ASIGNACION=1;
                    }
                    else if ($fecha_inicio <= $actual&&$fecha_fin>$actual){
                        $model->FK_ESTATUS_ASIGNACION=2;
                    }
                    else if($fecha_fin<$actual){
                        $model->FK_ESTATUS_ASIGNACION=3;
                    }
                }else if($model->FK_ESTATUS_ASIGNACION == 6 && $data['TblAsignaciones']['FK_ESTATUS_ASIGNACION']==6 && $estatusActualEmp != 4 && $estatusActualEmp != 6){

                    $modelComentariosAsignaciones->FECHA_FIN= transform_date($data['TblBitComentariosAsignaciones']['FECHA_FIN'],'Y-m-d');
                    $modelComentariosAsignaciones->MOTIVO= $data['TblBitComentariosAsignaciones']['MOTIVO'];
                    $modelComentariosAsignaciones->FK_ASIGNACION= $id;
                    $modelComentariosAsignaciones->FK_ESTATUS_ASIGNACION= 6; //Estatus Asignación Detenida
                    $modelComentariosAsignaciones->COMENTARIOS= $data['TblBitComentariosAsignaciones']['COMENTARIOS'];
                    $modelComentariosAsignaciones->FECHA_REGISTRO= date('Y-m-d H:i:s');
                    $modelComentariosAsignaciones->save(false);

                    if ($data['TblBitComentariosAsignaciones']['MOTIVO'] == 'Baja Empleado'){
                        $modelVacantes->FK_PRIORIDAD = 4;
                        $modelVacantes->FECHA_CREACION = date('Y-m-d H:i:s');
                        $modelVacantes->FECHA_CIERRE = date('Y-m-d', strtotime('+15 day')) ; // Sumo 15 días
                        $modelVacantes->CANT_PERSONAS = 1;
                        $modelVacantes->FK_AREA = 9;
                        $modelVacantes->FK_TIPO_VACANTE = 2;
                        $modelVacantes->FK_ESTATUS_VACANTE = 1;
                        $modelVacantes->FK_ESTACION_VACANTE = 1;
                        $modelVacantes->FK_USUARIO = user_info()['PK_USUARIO'];
                        $modelVacantes->FK_ASIGNACION = $id;
                        $modelVacantes->FK_UBICACION_CLIENTE = $model->FK_UBICACION;
                        $modelVacantes->FK_UBICACION = $model->FK_UBICACION;
                        $modelVacantes->FK_CLIENTE = $model->FK_CLIENTE;
                        $modelVacantes->FK_CONTACTO =  $model->FK_CONTACTO;
                        $modelVacantes->FK_PUESTO =  $modelPerfilEmpleado->FK_PUESTO;
                        $modelVacantes->COSTO_MAXIMO = $modelPerfilEmpleado->SUELDO_NETO;
                        $modelVacantes->DESC_VACANTE = 'ASIGNACION'.'_'.$model->NOMBRE;
                        $modelVacantes->NOMBRE_PROYECTO = $modelProyectos != null ? $modelProyectos->NOMBRE_PROYECTO : null;
                        $modelVacantes->save(false);

                        $descripcionBitacora = 'ASIGNACION DETENIDA ='.$model->PK_ASIGNACION.' '.'NOMBRE ='.$model->NOMBRE.' '.'TARIFA ='.' '.$model->TARIFA;
                        user_log_bitacora($descripcionBitacora,'Asignacion Detenida',$model->PK_ASIGNACION);
                    }

                    if($modelPerfilEmpleado->FK_ESTATUS_RECURSO!=1 && $modelPerfilEmpleado->FK_ESTATUS_RECURSO!=4){
                        $modelPerfilEmpleado->FK_ESTATUS_RECURSO=3; //Estatus del recurso Disponible
                    }

                    $modelPerfilEmpleado->save(false);
                    $modelSeguimiento = new TblAsignacionesSeguimiento();
                    $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelSeguimiento->COMENTARIOS = $data['TblBitComentariosAsignaciones']['COMENTARIOS'];
                    $modelSeguimiento->FK_ASIGNACION = $id;
                    $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelSeguimiento->save(false);

                }else if($model->FK_ESTATUS_ASIGNACION == 4 && $data['TblAsignaciones']['FK_ESTATUS_ASIGNACION']==4){

                    $modelComentariosAsignaciones->FECHA_FIN= transform_date($data['TblBitComentariosAsignaciones']['FECHA_FIN'],'Y-m-d');
                    $modelComentariosAsignaciones->MOTIVO= "ASIGNACION_CERRADA";
                    $modelComentariosAsignaciones->FK_ASIGNACION= $id;
                    $modelComentariosAsignaciones->FK_ESTATUS_ASIGNACION= 4; //Estatus Asignación Cerrada
                    $modelComentariosAsignaciones->COMENTARIOS = "Se cierra asignacion desde la pantalla Modificación de asignación";
                    $modelComentariosAsignaciones->FECHA_REGISTRO= date('Y-m-d H:i:s');
                    $modelComentariosAsignaciones->save(false);

                    $modelPerfilEmpleado->save(false);
                    $modelSeguimiento = new TblAsignacionesSeguimiento();
                    $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelSeguimiento->COMENTARIOS = "Se cierra asignacion desde la pantalla Modificación de asignación";
                    $modelSeguimiento->FK_ASIGNACION = $id;
                    $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelSeguimiento->save(false);

                    $descripcionBitacora = 'ASIGNACION CERRADA ='.$model->PK_ASIGNACION.' '.'NOMBRE ='.$model->NOMBRE.' '.'TARIFA ='.' '.$model->TARIFA;
                    user_log_bitacora($descripcionBitacora,'Asignacion Cerrada',$model->PK_ASIGNACION);

                }else if($model->FK_ESTATUS_ASIGNACION == 5 && $data['TblAsignaciones']['FK_ESTATUS_ASIGNACION']==5){
                    $modelComentariosAsignacionesCancelar->FECHA_FIN= transform_date($data['TblBitComentariosAsignaciones']['FECHA_FIN'],'Y-m-d');
                    $modelComentariosAsignacionesCancelar->MOTIVO= "Cancelación";
                    $modelComentariosAsignacionesCancelar->FK_ASIGNACION= $id;
                    $modelComentariosAsignacionesCancelar->FK_ESTATUS_ASIGNACION= 5; //Estatus Asignación Cancelada
                    $modelComentariosAsignacionesCancelar->COMENTARIOS= $data['TblBitComentariosAsignaciones']['COMENTARIOS'];
                    $modelComentariosAsignacionesCancelar->FECHA_REGISTRO= date('Y-m-d H:i:s');
                    $modelComentariosAsignacionesCancelar->save(false);

                    $modelSeguimiento = new TblAsignacionesSeguimiento();
                    $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelSeguimiento->COMENTARIOS = $data['TblBitComentariosAsignaciones']['COMENTARIOS'];
                    $modelSeguimiento->FK_ASIGNACION = $id;
                    $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelSeguimiento->save(false);
                }

                $model->FK_PROYECTO=$data['TblAsignaciones']['FK_PROYECTO'];

                /*Tarifas*/
                $cambio = TblBitAsignacionTarifas::find()->select('FK_PERIODO')->where(['FK_ASIGNACION' => $id])->orderBy('FECHA_REGISTRO DESC')->asArray()->one();
                if ($cambio['FK_PERIODO']) {
                    foreach ($periodos as $key) {
                        if ($key->PK_PERIODO >= $cambio['FK_PERIODO']) {
                            $updateTarifa = TblPeriodos::findOne($key->PK_PERIODO);
                            $updateTarifa->TARIFA = $model->TARIFA;
                            $updateTarifa->MONTO = $model->TARIFA * $updateTarifa->HORAS;
                            $updateTarifa->save(false);
                        }
                    }

                } else {
                    foreach ($periodos as $key) {
                        $updateTarifa = TblPeriodos::findOne($key->PK_PERIODO);
                        $updateTarifa->TARIFA = $model->TARIFA;
                        $updateTarifa->MONTO = $model->TARIFA * $updateTarifa->HORAS;
                        $updateTarifa->save(false);
                    }
                }

               /* INICIO HRIBI 16/03/16 Se obtiene el post del form_update_asignaciones para tener la unidad de negocio que se modificará
                   sea que ya exista en el historial de unidades de negocio por asignacion o que sea la primera vez que se modifica y
                   sólo existe en el perfil del empleado.
                   Inserta un registro en la tabla TblBitUnidadNegocioAsig para tener un histórico de las unidades de negocio por empleado*/
                if($model->FK_ESTATUS_ASIGNACION != 7){
                    $modelBitUnidadNegocioAsigN = new TblBitUnidadNegocioAsig();
                    $modelBitUnidadNegocioAsigN->FK_REGISTRO=$model->PK_ASIGNACION;

                    if(!empty(Yii::$app->request->post()['TblBitUnidadNegocioAsig']['FK_UNIDAD_NEGOCIO'])){
                        $modelBitUnidadNegocioAsigN->FK_UNIDAD_NEGOCIO=(Yii::$app->request->post()['TblBitUnidadNegocioAsig']['FK_UNIDAD_NEGOCIO']);
                    }
                    else if(!empty(Yii::$app->request->post()['TblPerfilEmpleados']['FK_UNIDAD_NEGOCIO'])){
                        $modelBitUnidadNegocioAsigN->FK_UNIDAD_NEGOCIO=(Yii::$app->request->post()['TblPerfilEmpleados']['FK_UNIDAD_NEGOCIO']);
                    }
                    else{
                        $modelBitUnidadNegocioAsigN->FK_UNIDAD_NEGOCIO=(Yii::$app->request->post()['TBLPERFILEMPLEADOS']['FK_UNIDAD_NEGOCIO']);
                    }
                    $modelBitUnidadNegocioAsig = TblBitUnidadNegocioAsig::find()->where(['FK_REGISTRO' => $model->PK_ASIGNACION])->andwhere(['FK_EMPLEADO'=>$modelPerfilEmpleado->FK_EMPLEADO])->andwhere(['like','MODULO_ORIGEN','asignaciones'])->orderBy('PK_UNIDAD_NEGOCIO_ASIG DESC')->limit(1)->one();
                    if(is_null($modelBitUnidadNegocioAsig)){
                        $modelBitUnidadNegocioAsig = TblBitUnidadNegocioAsig::find()->where(['FK_REGISTRO' => '0'])->andwhere(['FK_EMPLEADO'=>$modelPerfilEmpleado->FK_EMPLEADO])->andwhere(['like','MODULO_ORIGEN','empleados'])->orderBy('PK_UNIDAD_NEGOCIO_ASIG DESC')->limit(1)->one();
                    }
                    if(is_null($modelBitUnidadNegocioAsig)||($modelBitUnidadNegocioAsig->FK_UNIDAD_NEGOCIO != $modelBitUnidadNegocioAsigN->FK_UNIDAD_NEGOCIO)){
                        $modelBitUnidadNegocioAsigN->FK_EMPLEADO=$modelPerfilEmpleado->FK_EMPLEADO;
                        $modelBitUnidadNegocioAsigN->MODULO_ORIGEN = onToy();
                        $modelBitUnidadNegocioAsigN->FK_USUARIO = user_info()['PK_USUARIO'];
                        $modelBitUnidadNegocioAsigN->FECHA_CREACION=date('Y-m-d H:i:s');
                        $modelBitUnidadNegocioAsigN->save(false);
                    }
                    if(!empty(Yii::$app->request->post()['TblBitUnidadNegocioAsig']['FK_UNIDAD_NEGOCIO'])){
                        $model->FK_UNIDAD_NEGOCIO = (Yii::$app->request->post()['TblBitUnidadNegocioAsig']['FK_UNIDAD_NEGOCIO']);
                    }
                    else if(!empty(Yii::$app->request->post()['TblPerfilEmpleados']['FK_UNIDAD_NEGOCIO'])){
                        $model->FK_UNIDAD_NEGOCIO = (Yii::$app->request->post()['TblPerfilEmpleados']['FK_UNIDAD_NEGOCIO']);
                    }
                    else{
                        $model->FK_UNIDAD_NEGOCIO = (Yii::$app->request->post()['TBLPERFILEMPLEADOS']['FK_UNIDAD_NEGOCIO']);
                    }
                }
                /* FIN HRIBI 16/03/16 */

                /* INICIO HRIBI 25/03/16 - Inserta un registro en la tabla TblBitUbicacionFisica para tener un histórico de las ubicaciones físicas del empleado con el cliente. */
                if($modelAsignacionModificar->FK_UBICACION!=$model->FK_UBICACION&&$model->FK_ESTATUS_ASIGNACION != 7)
                {
                    /*$modelBitUbicacionFisica = new TblBitUbicacionFisica();
                    $modelBitUbicacionFisica->FK_EMPLEADO = $modelPerfilEmpleado->FK_EMPLEADO;
                    $modelBitUbicacionFisica->FK_PERFIL = $modelPerfilEmpleado->PK_PERFIL;
                    $modelBitUbicacionFisica->FK_UBICACION = $model->FK_UBICACION;
                    $modelBitUbicacionFisica->PROPIA_CLIENTE = 'CLIENTE';
                    $modelBitUbicacionFisica->FECHA_CREACION = date('Y-m-d H:i:s');
                    $modelBitUbicacionFisica->save(false);*/
                    user_log_bitacora_ubicacion_fisica($modelPerfilEmpleado->FK_EMPLEADO,$modelPerfilEmpleado->PK_PERFIL,$model->FK_UBICACION,'CLIENTE');
                    $ubicacionFisicaEmp = $model->FK_UBICACION;
                }
                /* Condición para que sólo cuando la asignación tenga estatus => 'En Ejecución' se afecte al empleado[REEMPLAZANTE]
                   en su valores de FK_UBICACION_FISICA Y FK_UNIDAD_NEGOCIO Y FK_ESTATUS_RECURSO */
                if($model->FK_ESTATUS_ASIGNACION == 2)
                {
                    $modelPerfilEmpleado->load(Yii::$app->request->post());
                    $modelPerfilEmpleado->FK_UBICACION_FISICA = $ubicacionFisicaEmp;
                    $modelPerfilEmpleado->FK_UNIDAD_NEGOCIO =  $modelBitUnidadNegocioAsigN->FK_UNIDAD_NEGOCIO;
                    $modelPerfilEmpleado->FK_ESTATUS_RECURSO = 2;
                    $modelPerfilEmpleado->FK_AREA = 9;
                    $modelPerfilEmpleado->save(false);
                }
                /* FIN HRIBI 01/06/16 */

                /* INICIO HRIBI 01/06/16 - Condición para cuando la asignación tenga un estatus diferente a => 'En Ejecución' se afecte al empleado[REEMPLAZANTE]
                   con sus datos propios antes de que fuera asignado en su valores de FK_UBICACION_FISICA Y FK_UNIDAD_NEGOCIO Y FK_ESTATUS_RECURSO */
                $modelBitUbicacionFisicaEmpPropia = TblBitUbicacionFisica::find()->where(['FK_EMPLEADO'=>$modelPerfilEmpleado->FK_EMPLEADO])->andwhere(['PROPIA_CLIENTE'=>'PROPIA'])->orderBy('PK_BIT_UBICACION_FISICA DESC')->limit(1)->one();
                $modelBitUnidadNegocioAsigEmpPropia = TblBitUnidadNegocioAsig::find()->where(['FK_EMPLEADO'=>$modelPerfilEmpleado->FK_EMPLEADO])->andwhere(['FK_REGISTRO'=>0])->andwhere(['like','MODULO_ORIGEN','empleados'])->orderBy('PK_UNIDAD_NEGOCIO_ASIG DESC')->limit(1)->one();

                if($model->FK_ESTATUS_ASIGNACION != 2&&$model->FK_ESTATUS_ASIGNACION != 4 && $estatusActualEmp != 4 && $estatusActualEmp != 6)
                {
                    $modelPerfilEmpleado->load(Yii::$app->request->post());
                    $modelPerfilEmpleado->FK_UBICACION_FISICA = $modelBitUbicacionFisicaEmpPropia->FK_UBICACION;
                    $modelPerfilEmpleado->FK_UNIDAD_NEGOCIO =  $modelBitUnidadNegocioAsigEmpPropia->FK_UNIDAD_NEGOCIO;
                    $modelPerfilEmpleado->FK_ESTATUS_RECURSO = 3;

                    $modelPerfilEmpleado->save(false);
                }

                if($estatusActualEmp != $modelPerfilEmpleado->FK_ESTATUS_RECURSO){
                    user_log_bitacora_estatus_empleado($modelPerfilEmpleado->FK_EMPLEADO,$modelPerfilEmpleado->FK_ESTATUS_RECURSO);
                }


               /* FIN HRIBI 01/06/16 */
                $model->save(false);
                $descripcionBitacora = 'FK_CLIENTE='.$model->FK_CLIENTE.',NOMBRE='.$model->NOMBRE.',FK_UBICACION='.$model->FK_UBICACION.',FK_PROYECTO='.$model->FK_PROYECTO;
                user_log_bitacora($descripcionBitacora,'Modificar informacion de Asignacion',$model->PK_ASIGNACION );

                return $this->redirect(['view', 'id' => $id]);

            } else {
                $periodos= (new \yii\db\Query)
                         ->select(['p.PK_PERIODO',
                            'p.FECHA_INI',
                            'p.FECHA_FIN',
                            'f.FECHA_INGRESO_BANCO',
                            'f.FK_ESTATUS'])
                         ->from('tbl_periodos p')
                         ->join('left join', 'tbl_documentos td',
                                'p.FK_DOCUMENTO_FACTURA = td.PK_DOCUMENTO')
                         ->join('left join', 'tbl_facturas f',
                                'f.FK_DOC_FACTURA = td.PK_DOCUMENTO')
                         ->where(['=','p.FK_ASIGNACION',$id])
                         ->all();
                $total_periodos= count($periodos);
                $periodos_p= (new \yii\db\Query)
                         ->select(['p.PK_PERIODO',
                            'p.FECHA_INI',
                            'p.FECHA_FIN',
                            'f.FECHA_INGRESO_BANCO',
                            'f.FK_ESTATUS'])
                         ->from('tbl_periodos p')
                         ->join('left join', 'tbl_documentos td',
                                'p.FK_DOCUMENTO_FACTURA = td.PK_DOCUMENTO')
                         ->join('left join', 'tbl_facturas f',
                                'f.FK_DOC_FACTURA = td.PK_DOCUMENTO')
                         ->where(['=','p.FK_ASIGNACION',$id])
                         ->andWhere(['=','f.FK_ESTATUS',2])
                         ->all();
                $total_periodos_p= count($periodos_p);
                $pagados='0';
                if($total_periodos==$total_periodos_p)
                {
                    $pagados='1';
                }
                $modelComentariosAsignaciones->MOTIVO='ASIGNACION_DETENIDA';
                $modelComentariosAsignaciones->FECHA_FIN= date('d/m/Y');
                $cat_tarifas=[];
                $fks_cat_tarifas = TblTarifasClientes::find()->select(['FK_CAT_TARIFA'])->where(['FK_CLIENTE'=>$model->FK_CLIENTE])->column();
                $cat_tarifas= TblCatTarifas::find()->andWhere(['IN','PK_CAT_TARIFA',$fks_cat_tarifas])->asArray()->all();

                return $this->render('_form_update_asignacion', [
                    'model'                                         => $model,
                    'modelEmpleadoAsignado'                         => $modelEmpleadoAsignado,
                    'modelPerfilEmpleado'                           => $modelPerfilEmpleado,
                    'modelResponsableOP'                            => $modelResponsableOP,
                    'modelCliente'                                  => $modelCliente,
                    'extra'                                         => $extra,
                    'id'                                            => $id,
                    'periodos'                                      => $periodos,
                    'periodos_p'                                    => $periodos_p,
                    'pagados'                                       => $pagados,
                    'modelComentariosAsignaciones'                  => $modelComentariosAsignaciones,
                    'modelComentariosAsignacionesCancelar'          => $modelComentariosAsignacionesCancelar,
                    'cat_tarifas'                                   => $cat_tarifas,
                    'modelCatMotivosAsignaciondetenida'             => $modelCatMotivosAsignaciondetenida,
                ]);
            }
       }

        public function actionUpdate()
        {
            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();//Inicio del funcionamiento de la 'Transacción' con su respectivo try/catch
            //$datosget = Yii::$app->request->get();

            $data = Yii::$app->request->post();
            //Nuevas variables
            $asig = $data['id'];
            $emp = $data['fk_emp'];

            try{

                $model = $this->findModel($asig);
                $modelEmpleado = tblempleados::find()->where(['PK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
                $modelPerfilEmpleado = TblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
                $modelPuesto = tblcatpuestos::find()->where(['PK_PUESTO' => $modelPerfilEmpleado->FK_PUESTO])->limit(1)->one();
                $modelClientes = tblclientes::find()->where(['PK_CLIENTE' => $model->FK_CLIENTE])->limit(1)->one();
                $modelVacantes = TblVacantes::find()->where(['FK_ASIGNACION' => $model->PK_ASIGNACION])->limit(1)->one();
                if($model->FK_PROYECTO){
                    $modelProyecto = tblcatproyectos::find()->where(['PK_PROYECTO' => $model->FK_PROYECTO])->limit(1)->one();
                }else{
                    $modelProyecto = new tblcatproyectos();
                }
                $modelContacto = tblcatcontactos::find()->where(['PK_CONTACTO' => $model->FK_CONTACTO])->limit(1)->one();
                $modelUbicacion = TblCatUbicaciones::find()->where(['PK_UBICACION' => $model->FK_UBICACION])->limit(1)->one();
                $modelUnidadNegocio = TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO' => $modelPerfilEmpleado->FK_UNIDAD_NEGOCIO])->limit(1)->one();
                $modelBitUnidadNegocioAsig = new TblBitUnidadNegocioAsig();
                $modelAsignacionesReemplazos = TblAsignacionesReemplazos::find()->where(['FK_ASIGNACION'=>$model->PK_ASIGNACION])->orderBy(['PK_ASIGNACION_REEMPLAZO' => SORT_DESC])->limit(1)->one();
                //Condición para verificar si la asignación tiene registro de haber sido detenida.
                $modelComentariosAsignaciones2 = TblBitComentariosAsignaciones::find()->where(['FK_ASIGNACION'=>$model->PK_ASIGNACION])->andWhere(['=','MOTIVO','ASIGNACION_DETENIDA'])->orderBy(['FECHA_FIN' => SORT_DESC])->limit(1)->one();
                $estatusEmpleadoActual = $modelPerfilEmpleado->FK_ESTATUS_RECURSO;
                /*Condición para identificar si al estar haciendo el reemplazo la asignación viene con estatus de detenida y guardar el valor '6' -> 'DETENIDA'
                  ya que más adelante se actualiza el estatus de la asignación en base a las fechas y posterior a eso se necesita saber si venia como detenida,
                  para guardar la fecha fin real que duro el [reemplazado] en la asignación, es decir de la fecha de la inicio o reinicio de la asignación hasta la fecha inicio de la detención. */
                if($model->FK_ESTATUS_ASIGNACION == 6){
                    $tempEstatusOrigenDetenida = 6;
                    $estatusRecurso = 3;
                }else{
                    $tempEstatusOrigenDetenida = 0;
                    $estatusRecurso = 0;
                }

                $validarDetenido = array();
                if($model->FK_ESTATUS_ASIGNACION == 6) {
                    $validarDetenido = TblBitComentariosAsignaciones::find()->where(['=','FK_ESTATUS_ASIGNACION',6])
                    ->andWhere(['=','FK_ASIGNACION',$model->PK_ASIGNACION])->andWhere(['FECHA_RETOMADA'=> null])->limit(1)->one();
                }

                /*$extra['NOMBRE_EMP'] = $modelEmpleado->NOMBRE_EMP;
                $extra['DESC_PUESTO'] = $modelPuesto->DESC_PUESTO;
                $extra['PORC_RENTA_ESPERADA_A'] =  $modelClientes ->PORC_RENTA_ESPERADA_A;
                $extra['PORC_RENTA_ESPERADA_DE'] =  $modelClientes ->PORC_RENTA_ESPERADA_DE;
                $extra['HORAS_ASIGNACION'] =  $modelClientes ->HORAS_ASIGNACION;
                $extra['NOMBRE_PROYECTO'] = $modelProyecto ->NOMBRE_PROYECTO;
                $extra['NOMBRE_CONTACTO'] = $modelContacto ->NOMBRE_CONTACTO;
                $extra['DESC_UBICACION'] = $modelUbicacion ->DESC_UBICACION;
                $extra['FK_UNIDAD_NEGOCIO'] = $modelPerfilEmpleado ->FK_UNIDAD_NEGOCIO;
                $extra['DESC_UNIDAD_NEGOCIO'] = $modelUnidadNegocio ->DESC_UNIDAD_NEGOCIO;*/

                $modelResponsableOP = (new \yii\db\Query)
                            ->select([
                                "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
                                'e.PK_EMPLEADO',
                            ])
                            ->from('tbl_empleados e')

                            ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
                            ->where(['p.FK_AREA'=>'5'])->andwhere(['or',['p.FK_ESTATUS_RECURSO'=>'3'],['p.FK_ESTATUS_RECURSO'=>'5']])
                            ->orderBy('nombre_emp DESC')->all();


                if (Yii::$app->request->post() ) {

                        $idEmpleadoAnterior = $model -> FK_EMPLEADO;
                        $fInicialRemplazo = $model -> FECHA_INI;

                        $model->FECHA_INI = transform_date($data['fi_r'],'Y-m-d');

                        if($data['ff_r'] != ''){

                            $model->FECHA_FIN = transform_date($data['ff_r'],'Y-m-d');
                        }else{

                            $model->FECHA_FIN = '';
                        }
                        //$model->FECHA_FIN= transform_date($data['fin'][$idEmpleado],'Y-m-d');

                        $modelPerfilEmpleadoPost= tblperfilempleados::find()->where(['FK_EMPLEADO'=>$emp])->limit(1)->one();
                        //$modelPerfilEmpleadoPost->FK_ESTATUS_RECURSO=2;


                        if($model->FK_ESTATUS_ASIGNACION != 4 || $model->FK_ESTATUS_ASIGNACION != 5){//Si es diferente de Cerrado o CANCELADO
                            $date1 = transform_date($data['fi_r'],'Y-m-d');
                            $date1_fin = transform_date($data['ff_r'],'Y-m-d');
                            $date2 = date('Y-m-d');
                            $fecha_inicio = strtotime($date1);
                            $fecha_fin = strtotime($date1_fin);
                            $actual = strtotime($date2);
                            if($modelPerfilEmpleadoPost->FK_ESTATUS_RECURSO == 1
                                || $modelPerfilEmpleadoPost->FK_ESTATUS_RECURSO == 2){
                                $model->FK_ESTATUS_ASIGNACION=7;
                            }else
                            if($fecha_inicio>$actual){
                                $model->FK_ESTATUS_ASIGNACION=1;
                            }else
                            if ($fecha_inicio <= $actual&&$fecha_fin>$actual){
                                $model->FK_ESTATUS_ASIGNACION=2;
                            }else
                            if($fecha_fin<$actual){
                                $model->FK_ESTATUS_ASIGNACION=3;
                            }
                        }

                        $model -> TARIFA = $data['nuevaTarifa'];
                        $model -> HORAS = $data['horas_periodo'];
                        $model -> MONTO = $data['monto'];
                        $model -> FK_EMPLEADO = $emp;
                        $model -> save(false);
                        //dd($modelVacantes->FK_ESTATUS_VACANTE);
                         if(!empty($modelVacantes->FK_ESTATUS_VACANTE)){
                            $modelVacantes->FK_ESTATUS_VACANTE = '4';
                            $modelVacantes->save(false);
                        }

                        $modelComentariosAsignaciones= TblBitComentariosAsignaciones::find()
                                ->where(['=','FK_ESTATUS_ASIGNACION',6])
                                ->andWhere(['=','FK_ASIGNACION',$model->PK_ASIGNACION])
                                ->andWhere(['=','MOTIVO','Baja Empleado'])
                                ->andWhere(['FECHA_RETOMADA'=> null])
                                ->limit(1)
                                ->one();
                        $modelComentariosAsignaciones3= TblBitComentariosAsignaciones::find()
                                ->where(['=','FK_ESTATUS_ASIGNACION',6])
                                ->andWhere(['=','FK_ASIGNACION',$model->PK_ASIGNACION])
                                ->andWhere(['=','MOTIVO','Petición del Cliente'])
                                ->andWhere(['FECHA_RETOMADA'=> null])
                                ->limit(1)
                                ->one();
                        if(!empty($modelComentariosAsignaciones->PK_COMENTARIO_ASIGNACION)){
                            $modelComentariosAsignaciones->FECHA_RETOMADA= transform_date($data['fi_r'],'Y-m-d');
                            $modelComentariosAsignaciones->save(false);
                        }
                        if(!empty($modelComentariosAsignaciones3->PK_COMENTARIO_ASIGNACION)){
                            $modelComentariosAsignaciones3->FECHA_RETOMADA= transform_date($data['fi_r'],'Y-m-d');
                            $modelComentariosAsignaciones3->save(false);
                        }
                        $descripcionBitacora = 'FK_CLIENTE='.$model->FK_CLIENTE.',NOMBRE='.$model->NOMBRE.',FK_UBICACION='.$model->FK_UBICACION.',FK_PROYECTO='.$model->FK_PROYECTO;
                        user_log_bitacora($descripcionBitacora,'Modificar informacion de Asignacion',$model->PK_ASIGNACION );

                        //INICIO HRIBI 27/07/2016 - Se agrega un registro en tbl_asignaciones_reemplazos haciendo referencia al empleado reemplazado.
                        $modelRemplazo = new TblAsignacionesReemplazos();
                        $modelRemplazo->FK_ASIGNACION=$model->PK_ASIGNACION;
                        $modelRemplazo->FK_EMPLEADO=$idEmpleadoAnterior;
                        //Si existe un registro anterior de reemplazo_asignacion entonces se toma la fecha fin de este ultimo como fecha inicio del reemplazado en el registro que se esta insertando.
                        //Si la asignación viene de origen con estatus DETENIDA
                        if($tempEstatusOrigenDetenida == 6){
                            //Si viene con estatus DETENIDA y también ya existen reemplazos anteriores para esta misma asignación.
                            $estatusRecurso = 3;
                            if($modelAsignacionesReemplazos){
                                $modelRemplazo->FECHA_INICIO=date('Y-m-d', strtotime($modelAsignacionesReemplazos->FECHA_FIN. ' + 1 days'));
                            //Si viene con estatus DETENIDA y es el primer reemplazo para esta asignación.
                            }else{
                                $modelRemplazo->FECHA_INICIO=transform_date($fInicialRemplazo,'Y-m-d');
                            }
                            //Si viene con estatus DETENIDA siempre se tomará como FECHA_FIN del reemplazado, la FECHA_FIN que es la Fecha en que se realiza la detención de la asignación.
                            $modelRemplazo->FECHA_FIN=date('Y-m-d', strtotime($modelComentariosAsignaciones2['FECHA_FIN']. ' - 1 days'));
                        //Si NO viene con estatus DETENIDA y pero ya existen reemplazos anteriores para esta misma asignación.
                        }else if($modelAsignacionesReemplazos){
                            $estatusRecurso = 0;
                            /*Si la FECHA_RETOMADA es mayor a la FECHA_FIN del ultimo reemplazo registrado para esta asignación,
                              entonces se toma como FECHA_INICIO del [reemplazado], la FECHA_FIN del ultimo registro de reemplazos encontrado.*/
                            if($modelComentariosAsignaciones2['FECHA_RETOMADA'] > $modelAsignacionesReemplazos->FECHA_FIN){
                                $modelRemplazo->FECHA_INICIO=date('Y-m-d', strtotime($modelComentariosAsignaciones2['FECHA_RETOMADA']. ' + 1 days'));
                            /*Si la FECHA_FIN es mayor a la FECHA_RETOMADA de la ultima detención para esta asignación,
                              entonces se toma como FECHA_INICIO del [reemplazado], la FECHA_FIN de la ultima detención*/
                            }else{
                                $modelRemplazo->FECHA_INICIO=date('Y-m-d', strtotime($modelAsignacionesReemplazos->FECHA_FIN. ' + 1 days'));
                            }
                            //Si existen registros anteriores de reemplazos para esta asignación, siempre se tomara como FECHA_FIN del [reemplazado], la fecha inicio que se captura al registrar el reemplazo.
                            $modelRemplazo->FECHA_FIN=date('Y-m-d', strtotime(transform_date($data['fi_r']). ' - 1 days'));
                        //Si no existen ni registros de DETENCIONES ni registros de REEMPLAZOS anteriores para esta asignación, entonces se toman las fechas capturadas en la asignación misma.
                        }else{
                            $modelRemplazo->FECHA_INICIO=transform_date($fInicialRemplazo,'Y-m-d');
                            $modelRemplazo->FECHA_FIN=date('Y-m-d', strtotime(transform_date($data['fi_r']). ' - 1 days'));
                        }
                        $modelRemplazo->FECHA_CREACION= date('Y-m-d');
                        //FIN HRIBI 27/07/2016
                        /* INICIO HRIBI 25/03/16 - [EMPLEADO REEMPLAZANTE] Inserta un registro en la tabla TblBitUbicacionFisica para tener un histórico de las ubicaciones físicas del empleado con el cliente. */
                        /*$modelBitUbicacionFisica = new TblBitUbicacionFisica();
                        $modelBitUbicacionFisica->FK_EMPLEADO = $modelPerfilEmpleadoPost->FK_EMPLEADO;
                        $modelBitUbicacionFisica->FK_PERFIL = $modelPerfilEmpleadoPost->PK_PERFIL;
                        $modelBitUbicacionFisica->FK_UBICACION = $model->FK_UBICACION;
                        $modelBitUbicacionFisica->PROPIA_CLIENTE = 'CLIENTE';
                        $modelBitUbicacionFisica->FECHA_CREACION = date('Y-m-d H:i:s');
                        $modelBitUbicacionFisica->save(false);*/
                        user_log_bitacora_ubicacion_fisica($modelPerfilEmpleadoPost->FK_EMPLEADO,$modelPerfilEmpleadoPost->PK_PERFIL,$model->FK_UBICACION,'CLIENTE');
                        /* INICIO HRIBI 16/03/16 - [EMPLEADO REEMPLAZANTE] Inserta un registro en la tabla TblBitUnidadNegocioAsig para tener un histórico de las unidades de negocio por empleado*/
                        /* INICIO SCM 29/06/2016 - Se agre validacion para ver si el empleado asignado tiene empleados a su cargo*/
                        /*if(isset($emp)){
                            
                            $modelPerfilNuevoJefe = tblperfilempleados::find()->where(['FK_EMPLEADO' => $emp])->limit(1)->one();
                            $modelPerfilNuevoJefe->FK_JEFE_DIRECTO = ;
                            $modelPerfilNuevoJefe->save(false);
                                
                            
                        }*/
                        /* FIN SCM 29/06/2016 */
                        //Al reemplazante se le pondrá la unidad de negocio que tiene el reemplazado dentro de la asignación, como tal tomará la unidad de negocio del reemplazado.
                        $modelBitUnidadNegocioAsigConsultaReemplazante = TblBitUnidadNegocioAsig::find()->where(['FK_REGISTRO' => $model->PK_ASIGNACION])->andWhere(['FK_EMPLEADO' => $modelPerfilEmpleado->FK_EMPLEADO])->andWhere(['LIKE','MODULO_ORIGEN','asignaciones'])->orderBy('PK_UNIDAD_NEGOCIO_ASIG DESC')->limit(1)->one();
                        //Al reemplazante se le pondrá la ultima unidad de negocio que tenia antes de estar en alguna asignación.
                        $modelBitUnidadNegocioAsigConsultaReemplazado = TblBitUnidadNegocioAsig::find()->where(['FK_REGISTRO' => 0])->andWhere(['FK_EMPLEADO' => $modelPerfilEmpleado->FK_EMPLEADO])->andWhere(['LIKE','MODULO_ORIGEN','empleados'])->orderBy('PK_UNIDAD_NEGOCIO_ASIG DESC')->limit(1)->one();

                        /*$modelBitUnidadNegocioAsigReemplazante = new TblBitUnidadNegocioAsig();
                        //var_dump($modelBitUnidadNegocioAsig);
                        $modelBitUnidadNegocioAsigReemplazante->FK_REGISTRO=$model->PK_ASIGNACION;
                        $modelBitUnidadNegocioAsigReemplazante->FK_UNIDAD_NEGOCIO=$modelBitUnidadNegocioAsigConsultaReemplazante->FK_UNIDAD_NEGOCIO;//Se guarda en el nuevo empleado[Reemplazante] la unidad de negocio del reemplazado.
                        $modelBitUnidadNegocioAsigReemplazante->FK_EMPLEADO=$modelPerfilEmpleadoPost->FK_EMPLEADO;
                        $modelBitUnidadNegocioAsigReemplazante->MODULO_ORIGEN= 'ASIGNACIONES';
                        $modelBitUnidadNegocioAsigReemplazante->FECHA_CREACION=date('Y-m-d H:i:s');
                        $modelBitUnidadNegocioAsigReemplazante->save(false);*/
                        //user_log_bitacora_unidad_negocio($modelPerfilEmpleadoPost->FK_EMPLEADO,$modelBitUnidadNegocioAsigConsultaReemplazante->FK_UNIDAD_NEGOCIO,$model->PK_ASIGNACION);
                        /* FIN HRIBI 16/03/16 */

                        /* Condición para sólo cuando la asignación tenga estatus => En Ejecución se afecte al empleado[REEMPLAZANTE]
                           en su valores de FK_UBICACION_FISICA Y FK_UNIDAD_NEGOCIO Y FK_ESTATUS_RECURSO */
                        if($model->FK_ESTATUS_ASIGNACION == 2){
                            $modelPerfilEmpleadoPost->FK_UBICACION_FISICA = $model->FK_UBICACION;
                            $modelPerfilEmpleadoPost->FK_UNIDAD_NEGOCIO = $modelBitUnidadNegocioAsigConsultaReemplazante->FK_UNIDAD_NEGOCIO;
                            $modelPerfilEmpleadoPost->FK_ESTATUS_RECURSO = 2;
                            $modelPerfilEmpleadoPost->save(false);
                        }
                        user_log_bitacora_estatus_empleado($modelPerfilEmpleadoPost->FK_EMPLEADO,$modelPerfilEmpleadoPost->FK_ESTATUS_RECURSO);
                        /* FIN HRIBI 25/03/16 */

                        /* INICIO HRIBI 01/06/16 - Condición para cuando la asignación tenga un estatus diferente a => 'En Ejecución' se afecte al empleado[REEMPLAZADO]
                           con sus datos propios antes de que fuera asignado en su valores de FK_UBICACION_FISICA Y FK_UNIDAD_NEGOCIO Y FK_ESTATUS_RECURSO */
                        $modelBitUbicacionFisicaEmpPropia = TblBitUbicacionFisica::find()->where(['FK_EMPLEADO'=>$modelPerfilEmpleado->FK_EMPLEADO])->andwhere(['PROPIA_CLIENTE'=>'PROPIA'])->orderBy('PK_BIT_UBICACION_FISICA DESC')->limit(1)->one();
                        $modelBitUnidadNegocioAsigEmpPropia = TblBitUnidadNegocioAsig::find()->where(['FK_EMPLEADO'=>$modelPerfilEmpleado->FK_EMPLEADO])->andwhere(['FK_REGISTRO'=>0])->andWhere(['LIKE','MODULO_ORIGEN','empleados'])->orderBy('PK_UNIDAD_NEGOCIO_ASIG DESC')->limit(1)->one();
                        if($model->FK_ESTATUS_ASIGNACION != 2){
                            if($modelPerfilEmpleado->FK_ESTATUS_RECURSO != 4 && $modelPerfilEmpleado->FK_ESTATUS_RECURSO != 6){
                                $modelPerfilEmpleado->load(Yii::$app->request->post());
                                //$modelPerfilEmpleado->FK_UBICACION_FISICA = $modelBitUbicacionFisicaEmpPropia->FK_UBICACION;
                                $modelPerfilEmpleado->FK_UNIDAD_NEGOCIO =  $modelBitUnidadNegocioAsigEmpPropia->FK_UNIDAD_NEGOCIO;
                                $modelPerfilEmpleado->FK_ESTATUS_RECURSO = 3;
                                $modelPerfilEmpleado->save(false);
                            }
                        }
                        /* FIN HRIBI 01/06/16 */

                        //$modelPerfilEmpleado = TblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $idEmpleadoReemplazado])->limit(1)->one();
                        //var_dump("modelPerfilEmpleado: ".$modelPerfilEmpleado->FK_EMPLEADO);

                        $modelUltimaUbicacionFisicaPropia = TblBitUbicacionFisica::find()->where(['FK_EMPLEADO'=>$modelPerfilEmpleado->FK_EMPLEADO, 'PROPIA_CLIENTE'=>'PROPIA'])->orderBy('PK_BIT_UBICACION_FISICA DESC')->limit(1)->one();

                        /* INICIO HRIBI 25/03/16 - [EMPLEADO REEMPLAZADO] Inserta un registro en la tabla TblBitUbicacionFisica para tener un histórico de las ubicaciones físicas del empleado con el cliente. */
                        /*$modelBitUbicacionFisica = new TblBitUbicacionFisica();
                        $modelBitUbicacionFisica->FK_EMPLEADO = $modelPerfilEmpleado->FK_EMPLEADO;
                        $modelBitUbicacionFisica->FK_PERFIL = $modelPerfilEmpleado->PK_PERFIL;
                        $modelBitUbicacionFisica->FK_UBICACION = $modelUltimaUbicacionFisicaPropia->FK_UBICACION;
                        $modelBitUbicacionFisica->PROPIA_CLIENTE = 'PROPIA';
                        $modelBitUbicacionFisica->FECHA_CREACION = date('Y-m-d H:i:s');
                        $modelBitUbicacionFisica->save(false);*/
                        user_log_bitacora_ubicacion_fisica($modelPerfilEmpleado->FK_EMPLEADO,$modelPerfilEmpleado->PK_PERFIL,$modelUltimaUbicacionFisicaPropia->FK_UBICACION,'PROPIA');

                        /* INICIO HRIBI 16/03/16 - [EMPLEADO REEMPLAZADO] Inserta un registro en la tabla TblBitUnidadNegocioAsig para tener un histórico de las unidades de negocio por empleado*/
                        $modelBitUnidadNegocioAsigReemplazado = new TblBitUnidadNegocioAsig();
                        $modelBitUnidadNegocioAsigReemplazado->FK_REGISTRO=$model->PK_ASIGNACION;
                        //var_dump($modelBitUnidadNegocioAsig);
                        if($modelBitUnidadNegocioAsigConsultaReemplazado->FK_UNIDAD_NEGOCIO != $modelBitUnidadNegocioAsigReemplazado->FK_UNIDAD_NEGOCIO){
                            $modelBitUnidadNegocioAsigReemplazado->FK_UNIDAD_NEGOCIO=$modelBitUnidadNegocioAsigConsultaReemplazado->FK_UNIDAD_NEGOCIO;
                            $modelBitUnidadNegocioAsigReemplazado->FK_EMPLEADO=$modelPerfilEmpleado->FK_EMPLEADO;
                            $modelBitUnidadNegocioAsigReemplazado->MODULO_ORIGEN=onToy();
                            $modelBitUnidadNegocioAsigReemplazado->FK_USUARIO = user_info()['PK_USUARIO'];
                            $modelBitUnidadNegocioAsigReemplazado->FECHA_CREACION=date('Y-m-d H:i:s');
                            $modelBitUnidadNegocioAsigReemplazado->save(false);
                        }
                        /* FIN HRIBI 16/03/16 */
                        $modelPerfilEmpleado->FK_UBICACION_FISICA = $modelUltimaUbicacionFisicaPropia->FK_UBICACION;
                        $modelPerfilEmpleado->FK_UNIDAD_NEGOCIO = $modelBitUnidadNegocioAsigReemplazado->FK_UNIDAD_NEGOCIO;
                        $modelPerfilEmpleado->save(false);

                        if($estatusEmpleadoActual!=$modelPerfilEmpleado->FK_ESTATUS_RECURSO){
                            user_log_bitacora_estatus_empleado($modelPerfilEmpleado->FK_EMPLEADO,$modelPerfilEmpleado->FK_ESTATUS_RECURSO);
                        }

                        //dd();
                        /* INICIO HRIBI 25/03/16 - Inserta un registro en la tabla TblBitUbicacionFisica para tener un histórico de las ubicaciones físicas del empleado con el cliente. */
                        /*if($modelAsignacionModificar->FK_UBICACION!=$model->FK_UBICACION){
                            $modelBitUbicacionFisica = new TblBitUbicacionFisica;
                            $modelBitUbicacionFisica->FK_EMPLEADO = $modelPerfilEmpleado->FK_EMPLEADO;
                            $modelBitUbicacionFisica->FK_PERFIL = $modelPerfilEmpleado->PK_PERFIL;
                            $modelBitUbicacionFisica->FK_UBICACION = $model->FK_UBICACION;
                            $modelBitUbicacionFisica->FECHA_CREACION = date('Y-m-d H:i:s');
                            $modelBitUbicacionFisica->save(false);
                        }*/

                        /* FIN HRIBI 25/03/16 */

                        $descripcionBitacora = 'FK_CLIENTE='.$model->FK_CLIENTE.',NOMBRE='.$model->NOMBRE.',FK_EMPLEADO='.$idEmpleadoAnterior.',FK_UBICACION='.$model->FK_UBICACION.',FK_PROYECTO='.$model->FK_PROYECTO;
                        $bitacora= user_log_bitacora($descripcionBitacora,'Registro de Reemplazo de Asignación',$model->PK_ASIGNACION );
                        $bitacora = json_decode($bitacora,true);
                        $modelRemplazo->FK_BITACORA=$bitacora['pk_bitacora'];

                        $modelRemplazo -> save(false);

                        $connection->createCommand("
                            CREATE TEMPORARY TABLE tmp_tbl_asignaciones AS
                                    (select periodos.pk_periodo, tbl_asignaciones.TARIFA
                                    from tbl_asignaciones_reemplazos
                                        join (select * from tbl_periodos) periodos
                                        on periodos.fk_asignacion = ".$modelRemplazo->FK_ASIGNACION."
                                        and tbl_asignaciones_reemplazos.fk_asignacion = ".$modelRemplazo->FK_ASIGNACION."
                                        join tbl_asignaciones on tbl_asignaciones.PK_ASIGNACION = ".$modelRemplazo->FK_ASIGNACION."
                                    where
                                        tbl_asignaciones_reemplazos.pk_asignacion_reemplazo =
                                        (select max(pk_asignacion_reemplazo)
                                            from tbl_asignaciones_reemplazos
                                            where tbl_asignaciones_reemplazos.fk_asignacion = ".$modelRemplazo->FK_ASIGNACION.")
                                        and periodos.fecha_ini >= tbl_asignaciones_reemplazos.fecha_fin);

                            update tbl_periodos
                            set fk_empleado = ".$model->FK_EMPLEADO.", TARIFA = ".$model->TARIFA."
                            where pk_periodo IN (SELECT PK_PERIODO FROM tmp_tbl_asignaciones);

                            DROP TABLE tmp_tbl_asignaciones;
                            "  )
                            ->execute();

                    /*$modelPeriodo = TblPeriodos::find()
                    ->where(['FK_ASIGNACION'=>$modelRemplazo->FK_ASIGNACION])
                    ->andWhere(['<=','FECHA_INI',$modelRemplazo->FECHA_FIN])
                    ->andWhere(['>=','FECHA_FIN',$modelRemplazo->FECHA_FIN])
                    ->limit(1)
                    ->one();

                    if(!empty($modelPeriodo)) // SI EL PERIODO VIENE VACIO  EL FLUJO CONTINUARA SINO .. ENTRARA A CODIGO ORIGINAL CLRR
                {
                   $fechaInicioRemplazo = strtotime($modelRemplazo->FECHA_FIN. ' + 1 days');
                   $fechaInicioPeriodo = strtotime($modelPeriodo->FECHA_INI);
                   $fechaFinPeriodo = strtotime($modelPeriodo->FECHA_FIN);

                   $diasEmpleadoRemplazado = 0;
                   $diasEmpleadoNuevo =0;

                   while($fechaInicioPeriodo < $fechaInicioRemplazo){

                        $fechaInicioPeriodo += 86400;
                        $diasEmpleadoRemplazado++;
                   }

                    while($fechaInicioRemplazo < $fechaFinPeriodo){

                       $fechaInicioRemplazo += 86400;
                        $diasEmpleadoNuevo++;
                   }

                        if($diasEmpleadoRemplazado <= $diasEmpleadoNuevo)
                        {
                            $modelPeriodo->FK_EMPLEADO = $model->FK_EMPLEADO;
                        }
                        else
                        {
                           $modelPeriodo->FK_EMPLEADO = $modelPerfilEmpleado->FK_EMPLEADO;
                        }
                   $modelPeriodo->save(false);
                }*/                 

                    return $this->redirect(['view', 'id' => $model->PK_ASIGNACION]);
                } else {

                    return $this->redirect(['index']);
                    /*return $this->render('update', [
                        'model' => $model,
                        'extra' => $extra,
                        'modelBitUnidadNegocioAsig' => $modelBitUnidadNegocioAsig,
                        'modelPerfilEmpleado' => $modelPerfilEmpleado,
                        'estatusRecurso' => $estatusRecurso,
                        'modelResponsableOP' => $modelResponsableOP,
                        'validarDetenido' => $validarDetenido,
                        'datosget' => $datosget,
                    ]);*/
                }

            $transaction->commit();//Si todo el proceso es correcto se ejectua la sentencia commit para guardar todas las operaciones en la base de datos.

            }catch(\Exception $e){
                $transaction->rollBack();//Si sucede algún error durante la operación, se ejecuta la sentencia rollback para evitar que hagan modificaciones en base de datos.
            throw $e;
            }//FIN del funcionamiento de la 'Transacción' con su respectivo try/catch
            $connection->close();
        }

        public function actionGetallasig()
        {
            if (Yii::$app->request->isAjax) {

                $data = Yii::$app->request->post();
                $post=null;
                // parse_str($data,$post);

                $tabla1 = TblAsignaciones::find()->where(['FK_EMPLEADO' => $data['data']['FK_EMPLEADO']])->all();
                $tabla2 = TblAsignacionesReemplazos::find()->where(['FK_EMPLEADO' => $data['data']['FK_EMPLEADO']])->all();
                $tabla3 = TblBitComentariosAsignaciones::find(['FK_ASIGNACION' =>$data['data']['PK_ASIGNACION']])->asArray()->all();

                $canceladas = array();
                if ($tabla3) {
                    foreach ($tabla3 as $key) {
                        $canceladas['id'] = $key['FK_ASIGNACION'];
                        $canceladas['fecha'] = $key['FECHA_FIN'];
                    }
                }

                $resultado = array_merge($tabla1, $tabla2);

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $res = array(
                    'modelo' => $resultado,
                    'canceladas' => $canceladas,
                    // 'data' => $data,
                );
            }

        }

        public function actionGetallfechas()
        {
            if (Yii::$app->request->isAjax)
                {

                    $data = Yii::$app->request->post();

                    if(isset($data['idEmp'])){
                        $tabla1 = TblAsignaciones::find()->where(['=','FK_EMPLEADO',$data['idEmp']])->all();
                        $tabla2 = TblAsignacionesReemplazos::find()->where(['=','FK_EMPLEADO',$data['idEmp']])->all();

                    }else{
                        $tabla1 = TblAsignaciones::find()->all();
                        $tabla2 = TblAsignacionesReemplazos::find()->all();
                    }

                    $tabla3 = TblBitComentariosAsignaciones::find()->all();

                    $canceladas = array();
                    if ($tabla3) {
                        foreach ($tabla3 as $key) {
                            $canceladas['id'] = $key['FK_ASIGNACION'];
                            $canceladas['fecha'] = $key['FECHA_FIN'];
                        }
                    }

                    $resultado = array_merge($tabla1, $tabla2);

                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return $res = array(
                        'modelo' => $resultado,
                        'canceladas' => $canceladas,
                    );
                }

        }

        public function actionDelete($id)
        {
            $this->findModel($id)->delete();

            return $this->redirect(['index']);

        }

        protected function findModel($id)
        {
            if (($model = TblAsignaciones::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }

        }

        public function actionRegistrar_seguimiento()
        {
            $data = Yii::$app->request->post();
            $modelSeguimiento = new TblAsignacionesSeguimiento();
            $modelSeguimiento->load(Yii::$app->request->post());
            $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
            $modelSeguimiento->FK_ASIGNACION = $data['FK_ASIGNACION'];
            $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
            $modelSeguimiento->save(false);
            return $this->redirect(['view', 'id' => $data['FK_ASIGNACION']]);

        }

        public function actionRecursos()
        {
            $url =      Yii::$app->request->url;
            $datosget = Yii::$app->request->get();
            /*$origen = explode('/', $url);
            $status = explode('_',$origen['4']);
            $ids =    explode('update?id=',$status['0']);
            $idurl =  explode('&',$ids['1']);*/
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre     =(!empty($post['nombre']))? trim($post['nombre']):'';
            $puesto     =(!empty($post['puestos']))? trim($post['puestos']):'';
            $tecnologia =(!empty($post['tecnologia']))? $post['tecnologia']:'';
            $cliente    =(!empty($post['cliente']))? trim($post['cliente']):'';
            $contacto   =(!empty($post['contacto']))? trim($post['contacto']):'';
            $estatusRec =(!empty($post['estatusRec']))? trim($post['estatusRec']):'';
            $empleadosId =(!empty($post['empleadosId']))? $post['empleadosId']:'';

            if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $razon_social ='';
            }else{
                $razon_social = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            }

            if(isset($datosget['_status']))
            {
              $dataProvider = new ActiveDataProvider([
              'query'=> (new \yii\db\Query())
                        ->select([
                                'DISTINCT (tbl_empleados.PK_EMPLEADO)',
                                'CONCAT(tbl_empleados.NOMBRE_EMP, tbl_empleados.APELLIDO_PAT_EMP, tbl_empleados.APELLIDO_MAT_EMP) as NOMBRE_EMP',
                                'tbl_perfil_empleados.TARIFA',
                                'tbl_perfil_empleados.FK_ESTATUS_RECURSO',
                                'tbl_cat_puestos.DESC_PUESTO',
                                'DATEDIFF(CURDATE(),tbl_perfil_empleados.FECHA_INGRESO) AS ANTIGUEDAD',
                                'tbl_cat_estatus_recursos.DESC_ESTATUS_RECURSO',
                                'tbl_perfil_empleados.SUELDO_DIARIO',
                                'tbl_perfil_empleados.SUELDO_NETO',
                                'tbl_perfil_empleados.ISR',
                                'tbl_perfil_empleados.APORTACION_IMSS',
                                'tbl_perfil_empleados.APORTACION_INFONAVIT',
                                'tbl_cat_administradoras.PORC_COMISION_ADMIN_EMPLEADO',
                                '(SELECT COUNT(perfil2.PK_PERFIL) FROM tbl_perfil_empleados perfil2 WHERE tbl_empleados.PK_EMPLEADO = perfil2.FK_JEFE_DIRECTO) CANT_EMPLEADOS_A_CARGO',
                                ])
                        ->from('tbl_empleados')
                        ->join('LEFT JOIN','tbl_perfil_empleados' , 'tbl_empleados.PK_EMPLEADO= tbl_perfil_empleados.FK_EMPLEADO')
                        ->join('LEFT JOIN','tbl_administradora_beneficiario' , 'tbl_perfil_empleados.FK_EMPLEADO = tbl_administradora_beneficiario.FK_EMPLEADO')
                        ->join('LEFT JOIN','tbl_bit_administradora_empleado' , 'tbl_administradora_beneficiario.FK_REGISTRO = tbl_bit_administradora_empleado.FK_REGISTRO_ADMON')
                        ->join('LEFT JOIN','tbl_candidatos' , 'tbl_bit_administradora_empleado.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                        ->join('LEFT JOIN','tbl_vacantes_candidatos' , 'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                        ->join('LEFT JOIN','tbl_vacantes' , 'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                        ->join('LEFT JOIN','tbl_asignaciones' , 'tbl_vacantes.FK_ASIGNACION = tbl_asignaciones.PK_ASIGNACION')
                        ->join('INNER JOIN','tbl_cat_puestos' , 'tbl_cat_puestos.PK_PUESTO = tbl_perfil_empleados.FK_PUESTO')
                        ->join('INNER JOIN','tbl_cat_estatus_recursos' , 'tbl_cat_estatus_recursos.PK_ESTATUS_RECURSO = tbl_perfil_empleados.FK_ESTATUS_RECURSO')
                        ->join('INNER JOIN','tbl_cat_administradoras' , 'tbl_perfil_empleados.FK_ADMINISTRADORA = tbl_cat_administradoras.PK_ADMINISTRADORA')
                        ->andWhere(['=','tbl_asignaciones.PK_ASIGNACION',$datosget['id']])
                        ->andWhere(['<>','tbl_perfil_empleados.FK_ESTATUS_RECURSO',4])
                        ->andWhere(['=','tbl_perfil_empleados.FK_TIPO_SERVICIO',2])
                       ]);
             }
             else
            {

            $dataProvider = new ActiveDataProvider([
              'query'=> (new \yii\db\Query())
                        ->select([
                                'DISTINCT (E.PK_EMPLEADO)',
                                "CONCAT_WS(' ', E.NOMBRE_EMP,E.APELLIDO_PAT_EMP, E.APELLIDO_MAT_EMP) as NOMBRE_EMP",
                                'PE.TARIFA',
                                'PE.FK_ESTATUS_RECURSO',
                                'P.DESC_PUESTO',
                                'DATEDIFF(CURDATE(),PE.FECHA_INGRESO) AS ANTIGUEDAD',
                                'ER.DESC_ESTATUS_RECURSO',
                                'PE.SUELDO_DIARIO',
                                'PE.SUELDO_NETO',
                                'PE.ISR',
                                'PE.APORTACION_IMSS',
                                'PE.APORTACION_INFONAVIT',
                                'A.PORC_COMISION_ADMIN_EMPLEADO',
                                '(SELECT COUNT(perfil2.PK_PERFIL)
                                    FROM tbl_perfil_empleados perfil2
                                    WHERE E.PK_EMPLEADO = perfil2.FK_JEFE_DIRECTO) CANT_EMPLEADOS_A_CARGO',
                                ])
                        ->from('tbl_empleados as E')
                        ->join('INNER JOIN','tbl_perfil_empleados as PE' , 'PE.FK_EMPLEADO = E.PK_EMPLEADO')
                        ->join('INNER JOIN','tbl_cat_puestos as P' , 'P.PK_PUESTO = PE.FK_PUESTO')
                        ->join('INNER JOIN','tbl_cat_estatus_recursos as ER' , 'ER.PK_ESTATUS_RECURSO = PE.FK_ESTATUS_RECURSO')
                        ->join('INNER JOIN','tbl_cat_administradoras as A' , 'PE.FK_ADMINISTRADORA = A.PK_ADMINISTRADORA')
                        ->join('LEFT JOIN','tbl_asignaciones as AG' , 'E.PK_EMPLEADO = AG.FK_EMPLEADO')
                        ->andWhere(['<>','PE.FK_ESTATUS_RECURSO',4])
                        ->andWhere(['=','PE.FK_TIPO_SERVICIO',2])
                        ->andFilterWhere(
                                ['and',
                                    ['LIKE', "CONCAT(E.NOMBRE_EMP,' ', E.APELLIDO_PAT_EMP ,' ',E.APELLIDO_MAT_EMP)", $nombre],
                                    ['=','P.PK_PUESTO', "$puesto"],
                                    ['=','PE.FK_ESTATUS_RECURSO', "$estatusRec"],
                                    ['not in','E.PK_EMPLEADO',$empleadosId],
                                    ['=', 'AG.FK_CLIENTE', $cliente],
                                    ['=', 'AG.FK_CONTACTO', $contacto],
                                    ['=', 'PE.FK_UNIDAD_NEGOCIO', $razon_social],
                                ])
                        ->orderBy(' NOMBRE_EMP ASC')
                      ,
              // 'pagination' => [
              //     'pageSize' => $tamanio_pagina,
              //     'page' => $pagina-1,
              // ],
            ]);
}
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'data'  => $dataProvider->getModels(),
            );

            return $res;

        }

        public function actionObtener_empleados_a_cargo()
        {
            $data = Yii::$app->request->post();
            $idEmpleado=(!empty($data['idEmpleado']))? trim($data['idEmpleado']):'';
            $idEmpleado=trim($idEmpleado,',');
            $datosEmpleadosACargo = (new \yii\db\Query())
                                ->select('emp.PK_EMPLEADO, emp.NOMBRE_EMP, emp.APELLIDO_PAT_EMP, emp.APELLIDO_MAT_EMP, perfil.FK_JEFE_DIRECTO')
                                ->from('tbl_empleados emp')
                                ->join('LEFT JOIN','tbl_perfil_empleados perfil',
                                        'emp.PK_EMPLEADO = perfil.FK_EMPLEADO')
                                ->where("perfil.FK_JEFE_DIRECTO IN ($idEmpleado) and perfil.FK_ESTATUS_RECURSO != 4 and perfil.FK_ESTATUS_RECURSO != 6")
                                ->all();

            $datosJefeDirecto = (new \yii\db\Query())
                                ->select([
                                    'emp.PK_EMPLEADO',
                                    "CONCAT(emp.NOMBRE_EMP,' ',emp.APELLIDO_PAT_EMP,' ',emp.APELLIDO_MAT_EMP) NOMBRE_EMP",
                                ])
                                ->from('tbl_empleados emp')
                                ->join('LEFT JOIN','tbl_perfil_empleados perfil',
                                        'emp.PK_EMPLEADO = perfil.FK_EMPLEADO')
                                ->join('LEFT JOIN','tbl_cat_puestos puestos',
                                        'perfil.FK_PUESTO = puestos.PK_PUESTO')
                                ->andWhere(['puestos.PERMITIR_SUBORDINADOS' => 1])
                                ->andWhere(['IN','perfil.FK_ESTATUS_RECURSO', [1,3,5] ])
                                ->andWhere("emp.PK_EMPLEADO NOT IN ($idEmpleado)")
                                ->all();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'empleadoReasignar'  => $datosEmpleadosACargo,
                'jefesDirectos' => $datosJefeDirecto,
            );

            return $res;

        }

        public function actionContactos()
        {
            $data = Yii::$app->request->get();
            $post=null;

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $q =(!empty($data['q']))? trim($data['q']):'';
            $p =(!empty($data['p']))? trim($data['p']):'';

            $p2='';
            if($p=='71'){
                $p2='2';
            }
            //INICIO HRIBI 06/05/16 - Se agrega validación para que el cliente con [PK_CLIENTE = 90] pueda visualizar contactos y ubicaciones de la misma forma que cliente Banorte
            if($p=='90'){
                $p2='2';
            }
            //FIN HRIBI 06/05/16
            $p= array($p);
            if($p2){
                $p[]=$p2;
            }

            if (!empty($q)) {
                $query = new Query;
                $query->select(['PK_CONTACTO AS  id','CONCAT_WS(\' \' , NOMBRE_CONTACTO, APELLIDO_PAT) AS text'])
                    ->from('tbl_cat_contactos')
                    ->where(['like','NOMBRE_CONTACTO', $q])
                    ->andWhere(['in','FK_CLIENTE', $p])
                    ->orderBy(['NOMBRE_CONTACTO'=>'SORT_ASC']);

                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
            else{
                $query = new Query;
                $query->select(['PK_CONTACTO AS  id','CONCAT_WS(\' \' , NOMBRE_CONTACTO, APELLIDO_PAT) AS text'])
                    ->from('tbl_cat_contactos')
                    ->andWhere(['in','FK_CLIENTE', $p])
                     ->orderBy(['NOMBRE_CONTACTO'=>'SORT_ASC']);

                $command = $query->createCommand();
                // dd($p);
            // dd($query->createCommand()->sql);
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
            return $out;

        }

        public function actionUbicaciones()
        {
            $data = Yii::$app->request->get();
            $post=null;

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $q =(!empty($data['q']))? trim($data['q']):'';
            $p =(!empty($data['p']))? trim($data['p']):'';

            /*$p2='';
            if($p=='71'){
                $p2='2';
            }
            //INICIO HRIBI 06/05/16 - Se agrega validación para que el cliente con [PK_CLIENTE = 90] pueda visualizar contactos y ubicaciones de la misma forma que cliente Banorte
            if($p=='90'){
                $p2='2';
            }
            //FIN HRIBI 06/05/16
            $p= array($p);
            if($p2){
                $p[]=$p2;
            }*/

            if (!is_null($q)) {
                $query = new Query;
                $query->select('U.PK_UBICACION AS  id, U.DESC_UBICACION AS text')
                    ->from('tbl_cat_ubicaciones AS U')
                    ->join('INNER JOIN','tbl_clientes AS C','C.PK_CLIENTE = U.FK_CLIENTE')
                    ->andWhere(['in','C.PK_CLIENTE', $p])
                    ->andWhere(['like','U.DESC_UBICACION', $q])
                    ->orderBy('U.DESC_UBICACION');
                $command = $query->createCommand();

                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select('U.PK_UBICACION AS  id, U.DESC_UBICACION AS text')
                    ->from('tbl_cat_ubicaciones AS U')
                    ->join('INNER JOIN','tbl_clientes AS C','C.PK_CLIENTE = U.FK_CLIENTE')
                    ->andWhere(['in','C.PK_CLIENTE', $p])
                    ->orderBy('U.DESC_UBICACION');
                $command = $query->createCommand();

                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }

            return $out;

        }

        public function actionProyectos()
        {
            $data = Yii::$app->request->get();
            $post=null;

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $q =(!empty($data['q']))? trim($data['q']):'';
            $p =(!empty($data['p']))? trim($data['p']):'';

            if (!is_null($q)) {
                $query = new Query;
                $query->select('PK_PROYECTO AS id, NOMBRE_PROYECTO AS text')
                    ->from('tbl_cat_proyectos')
                    ->where(['like','NOMBRE_PROYECTO', $q])
                    ->andWhere(['in','FK_CONTACTO', $p])
                    ->orderBy(['NOMBRE_PROYECTO'=>'SORT_ASC']);

                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
            else{
                $query = new Query;
                $query->select('PK_PROYECTO AS id, NOMBRE_PROYECTO AS text')
                    ->from('tbl_cat_proyectos')
                    ->andWhere(['in','FK_CONTACTO', $p])
                    ->orderBy(['NOMBRE_PROYECTO'=>'SORT_ASC']);

                $command = $query->createCommand();

                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
            return $out;

        }

        public function actionCancelar()
        {
            $vista_destino = 'index';
            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();//Inicio del funcionamiento de la 'Transacción' con su respectivo try/catch
            try{
                    $data = Yii::$app->request->post();

                    /* INICIO HRIBI 26/07/16 - Condición para verificar cual es la vista origen desde donde se realizó la petición de cancelar asignación */
                    if($data['vista_origen'] === "origen_update_asignacion"){
                        $vista_destino = ["asignaciones"."/"."view"."/".$data['idAsignacion']];
                        $modelComentariosAsignacionesOUA = new TblBitComentariosAsignaciones();
                        $data = Yii::$app->request->post();
                        $modelComentariosAsignacionesOUA->FK_ASIGNACION = $data['idAsignacion'];
                        $modelComentariosAsignacionesOUA->FK_ESTATUS_ASIGNACION = 5;
                        $modelComentariosAsignacionesOUA->MOTIVO = $data['TblBitComentariosAsignaciones']['MOTIVO'];
                        $modelComentariosAsignacionesOUA->COMENTARIOS = $data['TblBitComentariosAsignaciones']['COMENTARIOS'];
                        $modelComentariosAsignacionesOUA->FECHA_FIN = transform_date($data['TblBitComentariosAsignaciones']['FECHA_FIN'],'Y-m-d');
                        $modelComentariosAsignacionesOUA->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelComentariosAsignacionesOUA->save(false);

                    }else{
                        $vista_destino = ["index"];
                        $modelComentariosAsignaciones = new TblBitComentariosAsignaciones();

                        if ($modelComentariosAsignaciones->load(Yii::$app->request->post()) ){

                            $modelComentariosAsignaciones->FK_ASIGNACION = $data['idAsignacion'];
                            $modelComentariosAsignaciones->FK_ESTATUS_ASIGNACION = 5;
                            $modelComentariosAsignaciones->FECHA_FIN = transform_date($modelComentariosAsignaciones->FECHA_FIN,'Y-m-d');
                            $modelComentariosAsignaciones->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modelComentariosAsignaciones->save(false);
                        }
                    }
                    /* FIN HRIBI 26/07/16 */

                    $model = TblAsignaciones::find()->where(['PK_ASIGNACION' => $data['idAsignacion']])->limit(1)->one();

                    $modelEmpleadoAsignado = tblempleados::find()->where(['PK_EMPLEADO' => $model->FK_EMPLEADO])->limit(1)->one();
                    $modelPerfilEmpleado = TblPerfilEmpleados::find()->where(['FK_EMPLEADO' => $modelEmpleadoAsignado->PK_EMPLEADO])->limit(1)->one();

                    /* INICIO HRIBI 01/06/16 - Condición para cuando la asignación tenga un estatus diferente a => 'En Ejecución' se afecte al empleado[REEMPLAZANTE]
                           con sus datos propios antes de que fuera asignado en su valores de FK_UBICACION_FISICA Y FK_UNIDAD_NEGOCIO Y FK_ESTATUS_RECURSO */
                    $modelBitUbicacionFisicaEmpPropia = TblBitUbicacionFisica::find()->where(['FK_EMPLEADO'=>$modelPerfilEmpleado->FK_EMPLEADO])->andwhere(['PROPIA_CLIENTE'=>'PROPIA'])->orderBy('PK_BIT_UBICACION_FISICA DESC')->limit(1)->one();
                    $modelBitUnidadNegocioAsigEmpPropia = TblBitUnidadNegocioAsig::find()->where(['FK_EMPLEADO'=>$modelPerfilEmpleado->FK_EMPLEADO])->andwhere(['FK_REGISTRO'=>0])->andWhere(['like','MODULO_ORIGEN','empleados'])->orderBy('PK_UNIDAD_NEGOCIO_ASIG DESC')->limit(1)->one();
                    $modelPerfilEmpleado->load(Yii::$app->request->post());
                    $estatusActualEmpleado = $modelPerfilEmpleado->FK_ESTATUS_RECURSO;
                    $modelPerfilEmpleado->FK_UBICACION_FISICA = $modelBitUbicacionFisicaEmpPropia->FK_UBICACION;
                    $modelPerfilEmpleado->FK_UNIDAD_NEGOCIO =  $modelBitUnidadNegocioAsigEmpPropia->FK_UNIDAD_NEGOCIO;
                    $modelPerfilEmpleado->FK_ESTATUS_RECURSO = 3;
                    $modelPerfilEmpleado->save(false);
                    /* FIN HRIBI 01/06/16 */

                    $model->FK_ESTATUS_ASIGNACION = 5;
                    $model->save(false);

                    $descripcionBitacora = 'FK_CLIENTE='.$model->FK_CLIENTE.',NOMBRE='.$model->NOMBRE.',FK_UBICACION='.$model->FK_UBICACION.',FK_PROYECTO='.$model->FK_PROYECTO;
                    user_log_bitacora($descripcionBitacora,'Modificar informacion de Asignacion',$model->PK_ASIGNACION );

                    //Se llena bitacora de recursos de empleados
                    if($modelPerfilEmpleado->FK_ESTATUS_RECURSO != $estatusActualEmpleado){
                        user_log_bitacora_estatus_empleado($modelPerfilEmpleado->FK_EMPLEADO,$modelPerfilEmpleado->FK_ESTATUS_RECURSO);
                    }

                    $transaction->commit();//Si todo el proceso es correcto se ejectua la sentencia commit para guardar todas las operaciones en la base de datos.

                }catch(\Exception $e){
                    $transaction->rollBack();//Si sucede algún error durante la operación, se ejecuta la sentencia rollback para evitar que hagan modificaciones en base de datos.
                    throw $e;
                }//FIN del funcionamiento de la 'Transacción' con su respectivo try/catch

            $connection->close();
            return $this->redirect($vista_destino);

        }

    public function actionDocumentos()
    {
        // $tamanio_pagina=9;
        if (Yii::$app->request->isAjax)
        {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $idasignacion =(!empty($post['FK_ASIGNACION']))? trim($post['FK_ASIGNACION']):'';

            $filtro_años = (isset($post['año'])) ? "AND YEAR(`p1`.`FECHA_INI`) = ".$post['año'] : '';

            $connection = \Yii::$app->db;

            $query2 = "(SELECT
                        p1.HORAS_FACTURA,
                        p1.MONTO_FACTURA,
                        p1.FK_DOCUMENTO_ODC,
                        p1.FK_DOCUMENTO_HDE,
                        p1.FK_DOCUMENTO_FACTURA FK_FACTURA_EN_PERIODO,
                        f.FK_DOC_FACTURA FK_FACTURA_EN_FACTURA,
                        f.PK_FACTURA,
                        p1.HORAS_HDE,
                        p1.MONTO_HDE,
                        f.FK_ESTATUS,

                        p1.HORAS AS HORAS_ESTIMADAS,
                        ef.DESC_ESTATUS_FACTURA,
                        p1.HORAS_DEVENGAR,
                        e.NOMBRE_EMP,
                        e.APELLIDO_PAT_EMP,
                        e.APELLIDO_MAT_EMP,
                        p1.FECHA_INI,
                        DATE_FORMAT(p1.FECHA_INI,'%d/%m/%Y') AS FECHA_INI2,
                        p1.FECHA_FIN,
                        p1.FACTURA_PROVISION,
                        DATE_FORMAT(p1.FECHA_FIN,'%d/%m/%Y') AS FECHA_FIN2,
                        (CASE WHEN p1.TARIFA = NULL THEN 0 WHEN p1.TARIFA > 0 THEN p1.TARIFA ELSE 0 END) AS TARIFA,
                        (CASE WHEN p1.MONTO = NULL THEN 0 WHEN p1.MONTO > 0 THEN p1.MONTO ELSE 0 END) AS MONTO,
                        p1.PK_PERIODO,
                        (
                          SELECT f.FECHA_INGRESO_BANCO
                          FROM tbl_facturas f
                          LEFT JOIN tbl_periodos p2 ON p2.PK_PERIODO = f.FK_PERIODO
                          LEFT JOIN tbl_asignaciones a2 ON p2.FK_ASIGNACION = a2.PK_ASIGNACION
                          WHERE a2.PK_ASIGNACION = $idasignacion AND p1.pk_periodo=p2.pk_periodo
                          LIMIT 1
                        ) AS FECHA_INGRESO_BANCO,
                        (
                            SELECT f2.FECHA_ENTREGA_CLIENTE
                            FROM tbl_facturas f2
                            LEFT JOIN tbl_periodos p6 ON p6.PK_PERIODO = f2.FK_PERIODO
                            LEFT JOIN tbl_asignaciones a3 ON p6.FK_ASIGNACION = a3.PK_ASIGNACION
                            WHERE a3.PK_ASIGNACION = $idasignacion AND p1.pk_periodo=p6.pk_periodo
                            LIMIT 1
                        ) AS FECHA_ENTREGA_CLIENTE,
                        (
                          SELECT
                            d1.NUM_DOCUMENTO
                          FROM tbl_documentos d1
                          LEFT JOIN tbl_periodos p3
                            ON p3.FK_DOCUMENTO_ODC = d1.PK_DOCUMENTO
                          WHERE a.PK_ASIGNACION = $idasignacion AND d1.FK_TIPO_DOCUMENTO = 2 AND p1.pk_periodo=p3.pk_periodo
                          LIMIT 1
                        ) AS ODC,
                        (
                          SELECT
                            d2.NUM_DOCUMENTO
                          FROM tbl_documentos d2
                          LEFT JOIN tbl_periodos p4
                            ON p4.FK_DOCUMENTO_HDE = d2.PK_DOCUMENTO
                          WHERE a.PK_ASIGNACION = $idasignacion AND d2.FK_TIPO_DOCUMENTO = 3 AND p1.pk_periodo=p4.pk_periodo
                          LIMIT 1
                        ) AS HDE,
                        (
                          SELECT
                            d3.NUM_DOCUMENTO
                          FROM tbl_documentos d3
                          LEFT JOIN tbl_periodos p5
                            ON p5.FK_DOCUMENTO_FACTURA = d3.PK_DOCUMENTO
                          WHERE a.PK_ASIGNACION = $idasignacion AND d3.FK_TIPO_DOCUMENTO = 4 AND p1.pk_periodo=p5.pk_periodo
                          LIMIT 1
                        ) AS FACTURA,
                        DATEDIFF(CURDATE(), p1.FECHA_FIN) AS DIFF_FECHA_HOY,
                        'ES_PERIODO' AS ES_PERIODO,
                        bp.COMENTARIO
                        FROM tbl_periodos p1
                        INNER JOIN tbl_asignaciones a
                          ON a.PK_ASIGNACION = p1.FK_ASIGNACION
                        INNER JOIN tbl_empleados e
                          ON p1.fk_empleado = e.PK_empleado
                        LEFT JOIN tbl_bit_periodos bp
                          on bp.FK_PERIODO = p1.PK_PERIODO
                        LEFT JOIN tbl_facturas f
                          on f.FK_PERIODO= p1.PK_PERIODO
                          AND f.FK_DOC_FACTURA = p1.FK_DOCUMENTO_FACTURA
                        LEFT JOIN tbl_cat_estatus_facturas ef
                          on ef.PK_ESTATUS_FACTURA= f.FK_ESTATUS
                        WHERE p1.FK_ASIGNACION = '$idasignacion'
                        ORDER BY p1.fecha_ini)
                        UNION
                        (
                          SELECT
                            '', '', '', '', '', '', '', '', '', '',
                            '', '', '', '', '', '',
                            ba.FECHA_FIN as FECHA_INI,
                            '',
                            ba.FECHA_RETOMADA as FECHA_FIN,
                            '', '', '', '', '', '', '', '', '', '',
                            'ES_DETENIDA' as ES_PERIODO,
                            '', ''
                          FROM tbl_bit_comentarios_asignaciones ba
                          WHERE ba.FK_ASIGNACION = $idasignacion
                        )
                        ORDER BY FECHA_INI ASC";

                $resultado_copia = $connection->createCommand($query2)->queryAll();

                $query ="(SELECT
                            p1.HORAS_FACTURA,
                            p1.MONTO_FACTURA,
                            p1.FK_DOCUMENTO_ODC,
                            p1.FK_DOCUMENTO_HDE,
                            p1.FK_DOCUMENTO_FACTURA FK_FACTURA_EN_PERIODO,
                            f.FK_DOC_FACTURA FK_FACTURA_EN_FACTURA,
                            f.PK_FACTURA,
                            p1.HORAS_HDE,
                            p1.MONTO_HDE,
                            f.FK_ESTATUS,

                            p1.HORAS AS HORAS_ESTIMADAS,
                            ef.DESC_ESTATUS_FACTURA,
                            p1.HORAS_DEVENGAR,
                            e.NOMBRE_EMP,
                            e.APELLIDO_PAT_EMP,
                            e.APELLIDO_MAT_EMP,
                            p1.FECHA_INI,
                            DATE_FORMAT(p1.FECHA_INI,'%d/%m/%Y') AS FECHA_INI2,
                            p1.FECHA_FIN,
                            p1.FACTURA_PROVISION,
                            DATE_FORMAT(p1.FECHA_FIN,'%d/%m/%Y') AS FECHA_FIN2,
                            (CASE WHEN p1.TARIFA = NULL THEN 0 WHEN p1.TARIFA > 0 THEN p1.TARIFA ELSE 0 END) AS TARIFA,
                            (CASE WHEN p1.MONTO = NULL THEN 0 WHEN p1.MONTO > 0 THEN p1.MONTO ELSE 0 END) AS MONTO,
                            p1.PK_PERIODO,
                            (
                              SELECT f.FECHA_INGRESO_BANCO
                              FROM tbl_facturas f
                              LEFT JOIN tbl_periodos p2 ON p2.PK_PERIODO = f.FK_PERIODO
                              LEFT JOIN tbl_asignaciones a2 ON p2.FK_ASIGNACION = a2.PK_ASIGNACION
                              WHERE a2.PK_ASIGNACION = $idasignacion AND p1.pk_periodo=p2.pk_periodo
                              LIMIT 1
                            ) AS FECHA_INGRESO_BANCO,
                            (
                                SELECT f2.FECHA_ENTREGA_CLIENTE
                                FROM tbl_facturas f2
                                LEFT JOIN tbl_periodos p6 ON p6.PK_PERIODO = f2.FK_PERIODO
                                LEFT JOIN tbl_asignaciones a3 ON p6.FK_ASIGNACION = a3.PK_ASIGNACION
                                WHERE a3.PK_ASIGNACION = $idasignacion AND p1.pk_periodo=p6.pk_periodo
                                LIMIT 1
                            ) AS FECHA_ENTREGA_CLIENTE,
                            (
                              SELECT
                                d1.NUM_DOCUMENTO
                              FROM tbl_documentos d1
                              LEFT JOIN tbl_periodos p3
                                ON p3.FK_DOCUMENTO_ODC = d1.PK_DOCUMENTO
                              WHERE a.PK_ASIGNACION = $idasignacion AND d1.FK_TIPO_DOCUMENTO = 2 AND p1.pk_periodo=p3.pk_periodo
                              LIMIT 1
                            ) AS ODC,
                            (
                              SELECT
                                d2.NUM_DOCUMENTO
                              FROM tbl_documentos d2
                              LEFT JOIN tbl_periodos p4
                                ON p4.FK_DOCUMENTO_HDE = d2.PK_DOCUMENTO
                              WHERE a.PK_ASIGNACION = $idasignacion AND d2.FK_TIPO_DOCUMENTO = 3 AND p1.pk_periodo=p4.pk_periodo
                              LIMIT 1
                            ) AS HDE,
                            (
                              SELECT
                                d3.NUM_DOCUMENTO
                              FROM tbl_documentos d3
                              LEFT JOIN tbl_periodos p5
                                ON p5.FK_DOCUMENTO_FACTURA = d3.PK_DOCUMENTO
                              WHERE a.PK_ASIGNACION = $idasignacion AND d3.FK_TIPO_DOCUMENTO = 4 AND p1.pk_periodo=p5.pk_periodo
                              LIMIT 1
                            ) AS FACTURA,
                            DATEDIFF(CURDATE(), p1.FECHA_FIN) AS DIFF_FECHA_HOY,
                            'ES_PERIODO' AS ES_PERIODO,
                            bp.COMENTARIO
                            FROM tbl_periodos p1
                            INNER JOIN tbl_asignaciones a
                              ON a.PK_ASIGNACION = p1.FK_ASIGNACION
                            INNER JOIN tbl_empleados e
                              ON p1.fk_empleado = e.PK_empleado
                            LEFT JOIN tbl_bit_periodos bp
                              on bp.FK_PERIODO = p1.PK_PERIODO
                            LEFT JOIN tbl_facturas f
                              on f.FK_PERIODO= p1.PK_PERIODO
                              AND f.FK_DOC_FACTURA = p1.FK_DOCUMENTO_FACTURA
                            LEFT JOIN tbl_cat_estatus_facturas ef
                              on ef.PK_ESTATUS_FACTURA= f.FK_ESTATUS
                            WHERE p1.FK_ASIGNACION = '$idasignacion' ".$filtro_años."
                            ORDER BY p1.fecha_ini)
                            UNION
                            (
                              SELECT
                                '', '', '', '', '', '', '', '', '', '',
                                '', '', '', '', '', '',
                                ba.FECHA_FIN as FECHA_INI,
                                '',
                                ba.FECHA_RETOMADA as FECHA_FIN,
                                '', '', '', '', '', '', '', '', '', '',
                                'ES_DETENIDA' as ES_PERIODO,
                                '', ''
                              FROM tbl_bit_comentarios_asignaciones ba
                              WHERE ba.FK_ASIGNACION = $idasignacion
                            )
                            ORDER BY FECHA_INI ASC";
                $sql = $connection->createCommand($query)->queryAll();
                $resultado=$sql;
                $sinODC = 0;
                $sinHDE = 0;
                $sinFactura = 0;
                $orden=1;
                $periodo=1;

                $años = array();
                $color = array();
                $contadorAños = 0;
                foreach ($resultado_copia as $key => $value) {

                    $anio = explode('-', $resultado_copia[$key]['FECHA_INI']);

                    if (!in_array($anio[0], $años)) {
                        $años[] = $anio[0];
                        $contadorAños++;
                    }

                    //$color[$contadorAños] = (($anio[0] < date('Y')) && !$resultado_copia[$key]['FECHA_INGRESO_BANCO']) ? '<a href="javascript:void(0);" onclick="consulta_años('.$anio[0].');"><span style="color:red" class="font-bold">'.$anio[0].'</span></a>':'<a href="javascript:void(0);" onclick="consulta_años('.$anio[0].');"><span style="color:blue" class="font-bold">'.$anio[0].'</span></a>';
                    $color[$contadorAños] = (($anio[0] < date('Y')) ) ? '<a href="javascript:void(0);" onclick="consulta_años('.$anio[0].');"><span style="color:red" class="font-bold">'.$anio[0].'</span></a>':'<a href="javascript:void(0);" onclick="consulta_años('.$anio[0].');"><span style="color:blue" class="font-bold">'.$anio[0].'</span></a>';

                }

                foreach ($resultado as $key => $value) {

                    $resultado[$key]['MONTO']=number_format((float)$resultado[$key]['MONTO'],2);
                    $resultado[$key]['ORDEN']=$orden;
                    if($resultado[$key]['ES_PERIODO']=='ES_PERIODO'){
                        $resultado[$key]['PERIODO']=$periodo;
                    }else{
                        $resultado[$key]['PERIODO']=0;
                    }
                    if($resultado[$key]['ODC']== null && $resultado[$key]['DIFF_FECHA_HOY']>=7){
                        $sinODC++;
                        $resultado[$key]['ESTATUS']=1;
                    }
                    if($resultado[$key]['HDE']== null && $resultado[$key]['DIFF_FECHA_HOY']>=7){
                        $sinHDE++;
                        $resultado[$key]['ESTATUS']=1;
                    }
                    if($resultado[$key]['FACTURA']== null && $resultado[$key]['DIFF_FECHA_HOY']>=7){
                        $sinFactura++;
                        $resultado[$key]['ESTATUS']=1;
                    }

                    /*Monto Real*/
                    $horasDevengar                  = $resultado[$key]['HORAS_DEVENGAR'] ? $resultado[$key]['HORAS_DEVENGAR'] : 0;
                    $tarifa                         = $resultado[$key]['TARIFA'] ? $resultado[$key]['TARIFA'] : 0;
                    $montoReal                      = $horasDevengar * $tarifa;
                    $resultado[$key]['MONTO_REAL']  = number_format((float)$montoReal,2);


                    $orden++;
                    $periodo++;
                }


            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $res = array(
                    // 'pagina'        => $pagina,
                    'data'          => $resultado,
                    'post'          => $data,
                    'años'          => $color,
                    'añosNum'       => $años,
                    'resultado_copia'         => $resultado_copia,
                    // 'sql'          => dataProvider_last_query($dataProvider),
                    // 'command'          => $sql,
                    // 'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                );
                $connection->close();
                return $res;
            }
            else{
                return $this->render('index', [
                    'total_paginas' => 0,
                ]);
            }

        }

        public function actionPrec()
        {
            $data = Yii::$app->request->post();
            $post=null;

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $clienteId =(!empty($data['cliente']))? trim($data['cliente']):'';
            $valor_tarifa=0;

            if (!is_null($clienteId)) {
                $query = new Query;
                $query->select('PORC_RENTA_ESPERADA_DE as DE, PORC_RENTA_ESPERADA_A as A, HORAS_ASIGNACION as hora')
                  ->from('tbl_clientes')
                  ->where(['=','PK_CLIENTE', "$clienteId"]);

                $command = $query->createCommand();
                $dt = $command->queryAll();
                $out = $dt;

                $fks_tarifa = TblTarifasClientes::find()->select(['FK_CAT_TARIFA'])->where(['FK_CLIENTE'=>$clienteId])->column();
                if($fks_tarifa){
                    $valor_tarifa = TblCatTarifas::find()->andWhere(['in', 'PK_CAT_TARIFA', $fks_tarifa])->all();
                }
            }


            $res = [
                'query'=>$out,
                'fks_tarifa'=>$fks_tarifa,
                'valor_tarifa'=>$valor_tarifa,
                'clienteId'=>$clienteId,
            ];

            return $res;

        }

        public function actionRegistrar_bolsa()
        {

            $model= new TblCatBolsas();
            $modeloDocumentos = new TblDocumentos();

            $modelFile = new SubirArchivo();
            $modelFile->extensions = 'pdf';
            $modelFile->noRequired = true;

            $datosRezonesSociales= TblCatRazonSocial::find()->select(['PK_RAZON_SOCIAL','DESC_RAZON_SOCIAL'])->asArray()->all();

            if(Yii::$app->request->post()){
                $data = Yii::$app->request->post();

                // dd($data);

                $model->load($data);
                $model->MONTO_BOLSA= sanitized_number($model->MONTO_BOLSA);

                $model->HORAS_DISPONIBLES = $model->HORAS;
                $model->MONTO_DISPONIBLE  = $model->MONTO_BOLSA;
                $model->FECHA_ODC         = transform_date($model->FECHA_ODC,'Y-m-d');
                $model->FECHA_INI         = transform_date($model->FECHA_INI,'Y-m-d');
                $model->FECHA_FIN         = transform_date($model->FECHA_FIN,'Y-m-d');
                $model->FK_USUARIO        = user_info()['PK_USUARIO'];
                $model->FECHA_REGISTRO    = date('Y-m-d h:i:s');

                if(isset($data['pk_cat_tarifa_select'])){
                    $model->FK_CAT_TARIFA = $data['pk_cat_tarifa_select'];
                }

                $modelFile->file = UploadedFile::getInstance($modelFile, '[6]file');

                if (!empty($modelFile->file)) {
                    $fechaHoraHoy                          = date('YmdHis');
                    $rutaGuardado                          = '../uploads/DocumentosPeriodos/';
                    $nombreFisico                          = quitar_acentos(utf8_decode($fechaHoraHoy.'_'.$modelFile->file->basename));
                    $nombreBD                              = quitar_acentos(utf8_decode($modelFile->file->basename));
                    $extension                             = $modelFile->upload($rutaGuardado,$nombreFisico);
                    $rutaDoc                               = '/uploads/DocumentosPeriodos/';
                    $modeloDocumentos->NOMBRE_DOCUMENTO    = $nombreBD.'.'.$extension;
                    $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                }

                $modeloDocumentos->FECHA_DOCUMENTO      = $model->FECHA_ODC;
                $modeloDocumentos->NUM_DOCUMENTO        = $model->NUMERO_BOLSA;
                $modeloDocumentos->NUM_SP               = null;
                $modeloDocumentos->TARIFA               = $model->TARIFA;
                $modeloDocumentos->HORAS                = $model->HORAS;
                $modeloDocumentos->MONTO                = $model->MONTO_BOLSA;
                $modeloDocumentos->FK_RAZON_SOCIAL      = $model->FK_EMPRESA;
                $modeloDocumentos->FK_ASIGNACION        = null;
                $modeloDocumentos->FK_TIPO_DOCUMENTO    = 2;
                $modeloDocumentos->FK_UNIDAD_NEGOCIO    = null;
                $modeloDocumentos->FECHA_REGISTRO       = $model->FECHA_REGISTRO;
                $modeloDocumentos->FK_CLIENTE           = null;
                $modeloDocumentos->CONSECUTIVO_TIPO_DOC = 0;

                $modeloDocumentos->save(false);
                $model->FK_DOCUMENTO=$modeloDocumentos->PK_DOCUMENTO;
                $model->save(false);

            //     return $this->render('bolsa/index',[
            //         'modelBolsa'=>$model,
            //     ]);
                $this->redirect(['index_bolsa','create'=>1]);
            }

            return $this->render('bolsa/create_bolsa',[
                'model'=>$model,
                'modelFile'=>$modelFile,
                'datosRezonesSociales'=>$datosRezonesSociales,
                ]);

        }

        public function actionIndex_bolsa()
        {
            $datosRezonesSociales= TblCatRazonSocial::find()->select(['PK_RAZON_SOCIAL','DESC_RAZON_SOCIAL'])->asArray()->all();

            $tamanio_pagina= 20;
            if(Yii::$app->request->isAjax){
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $data = Yii::$app->request->post();

                parse_str($data['data'],$post);

                $numero_bolsa = (isset($post['numero_bolsa']))?$post['numero_bolsa']:'';
                $fk_empresa   = (isset($post['fk_empresa']))?$post['fk_empresa']:'';
                $pagina       = (!empty($post['page']))? trim($post['page']):'';

                if(empty($pagina)){
                    $pagina=0;
                }else{
                    $pagina= $pagina-1;
                }

                $total_registros = (new \Yii\db\Query)
                    ->select([
                        'count(*) as count'
                        ])
                    ->from('tbl_cat_bolsas as cb')
                    ->join('left join','tbl_cat_razon_social as ra','ra.PK_RAZON_SOCIAL= cb.FK_EMPRESA')
                    ->andFilterWhere(['and',
                            ['LIKE','cb.NUMERO_BOLSA',$numero_bolsa],
                            ['=','ra.PK_RAZON_SOCIAL',$fk_empresa],
                        ])
                    ->one();

                $total_paginas= ceil($total_registros['count']/$tamanio_pagina);

                if($total_registros['count']<=$tamanio_pagina){
                    $pagina=0;
                }


                $query = (new \Yii\db\Query)
                    ->select([
                        'cb.PK_BOLSA',
                        'cb.NUMERO_BOLSA',
                        'DATE_FORMAT(cb.FECHA_ODC, \'%d/%m/%Y\') as FECHA_ODC',
                        'DATE_FORMAT(cb.FECHA_INI, \'%d/%m/%Y\') as FECHA_INI',
                        'DATE_FORMAT(cb.FECHA_FIN, \'%d/%m/%Y\') as FECHA_FIN',
                        'CONCAT("$ ", FORMAT(cb.TARIFA, 2)) as TARIFA',
                        'ct.DESC_TARIFA',
                        'cb.HORAS',
                        'CONCAT("$ ", FORMAT(cb.MONTO_BOLSA, 2)) as MONTO_BOLSA',
                        'cb.HORAS_DISPONIBLES',
                        'CONCAT("$ ", FORMAT(cb.MONTO_DISPONIBLE, 2)) as MONTO_DISPONIBLE',
                        'ra.EMPRESA'
                        ])
                    ->from('tbl_cat_bolsas as cb')
                    ->join('left join','tbl_cat_razon_social as ra','ra.PK_RAZON_SOCIAL = cb.FK_EMPRESA')
                    ->join('left join','tbl_cat_tarifas as ct','ct.PK_CAT_TARIFA = cb.FK_CAT_TARIFA')
                    ->andFilterWhere(['and',
                            ['LIKE','cb.NUMERO_BOLSA',$numero_bolsa],
                            ['=','ra.PK_RAZON_SOCIAL',$fk_empresa],
                        ])
                    ->offset($pagina*$tamanio_pagina)
                    ->limit($tamanio_pagina)
                    ->all();

                $res =[
                    // 'data'=>$data,
                    'query'=>$query,
                    'total_paginas'=>$total_paginas,
                    'pagina'=>$pagina,
                    'total_registros'=>$total_registros['count'],
                    // 'post'=>$post,
                ];
                return $res;
            }else{

                return $this->render('bolsa/index',[
                    'datosRezonesSociales'=>ArrayHelper::map($datosRezonesSociales, 'PK_RAZON_SOCIAL','DESC_RAZON_SOCIAL'),
                ]);
           }

        }

        public function actionProyectos_bolsa()
        {
            $data = Yii::$app->request->get();
            $post=null;

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $q =(!empty($data['q']))? trim($data['q']):'';
            $p =(!empty($data['p']))? trim($data['p']):'';

                $query = new Query;
                $query->select('PK_PROYECTO AS id, NOMBRE_PROYECTO AS text')
                    ->from('tbl_cat_proyectos')
                    ->where(['like','NOMBRE_PROYECTO', $q])
                    // ->andWhere(['in','FK_CONTACTO', $p])
                    ->orderBy(['NOMBRE_PROYECTO'=>'SORT_ASC']);

                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            return $out;

        }

        public function actionAsociar_bolsa()
        {

            $post         =[];
            $bolsa        = new TblCatBolsas();
            $razon_social = ArrayHelper::map(TblCatUnidadesNegocio::find()->asArray()->orderBy('DESC_UNIDAD_NEGOCIO')->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
            $empresa      = ArrayHelper::map(TblCatRazonSocial::find()->asArray()->orderBy('DESC_RAZON_SOCIAL')->all(), 'PK_RAZON_SOCIAL', 'DESC_RAZON_SOCIAL');
            $proyectos    = ArrayHelper::map(TblCatProyectos::find()->asArray()->orderBy('NOMBRE_PROYECTO')->all(), 'PK_PROYECTO', 'NOMBRE_PROYECTO');
            $fabrica_desarrollo    = ArrayHelper::map(TblCatFabricaDesarrollo::find()->asArray()->orderBy('DESC_FABRICA')->all(), 'FK_FABRICA', 'DESC_FABRICA');

            $cliente= [];
            $desc_cliente = '';
            if(Yii::$app->request->isAjax){
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if(Yii::$app->request->post()){
                    $post= Yii::$app->request->post();
                    if(isset($post['numero_bolsa'])&&!empty($post['numero_bolsa'])){
                        $bolsa= TblCatBolsas::find()
                        ->select([
                            'PK_BOLSA',
                            'NUMERO_BOLSA',
                            'DATE_FORMAT(FECHA_ODC, \'%d/%m/%Y\') as FECHA_ODC',
                            'DATE_FORMAT(FECHA_INI, \'%d/%m/%Y\') as FECHA_INI',
                            'DATE_FORMAT(FECHA_FIN, \'%d/%m/%Y\') as FECHA_FIN',
                            'CONCAT("$ ", FORMAT(TARIFA, 2)) as TARIFA',
                            'TARIFA as TARIFA2',
                            'HORAS',
                            'CONCAT("$ ", FORMAT(MONTO_BOLSA, 2)) as MONTO_BOLSA',
                            'HORAS_DISPONIBLES',
                            'CONCAT("$ ", FORMAT(MONTO_DISPONIBLE, 2)) as MONTO_DISPONIBLE',
                            'MONTO_DISPONIBLE as MONTO_DISPONIBLE2',
                            'FK_EMPRESA',
                            'FK_CLIENTE',
                            'FK_CAT_TARIFA',
                        ])
                        ->where(['NUMERO_BOLSA'=>trim($post['numero_bolsa'])])->asArray()->limit(1)->one();

                        if($bolsa['FK_CLIENTE']){
                        $desc_cliente= TblClientes::find()->where(['PK_CLIENTE'=>$bolsa['FK_CLIENTE']])->asArray()->limit(1)->one();
                        }
                    }
                }
                $res=[
                    'bolsa'=>$bolsa,
                    'desc_cliente'=>$desc_cliente,
                ];
                return $res;
            }else{
                if(Yii::$app->request->post()){
                    $post= Yii::$app->request->post();
                    //dd($post);
                    if(isset($post['guardar'])&&!empty($post['guardar'])){
                        $model_bolsa                    = TblCatBolsas::find()->where(['PK_BOLSA'=>$post['id_bolsa']])->one();
                        $model_bolsa->MONTO_DISPONIBLE  = sanitized_number($post['monto_disponible']);
                        $model_bolsa->HORAS_DISPONIBLES = sanitized_number($post['horas_disponibles']);

                        $documento_bolsa = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$model_bolsa->FK_DOCUMENTO])->one();
                        $documento_bolsa->NUM_DOCUMENTO = $model_bolsa->NUMERO_BOLSA;
                        $documento_bolsa->save(false);

                        $ubicacion_doc_odc = $documento_bolsa->DOCUMENTO_UBICACION;

                        $horasCorreo = 0;
                        $pk_periodos_bls = '';

                        $fk_bit_bls = 0;

                        if($post['tipoElementosAsociarBolsa'] == 'ASIGNACIONES'){

                            /**********************************************************************************************************
                            *---------------------------------------------------------------------------------------------------------*
                            *                      SECCION PARA RELACIONAR PERIODOS DE ASIGNACIONES A BOLSAS                          *
                            *---------------------------------------------------------------------------------------------------------*
                            ***********************************************************************************************************/

                            $pks_asignaciones =[];
                            $sanitized_tarifa = sanitized_number($post['tarifa']);

                            foreach($post['horas'] as $key => $value) {
                                    $horasCorreo = $horasCorreo + $value;
                            }
                            foreach ($post['periodo'] as $key => $value) {
                                if(empty($post['horas'][$key])) {
                                    continue;
                                }

                                $periodo         = TblPeriodos::find()->where(['PK_PERIODO'=>$key])->one();
                                $pk_periodos_bls .= $key.',';

                                if(!in_array($periodo->FK_ASIGNACION, $pks_asignaciones)){
                                    $pks_asignaciones[]=$periodo->FK_ASIGNACION;
                                }

                                $sanitized_monto= sanitized_number($post['monto'][$key]);

                                $documento_odc                       = new TblDocumentos();
                                $documento_odc->FECHA_DOCUMENTO      = $documento_bolsa->FECHA_DOCUMENTO;
                                $documento_odc->NUM_DOCUMENTO        = 'BLS_ODC_'.$documento_bolsa->NUM_DOCUMENTO;
                                $documento_odc->DOCUMENTO_UBICACION  = $documento_bolsa->DOCUMENTO_UBICACION;
                                $documento_odc->NOMBRE_DOCUMENTO     = $documento_bolsa->NOMBRE_DOCUMENTO;
                                $documento_odc->TARIFA               = $documento_bolsa->TARIFA;
                                $documento_odc->FK_ASIGNACION        = $periodo->FK_ASIGNACION;
                                $documento_odc->FK_TIPO_DOCUMENTO    = 2;
                                $documento_odc->CONSECUTIVO_TIPO_DOC = 0;
                                $documento_odc->FK_RAZON_SOCIAL      = $model_bolsa->FK_EMPRESA;
                                $documento_odc->save(false);

                                $documento_hde                       = new TblDocumentos();
                                $documento_hde->FECHA_DOCUMENTO      = $documento_bolsa->FECHA_DOCUMENTO;
                                $documento_hde->NUM_DOCUMENTO        = 'BLS_HDE_'.$documento_bolsa->NUM_DOCUMENTO;
                                $documento_hde->DOCUMENTO_UBICACION  = $documento_bolsa->DOCUMENTO_UBICACION;
                                $documento_hde->NOMBRE_DOCUMENTO     = $documento_bolsa->NOMBRE_DOCUMENTO;
                                $documento_hde->TARIFA               = $documento_bolsa->TARIFA;
                                $documento_hde->FK_ASIGNACION        = $periodo->FK_ASIGNACION;
                                $documento_hde->FK_TIPO_DOCUMENTO    = 3;
                                $documento_hde->CONSECUTIVO_TIPO_DOC = 0;
                                $documento_hde->FK_RAZON_SOCIAL      = $model_bolsa->FK_EMPRESA;
                                $documento_hde->save(false);

                                $periodo->HORAS            = $post['horas'][$key];
                                $periodo->MONTO            = $sanitized_monto;
                                $periodo->TARIFA           = $sanitized_tarifa;

                                $periodo->FK_DOCUMENTO_ODC = $documento_odc->PK_DOCUMENTO;
                                $periodo->FK_DOCUMENTO_HDE = $documento_hde->PK_DOCUMENTO;
                                $periodo->HORAS_HDE        = $post['horas'][$key];
                                $periodo->MONTO_HDE        = $sanitized_monto;
                                $periodo->TARIFA_HDE       = $sanitized_tarifa;
                                $periodo->HORAS_DEVENGAR   = $post['horas'][$key];

                                if($post['fk_periodo_dividir']==$key&&isset($post['dividir_periodo'])&&!empty($post['dividir_periodo'])){
                                    $new_periodo_dividir = new TblPeriodos();
                                    $new_periodo_dividir->load($periodo);

                                    $new_periodo_dividir->FK_ASIGNACION=$periodo->FK_ASIGNACION;
                                    $new_periodo_dividir->FECHA_FIN=transform_date($post['fecha_fin_periodo2'],'Y-m-d');
                                    $new_periodo_dividir->TARIFA=$periodo->TARIFA;
                                    $new_periodo_dividir->FK_EMPLEADO=$periodo->FK_EMPLEADO;
                                    $new_periodo_dividir->FECHA_INGRESO=date('Y-m-d');

                                    $periodo->FECHA_FIN = transform_date($post['fecha_fin_periodo1'],'Y-m-d');

                                    $new_periodo_dividir->FECHA_INI= transform_date($post['fecha_ini_periodo2'],'Y-m-d');
                                    $new_periodo_dividir->HORAS = $post['horas_periodo2'];
                                    $new_periodo_dividir->MONTO= $new_periodo_dividir->HORAS* $new_periodo_dividir->TARIFA;
                                    // if(isset($post['fk_cat_tarifa'])){
                                    //     $new_periodo_dividir->FK_CAT_TARIFA    = $post['fk_cat_tarifa'];
                                    // }
                                    $new_periodo_dividir->FK_DOCUMENTO_ODC= null;
                                    $new_periodo_dividir->FK_DOCUMENTO_HDE= null;
                                    $new_periodo_dividir->TARIFA_HDE= null;
                                    $new_periodo_dividir->HORAS_HDE= null;
                                    $new_periodo_dividir->MONTO_HDE= null;
                                    $new_periodo_dividir->save(false);

                                    if(isset($post['horas_restantes'])&&!empty($post['horas_restantes'])){
                                        $new_bit_periodo = new TblBitPeriodos();
                                        $new_bit_periodo->FK_PERIODO = $new_periodo_dividir->PK_PERIODO;
                                        $new_bit_periodo->COMENTARIO = 'Se agrega '.$post['horas_restantes'].(($post['horas_restantes']>1)?' horas':' hora').' del periodo anterior';
                                        $new_bit_periodo->FECHA_REGISTRO = date('Y-m-d h:i:s');
                                        $new_bit_periodo->save(false);

                                        $new_bit_periodo = new TblBitPeriodos();
                                        $new_bit_periodo->FK_PERIODO = $periodo->PK_PERIODO;
                                        $new_bit_periodo->COMENTARIO = 'Faltante '.$post['horas_restantes'].(($post['horas_restantes']>1)?' horas':' hora').' para completar d&iacute;as del periodo';
                                        $new_bit_periodo->FECHA_REGISTRO = date('Y-m-d h:i:s');
                                        $new_bit_periodo->save(false);
                                    }

                                    $seguimiento = new TblAsignacionesSeguimiento();
                                    $seguimiento->FECHA_REGISTRO= date('Y-m-d h:i:s');
                                    $seguimiento->FK_ASIGNACION = $new_periodo_dividir->FK_ASIGNACION;
                                    $seguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                                    $seguimiento->COMENTARIOS = 'Se divide periodo de fechas del '.transform_date($periodo->FECHA_INI,'d/m/Y').' al '.transform_date($new_periodo_dividir->FECHA_FIN,'d/m/Y').' por que la bolsa no cubrio con las horas correspondientes';
                                    $seguimiento->save(false);
                                }

                                // if(isset($post['fk_cat_tarifa'])){
                                //     $periodo->FK_CAT_TARIFA    = $post['fk_cat_tarifa'];
                                // }
                                $periodo->FK_BOLSA = $model_bolsa->PK_BOLSA;
                                $periodo->save(false);


                                /**
                                 * Cambiar Tarifa
                                 */
                                $proximos_periodos= TblPeriodos::find()->where(['FK_ASIGNACION'=>$periodo->FK_ASIGNACION])->andWhere(['>','FECHA_INI',$periodo->FECHA_INI])->all();
                                foreach ($proximos_periodos as $key => $value) {
                                    if(!$value->FK_DOCUMENTO_ODC){
                                        $next_periodo= TblPeriodos::find()->where(['PK_PERIODO'=>$value->PK_PERIODO])->one();
                                        $next_periodo->TARIFA = $sanitized_tarifa;
                                        $new_monto = $next_periodo->TARIFA * $next_periodo->HORAS;
                                        $next_periodo->MONTO=$new_monto;
                                        $next_periodo->save(false);
                                    }else{
                                        break;
                                    }
                                }

                            }

                            //Guardar en bitacora asociacion a la bolsa
                            if($pk_periodos_bls != '') {
                                $bit_bolsa = new TblBitBlsDocs();
                                $bit_bolsa->FK_BOLSA = $model_bolsa->PK_BOLSA;
                                $bit_bolsa->FK_PERIODOS = $pk_periodos_bls;
                                $bit_bolsa->FECHA_REGISTRO = date('Y-m-d h:i:s');
                                if($bit_bolsa->save(false)){
                                    $fk_bit_bls=$bit_bolsa->PK_BIT_BLS;
                                }
                            }

                            foreach ($pks_asignaciones as $key => $value) {
                                $asignacion= TblAsignaciones::find()->where(['PK_ASIGNACION'=>$value])->one();
                                $asignacion->TARIFA=$sanitized_tarifa;
                                if(isset($post['fk_cat_tarifa'])){
                                    $asignacion->FK_CAT_TARIFA = $post['fk_cat_tarifa'];
                                }
                                $asignacion->save(false);
                            }

                        }else{
                            /**********************************************************************************************************
                            *---------------------------------------------------------------------------------------------------------*
                            *                      SECCION PARA RELACIONAR PERIODOS DE PROYECTOS A BOLSAS                             *
                            *---------------------------------------------------------------------------------------------------------*
                            ***********************************************************************************************************/

                            $pks_proyectos =[];
                            $sanitized_tarifa = sanitized_number($post['tarifa']);

                            foreach($post['horas'] as $key => $value) {
                                    $horasCorreo = $horasCorreo + $value;
                            }
                            foreach ($post['periodo'] as $key => $value) {

                                    $periodo     = TblProyectosPeriodos::find()->where(['PK_PROYECTO_PERIODO'=>$key])->one();
                                    $pk_periodos_bls .= $key.',';

                                    //Cambio para numero de referencia en proyectos
                                    $num_referencia_odc = ($periodo->FK_DOCUMENTO_ODC) ? TblDocumentosProyectos::find()->select(['NUM_REFERENCIA'])->where(['PK_DOCUMENTO' => $periodo->FK_DOCUMENTO_ODC])->one() : null;
                                    $num_referencia_hde = ($periodo->FK_DOCUMENTO_HDE) ? TblDocumentosProyectos::find()->select(['NUM_REFERENCIA'])->where(['PK_DOCUMENTO' => $periodo->FK_DOCUMENTO_HDE])->one() : null;

                                    if(!in_array($periodo->FK_PROYECTO_FASE, $pks_proyectos)){
                                        $pks_proyectos[]=$periodo->FK_PROYECTO_FASE;
                                    }

                                    $sanitized_monto= sanitized_number($post['monto'][$key]);

                                    $documento_odc_proy                       = new TblDocumentos();
                                    $documento_odc_proy->FECHA_DOCUMENTO      = $documento_bolsa->FECHA_DOCUMENTO;
                                    $documento_odc_proy->NUM_DOCUMENTO        = 'BLS_ODC_'.$documento_bolsa->NUM_DOCUMENTO;
                                    $documento_odc_proy->DOCUMENTO_UBICACION  = $documento_bolsa->DOCUMENTO_UBICACION;
                                    $documento_odc_proy->NOMBRE_DOCUMENTO     = $documento_bolsa->NOMBRE_DOCUMENTO;
                                    $documento_odc_proy->TARIFA               = $documento_bolsa->TARIFA;
                                    $documento_odc_proy->FK_PROYECTO_FASE     = $periodo->FK_PROYECTO_FASE;
                                    $documento_odc_proy->FK_TIPO_DOCUMENTO    = 2;
                                    $documento_odc_proy->CONSECUTIVO_TIPO_DOC = 0;
                                    $documento_odc_proy->FK_RAZON_SOCIAL      = $model_bolsa->FK_EMPRESA;
                                    $documento_odc_proy->NUM_REFERENCIA       = ($num_referencia_odc) ? $num_referencia_odc->NUM_REFERENCIA : $num_referencia_odc;
                                    $documento_odc_proy->save(false);

                                    $documento_hde_proy                       = new TblDocumentos();
                                    $documento_hde_proy->FECHA_DOCUMENTO      = $documento_bolsa->FECHA_DOCUMENTO;
                                    $documento_hde_proy->NUM_DOCUMENTO        = 'BLS_HDE_'.$documento_bolsa->NUM_DOCUMENTO;
                                    $documento_hde_proy->DOCUMENTO_UBICACION  = $documento_bolsa->DOCUMENTO_UBICACION;
                                    $documento_hde_proy->NOMBRE_DOCUMENTO     = $documento_bolsa->NOMBRE_DOCUMENTO;
                                    $documento_hde_proy->TARIFA               = $documento_bolsa->TARIFA;
                                    $documento_hde_proy->FK_PROYECTO_FASE     = $periodo->FK_PROYECTO_FASE;
                                    $documento_hde_proy->FK_TIPO_DOCUMENTO    = 3;
                                    $documento_hde_proy->CONSECUTIVO_TIPO_DOC = 0;
                                    $documento_hde_proy->FK_RAZON_SOCIAL      = $model_bolsa->FK_EMPRESA;
                                    $documento_hde_proy->NUM_REFERENCIA       = ($num_referencia_hde) ? $num_referencia_hde->NUM_REFERENCIA : $num_referencia_hde;
                                    $documento_hde_proy->save(false);

                                    $periodo->HORAS_ODC                  = $post['horas'][$key];
                                    $periodo->MONTO_ODC                  = $sanitized_monto;
                                    $periodo->TARIFA_ODC                 = $sanitized_tarifa;
                                    $periodo->FK_DOCUMENTO_ODC           = $documento_odc_proy->PK_DOCUMENTO;

                                    $periodo->HORAS_HDE                  = $post['horas'][$key];
                                    $periodo->MONTO_HDE                  = $sanitized_monto;
                                    $periodo->TARIFA_HDE                 = $sanitized_tarifa;
                                    $periodo->FK_DOCUMENTO_HDE           = $documento_hde_proy->PK_DOCUMENTO;

                                    if($post['fk_periodo_dividir']==$key&&isset($post['dividir_periodo'])&&!empty($post['dividir_periodo'])){
                                        $new_periodo_dividir = new TblProyectosPeriodos();
                                        $new_periodo_dividir->load($periodo);

                                        $new_periodo_dividir->FK_PROYECTO_FASE=$periodo->FK_PROYECTO_FASE;
                                        $new_periodo_dividir->FECHA_FIN=transform_date($post['fecha_fin_periodo2'],'Y-m-d');
                                        $new_periodo_dividir->TARIFA_ODC=$periodo->TARIFA_ODC;
                                        $new_periodo_dividir->FK_EMPLEADO=$periodo->FK_EMPLEADO;
                                        $new_periodo_dividir->FECHA_REGISTRO=date('Y-m-d');

                                        $periodo->FECHA_FIN = transform_date($post['fecha_fin_periodo1'],'Y-m-d');

                                        $new_periodo_dividir->FECHA_INI = transform_date($post['fecha_ini_periodo2'],'Y-m-d');
                                        $new_periodo_dividir->HORAS_ODC = $post['horas_periodo2'];
                                        $new_periodo_dividir->MONTO_ODC = $new_periodo_dividir->HORAS_ODC * $new_periodo_dividir->TARIFA_ODC;
                                        // if(isset($post['fk_cat_tarifa'])){
                                        //     $new_periodo_dividir->FK_CAT_TARIFA    = $post['fk_cat_tarifa'];
                                        // }
                                        $new_periodo_dividir->FK_DOCUMENTO_ODC = null;
                                        $new_periodo_dividir->FK_DOCUMENTO_HDE = null;
                                        $new_periodo_dividir->TARIFA_HDE = null;
                                        $new_periodo_dividir->HORAS_HDE = null;
                                        $new_periodo_dividir->MONTO_HDE = null;
                                        $new_periodo_dividir->save(false);

                                        if(isset($post['horas_restantes'])&&!empty($post['horas_restantes'])){
                                            $new_bit_periodo = new TblBitPeriodos();
                                            $new_bit_periodo->PK_PROYECTO_PERIODO = $new_periodo_dividir->PK_PROYECTO_PERIODO;
                                            $new_bit_periodo->COMENTARIO = 'Se agrega '.$post['horas_restantes'].(($post['horas_restantes']>1)?' horas':' hora').' del periodo anterior';
                                            $new_bit_periodo->FECHA_REGISTRO = date('Y-m-d h:i:s');
                                            $new_bit_periodo->save(false);

                                            $new_bit_periodo = new TblBitPeriodos();
                                            $new_bit_periodo->PK_PROYECTO_PERIODO = $periodo->PK_PROYECTO_PERIODO;
                                            $new_bit_periodo->COMENTARIO = 'Faltante '.$post['horas_restantes'].(($post['horas_restantes']>1)?' horas':' hora').' para completar d&iacute;as del periodo';
                                            $new_bit_periodo->FECHA_REGISTRO = date('Y-m-d h:i:s');
                                            $new_bit_periodo->save(false);
                                        }

                                        /*$seguimiento = new TblAsignacionesSeguimiento();
                                        $seguimiento->FECHA_REGISTRO= date('Y-m-d h:i:s');
                                        $seguimiento->FK_ASIGNACION = $new_periodo_dividir->FK_ASIGNACION;
                                        $seguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                                        $seguimiento->COMENTARIOS = 'Se divide periodo de fechas del '.transform_date($periodo->FECHA_INI,'d/m/Y').' al '.transform_date($new_periodo_dividir->FECHA_FIN,'d/m/Y').' por que la bolsa no cubrio con las horas correspondientes';
                                        $seguimiento->save(false);*/
                                        }

                                    $periodo->FK_BOLSA = $model_bolsa->PK_BOLSA;
                                    $periodo->save(false);

                                    /**
                                     * Cambiar Tarifa
                                     */
                                    $proximos_periodos= TblProyectosPeriodos::find()->where(['PK_PROYECTO_PERIODO'=>$periodo->PK_PROYECTO_PERIODO])->andWhere(['>','FECHA_INI',$periodo->FECHA_INI])->all();
                                    foreach ($proximos_periodos as $key => $value) {
                                        if(!$value->FK_DOCUMENTO_ODC){
                                            $next_periodo= TblProyectosPeriodos::find()->where(['PK_PROYECTO_PERIODO'=>$value->PK_PROYECTO_PERIODO])->one();
                                            $next_periodo->TARIFA_ODC = $sanitized_tarifa;
                                            $new_monto = $next_periodo->TARIFA_ODC * $next_periodo->HORAS_ODC;
                                            $next_periodo->MONTO_ODC=$new_monto;
                                            $next_periodo->save(false);
                                        }else{
                                            break;
                                        }
                                    }
                            }//Fin de foreach

                            //Guardar en bitacora asociacion a la bolsa
                            if($pk_periodos_bls != '') {
                                $bit_bolsa = new TblBitBlsDocs();
                                $bit_bolsa->FK_BOLSA = $model_bolsa->PK_BOLSA;
                                $bit_bolsa->FK_PROYECTO_PERIODOS = $pk_periodos_bls;
                                $bit_bolsa->FECHA_REGISTRO = date('Y-m-d h:i:s');
                                if($bit_bolsa->save(false)){
                                    $fk_bit_bls=$bit_bolsa->PK_BIT_BLS;
                                }
                            }

                            foreach ($pks_proyectos as $key => $value) {
                                $connection = \Yii::$app->db;
                                $consultaProyecto =  $connection->createCommand("select
                                                tp.PK_PROYECTO,
                                                tpf.PK_PROYECTO_FASE,
                                                tp.TARIFA,
                                                tp.FK_TARIFA
                                                from tbl_proyectos tp
                                                inner join tbl_proyectos_fases tpf
                                                on tp.PK_PROYECTO = tpf.FK_PROYECTO
                                                where tpf.PK_PROYECTO_FASE = ".$value)->queryOne();

                                $connection->close();
                                $proyecto= TblProyectos::find()->where(['PK_PROYECTO'=>$consultaProyecto['PK_PROYECTO']])->one();
                                $proyecto->TARIFA=$sanitized_tarifa;
                                if(isset($post['fk_cat_tarifa'])){
                                    $proyecto->FK_TARIFA = $post['fk_cat_tarifa'];
                                }
                                $proyecto->save(false);
                            }
                        }

                        $model_bolsa->save(false);

                    /*Incio envio Correo*/
                    $de = get_config('PERIODOS','CORREO_REMITENTE_FACTURA');
                    $para= explode(',',get_config('PERIODOS','CORREO_DESTINO_FACTURA_IBM'));
                    //$para="irving.rivera@eisei.net.mx";
                    //$para= explode(',', get_config('PERIODOS','CORREO_DESTINO_FACTURA_1'));
                    $asunto = 'IBM-Por Facturar Asociar Bolsa ';
                    $mensaje = '<style>p, ul, li {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}</style>
                                <p>Buen d&iacute;a</p>
                                <p>Se le notifica que se han enviado a facturar '.$horasCorreo.' horas asociadas a la bolsa con el número: '.$post["numero_bolsa"].'</p>
                                <p>ID Petición: '.$fk_bit_bls.'</p>
                                <p>Saludos y Gracias.</p>';

                        $enviado = send_mail($de,$para, $asunto, $mensaje,['..'.$ubicacion_doc_odc]);
                        /*Fin envio Correo*/


                        return $this->redirect(['asignaciones/index_bolsa']);
                    }
                }
                return $this->render('bolsa/asignaciones_bolsa',[
                    'post'=>$post,
                    'razon_social'=>$razon_social,
                    'empresa'=>$empresa,
                    'proyectos'=>$proyectos,
                    'fabrica_desarrollo'=>$fabrica_desarrollo,
                ]);

            }

        }

        public function actionPeriodos_bolsa()
        {
            $post= [];
            $post= Yii::$app->request->post();

            $modelAsignacion = TblAsignaciones::find()->where(['PK_ASIGNACION'=>$post['id_asignacion']]);
            $varConsultaFechas = '';

            $connection = \Yii::$app->db;
            $periodos = $connection->createCommand("
                SELECT
                tp.PK_PERIODO,
                tp.FECHA_INI,
                tp.FECHA_FIN,
                tp.FK_DOCUMENTO_ODC,
                tp.FK_ASIGNACION AS PK_ASIGNACION,
                tp.HORAS,
                tp.HORAS_DEVENGAR
                FROM tbl_periodos tp
                WHERE tp.FK_ASIGNACION = ".$post['id_asignacion']."
                AND (YEAR(tp.FECHA_FIN) <= ".date('Y')."
                OR (YEAR(tp.FECHA_FIN) = ".date('Y')." AND MONTH(tp.FECHA_FIN) <= ".date('m')."))
                ORDER BY tp.FECHA_INI ASC")->queryAll();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $connection->close();

            $res = [
                'periodos'=>$periodos,
                'post'=>$post
            ];
            return $res;

        }

        public function actionDetalle_bolsa($id)
        {
            $modelFile = new SubirArchivo();
            $modelFile->extensions = 'pdf';
            $modelFile->noRequired = true;
            $connection = \Yii::$app->db;

            if(Yii::$app->request->post()){
                $post= Yii::$app->request->post();
                 //dd($post);
                if($post['editar']==1){

                    $model = TblCatBolsas::find()->where(['PK_BOLSA'=>$id])->one();
                    $model->load($post);
                    $model->FECHA_ODC         = transform_date($model->FECHA_ODC,'Y-m-d');
                    $model->FECHA_INI         = transform_date($model->FECHA_INI,'Y-m-d');
                    $model->FECHA_FIN         = transform_date($model->FECHA_FIN,'Y-m-d');
                    $model->TARIFA            = sanitized_number($model->TARIFA);
                    $model->MONTO_BOLSA       = sanitized_number($model->MONTO_BOLSA);
                    $model->HORAS_DISPONIBLES = sanitized_number($post['TblCatBolsas']['HORAS_DISPONIBLES']);
                    $model->MONTO_DISPONIBLE  = sanitized_number($post['TblCatBolsas']['MONTO_DISPONIBLE']);

                    if(isset($post['pk_cat_tarifa_select'])){
                        $model->FK_CAT_TARIFA = $post['pk_cat_tarifa_select'];
                    }


                    $model->save(false);

                    $modelFile->file = UploadedFile::getInstance($modelFile, '[6]file');

                    $documento_bolsa = new TblDocumentos();
                    if (!empty($modelFile->file)) {
                        $fechaHoraHoy                          = date('YmdHis');
                        $rutaGuardado                          = '../uploads/DocumentosPeriodos/';
                        $nombreFisico                          = quitar_acentos(utf8_decode($fechaHoraHoy.'_'.$modelFile->file->basename));
                        $nombreBD                              = quitar_acentos(utf8_decode($modelFile->file->basename));
                        $extension                             = $modelFile->upload($rutaGuardado,$nombreFisico);
                        $rutaDoc                               = '/uploads/DocumentosPeriodos/';
                        $documento_bolsa->NOMBRE_DOCUMENTO    = $nombreBD.'.'.$extension;
                        $documento_bolsa->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;

                        $documento_bolsa->FECHA_DOCUMENTO      = $model->FECHA_ODC;
                        $documento_bolsa->NUM_DOCUMENTO        = $model->NUMERO_BOLSA;
                        $documento_bolsa->NUM_SP               = null;
                        $documento_bolsa->TARIFA               = $model->TARIFA;
                        $documento_bolsa->HORAS                = $model->HORAS;
                        $documento_bolsa->MONTO                = $model->MONTO_BOLSA;
                        $documento_bolsa->FK_RAZON_SOCIAL      = $model->FK_EMPRESA;
                        $documento_bolsa->FK_ASIGNACION        = null;
                        $documento_bolsa->FK_TIPO_DOCUMENTO    = 2;
                        $documento_bolsa->FK_UNIDAD_NEGOCIO    = null;
                        $documento_bolsa->FECHA_REGISTRO       = $model->FECHA_REGISTRO;
                        $documento_bolsa->FK_CLIENTE           = null;
                        $documento_bolsa->CONSECUTIVO_TIPO_DOC = 0;

                        $documento_bolsa->save(false);

                        $model->FK_DOCUMENTO =$documento_bolsa->PK_DOCUMENTO;

                        $model->save(false);
                    }else{
                        $documento_bolsa = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$model->FK_DOCUMENTO])->one();
                    }

                    $sanitized_tarifa = sanitized_number($model->TARIFA);

                    $periodos= (isset($post['periodo']))?$post['periodo']:[];
                    if(isset($post['eliminar_asignacion'])){
                        foreach ($post['eliminar_asignacion'] as $key => $pk_asignacion) {
                            foreach ($periodos as $pk_periodo => $periodo) {
                                if($periodo==$pk_asignacion){
                                    $model_perido = TblPeriodos::find()->where(['PK_PERIODO'=>$pk_periodo])->one();

                                    $documento_odc = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$model_perido->FK_DOCUMENTO_ODC])->one();
                                    $documento_odc->delete();

                                    $documento_hde = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$model_perido->FK_DOCUMENTO_HDE])->one();
                                    $documento_hde->delete();

                                    $model_perido->FK_DOCUMENTO_ODC= null;
                                    $model_perido->FK_DOCUMENTO_HDE= null;
                                    $model_perido->TARIFA_HDE= null;
                                    $model_perido->HORAS_HDE= null;
                                    $model_perido->MONTO_HDE= null;

                                    $model_perido->save(false);

                                    unset($periodos[$pk_periodo]);
                                }
                            }
                        }
                    }

                    /*Eliminacion de facturas*/
                    if (isset($post['eliminar_factura'])) {
                        foreach ($post['eliminar_factura'] as $key => $value) {
                            //Se registra la cancelacion en la bitacora
                            $modelCancel = new TblBitComentariosFacturas();
                            $modelCancel->MOTIVO = $post['eliminar_motivo'][$value];
                            $modelCancel->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            $modelCancel->FECHA_FIN = transform_date($post['eliminar_fechafin'][$value],'Y-m-d');
                            $modelCancel->FK_FACTURA = $value;
                            $modelCancel->save(false);

                            //Actualizacion del estatus de la factura
                            $modelFactura = TblFacturas::find()->where(['PK_FACTURA' => $value])->one();
                            $modelFactura->FK_ESTATUS = 3;
                            $modelFactura->save(false);

                            if($post['tipoServicio'] == 'asignacion')
                            {
                                 //Vonsultar todos los peridos que tengan la misma factura y limpiarlos
                                $modelPerido = TblPeriodos::find()->where(['FK_DOCUMENTO_FACTURA'=> $modelFactura->FK_DOC_FACTURA])->all();
                                if(count($modelPerido) > 0) {
                                    foreach ($modelPerido as $key => $value) {
                                        $value->FK_DOCUMENTO_FACTURA = null;
                                        $value->TARIFA_FACTURA = null;
                                        $value->HORAS_FACTURA = null;
                                        $value->FK_DOCUMENTO_FACTURA_XML = null;
                                        $value->MONTO_FACTURA = null;
                                        $value->save(false);
                                    }
                                }
                            }
                            else
                            {
                                //Vonsultar todos los peridos que tengan la misma factura y limpiarlos
                                $modelProyectosPeriodos = TblProyectosPeriodos::find()->where(['FK_DOCUMENTO_FACTURA' => $modelFactura->FK_DOC_FACTURA])->all();
                                if(count($modelProyectosPeriodos) > 0)
                                {
                                    foreach($modelProyectosPeriodos AS $value)
                                    {
                                        $value->FK_DOCUMENTO_FACTURA = NULL;
                                        $value->TARIFA_FACTURA = NULL;
                                        $value->HORAS_FACTURA = NULL;
                                        $value->FK_DOCUMENTO_FACTURA_XML = NULL;
                                        $value->MONTO_FACTURA = NULL;
                                        $value->save(false);
                                    }
                                }
                            }
                        }
                    }
                    /*Fin Eliminacion de facturas*/

                    foreach ($periodos as $pk_periodo => $periodo) {
                        if(empty($post['horas'][$pk_periodo])){
                            if($post['tipoServicio'] == 'asignacion')
                            {
                                $model_perido = TblPeriodos::find()->where(['PK_PERIODO'=>$pk_periodo])->one();

                                $documento_odc = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$model_perido->FK_DOCUMENTO_ODC])->one();
                                $documento_odc->delete();

                                $documento_hde = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$model_perido->FK_DOCUMENTO_HDE])->one();
                                $documento_hde->delete();

                                $model_perido->FK_DOCUMENTO_ODC  = null;
                                $model_perido->FK_DOCUMENTO_HDE  = null;
                                $model_perido->TARIFA_HDE        = null;
                                $model_perido->HORAS_HDE         = null;
                                $model_perido->MONTO_HDE         = null;

                                $model_perido->save(false);

                                // AGREGADO PARA BORRAR EL PERIODO DE EL PAQUETE DE LA BOLSA
                                $bitBlsDocs = TblBitBlsDocs::find()->where(['FK_BOLSA' => $model->PK_BOLSA])->asArray()->all();
                                foreach($bitBlsDocs as $paquete)
                                {
                                    $paquete['FK_PERIODOS'] = (substr($paquete['FK_PERIODOS'], -1) == ",") ? substr($paquete['FK_PERIODOS'], 0, -1): $paquete['FK_PERIODOS'];
                                    $fks_bolsas = explode(",", $paquete["FK_PERIODOS"]);
                                    if(($key = array_search($pk_periodo, $fks_bolsas)) !== false)
                                    {
                                        unset($fks_bolsas[$key]);
                                        $modelBitBls = TblBitBlsDocs::find()->where(['PK_BIT_BLS' => $paquete['PK_BIT_BLS']])->limit(1)->one();
                                        $modelBitBls->FK_PERIODOS = implode(',', $fks_bolsas);
                                        $modelBitBls->save(false);
                                    }
                                }
                            }
                            else
                            {
                                $model_proyecto_periodo = TblProyectosPeriodos::find()->where(['PK_PROYECTO_PERIODO' => $pk_periodo])->one();

                                $documento_odc = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$model_proyecto_periodo->FK_DOCUMENTO_ODC])->one();
                                if($documento_odc)
                                {
                                    $documento_odc->delete();
                                }

                                // DOCUMENTO HDE DE PROYECTOS SE GUARDA EN DOCUMENTOS PROYECTOS
                                $documento_hde = TblDocumentos::find()->where(['PK_DOCUMENTO' => $model_proyecto_periodo->FK_DOCUMENTO_HDE])->one();
                                if($documento_hde)
                                {
                                    $documento_hde->delete();
                                }


                                $model_proyecto_periodo->FK_DOCUMENTO_ODC = NULL;
                                $model_proyecto_periodo->FK_DOCUMENTO_HDE = NULL;
                                $model_proyecto_periodo->TARIFA_HDE       = NULL;
                                $model_proyecto_periodo->HORAS_HDE        = NULL;
                                $model_proyecto_periodo->MONTO_HDE        = NULL;
                                $model_proyecto_periodo->save(false);

                                // AGREGADO PARA BORRAR EL PERIODO DE EL PAQUETE DE LA BOLSA
                                $bitBlsDocs = TblBitBlsDocs::find()->where(['FK_BOLSA' => $model->PK_BOLSA])->asArray()->all();
                                foreach($bitBlsDocs as $paquete)
                                {
                                    $paquete['FK_PROYECTO_PERIODOS'] = (substr($paquete['FK_PROYECTO_PERIODOS'], -1) == ",") ? substr($paquete['FK_PROYECTO_PERIODOS'], 0, -1): $paquete['FK_PROYECTO_PERIODOS'];
                                    $fks_bolsas = explode(",", $paquete["FK_PROYECTO_PERIODOS"]);
                                    if(($key = array_search($pk_periodo, $fks_bolsas)) !== false)
                                    {
                                        unset($fks_bolsas[$key]);
                                        $modelBitBls = TblBitBlsDocs::find()->where(['PK_BIT_BLS' => $paquete['PK_BIT_BLS']])->limit(1)->one();
                                        $modelBitBls->FK_PROYECTO_PERIODOS = implode(',', $fks_bolsas);
                                        $modelBitBls->save(false);
                                    }
                                }
                            }

                        } else {

                            if($post['tipoServicio'] == 'asignacion')
                            {
                                $periodo  = TblPeriodos::find()->where(['PK_PERIODO'=>$pk_periodo])->one();

                                $documento_odc = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$periodo->FK_DOCUMENTO_ODC])->one();

                                if($documento_bolsa){
                                    $documento_odc->DOCUMENTO_UBICACION  = $documento_bolsa->DOCUMENTO_UBICACION;
                                    $documento_odc->NOMBRE_DOCUMENTO     = $documento_bolsa->NOMBRE_DOCUMENTO;

                                }

                                $documento_odc->FECHA_DOCUMENTO      = $model->FECHA_ODC;
                                $documento_odc->NUM_DOCUMENTO        = 'BLS_ODC_'.$model->NUMERO_BOLSA;
                                $documento_odc->TARIFA               = $model->TARIFA;
                                $documento_odc->save(false);

                                $documento_hde = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$periodo->FK_DOCUMENTO_HDE])->one();
                                if($documento_bolsa){
                                    $documento_hde->DOCUMENTO_UBICACION  = $documento_bolsa->DOCUMENTO_UBICACION;
                                    $documento_hde->NOMBRE_DOCUMENTO     = $documento_bolsa->NOMBRE_DOCUMENTO;

                                }

                                $documento_hde->FECHA_DOCUMENTO      = $model->FECHA_ODC;
                                $documento_hde->NUM_DOCUMENTO        = 'BLS_HDE_'.$model->NUMERO_BOLSA;
                                $documento_hde->TARIFA               = $model->TARIFA;
                                $documento_hde->save(false);

                                $sanitized_monto= sanitized_number($post['monto'][$pk_periodo]);

                                $periodo->HORAS            = $post['horas'][$pk_periodo];
                                $periodo->MONTO            = $sanitized_monto;
                                $periodo->TARIFA           = $sanitized_tarifa;

                                $periodo->HORAS_HDE        = $post['horas'][$pk_periodo];
                                $periodo->MONTO_HDE        = $sanitized_monto;
                                $periodo->TARIFA_HDE       = $sanitized_tarifa;
                                $periodo->save(false);
                            }
                            else
                            {
                                $model_proyecto_periodo  = TblProyectosPeriodos::find()->where(['PK_PROYECTO_PERIODO'=>$pk_periodo])->one();
                                $documento_odc = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$model_proyecto_periodo->FK_DOCUMENTO_ODC])->one();
                                if($documento_bolsa){
                                    $documento_odc->DOCUMENTO_UBICACION  = $documento_bolsa->DOCUMENTO_UBICACION;
                                    $documento_odc->NOMBRE_DOCUMENTO     = $documento_bolsa->NOMBRE_DOCUMENTO;
                                }

                                $documento_odc->FECHA_DOCUMENTO      = $model->FECHA_ODC;
                                $documento_odc->NUM_DOCUMENTO        = 'BLS_ODC_'.$model->NUMERO_BOLSA;
                                $documento_odc->TARIFA               = $model->TARIFA;
                                $documento_odc->save(false);

                                $documento_hde = TblDocumentos::find()->where(['PK_DOCUMENTO' => $model_proyecto_periodo->FK_DOCUMENTO_HDE])->one();
                                if($documento_bolsa){
                                    $documento_hde->DOCUMENTO_UBICACION  = $documento_bolsa->DOCUMENTO_UBICACION;
                                    $documento_hde->NOMBRE_DOCUMENTO     = $documento_bolsa->NOMBRE_DOCUMENTO;

                                }

                                $documento_hde->FECHA_DOCUMENTO      = $model->FECHA_ODC;
                                $documento_hde->NUM_DOCUMENTO        = 'BLS_HDE_'.$model->NUMERO_BOLSA;
                                $documento_hde->TARIFA               = $model->TARIFA;
                                $documento_hde->save(false);

                                $sanitized_monto= sanitized_number($post['monto'][$pk_periodo]);

                                $model_proyecto_periodo->HORAS_ODC        = $post['horas'][$pk_periodo];
                                $model_proyecto_periodo->MONTO_ODC            = $sanitized_monto;
                                $model_proyecto_periodo->TARIFA_ODC           = $sanitized_tarifa;

                                $model_proyecto_periodo->HORAS_HDE        = $post['horas'][$pk_periodo];
                                $model_proyecto_periodo->MONTO_HDE        = $sanitized_monto;
                                $model_proyecto_periodo->TARIFA_HDE       = $sanitized_tarifa;
                                $model_proyecto_periodo->save(false);
                            }
                        }
                    }

                    return $this->redirect(['asignaciones/index_bolsa']);
                }
                // dd($post);
            }else{

                $model = TblCatBolsas::find()->select([
                                'PK_BOLSA',
                                'NUMERO_BOLSA',
                                'DATE_FORMAT(FECHA_ODC, \'%d/%m/%Y\') as FECHA_ODC',
                                'DATE_FORMAT(FECHA_INI, \'%d/%m/%Y\') as FECHA_INI',
                                'DATE_FORMAT(FECHA_FIN, \'%d/%m/%Y\') as FECHA_FIN',
                                'CONCAT("$ ", FORMAT(TARIFA, 3)) as TARIFA',
                                'TARIFA as TARIFA2',
                                'HORAS',
                                'CONCAT("$ ", FORMAT(MONTO_BOLSA, 2)) as MONTO_BOLSA',
                                'HORAS_DISPONIBLES',
                                'CONCAT("$ ", FORMAT(MONTO_DISPONIBLE, 2)) as MONTO_DISPONIBLE',
                                'MONTO_DISPONIBLE as MONTO_DISPONIBLE2',
                                'FK_EMPRESA',
                                'FK_DOCUMENTO',
                                'FK_CLIENTE',
                                'FK_CAT_TARIFA',
                            ])->where(['PK_BOLSA'=>$id])->one();
                $documento_bolsa= TblDocumentos::find()->where(['PK_DOCUMENTO'=>$model->FK_DOCUMENTO])->asArray()->one();
                $datosRezonesSociales= TblCatRazonSocial::find()->select(['PK_RAZON_SOCIAL','DESC_RAZON_SOCIAL'])->asArray()->all();


/*/////////////////////////////////////////////////////////////////////////////////////////////
//                        CONSULTAS PARA MOSTRAR ASIGNACIONES - PERIODOS                     //
/////////////////////////////////////////////////////////////////////////////////////////////*/

                $fks_asignaciones = TblDocumentos::find()->select(['FK_ASIGNACION'])->where(['LIKE','NUM_DOCUMENTO','BLS_ODC_'.$model->NUMERO_BOLSA])->andWhere(['!=','FK_ASIGNACION','null'])->distinct()->asArray()->column();
                $pks_documentos = TblDocumentos::find()->select(['PK_DOCUMENTO','FK_ASIGNACION'])->where(['LIKE','NUM_DOCUMENTO','BLS_ODC_'.$model->NUMERO_BOLSA])->asArray()->all();
                // $pks_documentos = TblDocumentos::find()->select(['FK_ASIGNACION'])->where(['LIKE','NUM_DOCUMENTO','BLS_ODC_'.$model->NUMERO_BOLSA])->distinct()->asArray()->all();
                //var_dump($model->NUMERO_BOLSA);
                //dd($fks_asignaciones);
                $datos_asignaciones= [];

                $asignaciones= (new \yii\db\Query())
                        ->select([
                                    'distinct (a.PK_ASIGNACION)',
                                    'e.NOMBRE_EMP',
                                    'e.APELLIDO_PAT_EMP',
                                    'e.APELLIDO_MAT_EMP',
                                    'DATE_FORMAT(a.FECHA_INI, \'%d/%m/%Y\') as FECHA_INI',
                                    'DATE_FORMAT(a.FECHA_FIN, \'%d/%m/%Y\') as FECHA_FIN',
                                    'a.TARIFA',
                                    'cp.DESC_PUESTO',
                                    'a.MONTO',
                                    'ea.DESC_ESTATUS_ASIGNACION',
                                    'a.FK_ESTATUS_ASIGNACION',
                                    'a.NOMBRE',
                                    'a.fk_empleado',
                                    'cl.alias_cliente',
                                    'cl.NOMBRE_CLIENTE',

                                    '(
                                        SELECT td2.NUM_DOCUMENTO
                                            FROM tbl_documentos td2
                                            INNER join tbl_periodos p2
                                            on p2.FK_ASIGNACION = td2.FK_ASIGNACION
                                            and YEAR(
                                                    p2.fecha_ini
                                                ) = '.date('Y').'
                                            AND MONTH(
                                                    p2.fecha_ini
                                            ) = '.date('m').'
                                            AND td2.FK_TIPO_DOCUMENTO=2
                                            AND td2.PK_DOCUMENTO= p2.FK_DOCUMENTO_ODC
                                            where p2.fk_asignacion= a.pk_asignacion
                                            limit 1
                                    ) as NUM_DOCUMENTO',
                                    'cl.HORAS_ASIGNACION'
                                    ])
                        ->from('tbl_asignaciones as a')
                        ->join('inner JOIN','tbl_empleados as e',
                            'a.fk_empleado = e.PK_empleado')
                        ->join('LEFT JOIN','tbl_perfil_empleados',
                            'tbl_perfil_empleados.FK_empleado = e.PK_empleado')
                        ->join('LEFT JOIN','tbl_clientes as cl',
                            'a.FK_CLIENTE = cl.PK_cliente')
                        ->join('LEFT JOIN','tbl_cat_puestos as cp',
                            'tbl_perfil_empleados.fk_puesto = cp.PK_puesto')
                        ->join('LEFT JOIN','tbl_cat_unidades_negocio',
                            'tbl_perfil_empleados.FK_RAZON_SOCIAL = tbl_cat_unidades_negocio.PK_UNIDAD_NEGOCIO')
                        ->join('LEFT JOIN','tbl_periodos p',
                            'p.FK_Asignacion = a.PK_asignacion')
                        ->join('LEFT JOIN','tbl_documentos td',
                            'td.FK_ASIGNACION = p.FK_ASIGNACION ')
                        ->join('LEFT JOIN','tbl_cat_estatus_asignaciones ea',
                           'ea.pk_estatus_asignacion = a.FK_ESTATUS_ASIGNACION')
                        ->join('LEFT JOIN','tbl_asignaciones_reemplazos as ar',
                            'ar.FK_ASIGNACION = a.PK_ASIGNACION')
                        ->join('LEFT JOIN','tbl_empleados as e_ar',
                            'ar.FK_EMPLEADO = e_ar.PK_EMPLEADO')
                        ->where(['IN','a.PK_ASIGNACION',$fks_asignaciones])
                        ->groupBy('a.PK_ASIGNACION')
                        ->orderBy('a.PK_ASIGNACION DESC')
                        ->all();

                $periodos= array();
                foreach ($fks_asignaciones as $key => $value) {
                    // $periodos[$value['FK_ASIGNACION']][]= TblPeriodos::find()->where(['FK_DOCUMENTO_ODC'=>$value['PK_DOCUMENTO']])->andWhere(['FK_ASIGNACION'=>$value['FK_ASIGNACION']])->asArray()->one();
                    //$periodos[$value][]= TblPeriodos::find()->andWhere(['FK_ASIGNACION'=>$value])->orderBy('FECHA_INI')->asArray()->all();

                    $periodos[$value][] = (new \yii\db\Query())
                        ->select([
                            'p.*',
                            'p.MONTO_FACTURA',
                            'd.PK_DOCUMENTO',
                            'd.NUM_DOCUMENTO',
                            'f.PK_FACTURA',
                            'f.FECHA_ENTREGA_CLIENTE',
                            'f.FK_DOC_FACTURA',
                            'f.FECHA_INGRESO_BANCO',
                            'f.CONTACTO_ENTREGA',
                            'f.FK_ESTATUS',
                            'e.NOMBRE_EMP',
                            'e.APELLIDO_PAT_EMP',
                            'e.APELLIDO_MAT_EMP',
                            //'ar.FK_EMPLEADO as FK_EMPLEADO_PERIODO',
                            ])
                        ->from('tbl_periodos as p')
                        ->join('left join','tbl_documentos d',
                            'p.FK_DOCUMENTO_FACTURA = d.PK_DOCUMENTO')
                        ->join('left join','tbl_facturas f',
                            'f.FK_PERIODO = p.PK_PERIODO and f.FK_ESTATUS <> 3')
                        ->join('left join','tbl_cat_contactos c',
                            'c.PK_CONTACTO = f.CONTACTO_ENTREGA')
                        ->join('left join','tbl_empleados e',
                            'e.PK_EMPLEADO = p.FK_EMPLEADO')
                        //->join('left join','tbl_asignaciones_reemplazos ar',
                        //    'p.FK_ASIGNACION = ar.FK_ASIGNACION and ar.FECHA_INICIO <= p.FECHA_INI and ar.FECHA_FIN >= p.FECHA_FIN')
                        ->andWhere(['p.FK_ASIGNACION'=>$value])
                        ->orderBy('p.FECHA_INI')
                        ->all();
                }

/*/////////////////////////////////////////////////////////////////////////////////////////////
//                        CONSULTAS PARA MOSTRAR PROYECTOS - PERIODOS                        //
/////////////////////////////////////////////////////////////////////////////////////////////*/

                $fks_proyectos =  $connection->createCommand("select DISTINCT tp.PK_PROYECTO
                                    from tbl_proyectos tp
                                    left join tbl_proyectos_fases tpf
                                    on tp.PK_PROYECTO = tpf.FK_PROYECTO
                                    left join tbl_proyectos_periodos tpp
                                    on tpf.PK_PROYECTO_FASE = tpp.FK_PROYECTO_FASE
                                    where tpp.FK_BOLSA = ".$model->PK_BOLSA)->queryAll();
                /*$pks_documentos_proyectos =  $connection->createCommand("select
                                    td.PK_DOCUMENTO,
                                    tp.PK_PROYECTO
                                    from tbl_documentos td
                                    inner join tbl_proyectos_fases tpf
                                    on td.FK_PROYECTO_FASE = tpf.PK_PROYECTO_FASE
                                    inner join tbl_proyectos tp
                                    on tpf.FK_PROYECTO = tp.PK_PROYECTO
                                    where td.NUM_DOCUMENTO like concat('%BLS_ODC_4d243dadf2%')")->queryAll;*/
                $pks_documentos_proyectos =  $connection->createCommand("select
                                    td.PK_DOCUMENTO,
                                    td.NUM_DOCUMENTO,
                                    tp.PK_PROYECTO,
                                    tpp.FK_BOLSA
                                    from tbl_proyectos tp
                                    inner join tbl_proyectos_fases tpf
                                    on tp.PK_PROYECTO = tpf.FK_PROYECTO
                                    inner join tbl_proyectos_periodos tpp
                                    on tpp.FK_PROYECTO_FASE = tpf.PK_PROYECTO_FASE
                                    inner join tbl_documentos td
                                    on td.PK_DOCUMENTO = tpp.FK_DOCUMENTO_ODC
                                    where tpp.FK_BOLSA = ".$model->PK_BOLSA)->queryAll();

                $periodosProyectos = array();
                $contPeriodos = 0;
                foreach ($fks_proyectos as $key => $value) {
                $sqlPeriodos =  $connection->createCommand("
                            SELECT
                                tp.PK_PROYECTO,
                                tp.DESC_PROYECTO,
                                tp.NOMBRE_CORTO_PROYECTO,
                                tpf.PK_PROYECTO_FASE,
                                tcf.DESC_FASE,
                                tpp.PK_PROYECTO_PERIODO,
                                tpp.FK_BOLSA,
                                tpp.*,
                                td.PK_DOCUMENTO,
                                tdo.NUM_DOCUMENTO as NUM_DOCUMENTO_ODC,
                                tdh.NUM_DOCUMENTO as NUM_DOCUMENTO_HDE,
                                td.NUM_DOCUMENTO as NUM_DOCUMENTO_FAC,
                                tf.PK_FACTURA,
                                tf.FECHA_ENTREGA_CLIENTE,
                                tf.FK_DOC_FACTURA,
                                tf.FECHA_INGRESO_BANCO,
                                tf.CONTACTO_ENTREGA,
                                tf.FK_ESTATUS
                          FROM tbl_proyectos_periodos tpp
                                INNER JOIN tbl_proyectos_fases tpf ON tpp.FK_PROYECTO_FASE = tpf.PK_PROYECTO_FASE
                                INNER JOIN tbl_proyectos tp ON tp.PK_PROYECTO = tpf.FK_PROYECTO
                                LEFT JOIN tbl_documentos_proyectos td ON tpp.FK_DOCUMENTO_FACTURA = td.PK_DOCUMENTO
                                INNER JOIN tbl_documentos tdo ON tpp.FK_DOCUMENTO_ODC = tdo.PK_DOCUMENTO
                                INNER JOIN tbl_documentos tdh ON tpp.FK_DOCUMENTO_HDE = tdh.PK_DOCUMENTO
                                LEFT JOIN tbl_facturas tf ON tf.FK_PROYECTO_PERIODO = tpp.PK_PROYECTO_PERIODO and tf.FK_ESTATUS <> 3
                                INNER JOIN tbl_cat_fase tcf ON tcf.PK_CAT_FASE = tpf.FK_CAT_FASE
                          WHERE tp.PK_PROYECTO = '".$value['PK_PROYECTO']."'

                          ORDER BY tpp.FECHA_INI
                        ")->queryAll();

                $periodosProyectos[$contPeriodos] = $sqlPeriodos;
                $contPeriodos = $contPeriodos+1;
                }

                $tipoServicio = (count($asignaciones)>0) ? 'asignacion' : 'proyecto';

                /*foreach ($periodosProyectos as $doc => $val_doc) {
                }
                dd($periodosProyectos);*/
                return $this->render('bolsa/view_bolsa', [
                    'model'                =>$model,
                    'datosRezonesSociales' =>$datosRezonesSociales,
                    'asignaciones'         =>$asignaciones,
                    'periodos'             =>$periodos,
                    'modelFile'            =>$modelFile,
                    'documento_bolsa'      =>$documento_bolsa,
                    'pks_documentos'       =>$pks_documentos,
                    'fks_proyectos'        =>$fks_proyectos,
                    'periodosProyectos'    =>$periodosProyectos,
                    'pks_documentos_proyectos' =>$pks_documentos_proyectos,
                    'tipoServicio' => $tipoServicio
                ]);
            }

        }
        public function actionComentario(){
            if (Yii::$app->request->isAjax){

            $data= Yii::$app->request->post();
            $asignacion= TblAsignaciones::find()->where(['PK_ASIGNACION'=>$data['idAsignacion']])->one();
            $id= $data['idAsignacion'];
            $Nombre = $data['Nombre'];
            $comentario = $data['comentario'];
            $modelAsignacion= new TblBitComentariosAsignaciones;
            $modelAsignacion['FK_ASIGNACION']= $id;
            $modelAsignacion['FK_ESTATUS_ASIGNACION']=6;
            $modelAsignacion['MOTIVO']='ASIGNACION_APROBADA';
            $modelAsignacion['COMENTARIOS']=$comentario;
            $modelAsignacion['FECHA_FIN']=$asignacion->FECHA_FIN;
            $modelAsignacion->save(false);

           $connection = \Yii::$app->db;
            $update=$connection->createCommand("UPDATE tbl_perfil_empleados SET FK_ESTATUS_RECURSO=1 WHERE FK_EMPLEADO = '".$data['idRecursos']."'")->execute();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $connection->close();
            $this->envio_correo_rechazo($data);

          return [
            'data' => $data,
           ];
          }
        }
        public function actionAprobando(){
            if(Yii::$app->request->isAjax){
            $data= Yii::$app->request->post();
            $asignacion= TblAsignaciones::find()->where(['PK_ASIGNACION'=>$data['idAsignacion']])->one();
            $date1        = $asignacion->FECHA_INI;
            $date2        = date('Y-m-d');
            $fecha_inicio = strtotime($date1);
            $actual       = strtotime($date2);

            if($fecha_inicio<$actual){
               $asignacion->FK_ESTATUS_ASIGNACION=1;
            }
            if ($fecha_inicio >= $actual){
               $asignacion->FK_ESTATUS_ASIGNACION=2;
            }
              $asignacion->save(false);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
             $this->envio_correo_alta($data);
             return[
            'data' => $data,
             ];
            }
        }
        public function envio_correo_alta($data){

            $Nombre=$data['Nombre'];
            $correoAlta = (new \yii\db\Query())
            ->select([
            'e.EMAIL_INTERNO',
            'CONCAT(e.NOMBRE_EMP," ",e.APELLIDO_PAT_EMP," ",e.APELLIDO_MAT_EMP) AS NOMBRE'
          ])
          ->from('tbl_empleados e')
          ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
          ->where(['p.FK_AREA'=>'5'])
          ->andwhere(['or',['p.FK_ESTATUS_RECURSO'=>'3'],['p.FK_ESTATUS_RECURSO'=>'5']])
          ->andwhere(['p.FK_EMPLEADO' => $modelAsignaciones->FK_RESPONSABLE_OP])
          ->one();

          $Administrador = $correoAlta['NOMBRE'];
          // $DestinatarioAlta = $correoAlta['EMAIL_INTERNO'];

          $nombreAsignacion = $modelAsignaciones->NOMBRE;
          $nameEmisor     = 'EISEI Innovation';
          $emailEmisor    = get_config('Asignaciones','CORREO_REMITENTE_ASIGNABLE');
          $emailDestino   = 'fernando_chavez@eisei.net.mx'; //cambiar por $destino en QA
          $asunto         = 'Notificación de nueva asignación';
          $mensaje        = "<style>p {font-family: Calibri; font-size: 12pt;}</style>
          <p>Buen día, $Administrador <br><br>
          Se registraron nuevas asignaciones <br><br>
          Nombre de asignación: <b>$Nombre</b> <br><br>
          <a href='$url/web/asignaciones/index' style='color: #337ab7;'>Ver consulta general de asignaciones</a>
          <br><br>
          Saludos...</p><br><br>
          <img src='$url/web/iconos/correos/firmaErt.jpg'>";

          $respuestaEmail = send_mail($emailEmisor, $emailDestino, $asunto, $mensaje, []);
        }
        public function envio_correo_rechazo($data){
            $Nombre=$data['Nombre'];
            $comentario=$data['comentario'];
            //Obtener datos de usuario
            $nombreUsuario = $_SESSION['usuario']['NOMBRE_COMPLETO'];
            $url = server().get_upload_url();
            //Obtener correos de Destino
            $arrayCorreos = [];
            $query = new Query;
            $query->select('emp.EMAIL_INTERNO, emp.NOMBRE_EMP, emp.APELLIDO_PAT_EMP, emp.APELLIDO_MAT_EMP')
                ->from('tbl_perfil_empleados as perfil')
                ->join('LEFT JOIN','tbl_empleados as emp',
                    'perfil.FK_EMPLEADO = emp.PK_EMPLEADO')
                ->join('LEFT JOIN','tbl_usuarios as u',
                        'u.FK_EMPLEADO = emp.PK_EMPLEADO')
                ->join('LEFT JOIN','tbl_usuarios_grupo as ug',
                    'ug.PK_USUARIO_GRUPO = u.PK_USUARIO')
                ->join('LEFT JOIN','tbl_grupos as gr',
                        'gr.FK_ROL = ug.FK_GRUPO')
                ->Where(['NOT IN','perfil.FK_ESTATUS_RECURSO',array(4,6)])
                ->distinct();
            $command = $query->createCommand();
            $rows = $command->queryAll();
            foreach($rows as $array){
                if($array['EMAIL_INTERNO']){
                    $arrayCorreos[] = $array['EMAIL_INTERNO'];
                }
             }
            // Variables de envio de correo
                $de= get_config('Asignaciones','CORREO_REMITENTE_ASIGNABLE');
                $para=$arrayCorreos;
                $asunto= 'Notificación de Rechazo de Asignación';
                $mensaje="<style>p {font-family: Calibri; font-size: 11pt;}</style>
                <p>Buen día,<b>$nombreUsuario<b/>.<br><br>
                Se Rechazaron las siguientes asignaciones:
                <br/>
                <br/>
                "
                ."<p>$Nombre</p>  Motivo:<b>$comentario<b>"
                ."<p>Estas asignaciones contaron con estatus Detenida automáticamente en el sistema y se pueden visualizar en la bandeja de Asignaciones Detenidas.</p>"
                . "<a href='$url/web/asignaciones/index#detenidas';' style='color: #337ab7;'>Ver Asignaciones Detenidas</a><br/>"
                ."Saludos...</p>
                <br/>
             "."<img src='$url/web/iconos/correos/firmaErt.jpg'>";

                //Envio de correo
                $enviado = send_mail($de,$para, $asunto, $mensaje,[]);

        }
        // public function actionAprobar()
        // {
        //     if (Yii::$app->request->isAjax){
        //     $data= [];
        //     $data= Yii::$app->request->post();

        //     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        //   $asignacion= TblAsignaciones::find()->where(['PK_ASIGNACION'=>$data['idAsignacion']])->one();
        //     $date1        = $asignacion->FECHA_INI;
        //     $date1_fin    = $asignacion->FECHA_FIN;
        //     $date2        = date('Y-m-d');
        //     $fecha_inicio = strtotime($date1);
        //     $fecha_fin    = strtotime($date1_fin);
        //     $actual       = strtotime($date2);

        //     if($fecha_inicio<$actual){
        //         $asignacion->FK_ESTATUS_ASIGNACION=1;
        //     }else
        //     if ($fecha_inicio >= $actual){
        //         $asignacion->FK_ESTATUS_ASIGNACION=2;
        //     }else
        //     if($fecha_fin<$actual){
        //         $asignacion->FK_ESTATUS_ASIGNACION=3;
        //     }

        //     $perfil= tblperfilempleados::find()->where(['FK_EMPLEADO'=>$asignacion->FK_EMPLEADO])->one();
        //     $empleado= tblempleados::find()->where(['PK_EMPLEADO'=>$asignacion->FK_EMPLEADO])->one();

        //     if($asignacion->FK_ESTATUS_ASIGNACION == 2){
        //         $perfil->FK_ESTATUS_RECURSO  = 2;
        //         $perfil->FK_AREA  = 9;
        //         $perfil->FK_UNIDAD_NEGOCIO   = $asignacion->FK_UNIDAD_NEGOCIO;
        //         $perfil->FK_UBICACION_FISICA = $asignacion->FK_UBICACION;
        //         $perfil->save(false);
        //         user_log_bitacora_estatus_empleado($perfil->FK_EMPLEADO,$perfil->FK_ESTATUS_RECURSO);
        //         user_log_bitacora_unidad_negocio($perfil->FK_EMPLEADO,$perfil->FK_UNIDAD_NEGOCIO,$asignacion->PK_ASIGNACION);
        //         user_log_bitacora_ubicacion_fisica($perfil->FK_EMPLEADO,$perfil->PK_PERFIL,$asignacion->FK_UBICACION,'CLIENTE');
        //     }

        //     $result= $asignacion->save(false);

        //      $this->envio_correo_alta($data);
        //     // $usuario = TblUsuarios::find()->where(['PK_USUARIO'=>$asignacion->FK_USUARIO])->asArray()->one();
        //     // $nombreAsignacion = $asignacion->NOMBRE;
        //     // $nombreEmpleado = $empleado->NOMBRE_EMP.' '.$empleado->APELLIDO_PAT_EMP.' '.$empleado->APELLIDO_MAT_EMP;
        //     // $de= user_info()['CORREO'];
        //     // $para= $usuario['CORREO'];
        //     // $asunto= 'Asignación: '.$asignacion->NOMBRE;
        //     // $nombre_usuario= $usuario['NOMBRE_COMPLETO'];

        //     // $mensaje="<style>p, ul, li {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}</style>
        //     //     <p>Buen d&iacute;a <b>$nombre_usuario</b>, se le notifica que la asignaci&oacute;n pendiente de aprobar <b>".$nombreAsignacion."</b> que fue registrada el <b>".transform_date($asignacion->FECHA_REGISTRO,'d/m/Y')."</b> para el recurso <b>".$nombreEmpleado."</b> fue <b>APROBADA</b></p>
        //     //     <p>Saludos.</p>
        //     //     ";

        //     // $enviado = send_mail($de,$para, $asunto, $mensaje);
        //     // $res = [
        //     //     'data'=>$data,
        //     //     'result'=>$result,
        //     //     'usuario'=>$usuario
        //     // ];

        //     return [
        //         'data'=> $data
        //     ];
        //     }
        // }
        public function actionCancelar_asignacion()
        {
            $post= [];
            $post= Yii::$app->request->post();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $estatus = 8;

            $asignacion= TblAsignaciones::find()->where(['PK_ASIGNACION'=>$post['pk_asignacion']])->one();
            $asignacion->FK_ESTATUS_ASIGNACION= $estatus;
            $empleado= tblempleados::find()->where(['PK_EMPLEADO'=>$asignacion->FK_EMPLEADO])->one();

            $result = $asignacion->save(false);

            $usuario = TblUsuarios::find()->where(['PK_USUARIO'=>$asignacion->FK_USUARIO])->asArray()->one();
            $de= user_info()['CORREO'];
            $para= $usuario['CORREO'];
            $asunto= 'Asignación: '.$asignacion->NOMBRE;
            $nombre_usuario= $usuario['NOMBRE_COMPLETO'];
            $nombreAsignacion = $asignacion->NOMBRE;
            $nombreEmpleado = $empleado->NOMBRE_EMP.' '.$empleado->APELLIDO_PAT_EMP.' '.$empleado->APELLIDO_MAT_EMP;

            $mensaje="<style>p, ul, li {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}</style>
                <p>Buen d&iacute;a <b>$nombre_usuario</b>, se le notifica que la asignaci&oacute;n pendiente de aprobar <b>$nombreAsignacion</b> que fue registrada el <b>".transform_date($asignacion->FECHA_REGISTRO,'d/m/Y')."</b> para el recurso <b>".$nombreEmpleado."</b> fue <b>RECHAZADA</b></p>
                <p>Saludos.</p>
                ";

            $enviado = send_mail($de,$para, $asunto, $mensaje);

            $res = [
                'post'=>$post,
                'result'=>$result,
                'usuario'=>$usuario,
                'enviado'=>$enviado,
            ];

            return $res;

        }
        public function actionReporte_grafica()
        {


          $dataProvider = (new \yii\db\Query())
          ->select ([
            'cte.PK_CLIENTE',
            'cte.NOMBRE_CLIENTE',
            'p.FECHA_INI',
            '(
              SELECT
              SUM(p.MONTO_FACTURA)
              FROM tbl_periodos p
              LEFT JOIN tbl_asignaciones as a ON p.FK_ASIGNACION = a.PK_ASIGNACION
              LEFT JOIN tbl_facturas as fact ON p.PK_PERIODO = fact.FK_PERIODO
              LEFT JOIN tbl_clientes as cteP ON cteP.PK_CLIENTE = a.FK_CLIENTE
              WHERE fact.FECHA_INGRESO_BANCO IS NOT NULL
              AND cteP.NOMBRE_CLIENTE = cte.NOMBRE_CLIENTE
              ) AS PAGADO',
              '(
                SELECT
                SUM(p.MONTO_FACTURA)
                FROM tbl_periodos p
                LEFT JOIN tbl_asignaciones as a ON p.FK_ASIGNACION = a.PK_ASIGNACION
                LEFT JOIN tbl_facturas as fact ON p.PK_PERIODO = fact.FK_PERIODO
                LEFT JOIN tbl_clientes as cteP ON cteP.PK_CLIENTE = a.FK_CLIENTE
                WHERE fact.FECHA_INGRESO_BANCO IS NULL
                AND cteP.NOMBRE_CLIENTE = cte.NOMBRE_CLIENTE
                ) AS NOPAGADO',
                'SUM(p.MONTO_FACTURA)'
              ])
              ->from('tbl_asignaciones as a')
              ->join('left join', 'tbl_clientes as cte',
              'cte.PK_CLIENTE = a.FK_CLIENTE')
              ->join('left join', 'tbl_periodos as p',
              'p.FK_ASIGNACION = a.PK_ASIGNACION AND a.FK_ESTATUS_ASIGNACION <> 5
              OR
              (p.FK_ASIGNACION = a.PK_ASIGNACION AND a.FK_ESTATUS_ASIGNACION = 5
              AND p.FECHA_INI <= (SELECT com.FECHA_REGISTRO
              FROM tbl_bit_comentarios_asignaciones com
              WHERE com.FK_ASIGNACION = a.PK_ASIGNACION
              ORDER BY com.FECHA_REGISTRO DESC
              LIMIT 1))'
              )
              ->join('left join', 'tbl_facturas fact',
              'p.PK_PERIODO = fact.FK_PERIODO
              AND p.FK_DOCUMENTO_FACTURA = fact.FK_DOC_FACTURA'
              )
              ->groupBy(['cte.NOMBRE_CLIENTE']);




          if (Yii::$app->request->isAjax) { // Si la peticion viene de ajax
              $data = Yii::$app->request->post();

              if (!empty($data['IdCliente'])) {

                  $dataProvider->andWhere(['=', 'a.FK_CLIENTE', $data['IdCliente']]);
              }

              $dataProvider = new ActiveDataProvider([
                'query' => $dataProvider,
                'pagination' => false
              ]);

              $dataProvider = $dataProvider->getModels();

              \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
              return [
                'dataProvider' => $dataProvider
              ];
          }

          $dataProvider = new ActiveDataProvider([
            'query' => $dataProvider,
            'pagination' => false
          ]);

          $dataProvider = $dataProvider->getModels();

          foreach ($dataProvider as $key => $value) {
            /*Formato para las Fechas d-m-Y, también para el Grid y Sort del Grid*/
            $spanFecha = '';
            if ($dataProvider[$key]['FECHA_INI'] != '') {
              $dateFecha = str_replace('/', '-', $dataProvider[$key]['FECHA_INI']);
              $spanFecha = date('Y-m-d', strtotime($dateFecha));
              $spanFecha = str_replace('-', '', $spanFecha);
              $dataProvider[$key]['FECHA_INI'] = transform_date($dataProvider[$key]['FECHA_INI'],'d/m/Y');
            }
            $dataProvider[$key]['FECHA_INI'] = '<span class="hide">'.$spanFecha.'</span>'.$dataProvider[$key]['FECHA_INI'];
          }

          return $this->render('reporte_grafica', [
            'dataProvider' => $dataProvider
          ]);

      }

      public function guardarDocs($modeloDocumentos, $datos, $data, $modelSubirDoc, $id, $modelSubirDocHde) {

            $pk_documento_factura = '';
            $pk_documento_factura_xml='';
            $facturaNueva = 'false';

                            //Condición sólo para documentos de tipo 'FACTURA'.

                            // if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 4) {
                            //     $modelSubirDocFactura->file = UploadedFile::getInstance($modelSubirDocFactura, '[7]file');
                            //     if (!empty($modelSubirDocFactura->file)) {
                            //         $fechaHoraHoy = date('YmdHis');
                            //         $rutaGuardado = '../uploads/DocumentosPeriodos/';
                            //         $nombreFisico = $fechaHoraHoy.'_'.quitar_acentos(utf8_decode($modelSubirDocFactura->file->basename));
                            //         $nombreBD = quitar_acentos(utf8_decode($modelSubirDocFactura->file->basename));
                            //         $extension = $modelSubirDocFactura->upload($rutaGuardado,$nombreFisico);
                            //         $rutaDoc = '/uploads/DocumentosPeriodos/';
                            //         $pk_documento_factura='';
                            //         $facturaNueva = 'true';
                            //     }else{
                            //         $pk_documento_factura= (isset($data['pk_documento_factura'])&&!empty($data['pk_documento_factura']))?$data['pk_documento_factura']:'';
                            //     }
                            //     $modelSubirDocFacturaXML->file = UploadedFile::getInstance($modelSubirDocFacturaXML, '[8]file');
                            //     if (!empty($modelSubirDocFacturaXML->file)) {
                            //         $fechaHoraHoyXML = date('YmdHis');
                            //         $rutaGuardadoXML = '../uploads/DocumentosPeriodos/';
                            //         $nombreFisicoXML = $fechaHoraHoyXML.'_'.quitar_acentos(utf8_decode($modelSubirDocFacturaXML->file->basename));
                            //         $nombreBDXML = quitar_acentos(utf8_decode($modelSubirDocFacturaXML->file->basename));
                            //         $extensionXML = $modelSubirDocFacturaXML->upload($rutaGuardadoXML,$nombreFisicoXML);
                            //         $rutaDocXML = '/uploads/DocumentosPeriodos/';
                            //         $pk_documento_factura_xml='';
                            //     }
                            //     else{
                            //         $pk_documento_factura_xml= (isset($data['pk_documento_factura_xml'])&&!empty($data['pk_documento_factura_xml']))?$data['pk_documento_factura_xml']:'';
                            //     }
                            //
                            // if(isset($data['periodo'])){
                            //     foreach ($data['periodo'] as $key => $value) {
                            //         $pk_periodo_factura = $value;
                            //         $facturaNuevaModifica = TblPeriodos::find()->where(['PK_PERIODO' => $pk_periodo_factura])->limit(1)->one();
                            //         // $pk_documento_factura= $facturaNuevaModifica->FK_DOCUMENTO_FACTURA;
                            //         // INICIO HRIBI 02/08/2016- array para concatenar en el asunto del correo, el nombre del(os) mes(es) perteneciente(s) al periodo donde se guarda el documento de Factura.
                            //         $mesPeriodo = $mesPeriodo.(date("m",strtotime($facturaNuevaModifica->FECHA_INI)).',');
                            //         // FIN HRIBI
                            //
                            //         /**
                            //          * Se crea una nueva factura
                            //          */
                            //         if($facturaNuevaModifica->FK_DOCUMENTO_FACTURA==null&&empty($pk_documento_factura)){
                            //             $facturaNueva = 'true';
                            //             $modeloDocumentos->FECHA_DOCUMENTO      = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                            //             $modeloDocumentos->NUM_DOCUMENTO        = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                            //             $modeloDocumentos->NUM_SP               = isset($data['numSPFactura']) ? $data['numSPFactura'] : null;
                            //             //$modeloDocumentos->TARIFA               = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                            //             $modeloDocumentos->FK_RAZON_SOCIAL      = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                            //             $modeloDocumentos->FK_ASIGNACION        = $datos['id'];
                            //             $modeloDocumentos->FK_TIPO_DOCUMENTO    = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                            //             $modeloDocumentos->FK_UNIDAD_NEGOCIO    = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                            //             $modeloDocumentos->FECHA_REGISTRO       = date('Y-m-d H:i:s');
                            //             $modeloDocumentos->FK_CLIENTE           = $data['FK_CLIENTE'];
                            //             $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                            //
                            //             if (!empty($modelSubirDocFactura->file)) {
                            //                 $modeloDocumentos->NOMBRE_DOCUMENTO    = $nombreBD.'.'.$extension;
                            //                 $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                            //             }
                            //
                            //             $modeloDocumentos->save(false);
                            //
                            //             $modeloDocumentosXML->FECHA_DOCUMENTO      = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                            //             $modeloDocumentosXML->NUM_DOCUMENTO        = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                            //             $modeloDocumentosXML->NUM_SP               = isset($data['numSPFactura']) ? $data['numSPFactura'] : null;
                            //             //$modeloDocumentosXML->TARIFA               = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                            //             $modeloDocumentosXML->FK_RAZON_SOCIAL      = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                            //             $modeloDocumentosXML->FK_ASIGNACION        = $datos['id'];
                            //             $modeloDocumentosXML->FK_TIPO_DOCUMENTO    = 5;
                            //             $modeloDocumentosXML->FK_UNIDAD_NEGOCIO    = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                            //             $modeloDocumentosXML->FECHA_REGISTRO       = date('Y-m-d H:i:s');
                            //             $modeloDocumentosXML->FK_CLIENTE           = $data['FK_CLIENTE'];
                            //             $modeloDocumentosXML->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                            //
                            //             if (!empty($modelSubirDocFacturaXML->file)) {
                            //                 $modeloDocumentosXML->NOMBRE_DOCUMENTO    = $nombreBDXML.'.'.$extensionXML;
                            //                 $modeloDocumentosXML->DOCUMENTO_UBICACION = $rutaDocXML.$nombreFisicoXML.'.'.$extensionXML;
                            //             }
                            //             $modeloDocumentosXML->save(false);
                            //
                            //             $modelSeguimiento = new TblAsignacionesSeguimiento;
                            //             $modelSeguimiento->COMENTARIOS = 'Modificaci&oacute;n de documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                            //             $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            //             $modelSeguimiento->FK_ASIGNACION = $modeloDocumentos->FK_ASIGNACION;
                            //             $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                            //             $modelSeguimiento->save(false);
                            //
                            //             $pk_documento_factura=  $modeloDocumentos->PK_DOCUMENTO;
                            //             $pk_documento_factura_xml=  $modeloDocumentosXML->PK_DOCUMENTO;
                            //
                            //             $modeloFactura->FK_PERIODO            = $pk_periodo_factura;
                            //             $modeloFactura->FK_DOC_FACTURA        = $pk_documento_factura;
                            //             $modeloFactura->FECHA_EMISION         = !empty($data['TblFacturas']['FECHA_EMISION']) ? transform_date($data['TblFacturas']['FECHA_EMISION'],'Y-m-d') : null;
                            //             $modeloFactura->FECHA_ENTREGA_CLIENTE = !empty($data['TblFacturas']['FECHA_ENTREGA_CLIENTE']) ? transform_date($data['TblFacturas']['FECHA_ENTREGA_CLIENTE'],'Y-m-d') : null;
                            //             $modeloFactura->FECHA_INGRESO_BANCO   = !empty($data['TblFacturas']['FECHA_INGRESO_BANCO']) ? transform_date($data['TblFacturas']['FECHA_INGRESO_BANCO'],'Y-m-d') : null;
                            //             $modeloFactura->FECHA_RECEPCION_IR    = !empty($data['TblFacturas']['FECHA_RECEPCION_IR']) ? transform_date($data['TblFacturas']['FECHA_RECEPCION_IR'],'Y-m-d') : null;
                            //             //$modeloFactura->FACTURA_PROVISION     = 2;//isset($data['TblFacturas']['FACTURA_PROVISION']) ? $data['TblFacturas']['FACTURA_PROVISION'] : null;
                            //             $modeloFactura->CONTACTO_ENTREGA      = isset($data['TblFacturas']['CONTACTO_ENTREGA']) ? $data['TblFacturas']['CONTACTO_ENTREGA'] : null;
                            //             $modeloFactura->FK_SERVICIO           = 1;
                            //             $modeloFactura->NUMERO_IR             = isset($data['TblFacturas']['NUMERO_IR']) ? $data['TblFacturas']['NUMERO_IR'] : null;
                            //             $modeloFactura->FK_ESTATUS            = 1;
                            //             $modeloFactura->FK_PORCENTAJE         = 1;
                            //             $modeloFactura->TOTAL_FACTURABLE      = $data['total_facturable'][$pk_periodo_factura];
                            //             $modeloFactura->COMENTARIOS           = isset($data['comentariosFactura']) ? $data['comentariosFactura'] : null;
                            //             $modeloFactura->save(false);
                            //             //Bitacora para crear nuevo registro de un Documento Factura en Periodos.
                            //             $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$data['FK_CLIENTE'];
                            //             user_log_bitacora($descripcionBitacora,'Modificar Documento Factura en Periodos',$pk_periodo_factura);
                            //
                            //             $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentosXML->FK_ASIGNACION.', TIPO_DOCUMENTO=XML, NUM_DOCUMENTO='.$modeloDocumentosXML->NUM_DOCUMENTO.', FK_CLIENTE='.$data['FK_CLIENTE'];
                            //             user_log_bitacora($descripcionBitacora,'Modificar Documento XML en Periodos',$pk_documento_factura_xml);
                            //
                            //             $descripcionBitacora = 'FK_DOC_FACTURA='.$modeloFactura->FK_DOC_FACTURA.', FECHA_ENTREGA_CLIENTE='.$modeloFactura->FECHA_ENTREGA_CLIENTE.', COMENTARIOS='.$modeloFactura->COMENTARIOS;
                            //             user_log_bitacora($descripcionBitacora,'Cargar Información de Factura',$modeloFactura->FK_PERIODO);
                            //
                            //         }else{
                            //             /**
                            //              * Se modifica una factura ya existente
                            //              */
                            //
                            //             if(empty($pk_documento_factura)){
                            //                 // $modelSubirDocFactura->file = UploadedFile::getInstance($modelSubirDocFactura, '[7]file');
                            //                 if (!empty($modelSubirDocFactura->file)) {
                            //                     $modeloDocumentos = new TblDocumentos;
                            //                     $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                            //                     $modeloDocumentos->FK_ASIGNACION        = $datos['id'];
                            //                     $modeloDocumentos->NOMBRE_DOCUMENTO     = $nombreBD.'.'.$extension;
                            //                     $modeloDocumentos->DOCUMENTO_UBICACION  = $rutaDoc.$nombreFisico.'.'.$extension;
                            //                     $modeloDocumentos->FK_TIPO_DOCUMENTO    = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                            //                 }else{
                            //                     $modeloDocumentos = TblDocumentos::findOne($facturaNuevaModifica->FK_DOCUMENTO_FACTURA);
                            //                 }
                            //
                            //                 $modeloDocumentos->FECHA_DOCUMENTO   = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                            //                 $modeloDocumentos->NUM_DOCUMENTO     = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                            //                 //$modeloDocumentos->TARIFA            = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                            //                 $modeloDocumentos->FK_RAZON_SOCIAL   = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                            //                 $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                            //
                            //
                            //                 $modeloDocumentos->save(false);
                            //
                            //                 //Bitacora para modificación de Documento Factura en Periodos.
                            //                 $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                            //                 user_log_bitacora($descripcionBitacora,'Cargar Documento Factura en Periodos',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);
                            //
                            //                 $modelSeguimiento = new TblAsignacionesSeguimiento;
                            //                 $modelSeguimiento->COMENTARIOS    = 'Cargar de documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                            //                 $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            //                 $modelSeguimiento->FK_ASIGNACION  = $modeloDocumentos->FK_ASIGNACION;
                            //                 $modelSeguimiento->FK_USUARIO     = user_info()['PK_USUARIO'];
                            //                 $modelSeguimiento->save(false);
                            //
                            //                 $pk_documento_factura = $modeloDocumentos->PK_DOCUMENTO;
                            //
                            //             }else{/*Si al actualizar no se esta dando de alta un documento nuevo, se modifican los datos del documento ya existente*/
                            //
                            //                 $modeloDocumentos = TblDocumentos::findOne($pk_documento_factura);
                            //
                            //                 $modeloDocumentos->NUM_DOCUMENTO     = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                            //                 $modeloDocumentos->FK_RAZON_SOCIAL   = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                            //                 $modeloDocumentos->save(false);
                            //
                            //                 //Bitacora para modificación de Documento Factura en Periodos.
                            //                 $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                            //                 user_log_bitacora($descripcionBitacora,'Actualiza Documento Factura en Periodos',$facturaNuevaModifica->FK_DOCUMENTO_FACTURA);
                            //
                            //                 $modelSeguimiento = new TblAsignacionesSeguimiento;
                            //                 $modelSeguimiento->COMENTARIOS    = 'Modificación de documento Factura_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date('m',strtotime($facturaNuevaModifica->FECHA_INI))).date('Y',strtotime($facturaNuevaModifica->FECHA_INI));
                            //                 $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                            //                 $modelSeguimiento->FK_ASIGNACION  = $modeloDocumentos->FK_ASIGNACION;
                            //                 $modelSeguimiento->FK_USUARIO     = user_info()['PK_USUARIO'];
                            //                 $modelSeguimiento->save(false);
                            //             }
                            //
                            //             if(empty($pk_documento_factura_xml)){
                            //                 if (!empty($modelSubirDocFacturaXML->file)) {
                            //                     $modeloDocumentosXML = new TblDocumentos;
                            //                     $modeloDocumentosXML->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                            //                     $modeloDocumentosXML->FK_ASIGNACION        = $datos['id'];
                            //                     $modeloDocumentosXML->NOMBRE_DOCUMENTO     = $nombreBDXML.'.'.$extensionXML;
                            //                     $modeloDocumentosXML->DOCUMENTO_UBICACION  = $rutaDocXML.$nombreFisicoXML.'.'.$extensionXML;
                            //                     $modeloDocumentosXML->FK_TIPO_DOCUMENTO    = 5;
                            //                 }else{
                            //                     $modeloDocumentosXML = TblDocumentos::findOne($facturaNuevaModifica->FK_DOCUMENTO_FACTURA_XML);
                            //                 }
                            //                 if($modeloDocumentosXML){
                            //                     $modeloDocumentosXML->FECHA_DOCUMENTO   = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                            //                     $modeloDocumentosXML->NUM_DOCUMENTO     = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                            //                     //$modeloDocumentosXML->TARIFA            = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                            //                     $modeloDocumentosXML->FK_RAZON_SOCIAL   = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                            //                     $modeloDocumentosXML->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                            //
                            //                     $modeloDocumentosXML->save(false);
                            //                     $pk_documento_factura_xml = $modeloDocumentosXML->PK_DOCUMENTO;
                            //                 }
                            //             }
                            //
                            //             $modeloFacturaUpd = TblFacturas::find()
                            //                                        ->where(['=','FK_PERIODO', $pk_periodo_factura])
                            //                                        //->andWhere(['=','FK_ESTATUS', '1'])
                            //                                        //->andWhere(['=','FK_ESTATUS', '2'])
                            //                                        ->limit(1)
                            //                                        ->one();
                            //
                            //             if($modeloFacturaUpd){
                            //                 $modeloFacturaUpd->FK_DOC_FACTURA        = $pk_documento_factura;
                            //                 $modeloFacturaUpd->FECHA_EMISION         = !empty($data['TblFacturas']['FECHA_EMISION']) ? transform_date($data['TblFacturas']['FECHA_EMISION'],'Y-m-d') : null;
                            //                 $modeloFacturaUpd->FECHA_ENTREGA_CLIENTE = !empty($data['TblFacturas']['FECHA_ENTREGA_CLIENTE']) ? transform_date($data['TblFacturas']['FECHA_ENTREGA_CLIENTE'],'Y-m-d') : null;
                            //                 $modeloFacturaUpd->FECHA_INGRESO_BANCO   = !empty($data['TblFacturas']['FECHA_INGRESO_BANCO']) ? transform_date($data['TblFacturas']['FECHA_INGRESO_BANCO'],'Y-m-d') : null;
                            //                 $modeloFacturaUpd->FECHA_RECEPCION_IR    = !empty($data['TblFacturas']['FECHA_RECEPCION_IR']) ? transform_date($data['TblFacturas']['FECHA_RECEPCION_IR'],'Y-m-d') : null;
                            //                 //$modeloFacturaUpd->FACTURA_PROVISION     = 2;//isset($data['TblFacturas']['FACTURA_PROVISION']) ? $data['TblFacturas']['FACTURA_PROVISION'] : null;
                            //                 $modeloFacturaUpd->CONTACTO_ENTREGA      = isset($data['TblFacturas']['CONTACTO_ENTREGA']) ? $data['TblFacturas']['CONTACTO_ENTREGA'] : null;
                            //                 $modeloFacturaUpd->FK_SERVICIO           = 1;
                            //                 $modeloFacturaUpd->NUMERO_IR             = isset($data['TblFacturas']['NUMERO_IR']) ? $data['TblFacturas']['NUMERO_IR'] : null;
                            //                 if($modeloFacturaUpd->FECHA_INGRESO_BANCO != null){
                            //                     $modeloFacturaUpd->FK_ESTATUS = 2;
                            //                 }else{
                            //                     $modeloFacturaUpd->FK_ESTATUS = 1;
                            //                 }
                            //                 $modeloFacturaUpd->TOTAL_FACTURABLE      = $data['total_facturable'][$pk_periodo_factura];
                            //                 $modeloFacturaUpd->COMENTARIOS           = isset($data['comentariosFactura']) ? $data['comentariosFactura'] : null;
                            //                 $modeloFacturaUpd->save(false);
                            //
                            //                 //Bitacora para modificación de registro en tblFacturas.
                            //                 $descripcionBitacora = 'FK_DOC_FACTURA='.$modeloFacturaUpd->FK_DOC_FACTURA.', FECHA_ENTREGA_CLIENTE='.$modeloFacturaUpd->FECHA_ENTREGA_CLIENTE.
                            //                 ', FECHA_INGRESO_BANCO='.$modeloFacturaUpd->FECHA_INGRESO_BANCO.', COMENTARIOS='.$modeloFacturaUpd->COMENTARIOS;
                            //                 user_log_bitacora($descripcionBitacora,'Modificar Información de Factura',$modeloFactura->FK_PERIODO);
                            //             }else{
                            //                 $modeloFactura = new TblFacturas;
                            //                 $modeloFactura->FK_PERIODO            = $pk_periodo_factura;
                            //                 $modeloFactura->FK_DOC_FACTURA        = $pk_documento_factura;
                            //                 $modeloFactura->FECHA_EMISION         = !empty($data['TblFacturas']['FECHA_EMISION']) ? transform_date($data['TblFacturas']['FECHA_EMISION'],'Y-m-d') : null;
                            //                 $modeloFactura->FECHA_ENTREGA_CLIENTE = !empty($data['TblFacturas']['FECHA_ENTREGA_CLIENTE']) ? transform_date($data['TblFacturas']['FECHA_ENTREGA_CLIENTE'],'Y-m-d') : null;
                            //                 $modeloFactura->FECHA_INGRESO_BANCO   = !empty($data['TblFacturas']['FECHA_INGRESO_BANCO']) ? transform_date($data['TblFacturas']['FECHA_INGRESO_BANCO'],'Y-m-d') : null;
                            //                 $modeloFactura->FECHA_RECEPCION_IR    = !empty($data['TblFacturas']['FECHA_RECEPCION_IR']) ? transform_date($data['TblFacturas']['FECHA_RECEPCION_IR'],'Y-m-d') : null;
                            //                 //$modeloFactura->FACTURA_PROVISION     = 2;//isset($data['TblFacturas']['FACTURA_PROVISION']) ? $data['TblFacturas']['FACTURA_PROVISION'] : null;
                            //                 $modeloFactura->CONTACTO_ENTREGA      = isset($data['TblFacturas']['CONTACTO_ENTREGA']) ? $data['TblFacturas']['CONTACTO_ENTREGA'] : null;
                            //                 $modeloFactura->FK_SERVICIO           = 1;
                            //                 $modeloFactura->NUMERO_IR             = isset($data['TblFacturas']['NUMERO_IR']) ? $data['TblFacturas']['NUMERO_IR'] : null;
                            //                 $modeloFactura->FK_ESTATUS            = 1;
                            //                 $modeloFactura->FK_PORCENTAJE         = 1;
                            //                 $modeloFactura->TOTAL_FACTURABLE      = $data['total_facturable'][$pk_periodo_factura];
                            //                 $modeloFactura->COMENTARIOS           = isset($data['comentariosFactura']) ? $data['comentariosFactura'] : null;
                            //                 $modeloFactura->save(false);
                            //
                            //                 //Bitacora para crear nuevo registro de un Documento Factura en Periodos.
                            //                 $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=FACTURA, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$data['FK_CLIENTE'];
                            //                 user_log_bitacora($descripcionBitacora,'Modificar Documento Factura en Periodos',$pk_periodo_factura);
                            //
                            //                 $descripcionBitacora = 'FK_DOC_FACTURA='.$modeloFactura->FK_DOC_FACTURA.', FECHA_ENTREGA_CLIENTE='.$modeloFactura->FECHA_ENTREGA_CLIENTE.', COMENTARIOS='.$modeloFactura->COMENTARIOS;
                            //                 user_log_bitacora($descripcionBitacora,'Cargar Información de Factura',$modeloFactura->FK_PERIODO);
                            //
                            //             }
                            //         }
                            //         $modelPeriodosUpd = TblPeriodos::find()->where(['PK_PERIODO' => $pk_periodo_factura])->limit(1)->one();
                            //         $modelPeriodosUpd->FK_DOCUMENTO_FACTURA = $pk_documento_factura;
                            //         $modelPeriodosUpd->FK_DOCUMENTO_FACTURA_XML = $pk_documento_factura_xml;
                            //         //$modelPeriodosUpd->TARIFA_FACTURA = $data['TblDocumentos']['TARIFA'];
                            //         $modelPeriodosUpd->HORAS_FACTURA = $data['horas_factura'][$pk_periodo_factura];
                            //         $modelPeriodosUpd->MONTO_FACTURA = $data['monto_factura'][$pk_periodo_factura];
                            //         $modelPeriodosUpd->save(false);
                            //
                            //     }
                            // }
                            //
                            //     if(isset($nombreBD) && $facturaNueva = 'true'){
                            //         $modeloDocumentoFactura = TblDocumentos::findOne($pk_documento_factura);
                            //         $ubicacion_doc_factura = $modeloDocumentoFactura['DOCUMENTO_UBICACION'];
                            //         $ubicacion_doc_factura_xml = '';
                            //
                            //         if($pk_documento_factura_xml != null){
                            //             $modeloDocumentoFacturaXML = TblDocumentos::findOne($pk_documento_factura_xml);
                            //             $ubicacion_doc_factura_xml = $modeloDocumentoFacturaXML['DOCUMENTO_UBICACION'];
                            //         }
                            //
                            //         $arrayMeses = array("01" => "Enero",
                            //                                 "02" => "Febrero",
                            //                                 "03" => "Marzo",
                            //                                 "04" => "Abril",
                            //                                 "05" => "Mayo",
                            //                                 "06" => "Junio",
                            //                                 "07" => "Julio",
                            //                                 "08" => "Agosto",
                            //                                 "09" => "Septiembre",
                            //                                 "10" => "Octubre",
                            //                                 "11" => "Noviembre",
                            //                                 "12" => "Diciembre", );
                            //     }
                            //
                            // }
                            //
                            if(isset($data['es_bolsaHDE'])&&$data['es_bolsaHDE']!=1){
                                //Condición sólo para documentos de tipo 'HDE'.
                                if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 3) {

                                  foreach ($data['chkPeriodos'] as $key => $value) {
                                    $hdeNuevaModifica = TblPeriodos::findOne($value);

                                    if($hdeNuevaModifica->FK_DOCUMENTO_HDE == null) {
                                        $modeloDocumentos->FECHA_DOCUMENTO      = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                        $modeloDocumentos->NUM_DOCUMENTO        = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                        $modeloDocumentos->NUM_SP               = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;
                                        //$modeloDocumentos->TARIFA               = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                        $modeloDocumentos->FK_RAZON_SOCIAL      = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                        $modeloDocumentos->FK_ASIGNACION        = $data['fkasignacion'][$value];
                                        $modeloDocumentos->FK_TIPO_DOCUMENTO    = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                                        $modeloDocumentos->FK_UNIDAD_NEGOCIO    = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                                        $modeloDocumentos->FECHA_REGISTRO       = date('Y-m-d H:i:s');
                                        $modeloDocumentos->FK_CLIENTE           = $data['FK_CLIENTE'];
                                        $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                                        $modeloDocumentos->MONTO = $data['montoTotalADP'];

                                        $modelSubirDocHde->file = UploadedFile::getInstance($modelSubirDocHde, '[6]file');
                                        if (!empty($modelSubirDocHde->file)) {
                                            $fechaHoraHoy                          = date('YmdHis');
                                            $rutaGuardado                          = '../uploads/DocumentosPeriodos/';
                                            $nombreFisico                          = $fechaHoraHoy.'_'.quitar_acentos(utf8_decode($modelSubirDocHde->file->basename));
                                            $nombreBD                              = quitar_acentos(utf8_decode($modelSubirDocHde->file->basename));
                                            $extension                             = $modelSubirDocHde->upload($rutaGuardado,$nombreFisico);
                                            $rutaDoc                               = '/uploads/DocumentosPeriodos/';
                                            $modeloDocumentos->NOMBRE_DOCUMENTO    = $nombreBD.'.'.$extension;
                                            $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                                         }
                                        $modeloDocumentos->save(false);
                                        //Bitacora para crear nuevo registro de un Documento HDE en Periodos.
                                        $descripcionBitacora              = 'FK_ASIGNACION='.$data['fkasignacion'][$value].', TIPO_DOCUMENTO=HDE, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                                        user_log_bitacora($descripcionBitacora,'Modificar Documento HDE en Periodos',$hdeNuevaModifica->FK_DOCUMENTO_HDE);

                                        $modelSeguimiento                 = new TblAsignacionesSeguimiento;
                                        // $modelSeguimiento->load(Yii    ::$app->request->post());
                                        $modelSeguimiento->COMENTARIOS    = 'Modificaci&oacute;n de documento HDE_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date("m",strtotime($hdeNuevaModifica->FECHA_INI))).date("Y",strtotime($hdeNuevaModifica->FECHA_INI));
                                        $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                        $modelSeguimiento->FK_ASIGNACION  = $data['fkasignacion'][$value];
                                        $modelSeguimiento->FK_USUARIO     = user_info()['PK_USUARIO'];
                                        $modelSeguimiento->save(false);

                                        $id = $modeloDocumentos->PK_DOCUMENTO;
                                    }else{
                                        $modeloDocumentos                    = TblDocumentos::findOne($hdeNuevaModifica->FK_DOCUMENTO_HDE);
                                        $modeloDocumentos->FECHA_DOCUMENTO   = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                        $modeloDocumentos->NUM_DOCUMENTO     = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                        //$modeloDocumentos->TARIFA            = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                        $modeloDocumentos->FK_RAZON_SOCIAL   = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                        $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                                        $modeloDocumentos->MONTO = $data['montoTotalADP'];

                                            $modelSubirDocHde->file = UploadedFile::getInstance($modelSubirDocHde, '[6]file');
                                            if (!empty($modelSubirDocHde->file)) {
                                                $fechaHoraHoy                          = date('YmdHis');
                                                $rutaGuardado                          = '../uploads/DocumentosPeriodos/';
                                                $nombreFisico                          = $fechaHoraHoy.'_'.quitar_acentos(utf8_decode($modelSubirDocHde->file->basename));
                                                $nombreBD                              = quitar_acentos(utf8_decode($modelSubirDocHde->file->basename));
                                                $extension                             = $modelSubirDocHde->upload($rutaGuardado,$nombreFisico);
                                                $rutaDoc                               = '/uploads/DocumentosPeriodos/';
                                                $modeloDocumentos->NOMBRE_DOCUMENTO    = $nombreBD.'.'.$extension;
                                                $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                                            }
                                        $modeloDocumentos->save(false);
                                        //Bitacora para modificación de Documento HDE en Periodos.
                                        $descripcionBitacora              = 'FK_ASIGNACION='.$data['fkasignacion'][$value].', TIPO_DOCUMENTO=HDE, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE;
                                        user_log_bitacora($descripcionBitacora,'Cargar Documento HDE en Periodos',$data['pk_periodo_hde']);

                                        $modelSeguimiento                 = new TblAsignacionesSeguimiento;
                                        // $modelSeguimiento->load(Yii    ::$app->request->post());
                                        $modelSeguimiento->COMENTARIOS    = 'Cargar de documento HDE_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date("m",strtotime($hdeNuevaModifica->FECHA_INI))).date("Y",strtotime($hdeNuevaModifica->FECHA_INI));
                                        $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                        $modelSeguimiento->FK_ASIGNACION  = $data['fkasignacion'][$value];
                                        $modelSeguimiento->FK_USUARIO     = user_info()['PK_USUARIO'];
                                        $modelSeguimiento->save(false);

                                        $id = $modeloDocumentos->PK_DOCUMENTO;
                                    }
                                    if(isset($nombreBD) && $hdeNuevaModifica->FK_DOCUMENTO_FACTURA == null){
                                        $modeloDocumentoODC= TblDocumentos::findOne($hdeNuevaModifica->FK_DOCUMENTO_ODC);
                                        $ubicacion_doc_odc= $modeloDocumentoODC->DOCUMENTO_UBICACION;
                                        $modeloDocumentoHDE= TblDocumentos::findOne($id);
                                        $ubicacion_doc_hde= $modeloDocumentoHDE->DOCUMENTO_UBICACION;

                                        // $mesPeriodo = date("m",strtotime($hdeNuevaModifica->FECHA_INI));
                                    }

                                  }
                                }
                            }

                            if(isset($data['es_bolsa'])&&$data['es_bolsa']!=1){
                                //Condición sólo para documentos de tipo 'ODC'.

                                if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 2) {
                                  //Función para guardar numero de periodos
                                  $tblAsignaciones = TblAsignaciones::find()->where(['PK_ASIGNACION' => $id])->limit(1)->one();
                                  $tblAsignaciones->NUMERO_PERIODOS = $data['numPeriodos'];
                                  $cantPeriodos = count($data['chkPeriodos']);

                                  if($data['numPeriodos'] > $cantPeriodos){
                                    $tblAsignaciones->NUMERO_PERIODOS = ($data['numPeriodos'] - $cantPeriodos);
                                  } else {
                                    $tblAsignaciones->NUMERO_PERIODOS = 0;
                                  }
                                  $tblAsignaciones->save(false);

                                    $odcNuevaModifica = TblPeriodos::find()->where(['PK_PERIODO' => $data['chkPeriodos']])->limit(1)->one();

                                    $modelSubirDoc->file = UploadedFile::getInstance($modelSubirDoc, '[5]file');
                                    if (!empty($modelSubirDoc->file)) {
                                        $fechaHoraHoy = date('YmdHis');
                                        $rutaGuardado = '../uploads/DocumentosPeriodos/';
                                        $nombreFisico = $fechaHoraHoy.'_'.quitar_acentos(utf8_decode($modelSubirDoc->file->basename));
                                        $nombreBD = quitar_acentos(utf8_decode($modelSubirDoc->file->basename));

                                        $extension = $modelSubirDoc->upload($rutaGuardado,$nombreFisico);
                                        $rutaDoc = '/uploads/DocumentosPeriodos/';
                                        $modeloDocumentos->NOMBRE_DOCUMENTO = $nombreBD.'.'.$extension;
                                        $modeloDocumentos->DOCUMENTO_UBICACION = $rutaDoc.$nombreFisico.'.'.$extension;
                                        //$modeloDocumentos->FK_USUARIO = $pk_usuario_loggin = isset(user_info()['PK_USUARIO']) ? user_info()['PK_USUARIO'] : null;
                                        $modeloDocumentos->FK_USUARIO = isset(user_info()['PK_USUARIO']) ? user_info()['PK_USUARIO'] : null;
                                    }

                                    if ($modelSubirDoc->file == null) { //Condición para identificar si es un update de un registro ODC

                                        $modeloDocumentos = TblDocumentos::findOne($data['TblDocumentos']['PK_DOCUMENTO']);


                                        $modeloDocumentos->FECHA_DOCUMENTO = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                        $modeloDocumentos->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                        $modeloDocumentos->NUM_SP = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;

                                        //$modeloDocumentos->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                        $modeloDocumentos->FK_RAZON_SOCIAL = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                        $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                                        //Bitacora para crear nuevo registro de un Documento ODC en Periodos.
                                        $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=ODC, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE.', FK_USUARIO '.$modeloDocumentos->FK_USUARIO;
                                        user_log_bitacora($descripcionBitacora,'Modificar Documento ODC en Periodos',$data['TblDocumentos']['PK_DOCUMENTO']);
                                        $modeloDocumentos->MONTO = quitarFormatoMoneda($data['montoTotalODC']);
                                        $modelSeguimiento = new TblAsignacionesSeguimiento;
                                        // $modelSeguimiento->load(Yii::$app->request->post());
                                        $modelSeguimiento->COMENTARIOS = 'Modificaci&oacute;n de documento OdC_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date("m",strtotime($odcNuevaModifica->FECHA_INI))).date("Y",strtotime($odcNuevaModifica->FECHA_INI));
                                        $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                        $modelSeguimiento->FK_ASIGNACION = $modeloDocumentos->FK_ASIGNACION;
                                        $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                                        $modelSeguimiento->save(false);

                                    } else { //Condición para identificar si es un create de un registro ODC

                                        $modeloDocumentos->FECHA_DOCUMENTO = isset($data['TblDocumentos']['FECHA_DOCUMENTO']) ? transform_date($data['TblDocumentos']['FECHA_DOCUMENTO'],'Y-m-d') : null;
                                        $modeloDocumentos->NUM_DOCUMENTO = isset($data['TblDocumentos']['NUM_DOCUMENTO']) ? $data['TblDocumentos']['NUM_DOCUMENTO'] : null;
                                        $modeloDocumentos->NUM_SP = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;
                                        //$modeloDocumentos->TARIFA = isset($data['TblDocumentos']['TARIFA']) ? $data['TblDocumentos']['TARIFA'] : null;
                                        $modeloDocumentos->FK_RAZON_SOCIAL = isset($data['TblDocumentos']['FK_RAZON_SOCIAL']) ? $data['TblDocumentos']['FK_RAZON_SOCIAL'] : null;
                                        $modeloDocumentos->FK_ASIGNACION = $datos['id'];
                                        $modeloDocumentos->FK_TIPO_DOCUMENTO = isset($data['TblDocumentos']['FK_TIPO_DOCUMENTO']) ? $data['TblDocumentos']['FK_TIPO_DOCUMENTO'] : null;
                                        $modeloDocumentos->FK_UNIDAD_NEGOCIO = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                                        $modeloDocumentos->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                        $modeloDocumentos->FK_CLIENTE = $data['FK_CLIENTE'];
                                        $modeloDocumentos->CONSECUTIVO_TIPO_DOC = $data['consecutivo'];
                                        $modeloDocumentos->MONTO = quitarFormatoMoneda($data['montoTotalODC']);
                                        //Bitacora para modificación de Documento ODC en Periodos.
                                        $descripcionBitacora = 'FK_ASIGNACION='.$modeloDocumentos->FK_ASIGNACION.', TIPO_DOCUMENTO=ODC, NUM_DOCUMENTO='.$modeloDocumentos->NUM_DOCUMENTO.', FK_CLIENTE='.$modeloDocumentos->FK_CLIENTE.', FK_USUARIO '.$modeloDocumentos->FK_USUARIO;
                                        user_log_bitacora($descripcionBitacora,'Cargar Documento ODC en Periodos',$data['TblDocumentos']['PK_DOCUMENTO']);

                                        $modelSeguimiento = new TblAsignacionesSeguimiento;
                                        // $modelSeguimiento->load(Yii::$app->request->post());
                                        $modelSeguimiento->COMENTARIOS = 'Carga de documento OdC_'.$modeloDocumentos->NUM_DOCUMENTO.'_'.obtener_nombre_mes(date("m",strtotime($odcNuevaModifica->FECHA_INI))).date("Y",strtotime($odcNuevaModifica->FECHA_INI));
                                        $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                        $modelSeguimiento->FK_ASIGNACION = $modeloDocumentos->FK_ASIGNACION;
                                        $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                                        $modelSeguimiento->save(false);
                                    }

                                    $modeloDocumentos->save(false);
                                }
                                $id = $modeloDocumentos->PK_DOCUMENTO;
                            }

                            if (isset($data['chkPeriodos'])) { //identifica si estan seleccionados uno o mas periodos en el alta de ODC

                                //Entra con documentos
                                if($data['TblDocumentos']['FK_TIPO_DOCUMENTO'] == 3) { //Condición para documentos tipo HDE

                                    $bolsa_documento_hde= '';
                                    if(isset($data['es_bolsaHDE'])&&$data['es_bolsaHDE']==1){
                                        $bolsa_documento_hde = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$data['TblDocumentos']['PK_DOCUMENTO']])->one();
                                        $datos_bolsa = TblCatBolsas::find()->where(['PK_BOLSA'=>$data['pk_bolsaHDE']])->one();
                                    }

                                    foreach ($data['chkPeriodos'] as $key => $value) {
                                      $modelPeriodosUpd = TblPeriodos::findOne($value);

                                    if(isset($data['es_bolsaHDE'])&&$data['es_bolsaHDE']==1){
                                        $datos_bolsa->HORAS_DISPONIBLES = $datos_bolsa->HORAS_DISPONIBLES + $modelPeriodosUpd->HORAS_HDE;
                                        $datos_bolsa->MONTO_DISPONIBLE = $datos_bolsa->MONTO_DISPONIBLE + $modelPeriodosUpd->MONTO_HDE;
                                    }

                                    if(isset($data['es_bolsaHDE'])&&$data['es_bolsaHDE']!=1){
                                        $modelPeriodosUpd->FK_DOCUMENTO_HDE = $id;
                                        //$modelPeriodosUpd->TARIFA_HDE = $data['TblDocumentos']['TARIFA'];
                                    }else{
                                        $modelPeriodosUpd->HORAS = $data['txtHoras'];
                                        $modelPeriodosUpd->MONTO = $data['txtMonto'];
                                    }

                                    $modelPeriodosUpd->HORAS_HDE = $modelPeriodosUpd->HORAS;
                                    // if ($data['txtHoras'] != $modelPeriodosUpd->HORAS_DEVENGAR) {
                                    //
                                    //     $modelSeguimiento = new TblAsignacionesSeguimiento();
                                    //     $modelSeguimiento->COMENTARIOS = 'El Periodo <b>'.$modelPeriodosUpd->PK_PERIODO.'</b> ha tenido una actualizaci&oacute;n de <b>Horas a Devengar,</b> de <b>'.$modelPeriodosUpd->HORAS_DEVENGAR.'</b> horas a <b>'.$data['txtHoras'].'</b> horas.';
                                    //     $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
                                    //     $modelSeguimiento->FK_ASIGNACION = $modelPeriodosUpd->FK_ASIGNACION;
                                    //     $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
                                    //     $modelSeguimiento->save(false);
                                    //
                                    //     $modelPeriodosUpd->HORAS_DEVENGAR = $data['txtHoras'];
                                    // }
                                    $modelPeriodosUpd->MONTO_HDE = $modelPeriodosUpd->TARIFA;
                                    //$modelPeriodosUpd->MONTO_HDE = $data['txtMonto'];

                                    $docFactura = TblFacturas::find()->where(['FK_DOC_FACTURA' => $modelPeriodosUpd->FK_DOCUMENTO_FACTURA ])->orderBy(['PK_FACTURA' => SORT_DESC])->limit(1)-> one();

                                    if(!isset($docFactura->FECHA_INGRESO_BANCO)){
                                        $modelPeriodosUpd->HORAS_FACTURA = $data['txtHoras'];
                                        $modelPeriodosUpd->MONTO_FACTURA = ($data['txtHoras'] * $data['TblDocumentos']['TARIFA']);
                                    }

                                    $modelPeriodosUpd->save(false);

                                    if(isset($data['es_bolsaHDE'])&&$data['es_bolsaHDE']==1){
                                        $datos_bolsa->HORAS_DISPONIBLES =  $datos_bolsa->HORAS_DISPONIBLES - $modelPeriodosUpd->HORAS_HDE;
                                        $datos_bolsa->MONTO_DISPONIBLE = $datos_bolsa->MONTO_DISPONIBLE - $modelPeriodosUpd->MONTO_HDE;
                                        $datos_bolsa->save(false);
                                    }
                                    }
                                    //$modelPeriodosUpd = TblPeriodos::find()->where(['PK_PERIODO' => $data['pk_periodo_hde']])->limit(1)->one();


                                } else { //Condición para documentos tipo ODC

                                    $newAsignacion = TblAsignaciones::find()->where(['PK_ASIGNACION' => $datos['id']])->limit(1)->one();
                                    $periodosImpactados = null;

                                    $bolsa_documento_odc= '';
                                    if(isset($data['es_bolsa'])&&$data['es_bolsa']==1){
                                        $bolsa_documento_odc = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$data['TblDocumentos']['PK_DOCUMENTO']])->one();
                                        $datos_bolsa = TblCatBolsas::find()->where(['PK_BOLSA'=>$data['pk_bolsa']])->one();
                                    }


                                    foreach ($data['chkPeriodos'] as $key => $value) { //Recorre los periodos seleccionados para cargar ODC

                                        $modelPeriodosUpd = TblPeriodos::findOne($value);
                                        if(isset($data['es_bolsa'])&&$data['es_bolsa']==1&&$modelPeriodosUpd->FK_DOCUMENTO_ODC!=null){
                                            $datos_bolsa->HORAS_DISPONIBLES = (float)$datos_bolsa->HORAS_DISPONIBLES+ (float)$modelPeriodosUpd->HORAS;
                                            $datos_bolsa->MONTO_DISPONIBLE = (float)$datos_bolsa->MONTO_DISPONIBLE + (float)$modelPeriodosUpd->MONTO;
                                        }

                                        $horas = $data['txtHoras_'.$value];
                                        if(isset($data['cambio'])){
                                            $monto = $data['TblPeriodos']['TARIFA']*$horas;
                                            $monto = isset($data['txtMonto_'.$value]) ? $data['txtMonto_'.$value] : ($horas * $data['TblPeriodos']['TARIFA']);
                                        }else{
                                            if(isset($data['TblPeriodos']['TARIFA'])){
                                                $monto = isset($data['txtMonto_'.$value]) ? $data['txtMonto_'.$value] : ($horas * $data['TblPeriodos']['TARIFA']);
                                            }else{
                                                $monto = isset($data['txtMonto_'.$value]) ? $data['txtMonto_'.$value] : ($horas * $data['mask_tarifa_odc']);
                                            }
                                        }
                                        //$monto = isset($data['txtMonto_'.$value]) ? $data['txtMonto_'.$value] : ($data['txtHoras_'.$value] * $data['TblPeriodos']['TARIFA']);
                                        //$modelPeriodosUpd->FK_DOCUMENTO_ODC = ($modelSubirDoc->file == null) ? ($modelPeriodosUpd->FK_DOCUMENTO_ODC == 'null' || $modelPeriodosUpd->FK_DOCUMENTO_ODC == null) ? $id : $modelPeriodosUpd->FK_DOCUMENTO_ODC : $id;
                                        if(isset($data['es_bolsa'])&&$data['es_bolsa']!=1){

                                            $modelPeriodosUpd->FK_DOCUMENTO_ODC = ($modelSubirDoc->file == null) ? ($modelPeriodosUpd->FK_DOCUMENTO_ODC == 'null' || $modelPeriodosUpd->FK_DOCUMENTO_ODC == null) ? $id : $id : $id;
                                            $modelPeriodosUpd->FACTURA_PROVISION= $data['Tblperiodos']['FACTURA_PROVISION'];

                                        }else{

                                            if($modelPeriodosUpd->FK_DOCUMENTO_ODC==null){
                                                $nuevo_documento_odc                       = new TblDocumentos();
                                                $nuevo_documento_odc->FECHA_DOCUMENTO      = $bolsa_documento_odc->FECHA_DOCUMENTO;
                                                $nuevo_documento_odc->NUM_DOCUMENTO        = $bolsa_documento_odc->NUM_DOCUMENTO;
                                                $nuevo_documento_odc->NUM_SP               = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : null;
                                                //$nuevo_documento_odc->TARIFA               = $bolsa_documento_odc->TARIFA;
                                                $nuevo_documento_odc->FK_RAZON_SOCIAL      = $bolsa_documento_odc->FK_RAZON_SOCIAL;
                                                $nuevo_documento_odc->FK_ASIGNACION        = $bolsa_documento_odc->FK_ASIGNACION;
                                                $nuevo_documento_odc->FK_TIPO_DOCUMENTO    = $bolsa_documento_odc->FK_TIPO_DOCUMENTO;
                                                $nuevo_documento_odc->DOCUMENTO_UBICACION  = $bolsa_documento_odc->DOCUMENTO_UBICACION;
                                                $nuevo_documento_odc->NOMBRE_DOCUMENTO     = $bolsa_documento_odc->NOMBRE_DOCUMENTO;
                                                $nuevo_documento_odc->FK_UNIDAD_NEGOCIO    = isset($data['TblDocumentos']['FK_UNIDAD_NEGOCIO']) ? $data['TblDocumentos']['FK_UNIDAD_NEGOCIO'] : null;
                                                $nuevo_documento_odc->FECHA_REGISTRO       = date('Y-m-d H:i:s');
                                                $nuevo_documento_odc->FK_CLIENTE           = $data['FK_CLIENTE'];
                                                $nuevo_documento_odc->CONSECUTIVO_TIPO_DOC = $bolsa_documento_odc->CONSECUTIVO_TIPO_DOC;
                                                $nuevo_documento_odc->save(false);
                                                $modelPeriodosUpd->FK_DOCUMENTO_ODC        =$nuevo_documento_odc->PK_DOCUMENTO;
                                                $modelPeriodosUpd->TARIFA                  =isset($data['TblPeriodos']['TARIFA']) ? $data['TblPeriodos']['TARIFA'] : null;

                                                $nuevo_documento_hde                       = new TblDocumentos();
                                                $nuevo_documento_hde->FECHA_DOCUMENTO      = $bolsa_documento_odc->FECHA_DOCUMENTO;
                                                $nuevo_documento_hde->NUM_DOCUMENTO        = str_replace('BLS_ODC_','BLS_HDE_',$bolsa_documento_odc->NUM_DOCUMENTO);
                                                $nuevo_documento_hde->DOCUMENTO_UBICACION  = $bolsa_documento_odc->DOCUMENTO_UBICACION;
                                                $nuevo_documento_hde->NOMBRE_DOCUMENTO     = $bolsa_documento_odc->NOMBRE_DOCUMENTO;
                                                //$nuevo_documento_hde->TARIFA               = $bolsa_documento_odc->TARIFA;
                                                $nuevo_documento_hde->FK_ASIGNACION        = $bolsa_documento_odc->FK_ASIGNACION;
                                                $nuevo_documento_hde->FK_TIPO_DOCUMENTO    = 3;
                                                $nuevo_documento_hde->CONSECUTIVO_TIPO_DOC = 0;
                                                $nuevo_documento_hde->FK_RAZON_SOCIAL      = $bolsa_documento_odc->FK_RAZON_SOCIAL;
                                                $nuevo_documento_hde->save(false);

                                                $modelPeriodosUpd->FK_DOCUMENTO_HDE = $nuevo_documento_hde->PK_DOCUMENTO;
                                                $modelPeriodosUpd->HORAS_HDE        = $horas;
                                                $modelPeriodosUpd->MONTO_HDE        = $monto;
                                                $modelPeriodosUpd->FACTURA_PROVISION= $data['TblPeriodos']['FACTURA_PROVISION'];

                                            }else{
                                                $documento_periodo_bolsa                      = TblDocumentos::find()->where(['PK_DOCUMENTO'=>$modelPeriodosUpd->FK_DOCUMENTO_ODC])->one();
                                                $documento_periodo_bolsa->DOCUMENTO_UBICACION = $bolsa_documento_odc->DOCUMENTO_UBICACION;
                                                $documento_periodo_bolsa->NOMBRE_DOCUMENTO    = $bolsa_documento_odc->NOMBRE_DOCUMENTO;
                                                $documento_periodo_bolsa->NUM_SP              = isset($data['TblDocumentos']['NUM_SP']) ? $data['TblDocumentos']['NUM_SP'] : $documento_periodo_bolsa->NUM_SP;
                                                $documento_periodo_bolsa->FK_CLIENTE          = $data['FK_CLIENTE'];
                                                $documento_periodo_bolsa->save(false);
                                                $modelPeriodosUpd->TARIFA                     =isset($data['TblPeriodos']['TARIFA']) ? $data['TblPeriodos']['TARIFA'] : $modelPeriodosUpd->TARIFA;
                                                $modelPeriodosUpd->HORAS_HDE        = $horas;
                                                $modelPeriodosUpd->MONTO_HDE        = $monto;
                                                $modelPeriodosUpd->FACTURA_PROVISION = isset($data['TblPeriodos']['FACTURA_PROVISION']) ? $data['TblPeriodos']['FACTURA_PROVISION'] : $modelPeriodosUpd->FACTURA_PROVISION;
                                            }
                                        }
                                        $modelPeriodosUpd->HORAS = $horas;
                                        if(isset($data['cambio'])){
                                            $modelPeriodosUpd->MONTO = $data['TblPeriodos']['TARIFA']*$horas;
                                            $modelPeriodosUpd->TARIFA = $data['TblPeriodos']['TARIFA'];
                                        }else{
                                            $modelPeriodosUpd->MONTO = $modelPeriodosUpd->TARIFA*$horas;
                                        }
                                        $modelPeriodosUpd->save(false);

                                        if(isset($data['es_bolsa'])&&$data['es_bolsa']==1){
                                            $datos_bolsa->HORAS_DISPONIBLES =  (float)$datos_bolsa->HORAS_DISPONIBLES - (float)$modelPeriodosUpd->HORAS;
                                            $datos_bolsa->MONTO_DISPONIBLE = (float)$datos_bolsa->MONTO_DISPONIBLE - (float)$modelPeriodosUpd->MONTO;
                                        }

                                        /*Inicio Cambio de Tarifas*/
                                        if ($newAsignacion->TARIFA != $modelPeriodosUpd->TARIFA) {

                                            $nuevaTarifa = new TblBitAsignacionTarifas();
                                            $nuevaTarifa->FK_ASIGNACION = $newAsignacion->PK_ASIGNACION;
                                            $nuevaTarifa->FK_PERIODO = $modelPeriodosUpd->PK_PERIODO;
                                            $nuevaTarifa->CAMBIO_TARIFA = $modelPeriodosUpd->TARIFA;
                                            $nuevaTarifa->FK_CAT_TARIFA = isset($data['pk_cat_tarifa_select']) ? $data['pk_cat_tarifa_select'] : null;
                                            $nuevaTarifa->FECHA_REGISTRO = date('Y-m-d H:i:s');

                                            $nuevaTarifa->save(false);

                                            $newAsignacion->TARIFA = $modelPeriodosUpd->TARIFA;
                                            $newAsignacion->FK_CAT_TARIFA = isset($data['pk_cat_tarifa_select']) ? $data['pk_cat_tarifa_select'] : null;
                                            $newAsignacion->save(false);

                                            /**
                                             * Cambiar Tarifa periodos
                                             */
                                            $proximos_periodos= TblPeriodos::find()->where(['FK_ASIGNACION'=>$modelPeriodosUpd->FK_ASIGNACION])->andWhere(['>','FECHA_INI',$modelPeriodosUpd->FECHA_INI])->all();

                                            foreach ($proximos_periodos as $key => $value2) {
                                                if(!$value2->FK_DOCUMENTO_ODC){
                                                    $next_periodo= TblPeriodos::find()->where(['PK_PERIODO'=>$value2->PK_PERIODO])->one();
                                                    $next_periodo->TARIFA = $data['TblPeriodos']['TARIFA'];
                                                    $new_monto = $next_periodo->TARIFA * $next_periodo->HORAS;
                                                    $next_periodo->MONTO=$new_monto;
                                                    $next_periodo->save(false);
                                                }else{
                                                    break;
                                                }
                                            }

                                        }
                                        /*Fin Cambio de Tafiras*/

                                        if($periodosImpactados == null){
                                            $periodosImpactados = $periodosImpactados.''.$value;
                                        }else{
                                            $periodosImpactados = $periodosImpactados.', '.$value;
                                        }
                                    }
                                    if(isset($data['es_bolsa'])&&$data['es_bolsa']==1){
                                        $datos_bolsa->save(false);
                                        if(isset($data['eliminar_periodo_bolsa'])){
                                            foreach ($data['eliminar_periodo_bolsa'] as $pk_periodo_desligar) {
                                                $periodo_desligar = TblPeriodos::find()->where(['PK_PERIODO'=>$pk_periodo_desligar])->one();

                                                $documento_odc= TblDocumentos::find()->where(['PK_DOCUMENTO'=>$periodo_desligar->FK_DOCUMENTO_ODC])->one();
                                                $documento_odc->delete();

                                                $documento_hde= TblDocumentos::find()->where(['PK_DOCUMENTO'=>$periodo_desligar->FK_DOCUMENTO_HDE])->one();
                                                $documento_hde->delete();

                                                $datos_bolsa->HORAS_DISPONIBLES = $datos_bolsa->HORAS_DISPONIBLES + $periodo_desligar->HORAS;
                                                $datos_bolsa->MONTO_DISPONIBLE =  $datos_bolsa->HORAS_DISPONIBLES * $datos_bolsa->TARIFA;

                                                $datos_bolsa->save(false);

                                                $periodo_desligar->FK_DOCUMENTO_ODC=null;
                                                $periodo_desligar->FK_DOCUMENTO_HDE=null;
                                                $periodo_desligar->TARIFA_HDE=null;
                                                $periodo_desligar->HORAS_HDE=null;
                                                $periodo_desligar->MONTO_HDE=null;
                                                $periodo_desligar->save(false);
                                            }
                                        }
                                    }

                                    //Bitacora para Periodos impactados por Carga/Modificación de Documento ODC.
                                    $descripcionBitacora = 'FK_PERIODOS_IMPACTADOS= '.$periodosImpactados.' EN REGISTRO_AFECTADO SE INSERTA EL VALOR DE -PK_ASIGNACION-';
                                    user_log_bitacora($descripcionBitacora,'Periodos Impactados',$datos['id']);
                                }
                            }
                            $this->redirect(['asignaciones/view','id'=>$datos['id']]);
        }

}
