<?php

namespace app\controllers;

use Yii;
use app\models\TblAspirantes;
use app\models\TblVacantes;
use app\models\TblAspirantesVacantes;
use app\models\TblBitAspirantes;
use app\models\tblcandidatos;
use app\models\TblCandidatosTecnologias;
use app\models\TblCandidatosHabilidades;
use app\models\TblCandidatosHerramientas;
use app\models\TblCandidatosPerfiles;
use app\models\tblvacantescandidatos;
use app\models\TblBitProspectos;
use app\models\Tblprospectostecnologias;
use app\models\Tblprospectosherramientas;
use app\models\Tblprospectoshabilidades;
use app\models\Tblprospectosperfiles;
use app\models\TblProspectos;
use app\models\Tblcatplantillasprospectos;
use app\models\Tblconfigplantillasprospectos;
use app\models\Tblcatplantillasasignarprospectos;
use app\models\Tblconfigplantillasasignarprospectos;
use app\models\TblProspectosExamenes;
use app\models\TblProspectosDocumentos;
use app\models\TblCandidatosDocumentos;
use app\models\tblbitcomentarioscandidato;
use yii\data\ActiveDataProvider;
use app\models\SubirArchivo;
use app\models\TblCatTipoCV;
use app\models\TblCatExamenes;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\db\Query;
use yii\db\Expression;

class AspirantesController extends Controller
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

    public function actionIndex1()
    {
        //Ocupado para Index.php donde se cargan archivos  Excel
        $modelArchivoExcel = new SubirArchivo();
        $modelArchivoExcel->extensions = 'xls, xlsx';
        $modelArchivoExcel->noRequired = false;
        $modelAspirantesVacantes = new TblAspirantesVacantes;
        return $this->render('index1', [
            'modelArchivoExcel' => $modelArchivoExcel,
            'modelAspirantesVacantes' => $modelAspirantesVacantes,
        ]);
    }
    //eliminar prospecto
    public function actionEliminar(){

     if (Yii::$app->request->isAjax) {
          $data = Yii::$app->request->post();

          $connection = \Yii::$app->db;

          $Eliminar =
          $connection->createCommand("
            DELETE FROM  tbl_bit_comentarios_candidato
            WHERE FK_PROSPECTO = '".$data['idProspecto']."' ")->execute();
          $connection->createCommand("
            DELETE FROM  tbl_prospectos
            WHERE PK_PROSPECTO = '".$data['idProspecto']."' ")->execute();
          $connection->createCommand("
            DELETE FROM  tbl_Prospectos_habilidades
            WHERE FK_PROSPECTO = '".$data['idProspecto']."' ")->execute();
          $connection->createCommand("
            DELETE FROM  tbl_Prospectos_herramientas
            WHERE FK_PROSPECTO = '".$data['idProspecto']."' ")->execute();
          $connection->createCommand("
            DELETE FROM  tbl_prospectos_tecnologias
            WHERE FK_PROSPECTO = '".$data['idProspecto']."' ")->execute();
          $connection->createCommand("
            DELETE FROM  tbl_prospectos_documentos
            WHERE FK_PROSPECTO = '".$data['idProspecto']."' ")->execute();
          $connection->createCommand("
            DELETE FROM  tbl_prospectos_perfiles
            WHERE FK_PROSPECTO = '".$data['idProspecto']."' ")->execute();



         \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

          $connection->close();

        return [
                'Eliminar' => $Eliminar,
                'data'=>$data,
            ];
      }
    }

        public function actionIndex2()
    {
            //Ocupado para Index2.php donde se cargan archivos ZIP
        $modelArchivoZip = new SubirArchivo();
        $modelArchivoZip->extensions = 'zip';
        $modelArchivoZip->noRequired = false;
        $modelAspirantesVacantes = new TblAspirantesVacantes;
        return $this->render('index2', [
            'modelArchivoZip' => $modelArchivoZip,
            'modelAspirantesVacantes' => $modelAspirantesVacantes,
        ]);
    }
public function actionValidar_zip()
{       //Aqui se validara la funcionalidad de la carga de archivos ZIP

        $modelArchivoZip = new SubirArchivo();
        $nombreInicial='';
        $data = Yii::$app->request->post();
        $modelVacantes = TblVacantes::find()->select('PK_VACANTE, DESC_VACANTE')->where(['PK_VACANTE'=>$data['TblAspirantesVacantes']['FK_VACANTE']])->asArray()->limit(1)->one();
        $modelArchivoZip->file = UploadedFile::getInstance($modelArchivoZip, '[7]file');
        if($modelArchivoZip->file){
            $nombreInicial = $modelArchivoZip->file->name;
            $archivoSubido = 'true';
            $rutaGuardado = '../uploads/AspirantesZIP/';
            $nombre = 'seSubioConAjax';
            $arrayValidosZIP=[];
            $RowsValidos=0;
            $RowsFallidos=0;
            $RowsRepetidos=0;
            $extension = $modelArchivoZip->upload($rutaGuardado,$nombre);
            $rutaComepleta = $rutaGuardado.$nombre.'.'.$extension;
            $zip = new \ZipArchive;
                if ($zip->open($rutaComepleta) === true)
                {
                    for($i = 0; $i < $zip->numFiles; $i++)
                    {
                        $zip->extractTo($rutaGuardado, array($zip->getNameIndex($i)));
                        $nombreArchivoExtraido[] = $zip->getNameIndex($i);
                        $miArchivoExtraido = explode("_", $nombreArchivoExtraido[$i]);
                        if(count($miArchivoExtraido)==3)
                        {
                            $apellido_pat_aspZIP=$miArchivoExtraido[0];
                            $apellido_mat_aspZIP=$miArchivoExtraido[1];
                            $nombre_aspZIP=$miArchivoExtraido[2];
                            $nombre_asp = explode (".", $nombre_aspZIP);
                            $nombre_final = $nombre_asp[0];
                            $modelAspirantes = new TblAspirantes;
                            $modelAspirantes->APELLIDO_PAT_ASP = $apellido_pat_aspZIP;
                            $modelAspirantes->APELLIDO_MAT_ASP = $apellido_mat_aspZIP;
                            $modelAspirantes->NOMBRE_ASP = $nombre_final;
                            $vacante = $data['TblAspirantesVacantes']['FK_VACANTE'];
                            $modelAspirantes->RUTA_ARCHIVO = $rutaGuardado.$nombreArchivoExtraido[$i];

                            $link = mysqli_connect('localhost', 'root', '', 'erteisei_devcap_DES');
                            $query = ("select count(*) as TOTAL
                                                    from tbl_aspirantes a
                                                       INNER JOIN tbl_aspirantes_vacantes av ON a.PK_ASPIRANTES = av.FK_ASPIRANTE
                                                       where a.apellido_pat_asp ='$apellido_pat_aspZIP'
                                                       and   a.apellido_mat_asp ='$apellido_mat_aspZIP'
                                                       and   a.nombre_asp ='$nombre_final'
                                                       and   av.FK_VACANTE = '$vacante' ");
                            $query = $link->query($query);
                            $resultado=$query->fetch_object();
                            $query->close();
                            $total = $resultado->TOTAL;

                                        if((($nombre_asp[1] == 'pdf')  || ($nombre_asp[1] == 'doc')  || ($nombre_asp[1] == 'docx')) && (count($miArchivoExtraido)==3) && ($nombre_final !=''))
                                        {
                                                if($total > 0)
                                                {
                                                    $RowsRepetidos++;
                                                }
                                                else
                                                {
                                                    $RowsValidos++;
                                                    $arrayValidosZIP[] = $modelAspirantes;
                                                }

                            }
                            else
                            {
                               $RowsFallidos++;
                               unlink($rutaGuardado.$nombreArchivoExtraido[$i]);

                            }
                        }
                            else
                            {
                            $RowsFallidos++;
                            unlink($rutaGuardado.$nombreArchivoExtraido[$i]);
                            }
                    }
                $zip->close();
                }

        unlink($rutaComepleta);

        } else {
            $archivoSubido = 'false';
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'subioArchivo' => $archivoSubido ,
            'data' => $data,
            'NombreInicial' => $nombreInicial,
            'modelVacantes' => $modelVacantes,
            'nombreArchivoExtraido' => $nombreArchivoExtraido,
            'arrayValidosZIP' => $arrayValidosZIP,
            'RowsValidos'  => $RowsValidos,
            'RowsFallidos' => $RowsFallidos,
            'RowsRepetidos' => $RowsRepetidos,
            'miArchivoExtraido'=> $miArchivoExtraido,
            /*'nombre_asp' => $nombre_asp,*/
        ];
}


    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new TblProspectos();

        $modelSubirCV = new SubirArchivo();
        $modelSubirCV->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirCV->noRequired = true;

        $modelSubirEX = new SubirArchivo();
        $modelSubirEX->extensions = 'doc, docx, xls, xlsx, pdf';
        $modelSubirEX->noRequired = true;


        $userID = Yii::$app->session['usuario']['PK_USUARIO'];
        $fecha_registro = date('Y-m-d');
        $prospecto = '';
        if ($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post();

            $model->FECHA_NAC = (!empty($model->FECHA_NAC))? transform_date($model->FECHA_NAC,'Y-m-d'):'';
            $model->FK_USUARIO = $userID;
            $model->FK_ORIGEN = 1;
            $model->RECLUTADOR = 0;
            $model->FK_ESTADO = 1;
            $model->FK_ESTATUS = 2;
            $model->EXPECTATIVA = quitarFormatoMoneda($model->EXPECTATIVA);
            $model->SUELDO_ACTUAL = quitarFormatoMoneda($model->SUELDO_ACTUAL);
            //$model->PERFIL = null;
            $model->PERFIL = isset($data['TblProspectos']['PERFIL']);
            //var_dump($data['TblProspectos']['PERFIL']);
            //var_dump(count($data['TblProspectos']['PERFIL']));
            //dd($model);

            $model->FECHA_REGISTRO = $fecha_registro;

            if ($model->save(false)) {
                //Model successfully saved
                $datosTipoCV = TblCatTipoCV::find()->orderBy(['PK_TIPO_CV'=>SORT_ASC])->all();

                $datosEnviarCV = [];
                foreach ($datosTipoCV as $keyDTCV => $valueDTCV) {
                  $modelSubirCV->file = UploadedFile::getInstance($modelSubirCV, 'file['.$valueDTCV['PK_TIPO_CV'].']');

                  if ($modelSubirCV->file) {
                    $rutaGuardado = '../uploads/ProspectosCV/';
                    $nombre = 'CV'.$valueDTCV['DESC_CV'].'_'.$model->PK_PROSPECTO.'_'.date('Y-m-d');
                    $extCV = $modelSubirCV->upload($rutaGuardado,$nombre);

                    $modelProspectosDocumentos = new TblProspectosDocumentos();
                    $modelProspectosDocumentos->FK_PROSPECTO    = $model->PK_PROSPECTO;
                    $modelProspectosDocumentos->FK_TIPO_CV      = $valueDTCV['PK_TIPO_CV'];
                    $modelProspectosDocumentos->RUTA_CV         = '../'.$rutaGuardado.''.$nombre.'.'.$extCV;
                    $modelProspectosDocumentos->FECHA_REGISTRO  = date('Y-m-d');
                    $modelProspectosDocumentos->save(false);
                  }
                }

                $examenPsicosometrico = TblCatExamenes::find()->where(['=','TIPO','PSICOMETRICO'])->orderBy(['PK_EXAMEN'=>SORT_ASC])->all();

                  foreach ($examenPsicosometrico as $keyEX => $valueEX) {
                    $modelSubirEX->file = UploadedFile::getInstance($modelSubirEX, 'file['.$valueEX['PK_EXAMEN'].']');

                    if ($modelSubirEX->file) {
                      $rutaGuardadoEx = '../uploads/ProspectosExamenes/';
                      $nombreExamen = $model->PK_PROSPECTO.'_'.date('Y-m-d').'_'.$valueEX['DESC_EXAMEN'];
                      $extEX = $modelSubirEX->upload($rutaGuardadoEx, $nombreExamen);

                      $modelProspectosExamenes = new TblProspectosExamenes();
                      $modelProspectosExamenes->FK_PROSPECTO = $model->PK_PROSPECTO;
                      $modelProspectosExamenes->FK_EXAMEN = $valueEX['PK_EXAMEN'];
                      $modelProspectosExamenes->NOMBRE_DOCUMENTO = $nombreExamen;
                      $modelProspectosExamenes->UBICACION_DOCUMENTO = '../'.$rutaGuardadoEx.''.$nombreExamen.'.'.$extEX;
                      $modelProspectosExamenes->FECHA_REGISTRO = date('Y-m-d');
                      $modelProspectosExamenes->save(false);
                    }

                  }

                  if(!empty($data['ExamenTecn'])){

                    foreach ($data['ExamenTecn'] as $key => $value) {
                      $modelProspectosExamenes = new TblProspectosExamenes();
                      $modelProspectosExamenes['FK_PROSPECTO'] = $model->PK_PROSPECTO;;
                      $modelProspectosExamenes['FK_EXAMEN'] = $value;
                      $modelProspectosExamenes['VALOR'] =  $data['ExamenTec'][$value];
                      $modelProspectosExamenes['NOMBRE_DOCUMENTO']='';
                      $modelProspectosExamenes['UBICACION_DOCUMENTO']='';
                      $modelProspectosExamenes->save(false);
                    }
                  }


                if(!empty($data['Tecnologias'])){
                    foreach ($data['Tecnologias'] as $key => $value) {
                        $modelTecnologia= new Tblprospectostecnologias;
                        $modelTecnologia->FK_TECNOLOGIA = $key;
                        $modelTecnologia->FK_PROSPECTO = $model->PK_PROSPECTO;
                        $modelTecnologia->FECHA_REGISTRO = $fecha_registro;
                        $modelTecnologia->NIVEL_EXPERIENCIA = intval($data['Tecnologias'][$key]['Conocimiento']);
                        $modelTecnologia->TIEMPO_USO = intval($data['Tecnologias'][$key]['Años']);
                        $modelTecnologia->save();
                    }
                }
                if(!empty($data['Herramientas'])){
                    foreach ($data['Herramientas'] as $key => $value) {
                        $modelHerramienta= new Tblprospectosherramientas;
                        $modelHerramienta->FK_HERRAMIENTA = $key;
                        $modelHerramienta->FK_PROSPECTO = $model->PK_PROSPECTO;
                        $modelHerramienta->FECHA_REGISTRO = $fecha_registro;
                        $modelHerramienta->NIVEL_EXPERIENCIA = intval($data['Herramientas'][$key]['Conocimiento']);
                        $modelHerramienta->TIEMPO_USO = intval($data['Herramientas'][$key]['Años']);
                        $modelHerramienta->save();
                    }
                }
                if(!empty($data['Habilidades'])){
                    foreach ($data['Habilidades'] as $value) {
                        $modelHabilidad= new Tblprospectoshabilidades;
                        $modelHabilidad->FK_HABILIDAD = intval($value);
                        $modelHabilidad->FK_PROSPECTO = $model->PK_PROSPECTO;
                        $modelHabilidad->FECHA_REGISTRO = $fecha_registro;
                        $modelHabilidad->save();
                    }
                }
                if(!empty($model->PERFIL)){
                    if(count($data['TblProspectos']['PERFIL']) > 1 || ( count($data['TblProspectos']['PERFIL']) == 1 && $data['TblProspectos']['PERFIL'] != '' && $data['TblProspectos']['PERFIL'] != null ) ){
                        foreach ($data['TblProspectos']['PERFIL'] as $key => $value){
                            $modelPerfil = new Tblprospectosperfiles;
                            $modelPerfil->FK_PROSPECTO = $model->PK_PROSPECTO;
                            $modelPerfil->FK_PERFIL = intval($value);
                            $modelPerfil->FECHA_REGISTRO = $fecha_registro;
                            $modelPerfil->save(false);
                        }
                    }
                }

                $modelBitProspecto = new TblBitProspectos;
                $modelBitProspecto['FK_PROSPECTO'] = $model->PK_PROSPECTO;
                $modelBitProspecto['EMAIL'] = $model['EMAIL'];
                $modelBitProspecto['CELULAR'] = $model['CELULAR'];
                $modelBitProspecto['TELEFONO'] = $model['TELEFONO'];
                $modelBitProspecto['FK_ESTATUS'] = $model['FK_ESTATUS'];
                $modelBitProspecto['PERFIL'] = NULL;
                $modelBitProspecto['FECHA_CONVERSACION'] = $model['FECHA_CONVERSACION'];
                $modelBitProspecto['FK_ESTADO'] = $model['FK_ESTADO'];
                $modelBitProspecto['RECLUTADOR'] = $model['RECLUTADOR'];
                $modelBitProspecto['EXPECTATIVA'] = $model['EXPECTATIVA'];
                $modelBitProspecto['DISPONIBILIDAD_INTEGRACION'] = $model['DISPONIBILIDAD_INTEGRACION'];
                $modelBitProspecto['DISPONIBILIDAD_ENTREVISTA'] = $model['DISPONIBILIDAD_ENTREVISTA'];
                $modelBitProspecto['TRABAJA_ACTUALMENTE'] = $model['TRABAJA_ACTUALMENTE'];
                $modelBitProspecto['CANAL'] = $model['FK_CANAL'];
                $modelBitProspecto['SUELDO_ACTUAL'] = $model['SUELDO_ACTUAL'];
                $modelBitProspecto['COMENTARIOS'] = 'NUEVO PROSPECTO';
                $modelBitProspecto['FK_USUARIO'] = user_info()['PK_USUARIO'];
                $modelBitProspecto['FECHA_REGISTRO'] = date('Y-m-d');
                $modelBitProspecto->save(false);

                return $this->redirect(['aspirantes/index']);
            } else {
                // validation failed
                return $this->redirect(['vacantes/index']);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'modelSubirCV' => $modelSubirCV
            ]);
        }
    }

    public function actionConsultaemail()
    {
        if(null !== (Yii::$app->request->post('email'))){
            $email = Yii::$app->request->post('email');
            $result = TblProspectos::find()->where(['EMAIL' => $email])->exists();
        }else{
            $result = "Request failed";
        }
        // return Json
        return \yii\helpers\Json::encode($result);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PK_ASPIRANTES]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionValidar_excel()
    {

        //Aqui se valida la carga de archivos en formato de Excel
        $modelArchivoExcel = new SubirArchivo();
        $nombreInicial='';
        $data = Yii::$app->request->post();
        $modelVacantes = TblVacantes::find()->select('PK_VACANTE, DESC_VACANTE')->where(['PK_VACANTE'=>$data['TblAspirantesVacantes']['FK_VACANTE']])->asArray()->limit(1)->one();
        $modelArchivoExcel->file = UploadedFile::getInstance($modelArchivoExcel, '[7]file');
        if($modelArchivoExcel->file){
            $nombreInicial = $modelArchivoExcel->file->name;
            $archivoSubido = 'true';
            $rutaGuardado = '../uploads/AspirantesExcel/';
            $nombre = 'seSubioConAjax';
            $extension = $modelArchivoExcel->upload($rutaGuardado,$nombre);
            $rutaComepleta = $rutaGuardado.$nombre.'.'.$extension;
            $inputFileType = \PHPExcel_IOFactory::identify($rutaComepleta);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($rutaComepleta);
            $i=1;
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $arrayData[$i] = $worksheet->toArray();
                    $i++;
                }
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                unlink($rutaComepleta);
        }else{
            $archivoSubido = 'false';
        }

        $RowsValidos=0;
        $RowsFallidos=0;
        $RowsRepetidos=0;
        $arrayValidos=[];

               foreach($arrayData as $hoja)
                    {
                        foreach($hoja as $index => $row)
                            {
                                $apellido_pat_asp=$row[0];
                                $apellido_mat_asp=$row[1];
                                $nombre_asp=$row[2];
                                $email_asp=$row[3];
                                $telefono_asp=$row[4];
                                $modelAspirantes = new TblAspirantes;
                                $modelAspirantes->APELLIDO_PAT_ASP = $apellido_pat_asp;
                                $modelAspirantes->APELLIDO_MAT_ASP = $apellido_mat_asp;
                                $modelAspirantes->NOMBRE_ASP = $nombre_asp;
                                $modelAspirantes->EMAIL_ASP = $email_asp;
                                $modelAspirantes->TELEFONO_ASP = $telefono_asp;
                                $modelAspirantes->validate();
                                $modelProspectos= new Tblprospectoshabilidades;
                                $modelProspectos->DESC_HABILIDAD =

                                $vacante = $data['TblAspirantesVacantes']['FK_VACANTE'];
                                $link = mysqli_connect('localhost', 'root', '', 'erteisei_devcap_DES');
                                $query = ("select count(*) as TOTAL
                                                    from tbl_aspirantes a
                                                       INNER JOIN tbl_aspirantes_vacantes av ON a.PK_ASPIRANTES = av.FK_ASPIRANTE
                                                       where a.apellido_pat_asp ='$row[0]'
                                                       and   a.apellido_mat_asp ='$row[1]'
                                                       and   a.nombre_asp ='$row[2]'
                                                       and   a.EMAIL_ASP = '$row[3]'
                                                       and   av.FK_VACANTE = '$vacante' ");
                                $query = $link->query($query);
                                $resultado=$query->fetch_object();
                                $query->close();
                                $total = $resultado->TOTAL;

                               if ($apellido_pat_asp == '' && $apellido_mat_asp == '' && $nombre_asp =='' && $email_asp =='' && $telefono_asp == '')
                                {
                                 break;
                                }
                                        if($total>0)
                                        {
                                            $RowsRepetidos++;
                                        }
                                        else
                                        {
                                                if($modelAspirantes->validate())
                                                {
                                                   $RowsValidos++;
                                                   $arrayValidos[] = $modelAspirantes;

                                        }
                                        else
                                        {
                                          $RowsFallidos++;
                                        }
                                }


                }
         }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'subioArchivo' => $archivoSubido ,
            'data' => $data,
            'total' => $total,
            'resultado' => $resultado,
            'RowsValidos'  => $RowsValidos,
            'RowsFallidos' => $RowsFallidos,
            'RowsRepetidos' => $RowsRepetidos,
            'NombreInicial' => $nombreInicial,
            'arrayValidos' => $arrayValidos,
            'modelVacantes' => $modelVacantes,
        ];
    }

  public function actionComentario(){
    if (Yii::$app->request->isAjax) {

          $data= Yii::$app->request->post();

          $id= $data['idProspecto'];

          $prospectos = TblProspectos::find()->where(['PK_PROSPECTO' => $id])->one();

          $prospectos['FK_ESTADO'] = 8;
          $prospectos->save(false);

          $modelComentario = new TblBitProspectos;
          $modelComentario['FK_PROSPECTO'] = $id;
          $modelComentario['EMAIL'] = $prospectos['EMAIL'];
          $modelComentario['CELULAR'] = $prospectos['CELULAR'];
          $modelComentario['TELEFONO'] = $prospectos['TELEFONO'];
          $modelComentario['FK_ESTATUS'] = $prospectos['FK_ESTATUS'];;
          $modelComentario['PERFIL'] = $prospectos['PERFIL'];
          $modelComentario['FECHA_CONVERSACION'] = $prospectos['FECHA_CONVERSACION'];
          $modelComentario['FK_ESTADO'] = 8;
          $modelComentario['RECLUTADOR'] = $prospectos['RECLUTADOR'];
          $modelComentario['EXPECTATIVA'] = $prospectos['EXPECTATIVA'];
          $modelComentario['DISPONIBILIDAD_INTEGRACION'] = $prospectos['DISPONIBILIDAD_INTEGRACION'];
          $modelComentario['DISPONIBILIDAD_ENTREVISTA'] = $prospectos['DISPONIBILIDAD_ENTREVISTA'];
          $modelComentario['TRABAJA_ACTUALMENTE'] = $prospectos['TRABAJA_ACTUALMENTE'];
          $modelComentario['CANAL'] = $prospectos['FK_CANAL'];
          $modelComentario['SUELDO_ACTUAL'] = $prospectos['SUELDO_ACTUAL'];
          $modelComentario['COMENTARIOS'] = $data['comentario'];
          $modelComentario['FECHA_REGISTRO'] = date('Y-m-d');
          $modelComentario['FK_USUARIO']= user_info()['PK_USUARIO'];
          $modelComentario->save(false);
        }
    }

  public function actionCancelado(){
        if (Yii::$app->request->isAjax) {
            $data= Yii::$app->request->post();
            $id= $data['idProspecto'];

            $PK_CANDIDATO = (new \yii\db\Query())
                ->select('PK_CANDIDATO')
                ->from('tbl_candidatos')
                ->where(['FK_PROSPECTO'=>$id])
                ->one();

            $FK_VACANTE = (new \yii\db\Query())
                ->select('FK_VACANTE')
                ->from('tbl_vacantes_candidatos')
                ->where(['FK_CANDIDATO'=>$PK_CANDIDATO])
                ->all();

            $VACANTES = (new \yii\db\Query())
                ->select([
                    'v.PK_VACANTE',
                    'v.DESC_VACANTE',
                    'ec.DESC_ESTATUS_CANDIDATO',
                    'DATE_FORMAT(vc.FECHA_ACTUALIZACION, \'%d/%m/%Y\') as FECHA_ACTUALIZACION',
                    'bcc.COMENTARIOS'
                    ])
                ->from('tbl_vacantes_candidatos vc')
                ->join('LEFT JOIN','tbl_vacantes v',
                        'vc.FK_VACANTE = v.PK_VACANTE')
                ->join('LEFT JOIN','tbl_cat_estatus_candidato ec',
                        'vc.FK_ESTATUS_ACTUAL_CANDIDATO = ec.PK_ESTATUS_CANDIDATO')
                ->join('LEFT JOIN','tbl_bit_comentarios_candidato bcc',
                        'vc.FK_VACANTE = bcc.FK_VACANTE
                        AND vc.FK_CANDIDATO = bcc.FK_CANDIDATO
                        AND vc.FK_ESTACION_ACTUAL_CANDIDATO = bcc.FK_ESTACION_CANDIDATO
                        AND vc.FK_ESTATUS_ACTUAL_CANDIDATO = bcc.FK_ESTATUS_CANDIDATO
                        ')
                // ->where(['vc.FK_CANDIDATO'=>$PK_CANDIDATO['PK_CANDIDATO']]);
                ->where(['vc.FK_CANDIDATO'=>$PK_CANDIDATO['PK_CANDIDATO']])
                ->orderBy(['bcc.FECHA_REGISTRO' => SORT_DESC])
                ->all();

            foreach ($VACANTES as $key => $value) {
                $spanFechaCancelado = '';
                if ($VACANTES[$key]['FECHA_ACTUALIZACION'] != '') {
                  $dateFechaCancelado = str_replace('/', '-', $VACANTES[$key]['FECHA_ACTUALIZACION']);
                  $spanFechaCancelado = date('Y-m-d', strtotime($dateFechaCancelado));
                  $spanFechaCancelado = str_replace('-', '', $spanFechaCancelado);
                  $VACANTES[$key]['FECHA_ACTUALIZACION'] = transform_date($VACANTES[$key]['FECHA_ACTUALIZACION'],'d/m/Y');
                }
                $VACANTES[$key]['FECHA_ACTUALIZACION'] = '<span class="hide">'.$spanFechaCancelado.'</span>'.$VACANTES[$key]['FECHA_ACTUALIZACION'];
              }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
              'PK_CANDIDATO' => $PK_CANDIDATO,
              'PK_VACANTE' => $FK_VACANTE,
              'Vacantes' => $VACANTES
            ];
        }
    }

public function actionConversacion(){
  //$idProspecto = Yii::$app->request->get('id');
    if (Yii::$app->request->isAjax) {

        $data= Yii::$app->request->post();

        if (isset($data['recurso']) && $data['recurso'] == 2 ) {

          $idProspecto = $data['fkProspecto'];
          $candidatos = TblCandidatos::find()->where(['FK_PROSPECTO' => $idProspecto])->one();

          $candidatos['FECHA_CONVERSACION'] = date('Y-m-d');
          $candidatos['FK_ESTADO_PROSPECTO'] = $data['estadoProspecto'];
          $candidatos['FK_ESTATUS_PROSPECTO'] = 1;
          $candidatos['RECLUTADOR'] = user_info()['PK_USUARIO'];
          $candidatos['EXPECTATIVA'] = quitarFormatoMoneda($data['expectativa']);
          $candidatos['DISPONIBILIDAD_INTEGRACION'] = $data['dispoIntegra'];
          $candidatos['DISPONIBILIDAD_ENTREVISTA'] = $data['dispoEntrevista'];
          $candidatos['TRABAJA_ACTUALMENTE'] = $data['trabaja'];
          $candidatos['FK_CANAL'] = $data['canal'];
          $candidatos['SUELDO_ACTUAL'] = quitarFormatoMoneda($data['sueldoactual']);
          $candidatos['COMENTARIOS'] = $data['com1'];
          $candidatos->save(false);
        } else {

          $idProspecto = $data['idProspecto'];

          $prospectos = TblProspectos::find()->where(['PK_PROSPECTO' => $idProspecto])->one();

           $prospectos['FECHA_CONVERSACION'] = date('Y-m-d');
           $prospectos['FK_ESTADO'] = $data['estadoProspecto'];
           $prospectos['FK_ESTATUS'] = 1;
           $prospectos['RECLUTADOR'] = user_info()['PK_USUARIO'];
           $prospectos['EXPECTATIVA'] = quitarFormatoMoneda($data['expectativa']);
           $prospectos['DISPONIBILIDAD_INTEGRACION'] = $data['dispoIntegra'];
           $prospectos['DISPONIBILIDAD_ENTREVISTA'] = $data['dispoEntrevista'];
           $prospectos['TRABAJA_ACTUALMENTE'] = $data['trabaja'];
           $prospectos['FK_CANAL'] = $data['canal'];
           $prospectos['SUELDO_ACTUAL'] = quitarFormatoMoneda($data['sueldoactual']);
           $prospectos['COMENTARIOS'] = $data['com1'];
           $prospectos->save(false);

           $modelConversacionUpd = TblBitProspectos::find()->where(['FK_PROSPECTO' => $idProspecto])->orderBy(['FK_PROSPECTO' => SORT_DESC])->limit(1)->one();

           if($modelConversacionUpd['FK_ESTADO'] != $data['estadoProspecto'] || $modelConversacionUpd['EXPECTATIVA'] != $data['expectativa'] || $modelConversacionUpd['DISPONIBILIDAD_INTEGRACION'] != $data['dispoIntegra'] ||
              $modelConversacionUpd['DISPONIBILIDAD_ENTREVISTA'] != $data['dispoEntrevista'] || $modelConversacionUpd['TRABAJA_ACTUALMENTE'] != $data['trabaja'] || $modelConversacionUpd['CANAL'] != $data['canal'] ||
              $modelConversacionUpd['SUELDO_ACTUAL'] != $data['sueldoactual'] || $modelConversacionUpd['COMENTARIOS'] != $data['com1']){
                   $modelConversacion = new TblBitProspectos;
                   $modelConversacion['FK_PROSPECTO'] = $idProspecto;
                   $modelConversacion['EMAIL'] = $prospectos['EMAIL'];
                   $modelConversacion['CELULAR'] = $prospectos['CELULAR'];
                   $modelConversacion['TELEFONO'] = $prospectos['TELEFONO'];
                   $modelConversacion['FK_ESTATUS'] = 1;
                   $modelConversacion['PERFIL'] = $prospectos['PERFIL'];
                   $modelConversacion['FECHA_CONVERSACION'] = date('Y-m-d');
                   $modelConversacion['FK_ESTADO'] = $data['estadoProspecto'];
                   $modelConversacion['RECLUTADOR'] = user_info()['PK_USUARIO'];
                   $modelConversacion['EXPECTATIVA'] = quitarFormatoMoneda($data['expectativa']);
                   $modelConversacion['DISPONIBILIDAD_INTEGRACION'] = $data['dispoIntegra'];
                   $modelConversacion['DISPONIBILIDAD_ENTREVISTA'] = $data['dispoEntrevista'];
                   $modelConversacion['TRABAJA_ACTUALMENTE'] = $data['trabaja'];
                   $modelConversacion['CANAL'] = $data['canal'];
                   $modelConversacion['SUELDO_ACTUAL'] = quitarFormatoMoneda($data['sueldoactual']);
                   $modelConversacion['COMENTARIOS'] = $data['com1'];
                   $modelConversacion['FECHA_REGISTRO'] = date('Y-m-d');
                   $modelConversacion['FK_USUARIO'] = user_info()['PK_USUARIO'];
                   $modelConversacion->save(false);
                } else{
                   $modelConversacionUpd['FECHA_CONVERSACION'] = date('Y-m-d');
                   $modelConversacionUpd['FK_ESTADO'] = $data['estadoProspecto'];
                   $modelConversacionUpd['EXPECTATIVA'] =$data['expectativa'];
                   $modelConversacionUpd['DISPONIBILIDAD_INTEGRACION']= $data['dispoIntegra'];
                   $modelConversacionUpd['DISPONIBILIDAD_ENTREVISTA']=$data['dispoEntrevista'];
                   $modelConversacionUpd['TRABAJA_ACTUALMENTE']=$data['trabaja'];
                   $modelConversacionUpd['CANAL'] =$data['canal'];
                   $modelConversacionUpd['SUELDO_ACTUAL']=$data['sueldoactual'];
                   $modelConversacionUpd['COMENTARIOS'] = $data['com1'];
                   $modelConversacionUpd['FECHA_REGISTRO'] = date('Y-m-d');
                   $modelConversacionUpd['FK_USUARIO']= user_info()['PK_USUARIO'];
                   $modelConversacionUpd->save(false);
                 }
         }

        /*
        * Sección de examenes psicometricos
        */
        $modelExamenesPsico	=	TblProspectosExamenes::find();
        $datosTipoPsi = TblCatExamenes::find();
      if (!empty($_FILES['file']["name"])) {

        foreach ($_FILES['file']["name"] as $keyFILE => $valueFILE) {

            if (!empty($valueFILE)) {

                if ($data['arregloIdExamPsi'][$keyFILE][1] == "eliminar" || $data['arregloIdExamPsi'][$keyFILE][1] == "editar") {
                  $modelEliminar = $modelExamenesPsico->where(['FK_PROSPECTO' => $idProspecto, 'FK_EXAMEN' => $keyFILE])->one();
                  $archivoEliminar = substr($modelEliminar['UBICACION_DOCUMENTO'], 3, strlen($modelEliminar['UBICACION_DOCUMENTO']));
                  unlink($archivoEliminar);
                  $modelEliminar->delete();
                }
                // $keyArch = $keyFILE - 1;
                $tmp_name = $_FILES["file"]["tmp_name"][$keyFILE];
                $infoFile = pathInfo($_FILES["file"]["name"][$keyFILE]);
                $descPsi = $datosTipoPsi->where(['PK_EXAMEN' => $keyFILE])->one();
                $DESC_PSI = $descPsi['DESC_EXAMEN'];
                /*Cambio de nombre*/
                $nombre = $idProspecto.'_'.date('Y-m-d').'_'.$DESC_PSI.'.'.$infoFile['extension'];

                /*Subida de archivo*/
                move_uploaded_file($tmp_name, "../uploads/ProspectosExamenes/$nombre");

                $rutaGuardado = '../uploads/ProspectosExamenes/';
                $modelProspectosExamenes = new TblProspectosExamenes();
                $modelProspectosExamenes->FK_PROSPECTO = $idProspecto;
                $modelProspectosExamenes->FK_EXAMEN = $keyFILE;
                $modelProspectosExamenes->NOMBRE_DOCUMENTO = $nombre;
                $modelProspectosExamenes->UBICACION_DOCUMENTO = '../'.$rutaGuardado.''.$nombre;
                $modelProspectosExamenes->FECHA_REGISTRO = date('Y-m-d');
                $modelProspectosExamenes->save(false);

            }
            else {
              if ($data['arregloIdExamPsi'][$keyFILE][1] == "eliminar") {
                $modelEliminar = $modelExamenesPsico->where(['FK_PROSPECTO' => $idProspecto, 'FK_EXAMEN' => $keyFILE])->one();
                $archivoEliminar = substr($modelEliminar['UBICACION_DOCUMENTO'], 3, strlen($modelEliminar['UBICACION_DOCUMENTO']));
                unlink($archivoEliminar);
                $modelEliminar->delete();
              }
            }
        }
}
        /*
        * Sección de examenes técnicos,
        * mismo procedimiento como en tecnologias, herramientas y habilidades
        */
        $tecnicos = (new \yii\db\Query())
          ->select([
            'tblEx.FK_EXAMEN'
          ])
          ->from('tbl_prospectos_examenes as tblEx')
          ->join('INNER JOIN', 'tbl_cat_examenes CatEx',
                  'tblEx.FK_EXAMEN = CatEx.PK_EXAMEN')
          ->where(['tblEx.FK_PROSPECTO' => $idProspecto])
          ->andWhere(['CatEx.TIPO' => 'TECNICO'])
          ->all();

          foreach ($tecnicos as $keyT => $valueT) {
            $deleteExamenTec = TblProspectosExamenes::find()->where(['FK_PROSPECTO' => $idProspecto])->andWhere(['FK_EXAMEN' => $valueT])->all();
            foreach ($deleteExamenTec as $deleteExamenTec) {
              $deleteExamenTec->delete();
            }
          }

          if (!empty($data['ExamenTecn'])) {

          foreach ($data['ExamenTecn'] as $key => $value) {

            $modelProspectosExamenes = new TblProspectosExamenes();
            $modelProspectosExamenes['FK_PROSPECTO'] = $idProspecto;
            $modelProspectosExamenes['FK_EXAMEN'] = $value;
            $modelProspectosExamenes['VALOR'] =  $data['ExamenTec'][$value];
            $modelProspectosExamenes['NOMBRE_DOCUMENTO']='';
            $modelProspectosExamenes['UBICACION_DOCUMENTO']='';
            $modelProspectosExamenes['FECHA_REGISTRO'] = date('Y-m-d H:i:s');
            $modelProspectosExamenes->save();
          }
        }


    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return [
      'data' => $data
    ];

    }
}
public function actionCapturar_aspirantes_excel()
{   //Aui se carga en la base de datos a traves de los input hidden los registros del archivo de Excel
    $data = Yii::$app->request->post();
    //dd($data);
            foreach ($data['arrayAspirantes'] as $index => $array)
        {

                    $modelAspirantes = new TblAspirantes;
                    $modelAspirantes->APELLIDO_PAT_ASP = $array['APELLIDO_PAT_ASP'];
                    $modelAspirantes->APELLIDO_MAT_ASP = $array['APELLIDO_MAT_ASP'];
                    $modelAspirantes->NOMBRE_ASP = $array['NOMBRE_ASP'];
                    $modelAspirantes->EMAIL_ASP = $array['EMAIL_ASP'];
                    $modelAspirantes->TELEFONO_ASP = $array['TELEFONO_ASP'];
                    $modelAspirantes->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelAspirantes->save(false);

                    $modelAspirantesVacantes = new TblAspirantesVacantes;
                    $modelAspirantesVacantes->FK_VACANTE = $array['FK_VACANTE'];
                    $modelAspirantesVacantes->FK_ASPIRANTE = $modelAspirantes->PK_ASPIRANTES;
                    $modelAspirantesVacantes->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelAspirantesVacantes->save(false);

                    /*$modelProspectos= new TblProspectos;
                    $modelProspectos->PK_PROSPECTO=$array['PK_PROSPECTO'];
                    $modelProspectos->FK_TECNOLOGIA=$array['FK_TECNOLOGIA'];

                    $modelProspectoTecnologias= new tbl_cat_tecnologias;
                    $modelProspectoTecnologias->PK_TECNOLOGIA=  $modelProspectos->FK_TECNOLOGIA; */

                    $descripcionBitacora = 'ASPIRANTE ='.$modelAspirantes->PK_ASPIRANTES.','.$modelAspirantes->NOMBRE_ASP.' '.$modelAspirantes->APELLIDO_PAT_ASP.' '.$modelAspirantes->APELLIDO_MAT_ASP;
                    user_log_bitacora($descripcionBitacora,'Alta de Aspirante Excel',$modelAspirantes->PK_ASPIRANTES);
        }

        foreach ($data['arrayArchivos'] as $index => $array)
        {
                    $modelBitAspirantes = new TblBitAspirantes;
                    $modelBitAspirantes->NOMBRE_ARCHIVO_ASP = $array['NOMBRE_ARCHIVO_ASP'];
                    $modelBitAspirantes->ROWSVALIDOS = $array['ROWSVALIDOS'];
                    $modelBitAspirantes->ROWSREPETIDOS = $array['ROWSREPETIDOS'];
                    $modelBitAspirantes->ROWSFALLIDOS = $array['ROWSFALLIDOS'];
                    $modelBitAspirantes->FK_VACANTE = $array['FK_VACANTE'];
                    $modelBitAspirantes->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelBitAspirantes->FECHA_CARGA = date('Y-m-d H:i:s');
                    $modelBitAspirantes->save(false);
        }

    return $this->redirect(['index', 'action'=>'insert']);

}

public function actionCapturar_aspirantes_zip()
{   //Aui se carga en la base de datos a traves de los input hidden los registros del archivo
    $data = Yii::$app->request->post();
    foreach ($data['arrayAspirantes'] as $index => $array) {

                    $modelAspirantes = new TblAspirantes;
                    $modelAspirantes->APELLIDO_PAT_ASP = $array['APELLIDO_PAT_ASP'];
                    $modelAspirantes->APELLIDO_MAT_ASP = $array['APELLIDO_MAT_ASP'];
                    $modelAspirantes->NOMBRE_ASP = $array['NOMBRE_ASP'];
                    $modelAspirantes->EMAIL_ASP = ' ';
                    $modelAspirantes->TELEFONO_ASP = ' ';
                    $modelAspirantes->RUTA_ARCHIVO = $array['RUTA_ARCHIVO'];
                    $modelAspirantes->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelAspirantes->save(false);

                    $modelAspirantesVacantes = new TblAspirantesVacantes;
                    $modelAspirantesVacantes->FK_VACANTE = $array['FK_VACANTE'];
                    $modelAspirantesVacantes->FK_ASPIRANTE = $modelAspirantes->PK_ASPIRANTES;
                    $modelAspirantesVacantes->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelAspirantesVacantes->save(false);

                    $descripcionBitacora = 'ASPIRANTE ='.$modelAspirantes->PK_ASPIRANTES.','.$modelAspirantes->NOMBRE_ASP.' '.$modelAspirantes->APELLIDO_PAT_ASP.' '.$modelAspirantes->APELLIDO_MAT_ASP;
                    user_log_bitacora($descripcionBitacora,'Alta de Aspirante ZIP',$modelAspirantes->PK_ASPIRANTES);

    }

    foreach ($data['arrayArchivos'] as $index => $array) {
                    $modelBitAspirantes = new TblBitAspirantes;
                    $modelBitAspirantes->NOMBRE_ARCHIVO_ASP = $array['NOMBRE_ARCHIVO_ASP'];
                    $modelBitAspirantes->ROWSVALIDOS = $array['ROWSVALIDOS'];
                    $modelBitAspirantes->ROWSFALLIDOS = $array['ROWSFALLIDOS'];
                    $modelBitAspirantes->FK_VACANTE = $array['FK_VACANTE'];
                    $modelBitAspirantes->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelBitAspirantes->FECHA_CARGA = date('Y-m-d H:i:s');
                    $modelBitAspirantes->save(false);
}   //despues de la carga en la B.D. se refresca la pagina
    return $this->redirect(['index2', 'action'=>'insert']);

}


public function actionCancelar_carga()
{//Funcion del boton "Cancelar" ubicado a un lado del elemento file upload del archivo index.php
   return $this->redirect(['index']);

}

public function actionCancelar_carga2()
{//Funcion del boton "Cancelar" ubicado a un lado del elemento file upload del archivo index2.php
  // return $this->redirect(['index2']);
}

protected function findModel($id)
{
    if (($model = TblAspirantes::findOne($id)) !== null) {
        return $model;
    } else {
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

public function actionIndex3(){
    $request = Yii::$app->request;
    $pk_vacante = $request->get('pk_vacante');
    $modelVacante = tblvacantes::findOne($pk_vacante);

    $BajaMenorTresMeses = (new \yii\db\Query())
        ->select([
          'PK_PROSPECTO'
        ])
        ->from('tbl_prospectos')
        ->where(['=','FK_ESTATUS', 1])
        ->andWhere(['=','FK_ORIGEN', 2])
        ->andWhere(['<=','(TIMESTAMPDIFF(DAY, FECHA_REGISTRO + INTERVAL 3 MONTH, NOW()) )',0]);

    $dataProvider = (new \yii\db\Query())
        ->select([
            /**** Información General ***/
            'p.PK_PROSPECTO',
            'CONCAT(p.NOMBRE," ",p.APELLIDO_PATERNO," ",p.APELLIDO_MATERNO) AS NOMBRE',
            'p.CURP',
            'p.EDAD',
            'cg.DESC_GENERO AS GENERO',
            'p.FECHA_NAC',
            'p.EMAIL',
            'p.TELEFONO',
            'p.CELULAR',
            //'p.PERFIL',
            'p.UNIVERSIDAD',
            'p.CARRERA',
            'p.CONOCIMIENTOS_TECNICOS',
            'p.NIVEL_ESCOLARIDAD',
            'p.CARRERA',
            //'rh.NOMBRE_RESPONSABLE_RH AS RECLUTADOR',
            'p.EXPECTATIVA',

            /*** Conversación con prospecto ***/
            'cep.PK_ESTADO_PROSPECTO',
            'cep.DESC_ESTADO_PROSPECTO',
            'rh.NOMBRE_RESPONSABLE_RH AS RECLUTADOR',
            'p.FECHA_CONVERSACION',
            'est.DESC_ESTADO AS LUGAR_RESIDENCIA',
            // 'cv.DESC_CV AS TIPO_CV',
            // 'p.CV',
            'f.DESC_FUENTE AS FUENTE_VACANTE',
            'p.DISPONIBILIDAD_INTEGRACION',
            'p.DISPONIBILIDAD_ENTREVISTA',
            'p.TRABAJA_ACTUALMENTE',
            'p.FK_CANAL',
            'ca.DESC_CANAL as CANAL',
            'p.SUELDO_ACTUAL',
            'p.CAPACIDAD_RECURSO',
            'p.TACTO_CLIENTE',
            'p.DESEMPENIO_CLIENTE',

            'cestatusp.PK_ESTATUS_PROSPECTO',
            'cestatusp.DESC_ESTATUS_PROSPECTO',
            'p.FK_USUARIO_CHECKOUT',
            'u.NOMBRE_COMPLETO AS USUARIO_CHECKOUT',
            'p.FECHA_REGISTRO_CHECKOUT',

            'p.COMENTARIOS',
            'u.NOMBRE_COMPLETO',
            'p.FK_ORIGEN',
            'o.DESC_ORIGEN',
            'TIMESTAMPDIFF(MONTH, p.FECHA_REGISTRO, NOW()) AS MESES',
            //'p.FECHA_ACTUALIZACION',
            'p.FECHA_REGISTRO'
            ])
        ->from('tbl_prospectos as p')
        ->join('left join', 'tbl_cat_estado_prospecto cep',
                'p.FK_ESTADO = cep.PK_ESTADO_PROSPECTO')
        ->join('left join', 'tbl_cat_estatus_prospecto cestatusp',
                'p.FK_ESTATUS = cestatusp.PK_ESTATUS_PROSPECTO')
        ->join('left join', 'tbl_cat_genero cg',
                'p.FK_GENERO= cg.PK_GENERO')
        ->join('left join', 'tbl_usuarios u',
                'p.FK_USUARIO_CHECKOUT= u.PK_USUARIO')
        ->join('left join', 'tbl_cat_origen o',
                'p.FK_ORIGEN= o.PK_ORIGEN')
        ->join('left join', 'tbl_cat_canal ca',
                'p.FK_CANAL= ca.PK_CANAL')
        ->join('left join', 'tbl_cat_estados est',
                'p.LUGAR_RESIDENCIA = est.PK_ESTADO')
        ->join('left join', 'tbl_cat_fuentes f',
                'p.FK_FUENTE_VACANTE = f.PK_FUENTE')
        ->join('left join', 'tbl_cat_responsables_rh rh',
                'p.RECLUTADOR= rh.PK_RESPONSABLE_RH')
        // ->join('left join', 'tbl_cat_tipo_cv cv',
        //         'p.TIPO_CV= cv.PK_TIPO_CV')
        ->where(['=', 'FK_ESTATUS', 1])
        ->andWhere(['=', 'FK_ESTADO', 6])
        ->andWhere(['NOT IN', 'p.PK_PROSPECTO', $BajaMenorTresMeses]);

    $posiciones = [];
    $valorFront = [];
    $valorBD = [];
    $mensaje = "";

    $dataProvider = new ActiveDataProvider([
    'query' => $dataProvider
    ]);
    $dataProvider = $dataProvider->getModels();
    $dataProvider = $this->datosDataProvider($dataProvider);

    foreach ($dataProvider as $key => $value) {

        $dataProvider[$key]['NOMBRE'] = "<a href='#' id='prospecto' data-toggle='modal' data-target='#InformacionProspecto' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO']." >".$dataProvider[$key]['NOMBRE']."</a>";
        //$dataProvider[$key]['NOMBRE'] = '<a href="index4?id='.$dataProvider[$key]['PK_PROSPECTO'].'">'.$dataProvider[$key]['NOMBRE'].'</a>';
        // $dataProvider[$key]['HISTORICO'] = '<a href="#">Ver detalle</a>';

        /*Si FK_ORIGEN es 2 quiere decir que viene de empleados y es baja*/
        if ($dataProvider[$key]['FK_ORIGEN'] == 2) {
          $dataProvider[$key]['DESC_ESTATUS_PROSPECTO'] = "<a id='baja_empleado' href='#' data-toggle='modal' data-target='#estatusProspecto'  data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">". $dataProvider[$key]['DESC_ESTATUS_PROSPECTO'] ."</a>";
        }
        else if ($dataProvider[$key]['FK_ORIGEN'] == 3) {
          $dataProvider[$key]['DESC_ESTATUS_PROSPECTO'] = "<a id='cancelado' href='#' data-toggle='modal' data-target='#estatusProspecto' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">". $dataProvider[$key]['DESC_ESTATUS_PROSPECTO'] ."</a>";
        }

        $dataProvider[$key]['HISTORICO'] = '<p><a class="historicoProspecto" href="#"  data-toggle="modal" data-target="#HistorialProspecto" data-prospecto="'.$dataProvider[$key]['PK_PROSPECTO'].'">Ver detalle</a></p>';
        $prospectoComentarios = "";
        if ($dataProvider[$key]['COMENTARIOS'] == "") {
            $dataProvider[$key]['COMENTARIOS'] = '<span class="comentarioProspecto" data-prospecto="'.$dataProvider[$key]['PK_PROSPECTO'].'">No hay registros</span>';
        } else {
            $ProspectosComentarios = '<span class="hidden">'.$dataProvider[$key]['COMENTARIOS'].'</span>';
            $dataProvider[$key]['COMENTARIOS'] = '<p>'.$ProspectosComentarios.'<a class="comentarioProspecto" href="#"  data-toggle="modal" data-target="#comentariosProspecto" data-prospecto="'.$dataProvider[$key]['PK_PROSPECTO'].'">Ver comentarios</a></p>';
        }
        

        /*Agregar tecnologías al dataProvider*/
        $tecnologias = (new \yii\db\Query())
          ->select([
            'CatTec.DESC_TECNOLOGIA'
          ])
          ->from('tbl_prospectos as p')
          ->join('INNER JOIN', 'tbl_prospectos_tecnologias AspTec',
                  'p.PK_PROSPECTO = AspTec.FK_PROSPECTO')
          ->join('INNER JOIN', 'tbl_cat_tecnologias CatTec',
                  'AspTec.FK_TECNOLOGIA = CatTec.PK_TECNOLOGIA')
          ->where(['AspTec.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
          ->all();

        $dataProvider[$key]['TECNOLOGIAS'] = "<a id='datosProspectoTH' href='#!' data-toggle='modal' data-target='#datosth' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO']." data-value='1'>Ver Tecnologías</a>";

        $herramientas = (new \yii\db\Query())
          ->select([
            'CatHer.DESC_HERRAMIENTA'
          ])
          ->from('tbl_prospectos as p')
          ->join('INNER JOIN', 'tbl_prospectos_herramientas AspHer',
                  'p.PK_PROSPECTO = AspHer.FK_PROSPECTO')
          ->join('INNER JOIN', 'tbl_cat_herramientas CatHer',
                  'AspHer.FK_HERRAMIENTA = CatHer.PK_HERRAMIENTA')
          ->where(['AspHer.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
          ->all();

        $dataProvider[$key]['HERRAMIENTAS'] = "<a id='datosProspectoTH' href='#!' data-toggle='modal' data-target='#datosth' data-value='2' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">Ver Herramientas</a>";

        $habilidades = (new \yii\db\Query())
          ->select([
            'CatHab.DESC_HABILIDAD'
          ])
          ->from('tbl_prospectos as p')
          ->join('INNER JOIN', 'tbl_prospectos_habilidades AspHab',
                  'p.PK_PROSPECTO = AspHab.FK_PROSPECTO')
          ->join('INNER JOIN', 'tbl_cat_habilidades CatHab',
                  'AspHab.FK_HABILIDAD = CatHab.PK_HABILIDAD')
          ->where(['AspHab.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
          ->all();

        $dataProvider[$key]['HABILIDADES'] = "<a id='datosProspectoPH' href='#!' data-toggle='modal' data-target='#datosph' data-value='3' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">Ver Habilidades</a>";

        $perfil = (new \yii\db\Query())
          ->select([
            'CatPer.DESCRIPCION'
          ])
          ->from('tbl_prospectos as p')
          ->join('INNER JOIN', 'tbl_prospectos_perfiles PP',
                  'p.PK_PROSPECTO = PP.FK_PROSPECTO')
          ->join('INNER JOIN', 'tbl_cat_perfiles CatPer',
                  'PP.FK_PERFIL = CatPer.PK_PERFIL')
          ->where(['PP.FK_PROSPECTO' =>  $dataProvider[$key]['PK_PROSPECTO']])
          ->all();

        // $dataProvider[$key]['PERFIL'] = "<a id='datosProspectoPH' href='#!' data-toggle='modal' data-target='#datosph' data-value='4' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">Ver Perfiles</a>";

        $examenes = (new \yii\db\Query())
          ->select([
            'ex.FK_EXAMEN',
            'ex.VALOR'
          ])
          ->from('tbl_prospectos as p')
          ->join('INNER JOIN', 'tbl_prospectos_examenes ex',
                  'p.PK_PROSPECTO = ex.FK_PROSPECTO')
          ->where(['ex.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
          ->all();

        $examenes = (new \yii\db\Query())
        ->select([
          'ex.FK_EXAMEN',
          'ex.VALOR'
        ])
        ->from('tbl_prospectos as p')
        ->join('INNER JOIN', 'tbl_prospectos_examenes ex',
                'p.PK_PROSPECTO = ex.FK_PROSPECTO')
        ->where(['ex.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
        ->all();

        $ProspectosExamenes = "";
        if (empty($examenes) ) {
          $ProspectosExamenes = "";
        }
        else {
          $ProspectosExamenes = "<p>";
          foreach ($examenes as $tkey => $tvalue) {
            $ProspectosExamenes .= $examenes[$tkey]['FK_EXAMEN'].'<br>';
          }
          $ProspectosExamenes .= "</p>";
        }
      $dataProvider[$key]['EXAMENES'] = $ProspectosExamenes;

    }


    if (Yii::$app->request->isAjax) {

        if (Yii::$app->request->post()) {

            $data = Yii::$app->request->post();
            $pk_vacante =(!empty($data['pk_vacante']))? trim($data['pk_vacante']):'';

            $idPlantillaSel = isset($data['idPlantillaSel']) ? $data['idPlantillaSel'] : 1;

            $connection = \Yii::$app->db;

            $columnasPlantilla = $connection->createCommand("
                SELECT c.FK_CAT_PLANTILLA_PROSPECTOS AS id, c.MOSTRAR_COLUMNA, c.SECUENCIA_DESTINO, c.NOMBRE_COLUMNA, c.LABEL_COLUMNA
                FROM tbl_config_plantillas_prospectos AS c
                WHERE c.FK_CAT_PLANTILLA_PROSPECTOS = ".$idPlantillaSel."

            ")->queryAll();

            $cantColumnas = count($columnasPlantilla);

            foreach ($columnasPlantilla as $colPlantilla) {
                array_push($valorFront, $colPlantilla['NOMBRE_COLUMNA']);
                array_push($valorBD, $colPlantilla['LABEL_COLUMNA']);
            }

            $posiciones = array(
            $valorFront,
            $valorBD
            );
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
          'modelVacante' => $modelVacante,
          'columnasPlantilla' => $columnasPlantilla,
          'posiciones'=>$posiciones,
          'data' => $dataProvider,
          'datadata' => $data
        ];

    }
    //FIN DE AJAX

    if (Yii::$app->request->post()) {

            $data = Yii::$app->request->post();
            $pk_vacante =(!empty($data['pk_vacante']))? trim($data['pk_vacante']):'';

            // SE AGREGO FUNCIONALIDAD PARA QUE MODIFICARA EL ESTATUS DE LA VACANTE
            if($pk_vacante)
            {
                // Este estatus se asigna automaticamente cuando se le asocie un candidato a la Vacante
                $modelVacante = tblvacantes::findOne($pk_vacante);
                $modelVacante->FK_ESTACION_VACANTE=3;
                $modelVacante->FK_ESTATUS_VACANTE=2;
                $modelVacante->save(false);
            }

            if(isset($data['idProspecto'])){
                foreach ($data['idProspecto'] as $key => $value) {
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
                    $modelCandidato['ESTATUS_CAND_APLIC'] = 1;
                    $modelCandidato->save(false);
                }
                else {
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
                $modelBitProspecto['RECLUTADOR'] = $prospectos['RECLUTADOR'];
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
                *ASIGNACION DE LA VACANTE
                */

                $candidatos = (new \yii\db\Query())
                ->select('PK_CANDIDATO')
                ->from('tbl_candidatos')
                ->where(['FK_PROSPECTO' => $value])
                ->one();

                $modelVacantesCandidatos = new TblVacantesCandidatos;
                $modelVacantesCandidatos['FK_VACANTE'] = $pk_vacante;
                $modelVacantesCandidatos['FK_CANDIDATO'] = $candidatos['PK_CANDIDATO'];
                $modelVacantesCandidatos['FK_ESTACION_ACTUAL_CANDIDATO'] = '3';
                $modelVacantesCandidatos['FK_ESTATUS_ACTUAL_CANDIDATO'] = '1';
                $modelVacantesCandidatos['FECHA_REGISTRO'] = date('Y-m-d');
                $modelVacantesCandidatos['FECHA_ACTUALIZACION'] = date('Y-m-d');
                $modelVacantesCandidatos->save(false);

                $modelBitacoraCandidato = new tblbitcomentarioscandidato;
                $modelBitacoraCandidato['FK_VACANTE'] = $pk_vacante;
                $modelBitacoraCandidato['FK_CANDIDATO'] = $candidatos['PK_CANDIDATO'];
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
                /**
                * SE ENVÍAN LOS DATOS DE TECNOLOGÍAS, HERRAMIENTAS, HABILIDADES y PERFILES DE LOS PROSPECTOS A LAS TABLAS DE CANDIDATOS.
                */

                $dataTecnologias = (new \yii\db\Query())
                ->select('*')
                ->from ('tbl_prospectos_tecnologias')
                ->where(['FK_PROSPECTO' => $value])
                ->all();

                foreach ($dataTecnologias as $key2 => $tecnologias) {
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

                foreach ($dataHerramientas as $key3 => $herramientas) {
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

                foreach ($dataHabilidades as $key4 => $habilidades) {
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


                /**
                *Se elimina el prospecto.
                */

                $connection = (new \yii\db\Query())
                ->createCommand()
                ->delete('tbl_prospectos', 'PK_PROSPECTO = '.$value.'')
                ->execute();
            }
            return $this->redirect(["vacantes/index"]);
        }
    }

    $connection = \Yii::$app->db;
    $columnasPlantilla = $connection->createCommand("
        SELECT c.FK_CAT_PLANTILLA_PROSPECTOS AS id, c.MOSTRAR_COLUMNA, c.SECUENCIA_DESTINO, c.NOMBRE_COLUMNA, c.LABEL_COLUMNA
        FROM tbl_config_plantillas_prospectos AS c
        WHERE c.FK_CAT_PLANTILLA_PROSPECTOS = 1
    ")->queryAll();

    $cantColumnas = count($columnasPlantilla);

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

    return $this->render('index3',[
              'modelVacante' => $modelVacante,
              'columnasPlantilla' => $columnasPlantilla,
              'posiciones'=>$posiciones,
              'data' => $dataProvider,
              'dummyVacante' => $dummyVacante
    ]);
}

protected function prospecto($idProspecto)
{
  $dataProvider = (new \yii\db\Query())
      ->select([
              /**** Información General ***/
              'p.PK_PROSPECTO',
              'p.NOMBRE',
              'p.APELLIDO_PATERNO',
              'p.APELLIDO_MATERNO',
              'p.CURP',
              'p.EDAD',
              'p.EMAIL',
              'p.CELULAR',
              'p.TELEFONO',
              'DATE_FORMAT(p.FECHA_NAC, \'%d/%m/%Y\') as FECHA_NAC',
              'p.FK_FUENTE_VACANTE',
              'p.PERFIL',
              'p.FK_ORIGEN',
              'cestatusp.PK_ESTATUS_PROSPECTO',
              'cestatusp.DESC_ESTATUS_PROSPECTO',
              'cg.DESC_GENERO',
              'p.LUGAR_RESIDENCIA',
              'cest.DESC_ESTADO',
              'cf.DESC_FUENTE',
              'ccanal.DESC_CANAL',

              /*** Conversación con prospecto ***/
              'cep.PK_ESTADO_PROSPECTO',
              'cep.DESC_ESTADO_PROSPECTO',
              'p.RECLUTADOR',
              'rh.NOMBRE_RESPONSABLE_RH',
              'DATE_FORMAT(p.FECHA_CONVERSACION, \'%d/%m/%Y\') AS FECHA_CONVERSACION',
              'DATE_FORMAT(p.FECHA_REGISTRO, \'%d/%m/%Y\') as FECHA_REGISTRO',
              'p.FECHA_REGISTRO_CHECKOUT',
              'p.EXPECTATIVA',
              'p.DISPONIBILIDAD_INTEGRACION',
              'p.DISPONIBILIDAD_ENTREVISTA',
              'p.TRABAJA_ACTUALMENTE',
              'p.FK_CANAL',
              'p.FK_GENERO',
              'p.FK_ESTATUS',
              'p.FK_FUENTE_VACANTE',
              'p.SUELDO_ACTUAL',
              'p.COMENTARIOS'
          ])
      ->from('tbl_prospectos as p')
      ->join('left join', 'tbl_cat_estado_prospecto cep',
              'p.FK_ESTADO = cep.PK_ESTADO_PROSPECTO')
      ->join('left join', 'tbl_cat_estatus_prospecto cestatusp',
              'p.FK_ESTATUS = cestatusp.PK_ESTATUS_PROSPECTO')
      ->join('left join', 'tbl_cat_genero cg',
              'p.FK_GENERO= cg.PK_GENERO')
      ->join('left join', 'tbl_usuarios u',
               'p.FK_USUARIO_CHECKOUT= u.PK_USUARIO')
      ->join('left join', 'tbl_cat_origen o',
              'p.FK_ORIGEN= o.PK_ORIGEN')
      ->join('left join', 'tbl_cat_fuentes cf',
              'p.FK_FUENTE_VACANTE= cf.PK_FUENTE')
      ->join('left join', 'tbl_cat_canal ccanal',
              'p.FK_CANAL= ccanal.PK_CANAL')
      ->join('left join', 'tbl_cat_estados cest',
              'p.LUGAR_RESIDENCIA= cest.PK_ESTADO')
      // ->join('left join', 'tbl_cat_tipo_cv ctcv',
      //         'p.TIPO_CV= ctcv.PK_TIPO_CV')
      ->join('left join', 'tbl_cat_responsables_rh rh',
              'p.RECLUTADOR = rh.PK_RESPONSABLE_RH')
      ->where(['p.PK_PROSPECTO' => $idProspecto])
      ->one();


      /**Se agrega información de los curriculum del prospecto**/
      $cvs = (new \yii\db\Query())
        ->select([
          'CatTipoCV.DESC_CV',
          'PDocs.RUTA_CV'
        ])
        ->from('tbl_prospectos as p')
        ->join('INNER JOIN', 'tbl_prospectos_documentos PDocs',
                'p.PK_PROSPECTO = PDocs.FK_PROSPECTO')
        ->join('INNER JOIN', 'tbl_cat_tipo_cv CatTipoCV',
                'PDocs.FK_TIPO_CV = CatTipoCV.PK_TIPO_CV')
        ->where(['PDocs.FK_PROSPECTO' => $idProspecto])
        ->all();

        $CVsProspecto = "";
        // pathinfo('/www/htdocs/inc/lib.inc.php');
        if (empty($cvs) ) {
          $CVsProspecto = "";
        }
        else {
          $CVsProspecto = "<p>";
          foreach ($cvs as $keycvs => $valuecvs) {
            $pathInfo = pathinfo($cvs[$keycvs]['RUTA_CV']);
            $lenght = strlen($pathInfo['filename']);
            $nombreCVP = substr($pathInfo['filename'], 0, -11);
            $CVsProspecto .= '<a href="'.$cvs[$keycvs]['RUTA_CV'].'" download>'.$nombreCVP.'.'.$pathInfo['extension'].'</a><br>';
          }
          $CVsProspecto .= "</p>";
        }
      $dataProvider['CV'] = $CVsProspecto;

  // $dataProvider['TIPO_CV'] = pathinfo($dataProvider['CV']);

  return $dataProvider;
}


protected function candidato_prospecto($recurso, $id) {
      if ($recurso == 1) {
        $titulo   = "prospecto";
        $tablaTec = 'tbl_prospectos_tecnologias';
        $tablaHer = 'tbl_prospectos_herramientas';
        $tablaHab = 'tbl_prospectos_habilidades';
        $tablaPer = 'tbl_prospectos_perfiles';
        $tablaCV = 'tbl_prospectos_documentos';
        $Fk = 'FK_PROSPECTO';
        $FK_PROSPECTO = $id;

        $dataProvider = $this->prospecto($id);
      } else {
        $titulo   = "candidato";
        $tablaTec = 'tbl_candidatos_tecnologias';
        $tablaHer = 'tbl_candidatos_herramientas';
        $tablaHab = 'tbl_candidatos_habilidades';
        $tablaPer = 'tbl_candidatos_perfiles';
        $tablaCV = 'tbl_candidatos_documentos';
        $Fk = 'FK_CANDIDATO';

          $dataProvider = (new \yii\db\Query())
              ->select([
                      /**** Información General ***/
                      'c.PK_CANDIDATO',
                      'c.NOMBRE',
                      'c.APELLIDO_PATERNO',
                      'c.APELLIDO_MATERNO',
                      'c.CURP',
                      'c.EDAD',
                      'c.EMAIL',
                      'c.CELULAR',
                      'c.TELEFONO',
                      'c.FK_PROSPECTO',
                      'DATE_FORMAT(c.FECHA_NAC_CAN, \'%d/%m/%Y\') as FECHA_NAC',
                      'c.FK_FUENTE_VACANTE',
                      'c.FK_ORIGEN',
                      'cestatusp.PK_ESTATUS_PROSPECTO',
                      'c.FK_ESTATUS_PROSPECTO as FK_ESTATUS',
                      'cestatusp.DESC_ESTATUS_PROSPECTO',
                      'cg.DESC_GENERO',
                      'c.LUGAR_RESIDENCIA',
                      'cest.DESC_ESTADO',
                      'cf.DESC_FUENTE',
                      'ccanal.DESC_CANAL',

                      /*** Conversación con prospecto ***/
                      'cep.PK_ESTADO_PROSPECTO',
                      'c.FK_ESTADO_PROSPECTO as FK_ESTADO',
                      'cep.DESC_ESTADO_PROSPECTO',
                      'c.RECLUTADOR',
                      'DATE_FORMAT(c.FECHA_CONVERSACION, \'%d/%m/%Y\') AS FECHA_CONVERSACION',
                      'DATE_FORMAT(c.FECHA_REGISTRO, \'%d/%m/%Y\') as FECHA_REGISTRO',
                      'c.EXPECTATIVA',
                      'c.DISPONIBILIDAD_INTEGRACION',
                      'c.DISPONIBILIDAD_ENTREVISTA',
                      'c.TRABAJA_ACTUALMENTE',
                      'c.FK_CANAL',
                      'c.FK_GENERO',
                      'c.SUELDO_ACTUAL',
                      'c.COMENTARIOS'
                  ])
              ->from('tbl_candidatos as c')
              ->join('left join', 'tbl_cat_estado_prospecto cep',
                      'c.FK_ESTADO_PROSPECTO = cep.PK_ESTADO_PROSPECTO')
              ->join('left join', 'tbl_cat_estatus_prospecto cestatusp',
                      'c.FK_ESTATUS_PROSPECTO = cestatusp.PK_ESTATUS_PROSPECTO')
              ->join('left join', 'tbl_cat_genero cg',
                      'c.FK_GENERO= cg.PK_GENERO')
              // ->join('left join', 'tbl_usuarios u',
              //          'c.FK_USUARIO_CHECKOUT= u.PK_USUARIO')
              ->join('left join', 'tbl_cat_origen o',
                      'c.FK_ORIGEN= o.PK_ORIGEN')
              ->join('left join', 'tbl_cat_fuentes cf',
                      'c.FK_FUENTE_VACANTE= cf.PK_FUENTE')
              ->join('left join', 'tbl_cat_canal ccanal',
                      'c.FK_CANAL= ccanal.PK_CANAL')
              ->join('left join', 'tbl_cat_estados cest',
                      'c.LUGAR_RESIDENCIA= cest.PK_ESTADO')
              // ->join('left join', 'tbl_cat_tipo_cv ctcv',
              //         'c.TIPO_CV= ctcv.PK_TIPO_CV')
              ->join('left join', 'tbl_cat_responsables_rh rh',
                      'c.RECLUTADOR = rh.PK_RESPONSABLE_RH')
              ->where(['c.PK_CANDIDATO' => $id])
              ->one();


              /**Se agrega información de los curriculum del prospecto**/
              $cvs = (new \yii\db\Query())
                ->select([
                  'CatTipoCV.DESC_CV',
                  'CDocs.RUTA_CV'
                ])
                ->from('tbl_candidatos as c')
                ->join('INNER JOIN', 'tbl_candidatos_documentos CDocs',
                        'c.PK_CANDIDATO = CDocs.FK_CANDIDATO')
                ->join('INNER JOIN', 'tbl_cat_tipo_cv CatTipoCV',
                        'CDocs.FK_TIPO_CV = CatTipoCV.PK_TIPO_CV')
                ->where(['CDocs.FK_CANDIDATO' => $id])
                ->all();

                $CVsCandidato = "";
                if (empty($cvs) ) {
                  $CVsCandidato = "";
                }
                else {
                  $CVsCandidato = "<p>";
                  foreach ($cvs as $keycvs => $valuecvs) {
                    $pathInfo = pathinfo($cvs[$keycvs]['RUTA_CV']);
                    $lenght = strlen($pathInfo['filename']);
                    $nombreCVP = substr($pathInfo['filename'], 0, -11);
                    $CVsCandidato .= '<a href="'.$cvs[$keycvs]['RUTA_CV'].'" download>'.$nombreCVP.'.'.$pathInfo['extension'].'</a><br>';
                  }
                  $CVsCandidato .= "</p>";
                }
              $dataProvider['CV'] = $CVsCandidato;

              $FK_PROSPECTO = $dataProvider['FK_PROSPECTO'];
        } //Fin Else



        $ExamenesTec = (new \yii\db\Query())
            ->select([
              'pe.FK_EXAMEN',
              'ce.DESC_EXAMEN',
              'pe.VALOR'
            ])
            ->from('tbl_cat_examenes ce')
            ->join('INNER JOIN', 'tbl_prospectos_examenes pe',
                    'pe.FK_EXAMEN = ce.PK_EXAMEN')
            ->where(['ce.TIPO' => 'TECNICO'])
              ->andWhere(['FK_PROSPECTO'=>$FK_PROSPECTO])
            ->orderBy('DESC_EXAMEN')
            ->all();

        $ExamenesPsi = (new \yii\db\Query())
             ->select([
             'pe.FK_EXAMEN', 'ce.DESC_EXAMEN',
             'pe.UBICACION_DOCUMENTO', 'ce.TIPO'
            ])
            ->from('tbl_cat_examenes ce')
            ->join('INNER JOIN', 'tbl_prospectos_examenes pe',
                  'pe.FK_EXAMEN = ce.PK_EXAMEN')
            ->where(['ce.TIPO' => 'PSICOMETRICO'])
            ->andWhere(['FK_PROSPECTO'=>$FK_PROSPECTO])
            ->orderBy('DESC_EXAMEN')
            ->all();

        $modelTecnologias = (new \yii\db\Query())
                             ->select([
                               'tec.FK_TECNOLOGIA',
                               'tec.TIEMPO_USO',
                               'crt.DESC_RANK_TECNICO',
                               'tec.NIVEL_EXPERIENCIA'
                             ])
                              ->from($tablaTec.' tec')
                              ->join('left join', 'tbl_cat_rank_tecnico crt',
                                      'tec.NIVEL_EXPERIENCIA = crt.PK_RANK_TECNICO')
                              ->join('left join', 'tbl_cat_tecnologias t',
                                      'tec.FK_TECNOLOGIA = t.PK_TECNOLOGIA')
                              ->where([$Fk=>$id])
                              ->orderBy(['t.DESC_TECNOLOGIA' => SORT_ASC])
                              ->all();

        $modelHerramientas = (new \yii\db\Query())
                           ->select([
                             'her.NIVEL_EXPERIENCIA',
                             'crt.DESC_RANK_TECNICO',
                             'her.TIEMPO_USO',
                             'her.FK_HERRAMIENTA'
                           ])
                           ->from($tablaHer.' her')
                           ->join('left join', 'tbl_cat_rank_tecnico crt',
                                   'her.NIVEL_EXPERIENCIA = crt.PK_RANK_TECNICO')
                           ->join('left join', 'tbl_cat_herramientas h',
                                   'her.FK_HERRAMIENTA = h.PK_HERRAMIENTA')
                           ->where([$Fk=>$id])
                           ->orderBy(['h.DESC_HERRAMIENTA' => SORT_ASC])
                           ->all();

        $modelHabilidades = (new \yii\db\Query())
                            ->select(['FK_HABILIDAD','FK_PROSPECTO'])
                            ->from($tablaHab)
                            ->where([$Fk=>$id])
                            ->all();

        $modelPerfiles = (new \yii\db\Query())
                            ->select(['FK_PERFIL'])
                            ->from($tablaPer)
                            ->where([$Fk=>$id])
                            ->all();

        $modelCurriculim = (new \yii\db\Query())
          ->select([
            'cv.FK_TIPO_CV',
            'CatTipoCV.DESC_CV',
            'cv.RUTA_CV'
          ])
          ->from($tablaCV.' cv')
          ->join('INNER JOIN', 'tbl_cat_tipo_cv CatTipoCV',
                  'cv.FK_TIPO_CV = CatTipoCV.PK_TIPO_CV')
          ->where(['cv.'.$Fk => $id])
          ->orderBy(['cv.FK_TIPO_CV'=>SORT_ASC])
          ->all();

          $connection = \Yii::$app->db;

          $sqlReadExamenesPsi =
              $connection->createCommand("
                      SELECT ce.PK_EXAMEN, ce.DESC_EXAMEN, pe.VALOR, ce.TIPO FROM tbl_cat_examenes ce
                      INNER JOIN tbl_prospectos_examenes pe on pe.FK_EXAMEN = ce.PK_EXAMEN
                      WHERE ce.TIPO = 'PSICOMETRICO' AND pe.FK_PROSPECTO = ".$id." AND pe.FK_PROSPECTO = ".user_info()['PK_USUARIO'])->queryAll();

          $sqlReadExamenesTec =
              $connection->createCommand("
                      SELECT ce.PK_EXAMEN, ce.DESC_EXAMEN, pe.VALOR, ce.TIPO FROM tbl_cat_examenes ce
                      INNER JOIN tbl_prospectos_examenes pe on pe.FK_EXAMEN = ce.PK_EXAMEN
                      WHERE ce.TIPO = 'TECNICO' AND pe.FK_PROSPECTO = ".$id." AND pe.FK_PROSPECTO = ".user_info()['PK_USUARIO'])->queryAll();

          $connection->close();

          $data = array(
            'modelTecnologias'  => $modelTecnologias,
            'modelHerramientas' => $modelHerramientas,
            'modelHabilidades'  => $modelHabilidades,
            'modelPerfiles'     => $modelPerfiles,
            'prospecto'           => $dataProvider,
            'CVs'               => $modelCurriculim,
            'titulo'           => $titulo,
            'sqlReadExamenesPsi' => $sqlReadExamenesPsi,
            'sqlReadExamenesTec' => $sqlReadExamenesTec,
            'ExamenesTec' => $ExamenesTec,
            'ExamenesPsi' => $ExamenesPsi
          );
          return $data;

        //return $dataProvider;


}

public function actionIndex4()
    {


        if(Yii::$app->request->get()){
            $datos = Yii::$app->request->get();
            $idProspecto = Yii::$app->request->get('id');
        $modelTecnologias= (new \yii\db\Query())
                               ->select(['DESC_TECNOLOGIA','PK_TECNOLOGIA'])
                               ->from('tbl_cat_tecnologias')
                               ->all();




          $modelProspectoTecnologias=(new \yii\db\Query())
                              ->select([
                               'ph.NIVEL_EXPERIENCIA',
                               'crt.DESC_RANK_TECNICO',
                               'ph.TIEMPO_USO',
                               'ph.FK_TECNOLOGIA',
                               'ph.FK_PROSPECTO'
                               ])
                              ->from('tbl_prospectos_tecnologias ph')
                              ->join('left join', 'tbl_cat_rank_tecnico crt',
                                      'ph.NIVEL_EXPERIENCIA = crt.PK_RANK_TECNICO')
                              ->where(['FK_PROSPECTO'=>$idProspecto])
                              ->all();

             $modelHerramientas= (new \yii\db\Query())
                                 ->select(['DESC_HERRAMIENTA','PK_HERRAMIENTA'])
                                 ->from('tbl_cat_herramientas')
                                 ->all();

              $modelProspectoHerramientas=(new \yii\db\Query())
                                  ->select([
                                    'ph.NIVEL_EXPERIENCIA',
                                    'crt.DESC_RANK_TECNICO',
                                    'ph.TIEMPO_USO',
                                    'ph.FK_HERRAMIENTA',
                                    'ph.FK_PROSPECTO'
                                  ])
                                  ->from('tbl_prospectos_herramientas ph')
                                  ->join('left join', 'tbl_cat_rank_tecnico crt',
                                          'ph.NIVEL_EXPERIENCIA = crt.PK_RANK_TECNICO')
                                  ->where(['FK_PROSPECTO'=>$idProspecto])
                                  ->all();
              $modelPerfiles= (new \yii\db\Query())
                                  ->select(['DESCRIPCION','PK_PERFIL'])
                                  ->from('tbl_cat_perfiles')
                                  ->all();

               $modelProspectoPerfiles=(new \yii\db\Query())
                                   ->select([
                                     'pp.FK_PERFIL'
                                   ])
                                   ->from('tbl_prospectos_perfiles pp')
                                   ->where(['FK_PROSPECTO'=>$idProspecto])
                                   ->all();
             $modelHabilidades= (new \yii\db\Query())
                                 ->select(['DESC_HABILIDAD','PK_HABILIDAD'])
                                 ->from('tbl_cat_habilidades')
                                 ->all();

             $modelProspectoHabilidades=(new \yii\db\Query())
                                 ->select(['FK_HABILIDAD','FK_PROSPECTO'])
                                 ->from('tbl_prospectos_habilidades')
                                 ->where(['FK_PROSPECTO'=>$idProspecto])
                                 ->all();

            $ExamenesTec = (new \yii\db\Query())
                ->select([
                  'pe.FK_EXAMEN',
                  'ce.DESC_EXAMEN',
                  'pe.VALOR'
                ])
                ->from('tbl_cat_examenes ce')
                ->join('INNER JOIN', 'tbl_prospectos_examenes pe',
                        'pe.FK_EXAMEN = ce.PK_EXAMEN')
                ->where(['ce.TIPO' => 'TECNICO'])
                ->andWhere(['pe.FK_PROSPECTO' => $idProspecto])
                ->orderBy('DESC_EXAMEN')
                ->all();

            $ExamenesPsi = (new \yii\db\Query())
                                 ->select([
                                 'pe.FK_EXAMEN', 'ce.DESC_EXAMEN',
                                 'pe.UBICACION_DOCUMENTO', 'ce.TIPO'
                                ])
                                ->from('tbl_cat_examenes ce')
                                ->join('INNER JOIN', 'tbl_prospectos_examenes pe',
                                      'pe.FK_EXAMEN = ce.PK_EXAMEN')
                                ->where(['ce.TIPO' => 'PSICOMETRICO'])
                                ->andWhere(['pe.FK_PROSPECTO' => $idProspecto])
                                ->orderBy('DESC_EXAMEN')
                                ->all();

            $prospecto = $this->prospecto($idProspecto);

            $connection = \Yii::$app->db;

            $sqlReadExamenesPsi =
                $connection->createCommand("
                        SELECT ce.PK_EXAMEN, ce.DESC_EXAMEN, pe.VALOR, ce.TIPO FROM tbl_cat_examenes ce
                        INNER JOIN tbl_prospectos_examenes pe on pe.FK_EXAMEN = ce.PK_EXAMEN
                        WHERE ce.TIPO = 'PSICOMETRICO' AND pe.FK_PROSPECTO = ".$idProspecto." AND pe.FK_PROSPECTO = ".user_info()['PK_USUARIO'])->queryAll();

            $sqlReadExamenesTec =
                $connection->createCommand("
                        SELECT ce.PK_EXAMEN, ce.DESC_EXAMEN, pe.VALOR, ce.TIPO FROM tbl_cat_examenes ce
                        INNER JOIN tbl_prospectos_examenes pe on pe.FK_EXAMEN = ce.PK_EXAMEN
                        WHERE ce.TIPO = 'TECNICO' AND pe.FK_PROSPECTO = ".$idProspecto." AND pe.FK_PROSPECTO = ".user_info()['PK_USUARIO'])->queryAll();

            $connection->close();
            $dataProvider = $this->candidato_prospecto(1, $idProspecto);

            //dd($prospecto);
            return $this->render('index4', [
            // "modelTecnologias" => $modelTecnologias,
            "modelHabilidades" => $modelHabilidades,
            // "modelHerramientas" => $modelHerramientas,
            "modelPerfiles" =>$modelPerfiles,
            "modelProspectoTecnologias" =>$modelProspectoTecnologias,
            "modelProspectoHerramientas" =>$modelProspectoHerramientas,
            "modelProspectoHabilidades" =>$modelProspectoHabilidades,
            "modelProspectoPerfiles" =>$modelProspectoPerfiles,
            "ExamenesPsi" => $ExamenesPsi,
            "ExamenesTec" => $ExamenesTec,
            "prospecto" =>$prospecto,
            "sqlReadExamenesPsi" => $sqlReadExamenesPsi,
            "sqlReadExamenesTec" => $sqlReadExamenesTec,
            'CVs'               => $dataProvider['CVs'],
            'modelTecnologias'  => $dataProvider['modelTecnologias'],
            'modelHerramientas' => $dataProvider['modelHerramientas'],
            'modelPerfiles'     => $dataProvider['modelPerfiles'],
            ]);
        }
    }

  public function actionIndex5(){

    $modelProspectos = new TblProspectos;
    $dataProvider = "";
    $prospectos = "";

    if (Yii::$app->request->isAjax) {

            if (Yii::$app->request->post()) {

                $data = Yii::$app->request->post();

                foreach ($data['idProspectos'] as $key => $value) {
                    $modelProspectos = TblProspectos::find()->where(['PK_PROSPECTO' => $value])->limit(1)->one();
                    $modelProspectos->FK_ESTADO = 1;
                    $modelProspectos->FK_ESTATUS = 1;
                    $modelProspectos['FK_USUARIO_CHECKOUT'] = 0;
                    //$modelProspectos->FECHA_ACTUALIZACION = date('Y-m-d');
                    $modelProspectos->save(false);

                    // Historial de Prospectos
                    $modelBitProspecto = new TblBitProspectos;
                    $modelBitProspecto['FK_PROSPECTO'] = $value;
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
                    $modelBitProspecto['TRABAJA_ACTUALMENTE'] = $modelProspectos->TRABAJA_ACTUALMENTE;
                    $modelBitProspecto['CANAL'] = $modelProspectos->FK_CANAL;
                    $modelBitProspecto['SUELDO_ACTUAL'] = $modelProspectos->SUELDO_ACTUAL;
                    $modelBitProspecto['COMENTARIOS'] = 'TRANSICIÓN DE NO CONTRATABLE A CONTRATABLE';
                    $modelBitProspecto['FK_USUARIO'] = user_info()['PK_USUARIO'];
                    $modelBitProspecto['FECHA_REGISTRO'] = date('Y-m-d');
                    $modelBitProspecto->save(false);
                }
                return $this->redirect(["aspirantes/index"]);
            }
            else {

                  $prospectos = (new\yii\db\Query())
                  ->select(["
                    pr.PK_PROSPECTO,
                    CONCAT(pr.NOMBRE,' ',pr.APELLIDO_PATERNO,' ',pr.APELLIDO_MATERNO) AS PROSPECTO,
                    pr.FK_ORIGEN,
                    cato.DESC_ORIGEN,
                    estado.DESC_ESTADO_PROSPECTO,
                    pr.COMENTARIOS
                  "])
                  ->from('tbl_prospectos AS pr')
                  ->join('left join','tbl_cat_origen cato',
                  'pr.FK_ORIGEN = cato.PK_ORIGEN')
                  ->join('left join','tbl_cat_estado_prospecto  estado',
                  'pr.FK_ESTADO = estado.PK_ESTADO_PROSPECTO')
                  ->where(['=', 'pr.FK_ESTADO', 8])
                  ->all();

                  if (count($prospectos != 0)) {
                    foreach ($prospectos as $k => $value) {
                      $dataProvider = (new\yii\db\Query())
                      ->select(["
                          CONCAT(cat.DESC_CATEGORIA, ' /', subcat.DESC_SUBCATEGORIA, ' /', comEmp.COMENTARIOS) AS MOTIVOBAJA
                      "])
                      ->from('tbl_prospectos AS pr')
                      ->join('left join','tbl_perfil_empleados perEmp',
                      'pr.PK_PROSPECTO = perEmp.FK_PROSPECTO')
                      // ->join('left join','tbl_bit_prospectos bitpr',
                      // 'pr.PK_PROSPECTO = bitpr.FK_PROSPECTO')
                      ->join('left join','tbl_bit_comentarios_empleados comEmp',
                      'comEmp.FK_EMPLEADO = perEmp.FK_EMPLEADO')
                      ->join('left join','tbl_cat_categoria cat',
                      'comEmp.MOTIVO_CAT = cat.PK_CATEGORIA')
                      ->join('left join','tbl_cat_subcategoria subcat',
                      'comEmp.MOTIVO_SUBCAT = subcat.PK_SUBCATEGORIA')
                      ->andWhere(['perEmp.FK_PROSPECTO' => $prospectos[$k]['PK_PROSPECTO']])
                      ->all();

                      //$comentarios = "";
                      $motivoBaja = "";
                      foreach ($dataProvider as $key => $value) {
                        //$comentarios .= '<div>'.$dataProvider[$key]['COMENTARIOS'].'</div>';
                        $motivoBaja .= '<div>'.$dataProvider[$key]['MOTIVOBAJA'].'</div>';
                      }
                      //$prospectos[$k]['COMENTARIOS'] = $comentarios;
                      $prospectos[$k]['MOTIVOBAJA'] = $motivoBaja;
                    }
                  }
                  else {
                      $prospectos[] = 0;
                  }

                  foreach ($prospectos as $key => $value) {
                    $prospectos[$key]['PROSPECTO'] = "<a href='#' id='prospecto' data-toggle='modal' data-target='#InformacionProspecto' data-prospecto=".$prospectos[$key]['PK_PROSPECTO']." >".$prospectos[$key]['PROSPECTO']."</a>";
                    $ProspectosComentarios="";
                    if ($prospectos[$key]['COMENTARIOS'] == '') {
                        $prospectos[$key]['COMENTARIOS'] = '<span  id="funcionesVacante"  data-vacante="'.$prospectos[$key]['PK_PROSPECTO'].'">No hay registros</span>';
                    } else {
                        $ProspectosComentarios = "<span class='hidden'>".$prospectos[$key]['COMENTARIOS'] ."</span>";
                        $prospectos[$key]['COMENTARIOS'] = ''.$ProspectosComentarios.'<a class="" href="#!" id="prospectoComentarios" data-toggle="modal" data-target="#comentariosProspecto" data-prospecto="'.$prospectos[$key]['PK_PROSPECTO'].'">Ver comentarios</a>';
                    }
                    
                    /*Si FK_ORIGEN es 2 quiere decir que viene de empleados y es baja*/
                    if ($prospectos[$key]['FK_ORIGEN'] == 2) {
                      $prospectos[$key]['DESC_ORIGEN'] = "<a id='baja_empleado' href='#' data-toggle='modal' data-target='#estatusProspecto'  data-prospecto=".$prospectos[$key]['PK_PROSPECTO'].">". $prospectos[$key]['DESC_ORIGEN'] ."</a>";
                    }
                    else if ($prospectos[$key]['FK_ORIGEN'] == 3) {
                      $prospectos[$key]['DESC_ORIGEN'] = "<a id='cancelado' href='#' data-toggle='modal' data-target='#estatusProspecto' data-prospecto=".$prospectos[$key]['PK_PROSPECTO'].">". $prospectos[$key]['DESC_ORIGEN'] ."</a>";
                    }
                }
            }


            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
              'dataProvider' => $dataProvider,
              'data' => $prospectos,
            ];

            }

        $prospectos = (new\yii\db\Query())
                  ->select(["
                    pr.PK_PROSPECTO,
                    CONCAT(pr.NOMBRE,' ',pr.APELLIDO_PATERNO,' ',pr.APELLIDO_MATERNO) AS PROSPECTO,
                    pr.FK_ORIGEN,
                    cato.DESC_ORIGEN,
                    estado.DESC_ESTADO_PROSPECTO,
                    pr.COMENTARIOS
                  "])
                  ->from('tbl_prospectos AS pr')
                  ->join('left join','tbl_cat_origen cato',
                  'pr.FK_ORIGEN = cato.PK_ORIGEN')
                  ->join('left join','tbl_cat_estado_prospecto  estado',
                  'pr.FK_ESTADO = estado.PK_ESTADO_PROSPECTO')
                  ->where(['=', 'pr.FK_ESTADO', 8])
                  ->all();

                  if (count($prospectos != 0)) {
                    foreach ($prospectos as $k => $value) {
                      $dataProvider = (new\yii\db\Query())
                      ->select(["
                          CONCAT(cat.DESC_CATEGORIA, ' /', subcat.DESC_SUBCATEGORIA, ' /', comEmp.COMENTARIOS) AS MOTIVOBAJA
                      "])
                      ->from('tbl_prospectos AS pr')
                      ->join('left join','tbl_perfil_empleados perEmp',
                      'pr.PK_PROSPECTO = perEmp.FK_PROSPECTO')
                      // ->join('left join','tbl_bit_prospectos bitpr',
                      // 'pr.PK_PROSPECTO = bitpr.FK_PROSPECTO')
                      ->join('left join','tbl_bit_comentarios_empleados comEmp',
                      'comEmp.FK_EMPLEADO = perEmp.FK_EMPLEADO')
                      ->join('left join','tbl_cat_categoria cat',
                      'comEmp.MOTIVO_CAT = cat.PK_CATEGORIA')
                      ->join('left join','tbl_cat_subcategoria subcat',
                      'comEmp.MOTIVO_CAT = subcat.PK_SUBCATEGORIA')
                      ->andWhere(['perEmp.FK_PROSPECTO' => $prospectos[$k]['PK_PROSPECTO']])
                      ->all();

                      //$comentarios = "";
                      $motivoBaja = "";
                      foreach ($dataProvider as $key => $value) {
                        //$comentarios .= '<div>'.$dataProvider[$key]['COMENTARIOS'].'</div>';
                        $motivoBaja .= '<div>'.$dataProvider[$key]['MOTIVOBAJA'].'</div>';
                      }

                      //$prospectos[$k]['COMENTARIOS'] = $comentarios;
                      $prospectos[$k]['MOTIVOBAJA'] = $motivoBaja;

                    }
                  }
                  else {
                      $prospectos[] = 0;
                  }

                  foreach ($prospectos as $key => $value) {
                    $prospectos[$key]['PROSPECTO'] = "<a href='#' id='prospecto' data-toggle='modal' data-target='#InformacionProspecto' data-prospecto=".$prospectos[$key]['PK_PROSPECTO']." >".$prospectos[$key]['PROSPECTO']."</a>";
                    /*Si FK_ORIGEN es 2 quiere decir que viene de empleados y es baja*/
                    if ($prospectos[$key]['FK_ORIGEN'] == 2) {
                      $prospectos[$key]['DESC_ORIGEN'] = "<a id='baja_empleado' href='#' data-toggle='modal' data-target='#estatusProspecto'  data-prospecto=".$prospectos[$key]['PK_PROSPECTO'].">". $prospectos[$key]['DESC_ORIGEN'] ."</a>";
                    }
                    else if ($prospectos[$key]['FK_ORIGEN'] == 3) {
                      $prospectos[$key]['DESC_ORIGEN'] = "<a id='cancelado' href='#' data-toggle='modal' data-target='#estatusProspecto' data-prospecto=".$prospectos[$key]['PK_PROSPECTO'].">". $prospectos[$key]['DESC_ORIGEN'] ."</a>";
                    }
                    
                  }

                $dummyVacante = new tblvacantes;

                return $this->render('index5', [
                  'dataProvider' => $dataProvider,
                  'prospectos' => $prospectos,
                  'dummyVacante' => $dummyVacante
                ]);

    }


    protected function datosDataProvider($dataProvider, $enEvaluacion = 0)
    {
      foreach ($dataProvider as $key => $value) {
        /*Formato para las Fechas d-m-Y, también para el Grid y Sort del Grid*/
        $spanFechaNacimiento = '';
        if ($dataProvider[$key]['FECHA_NAC'] != '') {
          $dateFechaNacimiento = str_replace('/', '-', $dataProvider[$key]['FECHA_NAC']);
          $spanFechaNacimiento = date('Y-m-d', strtotime($dateFechaNacimiento));
          $spanFechaNacimiento = str_replace('-', '', $spanFechaNacimiento);
          $dataProvider[$key]['FECHA_NAC'] = transform_date($dataProvider[$key]['FECHA_NAC'],'d/m/Y');
        }
        $dataProvider[$key]['FECHA_NAC'] = '<span class="hide">'.$spanFechaNacimiento.'</span>'.$dataProvider[$key]['FECHA_NAC'];
        //** Fecha Conversación
        $spanFechaConversacion = '';
        if ($dataProvider[$key]['FECHA_CONVERSACION'] != '') {
          $dateFechaConversacion = str_replace('/', '-', $dataProvider[$key]['FECHA_CONVERSACION']);
          $spanFechaConversacion = date('Y-m-d', strtotime($dateFechaConversacion));
          $spanFechaConversacion = str_replace('-', '', $spanFechaConversacion);
          $dataProvider[$key]['FECHA_CONVERSACION'] = transform_date($dataProvider[$key]['FECHA_CONVERSACION'],'d/m/Y');
        }
        $dataProvider[$key]['FECHA_CONVERSACION'] = '<span class="hide">'.$spanFechaConversacion.'</span>'.$dataProvider[$key]['FECHA_CONVERSACION'];
        //** Fecha Checkout
        $spanFechaCheckout = '';
        if ($dataProvider[$key]['FECHA_REGISTRO_CHECKOUT'] != '') {
          $dateFechaCheckout = str_replace('/', '-', $dataProvider[$key]['FECHA_REGISTRO_CHECKOUT']);
          $spanFechaCheckout = date('Y-m-d', strtotime($dateFechaCheckout));
          $spanFechaCheckout = str_replace('-', '', $spanFechaCheckout);
          $dataProvider[$key]['FECHA_REGISTRO_CHECKOUT'] = transform_date($dataProvider[$key]['FECHA_REGISTRO_CHECKOUT'],'d/m/Y');
        }
        $dataProvider[$key]['FECHA_REGISTRO_CHECKOUT'] = '<span class="hide">'.$spanFechaCheckout.'</span>'.$dataProvider[$key]['FECHA_REGISTRO_CHECKOUT'];
        //** Fecha Registro
        $spanFechaRegistro = '';
        if ($dataProvider[$key]['FECHA_REGISTRO'] != '') {
          $dateFechaRegistro = str_replace('/', '-', $dataProvider[$key]['FECHA_REGISTRO']);
          $spanFechaRegistro = date('Y-m-d', strtotime($dateFechaRegistro));
          $spanFechaRegistro = str_replace('-', '', $spanFechaRegistro);
          $dataProvider[$key]['FECHA_REGISTRO'] = transform_date($dataProvider[$key]['FECHA_REGISTRO'],'d/m/Y');
        }
        $dataProvider[$key]['FECHA_REGISTRO'] = '<span class="hide">'.$spanFechaRegistro.'</span>'.$dataProvider[$key]['FECHA_REGISTRO'];
        /*Fin para el formato de Fechas*/

        if($enEvaluacion == 0){
            $dataProvider[$key]['NOMBRE'] = '<a href="detalle_prospecto?id='.$dataProvider[$key]['PK_PROSPECTO'].'&recurso=1">'.$dataProvider[$key]['NOMBRE'].'</a>';
        }else{
            $dataProvider[$key]['NOMBRE'] = "<a href='#' id='prospecto' data-toggle='modal' data-target='#InformacionProspecto' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO']." >".$dataProvider[$key]['NOMBRE']."</a>";
        }


        //$dataProvider[$key]['NOMBRE'] = '<a href="#">'.$dataProvider[$key]['NOMBRE'].'</a>';
        // $dataProvider[$key]['HISTORICO'] = '<a href="#">Ver detalle</a>';

        /*Si FK_ORIGEN es 2 quiere decir que viene de empleados y es baja*/
        if ($dataProvider[$key]['FK_ORIGEN'] == 2) {
          $dataProvider[$key]['DESC_ORIGEN'] = "<a id='baja' data-toggle='modal' data-target='#estatusProspecto' href='#' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">". $dataProvider[$key]['DESC_ORIGEN'] ."</a>";
        }
        else if ($dataProvider[$key]['FK_ORIGEN'] == 3) {
          $dataProvider[$key]['DESC_ORIGEN'] = "<a id='cancelado' data-toggle='modal' data-target='#estatusProspecto' href='#' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">". $dataProvider[$key]['DESC_ORIGEN'] ."</a>";
        }
        $dataProvider[$key]['HISTORICO'] = '<p><a class="historicoProspecto" href="#"  data-toggle="modal" data-target="#HistorialProspecto" data-prospecto="'.$dataProvider[$key]['PK_PROSPECTO'].'">Ver detalle</a></p>';
        $ProspectosComentarios = "";
        if ($dataProvider[$key]['COMENTARIOS'] == '') {
                $dataProvider[$key]['COMENTARIOS'] = '<span  id="prospectoComentarios"  data-vacante="'.$dataProvider[$key]['PK_PROSPECTO'].'">No hay registros</span>';
        } else {
            $ProspectosComentarios = "<span class='hidden'>".$dataProvider[$key]['COMENTARIOS']."</span>";
            $dataProvider[$key]['COMENTARIOS'] = ''.$ProspectosComentarios.'<a class="" href="#!" id="prospectoComentarios" data-toggle="modal" data-target="#comentariosProspecto" data-prospecto="'.$dataProvider[$key]['PK_PROSPECTO'].'">Ver comentarios</a>';
        }        
          /*Agregar tecnologías al dataProvider*/
          $tecnologias = (new \yii\db\Query())
            ->select([
              'CatTec.DESC_TECNOLOGIA'
            ])
            ->from('tbl_prospectos as p')
            ->join('INNER JOIN', 'tbl_prospectos_tecnologias AspTec',
                    'p.PK_PROSPECTO = AspTec.FK_PROSPECTO')
            ->join('INNER JOIN', 'tbl_cat_tecnologias CatTec',
                    'AspTec.FK_TECNOLOGIA = CatTec.PK_TECNOLOGIA')
            ->where(['AspTec.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
            ->all();

            $ProspectosTecnologias = "";
            if (empty($tecnologias) ) {
              $dataProvider[$key]['TECNOLOGIAS'] = "".$ProspectosTecnologias."<span id='datosProspectoTH' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO']." data-value='1'>No hay registros</span>";
            }
            else {
              $ProspectosTecnologias = "<span class='hide'>";
              foreach ($tecnologias as $tkey => $tvalue) {
                $ProspectosTecnologias .= $tecnologias[$tkey]['DESC_TECNOLOGIA'].', <br>';
              }
              $ProspectosTecnologias .= "</span>";
              $dataProvider[$key]['TECNOLOGIAS'] = "".$ProspectosTecnologias."<a id='datosProspectoTH' href='#!' data-toggle='modal' data-target='#datosth' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO']." data-value='1'>Ver Tecnologías</a>";
            }

          

          $herramientas = (new \yii\db\Query())
            ->select([
              'CatHer.DESC_HERRAMIENTA'
            ])
            ->from('tbl_prospectos as p')
            ->join('INNER JOIN', 'tbl_prospectos_herramientas AspHer',
                    'p.PK_PROSPECTO = AspHer.FK_PROSPECTO')
            ->join('INNER JOIN', 'tbl_cat_herramientas CatHer',
                    'AspHer.FK_HERRAMIENTA = CatHer.PK_HERRAMIENTA')
            ->where(['AspHer.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
            ->all();

            $ProspectosHerramientas = "";
            if (empty($herramientas) ) {
              $dataProvider[$key]['HERRAMIENTAS'] = "".$ProspectosHerramientas."<span id='datosProspectoTH' data-value='2' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">No hay registros</span>";
            }
            else {
              $ProspectosHerramientas = "<span class='hide'>";
              foreach ($herramientas as $tkey => $tvalue) {
                $ProspectosHerramientas .= $herramientas[$tkey]['DESC_HERRAMIENTA'].', <br>';
              }
              $ProspectosHerramientas .= "</span>";
              $dataProvider[$key]['HERRAMIENTAS'] = "".$ProspectosHerramientas."<a id='datosProspectoTH' href='#!' data-toggle='modal' data-target='#datosth' data-value='2' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">Ver Herramientas</a>";
            }
          // $dataProvider[$key]['HERRAMIENTAS'] = $ProspectosHerramientas;
          

          $habilidades = (new \yii\db\Query())
            ->select([
              'CatHab.DESC_HABILIDAD'
            ])
            ->from('tbl_prospectos as p')
            ->join('INNER JOIN', 'tbl_prospectos_habilidades AspHab',
                    'p.PK_PROSPECTO = AspHab.FK_PROSPECTO')
            ->join('INNER JOIN', 'tbl_cat_habilidades CatHab',
                    'AspHab.FK_HABILIDAD = CatHab.PK_HABILIDAD')
            ->where(['AspHab.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
            ->all();

            $ProspectosHabilidades = "";
            if (empty($habilidades) ) {
              $dataProvider[$key]['HABILIDADES'] = "".$ProspectosHabilidades."<span id='datosProspectoPH' data-value='3' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">No hay registros</span>";
            }
            else {
              $ProspectosHabilidades = "<span class='hide'>";
              foreach ($habilidades as $tkey => $tvalue) {
                $ProspectosHabilidades .= $habilidades[$tkey]['DESC_HABILIDAD'].', <br>';
              }
              $ProspectosHabilidades .= "</span>";
              $dataProvider[$key]['HABILIDADES'] = "".$ProspectosHabilidades."<a id='datosProspectoPH' href='#!' data-toggle='modal' data-target='#datosph' data-value='3' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">Ver Habilidades</a>";
            }
          // $dataProvider[$key]['HABILIDADES'] = $ProspectosHabilidades;
          

          $perfil = (new \yii\db\Query())
            ->select([
              'CatPer.DESCRIPCION'
            ])
            ->from('tbl_prospectos as p')
            ->join('INNER JOIN', 'tbl_prospectos_perfiles PP',
                    'p.PK_PROSPECTO = PP.FK_PROSPECTO')
            ->join('INNER JOIN', 'tbl_cat_perfiles CatPer',
                    'PP.FK_PERFIL = CatPer.PK_PERFIL')
            ->where(['PP.FK_PROSPECTO' =>  $dataProvider[$key]['PK_PROSPECTO']])
            ->all();

            $ProspectosPerfiles = "";
            if (empty($perfil) ) {
              $dataProvider[$key]['PERFIL'] = "<span id='datosProspectoPH' data-value='4' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">No hay registros</span>";
            }
            else {
              $ProspectosPerfiles = "<span class='hide'>";
              foreach ($perfil as $pkey => $pvalue) {
                $ProspectosPerfiles .= $perfil[$pkey]['DESCRIPCION'].'<br>';
              }
              $ProspectosPerfiles .= "</span>";
              $dataProvider[$key]['PERFIL'] = "".$ProspectosPerfiles."<a id='datosProspectoPH' href='#!' data-toggle='modal' data-target='#datosph' data-value='4' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">Ver Perfiles</a>";
            }
          // $dataProvider[$key]['PERFIL'] = $ProspectosPerfiles;
            

          $examenes = (new \yii\db\Query())
            ->select([
              'ex.FK_EXAMEN',
              'ex.VALOR'
            ])
            ->from('tbl_prospectos as p')
            ->join('INNER JOIN', 'tbl_prospectos_examenes ex',
                    'p.PK_PROSPECTO = ex.FK_PROSPECTO')
            ->where(['ex.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
            ->all();

            // $ProspectosExamenes = "";
            // if (empty($examenes) ) {
            //   $ProspectosExamenes = "";
            // }
            // else {
            //   $ProspectosExamenes = "<p>";
            //   foreach ($examenes as $tkey => $tvalue) {
            //     $ProspectosExamenes .= $examenes[$tkey]['FK_EXAMEN'].$examenes[$tkey]['VALOR'].'<br>';
            //   }
            //   $ProspectosExamenes .= "</p>";
            // }
          // CV
          /*Curriculums*/
          $cvs = (new \yii\db\Query())
            ->select([
              'CatTipoCV.DESC_CV',
              'PDocs.RUTA_CV'
            ])
            ->from('tbl_prospectos as p')
            ->join('INNER JOIN', 'tbl_prospectos_documentos PDocs',
                    'p.PK_PROSPECTO = PDocs.FK_PROSPECTO')
            ->join('INNER JOIN', 'tbl_cat_tipo_cv CatTipoCV',
                    'PDocs.FK_TIPO_CV = CatTipoCV.PK_TIPO_CV')
            ->where(['PDocs.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
            ->all();

            $CVsProspecto = "";
            // pathinfo('/www/htdocs/inc/lib.inc.php');
            if (empty($cvs) ) {
              $CVsProspecto = "";
            }
            else {
              $CVsProspecto = "<p>";
              foreach ($cvs as $keycvs => $valuecvs) {
                $pathInfo = pathinfo($cvs[$keycvs]['RUTA_CV']);
                $lenght = strlen($pathInfo['filename']);
                $nombreCVP = substr($pathInfo['filename'], 0, -11);;
                $CVsProspecto .= '<a href="'.$cvs[$keycvs]['RUTA_CV'].'" download>'.$nombreCVP.'.'.$pathInfo['extension'].'</a><br>';
              }
              $CVsProspecto .= "</p>";
            }
          $dataProvider[$key]['CV'] = $CVsProspecto;

          $dataProvider[$key]['EXAMENES'] = $examenes;
      }

      return $dataProvider;
    }

    public function actionIndex()
    {
      /* tablaprospectos
      * PK_PROSPECTO
      * NOMBRE
      * APELLIDO_PATERNO
      * APELLIDO_MATERNO
      **** Información General ***
      * EDAD
      * EMAIL
      * CV
      * PERFIL
      * FK_ESTATUS
      * CELULAR
      * FK_GENERO
      * CURP
      *** Conversación con prospecto ***
      * FK_ESTADO
      * RECLUTADOR
      * FECHA_CONVERSACION
      * EXPECTATIVAS
      * DISPONIBILIDAD_INTEGRACION
      * DISPONIBILIDAD_ENTREVISTA
      * TRABAJA_ACTUALMENTE
      * FK_CANAL
      * SUELDO_ACTUAL
      * COMENTARIOS
      *** Tecnologías ***
      * Tecnologias
      *** Herramientas ***
      * Herramientas
      *** Habilidades ***
      * Habilidades
      */

        $BajaMenorTresMeses = (new \yii\db\Query())
        ->select([
          'PK_PROSPECTO'
        ])
        ->from('tbl_prospectos')
        ->where(['=','FK_ESTATUS', 1])
        ->andWhere(['=','FK_ORIGEN', 2])
        ->andWhere(['<=','(TIMESTAMPDIFF(DAY, FECHA_REGISTRO + INTERVAL 3 MONTH, NOW()) )',0]);

        $dataProvider = (new \yii\db\Query())
          ->select([
                  /**** Información General ***/
                  'p.PK_PROSPECTO',
                  'CONCAT(p.NOMBRE," ",p.APELLIDO_PATERNO," ",p.APELLIDO_MATERNO) AS NOMBRE',
                  'p.CURP',
                  'p.EDAD',
                  'cg.DESC_GENERO AS GENERO',
                  'p.FECHA_NAC',
                  'p.EMAIL',
                  'p.TELEFONO',
                  'p.CELULAR',
                  //'p.PERFIL',
                  'p.UNIVERSIDAD',
                  'p.CARRERA',
                  'p.CONOCIMIENTOS_TECNICOS',
                  'p.NIVEL_ESCOLARIDAD',
                  'p.CARRERA',
                  //'rh.NOMBRE_RESPONSABLE_RH AS RECLUTADOR',
                    'ur.NOMBRE_COMPLETO AS RECLUTADOR',
                  'p.EXPECTATIVA',

                  /*** Conversación con prospecto ***/
                  'cep.PK_ESTADO_PROSPECTO',
                  'cep.DESC_ESTADO_PROSPECTO',

                  'p.FECHA_CONVERSACION',
                  'est.DESC_ESTADO AS LUGAR_RESIDENCIA',
                  // 'cv.DESC_CV AS TIPO_CV',
                  // 'p.CV',
                  'f.DESC_FUENTE AS FUENTE_VACANTE',
                  'p.DISPONIBILIDAD_INTEGRACION',
                  'p.DISPONIBILIDAD_ENTREVISTA',
                  'p.TRABAJA_ACTUALMENTE',
                  'p.FK_CANAL',
                  'ca.DESC_CANAL as CANAL',
                  'p.SUELDO_ACTUAL',
                  'p.CAPACIDAD_RECURSO',
                  'p.TACTO_CLIENTE',
                  'p.DESEMPENIO_CLIENTE',

                  'cestatusp.PK_ESTATUS_PROSPECTO',
                  'cestatusp.DESC_ESTATUS_PROSPECTO',
                  'p.FK_USUARIO_CHECKOUT',
                  'u.NOMBRE_COMPLETO AS USUARIO_CHECKOUT',
                  'p.FECHA_REGISTRO_CHECKOUT',

                  'p.COMENTARIOS',
                  // 'u.NOMBRE_COMPLETO',
                  'p.FK_ORIGEN',
                  'o.DESC_ORIGEN',
                  'TIMESTAMPDIFF(MONTH, p.FECHA_REGISTRO, NOW()) AS MESES',
                  //'p.FECHA_ACTUALIZACION',
                  'p.FECHA_REGISTRO'
              ])
          ->from('tbl_prospectos as p')
          ->join('left join', 'tbl_cat_estado_prospecto cep',
                  'p.FK_ESTADO = cep.PK_ESTADO_PROSPECTO')
          ->join('left join', 'tbl_cat_estatus_prospecto cestatusp',
                  'p.FK_ESTATUS = cestatusp.PK_ESTATUS_PROSPECTO')
          ->join('left join', 'tbl_cat_genero cg',
                  'p.FK_GENERO= cg.PK_GENERO')
          ->join('left join', 'tbl_usuarios u',
                   'p.FK_USUARIO_CHECKOUT= u.PK_USUARIO')
          ->join('left join', 'tbl_cat_origen o',
                  'p.FK_ORIGEN= o.PK_ORIGEN')
          ->join('left join', 'tbl_cat_canal ca',
                  'p.FK_CANAL= ca.PK_CANAL')
          ->join('left join', 'tbl_cat_estados est',
                   'p.LUGAR_RESIDENCIA = est.PK_ESTADO')
          ->join('left join', 'tbl_cat_fuentes f',
                   'p.FK_FUENTE_VACANTE = f.PK_FUENTE')
          ->join('left join', 'tbl_usuarios ur',
                    'p.RECLUTADOR = ur.PK_USUARIO')
          // ->join('left join', 'tbl_cat_tipo_cv cv',
          //         'p.TIPO_CV= cv.PK_TIPO_CV')

          ->where(['NOT', ['FK_ESTADO' => 8]])
          ->andWhere(['NOT IN', 'p.PK_PROSPECTO', $BajaMenorTresMeses]);


        $posiciones = [];
        $valorFront = [];
        $valorBD = [];
        $mensaje = "";


        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();


            if (!empty($data['Fecha1']) || isset($data['Expectativa1'])) {

                  $data = Yii::$app->request->post();

                if (!empty($data['Fecha1']) && !empty($data['Fecha2'])) {
                    $fecha1 = transform_date($data['Fecha1'],'Y-m-d');
                    $fecha2 = transform_date($data['Fecha2'],'Y-m-d');

                    $dataProvider->andWhere(['between', 'p.FECHA_CONVERSACION', $fecha1, $fecha2]);
                }

                if (isset($data['Expectativa1']) && isset($data['Expectativa2'])) {
                    $exp1 = number_format($data['Expectativa1'], 2, '.', '');
                    $exp2 = number_format($data['Expectativa2'], 2, '.', '');
                    if ($exp1 != 0 || $exp2 != 50000) {
                      $dataProvider->andWhere(['between', 'p.EXPECTATIVA', $exp1, $exp2]);
                    }
                }

                /*Se agregan validaciones y querys para tecnologías*/
                if (!empty($data['Tecnologias']['idTecnologia'])) {
                  $idTecnologia = $data['Tecnologias']['idTecnologia'];

                  $PKkProsTecnologia = Tblprospectostecnologias::find()->select('FK_PROSPECTO')->andWhere(['=', 'FK_TECNOLOGIA', $idTecnologia]);
                  // $dataProvider->andWhere(['IN', 'p.PK_PROSPECTO', $PKkProsTecnologia]);

                  if (!empty($data['Tecnologias']['nivelTech'])){
                    $nivelTech = $data['Tecnologias']['nivelTech'];
                    $PKkProsTecnologia->andWhere(['=', 'NIVEL_EXPERIENCIA', $nivelTech]);
                  }

                  if (!empty($data['Tecnologias']['anioTech'])){
                    $anioTech = $data['Tecnologias']['anioTech'];
                    $PKkProsTecnologia->andWhere(['=', 'TIEMPO_USO', $anioTech]);
                  }

                  $dataProvider->andWhere(['IN', 'p.PK_PROSPECTO', $PKkProsTecnologia]);
                }

                /*Se agregan validaciones y querys para herramientas*/
                if (!empty($data['Herramientas']['idHerramienta'])) {
                  $idHerramienta = $data['Herramientas']['idHerramienta'];

                  $PKkProsHerramienta = Tblprospectosherramientas::find()->select('FK_PROSPECTO')->andWhere(['=', 'FK_HERRAMIENTA', $idHerramienta]);
                  // $dataProvider->andWhere(['IN', 'p.PK_PROSPECTO', $PKkProsHerramienta]);

                  if (!empty($data['Herramientas']['nivelHerr'])){
                    $nivelTech = $data['Herramientas']['nivelHerr'];
                    $PKkProsHerramienta->andWhere(['=', 'NIVEL_EXPERIENCIA', $nivelTech]);
                  }

                  if (!empty($data['Herramientas']['anioHerr'])){
                    $anioTech = $data['Herramientas']['anioHerr'];
                    $PKkProsHerramienta->andWhere(['=', 'TIEMPO_USO', $anioTech]);
                  }

                  $dataProvider->andWhere(['IN', 'p.PK_PROSPECTO', $PKkProsHerramienta]);
                }

                /*Se agregan validaciones y querys para habilidades*/
                if (!empty($data['idHabilidad'])) {
                  $idHabilidad = $data['idHabilidad'];
                  $PKkProsHabilidad = Tblprospectoshabilidades::find()->select('FK_PROSPECTO')->andWhere(['=', 'FK_HABILIDAD', $idHabilidad]);
                  $dataProvider->andWhere(['IN', 'p.PK_PROSPECTO', $PKkProsHabilidad]);
                }

                  // $dataProvider = new ActiveDataProvider([
                  //     'query' => $dataProvider
                  // ]);

                  // \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                  // $res = array(
                  //     'posiciones'=>$posiciones,
                  //     'data'=>$dataProvider->getModels()
                  // );
                  // return $res;
            }
            else if(!empty($data['idProspectos'])){

                foreach ($data['idProspectos'] as $key => $value) {
                    /*
                    * Consulta para obtener los datos del prospecto e ingresarlos en la bitácora
                    * cuando se agrega para evaluación
                    */
                    $prospectos = (new \yii\db\Query())
                    ->select('*')
                    ->from ('tbl_prospectos')
                    ->where(['PK_PROSPECTO' => $value])
                    ->one();

                    /*
                    *Prospecto evaluación
                    */
                    $modelProspectos = TblProspectos::find()->where(['PK_PROSPECTO' => $value])->one();
                    $modelProspectos->FK_USUARIO_CHECKOUT = user_info()['PK_USUARIO'];
                    $modelProspectos->FECHA_REGISTRO_CHECKOUT = date("Y-m-d");
                    $modelProspectos->save(false);

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
                    $modelBitProspecto['RECLUTADOR'] = $prospectos['RECLUTADOR'];
                    $modelBitProspecto['EXPECTATIVA'] = $prospectos['EXPECTATIVA'];
                    $modelBitProspecto['DISPONIBILIDAD_INTEGRACION'] = $prospectos['DISPONIBILIDAD_INTEGRACION'];
                    $modelBitProspecto['DISPONIBILIDAD_ENTREVISTA'] = $prospectos['DISPONIBILIDAD_ENTREVISTA'];
                    $modelBitProspecto['TRABAJA_ACTUALMENTE'] = $prospectos['TRABAJA_ACTUALMENTE'];
                    $modelBitProspecto['CANAL'] = $prospectos['FK_CANAL'];
                    $modelBitProspecto['SUELDO_ACTUAL'] = $prospectos['SUELDO_ACTUAL'];
                    $modelBitProspecto['COMENTARIOS'] = 'PROSPECTO PASA A EVALUACIÓN';
                    $modelBitProspecto['FK_USUARIO'] = user_info()['PK_USUARIO'];
                    $modelBitProspecto['FECHA_REGISTRO'] = date('Y-m-d');
                    $modelBitProspecto->save(false);
                }

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                    'data' => $data
                ];
            }

            $dataProvider = new ActiveDataProvider([
            'query' => $dataProvider,
            'pagination' => false
            ]);
            $dataProvider = $dataProvider->getModels();
            $dataProvider = $this->datosDataProvider($dataProvider);
            // else{
            //
            //   $dataProvider = new ActiveDataProvider([
            //       'query' => $dataProvider
            //   ]);
            // }

            $idPlantillaSel = isset($data['idPlantillaSel']) ? $data['idPlantillaSel'] : 1;


            $connection = \Yii::$app->db;

            $columnasPlantilla = $connection->createCommand("
                SELECT c.FK_CAT_PLANTILLA_PROSPECTOS AS id, c.MOSTRAR_COLUMNA, c.SECUENCIA_DESTINO, c.NOMBRE_COLUMNA, c.LABEL_COLUMNA
                FROM tbl_config_plantillas_prospectos AS c
                WHERE c.FK_CAT_PLANTILLA_PROSPECTOS = ".$idPlantillaSel."
            ")->queryAll();

            //$out['results'] = array_values($registros);
            $cantColumnas = count($columnasPlantilla);

            foreach ($columnasPlantilla as $colPlantilla) {
                array_push($valorFront, $colPlantilla['NOMBRE_COLUMNA']);
                array_push($valorBD, $colPlantilla['LABEL_COLUMNA']);
            }

            $posiciones = array(
                $valorFront,
                $valorBD
            );


            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
              'mensaje'=>$mensaje,
              'columnasPlantilla' => $columnasPlantilla,
              'posiciones'=>$posiciones,
              'data' => $dataProvider,
              'datadata' => $data

            ];
        }


        $connection = \Yii::$app->db;
        $columnasPlantilla = $connection->createCommand("
            SELECT c.FK_CAT_PLANTILLA_PROSPECTOS AS id, c.MOSTRAR_COLUMNA, c.SECUENCIA_DESTINO, c.NOMBRE_COLUMNA, c.LABEL_COLUMNA
            FROM tbl_config_plantillas_prospectos AS c
            WHERE c.FK_CAT_PLANTILLA_PROSPECTOS = 1
        ")->queryAll();

        //$out['results'] = array_values($registros);
        $cantColumnas = count($columnasPlantilla);
        //$mensaje = $columnasPlantilla;
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

        return $this->render('index',[
          //'mensaje'=>$mensaje,
          'columnasPlantilla' => $columnasPlantilla,
          'posiciones'=>$posiciones,
          'dummyVacante' => $dummyVacante
          // 'datos' => $dataProvider
        ]);
    }

    public function actionInformacion_Baja(){
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

   //Funcion ajax para verificar el vinculo de
   //un candidato/prospecto con una vacante

     public function actionVerifica_vacante(){

        if(Yii::$app->request->isAjax){

            $mensajes = "";
            $mensajes2 = "";
            $modelCandidatosVacantes = '';

            $post = Yii::$app->request->post();

            if (Yii::$app->request->post()) {
                $data = Yii::$app->request->post();

                /*Obtener candidato seleccionado*/
                $vacantes = $data['idVacantes'];

                if (!empty($data['idRecurso'])) {

                    $prospectos[] = $data['idRecurso'];

                    foreach ($prospectos as $key => $prospecto){

                        $modelCandidato = TblCandidatos::find()->where(['FK_PROSPECTO' => $prospecto])->one();
                        $candidato = count($modelCandidato);

                        if($candidato == 1){

                                $modelVacantesCandidato = TblVacantesCandidatos::find()->where(['FK_VACANTE' => $vacantes])->andWhere(['FK_CANDIDATO' => $modelCandidato['PK_CANDIDATO']])->all();

                                if(count($modelVacantesCandidato) > 0){

                                    $mensajes = "Este prospecto ya tuvo un proceso cancelado con esta vacante";
                                }else{
                                    $mensajes = 0;
                                }

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

    public function actionConfig_index()
    {
         $data = Yii::$app->request->post();

        $connection = \Yii::$app->db;

        $sqlTblPlantillaProspectosIndex =
        $connection->createCommand("
                SELECT * FROM tbl_cat_plantillas_prospectos
                WHERE NOMBRE_PLANTILLA = 'DEFAULT' OR TIPO_PLANTILLA = 'CONSULTA' ")->queryAll();

        $connection->close();

        return $this->render('config_index', [
        'sqlTblPlantillaProspectosIndex' => $sqlTblPlantillaProspectosIndex,
        'data' => $data,
        ]);
    }


    public function actionPlantillasindex() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
                $connection = \Yii::$app->db;

                $sqlTblCatPlantillasProspectos =
                $connection->createCommand("
                    SELECT * FROM tbl_cat_plantillas_prospectos
                    WHERE TIPO_PLANTILLA = 'CONSULTA' ")->queryAll();

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $connection->close();

            return [
                'sqlTblCatPlantillasProspectos' => $sqlTblCatPlantillasProspectos,
                'data' => $data,
            ];
        }
    }

    public function actionPlantillaid() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $sqlConfigPlantillaProspectoDefault = "";
            $sqlConfigPlantillaProspectoDestino = "";

            if($data){
                $connection = \Yii::$app->db;

                $sqlConfigPlantillaProspecto =
                    $connection->createCommand("
                    SELECT * FROM  tbl_config_plantillas_prospectos cpv WHERE cpv.FK_CAT_PLANTILLA_PROSPECTOS = ".$data['idPlantillaSeleccionada']." ORDER BY cpv.SECUENCIA_ORIGEN ASC")->queryAll();

                $sqlCatPlantilla =
                    $connection->createCommand("
                    SELECT * FROM  tbl_cat_plantillas_prospectos cpv WHERE cpv.PK_CAT_PLANTILLA_PROSPECTOS = ".$data['idPlantillaSeleccionada'])->queryAll();

                //Si la consulta VIENE VACIA significa que se trata de una lista sin configurar y por ello no tiene información de campos en tbl_config_plantillas_vacantes, por lo tanto se necesitan los campos de la lista "Default".
                if(!$sqlConfigPlantillaProspecto){ //Si no se encuentra campos relacionados a la lista seleccionada, se realiza la petición de una lista sin configurar obteniendo los campos de la lista DEFAULT.

                    $sqlConfigPlantillaProspectoDefault =
                    $connection->createCommand("
                    SELECT * FROM  tbl_config_plantillas_prospectos cpv WHERE cpv.FK_CAT_PLANTILLA_PROSPECTOS = 1 ORDER by cpv.SECUENCIA_ORIGEN ASC")->queryAll();

                }else{
                    $sqlConfigPlantillaProspectoDestino =
                    $connection->createCommand("
                    SELECT * FROM  tbl_config_plantillas_prospectos cpv WHERE cpv.FK_CAT_PLANTILLA_PROSPECTOS = ".$data['idPlantillaSeleccionada']." AND cpv.MOSTRAR_COLUMNA = 1 ORDER BY cpv.SECUENCIA_DESTINO ASC")->queryAll();
                }

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $connection->close();
                return $res = array(
                    'sqlConfigPlantillaProspecto' => $sqlConfigPlantillaProspecto,
                    'sqlConfigPlantillaProspectoDestino' => $sqlConfigPlantillaProspectoDestino,
                    'sqlConfigPlantillaProspectoDefault' => $sqlConfigPlantillaProspectoDefault,
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
                    SELECT * FROM  tbl_cat_plantillas_prospectos cpv WHERE cpv.TIPO_PLANTILLA = 'CONSULTA' AND cpv.FK_USUARIO IN (0,".user_info()['PK_USUARIO'].") AND cpv.NOMBRE_PLANTILLA = '".$data['nombrePlantilla']."' ORDER BY cpv.PK_CAT_PLANTILLA_PROSPECTOS DESC")->queryOne();

                if($nombrePlantillaDuplicado == false){
                    $modelCatPlantillasProspectos = new tblcatplantillasprospectos();
                    $modelCatPlantillasProspectos->NOMBRE_PLANTILLA = $data['nombrePlantilla'];
                    $modelCatPlantillasProspectos->TIPO_PLANTILLA = "CONSULTA";
                    $modelCatPlantillasProspectos->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelCatPlantillasProspectos->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelCatPlantillasProspectos->save(false);

                    //Se obtiene el PK de la plantilla que recien se ha creado para insertar los registros de las columnas correspondientes en la tabla tbl_config_plantillas_vacantes
                    $pkPlantillaRecienCreada =
                    $connection->createCommand("
                        SELECT cpv.PK_CAT_PLANTILLA_PROSPECTOS FROM  tbl_cat_plantillas_prospectos cpv WHERE cpv.NOMBRE_PLANTILLA = '".$data['nombrePlantilla']."' ")->queryOne();

                    //Se crea la lista en tbl_config_plantillas_vacantes indicando los campos que ya son visibles por default (MOSTRAR_COLUMNA = 1) y el resto de columnas que podrá manipular el usuario (MOSTRAR_COLUMNA = 0)
                    foreach ($data['listaDefaulEstatica'] as $key => $value) {
                        $modelConfigPlantillasProspectosNueva = new tblconfigplantillasprospectos();
                        $modelConfigPlantillasProspectosNueva->FK_CAT_PLANTILLA_PROSPECTOS = $pkPlantillaRecienCreada['PK_CAT_PLANTILLA_PROSPECTOS'];
                        if($value['value'] == 'NOMBRE' || $value['value'] == 'DESC_ORIGEN' || $value['value'] == 'CV' || $value['value'] == 'RECLUTADOR' || $value['value'] == 'HISTORICO'){
                            $modelConfigPlantillasProspectosNueva->MOSTRAR_COLUMNA = 1;
                            $modelConfigPlantillasProspectosNueva->SECUENCIA_DESTINO = $secuenciaDestino;
                            $secuenciaDestino = $secuenciaDestino + 1;
                        }else{
                            $modelConfigPlantillasProspectosNueva->MOSTRAR_COLUMNA = 0;
                            $modelConfigPlantillasProspectosNueva->SECUENCIA_DESTINO = 0;
                        }
                        $modelConfigPlantillasProspectosNueva->NOMBRE_COLUMNA = $value['value'];
                        $modelConfigPlantillasProspectosNueva->LABEL_COLUMNA = $value['text'];
                        $modelConfigPlantillasProspectosNueva->SECUENCIA_ORIGEN = $secuenciaOrigen;
                        $modelConfigPlantillasProspectosNueva->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelConfigPlantillasProspectosNueva->save(false);
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
                    SELECT * FROM  tbl_cat_plantillas_prospectos cpv WHERE cpv.TIPO_PLANTILLA = 'CONSULTA' AND cpv.FK_USUARIO IN (0,".user_info()['PK_USUARIO'].") AND cpv.NOMBRE_PLANTILLA = '".$data['nombrePlantilla']."' ORDER BY cpv.PK_CAT_PLANTILLA_PROSPECTOS DESC")->queryOne();
                $connection->close();

                if($nombrePlantillaDuplicado == false){
                    $modelCatPlantillasProspectos = tblcatplantillasprospectos::find()->where(['PK_CAT_PLANTILLA_PROSPECTOS' => $data['idNombrePlantilla']])->limit(1)->one();
                    $modelCatPlantillasProspectos->NOMBRE_PLANTILLA = $data['nombrePlantilla'];
                    $modelCatPlantillasProspectos->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelCatPlantillasProspectos->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelCatPlantillasProspectos->save(false);
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
                $modelCatPlantillasProspectos = tblcatplantillasprospectos::find()->where(['PK_CAT_PLANTILLA_PROSPECTOS' => $data['idNombrePlantilla']])->limit(1)->one();
                $modelCatPlantillasProspectos->delete();

                $connection = \Yii::$app->db;
                $plantillaEliminada =
                $connection->createCommand("
                    SELECT * FROM  tbl_cat_plantillas_prospectos cpv WHERE cpv.PK_CAT_PLANTILLA_PROSPECTOS = ".$data['idNombrePlantilla'])->queryOne();

                $connection->createCommand("
                    DELETE FROM tbl_config_plantillas_prospectos WHERE FK_CAT_PLANTILLA_PROSPECTOS = ".$data['idNombrePlantilla'])->execute();

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
                $modelConfigPlantillasProspectos = tblconfigplantillasprospectos::find()->where(['FK_CAT_PLANTILLA_PROSPECTOS' => $idNombrePlantilla])->all();

                //Bloque 'if' que indica que ya existen los registras de la lista seleccionada, se comparan los campos seleccionados y enviados para modificar el valor 'MOSTRAR_COLUMNA' y el orden en que fueron enviados en 'SECUENCIA_ORIGEN'.
                if(count($modelConfigPlantillasProspectos) > 0){
                    foreach ($modelConfigPlantillasProspectos as $key => $value) {
                        $modelConfigPlantillasProspectosUpd = tblconfigplantillasprospectos::find()->where(['FK_CAT_PLANTILLA_PROSPECTOS' => $idNombrePlantilla])->andWhere(['NOMBRE_COLUMNA' => $value->NOMBRE_COLUMNA])->limit(1)->one();
                        $modelConfigPlantillasProspectosUpd->MOSTRAR_COLUMNA = 0;
                        $modelConfigPlantillasProspectosUpd->SECUENCIA_DESTINO = 0;
                        $modelConfigPlantillasProspectosUpd->save(false);
                    }//Fin de foreach

                    foreach ($data['camposSeleccionados'] as $key => $value) {
                        $modelConfigPlantillasProspectosUpd = tblconfigplantillasprospectos::find()->where(['FK_CAT_PLANTILLA_PROSPECTOS' => $idNombrePlantilla])->andWhere(['NOMBRE_COLUMNA' => $value['value']])->limit(1)->one();
                        //Si se encuentra el campo enviado 'NOMBRE_COLUMNA' y el FK_CAT_PLANTILLA_PROSPECTOS de la plantilla en edición, entra a colocar la columna como MOSTRABLE en la consulta general de vacantes.
                        if(isset($modelConfigPlantillasProspectosUpd)){
                            $modelConfigPlantillasProspectosUpd->MOSTRAR_COLUMNA = 1;
                            $modelConfigPlantillasProspectosUpd->SECUENCIA_DESTINO = $secuenciaDestino;
                            $modelConfigPlantillasProspectosUpd->save(false);
                            $secuenciaDestino = $secuenciaDestino + 1;
                        }
                    }//Fin de foreach
                }//Fin de if(count($modelConfigPlantillasProspectos) > 0)
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
        $query->select('c.PK_CAT_PLANTILLA_PROSPECTOS AS id, c.NOMBRE_PLANTILLA AS text')
            ->from('tbl_cat_plantillas_prospectos AS c')
            ->where(['like','c.NOMBRE_PLANTILLA',$q]);
        $command = $query->createCommand();

        $data = $command->queryAll();
        $out['results'] = array_values($data);


        return $out;
    }


    public function actionConfig_index3()
    {
         $data = Yii::$app->request->post();

        $connection = \Yii::$app->db;

        $sqlTblPlantillaProspectosIndex =
        $connection->createCommand("
                SELECT * FROM tbl_cat_plantillas_asignar_prospectos
                WHERE NOMBRE_PLANTILLA = 'DEFAULT' OR TIPO_PLANTILLA = 'CONSULTA' ")->queryAll();

        $connection->close();

        return $this->render('config_index3', [
        'sqlTblPlantillaProspectosIndex' => $sqlTblPlantillaProspectosIndex,
        'data' => $data,
        ]);
    }

    public function actionPlantillasindex3() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
                $connection = \Yii::$app->db;

                $sqlTblCatPlantillasProspectos =
                $connection->createCommand("
                    SELECT * FROM tbl_cat_plantillas_asignar_prospectos
                    WHERE TIPO_PLANTILLA = 'CONSULTA' ")->queryAll();

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $connection->close();

            return [
                'sqlTblCatPlantillasProspectos' => $sqlTblCatPlantillasProspectos,
                'data' => $data,
            ];
        }
    }

    public function actionPlantillaid3() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $sqlConfigPlantillaProspectoDefault = "";
            $sqlConfigPlantillaProspectoDestino = "";

            if($data){
                $connection = \Yii::$app->db;

                $sqlConfigPlantillaProspecto =
                    $connection->createCommand("
                    SELECT * FROM  tbl_config_plantillas_asignar_prospectos cpv WHERE cpv.FK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS = ".$data['idPlantillaSeleccionada']." ORDER BY cpv.SECUENCIA_ORIGEN ASC")->queryAll();

                $sqlCatPlantilla =
                    $connection->createCommand("
                    SELECT * FROM  tbl_cat_plantillas_asignar_prospectos cpv WHERE cpv.PK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS = ".$data['idPlantillaSeleccionada'])->queryAll();

                //Si la consulta VIENE VACIA significa que se trata de una lista sin configurar y por ello no tiene información de campos en tbl_config_plantillas_vacantes, por lo tanto se necesitan los campos de la lista "Default".
                if(!$sqlConfigPlantillaProspecto){ //Si no se encuentra campos relacionados a la lista seleccionada, se realiza la petición de una lista sin configurar obteniendo los campos de la lista DEFAULT.

                    $sqlConfigPlantillaProspectoDefault =
                    $connection->createCommand("
                    SELECT * FROM  tbl_config_plantillas_asignar_prospectos cpv WHERE cpv.FK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS = 1 ORDER by cpv.SECUENCIA_ORIGEN ASC")->queryAll();

                }else{
                    $sqlConfigPlantillaProspectoDestino =
                    $connection->createCommand("
                    SELECT * FROM  tbl_config_plantillas_asignar_prospectos cpv WHERE cpv.FK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS = ".$data['idPlantillaSeleccionada']." AND cpv.MOSTRAR_COLUMNA = 1 ORDER BY cpv.SECUENCIA_DESTINO ASC")->queryAll();
                }

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $connection->close();
                return $res = array(
                    'sqlConfigPlantillaProspecto' => $sqlConfigPlantillaProspecto,
                    'sqlConfigPlantillaProspectoDestino' => $sqlConfigPlantillaProspectoDestino,
                    'sqlConfigPlantillaProspectoDefault' => $sqlConfigPlantillaProspectoDefault,
                    'sqlCatPlantilla' => $sqlCatPlantilla,
                    //'sqlConfigPlantillaVacanteOrigen' => $sqlConfigPlantillaVacanteOrigen,
                );
            }
        }
    }

    public function actionCrearplantilla3() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $secuenciaOrigen = 1;
            $secuenciaDestino = 1;

            if($data){
                //Se valida el nombre que el usuario introdujo para verificar si ya existe en BD, si no existe entra al bloque if para crear una nueva plantilla, de lo contrario sólo se envia la variable para que en la vista se cache y se le notifique al usuario que el nombre que se desea utilizar ya existe y que necesite ingresar uno diferente.
                $connection = \Yii::$app->db;
                $nombrePlantillaDuplicado =
                $connection->createCommand("
                    SELECT * FROM  tbl_cat_plantillas_asignar_prospectos cpv WHERE cpv.TIPO_PLANTILLA = 'CONSULTA' AND cpv.FK_USUARIO IN (0,".user_info()['PK_USUARIO'].") AND cpv.NOMBRE_PLANTILLA = '".$data['nombrePlantilla']."' ORDER BY cpv.PK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS DESC")->queryOne();

                if($nombrePlantillaDuplicado == false){
                    $modelCatPlantillasProspectos = new tblcatplantillasasignarprospectos();
                    $modelCatPlantillasProspectos->NOMBRE_PLANTILLA = $data['nombrePlantilla'];
                    $modelCatPlantillasProspectos->TIPO_PLANTILLA = "CONSULTA";
                    $modelCatPlantillasProspectos->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelCatPlantillasProspectos->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelCatPlantillasProspectos->save(false);

                    //Se obtiene el PK de la plantilla que recien se ha creado para insertar los registros de las columnas correspondientes en la tabla tbl_config_plantillas_vacantes
                    $pkPlantillaRecienCreada =
                    $connection->createCommand("
                        SELECT cpv.PK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS FROM  tbl_cat_plantillas_asignar_prospectos cpv WHERE cpv.NOMBRE_PLANTILLA = '".$data['nombrePlantilla']."' ")->queryOne();

                    //Se crea la lista en tbl_config_plantillas_vacantes indicando los campos que ya son visibles por default (MOSTRAR_COLUMNA = 1) y el resto de columnas que podrá manipular el usuario (MOSTRAR_COLUMNA = 0)
                    foreach ($data['listaDefaulEstatica'] as $key => $value) {
                        $modelConfigPlantillasProspectosNueva = new tblconfigplantillasasignarprospectos();
                        $modelConfigPlantillasProspectosNueva->FK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS = $pkPlantillaRecienCreada['PK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS'];
                        if($value['value'] == 'PROSPECTO' || $value['value'] == 'USUARIO_CHECKOUT' || $value['value'] == 'FECHA_REGISTRO_CHECKOUT' || $value['value'] == 'HISTORICO'){
                            $modelConfigPlantillasProspectosNueva->MOSTRAR_COLUMNA = 1;
                            $modelConfigPlantillasProspectosNueva->SECUENCIA_DESTINO = $secuenciaDestino;
                            $secuenciaDestino = $secuenciaDestino + 1;
                        }else{
                            $modelConfigPlantillasProspectosNueva->MOSTRAR_COLUMNA = 0;
                            $modelConfigPlantillasProspectosNueva->SECUENCIA_DESTINO = 0;
                        }
                        $modelConfigPlantillasProspectosNueva->NOMBRE_COLUMNA = $value['value'];
                        $modelConfigPlantillasProspectosNueva->LABEL_COLUMNA = $value['text'];
                        $modelConfigPlantillasProspectosNueva->SECUENCIA_ORIGEN = $secuenciaOrigen;
                        $modelConfigPlantillasProspectosNueva->FECHA_REGISTRO = date('Y-m-d H:i:s');
                        $modelConfigPlantillasProspectosNueva->save(false);
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

    public function actionEditarnombreplantilla3() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            if($data){

                $connection = \Yii::$app->db;
                $nombrePlantillaDuplicado =
                $connection->createCommand("
                    SELECT * FROM  tbl_cat_plantillas_asignar_prospectos cpv WHERE cpv.TIPO_PLANTILLA = 'CONSULTA' AND cpv.FK_USUARIO IN (0,".user_info()['PK_USUARIO'].") AND cpv.NOMBRE_PLANTILLA = '".$data['nombrePlantilla']."' ORDER BY cpv.PK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS DESC")->queryOne();
                $connection->close();

                if($nombrePlantillaDuplicado == false){
                    $modelCatPlantillasProspectos = tblcatplantillasasignarprospectos::find()->where(['PK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS' => $data['idNombrePlantilla']])->limit(1)->one();
                    $modelCatPlantillasProspectos->NOMBRE_PLANTILLA = $data['nombrePlantilla'];
                    $modelCatPlantillasProspectos->FK_USUARIO = user_info()['PK_USUARIO'];
                    $modelCatPlantillasProspectos->FECHA_REGISTRO = date('Y-m-d H:i:s');
                    $modelCatPlantillasProspectos->save(false);
                }

            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'data' => $data,
                'nombrePlantillaDuplicado' => $nombrePlantillaDuplicado,
            ];
        }
    }

    public function actionEliminarplantilla3() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            if($data){
                $modelCatPlantillasProspectos = tblcatplantillasasignarprospectos::find()->where(['PK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS' => $data['idNombrePlantilla']])->limit(1)->one();
                $modelCatPlantillasProspectos->delete();

                $connection = \Yii::$app->db;
                $plantillaEliminada =
                $connection->createCommand("
                    SELECT * FROM  tbl_cat_plantillas_asignar_prospectos cpv WHERE cpv.PK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS = ".$data['idNombrePlantilla'])->queryOne();

                $connection->createCommand("
                    DELETE FROM tbl_config_plantillas_asignar_prospectos WHERE FK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS = ".$data['idNombrePlantilla'])->execute();

                $connection->close();
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'data' => $data,
                'plantillaEliminada' => $plantillaEliminada,
            ];
        }
    }

    public function actionGuardarplantilla3() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $idNombrePlantilla = $data['idNombrePlantilla'];
            $secuenciaDestino = 1;

            $mensaje = "";

            if($data){
                $modelConfigPlantillasProspectos = tblconfigplantillasasignarprospectos::find()->where(['FK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS' => $idNombrePlantilla])->all();

                //Bloque 'if' que indica que ya existen los registras de la lista seleccionada, se comparan los campos seleccionados y enviados para modificar el valor 'MOSTRAR_COLUMNA' y el orden en que fueron enviados en 'SECUENCIA_ORIGEN'.
                if(count($modelConfigPlantillasProspectos) > 0){
                    foreach ($modelConfigPlantillasProspectos as $key => $value) {
                        $modelConfigPlantillasProspectosUpd = tblconfigplantillasasignarprospectos::find()->where(['FK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS' => $idNombrePlantilla])->andWhere(['NOMBRE_COLUMNA' => $value->NOMBRE_COLUMNA])->limit(1)->one();
                        $modelConfigPlantillasProspectosUpd->MOSTRAR_COLUMNA = 0;
                        $modelConfigPlantillasProspectosUpd->SECUENCIA_DESTINO = 0;
                        $modelConfigPlantillasProspectosUpd->save(false);
                    }//Fin de foreach

                    foreach ($data['camposSeleccionados'] as $key => $value) {
                        $modelConfigPlantillasProspectosUpd = tblconfigplantillasasignarprospectos::find()->where(['FK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS' => $idNombrePlantilla])->andWhere(['NOMBRE_COLUMNA' => $value['value']])->limit(1)->one();
                        //Si se encuentra el campo enviado 'NOMBRE_COLUMNA' y el FK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS de la plantilla en edición, entra a colocar la columna como MOSTRABLE en la consulta general de vacantes.
                        if(isset($modelConfigPlantillasProspectosUpd)){
                            $modelConfigPlantillasProspectosUpd->MOSTRAR_COLUMNA = 1;
                            $modelConfigPlantillasProspectosUpd->SECUENCIA_DESTINO = $secuenciaDestino;
                            $modelConfigPlantillasProspectosUpd->save(false);
                            $secuenciaDestino = $secuenciaDestino + 1;
                        }
                    }//Fin de foreach
                }//Fin de if(count($modelConfigPlantillasProspectos) > 0)
            }//Fin de if($data)

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'data' => $data,
                'mensaje' => $mensaje,
                //'plantillaEliminada' => $plantillaEliminada,
            ];
        }
    }

    public function actionCatplantillas3()
    {
        $data = Yii::$app->request->get();
        $post=null;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';

        $query = new Query;
        $query->select('c.PK_CAT_PLANTILLA_ASIGNAR_PROSPECTOS AS id, c.NOMBRE_PLANTILLA AS text')
            ->from('tbl_cat_plantillas_asignar_prospectos AS c')
            ->where(['like','c.NOMBRE_PLANTILLA',$q]);
        $command = $query->createCommand();

        $data = $command->queryAll();
        $out['results'] = array_values($data);


        return $out;
    }

    public function actionMis_prospectos(){

      /* tablaprospectos
      * PK_PROSPECTO
      * NOMBRE
      * APELLIDO_PATERNO
      * APELLIDO_MATERNO
      **** Información General ***
      * EDAD
      * EMAIL
      * CV
      * PERFIL
      * FK_ESTATUS
      * CELULAR
      * FK_GENERO
      * CURP
      *** Conversación con prospecto ***
      * FK_ESTADO
      * RECLUTADOR
      * FECHA_CONVERSACION
      * EXPECTATIVAS
      * DISPONIBILIDAD_INTEGRACION
      * DISPONIBILIDAD_ENTREVISTA
      * TRABAJA_ACTUALMENTE
      * FK_CANAL
      * SUELDO_ACTUAL
      * COMENTARIOS
      *** Tecnologías ***
      * Tecnologias
      *** Herramientas ***
      * Herramientas
      *** Habilidades ***
      * Habilidades
      */

      $BajaMenorTresMeses = (new \yii\db\Query())
        ->select([
          'PK_PROSPECTO'
        ])
        ->from('tbl_prospectos')
        ->where(['=','FK_ESTATUS', 1])
        ->andWhere(['=','FK_ORIGEN', 2])
        ->andWhere(['<=','(TIMESTAMPDIFF(DAY, FECHA_REGISTRO + INTERVAL 3 MONTH, NOW()) )',0]);


      $dataProvider = (new \yii\db\Query())
          ->select([
                  /**** Información General ***/
                  'p.PK_PROSPECTO',
                  'CONCAT(p.NOMBRE," ",p.APELLIDO_PATERNO," ",p.APELLIDO_MATERNO) AS NOMBRE',
                  'p.EDAD',
                  'p.EMAIL',
                  // 'p.CV',
                  'p.FECHA_NAC',
                  'cestatusp.PK_ESTATUS_PROSPECTO',
                  'cestatusp.DESC_ESTATUS_PROSPECTO',
                  'p.CELULAR',
                  'cg.DESC_GENERO',
                  'p.CURP',

                  /*** Conversación con prospecto ***/
                  'cep.PK_ESTADO_PROSPECTO',
                  'cep.DESC_ESTADO_PROSPECTO',
                  // 'rh.NOMBRE_RESPONSABLE_RH',
                  'ur.NOMBRE_COMPLETO AS RECLUTADOR',
                  'p.FECHA_CONVERSACION',
                  'p.FECHA_REGISTRO',
                  'p.FECHA_REGISTRO_CHECKOUT',
                  'p.EXPECTATIVA',
                  'p.DISPONIBILIDAD_INTEGRACION',
                  'p.DISPONIBILIDAD_ENTREVISTA',
                  'p.TRABAJA_ACTUALMENTE',
                  'ca.DESC_CANAL',
                  'p.SUELDO_ACTUAL',
                  'p.COMENTARIOS',

                  /*FK*/
                  'p.FK_USUARIO_CHECKOUT',
                  'u.NOMBRE_COMPLETO',
                  'p.FK_ORIGEN',
                  'TIMESTAMPDIFF(MONTH, p.FECHA_REGISTRO, NOW()) AS MESES',
                  'o.DESC_ORIGEN'
              ])
          ->from('tbl_prospectos as p')
          ->join('left join', 'tbl_cat_estado_prospecto cep',
                  'p.FK_ESTADO = cep.PK_ESTADO_PROSPECTO')
          ->join('left join', 'tbl_cat_estatus_prospecto cestatusp',
                  'p.FK_ESTATUS = cestatusp.PK_ESTATUS_PROSPECTO')
          ->join('left join', 'tbl_cat_genero cg',
                  'p.FK_GENERO= cg.PK_GENERO')
          ->join('left join', 'tbl_usuarios u',
                   'p.FK_USUARIO_CHECKOUT= u.PK_USUARIO')
                   ->join('left join', 'tbl_usuarios ur',
                            'p.RECLUTADOR = ur.PK_USUARIO')
          ->join('left join', 'tbl_cat_origen o',
                  'p.FK_ORIGEN= o.PK_ORIGEN')
          ->join('left join', 'tbl_cat_canal ca',
                  'p.FK_CANAL = ca.PK_CANAL')

          ->where(['NOT', ['FK_ESTADO' => 8]])
          ->andWhere(['NOT IN', 'p.PK_PROSPECTO', $BajaMenorTresMeses])
          ->andWhere(['=', 'p.FK_USUARIO_CHECKOUT', user_info()['PK_USUARIO']]);


          /**/
          $valorFront = array(
            'Prospecto',
            'CURP',
            'Correo',
            'Celular',
            'Estatus',
            'ESTADO',
            'Reclutador',
            'Fecha Conversación',
            'Canal',
            'Comentarios',
            'Usuario',
            'Origen',
            'Historico'
          );
          $valorBD = array(
            'NOMBRE',
            'CURP',
            'EMAIL',
            'CELULAR',
            'DESC_ESTATUS_PROSPECTO',
            'DESC_ESTADO_PROSPECTO',
            'RECLUTADOR',
            'FECHA_CONVERSACION',
            'DESC_CANAL',
            'COMENTARIOS',
            'NOMBRE_COMPLETO',
            'DESC_ORIGEN',
            'HISTORICO'
          );
          $posiciones = array(
            $valorFront,
            $valorBD
          );


            if (Yii::$app->request->isAjax) {

                $data = Yii::$app->request->post();

                if(!empty($data['idProspectos'])){
                    foreach ($data['idProspectos'] as $key => $value) {
                    /*
                    * Consulta para obtener los datos del prospecto e ingresarlos en la bitácora
                    * cuando se agrega para evaluación
                    */
                    $prospectos = (new \yii\db\Query())
                    ->select('*')
                    ->from ('tbl_prospectos')
                    ->where(['PK_PROSPECTO' => $value])
                    ->one();

                    /*
                    *Prospecto evaluación
                    */
                    $modelProspectos = TblProspectos::find()->where(['PK_PROSPECTO' => $value])->one();
                    $modelProspectos->FK_USUARIO_CHECKOUT = 0;
                    $modelProspectos->FECHA_REGISTRO_CHECKOUT = '';
                    $modelProspectos->save(false);

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
                    $modelBitProspecto['RECLUTADOR'] = $prospectos['RECLUTADOR'];
                    $modelBitProspecto['EXPECTATIVA'] = $prospectos['EXPECTATIVA'];
                    $modelBitProspecto['DISPONIBILIDAD_INTEGRACION'] = $prospectos['DISPONIBILIDAD_INTEGRACION'];
                    $modelBitProspecto['DISPONIBILIDAD_ENTREVISTA'] = $prospectos['DISPONIBILIDAD_ENTREVISTA'];
                    $modelBitProspecto['TRABAJA_ACTUALMENTE'] = $prospectos['TRABAJA_ACTUALMENTE'];
                    $modelBitProspecto['CANAL'] = $prospectos['FK_CANAL'];
                    $modelBitProspecto['SUELDO_ACTUAL'] = $prospectos['SUELDO_ACTUAL'];
                    $modelBitProspecto['COMENTARIOS'] = 'PROSPECTO PASA A EVALUACIÓN';
                    $modelBitProspecto['FK_USUARIO'] = user_info()['PK_USUARIO'];
                    $modelBitProspecto['FECHA_REGISTRO'] = date('Y-m-d');
                    $modelBitProspecto->save(false);
                  }

                  \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                  return [
                    'data' => $data
                  ];
                }

                $dataProvider = new ActiveDataProvider([
                'query' => $dataProvider
                ]);
                $dataProvider = $dataProvider->getModels();
                $dataProvider = $this->datosDataProvider($dataProvider, 1);



                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                  'posiciones' => $posiciones,
                  'data' => $dataProvider
                ];
            }

                /*foreach ($prospectos as $key => $value) {
                    $prospectos[$key]['PROSPECTO'] = "<a href='#' id='prospecto' data-toggle='modal' data-target='#InformacionProspecto' data-prospecto=".$prospectos[$key]['PK_PROSPECTO']." >".$prospectos[$key]['PROSPECTO']."</a>";
                    //Si FK_ORIGEN es 2 quiere decir que viene de empleados y es baja
                    if ($prospectos[$key]['FK_ORIGEN'] == 2) {
                      $prospectos[$key]['DESC_ESTATUS_PROSPECTO'] = "<a id='baja_empleado' href='#' data-toggle='modal' data-target='#bajaempleado'  data-prospecto=".$prospectos[$key]['PK_PROSPECTO'].">". $prospectos[$key]['DESC_ESTATUS_PROSPECTO'] ."</a>";
                    }
                    else if ($prospectos[$key]['FK_ORIGEN'] == 3) {
                      $prospectos[$key]['DESC_ESTATUS_PROSPECTO'] = "<a id='cancelado' href='#' data-toggle='modal' data-target='#modal-cancelado' data-prospecto=".$prospectos[$key]['PK_PROSPECTO'].">". $prospectos[$key]['DESC_ESTATUS_PROSPECTO'] ."</a>";
                    }
                }*/

        $dataProvider = new ActiveDataProvider([
            'query' => $dataProvider
        ]);
        $dataProvider = $dataProvider->getModels();
        $dataProvider = $this->datosDataProvider($dataProvider);

        foreach ($dataProvider as $key => $value) {

          $dataProvider[$key]['NOMBRE'] = "<a href='#' id='prospecto' data-toggle='modal' data-target='#InformacionProspecto' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO']." >".$dataProvider[$key]['NOMBRE']."</a>";
            //$dataProvider[$key]['NOMBRE'] = '<a href="index4?id='.$dataProvider[$key]['PK_PROSPECTO'].'">'.$dataProvider[$key]['NOMBRE'].'</a>';
            // $dataProvider[$key]['HISTORICO'] = '<a href="#">Ver detalle</a>';

            /*Si FK_ORIGEN es 2 quiere decir que viene de empleados y es baja*/
            if ($dataProvider[$key]['FK_ORIGEN'] == 2) {
              $dataProvider[$key]['DESC_ESTATUS_PROSPECTO'] = "<a id='baja_empleado' href='#' data-toggle='modal' data-target='#estatusProspecto'  data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">". $dataProvider[$key]['DESC_ESTATUS_PROSPECTO'] ."</a>";
            }
            else if ($dataProvider[$key]['FK_ORIGEN'] == 3) {
              $dataProvider[$key]['DESC_ESTATUS_PROSPECTO'] = "<a id='cancelado' href='#' data-toggle='modal' data-target='#estatusProspecto' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">". $dataProvider[$key]['DESC_ESTATUS_PROSPECTO'] ."</a>";
            }



            $dataProvider[$key]['HISTORICO'] = '<p><a class="historicoProspecto" href="#"  data-toggle="modal" data-target="#HistorialProspecto" data-vacante="'.$dataProvider[$key]['PK_PROSPECTO'].'">Ver detalle</a></p>';

            /*Agregar tecnologías al dataProvider*/
            $tecnologias = (new \yii\db\Query())
              ->select([
                'CatTec.DESC_TECNOLOGIA'
              ])
              ->from('tbl_prospectos as p')
              ->join('INNER JOIN', 'tbl_prospectos_tecnologias AspTec',
                      'p.PK_PROSPECTO = AspTec.FK_PROSPECTO')
              ->join('INNER JOIN', 'tbl_cat_tecnologias CatTec',
                      'AspTec.FK_TECNOLOGIA = CatTec.PK_TECNOLOGIA')
              ->where(['AspTec.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
              ->all();

            $dataProvider[$key]['TECNOLOGIAS'] = "<a id='datosProspectoTH' href='#!' data-toggle='modal' data-target='#datosth' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO']." data-value='1'>Ver Tecnologías</a>";

            $herramientas = (new \yii\db\Query())
              ->select([
                'CatHer.DESC_HERRAMIENTA'
              ])
              ->from('tbl_prospectos as p')
              ->join('INNER JOIN', 'tbl_prospectos_herramientas AspHer',
                      'p.PK_PROSPECTO = AspHer.FK_PROSPECTO')
              ->join('INNER JOIN', 'tbl_cat_herramientas CatHer',
                      'AspHer.FK_HERRAMIENTA = CatHer.PK_HERRAMIENTA')
              ->where(['AspHer.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
              ->all();

            $dataProvider[$key]['HERRAMIENTAS'] = "<a id='datosProspectoTH' href='#!' data-toggle='modal' data-target='#datosth' data-value='2' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">Ver Herramientas</a>";

            $habilidades = (new \yii\db\Query())
              ->select([
                'CatHab.DESC_HABILIDAD'
              ])
              ->from('tbl_prospectos as p')
              ->join('INNER JOIN', 'tbl_prospectos_habilidades AspHab',
                      'p.PK_PROSPECTO = AspHab.FK_PROSPECTO')
              ->join('INNER JOIN', 'tbl_cat_habilidades CatHab',
                      'AspHab.FK_HABILIDAD = CatHab.PK_HABILIDAD')
              ->where(['AspHab.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
              ->all();

            $dataProvider[$key]['HABILIDADES'] = "<a id='datosProspectoPH' href='#!' data-toggle='modal' data-target='#datosph' data-value='3' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">Ver Habilidades</a>";

            $perfil = (new \yii\db\Query())
              ->select([
                'CatPer.DESCRIPCION'
              ])
              ->from('tbl_prospectos as p')
              ->join('INNER JOIN', 'tbl_prospectos_perfiles PP',
                      'p.PK_PROSPECTO = PP.FK_PROSPECTO')
              ->join('INNER JOIN', 'tbl_cat_perfiles CatPer',
                      'PP.FK_PERFIL = CatPer.PK_PERFIL')
              ->where(['PP.FK_PROSPECTO' =>  $dataProvider[$key]['PK_PROSPECTO']])
              ->all();

          $dataProvider[$key]['PERFIL'] = "<a id='datosProspectoPH' href='#!' data-toggle='modal' data-target='#datosph' data-value='4' data-prospecto=".$dataProvider[$key]['PK_PROSPECTO'].">Ver Perfiles</a>";

            $examenes = (new \yii\db\Query())
              ->select([
                'ex.FK_EXAMEN',
                'ex.VALOR'
              ])
              ->from('tbl_prospectos as p')
              ->join('INNER JOIN', 'tbl_prospectos_examenes ex',
                      'p.PK_PROSPECTO = ex.FK_PROSPECTO')
              ->where(['ex.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
              ->all();

          $examenes = (new \yii\db\Query())
            ->select([
              'ex.FK_EXAMEN',
              'ex.VALOR'
            ])
            ->from('tbl_prospectos as p')
            ->join('INNER JOIN', 'tbl_prospectos_examenes ex',
                    'p.PK_PROSPECTO = ex.FK_PROSPECTO')
            ->where(['ex.FK_PROSPECTO' => $dataProvider[$key]['PK_PROSPECTO']])
            ->all();

            $ProspectosExamenes = "";
            if (empty($examenes) ) {
              $ProspectosExamenes = "";
            }
            else {
              $ProspectosExamenes = "<p>";
              foreach ($examenes as $tkey => $tvalue) {
                $ProspectosExamenes .= $examenes[$tkey]['FK_EXAMEN'].'<br>';
              }
              $ProspectosExamenes .= "</p>";
            }
          $dataProvider[$key]['EXAMENES'] = $ProspectosExamenes;

        }
        $dummyVacante = new tblvacantes;
        return $this->render('mis_prospectos',[
            'posiciones' => $posiciones,
            'dummyVacante' => $dummyVacante
            //'datos' => $dataProvider
        ]);

    }


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
            //'bitP.PERFIL',
            'bitP.FECHA_CONVERSACION',
            'cep.DESC_ESTADO_PROSPECTO',
            'bitP.RECLUTADOR',
            'rh.NOMBRE_RESPONSABLE_RH',
            'bitP.EXPECTATIVA',
            'bitP.DISPONIBILIDAD_INTEGRACION',
            'bitP.DISPONIBILIDAD_ENTREVISTA',
            'bitP.TRABAJA_ACTUALMENTE',
            'ccanal.DESC_CANAL',
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
          ->join('left join', 'tbl_cat_responsables_rh rh',
                  'p.RECLUTADOR = rh.PK_RESPONSABLE_RH')
          ->join('left join', 'tbl_cat_canal ccanal',
                  'p.FK_CANAL= ccanal.PK_CANAL')
                  ->andWhere(['=', 'bitP.FK_PROSPECTO', $data['PK_PROSPECTO']])
    /////->where(['NOT', ['FK_ESTATUS' => 6]])
          ->all();

          foreach ($dataProvider as $key => $value) {
            $spanFechaConversacion = '';
            if ($dataProvider[$key]['FECHA_CONVERSACION'] != '') {
              $dateFechaConversacion = str_replace('/', '-', $dataProvider[$key]['FECHA_CONVERSACION']);
              $spanFechaConversacion = date('Y-m-d', strtotime($dateFechaConversacion));
              $spanFechaConversacion = str_replace('-', '', $spanFechaConversacion);
              $dataProvider[$key]['FECHA_CONVERSACION'] = transform_date($dataProvider[$key]['FECHA_CONVERSACION'],'d/m/Y');
            }
            $dataProvider[$key]['FECHA_CONVERSACION'] = '<span class="hide">'.$spanFechaConversacion.'</span>'.$dataProvider[$key]['FECHA_CONVERSACION'];

            $perfil = (new \yii\db\Query())
              ->select([
                'CatPer.DESCRIPCION'
              ])
              ->from('tbl_prospectos as p')
              ->join('INNER JOIN', 'tbl_prospectos_perfiles PP',
                      'p.PK_PROSPECTO = PP.FK_PROSPECTO')
              ->join('INNER JOIN', 'tbl_cat_perfiles CatPer',
                      'PP.FK_PERFIL = CatPer.PK_PERFIL')
              ->where(['PP.FK_PROSPECTO' => $data['PK_PROSPECTO']])
              ->all();

              $ProspectosPerfiles = "";
              if (empty($perfil) ) {
                $ProspectosPerfiles = "";
              }
              else {
                $ProspectosPerfiles = "";
                foreach ($perfil as $pkey => $pvalue) {
                  $ProspectosPerfiles .= $perfil[$pkey]['DESCRIPCION'].'<br>';
                }
                //$ProspectosPerfiles .= "</p>";
              }
            $dataProvider[$key]['PERFIL'] = $ProspectosPerfiles;
          }

    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return [
        'dataProvider' => $dataProvider
    ];
    }
  }

  public function actionBaja_recurso(){
      if (Yii::$app->request->isAjax) { // Si la peticion viene de ajax
          $data = Yii::$app->request->post();
          $dataProvider = (new \yii\db\Query())
          ->select([
            /**** Información General INGRESA LOS DATOS QUE USARAS DE LAS TABLAS***/
              'bitP.PK_PROSPECTO',
              'bitP.FK_ESTADO',
              'bitP.TACTO_CLIENTE',
              'bitP.DESEMPENIO_CLIENTE',
              'bitP.CAPACIDAD_RECURSO',
              'comEmp.COMENTARIOS',
              'bitP.COMENTARIOS AS COMENTARIOPROSPECTO',
              'cat.DESC_CATEGORIA',
              'subcat.DESC_SUBCATEGORIA',
              'comEmp.FECHA_BAJA'
            ])
              /**** CREA LOS JOIN QDE LOS CAMPOS/TABLAS QUE USARAS***/
              ->FROM ('tbl_prospectos bitP')
              ->join ('left join','tbl_perfil_empleados AS perEmp','bitP.PK_PROSPECTO = perEmp.FK_PROSPECTO')
              ->join ('left join','tbl_bit_comentarios_empleados AS comEmp','comEmp.FK_EMPLEADO = perEmp.FK_EMPLEADO')
              ->join ('left join','tbl_cat_categoria AS cat','comEmp.MOTIVO_CAT = cat.PK_CATEGORIA')
              ->join ('left join','tbl_cat_subcategoria AS subcat','comEmp.MOTIVO_CAT = subcat.PK_SUBCATEGORIA')
              ->andWhere(['=', 'bitP.PK_PROSPECTO', $data['PK_PROSPECTO']])//ATENCION AQUÍ
              ->all();

              foreach ($dataProvider as $key => $value) {
                $spanFechaBaja = '';
                if ($dataProvider[$key]['FECHA_BAJA'] != '') {
                  $dateFechaBaja = str_replace('/', '-', $dataProvider[$key]['FECHA_BAJA']);
                  $spanFechaBaja = date('Y-m-d', strtotime($dateFechaBaja));
                  $spanFechaBaja = str_replace('-', '', $spanFechaBaja);
                  $dataProvider[$key]['FECHA_BAJA'] = transform_date($dataProvider[$key]['FECHA_BAJA'],'d/m/Y');
                }
                $dataProvider[$key]['FECHA_BAJA'] = '<span class="hide">'.$spanFechaBaja.'</span>'.$dataProvider[$key]['FECHA_BAJA'];
              }

        $perfiles = (new \yii\db\Query())
          ->select([
            'CatPer.DESCRIPCION',
            'crt.DESC_RANK_TECNICO'
          ])
          ->from('tbl_prospectos as p')
          ->join('INNER JOIN', 'tbl_prospectos_perfiles AspPer',
                  'p.PK_PROSPECTO = AspPer.FK_PROSPECTO')
          ->join('INNER JOIN', 'tbl_cat_perfiles CatPer',
                  'AspPer.FK_PERFIL = CatPer.PK_PERFIL')
          ->join ('left join','tbl_cat_rank_tecnico AS crt',
          'AspPer.NIVEL_EXPERIENCIA=crt.PK_RANK_TECNICO')
          ->andWhere(['=', 'AspPer.FK_PROSPECTO', $data['PK_PROSPECTO']])
          ->all();

      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return [
          'dataProvider' => $dataProvider,
          'perfiles' => $perfiles
      ];
      }
    }

    public function actionDetalle_prospecto()
    {
      $idProspecto = Yii::$app->request->get('id');
      $recurso = Yii::$app->request->get('recurso');

      if (Yii::$app->request->isAjax) {
        $data= Yii::$app->request->post();

        /*
        * Validación para saber si es prospecto o candidato
        * -- Prospecto: recurso = 1
        * -- Candidato: recurso = 2
        */
        if ($data['recurso'] == 2) {
          $tblPrincipal         = tblcandidatos::find();
          $tblDocumentos        = TblCandidatosDocumentos::find();
          $tblDocumentosCreate  = new TblCandidatosDocumentos();
          $tblPerfiles          = Tblcandidatosperfiles::find();
          $tblPerfilesCreate    = new Tblcandidatosperfiles;
          $tblTecnologias       = Tblcandidatostecnologias::find();
          $tblTecnologiasCreate = new Tblcandidatostecnologias;
          $tblHerramientas       = Tblcandidatosherramientas::find();
          $tblHerramientasCreate = new Tblcandidatosherramientas;
          $tblHabilidades       = Tblcandidatoshabilidades::find();
          $tblHabilidadesCreate = new Tblcandidatoshabilidades;
          $PK                   = 'PK_CANDIDATO';
          $FK                   = 'FK_CANDIDATO';
          $FechaNac             = "FECHA_NAC_CAN";
          $FkEstatus            = "FK_ESTATUS_PROSPECTO";
          $ruta                 = "CandidatosCV";
          $index                = "../candidatos/index2";
        }
        else{
          $tblPrincipal         = TblProspectos::find();
          $tblDocumentos        = TblProspectosDocumentos::find();
          $tblDocumentosCreate  = new TblProspectosDocumentos();
          $tblExamenes          = TblProspectosExamenes::find();
          $tblExamenesCreate    = new TblProspectosExamenes();
          $tblPerfiles          = Tblprospectosperfiles::find();
          $tblPerfilesCreate    = new Tblprospectosperfiles;
          $tblTecnologias       = Tblprospectostecnologias::find();
          $tblTecnologiasCreate = new Tblprospectostecnologias;
          $tblHerramientas       = Tblprospectosherramientas::find();
          $tblHerramientasCreate = new Tblprospectosherramientas;
          $tblHabilidades       = Tblprospectoshabilidades::find();
          $tblHabilidadesCreate = new Tblprospectoshabilidades;
          $datosTipoPsi = TblCatExamenes::find();
          $PK                   = 'PK_PROSPECTO';
          $FK                   = 'FK_PROSPECTO';
          $FechaNac             = "FECHA_NAC";
          $FkEstatus            = "FK_ESTATUS";
          $ruta                 = "ProspectosCV";
          $index                = "index";
        }

        $datos = "";
        $modelRecurso = $tblPrincipal->where([$PK => $data['idRecurso']])->one();
        if (!empty($data['datosgrales'])) {

          $datosTipoCV = TblCatTipoCV::find()->orderBy(['PK_TIPO_CV'=>SORT_ASC])->all();

          // \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
          // return [
          //     'datosEnviados' => $data['cvs'],
          // ];


          foreach ($_FILES['file']["name"] as $keyFILE => $valueFILE) {

              if (!empty($valueFILE)) {

                  if ($data['cvs'][$keyFILE][1] == "eliminar" || $data['cvs'][$keyFILE][1] == "editar") {
                    $modelEliminar = $tblDocumentos->where([$FK => $data['idRecurso'], 'FK_TIPO_CV' => $datosTipoCV[$keyFILE]['PK_TIPO_CV']])->one();
                    $archivoEliminar = substr($modelEliminar['RUTA_CV'], 3, strlen($modelEliminar['RUTA_CV']));
                    unlink($archivoEliminar);
                    $modelEliminar->delete();
                  }
                  // $keyArch = $keyFILE + 1;
                  $tmp_name = $_FILES["file"]["tmp_name"][$keyFILE];
                  $infoFile = pathInfo($_FILES["file"]["name"][$keyFILE]);
                  $DESC_CV = $datosTipoCV[$keyFILE]['DESC_CV'];
                  /*Cambio de nombre*/
                  $nombre = 'CV'.$DESC_CV.'_'.$data['idRecurso'].'_'.date('Y-m-d').'.'.$infoFile['extension'];
                  /*Subida de archivo*/
                  move_uploaded_file($tmp_name, "../uploads/".$ruta."/$nombre");

                  $rutaGuardado = '../uploads/'.$ruta.'/';
                  $modelProspectosDocumentos = new $tblDocumentosCreate;
                  $modelProspectosDocumentos->$FK             = $data['idRecurso'];
                  $modelProspectosDocumentos->FK_TIPO_CV      = $datosTipoCV[$keyFILE]['PK_TIPO_CV'];
                  $modelProspectosDocumentos->RUTA_CV         = '../'.$rutaGuardado.''.$nombre;
                  $modelProspectosDocumentos->FECHA_REGISTRO  = date('Y-m-d');
                  if (!empty($modelRecurso['FK_PROSPECTO'])) {
                    $modelProspectosDocumentos['FK_PROSPECTO'] = $modelRecurso['FK_PROSPECTO'];
                  }
                  $modelProspectosDocumentos->save(false);
              }
              else {
                if ($data['cvs'][$keyFILE][1] == "eliminar") {
                  $modelEliminar = $tblDocumentos->where([$FK => $data['idRecurso'], 'FK_TIPO_CV' => $datosTipoCV[$keyFILE]['PK_TIPO_CV']])->one();
                  $archivoEliminar = substr($modelEliminar['RUTA_CV'], 3, strlen($modelEliminar['RUTA_CV']));
                  unlink($archivoEliminar);
                  $modelEliminar->delete();
                }
              }
          }



          /* Se edita el prospecto y/o candidato */
          // $modelRecurso = $tblPrincipal->where([$PK => $data['idRecurso']])->one();
          $modelRecurso->NOMBRE = $data['datosgrales'][0];
          $modelRecurso->APELLIDO_PATERNO = $data['datosgrales'][1];
          $modelRecurso->APELLIDO_MATERNO = $data['datosgrales'][2];
          $modelRecurso->CURP = $data['datosgrales'][3];
          $modelRecurso->$FechaNac = transform_date($data['datosgrales'][4],'Y-m-d');
          $modelRecurso->EMAIL = $data['datosgrales'][5];
          $modelRecurso->CELULAR = $data['datosgrales'][6];
          $modelRecurso->TELEFONO = $data['datosgrales'][7];
          $modelRecurso->EDAD = $data['datosgrales'][8];
          $modelRecurso->$FkEstatus = $data['datosgrales'][9];
          $modelRecurso->LUGAR_RESIDENCIA = $data['datosgrales'][10];
          //$modelRecurso['TIPO_CV'] = $data['datosgrales'][11];
          $modelRecurso->FK_GENERO = $data['datosgrales'][11];
          $modelRecurso->FK_FUENTE_VACANTE = $data['datosgrales'][12];
          $modelRecurso->save(false);
        }

        $deletePerfiles = $tblPerfiles->where([$FK => $data['idRecurso']])->all();
        foreach ($deletePerfiles as $deleteP) {
          $deleteP->delete();
        }

        if (isset($data['datosPerfiles'])) {
          foreach ($data['datosPerfiles'] as $keyP => $valueP) {
            $modelProsPerf = new $tblPerfilesCreate;
            $modelProsPerf->FK_PERFIL = $valueP;
            $modelProsPerf->$FK = $data['idRecurso'];
            $modelProsPerf->FECHA_REGISTRO = date('Y-m-d');
            if (!empty($modelRecurso['FK_PROSPECTO'])) {
              $modelProsPerf['FK_PROSPECTO'] = $modelRecurso['FK_PROSPECTO'];
            }
            $modelProsPerf->save(false);
          }
        }

        if (!empty($data['datosTecnologias'])) {
          $deleteTecnologias = $tblTecnologias->where([$FK => $data['idRecurso']])->all();
          foreach ($deleteTecnologias as $deleteT) {
            $deleteT->delete();
          }
          foreach ($data['datosTecnologias']['checkedTech'] as $keyTec => $valueTec) {
            $modelProsTecno = new $tblTecnologiasCreate;
            $modelProsTecno['FK_TECNOLOGIA'] = $valueTec;
            $modelProsTecno[$FK] = $data['idRecurso'];
            $modelProsTecno['NIVEL_EXPERIENCIA'] = $data['datosTecnologias']['nivelTech'][$keyTec];
            $modelProsTecno['FECHA_REGISTRO'] = date('Y-m-d');
            $modelProsTecno['TIEMPO_USO'] = $data['datosTecnologias']['aniosTech'][$keyTec];
            if (!empty($modelRecurso['FK_PROSPECTO'])) {
              $modelProsTecno['FK_PROSPECTO'] = $modelRecurso['FK_PROSPECTO'];
            }
            $modelProsTecno->save(false);
          }
        }
        if (!empty($data['datosHerramientas'])) {

          $deleteHerramientas = $tblHerramientas->where([$FK => $data['idRecurso']])->all();
          if (!empty($deleteTecnologias[0]['FK_PROSPECTO'])) {
            $FK_PRO_TEC = $deleteTecnologias[0]['FK_PROSPECTO'];
          }
          foreach ($deleteHerramientas as $deleteHerramientas) {
            $deleteHerramientas->delete();
          }

          foreach ($data['datosHerramientas']['checkedHerr'] as $keyHerr => $valueHerr) {
            $modelProsHerr = new $tblHerramientasCreate;
            $modelProsHerr['FK_HERRAMIENTA'] = $valueHerr;
            $modelProsHerr[$FK] = $data['idRecurso'];
            $modelProsHerr['NIVEL_EXPERIENCIA'] = $data['datosHerramientas']['nivelHerr'][$keyHerr];
            $modelProsHerr['FECHA_REGISTRO'] = date('Y-m-d');
            $modelProsHerr['TIEMPO_USO'] = $data['datosHerramientas']['aniosHerr'][$keyHerr];
            if (!empty($modelRecurso['FK_PROSPECTO'])) {
              $modelProsHerr['FK_PROSPECTO'] = $modelRecurso['FK_PROSPECTO'];
            }
            $modelProsHerr->save(false);
          }
        }

        if (!empty($data['datosHabilidades'])) {
          $deleteHabilidades = $tblHabilidades->where([$FK => $data['idRecurso']])->all();
          foreach ($deleteHabilidades as $deleteHabilidades) {
            $deleteHabilidades->delete();
          }

          foreach ($data['datosHabilidades'] as $keyHabi => $valueHabi) {
            $modelProsHabi = new $tblHabilidadesCreate;
            $modelProsHabi['FK_HABILIDAD'] = $valueHabi;
            $modelProsHabi[$FK] = $data['idRecurso'];
            $modelProsHabi['FECHA_REGISTRO'] = date('Y-m-d');
            if (!empty($modelRecurso['FK_PROSPECTO'])) {
              $modelProsHabi['FK_PROSPECTO'] = $modelRecurso['FK_PROSPECTO'];
            }
            $modelProsHabi->save(false);
          }
        }


        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'datosEnviados' => $data,
            'datos'         => $datos,
            'index'         => $index
        ];
      }

      $dataProvider = $this->candidato_prospecto($recurso, $idProspecto);
      //
      // $data = array(
      //   'modelTecnologias'  => $modelTecnologias,
      //   'modelHerramientas' => $modelHerramientas,
      //   'modelHabilidades'  => $modelHabilidades,
      //   'modelPerfiles'     => $modelPerfiles,
      //   'prospecto'           => $dataProvider,
      //   'CVs'               => $modelCurriculim
      // );

      return $this->render('detalleprospecto',[
        'modelTecnologias'  => $dataProvider['modelTecnologias'],
        'modelHerramientas' => $dataProvider['modelHerramientas'],
        'modelHabilidades'  => $dataProvider['modelHabilidades'],
        'modelPerfiles'     => $dataProvider['modelPerfiles'],
        'prospecto'         => $dataProvider['prospecto'],
        'CVs'               => $dataProvider['CVs'],
        'titulo'            => $dataProvider['titulo'],
        'sqlReadExamenesPsi' => $dataProvider['sqlReadExamenesPsi'],
        'sqlReadExamenesTec' => $dataProvider['sqlReadExamenesTec'],
        'ExamenesTec' => $dataProvider['ExamenesTec'],
        'ExamenesPsi' => $dataProvider['ExamenesPsi']


      ]);
    }



    public function actionInfo_prospecto(){
        if (Yii::$app->request->isAjax) { // Si la peticion viene de ajax
            $data = Yii::$app->request->post();
            $idProspecto= $data['idProspecto'];
            $infoth = $data['infoth'];

            $tecnologias = (new \yii\db\Query())
              ->select([
                'CatTec.DESC_TECNOLOGIA AS DESC',
                'crt.DESC_RANK_TECNICO',
                'AspPer.TIEMPO_USO'
              ])
              ->from('tbl_prospectos as p')
              ->join('INNER JOIN', 'tbl_prospectos_tecnologias AspPer',
                      'p.PK_PROSPECTO = AspPer.FK_PROSPECTO')
              ->join('INNER JOIN', 'tbl_cat_tecnologias CatTec',
                      'AspPer.FK_TECNOLOGIA = CatTec.PK_TECNOLOGIA')
              ->join ('left join','tbl_cat_rank_tecnico AS crt',
              'AspPer.NIVEL_EXPERIENCIA=crt.PK_RANK_TECNICO')
              ->andWhere(['=', 'AspPer.FK_PROSPECTO', $idProspecto])
              ->all();

              $herramientas = (new \yii\db\Query())
                ->select([
                  'CatHer.DESC_HERRAMIENTA AS DESC',
                  'crt.DESC_RANK_TECNICO',
                  'AspHer.TIEMPO_USO'
                ])
                ->from('tbl_prospectos as p')
                ->join('INNER JOIN', 'tbl_prospectos_herramientas AspHer',
                        'p.PK_PROSPECTO = AspHer.FK_PROSPECTO')
                ->join('INNER JOIN', 'tbl_cat_herramientas CatHer',
                        'AspHer.FK_HERRAMIENTA = CatHer.PK_HERRAMIENTA')
                ->join ('left join','tbl_cat_rank_tecnico AS crt',
                'AspHer.NIVEL_EXPERIENCIA=crt.PK_RANK_TECNICO')
                ->andWhere(['=', 'AspHer.FK_PROSPECTO', $idProspecto])
                ->all();

                $habilidades = (new \yii\db\Query())
                  ->select([
                    'CatHab.DESC_HABILIDAD AS DESCRIPCION'
                  ])
                  ->from('tbl_prospectos as p')
                  ->join('INNER JOIN', 'tbl_prospectos_habilidades AspHab',
                          'p.PK_PROSPECTO = AspHab.FK_PROSPECTO')
                  ->join('INNER JOIN', 'tbl_cat_habilidades CatHab',
                          'AspHab.FK_HABILIDAD = CatHab.PK_HABILIDAD')
                  ->andWhere(['=', 'AspHab.FK_PROSPECTO', $idProspecto])
                  ->all();

                  $perfil = (new \yii\db\Query())
                    ->select([
                      'CatPer.DESCRIPCION'
                    ])
                    ->from('tbl_prospectos as p')
                    ->join('INNER JOIN', 'tbl_prospectos_perfiles PP',
                            'p.PK_PROSPECTO = PP.FK_PROSPECTO')
                    ->join('INNER JOIN', 'tbl_cat_perfiles CatPer',
                            'PP.FK_PERFIL = CatPer.PK_PERFIL')
                    ->where(['PP.FK_PROSPECTO' => $idProspecto])
                    ->all();

                    $ProspectosPerfiles = "";
                    if (empty($perfil) ) {
                      $ProspectosPerfiles = "";
                    }
                    else {
                      $ProspectosPerfiles = "";
                      foreach ($perfil as $pkey => $pvalue) {
                        $ProspectosPerfiles .= $perfil[$pkey]['DESCRIPCION'].'<br>';
                      }
                      //$ProspectosPerfiles .= "</p>";
                    }
                  // $dataProvider[$key]['PERFIL'] = $ProspectosPerfiles;

                  if ($infoth == 1) {
                    $infoProspecto = $tecnologias;
                  } elseif ($infoth == 2) {
                    $infoProspecto = $herramientas;
                  } elseif ($infoth == 3) {
                    $infoProspecto = $habilidades;
                  } elseif($infoth == 4) {
                    $infoProspecto = $perfil;
                  }
                  else {
                    $infoProspecto = '';
                  }

            $dataProvider = $this->prospecto($idProspecto);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'dataProvider' => $dataProvider,
                'tecnologias'=> $tecnologias,
                'herramientas'=> $herramientas,
                'habilidades'=> $habilidades,
                'perfil'=> $perfil,
                'ProspectosPerfiles' => $ProspectosPerfiles,
                'infoProspecto' => $infoProspecto

            ];
        }
      }

}
