<?php

namespace app\controllers;

use Yii;
use app\models\SubirArchivo;
use app\models\tblvacantes;
use app\models\tblcatprioridades;
use app\models\tblcatestacionescandidato;
use app\models\tblcatestatuscandidato;
use app\models\tblvacantescandidatos;
use app\models\tblbitcomentarioscandidato;
use app\models\TblUsuarios;
use app\models\TblVacantesHabilidades;
use app\models\TblVacantesTecnologias;
use app\models\TblVacantesHerramientas;
use app\models\TblCandidatosTecnologias;
use app\models\TblCandidatosHabilidades;
use app\models\TblCandidatosHerramientas;
use app\models\TblCandidatosPerfiles;
use app\models\tblcatubicaciones;
use app\models\TblBitProspectos;
use app\models\TblProspectos;
use app\models\TblCandidatosDocumentos;
use app\models\TblCatTipoCV;
use app\models\TblProspectosDocumentos;
use yii\db\ActiveQuery;
use app\models\tblcandidatos;
use app\models\tblcatresponsablesrh;
use app\models\tblcatgenero;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\db\Expression;
use yii\web\UploadedFile;
use yii\grid\GridView;
use app\models\AsociarCandidatosSearch;

/**
 * DomiciliosController implements the CRUD actions for tblcandidatos model.
 */
class CandidatosController extends Controller
{
    /*public function behaviors()
    {
        return [
          'access' => [
          'class' => \yii\filters\AccessControl::className(),
          'only' => ['index', 'view','create', 'update', 'delete'],
          'rules' => [
            [
              'actions' => ['index', 'view',],
              'allow' => true,
              'roles' => ['@'],
              //'matchCallback' => function ($rule, $action) {
              // return PermissionHelpers::requireMinimumRole('Admin') && PermissionHelpers::requireStatus('Active');
              //}
            ],
            [
              'actions' => [ 'create', 'update', 'delete'],
              'allow' => true,
              'roles' => ['@'],
              //'matchCallback' => function ($rule, $action) {
              //return PermissionHelpers::requireMinimumRole('SuperUser') && PermissionHelpers::requireStatus('Active');
             // }
            ],
          ],
        ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
}*/
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

    /**
     * Lists all tblcandidatos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $pk_vacante = $request->get('pk_vacante');
        $modelVacante = new tblvacantes();
        if(!empty($pk_vacante)){
            $modelVacante = tblvacantes::findOne($pk_vacante);
            $prioridad = tblcatprioridades::find()->where(['PK_PRIORIDAD' => $modelVacante->FK_PRIORIDAD])->limit(1)->one();
                if(!empty($prioridad->DESC_PRIORIDAD)){
                    $idprioridad = $prioridad->DESC_PRIORIDAD;
                }else{
                    $idprioridad = 'Sin Definir';
                }
            $modelVacante->FK_PRIORIDAD=$idprioridad;
        }
        $tamanio_pagina=9;

        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio ='';
        }else{
            $unidadNegocio = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
        }

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre               =(!empty($post['nombre']))? trim($post['nombre']):'';
            $pk_vacante           =(!empty($post['pk_vacante']))? trim($post['pk_vacante']):'';
            $pk_estacion          =(!empty($post['pk_estacion']))? trim($post['pk_estacion']):'';
            $pk_estatus_candidato =(!empty($post['pk_estatus_candidato']))? trim($post['pk_estatus_candidato']):'';
            $pagina               =(!empty($data['pagina']))? trim($data['pagina']):'';
            $modelVacante         = tblvacantes::findOne($pk_vacante);


            $query= (new \yii\db\Query)
                ->select('count(*) as count')
                ->from('tbl_vacantes_candidatos as vc')
                ->join('inner join','tbl_vacantes v',
                        'vc.FK_VACANTE=v.PK_VACANTE')
                ->join('left join','tbl_usuarios u',
                        'u.PK_USUARIO= v.FK_USUARIO')
                ->join('left join', 'tbl_perfil_empleados p',
                        'u.FK_EMPLEADO= p.FK_EMPLEADO')
                ->andFilterWhere(
                    ['and',
                        ['=', 'vc.FK_VACANTE', $pk_vacante],
                        ['=', 'p.FK_UNIDAD_NEGOCIO', $unidadNegocio]
                    ])
                ->one()['count'];
            if($query<$tamanio_pagina){
                $pagina=1;
            }

            $dataProvider = new ActiveDataProvider([
                    'query' => (new \yii\db\Query)
                        ->select([
                                'PK_VACANTES_CANDIDATOS',
                                'FK_VACANTE',
                                'FK_CANDIDATO',
                                'FECHA_ACTUALIZACION',
                                'FK_ESTACION_ACTUAL_CANDIDATO',
                                'FK_ESTATUS_ACTUAL_CANDIDATO',
                            ])
                        ->from('tbl_vacantes_candidatos as vc')
                        ->join('inner join','tbl_vacantes v',
                                'vc.FK_VACANTE=v.PK_VACANTE')
                        ->join('left join','tbl_usuarios u',
                                'u.PK_USUARIO= v.FK_USUARIO')
                        ->join('left join', 'tbl_perfil_empleados p',
                                'u.FK_EMPLEADO= p.FK_EMPLEADO')
                        ->andFilterWhere(
                            ['and',
                                ['=', 'vc.FK_VACANTE', $pk_vacante],
                                ['=', 'vc.FK_ESTACION_ACTUAL_CANDIDATO', $pk_estacion],
                                ['=', 'vc.FK_ESTATUS_ACTUAL_CANDIDATO', $pk_estatus_candidato],
                                ['=', 'p.FK_UNIDAD_NEGOCIO', $unidadNegocio]
                            ])
                    ,
                    'pagination' => [
                        'pageSize' => $tamanio_pagina,
                        'page' => $pagina-1,
                    ],
                ]);






        $resultado=$dataProvider->getModels();
            foreach ($resultado as $key => $value) {
                $claveCandidato=$resultado[$key]['FK_CANDIDATO'];
                $estacion = tblcatestacionescandidato::find()->where(['PK_ESTACION_CANDIDATO' => $resultado[$key]['FK_ESTACION_ACTUAL_CANDIDATO']])->limit(1)->one();
                $estatus = tblcatestatuscandidato::find()->where(['PK_ESTATUS_CANDIDATO' => $resultado[$key]['FK_ESTATUS_ACTUAL_CANDIDATO']])->limit(1)->one();
                $nombreCandidato = tblcandidatos::find()->where(['PK_CANDIDATO' => $resultado[$key]['FK_CANDIDATO']])->limit(1)->one();
                if(!empty($estacion->DESC_ESTACION_CANDIDATO)){
                    $idestacion = $estacion->DESC_ESTACION_CANDIDATO;
                }else{
                    $idestacion = 'Sin Definir';
                }

                if(!empty($estatus->DESC_ESTATUS_CANDIDATO)){
                    $idestatus = $estatus->DESC_ESTATUS_CANDIDATO;
                }else{
                    $idestatus = 'Sin Definir';
                }

                 if(!empty($nombreCandidato->NOMBRE)){
                    $idnombre = $nombreCandidato->NOMBRE.' '.$nombreCandidato->APELLIDO_PATERNO.' '.$nombreCandidato->APELLIDO_MATERNO;
                }else{
                    $idnombre = 'Sin Definir';
                }

                $resultado[$key]['FK_ESTACION_ACTUAL_CANDIDATO']=$idestacion;
                $resultado[$key]['FK_ESTATUS_ACTUAL_CANDIDATO']=$idestatus;
                $resultado[$key]['FK_CANDIDATO']=$idnombre;
                $resultado[$key]['PK_VACANTES_CANDIDATOS']=$claveCandidato;
                $resultado[$key]['FECHA_ACTUALIZACION']=date("d/m/Y", strtotime($resultado[$key]['FECHA_ACTUALIZACION']));

            }




            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'post'        => $post,
                'pagina'        => $pagina,
                'data'          => $resultado,
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),

            );

            return $res;
        }else{

            return $this->render('index', [
            'total_paginas' => 0,
                'modelVacante' => $modelVacante,
                'PK_VACANTE' => $pk_vacante,
            ]);
       }
    }

    /**
     * Lists all tblcandidatos models.
     * @return mixed
     */
    public function actionIndex2()
    {

        if (Yii::$app->request->isAjax) {

            $request = Yii::$app->request;
            $datosCandidatos = (new \yii\db\Query())

                            ->select([
                                 'vc.FK_CANDIDATO'
                                , 'CONCAT(candi.NOMBRE," ",candi.APELLIDO_PATERNO," ",candi.APELLIDO_MATERNO) AS CANDIDATO'
                                // , 'candi.CV'
                                ])
                            ->from('tbl_vacantes_candidatos as vc')
                            ->join('LEFT JOIN','tbl_candidatos candi',
                                    'vc.FK_CANDIDATO = candi.PK_CANDIDATO')
                            ->where(['ESTATUS_CAND_APLIC' => 1])

                            ->distinct()
                            ->all();

            if(count($datosCandidatos)!=0){
                foreach ($datosCandidatos as $array => $valor) {
                    $datosVacantes = (new \yii\db\Query())
                                ->select(["tbl_vacantes_candidatos.FK_VACANTE"
                                        , "tbl_vacantes.DESC_VACANTE"
                                        , 'DATE_FORMAT(tbl_vacantes_candidatos.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION'
                                        , "tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO"
                                        , "tbl_bit_comentarios_candidato.COMENTARIOS"
                                        , "tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO"
                                        ])
                                ->from('tbl_vacantes_candidatos')
                                ->join('LEFT JOIN','tbl_vacantes',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                                ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                                        'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                                ->join('LEFT JOIN','tbl_bit_comentarios_candidato',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_bit_comentarios_candidato.FK_VACANTE
                                            AND tbl_vacantes_candidatos.FK_CANDIDATO = tbl_bit_comentarios_candidato.FK_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTACION_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTATUS_CANDIDATO
                                            ')
                                ->andWhere(['tbl_vacantes_candidatos.FK_CANDIDATO' => $datosCandidatos[$array]['FK_CANDIDATO']])
                                //->orderby('tbl_vacantes_candidatos.FECHA_ACTUALIZACION ASC')
                                ->all();

                            if (empty($datosVacantes)) {
                                $datosCandidatos[$array]['DESC_VACANTE'] = "Ninguna";
                                $datosCandidatos[$array]['FECHA_ACTUALIZACION'] = "";
                                $datosCandidatos[$array]['DESC_ESTATUS_CANDIDATO'] = "";
                            }
                            else{
                                $vac = "";
                                $fechaAct = "";
                                $estatusCan = "";
                                $seguimientoCan = "";
                                $i = 0;
                                foreach ($datosVacantes as $llave => $value) {

                                  $vac .= '<div><a href="!#" id="detalle-vacante" data-vacante='.$datosVacantes[$llave]['FK_VACANTE'].' data-toggle="modal" data-target="#vacante-detalle">'.$datosVacantes[$llave]['DESC_VACANTE'].'</a></div>';
                                  $fechaAct .= '<div>'.$datosVacantes[$llave]['FECHA_ACTUALIZACION'].'</div>';
                                  $estatusCan .= '<div>'.$datosVacantes[$llave]['DESC_ESTATUS_CANDIDATO'].'</div>';
                                  $seguimientoCan .= '<p style="margin-bottom:0"><a href="'.Url::to(['candidatos/view3?PK_CANDIDATO='.$datosCandidatos[$array]['FK_CANDIDATO'].'&PK_VACANTE='.$datosVacantes[$llave]['FK_VACANTE']]).'">Ver detalle</a></p>';



                                }
                                $datosCandidatos[$array]['DESC_VACANTE'] = $vac;
                                $datosCandidatos[$array]['FECHA_ACTUALIZACION'] = $fechaAct;
                                $datosCandidatos[$array]['DESC_ESTATUS_CANDIDATO'] = $estatusCan;
                                $datosCandidatos[$array]['SEGUIMIENTO'] = $seguimientoCan;
                            }

                            $datosCandidatos[$array]['CV'] = $this->CVs($datosCandidatos[$array]['FK_CANDIDATO']);

                }
            } else {
                $datosVacantes[] = 0;
            }

            $dummyCandidatos = new tblcandidatos;
            $dummyVacante = new tblvacantes;

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $res = array(
                'datosCandidatos' => $datosCandidatos,
                'dummyCandidatos' => $dummyCandidatos,
                'dummyVacante' => $dummyVacante,
            );
            return $res;


        }else{
            /*Script para los documentos de candidatos*/
            // $CandidatosCV = TblCandidatos::find(['PK_CANDIDATO', 'CV', 'FK_PROSPECTO'])->all();
            //
            // foreach ($CandidatosCV as $keyCandidatosCV => $valueCandidatosCV) {
            //
            //   if (!empty($valueCandidatosCV['CV'])) {
            //     $CV = pathinfo($valueCandidatosCV['CV']);
            //     $nombreArchivo = '../uploads/CandidatosCV/CVPERSONAL_'.$valueCandidatosCV['PK_CANDIDATO'].'_'.date('Y-m-d').'.'.$CV['extension'];
            //
            //     /* Si el archivo existe en la carpeta CandidatosCV se hace
            //     * la inserción en la tabla Candidatos documentos y se renombra el archivo a CVPERSONAL_PK_PROSPECTO_FECHA.ext
            //     */
            //     $existeArchivo = file_exists('..'.$valueCandidatosCV['CV']);
            //     if ($existeArchivo) {
            //       $modelCandidatosCV = new TblCandidatosDocumentos;
            //       $modelCandidatosCV->FK_CANDIDATO  = $valueCandidatosCV['PK_CANDIDATO'];
            //       $modelCandidatosCV->FK_PROSPECTO  = $valueCandidatosCV['FK_PROSPECTO'];
            //       $modelCandidatosCV->FK_TIPO_CV    = 2;
            //       $modelCandidatosCV->RUTA_CV       = '../'.$nombreArchivo;
            //       $modelCandidatosCV->save(false);
            //       rename('..'.$valueCandidatosCV['CV'], $nombreArchivo);
            //     }
            //   }
            // }

            $request = Yii::$app->request;
            $datosCandidatos = (new \yii\db\Query())

                            ->select([
                                 'vc.FK_CANDIDATO'
                                , 'CONCAT(candi.NOMBRE," ",candi.APELLIDO_PATERNO," ",candi.APELLIDO_MATERNO) AS CANDIDATO'
                                // , 'candi.CV'
                                ])
                            ->from('tbl_vacantes_candidatos as vc')
                            ->join('LEFT JOIN','tbl_candidatos candi',
                                    'vc.FK_CANDIDATO = candi.PK_CANDIDATO')
                            ->where(['ESTATUS_CAND_APLIC' => 1])
                            ->distinct()
                            ->all();

            if(count($datosCandidatos)!=0){
                foreach ($datosCandidatos as $array => $valor) {
                    $datosVacantes = (new \yii\db\Query())
                                ->select(["tbl_vacantes_candidatos.FK_VACANTE"
                                        , "tbl_vacantes.DESC_VACANTE"
                                        , 'DATE_FORMAT(tbl_vacantes_candidatos.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION'
                                        , "tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO"
                                        , "tbl_bit_comentarios_candidato.COMENTARIOS"
                                        , "tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO"
                                        ])
                                ->from('tbl_vacantes_candidatos')
                                ->join('LEFT JOIN','tbl_vacantes',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                                ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                                        'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                                ->join('LEFT JOIN','tbl_bit_comentarios_candidato',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_bit_comentarios_candidato.FK_VACANTE
                                            AND tbl_vacantes_candidatos.FK_CANDIDATO = tbl_bit_comentarios_candidato.FK_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTACION_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTATUS_CANDIDATO
                                            ')
                                ->andWhere(['tbl_vacantes_candidatos.FK_CANDIDATO' => $datosCandidatos[$array]['FK_CANDIDATO']])
                                //->orderby('tbl_vacantes_candidatos.FECHA_ACTUALIZACION ASC')
                                ->all();

                            if (empty($datosVacantes)) {
                                $datosCandidatos[$array]['DESC_VACANTE'] = "Ninguna";
                                $datosCandidatos[$array]['FECHA_ACTUALIZACION'] = "";
                                $datosCandidatos[$array]['DESC_ESTATUS_CANDIDATO'] = "";
                            }
                            else{
                                $vac = "";
                                $fechaAct = "";
                                $estatusCan = "";
                                $i = 0;
                                foreach ($datosVacantes as $llave => $value) {

                                  $vac .= '<div>'.$datosVacantes[$llave]['DESC_VACANTE'].'</div>';
                                  $fechaAct .= '<div>'.$datosVacantes[$llave]['FECHA_ACTUALIZACION'].'</div>';
                                  $estatusCan .= '<div>'.$datosVacantes[$llave]['DESC_ESTATUS_CANDIDATO'].'</div>';
                                }
                                $datosCandidatos[$array]['DESC_VACANTE'] = $vac;
                                $datosCandidatos[$array]['FECHA_ACTUALIZACION'] = $fechaAct;
                                $datosCandidatos[$array]['DESC_ESTATUS_CANDIDATO'] = $estatusCan;
                            }

                            $datosCandidatos[$array]['CV'] = $this->CVs($datosCandidatos[$array]['FK_CANDIDATO']);

                }
            } else {
                $datosVacantes[] = 0;
            }

            $dummyCandidatos = new tblcandidatos;
            $dummyVacante = new tblvacantes;
            return $this->render('index2', [
                'datosCandidatos' => $datosCandidatos,
                'dummyCandidatos' => $dummyCandidatos,
                'dummyVacante' => $dummyVacante,
            ]);
        }

    }


public function actionIndex4()
    {
     $request = Yii::$app->request;
        $tamanio_pagina= 20;

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $aspirante      =(!empty($post['nombre']))? trim($post['nombre']):''; //NOMBRE DEL CANDIDATO
            $nombreVacante  =(!empty($post['vacante']))? trim($post['vacante']):''; //VACANTE A LA QUE SE ASOCIO
            $estatusVacante =(!empty($post['estatuscandidato']))? trim($post['estatuscandidato']):'';
            $pagina         =(!empty($post['page']))? trim($post['page']):'';

        if(empty($pagina)){
            $pagina=0;
        }else{
            $pagina= $pagina-1;
        }

            $total_registros = (new \yii\db\Query())
                        ->select("
                                 count(distinct(tbl_aspirantes_vacantes.fk_aspirante)) as count,
                                ")
                        ->from('tbl_aspirantes_vacantes')
                        ->join('LEFT JOIN','tbl_aspirantes',
                                'tbl_aspirantes_vacantes.FK_ASPIRANTE = tbl_aspirantes.PK_ASPIRANTES')
                        ->join('LEFT JOIN','tbl_vacantes',
                                'tbl_aspirantes_vacantes.fk_vacante = tbl_vacantes.PK_VACANTE')
                        ->andFilterWhere(
                            ['and',
                                ['LIKE', "CONCAT(tbl_aspirantes.nombre_asp, ' ', tbl_aspirantes.apellido_pat_asp, ' ', tbl_aspirantes.apellido_mat_asp)", $aspirante],
                                ['LIKE', 'tbl_vacantes.DESC_VACANTE', $nombreVacante],
                                ['LIKE', 'tbl_vacantes.FK_ESTATUS_VACANTE', $estatusVacante]
                            ])
                        ->distinct()
                        ->one();
            $total_paginas= ceil($total_registros['count']/$tamanio_pagina);

            if($total_registros['count']<=$tamanio_pagina){
                $pagina=0;
            }

            $datosCandidatos = (new \yii\db\Query())
                        ->select("
                                tbl_aspirantes_vacantes.FK_ASPIRANTE,
                                tbl_aspirantes.nombre_asp,
                                tbl_aspirantes.apellido_pat_asp,
                                tbl_aspirantes.apellido_mat_asp,
                                tbl_aspirantes.EMAIL_ASP,
                                tbl_aspirantes.TELEFONO_ASP,
                                tbl_vacantes.DESC_VACANTE,
                                tbl_aspirantes.RUTA_ARCHIVO
                                ")
                        ->from('tbl_aspirantes_vacantes')
                        ->join('LEFT JOIN','tbl_aspirantes',
                                'tbl_aspirantes_vacantes.FK_ASPIRANTE = tbl_aspirantes.PK_ASPIRANTES')
                        ->join('LEFT JOIN','tbl_vacantes',
                                'tbl_aspirantes_vacantes.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                        ->andFilterWhere(
                            ['and',
                                ['LIKE', "CONCAT(tbl_aspirantes.nombre_asp, ' ', tbl_aspirantes.apellido_pat_asp, ' ', tbl_aspirantes.apellido_mat_asp)", $aspirante],
                                ['LIKE', 'tbl_vacantes.DESC_VACANTE', $nombreVacante],
                                ['LIKE', 'tbl_vacantes.FK_ESTATUS_VACANTE', $estatusVacante]
                            ])
                        ->distinct()
                        ->offset($pagina*$tamanio_pagina)
                        ->limit($tamanio_pagina)
                        ->all();

            foreach ($datosCandidatos as $array) {
                $datosVacantes[$array['FK_ASPIRANTE']] = (new \yii\db\Query())
                            ->select(["tbl_aspirantes_vacantes.FK_VACANTE",
                                       "tbl_vacantes.DESC_VACANTE",
                                       "tbl_vacantes.FK_ESTATUS_VACANTE"
                                        ])
                            ->from('tbl_aspirantes_vacantes')
                            ->join('LEFT JOIN','tbl_aspirantes',
                                    'tbl_aspirantes_vacantes.FK_ASPIRANTE = tbl_aspirantes.PK_ASPIRANTES')
                            ->join('LEFT JOIN','tbl_vacantes',
                                    'tbl_aspirantes_vacantes.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                            ->andFilterWhere(
                            ['and',
                          ['LIKE', "CONCAT(tbl_aspirantes.nombre_asp, ' ', tbl_aspirantes.apellido_pat_asp, ' ', tbl_aspirantes.apellido_mat_asp)", $aspirante],
                                ['LIKE', 'tbl_vacantes.DESC_VACANTE', $nombreVacante],
                                ['=', 'tbl_aspirantes_vacantes.FK_ASPIRANTE', $array['FK_ASPIRANTE']],
                                ['LIKE', 'tbl_vacantes.FK_ESTATUS_VACANTE', $estatusVacante]
                            ])
                            ->all();
            }
            $bgColor1 = '#FFFFFF';
            $bgColor2 = '#F5F5F5';
            $contadorCandidatos = 0;
            $html='';
            foreach ($datosCandidatos as $array) {
                $i = 0;
                $contadorCandidatos++;
                $cantRegVacantes = count($datosVacantes[$array['FK_ASPIRANTE']]);

            if($contadorCandidatos%2==0){
                $bgColor = $bgColor2;
            } else {
                $bgColor = $bgColor1;
            }

                foreach ($datosVacantes[$array['FK_ASPIRANTE']] as $arrayVac) {
                     $html.= '<tr>';
                        if($i==0){

                            $nombre = $array['nombre_asp'].' '.$array['apellido_pat_asp'].' '.$array['apellido_mat_asp'];
                            $url = "create?pk_vacante=".$arrayVac['FK_VACANTE']
                            ."&FK_ASPIRANTE=".$array['FK_ASPIRANTE']
                            ."&apellido_pat_asp=".$array['apellido_pat_asp']
                            ."&apellido_mat_asp=".$array['apellido_mat_asp']
                            ."&nombre_asp=".$array['nombre_asp']
                            ."&EMAIL_ASP=".$array['EMAIL_ASP']
                            ."&TELEFONO_ASP=".$array['TELEFONO_ASP']
                            ."&RUTA_ARCHIVO=".$array['RUTA_ARCHIVO']
                            ;

                            $html.= "
                                    <td style='background-color: $bgColor; text-align: center;' class='border-top name'>
                                    <a href= '$url' class = 'refProspectos'>
                                    $nombre
                                    </a>
                                    <input type ='hidden' id='FK_ASPIRANTE'     class = 'FK_ASPIRANTE'      name = 'FK_ASPIRANTE'      value='".$array['FK_ASPIRANTE']."' />
                                    <input type ='hidden' id='apellido_pat_asp' class = 'apellido_pat_asp'  name = 'apellido_pat_asp'  value='".$array['apellido_pat_asp']."' />
                                    <input type ='hidden' id='apellido_mat_asp' class = 'apellido_mat_asp'  name = 'apellido_mat_asp'  value='".$array['apellido_mat_asp']."' />
                                    <input type ='hidden' id='nombre_asp'       class = 'nombre_asp'        name = 'nombre_asp'        value='".$array['nombre_asp']."' />
                                    <input type ='hidden' id='EMAIL_ASP'        class = 'EMAIL_ASP'         name = 'EMAIL_ASP'         value='".$array['EMAIL_ASP']."' />
                                    <input type ='hidden' id='TELEFONO_ASP'     class = 'TELEFONO_ASP'      name = 'TELEFONO_ASP'      value='".$array['TELEFONO_ASP']."' />
                                    <input type ='hidden' id='FK_VACANTE'       class = 'FK_VACANTE'        name = 'FK_VACANTE'        value='".$arrayVac['FK_VACANTE']."' />
                                     <input type ='hidden' id='RUTA_ARCHIVO'       class = 'RUTA_ARCHIVO'        name = 'RUTA_ARCHIVO'        value='".$array['RUTA_ARCHIVO']."' />
                                </td>
                                ";
                        } else {
                            $html.= "<td style='border: 0px; background-color: $bgColor; text-align: center;'>&nbsp;</td>
                                    <td style='border: 0px; background-color: $bgColor;  text-align: center;'>&nbsp;</td>
                                    <td style='border: 0px; background-color: $bgColor;  text-align: center;'>&nbsp;</td>";
                        }
                    //   $html.= "<td style='background-color: $bgColor;' class='border-top name'><a href='".Url::to(["candidatos/view3",'PK_CANDIDATO'=> $array['FK_CANDIDATO'],'PK_VACANTE'=>$arrayVac['FK_VACANTE'] ])."'>".$arrayVac['DESC_VACANTE']."</a></td>";
                       $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['EMAIL_ASP']."</td>";
                       $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['TELEFONO_ASP']."</td>";
                       $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['DESC_VACANTE']."</td>";
                    //  $html.= "<td style='background-color: $bgColor; $pointer' class='border-top'>$comentariosCancelacion</td>";
                    $html.= '</tr>';
                    $i++;
                }
            }

            $cantCandidatos = count($datosCandidatos);
            if($cantCandidatos == 0){
                $html.="<tr>";
                    $html.='<td colspan="7" rowspan="3" style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.="</tr>";
            }

            return [
                'data'=>$html,
                'total_paginas'=>$total_paginas,
                'pagina'=>$pagina,
                'post'=>$post,
                'total_registros'=>$total_registros['count'],
                'datosCandidatos'=>$datosCandidatos,
            ];
        }
        else{

            $datosCandidatos = (new \yii\db\Query())
                        ->select("
                                tbl_aspirantes_vacantes.FK_ASPIRANTE,
                                tbl_aspirantes.nombre_asp,
                                tbl_aspirantes.apellido_pat_asp,
                                tbl_aspirantes.apellido_mat_asp,
                                tbl_aspirantes.EMAIL_ASP,
                                tbl_aspirantes.TELEFONO_ASP
                                ")
                        ->from('tbl_aspirantes_vacantes')
                        ->join('LEFT JOIN','tbl_aspirantes',
                                'tbl_aspirantes_vacantes.FK_ASPIRANTE = tbl_aspirantes.PK_ASPIRANTES')
                        ->join('LEFT JOIN','tbl_vacantes',
                                'tbl_aspirantes_vacantes.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                            ->distinct()
                            ->all();

            if(count($datosCandidatos)!=0){
                foreach ($datosCandidatos as $array) {
                    $datosVacantes[$array['FK_ASPIRANTE']] = (new \yii\db\Query())
                                ->select(["tbl_aspirantes_vacantes.FK_VACANTE",
                                       "tbl_vacantes.DESC_VACANTE",
                                       "tbl_vacantes.FK_ESTATUS_VACANTE"
                                        ])
                            ->from('tbl_aspirantes_vacantes')
                            ->join('LEFT JOIN','tbl_aspirantes',
                                    'tbl_aspirantes_vacantes.FK_ASPIRANTE = tbl_aspirantes.PK_ASPIRANTES')
                            ->join('LEFT JOIN','tbl_vacantes',
                                    'tbl_aspirantes_vacantes.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                                ->andWhere(['=', 'tbl_aspirantes_vacantes.FK_ASPIRANTE', $array['FK_ASPIRANTE']])
                                ->all();

            }
        } else {
            $datosVacantes[] = 0;
        }

            $dummyCandidatos = new tblcandidatos;
            $dummyVacante = new tblvacantes;
            return $this->render('index4', [
                'datosCandidatos' => $datosCandidatos,
                'datosVacantes' => $datosVacantes,
                'dummyCandidatos' => $dummyCandidatos,
                'dummyVacante' => $dummyVacante,
            ]);
        }
    }
public function actionIndex6()
    {
        $request = Yii::$app->request;
        $pk_vacante = $request->get('pk_vacante');
        $modelVacante = tblvacantes::findOne($pk_vacante);
        $tamanio_pagina= 20;

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $data['pk_vacante'];
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $aspirante      =(!empty($post['nombre']))? trim($post['nombre']):''; //NOMBRE DEL CANDIDATO
            $nombreVacante  =(!empty($post['vacante']))? trim($post['vacante']):''; //VACANTE A LA QUE SE ASOCIO
            $estatusVacante =(!empty($post['estatuscandidato']))? trim($post['estatuscandidato']):'';
            $pagina         =(!empty($post['page']))? trim($post['page']):'';
            $pk_vacante     = $data['pk_vacante'];
            if(empty($pagina)){
                $pagina=0;
            }else{
                $pagina= $pagina-1;
            }

            $total_registros = (new \yii\db\Query())
                        ->select("
                                 count(distinct(tbl_aspirantes_vacantes.fk_aspirante)) as count,
                                ")
                        ->from('tbl_aspirantes_vacantes')
                        ->join('LEFT JOIN','tbl_aspirantes',
                                'tbl_aspirantes_vacantes.FK_ASPIRANTE = tbl_aspirantes.PK_ASPIRANTES')
                        ->join('LEFT JOIN','tbl_vacantes',
                                'tbl_aspirantes_vacantes.fk_vacante = tbl_vacantes.PK_VACANTE')
                        ->andFilterWhere(
                            ['and',
                                ['LIKE', "CONCAT(tbl_aspirantes.nombre_asp, ' ', tbl_aspirantes.apellido_pat_asp, ' ', tbl_aspirantes.apellido_mat_asp)", $aspirante],
                                ['LIKE', 'tbl_vacantes.DESC_VACANTE', $nombreVacante],
                                ['LIKE', 'tbl_vacantes.FK_ESTATUS_VACANTE', $estatusVacante]
                            ])
                        ->distinct()
                        ->one();
            $total_paginas= ceil($total_registros['count']/$tamanio_pagina);

            if($total_registros['count']<=$tamanio_pagina){
                $pagina=0;
            }

            $datosCandidatos = (new \yii\db\Query())
                        ->select("
                                tbl_aspirantes_vacantes.FK_ASPIRANTE,
                                tbl_aspirantes.nombre_asp,
                                tbl_aspirantes.apellido_pat_asp,
                                tbl_aspirantes.apellido_mat_asp,
                                tbl_aspirantes.EMAIL_ASP,
                                tbl_aspirantes.TELEFONO_ASP,
                                tbl_vacantes.DESC_VACANTE,
                                tbl_aspirantes.RUTA_ARCHIVO
                                ")
                        ->from('tbl_aspirantes_vacantes')
                        ->join('LEFT JOIN','tbl_aspirantes',
                                'tbl_aspirantes_vacantes.FK_ASPIRANTE = tbl_aspirantes.PK_ASPIRANTES')
                        ->join('LEFT JOIN','tbl_vacantes',
                                'tbl_aspirantes_vacantes.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                        ->andFilterWhere(
                            ['and',
                                ['LIKE', "CONCAT(tbl_aspirantes.nombre_asp, ' ', tbl_aspirantes.apellido_pat_asp, ' ', tbl_aspirantes.apellido_mat_asp)", $aspirante],
                                ['LIKE', 'tbl_vacantes.DESC_VACANTE', $nombreVacante],
                                ['LIKE', 'tbl_vacantes.FK_ESTATUS_VACANTE', $estatusVacante]
                            ])
                        ->distinct()
                        ->offset($pagina*$tamanio_pagina)
                        ->limit($tamanio_pagina)
                        ->all();

            foreach ($datosCandidatos as $array) {
                $datosVacantes[$array['FK_ASPIRANTE']] = (new \yii\db\Query())
                            ->select(["tbl_aspirantes_vacantes.FK_VACANTE",
                                       "tbl_vacantes.DESC_VACANTE",
                                       "tbl_vacantes.FK_ESTATUS_VACANTE"
                                        ])
                            ->from('tbl_aspirantes_vacantes')
                            ->join('LEFT JOIN','tbl_aspirantes',
                                    'tbl_aspirantes_vacantes.FK_ASPIRANTE = tbl_aspirantes.PK_ASPIRANTES')
                            ->join('LEFT JOIN','tbl_vacantes',
                                    'tbl_aspirantes_vacantes.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                            ->andFilterWhere(
                            ['and',
                          ['LIKE', "CONCAT(tbl_aspirantes.nombre_asp, ' ', tbl_aspirantes.apellido_pat_asp, ' ', tbl_aspirantes.apellido_mat_asp)", $aspirante],
                                ['LIKE', 'tbl_vacantes.DESC_VACANTE', $nombreVacante],
                                ['=', 'tbl_aspirantes_vacantes.FK_ASPIRANTE', $array['FK_ASPIRANTE']],
                                ['LIKE', 'tbl_vacantes.FK_ESTATUS_VACANTE', $estatusVacante]
                            ])
                            ->all();
            }
            $bgColor1 = '#FFFFFF';
            $bgColor2 = '#F5F5F5';
            $contadorCandidatos = 0;
            $html='';
            foreach ($datosCandidatos as $array) {
                $i = 0;
                $contadorCandidatos++;
                $cantRegVacantes = count($datosVacantes[$array['FK_ASPIRANTE']]);

                if($contadorCandidatos%2==0){
                    $bgColor = $bgColor2;
                } else {
                    $bgColor = $bgColor1;
                }

                foreach ($datosVacantes[$array['FK_ASPIRANTE']] as $arrayVac) {
                     $html.= '<tr>';
                        if($i==0){
                            $nombre = $array['nombre_asp'].' '.$array['apellido_pat_asp'].' '.$array['apellido_mat_asp'];
                            $url = "create?pk_vacante=".$pk_vacante
                            ."&FK_ASPIRANTE=".$array['FK_ASPIRANTE']
                            ."&apellido_pat_asp=".$array['apellido_pat_asp']
                            ."&apellido_mat_asp=".$array['apellido_mat_asp']
                            ."&nombre_asp=".$array['nombre_asp']
                            ."&EMAIL_ASP=".$array['EMAIL_ASP']
                            ."&TELEFONO_ASP=".$array['TELEFONO_ASP']
                            ."&RUTA_ARCHIVO=".$array['RUTA_ARCHIVO']
                            ;

                            $html.= "
                                    <td style='background-color: $bgColor; text-align: center;' class='border-top name'>
                                    <a href= '$url' class = 'refProspectos'>
                                    $nombre
                                    </a>
                                    <input type ='hidden' id='FK_ASPIRANTE'     class = 'FK_ASPIRANTE'      name = 'FK_ASPIRANTE'      value='".$array['FK_ASPIRANTE']."' />
                                    <input type ='hidden' id='apellido_pat_asp' class = 'apellido_pat_asp'  name = 'apellido_pat_asp'  value='".$array['apellido_pat_asp']."' />
                                    <input type ='hidden' id='apellido_mat_asp' class = 'apellido_mat_asp'  name = 'apellido_mat_asp'  value='".$array['apellido_mat_asp']."' />
                                    <input type ='hidden' id='nombre_asp'       class = 'nombre_asp'        name = 'nombre_asp'        value='".$array['nombre_asp']."' />
                                    <input type ='hidden' id='EMAIL_ASP'        class = 'EMAIL_ASP'         name = 'EMAIL_ASP'         value='".$array['EMAIL_ASP']."' />
                                    <input type ='hidden' id='TELEFONO_ASP'     class = 'TELEFONO_ASP'      name = 'TELEFONO_ASP'      value='".$array['TELEFONO_ASP']."' />
                                    <input type ='hidden' id='FK_VACANTE'       class = 'FK_VACANTE'        name = 'FK_VACANTE'        value='".$arrayVac['FK_VACANTE']."' />
                                     <input type ='hidden' id='RUTA_ARCHIVO'       class = 'RUTA_ARCHIVO'        name = 'RUTA_ARCHIVO'        value='".$array['RUTA_ARCHIVO']."' />
                                </td>
                                ";
                        } else {
                            $html.= "<td style='border: 0px; background-color: $bgColor; text-align: center;'>&nbsp;</td>
                                    <td style='border: 0px; background-color: $bgColor;  text-align: center;'>&nbsp;</td>
                                    <td style='border: 0px; background-color: $bgColor;  text-align: center;'>&nbsp;</td>";
                        }
                       $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['EMAIL_ASP']."</td>";
                       $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['TELEFONO_ASP']."</td>";
                       $html.= "<td style='background-color: $bgColor; text-align: center;' class='border-top'>".$array['DESC_VACANTE']."</td>";
                    $html.= '</tr>';
                    $i++;
                }
            }

            $cantCandidatos = count($datosCandidatos);
            if($cantCandidatos == 0){
                $html.="<tr>";
                    $html.='<td colspan="7" rowspan="3" style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.="</tr>";
            }

            return [
                'data'=>$html,
                'total_paginas'=>$total_paginas,
                'pagina'=>$pagina,
                'post'=>$post,
                'total_registros'=>$total_registros['count'],
                'datosCandidatos'=>$datosCandidatos,
            ];
        }
        else{

            $datosCandidatos = (new \yii\db\Query())
                        ->select("
                                tbl_aspirantes_vacantes.FK_ASPIRANTE,
                                tbl_aspirantes.nombre_asp,
                                tbl_aspirantes.apellido_pat_asp,
                                tbl_aspirantes.apellido_mat_asp,
                                tbl_aspirantes.EMAIL_ASP,
                                tbl_aspirantes.TELEFONO_ASP
                                ")
                        ->from('tbl_aspirantes_vacantes')
                        ->join('LEFT JOIN','tbl_aspirantes',
                                'tbl_aspirantes_vacantes.FK_ASPIRANTE = tbl_aspirantes.PK_ASPIRANTES')
                        ->join('LEFT JOIN','tbl_vacantes',
                                'tbl_aspirantes_vacantes.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                            ->distinct()
                            ->all();

            if(count($datosCandidatos)!=0){
                foreach ($datosCandidatos as $array) {
                    $datosVacantes[$array['FK_ASPIRANTE']] = (new \yii\db\Query())
                                ->select(["tbl_aspirantes_vacantes.FK_VACANTE",
                                       "tbl_vacantes.DESC_VACANTE",
                                       "tbl_vacantes.FK_ESTATUS_VACANTE"
                                        ])
                            ->from('tbl_aspirantes_vacantes')
                            ->join('LEFT JOIN','tbl_aspirantes',
                                    'tbl_aspirantes_vacantes.FK_ASPIRANTE = tbl_aspirantes.PK_ASPIRANTES')
                            ->join('LEFT JOIN','tbl_vacantes',
                                    'tbl_aspirantes_vacantes.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                                ->andWhere(['=', 'tbl_aspirantes_vacantes.FK_ASPIRANTE', $array['FK_ASPIRANTE']])
                                ->all();

                }
            } else {
                $datosVacantes[] = 0;
            }

            $dummyCandidatos = new tblcandidatos;
            $dummyVacante = new tblvacantes;
            return $this->render('index6', [
                'datosCandidatos' => $datosCandidatos,
                'datosVacantes' => $datosVacantes,
                'dummyCandidatos' => $dummyCandidatos,
                'dummyVacante' => $dummyVacante,
                'modelVacante' => $modelVacante,
               // 'PK_VACANTE' => $pk_vacante,
            ]);
        }
    }

public function actionIndex3(){

        $request = Yii::$app->request;
        $pk_vacante = $request->get('pk_vacante');
        $modelVacante = tblvacantes::findOne($pk_vacante);
        //dd($pk_vacante);
        //dd(Yii::$app->request->post());
        if (Yii::$app->request->post()) {
            // var_dump("entra en post");

            $data = Yii::$app->request->post();
            $pk_vacante =(!empty($data['pk_vacante']))? trim($data['pk_vacante']):'';
           // var_dump($data['idEmpleado']);

            // SE AGREGO FUNCIONALIDAD PARA QUE MODIFICARA EL ESTATUS DE LA VACANTE
            if($pk_vacante)
            {
                // Este estatus se asigna automaticamente cuando se le asocie un candidato a la Vacante
                $modelVacante = tblvacantes::findOne($pk_vacante);
                $modelVacante->FK_ESTACION_VACANTE=3;
                $modelVacante->FK_ESTATUS_VACANTE=1;
                $modelVacante->save(false);
            }
            //dd($data);
            if(isset($data['idRecurso'])){
                /*foreach ($data['idRecurso'] as $key => $value) {
                    $modelVacantesCandidatos = new TblVacantesCandidatos;
                    $modelVacantesCandidatos['FK_VACANTE'] = $pk_vacante;
                    $modelVacantesCandidatos['FK_CANDIDATO'] = $value;
                    $modelVacantesCandidatos['FK_ESTACION_ACTUAL_CANDIDATO'] = '1';
                    $modelVacantesCandidatos['FK_ESTATUS_ACTUAL_CANDIDATO'] = '1';
                    $modelVacantesCandidatos['FECHA_REGISTRO'] = date('Y-m-d');
                    $modelVacantesCandidatos['FECHA_ACTUALIZACION'] = date('Y-m-d');
                    $modelVacantesCandidatos->save(false);
                }*/
                foreach ($data['idRecurso'] as $value) {
                    $modelCandidatosVacantes = TblVacantesCandidatos::find()->where(['FK_VACANTE' => $pk_vacante , 'FK_CANDIDATO' => $value])->all();
                    var_dump($modelCandidatosVacantes);
                    //dd($value);
                    if(count($modelCandidatosVacantes) == 0){
                        $modelCandidatosVacantes = new TblVacantesCandidatos;
                        $modelCandidatosVacantes['FK_VACANTE'] = $pk_vacante;
                        $modelCandidatosVacantes['FK_CANDIDATO'] = $value;
                        $modelCandidatosVacantes['FK_ESTACION_ACTUAL_CANDIDATO'] = '3';
                        $modelCandidatosVacantes['FK_ESTATUS_ACTUAL_CANDIDATO'] = '1';
                        $modelCandidatosVacantes['FECHA_REGISTRO'] = date('Y-m-d');
                        $modelCandidatosVacantes['FECHA_ACTUALIZACION'] = date('Y-m-d');
                        $modelCandidatosVacantes->save(false);

                        $modelBitacoraCandidato = new tblbitcomentarioscandidato();
                        $modelBitacoraCandidato['FK_VACANTE'] = $pk_vacante;
                        $modelBitacoraCandidato['FK_CANDIDATO'] = $value;
                        $modelBitacoraCandidato['FK_ESTACION_CANDIDATO'] = 3;
                        $modelBitacoraCandidato['FK_ESTATUS_CANDIDATO'] = 1;
                        $modelBitacoraCandidato['FK_USUARIO'] = 1;
                        $modelBitacoraCandidato['DOCUMENTO_ASOCIADO'] = '';
                        $modelBitacoraCandidato['FECHA_REGISTRO'] = date('Y-m-d');
                        $modelBitacoraCandidato->save(false);
                    }
                }

            return $this->redirect(["vacantes/index"]);
            }
        }


        if (Yii::$app->request->isAjax) {
            $datosVacantes = "";

            $query = (new \yii\db\Query())
                ->select('tbl_vacantes_candidatos.FK_CANDIDATO' )
                ->from('tbl_vacantes_candidatos')
                ->andWhere(['=', 'tbl_vacantes_candidatos.FK_VACANTE', $pk_vacante])
                ->distinct()
                ->column();

            $dataProvider = (new \yii\db\Query)
                ->select(["
                         tbl_vacantes_candidatos.FK_CANDIDATO,
                         CONCAT(tbl_candidatos.NOMBRE, ' ', tbl_candidatos.APELLIDO_PATERNO, ' ', tbl_candidatos.APELLIDO_MATERNO) AS CANDIDATO
                        "])
                ->from('tbl_vacantes_candidatos')
                ->join('LEFT JOIN','tbl_candidatos',
                        'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                ->join('LEFT JOIN','tbl_vacantes',
                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                ->andFilterWhere(
                    ['and',
                        ['<>', 'tbl_vacantes_candidatos.FK_VACANTE', $pk_vacante],
                        ['NOT IN', 'tbl_vacantes_candidatos.FK_CANDIDATO', $query],
                        ['=', 'tbl_candidatos.ESTATUS_CAND_APLIC', 1]
                    ])
                ->distinct()
                ->all();

            foreach ($dataProvider as $datos => $value) {
                $dataProvider[$datos]['CHECKBOX'] = '<input type="checkbox" name="idEmpleado[]" id="idEmpleado" value ="' . $dataProvider[$datos]['FK_CANDIDATO'] . '">';
                $dataProvider[$datos]['CANDIDATO'] = '<p data-toggle="modal" data-target="#candidatos-detalle" class="invocar-detalle" style="cursor: pointer;">'. $dataProvider[$datos]['CANDIDATO'] .'<input type="hidden" value="'. $dataProvider[$datos]['FK_CANDIDATO'] .'"></p>';

                $datosVacantes = (new \yii\db\Query())
                    ->select(["tbl_vacantes_candidatos.FK_VACANTE"
                            , "tbl_vacantes.DESC_VACANTE"
                            , 'DATE_FORMAT(tbl_vacantes_candidatos.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION'
                            , "tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO"
                            , "tbl_bit_comentarios_candidato.COMENTARIOS"
                            , "tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO"
                            ])
                    ->from('tbl_vacantes_candidatos')
                    ->join('LEFT JOIN','tbl_candidatos',
                            'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                    ->join('LEFT JOIN','tbl_vacantes',
                            'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                    ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                            'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                    ->join('LEFT JOIN','tbl_bit_comentarios_candidato',
                            'tbl_vacantes_candidatos.FK_VACANTE = tbl_bit_comentarios_candidato.FK_VACANTE
                                AND tbl_vacantes_candidatos.FK_CANDIDATO = tbl_bit_comentarios_candidato.FK_CANDIDATO
                                AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTACION_CANDIDATO
                                AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTATUS_CANDIDATO
                                ')
                    ->andWhere(['=', 'tbl_vacantes_candidatos.FK_CANDIDATO', $dataProvider[$datos]['FK_CANDIDATO']])
                    ->andWhere(['<>', 'tbl_vacantes_candidatos.FK_VACANTE', $pk_vacante])
                    ->all();

                    /*
                    $vacante = "";
                    $fecha = "";
                    $estatus = "";
                    */
                    if (empty($datosVacantes)) {
                      $dataProvider[$datos]['DESC_VACANTE'] = "Ninguna";
                      $dataProvider[$datos]['FECHA_ACTUALIZACION'] = "";
                      $dataProvider[$datos]['DESC_ESTATUS_CANDIDATO'] = "";
                    }else {

                      $vac = "";
                      $fechaAct = "";
                      $estatusCan = "";
                      $seguimientoCan = "";
                      $i = 0;
                        foreach ($datosVacantes as $vKey => $value) {

                          $vac .= '<div><a href="!#" id="detalle-vacante" data-vacante='.$datosVacantes[$vKey]['FK_VACANTE'].' data-toggle="modal" data-target="#vacante-detalle">'.$datosVacantes[$vKey]['DESC_VACANTE'].'</a></div>';
                          $fechaAct .= '<div>'.$datosVacantes[$vKey]['FECHA_ACTUALIZACION'].'</div>';
                          $estatusCan .= '<div>'.$datosVacantes[$vKey]['DESC_ESTATUS_CANDIDATO'].'</div>';
                          $seguimientoCan .= '<p><a href="'.Url::to(['candidatos/view3?PK_CANDIDATO='.$dataProvider[$datos]['FK_CANDIDATO'].'&PK_VACANTE='.$datosVacantes[$vKey]['FK_VACANTE']]).'">Ver detalle</a></p>';
                        }

                      /*foreach ($datosVacantes as $vKey => $vvalue) {
                        //Transformar fecha para hacer correcto el sort
                        $date = str_replace('-', '/', $datosVacantes[$vKey]['FECHA_ACTUALIZACION']);
                        $dataspan = date('Y-m-d', strtotime($date));
                        $dataspan = str_replace('-', '', $dataspan);
                        //FIN Transformar fecha

                        $vacante  .=   "<p>". $datosVacantes[$vKey]['DESC_VACANTE'] . "</p>";
                        $fecha    .=   "<p><span class='hide'>". $dataspan. "</span>". $date . "</p>";
                        $estatus  .=   "<p>". $datosVacantes[$vKey]['DESC_ESTATUS_CANDIDATO']. "</p>";
                      }*/

                      $dataProvider[$datos]['DESC_VACANTE'] = $vac;
                      $dataProvider[$datos]['FECHA_ACTUALIZACION'] = $fechaAct;
                      $dataProvider[$datos]['DESC_ESTATUS_CANDIDATO'] = $estatusCan;
                      $dataProvider[$datos]['SEGUIMIENTO'] = $seguimientoCan;
                    }


                    $dataProvider[$datos]['CV'] = $this->CVs($dataProvider[$datos]['FK_CANDIDATO']);
                    //$dataProvider[$datos]['DESC_VACANTE'] = $vacante;
                    //$dataProvider[$datos]['FECHA_ACTUALIZACION'] = $fecha;
                    //$dataProvider[$datos]['DESC_ESTATUS_CANDIDATO'] = $estatus;
                    // $dataProvider[$datos]['SEGUIMIENTO'] = "Ver detalle";
            }

            $dummyCandidatos = new tblcandidatos;
            $dummyVacante = new tblvacantes;

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $res = array(
                'modelVacante' => $modelVacante,
                'PK_VACANTE' => $pk_vacante,
                // 'datosCandidatos' => $datosCandidatos,
                'datosVacantes' => $datosVacantes,
                'dummyCandidatos' => $dummyCandidatos,
                'dummyVacante' => $dummyVacante,
                'dataProvider' => $dataProvider,
            );
            return $res;


        //FIN AJAX
        }else{
            // var_dump("else ajax");
	    $datosVacantes = '';
            $query = (new \yii\db\Query())
                ->select('tbl_vacantes_candidatos.FK_CANDIDATO' )
                ->from('tbl_vacantes_candidatos')
                ->where(['=', 'tbl_vacantes_candidatos.FK_VACANTE', $pk_vacante])
                ->distinct()
                ->column();
            //dd($query);
            $dataProvider = (new \yii\db\Query)
                ->select(["
                         tbl_vacantes_candidatos.FK_CANDIDATO,
                         CONCAT(tbl_candidatos.NOMBRE, ' ', tbl_candidatos.APELLIDO_PATERNO, ' ', tbl_candidatos.APELLIDO_MATERNO) AS CANDIDATO
                        "])
                ->from('tbl_vacantes_candidatos')
                ->join('LEFT JOIN','tbl_candidatos',
                        'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                ->join('LEFT JOIN','tbl_vacantes',
                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                ->andFilterWhere(
                    ['and',
                        ['<>', 'tbl_vacantes_candidatos.FK_VACANTE', $pk_vacante],
                        ['NOT IN', 'tbl_vacantes_candidatos.FK_CANDIDATO', $query]
                    ])
                ->distinct()
                ->all();
            //dd($dataProvider);
            foreach ($dataProvider as $datos => $value) {
                $dataProvider[$datos]['CHECKBOX'] = '<input type="checkbox" name="idEmpleado[]" id="idEmpleado" value ="' . $dataProvider[$datos]['FK_CANDIDATO'] . '">';
                $dataProvider[$datos]['CANDIDATO'] = '<p data-toggle="modal" data-target="#candidatos-detalle" class="invocar-detalle">'. $dataProvider[$datos]['CANDIDATO'] .'<input type="hidden" value="'. $dataProvider[$datos]['FK_CANDIDATO'] .'"></p>';
                // $nombreCV = substr($dataProvider[$datos]['CV'], 22);
                // $enlace = "";
                // if ($nombreCV == "") {
                //   $enlace = "javascript:void(0);";
                // }
                // else {
                //   $enlace = $dataProvider[$datos]['CV'];
                // }
                // $dataProvider[$datos]['CV'] = "<a href='". $enlace ."' download>". $nombreCV ."</a>";
                $datosVacantes = (new \yii\db\Query())
                    ->select(["tbl_vacantes_candidatos.FK_VACANTE"
                            , "tbl_vacantes.DESC_VACANTE"
                            , 'DATE_FORMAT(tbl_vacantes_candidatos.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION'
                            , "tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO"
                            , "tbl_bit_comentarios_candidato.COMENTARIOS"
                            , "tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO"
                            ])
                    ->from('tbl_vacantes_candidatos')
                    ->join('LEFT JOIN','tbl_candidatos',
                            'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                    ->join('LEFT JOIN','tbl_vacantes',
                            'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                    ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                            'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                    ->join('LEFT JOIN','tbl_bit_comentarios_candidato',
                            'tbl_vacantes_candidatos.FK_VACANTE = tbl_bit_comentarios_candidato.FK_VACANTE
                                AND tbl_vacantes_candidatos.FK_CANDIDATO = tbl_bit_comentarios_candidato.FK_CANDIDATO
                                AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTACION_CANDIDATO
                                AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTATUS_CANDIDATO
                                ')
                    ->andWhere(['=', 'tbl_vacantes_candidatos.FK_CANDIDATO', $dataProvider[$datos]['FK_CANDIDATO']])
                    ->andWhere(['<>', 'tbl_vacantes_candidatos.FK_VACANTE', $pk_vacante])
                    ->all();


                    $dataProvider[$datos]['CV'] = $this->CVs($dataProvider[$datos]['FK_CANDIDATO']);


                    /*if (empty($datosVacantes)) {
                      $dataProvider[$datos]['DESC_VACANTE'] = "Ninguna";
                      $dataProvider[$datos]['FECHA_ACTUALIZACION'] = "";
                      $dataProvider[$datos]['DESC_ESTATUS_CANDIDATO'] = "";
                    }else {

                      $vac = "";
                      $fechaAct = "";
                      $estatusCan = "";
                      $seguimientoCan = "";
                      $i = 0;
                        foreach ($datosVacantes as $vKey => $value) {

                          $vac .= '<div>'.$datosVacantes[$vKey]['DESC_VACANTE'].'</div>';
                          $fechaAct .= '<div>'.$datosVacantes[$vKey]['FECHA_ACTUALIZACION'].'</div>';
                          $estatusCan .= '<div>'.$datosVacantes[$vKey]['DESC_ESTATUS_CANDIDATO'].'</div>';
                          $seguimientoCan .= '<p><a href="'.Url::to(['candidatos/view3?PK_CANDIDATO='.$dataProvider[$datos]['FK_CANDIDATO'].'&PK_VACANTE='.$datosVacantes[$vKey]['FK_VACANTE']]).'">Ver detalle</a></p>';
                        }

                      $dataProvider[$datos]['DESC_VACANTE'] = $vac;
                      $dataProvider[$datos]['FECHA_ACTUALIZACION'] = $fechaAct;
                      $dataProvider[$datos]['DESC_ESTATUS_CANDIDATO'] = $estatusCan;
                      $dataProvider[$datos]['SEGUIMIENTO'] = $seguimientoCan;
                    }*/

            }
            //dd($dataProvider);
            $dummyCandidatos = new tblcandidatos;
            $dummyVacante = new tblvacantes;
            // $searchModel = new tblvacantes;

            return $this->render('index3', [
                'modelVacante' => $modelVacante,
                'PK_VACANTE' => $pk_vacante,
                // 'datosCandidatos' => $datosCandidatos,
                'datosVacantes' => $datosVacantes,
                'dummyCandidatos' => $dummyCandidatos,
                'dummyVacante' => $dummyVacante,
                'dataProvider' => $dataProvider,
                // s'searchModel' => $searchModel
            ]);
        }
    }// Fin index3

     /**
     * Bandeja candidatos para agendar entrevista
     * @return type
     */
    public function actionIndex7()
    {
        $request = Yii::$app->request;
        $tamanio_pagina= 20;
        $Limit = '';

        if (Yii::$app->request->isAjax) {
            //INICIO AJAX
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $candidato      =(!empty($post['nombre']))? trim($post['nombre']):'';
            $nombreVacante  =(!empty($post['vacante']))? trim($post['vacante']):'';
            $estatusVacante =(!empty($post['estatuscandidato']))? trim($post['estatuscandidato']): 2;
            $pagina         =(!empty($post['page']))? trim($post['page']):'';
            $Limit = 'Limit '.(($pagina * $tamanio_pagina)-$tamanio_pagina).', '.$tamanio_pagina.' ';

            if(empty($pagina)){
                $pagina=0;
            }else{
                $pagina= $pagina-1;
            }

            // TOTAL DE REGISTROS
            $connection = \Yii::$app->db;
            $totalStored = $connection->createCommand("CALL SP_CANDIDATOS_PARA_AGENDAR_ENTREVISTA(:P_NOMBRE, :P_DESC_VACANTE, :P_FK_VACANTE,:P_FK_ESTACION_ACTUAL_CANDIDATO,:P_FK_ESTATUS_ACTUAL_CANDIDATO,:P_FK_UNIDAD_NEGOCIO,:P_LIMIT,:P_COUNT)")
                    ->bindValue(':P_NOMBRE', $candidato)
                    ->bindValue(':P_DESC_VACANTE', $nombreVacante)
                    ->bindValue(':P_FK_VACANTE', 0)
                    ->bindValue(':P_FK_ESTACION_ACTUAL_CANDIDATO', 2)
                    ->bindValue(':P_FK_ESTATUS_ACTUAL_CANDIDATO', $estatusVacante)
                    ->bindValue(':P_FK_UNIDAD_NEGOCIO', 0)
                    ->bindValue(':P_LIMIT', '')
                    ->bindValue(':P_COUNT', 1)
                    ->queryAll();
                //Cerrar Conexion
            $connection->close();

            // CALCULAMOS TOTAL DE PAGINAS
            $total_registros = $totalStored[0]["NUM_ROWS"];
            $total_paginas = ($total_registros < $tamanio_pagina) ? 1 : ceil($total_registros / $tamanio_pagina);

            // RECUPERAMOS LOS DATOS A MOSTRAR
            $connection = \Yii::$app->db;
            $datosCandidatos = $connection->createCommand("CALL SP_CANDIDATOS_PARA_AGENDAR_ENTREVISTA(:P_NOMBRE, :P_DESC_VACANTE, :P_FK_VACANTE,:P_FK_ESTACION_ACTUAL_CANDIDATO,:P_FK_ESTATUS_ACTUAL_CANDIDATO,:P_FK_UNIDAD_NEGOCIO,:P_LIMIT,:P_COUNT)")
                    ->bindValue(':P_NOMBRE', $candidato)
                    ->bindValue(':P_DESC_VACANTE', $nombreVacante)
                    ->bindValue(':P_FK_VACANTE', 0)
                    ->bindValue(':P_FK_ESTACION_ACTUAL_CANDIDATO', 2)
                    ->bindValue(':P_FK_ESTATUS_ACTUAL_CANDIDATO', $estatusVacante)
                    ->bindValue(':P_FK_UNIDAD_NEGOCIO', 0)
                    ->bindValue(':P_LIMIT', $Limit)
                    ->bindValue(':P_COUNT', 0)
                    ->queryAll();
                //Cerrar Conexion
            $connection->close();


            foreach ($datosCandidatos as $array) {
                $datosVacantes[$array['FK_CANDIDATO']] = (new \yii\db\Query())
                            ->select(["tbl_vacantes_candidatos.FK_VACANTE"
                                        , "tbl_vacantes.DESC_VACANTE"
                                        , 'DATE_FORMAT(tbl_vacantes_candidatos.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION'
                                        , "tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO"
                                        , "tbl_bit_comentarios_candidato.COMENTARIOS"
                                        , "tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO"
                                        ])
                            ->from('tbl_vacantes_candidatos')
                            ->join('LEFT JOIN','tbl_candidatos',
                                    'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                            ->join('LEFT JOIN','tbl_vacantes',
                                    'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                            ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                                    'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                            ->join('LEFT JOIN','tbl_bit_comentarios_candidato',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_bit_comentarios_candidato.FK_VACANTE
                                            AND tbl_vacantes_candidatos.FK_CANDIDATO = tbl_bit_comentarios_candidato.FK_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTACION_CANDIDATO
                                            ')
                                            // AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTATUS_CANDIDATO
                            ->andFilterWhere(
                            ['and',
                                ['LIKE', "CONCAT(tbl_candidatos.NOMBRE, ' ', tbl_candidatos.APELLIDO_PATERNO, ' ', tbl_candidatos.APELLIDO_MATERNO)", $candidato],
                                ['LIKE', 'tbl_vacantes.DESC_VACANTE', $nombreVacante],
                                ['=', 'tbl_vacantes_candidatos.FK_CANDIDATO', $array['FK_CANDIDATO']],
                                ['=', 'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO', $estatusVacante]
                            ])
                            ->all();
            }
            $bgColor1 = '#FFFFFF';
            $bgColor2 = '#F5F5F5';
            $contadorCandidatos = 0;
            $html='';
            foreach ($datosCandidatos as $array) {
                $i = 0;
                $contadorCandidatos++;
                $cantRegVacantes = count($datosVacantes[$array['FK_CANDIDATO']]);

                if($contadorCandidatos%2==0){
                    $bgColor = $bgColor2;
                } else {
                    $bgColor = $bgColor1;
                }

                foreach ($datosVacantes[$array['FK_CANDIDATO']] as $arrayVac) {
                    if($arrayVac['FK_ESTATUS_ACTUAL_CANDIDATO']==5){
                        $comentariosCancelacion = ($arrayVac['COMENTARIOS']!='')?'<p data-toggle="modal" href="#candidatos-comentarios-cancelacion" onclick="traerComentarios('.$array['FK_CANDIDATO'].','.$arrayVac['FK_VACANTE'].')" class="name">Ver Detalle</p>':'N/A';
            $pointer = $arrayVac['COMENTARIOS']!=''?" cursor: pointer;":'';
                    } else {
                        $comentariosCancelacion = '';
            $pointer='';
                    }
                    $html.= '<tr>';
                        if($i==0){
                            $nombreCV = substr($array['CV'], 22);
                            $nombre = $array['NOMBRE'].' '.$array['APELLIDO_PATERNO'].' '.$array['APELLIDO_MATERNO'];
                            if(strcmp($array['CV'],'')!=0){
                                $array['CV'] = '../..'.$array['CV'];
                            } else {
                                $array['CV'] = 'javascript: void(0);';
                            }

                            $html.= "<td style='background-color: $bgColor;' class='border-top chk'><input type='checkbox' name='idEmpleado[]' id='idEmpleado' value='$array[FK_CANDIDATO]' class='chk_empleados'></td>";
                            $html.= "<td style='background-color: $bgColor;' class='border-top name'>
                                    <p data-toggle='modal' data-target='#candidatos-detalle' class='invocar-detalle' style='cursor: pointer;'>
                                        $nombre <input type='hidden' value='$array[FK_CANDIDATO]'/>
                                    <p>
                                </td>";
                            $html.= "<td style='background-color: $bgColor;' class='border-top'><a href='".$array['CV']."'>$nombreCV</a></td>";
                        } else {
                            $html.= "  <td style='border: 0px; background-color: $bgColor;'>&nbsp;</td>
                                    <td style='border: 0px; background-color: $bgColor;'>&nbsp;</td>
                                    <td style='border: 0px; background-color: $bgColor;'>&nbsp;</td>";
                        }
                        $html.= "<td style='background-color: $bgColor;' class='border-top name'><a href='".Url::to(["candidatos/view3",'PK_CANDIDATO'=> $array['FK_CANDIDATO'],'PK_VACANTE'=>$arrayVac['FK_VACANTE'] ])."'>".$arrayVac['DESC_VACANTE']."</a></td>";
                        $html.= "<td style='background-color: $bgColor;' class='border-top'>".$arrayVac['FECHA_ACTUALIZACION']."</td>";
                        $html.= "<td style='background-color: $bgColor;' class='border-top'>".$arrayVac['DESC_ESTATUS_CANDIDATO']."</td>";
                        $html.= "<td style='background-color: $bgColor; $pointer' class='border-top'>$comentariosCancelacion</td>";
                    $html.= '</tr>';
                    $i++;
                }
            }

            $cantCandidatos = count($datosCandidatos);
            if($cantCandidatos == 0){
                $html.="<tr>";
                    $html.='<td colspan="7"  style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.="</tr>";
            }

            return [
                'data'=>$html,
                'total_paginas'=>$total_paginas,
                'pagina'=>$pagina,
                'post'=>$post,
                'total_registros'=>$total_registros,
                // 'query'=>$query,
                // 'datosVacantes'=>$datosVacantes,
                'datosCandidatos'=>$datosCandidatos,
                // 'tamanio_pagina'=>((int)$pagina),
            ];
            //FIN AJAX
        }
        else{

            $datosCandidatos = (new \yii\db\Query())
                            ->select("
                                     tbl_vacantes_candidatos.FK_CANDIDATO
                                    , tbl_candidatos.NOMBRE
                                    , tbl_candidatos.APELLIDO_PATERNO
                                    , tbl_candidatos.APELLIDO_MATERNO
                                    , tbl_candidatos.CV
                                    ")
                            ->from('tbl_vacantes_candidatos')
                            ->join('LEFT JOIN','tbl_candidatos',
                                    'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                            ->distinct()
                            ->all();

            if(count($datosCandidatos)!=0){
                foreach ($datosCandidatos as $array) {
                    $datosVacantes[$array['FK_CANDIDATO']] = (new \yii\db\Query())
                                ->select(["tbl_vacantes_candidatos.FK_VACANTE"
                                        , "tbl_vacantes.DESC_VACANTE"
                                        , 'DATE_FORMAT(tbl_vacantes_candidatos.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION'
                                        , "tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO"
                                        , "tbl_bit_comentarios_candidato.COMENTARIOS"
                                        , "tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO"
                                        ])
                                ->from('tbl_vacantes_candidatos')
                                ->join('LEFT JOIN','tbl_vacantes',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                                ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                                        'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                                ->join('LEFT JOIN','tbl_bit_comentarios_candidato',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_bit_comentarios_candidato.FK_VACANTE
                                            AND tbl_vacantes_candidatos.FK_CANDIDATO = tbl_bit_comentarios_candidato.FK_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTACION_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTATUS_CANDIDATO
                                            ')
                                ->andWhere(['=', 'tbl_vacantes_candidatos.FK_CANDIDATO', $array['FK_CANDIDATO']])
                                ->all();

                }
            } else {
                $datosVacantes[] = 0;
            }

            $dummyCandidatos = new tblcandidatos;
            $dummyVacante = new tblvacantes;
            return $this->render('index7', [
                //'modelVacante' => $modelVacante,
                //'PK_VACANTE' => $pk_vacante,
                'datosCandidatos' => $datosCandidatos,
                'datosVacantes' => $datosVacantes,
                'dummyCandidatos' => $dummyCandidatos,
                'dummyVacante' => $dummyVacante,
            ]);
        }
    }

    /**
     * Bandeja candidatos descartados para entrevista
     * @return type
     */
    public function actionIndex8()
    {
        $request = Yii::$app->request;
        $tamanio_pagina= 20;
        $Limit = '';

        if (Yii::$app->request->isAjax) {
            //INICIO AJAX
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $candidato      =(!empty($post['nombre']))? trim($post['nombre']):'';
            $nombreVacante  =(!empty($post['vacante']))? trim($post['vacante']):'';
            $estatusVacante =(!empty($post['estatuscandidato']))? trim($post['estatuscandidato']):'';
            $pagina         =(!empty($post['page']))? trim($post['page']):'';
            $Limit = 'Limit '.(($pagina * $tamanio_pagina)-$tamanio_pagina).', '.$tamanio_pagina.' ';

            if(empty($pagina)){
                $pagina=0;
            }else{
                $pagina= $pagina-1;
            }

            // TOTAL DE REGISTROS
            $connection = \Yii::$app->db;
            $totalStored = $connection->createCommand("CALL SP_CANDIDATOS_DESCARTADOS_PARA_ENTREVISTA(:P_NOMBRE, :P_DESC_VACANTE, :P_FK_VACANTE,:P_FK_ESTACION_ACTUAL_CANDIDATO,:P_FK_ESTATUS_ACTUAL_CANDIDATO,:P_FK_UNIDAD_NEGOCIO,:P_LIMIT,:P_COUNT)")
                    ->bindValue(':P_NOMBRE', $candidato)
                    ->bindValue(':P_DESC_VACANTE', $nombreVacante)
                    ->bindValue(':P_FK_VACANTE', 0)
                    ->bindValue(':P_FK_ESTACION_ACTUAL_CANDIDATO', 0)
                    ->bindValue(':P_FK_ESTATUS_ACTUAL_CANDIDATO', 0)
                    ->bindValue(':P_FK_UNIDAD_NEGOCIO', 0)
                    ->bindValue(':P_LIMIT', '')
                    ->bindValue(':P_COUNT', 1)
                    ->queryAll();
                //Cerrar Conexion
            $connection->close();

            // CALCULAMOS TOTAL DE PAGINAS
            $total_registros = $totalStored[0]["NUM_ROWS"];
            $total_paginas = ($total_registros < $tamanio_pagina) ? 1 : ceil($total_registros / $tamanio_pagina);

            // RECUPERAMOS LOS DATOS A MOSTRAR
            $connection = \Yii::$app->db;
            $datosCandidatos = $connection->createCommand("CALL SP_CANDIDATOS_DESCARTADOS_PARA_ENTREVISTA(:P_NOMBRE, :P_DESC_VACANTE, :P_FK_VACANTE,:P_FK_ESTACION_ACTUAL_CANDIDATO,:P_FK_ESTATUS_ACTUAL_CANDIDATO,:P_FK_UNIDAD_NEGOCIO,:P_LIMIT,:P_COUNT)")
                    ->bindValue(':P_NOMBRE', $candidato)
                    ->bindValue(':P_DESC_VACANTE', $nombreVacante)
                    ->bindValue(':P_FK_VACANTE', 0)
                    ->bindValue(':P_FK_ESTACION_ACTUAL_CANDIDATO', 0)
                    ->bindValue(':P_FK_ESTATUS_ACTUAL_CANDIDATO', 0)
                    ->bindValue(':P_FK_UNIDAD_NEGOCIO', 0)
                    ->bindValue(':P_LIMIT', $Limit)
                    ->bindValue(':P_COUNT', 0)
                    ->queryAll();
                //Cerrar Conexion
            $connection->close();


            foreach ($datosCandidatos as $array) {
                $datosVacantes[$array['FK_CANDIDATO']] = (new \yii\db\Query())
                            ->select(["tbl_vacantes_candidatos.FK_VACANTE"
                                        , "tbl_vacantes.DESC_VACANTE"
                                        , 'DATE_FORMAT(tbl_vacantes_candidatos.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION'
                                        , "tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO"
                                        , "tbl_bit_comentarios_candidato.COMENTARIOS"
                                        , "tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO"
                                        ])
                            ->from('tbl_vacantes_candidatos')
                            ->join('LEFT JOIN','tbl_candidatos',
                                    'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                            ->join('LEFT JOIN','tbl_vacantes',
                                    'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                            ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                                    'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                            ->join('LEFT JOIN','tbl_bit_comentarios_candidato',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_bit_comentarios_candidato.FK_VACANTE
                                            AND tbl_vacantes_candidatos.FK_CANDIDATO = tbl_bit_comentarios_candidato.FK_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTACION_CANDIDATO
                                            ')
                                            // AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTATUS_CANDIDATO
                            ->andFilterWhere(
                            ['and',
                                ['LIKE', "CONCAT(tbl_candidatos.NOMBRE, ' ', tbl_candidatos.APELLIDO_PATERNO, ' ', tbl_candidatos.APELLIDO_MATERNO)", $candidato],
                                ['LIKE', 'tbl_vacantes.DESC_VACANTE', $nombreVacante],
                                ['=', 'tbl_vacantes_candidatos.FK_CANDIDATO', $array['FK_CANDIDATO']],
                                ['LIKE', 'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO', $estatusVacante]
                            ])
                            ->all();
            }
            $bgColor1 = '#FFFFFF';
            $bgColor2 = '#F5F5F5';
            $contadorCandidatos = 0;
            $html='';
            foreach ($datosCandidatos as $array) {
                $i = 0;
                $contadorCandidatos++;
                $cantRegVacantes = count($datosVacantes[$array['FK_CANDIDATO']]);

                if($contadorCandidatos%2==0){
                    $bgColor = $bgColor2;
                } else {
                    $bgColor = $bgColor1;
                }

                foreach ($datosVacantes[$array['FK_CANDIDATO']] as $arrayVac) {
                    if($arrayVac['FK_ESTATUS_ACTUAL_CANDIDATO']==5){
                        $comentariosCancelacion = ($arrayVac['COMENTARIOS']!='')?'<p data-toggle="modal" href="#candidatos-comentarios-cancelacion" onclick="traerComentarios('.$array['FK_CANDIDATO'].','.$arrayVac['FK_VACANTE'].')" class="name">Ver Detalle</p>':'N/A';
            $pointer = $arrayVac['COMENTARIOS']!=''?" cursor: pointer;":'';
                    } else {
                        $comentariosCancelacion = '';
            $pointer='';
                    }
                    $html.= '<tr>';
                        if($i==0){
                            $nombreCV = substr($array['CV'], 22);
                            $nombre = $array['NOMBRE'].' '.$array['APELLIDO_PATERNO'].' '.$array['APELLIDO_MATERNO'];
                            if(strcmp($array['CV'],'')!=0){
                                $array['CV'] = '../..'.$array['CV'];
                            } else {
                                $array['CV'] = 'javascript: void(0);';
                            }

                            $html.= "<td style='background-color: $bgColor;' class='border-top chk'><input type='checkbox' name='idEmpleado[]' id='idEmpleado' value='$array[FK_CANDIDATO]' class='chk_empleados'></td>";
                            $html.= "<td style='background-color: $bgColor;' class='border-top name'>
                                    <p data-toggle='modal' data-target='#candidatos-detalle' class='invocar-detalle' style='cursor: pointer;'>
                                        $nombre <input type='hidden' value='$array[FK_CANDIDATO]'/>
                                    <p>
                                </td>";
                            $html.= "<td style='background-color: $bgColor;' class='border-top'><a href='".$array['CV']."'>$nombreCV</a></td>";
                        } else {
                            $html.= "  <td style='border: 0px; background-color: $bgColor;'>&nbsp;</td>
                                    <td style='border: 0px; background-color: $bgColor;'>&nbsp;</td>
                                    <td style='border: 0px; background-color: $bgColor;'>&nbsp;</td>";
                        }
                        $html.= "<td style='background-color: $bgColor;' class='border-top name'><a href='".Url::to(["candidatos/view3",'PK_CANDIDATO'=> $array['FK_CANDIDATO'],'PK_VACANTE'=>$arrayVac['FK_VACANTE'] ])."'>".$arrayVac['DESC_VACANTE']."</a></td>";
                        $html.= "<td style='background-color: $bgColor;' class='border-top'>".$arrayVac['FECHA_ACTUALIZACION']."</td>";
                        $html.= "<td style='background-color: $bgColor;' class='border-top'>".$arrayVac['DESC_ESTATUS_CANDIDATO']."</td>";
                        $html.= "<td style='background-color: $bgColor; $pointer' class='border-top'>$comentariosCancelacion</td>";
                    $html.= '</tr>';
                    $i++;
                }
            }

            $cantCandidatos = count($datosCandidatos);
            if($cantCandidatos == 0){
                $html.="<tr>";
                    $html.='<td colspan="7"  style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.="</tr>";
            }

            return [
                'data'=>$html,
                'total_paginas'=>$total_paginas,
                'pagina'=>$pagina,
                'post'=>$post,
                'total_registros'=>$total_registros,
                // 'query'=>$query,
                // 'datosVacantes'=>$datosVacantes,
                'datosCandidatos'=>$datosCandidatos,
                // 'tamanio_pagina'=>((int)$pagina),
            ];
            //FIN AJAX
        }
        else{

            $datosCandidatos = (new \yii\db\Query())
                            ->select("
                                     tbl_vacantes_candidatos.FK_CANDIDATO
                                    , tbl_candidatos.NOMBRE
                                    , tbl_candidatos.APELLIDO_PATERNO
                                    , tbl_candidatos.APELLIDO_MATERNO
                                    , tbl_candidatos.CV
                                    ")
                            ->from('tbl_vacantes_candidatos')
                            ->join('LEFT JOIN','tbl_candidatos',
                                    'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                            ->distinct()
                            ->all();

            if(count($datosCandidatos)!=0){
                foreach ($datosCandidatos as $array) {
                    $datosVacantes[$array['FK_CANDIDATO']] = (new \yii\db\Query())
                                ->select(["tbl_vacantes_candidatos.FK_VACANTE"
                                        , "tbl_vacantes.DESC_VACANTE"
                                        , 'DATE_FORMAT(tbl_vacantes_candidatos.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION'
                                        , "tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO"
                                        , "tbl_bit_comentarios_candidato.COMENTARIOS"
                                        , "tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO"
                                        ])
                                ->from('tbl_vacantes_candidatos')
                                ->join('LEFT JOIN','tbl_vacantes',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                                ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                                        'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                                ->join('LEFT JOIN','tbl_bit_comentarios_candidato',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_bit_comentarios_candidato.FK_VACANTE
                                            AND tbl_vacantes_candidatos.FK_CANDIDATO = tbl_bit_comentarios_candidato.FK_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTACION_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTATUS_CANDIDATO
                                            ')
                                ->andWhere(['=', 'tbl_vacantes_candidatos.FK_CANDIDATO', $array['FK_CANDIDATO']])
                                ->all();

                }
            } else {
                $datosVacantes[] = 0;
            }

            $dummyCandidatos = new tblcandidatos;
            $dummyVacante = new tblvacantes;
            return $this->render('index8', [
                //'modelVacante' => $modelVacante,
                //'PK_VACANTE' => $pk_vacante,
                'datosCandidatos' => $datosCandidatos,
                'datosVacantes' => $datosVacantes,
                'dummyCandidatos' => $dummyCandidatos,
                'dummyVacante' => $dummyVacante,
            ]);
        }
    }

    /**
     * Bandeja candidatos que aplican para vacantes
     * @return type
     */
    public function actionIndex9()
    {
        $request = Yii::$app->request;
        $tamanio_pagina= 20;
        $Limit = '';

        if (Yii::$app->request->isAjax) {
            //INICIO AJAX
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $candidato      =(!empty($post['nombre']))? trim($post['nombre']):'';
            $nombreVacante  =(!empty($post['vacante']))? trim($post['vacante']):'';
            $estatusVacante =(!empty($post['estatuscandidato']))? trim($post['estatuscandidato']):'';
            $pagina         =(!empty($post['page']))? trim($post['page']):'';
            $Limit = 'Limit '.(($pagina * $tamanio_pagina)-$tamanio_pagina).', '.$tamanio_pagina.' ';

            if(empty($pagina)){
                $pagina=0;
            }else{
                $pagina= $pagina-1;
            }

            // TOTAL DE REGISTROS
            $connection = \Yii::$app->db;
            $totalStored = $connection->createCommand("CALL SP_CANDIDATOS_APLICAN_VACANTES(:P_NOMBRE, :P_DESC_VACANTE, :P_FK_VACANTE,:P_FK_ESTACION_ACTUAL_CANDIDATO,:P_FK_ESTATUS_ACTUAL_CANDIDATO,:P_FK_UNIDAD_NEGOCIO,:P_LIMIT,:P_COUNT)")
                    ->bindValue(':P_NOMBRE', $candidato)
                    ->bindValue(':P_DESC_VACANTE', $nombreVacante)
                    ->bindValue(':P_FK_VACANTE', 0)
                    ->bindValue(':P_FK_ESTACION_ACTUAL_CANDIDATO', 0)
                    ->bindValue(':P_FK_ESTATUS_ACTUAL_CANDIDATO', 0)
                    ->bindValue(':P_FK_UNIDAD_NEGOCIO', 0)
                    ->bindValue(':P_LIMIT', '')
                    ->bindValue(':P_COUNT', 1)
                    ->queryAll();
                //Cerrar Conexion
            $connection->close();

            // CALCULAMOS TOTAL DE PAGINAS
            $total_registros = $totalStored[0]["NUM_ROWS"];
            $total_paginas = ($total_registros < $tamanio_pagina) ? 1 : ceil($total_registros / $tamanio_pagina);

            // RECUPERAMOS LOS DATOS A MOSTRAR
            $connection = \Yii::$app->db;
            $datosCandidatos = $connection->createCommand("CALL SP_CANDIDATOS_APLICAN_VACANTES(:P_NOMBRE, :P_DESC_VACANTE, :P_FK_VACANTE,:P_FK_ESTACION_ACTUAL_CANDIDATO,:P_FK_ESTATUS_ACTUAL_CANDIDATO,:P_FK_UNIDAD_NEGOCIO,:P_LIMIT,:P_COUNT)")
                    ->bindValue(':P_NOMBRE', $candidato)
                    ->bindValue(':P_DESC_VACANTE', $nombreVacante)
                    ->bindValue(':P_FK_VACANTE', 0)
                    ->bindValue(':P_FK_ESTACION_ACTUAL_CANDIDATO', 0)
                    ->bindValue(':P_FK_ESTATUS_ACTUAL_CANDIDATO', 0)
                    ->bindValue(':P_FK_UNIDAD_NEGOCIO', 0)
                    ->bindValue(':P_LIMIT', $Limit)
                    ->bindValue(':P_COUNT', 0)
                    ->queryAll();
                //Cerrar Conexion
            $connection->close();


            foreach ($datosCandidatos as $array) {
                $datosVacantes[$array['FK_CANDIDATO']] = (new \yii\db\Query())
                            ->select(["tbl_vacantes_candidatos.FK_VACANTE"
                                        , "tbl_vacantes.DESC_VACANTE"
                                        , 'DATE_FORMAT(tbl_vacantes_candidatos.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION'
                                        , "tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO"
                                        , "tbl_bit_comentarios_candidato.COMENTARIOS"
                                        , "tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO"
                                        ])
                            ->from('tbl_vacantes_candidatos')
                            ->join('LEFT JOIN','tbl_candidatos',
                                    'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                            ->join('LEFT JOIN','tbl_vacantes',
                                    'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                            ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                                    'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                            ->join('LEFT JOIN','tbl_bit_comentarios_candidato',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_bit_comentarios_candidato.FK_VACANTE
                                            AND tbl_vacantes_candidatos.FK_CANDIDATO = tbl_bit_comentarios_candidato.FK_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTACION_CANDIDATO
                                            ')
                                            // AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTATUS_CANDIDATO
                            ->andFilterWhere(
                            ['and',
                                ['LIKE', "CONCAT(tbl_candidatos.NOMBRE, ' ', tbl_candidatos.APELLIDO_PATERNO, ' ', tbl_candidatos.APELLIDO_MATERNO)", $candidato],
                                ['LIKE', 'tbl_vacantes.DESC_VACANTE', $nombreVacante],
                                ['=', 'tbl_vacantes_candidatos.FK_CANDIDATO', $array['FK_CANDIDATO']],
                                ['LIKE', 'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO', $estatusVacante]
                            ])
                            ->all();
            }
            $bgColor1 = '#FFFFFF';
            $bgColor2 = '#F5F5F5';
            $contadorCandidatos = 0;
            $html='';
            foreach ($datosCandidatos as $array) {
                $i = 0;
                $contadorCandidatos++;
                $cantRegVacantes = count($datosVacantes[$array['FK_CANDIDATO']]);

                if($contadorCandidatos%2==0){
                    $bgColor = $bgColor2;
                } else {
                    $bgColor = $bgColor1;
                }

                foreach ($datosVacantes[$array['FK_CANDIDATO']] as $arrayVac) {
                    if($arrayVac['FK_ESTATUS_ACTUAL_CANDIDATO']==5){
                        $comentariosCancelacion = ($arrayVac['COMENTARIOS']!='')?'<p data-toggle="modal" href="#candidatos-comentarios-cancelacion" onclick="traerComentarios('.$array['FK_CANDIDATO'].','.$arrayVac['FK_VACANTE'].')" class="name">Ver Detalle</p>':'N/A';
            $pointer = $arrayVac['COMENTARIOS']!=''?" cursor: pointer;":'';
                    } else {
                        $comentariosCancelacion = '';
            $pointer='';
                    }
                    $html.= '<tr>';
                        if($i==0){
                            $nombreCV = substr($array['CV'], 22);
                            $nombre = $array['NOMBRE'].' '.$array['APELLIDO_PATERNO'].' '.$array['APELLIDO_MATERNO'];
                            if(strcmp($array['CV'],'')!=0){
                                $array['CV'] = '../..'.$array['CV'];
                            } else {
                                $array['CV'] = 'javascript: void(0);';
                            }

                            $html.= "<td style='background-color: $bgColor;' class='border-top chk'><input type='checkbox' name='idEmpleado[]' id='idEmpleado' value='$array[FK_CANDIDATO]' class='chk_empleados'></td>";
                            $html.= "<td style='background-color: $bgColor;' class='border-top name'>
                                    <p data-toggle='modal' data-target='#candidatos-detalle' class='invocar-detalle' style='cursor: pointer;'>
                                        $nombre <input type='hidden' value='$array[FK_CANDIDATO]'/>
                                    <p>
                                </td>";
                            $html.= "<td style='background-color: $bgColor;' class='border-top'><a href='".$array['CV']."'>$nombreCV</a></td>";
                        } else {
                            $html.= "  <td style='border: 0px; background-color: $bgColor;'>&nbsp;</td>
                                    <td style='border: 0px; background-color: $bgColor;'>&nbsp;</td>
                                    <td style='border: 0px; background-color: $bgColor;'>&nbsp;</td>";
                        }
                        $html.= "<td style='background-color: $bgColor;' class='border-top name'><a href='".Url::to(["candidatos/view3",'PK_CANDIDATO'=> $array['FK_CANDIDATO'],'PK_VACANTE'=>$arrayVac['FK_VACANTE'] ])."'>".$arrayVac['DESC_VACANTE']."</a></td>";
                        $html.= "<td style='background-color: $bgColor;' class='border-top'>".$arrayVac['FECHA_ACTUALIZACION']."</td>";
                        $html.= "<td style='background-color: $bgColor;' class='border-top'>".$arrayVac['DESC_ESTATUS_CANDIDATO']."</td>";
                        $html.= "<td style='background-color: $bgColor; $pointer' class='border-top'>$comentariosCancelacion</td>";
                    $html.= '</tr>';
                    $i++;
                }
            }

            $cantCandidatos = count($datosCandidatos);
            if($cantCandidatos == 0){
                $html.="<tr>";
                    $html.='<td colspan="7"  style="text-align: center;font-size: large; font-weight: bold; text-decoration: underline; width: 100%; height: 100px;"></br> NO SE ENCONTRARON DATOS CON LOS PARAMETROS ESPECIFICADOS </br></br></td>';
                $html.="</tr>";
            }

            return [
                'data'=>$html,
                'total_paginas'=>$total_paginas,
                'pagina'=>$pagina,
                'post'=>$post,
                'total_registros'=>$total_registros,
                // 'query'=>$query,
                // 'datosVacantes'=>$datosVacantes,
                'datosCandidatos'=>$datosCandidatos,
                // 'tamanio_pagina'=>((int)$pagina),
            ];
            //FIN AJAX
        }
        else{

            $datosCandidatos = (new \yii\db\Query())
                            ->select("
                                     tbl_vacantes_candidatos.FK_CANDIDATO
                                    , tbl_candidatos.NOMBRE
                                    , tbl_candidatos.APELLIDO_PATERNO
                                    , tbl_candidatos.APELLIDO_MATERNO
                                    , tbl_candidatos.CV
                                    ")
                            ->from('tbl_vacantes_candidatos')
                            ->join('LEFT JOIN','tbl_candidatos',
                                    'tbl_vacantes_candidatos.FK_CANDIDATO = tbl_candidatos.PK_CANDIDATO')
                            ->distinct()
                            ->all();

            if(count($datosCandidatos)!=0){
                foreach ($datosCandidatos as $array) {
                    $datosVacantes[$array['FK_CANDIDATO']] = (new \yii\db\Query())
                                ->select(["tbl_vacantes_candidatos.FK_VACANTE"
                                        , "tbl_vacantes.DESC_VACANTE"
                                        , 'DATE_FORMAT(tbl_vacantes_candidatos.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION'
                                        , "tbl_cat_estatus_candidato.DESC_ESTATUS_CANDIDATO"
                                        , "tbl_bit_comentarios_candidato.COMENTARIOS"
                                        , "tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO"
                                        ])
                                ->from('tbl_vacantes_candidatos')
                                ->join('LEFT JOIN','tbl_vacantes',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_vacantes.PK_VACANTE')
                                ->join('LEFT JOIN','tbl_cat_estatus_candidato',
                                        'tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_cat_estatus_candidato.PK_ESTATUS_CANDIDATO')
                                ->join('LEFT JOIN','tbl_bit_comentarios_candidato',
                                        'tbl_vacantes_candidatos.FK_VACANTE = tbl_bit_comentarios_candidato.FK_VACANTE
                                            AND tbl_vacantes_candidatos.FK_CANDIDATO = tbl_bit_comentarios_candidato.FK_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTACION_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTACION_CANDIDATO
                                            AND tbl_vacantes_candidatos.FK_ESTATUS_ACTUAL_CANDIDATO = tbl_bit_comentarios_candidato.FK_ESTATUS_CANDIDATO
                                            ')
                                ->andWhere(['=', 'tbl_vacantes_candidatos.FK_CANDIDATO', $array['FK_CANDIDATO']])
                                ->all();

                }
            } else {
                $datosVacantes[] = 0;
            }

            $dummyCandidatos = new tblcandidatos;
            $dummyVacante = new tblvacantes;
            return $this->render('index9', [
                //'modelVacante' => $modelVacante,
                //'PK_VACANTE' => $pk_vacante,
                'datosCandidatos' => $datosCandidatos,
                'datosVacantes' => $datosVacantes,
                'dummyCandidatos' => $dummyCandidatos,
                'dummyVacante' => $dummyVacante,
            ]);
        }
    }

    public function actionIndex10(){
      $request = Yii::$app->request;
      // $fk_candidato = $request->get('idRecurso');
      $fk_candidato = (!empty($request->get('idRecurso')))? $request->get('idRecurso'):'';
      $FK_PROSPECTO = (!empty($request->get('idProspecto')))? $request->get('idProspecto'):'';

      $data = Yii::$app->request->post();

            if (Yii::$app->request->post() && isset($data['idVacante'])) {
                $data = Yii::$app->request->post();

                /*Obtener vacantes seleccionadas*/
                $vacantes = $data['idVacante'];

                  //SE AGREGO FUNCIONALIDAD PARA QUE MODIFICARA EL ESTATUS DE LA VACANTE
                foreach ($vacantes as $key => $value) {
                      // Este estatus se asigna automaticamente cuando se le asocie un candidato a la Vacante
                      $modelVacante = tblvacantes::findOne($value);
                      $modelVacante->FK_ESTACION_VACANTE=3;
                      $modelVacante->FK_ESTATUS_VACANTE=2;
                      $modelVacante->save(false);
                  }

                if (!empty($data['idRecurso'])) {
                  $candidatos = $data['idRecurso'];

                  foreach ($vacantes as $key2 => $value)   {
                      foreach ($candidatos as $key => $candidato)   {

                        //Validación de candidatos asignados a vacantes
                        $variable[$key2][$key] = $modelCandidatosVacantes = TblVacantesCandidatos::find()->where(['FK_VACANTE' => $vacantes[$key2] , 'FK_CANDIDATO' => $candidatos[$key]])->all();
                          if(count($modelCandidatosVacantes) == 0){
                              $modelCandidatosVacantes = new TblVacantesCandidatos;
                              $modelCandidatosVacantes['FK_VACANTE'] = $vacantes[$key2];
                              $modelCandidatosVacantes['FK_CANDIDATO'] = $candidatos[$key];
                              $modelCandidatosVacantes['FK_ESTACION_ACTUAL_CANDIDATO'] = '3';
                              $modelCandidatosVacantes['FK_ESTATUS_ACTUAL_CANDIDATO'] = '1';
                              $modelCandidatosVacantes['FECHA_REGISTRO'] = date('Y-m-d');
                              $modelCandidatosVacantes['FECHA_ACTUALIZACION'] = date('Y-m-d');
                              $modelCandidatosVacantes->save(false);

                              $modelBitacoraCandidato = new tblbitcomentarioscandidato();
                              $modelBitacoraCandidato['FK_VACANTE'] = $value;
                              $modelBitacoraCandidato['FK_CANDIDATO'] = $candidatos[$key];
                              $modelBitacoraCandidato['FK_ESTACION_CANDIDATO'] = 3;
                              $modelBitacoraCandidato['FK_ESTATUS_CANDIDATO'] = 1;
                              $modelBitacoraCandidato['FK_USUARIO'] = 1;
                              $modelBitacoraCandidato['COMENTARIOS'] = 'SE ASOCIÓ A UNA O MÁS VACANTES';
                              $modelBitacoraCandidato['DOCUMENTO_ASOCIADO'] = '';
                              $modelBitacoraCandidato['FECHA_REGISTRO'] = date('Y-m-d');
                              $modelBitacoraCandidato->save(false);

                          }
                      }
                    }
                    $this->redirect(["candidatos/index2"]);
                  }
                  else{

                    $idProspecto = array_unique($data['idProspecto']);
                    //$idProspecto = $data['idProspecto'];

                    foreach ($idProspecto as $key => $value) {
                      /**
                      * CAMBIO DE PROSPECTO A CANDIDATO, SE OBTIENEN TODOS LOS DATOS DE LOS PROSPECTOS, SE INSERTAN EN LA TABLA DE CANDIDATOS
                      * Y SE GUARDA EL PK DEL PROSPECTO.
                      */
                      $prospectos = (new \yii\db\Query())
                      ->select('*')
                      ->from ('tbl_prospectos')
                      ->where(['PK_PROSPECTO' => $value])
                      ->one();

                      $modelCandidato = TblCandidatos::find()->where(['FK_PROSPECTO' => $value])->one();
                      $candidato = count($modelCandidato);

                      /*ACTUALIZACIÓN DE CANDIDADO SI SE ENCUENTRA EL PROSPECTO*/
                      if ($candidato == 1) {
                        $modelCandidato['NOMBRE'] = $prospectos['NOMBRE'];
                        $modelCandidato['APELLIDO_PATERNO'] = $prospectos['APELLIDO_PATERNO'];
                        $modelCandidato['APELLIDO_MATERNO'] = $prospectos['APELLIDO_MATERNO'];
                        $modelCandidato['RFC'] = '';
                        $modelCandidato['CURP'] = $prospectos['CURP'];
                        $modelCandidato['EDAD'] = $prospectos['EDAD'];
                        $modelCandidato['FK_GENERO'] = $prospectos['FK_GENERO'];
                        $modelCandidato['FECHA_NAC_CAN'] = $prospectos['FECHA_NAC'];
                        $modelCandidato['EMAIL'] = $prospectos['EMAIL'];
                        $modelCandidato['TELEFONO'] = $prospectos['TELEFONO'];
                        $modelCandidato['CELULAR'] = $prospectos['CELULAR'];
                        $modelCandidato['PERFIL'] = $prospectos['PERFIL'];
                        $modelCandidato['UNIVERSIDAD'] = $prospectos['UNIVERSIDAD'];
                        $modelCandidato['CARRERA'] = $prospectos['CARRERA'];
                        $modelCandidato['CONOCIMIENTOS_TECNICOS'] = $prospectos['CONOCIMIENTOS_TECNICOS'];
                        $modelCandidato['COMENTARIOS'] = $prospectos['COMENTARIOS'];
                        $modelCandidato['NIVEL_ESCOLARIDAD'] = $prospectos['NIVEL_ESCOLARIDAD'];
                        $modelCandidato['FK_ESTATUS_PROSPECTO'] = $prospectos['FK_ESTATUS'];
                        $modelCandidato['FK_ESTADO_PROSPECTO'] = $prospectos['FK_ESTADO'];
                        $modelCandidato['ESTATUS_CAND_APLIC'] = 1;

                        $modelCandidato->save(false);

                        foreach ($vacantes as $key2 => $Vvalue)   {
                          /**
                          *ASIGNACION DE LA VACANTE
                          */
                          $modelCandidatoVacantes = TblVacantesCandidatos::find()->where(['FK_VACANTE' => $vacantes[$key2] , 'FK_CANDIDATO' => $modelCandidato['PK_CANDIDATO']])->all();
                          if (count($modelCandidatoVacantes) == 0) {
                            $modelVacantesCandidatos = new TblVacantesCandidatos;
                            $modelVacantesCandidatos['FK_VACANTE'] = $Vvalue;
                            $modelVacantesCandidatos['FK_CANDIDATO'] = $modelCandidato['PK_CANDIDATO'];
                            $modelVacantesCandidatos['FK_ESTACION_ACTUAL_CANDIDATO'] = '3';
                            $modelVacantesCandidatos['FK_ESTATUS_ACTUAL_CANDIDATO'] = '1';
                            $modelVacantesCandidatos['FECHA_REGISTRO'] = date('Y-m-d');
                            $modelVacantesCandidatos['FECHA_ACTUALIZACION'] = date('Y-m-d');
                            $modelVacantesCandidatos->save(false);

                            // $modelBitacoraCandidato = new tblbitcomentarioscandidato();
                            // $modelBitacoraCandidato['FK_VACANTE'] = $Vvalue;
                            // $modelBitacoraCandidato['FK_CANDIDATO'] = $modelCandidato['PK_CANDIDATO'];
                            // $modelBitacoraCandidato['FK_PROSPECTO'] = $modelCandidato['FK_PROSPECTO'];
                            // $modelBitacoraCandidato['EMAIL'] = $modelCandidato['EMAIL'];
                            // $modelBitacoraCandidato['CELULAR'] = $modelCandidato['CELULAR'];
                            // $modelBitacoraCandidato['FK_ESTACION_CANDIDATO'] = 3;
                            // $modelBitacoraCandidato['FK_ESTATUS_CANDIDATO'] = 1;
                            // $modelBitacoraCandidato['FK_USUARIO'] = 1;
                            // $modelBitacoraCandidato['COMENTARIOS'] = 'PASO DE PROSPECTO A CANDIDATO';
                            // $modelBitacoraCandidato['DOCUMENTO_ASOCIADO'] = '';
                            // $modelBitacoraCandidato['FECHA_REGISTRO'] = date('Y-m-d');
                            // $modelBitacoraCandidato->save(false);
                          }
                        }
                      }
                      /*FIN ACTUALIZACIÓN DE CANDIDADO SI SE ENCUENTRA EL PROSPECTO*/
                      else{

                        $modelCandidato = new TblCandidatos;
                        $modelCandidato['FK_PROSPECTO'] = $value;
                        $modelCandidato['NOMBRE'] = $prospectos['NOMBRE'];
                        $modelCandidato['APELLIDO_PATERNO'] = $prospectos['APELLIDO_PATERNO'];
                        $modelCandidato['APELLIDO_MATERNO'] = $prospectos['APELLIDO_MATERNO'];
                        $modelCandidato['RFC'] = '';
                        $modelCandidato['CURP'] = $prospectos['CURP'];
                        $modelCandidato['EDAD'] = $prospectos['EDAD'];
                        $modelCandidato['FK_GENERO'] = $prospectos['FK_GENERO'];
                        $modelCandidato['FECHA_NAC_CAN'] = $prospectos['FECHA_NAC'];
                        $modelCandidato['EMAIL'] = $prospectos['EMAIL'];
                        $modelCandidato['TELEFONO'] = $prospectos['TELEFONO'];
                        $modelCandidato['CELULAR'] = $prospectos['CELULAR'];
                        $modelCandidato['PERFIL'] = $prospectos['PERFIL'];
                        $modelCandidato['UNIVERSIDAD'] = $prospectos['UNIVERSIDAD'];
                        $modelCandidato['CARRERA'] = $prospectos['CARRERA'];
                        $modelCandidato['CONOCIMIENTOS_TECNICOS'] = $prospectos['CONOCIMIENTOS_TECNICOS'];
                        $modelCandidato['COMENTARIOS'] = $prospectos['COMENTARIOS'];
                        $modelCandidato['NIVEL_ESCOLARIDAD'] = $prospectos['NIVEL_ESCOLARIDAD'];
                        $modelCandidato['ESTATUS_CAND_APLIC'] = 1;
                        $modelCandidato['FECHA_REGISTRO'] = date('Y-m-d');
                        $modelCandidato['RECLUTADOR'] = $prospectos['RECLUTADOR'];
                        $modelCandidato['EXPECTATIVA'] = $prospectos['EXPECTATIVA'];
                        $modelCandidato['DISPONIBILIDAD'] = $prospectos['DISPONIBILIDAD'];
                        $modelCandidato['FECHA_CONVERSACION'] = $prospectos['FECHA_CONVERSACION'];
                        $modelCandidato['LUGAR_RESIDENCIA'] = $prospectos['LUGAR_RESIDENCIA'];
                        $modelCandidato['FK_FUENTE_VACANTE'] = $prospectos['FK_FUENTE_VACANTE'];
                        $modelCandidato['DISPONIBILIDAD_INTEGRACION'] = $prospectos['DISPONIBILIDAD_INTEGRACION'];
                        $modelCandidato['DISPONIBILIDAD_ENTREVISTA'] = $prospectos['DISPONIBILIDAD_ENTREVISTA'];
                        $modelCandidato['TRABAJA_ACTUALMENTE'] = $prospectos['TRABAJA_ACTUALMENTE'];
                        $modelCandidato['FK_CANAL'] = $prospectos['FK_CANAL'];
                        $modelCandidato['SUELDO_ACTUAL'] = $prospectos['SUELDO_ACTUAL'];
                        $modelCandidato['CAPACIDAD_RECURSO'] = $prospectos['CAPACIDAD_RECURSO'];
                        $modelCandidato['TACTO_CLIENTE'] = $prospectos['TACTO_CLIENTE'];
                        $modelCandidato['DESEMPENIO_CLIENTE'] = $prospectos['DESEMPENIO_CLIENTE'];
                        $modelCandidato['FK_ESTATUS_PROSPECTO'] = $prospectos['FK_ESTATUS'];
                        $modelCandidato['FK_ESTADO_PROSPECTO'] = $prospectos['FK_ESTADO'];
                        $modelCandidato['FK_USUARIO'] = $prospectos['FK_USUARIO'];
                        $modelCandidato['FK_ORIGEN'] = $prospectos['FK_ORIGEN'];
                        $modelCandidato->save(false);

                        $candidatos = (new \yii\db\Query())
                          ->select('PK_CANDIDATO')
                          ->from('tbl_candidatos')
                          ->where(['FK_PROSPECTO' => $value])
                          ->one();


                        foreach ($vacantes as $key2 => $Vvalue)   {
                          /**
                          *ASIGNACION DE LA VACANTE
                          */
                          $modelVacantesCandidatos = new TblVacantesCandidatos;
                          $modelVacantesCandidatos['FK_VACANTE'] = $Vvalue;
                          $modelVacantesCandidatos['FK_CANDIDATO'] = $candidatos['PK_CANDIDATO'];
                          $modelVacantesCandidatos['FK_ESTACION_ACTUAL_CANDIDATO'] = '3';
                          $modelVacantesCandidatos['FK_ESTATUS_ACTUAL_CANDIDATO'] = '1';
                          $modelVacantesCandidatos['FECHA_REGISTRO'] = date('Y-m-d');
                          $modelVacantesCandidatos['FECHA_ACTUALIZACION'] = date('Y-m-d');
                          $modelVacantesCandidatos->save(false);

                          /**
                          * SE ENVÍAN LOS DATOS DE TECNOLOGÍAS, HERRAMIENTAS, HABILIDADES Y PERFILES A LA DE LOS PROSPECTOS A LAS TABLAS DE CANDIDATOS.
                          */

                          $dataTecnologias = (new \yii\db\Query())
                          ->select('*')
                          ->from ('tbl_prospectos_tecnologias')
                          ->where(['FK_PROSPECTO' => $value])
                          ->all();

                          foreach ($dataTecnologias as $Tkey => $tecnologias) {
                              $modelTecnologia= new TblCandidatosTecnologias;
                              $modelTecnologia['FK_CANDIDATO'] = $candidatos['PK_CANDIDATO'];
                              $modelTecnologia['FK_PROSPECTO'] = $value;
                              $modelTecnologia['FK_TECNOLOGIA'] = $tecnologias['FK_TECNOLOGIA'];
                              $modelTecnologia['NIVEL_EXPERIENCIA'] = $tecnologias['NIVEL_EXPERIENCIA'];
                              $modelTecnologia['TIEMPO_USO'] = $tecnologias['TIEMPO_USO'];
                              $modelTecnologia['FECHA_REGISTRO'] = date('Y-m-d');
                              $modelTecnologia->save(false);
                          }

                          $dataHerramientas = (new \yii\db\Query())
                          ->select('*')
                          ->from ('tbl_prospectos_herramientas')
                          ->where(['FK_PROSPECTO' => $value])
                          ->all();

                          foreach ($dataHerramientas as $Hekey => $herramientas) {
                              $modelHerramientas= new TblCandidatosHerramientas;
                              $modelHerramientas['FK_CANDIDATO'] = $candidatos['PK_CANDIDATO'];
                              $modelHerramientas['FK_PROSPECTO'] = $value;
                              $modelHerramientas['FK_HERRAMIENTA'] = $herramientas['FK_HERRAMIENTA'];
                              $modelHerramientas['NIVEL_EXPERIENCIA'] = $herramientas['NIVEL_EXPERIENCIA'];
                              $modelHerramientas['TIEMPO_USO'] = $herramientas['TIEMPO_USO'];
                              $modelHerramientas['FECHA_REGISTRO'] = date('Y-m-d');
                              $modelHerramientas->save(false);
                          }

                          $dataHabilidades = (new \yii\db\Query())
                          ->select('*')
                          ->from ('tbl_prospectos_habilidades')
                          ->where(['FK_PROSPECTO' => $value])
                          ->all();

                          foreach ($dataHabilidades as $Hakey => $habilidades) {
                              $modelHabilidades = new TblCandidatosHabilidades;
                              $modelHabilidades['FK_CANDIDATO'] = $candidatos['PK_CANDIDATO'];
                              $modelHabilidades['FK_PROSPECTO'] = $value;
                              $modelHabilidades['FK_HABILIDAD'] = $habilidades['FK_HABILIDAD'];
                              $modelHabilidades['FECHA_REGISTRO'] = date('Y-m-d');
                              $modelHabilidades->save(false);
                          }

                          $dataPerfiles = (new \yii\db\Query())
                          ->select('*')
                          ->from ('tbl_prospectos_perfiles')
                          ->where(['FK_PROSPECTO' => $value])
                          ->all();

                          foreach ($dataPerfiles as $Hakey => $perfiles) {
                              $modelPerfiles = new TblCandidatosPerfiles;
                              $modelPerfiles['FK_CANDIDATO'] = $candidatos['PK_CANDIDATO'];
                              $modelPerfiles['FK_PROSPECTO'] = $value;
                              $modelPerfiles['FK_PERFIL'] = $perfiles['FK_PERFIL'];
                              $modelPerfiles['NIVEL_EXPERIENCIA'] = $perfiles['NIVEL_EXPERIENCIA'];
                              $modelPerfiles['FECHA_REGISTRO'] = date('Y-m-d');
                              $modelPerfiles->save(false);
                          }

                        }

                      }

                      $modelBitacoraCandidato = new tblbitcomentarioscandidato();
                      $modelBitacoraCandidato['FK_VACANTE'] = $Vvalue;
                      $modelBitacoraCandidato['FK_CANDIDATO'] = $modelCandidato['PK_CANDIDATO'];
                      $modelBitacoraCandidato['FK_PROSPECTO'] = $value;
                      $modelBitacoraCandidato['EMAIL'] = $prospectos['EMAIL'];
                      $modelBitacoraCandidato['CELULAR'] = $prospectos['CELULAR'];
                      $modelBitacoraCandidato['FK_ESTACION_CANDIDATO'] = 3;
                      $modelBitacoraCandidato['FK_ESTATUS_CANDIDATO'] = 1;
                      $modelBitacoraCandidato['FK_USUARIO'] = 1;
                      $modelBitacoraCandidato['COMENTARIOS'] = 'PASO DE PROSPECTO A CANDIDATO';
                      $modelBitacoraCandidato['DOCUMENTO_ASOCIADO'] = '';
                      $modelBitacoraCandidato['FECHA_REGISTRO'] = date('Y-m-d');
                      $modelBitacoraCandidato->save(false);

                    //C.V de prospecto pasan a candidato.

                    $DocsProspectos = (new \yii\db\Query())
                      ->select('*')
                      ->from ('tbl_prospectos_documentos')
                      ->where(['FK_PROSPECTO' => $value])
                      ->orderBy(['FK_TIPO_CV' =>SORT_ASC])
                      ->all();

                      if (!empty($DocsProspectos)) {
                        $TipoCV = TblCatTipoCV::find()->orderBy(['PK_TIPO_CV'=>SORT_ASC])->all();
                        $PkCV = array_column($TipoCV, 'PK_TIPO_CV');

                        foreach ($DocsProspectos as $keydoc => $valuedoc) {

                          $tmp_name = substr($valuedoc['RUTA_CV'], 3, strlen($valuedoc['RUTA_CV']));
                          $infoFile = pathInfo($tmp_name);
                          $PositionCV = array_search($valuedoc['FK_TIPO_CV'], $PkCV);
                          $DESC_CV = $TipoCV[$PositionCV]['DESC_CV'];
                          $nombre = 'CV'.$DESC_CV.'_'.$modelCandidato['PK_CANDIDATO'].'_'.date('Y-m-d').'.'.$infoFile['extension'];

                          if (copy($tmp_name, "../uploads/CandidatosCV/$nombre")) {

                              unlink ($tmp_name);

                              $rutaGuardado = '../uploads/CandidatosCV/';
                              $modelDocCandidatos = new TblCandidatosDocumentos;
                              $modelDocCandidatos['FK_CANDIDATO'] = $modelCandidato['PK_CANDIDATO'];
                              $modelDocCandidatos['FK_PROSPECTO'] = $value;
                              $modelDocCandidatos['FK_TIPO_CV'] = $valuedoc['FK_TIPO_CV'];
                              $modelDocCandidatos['RUTA_CV'] = '../'.$rutaGuardado.''.$nombre;
                              $modelDocCandidatos->FECHA_REGISTRO = date('Y-m-d');
                              $modelDocCandidatos->save(false);

                              //Se elimina registro de C.V en el prospecto
                              $connection = (new \yii\db\Query())
                              ->createCommand()
                              ->delete('tbl_prospectos_documentos', 'FK_PROSPECTO = '.$value.'')
                              ->execute();
                            }

                        }
                      }
                      /**
                      * Historial de Prospectos
                      */
                      $modelBitProspecto = new TblBitProspectos;
                      $modelBitProspecto['FK_PROSPECTO'] = $value;
                      $modelBitProspecto['EMAIL'] = $prospectos['EMAIL'];
                      $modelBitProspecto['CELULAR'] = $prospectos['CELULAR'];
                      $modelBitProspecto['TELEFONO'] = $prospectos['TELEFONO'];
                      $modelBitProspecto['FK_ESTATUS'] = $prospectos['FK_ESTATUS'];
                      $modelBitProspecto['PERFIL'] = $prospectos['PERFIL'];
                      $modelBitProspecto['FECHA_CONVERSACION'] = $prospectos['FECHA_CONVERSACION'];
                      $modelBitProspecto['FK_ESTADO'] = $prospectos['FK_ESTADO'];
                      $modelBitProspecto['RECLUTADOR'] = isset($prospectos['RECLUTADOR']) ? $prospectos['RECLUTADOR'] : 0;
                      $modelBitProspecto['EXPECTATIVA'] = $prospectos['EXPECTATIVA'];
                      $modelBitProspecto['DISPONIBILIDAD_INTEGRACION'] = $prospectos['DISPONIBILIDAD_INTEGRACION'];
                      $modelBitProspecto['DISPONIBILIDAD_ENTREVISTA'] = $prospectos['DISPONIBILIDAD_ENTREVISTA'];
                      $modelBitProspecto['TRABAJA_ACTUALMENTE'] = $prospectos['TRABAJA_ACTUALMENTE'];
                      $modelBitProspecto['CANAL'] = $prospectos['FK_CANAL'];
                      $modelBitProspecto['SUELDO_ACTUAL'] = $prospectos['SUELDO_ACTUAL'];
                      $modelBitProspecto['COMENTARIOS'] = 'TRANSICIÓN DE PROSPECTO A CANDIDATO';
                      $modelBitProspecto['FK_USUARIO'] = user_info()['PK_USUARIO'];
                      $modelBitProspecto['FECHA_REGISTRO'] = date('Y-m-d');
                      $modelBitProspecto->save(false);

                      /**
                      *Se elimina el prospecto.
                      */

                      $connection = (new \yii\db\Query())
                      ->createCommand()
                      ->delete('tbl_prospectos', 'PK_PROSPECTO = '.$value.'')
                      ->execute();
                    }


                  $this->redirect(["aspirantes/index"]);
                }
            } else {

                $dataProvider = (new \yii\db\Query())
                ->select([
                        'v.PK_VACANTE',
                        'v.FECHA_CREACION AS FECHA_REGISTRO',
                        'v.FK_PRIORIDAD',
                        'v.FK_ESTACION_VACANTE',
                        'v.FK_ESTATUS_VACANTE',
                        'v.FK_UBICACION',
                        'v.FK_UBICACION_CLIENTE',

                        //**Información general**
                        'pr.DESC_PRIORIDAD',
                        'catpuesto.DESC_PUESTO',
                        'n.DESC_NIVEL',
                        'v.FECHA_CIERRE',
                        'v.DESC_VACANTE',
                        'v.COSTO_MAXIMO',
                        'v.CANT_PERSONAS',
                        'catareas.DESC_AREA',
                        'rh.NOMBRE_RESPONSABLE_RH',
                        'g.DESC_GENERO',

                        //**Perfil Vacante**
                        //+Tecnologias
                        //+Herramientas
                        //+Habilidades


                        //**Detalle de la vacante**
                        'duracioncontrato.DESC_DURACION',
                        'tv.DESC_TIPO_VACANTE',
                        'v.COMENTARIOS',
                        'v.FUNCIONES',
                        'workstation.DESC_WORKSTATION',
                        //+UbicacionCliente
                        'ev.DESC_ESTATUS_VACANTE',
                        'tc.DESC_TIPO_CONTRATO',
                        'v.NOMBRE_PROYECTO',
                        'ub.DESC_UBICACION',
                        'ubc.DESC_UBICACION AS DESC_UBICACION_CLIENTE',
                        //+UbicaciónInterna
                        'ub.PROPIA_CLIENTE',
                        'c.ALIAS_CLIENTE',
                        'estv.DESC_ESTACION_VACANTE AS ESTACION_VACANTE',
                        'V.ORIGEN_VACANTE',

                        //**Otros**
                        'TIMESTAMPDIFF(DAY, NOW(), v.FECHA_CIERRE) AS DIAS_CIERRE',
                        'u.NOMBRE_COMPLETO AS USUARIO_CREADOR',
                        //+Candidatos
                        //+Seguimiento
                    ])
                ->from('tbl_vacantes as v')
                ->join('left join', 'tbl_usuarios u',
                        'v.FK_USUARIO=u.PK_USUARIO')
                ->join('left join', 'tbl_perfil_empleados p',
                        'u.FK_EMPLEADO= p.FK_EMPLEADO')
                ->join('left join', 'tbl_cat_ubicaciones ub',
                        'v.FK_UBICACION= ub.PK_UBICACION')
                ->join('left join', 'tbl_cat_ubicaciones ubc',
                        'v.FK_UBICACION_CLIENTE= ubc.PK_UBICACION')
                ->join('left join', 'tbl_cat_responsables_rh rh',
                        'v.FK_RESPONSABLE_RH= rh.PK_RESPONSABLE_RH')
                ->join('left join', 'tbl_cat_nivel n',
                        'v.FK_NIVEL= n.PK_NIVEL')
                ->join('left join', 'tbl_cat_estatus_vacante ev',
                        'v.FK_ESTATUS_VACANTE= ev.PK_ESTATUS_VACANTE')
                ->join('left join', 'tbl_clientes c',
                        'v.FK_CLIENTE= c.PK_CLIENTE')
                ->join('left join', 'tbl_cat_prioridad pr',
                        'v.FK_PRIORIDAD= pr.PK_PRIORIDAD')
                ->join('left join', 'tbl_cat_tipo_contratos tc',
                        'v.FK_TIPO_CONTRATO= tc.PK_TIPO_CONTRATO')
                ->join('left join', 'tbl_cat_tipo_vacante tv',
                        'v.FK_TIPO_VACANTE= tv.PK_TIPO_VACANTE')
                ->join('left join', 'tbl_cat_puestos catpuesto',
                        'v.FK_PUESTO= catpuesto.PK_PUESTO')
                ->join('left join', 'tbl_cat_areas catareas',
                        'v.FK_AREA= catareas.PK_AREA')
                ->join('left join', 'tbl_cat_duracion_tipo_servicios duracioncontrato',
                        'v.FK_DURACION_CONTRATO= duracioncontrato.PK_DURACION')
                ->join('left join', 'tbl_cat_tipo_workstation workstation',
                        'v.FK_WORKSTATION= workstation.PK_WORKSTATION')
                ->join('left join', 'tbl_cat_genero  g',
                                'v.FK_GENERO = g.PK_GENERO')
                ->join('left join', 'tbl_cat_estaciones_vacante  estv',
                        'v.FK_ESTACION_VACANTE = estv.PK_ESTACION_VACANTE')
                ->where('v.FK_RESPONSABLE_RH IS NOT NULL')
                ->andWhere('v.FK_ESTATUS_VACANTE != 7')
                ->orderBy(['v.FECHA_CREACION' => SORT_DESC]);
                //->all();

                $roles = user_info()['ROLES'];
                $fk_responsable_rh = tblcatresponsablesrh::find()->where(['FK_USUARIO'=>user_info()['PK_USUARIO']])->asArray()->one()['PK_RESPONSABLE_RH'];

                  if (!empty($roles)) {
                      if ($roles[0] == 3) {
                        $dataProvider->andWhere(['=', 'v.FK_RESPONSABLE_RH', $fk_responsable_rh]);
                      } elseif ($roles[0] == 4) {
                        $dataProvider->andWhere(['=', 'v.FK_USUARIO', user_info()['PK_USUARIO']]);
                      }
                  }

                  $dataProvider = new ActiveDataProvider([
                    'query' => $dataProvider,
                    'pagination' => false
                 ]);
                 $dataProvider = $dataProvider->getModels();

                // $registros = $dataProvider->getModels();
                $registrosCont = 0;
                $candidatos= [];
                $query = "";
                $tecnologias = '';
                foreach ($dataProvider as $key => $valores) {

                  $dataProvider[$key]['DESC_VACANTE'] = '<a href="!#" id="detalleVacante" data-vacante='.$dataProvider[$key]['PK_VACANTE'].' data-toggle="modal" data-target="#vacanteDetalle">'.$dataProvider[$key]['DESC_VACANTE'].'</a>';
                  $dataProvider[$key]['HISTORICO'] = '<p><a class="historicoVacante" href="#"  data-toggle="modal" data-target="#HistorialVacante" data-vacante="'.$dataProvider[$key]['PK_VACANTE'].'">Ver detalle</a></p>';
                  $UBICACION_CLIENTE = tblcatubicaciones::find()->where(['PK_UBICACION' => $dataProvider[$key]['FK_UBICACION_CLIENTE']])->limit(1)->one();
                  $UBICACION_INTERNA = tblcatubicaciones::find()->where(['PK_UBICACION' => $dataProvider[$key]['FK_UBICACION']])->limit(1)->one();
                  $dataProvider[$key]['UBICACION_CLIENTE'] = $UBICACION_CLIENTE['DESC_UBICACION'];
                  $dataProvider[$key]['UBICACION_INTERNA'] = $UBICACION_INTERNA['DESC_UBICACION'];
                  $dataProvider[$key]['COMENTARIOS'] = '<span class="hide">'.$dataProvider[$key]['COMENTARIOS'].' </span> <a href="#!" id="comentariosVacante" data-toggle="modal" data-target="#comentariosv" data-vacante="'.$dataProvider[$key]['PK_VACANTE'].'">Ver Comentarios</a>';

                  $registrosCont++;

                  $spanFechaCierre = '';
                  if ($dataProvider[$key]['FECHA_CIERRE'] != '') {
                    $dateFechaConversacion = str_replace('/', '-', $dataProvider[$key]['FECHA_CIERRE']);
                    $spanFechaConversacion = date('Y-m-d', strtotime($dateFechaConversacion));
                    $spanFechaConversacion = str_replace('-', '', $spanFechaConversacion);
                    $dataProvider[$key]['FECHA_CIERRE'] = transform_date($dataProvider[$key]['FECHA_CIERRE'],'d/m/Y');
                  }

                  $spanFechaRegistro = '';
                  if ($dataProvider[$key]['FECHA_REGISTRO'] != '') {
                    $dateFechaConversacion = str_replace('/', '-', $dataProvider[$key]['FECHA_REGISTRO']);
                    $spanFechaConversacion = date('Y-m-d', strtotime($dateFechaConversacion));
                    $spanFechaConversacion = str_replace('-', '', $spanFechaConversacion);
                    $dataProvider[$key]['FECHA_REGISTRO'] = transform_date($dataProvider[$key]['FECHA_REGISTRO'],'d/m/Y');
                  }

                  $query= (new \yii\db\Query())
                      ->select([
                        'candi.PK_CANDIDATO',
                        'CONCAT(candi.NOMBRE," ",candi.APELLIDO_PATERNO," ",candi.APELLIDO_MATERNO) AS CANDIDATO'
                      ])
                      ->from('tbl_vacantes as v')
                      ->join('INNER JOIN', 'tbl_vacantes_candidatos vc',
                              'vc.FK_VACANTE=v.PK_VACANTE')
                      ->join('INNER JOIN', 'tbl_candidatos candi',
                              'vc.FK_CANDIDATO = candi.PK_CANDIDATO')
                      ->where(['vc.FK_VACANTE' => $dataProvider[$key]['PK_VACANTE']])
                      ->all();

                  if (empty($query)) {
                    $dataProvider[$key]['CANDIDATO'] = "Ninguno";
                    $dataProvider[$key]['SEGUIMIENTO'] = "";
                    $dataProvider[$key]['TECNOLOGIAS'] = "";
                    $dataProvider[$key]['HERRAMIENTAS'] = "";
                    $dataProvider[$key]['HABILIDADES'] = "";
                  }
                  else{
                    $cand = "";
                    $seguimiento = "";
                    // $historico = "";
                    $genero = "";
                    foreach ($query as $llave => $value) {
                      $cand .= '<p><a data-toggle="modal" data-target="#candidatos-detalle" class="invocarDetalle" style="cursor: pointer;" href="#!"> <input type = "hidden" value = "'.$query[$llave]['PK_CANDIDATO'].'">'.$query[$llave]['CANDIDATO'].'</a></p>';
                      $seguimiento .= '<p><a href="'.Url::to(['candidatos/view3?PK_CANDIDATO='.$query[$llave]['PK_CANDIDATO'].'&PK_VACANTE='.$dataProvider[$key]['PK_VACANTE']]).'">Ver detalle</a></p>';
                      // $historico .= '<p><a class="historicoVacante" href="#"  data-vacante="'.$dataProvider[$key]['PK_VACANTE'].'">Ver detalle</a></p>';
                      // $genero .= '<p>'.$query[$llave]['DESC_GENERO'].'</p>';
                    }

                    $dataProvider[$key]['CANDIDATO'] = $cand;
                    $dataProvider[$key]['SEGUIMIENTO'] = $seguimiento;
                    // $dataProvider[$key]['HISTORICO'] = $historico;
                    // $dataProvider[$key]['DESC_GENERO'] = $genero;
                  }


                  /*Agregar tecnologías al dataProvider*/
                  $tecnologias = (new \yii\db\Query())
                    ->select([
                      'catt.DESC_TECNOLOGIA'
                    ])
                    ->from('tbl_vacantes as v')
                    ->join('INNER JOIN', 'tbl_vacantes_tecnologias vt',
                            'v.PK_VACANTE=vt.FK_VACANTE')
                    ->join('INNER JOIN', 'tbl_cat_tecnologias catt',
                            'vt.FK_TECNOLOGIA = catt.PK_TECNOLOGIA')
                    ->where(['vt.FK_VACANTE' => $dataProvider[$key]['PK_VACANTE']])
                    ->all();

                    $CandidatoTecnologias = "";
                    if (empty($tecnologias) ) {
                      $CandidatoTecnologias = "<span class='hide'> No hay registros </span>";
                    }
                    else {
                      $CandidatoTecnologias = "<span class='hide'>";
                      foreach ($tecnologias as $tkey => $tvalue) {
                        $CandidatoTecnologias .= $tecnologias[$tkey]['DESC_TECNOLOGIA'].', <br>';
                      }
                      $CandidatoTecnologias .= "</span>";
                    }

                  $dataProvider[$key]['TECNOLOGIAS'] = "".$CandidatoTecnologias." <a id='datosVacanteTH' href='#!' data-toggle='modal' data-target='#datosth' data-vacante=".$dataProvider[$key]['PK_VACANTE']." data-value='1'>Ver Tecnologías</a>";

                  /*FIN Agregar tecnologías al dataProvider*/

                  /*Agregar herramientas al dataProvider*/
                  $herramientas = (new \yii\db\Query())
                    ->select([
                      'cath.DESC_HERRAMIENTA'
                    ])
                    ->from('tbl_vacantes as v')
                    ->join('INNER JOIN', 'tbl_vacantes_herramientas vh',
                            'v.PK_VACANTE=vh.FK_VACANTE')
                    ->join('INNER JOIN', 'tbl_cat_herramientas cath',
                            'vh.FK_HERRAMIENTA = cath.PK_HERRAMIENTA')
                    ->where(['Vh.FK_VACANTE' => $dataProvider[$key]['PK_VACANTE']])
                    ->all();

                    $CandidatoHerramientas = "";
                    if (empty($herramientas) ) {
                      $CandidatoHerramientas = "<span class='hide'> No hay registros </span>";
                    }
                    else {
                      $CandidatoHerramientas = "<span class='hide'>";
                      foreach ($herramientas as $hkey => $hvalue) {
                        $CandidatoHerramientas .= $herramientas[$hkey]['DESC_HERRAMIENTA'].', <br>';
                      }
                      $CandidatoHerramientas .= "</span>";
                    }

                    $dataProvider[$key]['HERRAMIENTAS'] = "".$CandidatoHerramientas." <a id='datosVacanteTH' href='#!' data-toggle='modal' data-target='#datosth' data-value='2' data-vacante=".$dataProvider[$key]['PK_VACANTE'].">Ver Herramientas</a>";


                  /*FIN Agregar herramientas al dataProvider*/

                  /*Agregar herramientas al dataProvider*/
                  $habilidades = (new \yii\db\Query())
                    ->select([
                      'catha.DESC_HABILIDAD'
                    ])
                    ->from('tbl_vacantes as v')
                    ->join('INNER JOIN', 'tbl_vacantes_habilidades vha',
                            'v.PK_VACANTE=vha.FK_VACANTE')
                    ->join('INNER JOIN', 'tbl_cat_habilidades catha',
                            'vha.FK_HABILIDAD = catha.PK_HABILIDAD')
                    ->where(['vha.FK_VACANTE' => $dataProvider[$key]['PK_VACANTE']])
                    ->all();

                    $CandidatoHabilidades = "";
                    if (empty($habilidades) ) {
                      $CandidatoHabilidades = "<span class='hide'> No hay registros </span>";
                    }
                    else {
                      $CandidatoHabilidades = "<span class='hide'>";
                      foreach ($habilidades as $hkey => $hvalue) {
                        $CandidatoHabilidades .= $habilidades[$hkey]['DESC_HABILIDAD'].', <br>';
                      }
                      $CandidatoHabilidades .= "</span>";
                    }

                    $dataProvider[$key]['HABILIDADES'] = "".$CandidatoHabilidades." <a id='datosVacantePH' href='#!' data-toggle='modal' data-target='#datosph' data-value='3' data-vacante=".$dataProvider[$key]['PK_VACANTE'].">Ver Habilidades</a>";

                  /*FIN Agregar herramientas al dataProvider*/

                }


                $posiciones = [];
                $valorFront = [];
                $valorBD = [];
                $mensaje = "";

                //Función ajax para obtener las columnas de la plantilla seleccionada en el combo plantillas en consulta general vacantes,
                //y asi validar con el arreglo devuelto que columnas deben o no mostrarse en la vista.
                if (Yii::$app->request->isAjax) {

                    $data = Yii::$app->request->post();
                    $mensaje = $mensaje."entra if ajax ";

                    $idPlantillaSel = isset($data['idPlantillaSel']) ? $data['idPlantillaSel'] : 1;

                    $connection = \Yii::$app->db;
                    $columnasPlantilla = $connection->createCommand("
                        SELECT c.FK_CAT_PLANTILLAS_VACANTES AS id, c.MOSTRAR_COLUMNA, c.SECUENCIA_DESTINO, c.NOMBRE_COLUMNA, c.LABEL_COLUMNA
                        FROM tbl_config_plantillas_vacantes AS c
                        WHERE c.FK_CAT_PLANTILLAS_VACANTES = ".$idPlantillaSel."
                        ORDER BY c.SECUENCIA_DESTINO ASC
                    ")->queryAll();

                    //$out['results'] = array_values($registros);
                    $cantColumnas = count($columnasPlantilla);
                    $mensaje = $columnasPlantilla;
                    foreach ($columnasPlantilla as $colPlantilla) {
                        array_push($valorFront, $colPlantilla['NOMBRE_COLUMNA']);
                        array_push($valorBD, $colPlantilla['LABEL_COLUMNA']);
                    }

                    $posiciones = array(
                        $valorFront,
                        $valorBD
                    );

                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    $connection->close();
                    $res = array(
                        'mensaje'=>$mensaje,
                        'columnasPlantilla' => $columnasPlantilla,
                        'posiciones'=>$posiciones,
                        'dataProvider'=>$dataProvider,
                        'tecnologias' => $tecnologias,
                        'registros' => $registrosCont,
                        'query' => $query,
                        'candidatos' => $candidatos
                    );
                    return $res;
                }

                $connection = \Yii::$app->db;
                $columnasPlantilla = $connection->createCommand("
                    SELECT c.FK_CAT_PLANTILLAS_VACANTES AS id, c.MOSTRAR_COLUMNA, c.SECUENCIA_DESTINO, c.NOMBRE_COLUMNA, c.LABEL_COLUMNA
                    FROM tbl_config_plantillas_vacantes AS c
                    WHERE c.FK_CAT_PLANTILLAS_VACANTES = 1
                    ORDER BY c.SECUENCIA_DESTINO ASC
                ")->queryAll();

                //$out['results'] = array_values($registros);
                $cantColumnas = count($columnasPlantilla);
                $mensaje = $columnasPlantilla;
                foreach ($columnasPlantilla as $colPlantilla) {
                    array_push($valorFront, $colPlantilla['LABEL_COLUMNA']);
                    array_push($valorBD, $colPlantilla['NOMBRE_COLUMNA']);
                }
                $posiciones = array(
                    $valorFront,
                    $valorBD
                );
                $connection->close();

                $dummyVacante = new tblvacantes;
                $dummyCandidatos = new tblcandidatos;
                return $this->render('index10', [
                    'mensaje'=>$mensaje,
                    'dummyVacante' => $dummyVacante,
                    'dummyCandidatos' => $dummyCandidatos,
                    'posiciones'=>$posiciones,
                    'dataProvider'=>$dataProvider,
                    'candidatos' => $fk_candidato,
                    'prospectos' => $FK_PROSPECTO,
                    'tecnologias' => $tecnologias,
                    'registros' => $registrosCont,
                    'query' => $query,
                    'candidatos' => $candidatos
                ]);
            }
  }

  /*Función para checar la asociación de prospecto a vacante*/
  public function actionCandidatos_vacantes()
  {
    if (Yii::$app->request->isAjax) {
      $data       = Yii::$app->request->post();
      $vacantes   = $data['arrVacCan']['vacantes'];
      $leyenda    = '';
      $aceptado   = 'Será asociado a la vacante';

      $respuesta  = array(
        "vacantes" => ''
      );

      foreach ($vacantes as $key2 => $value){

        $modelVacante = TblVacantes::find()
                        ->select('DESC_VACANTE')
                        ->where(['=', 'PK_VACANTE', $value])
                        ->one();
        $respuesta['vacantes'][$modelVacante['DESC_VACANTE']] = array();

        if (!empty($data['arrVacCan']['candidatos'])) {

          $candidatos = $data['arrVacCan']['candidatos'];

          foreach ($candidatos as $key => $candidato)   {
            $respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key] = array();

            $modelVacantesCandidato = TblVacantesCandidatos::find()
                                      ->where(['=', 'FK_VACANTE', $value])
                                      ->andWhere(['=', 'FK_CANDIDATO', $candidato])
                                      ->one();

            $modelCandidato         = TblCandidatos::find()
                                      ->select(['NOMBRE', 'APELLIDO_MATERNO', 'APELLIDO_PATERNO'])
                                      ->where(['=', 'PK_CANDIDATO', $candidato])
                                      ->one();

            array_push($respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key], $modelCandidato);

            if(count($modelVacantesCandidato) > 0){
              array_push($respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key], 1);

              $estatusCandidato = $modelVacantesCandidato['FK_ESTATUS_ACTUAL_CANDIDATO'];

              $leyenda = ($estatusCandidato == 4 || $estatusCandidato == 5) ? 'No será asociado, ya fue cancelado' : 'Ya esta asociado a esta vacante';

              array_push($respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key], $leyenda);
            }
            else{
              array_push($respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key], 0);
              array_push($respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key], $aceptado);
            }
          }
        }

        if (!empty($data['arrVacCan']['prospectos'])) {

          $prospectos = $data['arrVacCan']['prospectos'];

          foreach ($prospectos as $key => $prospecto)   {
            $respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key] = array();

            $modelCandidato            = TblCandidatos::find()
                                      ->where(['=', 'FK_PROSPECTO', $prospecto])
                                      ->one();

            if (!empty($PkCandidato)) {

              $modelVacantesCandidato = TblVacantesCandidatos::find()
                                        ->where(['=', 'FK_VACANTE', $value])
                                        ->andWhere(['=', 'FK_CANDIDATO', $PkCandidato['PK_CANDIDATO']])
                                        ->one();
            }
            else{
              $modelCandidato         = TblProspectos::find()
                                        ->select(['NOMBRE', 'APELLIDO_MATERNO', 'APELLIDO_PATERNO'])
                                        ->where(['=', 'PK_PROSPECTO', $prospecto])
                                        ->one();
            }

            array_push($respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key], $modelCandidato);

            if(!empty($modelVacantesCandidato) > 0){
              array_push($respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key], 1);

              $estatusCandidato = $modelVacantesCandidato['FK_ESTATUS_ACTUAL_CANDIDATO'];

              $leyenda = ($estatusCandidato == 4 || $estatusCandidato == 5) ? 'No será asociado, ya fue cancelado' : 'Ya esta asociado a esta vacante';

              array_push($respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key], $leyenda);
            }
            else{
              array_push($respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key], 0);
              array_push($respuesta['vacantes'][$modelVacante['DESC_VACANTE']][$key], $aceptado);
            }
          }
        }
      }

      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return $respuesta;
    }
  }

//Funcion ajax para verificar el vinculo de
//un candidato/prospecto con una vacante
  public function actionVerifica_vacante(){

    if(Yii::$app->request->isAjax){

        $mensajes = "";
        $modelCandidatosVacantes = '';

        $post = Yii::$app->request->post();

        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();
            //$mensajes=$data;
            /*Obtener vacantes seleccionadas*/
            $vacantes[] = $data['idVacantes'];

            if (!empty($data['idRecurso'])) {

                $candidatos = $data['idRecurso'];

                    foreach ($vacantes as $key2 => $value){
                        foreach ($candidatos as $key => $candidato){

                        //Validación de candidatos asignados a vacantes
                        $modelCandidatosVacantes = TblVacantesCandidatos::find()->where(['FK_VACANTE' => $vacantes[$key2]])->andWhere(['FK_CANDIDATO' => $candidatos[$key]])->all();
                            if(count($modelCandidatosVacantes) > 0){

                                $mensajes = "Este candidato ya tuvo un proceso cancelado con esta vacante";
                            }else{
                                $mensajes = 0;
                                $post = 0;
                            }
                        }
                    }

                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return [
                        'mensajes'=>$mensajes,
                        'post'=>$post
                    ];
            }else{

                $prospectos = $data['idProspecto'];

                foreach ($prospectos as $key => $values) {

                    $modelCandidato = TblCandidatos::find()->where(['FK_PROSPECTO' => $values])->one();
                    $candidato = count($modelCandidato);

                    if($candidato == 1){

                        foreach ($vacantes as $key2 => $values2) {
                            $modelVacantesCandidato = TblVacantesCandidatos::find()->where(['FK_VACANTE' => $vacantes[$key2]])->andWhere(['FK_CANDIDATO' => $modelCandidato['PK_CANDIDATO']])->all();

                            if(count($modelVacantesCandidato) > 0){

                                $mensajes = "Este candidato ya tuvo un proceso cancelado con esta vacante";
                            }else{
                                $mensajes = 0;
                            }
                        }
                    }
                }
            }

        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'mensajes'=>$mensajes
        ];
    } else{
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'mensajes'=>$mensajes,
            'post'=>$post
        ];
    }
  }

  public function actionVerifica_vacante2(){

    if(Yii::$app->request->isAjax){

        $mensajes = "";
        $modelCandidatosVacantes = '';

        $post = Yii::$app->request->post();

        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();

            /*Obtener candidato seleccionado*/
            $vacantes = $data['idVacantes'];

            if (!empty($data['idRecurso'])) {

                $candidatos[] = $data['idRecurso'];

                foreach ($candidatos as $key => $candidato){

                    //Validación de candidatos asignados a vacantes
                    $modelCandidatosVacantes = TblVacantesCandidatos::find()->where(['FK_VACANTE' => $vacantes])->andWhere(['FK_CANDIDATO' => $candidatos[$key]])->all();

                    if(count($modelCandidatosVacantes) > 0){

                        $mensajes = "Este candidato ya tuvo un proceso cancelado con esta vacante";
                    }else{
                        $mensajes = 0;
                    }
                }

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                    'mensajes'=>$mensajes,
                    'post'=>$post
                ];
            }
        }
    } else{
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'mensajes'=>$mensajes,
            'post'=>$post
        ];
    }
  }

  public function actionHistorial_vacante(){
      if (Yii::$app->request->isAjax) { // Si la peticion viene de ajax
          $data = Yii::$app->request->post();
          $dataProvider = (new \yii\db\Query())
          ->select([
            "v.DESC_VACANTE",
             "rh.NOMBRE_RESPONSABLE_RH",
             "bitV.CANT_PERSONAS",
             "prioridad.DESC_PRIORIDAD",
             "puesto.DESC_PUESTO",
             "nivel.DESC_NIVEL",
             "bitV.FUNCIONES",
             "tipoCon.DESC_TIPO_CONTRATO",
             "duracion.DESC_DURACION",
             "bitV.NOMBRE_PROYECTO",
             "station.DESC_WORKSTATION",
             "Tipovacante.DESC_TIPO_VACANTE",
             "cliente.NOMBRE_CLIENTE",
             "ubicacion.DESC_UBICACION"
           ])
          ->from('tbl_bit_vacantes AS bitV')
          ->join('left join','tbl_vacantes AS v',
          'bitV.FK_VACANTE = v.PK_VACANTE')
          ->join('left join','tbl_cat_responsables_rh AS rh',
          'bitV.FK_RESPONSABLE_RH = rh.PK_RESPONSABLE_RH')
          ->join('left join','tbl_cat_prioridad AS prioridad',
          'bitV.FK_PRIORIDAD = prioridad.PK_PRIORIDAD')
          ->join('left join','tbl_cat_puestos AS puesto',
          'bitV.FK_PUESTO = puesto.PK_PUESTO')
          ->join('left join','tbl_cat_nivel AS nivel',
          'bitV.FK_NIVEL = nivel.PK_NIVEL')
          ->join('left join','tbl_cat_tipo_contratos AS tipoCon',
          'bitV.FK_TIPO_CONTRATO = tipoCon.PK_TIPO_CONTRATO')
          ->join('left join','tbl_cat_duracion_tipo_servicios AS duracion',
          'bitV.FK_DURACION_CONTRATO = duracion.PK_DURACION')
          ->join('left join','tbl_cat_tipo_workstation AS station',
          'bitV.FK_WORKSTATION = station.PK_WORKSTATION')
          ->join('left join','tbl_cat_tipo_vacante AS Tipovacante',
          'bitV.FK_TIPO_VACANTE = Tipovacante.PK_TIPO_VACANTE')
          ->join('left join','tbl_clientes AS cliente',
          'bitV.FK_CLIENTE = cliente.PK_CLIENTE')
          ->join('left join','tbl_cat_ubicaciones AS ubicacion',
          'bitV.FK_UBICACION_CLIENTE = ubicacion.PK_UBICACION')
          ->andWhere(['=', 'bitV.FK_VACANTE', $data['FK_VACANTE']])
          ->all();

          \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
          return [
              'dataProvider' => $dataProvider
          ];
      }
  }




  //  CONSULTA INFORMACION BAJA RECURSOS
//   public function actionBaja_recurso(){
//       if (Yii::$app->request->isAjax) { // Si la peticion viene de ajax
//           $data = Yii::$app->request->post();
//           $dataProvider = (new \yii\db\Query())
//           ->select([
//             /**** Información General INGRESA LOS DATOS QUE USARAS DE LAS TABLAS***/
//                     'bitP.TACTO_CLIENTE,'
//                       'bitP.DESEMPENIO_CLIENTE,'
//                       'tblpp.FECHA_REGISTRO,'
//                         'f.DESCRIPCION,'
//                         'tl.DESC_RANK_TECNICO,'
//                         'comEmp.COMENTARIOS,'
//                         'cat.DESC_CATEGORIA,'
//                         'subcat.DESC_SUBCATEGORIA'
//             ])
// /**** CREA LOS JOIN QDE LOS CAMPOS/TABLAS QUE USARAS***/
//
// ->FROM ('tbl_prospectos bitP')
// ->join ('left join','tbl_prospectos_perfiles  AS tblpp','bitP.PK_PROSPECTO = tblpp.FK_PROSPECTO')
// ->join ('left join','tbl_cat_perfiles AS tblcp','tblpp.FK_PERFIL = tblcp.PK_PERFIL')
// ->join ('left join','tbl_cat_rank_tecnico AS crt','tblpp.NIVEL_EXPERIENCIA=tl.PK_RANK_TECNICO')
// ->join ('left join','tbl_perfil_empleados AS perEmp','bitP.PK_PROSPECTO=perEmp.FK_PROSPECTO')
// ->join('left join','tbl_bit_comentarios_empleados AS comEmp','comEmp.FK_EMPLEADO = perEmp.FK_EMPLEADO')
// ->join('left join','tbl_cat_categoria AS cat','comEmp.MOTIVO_CAT = cat.PK_CATEGORIA')
// ->join('left join','tbl_cat_subcategoria AS subcat','comEmp.MOTIVO_CAT = subcat.PK_SUBCATEGORIA')
// ->andWhere(['=', 'bitP.PK_PROSPECTO', $data['FK_PROSPECTO']])//ATENCION AQUÍ
//
//       /////->where(['NOT', ['FK_ESTATUS' => 6]])
//             ->all();
//
//
//       \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//       return [
//           'dataProvider' => $dataProvider
//       ];
//       }

protected function CVs($PkCandidato)
{
  /*Curriculums*/
  $candidatosCVs = (new \yii\db\Query())
    ->select([
      'CatTipoCV.DESC_CV',
      'CDocs.RUTA_CV'
    ])
    ->from('tbl_candidatos as c')
    ->join('INNER JOIN', 'tbl_candidatos_documentos CDocs',
            'c.PK_CANDIDATO = CDocs.FK_CANDIDATO')
    ->join('INNER JOIN', 'tbl_cat_tipo_cv CatTipoCV',
            'CDocs.FK_TIPO_CV = CatTipoCV.PK_TIPO_CV')
    ->where(['CDocs.FK_CANDIDATO' => $PkCandidato])
    ->all();

    $CVsCandidato = "";
    // pathinfo('/www/htdocs/inc/lib.inc.php');
    if (empty($candidatosCVs) ) {
      $CVsCandidato = "";
    }
    else {
      $CVsCandidato = "<p>";
      foreach ($candidatosCVs as $keycvs => $valuecvs) {
        $pathInfo = pathinfo($candidatosCVs[$keycvs]['RUTA_CV']);
        $lenght = strlen($pathInfo['filename']);
        $nombreCVP = substr($pathInfo['filename'], 0, -11);;
        $CVsCandidato .= '<a href="'.$candidatosCVs[$keycvs]['RUTA_CV'].'" download>'.$nombreCVP.'.'.$pathInfo['extension'].'</a><br>';
      }
      $CVsCandidato .= "</p>";
    }
  return $CVsCandidato;
}

  /****CONSULTAS A PROSPECTOS****/
  public function actionHistorial_prospecto(){
      if (Yii::$app->request->isAjax) { // Si la peticion viene de ajax
          $data = Yii::$app->request->post();
          $dataProvider = (new \yii\db\Query())
          ->select([
                /**** Información General ***/
  'bitP.FK_PROSPECTO',
  'CONCAT(p.NOMBRE," ",p.APELLIDO_PATERNO," ",p.APELLIDO_MATERNO) AS NOMBRE',
  'bitP.EMAIL',
  'bitP.CELULAR',
  'bitP.TELEFONO',
  'cestatusp.DESC_ESTATUS_PROSPECTO',
  'bitP.PERFIL',
  'bitP.FECHA_CONVERSACION',
  'cep.DESC_ESTADO_PROSPECTO',
  'bitP.RECLUTADOR',
  'bitP.EXPECTATIVA',
  'bitP.DISPONIBILIDAD_INTEGRACION',
  'bitP.DISPONIBILIDAD_ENTREVISTA',
  'bitP.TRABAJA_ACTUALMENTE',
  'bitP.CANAL',
  'bitP.SUELDO_ACTUAL',
  'bitP.COMENTARIOS'
  ])

        ->from('tbl_bit_prospectos as bitP')
        ->join('left join', 'tbl_prospectos AS p',
                'bitP.FK_PROSPECTO = p.PK_PROSPECTO')
        ->join('left join', 'tbl_cat_estado_prospecto AS cep',
                'bitP.FK_ESTADO = cep.PK_ESTADO_PROSPECTO')
        ->join('left join', 'tbl_cat_estatus_prospecto AS cestatusp',
                'bitP.FK_ESTATUS = cestatusp.PK_ESTATUS_PROSPECTO')
                ->andWhere(['=', 'bitP.FK_PROSPECTO', $data['PK_PROSPECTO']])


  /////->where(['NOT', ['FK_ESTATUS' => 6]])
        ->all();


  \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
  return [
      'dataProvider' => $dataProvider
  ];
  }
  }

     /**
     * Displays a single tblcandidatos model.
     * @param integer $id
     * @return mixed
     */

    public function actionComentarios_cancelacion()
    {
        if (Yii::$app->request->isAjax) { // Si la peticion viene de ajax
            $data = Yii::$app->request->post();
            $modelComentariosAsignaciones = tblbitcomentarioscandidato::find()->where(['FK_VACANTE' => $data['FK_VACANTE'], 'FK_CANDIDATO' => $data['FK_CANDIDATO'], 'FK_ESTATUS_CANDIDATO' => '4' ])->limit(1)->one();
            echo 'Comentarios: '. $modelComentariosAsignaciones['COMENTARIOS'];
        }
    }

    public function actionDetalle_candidato(){
        if (Yii::$app->request->isAjax) { // Si la peticion viene de ajax
            $data = Yii::$app->request->post();
            $model= $this->findModel($data['FK_CANDIDATO']);
            $modelGenero = tblcatgenero::find()->where(['PK_GENERO' => $model->FK_GENERO])->limit(1)->one();
            $model->FK_GENERO = $modelGenero->DESC_GENERO;
            $model->EXPECTATIVA = '$ '.number_format($model->EXPECTATIVA, 0, '.', ',');

            if ($model->FECHA_NAC_CAN != '') {
              $dateFechaNacimiento = str_replace('/', '-', $model->FECHA_NAC_CAN);
              $spanFechaNacimiento = date('Y-m-d', strtotime($dateFechaNacimiento));
              $spanFechaNacimiento = str_replace('-', '', $spanFechaNacimiento);
              $model->FECHA_NAC_CAN = transform_date($model->FECHA_NAC_CAN,'d/m/Y');
            }

            if ($model->FECHA_REGISTRO != '') {
              $dateFechaNacimiento = str_replace('/', '-', $model->FECHA_REGISTRO);
              $spanFechaNacimiento = date('Y-m-d', strtotime($dateFechaNacimiento));
              $spanFechaNacimiento = str_replace('-', '', $spanFechaNacimiento);
              $model->FECHA_REGISTRO = transform_date($model->FECHA_REGISTRO,'d/m/Y');
            }

            $tecnologias = (new \yii\db\Query())
              ->select([
                'CatTec.DESC_TECNOLOGIA',
                'crt.DESC_RANK_TECNICO',
                'CanT.TIEMPO_USO'
              ])
              ->from('tbl_candidatos as c')
              ->join('INNER JOIN', 'tbl_candidatos_tecnologias CanT',
                      'c.PK_CANDIDATO = CanT.FK_CANDIDATO')
              ->join('INNER JOIN', 'tbl_cat_tecnologias CatTec',
                      'CanT.FK_TECNOLOGIA = CatTec.PK_TECNOLOGIA')
              ->join ('left join','tbl_cat_rank_tecnico AS crt',
              'CanT.NIVEL_EXPERIENCIA=crt.PK_RANK_TECNICO')
              ->andWhere(['=', 'CanT.FK_CANDIDATO', $data['FK_CANDIDATO']])
              ->all();

              $herramientas = (new \yii\db\Query())
                ->select([
                  'CatHer.DESC_HERRAMIENTA',
                  'crt.DESC_RANK_TECNICO',
                  'CanH.TIEMPO_USO'
                ])
                ->from('tbl_candidatos as c')
                ->join('INNER JOIN', 'tbl_candidatos_herramientas CanH',
                        'c.PK_CANDIDATO = CanH.FK_CANDIDATO')
                ->join('INNER JOIN', 'tbl_cat_herramientas CatHer',
                        'CanH.FK_HERRAMIENTA = CatHer.PK_HERRAMIENTA')
                ->join ('left join','tbl_cat_rank_tecnico AS crt',
                'CanH.NIVEL_EXPERIENCIA = crt.PK_RANK_TECNICO')
                ->andWhere(['=', 'CanH.FK_CANDIDATO', $data['FK_CANDIDATO']])
                ->all();

                $habilidades = (new \yii\db\Query())
                  ->select([
                    'CatHab.DESC_HABILIDAD'
                  ])
                  ->from('tbl_candidatos as c')
                  ->join('INNER JOIN', 'tbl_candidatos_habilidades CanH',
                          'c.PK_CANDIDATO = CanH.FK_CANDIDATO')
                  ->join('INNER JOIN', 'tbl_cat_habilidades CatHab',
                          'CanH.FK_HABILIDAD = CatHab.PK_HABILIDAD')
                  ->andWhere(['=', 'CanH.FK_CANDIDATO', $data['FK_CANDIDATO']])
                  ->all();

            $model['CV'] = $this->CVs($data['FK_CANDIDATO']);;

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'model' => $model,
                'tecnologias'=> $tecnologias,
                'herramientas'=> $herramientas,
                'habilidades'=> $habilidades
            ];
        }
    }

    public function actionView($PK_CANDIDATO,$PK_VACANTE)
    {
        $model= $this->findModel($PK_CANDIDATO);
        $modelVacante = tblvacantes::findOne($PK_VACANTE);
        $modelGenero = TblCatGenero::find()->where(['PK_GENERO' => $model->FK_GENERO])->limit(1)->one();
        $modelTecnologias= (new \yii\db\Query())
                            ->select(['ct.PK_TECNOLOGIA','ct.DESC_TECNOLOGIA'])
                            ->from('tbl_cat_tecnologias as ct')
                            ->join('INNER JOIN','tbl_candidatos_tecnologias as vt',
                                'ct.PK_TECNOLOGIA = vt.FK_TECNOLOGIA')
                            ->where(['vt.FK_VACANTE'=>$PK_VACANTE, 'vt.FK_CANDIDATO'=>$PK_CANDIDATO])
                            ->orderBy('ct.DESC_TECNOLOGIA ASC')
                            ->all();
        //tbl_cat_herramientas
        //tbl_vacantes_herramientas
        $modelHerramientas= (new \yii\db\Query())
                            ->select(['ct.PK_HERRAMIENTA','ct.DESC_HERRAMIENTA'])
                            ->from('tbl_cat_herramientas as ct')
                            ->join('INNER JOIN','tbl_candidatos_herramientas as vt',
                                'ct.PK_HERRAMIENTA = vt.FK_HERRAMIENTA')
                            ->where(['vt.FK_VACANTE'=>$PK_VACANTE, 'vt.FK_CANDIDATO'=>$PK_CANDIDATO])
                            ->orderBy('ct.DESC_HERRAMIENTA ASC')
                            ->all();
    //tbl_cat_habilidades
        //tbl_vacantes_habilidades
        $modelHabilidades= (new \yii\db\Query())
                            ->select(['ct.PK_HABILIDAD','ct.DESC_HABILIDAD'])
                            ->from('tbl_cat_habilidades as ct')
                            ->join('INNER JOIN','tbl_candidatos_habilidades as vt',
                                'ct.PK_HABILIDAD = vt.FK_HABILIDAD')
                            ->where(['vt.FK_VACANTE'=>$PK_VACANTE, 'vt.FK_CANDIDATO'=>$PK_CANDIDATO])
                            ->orderBy('ct.DESC_HABILIDAD ASC')
                            ->all();

         $model->FK_GENERO = $modelGenero->DESC_GENERO;
        return $this->render('view', [
            'model' => $model,
            'modelVacante' => $modelVacante,
            'modelTecnologias' => $modelTecnologias,
            'modelHerramientas' => $modelHerramientas,
            'modelHabilidades' => $modelHabilidades,
        ]);
    }
    /**
     * Displays a single tblcandidatos model.
     * @param integer $id
     * @return mixed
     */
    public function actionView3($PK_CANDIDATO,$PK_VACANTE)
    {
        $data = Yii::$app->request->post();

        if(isset($data['PREV'])){
            $PREV = $data['PREV'];
        }else{
            $PREV = '..candidatos/index2';
        }
        $model= $this->findModel($PK_CANDIDATO);
        $modelVacante = tblvacantes::findOne($PK_VACANTE);
        $modelCandidato = TblCandidatos::findOne($PK_CANDIDATO);
        //var_dump($model);
        //dd($modelVacante);
        $modelVacanteCandidato = tblvacantescandidatos::find()->where(['FK_CANDIDATO' => $PK_CANDIDATO,'FK_VACANTE' => $PK_VACANTE])->limit(1)->one();
        $modelProspectos = new tblProspectos();
        $modelBitacora = new tblbitcomentarioscandidato();
        $modelSubirArchivo = new SubirArchivo();
        $modelSubirArchivo->extensions = 'doc, docx, pdf';
        $modelSubirArchivo->noRequired = true;

        //var_dump(Yii::$app->request->post());

        if ($modelBitacora->load(Yii::$app->request->post())) {

                $data= Yii::$app->request->post();
                $modelSubirArchivo->file = UploadedFile::getInstance($modelSubirArchivo, '[5]file');
                if($modelSubirArchivo->file){ //Valida si se subio foto
                    $rutaGuardado = '../uploads/CandidatoDocumentos/';
                    // echo $data['tipo'];
                    $nombre = $data['tipo'].'_'.$PK_CANDIDATO;
                    $extensionCVOriginal = $modelSubirArchivo-> upload($rutaGuardado,$nombre);
                    $modelBitacora->DOCUMENTO_ASOCIADO='/uploads/CandidatoDocumentos/'.$data['tipo'].'_'.$PK_CANDIDATO.'.'.$extensionCVOriginal;
                } else {
                    $modelBitacora->DOCUMENTO_ASOCIADO='';
                }
                // var_dump($modelSubirArchivo);
                // var_dump($extensionCVOriginal);
                // var_dump(Yii::$app->request->post());
                $modelBitacora->FK_PROSPECTO = $model->FK_PROSPECTO;
                $modelBitacora->FK_USUARIO = 1;
                $modelBitacora->FECHA_REGISTRO = date('Y-m-d');
                $modelBitacora->save(false);


                $estacion = $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO;

                if($modelBitacora->FK_ESTATUS_CANDIDATO == 5){
                    $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                }else{
                    if($estacion==1){
                        $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO = 2;
                        $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 2;
                    }elseif($estacion==2){
                        $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO = 3;
                        $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 2;
                    }elseif($estacion==3){
                        $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO = 4;
                        $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 2;
                    }elseif($estacion==4){
                        $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO = 5;
                        $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 2;
                        $EstacionActualVacante=$modelVacante->FK_ESTACION_VACANTE;
                        if($EstacionActualVacante==3){
                            $modelVacante->FK_ESTACION_VACANTE = 4;
                            $modelVacante->save(false);
                        }
                    }

                }

                $modelVacanteCandidato->FECHA_ACTUALIZACION = date('Y-m-d');
                $modelVacanteCandidato->save(false);

                if($modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO == 5){

                    $query= TblVacantesCandidatos::find()
                         ->andFilterWhere(
                            ['and',
                                ['=', 'FK_VACANTE', $modelVacanteCandidato->FK_VACANTE],
                                ['=', 'FK_ESTACION_ACTUAL_CANDIDATO', 5]
                            ])
                         ->all();
                        if(count($query)==$modelVacante->CANT_PERSONAS){
                            $modelVacante->FK_ESTACION_VACANTE = 5;
                            $modelVacante->FK_ESTATUS_VACANTE = 7;
                            $modelVacante->save(false);
                        }
                        //Busqueda de las demás vacantes del candidato
                          $VacantesCandidatos = tblvacantescandidatos::find()->where(['FK_CANDIDATO' => $modelVacanteCandidato->FK_CANDIDATO])->andWhere(['<>','FK_VACANTE',$modelVacanteCandidato->FK_VACANTE])->andWhere(['<>','FK_ESTATUS_ACTUAL_CANDIDATO',5])->all();

                          foreach ($VacantesCandidatos as $keyvca => $valuevca) {
                          $modeloVacantesCandidatos = tblvacantescandidatos::find()->where(['FK_VACANTE' => $valuevca->FK_VACANTE])->andWhere(['FK_CANDIDATO' => $valuevca->FK_CANDIDATO])->one();
                          $modeloVacantesCandidatos->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                          $modeloVacantesCandidatos->FECHA_ACTUALIZACION = date('Y-m-d');
                          $modeloVacantesCandidatos->save(false);

                          $modelBitacoraVC = new tblbitcomentarioscandidato();
                          $modelBitacoraVC->FK_VACANTE = $valuevca->FK_VACANTE;
                          $modelBitacoraVC->FK_CANDIDATO = $modelVacanteCandidato->FK_CANDIDATO;
                          $modelBitacoraVC->FK_ESTACION_CANDIDATO = $valuevca->FK_ESTACION_ACTUAL_CANDIDATO;
                          $modelBitacoraVC->FK_ESTATUS_CANDIDATO = $valuevca->FK_ESTATUS_ACTUAL_CANDIDATO;
                          $modelBitacoraVC->FK_USUARIO = user_info()['PK_USUARIO'];
                          $modelBitacoraVC->COMENTARIOS = 'VACANTE CANCELADA POR ASIGNACIÓN DE CANDIDATO';
                          $modelBitacoraVC->FECHA_REGISTRO =  date('Y-m-d');
                          $modelBitacoraVC->save(false);
                          }

                          //Busqueda de los demas candidatos asociados a la vacante terminada
                          $CandidatosEnVacante = tblvacantescandidatos::find()->where(['=','FK_VACANTE',$modelVacanteCandidato->FK_VACANTE])->andWhere(['<>','FK_ESTATUS_ACTUAL_CANDIDATO',3])->andWhere(['<>','FK_ESTATUS_ACTUAL_CANDIDATO',4])->andWhere(['<>','FK_ESTATUS_ACTUAL_CANDIDATO',5])->andWhere(['<>','FK_ESTACION_ACTUAL_CANDIDATO',5])->all();
                          
                          foreach ($CandidatosEnVacante as $keycev => $valuecev) {
                          $modeloCandidatosVacante = tblvacantescandidatos::find()->where(['FK_VACANTE' => $valuecev->FK_VACANTE])->andWhere(['FK_CANDIDATO' => $valuecev->FK_CANDIDATO])->one();
                          //$datoCandidato = tblCandidatos::find()->where(['PK_CANDIDATO' => $valuecev->FK_CANDIDATO])->one();

                          $v = (int) $valuecev->FK_VACANTE;
                          $c = (int) $valuecev->FK_CANDIDATO;
 
                          $sql = "select * from tbl_candidatos tc
                                  INNER JOIN tbl_vacantes_candidatos tvc ON
                                  tvc.FK_CANDIDATO = tc.PK_CANDIDATO
                                  where tvc.FK_VACANTE = '$v' and tc.PK_CANDIDATO = '$c'";

                          $datoCandidato = tblCandidatos::findBySql($sql)->one();
                          $modelBitacoraCV = new tblbitcomentarioscandidato();

                          $modeloCandidatoOtraVacante = tblvacantescandidatos::find()->where(['<>','FK_VACANTE',$valuecev->FK_VACANTE])->andWhere(['FK_CANDIDATO' => $valuecev->FK_CANDIDATO])->andWhere(['<>','FK_ESTATUS_ACTUAL_CANDIDATO',5])->all();

                          if($modelVacante->FK_ESTATUS_VACANTE == 7 && count($modeloCandidatoOtraVacante) == 0){

                            $modeloCandidatosVacante->FK_ESTACION_ACTUAL_CANDIDATO = 5;
                            $modeloCandidatosVacante->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                            $modeloCandidatosVacante->FECHA_ACTUALIZACION = date('Y-m-d');
                            $modeloCandidatosVacante->save(false);

                            $modelBitacoraCV->FK_VACANTE = $valuecev->FK_VACANTE;
                            $modelBitacoraCV->FK_CANDIDATO = $valuecev->FK_CANDIDATO;
                            $modelBitacoraCV->FK_ESTACION_CANDIDATO = $valuecev->FK_ESTACION_ACTUAL_CANDIDATO;
                            $modelBitacoraCV->FK_ESTATUS_CANDIDATO = $valuecev->FK_ESTATUS_ACTUAL_CANDIDATO;
                            $modelBitacoraCV->FK_USUARIO = 1;
                            $modelBitacoraCV->COMENTARIOS = 'CANCELADO DEBIDO A QUE LA VACANTE SE COMPLETO';
                            $modelBitacoraCV->FECHA_REGISTRO =  date('Y-m-d');
                            $modelBitacoraCV->save(false);

                              if($datoCandidato->FK_PROSPECTO != NULL){
                                $modelProspectos = new TblProspectos;
                                  $modelProspectos->PK_PROSPECTO = $datoCandidato->FK_PROSPECTO;
                                    $modelProspectos->NOMBRE = $datoCandidato->NOMBRE;
                                    $modelProspectos->APELLIDO_PATERNO = $datoCandidato->APELLIDO_PATERNO;
                                    $modelProspectos->APELLIDO_MATERNO = $datoCandidato->APELLIDO_MATERNO;
                                    $modelProspectos->CURP = $datoCandidato->CURP;
                                    $modelProspectos->EDAD = $datoCandidato->EDAD;
                                    $modelProspectos->FK_GENERO = $datoCandidato->FK_GENERO;
                                    $modelProspectos->FECHA_NAC = $datoCandidato->FECHA_NAC_CAN;
                                    $modelProspectos->EMAIL = $datoCandidato->EMAIL;
                                    $modelProspectos->TELEFONO = $datoCandidato->TELEFONO;
                                    $modelProspectos->CELULAR = $datoCandidato->CELULAR;
                                    $modelProspectos->PERFIL = $datoCandidato->PERFIL;
                                    $modelProspectos->UNIVERSIDAD = $datoCandidato->UNIVERSIDAD;
                                    $modelProspectos->CARRERA = $datoCandidato->CARRERA;
                                    $modelProspectos->CONOCIMIENTOS_TECNICOS = $datoCandidato->CONOCIMIENTOS_TECNICOS;
                                    $modelProspectos->COMENTARIOS = 'PASÓ DE CANDIDATO A PROSPECTO';
                                    $modelProspectos->NIVEL_ESCOLARIDAD = $datoCandidato->NIVEL_ESCOLARIDAD;
                                    $modelProspectos->FECHA_REGISTRO = date('Y-m-d');
                                    $modelProspectos->RECLUTADOR = $datoCandidato->RECLUTADOR;
                                    $modelProspectos->EXPECTATIVA = $datoCandidato->EXPECTATIVA;
                                    $modelProspectos->FECHA_CONVERSACION = $datoCandidato->FECHA_CONVERSACION;
                                    $modelProspectos->LUGAR_RESIDENCIA = $datoCandidato->LUGAR_RESIDENCIA;
                                    $modelProspectos->FK_FUENTE_VACANTE = $datoCandidato->FK_FUENTE_VACANTE;
                                    $modelProspectos->DISPONIBILIDAD_INTEGRACION = $datoCandidato->DISPONIBILIDAD_INTEGRACION;
                                    $modelProspectos->DISPONIBILIDAD_ENTREVISTA = $datoCandidato->DISPONIBILIDAD_ENTREVISTA;
                                    $modelProspectos->TRABAJA_ACTUALMENTE = $datoCandidato->TRABAJA_ACTUALMENTE;
                                    $modelProspectos->FK_CANAL = $datoCandidato->FK_CANAL;
                                    $modelProspectos->SUELDO_ACTUAL = $datoCandidato->SUELDO_ACTUAL;
                                    $modelProspectos->CAPACIDAD_RECURSO = $datoCandidato->CAPACIDAD_RECURSO;
                                    $modelProspectos->TACTO_CLIENTE = $datoCandidato->TACTO_CLIENTE;
                                    $modelProspectos->DESEMPENIO_CLIENTE = $datoCandidato->DESEMPENIO_CLIENTE;
                                  $modelProspectos->FK_ESTATUS = 1;
                                  $modelProspectos->FK_ESTADO = 1;
                                  $modelProspectos->FK_USUARIO = user_info()['PK_USUARIO'];
                                  $modelProspectos->FK_ORIGEN = 3;
                                  $modelProspectos->FK_USUARIO_CHECKOUT = 0;
                                  $modelProspectos->save(false);

                              } else{
                                    $modelProspectos = new TblProspectos;
                                    $modelProspectos->NOMBRE = $datoCandidato->NOMBRE;
                                    $modelProspectos->APELLIDO_PATERNO = $datoCandidato->APELLIDO_PATERNO;
                                    $modelProspectos->APELLIDO_MATERNO = $datoCandidato->APELLIDO_MATERNO;
                                    $modelProspectos->CURP = $datoCandidato->CURP;
                                    $modelProspectos->EDAD = $datoCandidato->EDAD;
                                    $modelProspectos->FK_GENERO = $datoCandidato->FK_GENERO;
                                    $modelProspectos->FECHA_NAC = $datoCandidato->FECHA_NAC_CAN;
                                    $modelProspectos->EMAIL = $datoCandidato->EMAIL;
                                    $modelProspectos->TELEFONO = $datoCandidato->TELEFONO;
                                    $modelProspectos->CELULAR = $datoCandidato->CELULAR;
                                    $modelProspectos->PERFIL = $datoCandidato->PERFIL;
                                    $modelProspectos->UNIVERSIDAD = $datoCandidato->UNIVERSIDAD;
                                    $modelProspectos->CARRERA = $datoCandidato->CARRERA;
                                    $modelProspectos->CONOCIMIENTOS_TECNICOS = $datoCandidato->CONOCIMIENTOS_TECNICOS;
                                    $modelProspectos->COMENTARIOS = 'PASÓ DE CANDIDATO A PROSPECTO';
                                    $modelProspectos->NIVEL_ESCOLARIDAD = $datoCandidato->NIVEL_ESCOLARIDAD;
                                    $modelProspectos->FECHA_REGISTRO = date('Y-m-d');
                                    $modelProspectos->RECLUTADOR = $datoCandidato->RECLUTADOR;
                                    $modelProspectos->EXPECTATIVA = $datoCandidato->EXPECTATIVA;
                                    $modelProspectos->FECHA_CONVERSACION = $datoCandidato->FECHA_CONVERSACION;
                                    $modelProspectos->LUGAR_RESIDENCIA = $datoCandidato->LUGAR_RESIDENCIA;
                                    $modelProspectos->FK_FUENTE_VACANTE = $datoCandidato->FK_FUENTE_VACANTE;
                                    $modelProspectos->DISPONIBILIDAD_INTEGRACION = $datoCandidato->DISPONIBILIDAD_INTEGRACION;
                                    $modelProspectos->DISPONIBILIDAD_ENTREVISTA = $datoCandidato->DISPONIBILIDAD_ENTREVISTA;
                                    $modelProspectos->TRABAJA_ACTUALMENTE = $datoCandidato->TRABAJA_ACTUALMENTE;
                                    $modelProspectos->FK_CANAL = $datoCandidato->FK_CANAL;
                                    $modelProspectos->SUELDO_ACTUAL = $datoCandidato->SUELDO_ACTUAL;
                                    $modelProspectos->CAPACIDAD_RECURSO = $datoCandidato->CAPACIDAD_RECURSO;
                                    $modelProspectos->TACTO_CLIENTE = $datoCandidato->TACTO_CLIENTE;
                                    $modelProspectos->DESEMPENIO_CLIENTE = $datoCandidato->DESEMPENIO_CLIENTE;
                                    $modelProspectos->FK_ESTATUS = 1;
                                    $modelProspectos->FK_ESTADO = 1;
                                    $modelProspectos->FK_USUARIO = user_info()['PK_USUARIO'];
                                    $modelProspectos->FK_ORIGEN = 3;
                                    $modelProspectos->FK_USUARIO_CHECKOUT = 0;
                                    $modelProspectos->save(false);

                                    $datoCandidato->FK_PROSPECTO = $modelProspectos->PK_PROSPECTO;
                                    $datoCandidato->save(false);

                              }

                              /*Se agregan los curriculums cuando el candidato es cancelado*/
                              $datosTipoCV = TblCatTipoCV::find()->orderBy(['PK_TIPO_CV'=>SORT_ASC])->all();
                              $FkTipoCV = array_column($datosTipoCV, 'PK_TIPO_CV');
                              $CandidatosCV = TblCandidatosDocumentos::find()->where(['FK_CANDIDATO'=> $valuecev->FK_CANDIDATO])->all();
                              if (!empty($CandidatosCV)) {
                                foreach ($CandidatosCV as $keyCCV => $valueCCV) {
                                  $candidatoCV = substr($valueCCV['RUTA_CV'], 3, strlen($valueCCV['RUTA_CV']));
                                  $positionTipoCV = array_search($valueCCV['FK_TIPO_CV'], $FkTipoCV);
                                  $DESC_CV = $datosTipoCV[$positionTipoCV]['DESC_CV'];
                                  $infoFile = pathInfo($valueCCV['RUTA_CV']);

                                  $nombre = 'CV'.$DESC_CV.'_'.$modelProspectos->PK_PROSPECTO.'_'.date('Y-m-d').'.'.$infoFile['extension'];
                                  $rutaGuardado = '../uploads/ProspectosCV/';

                                  if (copy($candidatoCV, $rutaGuardado.''.$nombre)) {
                                    /*Se borra archivo*/
                                    unlink($candidatoCV);
                                    /*Se inserta en la tabla de prospectos el elemento copiado*/
                                    $modelProspectosDocumentos = new TblProspectosDocumentos();
                                    $modelProspectosDocumentos->FK_PROSPECTO    = $modelProspectos->PK_PROSPECTO;
                                    $modelProspectosDocumentos->FK_TIPO_CV      = $valueCCV['FK_TIPO_CV'];
                                    $modelProspectosDocumentos->RUTA_CV         = '../'.$rutaGuardado.''.$nombre;
                                    $modelProspectosDocumentos->FECHA_REGISTRO  = date('Y-m-d');
                                    $modelProspectosDocumentos->save(false);
                                    /*Se borra de la tabla de candidatos documentos*/
                                    $modelEliminar = TblCandidatosDocumentos::find()->where(['FK_CANDIDATO' => $valuecev->FK_CANDIDATO, 'FK_TIPO_CV' => $valueCCV['FK_TIPO_CV']])->one();
                                    $modelEliminar->delete();
                                  }
                                }
                              }

                              $modelBitProspecto = new TblBitProspectos;
                              $modelBitProspecto['FK_PROSPECTO'] = $modelProspectos->PK_PROSPECTO;
                              $modelBitProspecto['EMAIL'] = $modelProspectos->EMAIL;
                              $modelBitProspecto['CELULAR'] = $modelProspectos->CELULAR;
                              $modelBitProspecto['TELEFONO'] = $modelProspectos->TELEFONO;
                              $modelBitProspecto['FK_ESTATUS'] = $modelProspectos->FK_ESTATUS;
                              $modelBitProspecto['PERFIL'] = $modelProspectos->PERFIL;
                              $modelBitProspecto['FECHA_CONVERSACION'] = $modelProspectos->FECHA_CONVERSACION;
                              $modelBitProspecto['FK_ESTADO'] = $modelProspectos->FK_ESTADO;
                              $modelBitProspecto['RECLUTADOR'] = $modelProspectos->RECLUTADOR;
                              $modelBitProspecto['EXPECTATIVA'] = $modelProspectos->EXPECTATIVA;
                              $modelBitProspecto['DISPONIBILIDAD_INTEGRACION'] = $modelProspectos->DISPONIBILIDAD_INTEGRACION;
                              $modelBitProspecto['DISPONIBILIDAD_ENTREVISTA'] = $modelProspectos->DISPONIBILIDAD_ENTREVISTA;
                              $modelBitProspecto['TRABAJA_ACTUALMENTE'] = 'NO';
                              $modelBitProspecto['CANAL'] = $modelProspectos->FK_CANAL;
                              $modelBitProspecto['SUELDO_ACTUAL'] = $modelProspectos->SUELDO_ACTUAL;
                              $modelBitProspecto['COMENTARIOS'] = 'TRANSICIÓN DE CANDIDATO A PROSPECTO';
                              $modelBitProspecto['FK_USUARIO'] = user_info()['PK_USUARIO'];
                              $modelBitProspecto['FECHA_REGISTRO'] = date('Y-m-d');
                              $modelBitProspecto->save(false);

                              $datoCandidato->ESTATUS_CAND_APLIC = 0;
                              $datoCandidato->save(false);

                          }else if($modelVacante->FK_ESTATUS_VACANTE == 7 && count($CandidatosEnVacante) != 0){

                                $modeloCandidatosVacante->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                                $modeloCandidatosVacante->FECHA_ACTUALIZACION = date('Y-m-d');
                                $modeloCandidatosVacante->save(false);

                                $modelBitacoraCV->FK_VACANTE = $valuecev->FK_VACANTE;
                                $modelBitacoraCV->FK_CANDIDATO = $valuecev->FK_CANDIDATO;
                                $modelBitacoraCV->FK_ESTACION_CANDIDATO = $valuecev->FK_ESTACION_ACTUAL_CANDIDATO;
                                $modelBitacoraCV->FK_ESTATUS_CANDIDATO = $valuecev->FK_ESTATUS_ACTUAL_CANDIDATO;
                                $modelBitacoraCV->FK_USUARIO = 1;
                                $modelBitacoraCV->COMENTARIOS = 'CANCELADO DEBIDO A QUE NO FUE ELEGIDO PARA LA VACANTE';
                                $modelBitacoraCV->FECHA_REGISTRO =  date('Y-m-d');
                                $modelBitacoraCV->save(false);
                            }
                          }
                          //dd($modeloCandidatosVacante);
                        // \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        //
                        // $val = array(
                        //     'value' => $modeloVacantesCandidatos
                        // );
                        // return $val;
                }

                $VacantesCandidato = tblvacantescandidatos::find()
                ->where(['FK_CANDIDATO' => $PK_CANDIDATO])
                // ->groupBy('FK_CANDIDATO')
                ->count();
                $EstatusCandidato = tblvacantescandidatos::find()
                ->where(['FK_CANDIDATO' => $PK_CANDIDATO, 'FK_ESTATUS_ACTUAL_CANDIDATO' => 5])
                // ->orWhere(['FK_CANDIDATO' => $PK_CANDIDATO, 'FK_ESTATUS_ACTUAL_CANDIDATO' => 4])
                // ->groupBy('FK_CANDIDATO')
                ->count();

                if($modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO == 4){
                    $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                    $modelVacanteCandidato->save(false);
                }
                // var_dump($VacantesCandidato);
                // var_dump($EstatusCandidato);
                //dd($modelVacanteCandidato);
                if ($VacantesCandidato == $EstatusCandidato) {
                  if($model->FK_PROSPECTO != NULL){
                      $modelProspectos->PK_PROSPECTO = $model->FK_PROSPECTO;
                      $modelProspectos->NOMBRE = $model->NOMBRE;
                      $modelProspectos->APELLIDO_PATERNO = $model->APELLIDO_PATERNO;
                      $modelProspectos->APELLIDO_MATERNO = $model->APELLIDO_MATERNO;
                      $modelProspectos->CURP = $model->CURP;
                      $modelProspectos->EDAD = $model->EDAD;
                      $modelProspectos->FK_GENERO = $model->FK_GENERO;
                      $modelProspectos->FECHA_NAC = $model->FECHA_NAC_CAN;
                      $modelProspectos->EMAIL = $model->EMAIL;
                      $modelProspectos->TELEFONO = $model->TELEFONO;
                      $modelProspectos->CELULAR = $model->CELULAR;
                      $modelProspectos->PERFIL = $model->PERFIL;
                      $modelProspectos->UNIVERSIDAD = $model->UNIVERSIDAD;
                      $modelProspectos->CARRERA = $model->CARRERA;
                      $modelProspectos->CONOCIMIENTOS_TECNICOS = $model->CONOCIMIENTOS_TECNICOS;
                      $modelProspectos->COMENTARIOS = 'PASÓ DE CANDIDATO A PROSPECTO';
                      $modelProspectos->NIVEL_ESCOLARIDAD = $model->NIVEL_ESCOLARIDAD;
                      $modelProspectos->FECHA_REGISTRO = date('Y-m-d');
                      $modelProspectos->RECLUTADOR = $model->RECLUTADOR;
                      $modelProspectos->EXPECTATIVA = $model->EXPECTATIVA;
                      $modelProspectos->FECHA_CONVERSACION = $model->FECHA_CONVERSACION;
                      $modelProspectos->LUGAR_RESIDENCIA = $model->LUGAR_RESIDENCIA;
                      $modelProspectos->FK_FUENTE_VACANTE = $model->FK_FUENTE_VACANTE;
                      $modelProspectos->DISPONIBILIDAD_INTEGRACION = $model->DISPONIBILIDAD_INTEGRACION;
                      $modelProspectos->DISPONIBILIDAD_ENTREVISTA = $model->DISPONIBILIDAD_ENTREVISTA;
                      $modelProspectos->TRABAJA_ACTUALMENTE = $model->TRABAJA_ACTUALMENTE;
                      $modelProspectos->FK_CANAL = $model->FK_CANAL;
                      $modelProspectos->SUELDO_ACTUAL = $model->SUELDO_ACTUAL;
                      $modelProspectos->CAPACIDAD_RECURSO = $model->CAPACIDAD_RECURSO;
                      $modelProspectos->TACTO_CLIENTE = $model->TACTO_CLIENTE;
                      $modelProspectos->DESEMPENIO_CLIENTE = $model->DESEMPENIO_CLIENTE;
                      $modelProspectos->FK_ESTATUS = 1;
                      $modelProspectos->FK_ESTADO = 1;
                      $modelProspectos->FK_USUARIO = user_info()['PK_USUARIO'];
                      $modelProspectos->FK_ORIGEN = 3;
                      // $modelProspectos->TIPO_CV = '';
                      // $modelProspectos->CV = $model->CV;
                      /*$modelProspectos->FK_CANAL = 4;
                      $modelProspectos->FK_ESTADO = 1;
                      $modelProspectos->FK_ESTATUS = 1;
                      $modelProspectos->FK_ORIGEN = 3;
                      $modelProspectos->FK_USUARIO = 0;*/
                      $modelProspectos->FK_USUARIO_CHECKOUT = 0;
                      $modelProspectos->save(false);

                  } else{
                        $modelProspectos = new TblProspectos;
                        $modelProspectos->NOMBRE = $model->NOMBRE;
                        $modelProspectos->APELLIDO_PATERNO = $model->APELLIDO_PATERNO;
                        $modelProspectos->APELLIDO_MATERNO = $model->APELLIDO_MATERNO;
                        $modelProspectos->CURP = $model->CURP;
                        $modelProspectos->EDAD = $model->EDAD;
                        $modelProspectos->FK_GENERO = $model->FK_GENERO;
                        $modelProspectos->FECHA_NAC = $model->FECHA_NAC_CAN;
                        $modelProspectos->EMAIL = $model->EMAIL;
                        $modelProspectos->TELEFONO = $model->TELEFONO;
                        $modelProspectos->CELULAR = $model->CELULAR;
                        $modelProspectos->PERFIL = $model->PERFIL;
                        $modelProspectos->UNIVERSIDAD = $model->UNIVERSIDAD;
                        $modelProspectos->CARRERA = $model->CARRERA;
                        $modelProspectos->CONOCIMIENTOS_TECNICOS = $model->CONOCIMIENTOS_TECNICOS;
                        $modelProspectos->COMENTARIOS = 'PASÓ DE CANDIDATO A PROSPECTO';
                        $modelProspectos->NIVEL_ESCOLARIDAD = $model->NIVEL_ESCOLARIDAD;
                        $modelProspectos->FECHA_REGISTRO = date('Y-m-d');
                        $modelProspectos->RECLUTADOR = $model->RECLUTADOR;
                        $modelProspectos->EXPECTATIVA = $model->EXPECTATIVA;
                        $modelProspectos->FECHA_CONVERSACION = $model->FECHA_CONVERSACION;
                        $modelProspectos->LUGAR_RESIDENCIA = $model->LUGAR_RESIDENCIA;
                        $modelProspectos->FK_FUENTE_VACANTE = $model->FK_FUENTE_VACANTE;
                        $modelProspectos->DISPONIBILIDAD_INTEGRACION = $model->DISPONIBILIDAD_INTEGRACION;
                        $modelProspectos->DISPONIBILIDAD_ENTREVISTA = $model->DISPONIBILIDAD_ENTREVISTA;
                        $modelProspectos->TRABAJA_ACTUALMENTE = $model->TRABAJA_ACTUALMENTE;
                        $modelProspectos->FK_CANAL = $model->FK_CANAL;
                        $modelProspectos->SUELDO_ACTUAL = $model->SUELDO_ACTUAL;
                        $modelProspectos->CAPACIDAD_RECURSO = $model->CAPACIDAD_RECURSO;
                        $modelProspectos->TACTO_CLIENTE = $model->TACTO_CLIENTE;
                        $modelProspectos->DESEMPENIO_CLIENTE = $model->DESEMPENIO_CLIENTE;
                        $modelProspectos->FK_ESTATUS = 1;
                        $modelProspectos->FK_ESTADO = 1;
                        $modelProspectos->FK_USUARIO = user_info()['PK_USUARIO'];
                        $modelProspectos->FK_ORIGEN = 3;
                        // $modelProspectos->TIPO_CV = 'NULL';
                        // $modelProspectos->CV = $model->CV;
                        /*$modelProspectos->FK_CANAL = 4;
                        $modelProspectos->FK_ESTADO = 1;
                        $modelProspectos->FK_ESTATUS = 1;
                        $modelProspectos->FK_ORIGEN = 3;
                        $modelProspectos->FK_USUARIO = 0;*/
                        $modelProspectos->FK_USUARIO_CHECKOUT = 0;
                        $modelProspectos->save(false);

                        $model->FK_PROSPECTO = $modelProspectos->PK_PROSPECTO;
                        $model->save(false);

                  }

                  /*Se agregan los curriculums cuando el candidato es cancelado*/
                  $datosTipoCV = TblCatTipoCV::find()->orderBy(['PK_TIPO_CV'=>SORT_ASC])->all();
                  $FkTipoCV = array_column($datosTipoCV, 'PK_TIPO_CV');
                  $CandidatosCV = TblCandidatosDocumentos::find()->where(['FK_CANDIDATO'=>$PK_CANDIDATO])->all();
                  if (!empty($CandidatosCV)) {
                    foreach ($CandidatosCV as $keyCCV => $valueCCV) {
                      $candidatoCV = substr($valueCCV['RUTA_CV'], 3, strlen($valueCCV['RUTA_CV']));
                      $positionTipoCV = array_search($valueCCV['FK_TIPO_CV'], $FkTipoCV);
                      $DESC_CV = $datosTipoCV[$positionTipoCV]['DESC_CV'];
                      $infoFile = pathInfo($valueCCV['RUTA_CV']);

                      $nombre = 'CV'.$DESC_CV.'_'.$modelProspectos->PK_PROSPECTO.'_'.date('Y-m-d').'.'.$infoFile['extension'];
                      $rutaGuardado = '../uploads/ProspectosCV/';

                      if (copy($candidatoCV, $rutaGuardado.''.$nombre)) {
                        /*Se borra archivo*/
                        unlink($candidatoCV);
                        /*Se inserta en la tabla de prospectos el elemento copiado*/
                        $modelProspectosDocumentos = new TblProspectosDocumentos();
                        $modelProspectosDocumentos->FK_PROSPECTO    = $modelProspectos->PK_PROSPECTO;
                        $modelProspectosDocumentos->FK_TIPO_CV      = $valueCCV['FK_TIPO_CV'];
                        $modelProspectosDocumentos->RUTA_CV         = '../'.$rutaGuardado.''.$nombre;
                        $modelProspectosDocumentos->FECHA_REGISTRO  = date('Y-m-d');
                        $modelProspectosDocumentos->save(false);
                        /*Se borra de la tabla de candidatos documentos*/
                        $modelEliminar = TblCandidatosDocumentos::find()->where(['FK_CANDIDATO' => $PK_CANDIDATO, 'FK_TIPO_CV' => $valueCCV['FK_TIPO_CV']])->one();
                        $modelEliminar->delete();
                      }
                    }
                  }

                  $modelBitProspecto = new TblBitProspectos;
                  $modelBitProspecto['FK_PROSPECTO'] = $modelProspectos->PK_PROSPECTO;
                  $modelBitProspecto['EMAIL'] = $modelProspectos->EMAIL;
                  $modelBitProspecto['CELULAR'] = $modelProspectos->CELULAR;
                  $modelBitProspecto['TELEFONO'] = $modelProspectos->TELEFONO;
                  $modelBitProspecto['FK_ESTATUS'] = $modelProspectos->FK_ESTATUS;
                  $modelBitProspecto['PERFIL'] = $modelProspectos->PERFIL;
                  $modelBitProspecto['FECHA_CONVERSACION'] = $modelProspectos->FECHA_CONVERSACION;
                  $modelBitProspecto['FK_ESTADO'] = $modelProspectos->FK_ESTADO;
                  $modelBitProspecto['RECLUTADOR'] = $modelProspectos->RECLUTADOR;
                  $modelBitProspecto['EXPECTATIVA'] = $modelProspectos->EXPECTATIVA;
                  $modelBitProspecto['DISPONIBILIDAD_INTEGRACION'] = $modelProspectos->DISPONIBILIDAD_INTEGRACION;
                  $modelBitProspecto['DISPONIBILIDAD_ENTREVISTA'] = $modelProspectos->DISPONIBILIDAD_ENTREVISTA;
                  $modelBitProspecto['TRABAJA_ACTUALMENTE'] = 'NO';
                  $modelBitProspecto['CANAL'] = $modelProspectos->FK_CANAL;
                  $modelBitProspecto['SUELDO_ACTUAL'] = $modelProspectos->SUELDO_ACTUAL;
                  $modelBitProspecto['COMENTARIOS'] = 'TRANSICIÓN DE CANDIDATO A PROSPECTO';
                  $modelBitProspecto['FK_USUARIO'] = user_info()['PK_USUARIO'];
                  $modelBitProspecto['FECHA_REGISTRO'] = date('Y-m-d');
                  $modelBitProspecto->save(false);

                  $model->ESTATUS_CAND_APLIC = 0;
                  $model->save(false);
                }

                $modelVacante->FK_PRIORIDAD = tblcatprioridades::find()->where(['PK_PRIORIDAD' => $modelVacante->FK_PRIORIDAD])->limit(1)->one();
                $estacionCandidato = ($modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO == 99) ? 5 : $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO;
                $nextPag='view3'.$estacionCandidato;

                $model->CV = $this->CVs($PK_CANDIDATO);

        return $this->render($nextPag, [
                    'model' => $model,
                    'modelVacante' => $modelVacante,
                    'modelVacanteCandidato' => $modelVacanteCandidato,
                    'modelBitacora' => $modelBitacora,
                    'PK_CANDIDATO' => $PK_CANDIDATO,
                    'PK_VACANTE' => $PK_VACANTE,
                    'modelSubirArchivo' => $modelSubirArchivo,
                    'PREV' => $PREV
                ]);

                }
      else {

        $estacionCandidato = ($modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO == 99) ? 5 : $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO;

        if($estacionCandidato==5 && $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO == 3){
            $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 3;
            $modelCandidato->ESTATUS_CAND_APLIC = 0;
            $modelCandidato->save(false);
        }

        $nextPag='view3'.$estacionCandidato;

        $model->CV = $this->CVs($PK_CANDIDATO);

        return $this->render($nextPag, [
            'model' => $model,
            'modelVacante' => $modelVacante,
            'modelVacanteCandidato' => $modelVacanteCandidato,
        'modelBitacora' => $modelBitacora,
        'modelSubirArchivo' => $modelSubirArchivo,
        'PREV' => $PREV,
        ]);
    }

    }
    /**
     * Creates a new tblcandidatos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new tblcandidatos();
        $modelBitacora = new tblbitcomentarioscandidato();

        $pk_vacante = $_GET['pk_vacante'];
        $RUTA_ARCHIVO = '';

        if(!empty($pk_vacante)){
            $modelVacante = tblvacantes::findOne($pk_vacante);
            $EstacionActualVacante=$modelVacante->FK_ESTACION_VACANTE;
        }

        $modelSubirCVOriginal = new SubirArchivo();
        $modelSubirCVOriginal->extensions = 'doc, docx, pdf';
        $modelSubirCVOriginal->noRequired = true;

         //$modelVacante = TblVacantes::findOne($pk_vacante);

        $modelTecnologias = TblVacantesTecnologias::find()->select('FK_TECNOLOGIA')->where(['FK_VACANTE'=>$pk_vacante])->asArray()->column();
        $modelHerramientas = TblVacantesHerramientas::find()->select('FK_HERRAMIENTA')->where(['FK_VACANTE'=>$pk_vacante])->asArray()->column();
        $modelHabilidades = TblVacantesHabilidades::find()->select('FK_HABILIDAD')->where(['FK_VACANTE'=>$pk_vacante])->asArray()->column();

        if($request = Yii::$app->request->post()){

        $apellido_pat_asp = $request['TblCandidatos']['APELLIDO_PATERNO'];
        $apellido_mat_asp = $request['TblCandidatos']['APELLIDO_MATERNO'];
        $nombre_asp = $request['TblCandidatos']['NOMBRE'];
        $EMAIL_ASP = $request['TblCandidatos']['EMAIL'];
        $CURP = $request['TblCandidatos']['CURP'];
        $RFC = $request['TblCandidatos']['RFC'];
        $FECHA_NAC_CAN = $request['TblCandidatos']['FECHA_NAC_CAN'];
        $TELEFONO_ASP = $request['TblCandidatos']['TELEFONO'];
        $CELULAR = $request['TblCandidatos']['CELULAR'];
        $EXPECTATIVA = $request['TblCandidatos']['EXPECTATIVA'];
        $DISPONIBILIDAD = $request['TblCandidatos']['DISPONIBILIDAD'];
        // $FK_ASPIRANTE = $request['TblCandidatos']('FK_ASPIRANTE');
        // $RUTA_ARCHIVO = $request['TblCandidatos']('RUTA_ARCHIVO');
        $EDAD = $request['edad_empleado'];
        $FK_GENERO = $request['TblCandidatos']['FK_GENERO'];


        if ($apellido_pat_asp){
            $model->APELLIDO_PATERNO = $apellido_pat_asp;
        }

        if ($apellido_mat_asp){
            $model->APELLIDO_MATERNO = $apellido_mat_asp;
        }

        if ($nombre_asp ){
            $model->NOMBRE = $nombre_asp;
        }

        if ($EMAIL_ASP){

            $model->EMAIL = $EMAIL_ASP;
        }

        if ($TELEFONO_ASP){
            $model->TELEFONO = $TELEFONO_ASP;
        }

         if ($CELULAR){
            $model->CELULAR = $CELULAR;
        }


        if ($CURP){
            $model->CURP = $CURP;
        }

        if ($FECHA_NAC_CAN){
            $format = ('Y-m-d');
            $date = str_replace('/', '-', $FECHA_NAC_CAN);
            $newDate = date($format, strtotime($date));

            $model->FECHA_NAC_CAN = $newDate;
        }

        if ($RFC){
            $model->RFC = $RFC;
        }


        if ($EXPECTATIVA){
            $model->EXPECTATIVA = $EXPECTATIVA;
        }


        if ($DISPONIBILIDAD){
            $model->DISPONIBILIDAD = $DISPONIBILIDAD;
        }

        if ($FK_GENERO){

            $model->FK_GENERO = $FK_GENERO;
        }

        if ($RUTA_ARCHIVO){
            $nuevaRutaGuardado = '../uploads/CandidatosCV/';
            $nuevoNombre = 'cvOriginal_'.$apellido_pat_asp.'_'.$apellido_mat_asp.'_'.$nombre_asp.'_'.$FK_ASPIRANTE;
            $nombrePrec = explode (".", ($RUTA_ARCHIVO = $request-> get('RUTA_ARCHIVO')));
            $extensionOriginal = $nombrePrec[3];
            $fullPath = $nuevaRutaGuardado.$nuevoNombre.'.'.$extensionOriginal;
            $model->CV=$fullPath;
        }

          if ($RUTA_ARCHIVO){
            $modelSubirCVOriginal->noRequired = true;
        }else{
            $modelSubirCVOriginal->noRequired = false;
        }

            $fecha_registro =date('Y-m-d');
            $model->FECHA_REGISTRO = date('Y-m-d');

            $model->save(false);

            if($RUTA_ARCHIVO != NULL){
                $fullPath = $rutaGuardado = '../uploads/CandidatosCV/cvOriginal_'.$model->PK_CANDIDATO.'.'.$extensionOriginal;
                rename($RUTA_ARCHIVO, $fullPath);
                $model->CV= str_replace('..', '', $fullPath);
            }else{

                $rutaGuardado = '../uploads/CandidatosCV/';
                $nombre = 'cvOriginal_'.$model->PK_CANDIDATO;
                $modelSubirCVOriginal->file = UploadedFile::getInstance($modelSubirCVOriginal, '[1]file');
                $extensionCVOriginal = $modelSubirCVOriginal-> upload($rutaGuardado,$nombre);
                $model->CV='/uploads/CandidatosCV/cvOriginal_'.$model->PK_CANDIDATO.'.'.$extensionCVOriginal;
            }
            $model->save(false);
            // \Yii::$app
            // ->db
            // ->createCommand()
            // ->delete('tbl_aspirantes', ['PK_ASPIRANTES' => $FK_ASPIRANTE])
            // ->execute();
            // \Yii::$app
            // ->db
            // ->createCommand()
            // ->delete('tbl_aspirantes_vacantes', ['FK_ASPIRANTE' => $FK_ASPIRANTE])
            // ->execute();

            $nvoIdVacante=$modelVacante->PK_VACANTE;
            $modelVacanteCandidato = new TblVacantesCandidatos();
            $modelVacanteCandidato->FK_VACANTE = $nvoIdVacante;
            $modelVacanteCandidato->FK_CANDIDATO = $model->PK_CANDIDATO;
            $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO = 1;
            $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 1;
            $modelVacanteCandidato->FECHA_ACTUALIZACION = date('Y-m-d');
            $insert= $modelVacanteCandidato->save(false);

            if($EstacionActualVacante==2){
                $modelVacante->FK_ESTACION_VACANTE = 3;
                $modelVacante->save(false);
            }

            if($insert){
                $nombre_usuario= user_info()['NOMBRE_COMPLETO'];
                $nombre_vacante= $modelVacante->DESC_VACANTE;
                $nombre_candidato= $model->NOMBRE.' '.$model->APELLIDO_PATERNO.' '.$model->APELLIDO_MATERNO;
                $modelUsuario = TblUsuarios::findOne($modelVacante->FK_USUARIO);
                $de = get_config('EMPLEADOS','CORREO_FELICITACIONES');
                $para =explode(',',get_config('CANDIDATOS','REGISTROS_CANDIDATO'));
                if($modelUsuario->CORREO != ''){
                    $para[] = $modelUsuario->CORREO;
                }
                $asunto = 'Nuevo candidato asociado a '.$nombre_vacante;
                $mensaje='<style>p {font-family: Calibri; font-size: 11pt;}</style>
                    <p>
                        Buen d&iacute;a '.$nombre_usuario.'
                        <br/><br/>
                        Se le notifica que la Vacante <b>'.$nombre_vacante.'</b> tiene un nuevo candidato <b>'.$nombre_candidato.'</b>.
                        <br/><br/>
                        Favor de notificar al &Aacute;rea de Recursos Humanos si el candidato aplica o no la entrevista.
                        <br/><br/>
                        Quedando a sus &oacute;rdenes para cualquier duda y/o comentario
                        <br/><br/>
                        Saludos.
                    </p>';

                    $enviado = send_mail($de,$para, $asunto, $mensaje);
                }


                if(!empty($data['Tecnologias'])){
                    foreach ($data['Tecnologias'] as $key => $value) {
                        $modelTecnologia= new TblCandidatosTecnologias;
                        $modelTecnologia->FK_CANDIDATO = $model->PK_CANDIDATO;
                        $modelTecnologia->FK_VACANTE = $nvoIdVacante;
                        $modelTecnologia->FK_TECNOLOGIA =$value;
                        $modelTecnologia->FECHA_REGISTRO = $fecha_registro;
                        $modelTecnologia->save();
                    }
                }
                if(!empty($data['Herramientas'])){
                    foreach ($data['Herramientas'] as $key2 => $value2) {
                        $modelHerramientas= new TblCandidatosHerramientas;
                        $modelHerramientas->FK_CANDIDATO = $model->PK_CANDIDATO;
                        $modelHerramientas->FK_VACANTE = $nvoIdVacante;
                        $modelHerramientas->FK_HERRAMIENTA =$value2;
                        $modelHerramientas->FECHA_REGISTRO = $fecha_registro;
                        $modelHerramientas->save();
                    }
                }
                if(!empty($data['Habilidades'])){
                    foreach ($data['Habilidades'] as $key3 => $value3) {
                        $modelHabilidades= new TblCandidatosHabilidades;
                        $modelHabilidades->FK_CANDIDATO = $model->PK_CANDIDATO;
                        $modelHabilidades->FK_VACANTE = $nvoIdVacante;
                        $modelHabilidades->FK_HABILIDAD =$value3;
                        $modelHabilidades->FECHA_REGISTRO = $fecha_registro;
                        $modelHabilidades->save();
                    }
                }
            return $this->redirect(['view',
                'PK_CANDIDATO' => $model->PK_CANDIDATO,
                'PK_VACANTE' => $nvoIdVacante,
                    'modelTecnologias' => $modelTecnologias,
                    'modelHerramientas' => $modelHerramientas,
                    'modelHabilidades' => $modelHabilidades,
                    'EDAD' => $EDAD,
                ]);
        }elseif($modelBitacora->load(Yii::$app->request->post())){
                $modelBitacora->FK_USUARIO = 1;
                $modelBitacora->FECHA_REGISTRO = date('Y-m-d');
                $modelBitacora->save(false);
                $estacion = $modelBitacora->FK_ESTACION_CANDIDATO;
                if($modelBitacora->FK_ESTATUS_CANDIDATO == 4){
                    $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                }else{
                    if($estacion==1){
                        $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO = 2;
                        $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 2;
                    }elseif($estacion==2){
                        $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO = 3;
                        $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 2;
                    }elseif($estacion==3){
                        $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO = 4;
                        $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 2;
                    }elseif($estacion==4){
                        $modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO = 5;
                        $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = 2;
            $modelVacante = tblvacantes::findOne($modelVacanteCandidato->FK_VACANTE);
                        $EstacionActualVacante=$modelVacante->FK_ESTACION_VACANTE;
                        if($EstacionActualVacante==3){
                            $modelVacante->FK_ESTACION_VACANTE = 4;
                            $modelVacante->save(false);
                        }
                    }
                }
                $modelVacanteCandidato->FECHA_ACTUALIZACION = date('Y-m-d');
                $modelVacanteCandidato->save(false);
        if($modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO == 5){
                    $query= TblVacantesCandidatos::find()
                         ->andFilterWhere(
                            ['and',
                                ['=', 'FK_VACANTE', $modelVacanteCandidato->FK_VACANTE],
                                ['=', 'FK_ESTACION_ACTUAL_CANDIDATO', 5]
                            ])
                         ->all();
                        if(count($query)==$modelVacante->CANT_PERSONAS){
                            $modelVacante->FK_ESTACION_VACANTE = 5;
                            $modelVacante->FK_ESTATUS_VACANTE = 7;
                            $modelVacante->save(false);
                        }

                        //Busqueda de las demás vacantes del candidato
                        // $VacantesCandidatos = tblvacantescandidatos::find()->where(['FK_CANDIDATO' => $PK_CANDIDATO])->andWhere(['NOT IN','FK_VACANTE',$PK_VACANTE])->all();
                        //
                        //   foreach ($VacantesCandidatos as $keyvca => $valuevca) {
                        //       $modelVacantesCandidatos = tblvacantescandidatos::find()->where(['FK_VACANTE' => $valuevca->FK_VACANTE])->one();
                        //       $modelVacantesCandidatos->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                        //       $modelVacantesCandidatos->FECHA_ACTUALIZACION = date('Y-m-d');
                        //       $modelVacantesCandidatos->save(false);
                        //   }
                }
                $nextPag='view3'.$modelBitacora->FK_ESTACION_ACTUAL_CANDIDATO;

                return $this->render($nextPag, [
                    'model' => $model,
                    'modelVacante' => $modelVacante,
                    'modelVacanteCandidato' => $modelVacanteCandidato,
                    'modelBitacora' => $modelBitacora,
                    'PK_CANDIDATO' => $PK_CANDIDATO,
                    'PK_VACANTE' => $PK_VACANTE,
                ]);
        } else {
        //     var_dump($_GET['pk_vacante']);
        // Yii::$app->end();
           $modelVacante->PK_VACANTE=$pk_vacante;
            return $this->render('create', [
                'modelVacante' => $modelVacante,
                'model' => $model,
                'modelSubirCVOriginal' => $modelSubirCVOriginal,
                'modelTecnologias' => $modelTecnologias,
                'modelHerramientas' => $modelHerramientas,
                'modelHabilidades' => $modelHabilidades,
                'RUTA_ARCHIVO' => $RUTA_ARCHIVO,
            ]);
        }
    }

    /**
     * Updates an existing tblcandidatos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($PK_CANDIDATO,$PK_VACANTE)
    {
        $model = $this->findModel($PK_CANDIDATO);
        $modelVacante = tblvacantes::findOne($PK_VACANTE);
    $modelSubirCVOriginal = new SubirArchivo();
        $modelSubirCVOriginal->extensions = 'doc, docx, pdf';
    $modelSubirCVOriginal->noRequired = true;
    $modelTecnologias = TblCandidatosTecnologias::find()->select('FK_TECNOLOGIA')->where(['FK_VACANTE'=>$PK_VACANTE, 'FK_CANDIDATO'=>$PK_CANDIDATO])->asArray()->column();
        $modelHerramientas = TblCandidatosHerramientas::find()->select('FK_HERRAMIENTA')->where(['FK_VACANTE'=>$PK_VACANTE, 'FK_CANDIDATO'=>$PK_CANDIDATO])->asArray()->column();
        $modelHabilidades = TblCandidatosHabilidades::find()->select('FK_HABILIDAD')->where(['FK_VACANTE'=>$PK_VACANTE, 'FK_CANDIDATO'=>$PK_CANDIDATO])->asArray()->column();
        if ($model->load(Yii::$app->request->post())) {

        $modelSubirCVOriginal->file = UploadedFile::getInstance($modelSubirCVOriginal, '[1]file');
            if($modelSubirCVOriginal->file){
                    if(file_exists('..'.$model->CV)){
                            $status = unlink('..'.$model->CV);
                    }

                $rutaGuardado = '../uploads/CandidatosCV/';
                $nombre = 'cvOriginal_'.$PK_CANDIDATO;
                $extensionCVOriginal = $modelSubirCVOriginal-> upload($rutaGuardado,$nombre);
                $model->CV='/uploads/CandidatosCV/cvOriginal_'.$PK_CANDIDATO.'.'.$extensionCVOriginal;
            }
\Yii::$app
                ->db
                ->createCommand()
                ->delete('tbl_candidatos_tecnologias', ['FK_VACANTE' => $PK_VACANTE,'FK_CANDIDATO'=>$PK_CANDIDATO])
                ->execute();

            \Yii::$app
                ->db
                ->createCommand()
                ->delete('tbl_candidatos_herramientas', ['FK_VACANTE' => $PK_VACANTE,'FK_CANDIDATO'=>$PK_CANDIDATO])
                ->execute();

            \Yii::$app
                ->db
                ->createCommand()
                ->delete('tbl_candidatos_habilidades', ['FK_VACANTE' => $PK_VACANTE,'FK_CANDIDATO'=>$PK_CANDIDATO])
                ->execute();
            $data= Yii::$app->request->post();
            $fecha_registro =date('Y-m-d');
            if(!empty($data['Tecnologias'])){
                foreach ($data['Tecnologias'] as $key => $value) {
                    $modelTecnologia= new TblCandidatosTecnologias;
                    $modelTecnologia->FK_CANDIDATO = $model->PK_CANDIDATO;
                    $modelTecnologia->FK_VACANTE = $PK_VACANTE;
                    $modelTecnologia->FK_TECNOLOGIA =$value;
                    $modelTecnologia->FECHA_REGISTRO = $fecha_registro;
                    $modelTecnologia->save();
                }
            }
            if(!empty($data['Herramientas'])){
                foreach ($data['Herramientas'] as $key2 => $value2) {
                    $modelHerramientas= new TblCandidatosHerramientas;
                    $modelHerramientas->FK_CANDIDATO = $model->PK_CANDIDATO;
                    $modelHerramientas->FK_VACANTE = $PK_VACANTE;
                    $modelHerramientas->FK_HERRAMIENTA =$value2;
                    $modelHerramientas->FECHA_REGISTRO = $fecha_registro;
                    $modelHerramientas->save();
                }
            }
            if(!empty($data['Habilidades'])){
                foreach ($data['Habilidades'] as $key3 => $value3) {
                    $modelHabilidades= new TblCandidatosHabilidades;
                    $modelHabilidades->FK_CANDIDATO = $model->PK_CANDIDATO;
                    $modelHabilidades->FK_VACANTE = $PK_VACANTE;
                    $modelHabilidades->FK_HABILIDAD =$value3;
                    $modelHabilidades->FECHA_REGISTRO = $fecha_registro;
                    $modelHabilidades->save();
                }
            }
        $model->save(false);
            return $this->redirect(['view', 'PK_CANDIDATO' => $model->PK_CANDIDATO,'PK_VACANTE' => $modelVacante->PK_VACANTE]);
        } else {
            return $this->render('update', [
                'model' => $model,
        'modelVacante' => $modelVacante,
                'modelSubirCVOriginal' => $modelSubirCVOriginal,
        'modelTecnologias' => $modelTecnologias,
                'modelHerramientas' => $modelHerramientas,
                'modelHabilidades' => $modelHabilidades,
            ]);
        }
    }
/**
     * Updates an existing tblcandidatos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate2($PK_VACANTES_CANDIDATOS,$FK_ESTATUS)
    {
        $request = Yii::$app->request;
        $PREV = $request->get('PREV');
        $modelVacanteCandidato = tblvacantescandidatos::findOne($PK_VACANTES_CANDIDATOS);
        $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO = $FK_ESTATUS;
        $model = $this->findModel($modelVacanteCandidato->FK_CANDIDATO);
        $modelVacante = tblvacantes::findOne($modelVacanteCandidato->FK_VACANTE);
        $contaCandidatos = tblvacantescandidatos::find()->where(["FK_VACANTE" => $modelVacanteCandidato->FK_VACANTE, "FK_ESTACION_ACTUAL_CANDIDATO" => 5, "FK_ESTATUS_ACTUAL_CANDIDATO" => 3])->count();
        // SE APLICA LA REGLA SOLO SE GUARDAN LOS DATOS SI AUN NO SE A CUMPLIDO CON EL TOTAL DE PERSONAS INGRESADAS PARA LA VACANTE
        if($modelVacante->CANT_PERSONAS > $contaCandidatos)
        {
            $modelVacanteCandidato->FK_ESTATUS_ACTUAL_CANDIDATO=$FK_ESTATUS;
            $modelVacanteCandidato->FECHA_ACTUALIZACION = date('Y-m-d');
            $modelVacanteCandidato->save(false);

            // SI EL  ESTATUS DEL CANDIDATO ES EN ESTACION 4 CONTRATACION  Y ESTATUS ACEPTADA 3 SE DEBE DE  MODIFICAR
            // LA ESTACION DE  LA VACANTE A 4 CONTRATACION Y 2 EN PROCESO
            if($modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO == 4 && $FK_ESTATUS == 3)
            {
                $modelVacante->FK_ESTACION_VACANTE = 4;
                $modelVacante->FK_ESTATUS_VACANTE = 2;
                $modelVacante->save(false);
            }

            $nextPag='view3';//.$modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO;
            return $this->redirect([$nextPag,
                'PK_CANDIDATO' => $modelVacanteCandidato->FK_CANDIDATO,
                'PK_VACANTE' => $modelVacanteCandidato->FK_VACANTE,
                'PREV' => $PREV,
            ]);
        }
        else
        {
            $nextPag='view3';//.$modelVacanteCandidato->FK_ESTACION_ACTUAL_CANDIDATO;
            return $this->redirect([$nextPag,
                'PK_CANDIDATO' => $modelVacanteCandidato->FK_CANDIDATO,
                'PK_VACANTE' => $modelVacanteCandidato->FK_VACANTE,
                'PREV' => $PREV,
                'error_cant_pers' => 1
            ]);
        }
    }

    /**
     * Deletes an existing tblcandidato model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the tblcandidato model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return tblcandidato the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($PK_CANDIDATO)
    {
        if (($model = tblcandidatos::findOne([
            'PK_CANDIDATO' => $PK_CANDIDATO,
            ])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpload()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'CV');
            if ($model->upload()) {
                // file is uploaded successfully
                return $this->redirect(['view', 'id' => $model->PK_CANDIDATO]);
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

    /**
     * Metodo que se utiliza para validar si en el alta de candidatos existe un correo repetido
     * @return \yii\web\Response::FORMAT_JSON
     */
    public function actionValidar_campos()
    {
         if (Yii::$app->request->isAjax){
            $data = Yii::$app->request->post();

            $pk_candidato  = (isset($data['pk_candidato'])) ? $data['pk_candidato'] : 0;
            $email = $data['email'];

            $emailRepetido = false;

            $modelCandidato = TblCandidatos::find()->where(['EMAIL' => $email])->andWhere(['NOT IN','PK_CANDIDATO',$pk_candidato])->all();
            if (count($modelCandidato) > 0)
            {
                $emailRepetido = true;
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'emailRepetido' => $emailRepetido,
            ];
         }
    }
}
