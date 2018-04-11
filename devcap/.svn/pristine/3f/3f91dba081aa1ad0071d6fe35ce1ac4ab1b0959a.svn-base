<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

use yii\helpers\ArrayHelper;
use app\models\TblCatTipoDocumentos;
use app\models\TblCatProyectos;
use app\models\TblCatEstatusAsignaciones;
use app\models\TblClientes;
use app\models\TblCatContactos;
use app\models\TblPerfilEmpleados;
//use app\models\TblCatUbicaciones;
use app\models\TblCatUnidadesNegocio;
use yii\web\JsExpression;
use kartik\select2\Select2; 





$modelo_cargado     ='asignaciones';
$datosTipoDocumento = ArrayHelper::map(TblCatTipoDocumentos::find()->asArray()->orderBy('DESC_TIPO_DOCUMENTO')->all(), 'PK_TIPO_DOCUMENTO', 'DESC_TIPO_DOCUMENTO');
$proyectos          = ArrayHelper::map(TblCatProyectos::find()->asArray()->orderBy('NOMBRE_PROYECTO')->all(), 'PK_PROYECTO', 'NOMBRE_PROYECTO');
$estatusAsig        = ArrayHelper::map(TblCatEstatusAsignaciones::find()->orderBy('DESC_ESTATUS_ASIGNACION')->asArray()->all(), 'PK_ESTATUS_ASIGNACION', 'DESC_ESTATUS_ASIGNACION');
$clientes           = ArrayHelper::map(TblClientes::find()->asArray()->orderBy('NOMBRE_CLIENTE')->all(), 'PK_CLIENTE', 'NOMBRE_CLIENTE');
$datosClientes2       = ArrayHelper::map(tblclientes::find()->orderBy('NOMBRE_CLIENTE')->where(['>','HORAS_ASIGNACION',0])->asArray()->all(), 'PK_CLIENTE', 'NOMBRE_CLIENTE');
$unidadNegocioUsuario = TblPerfilEmpleados::find()->select('FK_UNIDAD_NEGOCIO')->where(['=', 'FK_EMPLEADO', user_info()['FK_EMPLEADO']])->asArray()->one();
// $jefe               = TblPerfilEmpleados::find()->select('FK_JEFE_DIRECTO')->where(['=', 'FK_EMPLEADO', user_info()['FK_EMPLEADO']])->asArray()->one();
$contactos          = ArrayHelper::map(TblCatContactos::find()->asArray()->orderBy('NOMBRE_CONTACTO')->all(), 'PK_CONTACTO', 'NOMBRE_CONTACTO');
//$ubicacion          = ArrayHelper::map(TblCatUbicaciones::find()->asArray()->orderBy('DESC_UBICACION')->all(), 'PK_UBICACION', 'DESC_UBICACION');
//$razon_social       = ArrayHelper::map(TblCatUnidadesNegocio::find()->asArray()->orderBy('DESC_UNIDAD_NEGOCIO')->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
if(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!is_super_admin()){
    $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
    $razon_social = ArrayHelper::map(TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO'=>$unidadesNegocioValidas])->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
} else {
    $razon_social = ArrayHelper::map(TblCatUnidadesNegocio::find()->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
}
$url_contactos      = \yii\helpers\Url::to(['asignaciones/contactos']); 
$url_clientes      = \yii\helpers\Url::to(['site/clientes']);
$url_clientesAsignacion      = \yii\helpers\Url::to(['site/clientes_asignacion']); 
$url_ubicaciones    = \yii\helpers\Url::to(['asignaciones/ubicaciones']); 
$url_proyectos      = \yii\helpers\Url::to(['asignaciones/proyectos']); 
$modelResponsableOP   = ArrayHelper::map($modelResponsableOP, 'PK_EMPLEADO', 'nombre_emp');



/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Consulta de Asignaciones';
$this->params['breadcrumbs'][] = $this->title;
// $total_paginas='1';
//print_r(user_info());
?>
<head>
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<script>
    var ajaxUrl                     = '<?php echo Url::to(["$modelo_cargado/index"]); ?>';
    var ajaxUrlModal                ='<?php echo Url::to(["$modelo_cargado/modal"]); ?>';
    var _csrfToken                  = '<?=Yii::$app->request->getCsrfToken()?>';
    var url_view                    = '<?php echo Url::to(["$modelo_cargado/view",'id'=>'lorem']); ?>';
    var url_documentos              = '<?php echo Url::to(["documentos/index"]); ?>';
    var url_periodos                = '<?php echo Url::to(["periodos/index/lorem"]); ?>';
    var url                         = '<?php echo Url::to(["$modelo_cargado/view",'id'=>'lorem']); ?>';
    var url_update                  = '<?php echo Url::to(["$modelo_cargado/update",'id'=>'lorem','_status'=>'pend']); ?>'; //Variable viene de ERT.js
    // var ajaxAprobarAsignacion       = '<?php echo Url::to(["$modelo_cargado/aprobar"]); ?>';
    // var ajaxRechazarAsignacion      = '<?php echo Url::to(["$modelo_cargado/cancelar_asignacion"]); ?>';
    var ajaxNoAprobarAsignacion     = '<?php echo Url::to(["$modelo_cargado/comentario"]); ?>';
    var ajaxSiAprobarAsignacion     = '<?php echo Url::to(["$modelo_cargado/aprobando"]); ?>';
   
</script>
<style>
  .toggle-on{
    background-color: #39A0EF;
  }
  .toggle-off{
    background-color: #39A0EF;
  }
  #tarjeta{
    border-radius: 10%;
    position:absolute;
    top:45%;
    left:90%;
    width:30px;
    height: 30px;
    visibility: visible;
  }
  #grid{
    border-radius: 10%;
    position: absolute;
    top:45%;
    left:92.5%;
    width: 30px;
    height: 30px;
    visibility: visible;
  }

</style>
<div class="trabajo"></div>
<div class="row">
    <h1 class="title col-lg-12"><b><?= Html::encode($this->title) ?></b>
        <?php if(valida_permisos(['asignaciones/create'])){ ?>
          <?= Html::a('DAR DE ALTA ASIGNACI&Oacute;N', ['create'], ['class' => 'btn btn-success der', 'id'=>'alta','style'=>'display:none']) ?>
        <?php } ?>
        <?php if(valida_permisos(['asignaciones/index_bolsa'])){ ?>
          <?= Html::a('Relacionar Asignaciones a Bolsas', ['index_bolsa'], ['class' => 'btn btn-success der','id'=>'rel','style'=>'display:none']) ?>
        <?php } ?>
            <button class="btn btn-success der" id="altas" disabled="true" style="visibility: hidden;display: none;">Aprobar Asignación</button>
            <button class="toggle-on" id="tarjeta" ><i class="fa fa-user izq" style="color:white;"></i></button>
            <button class="toggle-off" id="grid" ><i class="fa fa-align-left izq" style="color:white;"></i></button>
        
    </h1>

             
            
    <div class="clear"></div>
      
    <div class="col-lg-12">
        <div class="resultados">
                <?= Html::beginForm(Url::to(["$modelo_cargado/index"]),'post',['class' => 'form-index-asignaciones', 'id'=>'form-ajax','data-history'=>'true']); ?>
            <div class="campos">
                <h3 class="campos-title font-bold">B&uacute;squeda 
                    <!-- <a href="javascript:void(0)" class="arrow-event item-up-arrow icon-12x21 der"></a> -->
                </h3>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <?= Html::label('Nombre','search',['class'=>'campos-label']) ?>
                        <?= Html::input('text','nombre',null,['id'=>'search','class'=>'form-control search']) ?>
                    </div>
            
                    <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12 styled-select">
                        <?= Html::label('Tipo de Documento','search',['class'=>'campos-label']) ?>
                        <?= Html::dropDownList ('documentos',null,$datosTipoDocumento,['id'=>'documentos','class' => 'giro form-control','prompt'=>'']) ?>
                    </div>

                     <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <?= Html::label('Número de Documento','search',['class'=>'campos-label']) ?>
                        <?= Html::input('text','NumDoc',null,['id'=>'search','class'=>'form-control search']) ?>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12 styled-select">
                        <?= Html::label('Estatus de asignación','search',['class'=>'campos-label']) ?> 
                        <?= Html::dropDownList ('estatus',null,$estatusAsig,['id'=>'estatus','class' => 'form-control giro','prompt'=>'']) ?>  
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <?= Html::label('Responsable de OP','search',['class'=>'campos-label']) ?> 
                         <?php echo Select2::widget([
                            'name' => 'fk_responsable_op',
                            'model' => '',
                            'attribute' => 'fk_responsable_op',
                            'data' => $modelResponsableOP,
                            'options' => ['placeholder' => '',
                                          'id' => 'fk_responsable_op_form',
                                          'class'=>'giro'
                                          ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);?>  
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <?= Html::label('Cliente','search',['class'=>'campos-label']) ?> 
                        <?php echo Select2::widget([
                            // 'disabled'=>true,
                            'name' => 'cliente',
                            'model' => '',
                            'attribute' => 'cliente',
                            'data' => $datosClientes2 ,
                            'options' => ['placeholder' => '',
                                      'id' => 'fk_clienteForm',
                                      'class'=>'giro'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'ajax' => [
                                    'url' => $url_clientes,
                                    'dataType' => 'json',
                                    'delay' => 250,
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(data) { return data.text; }'),
                            'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                        ],
                        ]);?> 
                    </div>



                   <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <?= Html::label('Contacto','search',['class'=>'campos-label']) ?> 

                        <?php echo Select2::widget([
                      'disabled'=>true,
                      'name' => 'contacto',
                      'model' => '',
                      'attribute' => 'contacto',
                      'data' => $contactos ,
                      'options' => ['placeholder' => '',
                                'id' => 'fk_contactoForm',
                                'class'=>'giro'],
                      'pluginOptions' => [
                          'allowClear' => true,
                          'ajax' => [
                              'url' => $url_contactos,
                              'dataType' => 'json',
                              'delay' => 250,
                              'data' => new JsExpression('function(params) { return {q:params.term,p:$("#fk_clienteForm").val()}; }')
                          ],
                      'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                      'templateResult' => new JsExpression('function(data) { return data.text; }'),
                      'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                  ],
                  ]);?> 
                  
                      
                    </div> 
                    <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <?= Html::label('Proyecto','search',['class'=>'campos-label']) ?>
                       


                           <?php echo Select2::widget([
                            'disabled'=>true,
                          'name' => 'proyectos',
                          'model' => '',
                          'attribute' => 'proyectos',
                          'data' => $proyectos,
                          'options' => ['placeholder' => '',
                                        'id' => 'proyectos',
                                'class'=>'giro'],
                          'pluginOptions' => [
                              'allowClear' => true,
                              'ajax' => [
                                      'url' => $url_proyectos,
                                      'dataType' => 'json',
                                      'delay' => 250,
                                      'data' => new JsExpression('function(params) { return {q:params.term,p:$("#fk_contactoForm").val()}; }')
                                  ],
                              'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                              'templateResult' => new JsExpression('function(data) { return data.text; }'),
                              'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                          ],
                          ]);?>  


                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <?= Html::label('Ubicación','search',['class'=>'campos-label']) ?> 
                        <?php echo Select2::widget([
                            'disabled'=>true,
                      'name' => 'ubicacion',
                      'model' => '',
                      'attribute' => 'ubicacion',
                      //'data' => $ubicacion ,
                      'options' => ['placeholder' => '',
                                'id' => 'fk_ubicacion_form',
                                'class'=>'giro'],
                      'pluginOptions' => [
                          'allowClear' => true,
                          'ajax' => [
                              'url' => $url_ubicaciones,
                              'dataType' => 'json',
                              'delay' => 250,
                              'data' => new JsExpression('function(params) { return {q:params.term,p:$("#fk_clienteForm").val()}; }')
                          ],
                      'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                      'templateResult' => new JsExpression('function(data) { return data.text; }'),
                      'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                  ],
                  ]);?> 
                    </div>
                    
                    <?php if(
                                (isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))
                                ||
                                (isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO']))
                                ||is_super_admin())
                            {
                    ?>
                      <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12">
                          <?= Html::label('Unidad de Negocio','search',['class'=>'campos-label']) ?> 
                          <?php echo Select2::widget([
                                  
                                  'name' => 'razon_social',
                                  'model' => '',
                                  'value'=>$unidadNegocioUsuario['FK_UNIDAD_NEGOCIO'],
                                  'attribute' => 'razon_social',
                                  'data' => $razon_social ,
                                  'options' => [
                                      'placeholder' => '',
                                      'prompt'=>'',
                                      'class'=>'giro'
                                    ],
                                  'pluginOptions' => [
                                      'allowClear' => true,
                                  ],
                              ]);?> 
                      </div>
                    <?php } ?>

                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                        <?= Html::submitButton('BUSCAR',['class'=>'btn btn-success btn-buscar der']) ?> 
                        <?= Html::resetButton('LIMPIAR', ['class' => 'btn btn-success reset btn-buscar der', 'onclick'=>'javascript:buscar_registros()']) ?>
                        <div class="form-group text-center der">
                            <div class="pagination-sm" style="margin:0 15px;" id="top-pg"></div><p class="paginas text-center"></p>
                        </div>
                    </div>
                    <div class="clear"></div>
            </div>
            <div class="resultados asignaciones-view">
                <div class="form-group col-lg-2 col-md-4 col-sm-4">

                   <!-- <div  >
                        <button type="button" class="btn btn-default" data-container="body" rel="popover" data-toggle="popover" data-placement="bottom" data-content="<div>En ejecucion</div><div>Pendiente</div><div>Finalizado</div><div>Cerrado</div>">
                          ESTATUS DE ASIGNACIÓN
                        </button>
                    </div>

                        <?= Html::label('Nombre','search',['class'=>'campos-label']) ?>
                        <?= Html::input('text','BUSCAR',null,['id'=>'search','class'=>'search form-control']) ?>
                    </div>-->

            </div>
            <div class="resultados asignaciones-create_remplazo">
                <div class="form-group col-lg-2 col-md-4 col-sm-4">
                   <!-- <div  >
                        <button type="button" class="btn btn-default" data-container="body" rel="popover" data-toggle="popover" data-placement="bottom" data-content="<div>En ejecucion</div><div>Pendiente</div><div>Finalizado</div><div>Cerrado</div>">
                          ESTATUS DE ASIGNACIÓN
                        </button>
                    </div>

                        <?= Html::label('Nombre','search',['class'=>'campos-label']) ?>
                        <?= Html::input('text','BUSCAR',null,['id'=>'search','class'=>'search form-control']) ?>
                    </div>-->

            </div>
                <?= Html::endForm(); ?>

               
     

            <div class="clear">

            </div>
            <div class="contenedor-parent row"> <!--style="position: relative; right: 30%; width:1500px; min-width: 90%; " -->
                <div class="resultados">
                 
                    <div class="contenedor">
                       
                    </div>
                    <div class="contenedor-parent row">
                      <div class="contenedorTabla">
                          <table id="tabla" data-role="table" data-mode="columntoggle" class="cell-border table table-striped table-bordered dt-responsive order-column hover row-border dataTable" style="display: none;">
                                    <thead>

                                          <tr>
         <td colspan="3" style="background-color:#082644;color:white;">Datos de la asignación</td>
         <td colspan="2" style="background-color:#0A3B66;color:white;" >Datos del Cliente</td>
         <td colspan="3" style="background-color:#16528A;color:white;">Datos Personales</td>
         <td colspan="2" style="background-color:#1F64A2;color:white;">Datos del Periodo </td>
      </tr>
      <tr>
         <td style="background-color:#082644;color:white;">Nombre de la asignación</td>
           <td style="background-color:#082644;color:white;">Inicio</td>
         <td style="background-color:#082644;color:white;">Fin</td>

          <td style="background-color:#0A3B66;color:white;">Cliente</td>
          <td style="background-color:#0A3B66;color:white;">Contacto</td>

           <td style="background-color:#16528A;color:white;">Nombre</td>
           <td style="background-color:#16528A;color:white;">Correo Interno</td>
           <td style="background-color:#16528A;color:white;">Celular</td>

            <td style="background-color:#1F64A2;color:white;">Monto Estimado</td>
             <td style="background-color:#1F64A2;color:white;">Tarifa</td>
      </tr>
      <tr>
 <td style="background-color:white;"></td>
 </tr>
 <tr>
<td style="background-color:white;"></td>
</tr>
<tr>
<td style="background-color:white;"></td>
</tr>
<tr>
<td style="background-color:white;"></td>
</tr>
<tr>
<td style="background-color:white;"></td>
</tr>


                                    </thead>
                                     <tbody>
                                  
                                    </tbody>
                                  </table>
                                </div>
                    </div>
                    <div class="form-group col-lg-3 der">
                        <button id="exportar-excel" class="btn btn-success btn-buscar der habilitado">Exportar a Excel</button>
                    </div>
                </div>
                <div class="modal fade" id="modal-info" role="dialog">
    <div class="modal-informa modal-dialog modal-lg">
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                     <img src="<?php echo get_home_url() ?>/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                     <label style="text-align: center; font-weight: bold;"><br/>Fecha baja</label>
                </div>
            </div>
        </div>
    </div>
</div>

            </div>
            <div class="clear"></div>
            <div class="campos">
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <div class="form-group col-lg-12 col-md-12 col-sm-12 text-center">
                        <div class="pagination-sm" id="bottom-pg"></div><p class="paginas text-center"></p>
                    </div>
                    
                </div>
                <div class="clear"></div>
            </div>

        </div>    
    </div>
</div>

<div class="modal fade" id="cancelar-cambios" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>

                    <img src="<?php echo get_home_url() ?>/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p style="text-align: center; font-weight: bold;">
                    ¿Desea exportar esta información a un Archivo Excel?
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-cancel">CANCELAR</button>
                    <!--<button type="button" class="btn btn-success cancelar-cambios">ACEPTAR</button>-->
                        <form action="../../views/asignaciones/excel.php" name="form1" target="_blank">
                            <input type="submit" class="btn btn-success" value="EXPORTAR A EXCEL"></input>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-excel" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>

                    <img src="<?php echo get_home_url() ?>/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p style="text-align: center; font-weight: bold;">
                    ¿Desea exportar esta información a un Archivo Excel?
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">CANCELAR</button>
                    <!--<button type="button" class="btn btn-success modal-excel">ACEPTAR</button>-->
                        <form action="../../views/asignaciones/excel.php" name="formExcel" id="formExcel" target="_blank" method="post">
                            <!-- <input type="hidden" name="excel_nombre" id="excel_nombre">
                            <input type="hidden" name="excel_tipoDoc" id="excel_tipoDoc">
                            <input type="hidden" name="excel_NumDoc" id="excel_NumDoc">
                            <input type="hidden" name="excel_proyecto" id="excel_proyecto">
                            <input type="hidden" name="excel_cliente" id="excel_cliente">
                            <input type="hidden" name="excel_contacto" id="excel_contacto">
                            <input type="hidden" name="excel_ubicacion" id="excel_ubicacion">
                            <input type="hidden" name="excel_estatus" id="excel_estatus"> -->
                            <input type="submit" class="btn btn-success" id="btnExportarExcel" value="EXPORTAR A EXCEL"></input>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-aprobar" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>

                    <img src="<?php echo get_home_url() ?>/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p style="text-align: center; font-weight: bold; padding:10px;">
                    El empleado que desea asignar se encuentra asociado a un proyecto, </br>¿Desea deslindarlo del proyecto?
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                        <button type="button" class="btn btn-cancel" data-dismiss="modal" id="cancelar_aprobado" data-pk_asignacion="">CANCELAR</button>
                        <button type="button" class="btn btn-success modal-excel" id="aceptar_aprobado" data-pk_asignacion="">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-rechazado" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>

                    <img src="<?php echo get_home_url() ?>/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p style="text-align: center; font-weight: bold; padding:10px;">
                    Desea continuar con el rechazo de la asignaci&oacute;n: <span id="nombre_asignacion"></span>?
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                        <button type="button" class="btn btn-cancel" data-dismiss="modal" id="cancelar_rechazado" data-pk_asignacion="">CANCELAR</button>
                        <button type="button" class="btn btn-success modal-excel" id="aceptar_rechazado" data-pk_asignacion="">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalAprobar" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div class="con">
                  <?= Html::beginForm(Url::to(["$modelo_cargado/comentario"]),'post',['class' => 'form']); ?>
                    <img src="<?php echo get_home_url() ?>/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p style="text-align: center; font-weight: bold; padding:10px;" id="parrafo">
                     ¿Autoriza asignación de recurso?El recurso<br/>
                     cambiara de Proyecto a Asignación<br/>
                    </p>
                   <!--  <div style="text-align: center;" id="checkboxes">
                      <label for="siAprobar">Si</label>
                        <input type="checkbox" name="si" id="siAprobar">
                      <label for="noAprobar">No</label>
                        <input type="checkbox" name="no" id="noAprobar">
                    </div>
                    <div style="text-align: center;" id="areaComentario">
                        <textarea id="comment" name="comment" rows="6" cols="30" disabled></textarea>
                    </div> -->
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                        <button type="button" class="btn btn-cancel" data-dismiss="modal" id="cancelar_pendienteAprobacion">CANCELAR</button>
                        <button type="button" class="btn btn-success modal-excel" id="aceptar_pendienteAprobacion">ACEPTAR</button>
                    </div>
                    <?= Html::endForm(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-aprobar-exito" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>

                    <img src="<?php echo get_home_url() ?>/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p style="text-align: center; font-weight: bold; padding:10px;">
                    El empleado se ha registrado con éxito en la nueva asignación, <br>¿Desea publicar una vacante con las características del empleado?
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                        <button type="button" class="btn btn-cancel" id="cancelar_aprobado_exito" data-pk_asignacion="">CANCELAR</button>
                        <a class="btn btn-success modal-excel" id="aceptar_aprobado_exito" data-pk_asignacion="" href="<?php echo Url::to(['vacantes/create']) ?>">ACEPTAR</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
  .resultados .contenedor .contenedor-item {
    min-height: 152px!important
  }
  .asignaciones-view .contenedor-item 
  {
    min-height: auto!important;
  }
.resultados .contenedor .contenedor-item:hover> .row {
    position: relative;
    top: 0;
    left: 0;
    /* padding: 35px 0; */
    background-color: #fff;
    /*z-index: 3;*/
    box-shadow: 0px 0px 5px 3px #DDD;
    width: auto;
    height: 150px;
}

</style>
<script>
  //cambio de vista "vista tipo tarjeta a tipo de vista Gridview"
$('#tarjeta').mouseleave(function(){
  $('#tarjeta').attr('title','');
});
  $('#tarjeta').on("click",function(){
    $('.contenedor').toggle(true);
    $('.contenedorTabla').toggle(false);    
    $('#tabla').css('display','none');
    $('.campos').toggle(true);
    $('#tarjeta').css('background-color','#04243B'); 
    $('#grid').css('background-color','#39A0EF');
    $('.btn-warning').tooltip({trigger: "hover"}); 
  });  
  $('#grid').on("click",function(){
     $('#tabla').css('display','block');
    $('.contenedor').toggle(false);
    $('.contenedorTabla').toggle(true);
    $('.campos').toggle(false);
    $('#grid').css('background-color','#04243B');
    $('#tarjeta').css('background-color','#39A0EF');
  });
  //deshabilitar el boton de alta de la bandeja pendientes de aprobacion
    $('.container').on('change','input[name="id"]',function(){
      
      if(this.checked==true){
        $('#altas').attr('disabled',false);
      } else {
       $('#altas').attr('disabled',true);
          $('input[type=checkbox]:checked').each(function() {
           $('#altas').attr('disabled',false); 
           
          });
      }
      if(this.checked==true){

      $('#parrafo').append('<form class="row" style="margin: 0px; padding: 0px; text-align: center;" id="form">'+'<input type="checkbox" name="si" id="siAprobar">SI'+'<input type="checkbox" name="no" id="noAprobar">NO'+'</form>'+'<div style="text-align: center;" id="areaComentario">'+'<textarea id="comment" name="comment" rows="6" cols="30" disabled></textarea>'+'</div>');
      }

    });

  //habilitar y deshabilitar el area de comentarios
    $('.con').on('change','input[type="checkbox"]',function(){

      if($('#noAprobar').is(':checked') == true){
        $('#comment').attr('disabled',false);
      }else{
        $('#comment').attr('disabled',false);
        $('input[name="si"]:checked').each(function() {
           $('#comment').attr('disabled',true); 
        });
      }
      });
    //mostrar modal de aprobación
    $('#altas').on("click",function(){
         $('#modalAprobar').modal('show');
    });
    // logica de aprobacion
    $('#aceptar_pendienteAprobacion').on("click",function(){
      var idAsignacion= $('.check:checked').attr('data-pk_asignacion');
      var idRecursos =$('.check:checked').attr('data-recurso');
      var Nombre =$('.check:checked').attr('data-nombre');
      var comentario=$('#comment').val();
      if( $('#noAprobar').prop('checked') ) {
          $.ajax({
          url:  ajaxNoAprobarAsignacion,
          type:'post',
          data: {
              idAsignacion : idAsignacion,
              idRecursos : idRecursos,
              comentario: comentario,
              Nombre : Nombre
          },
          success:function(data){
              console.log(data)
              $('#modalAprobar').modal('hide');
          }
      });
        }
    });
    $('#aceptar_pendienteAprobacion').on("click",function(){
      var idAsignacion= $('.check:checked').attr('data-pk_asignacion');
      var Nombre =$('.check:checked').attr('data-nombre');
      if($('#siAprobar').prop('checked')){
          $.ajax({
          url:  ajaxSiAprobarAsignacion,
          type:'post',
          data: {
              idAsignacion : idAsignacion,
              Nombre : Nombre
          },
          success:function(data){
              console.log(data)
              $('#modalAprobar').modal('hide');
          }
        });
        }
    });
$(document).ready(function(){
    
    $('#tarjeta').tooltip({title: "Vista Tarjeta", trigger: "hover"}); 
    $('#grid').tooltip({title: "Vista Gridview", trigger: "hover"});
});
  
    jQuery(document).ready(function(){
      // $('#tbl').DataTable();
      $("#btnExportarExcel").click(function(){
        $("#modal-excel").modal('toggle');
      });
        $("#exportar-excel").click(function(){
            if($('#exportar-excel').attr('class') == 'btn btn-success btn-buscar der habilitado'){
                $('#formExcel [type="hidden"]').remove();
                $('#form-ajax [name]').each(function(index, el) {
                    // if($(el).val()!=''){
                        $('#formExcel').append('<input type="hidden" name="'+$(el).attr('name')+'" value="'+$(el).val()+'">')
                    // }
                });
                $("#modal-excel").modal();
            }
        });

        function habilitarExcel()
        {
            if($('.contenedor-parent').find('.contenedor-item').length ==0)
            {
                $('#exportar-excel').attr('class','btn btn-success btn-buscar der deshabilitado');
                $('#exportar-excel').prop('disabled',true);
            }
            else
            {
                $('#exportar-excel').attr('class','btn btn-success btn-buscar der habilitado');
                $('#exportar-excel').prop('disabled',false);
            }
        }

        //setInterval(habilitarExcel, 500);
    });
    var busqueda='nombre';
 
    function crear_elemento(data){
        mensaje = 'No existen asignaciones que cuenten con los criterios especificados en los parámetros de búsqueda';
        var div= jQuery('<div></div>');
        var caja= jQuery('<div></div>');
            caja.addClass('contenedor-item col-lg-12 col-md-12 col-sm-12 col-xs-12');

        var row = jQuery('<div></div>');
            row.addClass('row');

        var key= data.PK_ASIGNACION;
        var pk= data.fk_empleado;
        var nombre= data.NOMBRE_EMP;
        // var col_2= jQuery('<div></div>');
        //     col_2.addClass('col-lg-2 col-md-2 col-sm-2 col-xs-12 top');

            var a_view= '<a href="'+((url.indexOf('periodos')>-1)?url+'?i=true&h='+has:url)+'" title="Ver" class="item-ver">';
            a_view= a_view.replace("lorem", key);

        var col_10= jQuery('<div></div>');
            col_10.addClass('col-lg-12 col-md-12 col-sm-12 col-xs-12 top');

            var col_5= jQuery('<div></div>');

            col_5.addClass('col-lg-6 col-md-6 col-sm-12 col-xs-12');

            var html=a_view+'<form class="form-inline">'+'<input name="id" id="idRecursos" type="checkbox" style="position:relative; right:19%; top:5%;" class="check" data-recurso="'+pk+'" data-pk_asignacion="'+key+'" data-nombre="'+nombre+'">'+
            '<div class="form-group col-lg-3 col-md-4 col-sm-4 col-xs-12" style="background:url(<?php echo get_upload_url() ?>'+((data.FOTO_EMP!='default'&&data.FOTO_EMP!=''&&data.FOTO_EMP!=null)?data.FOTO_EMP:'/uploads/EmpleadosFotos/sin_perfil.png')+');height: 100px;width: 100px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;">'+'</div>'+
            '<div class="form-group" style="position:relative; left:10%;">'+
              '<label class="campos-label campos-label-title">'+data.NOMBRE+'</label>'+
              '<h4 class="campos-title zero-padding font-bold margin-10">'+data.NOMBRE_EMP + " " + data.APELLIDO_PAT_EMP + " " + 
                data.APELLIDO_MAT_EMP + '</br> '+data.DESC_PUESTO +'</h3>' +
              '<label class="campos-label campos-label-title" style="color:#000">'+((data.alias_cliente)?data.alias_cliente:data.NOMBRE_CLIENTE)+'</label>'
            +'</div><div class="clear"></div></form>';

      
            col_5.append(html);

            col_10.append(col_5);

            var col_5= jQuery('<div></div>');

            col_5.addClass('col-lg-4 col-md-3 col-sm-6 col-xs-6');

            var html=a_view+'<div class="form-group font-bold col-lg-6 col-md-12 col-sm-12">'+
                        
                        '<?= Html::label('Inicio','',['class'=>'campos-label']) ?>' +
                        '<p class="">'+data.FECHA_INI + '</p>'+
                       
                     '</div>';
            html+='<div class="form-group font-bold col-lg-6 col-md-12 col-sm-12">'+
                        '<?= Html::label('Fin','',['class'=>'campos-label']) ?>' +
                        '<p class="">'+data.FECHA_FIN + '</p>'+
                       
                     '</div>';
            col_5.append(html);

            col_10.append(col_5);

            var col_5= jQuery('<div></div>');

            col_5.addClass('col-lg-4 col-md-3 col-sm-6 col-xs-6');
            var html='<div class="form-group font-bold col-lg-6 col-md-12 col-sm-12">'+
                        '<?= Html::label('Tarifa','',['class'=>'campos-label']) ?>'+
                        '<p class="">'+ '$ '+data.TARIFA + '</p>' +
                     '</div>';
            if(data.NUM_DOCUMENTO!=null&&data.BANDEJA==0){
              html='<div class="form-group font-bold col-lg-6 col-md-12 col-sm-12">'+
                          '<?= Html::label('ODC','',['class'=>'campos-label']) ?>' +
                          '<p class="">' + data.NUM_DOCUMENTO + '</p>'+
                       '</div>';
              
            }else if(data.BANDEJA==1){
              html+='<div class="form-group font-bold col-lg-6 col-md-12 col-sm-12">'+
                          '<?= Html::label('Monto','',['class'=>'campos-label']) ?>' +
                          '<p class="">$ ' + data.MONTO + '</p>'+
                    '</div>';
              
            }

            col_5.append(html);
            row.append('<div class="estatus-item estatus-asignacion-'+data.FK_ESTATUS_ASIGNACION+'">'+data.DESC_ESTATUS_ASIGNACION+'</div>')
            // row.append('<div class="clear"></div>');

            col_10.append(col_5);
            <?php if(in_array('7',user_info()['ROLES'])||is_super_admin()){ ?>
            if(<?php echo user_info()['PK_USUARIO']?>!=data.FK_USUARIO)
            if((data.BANDEJA==1&&data.FK_ESTATUS_ASIGNACION==7)||(data.BANDEJA==1&&data.FK_ESTATUS_ASIGNACION==8)){
              html='<div class="col-lg-8 styled-select">'+
                          '<?= Html::label("Estatus de aprobaci&oacute;n",'',['class'=>'campos-label']) ?>'+
                          '<select '+((data.FK_ESTATUS_ASIGNACION==8)?'disabled':'')+' name="estatus_asignacion" class="estatus_asignacion form-control select-border-none" id="estatus_asignacion_'+key+'" data-pk_asignacion="'+key+'">'+
                              '<option value="7">Pendiente de aprobaci&oacute;n</option>'+
                              '<option value="8">Aprobado</option>'+
                              '<option value="9" '+((data.FK_ESTATUS_ASIGNACION==8)?'selected':'')+'>Rechazado</option>'+
                          '</select>'+
                      '</div>';
              col_10.append(html)
            }
            <?php } ?>
            //col_10.append(html);
            var a_update= '<a href="'+ url_documentos +'" title="Cargar" class="load-arrow icon-24x24"></a>';
            // col_2.append(a_update)
            // row.append(col_2);
            row.append(col_10);
            row.append('<div class="clear"></div>');
            caja.append(row);
            caja.append('<div class="clear"></div>');
        return caja;        
    }


$(function(){
  $('#cambio').bootstrapToggle();
  $('#cambio').change(function() {
      console.log('aeiou');
    })
    $('[rel="popover"]').popover({
        container: 'body',
        html: true,
        content: function () {
            var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
            return clone;
        }
    }).click(function(e) {
        e.preventDefault();
    });
    <?php if(in_array('7',user_info()['ROLES'])||is_super_admin()){ ?>
      $('.contenedor').on('change','.estatus_asignacion',function(){
        var elemento = $(this);
        var estatus = elemento.val();

        if (estatus==8) {
            $('#modal-aprobar').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });
            $('#cancelar_aprobado').data('pk_asignacion',elemento.data('pk_asignacion'))
            $('#aceptar_aprobado').data('pk_asignacion',elemento.data('pk_asignacion'))
        }else if(estatus==9){
            $('#modal-rechazado').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });
            $('#cancelar_rechazado').data('pk_asignacion',elemento.data('pk_asignacion'))
            $('#aceptar_rechazado').data('pk_asignacion',elemento.data('pk_asignacion'))
        }
      })
      $('#cancelar_aprobado').click(function(event) {
        $('#estatus_asignacion_'+$(this).data('pk_asignacion')).val('7')       
      });
      $('#cancelar_rechazado').click(function(event) {
        $('#estatus_asignacion_'+$(this).data('pk_asignacion')).val('7')       
      });

      $('#aceptar_aprobado').click(function(event) {
        var elemento = $(this);
        var estatus = elemento.val();
        $('#modal-aprobar').modal('hide');
        $('#modal-aprobar-exito').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });
        $('#cancelar_aprobado_exito').data('pk_asignacion',elemento.data('pk_asignacion'))
        $('#aceptar_aprobado_exito').data('pk_asignacion',elemento.data('pk_asignacion'))
      });

      // $('#cancelar_aprobado_exito').click(function(event) {
      //       var pk_asignacion=$(this).data('pk_asignacion');
      //       $.ajax({
      //         url: ajaxAprobarAsignacion,
      //         type: 'POST',
      //         dataType: 'JSON',
      //         data: {
      //           pk_asignacion:pk_asignacion,
      //           _csrf: _csrfToken,
      //       },
      //       })
      //       .done(function(data) {
      //           $('#modal-aprobar-exito').modal('hide');
      //           $('input[value="'+pk_asignacion+'"].filtro-asignacion').remove();
      //           buscar_registros();
      //           console.log(data);
      //       })
      //       .fail(function() {
      //         console.log("error");
      //       })
      //       .always(function() {
      //         console.log("complete");
      //       });
          
      // });

      // $('#aceptar_aprobado_exito').click(function(event) {
      //       var elemento = $(this).attr('href');
      //       $.ajax({
      //         url: ajaxAprobarAsignacion,
      //         type: 'POST',
      //         dataType: 'JSON',
      //         data: {
      //           pk_asignacion:$(this).data('pk_asignacion'),
      //           _csrf: _csrfToken,
      //       },
      //       })
      //       .done(function(data) {
      //           if(data.result==true){
      //               $('#modal-aprobar-exito').modal('hide');
      //               buscar_registros();
      //               window.location.href=elemento;
      //           }
                
      //       })
      //       .fail(function() {
      //         console.log("error");
      //       })
      //       .always(function() {
      //         console.log("complete");
                
      //       });
          
      // });

      $('#aceptar_rechazado').click(function(event) {
            var pk_asignacion=$(this).data('pk_asignacion');
            $.ajax({
              url: ajaxRechazarAsignacion,
              type: 'POST',
              dataType: 'JSON',
              data: {
                pk_asignacion:pk_asignacion,
                _csrf: _csrfToken,
              },
            })
            .done(function(data) {
              console.log(data);
              if(data.result==true){
                $('#modal-rechazado').modal('hide');
                $('input[value="'+pk_asignacion+'"].filtro-asignacion').remove();
                buscar_registros();
              }
            })
            .fail(function() {
              console.log("error");
            })
            .always(function() {
              console.log("complete");
            });
          
      });
    <?php } ?>

});

</script>