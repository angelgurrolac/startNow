<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;

use app\models\TblCatPuestos;
use app\models\TblCatEstatusRecursos;
use app\models\TblCatAdministradoras;
use app\models\TblCatUbicacionRazonSocial;
use app\models\TblCatUnidadesNegocio;
use app\models\TblCatUbicaciones;
use app\models\tblcatproyectos;
use app\models\tblclientes;
use app\models\tblcatcontactos;
use app\models\tblperfilempleados;

use yii\jui\DatePicker;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;

$userLog = $_SESSION;
$fkUser = $userLog['usuario']['FK_EMPLEADO'];

$query = tblperfilempleados::find()->where(['FK_EMPLEADO' => $fkUser])->limit(1)->one();
$unidadNegocioUser = $query->FK_UNIDAD_NEGOCIO;

$modelo_cargado='empleados';
$url_clientes      = \yii\helpers\Url::to(['site/clientes']);
$url_contactos        = \yii\helpers\Url::to(['asignaciones/contactos']);
$url_ubicaciones      = \yii\helpers\Url::to(['asignaciones/ubicaciones']);
$url_proyectos        = \yii\helpers\Url::to(['asignaciones/proyectos']);
$datosCatPuestos = ArrayHelper::map(TblCatPuestos::find()->orderBy(['DESC_PUESTO'=>SORT_ASC])->asArray()->all(), 'PK_PUESTO', 'DESC_PUESTO');
$datosCatEstatusRecursos = ArrayHelper::map(TblCatEstatusRecursos::find()->andWhere('PK_ESTATUS_RECURSO <> 6')->andWhere('PK_ESTATUS_RECURSO <> 101')->orderBy('DESC_ESTATUS_RECURSO')->asArray()->all(), 'PK_ESTATUS_RECURSO', 'DESC_ESTATUS_RECURSO');
$datosCatAdministradoras = ArrayHelper::map(TblCatAdministradoras::find()->orderBy(['NOMBRE_ADMINISTRADORA'=>SORT_ASC])->asArray()->all(), 'PK_ADMINISTRADORA', 'NOMBRE_ADMINISTRADORA');
$datosCatUbicaciones = ArrayHelper::map(TblCatUbicacionRazonSocial::find()->orderBy(['DESC_UBICACION'=>SORT_ASC])->asArray()->all(), 'PK_UBICACION_RAZON_SOCIAL', 'DESC_UBICACION');
$datosUnidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
$datosUbicacionFisica = ArrayHelper::map(TblCatUbicaciones::find()->where(['=','PROPIA_CLIENTE','Propia'])->asArray()->all(), 'PK_UBICACION', 'DESC_UBICACION');
$modelResponsableOP   = ArrayHelper::map($modelResponsableOP, 'PK_EMPLEADO', 'nombre_emp');
$datosClientes        = ArrayHelper::map(tblclientes::find()->orderBy('NOMBRE_CLIENTE')->asArray()->all(), 'PK_CLIENTE', 'NOMBRE_CLIENTE');
$datosClientes2        = ArrayHelper::map(tblclientes::find()->orderBy('NOMBRE_CLIENTE')->where(['>','HORAS_ASIGNACION',0])->asArray()->all(), 'PK_CLIENTE', 'NOMBRE_CLIENTE');
if(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!is_super_admin()){
    $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
    $datosUnidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO'=>$unidadesNegocioValidas])->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
} else {
    $datosUnidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
}
$this->title = ' Nuevos ingresos considerados para asignación';
$this->params['breadcrumbs'][] = $this->title;
?>
<script>
    var ajaxUrl =           '<?php echo Url::to(["$modelo_cargado/index_asignables"]); ?>';
    var ajaxActualiza = '<?php echo Url::to(["$modelo_cargado/actualiza_perfil"]); ?>';
    var _csrfToken =        '<?=Yii::$app->request->getCsrfToken()?>';
    var url_view =          '<?php echo Url::to(["$modelo_cargado/view",'id'=>'lorem']); ?>';
    var url_pendientes =    '<?php echo Url::to(["$modelo_cargado/create",'id'=>'lorem']); ?>';
    var url_update =        '<?php echo Url::to(["$modelo_cargado/update",'id'=>'lorem']); ?>';
    var url_delete =        '<?php echo Url::to(["$modelo_cargado/delete",'id'=>'lorem']); ?>';
    var historial = 'empleados/pendientes_cvoriginal';
    var ajaxUrlClientes = '<?php echo Url::to(["asignaciones/prec"]); ?>';
    var ajaxObtenerEmpleadosACargo = '<?php echo Url::to(["asignaciones/obtener_empleados_a_cargo"]); ?>';
    var ajaxUrlFechas = '<?php echo Url::to(["asignaciones/getallfechas"]); ?>';
    var existe_tarifa =  false;
    var arr_tarifas_cte;
    var options_select_tarifa;
    if(!localStorage.getItem(historial)){
        localStorage.setItem(historial, JSON.stringify([]));
    }
</script>

<style type="text/css">
.datepicker{
    z-index: 99999 !important;
}
.centerFilters{
    display: inline-block;
    width: 100%;
    padding:0px 0px 0px 30px;
}
.centerFechaIngreso{
    display: inline-block;
    padding:0px 0px 0px 0px;
    margin: 0;
}
[class*="icon-"] {
    font-family: 'Glyphicons Halflings';
    font-style: normal;
    font-size: 1.1em;
    speak: none;
}
#ValidaAsignacion.modal-body{
  height: 500px;
overflow-y: auto;
}

</style>

<div class="row">
    <h1 class="title col-lg-12"><a href="<?php echo Url::to(['empleados/index']); ?>"  class="return-arrow icon-12x21"></a><b><?= Html::encode($this->title) ?></b>
        <?php if(valida_permisos(['empleados/create'])){ ?>
       <!-- <//?= Html::a('Dar de Alta Empleado', ['create'], ['class' => 'btn btn-success der']) ?> -->
        <?php } ?>
    </h1>
    <div class="clear"></div>

    <div class="col-lg-12">
        <div class="resultados">
            <div class="campos">
                <h3 class="campos-title font-bold">B&uacute;squeda <a href="javascript:void(0)" class="arrow-event item-up-arrow icon-12x21 der"></a></h3>
                <?= Html::beginForm(Url::to(["$modelo_cargado/pendientes_cvoriginal"]),'post',['class' => 'form', 'id'=>'form-ajax','style'=>'display:none','data-history'=>'true']); ?>
                    <div class="row">
                        <!-- <div class="form-group col-lg-10 col-md-4 col-sm-4"> -->
                            <div class="row centerFilters">
                                <div class="form-group col-lg-2 col-md-4 col-sm-4">
                                    <?= Html::label('Nombre','search',['class'=>'campos-label']) ?>
                                    <?= Html::input('text','nombre',null,['id'=>'search','class'=>'search form-control']) ?>
                                </div>
                                <div class="form-group col-lg-2 col-md-4 col-sm-4">
                                    <?= Html::label('Apellido Paterno','search',['class'=>'campos-label']) ?>
                                    <?= Html::input('text','aPaterno',null,['id'=>'search','class'=>'search form-control']) ?>
                                </div>
                                <div class="form-group col-lg-2 col-md-4 col-sm-4 styled-select">
                                    <?= Html::label('Ubicacion Física','search',['class'=>'campos-label']) ?>
                                    <?= Html::dropDownList ('idUbicacionFisica',null,$datosUbicacionFisica,['id'=>'idUbicacionFisica','class' => 'giro form-control','prompt'=>'TODOS']) ?>
                                </div>
                                <div class="form-group col-lg-2 col-md-4 col-sm-4 styled-select">
                                    <?= Html::label('Puesto','search',['class'=>'campos-label']) ?>
                                    <?= Html::dropDownList ('idPuesto',null,$datosCatPuestos,['id'=>'idPuesto','class' => 'giro form-control','prompt'=>'TODOS']) ?>
                                </div>
                                <div class="form-group col-lg-2 col-md-4 col-sm-4 styled-select">
                                    <?= Html::label('Ubicación Administrativa','search',['class'=>'campos-label']) ?>
                                    <?= Html::dropDownList ('idUbicacion',null,$datosCatUbicaciones,['id'=>'idUbicacion','class' => 'giro form-control','prompt'=>'TODOS']) ?>
                                </div>
                                <div class="form-group col-lg-2 col-md-4 col-sm-4 styled-select">
                                    <?= Html::label('Administradora','search',['class'=>'campos-label']) ?>
                                    <?= Html::dropDownList ('administradora',null,$datosCatAdministradoras,['id'=>'administradora','class' => 'giro form-control','prompt'=>'TODOS']) ?>
                                </div>
                            </div><!-- row -->
                            <div class="row centerFilters">
                                <div class="form-group col-lg-2 col-md-4 col-sm-4 styled-select" >
                                    <?= Html::label('Estatus de Empleado','search',['class'=>'campos-label']) ?>
                                    <?= Html::dropDownList ('estatusEmpleado',null,$datosCatEstatusRecursos,['id'=>'estatusEmpleado','class' => 'giro form-control','prompt'=>'TODOS']) ?>
                                </div>
                                <?php
                                if(
                                    (isset(user_info()['IS_ADMIN_UNIDAD_NEGOCIO'])&&!empty(user_info()['IS_ADMIN_UNIDAD_NEGOCIO']))
                                    ||
                                    (isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO']))
                                    ||is_super_admin())
                                {
                                    ?>
                                <div class="form-group col-lg-2 col-md-4 col-sm-4 styled-select" >
                                    <?= Html::label('Unidad de Negocio','search',['class'=>'campos-label']) ?>
                                    <?= Html::dropDownList ('unidadNegocio',null,$datosUnidadNegocio,['id'=>'unidadNegocio','class' => 'giro form-control','prompt'=>'TODOS']) ?>
                                </div>
                                <?php   } ?>
                                <div class="form-group col-lg-4 col-md-12 col-sm-12 centerFechaIngreso">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <?= Html::label('Rango de Fecha Ingreso','search',['class'=>'campos-label']) ?>
                                    </div>
                                    <!-- <div class="form-group col-lg-1 col-md-1 col-sm-1">
                                        <//?= Html::label('De','search',['class'=>'campos-label']) ?>
                                    </div> -->
                                    <div class="form-group col-lg-6 col-md-5 col-sm-5">
                                        <?=
                                            DatePicker::widget([
                                                'name'  => 'ingresoFechaIni',
                                                'options'=> ['class'=>'datepicker form-control datepicker-upa giro', 'placeholder'=>"DD/MM/AAAA"],
                                            ]);
                                        ?>
                                    </div>


                                    <!-- <div class="form-group col-lg-1 col-md-1 col-sm-1">
                                        <//?= Html::label('al','search',['class'=>'campos-label']) ?>
                                    </div> -->
                                    <div class="form-group col-lg-6 col-md-5 col-sm-5">
                                        <?=
                                            DatePicker::widget([
                                                'name'  => 'ingresoFechaFin',
                                                'options'=> ['class'=>'datepicker form-control datepicker-upa giro', 'placeholder'=>"DD/MM/AAAA"],
                                            ]);
                                        ?>
                                    </div>

                                </div><!-- row -->
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <?= Html::submitButton('BUSCAR',['class'=>'btn btn-success btn-buscar der']) ?>
                                <?= Html::resetButton('LIMPIAR', ['class' => 'btn btn-success reset btn-buscar der', 'onclick'=>'javascript:buscar_registros()']) ?>
                                <div class="form-group text-center der">
                                    <div class="pagination-sm" style="margin:0 15px;" id="top-pg"></div><p class="paginas text-center"></p>
                                </div>
                            </div>
                    </div>
                    <div class="clear"></div>

                <?= Html::endForm(); ?>
            </div>
            <div class="clear"></div>
            <div class="contenedor-parent row">
                <div class="contenedor empleados">
                    <div class="clear"></div>
                </div>
                <div class="form-group col-lg-3 der">
                    <form id="form-excel" name="form-excel" action="../../views/empleados/excel_index.php" method="post" target="_blank">
                        <input type="hidden" id="excel-nombre" name="excel-nombre" value="">
                        <input type="hidden" id="excel-aPaterno" name="excel-aPaterno" value="">
                        <input type="hidden" id="excel-idPuesto" name="excel-idPuesto" value="">
                        <input type="hidden" id="excel-idUbicacion" name="excel-idUbicacion" value="">
                        <input type="hidden" id="excel-administradora" name="excel-administradora" value="">
                        <input type="hidden" id="excel-ingresoFecha" name="excel-ingresoFecha" value="">
                        <input type="hidden" id="excel-estatusEmpleado" name="excel-estatusEmpleado" value="">
                        <input type="hidden" id="excel-unidadNegocio" name="excel-unidadNegocio" value="">
                        <input type="hidden" id="excel-estatusEmpleado" name="excel-estatusEmpleado" value="">
                        <input type="hidden" id="excel-idUbicacionFisica" name="excel-idUbicacionFisica" value="">
                        <button id="exportar-excel" class="btn btn-success btn-buscar der habilitado" onclick="llamarExcel();">Exportar a Excel</button>
                    </form>
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
    </div> <!-- FIN de class="col-lg-12" -->

</div> <!-- FIN de class="row" -->

<div class="modal fade" id="modal-insert" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <p style="text-align: center; font-weight: bold; padding: 20px 10px;" >
                        Los datos se han guardado correctamente</br>
                    </p>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-success" data-dismiss="modal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ValidaAsignacion" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p id="mensaje" style="text-align: center; font-weight: bold; display:block;">
                    ¿Desea dar de alta la<br>
                    asignación del recurso?
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" id="hacerAsignacion" class="btn btn-success" data-toggle="modal" data-dismiss="modal" data-target="#AltaAsignacion" value="">Si</button>
                    <button type="button" id="btnNo" class="btn btn-success" data-dismiss="modal" value="">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="AltaAsignacion" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
          <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
          <div class="modal-header">
            <div class="">
              <h1 class="title" style="text-align:  center;">Alta de asignación</h1>
            </div>
          </div>
            <div class="modal-body" style="padding: 0px;">
                <div class="col-lg-12">
                  <?php $form = ActiveForm::begin(['id'=>'formAltaAsignaciones']); ?>
                    <?= Html::hiddeninput('FkEmpleado')?>
                    <div class="row">
                      <?= $form->field($modelAsignaciones, 'FK_CLIENTE',
                      [
                          'template' => ' <div class="col-lg-4">{label}<label><font color="red">*</font></label>{input}{error}{hint}<div class="clear"></div></div>',
                      ]
                      )->widget(Select2::classname(), [
                      'data' => $datosClientes2,
                      'options' => ['placeholder' => ''],
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
                      ]); ?>
                      <input type="hidden" id="PRECde">
                      <input type="hidden"id="PRECa">
                      <input type="hidden"id="HORA_CLIENTE">

                      <?= $form->field($modelAsignaciones, 'FK_CONTACTO',
                      [
                          'template' => ' <div class="col-lg-4">{label}<label><font color="red">*</font></label>{input}{error}{hint}<div class="clear"></div></div>',
                      ]
                      )->widget(Select2::classname(), [
                      'disabled' => true,
                      'options' => ['placeholder' => ''],
                      'pluginOptions' => [
                          'allowClear' => true,
                          'language' => [
                            "noResults"=> new JsExpression('function () { return "Es requerido registrar un contacto para el cliente"; }')
                          ],
                          'ajax' => [
                              'url' => $url_contactos,
                              'dataType' => 'json',
                              'delay' => 250,
                              'data' => new JsExpression('function(params) { return {q:params.term,p:$("#tblasignaciones-fk_cliente").val()}; }')
                          ],
                          'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                          'templateResult' => new JsExpression('function(data) { return data.text; }'),
                          'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                      ],
                      ]); ?>

                      <?= $form->field($modelAsignaciones, 'FK_UBICACION',
                      [
                        'template' => ' <div class="col-lg-4">{label}<label><font color="red">*</font></label>{input}{error}{hint}<div class="clear"></div></div>',
                      ]
                      )->widget(Select2::classname(), [
                      'disabled' => true,
                      'options' => ['placeholder' => ''],
                      'pluginOptions' => [
                          'allowClear' => true,
                          'ajax' => [
                              'url' => $url_ubicaciones,
                              'dataType' => 'json',
                              'delay' => 250,
                              'data' => new JsExpression('function(params) { return {p:$("#tblasignaciones-fk_cliente").val()}; }')
                          ],
                          'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                          'templateResult' => new JsExpression('function(data) { return data.text; }'),
                          'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                      ],
                      ]); ?>

                    </div>

                    <div class="row">
                      <?= $form->field($modelAsignaciones, 'NOMBRE',
                      [
                          'template' => ' <div class="col-lg-6">{label}<label><font color="red">*</font></label>{input}{error}{hint}<div class="clear"></div></div>',
                      ]
                      )->textInput(['maxlength' => true]) ?>

                      <?= $form->field($modelAsignaciones, 'FK_RESPONSABLE_OP',
                      [
                          'template' => ' <div class="col-lg-6">{label}<label><font color="red">*</font></label>{input}{error}{hint}<div class="clear"></div></div>',
                      ]
                      )->widget(Select2::classname(), [
                      'data' => $modelResponsableOP,
                      'options' => ['placeholder' => ''],
                      'pluginOptions' => [
                          'allowClear' => true,
                      ],
                      ])->label('Responsable de asignación'); ?>
                    </div>

                    <div class="row">

                      <?= $form->field($modelAsignaciones, 'FK_UNIDAD_NEGOCIO',
                      [
                          'template' => ' <div class="col-lg-4">{label}<label><font color="red">*</font></label>{input}{error}{hint}<div class="clear"></div></div>',
                      ]
                      )->widget(Select2::classname(), [
                      'data' => $datosUnidadNegocio,
                      'options' => ['placeholder' => ''],
                      'pluginOptions' => [
                          'allowClear' => true,
                          'language' => [
                            "noResults"=> new JsExpression('function () { return "Es requerido registrar la unidad de negocio"; }')
                          ]
                      ],
                      ]); ?>

                      <?= $form->field($modelAsignaciones, 'FECHA_INI',
                      [
                          'template' => ' <div class="col-lg-4">{label}<label><font color="red">*</font></label>{input}{error}{hint}<div class="clear"></div></div>',
                      ]
                      )->widget(\yii\jui\DatePicker::classname(),
                      [])->textInput([
                      'maxlength' => true,
                      'class' => 'form-control datepicker datepicker-upa FechaInicio',
                      'placeholder'=>'DD/MM/AAAA'])->label('Inicio');
                      ?>

                      <?= $form->field($modelAsignaciones, 'FECHA_FIN',
                      [
                          'template' => ' <div class="col-lg-4">{label}<label><font color="red">*</font></label>{input}<input type="checkbox" id="indefinido"/>Indefinida{error}{hint}<div class="clear"></div></div>',
                      ]
                      )->widget(\yii\jui\DatePicker::classname(),
                      [])->textInput([
                      'maxlength' => true,
                      'class' => 'form-control datepicker datepicker-upa FechaFin',
                      'placeholder'=>'DD/MM/AAAA'])->label('Fin');
                      ?>

                    </div>

                    <div class="row calculos">

                      <input type="hidden" id="cant_personas_a_cargo">
                      <input type="hidden" id="tblasignaciones-tarifa">

                      <div class="form-group col-lg-4">
                        <?= Html::label('Tarifa Hora','search',['class'=>'campos-label']) ?>
                        <div class="tarifas"></div>
                        <div class="help-block"></div>
                        <div class="clear"></div>
                      </div>


                    </div>

                </div>
            </div>
            <div class="modal-footer">
              <?= Html::submitButton('<span class="glyphicon glyphicon-ban-circle"></span> <span class="nombreBoton">Guardar</span>',['class'=>'btn btn-success der', 'id'=>'botonGuardar', 'onclick' => 'validaFechaFin();']) ?>
              <?= Html::button('<span class="glyphicon glyphicon-floppy-disk"></span> <span class="nombreBoton">Cancelar</span>',['class'=>'btn btn-success der', 'id'=>'botonCancelar', 'data-dismiss'=>'modal']) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<iframe  id = "iframe"  style = " display : none ; " ></iframe>

<style>
.resultados .empleados .contenedor-item {
    min-height: 168px;
}
.resultados .contenedor .contenedor-item p {
    height: auto;
}
.resultados .contenedor .contenedor-item:hover .cv_original {
    padding: 15px;
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
    height: auto;
}
.margin-card{
    margin:5px 0;
}
a:hover { cursor: pointer; }
</style>

<script>


    $(document).ready(function(){

        $('body').on('click', '#validarAsignacion', function(){
            var empleado = $(this).attr('value');
            var tarifa = parseFloat($(this).attr('data-tarifa'));

            $('#tblasignaciones-tarifa').val(tarifa);
             $('input[name="FkEmpleado"]').val(empleado);
            $('#btnNo').val($(this).attr('value'));
          });

        $('#btnNo').on("click",function(e) {
          var idRecurso = $(this).val();

          $.ajax({
            url: ajaxActualiza,
            type: 'post',
            data: {
              idRecurso : idRecurso,
              _csrf  : _csrfToken
            },
            success: function(data){
                //alert(idRecurso);
              console.log(data);
            },
            error: function(exception){
                //alert('entra');
              console.log('error' + exception);
            }
          });

        });

        $('#botonCancelar').on("click",function() {

          $('#tblasignaciones-nombre').val('');
          $('#tblasignaciones-fecha_ini').val('');
          $('#tblasignaciones-fecha_fin').val('');
          $('.select2-selection__rendered').text('');

        });


        $('#indefinido').on('change', function(){
          if ( $('.field-tblasignaciones-fecha_fin #indefinido').is(':checked') ) {
            $("#tblasignaciones-fecha_fin").prop('disabled',true);
            $("#tblasignaciones-fecha_fin").val('');

            $(".field-tblasignaciones-fecha_fin").removeClass('has-error').find('.help-block').html('');
            $(".field-tblasignaciones-fecha_fin").addClass('has-success');
          } else {
            $("#tblasignaciones-fecha_fin").prop('disabled',false);

          }
        });


      $('#formAltaAsignaciones .datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayBtn: true,
            todayHighlight: true
        }).on('changeDate', function(ev) {
          validar_fechas($(this));
        });

        $('.calculos input').prop('disabled', true).css({'opacity': '0.3'});
        $('.calculos input').prop('disabled', true).css({'opacity': '0.3'});

        $('#tblasignaciones-fk_cliente').change(function() {
              var cliente = $(this).val();

            if( cliente != '' ){
                $('.calculos input').prop('disabled', null).css({'opacity': '1'});
                $('.calculos input').prop('disabled', null).css({'opacity': '1'});
            } else {
                $('.calculos input').prop('disabled', true).css({'opacity': '0.3'});
                $('.calculos input').prop('disabled', true).css({'opacity': '0.3'});
            }

            var tarifa = $('#tblasignaciones-tarifa').val();
            $('input.tarifa-hora').val(tarifa);
        });

        $("#divNuevosJefes").on('change','.select_nuevo_jefe',function(){
          var contadorFaltantes = 0;
          $(".select_nuevo_jefe").each(function (index){
              if($(this).val()==""){
                  contadorFaltantes++;
              }
          });
          if(contadorFaltantes > 0){
              $("#botonEnviar2").prop('disabled',true);
          } else {
              $("#botonEnviar2").prop('disabled',false);
          }
        });

        $("#botonEnviar2").on("click",function(){
          var jefe = [];
          var formData = new FormData($('#divNuevosJefes')[0]);

          $('.asignacion-jefe').each(function(i) {
            console.log($(this).val());
            formData.append('emp_reasignar[]', $(this).val());
            jefe[i];
          });

          $('.select_nuevo_jefe').each(function(i) {
            formData.append('emp_reasignar[jefe][]', $(this).val());

          });

          $.ajax({
              url: ajaxUrl,
              cache: false,
              contentType: false,
              processData: false,
              data: formData,
              type:'post',
              success:function(data){
                  console.log(data);
              }

          });

          $('#AltaAsignacion').modal('show');
          $('#empleados-cargo').modal('hide');
        });

    $('#tblasignaciones-monto').on('change', function(e) {
      var monto = number_format($('#tblasignaciones-monto').val(),2);
      $('#tblasignaciones-monto').val(monto);
    });


    //Tarifas cliente
    $("#AltaAsignacion").on('shown.bs.modal', function () {
      var empleado = $('input[name="FkEmpleado"]').val();
      var tarifa = $('#tblasignaciones-tarifa').val();
      var Tarifas = "";
      if(existe_tarifa == false){
        Tarifas +=  '<input type="text" data-idemp="'+empleado+'" class="numeric form-control required tarifa-hora valida_cliente" name="tarifa['+empleado+']" value="'+tarifa+'">'+
      '<div class="help-block"></div>';
      }
      else {
        Tarifas +=  '<select name="tarifa['+empleado+']" data-idemp='+empleado+' id="select_pk_cat_tarifa_'+empleado+'" class="numeric form-control required tarifa-hora valida_cliente select_pk_cat_tarifa">'+
            options_select_tarifa+
          '</select>'+
          '<input type="hidden" class="pk_cat_tarifa" name="pk_cat_tarifa_select['+empleado+']" value="" id="pk_cat_tarifa_select_'+empleado+'">'+
          '<div class="help-block"></div>';
        }
        $('.tarifas').html(Tarifas);
    });

    $('#AltaAsignacion').on('change , blur','.select_pk_cat_tarifa',function(){
        $('#pk_cat_tarifa_select_'+$(this).data('idemp')).val($(this).find('option:selected').data('pk_cat_tarifa'))
    })

  });

    function validaFechaFin(){

        var post1 = true;

        if($("#tblasignaciones-fecha_fin").val()  == ''){

            $(".field-tblasignaciones-fecha_fin").addClass('has-error').find('.help-block').html('La fecha fin no puede estar vacía.');
            post1 = false;
        }else{
            post1 = true;
        }

        return post1;
    }

    function habilitarCamposRecursosAsignados(){
      $('input[class*="fecha_ini"]').removeAttr('readonly');
      $('input[class*="fecha_fin"]').removeAttr('readonly');
      $('input[class*="tarifa-hora"]').prop('readonly', false);
    }

    $(function() {
        setTimeout(function(){
        $('.datepicker').datepicker('remove');

        $('.datepicker-upa').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayBtn: true,
                    todayHighlight: true
                })
       }, 500);
    });
    function crear_elemento(data){
        mensaje= 'No existen elementos que cuenten con los criterios especificados para los parámetros de búsqueda';

        divLinkValidar = '';

        datoU = <?= json_encode($unidadNegocioUser,JSON_HEX_QUOT |
                        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
                    ) ?>;

        if(data.PK_UNIDAD_NEGOCIO == datoU){

            divLinkValidar = '<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 margin-card">'+
                                        '<a id="validarAsignacion" data-toggle="modal" data-target="#ValidaAsignacion"'+' data-tarifa='+data.TARIFA+' value="'+data.PK_EMPLEADO+'">'+
                                        '<p class="text-primary">Validar</p>'+
                                        '</a>'+
                                    '</div>';
        }

        divFechaBaja = '';
        if (data.FK_ESTATUS_RECURSO=='4'||data.FK_ESTATUS_RECURSO=='6') {
            divFechaBaja = '<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12 margin-card">'+
                                '<label class="campos-label">Fecha de Baja</label>'+
                                '<p class="font-regular"> '+data.FECHA_BAJA+' </p>'+
                            '</div>';
        };

        divEstatus = '';

        if(data.FK_ESTATUS_RECURSO == 101){
            divEstatus = '<div class="estatus-item estatus-empleado-3">Disponible</div>';
        }else{
            divEstatus = '<div class="estatus-item estatus-empleado-'+data.FK_ESTATUS_RECURSO+'">'+data.DESC_ESTATUS_RECURSO+'</div>';
        }

        var html='<div class="contenedor-item col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                        '<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12" >'+
                            '<a href="<?php echo Url::to(['empleados/view']) ?>?id='+data.PK_EMPLEADO+'">'+
                            '<div style="background:url(<?php echo get_upload_url() ?>'+((data.FOTO_EMP!='defoult'&&data.FOTO_EMP!=''&&data.FOTO_EMP!=null)?data.FOTO_EMP:'/uploads/EmpleadosFotos/sin_perfil.png')+');height: 100px;width: 100px;background-size: cover;background-position: 50% 0%;background-repeat: no-repeat; margin: 10px auto; border: 1px solid #ccc;"></div>'+
                            '</a>'+
                            '<div class="col-lg-12 text-center">'+
                                '<div class="col-lg-6">'+
                                    '<a href="javascript:void(0)"  style="border: solid 1px #ccc; padding:5px; margin-bottom:5px; '+((data.CV_ORIGINAL!='')?'color:blue!important':'')+
                                    '" data-url="'+data.CV_ORIGINAL+'" class="font-bold cv_down">CV ORIGINAL </a>'+
                                '</div>'+
                                '<div class="col-lg-6">'+
                                    '<a href="javascript:void(0)" style="border: solid 1px #ccc; padding:5px; margin-bottom:5px; '+((data.CV_EISEI!='')?'color:blue!important':'')+
                                    '"  data-url="'+data.CV_EISEI+'" class="font-bold cv_down">CV EISEI </a>'+
                                '</div>'+
                               '<div class="col-lg-6" >'+
                                    '<a href="../../views/empleados/template.php?id='+data.PK_EMPLEADO+'" style="border: solid 1px #ccc; padding:5px; margin-bottom:5px;"  class="font-bold cv_down">FORMATO</a>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12" style="margin-top:5px">'+
                            '<div class="form-group font-bold">'+
                                '<label class="campos-label">'+data.DESC_UBICACION+'</label>'+
                                '<a href="<?php echo Url::to(['empleados/view']) ?>?id='+data.PK_EMPLEADO+'">'+
                                '<p class="razon_social">'+data.NOMBRE_EMP+' '+data.APELLIDO_PAT_EMP+' '+data.APELLIDO_MAT_EMP+'</p>'+
                                '</a>'+
                                '<div class="row">'+
                                    '<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Puesto</label>'+
                                        '<p class="font-regular"> '+data.DESC_PUESTO+' </p>'+
                                    '</div>'+
                                    '<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Unidad de Negocio</label>'+
                                        '<p class="font-regular"> '+data.DESC_UNIDAD_NEGOCIO+' </p>'+
                                    '</div>'+
                                    '<div class="clearfix visible-md"></div>'+
                                    '<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Ubicacion Física</label>'+
                                        '<p class="font-regular"> '+data.DESC_UBICACION_FISICA+' </p>'+
                                    '</div>'+
                                    '<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Fecha de ingreso</label>'+
                                        '<p class="font-regular"> '+data.FECHA_INGRESO+' </p>'+
                                    '</div>'+
                                    '<div class="clearfix visible-lg"></div>'+
                                    '<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Años</label>'+
                                        '<p class="font-regular"> '+data.ANIOS+' </p>'+
                                    '</div>'+
                                    '<div class="col-lg-4 col-md-3 col-sm-12 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Fecha nacimiento</label>'+
                                        '<p class="font-regular"> '+data.FECHA_NAC_EMP+' </p>'+
                                    '</div>'+
                                    '<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 margin-card">'+
                                        '<label class="campos-label">Celular</label>'+
                                        '<p class="font-regular"> '+data.CELULAR+' </p>'+
                                    '</div>'+
                                    divLinkValidar+
                                    divFechaBaja+
                                '</div>'+
                             '</div>'+
                        '</div>'+
                    divEstatus+
                '</div>';
        return html;
    }

    function validarJefes(){
      cantPersonasCargo = 0;
      empleados = '';
        var cant_personas_a_cargo = parseFloat($('#cant_personas_a_cargo').val());
        console.log(cant_personas_a_cargo);
        var idEmp = $('input[name="FkEmpleado"]').val();
        cantPersonasCargo+= cant_personas_a_cargo;
        empleados += idEmp+',';

      if(cantPersonasCargo > 0){
        $.ajax({
          url: ajaxObtenerEmpleadosACargo,
          type: 'post',
          data: {
            _csrf  : _csrfToken,
            idEmpleado : empleados,
          },
          success: function (data) {
            htmlJefesOptions = '<option value=""></option>';
            htmlDivs = '';
            $.each(data.jefesDirectos,function(index,contenido){
              htmlJefesOptions+='<option value="'+contenido.PK_EMPLEADO+'">'+contenido.NOMBRE_EMP+'</option>';
            });
            $.each(data.empleadoReasignar,function(index,contenido){
              nombreEmp = contenido.NOMBRE_EMP+' '+contenido.APELLIDO_PAT_EMP+' '+contenido.APELLIDO_MAT_EMP;
              htmlDivs+=
                "<div class='row empleadoNum_"+contenido.FK_JEFE_DIRECTO+"'>"+
                  "<div class='col-xs-12 col-sm-12 col-md-6 col-lg-6'>"+
                    "<p style='font-weight: bold;'>"+
                      nombreEmp+
                    "</p>"+
                  "</div>"+
                  "<div class='col-xs-12 col-sm-12 col-md-6 col-lg-6'>"+
                    "<input type='hidden' id='personal' class='asignacion-jefe' value="+contenido.PK_EMPLEADO+">"+
                    "<select name='emp_reasignar["+contenido.PK_EMPLEADO+"]' class='select_nuevo_jefe form-control'>"+
                      htmlJefesOptions+
                    "</select>"+
                    "<div class='help-block'></div>"+
                  "</div>"+
                "</div>";
            });
            $("#divNuevosJefes").html(htmlDivs);
          }, error: function(error) {
            console.log(error);
          }
        });
        $('#guardar-asignacion').modal('hide');
        jQuery('#empleados-cargo').modal({
          show: true,
          keyboard: false,
          backdrop: 'static'
        })
        $("#botonEnviar2").prop('disabled',true);
      } else {
        $('#AltaAsignacion').modal('show');
        $('#guardar-asignacion').modal('hide');
      }
    }

    function validar_fechas(element){

      if(element.val()==''){
        element.parent().addClass('has-error').find('.help-block').html('Este campo no puede estar vacio');
        return false;
      }
      if(element.hasClass('FechaFin')){
        if( (new Date(reverse_date_ddmmYY(element.val())).getTime() <= new Date(reverse_date_ddmmYY($('.FechaInicio').val())).getTime()))
        {
            element.parent().addClass('has-error').find('.help-block').html('La Fecha Fin debe ser mayor a la Fecha Inicial, Favor de Verificar');
            return false;
        }else{
            element.parent().removeClass('has-error').find('.help-block').html('')
            //validar_fechas_calculo_horas(element);
            return true;
        }
      }else if(element.hasClass('FechaInicio')){
        if( (new Date(reverse_date_ddmmYY(element.val())).getTime() >= new Date(reverse_date_ddmmYY($('.FechaFin').val())).getTime()))
        {
            element.parent().addClass('has-error').find('.help-block').html('La Fecha inicio debe ser menor a la Fecha Final, Favor de Verificar');
            return false;
        }else{
            element.parent().removeClass('has-error').find('.help-block').html('')
            //validar_fechas_calculo_horas(element);
            return true;
        }

      }

    }

    function get_month_days(mes,anio) {
      var meses = ['01', '03', '05', '07', '08', '10', '12'];
      if(jQuery.inArray(mes,meses) !== -1){
        dias = 31;
      } else if(mes=='02'){
        if(parseInt(anio)%4==0){
          dias = 29
        } else {
          dias = 28
        }
      } else {
        dias = 30
      }
      return dias;
    }

    function llamarExcel(){
        $('#form-excel input').remove();
        $('#form-ajax input, #form-ajax select').each(function(index, el) {
            if($(el).attr('name')!='_csrf'){
                console.log($(el).val());
                $('#form-excel').append('<input  type= "hidden" name="'+$(el).attr('name')+'" value="'+$(el).val()+'"/>');
            }
        });
        return false;
    }
</script>
<?php if(isset($_GET['action'])){
        if($_GET['action']=='insert'){
            ?>
            <script>
                jQuery(document).ready(function($) {
                    jQuery('#modal-insert').modal();
                });
            </script>
            <?php
        }
} ?>
