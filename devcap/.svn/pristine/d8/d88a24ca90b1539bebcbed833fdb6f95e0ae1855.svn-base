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
use app\models\TblAsignaciones;
use yii\jui\DatePicker;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$idA = '';

if(isset($_GET['idAsignacion'])){
  $idA = $_GET['idAsignacion'];
}

$pkEmp_user = user_info()['FK_EMPLEADO'];

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
$datosUbicacionFisica = ArrayHelper::map(TblCatUbicaciones::find()->where(['=','PROPIA_CLIENTE','PROPIA'])->orderBy(['DESC_UBICACION'=>SORT_ASC])->asArray()->all(), 'PK_UBICACION', 'DESC_UBICACION');
$modelResponsableOP   = ArrayHelper::map($modelResponsableOP, 'PK_EMPLEADO', 'nombre_emp');
$datosClientes        = ArrayHelper::map(tblclientes::find()->orderBy('NOMBRE_CLIENTE')->asArray()->all(), 'PK_CLIENTE', 'NOMBRE_CLIENTE');
$datosClientes2        = ArrayHelper::map(tblclientes::find()->orderBy('NOMBRE_CLIENTE')->where(['>','HORAS_ASIGNACION',0])->asArray()->all(), 'PK_CLIENTE', 'NOMBRE_CLIENTE');
$unidadNegocio = ArrayHelper::getValue($unidadNegocioUsuario, 'FK_UNIDAD_NEGOCIO');

if($unidadNegocio == 3){
    $datosUnidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO'=>3])->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
} else {
    $datosUnidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
}
$this->title = 'Consulta General de Recurso';
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
    var ajaxUrl = '<?php echo Url::to(["$modelo_cargado/index"]); ?>';
    var ajaxModal = '<?php echo Url::to(["$modelo_cargado/modal"]); ?>';
    var ajaxRemplazo = '<?php echo Url::to(["asignaciones/update"]); ?>';
    var _csrfToken = '<?=Yii::$app->request->getCsrfToken()?>';
    var url_view = '<?php echo Url::to(["$modelo_cargado/view",'id'=>'lorem']); ?>';
    var url_update = '<?php echo Url::to(["$modelo_cargado/update",'id'=>'lorem']); ?>';
    var url_delete = '<?php echo Url::to(["$modelo_cargado/delete",'id'=>'lorem']); ?>';
    var ajaxUrlClientes = '<?php echo Url::to(["asignaciones/prec"]); ?>';
    var ajaxObtenerEmpleadosACargo = '<?php echo Url::to(["asignaciones/obtener_empleados_a_cargo"]); ?>';
    var ajaxUrlFechas = '<?php echo Url::to(["asignaciones/getallfechas"]); ?>';
    var historial = 'empleados/index';
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
.enlace-asignar{
  color: blue!important;
  text-decoration: underline !important;
}
.enlace-remplazar{
  color: blue!important;
  text-decoration: underline !important;
}
[class*="icon-"] {
    font-family: 'Glyphicons Halflings';
    font-style: normal;
    font-size: 1.1em;
    speak: none;
}
#empleados-cargo .modal-body{
  height: 300px;
overflow-y: auto;
}

#modalRemplazarRecurso .modal-body{
    width: 698px;
    overflow-y: auto;
}

#modalRemplazarRecurso .modal-content{
    width: 700px;
    height: 500px;
}
</style>
<div class="row">
    <h1 class="title col-lg-12"><b><?= Html::encode($this->title) ?></b>
        <?php if(valida_permisos(['empleados/create'])){ ?>
       <!-- <?= Html::a('Dar de Alta Empleado', ['create'], ['class' => 'btn btn-success der']) ?> -->
        <?php } ?>
    </h1>
    <div class="clear"></div>
    <div class="col-lg-12">
        <div class="resultados">
            <div class="campos">
                <h3 class="campos-title font-bold">B&uacute;squeda <a href="javascript:void(0)" class="arrow-event item-up-arrow icon-12x21 der"></a></h3>
                <?= Html::beginForm(Url::to(["$modelo_cargado/index"]),'post',['class' => 'form', 'id'=>'form-ajax','style'=>'display:none','data-history'=>'true']); ?>
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
                                    <?= Html::label('Ubicación Física','search',['class'=>'campos-label']) ?>
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
                                    <?= Html::dropDownList ('unidadNegocio',$unidadNegocio,$datosUnidadNegocio,['id'=>'unidadNegocio','class' => 'giro form-control','prompt'=>'TODOS']) ?>
                                </div>
                                <?php   } ?>

                                <div class="form-group col-lg-4 col-md-12 col-sm-12 centerFechaIngreso">
                                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                        <?= Html::label('Fecha Ingreso Inicio','search',['class'=>'campos-label']) ?>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                        <?= Html::label('Fecha Ingreso Fin','search',['class'=>'campos-label']) ?>
                                    </div>

                                    <!-- <div class="col-lg-1 col-md-1 col-sm-1 segundoPlano">
                                        <//?= Html::label('De','search',['class'=>'campos-label']) ?>
                                    </div> -->
                                    <div class="form-group col-lg-6 col-md-4 col-sm-6">
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
                                    <div class="form-group col-lg-6 col-md-4 col-sm-6">
                                        <?=
                                            DatePicker::widget([
                                                'name'  => 'ingresoFechaFin',
                                                'options'=> ['class'=>'datepicker form-control datepicker-upa giro', 'placeholder'=>"DD/MM/AAAA"],
                                            ]);

                                        ?>
                                    </div>

                                </div><!-- row -->

                                <!-- Opcion viable para rango de fechas con opciones 7 dias, 30 dias, mes actual, personalizado-->
                                <!-- <div class="form-group col-lg-4 col-md-12 col-sm-12 centerFechaIngreso">

                                    <div class="input-group drp-container col-lg-12 col-md-4 col-sm-6">
                                        <label class="control-label">Date Range</label>;
                                        <div class="drp-container">
                                        <//?=
                                        DateRangePicker::widget([
                                            'name'=>'date_range_2',
                                            'presetDropdown'=>true,
                                            'hideInput'=>true
                                        ]);
                                        ?>
                                        </div>
                                    </div>

                                </div> -->

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
    </div>

</div>
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
<div class="modal fade" id="AltaAsignacion" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
          <img src="<?php echo get_home_url() ?>/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
            <div class="modal-header">
                <h4 class="modal-title" style="text-align: center;">Alta de asignación</h4>
            </div>
            <div class="modal-body">

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
                      ]);
                      ?>

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
                      <?= Html::hiddeninput('horasPeriodo','',['id' => 'horasPeriodo'])?>
                      <?= Html::hiddeninput('totalMonto','',['id' => 'totalMonto'])?>

                      <div class="form-group col-lg-4">
                        <?= Html::label('Tarifa Hora','search',['class'=>'campos-label']) ?>
                        <div class="tarifas"></div>
                        <div class="help-block"></div>
                        <div class="clear"></div>
                      </div>
                  </div>

            
            </div> <!-- modal body -->
            <div class="modal-footer">
              <?= Html::submitButton('<span class="glyphicon glyphicon-ban-circle"></span> <span class="nombreBoton">Guardar</span>',['class'=>'btn btn-success der', 'id'=>'botonGuardar']) ?>
              <?= Html::button('<span class="glyphicon glyphicon-floppy-disk"></span> <span class="nombreBoton">Cancelar</span>',['class'=>'btn btn-success der', 'id'=>'botonCancelar', 'data-dismiss'=>'modal']) ?>
            </div>
        </div> <!--modal content -->
    </div> <!--dialog -->
</div> <!-- div modal -->

<div class="modal fade" id="guardar-asignacion" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p id="mensaje" style="text-align: center; font-weight: bold;">
                    Este recurso se encuentra actualmente asignado a un proyecto, ¿Desea registrar la asignación?
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-cancel salir" data-dismiss="modal">CANCELAR</button>
                    <button type="button" class="btn btn-success" onclick="validarJefes();" id="btnContinuarAsignacionProyecto">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="guardar-asignacion2" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p id="mensaje" style="text-align: center; font-weight: bold;">
                    Este recurso se encuentra actualmente asignado a un proyecto, ¿Desea registrar la asignación?
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-cancel salir" data-dismiss="modal">CANCELAR</button>
                    <button type="button" class="btn btn-success" onclick="validarJefes2();" id="btnContinuarAsignacionProyecto2">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="empleados-cargo" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style='border-radius: 0px;'>
          <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
          <div class="modal-header">
            <p id="mensaje-baja-asignacion" style="font-weight: bold; padding: 0% 5%;">
            No es posible realizar esta accion debido a que este empleado
            tiene personal a su cargo, favor de asignarles un Jefe Directo
            </p>
          </div>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <div class="row" style="margin: 0px; padding: 0px 5%;" id="divNuevosJefes"></div>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <?= Html::button('ACEPTAR', ['class' => 'btn btn-success der', 'id'=>'botonEnviar2', 'disabled' => true]) ?>
              <?= Html::button('CANCELAR', ['class' => 'btn btn-cancel der', 'data-dismiss'=>'modal', 'disabled' => false]) ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<!--Modal para tomar recurso como remplazo -->
<div class="modal fade" id="modalRemplazarRecurso" tabindex="-1" role="dialog" aria-labelledby="modalEditarPeriodoLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style='border-radius: 0px;'>
      <div class="modal-body" style="padding: 0px; position: relative;">
        <img src="<?php echo get_home_url() ?>/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
        <div class="clear"></div>
        <h3 class="modal-title" style="text-align: center;">Remplazo de recurso</h3>
        <form method="post" action="" id="RemplazaRecurso" style="padding: 25px; ">
          <div class="form-group" style="padding: 15px;border:1px solid #dddddd;">
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <?= Html::label('Cliente','search',['class'=>'campos-label']) ?>
                  <?= Html::input('text','id_nombreCliente_mascara','',['id'=>'id_nombreCliente_mascara_r','class'=>'search form-control', 'readonly'=>'readonly']) ?>
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <?= Html::label('Contacto','search',['class'=>'campos-label']) ?>
                  <?= Html::input('text','id_nombreContacto_mascara','',['id'=>'id_nombreContacto_mascara_r','class'=>'search form-control', 'readonly'=>'readonly']) ?>
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <?= Html::label('Ubicación','search',['class'=>'campos-label']) ?>
                  <?= Html::input('text','id_ubicacion_mascara','',['id'=>'id_ubicacion_mascara_r','class'=>'search form-control', 'readonly'=>'readonly']) ?>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <?= Html::label('Nombre de la asignación','search',['class'=>'campos-label']) ?>
                  <?= Html::input('text','id_nombreAsignacion_mascara','',['id'=>'id_nombreAsignacion_mascara_r','class'=>'search form-control', 'readonly'=>'readonly']) ?>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <?= Html::label('Responsable de asignación','search',['class'=>'campos-label']) ?>
                  <?= Html::input('text','id_responsableOp_mascara','',['id'=>'id_responsableOp_mascara_r','class'=>'search form-control', 'readonly'=>'readonly']) ?>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <?= Html::label('Unidad de negocio','search',['class'=>'campos-label']) ?>
                  <?= Html::input('text','id_unidadNegocio_mascara','',['id'=>'id_unidadNegocio_mascara_r','class'=>'search form-control', 'readonly'=>'readonly']) ?>
                </div>
              </div>
              <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12 col-xs-offset-12"></div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="form-group font-bold">
                  <?= Html::hiddeninput('pkEmpleado')?>
                  <?= Html::hiddeninput('tarifaRemplazo','',['id' => 'tarifaRemplazo'])?>
                  <?= Html::hiddeninput('horasCliente','',['id' => 'horasCliente'])?>
                  <?= Html::hiddeninput('horasPeriodo','',['id' => 'horasPeriodo'])?>
                  <?= Html::hiddeninput('totalMonto','',['id' => 'totalMonto'])?>
                  <?= Html::hiddenInput('pkCliente','',['id' => 'pkCliente']) ?>
                  <?= Html::hiddeninput('pkContacto','',['id' => 'pkContacto'])?>
                  <?= Html::hiddeninput('pkUbicacion','',['id' => 'pkUbicacion'])?>
                  <?= Html::hiddeninput('pkResponsable','',['id' => 'pkResponsable'])?>
                  <?= Html::hiddeninput('pkUnidad','',['id' => 'pkUnidad'])?>
                  <?= Html::hiddeninput('finAsignacion','',['id' => 'finAsignacion'])?>
                  <?= Html::hiddeninput('pkEstatus','',['id' => 'pkEstatus'])?>
                  <?= Html::hiddeninput('pkProyecto','',['id' => 'pkProyecto'])?>
                  <input type="hidden" id="cant_personas_a_cargo">
                  <?= Html::label('Inicio','fechaInicio',['class'=>'campos-label ']) ?>
                    <?php echo DatePicker::widget([
                          'name'=>'fechaInicio',
                          'class'=>'campos-label',
                          'dateFormat' => 'dd/mm/yyyy',
                          'clientOptions' => [
                              'yearRange' => '-115:+0',
                              'changeYear' => true,
                              'changeMonth' => true],
                          'options' => [
                              'class' => 'form-control datepicker fecha_ini datepicker-upa',
                              'pattern'=>'\d{2}/\d{2}/\d{4}',
                              'placeholder' => 'DD/MM/YYYY'
                          ],
                      ]); ?>
                      <div class="help-block"></div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="form-group font-bold">
                    <?= Html::label('Fin','fechaFin',['class'=>'campos-label ']) ?>
                    <?php echo DatePicker::widget([
                      'name'=>'fechaFin',
                      'class'=>'campos-label',
                      'dateFormat' => 'dd/mm/yyyy',
                      'clientOptions' => [
                        'yearRange' => '-115:+0',
                        'changeYear' => true,
                        'changeMonth' => true],
                      'options' => [
                        'class' => 'form-control datepicker fecha_fin datepicker-upa',
                        'pattern'=>'\d{2}/\d{2}/\d{4}',
                        'placeholder' => 'DD/MM/YYYY'
                      ],
                    ]); ?>
                    <?= Html::input('checkbox','indefinidoFin','',['id'=>'indefinidoFin']) ?><?= Html::label('Indefinida','search',['class'=>'campos-label']) ?>
                    <div class="help-block"></div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="form-group font-bold">
                    <?= Html::hiddenInput('tarifa','',['id'=>'id_tarifa_value_edit','class'=>'search form-control']) ?>
                    <?= Html::label('Tarifa','search',['class'=>'campos-label']) ?>
                    <?= Html::input('text','id_tarifa_mascara','',['id'=>'id_tarifa_mascara','class'=>'search form-control', 'readonly'=>'readonly']) ?>
                    <div class="help-block"></div>
                  </div>
                </div>
              </div>
              <div class="contador-item"></div>
              <div class="clear"></div>
            </div> <!-- row -->
          </div> <!-- form-group -->
        </form>
      </div> <!-- modal body -->
      <div class="row text-center">
        <button type="button" class="btn btn-cancel" onclick="cancelar()" data-dismiss="modal">CANCELAR</button>
        <button type="button" class="btn btn-success" id="remplazarRecurso">Guardar</button>
        <div class="form-group">
          <div class="help-block" id="res-modal"></div>
        </div>
      </div>
    </div>
  </div>
</div>

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

.resultados .contenedor .contenedor-itememp{

    margin: 0;
    /*margin: 1px 1px;*/
    padding: 2px;
    background: #fff;
    box-shadow: 0px 1px 2px #666;
    border: 1px solid #ddd;
    position: relative;
    min-height: 125px;
}
.resultados .contenedor .contenedor-itememp .fotoperfil{
    margin: 0;
    padding: 2px;
    width: auto;
}

.centrarVerticalmente{
    /*width : 100%;*/
    height : 100%;
    display : table-cell;
    vertical-align : middle;
    /*border : 1px solid red;*/
    float:none;
}
.marginLegend{
    margin-bottom: 6px;
}
.marginLabel{
    margin-bottom: 1px;
}
.descarga-activa{
    color: #337ab7 !important;
}
a:hover { cursor: pointer; }
</style>
<script>
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
    var unidadNegocioUsuario =    <?= json_encode($unidadNegocio,JSON_HEX_QUOT |
                    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
                ) ?>;

    function crear_elemento(data){


        mensaje= 'No existen elementos que cuenten con los criterios especificados para los parámetros de búsqueda';
        divFechaBaja = '';
        divAsignar = '';
        Tarifas = '';
        if (data.FK_ESTATUS_RECURSO=='4'||data.FK_ESTATUS_RECURSO=='6') {
            divFechaBaja = '<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12 margin-card">'+
                                '<label class="campos-label">Fecha de Baja</label>'+
                                '<p class="font-regular"> '+data.FECHA_BAJA+' </p>'+
                            '</div>';
        };

        var linkRemplazo = '';

        <?php if(isset($_GET['idAsignacion'])){ ?>

            if (data.FK_ESTATUS_RECURSO =='1' || data.FK_ESTATUS_RECURSO =='3'||data.FK_ESTATUS_RECURSO =='101') {

                linkRemplazo =
                '<div class="col-lg-push-9 col-lg-3 col-md-push-6 col-sm-6 col-xs-12 margin-card" style="text-align: right;">'+
                  '<a class="font-regular enlace-remplazar" href="#" id="remplazar" data-toggle="modal" data-target="#modalRemplazarRecurso" data-empleado='+data.PK_EMPLEADO+' data-tarifa='+data.TARIFA+' data-cantPersonasCargo='+data.CANT_EMPLEADOS_A_CARGO+' data-estatus='+data.FK_ESTATUS_RECURSO+'> Tomar como remplazo </a>'+
                  '</div>';
            };

        <?php } ?>

        divEstatus = '';

        if(data.FK_ESTATUS_RECURSO == 101){
            divEstatus = '<div class="estatus-item estatus-empleado-3">Disponible</div>';
        }else{
            divEstatus = '<div class="estatus-item estatus-empleado-'+data.FK_ESTATUS_RECURSO+'">'+data.DESC_ESTATUS_RECURSO+'</div>';
        }


        if (data.PK_UNIDAD_NEGOCIO == unidadNegocioUsuario && (data.FK_ESTATUS_RECURSO =='1'||data.FK_ESTATUS_RECURSO =='3'||data.FK_ESTATUS_RECURSO =='101')) {
            divAsignar =
            '<div class="col-lg-push-9 col-lg-3 col-md-push-6 col-sm-6 col-xs-12 margin-card" style="text-align: right;">'+
              '<a class="font-regular enlace-asignar" href="#" id="asignar" data-toggle="modal" data-target="#AltaAsignacion" data-empleado='+data.PK_EMPLEADO+' data-tarifa='+data.TARIFA+' data-cantPersonasCargo='+data.CANT_EMPLEADOS_A_CARGO+' data-estatus='+data.FK_ESTATUS_RECURSO+'> Asignar </a>'+
              '</div>';
        };

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
                                '<a href="<?php echo Url::to(['empleados/view']) ?>?id='+data.PK_EMPLEADO+'&idAsignacion= <?php echo $idA ?>">'+
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
                                        '<label class="campos-label">Ubicación Física</label>'+
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
                                    '<div class="col-lg-1 col-md-6 col-sm-12 col-xs-12 margin-card">'+
                                        '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                                            divAsignar+
                                        '</div>'+
                                        '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                                            linkRemplazo+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 margin-card">'+
                                        divFechaBaja+
                                    '</div>'+
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

    function validarJefes2(){
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
        $('#guardar-asignacion2').modal('hide');
        jQuery('#empleados-cargo').modal({
          show: true,
          keyboard: false,
          backdrop: 'static'
        })
        $("#botonEnviar2").prop('disabled',true);
      } else {


        var id =    <?= json_encode($idA,JSON_HEX_QUOT |
                        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
                    ) ?>;

        var pkEmpleado_user = <?= json_encode($pkEmp_user,JSON_HEX_QUOT |
                        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
                    ) ?>;

        $.ajax({
          url: ajaxModal,
          type: 'post',
          data: {
            id : id,
            pkEmpleado_user : pkEmpleado_user,
            _csrf  : _csrfToken
          },
          success: function (data) {
            console.log(data);
            $('#pkCliente').val(data.query['PK_CLIENTE']);
            $('#id_nombreCliente_mascara_r').val(data.query['NOMBRE_CLIENTE']);
            $('#pkContacto').val(data.query['PK_CONTACTO']);
            $('#id_nombreContacto_mascara_r').val(data.query['NOMBRE_CONTACTO']);
            $('#pkUbicacion').val(data.query['PK_UBICACION']);
            $('#id_ubicacion_mascara_r').val(data.query['DESC_UBICACION']);
            $('#id_nombreAsignacion_mascara_r').val(data.query['NOMBRE']);
            $('#pkResponsable').val(data.query['PK_RESPONSABLE']);
            $('#id_responsableOp_mascara_r').val(data.query['NOMBRE_RESPONSABLE']);

            if( data.query2['PK_UNIDAD_NEGOCIO'] == 3){

                $('#pkUnidad').val(data.query2['PK_UNIDAD_NEGOCIO']);
                $('#id_unidadNegocio_mascara_r').val(data.query2['DESC_UNIDAD_NEGOCIO']);
            }else{

                $('#pkUnidad').val(data.query['PK_UNIDAD_NEGOCIO']);
                $('#id_unidadNegocio_mascara_r').val(data.query['DESC_UNIDAD_NEGOCIO']);
            }

            $('#finAsignacion').val(data.query['FECHA_FIN']);
            $('#pkEstatus').val(data.query['FK_ESTATUS_ASIGNACION']);
            $('#horasCliente').val(data.query['HORAS_ASIGNACION']);
            $('#pkProyecto').val(data.query['FK_PROYECTO']);
            $('#id_tarifa_mascara').val($('#tarifaRemplazo').val());
          }, error: function(error) {

            console.log('Error');
          }
        });

        $('#modalRemplazarRecurso').modal('show');

        $('#guardar-asignacion2').modal('hide');

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
            validar_fechas_calculo_horas(element);
            return true;
        }
      }else if(element.hasClass('FechaInicio')){
        if( (new Date(reverse_date_ddmmYY(element.val())).getTime() >= new Date(reverse_date_ddmmYY($('.FechaFin').val())).getTime()))
        {
            element.parent().addClass('has-error').find('.help-block').html('La Fecha inicio debe ser menor a la Fecha Final, Favor de Verificar');
            return false;
        }else{
            element.parent().removeClass('has-error').find('.help-block').html('')
            validar_fechas_calculo_horas(element);
            return true;
        }

      }

    }

    function validar_fechas_calculo_horas(element){

                tarifa = $('.tarifa-hora').val();

                if($('.FechaFin').val() && $('.FechaInicio').val())
                {
                  var fecha_ini = $('.FechaInicio').val();
                  var fecha_ini_dia = fecha_ini.substring(0,2);
                  var fecha_ini_mes = fecha_ini.substring(3,5);
                  var fecha_ini_anio = fecha_ini.substring(6,10);
                  var fecha_fin = $('.FechaFin').val();
                  var fecha_fin_dia = fecha_fin.substring(0,2);
                  var fecha_fin_mes = fecha_fin.substring(3,5);
                  var fecha_fin_anio = fecha_fin.substring(6,10);
                  //var fecha_ini_horas = new Date(reverse_date_ddmmYY($('.fecha_ini'+id).val()));
                  var fecha_ini_horas = new Date(fecha_ini_anio,parseInt(fecha_ini_mes)-1,fecha_ini_dia,0,0,0);
                  var fecha_fin_horas = new Date(fecha_fin_anio,parseInt(fecha_fin_mes)-1,fecha_fin_dia,0,0,0);
                  var dia_mls = (24*60*60*1000);
                  var hora_mls = (60*60*1000)+1;
                  var dias = 0;
                  var dia_fecha_inicio_ultimo = get_month_days(fecha_ini_mes,fecha_ini_anio);//Ultimo dia del mes de la fecha de inicio
                  var dia_fecha_fin_ultimo = get_month_days(fecha_fin_mes,fecha_fin_anio);//Ultimo dia del mes de la fecha fin
                  var dias_habiles = 0;
                var dias_habiles_mes_uno = 0;
                var dias_habiles_mes_dos = 0;

                  //Si la fecha de inicio y la fecha de fin estan en un mes diferente
                  if((fecha_ini_mes!=fecha_fin_mes)||(fecha_ini_mes==fecha_fin_mes && fecha_ini_anio!=fecha_fin_anio)){

                    //Si la fecha inicial no inicia el dia primero, se toman en cuenta solo los dias habiles correspondientes a ese mes y acorde a la fecha de inicio
                    if(fecha_ini_dia!='01'){
                      var fecha_inicio_calculada = new Date(fecha_ini_anio,parseInt(fecha_ini_mes)-1,fecha_ini_dia,0,0,0); //Inicializa la fecha de inicio
                      var fecha_inicio_mes_fin = new Date(fecha_ini_anio, parseInt(fecha_ini_mes)-1, dia_fecha_inicio_ultimo, 0, 0, 0);//Inicializa la fecha fin de este mes
                      var tiempo_inicio_mls = fecha_inicio_calculada.getTime();
                      var tiempo_fin_mls = fecha_inicio_mes_fin.getTime();
                      for(i=tiempo_inicio_mls;i<=tiempo_fin_mls;i=i+dia_mls){
                        tiempo_inicio_date = new Date(i);
                        if(tiempo_inicio_date.getDay()>0&&tiempo_inicio_date.getDay()<6){
                          dias_habiles++;
                        dias_habiles_mes_uno++;
                        }
                      }
                      fecha_ini_horas = new Date(fecha_inicio_mes_fin.getTime()+dia_mls);
                    }

                    //Se saca la diferencia de dias entre la fecha de inicio y la fecha de fin, y se le agrega un dia mas, correspondiente al ultimo dia de la fecha fin
                    dias_diferencia = (fecha_fin_horas.getTime()-fecha_ini_horas.getTime()+dia_mls)/dia_mls;
                    dias = dias + dias_diferencia;
                    dias_del_mes=0;

                    //Si el dia de la fecha fin no es el dia final del mes
                    if(parseInt(dia_fecha_fin_ultimo) != parseInt(fecha_fin_dia)){
                      fecha_inicio_mes_fin = new Date(fecha_fin_anio,parseInt(fecha_fin_mes)-1,1,0,0,0);
                      for(i=fecha_inicio_mes_fin.getTime();i<=fecha_fin_horas.getTime();i=i+dia_mls){
                        tiempo_actual = new Date(i);
                        if(tiempo_actual.getDay()>0&&tiempo_actual.getDay()<6){
                          dias_habiles++;
                        dias_habiles_mes_dos++;
                        }
                        dias_del_mes++;
                      }
                      dias = dias - dias_del_mes;
                    }
                    meses = Math.round(dias/30);
                } else {
                  //Si la fecha de inicio y la fecha de fin estan en el mismo mes
                    if(fecha_ini_dia=='01' && parseInt(dia_fecha_fin_ultimo)==parseInt(fecha_fin_dia)){//Si el mes no esta completo
                      meses = 1;
                    }else{//Se sacan dias habiles
                      for(i=fecha_ini_horas.getTime();i<=(fecha_fin_horas.getTime()+hora_mls);i=i+dia_mls){//En la condicion se agrega una hora mas un milisegundo por efectos del cambios de horario
                        tiempo = new Date(i);
                        console.log(tiempo.toString());
                        if(tiempo.getDay()>0&&tiempo.getDay()<6){
                          dias_habiles++;
                        dias_habiles_mes_uno++;
                        }
                      }
                      meses = 0;
                    }
                  }

                var horas_cliente = parseFloat($('#HORA_CLIENTE').val());
                var horas_mes_uno = 0;
                var horas_mes_dos = 0;
                var horas_dias_habiles = 0;


                //Calcula las horas del primer mes incompleto
                if(dias_habiles_mes_uno*8>horas_cliente){
                  horas_mes_uno = horas_cliente;
                } else {
                  horas_mes_uno = dias_habiles_mes_uno*8;
                }

                //Calcula las horas del ultimo mes incompleto
                if(dias_habiles_mes_dos*8>horas_cliente){
                  horas_mes_dos = horas_cliente;
                } else {
                  horas_mes_dos = dias_habiles_mes_dos*8;
                }

                horas_dias_habiles = parseInt(horas_mes_uno) + parseInt(horas_mes_dos);

                console.log("horas_cliente "+horas_cliente);
                console.log("horas_mes_uno "+horas_mes_uno);
                console.log("horas_mes_dos "+horas_mes_dos);
                console.log("horas_dias_habiles "+horas_dias_habiles);
                  if(meses > 0){
                  var horasTotal = (horas_dias_habiles) + Math.floor(meses * horas_cliente);
                  }else{
                  var horasTotal = (horas_dias_habiles);
                  }
                  $('#horasPeriodo').val(horasTotal);
                  $('#totalMonto').val(parseFloat(horasTotal) * parseFloat(tarifa));
                    return false;
                }else{
                  $('#horasPeriodo').val('');
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

    function validar_fechas_calculo_horas_r(){

      var tarifa = $('#tarifaRemplazo').val();

      if($('#modalRemplazarRecurso .fecha_fin').val() == null || $('#modalRemplazarRecurso .fecha_fin').val() == ''){

        $('#horasPeriodo').val(0);
        $('#totalMonto').val(0);
      }else if($('#modalRemplazarRecurso .fecha_ini').val() && $('#modalRemplazarRecurso .fecha_fin').val()){

        //var fecha_ini = $('#tblasignaciones-fecha_ini').val();
        var fecha_ini = $('.fecha_ini').val();
        var fecha_ini_dia = fecha_ini.substring(0,2);
        var fecha_ini_mes = fecha_ini.substring(3,5);
        var fecha_ini_anio = fecha_ini.substring(6,10);
        //var fecha_fin = $('#tblasignaciones-fecha_fin').val();
        var fecha_fin = $('.fecha_fin').val();
        var fecha_fin_dia = fecha_fin.substring(0,2);
        var fecha_fin_mes = fecha_fin.substring(3,5);
        var fecha_fin_anio = fecha_fin.substring(6,10);
        //var fecha_ini_horas = new Date(reverse_date_ddmmYY($('.fecha_ini'+id).val()));
        var fecha_ini_horas = new Date(fecha_ini_anio,parseInt(fecha_ini_mes)-1,fecha_ini_dia,0,0,0);
        var fecha_fin_horas = new Date(fecha_fin_anio,parseInt(fecha_fin_mes)-1,fecha_fin_dia,0,0,0);
        var dia_mls = (24*60*60*1000);
        var hora_mls = (60*60*1000)+1;
        var dias = 0;
        var dia_fecha_inicio_ultimo = get_month_days(fecha_ini_mes,fecha_ini_anio);//Ultimo dia del mes de la fecha de inicio
        var dia_fecha_fin_ultimo = get_month_days(fecha_fin_mes,fecha_fin_anio);//Ultimo dia del mes de la fecha fin
        var dias_habiles = 0;
        var dias_habiles_mes_uno = 0;
        var dias_habiles_mes_dos = 0;

                      //Si la fecha de inicio y la fecha de fin estan en un mes diferente
        if((fecha_ini_mes!=fecha_fin_mes)||(fecha_ini_mes==fecha_fin_mes && fecha_ini_anio!=fecha_fin_anio)){

          //Si la fecha inicial no inicia el dia primero, se toman en cuenta solo los dias habiles correspondientes a ese mes y acorde a la fecha de inicio
          if(fecha_ini_dia!='01'){
            var fecha_inicio_calculada = new Date(fecha_ini_anio,parseInt(fecha_ini_mes)-1,fecha_ini_dia,0,0,0); //Inicializa la fecha de inicio
            var fecha_inicio_mes_fin = new Date(fecha_ini_anio, parseInt(fecha_ini_mes)-1, dia_fecha_inicio_ultimo, 0, 0, 0);//Inicializa la fecha fin de este mes
            var tiempo_inicio_mls = fecha_inicio_calculada.getTime();
            var tiempo_fin_mls = fecha_inicio_mes_fin.getTime();
            for(i=tiempo_inicio_mls;i<=tiempo_fin_mls;i=i+dia_mls){
              tiempo_inicio_date = new Date(i);
              if(tiempo_inicio_date.getDay()>0&&tiempo_inicio_date.getDay()<6){
                dias_habiles++;
                dias_habiles_mes_uno++;
              }
            }
            fecha_ini_horas = new Date(fecha_inicio_mes_fin.getTime()+dia_mls);
          }

         //Se saca la diferencia de dias entre la fecha de inicio y la fecha de fin, y se le agrega un dia mas, correspondiente al ultimo dia de la fecha fin
          dias_diferencia = (fecha_fin_horas.getTime()-fecha_ini_horas.getTime()+dia_mls)/dia_mls;
          dias = dias + dias_diferencia;
          dias_del_mes=0;

         //Si el dia de la fecha fin no es el dia final del mes
          if(parseInt(dia_fecha_fin_ultimo) != parseInt(fecha_fin_dia)){
            fecha_inicio_mes_fin = new Date(fecha_fin_anio,parseInt(fecha_fin_mes)-1,1,0,0,0);
            for(i=fecha_inicio_mes_fin.getTime();i<=fecha_fin_horas.getTime();i=i+dia_mls){
              tiempo_actual = new Date(i);
              if(tiempo_actual.getDay()>0&&tiempo_actual.getDay()<6){
                dias_habiles++;
                dias_habiles_mes_dos++;
              }
              dias_del_mes++;
            }
            dias = dias - dias_del_mes;
          }
          meses = Math.round(dias/30);
        } else {//Si la fecha de inicio y la fecha de fin estan en el mismo mes
          if(fecha_ini_dia=='01' && parseInt(dia_fecha_fin_ultimo)==parseInt(fecha_fin_dia)){//Si el mes no esta completo
            meses = 1;
          }else{//Se sacan dias habiles
            for(i=fecha_ini_horas.getTime();i<=(fecha_fin_horas.getTime()+hora_mls);i=i+dia_mls){//En la condicion se agrega una hora mas un milisegundo por efectos del cambios de horario
              tiempo = new Date(i);

              if(tiempo.getDay()>0&&tiempo.getDay()<6){
                dias_habiles++;
                dias_habiles_mes_uno++;
              }
            }
            meses = 0;
          }
        }
        //var horas_cliente = ($('#horas_cliente').val());
        var horas_cliente = $('#horasCliente').val();//Pendiente
        var horas_mes_uno = 0.00;
        var horas_mes_dos = 0.00;
        var horas_dias_habiles = 0.00;

        //Calcula las horas del primer mes incompleto
        if(dias_habiles_mes_uno*8>horas_cliente){
          horas_mes_uno = horas_cliente;
        } else {
          horas_mes_uno = dias_habiles_mes_uno*8;
        }

        //Calcula las horas del ultimo mes incompleto
        if(dias_habiles_mes_dos*8>horas_cliente){
          horas_mes_dos = horas_cliente;
        } else {
          horas_mes_dos = dias_habiles_mes_dos*8;
        }

        horas_dias_habiles = parseFloat(horas_mes_uno) + parseFloat(horas_mes_dos);

        if(meses > 0){
          var horasTotal = (dias_habiles * 8) + Math.floor(meses * horas_cliente);
        }else{
          var horasTotal = (dias_habiles * 8);
        }

        $('#horasPeriodo').val(horasTotal);

       monto_total = horasTotal * tarifa;

        $('#totalMonto').val(monto_total);
      }

    }

jQuery(document).ready(function($) {

    jQuery('body').on('click', '#remplazar', function(){

        jQuery('.fecha_ini').datepicker('setDate', null);
        jQuery('.fecha_fin').datepicker('setDate', null);


    })
    jQuery('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: true,
        todayHighlight: true
      }).on('changeDate', function(ev) {

        if (ev.viewMode=='days') {
          $('.dropdown-menu').hide();
        };
        validaFecha($(this));
        validar_fechas_calculo_horas_r();

      });
      $('.datepicker').on('change , blur ', function(event) {
        validaFecha($(this));
      });
});

$(document).ready(function() {

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
      $('#pk_cat_tarifa_select_'+$(this).data('idemp')).val($(this).find('option:selected').data('pk_cat_tarifa'));
      var tarifa = parseFloat($(this).val());
      var horas = parseFloat($('#horasPeriodo').val());
      $('#totalMonto').val(tarifa * horas);
  })

  $('#indefinido').on('change', function(){
    if ( $('.field-tblasignaciones-fecha_fin #indefinido').is(':checked') ) {
      $("#tblasignaciones-fecha_fin").prop('disabled',true);
      $("#tblasignaciones-fecha_fin").val('');
      $("#horasPeriodo").val('');
    } else {
      $("#tblasignaciones-fecha_fin").prop('disabled',false);
    }
  });

  $('#indefinidoFin').on('change', function(){
    if ( $('#indefinidoFin').is(':checked') ) {

      $("#w3").prop('disabled',true);
      $("#w3").val('');

      validar_fechas_calculo_horas_r();

      $('.fecha_fin').parent().removeClass('has-error').find('.help-block').html('');
    } else {

      $("#w3").prop('disabled',false);
      $('.fecha_fin').parent().addClass('has-error').find('.help-block').html('Este campo no puede estar vacío');
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

      $('#tblasignaciones-fk_cliente').on('change',function() {
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


    $('.contenedor-parent').on('click', '#asignar', function(){
        var empleado = parseFloat($(this).attr('data-empleado'));
        var tarifa = parseFloat($(this).attr('data-tarifa'));
        var PkEmpleado = parseFloat($(this).attr('data-empleado'));
        var cant_personas_a_cargo = parseFloat($(this).attr('data-cantPersonasCargo'));
        $('#tblasignaciones-tarifa').val(tarifa);
        $('#cant_personas_a_cargo').val(cant_personas_a_cargo);
        $('input[name="FkEmpleado"]').val(empleado);

        var hayAsignados = false;
        var estatus = $(this).data('estatus');
        if(estatus != 3 && estatus!=4 && estatus!=101){
          hayAsignados = true;
        }
        if(hayAsignados){
          $('#guardar-asignacion').modal('show');
          return false;
        }

    });

  $('#botonCancelar').on("click",function() {
    document.getElementById('formAltaAsignaciones').reset();
  });

    $('#tblasignaciones-monto').on('change', function(e) {
      var monto = number_format($('#tblasignaciones-monto').val(),2);
      $('#tblasignaciones-monto').val(monto);
    });

    $('body').on('click', '#remplazar', function(){

        var id =    <?= json_encode($idA,JSON_HEX_QUOT |
                        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
                    ) ?>;

        var pkEmpleado_user = <?= json_encode($pkEmp_user,JSON_HEX_QUOT |
                        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
                    ) ?>;

        var emp = parseFloat($(this).attr('data-empleado'));
        var t = parseFloat($(this).attr('data-tarifa'));

        $('input[name="pkEmpleado"]').val(emp);
        $('input[name="tarifaRemplazo"]').val(t);

        var cant_personas_a_cargo = parseFloat($(this).attr('data-cantPersonasCargo'));

        $('#cant_personas_a_cargo').val(cant_personas_a_cargo);

        var hayAsignados = false;
        var estatus = $(this).data('estatus');
        if(estatus != 3 && estatus!=4 && estatus!=101){
          hayAsignados = true;
        }
        if(hayAsignados){
          $('#guardar-asignacion2').modal('show');
          return false;
        }

        $.ajax({
          url: ajaxModal,
          type: 'post',
          data: {
            id : id,
            pkEmpleado_user : pkEmpleado_user,
            _csrf  : _csrfToken
          },
          success: function (data) {
            console.log(data);
            $('#pkCliente').val(data.query['PK_CLIENTE']);
            $('#id_nombreCliente_mascara_r').val(data.query['NOMBRE_CLIENTE']);
            $('#pkContacto').val(data.query['PK_CONTACTO']);
            $('#id_nombreContacto_mascara_r').val(data.query['NOMBRE_CONTACTO']);
            $('#pkUbicacion').val(data.query['PK_UBICACION']);
            $('#id_ubicacion_mascara_r').val(data.query['DESC_UBICACION']);
            $('#id_nombreAsignacion_mascara_r').val(data.query['NOMBRE']);
            $('#pkResponsable').val(data.query['PK_RESPONSABLE']);
            $('#id_responsableOp_mascara_r').val(data.query['NOMBRE_RESPONSABLE']);

            if( data.query2['PK_UNIDAD_NEGOCIO'] == 3){

                $('#pkUnidad').val(data.query2['PK_UNIDAD_NEGOCIO']);
                $('#id_unidadNegocio_mascara_r').val(data.query2['DESC_UNIDAD_NEGOCIO']);
            }else{

                $('#pkUnidad').val(data.query['PK_UNIDAD_NEGOCIO']);
                $('#id_unidadNegocio_mascara_r').val(data.query['DESC_UNIDAD_NEGOCIO']);
            }

            $('#finAsignacion').val(data.query['FECHA_FIN']);
            $('#pkEstatus').val(data.query['FK_ESTATUS_ASIGNACION']);
            $('#horasCliente').val(data.query['HORAS_ASIGNACION']);
            $('#pkProyecto').val(data.query['FK_PROYECTO']);
            $('#id_tarifa_mascara').val($('#tarifaRemplazo').val());
          }, error: function(error) {

            console.log('Error');
          }
        });

    })

    $('#remplazarRecurso').on("click",function(e) {

        e.preventDefault();
        $('#res-modal').html('').parent().removeClass('has-error');

        if($('#modalRemplazarRecurso .has-error').length>0){
          return false;
        }else if($("#indefinidoFin").prop('checked',true)){

            if($('#modalRemplazarRecurso .has-error').length>0){
              return true;
            }

        }

        bandera = true;

        if (bandera) {

            var id =    <?= json_encode($idA,JSON_HEX_QUOT |
                        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
                    ) ?>;

            var fk_emp = $('input[name="pkEmpleado"]').val();
            var fk_cliente = $('input[name="pkCliente"]').val();
            var fk_contacto = $('input[name="pkContacto"]').val();
            var fk_ubicacion = $('input[name="pkUbicacion"]').val();
            var fk_res = $('input[name="pkResponsable"]').val();
            var fk_unidad = $('input[name="pkUnidad"]').val();
            var fi_asignacion = $('input[name="finAsignacion"]').val();
            var fk_estatus = $('input[name="pkEstatus"]').val();
            var fk_proyecto = $('input[name="pkProyecto"]').val();
            var nuevaTarifa = $('input[name="tarifaRemplazo"]').val();
            var horas_periodo = $('input[name="horasPeriodo"]').val();
            var monto = $('input[name="totalMonto"]').val();

            var fi_r = $('#w2').val();
            var ff_r = $('#w3').val();

            $.ajax({
              url: ajaxRemplazo,
              type: 'post',
              data: {
                id : id,
                fk_emp : fk_emp,
                fk_cliente : fk_cliente,
                fk_contacto : fk_contacto,
                fk_ubicacion : fk_ubicacion,
                fk_res : fk_res,
                fk_unidad : fk_unidad,
                fi_asignacion : fi_asignacion,
                fk_estatus : fk_estatus,
                fk_proyecto : fk_proyecto,
                nuevaTarifa : nuevaTarifa,
                horas_periodo : horas_periodo,
                monto : monto,
                fi_r : fi_r,
                ff_r : ff_r,
                _csrf  : _csrfToken
              },
              success: function (data) {

                console.log(data);
              }, error: function(error) {

                console.log('Error');
              }
            });
        }

    });

  });

    function validaFecha(element){

      var f_fin_a = $('#finAsignacion').val();

      var fin_asignacion = convierteFecha(f_fin_a);

      if(element.val()==''){
        element.parent().addClass('has-error').find('.help-block').html('Este campo no puede estar vacío');
        return false;
      }

      if(!validaFechaDDMMAAAA(element.val())){
        element.parent().addClass('has-error').find('.help-block').html('El formato de las fechas no es correcto, favor de validarlo');
        return false;
      }

      if(element.hasClass('fecha_fin')){

        if( (new Date(element.val().split("/").reverse().join("-")).getTime() <= new Date($('.fecha_ini').val().split("/").reverse().join("-")).getTime()))
        {
            element.parent().addClass('has-error').find('.help-block').html('La fecha Fin debe ser mayor a la fecha de Inicio, favor de verificar');

            if((new Date($('.fecha_ini').val().split("/").reverse().join("-")).getTime() >= new Date(element.val().split("/").reverse().join("-")).getTime())){

              $('.fecha_ini').parent().addClass('has-error').find('.help-block').html('La fecha de Inicio debe ser menor a la fecha Fin, favor de verificar');
            }

        }else{
            element.parent().removeClass('has-error').find('.help-block').html('')

            $('.fecha_ini').parent().removeClass('has-error').find('.help-block').html('')

        }
        $('.fecha_ini').change();
      }else if(element.hasClass('fecha_ini')){
        if( (new Date(element.val().split("/").reverse().join("-")).getTime() >= new Date($('.fecha_fin').val().split("/").reverse().join("-")).getTime()))
        {
            element.parent().addClass('has-error').find('.help-block').html('La fecha de Inicio debe ser menor a la fecha Fin, favor de verificar');

        }else if((new Date(element.val().split("/").reverse().join("-")).getTime() <= new Date(fin_asignacion.split("/").reverse().join("-")).getTime())){
            element.parent().addClass('has-error').find('.help-block').html('La fecha de Inicio debe ser mayor a la fecha de Fin de Asignación a remplazar, favor de verificar');
        }else{
            element.parent().removeClass('has-error').find('.help-block').html('')

        }
        $('.fecha_fin').change();
      }

    }

    function convierteFecha(fecha){

        var separador = "-";

        var dato = fecha.split(separador);

        fecha = dato[2] + "/" + dato[1] + "/" + dato[0];

        return fecha;
      }

    function cancelar(){

        $('#w2').val('');
        $('#w3').val('');

        $('#indefinidoFin').prop("checked", false);
        $('#w3').prop("disabled", false);
    }

    function habilitarCamposRecursosAsignados(){
      $('input[class*="fecha_ini"]').removeAttr('readonly');
      $('input[class*="fecha_fin"]').removeAttr('readonly');
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


</script>
