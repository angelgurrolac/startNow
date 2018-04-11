<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\TblTarifasClientes;
use app\models\tblcatpaises;
use app\models\TblCatCategoria;
use yii\db\Query;
use yii\db\Expression;

class SiteController extends Controller
{
    // public function behaviors()
    // {
    //     return [
    //         'access' => [
    //             'class' => AccessControl::className(),
    //             'only' => ['logout'],
    //             'rules' => [
    //                 [
    //                     'actions' => ['logout'],
    //                     'allow' => true,
    //                     'roles' => ['@'],
    //                 ],
    //             ],
    //         ],
    //         'verbs' => [
    //             'class' => VerbFilter::className(),
    //             'actions' => [
    //                 'logout' => ['post'],
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

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if(user_is_login()){
            return $this->render('index');
        } else {
            return $this->redirect(['usuarios/login']);
        }

    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSay($message = 'Hello')
    {
        return $this->render('say', ['message' => $message]);
    }

    // public function actionPaises($q = null, $id = null)
    // {
    //     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //     $out = ['results' => ['PK_PAIS' => '', 'DESC_PAIS' => '']];
    //     if (!is_null($q)) {
    //         $query = new Query;
    //         $query->select('PK_PAIS AS  id, DESC_PAIS AS text')
    //             ->from('tbl_cat_paises')
    //             ->where(['like','DESC_PAIS', "$q"])
    //             ->limit(20);
    //         $command = $query->createCommand();
    //         $data = $command->queryAll();
    //         $out['results'] = array_values($data);
    //     }
    //     elseif ($id > 0) {
    //         // $out['results'] = ['PK_PAIS' => $id, 'DESC_PAIS' => tblcatpaises::find()->all()];
    //     }
    //     return $out;
    // }
    public function actionEstados()
    {
        $data = Yii::$app->request->get();
        $post=null;
        // parse_str($data['data'],$post);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';

        $out = ['results' => ['PK_PAIS' => '', 'DESC_ESTADO' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('PK_ESTADO AS  id, DESC_ESTADO AS text')
                ->from('tbl_cat_estados')
                ->where(['like','DESC_ESTADO', "$q"])
                ->andWhere(['=','FK_PAIS', "$p"])
                ->orderBy(['DESC_ESTADO'=>'SORT_ASC']);
                // ->limit(20);
            // return $query;
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        else{
            $query = new Query;
            $query->select('PK_ESTADO AS  id, DESC_ESTADO AS text')
                ->from('tbl_cat_estados')
                ->andWhere(['=','FK_PAIS', "$p"])
                 ->orderBy(['DESC_ESTADO'=>'SORT_ASC']);
                // ->limit(20);
            $command = $query->createCommand();

            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
        // return $q;
    }

    public function actionSubcategoria()
    {
        $data = Yii::$app->request->get();
        $post=null;
        // parse_str($data['data'],$post);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';

        $out = ['results' => ['PK_CATEGORIA' => '', 'DESC_SUBCATEGORIA' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('PK_SUBCATEGORIA AS  id, DESC_SUBCATEGORIA AS text')
                ->from('tbl_cat_subcategoria')
                ->where(['like','DESC_SUBCATEGORIA', "$q"])
                ->andWhere(['=','FK_CATEGORIA', "$p"])
                ->orderBy(['DESC_SUBCATEGORIA'=>'SORT_ASC']);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        else{
            $query = new Query;
            $query->select('PK_SUBCATEGORIA AS  id, DESC_SUBCATEGORIA AS text')
                ->from('tbl_cat_subcategoria')
                ->andWhere(['=','FK_CATEGORIA', "$p"])
                 ->orderBy(['DESC_SUBCATEGORIA'=>'SORT_ASC']);
            $command = $query->createCommand();

            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    public function actionMunicipios()
    {
        $data = Yii::$app->request->get();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';
        $e =(!empty($data['e']))? trim($data['e']):'';

        $out = ['results' => ['PK_MUNICIPIO' => '', 'DESC_MUNICIPIO' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('PK_MUNICIPIO AS  id, DESC_MUNICIPIO AS text')
                ->from('tbl_cat_municipios')
                ->andWhere(['like','DESC_MUNICIPIO', "$q"])
                ->andWhere(['=','FK_PAIS', "$p"])
                ->andWhere(['=','FK_ESTADO', "$e"])
                ->orderBy(['DESC_MUNICIPIO'=>'SORT_ASC']);
                // ->limit(20);
            // return $query;
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        else{
            $query = new Query;
            $query->select('PK_MUNICIPIO AS  id, DESC_MUNICIPIO AS text')
                ->from('tbl_cat_municipios')
                ->andWhere(['=','FK_PAIS', "$p"])
                ->andWhere(['=','FK_ESTADO', "$e"])
                ->orderBy(['DESC_MUNICIPIO'=>'SORT_ASC']);
                // ->limit(20);
            // return $query;
            $command = $query->createCommand();

            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    public function actionAreas()
    {
        $data = Yii::$app->request->get();
        $post=null;
        // parse_str($data['data'],$post);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';

        $out = ['results' => ['PK_AREA' => '', 'DESC_AREA' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('PK_AREA AS  id, DESC_AREA AS text')
                ->from('tbl_cat_areas')
                ->where(['like','DESC_AREA', "$q"])
                ->andWhere(['=','FK_TIPO_SERVICIO', "$p"])
                ->orderBy(['DESC_AREA'=>'SORT_ASC']);
                // ->limit(20);
            // return $query;
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        else{
            $query = new Query;
            $query->select('PK_AREA AS  id, DESC_AREA AS text')
                ->from('tbl_cat_areas')
                ->andWhere(['=','FK_TIPO_SERVICIO', "$p"])
                 ->orderBy(['DESC_AREA'=>'SORT_ASC']);
                // ->limit(20);
            $command = $query->createCommand();

            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    public function actionPuestos()
    {
        $data = Yii::$app->request->get();
        $post=null;
        // parse_str($data['data'],$post);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';

        $out = ['results' => ['PK_PUESTO' => '', 'DESC_PUESTO' => '']];
        if (!empty($q)) {
            $query = new Query;
            $query->select('PK_PUESTO AS  id, DESC_PUESTO AS text')
                ->from('tbl_cat_puestos')
                ->join('INNER JOIN','tbl_areas_puestos',
                                    'tbl_areas_puestos.FK_PUESTO = tbl_cat_puestos.PK_PUESTO')
                ->where(['like','tbl_cat_puestos.DESC_PUESTO', $q])
                ->andWhere(['=','tbl_areas_puestos.FK_AREA', $p])
                ->orderBy(['DESC_PUESTO'=>'SORT_ASC']);
                // ->limit(20);
            // return $query;
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        else{
            $query = new Query;
            $query->select('PK_PUESTO AS  id, DESC_PUESTO AS text')
                ->from('tbl_cat_puestos')
                ->join('INNER JOIN','tbl_areas_puestos',
                                    'tbl_areas_puestos.FK_PUESTO = tbl_cat_puestos.PK_PUESTO')
                ->where(['=','tbl_areas_puestos.FK_AREA', $p])
                ->orderBy(['DESC_PUESTO'=>'SORT_ASC']);
                // ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    public function actionPuestos2()
    {
        $data = Yii::$app->request->post();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $FK_AREA= explode(":", $data['FK_AREA']);
        $FK_AREA = $FK_AREA[0];
        $out = ['results' => ['PK_PUESTO' => '', 'DESC_PUESTO' => '']];
        if (!empty($FK_AREA)) {
            $query = new Query;
            $query->select('PK_PUESTO AS  id, DESC_PUESTO AS text')
                ->from('tbl_cat_puestos')
                ->where(['=','FK_AREA', $FK_AREA])
                ->orderBy(['DESC_PUESTO'=>'SORT_ASC']);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    public function actionEstatus_recursos()
    {
        $data = Yii::$app->request->get();
        $post=null;
        // parse_str($data['data'],$post);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';

        $out = ['results' => ['PK_ESTATUS_RECURSO' => '', 'DESC_ESTATUS_RECURSO' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('PK_ESTATUS_RECURSO AS  id, DESC_ESTATUS_RECURSO AS text')
                ->from('tbl_cat_estatus_recursos')
                ->where(['like','DESC_ESTATUS_RECURSO', "$q"])
                ->andWhere(['=','FK_TIPO_SERVICIO', "$p"])
                ->orderBy(['DESC_ESTATUS_RECURSO'=>'SORT_ASC']);
                // ->limit(20);
            // return $query;
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        else{
            $query = new Query;
            $query->select('PK_ESTATUS_RECURSO AS  id, DESC_ESTATUS_RECURSO AS text')
                ->from('tbl_cat_estatus_recursos')
                ->andWhere(['=','FK_TIPO_SERVICIO', "$p"])
                 ->orderBy(['DESC_ESTATUS_RECURSO'=>'SORT_ASC']);
                // ->limit(20);
            $command = $query->createCommand();

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
        $p =(!empty($data['p']))? trim($data['p']):'';

        $p2='';
        if($p=='71'){
            $p2='2';
        }
        $p= array($p);
        if($p2){
            $p[]=$p2;
        }

        $query = new Query;
        $query->select('U.PK_UBICACION AS  id, U.DESC_UBICACION AS text')
            ->from('tbl_cat_ubicaciones AS U')
            ->join('INNER JOIN','tbl_clientes AS C','C.PK_CLIENTE = U.FK_CLIENTE')
            ->andWhere(['in','C.PK_CLIENTE', $p]);
        $command = $query->createCommand();

        $data = $command->queryAll();
        $out['results'] = array_values($data);

        return $out;
    }

    public function actionClientes()
    {
        $data = Yii::$app->request->get();
        $post=null;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';

        $query = new Query;
        $query->select('c.PK_CLIENTE AS  id, c.NOMBRE_CLIENTE AS text')
            ->from('tbl_clientes AS c')
            ->andFilterWhere(['or',
                    ['like','c.NOMBRE_CLIENTE',$q],
                    ['like','c.ALIAS_CLIENTE',$q],
                ]);
        $command = $query->createCommand();

        $data = $command->queryAll();
        $out['results'] = array_values($data);

        return $out;
    }

    //Llena y filtra el combo clientes del m�dulo Contabilidad en su secci�n pendientes de pago - servicios
    public function actionClientes_pp_servicios()
    {
        $data = Yii::$app->request->get();
        $post=null;

        $connection = \Yii::$app->db;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';

        $data = $connection->createCommand("
            SELECT DISTINCT (tbc.PK_CLIENTE) AS id, tbc.NOMBRE_CLIENTE AS text
            FROM tbl_periodos tbp
            INNER JOIN tbl_facturas tbf ON tbp.PK_PERIODO = tbf.FK_PERIODO AND tbp.FK_DOCUMENTO_FACTURA = tbf.FK_DOC_FACTURA
            INNER JOIN tbl_asignaciones tba on tba.PK_ASIGNACION = tbp.FK_ASIGNACION
            INNER JOIN tbl_documentos tbdodc ON tbdodc.PK_DOCUMENTO = tbp.FK_DOCUMENTO_ODC and tbdodc.NUM_DOCUMENTO NOT LIKE '%BLS_ODC%'
            LEFT JOIN tbl_clientes tbc ON tbc.PK_CLIENTE = tba.FK_CLIENTE
            LEFT JOIN tbl_empleados tbe ON tbe.PK_EMPLEADO = tba.FK_EMPLEADO
            LEFT JOIN tbl_cat_servicios tbcs ON tbf.FK_SERVICIO = tbcs.PK_SERVICIO
            LEFT JOIN tbl_cat_estatus_facturas tbef ON tbf.FK_ESTATUS = tbef.PK_ESTATUS_FACTURA
            LEFT JOIN tbl_cat_razon_social tbrs ON tbdodc.FK_RAZON_SOCIAL = tbrs.PK_RAZON_SOCIAL WHERE tbf.FECHA_INGRESO_BANCO IS NULL
            AND (tbc.NOMBRE_CLIENTE LIKE '%".$q."%' OR tbc.ALIAS_CLIENTE LIKE '%".$q."%')")->queryAll();

        $out['results'] = array_values($data);
        $connection->close();
        return $out;
    }

    //Llena y filtra el combo clientes del m�dulo Contabilidad en su secci�n pendientes de pago - bolsas
    public function actionClientes_pp_bolsas()
    {
        $data = Yii::$app->request->get();
        $post=null;

        $connection = \Yii::$app->db;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';

        $data = $connection->createCommand("
            SELECT DISTINCT (tbc.PK_CLIENTE) AS id, tbc.NOMBRE_CLIENTE AS text
            FROM tbl_periodos tbp
            INNER JOIN tbl_facturas tbf ON tbp.PK_PERIODO = tbf.FK_PERIODO AND tbp.FK_DOCUMENTO_FACTURA = tbf.FK_DOC_FACTURA
            INNER JOIN tbl_asignaciones tba on tba.PK_ASIGNACION = tbp.FK_ASIGNACION
            INNER JOIN tbl_documentos tbdodc ON tbdodc.PK_DOCUMENTO = tbp.FK_DOCUMENTO_ODC
            INNER JOIN tbl_documentos tbdfac ON tbdfac.PK_DOCUMENTO = tbp.FK_DOCUMENTO_FACTURA
            INNER JOIN tbl_cat_bolsas B ON tbdodc.NUM_DOCUMENTO LIKE CONCAT('%BLS_ODC_',B.NUMERO_BOLSA,'%')
            LEFT JOIN tbl_clientes tbc ON tbc.PK_CLIENTE = tba.FK_CLIENTE
            LEFT JOIN tbl_empleados tbe ON tbe.PK_EMPLEADO = tba.FK_EMPLEADO
            LEFT JOIN tbl_cat_servicios tbcs ON tbf.FK_SERVICIO = tbcs.PK_SERVICIO
            LEFT JOIN tbl_cat_estatus_facturas tbef ON tbf.FK_ESTATUS = tbef.PK_ESTATUS_FACTURA
            LEFT JOIN tbl_cat_razon_social tbrs ON tbdodc.FK_RAZON_SOCIAL = tbrs.PK_RAZON_SOCIAL WHERE tbf.FECHA_INGRESO_BANCO IS NULL
            AND (tbc.NOMBRE_CLIENTE LIKE '%".$q."%' OR tbc.ALIAS_CLIENTE LIKE '%".$q."%')")->queryAll();

        $out['results'] = array_values($data);

        $connection->close();
        return $out;
    }

    //Llena y filtra el combo clientes del m�dulo Contabilidad en su secci�n pendientes por facturar - servicios
    public function actionClientes_pf_servicios()
    {
        $data = Yii::$app->request->get();
        $post=null;

        $connection = \Yii::$app->db;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';

        $data = $connection->createCommand("
            SELECT DISTINCT (C.PK_CLIENTE) AS id, C.NOMBRE_CLIENTE AS text
            FROM tbl_asignaciones A
            INNER JOIN tbl_periodos P ON P.FK_ASIGNACION = A.PK_ASIGNACION
            LEFT JOIN tbl_clientes C ON A.FK_CLIENTE = C.PK_CLIENTE
            LEFT JOIN tbl_empleados E ON A.FK_EMPLEADO = E.PK_EMPLEADO
            LEFT JOIN tbl_documentos D ON P.FK_DOCUMENTO_ODC = D.PK_DOCUMENTO AND D.NUM_DOCUMENTO NOT LIKE '%BLS_ODC%'
            LEFT JOIN tbl_documentos hde ON P.FK_DOCUMENTO_HDE = hde.PK_DOCUMENTO
            LEFT JOIN tbl_facturas F ON P.FK_DOCUMENTO_FACTURA = F.FK_DOC_FACTURA
            LEFT JOIN tbl_cat_servicios S ON F.FK_SERVICIO = S.PK_SERVICIO
            WHERE P.FK_DOCUMENTO_ODC IS NOT NULL AND ((P.FK_DOCUMENTO_HDE IS NOT null AND P.FK_DOCUMENTO_FACTURA IS NULL) OR (P.FK_DOCUMENTO_FACTURA IS NULL AND P.FACTURA_PROVISION = 1)) AND A.FK_ESTATUS_ASIGNACION != 5
            AND (C.NOMBRE_CLIENTE LIKE '%".$q."%' OR C.ALIAS_CLIENTE LIKE '%".$q."%')")->queryAll();

        $out['results'] = array_values($data);

        $connection->close();
        return $out;
    }

    //Llena y filtra el combo clientes del m�dulo Contabilidad en su secci�n pendientes por facturar - bolsas
    public function actionClientes_pf_bolsas()
    {
        $data = Yii::$app->request->get();
        $post=null;

        $connection = \Yii::$app->db;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';

        $data = $connection->createCommand("
            SELECT DISTINCT (C2.PK_CLIENTE) AS id, C2.NOMBRE_CLIENTE AS text
            FROM tbl_asignaciones A
            INNER JOIN tbl_periodos P ON P.FK_ASIGNACION = A.PK_ASIGNACION
            LEFT JOIN tbl_documentos D ON P.FK_DOCUMENTO_ODC = D.PK_DOCUMENTO
            LEFT JOIN tbl_cat_bolsas B ON D.NUM_DOCUMENTO LIKE CONCAT('%BLS_ODC_',B.NUMERO_BOLSA,'%')
            LEFT JOIN tbl_clientes C2 ON B.FK_CLIENTE = C2.PK_CLIENTE
            LEFT JOIN tbl_cat_razon_social RZ2 ON B.FK_EMPRESA = RZ2.PK_RAZON_SOCIAL
            LEFT JOIN tbl_facturas F ON P.FK_DOCUMENTO_FACTURA = F.FK_DOC_FACTURA
            WHERE P.FK_DOCUMENTO_ODC IS NOT NULL AND ((P.FK_DOCUMENTO_FACTURA IS NULL AND P.FACTURA_PROVISION = 1) OR (P.FK_DOCUMENTO_HDE IS NOT null AND P.FK_DOCUMENTO_FACTURA IS NULL)) AND A.FK_ESTATUS_ASIGNACION != 5
            AND (C2.NOMBRE_CLIENTE LIKE '%".$q."%' OR C2.ALIAS_CLIENTE LIKE '%".$q."%')")->queryAll();

        $out['results'] = array_values($data);

        $connection->close();
        return $out;
    }

    //Llena y filtra el combo clientes del m�dulo Contabilidad en su secci�n index(consulta general) - servicios
    public function actionClientes_index_servicios()
    {
        $data = Yii::$app->request->get();
        $post=null;

        $connection = \Yii::$app->db;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';

        $data = $connection->createCommand("
            SELECT DISTINCT (C.PK_CLIENTE) AS id, C.NOMBRE_CLIENTE AS text
            FROM tbl_facturas F
            INNER JOIN tbl_periodos P ON F.FK_PERIODO = P.PK_PERIODO
            LEFT JOIN tbl_asignaciones A ON P.FK_ASIGNACION = A.PK_ASIGNACION
            LEFT JOIN tbl_clientes C ON A.FK_CLIENTE = C.PK_CLIENTE
            LEFT JOIN tbl_empleados E ON A.FK_EMPLEADO = E.PK_EMPLEADO
            LEFT JOIN tbl_documentos D ON D.PK_DOCUMENTO = F.FK_DOC_FACTURA
            LEFT JOIN tbl_documentos boss ON P.FK_DOCUMENTO_ODC = boss.PK_DOCUMENTO AND boss.NUM_DOCUMENTO NOT LIKE '%BLS_ODC%'
            LEFT JOIN tbl_cat_servicios S ON F.FK_SERVICIO = S.PK_SERVICIO
            LEFT JOIN tbl_cat_estatus_facturas EF ON F.FK_ESTATUS = EF.PK_ESTATUS_FACTURA
            LEFT JOIN tbl_cat_razon_social RZ ON D.FK_RAZON_SOCIAL = RZ.PK_RAZON_SOCIAL WHERE 1
            AND (C.NOMBRE_CLIENTE LIKE '%".$q."%' OR C.ALIAS_CLIENTE LIKE '%".$q."%')")->queryAll();

        $out['results'] = array_values($data);

        $connection->close();
        return $out;
    }

    //Llena y filtra el combo clientes del m�dulo Contabilidad en su secci�n index(consulta general) - bolsas
    public function actionClientes_index_bolsas()
    {
        $data = Yii::$app->request->get();
        $post=null;

        $connection = \Yii::$app->db;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';

        $data = $connection->createCommand("
            SELECT DISTINCT (C.PK_CLIENTE) AS id, C.NOMBRE_CLIENTE AS text
            FROM tbl_facturas F
            INNER JOIN tbl_periodos P ON F.FK_PERIODO = P.PK_PERIODO
            LEFT JOIN tbl_asignaciones A ON P.FK_ASIGNACION = A.PK_ASIGNACION
            LEFT JOIN tbl_clientes C ON A.FK_CLIENTE = C.PK_CLIENTE
            LEFT JOIN tbl_empleados E ON A.FK_EMPLEADO = E.PK_EMPLEADO
            LEFT JOIN tbl_documentos D ON D.PK_DOCUMENTO = F.FK_DOC_FACTURA
            LEFT JOIN tbl_documentos boss ON P.FK_DOCUMENTO_ODC = boss.PK_DOCUMENTO
            LEFT JOIN tbl_cat_bolsas B ON boss.NUM_DOCUMENTO LIKE CONCAT('%BLS_ODC_',B.NUMERO_BOLSA,'%')
            LEFT JOIN tbl_cat_razon_social RZ2 ON B.FK_EMPRESA = RZ2.PK_RAZON_SOCIAL
            LEFT JOIN tbl_cat_estatus_facturas EF ON F.FK_ESTATUS = EF.PK_ESTATUS_FACTURA LEFT JOIN tbl_cat_servicios S ON F.FK_SERVICIO = S.PK_SERVICIO
            WHERE A.PK_ASIGNACION IN (SELECT DISTINCT Bol.FK_ASIGNACION FROM tbl_documentos Bol WHERE Bol.NUM_DOCUMENTO LIKE '%BLS_ODC%')
            AND (C.NOMBRE_CLIENTE LIKE '%".$q."%' OR C.ALIAS_CLIENTE LIKE '%".$q."%')")->queryAll();

        $out['results'] = array_values($data);

        $connection->close();
        return $out;
    }

    public function actionObtener_empleados()
    {
        $data = Yii::$app->request->get();
        $post=null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $long= (!is_null($q)?strlen(trim($q)):0);
        $out = ['results' => ['PK_EMPLEADO' => '', 'NOMBRE_EMPLEADO' => '']];

        //Verificar unidad de negocio del empleado
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $op1=[];
        } else {
            $op1=['=','perfil.FK_UNIDAD_NEGOCIO',user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO']];
        }

        if (!is_null($q) && $long >= 3) {
            $query = new Query;
            $query->select(['PK_EMPLEADO AS id',"CONCAT(NOMBRE_EMP,' ',APELLIDO_PAT_EMP,' ',APELLIDO_MAT_EMP) AS text"])
                ->from('tbl_empleados emp')
                ->join('LEFT JOIN','tbl_perfil_empleados perfil',
                                    'emp.PK_EMPLEADO = perfil.FK_EMPLEADO')
                ->where(['like',"CONCAT(emp.NOMBRE_EMP,' ',emp.APELLIDO_PAT_EMP,' ',emp.APELLIDO_MAT_EMP)", "$q"])
                ->andWhere(['NOT IN','FK_ESTATUS_RECURSO', [4,6]])
                ->andFilterWhere(
                    ['and',
                        $op1
                    ]
                )
                ->orderBy(["CONCAT(NOMBRE_EMP,' ',APELLIDO_PAT_EMP,' ',APELLIDO_MAT_EMP)"=>'SORT_ASC']);
                // ->limit(20);
            // return $query;
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
        // return $q;
    }

    public function actionObtener_empleados_proyectos()
    {
        $data = Yii::$app->request->get();
        $post=null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $long= (!is_null($q)?strlen(trim($q)):0);
        $out = ['results' => ['PK_EMPLEADO' => '', 'NOMBRE_EMPLEADO' => '']];

        //Verificar unidad de negocio del empleado
        if((isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))||is_super_admin()){
            $op1=[];
        } else {
            $op1=['=','perfil.FK_UNIDAD_NEGOCIO',user_info()['INFO_EMP']['FK_UNIDAD_NEGOCIO']];
        }

        /*Se consultan a los empleados que coincidan con las primeras tres letras de su nombre ademas de tener en su perfil_empleados el FK_SERVICIO=2,
          o tener el FK_SERVICIO=1 y el FK_AREA=4 de calidad. */
        if (!is_null($q) && $long >= 3) {
            $query = new Query;
            $query->select(['PK_EMPLEADO AS id',"CONCAT(NOMBRE_EMP,' ',APELLIDO_PAT_EMP,' ',APELLIDO_MAT_EMP) AS text"])
                ->from('tbl_empleados emp')
                ->join('LEFT JOIN','tbl_perfil_empleados perfil',
                                    'emp.PK_EMPLEADO = perfil.FK_EMPLEADO')
                ->where(['like',"CONCAT(emp.NOMBRE_EMP,' ',emp.APELLIDO_PAT_EMP,' ',emp.APELLIDO_MAT_EMP)", "$q"])
                //->andWhere(['NOT IN','FK_ESTATUS_RECURSO', [4,6]])
                ->andWhere(['IN','FK_ESTATUS_RECURSO', [1,3]])
                ->andWhere(['or',['=','perfil.FK_TIPO_SERVICIO', '2'], ['and',['=','perfil.FK_TIPO_SERVICIO', '1'],['=','perfil.FK_AREA', '4']]])
                ->andFilterWhere(
                    ['and',
                        $op1
                    ]
                )
                ->orderBy(["CONCAT(NOMBRE_EMP,' ',APELLIDO_PAT_EMP,' ',APELLIDO_MAT_EMP)"=>'SORT_ASC']);
                // ->limit(20);
            // return $query;
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        /*Se consultan a todos los empleados reigstrados filtrando a quienes tienen en su perfil_empleados el FK_SERVICIO=2,
          o tener el FK_SERVICIO=1 y el FK_AREA=4 que es del �rea de calidad. */
        }else{
            $query = new Query;
            $query->select(['PK_EMPLEADO AS id',"CONCAT(NOMBRE_EMP,' ',APELLIDO_PAT_EMP,' ',APELLIDO_MAT_EMP) AS text"])
                ->from('tbl_empleados emp')
                ->join('LEFT JOIN','tbl_perfil_empleados perfil',
                                    'emp.PK_EMPLEADO = perfil.FK_EMPLEADO')
                ->where(['like',"CONCAT(emp.NOMBRE_EMP,' ',emp.APELLIDO_PAT_EMP,' ',emp.APELLIDO_MAT_EMP)", "$q"])
                //->andWhere(['NOT IN','FK_ESTATUS_RECURSO', [4,6]])
                ->andWhere(['IN','FK_ESTATUS_RECURSO', [1,3]])
                ->andWhere(['or',['=','perfil.FK_TIPO_SERVICIO', '2'], ['and',['=','perfil.FK_TIPO_SERVICIO', '1'],['=','perfil.FK_AREA', '4']]])
                ->andFilterWhere(
                    ['and',
                        $op1
                    ]
                )
                ->orderBy(["CONCAT(NOMBRE_EMP,' ',APELLIDO_PAT_EMP,' ',APELLIDO_MAT_EMP)"=>'SORT_ASC']);
                // ->limit(20);
            // return $query;
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
        // return $q;
    }

    public function actionObtener_roles_proyectos()
    {
            $data = Yii::$app->request->get();
            $post=null;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $q =(!empty($data['q']))? trim($data['q']):'';
            $long= (!is_null($q)?strlen(trim($q)):0);
            $out = ['results' => ['PK_ROL' => '', 'DESC_ROL' => '']];

            if (!is_null($q) && $long >= 3) {
                $query = new Query;
                $query->select(['PK_ROL AS id', 'DESC_ROL AS text'])
                    ->from('tbl_proyectos_roles roles')
                    ->where(['like','roles.DESC_ROL', "$q"])
                    ->orderBy(['roles.DESC_ROL'=>'SORT_ASC']);
                    // ->limit(20);
                // return $query;
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_ROL AS id', 'DESC_ROL AS text'])
                    ->from('tbl_proyectos_roles roles')
                    ->where(['like','roles.DESC_ROL', "$q"])
                    ->orderBy(['roles.DESC_ROL'=>'SORT_ASC']);
                    // ->limit(20);
                // return $query;
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
            return $out;
            // return $q;
    }


    public function actionObtener_tiposincidencias()
    {
        $data = Yii::$app->request->get();
        $post=null;
        // parse_str($data['data'],$post);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';
        $condicion = [];
        $out = ['results' => ['PK_TIPO_INCIDENCIA' => '', 'DESC_TIPO_INCIDENCIA' => '']];
        if($p==1){
            $condicion = ['NOT IN','PK_TIPO_INCIDENCIA', 11];
        }
        if (!is_null($q)) {
            $query = new Query;
            $query->select('PK_TIPO_INCIDENCIA AS  id, DESC_TIPO_INCIDENCIA AS text')
                ->from('tbl_cat_tipo_incidencia')
                ->where(['like','DESC_TIPO_INCIDENCIA', "$q"])
                ->andWhere($condicion)
                ->andWhere(['=','VISIBLE',1])
                ->orderBy(['DESC_TIPO_INCIDENCIA'=>'SORT_ASC']);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        else{
            $query = new Query;
            $query->select('PK_TIPO_INCIDENCIA AS  id, DESC_TIPO_INCIDENCIA AS text')
                ->from('tbl_cat_tipo_incidencia')
                ->where($condicion)
                ->andWhere(['=','VISIBLE',1])
                ->orderBy(['DESC_TIPO_INCIDENCIA'=>'SORT_ASC']);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    public function actionAplicativo()
    {

        $data = Yii::$app->request->get();
        $post=null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';

        $out = ['results' => ['PK_CLIENTE' => '', 'DESC_APLICATIVO' => '']];
        if (!is_null($q)){
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select('PK_APLICATIVO AS  id, DESC_APLICATIVO AS text')
                    ->from('tbl_cat_aplicativo')
                    ->where(['like','DESC_APLICATIVO', "$q"])
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90] ])
                    ->orderBy(['DESC_APLICATIVO'=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select('PK_APLICATIVO AS  id, DESC_APLICATIVO AS text')
                    ->from('tbl_cat_aplicativo')
                    ->where(['like','DESC_APLICATIVO', "$q"])
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                    ->orderBy(['DESC_APLICATIVO'=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }else{
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select('PK_APLICATIVO AS  id, DESC_APLICATIVO AS text')
                    ->from('tbl_cat_aplicativo')
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90] ])
                    ->orderBy(['DESC_APLICATIVO'=>'SORT_ASC']);
                $command = $query->createCommand();

                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else {
                $query = new Query;
                $query->select('PK_APLICATIVO AS  id, DESC_APLICATIVO AS text')
                    ->from('tbl_cat_aplicativo')
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                     ->orderBy(['DESC_APLICATIVO'=>'SORT_ASC']);
                $command = $query->createCommand();

                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }

        }
        return $out;
    }

//map(tblcatcontactos::find()->where(['=','FK_PUESTO','59'])->orderBy('NOMBRE_CONTACTO')->asArray()->all(), 'PK_CONTACTO',function($model, $defaultValue) { return $model['NOMBRE_CONTACTO'].' '.$model['APELLIDO_PAT'].' '.$model['APELLIDO_MAT'];});


    public function actionPm()
    {

        $data = Yii::$app->request->get();
        $post=null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';

        $out = ['results' => ['PK_CLIENTE' => '', 'NOMBRE_CONTACTO' => '']];
        if (!is_null($q)) {
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->where(['like',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)", "$q"])
                    ->andWhere(['=','FK_PUESTO','7'])
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90] ])
                    ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->where(['like',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)", "$q"])
                    ->andWhere(['=','FK_PUESTO','7'])
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                    ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }else{
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->andWhere(['=','FK_PUESTO','7'])
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90] ])
                    ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();

                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->andWhere(['=','FK_PUESTO','7'])
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                     ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();

                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }

        }
        return $out;
    }

    public function actionDirector()
    {

        $data = Yii::$app->request->get();
        $post=null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';
        $out = ['results' => ['PK_CLIENTE' => '', 'NOMBRE_CONTACTO' => '']];
        if(!is_null($q)){
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->where(['like',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)", "$q"])
                    ->andWhere(['=','FK_PUESTO','1'])
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90]])
                    ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->where(['like',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)", "$q"])
                    ->andWhere(['=','FK_PUESTO','1'])
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                    ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }else{
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->andWhere(['=','FK_PUESTO','1'])
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90]])
                     ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->andWhere(['=','FK_PUESTO','1'])
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                     ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }
        return $out;
    }

    public function actionContacto()
    {
        $data = Yii::$app->request->get();
        $post=null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';
        $out = ['results' => ['PK_CLIENTE' => '', 'NOMBRE_CONTACTO' => '']];
        if (!is_null($q)){
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->where(['like',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)", "$q"])
                    //->andWhere(['=','FK_CLIENTE', "$p"])
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90]])
                    ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->where(['like',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)", "$q"])
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                    ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }else{
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90]])
                    ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();

                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_CONTACTO AS id',"CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT) AS text"])
                    ->from('tbl_cat_contactos')
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                     ->orderBy(["CONCAT(NOMBRE_CONTACTO,' ',APELLIDO_PAT,' ',APELLIDO_MAT)"=>'SORT_ASC']);
                $command = $query->createCommand();

                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }
        return $out;
    }

    public function actionDireccion()
    {
        $data = Yii::$app->request->get();
        $post=null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';
        $out = ['results' => ['PK_CLIENTE' => '', 'NOMBRE_DIRECCION' => '']];
        if (!is_null($q)){
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['PK_DIRECCION AS id',"NOMBRE_DIRECCION AS text"])
                    ->from('tbl_cat_direccion')
                    ->where(['like',"NOMBRE_DIRECCION", "$q"])
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90] ])
                    ->orderBy(["NOMBRE_DIRECCION"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_DIRECCION AS id',"NOMBRE_DIRECCION AS text"])
                    ->from('tbl_cat_direccion')
                    ->where(['like',"NOMBRE_DIRECCION", "$q"])
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                    ->orderBy(["NOMBRE_DIRECCION"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }else{
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['PK_DIRECCION AS id',"NOMBRE_DIRECCION AS text"])
                    ->from('tbl_cat_direccion')
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90] ])
                    ->orderBy(["NOMBRE_DIRECCION"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_DIRECCION AS id',"NOMBRE_DIRECCION AS text"])
                    ->from('tbl_cat_direccion')
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                    ->orderBy(["NOMBRE_DIRECCION"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }

        }
        return $out;
    }

    public function actionSubdireccion()
    {
        $data = Yii::$app->request->get();
        $post=null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';
        $r =(!empty($data['r']))? trim($data['r']):'';

        $out = ['results' => ['PK_CLIENTE' => '', 'NOMBRE_SUBDIRECCION' => '']];
        if (!is_null($q)){
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['PK_SUBDIRECCION AS id',"NOMBRE_SUBDIRECCION AS text"])
                    ->from('tbl_cat_subdireccion')
                    ->where(['like',"NOMBRE_SUBDIRECCION", "$q"])
                    ->andWhere(['IN',"FK_DIRECCION", "$r"])
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90]])
                    ->orderBy(["NOMBRE_SUBDIRECCION"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_SUBDIRECCION AS id',"NOMBRE_SUBDIRECCION AS text"])
                    ->from('tbl_cat_subdireccion')
                    ->where(['like',"NOMBRE_SUBDIRECCION", "$q"])
                    ->andWhere(['IN',"FK_DIRECCION", "$r"])
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                    ->orderBy(["NOMBRE_SUBDIRECCION"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }else{
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['PK_SUBDIRECCION AS id',"NOMBRE_SUBDIRECCION AS text"])
                    ->from('tbl_cat_subdireccion')
                    ->andWhere(['IN','FK_CLIENTE', [2,71,90]])
                    ->andWhere(['IN',"FK_DIRECCION", "$r"])
                    ->orderBy(["NOMBRE_SUBDIRECCION"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['PK_SUBDIRECCION AS id',"NOMBRE_SUBDIRECCION AS text"])
                    ->from('tbl_cat_subdireccion')
                    ->andWhere(['=','FK_CLIENTE', "$p"])
                    ->andWhere(['IN',"FK_DIRECCION", "$r"])
                    ->orderBy(["NOMBRE_SUBDIRECCION"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }
        return $out;
    }


    public function actionCliente_tarifa()
    {
        $data = Yii::$app->request->get();
        $post=null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';
        $p =(!empty($data['p']))? trim($data['p']):'';
        $out = ['results' => ['PK_CLIENTE' => '', 'TARIFA' => '']];
        if (!is_null($q)){
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['tc.FK_CAT_TARIFA AS id', "CONCAT(ct.DESC_TARIFA,' ',ct.TARIFA) AS text"])
                ->from('tbl_clientes cl')
                ->join('LEFT JOIN','tbl_tarifas_clientes tc',
                                   'cl.PK_CLIENTE = tc.FK_CLIENTE')
                ->join('LEFT JOIN','tbl_cat_tarifas ct',
                                   'tc.FK_CAT_TARIFA = ct.PK_CAT_TARIFA')
                ->where(['like',"CONCAT(ct.DESC_TARIFA,' ',ct.TARIFA)", "$q"])
                ->andWhere(['IN','cl.PK_CLIENTE', [2,71,90]])
                ->orderBy(["CONCAT(ct.DESC_TARIFA,' ',ct.TARIFA)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['tc.FK_CAT_TARIFA AS id', "CONCAT(ct.DESC_TARIFA,' ',ct.TARIFA) AS text"])
                ->from('tbl_clientes cl')
                ->join('LEFT JOIN','tbl_tarifas_clientes tc',
                                   'cl.PK_CLIENTE = tc.FK_CLIENTE')
                ->join('LEFT JOIN','tbl_cat_tarifas ct',
                                   'tc.FK_CAT_TARIFA = ct.PK_CAT_TARIFA')
                ->where(['=','cl.PK_CLIENTE', "$p"])
                ->orderBy(["CONCAT(ct.DESC_TARIFA,' ',ct.TARIFA)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }else{
            if($p == 71 || $p == 90){
                $query = new Query;
                $query->select(['tc.FK_CAT_TARIFA AS id', "CONCAT(ct.DESC_TARIFA,' ',ct.TARIFA) AS text"])
                    ->from('tbl_clientes cl')
                    ->join('LEFT JOIN','tbl_tarifas_clientes tc',
                                       'cl.PK_CLIENTE = tc.FK_CLIENTE')
                    ->join('LEFT JOIN','tbl_cat_tarifas ct',
                                       'tc.FK_CAT_TARIFA = ct.PK_CAT_TARIFA')
                    ->andWhere(['IN','cl.PK_CLIENTE', [2,71,90]])
                    ->orderBy(["CONCAT(ct.DESC_TARIFA,' ',ct.TARIFA)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }else{
                $query = new Query;
                $query->select(['tc.FK_CAT_TARIFA AS id', "CONCAT(ct.DESC_TARIFA,' ',ct.TARIFA) AS text"])
                    ->from('tbl_clientes cl')
                    ->join('LEFT JOIN','tbl_tarifas_clientes tc',
                                       'cl.PK_CLIENTE = tc.FK_CLIENTE')
                    ->join('LEFT JOIN','tbl_cat_tarifas ct',
                                       'tc.FK_CAT_TARIFA = ct.PK_CAT_TARIFA')
                    ->where(['=','cl.PK_CLIENTE', "$p"])
                    ->orderBy(["CONCAT(ct.DESC_TARIFA,' ',ct.TARIFA)"=>'SORT_ASC']);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            }
        }
        return $out;
    }


     public function actionCat_tarifa()
    {
            if (Yii::$app->request->isAjax)
            {
                $data = Yii::$app->request->post();
                $FK_CLIENTE= $data['FK_CLIENTE'];
                $traedatos = false;
                    if($FK_CLIENTE != '' && $FK_CLIENTE >0)
                    {
                        $modelTarifas = TblTarifasClientes::find()->where(['FK_CLIENTE' => $FK_CLIENTE])->limit(1)->one();
                        if(count($modelTarifas)>0){
                        $traedatos = true;
                        }

                    }
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return[
                            'FK_CLIENTE' => $FK_CLIENTE,
                            'traedatos' => $traedatos,
                        ];
            }

    }
    public function actionMarcas()
    {
        $data = Yii::$app->request->get();
        $post=null;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q =(!empty($data['q']))? trim($data['q']):'';

        $query = new Query;
        $query->select('M.PK_MARCA AS id, M.DESC_MARCA AS text')
            ->from('tbl_cat_marcas AS M')
            ->where(['like','M.DESC_MARCA',$q]);
        $command = $query->createCommand();

        $data = $command->queryAll();
        $out['results'] = array_values($data);

        return $out;
    }



}
