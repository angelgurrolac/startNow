<?php

namespace app\controllers;

use Yii;
use app\models\TblUsuarios;
use app\models\LoginForm;
use app\models\TblAdConfig;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\db\Query;
use yii\db\Expression;
use yii\db\ActiveQuery;
use app\models\UsuariosSearch;

/**
 * UsuariosController implements the CRUD actions for TblUsuarios model.
 */
class UsuariosController extends Controller
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
     * Lists all TblUsuarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsuariosSearch();
        $queryFiltrado = $searchModel->search(\Yii::$app->request->get());
        
        $dataProvider = $queryFiltrado; //data from filter form

        //if(Yii::$app->request->post()){
            //$data = Yii::$app->request->post();

            /*$query= TblUsuarios::find()->where(['LIKE', 'NOMBRE_COMPLETO', $data['NOMBRE_COMPLETO']]);
            //$query= TblUsuarios::find();
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 2,
                    'page' => 1-1,
                ],
            ]);*/
            //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            //$resultado=$dataProvider->getModels();
            //$query_cont = $dataProvider->getTotalCount();
            
            /*$res = array(
                
                'pagina'        => 1,
                // 'data'          => $dataProvider->getModels(),
                'data'          => $resultado,
                'total_paginas' => ceil($query_cont / 2),
                'total_registros' => $query_cont,
            );*/

        //}
            /*$dataProvider = new ActiveDataProvider([
                'query' => TblUsuarios::find(),
            ]);*/
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single TblUsuarios model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblUsuarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TblUsuarios();

        if ($model->load(Yii::$app->request->post()) ) {
            $data = Yii::$app->request->post();
            $length = 10;
            $key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

            $model->USUARIO= strtolower($model->USUARIO);

            $model->KEY= $key;
            $model->PASSWORD= crypt($model->PASSWORD,$key);
            $model->CREATE_AT=date('Y-m-d h:i:s');
            $model->CREATE_IP = user_ip();

            $connection = \Yii::$app->db;
            $fkEmpleado = $data['NOMBRE_COMPLETO'];
            $nombreEmpleado = $connection->createCommand("SELECT CONCAT(e.NOMBRE_EMP,' ',e.APELLIDO_PAT_EMP,' ',e.APELLIDO_MAT_EMP) as NOMBRE_EMP FROM tbl_empleados e WHERE e.PK_EMPLEADO = ".$fkEmpleado)->queryOne();
            $connection->close();
            
            $model->NOMBRE_COMPLETO = $nombreEmpleado['NOMBRE_EMP'];
            $model->INICIALES = $data['TblUsuarios']['INICIALES'];
            $model->ESTATUS = $data['TblUsuarios']['ESTATUS'];
            $model->CORREO = $data['TblUsuarios']['CORREO'];
            $model->DEVICE_IP = $data['TblUsuarios']['DEVICE_IP'];
            $model->FK_EMPLEADO = $fkEmpleado;
            $model->IS_SUPER_ADMIN = 0;
            
            $insert=$model->save();
            
            if($insert){
                return $this->redirect(['view', 'id' => $model->PK_USUARIO]);
            }else{
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TblUsuarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post();
            
            $length = 10;
            $key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
            $fkEmpleado = '';

            $model->USUARIO= strtolower($model->USUARIO);
            $connection = \Yii::$app->db;

            if($data['TblUsuarios']['PASSWORD'] != '**********'){
                $model->KEY= $key;
                $model->PASSWORD= crypt($model->PASSWORD,$key);
            }else{
                $passUser = $connection->createCommand("SELECT u.PASSWORD FROM tbl_usuarios u WHERE u.PK_USUARIO = ".$data['TblUsuarios']['PK_USUARIO'])->queryOne();
                $model->PASSWORD = $passUser['PASSWORD'];
            }
            
            $model->UPDATE_AT=date('Y-m-d h:i:s');
            $model->CREATE_IP = user_ip();

            if(isset($data['TblUsuarios']['FK_EMPLEADO'])){
                $fkEmpleado = $data['TblUsuarios']['FK_EMPLEADO'];
            }else{
                $fkEmpleado = $data['NOMBRE_COMPLETO'];
                $nombreEmpleado = $connection->createCommand("SELECT CONCAT(e.NOMBRE_EMP,' ',e.APELLIDO_PAT_EMP,' ',e.APELLIDO_MAT_EMP) as NOMBRE_EMP FROM tbl_empleados e WHERE e.PK_EMPLEADO = ".$fkEmpleado)->queryOne();
            }
            $connection->close();
            
            $model->INICIALES = $data['TblUsuarios']['INICIALES'];
            $model->ESTATUS = $data['TblUsuarios']['ESTATUS'];
            $model->CORREO = $data['TblUsuarios']['CORREO'];
            $model->DEVICE_IP = $data['TblUsuarios']['DEVICE_IP'];
            $model->FK_EMPLEADO = $fkEmpleado;
            $model->IS_SUPER_ADMIN = $data['TblUsuarios']['IS_SUPER_ADMIN'];
            
            $insert=$model->save();
            
            if($insert){
                return $this->redirect(['view', 'id' => $model->PK_USUARIO]);
            }
        } else {
            $model->PASSWORD='**********';
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TblUsuarios model.
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
     * Finds the TblUsuarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblUsuarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblUsuarios::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    function actionLogin(){
        $session = Yii::$app->session;
        $usuario='';

        $model = new LoginForm();
        $user_auth     = false;
        $required_pass = true;
        $is_email      = false;

        $errors= [];
        if(user_is_login()){
            return $this->goHome();
        }

        if ($session->get('last_activity') && (time() - $session->get('last_activity') > 1*60)) {
            $session->remove('protected');
            $session->remove('blocked');
            $session->remove('last_activity');
            session_destroy();   // destroy session data in storage
        }

        if(Yii::$app->request->post()){
            if($session->get('blocked')){
                Yii::$app->response->redirect(['usuarios/login']);
            }
            $data= Yii::$app->request->post();
            $correo = filter_var($data['LoginForm']['CORREO'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($data['LoginForm']['PASSWORD'], FILTER_SANITIZE_STRING);

            $servers= TblAdConfig::find()->where(['activo'=>1])->asArray()->all();

            $usuario =  TblUsuarios::find()
                ->andFilterWhere(['or',
                    ['=','CORREO',$correo],
                    ['=','USUARIO',$correo],
                ])->limit(1)->asArray()->one();
                
            if($usuario){
                if($usuario['ESTATUS'] == 1){
                    $adldap= user_connect_ad();
                    if($adldap){
                        if(user_login_ad($adldap, $usuario['USUARIO'],$password)){
                           $required_pass=false; 
                        }
                    }

                    if($required_pass){
                        $key= $usuario['KEY'];
                        $hashed_password = crypt($password,$key); // dejar que el salt se genera automáticamente
                        if (hash_equals($hashed_password, $usuario['PASSWORD'])) {
                        //if ($password == $usuario['PASSWORD']) {
                            $user_auth= true;
                        }else{
                            $errors[]='El password es incorrecto';
                        }
                    }else{
                        $user_auth= true;
                    }
                }else{
                    $errors[]='El usuario esta desactivado';
                }
            }else{
                $errors[]='El usuario no existe';
                /*$adldap= user_connect_ad();
                $correo = explode('@', $correo);
                $correo= $correo[0];

                if(user_login_ad($adldap, $correo,$password)){
                    $new_user = new TblUsuarios();
                    $length = 10;
                    $key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

                    $new_user->USUARIO= strtolower($correo);

                    $new_user->KEY= $key;
                    $new_user->PASSWORD= crypt($password,$key);

                    $new_user->CREATE_AT=date('Y-m-d h:i:s');
                    $new_user->CREATE_IP = user_ip();
                    
                    $user = $adldap->user()->infoCollection($correo, array('*'));
                    $new_user->NOMBRE_COMPLETO=$user->displayname;
                    $new_user->CORREO= str_replace('sip:', '', $user->proxyaddresses);
                                        
                    $insert=$new_user->save(false); 

                    $usuario =  TblUsuarios::find()->andWhere(['=','PK_USUARIO',$new_user->PK_USUARIO])->limit(1)->asArray()->one();
                    $user_auth= true;
                }else{
                    $errors[]='El usuario no existe';
                }*/
            }           

            if($user_auth){
                
                /*if($usuario['SESSION_ID']){
                    session_id($usuario['SESSION_ID']);
                    session_destroy();
                }*/

                /*
                select distinct (i.ID_CUSTOM)
                from  tbl_usuarios_grupo up 
                inner join tbl_grupos g on g.PK_GRUPO = up.FK_GRUPO
                inner join tbl_roles r on r.PK_ROL = g.FK_ROL
                inner join tbl_item_rol ir on ir.FK_ROL = r.PK_ROL
                inner join tbl_items i on i.PK_ITEM = ir.FK_ITEM
                where up.FK_USUARIO =$id_usuario
                 */
                $query = new Query;
                $query->select('i.ID_CUSTOM')
                    ->from('tbl_usuarios_grupo as up')
                    ->join('inner JOIN','tbl_grupos as g',
                        'g.PK_GRUPO = up.FK_GRUPO')
                    ->join('inner JOIN','tbl_roles as r',
                        'r.PK_ROL = g.FK_ROL')
                    ->join('inner JOIN','tbl_item_rol as ir',
                        'ir.FK_ROL = r.PK_ROL')
                    ->join('inner JOIN','tbl_items as i',
                        'i.PK_ITEM = ir.FK_ITEM')
                    ->where(['=','ir.ESTATUS', 1])
                    ->andWhere(['=','up.FK_USUARIO', $usuario['PK_USUARIO']])
                    ->distinct();
                $command = $query->createCommand();
                // $command->sql returns the actual SQL
                $rows = $command->queryAll();
                $permisos=[];
                foreach ($rows as $key => $value) {
                    $permisos[]=$value['ID_CUSTOM'];
                }

                $usuario['PERMISOS']=$permisos;
                $usuario['HASH_TOKEN']=HASH_TOKEN;

                $query = new Query;
                $query->select('r.PK_ROL')
                    ->from('tbl_usuarios_grupo as up')
                    ->join('inner JOIN','tbl_grupos as g',
                        'g.PK_GRUPO = up.FK_GRUPO')
                    ->join('inner JOIN','tbl_roles as r',
                        'r.PK_ROL = g.FK_ROL')
                    ->join('inner JOIN','tbl_item_rol as ir',
                        'ir.FK_ROL = r.PK_ROL')
                    ->join('inner JOIN','tbl_items as i',
                        'i.PK_ITEM = ir.FK_ITEM')
                    ->where(['=','ir.ESTATUS', 1])
                    ->andWhere(['=','up.FK_USUARIO', $usuario['PK_USUARIO']])
                    ->distinct();
                $command = $query->createCommand();
                // $command->sql returns the actual SQL
                $rows = $command->queryAll();

                $roles=[];
                foreach ($rows as $key => $value) {
                    $roles[]=$value['PK_ROL'];
                }

                $usuario['ROLES']=$roles;


                $query = new Query;
                $query->select([
                    'e.PK_EMPLEADO',
                    'e.NOMBRE_EMP',
                    'e.APELLIDO_PAT_EMP',
                    'e.APELLIDO_MAT_EMP',
                    'e.FK_GENERO_EMP',
                    'e.FECHA_NAC_EMP',
                    'e.RFC_EMP',
                    'e.CURP_EMP',
                    'e.NSS_EMP',
                    'e.EMAIL_EMP',
                    'e.FOTO_EMP',
                    'p.FECHA_INGRESO',
                    'p.PK_PERFIL',
                    'p.FK_UBICACION_FISICA',
                    'p.FK_CONTRATO',
                    'p.FK_AREA',
                    'p.FK_PUESTO',
                    'p.FK_RAZON_SOCIAL',
                    'p.FK_UNIDAD_NEGOCIO',
                    'p.FK_UBICACION',
                    'p.FK_ESTATUS_RECURSO',
                    'p.FK_ADMINISTRADORA',
                    'p.FK_TIPO_SERVICIO',
                    'p.FK_DURACION_CONTRATO',
                    'p.FK_RANK_TECNICO',
                    'p.FK_UNIDAD_TRABAJO',
                    'p.SUELDO_NETO',
                    ])
                    ->from('tbl_empleados as e')
                    ->join('inner join','tbl_perfil_empleados as p',
                            'e.PK_EMPLEADO= P.FK_EMPLEADO')
                    ->where(['e.PK_EMPLEADO'=>$usuario['FK_EMPLEADO']])
                    ->limit(1);
                $command = $query->createCommand();
                // $command->sql returns the actual SQL
                $row = $command->queryOne();

                $usuario['INFO_EMP']=$row;

                $arr_unidades_negocios = explode(',',get_config('CONFIG','IS_ADMIN_UNIDAD_NEGOCIO'));
                if(in_array($row['FK_UNIDAD_NEGOCIO'], $arr_unidades_negocios)){
                    $usuario['IS_ADMIN_UNIDAD_NEGOCIO']= $arr_unidades_negocios;
                }

                $arr_emp_unidades_negocios = explode(',',get_config('CONFIG','EMP_IS_ADMIN_UNIDAD_NEGOCIO'));
                if(in_array($row['PK_EMPLEADO'], $arr_emp_unidades_negocios)){
                    $usuario['IS_ADMIN_UNIDAD_NEGOCIO']= $arr_emp_unidades_negocios;
                }

                $arr_unidades_negocios = explode(',',get_config('CONFIG','EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'));
                if(in_array($row['FK_UNIDAD_NEGOCIO'], $arr_unidades_negocios)){
                    $usuario['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO']=$arr_unidades_negocios;
                }

                $session->set('usuario', $usuario);
                $session_id= $session->getId();
                $model_user = $this->findModel($usuario['PK_USUARIO']);
                $model_user->SESSION_ID=$session_id;
                $model_user->LOGGED_IN_AT=date('Y-m-d h:i:s');
                $model_user->UPDATE_AT=date('Y-m-d h:i:s');
                $model_user->LOGGED_IN_IP = user_ip();
                $model_user->save(false);

                // $session->setTimeout('10');
                if(isset($_GET['rd'])&&!empty($_GET['rd'])){
                    Yii::$app->response->redirect($_GET['rd']);
                }else{
                    $last_page = get_last_page();
                    /*var_dump($last_page);
                    $last_page = substr($last_page, strpos($last_page, 'http'));
                    $last_page = str_replace('%3A', ':', $last_page);
                    $last_page = str_replace('%2F', '/', $last_page);
                    var_dump($last_page);
                    die();*/
                    if($last_page){
                        return $this->redirect("$last_page");
                    }
                    return $this->goHome();
                }
            }else{

                if($errors){
                    if(!$session->get('protected')){
                        $session->set('protected',0);
                    }
                    $session->set('protected',$session->get('protected')+1);
                    if($session->get('protected')>=3){
                        if(!$session->get('blocked')){
                            $session->set('blocked',1);
                            // $errors[]='Has sobrepasado el limite de intentos';
                        }
                        // if ($session->get('last_activity') && (time() - $session->get('last_activity') > 1*60)) {
                        //     $session->remove('protected');
                        //     $session->remove('blocked');
                        //     $session->remove('last_activity');
                        //     session_destroy();   // destroy session data in storage
                        // }else
                        if(!$session->get('last_activity')){
                            $session->set('last_activity', time());
                        }

                    }
                }

                $model->CORREO= $correo;
                return $this->render('form/login', [
                    'model'=>$model,
                    'errors'=>$errors,
                ]);
            }
            
        }
        return $this->render('form/login', [
            'model'=>$model,
            'errors'=>[],
        ]);
    }

    function actionLogout(){
        save_last_page();
        if(user_is_login()){
            user_logout();
        }
        return $this->goHome();
    }

    function actionPermisos(){

        $id_usuario= user_current_id();
        /*
        select distinct (i.ID_CUSTOM)
        from  tbl_usuarios_grupo up 
        inner join tbl_grupos g on g.PK_GRUPO = up.FK_GRUPO
        inner join tbl_roles r on r.PK_ROL = g.FK_ROL
        inner join tbl_item_rol ir on ir.FK_ROL = r.PK_ROL
        inner join tbl_items i on i.PK_ITEM = ir.FK_ITEM
        where up.FK_USUARIO =$id_usuario
         */
        $query = new Query;
        $query->select('i.ID_CUSTOM')
            ->from('tbl_usuarios_grupo as up')
            ->join('inner JOIN','tbl_grupos as g',
                'g.PK_GRUPO = up.FK_GRUPO')
            ->join('inner JOIN','tbl_roles as r',
                'r.PK_ROL = g.FK_ROL')
            ->join('inner JOIN','tbl_item_rol as ir',
                'ir.FK_ROL = r.PK_ROL')
            ->join('inner JOIN','tbl_items as i',
                'i.PK_ITEM = ir.FK_ITEM')
            ->where(['=','up.FK_USUARIO', "$id_usuario"])
            ->distinct();
        $command = $query->createCommand();
        // $command->sql returns the actual SQL
        $rows = $command->queryAll();
        $permisos=[];
        foreach ($rows as $key => $value) {
            $permisos[]=$value['ID_CUSTOM'];
        }
        // dd($rows);
    }
}
