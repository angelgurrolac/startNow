<?php

namespace app\controllers;

use Yii;
use app\models\SubirArchivo;
use app\models\TblCatPuestos;
use app\models\TblCatUbicaciones;
use app\models\tblcatpaises;
use app\models\TblCatUbicacionRazonSocial;
use app\models\tblperfilempleados;
use app\models\TblDomicilios;
use app\models\TblEmpleados;
use app\models\TblCatGenero;
use app\models\TblCatRazonSocial;
use app\models\TblCatTipoServicios;
use app\models\TblCatAreas;
use app\models\TblCatAdministradoras;
use app\models\TblDocumentosEmpleados;
use app\models\TblCatTipoContrato;
use app\models\TblCatDuracionTipoServicios;
use app\models\TblCatEstados;
use app\models\TblBitAdministradoraEmpleado;
use app\models\TblCandidatos;
use app\models\TblCandidatoEmpleado;
use app\models\TblContactos;
use app\models\TblBeneficiario;
use app\models\TblVacantesCandidatos;
use app\models\TblEmpleadosMotivos;
use app\models\TblVacantes;
use app\models\TblCatMunicipios;
use app\models\TblCatEstatusRecursos;
use app\models\TblCatRankTecnico;
use app\models\TblCatUnidadesNegocio;
use app\models\TblCatUnidadTrabajo;
use app\models\TblBitComentariosEmpleados;
use app\models\TblBitUnidadNegocioAsig;
use app\models\TblBitUbicacionFisica;
use app\models\TblAsignaciones;
use app\models\TblTareasJobLog;
use app\models\TblVacacionesEmpleado;
use app\models\TblBitVacacionesEmpleado;
use app\models\TblFechasVacaciones;
use app\models\TblBitFechaIngresoEmpleado;
use app\models\TblIncidenciasNomina;
use app\models\TblNombresExcluir;
use app\models\TblProspectosPerfiles;
use app\models\TblProspectos;
use app\models\TblBitProspectos;
use app\models\TblProspectosDocumentos;
use app\models\TblCandidatosDocumentos;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * EmpleadosController implements the CRUD actions for tblempleados model.
 */
class EmpleadosController extends Controller
{
    // public function behaviors()
    // {
    //     return [
    //       'access' => [
    //       'class' => \yii\filters\AccessControl::className(),
    //       'only' => ['index', 'view','create', 'update', 'delete'],
    //       'rules' => [
    //         [
    //           'actions' => ['index', 'view',],
    //           'allow' => true,
    //           'roles' => ['@'],
    //           //'matchCallback' => function ($rule, $action) {
    //           //return PermissionHelpers::requireMinimumRole('Admin') && PermissionHelpers::requireStatus('Active');
    //           //}
    //         ],
    //         [
    //           'actions' => [ 'create', 'update', 'delete'],
    //           'allow' => true,
    //           'roles' => ['@'],
    //           //'matchCallback' => function ($rule, $action) {
    //           //return PermissionHelpers::requireMinimumRole('SuperUser') && PermissionHelpers::requireStatus('Active');
    //          // }
    //         ],
    //       ],
    //     ],
    //         'verbs' => [
    //             'class' => VerbFilter::className(),
    //             'actions' => [
    //                 'delete' => ['post'],
    //             ],
    //         ],
    //     ];
    // }
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
     * Lists all tblempleados models.
     * @return mixed
     */
    public function actionIndex()
    {
      $modelAsignaciones = new TblAsignaciones();
        $tamanio_pagina=18;

        if ($modelAsignaciones->load(Yii::$app->request->post())) {

          $data = Yii::$app->request->post();
          $this->altaAsignacion($data, $modelAsignaciones);

          return $this->redirect(['asignaciones/index']);
        }

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            if(isset($data['emp_reasignar'])){
                foreach ($data['emp_reasignar'] as $key => $value) {
                    if($value != ''){
                        $modelPerfilNuevoJefe = tblperfilempleados::find()->where(['FK_EMPLEADO' => $value])->limit(1)->one();
                        $modelPerfilNuevoJefe->FK_JEFE_DIRECTO = $data['emp_reasignar']['jefe'][$key];
                        $modelPerfilNuevoJefe->save(false);
                    }
                }
            }

            $post=null;
            parse_str($data['data'],$post);
            $nombre =(!empty($post['nombre']))? trim($post['nombre']):'';
            $aPaterno =(!empty($post['aPaterno']))? trim($post['aPaterno']):'';
            $idPuesto =(!empty($post['idPuesto']))? trim($post['idPuesto']):'';
            $idUbicacion =(!empty($post['idUbicacion']))? trim($post['idUbicacion']):'';
            $consultaUbicacionOficinaCliente = '';
            if( !empty($post['ingresoFechaIni']) && !empty($post['ingresoFechaFin'])){
                $ingresoFechaIni  = transform_date(trim($post['ingresoFechaIni']));
                $ingresoFechaFin  = transform_date(trim($post['ingresoFechaFin']));
                $condFechaIngreso = ['between', 'tbl_perfil_empleados.FECHA_INGRESO', $ingresoFechaIni, $ingresoFechaFin];
            } else {
                $ingresoFechaIni = '';
                $ingresoFechaFin = '';
                $condFechaIngreso = [];
            }
            $administradora =(!empty($post['administradora']))? trim($post['administradora']):'';
            $estatusEmpleado =(!empty($post['estatusEmpleado']))? trim($post['estatusEmpleado']):'';
            // $unidadNegocio =(!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):'';

            if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidadNegocio =(!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):'';
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $unidadNegocio = (!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):$unidadesNegocioValidas;
            }else{
                $unidadNegocio = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            }

            $idUbicacionFisica =(!empty($post['idUbicacionFisica']))? trim($post['idUbicacionFisica']):'';
            $datosUbicacionFisica = !empty($idUbicacionFisica) ? TblCatUbicaciones::find()->where(['=','PK_UBICACION', $idUbicacionFisica])->one() : '';
            $ubicacionesPropias = ArrayHelper::map(TblCatUbicaciones::find()->where(['=','PROPIA_CLIENTE', 'PROPIA'])->andWhere(['!=','PK_UBICACION','-1'])->orderBy(['DESC_UBICACION'=>SORT_ASC])->asArray()->all(), 'PK_UBICACION', 'PROPIA_CLIENTE');
            $pksUbicacionesPropias = TblCatUbicaciones::find()->select(['PK_UBICACION'])->where(['=','PROPIA_CLIENTE', 'PROPIA'])->andWhere(['!=','PK_UBICACION','-1'])->column();

            //Oficina Cliente
            if(!empty($datosUbicacionFisica)){
                if($datosUbicacionFisica->PROPIA_CLIENTE == 'cliente' || $datosUbicacionFisica->PK_UBICACION == -1){
                    $consultaUbicacionOficinaCliente = ['NOT IN', 'tbl_perfil_empleados.FK_UBICACION_FISICA', $pksUbicacionesPropias];
                }else{
                    $consultaUbicacionOficinaCliente = ['=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', $idUbicacionFisica];
                }
            }else{
                $consultaUbicacionOficinaCliente = ['>=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', -1];
            }

            //Se agrega IF para que en el index solo se muestre un solo estatus de BAJA, pero se busque por los dos estatus
            if($estatusEmpleado==4){
                unset($estatusEmpleado);
                $estatusEmpleado = array(4,6);
            }
            //Se agrega IF para que en el index solo se muestre un solo estatus de Disponible, pero se busque por los dos estatus
            if($estatusEmpleado==3){
                unset($estatusEmpleado);
                $estatusEmpleado = array(3,101);
            }



            $pagina =(!empty($data['pagina']))? trim($data['pagina']):'';
                $datos = (new \yii\db\Query())
                            ->select('count(tbl_empleados.PK_EMPLEADO) as count')
                            ->from('tbl_empleados')
                            ->join('LEFT JOIN','tbl_perfil_empleados',
                                    'tbl_empleados.PK_EMPLEADO = tbl_perfil_empleados.FK_EMPLEADO')
                            /*->join('LEFT JOIN','tbl_cat_ubicacion_razon_social',
                                    'tbl_perfil_empleados.FK_UBICACION = tbl_cat_ubicacion_razon_social.PK_UBICACION_RAZON_SOCIAL')
                            ->join('LEFT JOIN','tbl_domicilios',
                                    'tbl_empleados.FK_DOMICILIO = tbl_domicilios.PK_DOMICILIO')
                            ->join('LEFT JOIN','tbl_cat_estatus_recursos',
                                    'tbl_perfil_empleados.FK_ESTATUS_RECURSO = tbl_cat_estatus_recursos.PK_ESTATUS_RECURSO')
                            ->join('LEFT JOIN','tbl_cat_ubicaciones',
                                    'tbl_perfil_empleados.FK_UBICACION_FISICA = tbl_cat_ubicaciones.PK_UBICACION')*/
                            ->andFilterWhere(
                                ['and',
                                    ['LIKE', 'tbl_empleados.NOMBRE_EMP', $nombre],
                                    ['LIKE', 'tbl_empleados.APELLIDO_PAT_EMP', $aPaterno],
                                    ['=', 'tbl_perfil_empleados.FK_PUESTO', $idPuesto],
                                    ['IN', 'tbl_perfil_empleados.FK_UNIDAD_NEGOCIO', $unidadNegocio],
                                    ['=', 'tbl_perfil_empleados.FK_UBICACION', $idUbicacion],
                                    //['=', 'tbl_perfil_empleados.FECHA_INGRESO', $ingresoFecha],
                                    $condFechaIngreso,
                                    ['=', 'tbl_perfil_empleados.FK_ADMINISTRADORA', $administradora],
                                    $consultaUbicacionOficinaCliente,
                                    //['=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', $idUbicacionFisica],
                                    (!empty($estatusEmpleado))?['IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:(($nombre||$aPaterno)?['=', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:['NOT IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', [4,6]]),
                                ])
                            ->one()['count'];

            if($datos<$tamanio_pagina){
                $pagina=1;
            }
            $paginas=$datos/$tamanio_pagina;
            if($pagina>$paginas){
                $pagina=(int)$paginas+1;
            }

            $dataProvider = new ActiveDataProvider([
                'query'=>(new \yii\db\Query())
                             ->select([
                                    'tbl_empleados.NOMBRE_EMP',
                                    'tbl_empleados.PK_EMPLEADO',
                                    'tbl_empleados.APELLIDO_PAT_EMP',
                                    'tbl_empleados.APELLIDO_MAT_EMP',
                                    'tbl_perfil_empleados.CV_ORIGINAL',
                                    'tbl_perfil_empleados.CV_EISEI',
                                    'tbl_perfil_empleados.TARIFA',
                                    'tbl_perfil_empleados.SUELDO_NETO',
                                    'tbl_perfil_empleados.SUELDO_DIARIO',
                                    'tbl_perfil_empleados.APORTACION_IMSS',
                                    'tbl_perfil_empleados.APORTACION_INFONAVIT',
                                    'tbl_perfil_empleados.ISR',
                                    '(SELECT COUNT(perfil2.PK_PERFIL) FROM tbl_perfil_empleados perfil2 WHERE tbl_empleados.PK_EMPLEADO = perfil2.FK_JEFE_DIRECTO AND perfil2.FK_ESTATUS_RECURSO NOT IN (4,6)) CANT_EMPLEADOS_A_CARGO',
                                    'A.PORC_COMISION_ADMIN_EMPLEADO',
                                    'tbl_cat_puestos.DESC_PUESTO',
                                    'tbl_cat_ubicacion_razon_social.DESC_UBICACION',
                                    'DATE_FORMAT(tbl_perfil_empleados.FECHA_INGRESO, "%d/%m/%Y") FECHA_INGRESO',
                                    'tbl_domicilios.CELULAR',
                                    'tbl_cat_estatus_recursos.DESC_ESTATUS_RECURSO',
                                    'tbl_perfil_empleados.FK_ESTATUS_RECURSO',
                                    'tbl_empleados.FECHA_NAC_EMP',
                                    'tbl_empleados.FOTO_EMP',
                                    'tbl_cat_ubicaciones.DESC_UBICACION DESC_UBICACION_FISICA',
                                    'tbl_cat_ubicaciones.PK_UBICACION AS PK_UBICACION_FISICA',
                                    'tbl_cat_unidades_negocio.DESC_UNIDAD_NEGOCIO DESC_UNIDAD_NEGOCIO',
                                    'tbl_cat_unidades_negocio.PK_UNIDAD_NEGOCIO',
                                    'CASE
                                        WHEN tbl_perfil_empleados.FK_ESTATUS_RECURSO IN (4,6) THEN (SELECT DATE_FORMAT(MAX(FECHA_BAJA), "%d/%m/%Y")
                                                                                        FROM tbl_bit_comentarios_empleados
                                                                                        WHERE tbl_bit_comentarios_empleados.FK_EMPLEADO = tbl_empleados.PK_EMPLEADO)
                                        ELSE NULL
                                    END FECHA_BAJA'
                                    ])
                            ->from('tbl_empleados')
                            ->join('LEFT JOIN','tbl_perfil_empleados',
                                    'tbl_empleados.PK_EMPLEADO = tbl_perfil_empleados.FK_EMPLEADO')
                            ->join('LEFT JOIN','tbl_cat_puestos',
                                    'tbl_perfil_empleados.FK_PUESTO = tbl_cat_puestos.PK_PUESTO')
                            ->join('LEFT JOIN','tbl_cat_ubicacion_razon_social',
                                    'tbl_perfil_empleados.FK_UBICACION = tbl_cat_ubicacion_razon_social.PK_UBICACION_RAZON_SOCIAL')
                            ->join('LEFT JOIN','tbl_domicilios',
                                    'tbl_empleados.FK_DOMICILIO = tbl_domicilios.PK_DOMICILIO')
                            ->join('LEFT JOIN','tbl_cat_estatus_recursos',
                                    'tbl_perfil_empleados.FK_ESTATUS_RECURSO = tbl_cat_estatus_recursos.PK_ESTATUS_RECURSO')
                            ->join('LEFT JOIN','tbl_cat_ubicaciones',
                                    'tbl_perfil_empleados.FK_UBICACION_FISICA = tbl_cat_ubicaciones.PK_UBICACION')
                            ->join('LEFT JOIN','tbl_cat_unidades_negocio',
                                    'tbl_perfil_empleados.FK_UNIDAD_NEGOCIO = tbl_cat_unidades_negocio.PK_UNIDAD_NEGOCIO')
                            ->join('LEFT JOIN','tbl_cat_administradoras as A' , 'tbl_perfil_empleados.FK_ADMINISTRADORA = A.PK_ADMINISTRADORA')
                            ->andFilterWhere(
                                ['and',
                                    ['LIKE', 'tbl_empleados.NOMBRE_EMP', $nombre],
                                    ['LIKE', 'tbl_empleados.APELLIDO_PAT_EMP', $aPaterno],
                                    ['=', 'tbl_perfil_empleados.FK_PUESTO', $idPuesto],
                                    ['IN', 'tbl_perfil_empleados.FK_UNIDAD_NEGOCIO', $unidadNegocio],
                                    ['=', 'tbl_perfil_empleados.FK_UBICACION', $idUbicacion],
                                    //['=', 'tbl_perfil_empleados.FECHA_INGRESO', $ingresoFecha],
                                    $condFechaIngreso,
                                    ['=', 'tbl_perfil_empleados.FK_ADMINISTRADORA', $administradora],
                                    $consultaUbicacionOficinaCliente,
                                    //['=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', $idUbicacionFisica],
                                    (!empty($estatusEmpleado))?['IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:(($nombre||$aPaterno)?['=', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:['NOT IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', [4,6]]),
                                ])
                            ->orderBy('tbl_empleados.APELLIDO_PAT_EMP, tbl_empleados.APELLIDO_MAT_EMP, tbl_empleados.NOMBRE_EMP asc')
                        ,
                'pagination' => [
                    'pageSize' => $tamanio_pagina,
                    'page' => $pagina-1,
                ],
            ]);

            $resultado=$dataProvider->getModels();
            foreach ($resultado as $key => $value) {
                $resultado[$key]['ANIOS']=CalculaEdad($resultado[$key]['FECHA_NAC_EMP']);
                $resultado[$key]['FECHA_NAC_EMP']= transform_date($resultado[$key]['FECHA_NAC_EMP'],'d/m/Y');

                $modelUbicacionFisica = TblCatUbicaciones::find()->where(['PK_UBICACION' => $resultado[$key]['PK_UBICACION_FISICA']])->limit(1)->one();
                $modelAsignaciones = TblAsignaciones::find()
                                                ->where(['FK_EMPLEADO' => $resultado[$key]['PK_EMPLEADO']])
                                                ->andWhere(['=','FK_ESTATUS_ASIGNACION',2])//Asignacion con estatus 'En Ejecución'
                                                ->one();
                //Cambio Oficina Cliente
                $descUbicacionFisica = '';
                if(!empty($modelUbicacionFisica)){

                    if($resultado[$key]['FK_ESTATUS_RECURSO'] == 2){
                        if($modelUbicacionFisica->PROPIA_CLIENTE == 'cliente'){
                            /*if(isset($modelAsignaciones)){
                                $modelUbicacionFisica->FK_CLIENTE == $modelAsignaciones->FK_CLIENTE
                                    ? $descUbicacionFisica = $modelUbicacionFisica->DESC_UBICACION
                                    : $descUbicacionFisica = 'No se tiene asignada una ubicación del Cliente';
                            }*/
                            $descUbicacionFisica = $modelUbicacionFisica->DESC_UBICACION;
                        }else{
                            $descUbicacionFisica = 'Sin Ubicación Fisica';
                        }
                    }else{
                        if($modelUbicacionFisica->PROPIA_CLIENTE == 'PROPIA' && $modelUbicacionFisica->PK_UBICACION != -1){
                            $descUbicacionFisica = $modelUbicacionFisica->DESC_UBICACION;
                        }else{
                            $descUbicacionFisica = 'Sin Ubicación Fisica';
                        }
                    }
                }
                $resultado[$key]['DESC_UBICACION_FISICA'] = $descUbicacionFisica;

            }


            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'pagina'        => $pagina,
                'data'          => $resultado,
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                'total_registros' => $datos,
                'idUbicacionFisica' => $idUbicacionFisica,
                'consultaUbicacionOficinaCliente' => $consultaUbicacionOficinaCliente,
                // 'Email'         => $respuestaEmail
            );

            return $res;
        }else{

          $modelResponsableOP = (new \yii\db\Query)
            ->select([
                "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
                'e.PK_EMPLEADO',
            ])
            ->from('tbl_empleados e')

            ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
            ->where(['p.FK_AREA'=>'5'])->andwhere(['or',['p.FK_ESTATUS_RECURSO'=>'3'],['p.FK_ESTATUS_RECURSO'=>'5']])
            ->orderBy('nombre_emp DESC')->all();

            $unidadNegocioUsuario = (new \yii\db\Query)
              ->select([
                  'p.FK_UNIDAD_NEGOCIO',
              ])
              ->from('tbl_perfil_empleados p')
              ->where(['p.FK_EMPLEADO'=> user_info()['FK_EMPLEADO']])
              ->one();

             return $this->render('index', [
                'total_paginas' => 0,
                'modelAsignaciones' => $modelAsignaciones,
                'modelResponsableOP' => $modelResponsableOP,
                'unidadNegocioUsuario' => $unidadNegocioUsuario
            ]);

        }
    }

    public function actionIndex_pendientes()
    {
        $tamanio_pagina=18;
        if (Yii::$app->request->isAjax){
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre =(!empty($post['nombre']))? trim($post['nombre']):'';
            $aPaterno =(!empty($post['aPaterno']))? trim($post['aPaterno']):'';
            $aMaterno =(!empty($post['aMaterno']))? trim($post['aMaterno']):'';
            $pagina =(!empty($data['pagina']))? trim($data['pagina']):'';
                            $datos = (new \yii\db\Query())
                            ->select('count(PK_REGISTRO) as count')
                            ->from('tbl_candidato_empleado')
                            ->one()['count'];

            if($datos<$tamanio_pagina){
                $pagina=1;
            }
            $dataProvider = new ActiveDataProvider([
            'query'=>(new \yii\db\Query())
                        ->select([
                                'tbl_candidato_empleado.PK_REGISTRO',
                                'tbl_candidato_empleado.APELLIDO_PATERNO',
                                'tbl_candidato_empleado.APELLIDO_MATERNO',
                                'tbl_candidato_empleado.NOMBRE',
                                'tbl_candidato_empleado.NSS',
                                'tbl_candidato_empleado.RFC',
                                'tbl_candidato_empleado.CURP',
                                //'tbl_candidato_empleado.LUGAR_NACIMIENTO',
                                'tbl_candidato_empleado.FECHA_NACIMIENTO',
                                'tbl_candidato_empleado.EDAD',
                                'tbl_cat_estados.DESC_ESTADO AS ESTADO',
                                'tbl_contactos.TELEFONO',
                                'tbl_candidatos.PK_CANDIDATO',
                                // 'tbl_candidatos_documentos.RUTA_CV',
                                '(CASE WHEN tbl_bit_administradora_empleado.FK_CANDIDATO != "" THEN tbl_vacantes.DESC_VACANTE ELSE "SIN VACANTE"  END ) as VACANTE',
                                // '(CASE WHEN tbl_bit_administradora_empleado.FK_CANDIDATO != "" THEN tbl_candidatos_documentos.RUTA_CV ELSE "SIN CV"  END ) as CV_ORIGINAL',
                                '(CASE WHEN tbl_bit_administradora_empleado.FK_CANDIDATO != "" THEN tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO ELSE "SIN ESTATUS"  END ) as ESTATUS',
                                ])
                        ->distinct()
                        ->from('tbl_candidato_empleado')
                        ->join('LEFT JOIN','tbl_contactos',
                                'tbl_candidato_empleado.FK_CONTACTO = tbl_contactos.PK_ADM_CONTC')
                        ->join('LEFT JOIN', 'tbl_cat_estados',
                                'tbl_contactos.ESTADO = tbl_cat_estados.PK_ESTADO')
                        ->join('LEFT JOIN','tbl_bit_administradora_empleado',
                                'tbl_candidato_empleado.PK_REGISTRO = tbl_bit_administradora_empleado.FK_REGISTRO_ADMON')
                        ->join('LEFT JOIN','tbl_vacantes_candidatos',
                                'tbl_bit_administradora_empleado.FK_CANDIDATO = tbl_vacantes_candidatos.FK_CANDIDATO AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = 5 AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = 3')
                        ->join('LEFT JOIN','tbl_vacantes',
                                'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                        ->join('LEFT JOIN','tbl_candidatos',
                                'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                        ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                                'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                        ->andFilterWhere(
                            ['and',
                                ['LIKE', 'tbl_candidato_empleado.APELLIDO_PATERNO', $aPaterno],
                                ['LIKE', 'tbl_candidato_empleado.APELLIDO_MATERNO', $aMaterno],
                                ['LIKE', 'tbl_candidato_empleado.NOMBRE', $nombre],
                            ]
                        )
                        ->orderBy('tbl_candidato_empleado.APELLIDO_PATERNO, tbl_candidato_empleado.APELLIDO_MATERNO, tbl_candidato_empleado.NOMBRE asc')
                        ,
                        'pagination' => [
                            'pageSize' => $tamanio_pagina,
                            'page' => $pagina-1,
                        ],
            ]);



            $resultado=$dataProvider->getModels();

            foreach ($resultado as $key => $value) {

            $cvs = (new \yii\db\Query())
              ->select([
                'tbl_candidatos_documentos.FK_TIPO_CV',
                'tbl_candidatos_documentos.RUTA_CV',
              ])
              ->from('tbl_candidatos_documentos')
              ->where(['tbl_candidatos_documentos.FK_CANDIDATO' => $resultado[$key]['PK_CANDIDATO']])
              ->andWhere(['NOT IN', 'tbl_candidatos_documentos.FK_TIPO_CV', 3])
              ->all();

              $CVsCandidato = "";

              if (empty($cvs) ) {
                $CVsCandidato = "";
              }
              else {
                foreach ($cvs as $keycvs => $valuecvs) {
                  $pathInfo = pathinfo($cvs[$keycvs]['RUTA_CV']);
                  $lenght = strlen($pathInfo['filename']);
                  $nombreCVP = "";
                      if ($cvs[$keycvs]['FK_TIPO_CV'] == 1) {
                        $nombreCVP = 'CV EISEI';
                      } elseif ($cvs[$keycvs]['FK_TIPO_CV'] == 2) {
                        $nombreCVP = 'CV ORIGINAL';
                      }
                $CVsCandidato .= '<a style="border: solid 1px #ccc; padding:10px; margin-bottom:10px; color: blue;" href="'.$cvs[$keycvs]['RUTA_CV'].'" download>'.$nombreCVP.'';
                }
              }
            $resultado[$key]['CV'] = $CVsCandidato;

            }



            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'pagina'        => $pagina,
                'data'          => $resultado,
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                'total_registros' => $datos,
                'dataProvider' => $dataProvider,
            );

            return $res;
        }else{
            return $this->render('index_pendientes', ['total_paginas' => 0,]);
        }
    }

    public function actionPendientes_cvoriginal()
    {
        $tamanio_pagina=18;
        if (Yii::$app->request->isAjax){
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre =(!empty($post['nombre']))? trim($post['nombre']):'';
            $aPaterno =(!empty($post['aPaterno']))? trim($post['aPaterno']):'';
            $idPuesto =(!empty($post['idPuesto']))? trim($post['idPuesto']):'';
            $idUbicacion =(!empty($post['idUbicacion']))? trim($post['idUbicacion']):'';
            $consultaUbicacionOficinaCliente = '';
            if( !empty($post['ingresoFechaIni']) && !empty($post['ingresoFechaFin'])){
                $ingresoFechaIni  = transform_date(trim($post['ingresoFechaIni']));
                $ingresoFechaFin  = transform_date(trim($post['ingresoFechaFin']));
                $condFechaIngreso = ['between', 'tbl_perfil_empleados.FECHA_INGRESO', $ingresoFechaIni, $ingresoFechaFin];
            } else {
                $ingresoFechaIni = '';
                $ingresoFechaFin = '';
                $condFechaIngreso = [];
            }
            $administradora =(!empty($post['administradora']))? trim($post['administradora']):'';
            $estatusEmpleado =(!empty($post['estatusEmpleado']))? trim($post['estatusEmpleado']):'';
            // $unidadNegocio =(!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):'';

            if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidadNegocio =(!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):'';
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $unidadNegocio = (!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):$unidadesNegocioValidas;
            }else{
                $unidadNegocio = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            }

            $idUbicacionFisica =(!empty($post['idUbicacionFisica']))? trim($post['idUbicacionFisica']):'';

            //Oficina Cliente
            if(!empty($idUbicacionFisica)){
                if($idUbicacionFisica != 3 && $idUbicacionFisica != 4 && $idUbicacionFisica != 5 && $idUbicacionFisica != 6 && $idUbicacionFisica != 7 &&
                    $idUbicacionFisica != 8 && $idUbicacionFisica != 18 && $idUbicacionFisica != 19 && $idUbicacionFisica != 41){
                    $consultaUbicacionOficinaCliente = ['NOT IN', 'tbl_perfil_empleados.FK_UBICACION_FISICA', [3,4,5,6,7,8,18,19,41]];
                }
                else{
                    $consultaUbicacionOficinaCliente = ['=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', $idUbicacionFisica];
                }
            }else{
                $consultaUbicacionOficinaCliente = ['>=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', -1];
            }

            //Se agrega IF para que en el index solo se muestre un solo estatus de BAJA, pero se busque por los dos estatus
            if($estatusEmpleado==4){
                unset($estatusEmpleado);
                $estatusEmpleado = array(4,6);
            }

            $pagina =(!empty($data['pagina']))? trim($data['pagina']):'';

            $datos = (new \yii\db\Query())
                            ->select('count(tbl_empleados.PK_EMPLEADO) as count')
                            ->from('tbl_empleados')
                            ->join('LEFT JOIN','tbl_perfil_empleados',
                                    'tbl_empleados.PK_EMPLEADO = tbl_perfil_empleados.FK_EMPLEADO')
                            /*->join('LEFT JOIN','tbl_cat_ubicacion_razon_social',
                                    'tbl_perfil_empleados.FK_UBICACION = tbl_cat_ubicacion_razon_social.PK_UBICACION_RAZON_SOCIAL')
                            ->join('LEFT JOIN','tbl_domicilios',
                                    'tbl_empleados.FK_DOMICILIO = tbl_domicilios.PK_DOMICILIO')
                            ->join('LEFT JOIN','tbl_cat_estatus_recursos',
                                    'tbl_perfil_empleados.FK_ESTATUS_RECURSO = tbl_cat_estatus_recursos.PK_ESTATUS_RECURSO')
                            ->join('LEFT JOIN','tbl_cat_ubicaciones',
                                    'tbl_perfil_empleados.FK_UBICACION_FISICA = tbl_cat_ubicaciones.PK_UBICACION')*/
                            ->andFilterWhere(
                                ['and',
                                    ['LIKE', 'tbl_empleados.NOMBRE_EMP', $nombre],
                                    ['LIKE', 'tbl_empleados.APELLIDO_PAT_EMP', $aPaterno],
                                    ['=', 'tbl_perfil_empleados.FK_PUESTO', $idPuesto],
                                    ['IN', 'tbl_perfil_empleados.FK_UNIDAD_NEGOCIO', $unidadNegocio],
                                    ['=', 'tbl_perfil_empleados.FK_UBICACION', $idUbicacion],
                                    //['=', 'tbl_perfil_empleados.FECHA_INGRESO', $ingresoFecha],
                                    $condFechaIngreso,
                                    ['=', 'tbl_perfil_empleados.FK_ADMINISTRADORA', $administradora],
                                    //['=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', $idUbicacionFisica],
                                    $consultaUbicacionOficinaCliente,
                                    (!empty($estatusEmpleado))?['IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:(($nombre||$aPaterno)?['=', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:['NOT IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', [4,6]]),
                                ])
                                ->andWhere(
                                ['or',
                                    ['IS','tbl_perfil_empleados.CV_ORIGINAL',null],
                                    ['=','tbl_perfil_empleados.CV_ORIGINAL',''],
                                ])
                            ->one()['count'];

            if($datos<$tamanio_pagina){
                $pagina=1;
            }

            $dataProvider = new ActiveDataProvider([
            'query'=>(new \yii\db\Query())
                        ->select([
                                    'tbl_empleados.NOMBRE_EMP',
                                    'tbl_empleados.PK_EMPLEADO',
                                    'tbl_empleados.APELLIDO_PAT_EMP',
                                    'tbl_empleados.APELLIDO_MAT_EMP',
                                    'tbl_perfil_empleados.CV_ORIGINAL',
                                    'tbl_perfil_empleados.CV_EISEI',
                                    'tbl_cat_puestos.DESC_PUESTO',
                                    'tbl_cat_ubicacion_razon_social.DESC_UBICACION',
                                    'DATE_FORMAT(tbl_perfil_empleados.FECHA_INGRESO, "%d/%m/%Y") FECHA_INGRESO',
                                    'tbl_domicilios.CELULAR',
                                    'tbl_cat_estatus_recursos.DESC_ESTATUS_RECURSO',
                                    'tbl_perfil_empleados.FK_ESTATUS_RECURSO',
                                    'tbl_empleados.FECHA_NAC_EMP',
                                    'tbl_empleados.FOTO_EMP',
                                    'tbl_cat_ubicaciones.DESC_UBICACION DESC_UBICACION_FISICA',
                                    'tbl_cat_unidades_negocio.DESC_UNIDAD_NEGOCIO DESC_UNIDAD_NEGOCIO',
                                    'CASE
                                        WHEN tbl_perfil_empleados.FK_ESTATUS_RECURSO IN (4,6) THEN (SELECT DATE_FORMAT(MAX(FECHA_BAJA), "%d/%m/%Y")
                                                                                        FROM tbl_bit_comentarios_empleados
                                                                                        WHERE tbl_bit_comentarios_empleados.FK_EMPLEADO = tbl_empleados.PK_EMPLEADO)
                                        ELSE NULL
                                    END FECHA_BAJA'
                                    ])
                            ->from('tbl_empleados')
                            ->join('LEFT JOIN','tbl_perfil_empleados',
                                    'tbl_empleados.PK_EMPLEADO = tbl_perfil_empleados.FK_EMPLEADO')
                            ->join('LEFT JOIN','tbl_cat_puestos',
                                    'tbl_perfil_empleados.FK_PUESTO = tbl_cat_puestos.PK_PUESTO')
                            ->join('LEFT JOIN','tbl_cat_ubicacion_razon_social',
                                    'tbl_perfil_empleados.FK_UBICACION = tbl_cat_ubicacion_razon_social.PK_UBICACION_RAZON_SOCIAL')
                            ->join('LEFT JOIN','tbl_domicilios',
                                    'tbl_empleados.FK_DOMICILIO = tbl_domicilios.PK_DOMICILIO')
                            ->join('LEFT JOIN','tbl_cat_estatus_recursos',
                                    'tbl_perfil_empleados.FK_ESTATUS_RECURSO = tbl_cat_estatus_recursos.PK_ESTATUS_RECURSO')
                            ->join('LEFT JOIN','tbl_cat_ubicaciones',
                                    'tbl_perfil_empleados.FK_UBICACION_FISICA = tbl_cat_ubicaciones.PK_UBICACION')
                            ->join('LEFT JOIN','tbl_cat_unidades_negocio',
                                    'tbl_perfil_empleados.FK_UNIDAD_NEGOCIO = tbl_cat_unidades_negocio.PK_UNIDAD_NEGOCIO')
                            ->andFilterWhere(
                                ['and',
                                    ['LIKE', 'tbl_empleados.NOMBRE_EMP', $nombre],
                                    ['LIKE', 'tbl_empleados.APELLIDO_PAT_EMP', $aPaterno],
                                    ['=', 'tbl_perfil_empleados.FK_PUESTO', $idPuesto],
                                    ['IN', 'tbl_perfil_empleados.FK_UNIDAD_NEGOCIO', $unidadNegocio],
                                    ['=', 'tbl_perfil_empleados.FK_UBICACION', $idUbicacion],
                                    //['=', 'tbl_perfil_empleados.FECHA_INGRESO', $ingresoFecha],
                                    $condFechaIngreso,
                                    ['=', 'tbl_perfil_empleados.FK_ADMINISTRADORA', $administradora],
                                    $consultaUbicacionOficinaCliente,
                                    //['=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', $idUbicacionFisica],
                                    (!empty($estatusEmpleado))?['IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:(($nombre||$aPaterno)?['=', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:['NOT IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', [4,6]]),
                                ])
                            ->andFilterWhere(
                                ['or',
                                    ['=','tbl_perfil_empleados.CV_ORIGINAL',null],
                                    ['=','tbl_perfil_empleados.CV_ORIGINAL',''],
                                ])
                            ->andWhere(
                                ['or',
                                    ['IS','tbl_perfil_empleados.CV_ORIGINAL',null],
                                    ['=','tbl_perfil_empleados.CV_ORIGINAL',''],
                                ])

                            ->orderBy('tbl_empleados.APELLIDO_PAT_EMP, tbl_empleados.APELLIDO_MAT_EMP, tbl_empleados.NOMBRE_EMP asc')
                        ,
                        'pagination' => [
                            'pageSize' => $tamanio_pagina,
                            'page' => $pagina-1,
                        ],
            ]);
            $resultado=$dataProvider->getModels();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'pagina'        => $pagina,
                'data'          => $resultado,
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                'total_registros' => $datos,
                'dataProvider' => $dataProvider,
            );

            return $res;
        }else{
            return $this->render('pendientes_cvoriginal', ['total_paginas' => 0,]);
        }
    }

    //Inicio nuevo accion para nuevos ingresos considerados para asignacion
    public function actionIndex_asignables()
    {
        $modelAsignaciones = new TblAsignaciones();

        $tamanio_pagina=18;

        if ($modelAsignaciones->load(Yii::$app->request->post())) {

          $data = Yii::$app->request->post();
          $this->altaAsignacion($data, $modelAsignaciones);

          return $this->redirect(['empleados/index_asignables']);
        }

        if (Yii::$app->request->isAjax){
            $data = Yii::$app->request->post();

            if(isset($data['emp_reasignar'])){
                foreach ($data['emp_reasignar'] as $key => $value) {
                    if($value != ''){
                        $modelPerfilNuevoJefe = tblperfilempleados::find()->where(['FK_EMPLEADO' => $value])->limit(1)->one();
                        $modelPerfilNuevoJefe->FK_JEFE_DIRECTO = $data['emp_reasignar']['jefe'][$key];
                        $modelPerfilNuevoJefe->save(false);
                    }
                }
            }

            $post=null;
            parse_str($data['data'],$post);
            $nombre =(!empty($post['nombre']))? trim($post['nombre']):'';
            $aPaterno =(!empty($post['aPaterno']))? trim($post['aPaterno']):'';
            $idPuesto =(!empty($post['idPuesto']))? trim($post['idPuesto']):'';
            $idUbicacion =(!empty($post['idUbicacion']))? trim($post['idUbicacion']):'';
            $consultaUbicacionOficinaCliente = '';
            if( !empty($post['ingresoFechaIni']) && !empty($post['ingresoFechaFin'])){
                $ingresoFechaIni  = transform_date(trim($post['ingresoFechaIni']));
                $ingresoFechaFin  = transform_date(trim($post['ingresoFechaFin']));
                $condFechaIngreso = ['between', 'tbl_perfil_empleados.FECHA_INGRESO', $ingresoFechaIni, $ingresoFechaFin];
            } else {
                $ingresoFechaIni = '';
                $ingresoFechaFin = '';
                $condFechaIngreso = [];
            }
            $administradora =(!empty($post['administradora']))? trim($post['administradora']):'';
            $estatusEmpleado =(!empty($post['estatusEmpleado']))? trim($post['estatusEmpleado']):'';
            // $unidadNegocio =(!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):'';

            if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
                $unidadNegocio =(!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):'';
            }elseif(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])){
                $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
                $unidadNegocio = (!empty($post['unidadNegocio']))? trim($post['unidadNegocio']):$unidadesNegocioValidas;
            }else{
                $unidadNegocio = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
            }

            $idUbicacionFisica =(!empty($post['idUbicacionFisica']))? trim($post['idUbicacionFisica']):'';

            //Oficina Cliente
            if(!empty($idUbicacionFisica)){
                if($idUbicacionFisica != 3 && $idUbicacionFisica != 4 && $idUbicacionFisica != 5 && $idUbicacionFisica != 6 && $idUbicacionFisica != 7 &&
                    $idUbicacionFisica != 8 && $idUbicacionFisica != 18 && $idUbicacionFisica != 19 && $idUbicacionFisica != 41){
                    $consultaUbicacionOficinaCliente = ['NOT IN', 'tbl_perfil_empleados.FK_UBICACION_FISICA', [3,4,5,6,7,8,18,19,41]];
                }
                else{
                    $consultaUbicacionOficinaCliente = ['=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', $idUbicacionFisica];
                }
            }else{
                $consultaUbicacionOficinaCliente = ['>=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', -1];
            }

            //Se agrega IF para que en el index solo se muestre un solo estatus de BAJA, pero se busque por los dos estatus
            if($estatusEmpleado==4){
                unset($estatusEmpleado);
                $estatusEmpleado = array(4,6);
            }

            $pagina =(!empty($data['pagina']))? trim($data['pagina']):'';

            $datos = (new \yii\db\Query())
                            ->select('count(tbl_empleados.PK_EMPLEADO) as count')
                            ->from('tbl_empleados')
                            ->join('LEFT JOIN','tbl_perfil_empleados',
                                    'tbl_empleados.PK_EMPLEADO = tbl_perfil_empleados.FK_EMPLEADO')
                            /*->join('LEFT JOIN','tbl_cat_ubicacion_razon_social',
                                    'tbl_perfil_empleados.FK_UBICACION = tbl_cat_ubicacion_razon_social.PK_UBICACION_RAZON_SOCIAL')
                            ->join('LEFT JOIN','tbl_domicilios',
                                    'tbl_empleados.FK_DOMICILIO = tbl_domicilios.PK_DOMICILIO')
                            ->join('LEFT JOIN','tbl_cat_estatus_recursos',
                                    'tbl_perfil_empleados.FK_ESTATUS_RECURSO = tbl_cat_estatus_recursos.PK_ESTATUS_RECURSO')
                            ->join('LEFT JOIN','tbl_cat_ubicaciones',
                                    'tbl_perfil_empleados.FK_UBICACION_FISICA = tbl_cat_ubicaciones.PK_UBICACION')*/
                            ->andFilterWhere(
                                ['and',
                                    ['LIKE', 'tbl_empleados.NOMBRE_EMP', $nombre],
                                    ['LIKE', 'tbl_empleados.APELLIDO_PAT_EMP', $aPaterno],
                                    ['=', 'tbl_perfil_empleados.FK_PUESTO', $idPuesto],
                                    ['IN', 'tbl_perfil_empleados.FK_UNIDAD_NEGOCIO', $unidadNegocio],
                                    ['=', 'tbl_perfil_empleados.FK_UBICACION', $idUbicacion],
                                    //['=', 'tbl_perfil_empleados.FECHA_INGRESO', $ingresoFecha],
                                    $condFechaIngreso,
                                    ['=', 'tbl_perfil_empleados.FK_ADMINISTRADORA', $administradora],
                                    //['=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', $idUbicacionFisica],
                                    $consultaUbicacionOficinaCliente,
                                    (!empty($estatusEmpleado))?['IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:(($nombre||$aPaterno)?['=', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:['NOT IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', [4,6]]),
                                ])
                                ->andWhere(
                                ['or',
                                    ['=','tbl_perfil_empleados.FK_ESTATUS_RECURSO',101],
                                ])
                            ->one()['count'];

            if($datos<$tamanio_pagina){
                $pagina=1;
            }

            $dataProvider = new ActiveDataProvider([
            'query'=>(new \yii\db\Query())
                        ->select([
                                    'tbl_empleados.NOMBRE_EMP',
                                    'tbl_empleados.PK_EMPLEADO',
                                    'tbl_empleados.APELLIDO_PAT_EMP',
                                    'tbl_empleados.APELLIDO_MAT_EMP',
                                    'tbl_perfil_empleados.CV_ORIGINAL',
                                    'tbl_perfil_empleados.CV_EISEI',
                                    'tbl_perfil_empleados.TARIFA',
                                    'tbl_perfil_empleados.SUELDO_NETO',
                                    'tbl_perfil_empleados.SUELDO_DIARIO',
                                    'tbl_perfil_empleados.APORTACION_IMSS',
                                    'tbl_perfil_empleados.APORTACION_INFONAVIT',
                                    'tbl_perfil_empleados.ISR',
                                    '(SELECT COUNT(perfil2.PK_PERFIL) FROM tbl_perfil_empleados perfil2 WHERE tbl_empleados.PK_EMPLEADO = perfil2.FK_JEFE_DIRECTO AND perfil2.FK_ESTATUS_RECURSO NOT IN (4,6)) CANT_EMPLEADOS_A_CARGO',
                                    'A.PORC_COMISION_ADMIN_EMPLEADO',
                                    'tbl_cat_puestos.DESC_PUESTO',
                                    'tbl_cat_ubicacion_razon_social.DESC_UBICACION',
                                    'DATE_FORMAT(tbl_perfil_empleados.FECHA_INGRESO, "%d/%m/%Y") FECHA_INGRESO',
                                    'tbl_domicilios.CELULAR',
                                    'tbl_cat_estatus_recursos.DESC_ESTATUS_RECURSO',
                                    'tbl_perfil_empleados.FK_ESTATUS_RECURSO',
                                    'tbl_empleados.FECHA_NAC_EMP',
                                    'tbl_empleados.FOTO_EMP',
                                    'tbl_cat_ubicaciones.DESC_UBICACION DESC_UBICACION_FISICA',
                                    'tbl_cat_unidades_negocio.PK_UNIDAD_NEGOCIO',
                                    'tbl_cat_unidades_negocio.DESC_UNIDAD_NEGOCIO DESC_UNIDAD_NEGOCIO',
                                    'CASE
                                        WHEN tbl_perfil_empleados.FK_ESTATUS_RECURSO IN (4,6) THEN (SELECT DATE_FORMAT(MAX(FECHA_BAJA), "%d/%m/%Y")
                                                                                        FROM tbl_bit_comentarios_empleados
                                                                                        WHERE tbl_bit_comentarios_empleados.FK_EMPLEADO = tbl_empleados.PK_EMPLEADO)
                                        ELSE NULL
                                    END FECHA_BAJA'
                                    ])
                            ->from('tbl_empleados')
                            ->join('LEFT JOIN','tbl_perfil_empleados',
                                    'tbl_empleados.PK_EMPLEADO = tbl_perfil_empleados.FK_EMPLEADO')
                            ->join('LEFT JOIN','tbl_cat_puestos',
                                    'tbl_perfil_empleados.FK_PUESTO = tbl_cat_puestos.PK_PUESTO')
                            ->join('LEFT JOIN','tbl_cat_ubicacion_razon_social',
                                    'tbl_perfil_empleados.FK_UBICACION = tbl_cat_ubicacion_razon_social.PK_UBICACION_RAZON_SOCIAL')
                            ->join('LEFT JOIN','tbl_domicilios',
                                    'tbl_empleados.FK_DOMICILIO = tbl_domicilios.PK_DOMICILIO')
                            ->join('LEFT JOIN','tbl_cat_estatus_recursos',
                                    'tbl_perfil_empleados.FK_ESTATUS_RECURSO = tbl_cat_estatus_recursos.PK_ESTATUS_RECURSO')
                            ->join('LEFT JOIN','tbl_cat_ubicaciones',
                                    'tbl_perfil_empleados.FK_UBICACION_FISICA = tbl_cat_ubicaciones.PK_UBICACION')
                            ->join('LEFT JOIN','tbl_cat_unidades_negocio',
                                    'tbl_perfil_empleados.FK_UNIDAD_NEGOCIO = tbl_cat_unidades_negocio.PK_UNIDAD_NEGOCIO')
                            ->join('LEFT JOIN','tbl_cat_administradoras as A' , 'tbl_perfil_empleados.FK_ADMINISTRADORA = A.PK_ADMINISTRADORA')
                            ->andFilterWhere(
                                ['and',
                                    ['LIKE', 'tbl_empleados.NOMBRE_EMP', $nombre],
                                    ['LIKE', 'tbl_empleados.APELLIDO_PAT_EMP', $aPaterno],
                                    ['=', 'tbl_perfil_empleados.FK_PUESTO', $idPuesto],
                                    ['IN', 'tbl_perfil_empleados.FK_UNIDAD_NEGOCIO', $unidadNegocio],
                                    ['=', 'tbl_perfil_empleados.FK_UBICACION', $idUbicacion],
                                    //['=', 'tbl_perfil_empleados.FECHA_INGRESO', $ingresoFecha],
                                    $condFechaIngreso,
                                    ['=', 'tbl_perfil_empleados.FK_ADMINISTRADORA', $administradora],
                                    $consultaUbicacionOficinaCliente,
                                    //['=', 'tbl_perfil_empleados.FK_UBICACION_FISICA', $idUbicacionFisica],
                                    (!empty($estatusEmpleado))?['IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:(($nombre||$aPaterno)?['=', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', $estatusEmpleado]:['NOT IN', 'tbl_perfil_empleados.FK_ESTATUS_RECURSO', [4,6]]),
                                ])
                            ->andFilterWhere(
                                ['or',
                                    ['=','tbl_perfil_empleados.FK_ESTATUS_RECURSO',101],
                                ])

                            ->orderBy('tbl_empleados.APELLIDO_PAT_EMP, tbl_empleados.APELLIDO_MAT_EMP, tbl_empleados.NOMBRE_EMP asc')
                        ,
                        'pagination' => [
                            'pageSize' => $tamanio_pagina,
                            'page' => $pagina-1,
                        ],
            ]);

            $resultado=$dataProvider->getModels();

            foreach ($resultado as $key => $value) {
                $resultado[$key]['ANIOS']=CalculaEdad($resultado[$key]['FECHA_NAC_EMP']);
                $resultado[$key]['FECHA_NAC_EMP']= transform_date($resultado[$key]['FECHA_NAC_EMP'],'d/m/Y');
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'pagina'        => $pagina,
                'data'          => $resultado,
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                'total_registros' => $datos,
                'dataProvider' => $dataProvider,
            );

            return $res;
        }else{

            $modelResponsableOP = (new \yii\db\Query)
            ->select([
                "concat_ws(' ',e.NOMBRE_EMP,e.APELLIDO_PAT_EMP,e.APELLIDO_MAT_EMP) nombre_emp",
                'e.PK_EMPLEADO',
            ])
            ->from('tbl_empleados e')

            ->join('inner join', 'tbl_perfil_empleados p','e.PK_EMPLEADO =  p.FK_EMPLEADO')
            ->where(['p.FK_AREA'=>'5'])->andwhere(['or',['p.FK_ESTATUS_RECURSO'=>'3'],['p.FK_ESTATUS_RECURSO'=>'5']])
            ->orderBy('nombre_emp DESC')->all();

            return $this->render('index_asignables', [
                'total_paginas' => 0,
                'modelAsignaciones' => $modelAsignaciones,
                'modelResponsableOP' => $modelResponsableOP,
                ]);
        }
    }
    //Fin nuevo accion para nuevos ingresos considerados para asignacion

    public function altaAsignacion($data, $modelAsignaciones){

      $url = server().get_upload_url();
      $fkEmpleado = $data['FkEmpleado'];
      $perfil= tblperfilempleados::find()->where(['FK_EMPLEADO'=>$fkEmpleado])->limit(1)->one();

      $modelAsignaciones->FECHA_INI = transform_date($modelAsignaciones->FECHA_INI,'Y-m-d');
      if ($modelAsignaciones->FECHA_FIN != '') {
        $modelAsignaciones->FECHA_FIN = transform_date($modelAsignaciones->FECHA_FIN,'Y-m-d');
        $modelAsignaciones->HORAS = $data['horasPeriodo'];
        $modelAsignaciones->MONTO = $data['totalMonto'];
      } else{
        $modelAsignaciones->FECHA_FIN = '';
          $modelAsignaciones->HORAS = 0;
      }

      $TarifaHora= $data['tarifa'][$fkEmpleado];

      $modelAsignaciones->FK_OPERACION = 23;
      $modelAsignaciones->FK_USUARIO = user_info()['PK_USUARIO'];
      $modelAsignaciones->FK_EMPLEADO = $fkEmpleado;
      $modelAsignaciones->TARIFA = quitarFormatoMoneda($TarifaHora);
      $modelAsignaciones->FK_CAT_TARIFA = (isset($data['pk_cat_tarifa_select'][$fkEmpleado])?$data['pk_cat_tarifa_select'][$fkEmpleado]:null);
      $modelAsignaciones->FECHA_REGISTRO = date('Y-m-d');

      $date1 = str_replace('/','-',$modelAsignaciones->FECHA_INI);
      $date1_fin = str_replace('/','-',$modelAsignaciones->FECHA_FIN);
      $date2 = date('Y-m-d');
      $fecha_inicio = strtotime($date1);
      $fecha_fin = strtotime($date1_fin);
      $actual = strtotime($date2);

      if($fecha_inicio > $actual && $perfil->FK_ESTATUS_RECURSO != 1){
        $modelAsignaciones->FK_ESTATUS_ASIGNACION = 1;
      }
      if ($fecha_inicio <= $actual && $fecha_fin >= $actual && $perfil->FK_ESTATUS_RECURSO != 1){
        $modelAsignaciones->FK_ESTATUS_ASIGNACION = 2;
      }
      if ($fecha_inicio <= $actual && $fecha_fin == '' && $perfil->FK_ESTATUS_RECURSO != 1){
        $modelAsignaciones->FK_ESTATUS_ASIGNACION = 2;
      }

      //Validación empleado en proyecto y envio de correo al Jefe directo del recurso
      if($perfil->FK_ESTATUS_RECURSO == 1){
          $modelAsignaciones->FK_ESTATUS_ASIGNACION = 7;
          $perfil->FK_ESTATUS_RECURSO = 2;

          //Consulta Nombre y Correo jefe directo
          $correo = (new \yii\db\Query())
            ->select([
            'emp.EMAIL_INTERNO',
            'CONCAT(emp.NOMBRE_EMP," ",emp.APELLIDO_PAT_EMP," ",emp.APELLIDO_MAT_EMP) AS NOMBRE'
          ])
            ->from('tbl_empleados emp')
            ->where(['emp.PK_EMPLEADO' => $perfil['FK_JEFE_DIRECTO']])
            ->one();

          $nombreJefe = $correo['NOMBRE'];
          $destino = $correo['EMAIL_INTERNO'];
          $nameEmisor     = 'EISEI Innovation';
          $emailEmisor    = get_config('Asignaciones','CORREO_REMITENTE_ASIGNABLE');
          $emailDestino   = $destino; //cambiar por $destino en QA
          $asunto         = 'Notificación de asignación pendientes de aprobar';
          $mensaje        = "<style>p {font-family: Calibri; font-size: 12pt;}</style>
          <p>Buen día, $nombreJefe <br><br>
          Existen nuevas asignaciones que requieren de su aprobación <br><br>
          <a href='$url/web/empleados/index_asignables' style='color: #337ab7;'>Ver asignaciones pendientes de aprobar</a>
          <br><br>
          Saludos...</p><br><br>
          <img src='$url/web/iconos/correos/firmaErt.jpg'>";

          $respuestaEmail = send_mail($emailEmisor, $emailDestino, $asunto, $mensaje, []);
      } if ($perfil->FK_ESTATUS_RECURSO == 3 || $perfil->FK_ESTATUS_RECURSO == 101) {

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
          $DestinatarioAlta = $correoAlta['EMAIL_INTERNO'];

          $nombreAsignacion = $modelAsignaciones->NOMBRE;
          $nameEmisor     = 'EISEI Innovation';
          $emailEmisor    = get_config('Asignaciones','CORREO_REMITENTE_ASIGNABLE');
          $emailDestino   = $DestinatarioAlta; //cambiar por $destino en QA
          $asunto         = 'Notificación de nueva asignación';
          $mensaje        = "<style>p {font-family: Calibri; font-size: 12pt;}</style>
          <p>Buen día, $Administrador <br><br>
          Se registraron nuevas asignaciones <br><br>
          Nombre de asignación: <b>$nombreAsignacion</b> <br><br>
          <a href='$url/web/asignaciones/index' style='color: #337ab7;'>Ver consulta general de asignaciones</a>
          <br><br>
          Saludos...</p><br><br>
          <img src='$url/web/iconos/correos/firmaErt.jpg'>";

          $respuestaEmail = send_mail($emailEmisor, $emailDestino, $asunto, $mensaje, []);

          $perfil->FK_ESTATUS_RECURSO = 2;
      }

      $modelAsignaciones->save(false);
      $perfil->save(false);

      if($modelAsignaciones->FK_ESTATUS_ASIGNACION != 7){
          $modelBitUnidadNegocioAsigN = new TblBitUnidadNegocioAsig();
          $modelBitUnidadNegocioAsigN->FK_REGISTRO = $modelAsignaciones->PK_ASIGNACION;
          if(!empty($modelAsignaciones->FK_UNIDAD_NEGOCIO)){
              $modelBitUnidadNegocioAsigN->FK_UNIDAD_NEGOCIO = $modelAsignaciones->FK_UNIDAD_NEGOCIO;
          }

          $modelBitUnidadNegocioAsigN->FK_EMPLEADO=$perfil->FK_EMPLEADO;
          $modelBitUnidadNegocioAsigN->MODULO_ORIGEN = onToy();
          $modelBitUnidadNegocioAsigN->FK_USUARIO = user_info()['PK_USUARIO'];
          $modelBitUnidadNegocioAsigN->FECHA_CREACION=date('Y-m-d H:i:s');
          $modelBitUnidadNegocioAsigN->save(false);

      }

      $descripcionBitacora = 'FK_CLIENTE='.$modelAsignaciones->FK_CLIENTE.',NOMBRE='.$modelAsignaciones->NOMBRE.',FK_UBICACION='.$modelAsignaciones->FK_UBICACION.',FK_PROYECTO='.$modelAsignaciones->FK_PROYECTO;
      user_log_bitacora($descripcionBitacora,'Registro de Asignación',$modelAsignaciones->PK_ASIGNACION );
      /* Condición para sólo cuando la asignación tenga estats => En Ejecución se afecte al empleado[REEMPLAZANTE]
         en su valores de FK_ESTATUS_RECURSO, FK_UBICACION_FISICA y FK_UNIDAD_NEGOCIO */
      if($modelAsignaciones->FK_ESTATUS_ASIGNACION == 2){
          user_log_bitacora_estatus_empleado($perfil->FK_EMPLEADO,$perfil->FK_ESTATUS_RECURSO);
          user_log_bitacora_unidad_negocio($perfil->FK_EMPLEADO,$perfil->FK_UNIDAD_NEGOCIO,$modelAsignaciones->PK_ASIGNACION);
          user_log_bitacora_ubicacion_fisica($perfil->FK_EMPLEADO,$perfil->PK_PERFIL,$modelAsignaciones->FK_UBICACION,'CLIENTE');
      }

    }

    //Funcion para buscar y llenar modal de remplazo
        public function actionModal(){

            $connection = \Yii::$app->db;

            $data = Yii::$app->request->post();
            $pk_a = $data['id'];
            $pkEmp_user = $data['pkEmpleado_user'];

            $query = $connection->createCommand("SELECT a.NOMBRE ,
                                                (   SELECT CONCAT(em.NOMBRE_EMP,' ',em.APELLIDO_PAT_EMP,' ',em.APELLIDO_MAT_EMP)
                                                    FROM tbl_empleados em
                                                    INNER JOIN tbl_asignaciones ass
                                                    ON ass.FK_RESPONSABLE_OP = em.PK_EMPLEADO
                                                    WHERE ass.PK_ASIGNACION =".($pk_a)."
                                                ) AS NOMBRE_RESPONSABLE,
                                                (   SELECT em.PK_EMPLEADO
                                                    FROM tbl_empleados em
                                                    INNER JOIN tbl_asignaciones ass
                                                    ON ass.FK_RESPONSABLE_OP = em.PK_EMPLEADO
                                                    WHERE ass.PK_ASIGNACION =".($pk_a)."
                                                ) AS PK_RESPONSABLE,
                                                a.TARIFA,
                                                c.PK_CLIENTE,
                                                c.NOMBRE_CLIENTE,
                                                c.HORAS_ASIGNACION,
                                                cont.PK_CONTACTO,
                                                cont.NOMBRE_CONTACTO,
                                                u.PK_UBICACION,
                                                u.DESC_UBICACION,
                                                un.PK_UNIDAD_NEGOCIO,
                                                un.DESC_UNIDAD_NEGOCIO,
                                                a.FECHA_INI,
                                                a.FECHA_FIN,
                                                a.FK_ESTATUS_ASIGNACION,
                                                a.FK_PROYECTO
                                                FROM tbl_asignaciones a
                                                INNER JOIN tbl_clientes c
                                                ON a.FK_CLIENTE = c.PK_CLIENTE
                                                INNER JOIN tbl_cat_contactos cont
                                                ON a.FK_CONTACTO = cont.PK_CONTACTO
                                                INNER JOIN tbl_cat_ubicaciones u
                                                ON a.FK_UBICACION = u.PK_UBICACION
                                                INNER JOIN tbl_cat_unidades_negocio un
                                                ON a.FK_UNIDAD_NEGOCIO = un.PK_UNIDAD_NEGOCIO
                                                INNER JOIN tbl_empleados e
                                                ON a.FK_EMPLEADO = e.PK_EMPLEADO
                                                WHERE a.PK_ASIGNACION =".($pk_a))->queryOne();

            $perfilUser = tblperfilempleados::find()->where(['FK_EMPLEADO' => $pkEmp_user])->limit(1)->one();

            if($perfilUser->FK_UNIDAD_NEGOCIO == 3){

                $query2 = $connection->createCommand("SELECT PK_UNIDAD_NEGOCIO, DESC_UNIDAD_NEGOCIO FROM tbl_cat_unidades_negocio WHERE PK_UNIDAD_NEGOCIO = 3")->queryOne();
            }else{

                $query2 = 0;
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $sql = array(
                'query' => $query,
                'query2' => $query2,
            );

            return $sql;

            $connection->close();

       }

    /**
     * Displays a single tblempleados model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        //Se agrego modelContactos
        $modelContactos= tblcontactos::find()->where(['FK_EMPLEADO' => $model->PK_EMPLEADO])->limit(1)->one();
        $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        $modelDomicilios = tbldomicilios::find()->where(['PK_DOMICILIO' => $model->FK_DOMICILIO])->limit(1)->one();
        $modelBitComentariosEmpleados = TblBitComentariosEmpleados::find()->where(['FK_EMPLEADO' => $id])->count();
        $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
        //$SecondmodelBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
        //$SecondmodelBenef = TblAdministradoraBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();
        if(empty($modelContactos)){
            $modelContactos = new TblContactos();
        }

        if(empty($modelAdministradoraBenef)){
            $modelAdministradoraBenef = '';
        }else{
            $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
             $SecondmodelBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();
        }

        if(empty($SecondmodelBenef)){
            $SecondmodelBenef = '';
        }

        //dd($SecondmodelBenef);
        //Verifica si hay capturado un CV_Original o CV_EISEI, si no lo hay deshabilita la liga
        if(strcmp($modelPerfilEmpleados->CV_ORIGINAL,'')!=0){
            $modelPerfilEmpleados->CV_ORIGINAL = '../..'.$modelPerfilEmpleados->CV_ORIGINAL;
        }
        if(strcmp($modelPerfilEmpleados->CV_EISEI,'')!=0){
            $modelPerfilEmpleados->CV_EISEI = '../..'.$modelPerfilEmpleados->CV_EISEI;
        }

        //Models DUMMY
        // Desc: Solo se usan para modificar valores de las FK de los models que se mandan a la vista
        $modelGenero = TblCatGenero::find()->where(['PK_GENERO' => $model->FK_GENERO_EMP])->limit(1)->one();
        $modelRazonSocial = TblCatRazonSocial::find()->where(['PK_RAZON_SOCIAL' => $modelPerfilEmpleados->FK_RAZON_SOCIAL])->limit(1)->one();
        $modelTipoServicio = TblCatTipoServicios::find()->where(['PK_TIPO_SERVICIO' => $modelPerfilEmpleados->FK_TIPO_SERVICIO])->limit(1)->one();
        $modelUbicaciones = TblCatUbicacionRazonSocial::find()->where(['PK_UBICACION_RAZON_SOCIAL' => $modelPerfilEmpleados->FK_UBICACION])->limit(1)->one();
        $modelAreas = TblCatAreas::find()->where(['PK_AREA' => $modelPerfilEmpleados->FK_AREA])->limit(1)->one();
        $modelAdministradoras = TblCatAdministradoras::find()->where(['PK_ADMINISTRADORA' => $modelPerfilEmpleados->FK_ADMINISTRADORA])->limit(1)->one();
        $modelPuestos = TblCatPuestos::find()->where(['PK_PUESTO' => $modelPerfilEmpleados->FK_PUESTO])->limit(1)->one();
        $modelTipoContrato = TblCatTipoContrato::find()->where(['PK_TIPO_CONTRATO' => $modelPerfilEmpleados->FK_CONTRATO])->limit(1)->one();
        $modelDuracionContrato = TblCatDuracionTipoServicios::find()->where(['PK_DURACION' => $modelPerfilEmpleados->FK_DURACION_CONTRATO])->limit(1)->one();
        $modelEstatusRecurso = TblCatEstatusRecursos::find()->where(['PK_ESTATUS_RECURSO' => $modelPerfilEmpleados->FK_ESTATUS_RECURSO])->limit(1)->one();
        $modelRankTecnico = TblCatRankTecnico::find()->where(['PK_RANK_TECNICO' => $modelPerfilEmpleados->FK_RANK_TECNICO])->limit(1)->one();
        $modelUnidadesNegocio = TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO' => $modelPerfilEmpleados->FK_UNIDAD_NEGOCIO])->limit(1)->one();
        $modelUnidadesTrabajo = TblCatUnidadTrabajo::find()->where(['PK_UNIDAD_TRABAJO' => $modelPerfilEmpleados->FK_UNIDAD_TRABAJO])->limit(1)->one();
        $modelJefeDirecto = TblEmpleados::find()->where(['PK_EMPLEADO' => $modelPerfilEmpleados->FK_JEFE_DIRECTO])->limit(1)->one();
        $modelUbicacionFisica = TblCatUbicaciones::find()->where(['PK_UBICACION' => $modelPerfilEmpleados->FK_UBICACION_FISICA])->limit(1)->one();
        $modelAsignaciones = TblAsignaciones::find()
                                        ->where(['FK_EMPLEADO' => $id])
                                        ->andWhere(['=','FK_ESTATUS_ASIGNACION',2])//Asignacion con estatus 'En Ejecución'
                                        ->one();
        //Cambio Oficina Cliente
        $descUbicacionFisica = '';
        if(!empty($modelUbicacionFisica)){

            if($modelPerfilEmpleados->FK_ESTATUS_RECURSO == 2){
                if($modelUbicacionFisica->PROPIA_CLIENTE == 'cliente'){
                    /*if(isset($modelAsignaciones)){
                        $modelUbicacionFisica->FK_CLIENTE == $modelAsignaciones->FK_CLIENTE
                            ? $descUbicacionFisica = $modelUbicacionFisica->DESC_UBICACION
                            : $descUbicacionFisica = 'No se tiene asignada una ubicación del Cliente';
                    }*/
                    $descUbicacionFisica = $modelUbicacionFisica->DESC_UBICACION;
                }else{
                    $descUbicacionFisica = 'Sin Ubicación Fisica';
                }
            }else{
                if($modelUbicacionFisica->PROPIA_CLIENTE == 'PROPIA' && $modelUbicacionFisica->PK_UBICACION != -1){
                    $descUbicacionFisica = $modelUbicacionFisica->DESC_UBICACION;
                }else{
                    $descUbicacionFisica = 'Sin Ubicación Fisica';
                }
            }

        }

        $modelEstadoNac = TblCatEstados::find()->where([
                                                    'PK_ESTADO' => $model->ESTADO,
                                                    'FK_PAIS' => $model->PAIS
                                                    ])->limit(1)->one();
        $modelMunicipiosNac = TblCatMunicipios::find()->where([
                                                            'PK_MUNICIPIO' => $model->MUNICIPIO,
                                                            'FK_ESTADO' => $model->ESTADO,
                                                            'FK_PAIS' => $model->PAIS
                                                            ])->limit(1)->one();
        $modelPaisNac = tblcatpaises::find()->where([
                                                    'PK_PAIS' => $model->PAIS
                                                    ])->limit(1)->one();

        $modelEstado = TblCatEstados::find()->where([
                                                    'PK_ESTADO' => $modelDomicilios->FK_ESTADO,
                                                    'FK_PAIS' => $modelDomicilios->FK_PAIS
                                                    ])->limit(1)->one();
        $modelMunicipios = TblCatMunicipios::find()->where([
                                                            'PK_MUNICIPIO' => $modelDomicilios->FK_MUNICIPIO,
                                                            'FK_ESTADO' => $modelDomicilios->FK_ESTADO,
                                                            'FK_PAIS' => $modelDomicilios->FK_PAIS
                                                            ])->limit(1)->one();
        $modelPais = tblcatpaises::find()->where([
                                                    'PK_PAIS' => $modelDomicilios->FK_PAIS
                                                    ])->limit(1)->one();
        $incidenciasAumentos = TblIncidenciasNomina::find()->select(['FK_TIPO_INCIDENCIA','QUINCENA_APLICAR','VALOR'])->where(['FK_EMPLEADO' => $id, 'FK_TIPO_INCIDENCIA' => array(4,12),'FK_ESTATUS_INCIDENCIA' => 1,])->andWhere(['>=','QUINCENA_APLICAR',$modelPerfilEmpleados->FECHA_INGRESO])->orderBy('QUINCENA_APLICAR')->asArray()->all();
        $modelSueldoEmpleado = TblIncidenciasNomina::find()->where(['FK_EMPLEADO'=> $modelPerfilEmpleados->FK_EMPLEADO, 'FK_TIPO_INCIDENCIA' => array(4,12), 'FK_ESTATUS_INCIDENCIA' => 1,])->andWhere('QUINCENA_APLICAR <= NOW()')->orderBy(['PK_INCIDENCIA_NOMINA'=>SORT_DESC])->limit(1)->one();
        //Sustitucion de los valores de las FK, por sus descripciones y formateo de valores
        $model->RFC_EMP = substr($model->RFC_EMP, 0,4).'-'.substr($model->RFC_EMP, 4,6).'-'.substr($model->RFC_EMP, 10);
        $model->FK_GENERO_EMP = $modelGenero->DESC_GENERO;

        if($model->PAIS != NULL){
            $model->PAIS = $modelPaisNac->DESC_PAIS;
        }else{
            $model->PAIS = '';
        }

        if($model->ESTADO != NULL){
            $model->ESTADO = $modelEstadoNac->DESC_ESTADO;
        }else{
            $model->ESTADO = '';
        }

        if($model->MUNICIPIO != NULL){
             $model->MUNICIPIO = $modelMunicipiosNac->DESC_MUNICIPIO;
        }else{
            $model->MUNICIPIO = '';
        }

        $modelPerfilEmpleados->FK_RAZON_SOCIAL = $modelRazonSocial->DESC_RAZON_SOCIAL;
        $modelPerfilEmpleados->FK_TIPO_SERVICIO = $modelTipoServicio->DESC_TIPO_SERVICIO;
        $modelPerfilEmpleados->FK_UBICACION = $modelUbicaciones->DESC_UBICACION;
        $modelPerfilEmpleados->FK_AREA = $modelAreas->DESC_AREA;
        $modelPerfilEmpleados->FK_ADMINISTRADORA = $modelAdministradoras->NOMBRE_ADMINISTRADORA;
        $modelPerfilEmpleados->FK_PUESTO = $modelPuestos->DESC_PUESTO;
        $modelPerfilEmpleados->FK_CONTRATO = $modelTipoContrato->DESC_TIPO_CONTRATO;
        $modelPerfilEmpleados->FK_DURACION_CONTRATO = $modelDuracionContrato->DESC_DURACION;
        $modelPerfilEmpleados->FK_ESTATUS_RECURSO = $modelEstatusRecurso->DESC_ESTATUS_RECURSO;
        $modelPerfilEmpleados->FK_RANK_TECNICO = isset($modelRankTecnico->DESC_RANK_TECNICO)?$modelRankTecnico->DESC_RANK_TECNICO:'';//Se aplica esta validacion para cuando el empleado no tenga RANK TECNICO
        $modelPerfilEmpleados->FK_UNIDAD_NEGOCIO = $modelUnidadesNegocio->DESC_UNIDAD_NEGOCIO;
        $modelPerfilEmpleados->FK_UNIDAD_TRABAJO = $modelUnidadesTrabajo->DESC_UNIDAD_TRABAJO;
        if($modelJefeDirecto){
            $modelPerfilEmpleados->FK_JEFE_DIRECTO = $modelJefeDirecto->NOMBRE_EMP.' '.$modelJefeDirecto->APELLIDO_PAT_EMP.' '. $modelJefeDirecto->APELLIDO_MAT_EMP;
        } else {
            $modelPerfilEmpleados->FK_JEFE_DIRECTO = 'SIN JEFE DIRECTO';
        }
        $modelPerfilEmpleados->FK_UBICACION_FISICA = $descUbicacionFisica;
        $modelPerfilEmpleados->SUELDO_NETO = isset($modelSueldoEmpleado->VALOR) ? $modelSueldoEmpleado->VALOR : $modelPerfilEmpleados->SUELDO_NETO;
        $modelDomicilios->FK_ESTADO = $modelEstado->DESC_ESTADO;
        $modelDomicilios->FK_PAIS = $modelPais->DESC_PAIS;
        $modelDomicilios->FK_MUNICIPIO = $modelMunicipios->DESC_MUNICIPIO;
        //dd($modelAdministradoraBenef);

        return $this->render('view', [
            'model' => $model,
            'modelContactos'=>$modelContactos,
            'modelPerfilEmpleados' => $modelPerfilEmpleados,
            'modelDomicilios' => $modelDomicilios,
            'modelBitComentariosEmpleados' => $modelBitComentariosEmpleados,
            'modelPuestos' => $modelPuestos,
            'modelAdministradoraBenef' => $modelAdministradoraBenef,
            'SecondmodelBenef' => $SecondmodelBenef,
            'incidenciasAumentos' => $incidenciasAumentos,
            'descUbicacionFisica' => $descUbicacionFisica,
        ]);
    }
    /**
     * Creates a new tblempleados model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $request = Yii::$app->request;
        $model = new tblempleados();
        $data = Yii::$app->request->post();
        //dd($data);
        $urls=server().get_upload_url();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();//Inicio del funcionamiento de la 'Transacción' con su respectivo try/catch
        try{

          //Funcionalidad para enviar correo si el usuario indico que el nuevo empleado es asignable
          if(isset($data['EMPLEADO_ASIGNABLE'])){
              if($data['EMPLEADO_ASIGNABLE'] == 1){
                  $this->envio_correo_asignable();
                  //dd('envio correos controller');
              }
          }
          //dd('no es necesario envio correos controller');


        //Inicio nuevo segmento para prellenar campos CLRR
        $modelMotivosContrato = new TblEmpleadosMotivos();
        $modelAdministradora = TblCandidatoEmpleado::find()->where(['PK_REGISTRO'=>$request->GET('id')])->asArray()->limit(1)->one();

        //dd($modelAdministradora['LUGAR_NACIMIENTO']);
        /*$porciones = explode(", ",$modelAdministradora['LUGAR_NACIMIENTO']);
        //dd($porciones[0]);
        if(sizeof($porciones)>=3){
            $lugarnaciemiento['PAIS'] = TblCatPaises::find()->select(['PK_PAIS'])->where(['DESC_PAIS' => $porciones[(sizeof($porciones)-1)]])->limit(1)->one();
            $lugarnaciemiento['ESTADO']  = TblCatEstados::find()->select(['PK_ESTADO','DESC_ESTADO'])->where(['DESC_ESTADO' => $porciones[(sizeof($porciones)-2)]])->limit(1)->one();
            $ciudad_mun="";
            for($i=0;$i<sizeof($porciones);$i++){
                if($i!=(sizeof($porciones)-1)&&$i!=(sizeof($porciones)-2)){
                    $ciudad_mun[$i]=$porciones[$i];
                }
            }
            $ciudad_mun=implode(" ",$ciudad_mun);
            //dd($ciudad_mun);

            $lugarnaciemiento['MUNICIPIO']  = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['DESC_MUNICIPIO' => $ciudad_mun])->limit(1)->one();
        }else{
            $lugarnaciemiento['PAIS'] = '';
            $lugarnaciemiento['PAIS']['PK_PAIS'] = '';
            $lugarnaciemiento['ESTADO']  = '';
            $lugarnaciemiento['ESTADO']['PK_ESTADO'] = '';
            $lugarnaciemiento['ESTADO']['DESC_ESTADO'] = '';
            $lugarnaciemiento['MUNICIPIO'] = '';
            $lugarnaciemiento['MUNICIPIO']['PK_MUNICIPIO'] = '';
            $lugarnaciemiento['MUNICIPIO']['DESC_MUNICIPIO'] = '';
        }*/
        //dd($lugarnaciemiento['PAIS']['DESC_PAIS']);


        $AdministradoraDatosPer = (new \yii\db\Query())
                            ->select(['c.PK_ADM_CONTC', 'c.CALLE', 'c.COLONIA', 'c.CP', 'c.TELEFONO', 'c.NOMBRE_PADRE', 'c.TEL_PADRE', 'c.NOMBRE_MADRE', 'c.TEL_MADRE', 'c.TIPO_SANGRE', 'c.CASO_ACCIDENTE', 'c.TEL_ACCIDENTE', 'c.PAIS', 'c.CIUDAD', 'c.ESTADO'])
                            ->from('tbl_contactos c')
                            ->join('LEFT JOIN','tbl_candidato_empleado',
                                'c.PK_ADM_CONTC = tbl_candidato_empleado.FK_CONTACTO')
                            ->where(['=','tbl_candidato_empleado.PK_REGISTRO',$request->GET('id')])
                            ->limit(1)->one();
         //dd($modelAdministradoraDatosPer);
        $modelAdministradoraDatosPer = TblContactos::find()->where(['PK_ADM_CONTC'=>$AdministradoraDatosPer['PK_ADM_CONTC']])->limit(1)->one();
        $modelBitAdministradoraEmp = TblBitAdministradoraEmpleado::find()->WHERE(['FK_REGISTRO_ADMON'=>$request->GET('id')])->asArray()->limit(1)->one();

        $modelCandidatos = TblCandidatos::find()->WHERE(['PK_CANDIDATO'=>$modelBitAdministradoraEmp['FK_CANDIDATO']])->asArray()->limit(1)->one();
        $modelVAcantesCandidatos = TblVacantesCandidatos::find()->WHERE(['FK_CANDIDATO'=>$modelBitAdministradoraEmp['FK_CANDIDATO']])->asArray()->limit(1)->one();
        $modelVacantes = TblVacantes::find()->WHERE(['PK_VACANTE'=>$modelVAcantesCandidatos['FK_VACANTE']])->asArray()->limit(1)->one();
        $modelDomicilios = new tbldomicilios();
        $modelPerfilEmpleados = new tblperfilempleados();
        $AdministradoraBenef = (new \yii\db\Query())
                            ->select(['PK_ADM_BENEF', 'NOMBRE_BEN', 'RFC_BEN', 'PARENTESCO_BEN', 'PORCENTAJE', 'DOMICILIO'])
                            ->from('tbl_beneficiario')
                            ->join('LEFT JOIN','tbl_candidato_empleado',
                                'tbl_beneficiario.PK_ADM_BENEF = tbl_candidato_empleado.FK_BENEFICIARIO_1')
                            ->where(['=','tbl_candidato_empleado.PK_REGISTRO',$request->GET('id')])
                            ->limit(1)->one();
        $modelAdministradoraBenef = TblBeneficiario::find()->where(['PK_ADM_BENEF'=> $AdministradoraBenef['PK_ADM_BENEF']])->limit(1)->one();
        $modelAdministradoraBenef->scenario = 'SCENARIO_SEGUNDO_BENEF';
        $SecondBenef = (new \yii\db\Query())
                            ->select(['PK_ADM_BENEF', 'NOMBRE_BEN', 'RFC_BEN', 'PARENTESCO_BEN', 'PORCENTAJE', 'DOMICILIO'])
                            ->from('tbl_beneficiario')
                            ->join('LEFT JOIN','tbl_candidato_empleado',
                                'tbl_beneficiario.PK_ADM_BENEF = tbl_candidato_empleado.FK_BENEFICIARIO_2')
                            ->where(['=','tbl_candidato_empleado.PK_REGISTRO',$request->GET('id')])
                            ->limit(1)->one();
        $SecondmodelBenef = TblBeneficiario::find()->where(['PK_ADM_BENEF'=> $SecondBenef['PK_ADM_BENEF']])->limit(1)->one();
        if(empty($modelAdministradoraBenef))
        {
            $modelAdministradoraBenef = '';
            $SecondmodelBenef = '';
        }
        else
        {
                if(empty($SecondmodelBenef))
                {
                    $SecondmodelBenef = '';
                }
                else
                {
            //dd($SecondmodelBenef);
            //$SecondmodelBenef = TblAdministradoraBeneficiario::find()->where(['FK_REGISTRO'=> $request->GET('id')])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();
            $SecondmodelBenef->scenario = 'SCENARIO_SEGUNDO_BENEF';
                }
        }

        $model->NOMBRE_EMP = $modelAdministradora['NOMBRE'];
        $model->APELLIDO_PAT_EMP = $modelAdministradora['APELLIDO_PATERNO'];
        $model->APELLIDO_MAT_EMP = $modelAdministradora['APELLIDO_MATERNO'];
        $model->FK_GENERO_EMP = $modelCandidatos['FK_GENERO'];
        if(($model->FECHA_NAC_EMP = $modelAdministradora['FECHA_NACIMIENTO']) != NULL)
        {
        $model->FECHA_NAC_EMP = transform_date($modelAdministradora['FECHA_NACIMIENTO'],'d/m/Y');
        }

        //$model->LUGAR_NAC_EMP =  $modelAdministradora['LUGAR_NACIMIENTO'];


        $model->RFC_EMP =  $modelAdministradora['RFC'];
        $model->CURP_EMP =  $modelAdministradora['CURP'];
        $model->NSS_EMP = $modelAdministradora['NSS'];
        $model->EMAIL_EMP =  $modelCandidatos['EMAIL'];
        $modelPerfilEmpleados->FK_UBICACION_FISICA = $modelVacantes['FK_UBICACION'];
        $modelPerfilEmpleados->FK_CONTRATO = $modelVacantes['FK_TIPO_CONTRATO'];
        $modelPerfilEmpleados->FK_DURACION_CONTRATO = $modelVacantes['FK_DURACION_CONTRATO'];
        $modelDomicilios->CALLE = $modelAdministradoraDatosPer['CALLE'];
        $modelDomicilios->COLONIA = $modelAdministradoraDatosPer['COLONIA'];
        $modelDomicilios->CP = $modelAdministradoraDatosPer['CP'];
        $modelDomicilios->FK_PAIS = $modelAdministradoraDatosPer['PAIS'];
        $modelDomicilios->FK_ESTADO = $modelAdministradoraDatosPer['ESTADO'];
        $modelDomicilios->FK_MUNICIPIO = $modelAdministradoraDatosPer['CIUDAD'];
        $modelDomicilios->CELULAR = $modelAdministradoraDatosPer['TELEFONO'];
        //$modelDomicilios->TELEFONO = $modelAdministradoraDatosPer['TELEFONO'];
        $modelDomicilios->TEL_EMERGENCIA = $modelAdministradoraDatosPer['TEL_ACCIDENTE'];

        $extra['DESC_ESTADO'] = tblcatestados::findOne($modelAdministradoraDatosPer->ESTADO);
        $extra['DESC_MUNICIPIO'] = tblcatmunicipios::find()->where(['PK_MUNICIPIO'=>$modelAdministradoraDatosPer->CIUDAD,'FK_ESTADO'=>$modelAdministradoraDatosPer->ESTADO,'FK_PAIS'=>$modelAdministradoraDatosPer->PAIS])->limit(1)->one();
        //dd($extra['DESC_MUNICIPIO']);
        //Fin de nuevo segmento para prellenar campos CLRR
        $modelSubirFotoEmpleado = new SubirArchivo();
        $modelSubirFotoEmpleado->extensions = 'png, jpg, jpeg';
        $modelSubirFotoEmpleado->noRequired = true;

        $modelSubirCVOriginal = new SubirArchivo();
        $modelSubirCVOriginal->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVOriginal->noRequired = true;

        $modelSubirCVEISEI = new SubirArchivo();
        $modelSubirCVEISEI->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVEISEI->noRequired = true;

        if(!isset($model, $modelDomicilios,$modelPerfilEmpleados)){
            throw new Exception("Error al obtener Datos de form");
        }

        $extraVals['idTempArchivos'] = rand(1,999999);

        if ($model->load(Yii::$app->request->post()) && $modelDomicilios->load(Yii::$app->request->post()) && $modelPerfilEmpleados->load(Yii::$app->request->post()) && $modelAdministradoraDatosPer->load(Yii::$app->request->post()) && $modelAdministradoraBenef->load(Yii::$app->request->post()))
        {

            $fechaIngreso = strtotime(str_replace('/', '-',$modelPerfilEmpleados->FECHA_INGRESO));
            $quincena_aplicar = '';
            if(date('j',$fechaIngreso) <= 15){
                $quincena_aplicar = date('Y-m',$fechaIngreso).'-15';
            }else{
                $month = date('Y-m',$fechaIngreso);
                $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
                $last_day = date('Y-m-d', strtotime("{$aux} - 1 day"));
                $quincena_aplicar = $last_day;
            }
            $modelMotivosContrato->load(Yii::$app->request->post());
            //Guarda los valores del post en la variable $data
            $data = Yii::$app->request->post();

            $modelSubirFotoEmpleado->file = UploadedFile::getInstance($modelSubirFotoEmpleado, '[1]file');
            //NO MODIFICAR EL ORDEN DEL GUARDADO DE LOS 4 MODELOS INCLUIDOS EN ESTE IF
            //SI SE LLEGA A MODIFICAR EL ORDEN VA A TRONAR LA PAGINA Y NO VA A GUARDAR NI ARCHIVOS NI REGISTROS EN LA BD
            // ORDEN CORRECTO DE GUARDADO
            /*
                1.- modelDomicilios
                2.- modelEmpleados
                3.- Subir Foto Empleado
                4.- Subir CV Original
                5.- Subir CV EISEI
                6.- modelPerfilEmpleados
                7.- Mover archivos de carpeta temporal a ubicacion definitiva
                8.- modelBitUnidadNegocio
                9.- modelBitUbicacionFisica
                10.- Guarda Bitacora tbl_bitacora
            */

            // 1.- Guardar modelo de Domicilios y hace varios valores nulos
            $modelDomicilios->NUM_INTERIOR=null;
            $modelDomicilios->NUM_EXTERIOR=null;
            $modelDomicilios->PISO=null;
            $modelDomicilios->save(false);

            //2.- Guarda el modelo de Empleados y regresa el PK_EMPLEADO insertado
            $model->FECHA_NAC_EMP = transform_date($model->FECHA_NAC_EMP,'Y-m-d');
            $model->RFC_EMP = str_replace('-', '', $model->RFC_EMP);
            $model->FK_DOMICILIO = $modelDomicilios->PK_DOMICILIO;
            $model->EMAIL_INTERNO = ($model->EMAIL_INTERNO==''?NULL:$model->EMAIL_INTERNO);
            $model->EMAIL_ASIGNADO = ($model->EMAIL_ASIGNADO==''?NULL:$model->EMAIL_ASIGNADO);
            if(is_numeric($data['fk_estado'])){
                $estado = TblCatEstados::find()->select(['DESC_ESTADO'])->where(['PK_ESTADO' => $data['fk_estado']])->limit(1)->one();
                if(is_numeric($data['fk_municipio'])){
                    $municipio = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['PK_MUNICIPIO' => $data['fk_municipio'],'FK_ESTADO' => $data['fk_estado'] ])->limit(1)->one();
                }else{
                    $municipio = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['PK_MUNICIPIO' => $data['fk_municipio'],'FK_ESTADO' => $data['fk_estado'] ])->limit(1)->one();
                }
            }else{
                $estado = TblCatEstados::find()->select(['DESC_ESTADO'])->where(['DESC_ESTADO' => $data['fk_estado']])->limit(1)->one();
                $id_estado = TblCatEstados::find()->select(['PK_ESTADO'])->where(['DESC_ESTADO' => $data['fk_estado']])->limit(1)->one();
                if(is_numeric($data['fk_municipio'])){
                    $municipio = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['PK_MUNICIPIO' => $data['fk_municipio'],'FK_ESTADO' => $id_estado['PK_ESTADO'] ])->limit(1)->one();
                }else{
                    //dd($data['fk_municipio']);
                    $municipio = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['DESC_MUNICIPIO' => $data['fk_municipio'],'FK_ESTADO' => $id_estado['PK_ESTADO'] ])->limit(1)->one();
                }
            }
            $pais = TblCatPaises::find()->select(['DESC_PAIS'])->where(['PK_PAIS' => $data['fk_pais']])->limit(1)->one();
                        //dd($municipio['DESC_MUNICIPIO']);
                        //dd($data['fk_municipio']);

            $model->LUGAR_NAC_EMP = $municipio['DESC_MUNICIPIO'].", ".$estado['DESC_ESTADO'].", ".$pais['DESC_PAIS'];

            //dd($data['fk_pais']);
            // Calculo de iniciales del empleado
            $model->INICIALES = '';
            $arregloNombre = explode(' ', trim($model->NOMBRE_EMP));
            $arregloNombresExluir = TblNombresExcluir::find()->asArray()->all();
            if(count($arregloNombre) == 1){
                $model->INICIALES = substr($model->NOMBRE_EMP, 0, 1).substr($model->APELLIDO_PAT_EMP, 0, 1).substr($model->APELLIDO_MAT_EMP, 0, 1);
            } else {
                foreach ($arregloNombre as $key => $value) {
                    foreach ($arregloNombresExluir as $key2 => $array) {
                        if($value == $array['NOMBRES']){
                            unset($arregloNombre[$key]);
                        }
                    }
                }
                $arregloNombre = array_values($arregloNombre);

                if(count($arregloNombre) == 1){
                    $model->INICIALES = substr($model->NOMBRE_EMP, 0, 1).substr($model->APELLIDO_PAT_EMP, 0, 1).substr($model->APELLIDO_MAT_EMP, 0, 1);
                }elseif(count($arregloNombre) > 1){
                    $model->INICIALES = substr($model->NOMBRE_EMP, 0, 1).substr($arregloNombre[1], 0, 1).substr($model->APELLIDO_PAT_EMP, 0, 1).substr($model->APELLIDO_MAT_EMP, 0, 1);
                }else{
                    $model->INICIALES = '';
                }
            }
            $model->save(false);

            $modelAdministradoraBenef->FK_EMPLEADO = $model->PK_EMPLEADO;
            $modelAdministradoraBenef->NOMBRE_BEN       = $data['TblBeneficiario']['1']['NOMBRE_BEN'];
            $modelAdministradoraBenef->RFC_BEN          = $data['TblBeneficiario']['1']['RFC_BEN'];
            $modelAdministradoraBenef->PARENTESCO_BEN   = $data['TblBeneficiario']['1']['PARENTESCO_BEN'];
            $modelAdministradoraBenef->PORCENTAJE       = $data['TblBeneficiario']['1']['PORCENTAJE'];
            $modelAdministradoraBenef->DOMICILIO        = $data['TblBeneficiario']['1']['DOMICILIO'];
            $modelAdministradoraBenef->save(false);

            $modelAdministradoraDatosPer->FK_EMPLEADO = $model->PK_EMPLEADO;
            $modelAdministradoraDatosPer->save(false);

            if($SecondmodelBenef != '')
            {
            $SecondmodelBenef->FK_EMPLEADO = $model->PK_EMPLEADO;
            $SecondmodelBenef->NOMBRE_BEN       = $data['TblBeneficiario']['2']['NOMBRE_BEN'];
            $SecondmodelBenef->RFC_BEN          = $data['TblBeneficiario']['2']['RFC_BEN'];
            $SecondmodelBenef->PARENTESCO_BEN   = $data['TblBeneficiario']['2']['PARENTESCO_BEN'];
            $SecondmodelBenef->PORCENTAJE       = $data['TblBeneficiario']['2']['PORCENTAJE'];
            $SecondmodelBenef->DOMICILIO        = $data['TblBeneficiario']['2']['DOMICILIO'];
            $SecondmodelBenef->save(false);
            }

            //3.- Subir foto empleado y obtiene la extension del archivo subido

            if($modelSubirFotoEmpleado->file){ //Valida si se subio foto
                $rutaGuardado = '../uploads/EmpleadosFotos/';
                $nombre = 'foto_'.$model->PK_EMPLEADO;
                $extension = $modelSubirFotoEmpleado-> upload($rutaGuardado,$nombre);
            }
            $model->FOTO_EMP = $modelSubirFotoEmpleado->file?('/uploads/EmpleadosFotos/foto_'.$model->PK_EMPLEADO.'.'.$extension):'';


            $model->save(false);

            //4.- Guarda el CV Original
            $modelSubirCVOriginal->file = UploadedFile::getInstance($modelSubirCVOriginal, '[2]file');
            if($modelSubirCVOriginal->file){ //Valida si se subio foto
                $rutaGuardado = '../uploads/EmpleadosCVOriginal/';
                $nombre = 'cvOriginal_'.$model->PK_EMPLEADO;
                $extensionCVOriginal = $modelSubirCVOriginal-> upload($rutaGuardado,$nombre);
                $modelPerfilEmpleados->CV_ORIGINAL='/uploads/EmpleadosCVOriginal/cvOriginal_'.$model->PK_EMPLEADO.'.'.$extensionCVOriginal;
            } else {
                $modelPerfilEmpleados->CV_ORIGINAL='';
            }

            //5.- Guarda el CV EISEI
            $modelSubirCVEISEI->file = UploadedFile::getInstance($modelSubirCVEISEI, '[3]file');
            if($modelSubirCVEISEI->file){ //Valida si se subio foto
                $rutaGuardado = '../uploads/EmpleadosCVEISEI/';
                $nombre = 'cvEISEI_'.$model->PK_EMPLEADO;
                $extensionCVEISEI = $modelSubirCVEISEI-> upload($rutaGuardado,$nombre);
                $modelPerfilEmpleados->CV_EISEI='/uploads/EmpleadosCVEISEI/cvEISEI_'.$model->PK_EMPLEADO.'.'.$extensionCVEISEI;
            } else {
                $modelPerfilEmpleados->CV_EISEI='';
            }

            //6.- Guarda el modelo de Perfil Empleados, y genera la fecha de ingreso automatica
            $modelPerfilEmpleados->FK_EMPLEADO=$model->PK_EMPLEADO;
            $modelPerfilEmpleados->FECHA_REGISTRO= date('Y-m-d H:i:s');

            $bandera = $data['EMPLEADO_ASIGNABLE'];

            if($bandera == 1 && $modelPerfilEmpleados->FK_TIPO_SERVICIO==2){
                $modelPerfilEmpleados->FK_ESTATUS_RECURSO = 101;
            }else{
                $modelPerfilEmpleados->FK_ESTATUS_RECURSO = $modelPerfilEmpleados->FK_TIPO_SERVICIO==1?5:3;//Si FK_TIPO_SERVICIO==1 se pone el estatus de Administrativo al empleado, si no se le pone el estatus de Disponible
            }

            $modelPerfilEmpleados->FECHA_ACTUALIZA = NULL;
            $modelPerfilEmpleados->FECHA_INGRESO = transform_date($modelPerfilEmpleados->FECHA_INGRESO,'Y-m-d');
            $modelPerfilEmpleados->FK_RANK_TECNICO = $modelPerfilEmpleados->FK_RANK_TECNICO==null?'0':$modelPerfilEmpleados->FK_RANK_TECNICO;//Se agrega valdiacion debido a que el empleado pueden tener o no rank tecnico
            $modelPerfilEmpleados->ID_EMP_ADMINISTRADORA = 'Sin ID Empleado';
            $modelPerfilEmpleados->save(false);

            //7.- Mueve los archivos de la ubicacion temporal a la ubicacion en el servidor
            $data['doc_list'] =(!empty($data['doc_list']))? $data['doc_list']:[];
            foreach ($data['doc_list'] as $key => $value) {

                //Se recogen valores
                //$rutaTemporal = substr($data[$value],3);
                $idTemporal = $data['idTempArchivos'];
                $nombreArchivo = quitar_acentos(utf8_decode($value));

                //Se copia archivo de ruta temporal a ruta permanente
                $rutaTemporal = '../uploads/EmpleadosTEMP/'.$idTemporal.'_'.$nombreArchivo;
                copy($rutaTemporal, '../uploads/EmpleadosDocumentos/'.$model->PK_EMPLEADO.'_'.$nombreArchivo);

                //Se guarda en base de datos la ubicacion del archivo
                $modelDocumentosEmpleados = new TblDocumentosEmpleados;
                $modelDocumentosEmpleados->NOMBRE_DOCUMENTO = $nombreArchivo;
                $modelDocumentosEmpleados->RUTA_DOCUMENTO = '/uploads/EmpleadosDocumentos/'.$model->PK_EMPLEADO.'_'.$nombreArchivo;
                $modelDocumentosEmpleados->FECHA_CREACION= date('Y-m-d');
                $modelDocumentosEmpleados->FK_BITACORA = '0';
                $modelDocumentosEmpleados->FK_EMPLEADO = $model->PK_EMPLEADO;
                $modelDocumentosEmpleados->save(false);

                //Una vez guardado en la carpeta EmpleadosDocumentos, se elimina archivo de la ubicacion temporal
                unlink($rutaTemporal);
            }

            //8.- Inicializa la bitacora de unidad de negocio del empleado
            user_log_bitacora_unidad_negocio($model->PK_EMPLEADO,$modelPerfilEmpleados->FK_UNIDAD_NEGOCIO,0);

            //9.- Inicializa la bitacora de ubicacion fisica
            user_log_bitacora_ubicacion_fisica($model->PK_EMPLEADO,$modelPerfilEmpleados->PK_PERFIL,$modelPerfilEmpleados->FK_UBICACION_FISICA,'PROPIA');

            //10.- Inserta registro en tabla de vacaciones empleado
            $modelVacacionesempleados = new TblVacacionesEmpleado;
            $modelVacacionesempleados->FK_EMPLEADO = $model->PK_EMPLEADO;
            $modelVacacionesempleados->DIAS_DISPONIBLES = 0;
            $modelVacacionesempleados->DIAS_PERIODO_ANTERIOR = 0;
            $modelVacacionesempleados->DIAS_HORAS_EXTRAS = 0;
            $modelVacacionesempleados->ACUMULAR_DIAS = 0;
            $modelVacacionesempleados->FECHA_REGISTRO = date('Y-m-d H:i:s');
            $modelVacacionesempleados->FECHA_ACTUALIZACION = date('Y-m-d H:i:s');
            $modelVacacionesempleados->save(false);

            //11.- Guardar la bitacora
            $descripcionBitacora = 'PK_EMPLEADO='.$model->PK_EMPLEADO.','.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP.',PK_PERFIL='.$modelPerfilEmpleados->PK_PERFIL.',PK_DOMICILIO='.$model->FK_DOMICILIO;
            user_log_bitacora($descripcionBitacora,'Alta de Recurso',$model->PK_EMPLEADO);

            //12.- Guarda la bitacora de fechas de ingreso
            user_log_bitacora_fecha_ingreso_empleado($model->PK_EMPLEADO,$modelPerfilEmpleados->FECHA_INGRESO);


            //13.- Guarda la bitacora de estatus de recurso
            user_log_bitacora_estatus_empleado($model->PK_EMPLEADO,$modelPerfilEmpleados->FK_ESTATUS_RECURSO);

            //14.- Crea la incidencia de sueldo inicial
            $fechaIngreso = $modelPerfilEmpleados->FECHA_INGRESO;
            $modelIncidenciasSueldoInicial = new TblIncidenciasNomina;
            $modelIncidenciasSueldoInicial->FK_EMPLEADO = $model->PK_EMPLEADO;
            $modelIncidenciasSueldoInicial->FK_ADMINISTRADORA = $modelPerfilEmpleados->FK_ADMINISTRADORA;
            $modelIncidenciasSueldoInicial->FK_TIPO_INCIDENCIA = 12;
            $modelIncidenciasSueldoInicial->FK_ESTATUS_INCIDENCIA = 1;
            $modelIncidenciasSueldoInicial->DIAS = NULL;
            $modelIncidenciasSueldoInicial->VALOR = $modelPerfilEmpleados->SUELDO_NETO;
            $modelIncidenciasSueldoInicial->QUINCENA_APLICAR = $quincena_aplicar;
            $modelIncidenciasSueldoInicial->VIGENCIA = $quincena_aplicar;
            $modelIncidenciasSueldoInicial->FECHA_BAJA = NULL;
            $modelIncidenciasSueldoInicial->PORCENTAJE_INCAPACIDAD = NULL;
            $modelIncidenciasSueldoInicial->IMSS = NULL;
            $modelIncidenciasSueldoInicial->COMENTARIOS = 'SALARIO INICIAL';
            $modelIncidenciasSueldoInicial->DESCUENTO_BONIFICACION = NULL;
            $modelIncidenciasSueldoInicial->FK_USUARIO = user_info()['PK_USUARIO'];
            $modelIncidenciasSueldoInicial->FECHA_REGISTRO = date('Y-m-d H:i:s');
            $modelIncidenciasSueldoInicial->save(false);

            if(!$modelBitAdministradoraEmp){
            $modelMotivosContrato->FK_EMPLEADO = $model['PK_EMPLEADO'];
            $modelMotivosContrato->FECHA_REGISTRO = date('Y-m-d H:i:s');
            $modelMotivosContrato->save(false);
            $descripcionBitacora = 'MOTIVOS_CONTRATO ='.$modelMotivosContrato->PK_MOT_EMP.','.$modelMotivosContrato->MOTIVO_CONTRATACION;
            user_log_bitacora($descripcionBitacora,'Alta de Motivos de Contrato',$modelMotivosContrato->PK_MOT_EMP);
            }
            $modelAdministradoraDatosPer->save(false);
            $descripcionBitacora = 'ACTUALIZAR_CONTACTOS ='.$modelAdministradoraDatosPer->PK_ADM_CONTC.', PK_ADM_CONTC = '.$modelAdministradoraDatosPer->PK_ADM_CONTC;
            user_log_bitacora($descripcionBitacora,'Actualizacion de Datos Contactos',$modelAdministradoraDatosPer->PK_ADM_CONTC);

            //$modelAdministradoraBenef->save(false);
            $descripcionBitacora = 'ACTUALIZAR_BENEFICIARIO = '.$modelAdministradoraBenef->PK_ADM_BENEF.', PK_ADM_BENEF = '.$modelAdministradoraBenef->PK_ADM_BENEF;
            user_log_bitacora($descripcionBitacora,'Actualizacion de Beneficiarios',$modelAdministradoraBenef->PK_ADM_BENEF);

            if ($SecondmodelBenef != '')
            {
            //$SecondmodelBenef->save(false);
            $descripcionBitacora = 'ACTUALIZAR_SEGUNDO_BENEFICIARIO ='.$SecondmodelBenef->PK_ADM_BENEF.', PK_ADM_BENEF = '.$SecondmodelBenef->PK_ADM_BENEF;
            user_log_bitacora($descripcionBitacora,'Actualizacion de Beneficiarios',$SecondmodelBenef->PK_ADM_BENEF);
            }

            //dd($descripcionBitacora);

            //Eliminamos El registro que se cargo en la administradora
            \Yii::$app
                    ->db
                    ->createCommand()
                    ->delete('tbl_bit_administradora_empleado', ['FK_REGISTRO_ADMON' => $modelAdministradora['PK_REGISTRO']])
                    ->execute();

               \Yii::$app
                    ->db
                    ->createCommand()
                    ->delete('tbl_candidato_empleado', ['PK_REGISTRO' => $modelAdministradora['PK_REGISTRO']])
                    ->execute();

             //Fin de Bloque del Borrado CLRR
            //Yii::$app->session->setFlash('success', 'Los datos se han guardado correctamente.');
            // return $this->redirect(['view', 'id' => $model->PK_EMPLEADO,'action'=>'insert']);

            $transaction->commit();//Si todo el proceso es correcto se ejectua la sentencia commit para guardar todas las operaciones en la base de datos.

            return $this->redirect(['index', 'action'=>'insert']);

        } else {

            $transaction->commit();//Si todo el proceso es correcto se ejectua la sentencia commit para guardar todas las operaciones en la base de datos.

            //Yii::$app->session->setFlash('success', 'Error al guardar, inténtelo de nuevo.');
            return $this->render('create', [
                'model' => $model,
                'modelDomicilios' => $modelDomicilios,
                'modelPerfilEmpleados' => $modelPerfilEmpleados,
                'modelSubirFotoEmpleado' => $modelSubirFotoEmpleado,
                'modelSubirCVOriginal' => $modelSubirCVOriginal,
                'modelSubirCVEISEI' => $modelSubirCVEISEI,
                'modelMotivosContrato' => $modelMotivosContrato,
                'modelAdministradoraDatosPer' => $modelAdministradoraDatosPer,
                'modelBitAdministradoraEmp' => $modelBitAdministradoraEmp,
                'modelAdministradoraBenef' => $modelAdministradoraBenef,
                'SecondmodelBenef' => $SecondmodelBenef,
                //'lugarnaciemiento' => $lugarnaciemiento,
                'extra' => $extra,
                'extraVals' => $extraVals,
            ]);
        }


        }catch(\Exception $e){
            $transaction->rollBack();//Si sucede algún error durante la operación, se ejecuta la sentencia rollback para evitar que hagan modificaciones en base de datos.
        throw $e;
        }//FIN del funcionamiento de la 'Transacción' con su respectivo try/catch
        $connection->close();
    }

    /**
     * Updates an existing tblempleados model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_EMPLEADO]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionCambiar_empleado($id)
    {
        $model = $this->findModel($id);
        $modelBitComentariosEmpleados = new TblBitComentariosEmpleados;
        $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        $modelDomicilios = tbldomicilios::find()->where(['PK_DOMICILIO' => $model->FK_DOMICILIO])->limit(1)->one();
        $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
        $modelContactos= TblContactos::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
         $data = Yii::$app->request->post();



        /**$porciones = explode(", ",$model['LUGAR_NAC_EMP']);
        //dd($porciones[0]);
        if(sizeof($porciones)>=3){
            $lugarnaciemiento['PAIS'] = TblCatPaises::find()->select(['PK_PAIS'])->where(['DESC_PAIS' => $porciones[(sizeof($porciones)-1)]])->limit(1)->one();
            $lugarnaciemiento['ESTADO']  = TblCatEstados::find()->select(['PK_ESTADO','DESC_ESTADO'])->where(['DESC_ESTADO' => $porciones[(sizeof($porciones)-2)]])->limit(1)->one();
            $ciudad_mun="";
            for($i=0;$i<sizeof($porciones);$i++){
                if($i!=(sizeof($porciones)-1)&&$i!=(sizeof($porciones)-2)){
                    $ciudad_mun[$i]=$porciones[$i];
                }
            }
            $ciudad_mun=implode(" ",$ciudad_mun);
            //dd($ciudad_mun);

            $lugarnaciemiento['MUNICIPIO']  = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['DESC_MUNICIPIO' => $ciudad_mun])->limit(1)->one();
        }else{
            $lugarnaciemiento['PAIS'] = '';
            $lugarnaciemiento['PAIS']['PK_PAIS'] = '';
            $lugarnaciemiento['ESTADO']  = '';
            $lugarnaciemiento['ESTADO']['PK_ESTADO'] = '';
            $lugarnaciemiento['ESTADO']['DESC_ESTADO'] = '';
            $lugarnaciemiento['MUNICIPIO'] = '';
            $lugarnaciemiento['MUNICIPIO']['PK_MUNICIPIO'] = '';
            $lugarnaciemiento['MUNICIPIO']['DESC_MUNICIPIO'] = '';
        }*/
        //$SecondmodelBenef = TblAdministradoraBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();

        if(empty($modelAdministradoraBenef)){
            $modelAdministradoraBenef = '';
        }else{
            $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
             $SecondmodelBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();
        }

        if(empty($SecondmodelBenef)){
            $SecondmodelBenef = '';
        }


        $modelSubirFotoEmpleado = new SubirArchivo();
        $modelSubirFotoEmpleado->extensions = 'png, jpg, jpeg';
        $modelSubirFotoEmpleado->noRequired = true;

        $modelSubirCVOriginal = new SubirArchivo();
        $modelSubirCVOriginal->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVOriginal->noRequired = true;

        $modelSubirCVEISEI = new SubirArchivo();
        $modelSubirCVEISEI->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVEISEI->noRequired = true;

        $model->FECHA_NAC_EMP = transform_date($model->FECHA_NAC_EMP,'d/m/Y');
        $modelDocumentosEmpleados = TblDocumentosEmpleados::find()->select('NOMBRE_DOCUMENTO, RUTA_DOCUMENTO, FECHA_CREACION')->where(['FK_EMPLEADO' => $id])->asArray()->all();
        $extra['DESC_ESTADO'] = tblcatestados::findOne($model->ESTADO);
        $extra['DESC_MUNICIPIO'] = tblcatmunicipios::find()->where(['PK_MUNICIPIO'=>$model->MUNICIPIO,'FK_ESTADO'=>$model->ESTADO,'FK_PAIS'=>$model->PAIS])->limit(1)->one();
        //dd($extra['DESC_ESTADO']);
        $extra['formCargar'] = '_form_update_empleado';

        $extraVals['idTempArchivos'] = rand(1,999999);

        if(strcmp($model->FOTO_EMP,'')==0){
            $model->FOTO_EMP = 'defoult';
        }

        if ($model->load(Yii::$app->request->post()) ) {
            $datosPost = Yii::$app->request->post();
            $model->FECHA_NAC_EMP = transform_date($model->FECHA_NAC_EMP,'Y-m-d');
            $model->RFC_EMP = str_replace('-', '', $model->RFC_EMP);
            $model->CURP_EMP = ($datosPost['TblEmpleados']['CURP_EMP']==''?NULL:$datosPost['TblEmpleados']['CURP_EMP']);
            $model->EMAIL_INTERNO = ($model->EMAIL_INTERNO==''?NULL:$model->EMAIL_INTERNO);
            $model->EMAIL_ASIGNADO = ($model->EMAIL_ASIGNADO==''?NULL:$model->EMAIL_ASIGNADO);
            $model->CELULAR = ($model->CELULAR==''?NULL:$model->CELULAR); //se agrego para el campo de celular
            $model->INICIALES = '';
            $arregloNombre = explode(' ', trim($model->NOMBRE_EMP));
            $arregloNombresExluir = TblNombresExcluir::find()->asArray()->all();
            if(count($arregloNombre) == 1){
                $model->INICIALES = substr($model->NOMBRE_EMP, 0, 1).substr($model->APELLIDO_PAT_EMP, 0, 1).substr($model->APELLIDO_MAT_EMP, 0, 1);
            } else {
                foreach ($arregloNombre as $key => $value) {
                    foreach ($arregloNombresExluir as $key2 => $array) {
                        if($value == $array['NOMBRES']){
                            unset($arregloNombre[$key]);
                        }
                    }
                }
                $arregloNombre = array_values($arregloNombre);

                if(count($arregloNombre) == 1){
                    $model->INICIALES = substr($model->NOMBRE_EMP, 0, 1).substr($model->APELLIDO_PAT_EMP, 0, 1).substr($model->APELLIDO_MAT_EMP, 0, 1);
                }elseif(count($arregloNombre) > 1){
                    $model->INICIALES = substr($model->NOMBRE_EMP, 0, 1).substr($arregloNombre[1], 0, 1).substr($model->APELLIDO_PAT_EMP, 0, 1).substr($model->APELLIDO_MAT_EMP, 0, 1);
                }else{
                    $model->INICIALES = '';
                }
            }
            $modelSubirFotoEmpleado->file = UploadedFile::getInstance($modelSubirFotoEmpleado, '[1]file');//Se carga la foto enviada desde la vista

            if($modelSubirFotoEmpleado->file){//Se valida si se envio una foto o no
                if(file_exists('..'.$model->FOTO_EMP)){//Valida si ya existe una foto cargada
                    $status = unlink('..'.$model->FOTO_EMP);
                }

                $rutaGuardado = '../uploads/EmpleadosFotos/';
                $nombre = 'foto_'.$id;

                $extension = $modelSubirFotoEmpleado-> upload($rutaGuardado,$nombre);
                $model->FOTO_EMP = '/uploads/EmpleadosFotos/foto_'.$id.'.'.$extension;

            }
            if(is_numeric($_POST['TblEmpleados']['ESTADO'])){
                $estado = TblCatEstados::find()->select(['DESC_ESTADO'])->where(['PK_ESTADO' => $_POST['TblEmpleados']['ESTADO']])->limit(1)->one();
                if(is_numeric($_POST['TblEmpleados']['MUNICIPIO'])){
                    $municipio = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['PK_MUNICIPIO' => $_POST['TblEmpleados']['MUNICIPIO'],'FK_ESTADO' => $_POST['TblEmpleados']['ESTADO'] ])->limit(1)->one();
                }else{
                    $municipio = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['PK_MUNICIPIO' => $_POST['TblEmpleados']['MUNICIPIO'],'FK_ESTADO' => $_POST['TblEmpleados']['ESTADO'] ])->limit(1)->one();
                }
            }else{
                $estado = TblCatEstados::find()->select(['DESC_ESTADO'])->where(['DESC_ESTADO' => $_POST['TblEmpleados']['ESTADO']])->limit(1)->one();
                $id_estado = TblCatEstados::find()->select(['PK_ESTADO'])->where(['DESC_ESTADO' => $_POST['TblEmpleados']['ESTADO']])->limit(1)->one();
                if(is_numeric($_POST['TblEmpleados']['MUNICIPIO'])){
                    $municipio = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['PK_MUNICIPIO' => $_POST['TblEmpleados']['MUNICIPIO'],'FK_ESTADO' => $id_estado['PK_ESTADO'] ])->limit(1)->one();
                }else{
                    //dd($data['fk_municipio']);
                    $municipio = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['DESC_MUNICIPIO' => $_POST['TblEmpleados']['MUNICIPIO'],'FK_ESTADO' => $id_estado['PK_ESTADO'] ])->limit(1)->one();
                }
            }
            $pais = TblCatPaises::find()->select(['DESC_PAIS'])->where(['PK_PAIS' => $_POST['TblEmpleados']['PAIS']])->limit(1)->one();
                        //dd($municipio['DESC_MUNICIPIO']);
                        //dd($data['fk_municipio']);

        $pais = TblCatPaises::find()->select(['DESC_PAIS'])->where(['PK_PAIS' => $data['TblEmpleados']['PAIS']])->limit(1)->one();
        $estado = TblCatEstados::find()->select(['DESC_ESTADO'])->where(['PK_ESTADO' => $data['TblEmpleados']['ESTADO']])->limit(1)->one();
        $municipio = TblCatMunicipios::find()->select(['DESC_MUNICIPIO'])->where(['PK_MUNICIPIO' => $data['TblEmpleados']['MUNICIPIO'],'FK_ESTADO' => $data['TblEmpleados']['ESTADO'] ])->limit(1)->one();

            $model->PAIS = $data['TblEmpleados']['PAIS'];
            $model->ESTADO = $data['TblEmpleados']['ESTADO'];
            $model->MUNICIPIO = $data['TblEmpleados']['MUNICIPIO'];
            $model->save(false);
            //$model->LUGAR_NAC_EMP = $municipio['DESC_MUNICIPIO'].", ".$estado['DESC_ESTADO'].", ".$pais['DESC_PAIS'];

            $model->INICIALES = quitar_acentos($model->INICIALES);
            $model->save(false);
            //Guardar la bitacora
            $descripcionBitacora = 'PK_EMPLEADO='.$model->PK_EMPLEADO.','.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP.',PK_PERFIL='.$modelPerfilEmpleados->PK_PERFIL.',PK_DOMICILIO='.$model->FK_DOMICILIO;
            user_log_bitacora($descripcionBitacora,'Modificar Empleado',$model->PK_EMPLEADO);

            return $this->redirect(['view', 'id' => $model->PK_EMPLEADO]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modelDomicilios' => $modelDomicilios,
                'modelPerfilEmpleados' => $modelPerfilEmpleados,
                'modelSubirFotoEmpleado' => $modelSubirFotoEmpleado,
                'modelSubirCVOriginal' => $modelSubirCVOriginal,
                'modelSubirCVEISEI' => $modelSubirCVEISEI,
                'modelAdministradoraBenef' => $modelAdministradoraBenef,
                'SecondmodelBenef' => $SecondmodelBenef,
                //'lugarnaciemiento' => $lugarnaciemiento,
                'extra' => $extra,
                'extraVals' => $extraVals,
                'modelDocumentosEmpleados' => $modelDocumentosEmpleados,
                'modelBitComentariosEmpleados' => $modelBitComentariosEmpleados,
                'modelContactos' => $modelContactos,
            ]);
        }
    }

    public function actionCambiar_contactos($id)
    {

        $model = $this->findModel($id);
        $modelBitComentariosEmpleados = new TblBitComentariosEmpleados;
        $modelDomicilios = tbldomicilios::find()->where(['PK_DOMICILIO' => $model->FK_DOMICILIO])->limit(1)->one();
        $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        //$lugarnaciemiento="";

        $modelContactos= TblContactos::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one(); // smodelo en el cual nos vamos a basar para hacer las consultas
        $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();

        if(empty($modelContactos)){
            $modelContactos = new TblContactos();
        }

        if(empty($modelAdministradoraBenef)){
            $modelAdministradoraBenef = new TblBeneficiario();
        }else{
            $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
             $SecondmodelBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();
        }

        if(empty($SecondmodelBenef)){
            $SecondmodelBenef = '';
        }


        $modelSubirFotoEmpleado = new SubirArchivo();
        $modelSubirFotoEmpleado->extensions = 'png, jpg, jpeg';
        $modelSubirFotoEmpleado->noRequired = true;

        $modelSubirCVOriginal = new SubirArchivo();
        $modelSubirCVOriginal->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVOriginal->noRequired = true;

        $modelSubirCVEISEI = new SubirArchivo();
        $modelSubirCVEISEI->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVEISEI->noRequired = true;

        $modelDocumentosEmpleados = TblDocumentosEmpleados::find()->select('NOMBRE_DOCUMENTO, RUTA_DOCUMENTO, FECHA_CREACION')->where(['FK_EMPLEADO' => $id])->asArray()->all();

        $extra['formCargar'] = '_form_update_contactos';
        $extraVals['idTempArchivos'] = rand(1,999999);


        if ($modelContactos->load(Yii::$app->request->post())  ) {

            //Guardar la bitacora
            $descripcionBitacora = 'PK_EMPLEADO='.$model->PK_EMPLEADO.','.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$modelContactos->CASO_ACCIDENTE.' '.$model->APELLIDO_MAT_EMP.',PK_PERFIL='.$modelPerfilEmpleados->PK_PERFIL.',PK_DOMICILIO='.$model->FK_DOMICILIO;
            user_log_bitacora($descripcionBitacora,'Modificar Contactos',$model->FK_DOMICILIO);
            $contactoifnot= TblContactos::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
            if(empty($contactoifnot)){
                $modelContactos->FK_EMPLEADO = $id;
                $modelContactos->FECHA_REGISTRO =   date('Y-m-d H:i:s');

            }
            $modelContactos->save(false);
           // $modelDomicilios->save(false);
            return $this->redirect(['view', 'id' => $model->PK_EMPLEADO]);
        }  else {

            return $this->render('update', [
                'model' => $model,
                'modelDomicilios' => $modelDomicilios,
                'modelPerfilEmpleados' => $modelPerfilEmpleados,
                'modelSubirFotoEmpleado' => $modelSubirFotoEmpleado,
                'modelSubirCVOriginal' => $modelSubirCVOriginal,
                'modelSubirCVEISEI' => $modelSubirCVEISEI,
                'extra' => $extra,
                'extraVals' => $extraVals,
                'modelDocumentosEmpleados' => $modelDocumentosEmpleados,
                'modelBitComentariosEmpleados' => $modelBitComentariosEmpleados,
                //'lugarnaciemiento' => $lugarnaciemiento,
                'modelAdministradoraBenef' => $modelAdministradoraBenef,
                'SecondmodelBenef' => $SecondmodelBenef,
                'modelContactos' => $modelContactos,
            ]);
        }
    }


    public function actionCambiar_domicilio($id)
    {
        $model = $this->findModel($id);
        $modelBitComentariosEmpleados = new TblBitComentariosEmpleados;
        $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        $modelDomicilios = tbldomicilios::find()->where(['PK_DOMICILIO' => $model->FK_DOMICILIO])->limit(1)->one();
        $modelContactos= TblContactos::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        //$lugarnaciemiento="";

        if(empty($modelContactos)){
            $modelContactos = new TblContactos;
        }



        $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();

        if(empty($modelAdministradoraBenef)){
            $modelAdministradoraBenef = '';
        }else{
            $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
             $SecondmodelBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();
        }

        if(empty($SecondmodelBenef)){
            $SecondmodelBenef = '';
        }

        if (!$modelDomicilios && !$modelContactos) {
            throw new NotFoundHttpException("El contacto no fue encontrado");
        }

        $modelSubirFotoEmpleado = new SubirArchivo();
        $modelSubirFotoEmpleado->extensions = 'png, jpg, jpeg';
        $modelSubirFotoEmpleado->noRequired = true;

        $modelSubirCVOriginal = new SubirArchivo();
        $modelSubirCVOriginal->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVOriginal->noRequired = true;

        $modelSubirCVEISEI = new SubirArchivo();
        $modelSubirCVEISEI->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVEISEI->noRequired = true;

        $modelDocumentosEmpleados = TblDocumentosEmpleados::find()->select('NOMBRE_DOCUMENTO, RUTA_DOCUMENTO, FECHA_CREACION')->where(['FK_EMPLEADO' => $id])->asArray()->all();

        $extra['DESC_ESTADO'] = tblcatestados::findOne($modelDomicilios->FK_ESTADO);
        $extra['DESC_MUNICIPIO'] = tblcatmunicipios::find()->where(['PK_MUNICIPIO'=>$modelDomicilios->FK_MUNICIPIO,'FK_ESTADO'=>$modelDomicilios->FK_ESTADO,'FK_PAIS'=>$modelDomicilios->FK_PAIS])->limit(1)->one();
        $extra['formCargar'] = '_form_update_domicilio';

        $extraVals['idTempArchivos'] = rand(1,999999);


        if ($modelDomicilios->load(Yii::$app->request->post()) || $modelContactos->load(Yii::$app->request->post())  ) {

            //Guardar la bitacora
            $descripcionBitacora = 'PK_EMPLEADO='.$model->PK_EMPLEADO.','.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$modelContactos->CASO_ACCIDENTE.' '.$model->APELLIDO_MAT_EMP.',PK_PERFIL='.$modelPerfilEmpleados->PK_PERFIL.',PK_DOMICILIO='.$model->FK_DOMICILIO;
            user_log_bitacora($descripcionBitacora,'Modificar Domicilio',$model->FK_DOMICILIO);
            $contactoifnot= TblContactos::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
            if(empty($contactoifnot)){
                $modelContactos->FK_EMPLEADO = $id;
                $modelContactos->FECHA_REGISTRO =   date('Y-m-d H:i:s');

            }
            $modelContactos->save(false);
            $modelDomicilios->save(false);
            return $this->redirect(['view', 'id' => $model->PK_EMPLEADO]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modelDomicilios' => $modelDomicilios,
                'modelContactos' => $modelContactos,
                'modelPerfilEmpleados' => $modelPerfilEmpleados,
                'modelSubirFotoEmpleado' => $modelSubirFotoEmpleado,
                'modelSubirCVOriginal' => $modelSubirCVOriginal,
                'modelSubirCVEISEI' => $modelSubirCVEISEI,
                //'lugarnaciemiento' => $lugarnaciemiento,
                'modelAdministradoraBenef' => $modelAdministradoraBenef,
                'SecondmodelBenef' => $SecondmodelBenef,
                'extra' => $extra,
                'extraVals' => $extraVals,
                'modelDocumentosEmpleados' => $modelDocumentosEmpleados,
                'modelBitComentariosEmpleados' => $modelBitComentariosEmpleados,
            ]);
        }
    }

    public function actionCambiar_perfil($id)
    {
        $model = $this->findModel($id);
        $modelBitComentariosEmpleados = new TblBitComentariosEmpleados;
        $modelProspectos = new TblProspectos;
        $modelProspectosPerfiles = new TblProspectosPerfiles;
        $modelBitProspecto = new TblBitProspectos;
        $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        $modelDomicilios = tbldomicilios::find()->where(['PK_DOMICILIO' => $model->FK_DOMICILIO])->limit(1)->one();
        $datosEmpleadosACargo = (new \yii\db\Query())
                            ->select('emp.PK_EMPLEADO, emp.NOMBRE_EMP, emp.APELLIDO_PAT_EMP, emp.APELLIDO_MAT_EMP')
                            ->from('tbl_empleados emp')
                            ->join('LEFT JOIN','tbl_perfil_empleados perfil',
                                    'emp.PK_EMPLEADO = perfil.FK_EMPLEADO')
                            ->where(['perfil.FK_JEFE_DIRECTO' => $id])
                            ->andWhere(['NOT IN','perfil.FK_ESTATUS_RECURSO',[4,6]])
                            ->all();
        $modelPuestos = TblCatPuestos::find()->select('PK_PUESTO')->where(['PERMITIR_SUBORDINADOS' => 1])->all();
        $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
        $modelVacacionesEmpleados = TblVacacionesEmpleado::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        $modelBitVacacionesEmpleado = TblBitVacacionesEmpleado::find()->where(['FK_VACACIONES_EMPLEADO' => $modelVacacionesEmpleados->PK_VACACIONES_EMPLEADO])->andWhere(['FK_TIPO_VACACIONES' => 1])->orderBy('FECHA_REGISTRO DESC')->limit(1)->one();

        if(empty($modelAdministradoraBenef)){
            $modelAdministradoraBenef = '';
        }else{
            $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
            $SecondmodelBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();
        }

        if(empty($SecondmodelBenef)){
            $SecondmodelBenef = '';
        }

        $modelSubirFotoEmpleado = new SubirArchivo();
        $modelSubirFotoEmpleado->extensions = 'png, jpg, jpeg';
        $modelSubirFotoEmpleado->noRequired = true;

        $modelSubirCVOriginal = new SubirArchivo();
        $modelSubirCVOriginal->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVOriginal->noRequired = true;

        $modelSubirCVEISEI = new SubirArchivo();
        $modelSubirCVEISEI->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVEISEI->noRequired = true;

        $modelPerfilEmpleados->FECHA_INGRESO = transform_date($modelPerfilEmpleados->FECHA_INGRESO,'d/m/Y');
        $estatusActual = $modelPerfilEmpleados->FK_ESTATUS_RECURSO;
        $permitirEmpleadosACargoActual = TblCatPuestos::find()->where(['PK_PUESTO' => $modelPerfilEmpleados->FK_PUESTO])->limit(1)->one();
        $modelDocumentosEmpleados = TblDocumentosEmpleados::find()->select('NOMBRE_DOCUMENTO, RUTA_DOCUMENTO, FECHA_CREACION')->where(['FK_EMPLEADO' => $id])->asArray()->all();
        $ultimaFechaBaja = TblBitComentariosEmpleados::find()->select(['MAX(FECHA_BAJA) FECHA_BAJA'])->where(['FK_EMPLEADO' => $id])->asArray()->one();
        $ultimaFechaBaja = $ultimaFechaBaja['FECHA_BAJA']?transform_date($ultimaFechaBaja['FECHA_BAJA'],'d/m/Y'):'';
        $extra['DESC_AREA'] = TblCatAreas::findOne($modelPerfilEmpleados->FK_AREA);
        $extra['DESC_PUESTO'] = TblCatPuestos::findOne($modelPerfilEmpleados->FK_PUESTO);
        $extra['DESC_ESTATUS_RECURSO'] = TblCatEstatusRecursos::findOne($modelPerfilEmpleados->FK_ESTATUS_RECURSO);
        $extra['ultima_fecha_vacaciones'] = isset($modelBitVacacionesEmpleado)?substr($modelBitVacacionesEmpleado->FECHA_REGISTRO,0,10):'';
        $extra['formCargar'] = '_form_update_perfil';
        //$extra['ultimaFechaOtorgaronVacaciones'] = TblBitVacacionesEmpleado::find()->where([])
        $extraVals['idTempArchivos'] = rand(1,999999);
        //Valida si el empleado tiene una asignacion pendiente o en ejecucion
        $modelAsignaciones = TblAsignaciones::find()
                                        ->where(['FK_EMPLEADO' => $id])
                                        ->andWhere(['IN','FK_ESTATUS_ASIGNACION',array(1,2,7)])//Asignaciones con estatus 'Pendiente' o en 'Ejecución' o 'Pendiente de aprobación'
                                        ->orderBy(['PK_ASIGNACION' => SORT_DESC])
                                        ->all();

        $modelSueldoEmpleado = TblIncidenciasNomina::find()->where(['FK_EMPLEADO'=> $modelPerfilEmpleados->FK_EMPLEADO, 'FK_TIPO_INCIDENCIA' => array(4,12), 'FK_ESTATUS_INCIDENCIA' => 1,])->andWhere('QUINCENA_APLICAR <= NOW()')->orderBy(['PK_INCIDENCIA_NOMINA'=>SORT_DESC])->limit(1)->one();

        if ($modelPerfilEmpleados->load(Yii::$app->request->post())) {
            $data= Yii::$app->request->post();

            $permitirEmpleadosACargoNuevo = TblCatPuestos::find()->where(['PK_PUESTO' => $modelPerfilEmpleados->FK_PUESTO])->limit(1)->one();
            $modelSubirCVOriginal->file = UploadedFile::getInstance($modelSubirCVOriginal, '[2]file');//Se carga el CV Original cargado en la vista
            $modelSubirCVEISEI->file = UploadedFile::getInstance($modelSubirCVEISEI, '[3]file');      //Se carga el CV EISEI cargado en la vista
            $modelPerfilEmpleados->FK_JEFE_DIRECTO = $data['hiddenJefeDirecto']==''?null:$data['hiddenJefeDirecto'];

            if($modelSubirCVOriginal->file){ //Valida si se cargo un nuevo CV Original

                if(strcmp($modelPerfilEmpleados->CV_ORIGINAL,'')!= 0){
                    if(file_exists('..'.$modelPerfilEmpleados->CV_ORIGINAL)){//Valida si ya existe una foto cargada
                    $status = unlink('..'.$modelPerfilEmpleados->CV_ORIGINAL);
                    }
                }
                $rutaGuardado = '../uploads/EmpleadosCVOriginal/';
                $nombre = 'cvOriginal_'.$id;
                $extensionCVOriginal = $modelSubirCVOriginal-> upload($rutaGuardado,$nombre);
                $modelPerfilEmpleados->CV_ORIGINAL='/uploads/EmpleadosCVOriginal/cvOriginal_'.$id.'.'.$extensionCVOriginal;
            }

            if($modelSubirCVEISEI->file){ // Valida si se cargo un nuevo CV EISEI
                if(strcmp($modelPerfilEmpleados->CV_EISEI,'')!= 0){
                    if(file_exists('..'.$modelPerfilEmpleados->CV_EISEI)){//Valida si ya existe una foto cargada
                        $status = unlink('..'.$modelPerfilEmpleados->CV_EISEI);
                    }
                }
                $rutaGuardado = '../uploads/EmpleadosCVEISEI/';
                $nombre = 'cvEISEI_'.$id;
                $extensionCVEISEI = $modelSubirCVEISEI-> upload($rutaGuardado,$nombre);
                $modelPerfilEmpleados->CV_EISEI='/uploads/EmpleadosCVEISEI/cvEISEI_'.$id.'.'.$extensionCVEISEI;
            } elseif($modelPerfilEmpleados->CV_EISEI == 'defoult'){
                $modelPerfilEmpleados->CV_EISEI = '';
            }


            //if ($modelBitComentariosEmpleados->load(Yii::$app->request->post()) ) {
            if(($modelPerfilEmpleados->FK_ESTATUS_RECURSO==4 || $modelPerfilEmpleados->FK_ESTATUS_RECURSO==6) && $estatusActual!=4 && $estatusActual!=6){

              $modelBitComentariosEmpleados->load(Yii::$app->request->post());
              $modelBitComentariosEmpleados->FECHA_REGISTRO=date('Y-m-d H:m:s');
              $modelBitComentariosEmpleados->FECHA_BAJA=transform_date($modelBitComentariosEmpleados->FECHA_BAJA,'Y-m-d');
              $modelBitComentariosEmpleados->FK_EMPLEADO=$id;
              $modelBitComentariosEmpleados->FK_USUARIO = user_info()['PK_USUARIO'];
              $modelBitComentariosEmpleados->MOTIVO = 'BAJA';
              $modelBitComentariosEmpleados->MOTIVO_CAT = $modelBitComentariosEmpleados->MOTIVO_CAT;
              $modelBitComentariosEmpleados->MOTIVO_SUBCAT = $modelBitComentariosEmpleados->MOTIVO_SUBCAT;
              $modelBitComentariosEmpleados->COMENTARIOS = $modelBitComentariosEmpleados->COMENTARIOS;
              $modelBitComentariosEmpleados->save(false);
              $modelPerfilEmpleados->TIPO_OPERACION = 'BAJA';
              $accionBitacora = 'Baja de Empleado';

              $modelProspectos->load(Yii::$app->request->post());
              //Valida si el empleado cuenta con la clave de prospecto

              if($modelPerfilEmpleados->FK_PROSPECTO != NULL){
                  // $modelProspectos = TblProspectos::find()->where(['PK_PROSPECTO' => $modelPerfilEmpleados->FK_PROSPECTO])->limit(1)->one();
                  $modelProspectos->PK_PROSPECTO = $modelPerfilEmpleados->FK_PROSPECTO;
                  // $modelProspectos->FK_ESTATUS = 1;
                  // $modelProspectos->FK_ORIGEN = 2;
                  // $modelProspectos->save(false);
              }
              // else{
                $modelProspectos->NOMBRE = $model->NOMBRE_EMP;
                $modelProspectos->APELLIDO_PATERNO = $model->APELLIDO_PAT_EMP;
                $modelProspectos->APELLIDO_MATERNO = $model->APELLIDO_MAT_EMP;
                $modelProspectos->CURP = $model->CURP_EMP;
                $modelProspectos->FK_GENERO = $model->FK_GENERO_EMP;
                $modelProspectos->FECHA_NAC = $model->FECHA_NAC_EMP;
                $modelProspectos->EMAIL = $model->EMAIL_EMP;
                $modelProspectos->CELULAR = $model->CELULAR;
                $modelProspectos->TELEFONO = '';
                $modelProspectos->COMENTARIOS = $modelProspectos->COMENTARIOS;
                $modelProspectos->FECHA_REGISTRO = date('Y-m-d');
                $modelProspectos->RECLUTADOR = 'NA';
                $modelProspectos->EXPECTATIVA = '0';
                $modelProspectos->DISPONIBILIDAD = 'NA';
                $modelProspectos->FECHA_CONVERSACION = date('Y-m-d');
                // $modelProspectos->LUGAR_RESIDENCIA = 1;
                // $modelProspectos->TIPO_CV = "NULL";
                // $modelProspectos->CV = $modelPerfilEmpleados->CV_ORIGINAL;
                $modelProspectos->TRABAJA_ACTUALMENTE = 'NO';
                $modelProspectos->FK_CANAL = 'NINGUNO';
                $modelProspectos->CAPACIDAD_RECURSO = $modelProspectos->CAPACIDAD_RECURSO;
                $modelProspectos->TACTO_CLIENTE = $modelProspectos->TACTO_CLIENTE;
                $modelProspectos->DESEMPENIO_CLIENTE = $modelProspectos->DESEMPENIO_CLIENTE;
                $modelProspectos->FK_ESTATUS = 1;
                $modelProspectos->FK_ESTADO = $modelProspectos->FK_ESTADO;
                $modelProspectos->FK_USUARIO = 0;
                $modelProspectos->FK_ORIGEN = 2;
                $modelProspectos->save(false);

                $fechaIngreso = strtotime(str_replace('/', '-',$modelPerfilEmpleados->FECHA_INGRESO));
                $modelPerfilEmpleados->FECHA_INGRESO = transform_date($modelPerfilEmpleados->FECHA_INGRESO,'Y-m-d');
                $modelPerfilEmpleados->FK_PROSPECTO = $modelProspectos->PK_PROSPECTO;
                $modelPerfilEmpleados->save(false);
              // }

              /*Ruta para guardar los CV*/
              $rutaGuardado = '../uploads/ProspectosCV/';

              /*Se pasa CVOriginal a prospectos como CVPersonal*/
              if (!empty($modelPerfilEmpleados->CV_ORIGINAL)) {
                $CVOriginal   = $modelPerfilEmpleados->CV_ORIGINAL;
                $infoFile     = pathInfo($CVOriginal);
                $nombre       = 'CVPERSONAL_'.$modelProspectos->PK_PROSPECTO.'_'.date('Y-m-d').'.'.$infoFile['extension'];
                if (file_exists('..'.$CVOriginal)) {
                  if (copy('..'.$CVOriginal, $rutaGuardado.''.$nombre)) {
                    /*Se inserta en la tabla de prospectos el elemento copiado*/
                    $modelProspectosDocumentos                  = new TblProspectosDocumentos();
                    $modelProspectosDocumentos->FK_PROSPECTO    = $modelProspectos->PK_PROSPECTO;
                    $modelProspectosDocumentos->FK_TIPO_CV      = 2;
                    $modelProspectosDocumentos->RUTA_CV         = '../'.$rutaGuardado.''.$nombre;
                    $modelProspectosDocumentos->FECHA_REGISTRO  = date('Y-m-d');
                    $modelProspectosDocumentos->save(false);
                  }
                }
              }
              /*Se pasa CVEisei a prospectos como CVInterno*/
              if (!empty($modelPerfilEmpleados->CV_EISEI)) {
                $CVEisei      = $modelPerfilEmpleados->CV_EISEI;
                $infoFile     = pathInfo($CVEisei);
                $nombre       = 'CVINTERNO_'.$modelProspectos->PK_PROSPECTO.'_'.date('Y-m-d').'.'.$infoFile['extension'];
                if (file_exists('..'.$CVEisei)) {
                  if (copy('..'.$CVEisei, $rutaGuardado.''.$nombre)) {
                    /*Se inserta en la tabla de prospectos el elemento copiado*/
                    $modelProspectosDocumentos                  = new TblProspectosDocumentos();
                    $modelProspectosDocumentos->FK_PROSPECTO    = $modelProspectos->PK_PROSPECTO;
                    $modelProspectosDocumentos->FK_TIPO_CV      = 1;
                    $modelProspectosDocumentos->RUTA_CV         = '../'.$rutaGuardado.''.$nombre;
                    $modelProspectosDocumentos->FECHA_REGISTRO  = date('Y-m-d');
                    $modelProspectosDocumentos->save(false);
                  }
                }
              }

              /*Buscar CVSTEFANINI en candidatos, con el FK de candidato asociado*/
              if (!empty($modelPerfilEmpleados->FK_CANDIDATO)) {
                /*Se consulta para saber si en la tabla Candidatos Documentos, está asociado el CVSTEFANINI*/
                $modelCandidatoCV = TblCandidatosDocumentos::find()->where(['FK_CANDIDATO' => $modelPerfilEmpleados->FK_CANDIDATO, 'FK_TIPO_CV' => 3])->one();
                /*Validación para saber si existe el registro*/
                if (!empty($modelCandidatoCV)) {
                  /*En caso de que si exista el registro, se toma la ruta del archivo y se eliminan
                  * los primeros tres caracteres para obtener la rutaa acceder al archivo
                  */
                  $archivoMover = substr($modelCandidatoCV->RUTA_CV, 3, strlen($modelCandidatoCV->RUTA_CV));
                  /*Se valida si el archivo existe en la carpeta CandidatosCV, si existe el archivo
                  * Se mueve a la carpeta de ProspectosCV y se hace el registro en la tabla ProspectosDocumentos*/
                  if (file_exists($archivoMover)) {
                    $infoFile     = pathInfo($archivoMover);
                    $nombre       = 'CVSTEFANINI_'.$modelProspectos->PK_PROSPECTO.'_'.date('Y-m-d').'.'.$infoFile['extension'];
                    if(copy($archivoMover, $rutaGuardado.''.$nombre)){
                      /*Se inserta en la tabla de prospectos el elemento copiado*/
                      $modelProspectosDocumentos                  = new TblProspectosDocumentos();
                      $modelProspectosDocumentos->FK_PROSPECTO    = $modelProspectos->PK_PROSPECTO;
                      $modelProspectosDocumentos->FK_TIPO_CV      = 3;
                      $modelProspectosDocumentos->RUTA_CV         = '../'.$rutaGuardado.''.$nombre;
                      $modelProspectosDocumentos->FECHA_REGISTRO  = date('Y-m-d');
                      $modelProspectosDocumentos->save(false);
                    }
                  }
                }
              }

              //$modelBitProspecto = new TblBitProspectos;
              $modelBitProspecto->FK_PROSPECTO = $modelPerfilEmpleados->FK_PROSPECTO;
              $modelBitProspecto->EMAIL = $modelProspectos->EMAIL;
              $modelBitProspecto->CELULAR = $modelProspectos->CELULAR;
              $modelBitProspecto->TELEFONO = $modelProspectos->TELEFONO;
              $modelBitProspecto->FK_ESTATUS = $modelProspectos->FK_ESTATUS;
              $modelBitProspecto->PERFIL = $modelProspectos->PERFIL;
              $modelBitProspecto->FECHA_CONVERSACION = $modelProspectos->FECHA_CONVERSACION;
              $modelBitProspecto->FK_ESTADO = $modelProspectos->FK_ESTADO;
              $modelBitProspecto->RECLUTADOR = $modelProspectos->RECLUTADOR;
              $modelBitProspecto->EXPECTATIVA = $modelProspectos->EXPECTATIVA;
              $modelBitProspecto->DISPONIBILIDAD_INTEGRACION = $modelProspectos->DISPONIBILIDAD_INTEGRACION;
              $modelBitProspecto->DISPONIBILIDAD_ENTREVISTA = $modelProspectos->DISPONIBILIDAD_ENTREVISTA;
              $modelBitProspecto->TRABAJA_ACTUALMENTE = $modelProspectos->TRABAJA_ACTUALMENTE;
              $modelBitProspecto->CANAL = $modelProspectos->FK_CANAL;
              $modelBitProspecto->SUELDO_ACTUAL = $modelProspectos->SUELDO_ACTUAL;
              $modelBitProspecto->COMENTARIOS = 'PASO DE EMPLEADO A PROSPECTO';
              $modelBitProspecto->FK_USUARIO = user_info()['PK_USUARIO'];
              $modelBitProspecto->FECHA_REGISTRO = date('Y-m-d');
              $modelBitProspecto->save(false);

              $EstadoProspecto = Yii::$app->request->post();
              $modelProspectosPerfiles->load(Yii::$app->request->post());
              // if (Yii::$app->request->post()) {
              //$data = Yii::$app->request->post();
              //var_dump($data['nivelPerfil']);
              if ($EstadoProspecto['TblProspectos']['FK_ESTADO']==1) {
                $nivelP = array_pop($data['nivelPerfil']);
                var_dump($data['nivelPerfil']);

                if($modelProspectosPerfiles->FK_PERFIL != '' || $modelProspectosPerfiles->FK_PERFIL != null){
                    foreach ($modelProspectosPerfiles->FK_PERFIL as $key1 => $value2) {
                        $modelProspectosPerfiles = new TblProspectosPerfiles;
                        $modelProspectosPerfiles->FK_PROSPECTO = $modelPerfilEmpleados->FK_PROSPECTO;
                        $modelProspectosPerfiles->FK_PERFIL = $value2;
                        $modelProspectosPerfiles->NIVEL_EXPERIENCIA = $data['nivelPerfil'][$key1];
                        $modelProspectosPerfiles->FECHA_REGISTRO = date('Y-m-d');
                        $modelProspectosPerfiles->save(false);
                    }
                }else if($modelProspectosPerfiles->FK_PERFIL == '' || $modelProspectosPerfiles->FK_PERFIL == null){
                        $modelProspectosPerfiles = new TblProspectosPerfiles;
                        $modelProspectosPerfiles->FK_PROSPECTO = $modelPerfilEmpleados->FK_PROSPECTO;
                        $modelProspectosPerfiles->FK_PERFIL = 0;
                        $modelProspectosPerfiles->FECHA_REGISTRO = date('Y-m-d');
                        $modelProspectosPerfiles->save(false);
                }
              }

                $fechaBaja = transform_date($modelBitComentariosEmpleados->FECHA_BAJA, 'd/m/Y');
                $nombreEmpleado = $model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP;
                $unidadNegocioEmpleado = $modelPerfilEmpleados->FK_UNIDAD_NEGOCIO;
                $this->enviarCorreoBaja($fechaBaja, $nombreEmpleado, $unidadNegocioEmpleado);
                //$this->limpiarVacaciones($id,$modelBitComentariosEmpleados->FECHA_BAJA);
                $this->finalizarIncidenciasRecurrentes($id,$modelBitComentariosEmpleados->FECHA_BAJA);

            } else {
                $accionBitacora = 'Modificación Perfil';
            }

            //Valida si la unidad de negocio cambia y de cumplirse esta condicion, guarda la bitacora de unidad de negocio
            if($modelPerfilEmpleados->FK_UNIDAD_NEGOCIO!=$data['FK_UNIDAD_NEGOCIO_ANTERIOR']){
                user_log_bitacora_unidad_negocio($id,$modelPerfilEmpleados->FK_UNIDAD_NEGOCIO,0);
            }

            //Valida si la ubicacion fisica cambia y de cumplirse esta condicion, guarda la bitacora de ubicacion fisica
            if($modelPerfilEmpleados->FK_UBICACION_FISICA!=$data['FK_UBICACION_FISICA_ANTERIOR']){
                user_log_bitacora_ubicacion_fisica($model->PK_EMPLEADO,$modelPerfilEmpleados->PK_PERFIL,$modelPerfilEmpleados->FK_UBICACION_FISICA,'PROPIA');
            }

            // Guarda la bitacora de estatus de recurso
            if(($estatusActual!=$modelPerfilEmpleados->FK_ESTATUS_RECURSO && $estatusActual!=4 && $estatusActual!=6) || ($estatusActual==4 && $modelPerfilEmpleados->FK_ESTATUS_RECURSO != 6) || ($estatusActual==6 && $modelPerfilEmpleados->FK_ESTATUS_RECURSO != 4)){
                user_log_bitacora_estatus_empleado($model->PK_EMPLEADO,$modelPerfilEmpleados->FK_ESTATUS_RECURSO);
            }

            // Guarda el model de TblPerfilempleados
            $fechaIngreso = strtotime(str_replace('/', '-',$modelPerfilEmpleados->FECHA_INGRESO));
            $modelPerfilEmpleados->FECHA_INGRESO = transform_date($modelPerfilEmpleados->FECHA_INGRESO,'Y-m-d');
            $modelPerfilEmpleados->FECHA_ACTUALIZA = date('Y-m-d H:i:s');
            $modelPerfilEmpleados->FK_RANK_TECNICO = $modelPerfilEmpleados->FK_RANK_TECNICO==null?'0':$modelPerfilEmpleados->FK_RANK_TECNICO;
            $modelPerfilEmpleados->ID_EMP_ADMINISTRADORA = isset($modelPerfilEmpleados->ID_EMP_ADMINISTRADORA) ? trim($modelPerfilEmpleados->ID_EMP_ADMINISTRADORA) : 'Sin ID Empleado';
            //Guarda Modelo de Perfil
            $modelPerfilEmpleados->save(false);

            //Valida si hay que reasignar los empleados a cargo a un nuevo jefe
            if(($permitirEmpleadosACargoActual->PERMITIR_SUBORDINADOS=='1' && $permitirEmpleadosACargoNuevo->PERMITIR_SUBORDINADOS=='0') || (($modelPerfilEmpleados->FK_ESTATUS_RECURSO==4 || $modelPerfilEmpleados->FK_ESTATUS_RECURSO==6) && $estatusActual!=4 && $estatusActual!=6) ){
                if(isset($data['jefe_directo'])){
                   foreach ($data['jefe_directo'] as $key => $value) {
                        if($value != ''){
                            $modelPerfilNuevoJefe = tblperfilempleados::find()->where(['FK_EMPLEADO' => $key])->limit(1)->one();
                            $modelPerfilNuevoJefe->FK_JEFE_DIRECTO = $value;
                            $modelPerfilNuevoJefe->save(false);
                        }
                    }
                }
            }

            //Guardar la bitacora
            $descripcionBitacora = 'PK_EMPLEADO='.$model->PK_EMPLEADO.','.$model->NOMBRE_EMP.' '.$model->APELLIDO_PAT_EMP.' '.$model->APELLIDO_MAT_EMP.',PK_PERFIL='.$modelPerfilEmpleados->PK_PERFIL.',PK_DOMICILIO='.$model->FK_DOMICILIO.',FK_ESTATUS_RECURSO='.$modelPerfilEmpleados->FK_ESTATUS_RECURSO;
            user_log_bitacora($descripcionBitacora,$accionBitacora,$modelPerfilEmpleados->PK_PERFIL);

            //12.- Guarda la bitacora de fechas de ingreso
            if($modelPerfilEmpleados->FK_ESTATUS_RECURSO!= 4 && $modelPerfilEmpleados->FK_ESTATUS_RECURSO!= 6 && ($data['FK_ESTATUS_RECURSO_ANTERIOR'] == 4 || $data['FK_ESTATUS_RECURSO_ANTERIOR'] == 6)){
                user_log_bitacora_fecha_ingreso_empleado($model->PK_EMPLEADO,$modelPerfilEmpleados->FECHA_INGRESO);
                $quincena_aplicar = '';
                if(date('j',$fechaIngreso) <= 15){
                    $quincena_aplicar = date('Y-m',$fechaIngreso).'-15';
                }else{
                    $month = date('Y-m',$fechaIngreso);
                    $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
                    $last_day = date('Y-m-d', strtotime("{$aux} - 1 day"));
                    $quincena_aplicar = $last_day;
                }
                $modelIncidenciasSueldoInicial = new TblIncidenciasNomina;
                $modelIncidenciasSueldoInicial->FK_EMPLEADO = $model->PK_EMPLEADO;
                $modelIncidenciasSueldoInicial->FK_ADMINISTRADORA = $modelPerfilEmpleados->FK_ADMINISTRADORA;
                $modelIncidenciasSueldoInicial->FK_TIPO_INCIDENCIA = 12;
                $modelIncidenciasSueldoInicial->FK_ESTATUS_INCIDENCIA = 1;
                $modelIncidenciasSueldoInicial->DIAS = NULL;
                $modelIncidenciasSueldoInicial->VALOR = $modelPerfilEmpleados->SUELDO_NETO;
                $modelIncidenciasSueldoInicial->QUINCENA_APLICAR = $quincena_aplicar;
                $modelIncidenciasSueldoInicial->VIGENCIA = $quincena_aplicar;
                $modelIncidenciasSueldoInicial->FECHA_BAJA = NULL;
                $modelIncidenciasSueldoInicial->PORCENTAJE_INCAPACIDAD = NULL;
                $modelIncidenciasSueldoInicial->IMSS = NULL;
                $modelIncidenciasSueldoInicial->COMENTARIOS = 'SALARIO INICIAL';
                $modelIncidenciasSueldoInicial->DESCUENTO_BONIFICACION = NULL;
                $modelIncidenciasSueldoInicial->FK_USUARIO = user_info()['PK_USUARIO'];
                $modelIncidenciasSueldoInicial->FECHA_REGISTRO = date('Y-m-d H:i:s');
                $modelIncidenciasSueldoInicial->save(false);
            }

            $modelPerfilEmpleados->SUELDO_NETO = isset($modelSueldoEmpleado->VALOR) ? $modelSueldoEmpleado->VALOR : $modelPerfilEmpleados->SUELDO_NETO;
            return $this->redirect(['view', 'id' => $model->PK_EMPLEADO]);
        } else {
            $modelPerfilEmpleados->SUELDO_NETO = isset($modelSueldoEmpleado->VALOR) ? $modelSueldoEmpleado->VALOR : $modelPerfilEmpleados->SUELDO_NETO;
            return $this->render('_form_update_perfil', [
                'model' => $model,
                'modelDomicilios' => $modelDomicilios,
                'modelPerfilEmpleados' => $modelPerfilEmpleados,
                'modelSubirFotoEmpleado' => $modelSubirFotoEmpleado,
                'modelSubirCVOriginal' => $modelSubirCVOriginal,
                'modelSubirCVEISEI' => $modelSubirCVEISEI,
                'modelAdministradoraBenef' => $modelAdministradoraBenef,
                'SecondmodelBenef' => $SecondmodelBenef,
                'extra' => $extra,
                'extraVals' => $extraVals,
                'modelDocumentosEmpleados' => $modelDocumentosEmpleados,
                'modelBitComentariosEmpleados' => $modelBitComentariosEmpleados,
                'modelAsignaciones' => $modelAsignaciones,
                'datosEmpleadosACargo' => $datosEmpleadosACargo,
                'modelPuestos' => $modelPuestos,
                'ultimaFechaBaja' => $ultimaFechaBaja,
                'modelProspectosPerfiles' => $modelProspectosPerfiles,
                'modelProspectos' => $modelProspectos
            ]);
        }

    }

    public function actionActualiza_perfil()
    {

        if(Yii::$app->request->isAjax){
            $data= Yii::$app->request->post();

            $id = $data['idRecurso'];

            $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
            $modelPerfilEmpleados->FK_ESTATUS_RECURSO = 3;
            $modelPerfilEmpleados->save(false);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $this->redirect(['empleados/index_asignables']);

            return [
                'data' => $data,
            ];
        }
    }


    public function actionCambiar_documentos($id)
    {
        $model = $this->findModel($id);
        $modelBitComentariosEmpleados = new TblBitComentariosEmpleados;
        $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        $modelDomicilios = tbldomicilios::find()->where(['PK_DOMICILIO' => $model->FK_DOMICILIO])->limit(1)->one();
        $modelContactos= TblContactos::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        //$lugarnaciemiento = "";
        $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();

        if(empty($modelAdministradoraBenef)){
            $modelAdministradoraBenef = '';
        }else{
            $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
             $SecondmodelBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();
        }

        if(empty($SecondmodelBenef)){
            $SecondmodelBenef = '';
        }


        $modelSubirFotoEmpleado = new SubirArchivo();
        $modelSubirFotoEmpleado->extensions = 'png, jpg, jpeg';
        $modelSubirFotoEmpleado->noRequired = true;

        $modelSubirCVOriginal = new SubirArchivo();
        $modelSubirCVOriginal->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVOriginal->noRequired = true;

        $modelSubirCVEISEI = new SubirArchivo();
        $modelSubirCVEISEI->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVEISEI->noRequired = true;

        $modelDocumentosEmpleados = TblDocumentosEmpleados::find()->select('NOMBRE_DOCUMENTO, RUTA_DOCUMENTO, FECHA_CREACION')->where(['FK_EMPLEADO' => $id])->asArray()->all();

        $extra['DESC_ESTADO'] = tblcatestados::findOne($modelDomicilios->FK_ESTADO);
        $extra['DESC_MUNICIPIO'] = tblcatmunicipios::find()->where(['PK_MUNICIPIO'=>$modelDomicilios->FK_MUNICIPIO,'FK_ESTADO'=>$modelDomicilios->FK_ESTADO,'FK_PAIS'=>$modelDomicilios->FK_PAIS])->limit(1)->one();
        $extra['formCargar'] = '_form_update_documentos';

        $extraVals['idTempArchivos'] = rand(1,999999);

        if(strcmp($modelPerfilEmpleados->CV_EISEI,'')==0){
            $modelPerfilEmpleados->CV_EISEI = 'defoult';
        }
        $data= Yii::$app->request->post();
        $banderaPost = isset($data['postButton'])?1:0;

        if ($banderaPost == 1) {
            return $this->redirect(['view', 'id' => $model->PK_EMPLEADO]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modelDomicilios' => $modelDomicilios,
                'modelPerfilEmpleados' => $modelPerfilEmpleados,
                'modelSubirFotoEmpleado' => $modelSubirFotoEmpleado,
                'modelSubirCVOriginal' => $modelSubirCVOriginal,
                'modelSubirCVEISEI' => $modelSubirCVEISEI,
                'modelAdministradoraBenef' => $modelAdministradoraBenef,
                //'lugarnaciemiento' => $lugarnaciemiento,
                'SecondmodelBenef' => $SecondmodelBenef,
                'extra' => $extra,
                'extraVals' => $extraVals,
                'modelDocumentosEmpleados' => $modelDocumentosEmpleados,
                'modelBitComentariosEmpleados' => $modelBitComentariosEmpleados,
                'modelContactos' => $modelContactos,
            ]);
        }
    }

        public function actionCambiar_beneficiario($id)
    {
        $model = $this->findModel($id);
        $modelBitComentariosEmpleados = new TblBitComentariosEmpleados;
        $modelDomicilios = tbldomicilios::find()->where(['PK_DOMICILIO' => $model->FK_DOMICILIO])->limit(1)->one();
        $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        $modelContactos= TblContactos::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        //$lugarnaciemiento="";

        $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
        $SecondmodelBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();

        if(empty($modelAdministradoraBenef)){
            $modelAdministradoraBenef = new TblBeneficiario();
        }else{
            $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
             $SecondmodelBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();
        }

        if(empty($SecondmodelBenef)){
            $SecondmodelBenef = new TblBeneficiario();
        }

        $modelSubirFotoEmpleado = new SubirArchivo();
        $modelSubirFotoEmpleado->extensions = 'png, jpg, jpeg';
        $modelSubirFotoEmpleado->noRequired = true;

        $modelSubirCVOriginal = new SubirArchivo();
        $modelSubirCVOriginal->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVOriginal->noRequired = true;

        $modelSubirCVEISEI = new SubirArchivo();
        $modelSubirCVEISEI->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVEISEI->noRequired = true;

        $modelDocumentosEmpleados = TblDocumentosEmpleados::find()->select('NOMBRE_DOCUMENTO, RUTA_DOCUMENTO, FECHA_CREACION')->where(['FK_EMPLEADO' => $id])->asArray()->all();

        $extra['formCargar'] = '_form_update_beneficiario';
        $extraVals['idTempArchivos'] = rand(1,999999);


        if ($modelAdministradoraBenef->load(Yii::$app->request->post()) ) {

            //Guardar la bitacora
            $descripcionBitacora = 'ACTUALIZAR_BENEFICIARIO ='.$modelAdministradoraBenef->PK_ADM_BENEF.', PK_ADM_BENEF = '.$modelAdministradoraBenef->PK_ADM_BENEF;
            user_log_bitacora($descripcionBitacora,'Actualizacion de Beneficiarios',$modelAdministradoraBenef->PK_ADM_BENEF);

            $modelAdministradoraBenef->save(false);
            //$SecondmodelBenef->save(false);
            return $this->redirect(['view', 'id' => $model->PK_EMPLEADO]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modelDomicilios' => $modelDomicilios,
                'modelPerfilEmpleados' => $modelPerfilEmpleados,
                'modelSubirFotoEmpleado' => $modelSubirFotoEmpleado,
                'modelSubirCVOriginal' => $modelSubirCVOriginal,
                'modelSubirCVEISEI' => $modelSubirCVEISEI,
                'extra' => $extra,
                'extraVals' => $extraVals,
                'modelDocumentosEmpleados' => $modelDocumentosEmpleados,
                'modelBitComentariosEmpleados' => $modelBitComentariosEmpleados,
                //'lugarnaciemiento' => $lugarnaciemiento,
                'modelAdministradoraBenef' => $modelAdministradoraBenef,
                'SecondmodelBenef' => $SecondmodelBenef,
                'modelContactos' => $modelContactos,
            ]);
        }
    }

        public function actionCambiar_segundobeneficiario($id)
    {
        $model = $this->findModel($id);
        $modelBitComentariosEmpleados = new TblBitComentariosEmpleados;
        $modelDomicilios = tbldomicilios::find()->where(['PK_DOMICILIO' => $model->FK_DOMICILIO])->limit(1)->one();
        $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
        $modelAdministradoraBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->limit(1)->one();
        $SecondmodelBenef = TblBeneficiario::find()->where(['FK_EMPLEADO'=> $id])->andWhere(['not in','PK_ADM_BENEF', $modelAdministradoraBenef->PK_ADM_BENEF])->limit(1)->one();
        $modelContactos= TblContactos::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();

        if(empty($SecondmodelBenef)){
            $SecondmodelBenef = new TblBeneficiario();
        }

        $modelSubirFotoEmpleado = new SubirArchivo();
        $modelSubirFotoEmpleado->extensions = 'png, jpg, jpeg';
        $modelSubirFotoEmpleado->noRequired = true;

        $modelSubirCVOriginal = new SubirArchivo();
        $modelSubirCVOriginal->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVOriginal->noRequired = true;

        $modelSubirCVEISEI = new SubirArchivo();
        $modelSubirCVEISEI->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCVEISEI->noRequired = true;

        $modelDocumentosEmpleados = TblDocumentosEmpleados::find()->select('NOMBRE_DOCUMENTO, RUTA_DOCUMENTO, FECHA_CREACION')->where(['FK_EMPLEADO' => $id])->asArray()->all();

        $extra['formCargar'] = '_form_update_segundo_beneficiario';
        $extraVals['idTempArchivos'] = rand(1,999999);

        if ($SecondmodelBenef->load(Yii::$app->request->post()) ) {
            //Guardar la bitacora
            $descripcionBitacora = 'ACTUALIZAR_BENEFICIARIO ='.$SecondmodelBenef->PK_ADM_BENEF.', PK_ADM_BENEF = '.$SecondmodelBenef->PK_ADM_BENEF;
            user_log_bitacora($descripcionBitacora,'Actualizacion de Beneficiarios',$SecondmodelBenef->PK_ADM_BENEF);

            $SecondmodelBenef->save(false);
            return $this->redirect(['view', 'id' => $model->PK_EMPLEADO]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modelDomicilios' => $modelDomicilios,
                'modelPerfilEmpleados' => $modelPerfilEmpleados,
                'modelSubirFotoEmpleado' => $modelSubirFotoEmpleado,
                'modelSubirCVOriginal' => $modelSubirCVOriginal,
                'modelSubirCVEISEI' => $modelSubirCVEISEI,
                'extra' => $extra,
                'extraVals' => $extraVals,
                'modelDocumentosEmpleados' => $modelDocumentosEmpleados,
                'modelBitComentariosEmpleados' => $modelBitComentariosEmpleados,
                'modelAdministradoraBenef' => $modelAdministradoraBenef,
                'SecondmodelBenef' => $SecondmodelBenef,
                'modelContactos' => $modelContactos,
            ]);
        }
    }
    /**
     * Deletes an existing tblempleados model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    /*
    public function actionDarBajaEmpleado(){
        $modelBitComentariosEmpleados = new TblBitComentariosEmpleados();
        if ($modelBitComentariosEmpleados->load(Yii::$app->request->post()) ){
            $data = Yii::$app->request->post();
            dd($data);
            $modelBitComentariosEmpleados->FECHA_REGISTRO = date('Y-m-d');
            $modelBitComentariosEmpleados->FK_ASIGNACION = $data['idAsignacion'];
            $modelBitComentariosEmpleados->save(false);

            $modelPerfilEmpleados = tblperfilempleados::find()->where(['FK_EMPLEADO' => $id])->limit(1)->one();
            //$modelAsignaciones = TblAsignaciones::find()->where(['PK_ASIGNACION' => $data['idAsignacion']])->limit(1)->one();
            $modelPerfilEmpleados->FK_ESTATUS_RECURSO = ($modelPerfilEmpleados->FK_TIPO_SERVICIO==1?6:4);
            $modelPerfilEmpleados->FECHA_ACTUALIZA = date('Y-m-d H:i:s');
            $modelPerfilEmpleados->save(false);
        }
        return $this->redirect(['index']);
    }*/

    public function actionEnvio_correo_intento_baja()
    {
        if (Yii::$app->request->isAjax){
            //Recepción de variables de entrada
            $data = Yii::$app->request->post();
            $idEmpleado= explode(":", $data['idEmpleado']);
            $idEmpleado = $idEmpleado[0];
            $FK_UNIDAD_NEGOCIO_ANTERIOR= explode(":", $data['FK_UNIDAD_NEGOCIO_ANTERIOR']);
            $FK_UNIDAD_NEGOCIO_ANTERIOR = $FK_UNIDAD_NEGOCIO_ANTERIOR[0];
            $NOMBRE_EMPLEADO= explode(":", $data['NOMBRE_EMPLEADO']);
            $NOMBRE_EMPLEADO = $NOMBRE_EMPLEADO[0];
            $fechaHoraHoy = date('d/m/Y H:i:s');
            $envioCorreo =false;

            //Obtener Puestos para Correo
            $puestosEnviarCorreo= explode(',',get_config('EMPLEADOS','PUESTOS_ENVIO_CORREOS_BAJA'));

            //Obtener datos de usuario
            $nombreUsuario = $_SESSION['usuario']['NOMBRE_COMPLETO'];

            //Obtener Unidad de Negocio
            $unidadNegocioExcepcion1 = explode(',',get_config('CONFIG','EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'));
            if(in_array($FK_UNIDAD_NEGOCIO_ANTERIOR, $unidadNegocioExcepcion1)){
                $unidadNegocio = $unidadNegocioExcepcion1;
            } else {
                $unidadNegocio[] = $FK_UNIDAD_NEGOCIO_ANTERIOR;
            }

            //Obtener correos de Destino
            $arrayCorreos = [];
            $query = new Query;
            $query->select('emp.EMAIL_INTERNO, emp.NOMBRE_EMP, emp.APELLIDO_PAT_EMP, emp.APELLIDO_MAT_EMP')
                ->from('tbl_perfil_empleados as perfil')
                ->join('LEFT JOIN','tbl_empleados as emp',
                    'perfil.FK_EMPLEADO = emp.PK_EMPLEADO')
                ->where(['IN','perfil.FK_PUESTO', $puestosEnviarCorreo])
                ->andWhere(['IN','perfil.FK_UNIDAD_NEGOCIO',$unidadNegocio])
                ->andWhere(['NOT IN','perfil.FK_ESTATUS_RECURSO',array(4,6)])
                ->distinct();
            $command = $query->createCommand();
            $rows = $command->queryAll();
            foreach($rows as $array){
                if($array['EMAIL_INTERNO']){
                    $arrayCorreos[] = $array['EMAIL_INTERNO'];
                }
            }

            if(count($arrayCorreos) > 0){
                // Variables de envio de correo
                $de= get_config('EMPLEADOS','CORREO_FELICITACIONES');
                $para=$arrayCorreos;
                $asunto= 'Baja de Asignación';
                $mensaje="Buen día <br><br>
                El usuario <b>$nombreUsuario</b> intento dar de baja al empleado <u>$NOMBRE_EMPLEADO</u> el cual esta <b>asignado - proyecto</b>, favor de revisarlo.<br><br>
                $fechaHoraHoy";

                //Envio de correo
                $enviado = send_mail($de,$para, $asunto, $mensaje,[]);
                $envioCorreo=true;
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'envioCorreo' => $envioCorreo,
            ];
        }
    }
    public function envio_correo_asignable()
    {
         $url=server().get_upload_url();

            //Recepción de variables de entrada
            $data = Yii::$app->request->post();
            //$envioCorreoAsignacion=false;
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
           if(count($arrayCorreos) > 0){

                // Variables de envio de correo
                $de= get_config('Asignaciones','CORREO_REMITENTE_ASIGNABLE');
                $para=$arrayCorreos;
                $asunto= 'Se registro un nuevo ingreso considerado para asignación';
                $mensaje="<style>p {font-family: Calibri; font-size: 11pt;}</style>
                <p>Buen día,<b>$nombreUsuario<b/>.<br><br>
                Se le notifica que se registró un nuevo ingreso considerado para asignación.
                <br/>
                <br/>
                ". "<a href='$url/web/empleados/index_asignables');' style='color: #337ab7;'>Ver ingresos considerados para asignación</a><br/>"
                ."Saludos...</p>
                <br/>
             "."<img src='$url/web/iconos/correos/firmaErt.jpg'>";

                //Envio de correo
                $enviado = send_mail($de,$para, $asunto, $mensaje,[]);
                //$envioCorreoAsignacion=true;
            }

            // \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            // return [
            //     'envioCorreoAsignacion' => $envioCorreoAsignacion,
            // ];

    }

    public function actionValidar_campos()
    {
        if (Yii::$app->request->isAjax){
            $data = Yii::$app->request->post();

            if(isset($data['pk_empleado'])){
                $pk_empleado= explode(":", $data['pk_empleado']);
                $pk_empleado = $pk_empleado[0];
            } else {
                $pk_empleado = 0;
            }

            $curp= explode(":", $data['curp']);
            $curp = $curp[0];
            $nss= explode(":", $data['nss']);
            $nss = $nss[0];
            $rfc= explode(":", $data['rfc']);
            $rfc = $rfc[0];
            $rfc = str_replace('-', '', $rfc);
            $curpRepetido = false;
            $nssRepetido = false;
            $rfcRepetido  = false;

            if(strlen($curp) == 18){
                $modelPerfilEmpleadosCURP = TblEmpleados::find()->where(['CURP_EMP' => $curp])->andWhere(['NOT IN','PK_EMPLEADO',$pk_empleado])->all();
                if(count($modelPerfilEmpleadosCURP)>0){
                $curpRepetido = true;
                }
            }

            if(strlen($nss) > 0){
                $modelPerfilEmpleadosNSS = TblEmpleados::find()->where(['NSS_EMP' => $nss])->andWhere(['NOT IN','PK_EMPLEADO',$pk_empleado])->all();
                if(count($modelPerfilEmpleadosNSS)>0){
                    $nssRepetido = true;
                }
            }

            if(strlen($rfc) == 13){
                $modelPerfilEmpleadosRFC = TblEmpleados::find()->where(['RFC_EMP' => $rfc])->andWhere(['NOT IN','PK_EMPLEADO',$pk_empleado])->all();
                if(count($modelPerfilEmpleadosRFC)>0){
                    $rfcRepetido = true;
                }
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'curpRepetido' => $curpRepetido,
                'nssRepetido' => $nssRepetido,
                'rfcRepetido' => $rfcRepetido,
                'code' => 100,
            ];
        }
    }

    public function finalizarIncidenciasRecurrentes($FK_EMPLEADO, $FECHA_BAJA)
    {
        $diaBaja = substr($FECHA_BAJA,8,2);
        $mesBaja = substr($FECHA_BAJA,5,2);
        $anioBaja = substr($FECHA_BAJA,0,4);
        if($diaBaja <= 14){
            $mesBaja--;
            if($mesBaja==0){
                $mesBaja='12';
                $anioBaja--;
            }
            if(in_array($mesBaja, [1,3,5,7,8,10,12])){
                $diaBaja = 31;
            }elseif(in_array($mesBaja, [4,6,9,11])){
                $diaBaja = 30;
            }elseif($mesBaja==2){
                if(fmod($anioBaja,4)==0){
                    $diaBaja = 29;
                } else {
                    $diaBaja = 28;
                }
            }
        } else {
            $diaBaja = 15;
        }
        $quincenaFinaliza = $anioBaja.'-'.(strlen($mesBaja)==1?('0'.$mesBaja):$mesBaja).'-'.(strlen($diaBaja)==1?('0'.$diaBaja):$diaBaja);

        //Se finalizan las incidencias recurrentes
        $modelIncidenciasRecurrentes = TblIncidenciasNomina::find()->where("FK_EMPLEADO = $FK_EMPLEADO
                                                                AND
                                                                QUINCENA_APLICAR <> VIGENCIA
                                                                AND
                                                                VIGENCIA > '$quincenaFinaliza'
                                                                AND
                                                                QUINCENA_APLICAR <= '$quincenaFinaliza'")->all();
        foreach($modelIncidenciasRecurrentes as $array){
            $array->VIGENCIA = $quincenaFinaliza;
            $array->save(false);
        }
        //Se seleccionan las incidencias qcuya fecha de QUINCENA_APLICAR es mayor a la quincena de baja, y se cancelan estas incidencias
        $modelIncidenciasCancelar = TblIncidenciasNomina::find()->where(['FK_EMPLEADO' => $FK_EMPLEADO])->andWhere(['>','QUINCENA_APLICAR', $quincenaFinaliza])->andWhere(['FK_ESTATUS_INCIDENCIA' => 1])->all();
        foreach($modelIncidenciasCancelar as $array){
            $array->FK_ESTATUS_INCIDENCIA = 2;
            $array->save(false);
        }
    }

    /*public function limpiarVacaciones($FK_EMPLEADO, $FECHA_BAJA)
    {
        $modelVacacionesEmpleados = TblVacacionesEmpleado::find()->where(['FK_EMPLEADO'=>$FK_EMPLEADO])->one();
        $modelBitVacacionesEmpleado = TblBitVacacionesEmpleado::find()
                            ->where(['FK_VACACIONES_EMPLEADO' => $modelVacacionesEmpleados->PK_VACACIONES_EMPLEADO])
                            ->andWhere(['FK_TIPO_VACACIONES' => 2])
                            ->all();
        $arrayPK_BIT_VACACIONES_EMPLEADO = [];
        foreach($modelBitVacacionesEmpleado as $array){
            $arrayPK_BIT_VACACIONES_EMPLEADO[] = $array['PK_BIT_VACACIONES_EMPLEADO'];
        }

        //Comprueba si el empleado tuvo en algun momento derecho vacaciones
        $modelBitComprobaciones = TblBitVacacionesEmpleado::find()
                            ->where(['FK_VACACIONES_EMPLEADO' => $modelVacacionesEmpleados->PK_VACACIONES_EMPLEADO])
                            ->andWhere(['FK_TIPO_VACACIONES' => 1])
                            ->all();

        //Se declara la conexion
        $connection = \Yii::$app->db;
        //$transaction = $connection->beginTransaction();

        //Se verifica si en algun momento el empleado tomo vacaciones
        if(count($arrayPK_BIT_VACACIONES_EMPLEADO) > 0){
            //Se obtienen las vacaciones programadas del empleado
            unset($modelBitVacacionesEmpleado);
            $modelFechasVacaciones = TblFechasVacaciones::find()
                                ->where(['IN','FK_BIT_VACACIONES_EMPLEADO',$arrayPK_BIT_VACACIONES_EMPLEADO])
                                ->andWhere(['>', 'FECHA_SOLICITADA', $FECHA_BAJA])
                                ->all();

            //Se eliminan las vacaciones programados del empleados, es decir, dias que se iba a tomar despues de su fecha de baja
            foreach($modelFechasVacaciones as $arrayFechasVacaciones){
                $modelBitVacacionesEmpleado = TblBitVacacionesEmpleado::find()->where(['PK_BIT_VACACIONES_EMPLEADO' => $arrayFechasVacaciones->FK_BIT_VACACIONES_EMPLEADO])->one();
                if($modelBitVacacionesEmpleado->APLICA_2X1 == 1){
                    $modelBitVacacionesEmpleado->APLICA_2X1 = 0;
                } elseif($modelBitVacacionesEmpleado->DIAS_HORAS_EXTRAS > 0) {
                    $modelBitVacacionesEmpleado->DIAS_HORAS_EXTRAS--;
                    $modelVacacionesEmpleados->DIAS_HORAS_EXTRAS++;
                } elseif($modelBitVacacionesEmpleado->DIAS_DISPONIBLES > 0){
                    $modelBitVacacionesEmpleado->DIAS_DISPONIBLES--;
                    $modelVacacionesEmpleados->DIAS_DISPONIBLES++;
                } elseif($modelBitVacacionesEmpleado->DIAS_PERIODO_ANTERIOR > 0){
                    $modelBitVacacionesEmpleado->DIAS_PERIODO_ANTERIOR--;
                    $modelVacacionesEmpleados->DIAS_PERIODO_ANTERIOR++;
                }
                $descripcionBitacora = 'PK_BIT_VACACIONES_EMPLEADO='.$modelBitVacacionesEmpleado->PK_BIT_VACACIONES_EMPLEADO
                                            .',PK_FECHAS_VACACIONES='.$arrayFechasVacaciones->PK_FECHAS_VACACIONES
                                            .',DIAS_DISP_INI='.$modelBitVacacionesEmpleado->getOldAttributes()['DIAS_DISPONIBLES']
                                            .',DIAS_DISP_FIN='.$modelBitVacacionesEmpleado->DIAS_DISPONIBLES
                                            .',DIAS_ANT_INI='.$modelVacacionesEmpleados->getOldAttributes()['DIAS_PERIODO_ANTERIOR']
                                            .',DIAS_ANT_FIN='.$modelVacacionesEmpleados->DIAS_PERIODO_ANTERIOR
                                            .',DIAS_HRS_EXS_INI='.$modelBitVacacionesEmpleado->getOldAttributes()['DIAS_HORAS_EXTRAS']
                                            .',DIAS_HRS_EXS_FIN='.$modelBitVacacionesEmpleado->DIAS_HORAS_EXTRAS
                                            .',AP_2X1_INI='.$modelBitVacacionesEmpleado->getOldAttributes()['APLICA_2X1']
                                            .',AP_2X1_FIN='.$modelBitVacacionesEmpleado->APLICA_2X1
                                            .',FECHAS_ELIMINADAS='.$arrayFechasVacaciones->FECHA_SOLICITADA;

                user_log_bitacora($descripcionBitacora,'Eliminacion de dias programados por baja de empleado',$modelBitVacacionesEmpleado->PK_BIT_VACACIONES_EMPLEADO);
                $modelBitVacacionesEmpleado->save(false);
                $arrayFechasVacaciones->delete();
            }

            //Si el empleado nunca tuvo derecho a vacaciones se eliminan los dias de vacaciones que tomo por adelantado
            if(count($modelBitComprobaciones) == 0){
                $fechasVacacionesTomada = TblFechasVacaciones::find()->where(['IN','FK_BIT_VACACIONES_EMPLEADO',$arrayPK_BIT_VACACIONES_EMPLEADO])->asArray()->all();
                $bitacoras = '';
                foreach($fechasVacacionesTomada as $array){
                    $bitacoras.= $array['FK_BIT_VACACIONES_EMPLEADO'].',';
                    $descripcionBitacora = 'PK_BIT_VACACIONES_EMPLEADO='.$array['FK_BIT_VACACIONES_EMPLEADO']
                                            .',PK_FECHAS_VACACIONES='.$array['PK_FECHAS_VACACIONES']
                                            .',FECHA_ELIMINADA='.transform_date($array['FECHA_SOLICITADA'],'d/m/Y');
                    user_log_bitacora($descripcionBitacora,'Eliminacion de dias de vacaciones',$array['PK_FECHAS_VACACIONES']);
                    $modelFechasVacaciones = TblFechasVacaciones::find()->andWhere(['PK_FECHAS_VACACIONES' => $array['PK_FECHAS_VACACIONES']])->one();
                    $modelFechasVacaciones->delete();
                }
                if($bitacoras!=''){
                    TblBitVacacionesEmpleado::deleteAll('PK_BIT_VACACIONES_EMPLEADO IN ('.trim($bitacoras,',').')');
                }
            }
        }

        //Elimina Bitacoras con DIAS_DISPONIBLES = 0 y DIAS_HORAS_EXTRAS = 0
        TblBitVacacionesEmpleado::deleteAll('FK_VACACIONES_EMPLEADO = '.$modelVacacionesEmpleados->PK_VACACIONES_EMPLEADO.' AND FK_TIPO_VACACIONES = 2 AND DIAS_DISPONIBLES = 0 AND DIAS_HORAS_EXTRAS = 0 AND DIAS_PERIODO_ANTERIOR = 0');

        //Cancelacion de dias de vacaciones
        $diasDisponiblesPorAño = $connection->createCommand("CALL SP_VACACIONES_OBTENER_DIAS_DISPONIBLES(".$modelVacacionesEmpleados->PK_VACACIONES_EMPLEADO.")")->queryAll();
        foreach($diasDisponiblesPorAño as $array){
            $modelBitVacacionesEmpleadoCancelar = new TblBitVacacionesEmpleado();
            $modelBitVacacionesEmpleadoCancelar->FK_VACACIONES_EMPLEADO = $modelVacacionesEmpleados->PK_VACACIONES_EMPLEADO;
            $modelBitVacacionesEmpleadoCancelar->FK_TIPO_VACACIONES = 3;
            $modelBitVacacionesEmpleadoCancelar->ANIO = $array['ANIO'];
            $modelBitVacacionesEmpleadoCancelar->APLICA_2X1 = 0;
            $modelBitVacacionesEmpleadoCancelar->DIAS_PERIODO_ANTERIOR = 0;
            $modelBitVacacionesEmpleadoCancelar->DIAS_HORAS_EXTRAS = 0;
            $modelBitVacacionesEmpleadoCancelar->COMENTARIOS = 'Cancelacion de dias por baja de empleado';
            $modelBitVacacionesEmpleadoCancelar->FECHA_REGISTRO = date('Y-m-d H:i:s');
            $modelBitVacacionesEmpleadoCancelar->DIAS_DISPONIBLES = $array['DIAS_DISPONIBLES'];
            $modelBitVacacionesEmpleadoCancelar->DIAS_EJECUTADOS = 0;
            $modelBitVacacionesEmpleadoCancelar->save(false);
        }
        //$transaction->rollBack();

        $modelVacacionesEmpleados->DIAS_HORAS_EXTRAS = 0;
        $modelVacacionesEmpleados->DIAS_DISPONIBLES = 0;
        $modelVacacionesEmpleados->FECHA_ACTUALIZACION = date('Y-m-d H:i:s');
        $modelVacacionesEmpleados->save(false);
        $descripcionBitacora = 'PK_VACACIONES_EMPLEADO='.$modelVacacionesEmpleados->PK_VACACIONES_EMPLEADO
                                            .',DIAS_DISP_INI='.$modelVacacionesEmpleados->getOldAttributes()['DIAS_DISPONIBLES']
                                            .',DIAS_DISP_FIN='.$modelVacacionesEmpleados->DIAS_DISPONIBLES
                                            .',DIAS_ANT_INI='.$modelVacacionesEmpleados->getOldAttributes()['DIAS_PERIODO_ANTERIOR']
                                            .',DIAS_ANT_FIN='.$modelVacacionesEmpleados->DIAS_PERIODO_ANTERIOR
                                            .',DIAS_HRS_EXS_INI='.$modelVacacionesEmpleados->getOldAttributes()['DIAS_HORAS_EXTRAS']
                                            .',DIAS_HRS_EXS_FIN='.$modelVacacionesEmpleados->DIAS_HORAS_EXTRAS;
        user_log_bitacora($descripcionBitacora,'Baja de empleado, se reinician las vacaciones',$modelVacacionesEmpleados->PK_VACACIONES_EMPLEADO);
        $connection->close();
    }*/

    public function actionEjecutar_diario()
    {
        $this->obtenerCumpleanos();
        $this->obtenerCumpleAniosEmpresa();
    }

    public function obtenerCumpleanos()
    {
        $connection = \Yii::$app->db;
        $fechaHoraInicial = date('Y-m-d H:i:s');
        $total_registros = $connection->createCommand("CALL SP_OBTENER_EMPLEADOS_CUMPLEANOS()")->queryAll();

        if(count($total_registros) > 0){
            foreach ($total_registros as $key => $array) {
                /*
                El stored procedure trae los siguientes campos
                    NOMBRE_EMP, APELLIDO_PAT_EMP, APELLIDO_MAT_EMP, PK_EMPLEADO, CORREO
                */
                $asunto= 'FELIZ CUMPLEAÑOS';
                unset($arrayCorreos);
                if(!empty($array['CORREO'])){
                    $arrayCorreos[] = $array['CORREO'];
                } else {
                    $arrayCorreos = explode(',',get_config('CONFIG','ENVIO_SIN_CORREOS_FELICITACIONES'));
                    $asunto= 'PENDIENTE DE ENVIO POR FALTA DE INFORMACIÓN DE EMAIL DEL EMPLEADO';
                }
                $arrayCorreos[] = 'irving.rivera@eisei.net.mx';
                $nombreArchivo = $array['PK_EMPLEADO'].'.jpg';
                $parametro = $array['NOMBRE_EMP'].'+'.$array['APELLIDO_PAT_EMP'].'+'.$array['APELLIDO_MAT_EMP'].'.jpg';
                $parametro = str_replace(' ', '+', $parametro);
                $de= get_config('EMPLEADOS','CORREO_FELICITACIONES');
                $para=$arrayCorreos;
                $asunto= 'FELIZ CUMPLEAÑOS';
                $mensaje='<img src="cid:correo_cumpleanos.php_PK_EMPLEADO='.$array['PK_EMPLEADO'].'&name='.$parametro.'" width="760" height="560">';
                $enviado = send_mail($de,$para, $asunto, $mensaje, [server().get_upload_url().'/views/empleados/correo_cumpleanos.php?PK_EMPLEADO='.$array['PK_EMPLEADO'].'&name='.$parametro]);
                unlink('../views/empleados/'.$nombreArchivo);
                echo $enviado;
            }
        }
        $cantRegistros = count($total_registros);
        $fechaHoraFinal = date('Y-m-d H:i:s');
        $modelTareasJobLog = new TblTareasJobLog;
        $modelTareasJobLog->FK_TAREAS_JOB = 1;
        $modelTareasJobLog->FECHA_INICIO_EJECUCION = $fechaHoraInicial;
        $modelTareasJobLog->FECHA_FINAL_EJECUCION = $fechaHoraFinal;
        $modelTareasJobLog->REGISTROS_AFECTADOS = $cantRegistros;
        $modelTareasJobLog->save(false);

        $connection->close();
    }

    public function obtenerCumpleAniosEmpresa()
    {
        $connection = \Yii::$app->db;
        $fechaHoraInicial = date('Y-m-d H:i:s');
        $total_registros = $connection->createCommand("CALL SP_OBTENER_EMPLEADOS_ANIVERSARIO_EMPRESA()")->queryAll();
        if(count($total_registros) > 0){
            foreach ($total_registros as $key => $array) {
                /*
                El stored procedure trae los siguientes campos
                    NOMBRE_EMP, APELLIDO_PAT_EMP, APELLIDO_MAT_EMP, PK_EMPLEADO, CORREO, ANIOS
                */
                $asunto= '¡FELIZ ANIVERSARIO!.....¡Gracias por tu CONFIANZA!';
                unset($arrayCorreos);
                if(!empty($array['CORREO'])){
                    $arrayCorreos[] = $array['CORREO'];
                } else {
                    $arrayCorreos = explode(',',get_config('CONFIG','ENVIO_SIN_CORREOS_FELICITACIONES'));
                    $asunto= 'PENDIENTE DE ENVIO POR FALTA DE INFORMACIÓN DE EMAIL DEL EMPLEADO';
                }
                $arrayCorreos[] = 'irving.rivera@eisei.net.mx';
                $nombreArchivo = $array['ANIOS'].'.jpg';
                $de= get_config('EMPLEADOS','CORREO_FELICITACIONES');
                $para=$arrayCorreos;
                $asunto= '¡FELIZ ANIVERSARIO!.....¡Gracias por tu CONFIANZA!';
                $mensaje='<img src="cid:correo_aniversario.php_name='.$array['ANIOS'].'.jpg" width="760" height="560">';
                $enviado = send_mail($de,$para, $asunto, $mensaje, [server().get_upload_url().'/views/empleados/correo_aniversario.php?name='.$array['ANIOS'].'.jpg']);
                unlink('../views/empleados/'.$nombreArchivo);
                echo $enviado;
            }
        }
        $cantRegistros = count($total_registros);
        $fechaHoraFinal = date('Y-m-d H:i:s');
        $modelTareasJobLog = new TblTareasJobLog;
        $modelTareasJobLog->FK_TAREAS_JOB = 2;
        $modelTareasJobLog->FECHA_INICIO_EJECUCION = $fechaHoraInicial;
        $modelTareasJobLog->FECHA_FINAL_EJECUCION = $fechaHoraFinal;
        $modelTareasJobLog->REGISTROS_AFECTADOS = $cantRegistros;
        $modelTareasJobLog->save(false);

        $connection->close();
    }

    public function enviarCorreoBaja($fechaBaja, $nombreEmpleado, $unidadNegocioEmpleado)
    {
        //Obtener Puestos para Correo
        $puestosEnviarCorreo= explode(',',get_config('EMPLEADOS','PUESTOS_ENVIO_CORREOS_BAJA'));

        //Obtener Unidad de Negocio
        $unidadNegocioExcepcion1 = explode(',',get_config('CONFIG','EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'));
        if(in_array($unidadNegocioEmpleado, $unidadNegocioExcepcion1)){
            $unidadNegocio = $unidadNegocioExcepcion1;
        } else {
            $unidadNegocio[] = $unidadNegocioEmpleado;
        }

        //Obtener correos de Destino
        $arrayCorreos = [];
        $query = new Query;
        $query->select('emp.EMAIL_INTERNO')
            ->from('tbl_perfil_empleados as perfil')
            ->join('LEFT JOIN','tbl_empleados as emp',
                'perfil.FK_EMPLEADO = emp.PK_EMPLEADO')
            ->where(['IN','perfil.FK_PUESTO', $puestosEnviarCorreo])
            ->andWhere(['IN','perfil.FK_UNIDAD_NEGOCIO',$unidadNegocio])
            ->andWhere(['NOT IN','perfil.FK_ESTATUS_RECURSO',array(4,6)])
            ->distinct();
        $command = $query->createCommand();
        $rows = $command->queryAll();
        foreach($rows as $array){
            if($array['EMAIL_INTERNO']){
                $arrayCorreos[] = $array['EMAIL_INTERNO'];
            }
        }

        $correosFijos = explode(',',get_config('EMPLEADOS','ENVIO_CORREOS_FIJOS_BAJA_EMPLEADO'));

        $arrayCorreos = array_merge($arrayCorreos,$correosFijos);

        if(count($arrayCorreos) > 0){
            // Variables de envio de correo
            $de= get_config('EMPLEADOS','CORREO_FELICITACIONES');
            $para=$arrayCorreos;
            $asunto= 'Baja de Empleado';
            $mensaje="Buen día <br><br>
            El empleado <u>$nombreEmpleado</u> se dio de baja, por lo cual ya no se presentera desde el día $fechaBaja.<br><br>
            Saludos";

            //Envio de correo
            $enviado = send_mail($de,$para, $asunto, $mensaje,[]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the tblempleados model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return tblempleados the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = tblempleados::findOne(['PK_EMPLEADO' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
