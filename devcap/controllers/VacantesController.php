<?php


namespace app\controllers;

use Yii;
use app\models\tblvacantes;
use yii\db\ActiveQuery;
use app\models\TblAsignaciones;
use app\models\TblBitComentariosVacantes;
use app\models\TblBitVacantes;
use app\models\TblBitProspectos;
use app\models\TblVacantesHabilidades;
use app\models\TblVacantesTecnologias;
use app\models\TblVacantesHerramientas;
use app\models\tblbitcomentarioscandidato;
use app\models\tblcatperfilvacantes;
use app\models\tblcatestacionesvacante;
use app\models\tblcatestatusvacante;
use app\models\tblcandidatos;
use app\models\TblProspectos;
use app\models\tblcatprioridades;
use app\models\tblcatresponsablesrh;
use app\models\tblcatareas;
use app\models\tblcatpuestos;
use app\models\tblcatnivel;
use app\models\tblcattipocontrato;
use app\models\tblclientes;
use app\models\tblcatworkstation;
use app\models\tblcatduraciontiposervicios;
use app\models\tblcatubicaciones;
use app\models\tblcattipovacante;
use app\models\tblcatplantillasvacantes;
use app\models\tblconfigplantillasvacantes;
use app\models\tblconfigreportesvacantes;
use app\models\TblVacantesCandidatos;
use app\models\TblUsuarios;
use app\models\TblCandidatosDocumentos;
use app\models\TblCatTipoCV;
use app\models\TblProspectosDocumentos;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use app\models\Vacantes;
use yii\helpers\Url;
use yii\db\Query;
use yii\db\Expression;
Yii::$app->view->params['customParam'] = 'customValue';
/**
 * VacantesController implements the CRUD actions for tblvacantes model.
 */
class VacantesController extends Controller
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
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }


    public function actionConfig_index()
    {
         $data = Yii::$app->request->post();

        $connection = \Yii::$app->db;

        $sqlTblPlantillaVacantesIndex =
        $connection->createCommand("
                SELECT * FROM tbl_cat_plantillas_vacantes
                WHERE TIPO_PLANTILLA = 'DEFAULT' OR TIPO_PLANTILLA = 'CONSULTA' ")->queryAll();

        $connection->close();

        return $this->render('config_index', [
        'sqlTblPlantillaVacantesIndex' => $sqlTblPlantillaVacantesIndex,
        'data' => $data,
        ]);
    }
    public function actionEliminar(){

      if (Yii::$app->request->isAjax) {
          $data = Yii::$app->request->post();
          //Inicio nuevo bloque
          //Busqueda de los demas candidatos asociados a la vacante eliminada

                        $modelCandidatoVacantes = TblVacantesCandidatos::find()->where(['and',['FK_VACANTE' => $data['idVacante']],['NOT IN','FK_ESTATUS_ACTUAL_CANDIDATO',4],['NOT IN','FK_ESTATUS_ACTUAL_CANDIDATO',5]])->all();

                          //$CandidatosEnVacante = tblvacantescandidatos::find()->where(['=','FK_VACANTE',$modelVacanteCandidato->FK_VACANTE])->andWhere(['<>','FK_ESTATUS_ACTUAL_CANDIDATO',4])->andWhere(['<>','FK_ESTATUS_ACTUAL_CANDIDATO',5])->all();
                          
                        foreach ($modelCandidatoVacantes as $keycev => $valuecev) {
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

                          if(count($modeloCandidatoOtraVacante) == 0){

                            $modeloCandidatosVacante->FK_ESTACION_ACTUAL_CANDIDATO = 5;
                            $modeloCandidatosVacante->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                            $modeloCandidatosVacante->FECHA_ACTUALIZACION = date('Y-m-d');
                            $modeloCandidatosVacante->save(false);

                            $modelBitacoraCV->FK_VACANTE = $valuecev->FK_VACANTE;
                            $modelBitacoraCV->FK_CANDIDATO = $valuecev->FK_CANDIDATO;
                            $modelBitacoraCV->FK_ESTACION_CANDIDATO = $valuecev->FK_ESTACION_ACTUAL_CANDIDATO;
                            $modelBitacoraCV->FK_ESTATUS_CANDIDATO = $valuecev->FK_ESTATUS_ACTUAL_CANDIDATO;
                            $modelBitacoraCV->FK_USUARIO = 1;
                            $modelBitacoraCV->COMENTARIOS = 'CANCELADO POR ELIMINACION DE VACANTE';
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

                          }else if(count($modelCandidatoVacantes) != 0){

                                $modeloCandidatosVacante->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                                $modeloCandidatosVacante->FECHA_ACTUALIZACION = date('Y-m-d');
                                $modeloCandidatosVacante->save(false);

                                $modelBitacoraCV->FK_VACANTE = $valuecev->FK_VACANTE;
                                $modelBitacoraCV->FK_CANDIDATO = $valuecev->FK_CANDIDATO;
                                $modelBitacoraCV->FK_ESTACION_CANDIDATO = $valuecev->FK_ESTACION_ACTUAL_CANDIDATO;
                                $modelBitacoraCV->FK_ESTATUS_CANDIDATO = $valuecev->FK_ESTATUS_ACTUAL_CANDIDATO;
                                $modelBitacoraCV->FK_USUARIO = 1;
                                $modelBitacoraCV->COMENTARIOS = 'CANCELADO POR ELIMINACION DE VACANTE';
                                $modelBitacoraCV->FECHA_REGISTRO =  date('Y-m-d');
                                $modelBitacoraCV->save(false);
                            }
                          }
          //Fin nuevo bloque


          //$modelCandidatoVacantes = TblVacantesCandidatos::find()->where(['and',['FK_VACANTE' => $data['idVacante']],['NOT IN','FK_ESTATUS_ACTUAL_CANDIDATO',5]])->all();

          /*foreach ($modelCandidatoVacantes as $keyvca => $valuevca) {

            $modeloCandidatoOtraVacante = tblvacantescandidatos::find()->where(['<>','FK_VACANTE',$data['idVacante']])->andWhere(['FK_CANDIDATO' => $valuevca->FK_CANDIDATO])->all();
            $modelCandidatos = tblcandidatos::find()->where(['PK_CANDIDATO' => $valuevca->FK_CANDIDATO])->one();

            if (count($modeloCandidatoOtraVacante) == 0) {
              $modelCandidatos->ESTATUS_CAND_APLIC = 0;
              $modelCandidatos->save(false);

              if ($modelCandidatos->FK_PROSPECTO != NULL) {
                $modelProspectos = new tblProspectos();
                $modelProspectos->PK_PROSPECTO = $modelCandidatos->FK_PROSPECTO;
              } else{
                $modelProspectos = new tblProspectos();
              }

              $modelProspectos->NOMBRE = $modelCandidatos->NOMBRE;
              $modelProspectos->APELLIDO_PATERNO = $modelCandidatos->APELLIDO_PATERNO;
              $modelProspectos->APELLIDO_MATERNO = $modelCandidatos->APELLIDO_MATERNO;
              $modelProspectos->CURP = $modelCandidatos->CURP;
              $modelProspectos->EDAD = $modelCandidatos->EDAD;
              $modelProspectos->FK_GENERO = $modelCandidatos->FK_GENERO;
              $modelProspectos->FECHA_NAC = $modelCandidatos->FECHA_NAC_CAN;
              $modelProspectos->EMAIL = $modelCandidatos->EMAIL;
              $modelProspectos->TELEFONO = $modelCandidatos->TELEFONO;
              $modelProspectos->CELULAR = $modelCandidatos->CELULAR;
              $modelProspectos->PERFIL = $modelCandidatos->PERFIL;
              $modelProspectos->UNIVERSIDAD = $modelCandidatos->UNIVERSIDAD;
              $modelProspectos->CARRERA = $modelCandidatos->CARRERA;
              $modelProspectos->CONOCIMIENTOS_TECNICOS = $modelCandidatos->CONOCIMIENTOS_TECNICOS;
              $modelProspectos->COMENTARIOS = 'CANCELADO POR ELIMINACION DE VACANTE.';
              $modelProspectos->NIVEL_ESCOLARIDAD = $modelCandidatos->NIVEL_ESCOLARIDAD;
              $modelProspectos->FECHA_REGISTRO = date('Y-m-d');
              $modelProspectos->RECLUTADOR = $modelCandidatos->RECLUTADOR;
              $modelProspectos->EXPECTATIVA = $modelCandidatos->EXPECTATIVA;
              $modelProspectos->FECHA_CONVERSACION = $modelCandidatos->FECHA_CONVERSACION;
              $modelProspectos->LUGAR_RESIDENCIA = $modelCandidatos->LUGAR_RESIDENCIA;
              $modelProspectos->FK_FUENTE_VACANTE = $modelCandidatos->FK_FUENTE_VACANTE;
              $modelProspectos->DISPONIBILIDAD_INTEGRACION = $modelCandidatos->DISPONIBILIDAD_INTEGRACION;
              $modelProspectos->DISPONIBILIDAD_ENTREVISTA = $modelCandidatos->DISPONIBILIDAD_ENTREVISTA;
              $modelProspectos->TRABAJA_ACTUALMENTE = $modelCandidatos->TRABAJA_ACTUALMENTE;
              $modelProspectos->FK_CANAL = $modelCandidatos->FK_CANAL;
              $modelProspectos->SUELDO_ACTUAL = $modelCandidatos->SUELDO_ACTUAL;
              $modelProspectos->CAPACIDAD_RECURSO = $modelCandidatos->CAPACIDAD_RECURSO;
              $modelProspectos->TACTO_CLIENTE = $modelCandidatos->TACTO_CLIENTE;
              $modelProspectos->DESEMPENIO_CLIENTE = $modelCandidatos->DESEMPENIO_CLIENTE;
              $modelProspectos->FK_ESTATUS = 1;
              $modelProspectos->FK_ESTADO = 1;
              $modelProspectos->FK_USUARIO = user_info()['PK_USUARIO'];
              $modelProspectos->FK_ORIGEN = 3;
              $modelProspectos->FK_USUARIO_CHECKOUT = 0;
              $modelProspectos->save(false);

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
              $modelBitProspecto['COMENTARIOS'] = 'CANCELADO POR ELIMINACION DE VACANTE';
              $modelBitProspecto['FK_USUARIO'] = user_info()['PK_USUARIO'];
              $modelBitProspecto['FECHA_REGISTRO'] = date('Y-m-d');
              $modelBitProspecto->save(false);*/

              /*Se agregan los curriculums cuando el candidato es cancelado*/
              /*$datosTipoCV = TblCatTipoCV::find()->orderBy(['PK_TIPO_CV'=>SORT_ASC])->all();
              $FkTipoCV = array_column($datosTipoCV, 'PK_TIPO_CV');
              $CandidatosCV = TblCandidatosDocumentos::find()->where(['FK_CANDIDATO'=>$valuevca->FK_CANDIDATO])->all();
              if (!empty($CandidatosCV)) {
                foreach ($CandidatosCV as $keyCCV => $valueCCV) {
                  $candidatoCV = substr($valueCCV['RUTA_CV'], 3, strlen($valueCCV['RUTA_CV']));
                  $positionTipoCV = array_search($valueCCV['FK_TIPO_CV'], $FkTipoCV);
                  $DESC_CV = $datosTipoCV[$positionTipoCV]['DESC_CV'];
                  $infoFile = pathInfo($valueCCV['RUTA_CV']);

                  $nombre = 'CV'.$DESC_CV.'_'.$modelProspectos->PK_PROSPECTO.'_'.date('Y-m-d').'.'.$infoFile['extension'];
                  $rutaGuardado = '../uploads/ProspectosCV/';

                  if (copy($candidatoCV, $rutaGuardado.''.$nombre)) {*/
                    /*Se borra archivo*/
                    //unlink($candidatoCV);
                    /*Se inserta en la tabla de prospectos el elemento copiado*/
                    /*$modelProspectosDocumentos = new TblProspectosDocumentos();
                    $modelProspectosDocumentos->FK_PROSPECTO    = $modelProspectos->PK_PROSPECTO;
                    $modelProspectosDocumentos->FK_TIPO_CV      = $valueCCV['FK_TIPO_CV'];
                    $modelProspectosDocumentos->RUTA_CV         = '../'.$rutaGuardado.''.$nombre;
                    $modelProspectosDocumentos->FECHA_REGISTRO  = date('Y-m-d');
                    $modelProspectosDocumentos->save(false);*/
                    /*Se borra de la tabla de candidatos documentos*/
                    /*$modelEliminar = TblCandidatosDocumentos::find()->where(['FK_CANDIDATO' => $valuevca->FK_CANDIDATO, 'FK_TIPO_CV' => $valueCCV['FK_TIPO_CV']])->one();
                    $modelEliminar->delete();
                  }
                }
              }
            }

            $modelBitacoraVC = new tblbitcomentarioscandidato();
            $modelBitacoraVC->FK_VACANTE = $valuevca->FK_VACANTE;
            $modelBitacoraVC->FK_CANDIDATO = $valuevca->FK_CANDIDATO;
            $modelBitacoraVC->FK_PROSPECTO = $modelCandidatos->FK_PROSPECTO;
            $modelBitacoraVC->FK_ESTACION_CANDIDATO = $valuevca->FK_ESTACION_ACTUAL_CANDIDATO;
            $modelBitacoraVC->FK_ESTATUS_CANDIDATO = 5;
            $modelBitacoraVC->FK_USUARIO = 1;
            $modelBitacoraVC->COMENTARIOS = 'CANCELADO POR ELIMINACION DE VACANTE';
            $modelBitacoraVC->FECHA_REGISTRO =  date('Y-m-d');
            $modelBitacoraVC->save(false);

          }*/

          $connection = \Yii::$app->db;

          $Eliminar =
          $connection->createCommand("
            DELETE FROM  tbl_vacantes_candidatos
            WHERE FK_VACANTE = '".$data['idVacante']."' ")->execute();
          $connection->createCommand("
            DELETE FROM  tbl_vacantes
            WHERE PK_VACANTE = '".$data['idVacante']."' ")->execute();
          $connection->createCommand("
            DELETE FROM  tbl_vacantes_habilidades
            WHERE FK_VACANTE = '".$data['idVacante']."' ")->execute();
          $connection->createCommand("
            DELETE FROM  tbl_vacantes_herramientas
            WHERE FK_VACANTE = '".$data['idVacante']."' ")->execute();
          $connection->createCommand("
            DELETE FROM  tbl_vacantes_tecnologias
            WHERE FK_VACANTE = '".$data['idVacante']."' ")->execute();


         \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

          $connection->close();

         return [
                'data'=>$data
            ];
      }
    }

    public function actionPlantillasindex() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
                $connection = \Yii::$app->db;

                $sqlTblCatPlantillasVacantes =
                $connection->createCommand("
                    SELECT * FROM tbl_cat_plantillas_vacantes
                    WHERE TIPO_PLANTILLA = 'CONSULTA' ")->queryAll();

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $connection->close();

            return [
                'sqlTblCatPlantillasVacantes' => $sqlTblCatPlantillasVacantes,
                'data' => $data,
            ];
        }
    }

    public function actionPlantillaid() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $sqlConfigPlantillaVacanteDefault = "";
            $sqlConfigPlantillaVacanteDestino = "";

            if($data){
                $connection = \Yii::$app->db;

                $sqlConfigPlantillaVacante =
                    $connection->createCommand("
                    SELECT * FROM  tbl_config_plantillas_vacantes cpv WHERE cpv.FK_CAT_PLANTILLAS_VACANTES = ".$data['idPlantillaSeleccionada']." ORDER BY cpv.SECUENCIA_ORIGEN ASC")->queryAll();

                $sqlCatPlantilla =
                    $connection->createCommand("
                    SELECT * FROM  tbl_cat_plantillas_vacantes cpv WHERE cpv.PK_CAT_PLANTILLAS_VACANTES = ".$data['idPlantillaSeleccionada'])->queryAll();

                //Si la consulta VIENE VACIA significa que se trata de una lista sin configurar y por ello no tiene información de campos en tbl_config_plantillas_vacantes, por lo tanto se necesitan los campos de la lista "Default".
                if(!$sqlConfigPlantillaVacante){ //Si no se encuentra campos relacionados a la lista seleccionada, se realiza la petición de una lista sin configurar obteniendo los campos de la lista DEFAULT.

                    $sqlConfigPlantillaVacanteDefault =
                    $connection->createCommand("
                    SELECT * FROM  tbl_config_plantillas_vacantes cpv WHERE cpv.FK_CAT_PLANTILLAS_VACANTES = 1 ORDER by cpv.SECUENCIA_ORIGEN ASC")->queryAll();

                }else{
                    $sqlConfigPlantillaVacanteDestino =
                    $connection->createCommand("
                    SELECT * FROM  tbl_config_plantillas_vacantes cpv WHERE cpv.FK_CAT_PLANTILLAS_VACANTES = ".$data['idPlantillaSeleccionada']." AND cpv.MOSTRAR_COLUMNA = 1 ORDER BY cpv.SECUENCIA_DESTINO ASC")->queryAll();
                }

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $connection->close();
                return $res = array(
                    'sqlConfigPlantillaVacante' => $sqlConfigPlantillaVacante,
                    'sqlConfigPlantillaVacanteDestino' => $sqlConfigPlantillaVacanteDestino,
                    'sqlConfigPlantillaVacanteDefault' => $sqlConfigPlantillaVacanteDefault,
                    'sqlCatPlantilla' => $sqlCatPlantilla,
                    //'sqlConfigPlantillaVacanteOrigen' => $sqlConfigPlantillaVacanteOrigen,
                );
            }
        }
    }

    public function actionCrearplantilla() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $secuenciaOrigen = 1;
            $secuenciaDestino = 1;

            if($data){
                //Se valida el nombre que el usuario introdujo para verificar si ya existe en BD, si no existe entra al bloque if para crear una nueva plantilla, de lo contrario sólo se envia la variable para que en la vista se cache y se le notifique al usuario que el nombre que se desea utilizar ya existe y que necesite ingresar uno diferente.
                $connection = \Yii::$app->db;
                $nombrePlantillaDuplicado =
                $connection->createCommand("
                    SELECT * FROM  tbl_cat_plantillas_vacantes cpv WHERE cpv.TIPO_PLANTILLA = 'CONSULTA' AND cpv.FK_USUARIO IN (0,".user_info()['PK_USUARIO'].") AND cpv.NOMBRE_PLANTILLA = '".$data['nombrePlantilla']."' ORDER BY cpv.PK_CAT_PLANTILLAS_VACANTES DESC")->queryOne();

                if($nombrePlantillaDuplicado == false){
                    $modelCatPlantillasVacantes = new tblcatplantillasvacantes();
                    $modelCatPlantillasVacantes->NOMBRE_PLANTILLA = $data['nombrePlantilla'];
                    $modelCatPlantillasVacantes->TIPO_PLANTILLA = "CONSULTA";
                    $modelCatPlantillasVacantes->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelCatPlantillasVacantes->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelCatPlantillasVacantes->save(false);

                    //Se obtiene el PK de la plantilla que recien se ha creado para insertar los registros de las columnas correspondientes en la tabla tbl_config_plantillas_vacantes
                    $pkPlantillaRecienCreada =
                    $connection->createCommand("
                        SELECT cpv.PK_CAT_PLANTILLAS_VACANTES FROM  tbl_cat_plantillas_vacantes cpv WHERE cpv.NOMBRE_PLANTILLA = '".$data['nombrePlantilla']."' ")->queryOne();

                    //Se crea la lista en tbl_config_plantillas_vacantes indicando los campos que ya son visibles por default (MOSTRAR_COLUMNA = 1) y el resto de columnas que podrá manipular el usuario (MOSTRAR_COLUMNA = 0)
                    foreach ($data['listaDefaulEstatica'] as $key => $value) {
                        $modelConfigPlantillasVacantesNueva = new tblconfigplantillasvacantes();
                        $modelConfigPlantillasVacantesNueva->FK_CAT_PLANTILLAS_VACANTES = $pkPlantillaRecienCreada['PK_CAT_PLANTILLAS_VACANTES'];
                        if($value['value'] == 'DESC_VACANTE' || $value['value'] == 'CANDIDATO' || $value['value'] == 'SEGUIMIENTO' || $value['value'] == 'HISTORICO' || $value['value'] == 'FECHA_REGISTRO'){
                            $modelConfigPlantillasVacantesNueva->MOSTRAR_COLUMNA = 1;
                            $modelConfigPlantillasVacantesNueva->SECUENCIA_DESTINO = $secuenciaDestino;
                            $secuenciaDestino = $secuenciaDestino + 1;
                        }else{
                            $modelConfigPlantillasVacantesNueva->MOSTRAR_COLUMNA = 0;
                            $modelConfigPlantillasVacantesNueva->SECUENCIA_DESTINO = 0;
                        }
                        $modelConfigPlantillasVacantesNueva->NOMBRE_COLUMNA = $value['value'];
                        $modelConfigPlantillasVacantesNueva->LABEL_COLUMNA = $value['text'];
                        $modelConfigPlantillasVacantesNueva->SECUENCIA_ORIGEN = $secuenciaOrigen;
                        $modelConfigPlantillasVacantesNueva->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelConfigPlantillasVacantesNueva->save(false);
                        $secuenciaOrigen = $secuenciaOrigen + 1;
                    }//Fin de foreach
                }
                $connection->close();
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'data' => $data,
                'nombrePlantillaDuplicado' => $nombrePlantillaDuplicado,
            ];
        }
    }

    public function actionEditarnombreplantilla() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            if($data){

                $connection = \Yii::$app->db;
                $nombrePlantillaDuplicado =
                $connection->createCommand("
                    SELECT * FROM  tbl_cat_plantillas_vacantes cpv WHERE cpv.TIPO_PLANTILLA = 'CONSULTA' AND cpv.FK_USUARIO IN (0,".user_info()['PK_USUARIO'].") AND cpv.NOMBRE_PLANTILLA = '".$data['nombrePlantilla']."' ORDER BY cpv.PK_CAT_PLANTILLAS_VACANTES DESC")->queryOne();
                $connection->close();

                if($nombrePlantillaDuplicado == false){
                    $modelCatPlantillasVacantes = tblcatplantillasvacantes::find()->where(['PK_CAT_PLANTILLAS_VACANTES' => $data['idNombrePlantilla']])->limit(1)->one();
                    $modelCatPlantillasVacantes->NOMBRE_PLANTILLA = $data['nombrePlantilla'];
                    $modelCatPlantillasVacantes->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelCatPlantillasVacantes->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelCatPlantillasVacantes->save(false);
                }

            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'data' => $data,
                'nombrePlantillaDuplicado' => $nombrePlantillaDuplicado,
            ];
        }
    }

    public function actionEliminarplantilla() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            if($data){
                $modelCatPlantillasVacantes = tblcatplantillasvacantes::find()->where(['PK_CAT_PLANTILLAS_VACANTES' => $data['idNombrePlantilla']])->limit(1)->one();
                $modelCatPlantillasVacantes->delete();

                $connection = \Yii::$app->db;
                $plantillaEliminada =
                $connection->createCommand("
                    SELECT * FROM  tbl_cat_plantillas_vacantes cpv WHERE cpv.PK_CAT_PLANTILLAS_VACANTES = ".$data['idNombrePlantilla'])->queryOne();

                $connection->createCommand("
                    DELETE FROM tbl_config_plantillas_vacantes WHERE FK_CAT_PLANTILLAS_VACANTES = ".$data['idNombrePlantilla'])->execute();

                $connection->close();
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'data' => $data,
                'plantillaEliminada' => $plantillaEliminada,
            ];
        }
    }

    public function actionGuardarplantilla() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $idNombrePlantilla = $data['idNombrePlantilla'];
            $secuenciaDestino = 1;

            $mensaje = "";

            if($data){
                $modelConfigPlantillasVacantes = tblconfigplantillasvacantes::find()->where(['FK_CAT_PLANTILLAS_VACANTES' => $idNombrePlantilla])->all();

                //Bloque 'if' que indica que ya existen los registras de la lista seleccionada, se comparan los campos seleccionados y enviados para modificar el valor 'MOSTRAR_COLUMNA' y el orden en que fueron enviados en 'SECUENCIA_ORIGEN'.
                if(count($modelConfigPlantillasVacantes) > 0){
                    foreach ($modelConfigPlantillasVacantes as $key => $value) {
                        $modelConfigPlantillasVacantesUpd = tblconfigplantillasvacantes::find()->where(['FK_CAT_PLANTILLAS_VACANTES' => $idNombrePlantilla])->andWhere(['NOMBRE_COLUMNA' => $value->NOMBRE_COLUMNA])->limit(1)->one();
                        $modelConfigPlantillasVacantesUpd->MOSTRAR_COLUMNA = 0;
                        $modelConfigPlantillasVacantesUpd->SECUENCIA_DESTINO = 0;
                        $modelConfigPlantillasVacantesUpd->save(false);
                    }//Fin de foreach

                    foreach ($data['camposSeleccionados'] as $key => $value) {
                        $modelConfigPlantillasVacantesUpd = tblconfigplantillasvacantes::find()->where(['FK_CAT_PLANTILLAS_VACANTES' => $idNombrePlantilla])->andWhere(['NOMBRE_COLUMNA' => $value['value']])->limit(1)->one();
                        //Si se encuentra el campo enviado 'NOMBRE_COLUMNA' y el FK_CAT_PLANTILLAS_VACANTES de la plantilla en edición, entra a colocar la columna como MOSTRABLE en la consulta general de vacantes.
                        if(isset($modelConfigPlantillasVacantesUpd)){
                            $modelConfigPlantillasVacantesUpd->MOSTRAR_COLUMNA = 1;
                            $modelConfigPlantillasVacantesUpd->SECUENCIA_DESTINO = $secuenciaDestino;
                            $modelConfigPlantillasVacantesUpd->save(false);
                            $secuenciaDestino = $secuenciaDestino + 1;
                        }
                    }//Fin de foreach
                }//Fin de if(count($modelConfigPlantillasVacantes) > 0)
            }//Fin de if($data)

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'data' => $data,
                'mensaje' => $mensaje,
                //'plantillaEliminada' => $plantillaEliminada,
            ];
        }
    }

    public function actionCatplantillas()
    {
        $data = Yii::$app->request->get();
        $post=null;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';

        $query = new Query;
        $query->select('c.PK_CAT_PLANTILLAS_VACANTES AS id, c.NOMBRE_PLANTILLA AS text')
            ->from('tbl_cat_plantillas_vacantes AS c')
            ->where(['like','c.NOMBRE_PLANTILLA',$q]);
        $command = $query->createCommand();

        $data = $command->queryAll();
        $out['results'] = array_values($data);


        return $out;
    }


    /**
     * Lists all tblvacantes models.
     * @return mixed
     */
    public function actionIndex($id = 0)
    {
    // $tamanio_pagina=9;
    //     if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
    //         $unidadNegocio ='';
    //     }else{
    //         $unidadNegocio = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
    //     }
    //     if (Yii::$app->request->isAjax) {
    //         $data = Yii::$app->request->post();
    //         $post=null;
    //         parse_str($data['data'],$post);
    //         $nombre         =(!empty($post['nombre']))? trim($post['nombre']):'';
    //         $fechaCierre    =(!empty($post['fechaCierre']))? transform_date(trim($post['fechaCierre'])):'';
    //         $fechaCreacion  =(!empty($post['fechaCreacion']))? transform_date(trim($post['fechaCreacion'])):'';
    //         $prioridad      =(!empty($post['prioridad']))? trim($post['prioridad']):'';
    //         $responsablerh  =(!empty($post['responsablerh']))? trim($post['responsablerh']):'';
    //         $estatusvacante =(!empty($post['estatusvacante']))? trim($post['estatusvacante']): [2,3,1];
    //         $pagina         =(!empty($data['pagina']))? trim($data['pagina']):'';
    //
    //         if (function_exists('user_info')) {
    //             if((in_array('3', user_info()['ROLES'])||in_array('11', user_info()['ROLES']))&&!in_array('2',user_info()['ROLES'])){
    //                 $fk_responsable_rh = tblcatresponsablesrh::find()->where(['FK_USUARIO'=>user_info()['PK_USUARIO']])->asArray()->one()['PK_RESPONSABLE_RH'];
    //                 if($fk_responsable_rh){
    //                     $responsablerh= $fk_responsable_rh;
    //                 }
    //             }
    //         }
    //
    //         $query= (new \yii\db\Query())
    //             ->select('count(*) as count')
    //             ->from('tbl_vacantes as v')
    //             ->join('left join', 'tbl_usuarios u',
    //                     'v.FK_USUARIO=u.PK_USUARIO')
    //             ->join('left join', 'tbl_perfil_empleados p',
    //                     'u.FK_EMPLEADO= p.FK_EMPLEADO')
    //             ->where(['v.FK_ESTATUS_VACANTE' => $estatusvacante])
    //             ->andFilterWhere(
    //                 ['and',
    //                     ['LIKE', 'v.DESC_VACANTE', $nombre],
    //                     ['=', 'v.FK_PRIORIDAD', $prioridad],
    //                     ['=', 'v.FECHA_CIERRE', $fechaCierre],
    //                     ['=', 'v.FECHA_CREACION', $fechaCreacion],
    //                     ['=', 'v.FK_RESPONSABLE_RH', $responsablerh],
    //                     ['=', 'p.FK_UNIDAD_NEGOCIO', $unidadNegocio]
    //                 ])
    //             ->one()['count'];
    //
    //         if($query<$tamanio_pagina){
    //             $pagina=1;
    //         }
    //         $paginas=$query/$tamanio_pagina;
    //         if($pagina>$paginas){
    //             $pagina=(int)$paginas+1;
    //         }
    //
    //         $dataProvider = new ActiveDataProvider([
    //             'query' => (new \yii\db\Query())
    //                 ->select([
    //                         'v.PK_VACANTE',
    //                         'v.FK_UBICACION',
    //                         'v.DESC_VACANTE',
    //                         'v.FECHA_CREACION',
    //                         'v.FECHA_CIERRE',
    //                         'v.FK_RESPONSABLE_RH',
    //                         'v.FK_PRIORIDAD',
    //                         'v.FK_ESTACION_VACANTE',
    //                         'v.FK_ESTATUS_VACANTE',
    //                         'v.FK_UBICACION_CLIENTE',
    //                     ])
    //                 ->from('tbl_vacantes as v')
    //                 ->join('left join', 'tbl_usuarios u',
    //                         'v.FK_USUARIO=u.PK_USUARIO')
    //                 ->join('left join', 'tbl_perfil_empleados p',
    //                         'u.FK_EMPLEADO= p.FK_EMPLEADO')
    //                 ->where(['v.FK_ESTATUS_VACANTE' => $estatusvacante])
    //                 ->andFilterWhere(
    //                     ['and',
    //                         ['LIKE', 'v.DESC_VACANTE', $nombre],
    //                         ['=', 'v.FK_PRIORIDAD', $prioridad],
    //                         ['=', 'v.FECHA_CIERRE', $fechaCierre],
    //                         ['=', 'v.FECHA_CREACION', $fechaCreacion],
    //                         ['=', 'v.FK_RESPONSABLE_RH', $responsablerh],
    //                         ['=', 'p.FK_UNIDAD_NEGOCIO', $unidadNegocio]
    //                     ])
    //                 ->orderBy(['v.FK_ESTACION_VACANTE' => SORT_ASC])
    //             ,
    //             'pagination' => [
    //                 'pageSize' => $tamanio_pagina,
    //                 'page' => $pagina-1,
    //             ],
    //         ]);
    //
    //         $resultado=$dataProvider->getModels();
    //         foreach ($resultado as $key => $value) {
    //             $prioridad = tblcatprioridades::find()->where(['PK_PRIORIDAD' => $resultado[$key]['FK_PRIORIDAD']])->limit(1)->one();
    //     if(!empty($resultado[$key]['FK_UBICACION'])){
    //                 $ubicacion = tblcatubicaciones::find()->where(['PK_UBICACION' => $resultado[$key]['FK_UBICACION']])->limit(1)->one();
    //     }else{
    //                 $ubicacion = tblcatubicaciones::find()->where(['PK_UBICACION' => $resultado[$key]['FK_UBICACION_CLIENTE']])->limit(1)->one();
    //             }
    //             $estacion = tblcatestacionesvacante::find()->where(['PK_ESTACION_VACANTE' => $resultado[$key]['FK_ESTACION_VACANTE']])->limit(1)->one();
    //             $estatus = tblcatestatusvacante::find()->where(['PK_ESTATUS_VACANTE' => $resultado[$key]['FK_ESTATUS_VACANTE']])->limit(1)->one();
    //             if($prioridad){
    //                 $idprioridad = $prioridad->DESC_PRIORIDAD;
    //             }else{
    //                 $idprioridad = 'Sin Definir';
    //             }
    //
    //             if($estacion){
    //                 $idestacion = $estacion->DESC_ESTACION_VACANTE;
    //             }else{
    //                 $idestacion = 'Sin Definir';
    //             }
    //
    //             if($ubicacion){
    //                 $idubicacion = $ubicacion->DESC_UBICACION;
    //             }else{
    //                 $idubicacion = 'Sin Definir';
    //             }
    //
    //             if($estatus){
    //                 $idestatus = $estatus->DESC_ESTATUS_VACANTE;
    //             }else{
    //                 $idestatus = 'Sin Definir';
    //             }
    //             $resultado[$key]['FK_PRIORIDAD']=$idprioridad;
    //             $resultado[$key]['FK_UBICACION']=$idubicacion;
    //             $resultado[$key]['FK_ESTACION_VACANTE']=$idestacion;
    //             $resultado[$key]['FK_ESTATUS_VACANTE']=$idestatus;
    //             $resultado[$key]['FECHA_CIERRE'] = transform_date($resultado[$key]['FECHA_CIERRE'], 'd/m/Y');
    //             $resultado[$key]['FECHA_CREACION'] = transform_date($resultado[$key]['FECHA_CREACION'], 'd/m/Y');
    //         }
    //
    //
    //
    //         \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //         $res = array(
    //             'post'          => $post,
    //             'pagina'        => $pagina,
    //             'data'          => $resultado,
    //             'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
    //             'total_registros' => $query,
    //             'responsablerh' => $responsablerh,
    //
    //         );
    //
    //         return $res;
    //     }else{
            /*Datos para el GridView Kartik*/
            // $model = new TblVacantes();
            // $searchModel = new Vacantes;
            // $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());


            $dataProvider = (new \yii\db\Query())
                ->select([
                        'v.PK_VACANTE',
                        'v.FECHA_CREACION AS FECHA_REGISTRO',
                        'v.FK_PRIORIDAD',
                        'v.FK_ESTACION_VACANTE',
                        'v.FK_ESTATUS_VACANTE',
                        'v.FK_UBICACION',
                        'v.FK_UBICACION_CLIENTE',
                        'v.FK_RESPONSABLE_RH',

                        //**Información general**
                        'pr.DESC_PRIORIDAD',
                        'catpuesto.DESC_PUESTO',
                        'n.DESC_NIVEL',
                        'v.FECHA_CIERRE',
                        'v.DESC_VACANTE',
                        'v.COSTO_MAXIMO',
                        'v.CANT_PERSONAS',
                        'v.CANT_HOMBRES',
                        'V.CANT_MUJERES',
                        'catareas.DESC_AREA',
                        'rh.NOMBRE_RESPONSABLE_RH',
                        'g.DESC_GENERO',

                        //**Perfil Vacante**
                        // +Tecnologias
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
                ->orderBy(['v.FECHA_CREACION' => SORT_DESC]);
                //->all();
                $roles = user_info()['ROLES'];
                $fk_responsable_rh = tblcatresponsablesrh::find()->where(['FK_USUARIO'=>user_info()['PK_USUARIO']])->asArray()->one()['PK_RESPONSABLE_RH'];
                  /*if (!empty($roles)) {
                      if ($roles[0] == 3) {
                        $dataProvider->andWhere(['=', 'v.FK_RESPONSABLE_RH', $fk_responsable_rh]);
                      } elseif ($roles[0] == 4) {
                        $dataProvider->andWhere(['=', 'v.FK_USUARIO', user_info()['PK_USUARIO']]);
                      }
                  }*/

                  /*Control para pantallas de vacantes*/
                  if ($id == 1) {
                    //Caso 1 Vacantes sin candidatos
                    $title = "Vacantes sin candidatos asociados";
                    $VacantesCandidatos = (new yii\db\Query())
                      ->select(['FK_VACANTE'])
                      ->from('tbl_vacantes_candidatos')
                      ->distinct()
                      ->all();

                    foreach ($VacantesCandidatos as $keyVC => $valueVC) {
                      $VacantesCandidatos[$keyVC] = $VacantesCandidatos[$keyVC]['FK_VACANTE'];
                    }

                    // $dataProvider->andWhere(['NOT', 'v.PK_VACANTE', $VacantesCandidatos]);
                    $dataProvider->andFilterWhere(['and',
                        ['NOT IN', 'v.PK_VACANTE', $VacantesCandidatos]
                      ]);
                  }
                  elseif ($id == 2) {
                    //Caso 2 Vacantes sin responsable de RH
                    $title = "Vacantes sin responsable";

                    // $dataProvider->andWhere(['NOT', 'v.PK_VACANTE', $VacantesCandidatos]);
                    $dataProvider->where(['v.FK_RESPONSABLE_RH' => NULL]);
                  }
                  else{
                    //Caso 3 Consulta General
                    $title = "Consulta General Vacantes";
                  }
                  /*fin Control para pantallas de vacantes*/

                  $dataProvider = new ActiveDataProvider([
                    'query' => $dataProvider,
                    'pagination' => false
                 ]);
                 $dataProvider = $dataProvider->getModels();


            // $registros = $dataProvider->getModels();
            $registrosCont = 0;
            $candidatos= [];
            $query = "";
            $tecnologias = "";

            if (!empty($dataProvider)) {

            foreach ($dataProvider as $key => $valores) {

              $dataProvider[$key]['DESC_VACANTE'] = '<a href="' . Url::to(["vacantes/view?id=" . $dataProvider[$key]['PK_VACANTE']]) . '">'. $dataProvider[$key]['DESC_VACANTE']. '</a>';

              $dataProvider[$key]['HISTORICO'] = '<p><a class="historicoVacante" href="#"  data-toggle="modal" data-target="#HistorialVacante" data-vacante="'.$dataProvider[$key]['PK_VACANTE'].'">Ver detalle</a></p>';
              $UBICACION_CLIENTE = tblcatubicaciones::find()->where(['PK_UBICACION' => $dataProvider[$key]['FK_UBICACION_CLIENTE']])->limit(1)->one();
              $UBICACION_INTERNA = tblcatubicaciones::find()->where(['PK_UBICACION' => $dataProvider[$key]['FK_UBICACION']])->limit(1)->one();
              $dataProvider[$key]['UBICACION_CLIENTE'] = $UBICACION_CLIENTE['DESC_UBICACION'];
              $dataProvider[$key]['UBICACION_INTERNA'] = $UBICACION_INTERNA['DESC_UBICACION'];
              if ($dataProvider[$key]['COMENTARIOS'] == '') {
                $dataProvider[$key]['COMENTARIOS'] = '<span  id="funcionesVacante"   data-vacante="'.$dataProvider[$key]['PK_VACANTE'].'">No hay registros</span>';
              } else {
                $dataProvider[$key]['COMENTARIOS'] = '<span class="hide">'.$dataProvider[$key]['COMENTARIOS'].' </span> <a href="#!" id="comentariosVacante" data-toggle="modal" data-target="#comentariosv" data-vacante="'.$dataProvider[$key]['PK_VACANTE'].'">Ver Comentarios</a>';
              }
              if ($dataProvider[$key]['FUNCIONES'] == '') {
                $dataProvider[$key]['FUNCIONES'] = '<span  id="funcionesVacante"   data-vacante="'.$dataProvider[$key]['PK_VACANTE'].'">No hay registros</span>';
              } else {
                $dataProvider[$key]['FUNCIONES'] = '<span class="hide">'.$dataProvider[$key]['FUNCIONES'].' </span> <a href="#!" id="funcionesVacante" data-toggle="modal" data-value="4" data-target="#funcionesv" data-vacante="'.$dataProvider[$key]['PK_VACANTE'].'">Ver Funciones</a>';
              }

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

              if ($dataProvider[$key]['ORIGEN_VACANTE'] == '') {
                if($dataProvider[$key]['FK_ASIGNACION'] != ''){
                    $dataProvider[$key]['ORIGEN_VACANTE'] = "ASIGNACIONES";
                }else{
                    $dataProvider[$key]['ORIGEN_VACANTE'] = "VACANTES";
                }
              }

              /*Combo reclutador*/
              // $modelRH = tblcatresponsablesrh::find()->all();
              // $ComboResponsableRH = "<span class='hidden'>".$dataProvider[$key]['NOMBRE_RESPONSABLE_RH']."</span>";
              // $ComboResponsableRH .= "<select name='responsablesRH' onchange='chageResponsableRH(this, ".$dataProvider[$key]['PK_VACANTE'].");' id='responsablesRH' class='form-control select-border-none' data-id_vacante='".$dataProvider[$key]['PK_VACANTE']."'>";
              //
              // foreach ($modelRH as $keyRH => $valueRH) {
              //   $selected = ($modelRH[$keyRH]['PK_RESPONSABLE_RH'] == $dataProvider[$key]['FK_RESPONSABLE_RH']) ? "selected" : "";
              //   $ComboResponsableRH .= "<option value='".$modelRH[$keyRH]['PK_RESPONSABLE_RH']."' $selected >". $modelRH[$keyRH]['NOMBRE_RESPONSABLE_RH'] ."</option>";
              // }
              //
              // $ComboResponsableRH .= "</select>";
              //
              // $dataProvider[$key]['NOMBRE_RESPONSABLE_RH'] = $ComboResponsableRH;

              if ($dataProvider[$key]['NOMBRE_RESPONSABLE_RH'] == '') {
                $dataProvider[$key]['NOMBRE_RESPONSABLE_RH'] = 'NINGUNO';                
              } else {
                $modelRH = tblcatresponsablesrh::find()->all();
                // $ComboResponsableRH = "<span class='hidden'>".$dataProvider[$key]['NOMBRE_RESPONSABLE_RH']."</span>";
                $ComboResponsableRH = "<select name='responsablesRH' onchange='chageResponsableRH(this);' id='responsablesRH' class='form-control select-border-none' data-id_vacante='".$dataProvider[$key]['PK_VACANTE']."'>";
                
                foreach ($modelRH as $keyRH => $valueRH)  {
                  $selected = ($modelRH[$keyRH]['PK_RESPONSABLE_RH'] == $dataProvider[$key]['FK_RESPONSABLE_RH']) ? "selected" : "";
                  $ComboResponsableRH .= "<option value='".$modelRH[$keyRH]['PK_RESPONSABLE_RH']."' $selected >". $modelRH[$keyRH]['NOMBRE_RESPONSABLE_RH'] ."</option>";

                }
                $ComboResponsableRH .= "</select>";
              }
              $dataProvider[$key]['NOMBRE_RESPONSABLE_RH'] = "<a style='cursor:pointer' idrh='". $dataProvider[$key]['FK_RESPONSABLE_RH'] ."' onclick='forceCambio(". $dataProvider[$key]['PK_VACANTE'] .")'>". $dataProvider[$key]['NOMBRE_RESPONSABLE_RH'] ."</a>";

              $query= (new \yii\db\Query())
                  ->select([
                    'candi.PK_CANDIDATO',
                    'CONCAT(candi.NOMBRE," ",candi.APELLIDO_PATERNO," ",candi.APELLIDO_MATERNO) AS CANDIDATO'
                  ])
                  ->from('tbl_vacantes as v')
                  ->join('INNER JOIN', 'tbl_vacantes_candidatos vc',
                          'vc.FK_VACANTE=v.PK_VACANTE')
                  ->join('INNER JOIN', 'tbl_candidatos candi',
                          'vc.FK_CANDIDATO = candi.PK_CANDIDATO AND
                          vc.FK_ESTATUS_ACTUAL_CANDIDATO = 1 OR
                          vc.FK_ESTATUS_ACTUAL_CANDIDATO = 2 OR
                          vc.FK_ESTATUS_ACTUAL_CANDIDATO = 3')
                  ->where(['vc.FK_VACANTE' => $dataProvider[$key]['PK_VACANTE']])
                  ->all();

              if (empty($query)) {
                //$dataProvider[$key]['CANDIDATO'] = "Ninguno";
                $dataProvider[$key]['SEGUIMIENTO'] = "";
                $dataProvider[$key]['TECNOLOGIAS'] = "";
                $dataProvider[$key]['HERRAMIENTAS'] = "";
                $dataProvider[$key]['HABILIDADES'] = "";
              }
              else{
                //$cand = "";
                $seguimiento = "";
                // $historico = "";
                //$genero = "";
                /*foreach ($query as $llave => $value) {
                  $cand .= '<p><a data-toggle="modal" data-target="#candidatos-detalle" class="invocar-detalle" style="cursor: pointer;">'.$query[$llave]['CANDIDATO'].' <input type="hidden" value='.$query[$llave]['PK_CANDIDATO'].'></a></p>';
                  //Cambio en variable $seguimiento para llamar a modal consulta-candidatos

                  // $historico .= '<p><a class="historicoVacante" href="#"  data-vacante="'.$dataProvider[$key]['PK_VACANTE'].'">Ver detalle</a></p>';
                  // $genero .= '<p>'.$query[$llave]['DESC_GENERO'].'</p>';
                }*/

                 $seguimiento .= '<p><a data-toggle="modal" data-target="#ConsultaCandidatos" class="seguimientoCandidatos" data-vacante="'.$dataProvider[$key]['PK_VACANTE'].'" style = "cursor: pointer;">Ver detalle</a></p>';

                //$dataProvider[$key]['CANDIDATO'] = $cand;
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
                $dataProvider[$key]['TECNOLOGIAS'] = "".$CandidatoTecnologias."<span id='datosVacanteTH' data-vacante=".$dataProvider[$key]['PK_VACANTE']." data-value='1'>No hay registros</span>";
              }
              else {
                $CandidatoTecnologias = "<span class='hide'>";
                foreach ($tecnologias as $tkey => $tvalue) {
                  $CandidatoTecnologias .= $tecnologias[$tkey]['DESC_TECNOLOGIA'].', <br>';
                }
                $CandidatoTecnologias .= "</span>";
                $dataProvider[$key]['TECNOLOGIAS'] = "".$CandidatoTecnologias."<a id='datosVacanteTH' href='#!' data-toggle='modal' data-target='#datosth' data-vacante=".$dataProvider[$key]['PK_VACANTE']." data-value='1'>Ver Tecnologías</a>";
              }

              // $dataProvider[$key]['TECNOLOGIAS'] = $CandidatoTecnologias;
              

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
                $dataProvider[$key]['HERRAMIENTAS'] = "".$CandidatoHerramientas."<span id='datosVacanteTH' data-vacante=".$dataProvider[$key]['PK_VACANTE']." data-value='1'>No hay registros</span>";
              }
              else {
                $CandidatoHerramientas = "<span class='hide'>";
                foreach ($herramientas as $hkey => $hvalue) {
                  $CandidatoHerramientas .= $herramientas[$hkey]['DESC_HERRAMIENTA'].', <br>';
                }
                $CandidatoHerramientas .= "</span>";
                $dataProvider[$key]['HERRAMIENTAS'] = "".$CandidatoHerramientas."<a id='datosVacanteTH' class='linkH' href='#!' data-toggle='modal' data-target='#datosth' data-value='2' data-vacante=".$dataProvider[$key]['PK_VACANTE'].">Ver Herramientas</a>";
              }
              // $dataProvider[$key]['HERRAMIENTAS'] = $CandidatoHerramientas;
              
              /*FIN Agregar herramientas al dataProvider*/

              /*Agregar habiidades al dataProvider*/
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
                $dataProvider[$key]['HABILIDADES'] = "".$CandidatoHerramientas."<span id='datosVacanteTH' data-vacante=".$dataProvider[$key]['PK_VACANTE']." data-value='1'>No hay registros</span>";
              }
              else {
                $CandidatoHabilidades = "<span class='hide'>";
                foreach ($habilidades as $hkey => $hvalue) {
                  $CandidatoHabilidades .= $habilidades[$hkey]['DESC_HABILIDAD'].', <br>';
                }
                $CandidatoHabilidades .= "</span>";
                $dataProvider[$key]['HABILIDADES'] = "".$CandidatoHabilidades."<a id='datosVacantePH' href='#!' data-toggle='modal' data-target='#datosph' data-value='3' data-vacante=".$dataProvider[$key]['PK_VACANTE'].">Ver Habilidades</a>";
              }
              // $dataProvider[$key]['HABILIDADES'] = $CandidatoHabilidades;
              

              /*FIN Agregar habilidades al dataProvider*/

            }

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
                    WHERE c.FK_CAT_PLANTILLAS_VACANTES = ".$idPlantillaSel."")->queryAll();

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
            //dd($dataProvider);

            $dummyCandidatos = new tblcandidatos;
            return $this->render('index', [
                'mensaje'=>$mensaje,
                'posiciones'=>$posiciones,
                'dataProvider'=>$dataProvider,
                'tecnologias' => $tecnologias,
                'registros' => $registrosCont,
                'query' => $query,
                'candidatos' => $candidatos,
                'dummyCandidatos' => $dummyCandidatos,
                'roles' => $roles,
                'title' => $title
            ]);
      //  }

    }


/**
     * Lists all tblvacantes models.
     * @return mixed
     */
    public function actionIndex2()
    {
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

            $nombre         =(!empty($post['nombre']))? trim($post['nombre']):'';
            $fechaCierre    =(!empty($post['fechaCierre']))? transform_date(trim($post['fechaCierre'])):'';
            $fechaCreacion  =(!empty($post['fechaCreacion']))? transform_date(trim($post['fechaCreacion'])):'';
            $prioridad      =(!empty($post['prioridad']))? trim($post['prioridad']):'';
            $responsablerh  = NULL;
            $estatusvacante = 4;
            $pagina         =(!empty($data['pagina']))? trim($data['pagina']):'';

            if (function_exists('user_info')) {
                if((in_array('3', user_info()['ROLES'])||in_array('11', user_info()['ROLES']))&&!in_array('2',user_info()['ROLES'])){
                    $fk_responsable_rh = tblcatresponsablesrh::find()->where(['FK_USUARIO'=>user_info()['PK_USUARIO']])->asArray()->one()['PK_RESPONSABLE_RH'];
                    if($fk_responsable_rh){
                        $responsablerh= $fk_responsable_rh;
                    }
                }
            }

            $query= (new \yii\db\Query())
                ->select('count(*) as count')
                ->from('tbl_vacantes as v')
                ->join('left join', 'tbl_usuarios u','v.FK_USUARIO=u.PK_USUARIO')
                   ->where(['NOT IN', 'v.FK_ESTATUS_VACANTE', [$estatusvacante]])
                   ->where(['v.FK_RESPONSABLE_RH' => NULL])
                ->andFilterWhere(
                    ['and',
                        ['LIKE', 'v.DESC_VACANTE', $nombre],
                        ['=', 'v.FK_PRIORIDAD', $prioridad],
                        ['=', 'v.FECHA_CIERRE', $fechaCierre],


                    ])
                ->one()['count'];

            if($query<$tamanio_pagina){
                $pagina=1;
            }
            $paginas=$query/$tamanio_pagina;
            if($pagina>$paginas){
                $pagina=(int)$paginas+1;
            }

            $dataProvider = new ActiveDataProvider([
                'query' => (new \yii\db\Query())
                    ->select([
                            'v.PK_VACANTE',
                            'v.FK_UBICACION',
                            'v.DESC_VACANTE',
                            'v.FECHA_CREACION',
                            'v.FECHA_CIERRE',
                            'v.FK_RESPONSABLE_RH',
                            'v.FK_PRIORIDAD',
                            'v.FK_ESTACION_VACANTE',
                            'v.FK_ESTATUS_VACANTE',
                            'v.FK_UBICACION_CLIENTE',
                        ])
                    ->from('tbl_vacantes as v')
                    ->join('left join', 'tbl_usuarios u',
                            'v.FK_USUARIO=u.PK_USUARIO')
                    ->join('left join', 'tbl_perfil_empleados p',
                            'u.FK_EMPLEADO= p.FK_EMPLEADO')
                    ->where(['NOT IN','v.FK_ESTATUS_VACANTE', [$estatusvacante]])
                    ->where(['v.FK_RESPONSABLE_RH' => NULL])
                    ->andFilterWhere(
                        ['and',
                            ['LIKE', 'v.DESC_VACANTE', $nombre],
                            ['=', 'v.FK_PRIORIDAD', $prioridad],
                            ['=', 'v.FECHA_CIERRE', $fechaCierre],
                            ['=', 'v.FECHA_CREACION', $fechaCreacion]
                        ])
                    ->orderBy(['v.FK_ESTACION_VACANTE' => SORT_ASC])
                ,
                'pagination' => [
                    'pageSize' => $tamanio_pagina,
                    'page' => $pagina-1,
                ],
            ]);

            $resultado=$dataProvider->getModels();
            foreach ($resultado as $key => $value) {
                $prioridad = tblcatprioridades::find()->where(['PK_PRIORIDAD' => $resultado[$key]['FK_PRIORIDAD']])->limit(1)->one();
                if(!empty($resultado[$key]['FK_UBICACION'])){
                    $ubicacion = tblcatubicaciones::find()->where(['PK_UBICACION' => $resultado[$key]['FK_UBICACION']])->limit(1)->one();
                }else{
                    $ubicacion = tblcatubicaciones::find()->where(['PK_UBICACION' => $resultado[$key]['FK_UBICACION_CLIENTE']])->limit(1)->one();
                }
                $estacion = tblcatestacionesvacante::find()->where(['PK_ESTACION_VACANTE' => $resultado[$key]['FK_ESTACION_VACANTE']])->limit(1)->one();
                $estatus = tblcatestatusvacante::find()->where(['PK_ESTATUS_VACANTE' => $resultado[$key]['FK_ESTATUS_VACANTE']])->limit(1)->one();
                if($prioridad){
                    $idprioridad = $prioridad->DESC_PRIORIDAD;
                }else{
                    $idprioridad = 'Sin Definir';
                }


                if($estacion){
                    $idestacion = $estacion->DESC_ESTACION_VACANTE;
                }else{
                    $idestacion = 'Sin Definir';
                }

                if($ubicacion){
                    $idubicacion = $ubicacion->DESC_UBICACION;
                }else{
                    $idubicacion = 'Sin Definir';
                }

                if($estatus){
                    $idestatus = $estatus->DESC_ESTATUS_VACANTE;
                }else{
                    $idestatus = 'Sin Definir';
                }
                $resultado[$key]['FK_PRIORIDAD']=$idprioridad;
                $resultado[$key]['FK_UBICACION']=$idubicacion;
                $resultado[$key]['FK_ESTACION_VACANTE']=$idestacion;
                $resultado[$key]['FK_ESTATUS_VACANTE']=$idestatus;
                $resultado[$key]['FECHA_CIERRE'] = transform_date($resultado[$key]['FECHA_CIERRE'], 'd/m/Y');
                $resultado[$key]['FECHA_CREACION'] = transform_date($resultado[$key]['FECHA_CREACION'], 'd/m/Y');
            }



            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'post'          => $post,
                'pagina'        => $pagina,
                'data'          => $resultado,
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
                'total_registros' => $query,
                'responsablerh' => '',
            );

            return $res;
        }else{
            $dataProvider = new ActiveDataProvider([
                'query' => tblvacantes::find()
                ->andWhere(['=', 'FK_ESTACION_VACANTE', 1])
                ->orderBy(['FECHA_CREACION' => SORT_DESC]),
                'pagination' => [
                    'pageSize' => $tamanio_pagina,
                    'page' => 0,
                ],
            ]);


             $catResponsables = (new \yii\db\Query())
                        ->select([
                            'rh.PK_RESPONSABLE_RH',
                            'rh.NOMBRE_RESPONSABLE_RH',
                            ])
                        ->from('tbl_cat_responsables_rh rh')
                        ->join('left join','tbl_usuarios u',
                                'u.PK_USUARIO= rh.FK_USUARIO')
                        ->join('left join', 'tbl_perfil_empleados p',
                                'u.FK_EMPLEADO=p.FK_EMPLEADO')
                        ->andFilterWhere(['and',['p.FK_UNIDAD_NEGOCIO'=> $unidadNegocio]])
                        ->all();
        return $this->render('index2', [
        'data' => $dataProvider->getModels(),
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
            'dataProvider' => $dataProvider,
            'catResponsables'=>$catResponsables,
        ]);
       }
    }

    public function actionIndex3()
    {
        $tamanio_pagina=9;
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre    =(!empty($post['nombre']))? trim($post['nombre']):'';
            $prioridad =(!empty($post['prioridad']))? trim($post['prioridad']):'';
            $pagina    =(!empty($data['pagina']))? trim($data['pagina']):'';

            if($prioridad){
                $query= tblvacantes::find()
                    ->andWhere(['LIKE', 'DESC_VACANTE', $nombre])
                    ->andWhere(['=', 'FK_PRIORIDAD', $prioridad])
                    ->orderBy(['FECHA_CIERRE' => SORT_DESC])->all();
                if(count($query)<$tamanio_pagina){
                    $pagina=1;
                }
                $dataProvider = new ActiveDataProvider([
                    'query' => tblvacantes::find()
                        ->andWhere(['LIKE', 'DESC_VACANTE', $nombre])
                        ->andWhere(['=', 'FK_PRIORIDAD', $prioridad])
                        ->orderBy(['FECHA_CIERRE' => SORT_DESC])
                        ,
                    'pagination' => [
                        'pageSize' => $tamanio_pagina,
                        'page' => $pagina-1,
                    ],
                ]);
            }else{
                $query= tblvacantes::find()
                    ->andWhere(['LIKE', 'DESC_VACANTE', $nombre])
                    ->orderBy(['FECHA_CIERRE' => SORT_DESC])->all();
                if(count($query)<$tamanio_pagina){
                    $pagina=1;
                }
                $dataProvider = new ActiveDataProvider([
                    'query' => tblvacantes::find()
                        ->andWhere(['LIKE', 'DESC_VACANTE', $nombre])
                        ->orderBy(['FECHA_CIERRE' => SORT_DESC])

                    ,
                    'pagination' => [
                        'pageSize' => $tamanio_pagina,
                        'page' => $pagina-1,
                    ],
                ]);
            }

        $resultado=$dataProvider->getModels();
            foreach ($resultado as $key => $value) {
                $prioridad = tblcatprioridades::find()->where(['PK_PRIORIDAD' => $resultado[$key]->FK_PRIORIDAD])->limit(1)->one();
                $responsable = tblcatresponsablesrh::find()->where(['PK_RESPONSABLE_RH' => $resultado[$key]->FK_RESPONSABLE_RH])->limit(1)->one();

                if($prioridad){
                    $idprioridad = $prioridad->DESC_PRIORIDAD;
                }else{
                    $idprioridad = 'Sin Definir';
                }


               if($responsable){
                    $idresponsable = $responsable->NOMBRE_RESPONSABLE_RH;
                }else{
                    $idresponsable = 'Sin Definir';
                }
                $resultado[$key]->FK_RESPONSABLE_RH=$idresponsable;
                $resultado[$key]->FK_PRIORIDAD=$idprioridad;

            }



            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $res = array(
                'post'        => $data,
                'pagina'        => $pagina,
                'data'          => $dataProvider->getModels(),
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
            );

            return $res;
        }else{
            $dataProvider = new ActiveDataProvider([
                'query' => tblvacantes::find()
        ->orderBy(['FECHA_CREACION' => SORT_DESC])

            ,
                'pagination' => [
                    'pageSize' => $tamanio_pagina,
                    'page' => 0,
                ],
            ]);
        return $this->render('index3', [
        'data' => $dataProvider->getModels(),
                'total_paginas' => ceil($dataProvider->getPagination()->totalCount / $tamanio_pagina),
            'dataProvider' => $dataProvider,
        ]);
       }
    }


    /**
     * Vacantes para buscar candidato
     * @return mixed
     */
    public function actionIndex4()
    {
        $pagina = 1;
        $tamanio_pagina=9;
        $Limit = '';
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $unidadNegocio ='';
        }else{
            $unidadNegocio = user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO'];
        }

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $post=null;
            parse_str($data['data'],$post);
            $nombre         =(!empty($post['nombre']))? trim($post['nombre']):'';
            $fechaCierre    =(!empty($post['fechaCierre']))? transform_date(trim($post['fechaCierre'])):'';
            $fechaCreacion  =(!empty($post['fechaCreacion']))? transform_date(trim($post['fechaCreacion'])):'';
            $prioridad      =(!empty($post['prioridad']))? trim($post['prioridad']):'';
            $responsablerh  =(!empty($post['responsablerh']))? trim($post['responsablerh']):'';
            //$estatusvacante =(!empty($post['estatusvacante']))? trim($post['estatusvacante']): [2,3,1];
            $estatusvacante =(!empty($post['estatusvacante']))? trim($post['estatusvacante']): '';
            $pagina         =(!empty($data['pagina']))? trim($data['pagina']):'';
            $Limit = 'Limit '.(($pagina * 9)-9).', 9';

            if (function_exists('user_info')) {
                if((in_array('3', user_info()['ROLES'])||in_array('11', user_info()['ROLES']))&&!in_array('2',user_info()['ROLES'])){
                    $fk_responsable_rh = tblcatresponsablesrh::find()->where(['FK_USUARIO'=>user_info()['PK_USUARIO']])->asArray()->one()['PK_RESPONSABLE_RH'];
                    if($fk_responsable_rh){
                        $responsablerh= $fk_responsable_rh;
                    }
                }
            }

            $connection = \Yii::$app->db;
            $totalCount = $connection->createCommand("CALL SP_VACANTES_BUSCAR_CANDIDATO(:P_NOMBRE, :P_PRIORIDAD, :P_FECHA_CIERRE, :P_FECHA_CREACION, :P_RESPONSABLE_RH, :P_UNIDAD_NEGOCIO,:P_ESTATUS_VACANTE, :P_LIMIT, :P_COUNT)")
                     ->bindValue(':P_NOMBRE', $nombre)
                     ->bindValue(':P_PRIORIDAD', $prioridad)
                     ->bindValue(':P_FECHA_CIERRE', $fechaCierre)
                     ->bindValue(':P_FECHA_CREACION', $fechaCreacion)
                     ->bindValue(':P_RESPONSABLE_RH', $responsablerh)
                     ->bindValue(':P_UNIDAD_NEGOCIO', $unidadNegocio)
                     ->bindValue(':P_ESTATUS_VACANTE', $estatusvacante)
                     ->bindValue(':P_LIMIT', '')
                     ->bindValue(':P_COUNT', 1)
                    ->queryAll();
                //Cerrar Conexion
            $connection->close();
            $todos = $totalCount[0]["NUM_ROWS"];

            $connection = \Yii::$app->db;
            $resultado = $connection->createCommand("CALL SP_VACANTES_BUSCAR_CANDIDATO(:P_NOMBRE, :P_PRIORIDAD, :P_FECHA_CIERRE, :P_FECHA_CREACION, :P_RESPONSABLE_RH, :P_UNIDAD_NEGOCIO,:P_ESTATUS_VACANTE, :P_LIMIT, :P_COUNT)")
                     ->bindValue(':P_NOMBRE', $nombre)
                     ->bindValue(':P_PRIORIDAD', $prioridad)
                     ->bindValue(':P_FECHA_CIERRE', $fechaCierre)
                     ->bindValue(':P_FECHA_CREACION', $fechaCreacion)
                     ->bindValue(':P_RESPONSABLE_RH', $responsablerh)
                     ->bindValue(':P_UNIDAD_NEGOCIO', $unidadNegocio)
                    ->bindValue(':P_ESTATUS_VACANTE', $estatusvacante)
                     ->bindValue(':P_LIMIT', $Limit)
                     ->bindValue(':P_COUNT', 0)
                    ->queryAll();
                //Cerrar Conexion
            $connection->close();

            foreach ($resultado as $key => $value) {
                $prioridad = tblcatprioridades::find()->where(['PK_PRIORIDAD' => $resultado[$key]['FK_PRIORIDAD']])->limit(1)->one();
                if(!empty($resultado[$key]['FK_UBICACION'])){
                    $ubicacion = tblcatubicaciones::find()->where(['PK_UBICACION' => $resultado[$key]['FK_UBICACION']])->limit(1)->one();
                }else{
                    $ubicacion = tblcatubicaciones::find()->where(['PK_UBICACION' => $resultado[$key]['FK_UBICACION_CLIENTE']])->limit(1)->one();
                }
                $estacion = tblcatestacionesvacante::find()->where(['PK_ESTACION_VACANTE' => $resultado[$key]['FK_ESTACION_VACANTE']])->limit(1)->one();
                $estatus = tblcatestatusvacante::find()->where(['PK_ESTATUS_VACANTE' => $resultado[$key]['FK_ESTATUS_VACANTE']])->limit(1)->one();
                if($prioridad){
                    $idprioridad = $prioridad->DESC_PRIORIDAD;
                }else{
                    $idprioridad = 'Sin Definir';
                }

                if($estacion){
                    $idestacion = $estacion->DESC_ESTACION_VACANTE;
                }else{
                    $idestacion = 'Sin Definir';
                }

                if($ubicacion){
                    $idubicacion = $ubicacion->DESC_UBICACION;
                }else{
                    $idubicacion = 'Sin Definir';
                }

                if($estatus){
                    $idestatus = $estatus->DESC_ESTATUS_VACANTE;
                }else{
                    $idestatus = 'Sin Definir';
                }
                $resultado[$key]['FK_PRIORIDAD']=$idprioridad;
                $resultado[$key]['FK_UBICACION']=$idubicacion;
                $resultado[$key]['FK_ESTACION_VACANTE']=$idestacion;
                $resultado[$key]['FK_ESTATUS_VACANTE']=$idestatus;
                $resultado[$key]['FECHA_CIERRE'] = transform_date($resultado[$key]['FECHA_CIERRE'], 'd/m/Y');
                $resultado[$key]['FECHA_CREACION'] = transform_date($resultado[$key]['FECHA_CREACION'], 'd/m/Y');
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $total_paginas = ($todos < 9) ? 1 : ceil($todos / 9);

            $res = array(
                'post'          => $post,
                'pagina'        => $pagina,
                'data'          => $resultado,
                'total_paginas' => $total_paginas,
                'total_registros' => $todos,
                'responsablerh' => $responsablerh,
            );
            return $res;
        }else{
            $catResponsables = (new \yii\db\Query())
                        ->select([
                            'rh.PK_RESPONSABLE_RH',
                            'rh.NOMBRE_RESPONSABLE_RH',
                            ])
                        ->from('tbl_cat_responsables_rh rh')
                        ->join('left join','tbl_usuarios u',
                                'u.PK_USUARIO= rh.FK_USUARIO')
                        ->join('left join', 'tbl_perfil_empleados p',
                                'u.FK_EMPLEADO=p.FK_EMPLEADO')
                        ->andFilterWhere(['and',['p.FK_UNIDAD_NEGOCIO'=> $unidadNegocio]])
                        ->all();
            return $this->render('index4', [
                'catResponsables'=>$catResponsables,

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
        $model->FK_RESPONSABLE_RH    = tblcatresponsablesrh::find()->where(['PK_RESPONSABLE_RH' => $model->FK_RESPONSABLE_RH])->limit(1)->one();
        $model->FK_PRIORIDAD         = tblcatprioridades::find()->where(['PK_PRIORIDAD' => $model->FK_PRIORIDAD])->limit(1)->one();
        $model->FK_AREA              = tblcatareas::find()->where(['PK_AREA' => $model->FK_AREA])->limit(1)->one();
        $model->FK_PUESTO            = tblcatpuestos::find()->where(['PK_PUESTO' => $model->FK_PUESTO])->limit(1)->one();
        $model->FK_NIVEL             = tblcatnivel::find()->where(['PK_NIVEL' => $model->FK_NIVEL])->limit(1)->one();
        $model->FK_TIPO_CONTRATO     = tblcattipocontrato::find()->where(['PK_TIPO_CONTRATO' => $model->FK_TIPO_CONTRATO])->limit(1)->one();
        $model->FK_CLIENTE           = tblclientes::find()->where(['PK_CLIENTE' => $model->FK_CLIENTE])->limit(1)->one();
        $model->FK_WORKSTATION       = tblcatworkstation::find()->where(['PK_WORKSTATION' => $model->FK_WORKSTATION])->limit(1)->one();
        $model->FK_UBICACION         = tblcatubicaciones::find()->where(['PK_UBICACION' => $model->FK_UBICACION])->limit(1)->one();
        $model->FK_UBICACION_CLIENTE = tblcatubicaciones::find()->where(['PK_UBICACION' => $model->FK_UBICACION_CLIENTE])->limit(1)->one();
        $model->FK_DURACION_CONTRATO = tblcatduraciontiposervicios::find()->where(['PK_DURACION' => $model->FK_DURACION_CONTRATO])->limit(1)->one();
        $model->FK_ESTACION_VACANTE  = tblcatestacionesvacante::find()->where(['PK_ESTACION_VACANTE' => $model->FK_ESTACION_VACANTE])->limit(1)->one();
        $model->FK_ESTATUS_VACANTE   = tblcatestatusvacante::find()->where(['PK_ESTATUS_VACANTE' => $model->FK_ESTATUS_VACANTE])->limit(1)->one();
        $model->FK_TIPO_VACANTE      = tblcattipovacante::find()->where(['PK_TIPO_VACANTE' => $model->FK_TIPO_VACANTE])->limit(1)->one();
        $model->FECHA_CIERRE = transform_date($model->FECHA_CIERRE,'d/m/Y');
        $model->FECHA_CREACION = transform_date($model->FECHA_CREACION,'d/m/Y');
        $modelBitComentariosVacantes = TblBitComentariosVacantes::find()->where(['FK_VACANTE' => $id])->count();
        $modelFKUsuario            =(new \yii\db\Query())
                                    ->select("
                                        rh.FK_USUARIO
                                        ")
                                    ->from("tbl_vacantes as v")
                                    ->join('LEFT JOIN','tbl_cat_responsables_rh as rh',
                                        'rh.PK_RESPONSABLE_RH = v.FK_RESPONSABLE_RH')
                                    ->where(['v.PK_VACANTE'=>$id])
                                    ->all();
        $modelSeguimientoRegistros = (new \yii\db\Query())
                                    ->select("
                                        tu.NOMBRE_COMPLETO,
                                        cv.FECHA_REGISTRO,
                                        cv.COMENTARIOS
                                        ")
                                    ->from("tbl_bit_comentarios_vacantes as cv")
                                    ->join('LEFT JOIN','tbl_usuarios as tu',
                                        'cv.FK_USUARIO = tu.PK_USUARIO')
                                    ->where(['FK_VACANTE'=>$id])
                                    ->orderBy('PK_COMENTARIO_VACANTE DESC')
                                    ->all();
        // $model->COSTO_MAXIMO = '$ '.number_format($model->COSTO_MAXIMO,2,'.',',');

        $modelTecnologias= (new \yii\db\Query())
                            ->select(['ct.PK_TECNOLOGIA','ct.DESC_TECNOLOGIA'])
                            ->from('tbl_cat_tecnologias as ct')
                            ->join('INNER JOIN','tbl_vacantes_tecnologias as vt',
                                'ct.PK_TECNOLOGIA = vt.FK_TECNOLOGIA')
                            ->where(['vt.FK_VACANTE'=>$id])
                            ->orderBy('ct.DESC_TECNOLOGIA ASC')
                            ->all();
        //tbl_cat_herramientas
        //tbl_vacantes_herramientas
        $modelHerramientas= (new \yii\db\Query())
                            ->select(['ct.PK_HERRAMIENTA','ct.DESC_HERRAMIENTA'])
                            ->from('tbl_cat_herramientas as ct')
                            ->join('INNER JOIN','tbl_vacantes_herramientas as vt',
                                'ct.PK_HERRAMIENTA = vt.FK_HERRAMIENTA')
                            ->where(['vt.FK_VACANTE'=>$id])
                            ->orderBy('ct.DESC_HERRAMIENTA ASC')
                            ->all();

        //tbl_cat_habilidades
        //tbl_vacantes_habilidades
        $modelHabilidades= (new \yii\db\Query())
                            ->select(['ct.PK_HABILIDAD','ct.DESC_HABILIDAD'])
                            ->from('tbl_cat_habilidades as ct')
                            ->join('INNER JOIN','tbl_vacantes_habilidades as vt',
                                'ct.PK_HABILIDAD = vt.FK_HABILIDAD')
                            ->where(['vt.FK_VACANTE'=>$id])
                            ->orderBy('ct.DESC_HABILIDAD ASC')
                            ->all();

        return $this->render('view', [
            'model' => $model,
            'modelFKUsuario' => $modelFKUsuario,
            'modelTecnologias' => $modelTecnologias,
            'modelHerramientas' => $modelHerramientas,
            'modelHabilidades' => $modelHabilidades,
            'modelBitComentariosVacantes' => $modelBitComentariosVacantes,
            'modelSeguimientoRegistros' => $modelSeguimientoRegistros,
        ]);
    }

    public function actionRegistrar_seguimiento()
    {
        $data = Yii::$app->request->post();
        $modelSeguimiento = new TblBitComentariosVacantes();
        $modelSeguimiento->load(Yii::$app->request->post());
        $modelSeguimiento->FK_VACANTE = $data['FK_VACANTE'];
        $modelSeguimiento->FK_ESTATUS_VACANTE = $data['FK_ESTATUS_VACANTE'];
        $modelSeguimiento->MOTIVO = $data['MOTIVO'];
        $modelSeguimiento->COMENTARIOS = $data['TblVacantes']['COMENTARIOS'];
        $modelSeguimiento->FK_USUARIO = user_info()['PK_USUARIO'];
        $modelSeguimiento->FECHA_REGISTRO = date('Y-m-d H:i:s');
        $modelSeguimiento->save(false);
        return $this->redirect(['view', 'id' => $data['FK_VACANTE']]);

    }

    public function actionViewquery($id)
    {
        $dataProviderC = new ActiveDataProvider([
            'query' => tblcandidatos::find($id),
        ]);

        return $this->render('viewquery', [
            'dataProvider' => $dataProviderC,
        ]);
    }

    /**
     * Creates a new tblvacantes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate()
    {

        //dd(Yii::$app->request->post());
        $model = new tblvacantes();
        $BitVacantes = new TblBitVacantes;
        $BitComentariosVacantes = new TblBitComentariosVacantes;
        $modelTecnologias = new TblVacantesTecnologias();
        $modelHerramientas = new TblVacantesHerramientas();
        $modelHabilidades = new TblVacantesHabilidades();

        if(!isset($model)){
            throw new Exception("Error al obtener Datos de form");
        }
        $fecha_registro =date('Y-m-d');
        if ($model->load(Yii::$app->request->post())) {
                //Se limpia la cadena de tabuladores, saltos de linea y retorno de carro, para que no excede el total en PR.
                $comentariosNoTabs = trim(str_replace("\t","",$model->COMENTARIOS));
                $varComentariosNoSaltos = trim(str_replace("\r"," ",$comentariosNoTabs));
                $varComentariosNoReturn = trim(str_replace("\n","",$varComentariosNoSaltos));
                $varComentariosLimpia = trim($varComentariosNoReturn);

                $funcionesNoTabs = trim(str_replace("\t","",$model->FUNCIONES));
                $varFuncionesNoSaltos = trim(str_replace("\r"," ",$funcionesNoTabs));
                $varFuncionesNoReturn = trim(str_replace("\n","",$varFuncionesNoSaltos));
                $varFuncionesLimpia = trim($varFuncionesNoReturn);

                $model->FECHA_CREACION = $fecha_registro;
                $model->FECHA_CIERRE = transform_date($model->FECHA_CIERRE,'Y-m-d');
                $model->FK_ESTACION_VACANTE = 1;
                $model->FK_ESTATUS_VACANTE = 1;
                $model->ORIGEN_VACANTE = "VACANTES";
                $model->FK_USUARIO = user_info()['PK_USUARIO'];

                $model->COSTO_MAXIMO = quitarFormatoMoneda($model->COSTO_MAXIMO);
                $model->FUNCIONES = $varFuncionesLimpia;
                $model->COMENTARIOS = $varComentariosLimpia;
                $model->save(false);

                $PK_VACANTE=$model->PK_VACANTE;

                $descripcionBitacora = 'PK_VACANTE='.$model->PK_VACANTE.',DESC_VACANTE='.$model->DESC_VACANTE;
                user_log_bitacora($descripcionBitacora,'Alta de vacante ',$model->PK_VACANTE );

                $BitVacantes->FK_VACANTE = $PK_VACANTE;
                $BitVacantes->FK_ESTACION_VACANTE = 1;
                $BitVacantes->FK_ESTATUS_VACANTE = 1;
                $BitVacantes->FK_USUARIO = user_info()['PK_USUARIO'];
                $BitVacantes->FECHA_REGISTRO = $fecha_registro;
                $BitVacantes->CANT_PERSONAS = $model->CANT_PERSONAS;
                $BitVacantes->CANT_HOMBRES = $model->CANT_HOMBRES;
                $BitVacantes->CANT_MUJERES = $model->CANT_MUJERES;
                $BitVacantes->FK_PRIORIDAD = $model->FK_PRIORIDAD;
                $BitVacantes->FK_PUESTO = $model->FK_PUESTO;
                $BitVacantes->FK_NIVEL = $model->FK_NIVEL;
                $BitVacantes->FUNCIONES = $model->FUNCIONES;
                $BitVacantes->FK_TIPO_CONTRATO = $model->FK_TIPO_CONTRATO;
                $BitVacantes->FK_DURACION_CONTRATO = $model->FK_DURACION_CONTRATO;
                $BitVacantes->NOMBRE_PROYECTO = $model->NOMBRE_PROYECTO;
                $BitVacantes->FK_WORKSTATION = $model->FK_WORKSTATION;
                $BitVacantes->FK_TIPO_VACANTE = $model->FK_TIPO_VACANTE;
                $BitVacantes->FK_CLIENTE = $model->FK_CLIENTE;
                $BitVacantes->FK_UBICACION_CLIENTE = $model->FK_UBICACION_CLIENTE;
                if(isset($model->FK_RESPONSABLE_RH)){
                    $BitVacantes->FK_RESPONSABLE_RH = $model->FK_RESPONSABLE_RH;
                }else{
                    $BitVacantes->FK_RESPONSABLE_RH = 0;
                }
                $BitVacantes->save(false);

                $data= Yii::$app->request->post();


                if(!empty($data['Tecnologias'])){
                    foreach ($data['Tecnologias'] as $key => $value) {
                        $modelTecnologias = new TblVacantesTecnologias;
                        $modelTecnologias->FK_VACANTE = $model->PK_VACANTE;
                        $modelTecnologias->FK_TECNOLOGIA = $value;
                        $modelTecnologias->NIVEL_EXPERIENCIA = $data['nivelTech'][$key];
                        $modelTecnologias->TIEMPO_USO = $data['usoTec'][$key];
                        $modelTecnologias->FECHA_REGISTRO = date("Y-m-d");
                        $modelTecnologias->save();
                    }
                }
                    if(!empty($data['Herramientas'])){
                        foreach ($data['Herramientas'] as $key2 => $value2) {
                            $modelHerramientas = new TblVacantesHerramientas;
                            $modelHerramientas->FK_VACANTE = $model->PK_VACANTE;
                            $modelHerramientas->FK_HERRAMIENTA =$value2;
                            $modelHerramientas->NIVEL_EXPERIENCIA = $data['nivelHerr'][$key2];
                            $modelHerramientas->TIEMPO_USO = $data['usoHerr'][$key2];
                            $modelHerramientas->FECHA_REGISTRO = date("Y-m-d");
                            $modelHerramientas->save();
                        }
                    }
                if(!empty($data['Habilidades'])){
                    foreach ($data['Habilidades'] as $key3 => $value3) {
                        $modelHabilidades = new TblVacantesHabilidades;
                        $modelHabilidades->FK_VACANTE = $model->PK_VACANTE;
                        $modelHabilidades->FK_HABILIDAD =$value3;
                        $modelHabilidades->FECHA_REGISTRO = date("Y-m-d");
                        $modelHabilidades->save();
                    }
                }

                //Yii::$app->session->setFlash('success', 'Model has been saved');
                //return $this->redirect(['view', 'id' => $model->PK_VACANTE]);
                return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'modelTecnologias' => $modelTecnologias,
                'modelHerramientas' => $modelHerramientas,
                'BitComentariosVacantes' => $BitComentariosVacantes,
                'modelHabilidades' => $modelHabilidades,
                'BitVacantes' => $BitVacantes,
            ]);
        }
    }

    /**
     * Creates a new tblvacantes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate2()
    {
        $model = new tblvacantes();


        if ($model->load(Yii::$app->request->post())) {
                $model->save(false);
                Yii::$app->session->setFlash('success', 'Model has been saved');
                return $this->redirect(['update3', 'id' => $model->PK_VACANTE]);
        } else {
            return $this->render('update2', [
                'model' => $model,
            ]);
        }
    }

/**
     * Creates a new tblvacantes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate3()
    {
        $model = new tblvacantes();

        if ($model->load(Yii::$app->request->post())) {
                $model->save(false);

                Yii::$app->session->setFlash('success', 'Model has been saved');
                return $this->redirect(['index', 'id' => $model->PK_VACANTE]);
        } else {
            return $this->render('update3', [
                'model' => $model,

            ]);
        }
    }

    /**
     * Updates an existing tblvacantes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            foreach ($data['id'] as $key => $value) {
              $model = $this->findModel($value);
              $BitComentariosVacantes = new TblBitComentariosVacantes;
              $BitVacantes = new TblBitVacantes;

              $modelAsignaciones = TblAsignaciones::find()->where(['PK_ASIGNACION' => $model->FK_ASIGNACION])->one();
              $modelVacantes = tblvacantes::find()->where(['PK_VACANTE' => $model->PK_VACANTE])->asArray()->all();
              // este campo se utiliza unicamente para saber si existe un candidato en estacion ingreso estatus contratado, para que en el listado de estatus
              // aparesca en el listadode estatus completado
              $modelVacantesCandidatos = \app\models\TblVacantesCandidatos::find()->where([
                  "FK_VACANTE" => $model->PK_VACANTE,
                  "FK_ESTACION_ACTUAL_CANDIDATO" => 5,
                  "FK_ESTATUS_ACTUAL_CANDIDATO" => 3
              ])->one();

              $model->FECHA_CIERRE = transform_date($model->FECHA_CIERRE,'Y-m-d');
              $fechaCierre = $model->FECHA_CIERRE; //Se agrego para prueba

            // $data = Yii::$app->request->post();
              //Se limpia la cadena de tabuladores, saltos de linea y retorno de carro, para que no excede el total en PR.
              $comentariosNoTabs = trim(str_replace("\t","",$model->COMENTARIOS));
              $varComentariosNoSaltos = trim(str_replace("\r"," ",$comentariosNoTabs));
              $varComentariosNoReturn = trim(str_replace("\n","",$varComentariosNoSaltos));
              $varComentariosLimpia = trim($varComentariosNoReturn);

              $funcionesNoTabs = trim(str_replace("\t","",$model->FUNCIONES));
              $varFuncionesNoSaltos = trim(str_replace("\r"," ",$funcionesNoTabs));
              $varFuncionesNoReturn = trim(str_replace("\n","",$varFuncionesNoSaltos));
              $varFuncionesLimpia = trim($varFuncionesNoReturn);

              $model->COSTO_MAXIMO = quitarFormatoMoneda($model->COSTO_MAXIMO);
              $model->FUNCIONES = $varFuncionesLimpia;
              $model->COMENTARIOS = $varComentariosLimpia;

              $model->FK_RESPONSABLE_RH=$data['id_RH'];
              $model->FK_ESTACION_VACANTE=$data['id_estacion'];
              $model->FK_ESTATUS_VACANTE=$data['id_estatus'];
              $descripcionBitacora = 'Actualizada ='.$model->PK_VACANTE.' '.'NOMBRE ='.$model->DESC_VACANTE.' '.'FK_ASIGNACION ='.' '.$model->FK_ASIGNACION;
              user_log_bitacora($descripcionBitacora,'Vacante Actualizada',$model->PK_VACANTE);
              $model->save(false);

                  $estacion = tblcatestacionesvacante::find()->where(['PK_ESTACION_VACANTE' => $model->FK_ESTACION_VACANTE])->limit(1)->one();
                  $estatus = tblcatestatusvacante::find()->where(['PK_ESTATUS_VACANTE' => $model->FK_ESTATUS_VACANTE])->limit(1)->one();

                  if($estacion){
                      $idestacion = $estacion->DESC_ESTACION_VACANTE;
                  }else{
                      $idestacion = 'Sin Definir';
                  }

                  if($estatus){
                      $idestatus = $estatus->DESC_ESTATUS_VACANTE;
                  }else{
                      $idestatus = 'Sin Definir';
                  }

                  $model->FK_ESTACION_VACANTE=$idestacion;
                  $model->FK_ESTATUS_VACANTE=$idestatus;
                  \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                  var_dump($model);
            }
        }else{
            $model = $this->findModel($id);
            $BitComentariosVacantes = new TblBitComentariosVacantes;
            $BitVacantes = new TblBitVacantes;

            $modelAsignaciones = TblAsignaciones::find()->where(['PK_ASIGNACION' => $model->FK_ASIGNACION])->one();
            $modelVacantes = tblvacantes::find()->where(['PK_VACANTE' => $model->PK_VACANTE])->asArray()->all();
            // este campo se utiliza unicamente para saber si existe un candidato en estacion ingreso estatus contratado, para que en el listado de estatus
            // aparesca en el listadode estatus completado
            $modelVacantesCandidatos = \app\models\TblVacantesCandidatos::find()->where([
                "FK_VACANTE" => $model->PK_VACANTE,
                "FK_ESTACION_ACTUAL_CANDIDATO" => 5,
                "FK_ESTATUS_ACTUAL_CANDIDATO" => 3
            ])->one();

            $model->FECHA_CIERRE = transform_date($model->FECHA_CIERRE,'Y-m-d');
            $fechaCierre = $model->FECHA_CIERRE; //Se agrego para prueba


            $estatusAnt=$model->FK_ESTATUS_VACANTE;
            $data= Yii::$app->request->post();
            if ($model->load(Yii::$app->request->post())) {
                //Se limpia la cadena de tabuladores, saltos de linea y retorno de carro, para que no excede el total en PR.
                $comentariosNoTabs = trim(str_replace("\t","",$model->COMENTARIOS));
                $varComentariosNoSaltos = trim(str_replace("\r"," ",$comentariosNoTabs));
                $varComentariosNoReturn = trim(str_replace("\n","",$varComentariosNoSaltos));
                $varComentariosLimpia = trim($varComentariosNoReturn);

                $funcionesNoTabs = trim(str_replace("\t","",$model->FUNCIONES));
                $varFuncionesNoSaltos = trim(str_replace("\r"," ",$funcionesNoTabs));
                $varFuncionesNoReturn = trim(str_replace("\n","",$varFuncionesNoSaltos));
                $varFuncionesLimpia = trim($varFuncionesNoReturn);

                $model->COSTO_MAXIMO = quitarFormatoMoneda($model->COSTO_MAXIMO);
                $model->FUNCIONES = $varFuncionesLimpia;
                $model->COMENTARIOS = $varComentariosLimpia;

                $model->FECHA_CIERRE = transform_date($model->FECHA_CIERRE,'Y-m-d');

                if(!$model->FK_ESTATUS_VACANTE){
                    $model->FK_ESTATUS_VACANTE=$estatusAnt;
                }

                // Estatus que se puede modificar de manera manual, cuando uno de los candidatos se encuentre con Estatus "Aceptada" en la Estación Contratación. Y el cliente/solicitante confirme que no es necesario cubrir en su totalidad la cantidad de personas solicitadas en el registro de la Vacante
                if($model->FK_ESTACION_VACANTE == 4 && $data["TblVacantes"]["FK_ESTATUS_VACANTE"] == 7)
                {
                    $model->FK_ESTACION_VACANTE = 5;
                }
                $descripcionBitacora = 'VACANTE ACTUALIZADA ='.$model->PK_VACANTE.' '.'NOMBRE ='.$model->DESC_VACANTE.' '.'FK_ASIGNACION ='.' '.$model->FK_ASIGNACION;
                user_log_bitacora($descripcionBitacora,'Vacante ACTUALIZADA',$model->PK_VACANTE);
                $model->save(false);

                 if ($model->FK_ESTATUS_VACANTE == 4 && $estatusAnt != $model->FK_ESTATUS_VACANTE)
                 {

                $BitComentariosVacantes->load(Yii::$app->request->post());
                $BitComentariosVacantes->FK_ESTATUS_VACANTE = $model->FK_ESTATUS_VACANTE;
                $BitComentariosVacantes->FECHA_REGISTRO = transform_date($BitComentariosVacantes->FECHA_REGISTRO,'Y-m-d');
                $BitComentariosVacantes->save(false);

                // MODIFICACION PARA SOLUCIONAR BUG NO SIEMPRE EXISTE UNA ASIGNACION
                if(isset($modelAsignaciones->FK_ESTATUS_ASIGNACION))
                {
                    $estatuscancelado = 5;
                    $modelAsignaciones->FK_ESTATUS_ASIGNACION= $estatuscancelado;
                    $modelAsignaciones->save(false);
                }

                $descripcionBitacora = 'VACANTE CANCELADA ='.$model->PK_VACANTE.' '.'NOMBRE ='.$model->DESC_VACANTE.' '.'FK_ASIGNACION ='.' '.$model->FK_ASIGNACION;
                user_log_bitacora($descripcionBitacora,'Vacante Cancelada',$model->PK_VACANTE);
                 }

                $fecha_registro =date('Y-m-d');
                \Yii::$app
                  ->db
                  ->createCommand()
                  ->delete('tbl_vacantes_tecnologias', ['FK_VACANTE' => $model->PK_VACANTE])
                  ->execute();

                  if(!empty($data['Tecnologias'])){
                      foreach ($data['Tecnologias'] as $key => $value) {
                          $modelTecnologia= new TblVacantesTecnologias;
                          $modelTecnologia->FK_VACANTE = $model->PK_VACANTE;
                          $modelTecnologia->FK_TECNOLOGIA = $value;
                          $modelTecnologia->NIVEL_EXPERIENCIA = $data['nivelTech'][$key];
                          $modelTecnologia->TIEMPO_USO = $data['usoTec'][$key];
                          $modelTecnologia->FECHA_REGISTRO = $fecha_registro;
                          $modelTecnologia->save();
                      }
                  }

                  \Yii::$app
                      ->db
                      ->createCommand()
                      ->delete('tbl_vacantes_herramientas', ['FK_VACANTE' => $model->PK_VACANTE])
                      ->execute();

                      if(!empty($data['Herramientas'])){
                          foreach ($data['Herramientas'] as $key2 => $value2) {
                              $modelHerramientas= new TblVacantesHerramientas;
                              $modelHerramientas->FK_VACANTE = $model->PK_VACANTE;
                              $modelHerramientas->FK_HERRAMIENTA =$value2;
                              $modelHerramientas->NIVEL_EXPERIENCIA = $data['nivelHerr'][$key2];
                              $modelHerramientas->TIEMPO_USO = $data['usoHerr'][$key2];
                              $modelHerramientas->FECHA_REGISTRO = $fecha_registro;
                              $modelHerramientas->save();
                          }
                      }

                  \Yii::$app
                      ->db
                      ->createCommand()
                      ->delete('tbl_vacantes_habilidades', ['FK_VACANTE' => $model->PK_VACANTE])
                      ->execute();

                  if(!empty($data['Habilidades'])){

                      foreach ($data['Habilidades'] as $key3 => $value3) {
                          $modelHabilidades= new TblVacantesHabilidades;
                          $modelHabilidades->FK_VACANTE = $model->PK_VACANTE;
                          $modelHabilidades->FK_HABILIDAD =$value3;
                          $modelHabilidades->FECHA_REGISTRO = $fecha_registro;
                          $modelHabilidades->save();
                      }
                  }

                $BitVacantes->FK_VACANTE = $model->PK_VACANTE;
                $BitVacantes->FK_ESTACION_VACANTE = $model->FK_ESTACION_VACANTE;
                $BitVacantes->FK_ESTATUS_VACANTE = $model->FK_ESTATUS_VACANTE;
                $BitVacantes->FK_USUARIO = user_info()['PK_USUARIO'];
                $BitVacantes->FECHA_REGISTRO = date('Y-m-d');
                $BitVacantes->FK_RESPONSABLE_RH = $model->FK_RESPONSABLE_RH;
                $BitVacantes->CANT_PERSONAS = $model->CANT_PERSONAS;
                $BitVacantes->CANT_HOMBRES = $model->CANT_HOMBRES;
                $BitVacantes->CANT_MUJERES = $model->CANT_MUJERES;
                $BitVacantes->FK_PRIORIDAD = $model->FK_PRIORIDAD;
                $BitVacantes->FK_PUESTO = $model->FK_PUESTO;
                $BitVacantes->FK_NIVEL = $model->FK_NIVEL;
                $BitVacantes->FUNCIONES = $model->FUNCIONES;
                $BitVacantes->FK_TIPO_CONTRATO = $model->FK_TIPO_CONTRATO;
                $BitVacantes->FK_DURACION_CONTRATO = $model->FK_DURACION_CONTRATO;
                $BitVacantes->NOMBRE_PROYECTO = $model->NOMBRE_PROYECTO;
                $BitVacantes->FK_WORKSTATION = $model->FK_WORKSTATION;
                $BitVacantes->FK_TIPO_VACANTE = $model->FK_TIPO_VACANTE;
                $BitVacantes->FK_CLIENTE = $model->FK_CLIENTE;
                $BitVacantes->FK_UBICACION_CLIENTE = $model->FK_UBICACION_CLIENTE;
                $BitVacantes->save(false);

                Yii::$app->session->setFlash('success', 'Model has been saved');
                //return $this->redirect(['view', 'id' => $model->PK_VACANTE]);
                return $this->redirect(['index']);
            } else {

              $modelTecnologias = (new \yii\db\Query())
                                   ->select([
                                     'vt.FK_TECNOLOGIA',
                                     'vt.TIEMPO_USO',
                                     'vt.NIVEL_EXPERIENCIA'
                                   ])
                                    ->from('tbl_vacantes_tecnologias vt')
                                    ->join('left join', 'tbl_cat_tecnologias t',
                                            'vt.FK_TECNOLOGIA = t.PK_TECNOLOGIA')
                                    ->where(['FK_VACANTE'=>$model->PK_VACANTE])
                                    ->orderBy(['t.DESC_TECNOLOGIA' => SORT_ASC])
                                    ->all();
              $modelHerramientas = (new \yii\db\Query())
                                 ->select([
                                   'vh.NIVEL_EXPERIENCIA',
                                   'vh.TIEMPO_USO',
                                   'vh.FK_HERRAMIENTA'
                                 ])
                                 ->from('tbl_vacantes_herramientas vh')
                                 ->join('left join', 'tbl_cat_herramientas h',
                                         'vh.FK_HERRAMIENTA = h.PK_HERRAMIENTA')
                                 ->where(['FK_VACANTE'=>$model->PK_VACANTE])
                                 ->orderBy(['h.DESC_HERRAMIENTA' => SORT_ASC])
                                 ->all();
              // $modelHabilidades = (new \yii\db\Query())
              //                     ->select(['FK_HABILIDAD','FK_VACANTE'])
              //                     ->from('tbl_vacantes_habilidades')
              //                     ->where(['FK_VACANTE'=>$model->PK_VACANTE])
              //                     ->all();

            //$modelTecnologias = TblVacantesTecnologias::find()->select('FK_TECNOLOGIA, NIVEL_EXPERIENCIA, TIEMPO_USO')->where(['FK_VACANTE'=>$model->PK_VACANTE])->asArray()->all();
            //$modelHerramientas = TblVacantesHerramientas::find()->select('FK_HERRAMIENTA, NIVEL_EXPERIENCIA, TIEMPO_USO')->where(['FK_VACANTE'=>$model->PK_VACANTE])->asArray()->all();
            $modelHabilidades = TblVacantesHabilidades::find()->select('FK_HABILIDAD')->where(['FK_VACANTE'=>$model->PK_VACANTE])->asArray()->column();
            return $this->render('update', [
                'model' => $model,
                'modelTecnologias' => $modelTecnologias,
                'modelHerramientas' => $modelHerramientas,
                'modelHabilidades' => $modelHabilidades,
                'BitComentariosVacantes' => $BitComentariosVacantes,
                'modelVacantesCandidatos' => $modelVacantesCandidatos,
                'fechaCierre' => $fechaCierre, //Se agrego para prueba
                'id' => $model->PK_VACANTE,
            ]);
            }
        }
    }



    public function actionStatus($id)
    {

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            foreach ($data['id'] as $key => $value) {
              $model = $this->findModel($value);
              $BitComentariosVacantes = new TblBitComentariosVacantes;
              $BitVacantes = new TblBitVacantes;
              $modelProspectos = new tblProspectos();

              $modelAsignaciones = TblAsignaciones::find()->where(['PK_ASIGNACION' => $model->FK_ASIGNACION])->one();
              $modelVacantes = tblvacantes::find()->where(['PK_VACANTE' => $model->PK_VACANTE])->asArray()->all();
              // este campo se utiliza unicamente para saber si existe un candidato en estacion ingreso estatus contratado, para que en el listado de estatus
              // aparesca en el listadode estatus completado
              $modelVacantesCandidatos = \app\models\TblVacantesCandidatos::find()->where([
                  "FK_VACANTE" => $model->PK_VACANTE,
                  "FK_ESTACION_ACTUAL_CANDIDATO" => 5,
                  "FK_ESTATUS_ACTUAL_CANDIDATO" => 3
              ])->one();

              $model->FECHA_CIERRE = transform_date($model->FECHA_CIERRE,'Y-m-d');
              $fechaCierre = $model->FECHA_CIERRE; //Se agrego para prueba

            // $data = Yii::$app->request->post();
              //Se limpia la cadena de tabuladores, saltos de linea y retorno de carro, para que no excede el total en PR.
              $comentariosNoTabs = trim(str_replace("\t","",$model->COMENTARIOS));
              $varComentariosNoSaltos = trim(str_replace("\r"," ",$comentariosNoTabs));
              $varComentariosNoReturn = trim(str_replace("\n","",$varComentariosNoSaltos));
              $varComentariosLimpia = trim($varComentariosNoReturn);

              $funcionesNoTabs = trim(str_replace("\t","",$model->FUNCIONES));
              $varFuncionesNoSaltos = trim(str_replace("\r"," ",$funcionesNoTabs));
              $varFuncionesNoReturn = trim(str_replace("\n","",$varFuncionesNoSaltos));
              $varFuncionesLimpia = trim($varFuncionesNoReturn);

              $model->FUNCIONES = $varFuncionesLimpia;
              $model->COMENTARIOS = $varComentariosLimpia;

              $model->FK_ESTATUS_VACANTE=$data['id_estatus'];

              //Busqueda de los demas candidatos asociados a la vacante detenida, cancelada o perdida

                        if($model->FK_ESTATUS_VACANTE == 3){
                          $CandidatosEnVacante = tblvacantescandidatos::find()->where(['=','FK_VACANTE',$model->PK_VACANTE])->andWhere(['<>','FK_ESTATUS_ACTUAL_CANDIDATO',3])->andWhere(['<>','FK_ESTATUS_ACTUAL_CANDIDATO',4])->andWhere(['!=','FK_ESTATUS_ACTUAL_CANDIDATO',5])->all();
                        }else if($model->FK_ESTATUS_VACANTE == 4 || $model->FK_ESTATUS_VACANTE == 5){
                          $CandidatosEnVacante = tblvacantescandidatos::find()->where(['=','FK_VACANTE',$model->PK_VACANTE])->andWhere(['<>','FK_ESTATUS_ACTUAL_CANDIDATO',4])->andWhere(['!=','FK_ESTATUS_ACTUAL_CANDIDATO',5])->all();
                        }

                        if($model->FK_ESTATUS_VACANTE != 6){
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

                          if($model->FK_ESTATUS_VACANTE == 3 || $model->FK_ESTATUS_VACANTE == 4 || $model->FK_ESTATUS_VACANTE == 5 && count($modeloCandidatoOtraVacante) == 0){

                            $modeloCandidatosVacante->FK_ESTACION_ACTUAL_CANDIDATO = 5;
                            $modeloCandidatosVacante->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                            $modeloCandidatosVacante->FECHA_ACTUALIZACION = date('Y-m-d');
                            $modeloCandidatosVacante->save(false);

                            $modelBitacoraCV->FK_VACANTE = $valuecev->FK_VACANTE;
                            $modelBitacoraCV->FK_CANDIDATO = $valuecev->FK_CANDIDATO;
                            $modelBitacoraCV->FK_ESTACION_CANDIDATO = $valuecev->FK_ESTACION_ACTUAL_CANDIDATO;
                            $modelBitacoraCV->FK_ESTATUS_CANDIDATO = $valuecev->FK_ESTATUS_ACTUAL_CANDIDATO;
                            $modelBitacoraCV->FK_USUARIO = 1;
                            $modelBitacoraCV->COMENTARIOS = 'CANDIDATO CANCELADO DEBIDO A QUE LA VACANTE SE DETUVO, CANCELO O PERDIO';
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

                          }else if($model->FK_ESTATUS_VACANTE == 3 || $model->FK_ESTATUS_VACANTE == 4 || $model->FK_ESTATUS_VACANTE == 5 && count($CandidatosEnVacante) != 0){

                                $modeloCandidatosVacante->FK_ESTATUS_ACTUAL_CANDIDATO = 5;
                                $modeloCandidatosVacante->FECHA_ACTUALIZACION = date('Y-m-d');
                                $modeloCandidatosVacante->save(false);

                                $modelBitacoraCV->FK_VACANTE = $valuecev->FK_VACANTE;
                                $modelBitacoraCV->FK_CANDIDATO = $valuecev->FK_CANDIDATO;
                                $modelBitacoraCV->FK_ESTACION_CANDIDATO = $valuecev->FK_ESTACION_ACTUAL_CANDIDATO;
                                $modelBitacoraCV->FK_ESTATUS_CANDIDATO = $valuecev->FK_ESTATUS_ACTUAL_CANDIDATO;
                                $modelBitacoraCV->FK_USUARIO = 1;
                                $modelBitacoraCV->COMENTARIOS = 'CANDIDATO CANCELADO DEBIDO A QUE LA VACANTE SE DETUVO, CANCELO O PERDIO';
                                $modelBitacoraCV->FECHA_REGISTRO =  date('Y-m-d');
                                $modelBitacoraCV->save(false);
                            }
                          }
                        }

              $descripcionBitacora = 'Actualizada ='.$model->PK_VACANTE.' '.'NOMBRE ='.$model->DESC_VACANTE.' '.'FK_ASIGNACION ='.' '.$model->FK_ASIGNACION;
              user_log_bitacora($descripcionBitacora,'Vacante Actualizada',$model->PK_VACANTE);
              $model->save(false);

                  $estatus = tblcatestatusvacante::find()->where(['PK_ESTATUS_VACANTE' => $model->FK_ESTATUS_VACANTE])->limit(1)->one();

                  if($estatus){
                      $idestatus = $estatus->DESC_ESTATUS_VACANTE;
                  }else{
                      $idestatus = 'Sin Definir';
                  }

                  $model->FK_ESTATUS_VACANTE=$idestatus;
                  \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                  var_dump($model);
            }
        }else{
            $model = $this->findModel($id);
            $BitComentariosVacantes = new TblBitComentariosVacantes;
            $BitVacantes = new TblBitVacantes;

            $modelAsignaciones = TblAsignaciones::find()->where(['PK_ASIGNACION' => $model->FK_ASIGNACION])->one();
            $modelVacantes = tblvacantes::find()->where(['PK_VACANTE' => $model->PK_VACANTE])->asArray()->all();
            // este campo se utiliza unicamente para saber si existe un candidato en estacion ingreso estatus contratado, para que en el listado de estatus
            // aparesca en el listadode estatus completado
            $modelVacantesCandidatos = \app\models\TblVacantesCandidatos::find()->where([
                "FK_VACANTE" => $model->PK_VACANTE,
                "FK_ESTACION_ACTUAL_CANDIDATO" => 5,
                "FK_ESTATUS_ACTUAL_CANDIDATO" => 3
            ])->one();

            $model->FECHA_CIERRE = transform_date($model->FECHA_CIERRE,'Y-m-d');
            $fechaCierre = $model->FECHA_CIERRE; //Se agrego para prueba


            $estatusAnt=$model->FK_ESTATUS_VACANTE;
            $data= Yii::$app->request->post();
            if ($model->load(Yii::$app->request->post())) {
                //Se limpia la cadena de tabuladores, saltos de linea y retorno de carro, para que no excede el total en PR.
                $comentariosNoTabs = trim(str_replace("\t","",$model->COMENTARIOS));
                $varComentariosNoSaltos = trim(str_replace("\r"," ",$comentariosNoTabs));
                $varComentariosNoReturn = trim(str_replace("\n","",$varComentariosNoSaltos));
                $varComentariosLimpia = trim($varComentariosNoReturn);

                $funcionesNoTabs = trim(str_replace("\t","",$model->FUNCIONES));
                $varFuncionesNoSaltos = trim(str_replace("\r"," ",$funcionesNoTabs));
                $varFuncionesNoReturn = trim(str_replace("\n","",$varFuncionesNoSaltos));
                $varFuncionesLimpia = trim($varFuncionesNoReturn);

                $model->FUNCIONES = $varFuncionesLimpia;
                $model->COMENTARIOS = $varComentariosLimpia;

                $model->FECHA_CIERRE = transform_date($model->FECHA_CIERRE,'Y-m-d');

                if(!$model->FK_ESTATUS_VACANTE){
                    $model->FK_ESTATUS_VACANTE=$estatusAnt;
                }

                // Estatus que se puede modificar de manera manual, cuando uno de los candidatos se encuentre con Estatus "Aceptada" en la Estación Contratación. Y el cliente/solicitante confirme que no es necesario cubrir en su totalidad la cantidad de personas solicitadas en el registro de la Vacante
                if($model->FK_ESTACION_VACANTE == 4 && $data["TblVacantes"]["FK_ESTATUS_VACANTE"] == 7)
                {
                    $model->FK_ESTACION_VACANTE = 5;
                }
                $descripcionBitacora = 'VACANTE ACTUALIZADA ='.$model->PK_VACANTE.' '.'NOMBRE ='.$model->DESC_VACANTE.' '.'FK_ASIGNACION ='.' '.$model->FK_ASIGNACION;
                user_log_bitacora($descripcionBitacora,'Vacante ACTUALIZADA',$model->PK_VACANTE);
                $model->save(false);

                 if ($model->FK_ESTATUS_VACANTE == 4 && $estatusAnt != $model->FK_ESTATUS_VACANTE)
                 {

                $BitComentariosVacantes->load(Yii::$app->request->post());
                $BitComentariosVacantes->FK_ESTATUS_VACANTE = $model->FK_ESTATUS_VACANTE;
                $BitComentariosVacantes->FECHA_REGISTRO = transform_date($BitComentariosVacantes->FECHA_REGISTRO,'Y-m-d');
                $BitComentariosVacantes->save(false);

                // MODIFICACION PARA SOLUCIONAR BUG NO SIEMPRE EXISTE UNA ASIGNACION
                if(isset($modelAsignaciones->FK_ESTATUS_ASIGNACION))
                {
                    $estatuscancelado = 5;
                    $modelAsignaciones->FK_ESTATUS_ASIGNACION= $estatuscancelado;
                    $modelAsignaciones->save(false);
                }

                $descripcionBitacora = 'VACANTE CANCELADA ='.$model->PK_VACANTE.' '.'NOMBRE ='.$model->DESC_VACANTE.' '.'FK_ASIGNACION ='.' '.$model->FK_ASIGNACION;
                user_log_bitacora($descripcionBitacora,'Vacante Cancelada',$model->PK_VACANTE);
                 }

                $fecha_registro =date('Y-m-d');
                \Yii::$app
                  ->db
                  ->createCommand()
                  ->delete('tbl_vacantes_tecnologias', ['FK_VACANTE' => $model->PK_VACANTE])
                  ->execute();

                  if(!empty($data['Tecnologias'])){
                      foreach ($data['Tecnologias'] as $key => $value) {
                          $modelTecnologia= new TblVacantesTecnologias;
                          $modelTecnologia->FK_VACANTE = $model->PK_VACANTE;
                          $modelTecnologia->FK_TECNOLOGIA = $value;
                          $modelTecnologia->NIVEL_EXPERIENCIA = $data['nivelTech'][$key];
                          $modelTecnologia->TIEMPO_USO = $data['usoTec'][$key];
                          $modelTecnologia->FECHA_REGISTRO = $fecha_registro;
                          $modelTecnologia->save();
                      }
                  }

                  \Yii::$app
                      ->db
                      ->createCommand()
                      ->delete('tbl_vacantes_herramientas', ['FK_VACANTE' => $model->PK_VACANTE])
                      ->execute();

                      if(!empty($data['Herramientas'])){
                          foreach ($data['Herramientas'] as $key2 => $value2) {
                              $modelHerramientas= new TblVacantesHerramientas;
                              $modelHerramientas->FK_VACANTE = $model->PK_VACANTE;
                              $modelHerramientas->FK_HERRAMIENTA =$value2;
                              $modelHerramientas->NIVEL_EXPERIENCIA = $data['nivelHerr'][$key2];
                              $modelHerramientas->TIEMPO_USO = $data['usoHerr'][$key2];
                              $modelHerramientas->FECHA_REGISTRO = $fecha_registro;
                              $modelHerramientas->save();
                          }
                      }

                  \Yii::$app
                      ->db
                      ->createCommand()
                      ->delete('tbl_vacantes_habilidades', ['FK_VACANTE' => $model->PK_VACANTE])
                      ->execute();

                  if(!empty($data['Habilidades'])){

                      foreach ($data['Habilidades'] as $key3 => $value3) {
                          $modelHabilidades= new TblVacantesHabilidades;
                          $modelHabilidades->FK_VACANTE = $model->PK_VACANTE;
                          $modelHabilidades->FK_HABILIDAD =$value3;
                          $modelHabilidades->FECHA_REGISTRO = $fecha_registro;
                          $modelHabilidades->save();
                      }
                  }

                $BitVacantes->FK_VACANTE = $model->PK_VACANTE;
                $BitVacantes->FK_ESTACION_VACANTE = $model->FK_ESTACION_VACANTE;
                $BitVacantes->FK_ESTATUS_VACANTE = $model->FK_ESTATUS_VACANTE;
                $BitVacantes->FK_USUARIO = user_info()['PK_USUARIO'];
                $BitVacantes->FECHA_REGISTRO = date('Y-m-d');
                $BitVacantes->FK_RESPONSABLE_RH = $model->FK_RESPONSABLE_RH;
                $BitVacantes->CANT_PERSONAS = $model->CANT_PERSONAS;
                $BitVacantes->CANT_HOMBRES = $model->CANT_HOMBRES;
                $BitVacantes->CANT_MUJERES = $model->CANT_MUJERES;
                $BitVacantes->FK_PRIORIDAD = $model->FK_PRIORIDAD;
                $BitVacantes->FK_PUESTO = $model->FK_PUESTO;
                $BitVacantes->FK_NIVEL = $model->FK_NIVEL;
                $BitVacantes->FUNCIONES = $model->FUNCIONES;
                $BitVacantes->FK_TIPO_CONTRATO = $model->FK_TIPO_CONTRATO;
                $BitVacantes->FK_DURACION_CONTRATO = $model->FK_DURACION_CONTRATO;
                $BitVacantes->NOMBRE_PROYECTO = $model->NOMBRE_PROYECTO;
                $BitVacantes->FK_WORKSTATION = $model->FK_WORKSTATION;
                $BitVacantes->FK_TIPO_VACANTE = $model->FK_TIPO_VACANTE;
                $BitVacantes->FK_CLIENTE = $model->FK_CLIENTE;
                $BitVacantes->FK_UBICACION_CLIENTE = $model->FK_UBICACION_CLIENTE;
                $BitVacantes->save(false);

                Yii::$app->session->setFlash('success', 'Model has been saved');
                //return $this->redirect(['view', 'id' => $model->PK_VACANTE]);
                return $this->redirect(['index']);
            } else {

              $modelTecnologias = (new \yii\db\Query())
                                   ->select([
                                     'vt.FK_TECNOLOGIA',
                                     'vt.TIEMPO_USO',
                                     'vt.NIVEL_EXPERIENCIA'
                                   ])
                                    ->from('tbl_vacantes_tecnologias vt')
                                    ->join('left join', 'tbl_cat_tecnologias t',
                                            'vt.FK_TECNOLOGIA = t.PK_TECNOLOGIA')
                                    ->where(['FK_VACANTE'=>$model->PK_VACANTE])
                                    ->orderBy(['t.DESC_TECNOLOGIA' => SORT_ASC])
                                    ->all();
              $modelHerramientas = (new \yii\db\Query())
                                 ->select([
                                   'vh.NIVEL_EXPERIENCIA',
                                   'vh.TIEMPO_USO',
                                   'vh.FK_HERRAMIENTA'
                                 ])
                                 ->from('tbl_vacantes_herramientas vh')
                                 ->join('left join', 'tbl_cat_herramientas h',
                                         'vh.FK_HERRAMIENTA = h.PK_HERRAMIENTA')
                                 ->where(['FK_VACANTE'=>$model->PK_VACANTE])
                                 ->orderBy(['h.DESC_HERRAMIENTA' => SORT_ASC])
                                 ->all();
              // $modelHabilidades = (new \yii\db\Query())
              //                     ->select(['FK_HABILIDAD','FK_VACANTE'])
              //                     ->from('tbl_vacantes_habilidades')
              //                     ->where(['FK_VACANTE'=>$model->PK_VACANTE])
              //                     ->all();

            //$modelTecnologias = TblVacantesTecnologias::find()->select('FK_TECNOLOGIA, NIVEL_EXPERIENCIA, TIEMPO_USO')->where(['FK_VACANTE'=>$model->PK_VACANTE])->asArray()->all();
            //$modelHerramientas = TblVacantesHerramientas::find()->select('FK_HERRAMIENTA, NIVEL_EXPERIENCIA, TIEMPO_USO')->where(['FK_VACANTE'=>$model->PK_VACANTE])->asArray()->all();
            $modelHabilidades = TblVacantesHabilidades::find()->select('FK_HABILIDAD')->where(['FK_VACANTE'=>$model->PK_VACANTE])->asArray()->column();
            return $this->render('update', [
                'model' => $model,
                'modelTecnologias' => $modelTecnologias,
                'modelHerramientas' => $modelHerramientas,
                'modelHabilidades' => $modelHabilidades,
                'BitComentariosVacantes' => $BitComentariosVacantes,
                'modelVacantesCandidatos' => $modelVacantesCandidatos,
                'fechaCierre' => $fechaCierre, //Se agrego para prueba
                'id' => $model->PK_VACANTE,
            ]);
            }
        }
    }

/**
     * Updates an existing tblvacantes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate2($id)
    {
        $model = $this->findModel($id);
         $modelHabilidades = new tblvacantehabilidades();

         if ($model->load(Yii::$app->request->post()) && $modelHabilidades->load(Yii::$app->request->post())) {
                $model->save(false);
                //$modelHabilidades->save(false);
                Yii::$app->session->setFlash('success', 'Model has been saved');
                return $this->redirect(['update3', 'id' => $model->PK_VACANTE]);
        } else {
           $modelHabilidades = new tblvacantehabilidades();
            return $this->render('update2', [
                'model' => $model,
                'modelHabilidades' => $modelHabilidades,
                'id' => $model->PK_VACANTE,
            ]);
        }
    }


/**
     * Updates an existing tblvacantes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate3($id)
    {
        $model = $this->findModel($id);

         if ($model->load(Yii::$app->request->post())) {
                $model->save(false);
                Yii::$app->session->setFlash('success', 'Model has been saved');
                return $this->redirect(['index', 'id' => $model->PK_VACANTE]);
        } else {
            return $this->render('update3', [
                'model' => $model,
                'id' => $model->PK_VACANTE,
            ]);
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

    //datos ajaxCandidatosAplicando
    public function actionCandidatos_aplicando(){
        if (Yii::$app->request->isAjax) { // Si la peticion viene de ajax
            $data = Yii::$app->request->post();
            $dataProvider = (new \yii\db\Query())
            ->select([
                    'v.PK_VACANTE',
                    'candi.PK_CANDIDATO',
                    'CONCAT(candi.NOMBRE," ",candi.APELLIDO_PATERNO," ",candi.APELLIDO_MATERNO) AS CANDIDATO',
                    'est.DESC_ESTATUS_CANDIDATO'
                  ])
                  ->from('tbl_vacantes AS v')
                  ->join('INNER JOIN', 'tbl_vacantes_candidatos vc',
                          'vc.FK_VACANTE=v.PK_VACANTE')
                  ->join('INNER JOIN', 'tbl_candidatos candi',
                          'vc.FK_CANDIDATO = candi.PK_CANDIDATO')
                  ->join('INNER JOIN', 'tbl_cat_estatus_candidato est',
                          'vc.FK_ESTATUS_ACTUAL_CANDIDATO = est.PK_ESTATUS_CANDIDATO')
                  ->andWhere(['=', 'vc.FK_VACANTE', $data['FK_VACANTE']])
                  ->all();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'dataProvider' => $dataProvider
            ];
        }
    }

    public function actionGuardarPerfil(){
      if (Yii::$app->request->isAjax) {
        $data= Yii::$app->request->post();
        if(!empty($data['datosTecnologias'])){
            foreach ($data['datosTecnologias']['checkedTech'] as $key => $value) {
                $modelTecnologia= new TblVacantesTecnologias;
                $modelTecnologia->FK_VACANTE = $model->PK_VACANTE;
                $modelTecnologia->FK_TECNOLOGIA = $value;
                $modelTecnologia->NIVEL_EXPERIENCIA = $data['datosTecnologias']['nivelTech'][$key];
                $modelTecnologia->TIEMPO_USO = $data['datosTecnologias']['aniosTech'][$key];
                $modelTecnologia->FECHA_REGISTRO = date("Y-m-d");
                $modelTecnologia->save(false);
            }
        }
            if(!empty($data['datosHerramientas'])){
                foreach ($data['datosHerramientas']['checkedHerra'] as $key2 => $value2) {
                    $modelHerramientas= new TblVacantesHerramientas;
                    $modelHerramientas->FK_VACANTE = $model->PK_VACANTE;
                    $modelHerramientas->FK_HERRAMIENTA =$value2;
                    $modelHerramientas->NIVEL_EXPERIENCIA = $data['datosHerramientas']['nivelHerr'][$key2];
                    $modelHerramientas->TIEMPO_USO = $data['datosHerramientas']['aniosHerr'][$key2];
                    $modelHerramientas->FECHA_REGISTRO = date("Y-m-d");
                    $modelHerramientas->save();
                }
            }
        if(!empty($data['datosHabilidades'])){
            foreach ($data['datosHabilidades'] as $key3 => $value3) {
                $modelHabilidades= new TblVacantesHabilidades;
                $modelHabilidades->FK_VACANTE = $model->PK_VACANTE;
                $modelHabilidades->FK_HABILIDAD =$value3;
                $modelHabilidades->FECHA_REGISTRO = date("Y-m-d");
                $modelHabilidades->save();
            }
        }
      }
    }

    public function actionPerfil_vacante(){
      if (Yii::$app->request->isAjax) {
        $data = Yii::$app->request->post();
        $idVacante = $data['idVacante'];
        $infoPerfil = $data['infoPerfil'];

        $comentarios = (new \yii\db\Query())
        ->select([
        'v.PK_VACANTE',
        'v.FECHA_CREACION AS FECHA_REGISTRO',
        'v.FK_PRIORIDAD',
        'v.FK_ESTACION_VACANTE',
        'v.FK_ESTATUS_VACANTE',
        'v.FK_UBICACION',
        'v.FK_UBICACION_CLIENTE',
        'v.FUNCIONES',
        'v.FK_USUARIO',
        'v.FK_ASIGNACION',
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
        'duracioncontrato.DESC_DURACION',
        'tv.DESC_TIPO_VACANTE',
        'v.COMENTARIOS',
        'workstation.DESC_WORKSTATION',
        'ev.DESC_ESTATUS_VACANTE',
        'tc.DESC_TIPO_CONTRATO',
        'v.NOMBRE_PROYECTO',
        'ub.DESC_UBICACION',
        'ubc.DESC_UBICACION AS DESC_UBICACION_CLIENTE',
        'ub.PROPIA_CLIENTE',
        'c.ALIAS_CLIENTE',
        'es.DESC_ESTACION_VACANTE'
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
          ->join('left join', 'tbl_cat_estaciones_vacante  es',
                          'v.FK_ESTACION_VACANTE = es.PK_ESTACION_VACANTE')
          ->where(['v.PK_VACANTE' => $idVacante])
          ->orderBy(['v.FK_ESTACION_VACANTE' => SORT_ASC])
          ->all();

        // $comentarios = (new \yii\db\Query())
        //   ->select([
        //     'v.DESC_VACANTE',
        //     'v.COMENTARIOS'
        //   ])
        //   ->from('tbl_vacantes as v')
        //   ->where(['v.PK_VACANTE' => $idVacante])
        //   ->all();
        $comentarios[0]['FECHA_CIERRE'] = transform_date($comentarios[0]['FECHA_CIERRE'],'d/m/Y');
        $comentarios[0]['FECHA_REGISTRO'] = transform_date($comentarios[0]['FECHA_REGISTRO'],'d/m/Y');
        $tecnologias = (new \yii\db\Query())
          ->select([
            'v.DESC_VACANTE',
            'catt.DESC_TECNOLOGIA AS DESCRIPCION',
            'crt.DESC_RANK_TECNICO',
            'vt.TIEMPO_USO'
          ])
          ->from('tbl_vacantes as v')
          ->join('INNER JOIN', 'tbl_vacantes_tecnologias vt',
                  'v.PK_VACANTE=vt.FK_VACANTE')
          ->join('INNER JOIN', 'tbl_cat_tecnologias catt',
                  'vt.FK_TECNOLOGIA = catt.PK_TECNOLOGIA')
          ->join ('left join','tbl_cat_rank_tecnico AS crt',
          'vt.NIVEL_EXPERIENCIA=crt.PK_RANK_TECNICO')
          ->where(['vt.FK_VACANTE' => $idVacante])
          ->all();

        $herramientas = (new \yii\db\Query())
          ->select([
            'v.DESC_VACANTE',
            'cath.DESC_HERRAMIENTA AS DESCRIPCION',
            'crt.DESC_RANK_TECNICO',
            'vh.TIEMPO_USO'
          ])
          ->from('tbl_vacantes as v')
          ->join('INNER JOIN', 'tbl_vacantes_herramientas vh',
                  'v.PK_VACANTE=vh.FK_VACANTE')
          ->join('INNER JOIN', 'tbl_cat_herramientas cath',
                  'vh.FK_HERRAMIENTA = cath.PK_HERRAMIENTA')
          ->join ('left join','tbl_cat_rank_tecnico AS crt',
          'vh.NIVEL_EXPERIENCIA=crt.PK_RANK_TECNICO')
          ->where(['Vh.FK_VACANTE' => $idVacante])
          ->all();

        $habilidades = (new \yii\db\Query())
          ->select([
            'v.DESC_VACANTE',
            'catha.DESC_HABILIDAD'
          ])
          ->from('tbl_vacantes as v')
          ->join('INNER JOIN', 'tbl_vacantes_habilidades vha',
                  'v.PK_VACANTE=vha.FK_VACANTE')
          ->join('INNER JOIN', 'tbl_cat_habilidades catha',
                  'vha.FK_HABILIDAD = catha.PK_HABILIDAD')
          ->where(['vha.FK_VACANTE' => $idVacante])
          ->all();

          if ($infoPerfil == 1) {
            $PerfilVacante = $tecnologias;
          } elseif ($infoPerfil == 2) {
            $PerfilVacante = $herramientas;
          } elseif ($infoPerfil == 3) {
            $PerfilVacante = $habilidades;
          } else {
            $PerfilVacante = '';
          }

          $modelUsuarios = '';
          $modelAsignacion = '';
          $modelBitAsignacion = '';
          $usuarioCreadorVacante = '';
          $moduloOrigenPeticion= '';

          $modelUsuarios = TblUsuarios::find()->where(['=','PK_USUARIO',$comentarios[0]['FK_USUARIO']])->asArray()->one();

          if(isset($model->FK_ASIGNACION)){
              $modelAsignacion = TblAsignaciones::find()->where(['=','PK_ASIGNACION',$comentarios[0]['FK_ASIGNACION']])->asArray()->one();
              $modelBitAsignacion = TblBitacora::find()->where(['like','DESC_OPERACION','Asignacion Detenida'])->andWhere(['=','REGISTRO_AFECTADO',$comentarios[0]['FK_ASIGNACION']])->orderBy(['PK_BITACORA' => SORT_DESC])->asArray()->one();
          }

          if($modelBitAsignacion){
              if(!isset($modelUsuarios)){
                  $modelUsuarios = TblUsuarios::find()->where(['=','PK_USUARIO',$modelBitAsignacion['FK_USUARIO']])->asArray()->one();
              }
          }

          $usuarioCreadorVacante = isset($modelUsuarios['NOMBRE_COMPLETO']) ? (isset($modelAsignacion['NOMBRE']) ? nl2br('Sistema: Por baja de empleado <br />Detonador: '.$modelUsuarios['NOMBRE_COMPLETO']) : $modelUsuarios['NOMBRE_COMPLETO']) : 'Sistema: Por baja de empleado';
          $moduloOrigenPeticion = isset($modelAsignacion['NOMBRE']) ? nl2br('ASIGNACIONES <br />Nombre: ').$modelAsignacion['NOMBRE'] : 'VACANTES';

          \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
          return [
              'tecnologias'=> $tecnologias,
              'herramientas'=> $herramientas,
              'habilidades'=> $habilidades,
              'PerfilVacante' => $PerfilVacante,
              'comentarios'=> $comentarios,
              'usuarioCreadorVacante' => $usuarioCreadorVacante,
              'moduloOrigenPeticion' => $moduloOrigenPeticion

          ];
      }
    }

    /**
     * Deletes an existing tblvacantes model.
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
     * Finds the tblvacantes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return tblvacantes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = tblvacantes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
