<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\TblCatPuestos;
use app\models\TblCatEstatusRecursos;
use app\models\TblCatAdministradoras;
use app\models\TblCatUbicacionRazonSocial;
use app\models\TblCatUnidadesNegocio;
use app\models\TblCatUbicaciones;
use yii\jui\DatePicker;

$modelo_cargado='empleados';
$datosCatPuestos = ArrayHelper::map(TblCatPuestos::find()->orderBy(['DESC_PUESTO'=>SORT_ASC])->asArray()->all(), 'PK_PUESTO', 'DESC_PUESTO');
$datosCatEstatusRecursos = ArrayHelper::map(TblCatEstatusRecursos::find()->andWhere('PK_ESTATUS_RECURSO <> 6')->andWhere('PK_ESTATUS_RECURSO <> 101')->orderBy('DESC_ESTATUS_RECURSO')->asArray()->all(), 'PK_ESTATUS_RECURSO', 'DESC_ESTATUS_RECURSO');
$datosCatAdministradoras = ArrayHelper::map(TblCatAdministradoras::find()->orderBy(['NOMBRE_ADMINISTRADORA'=>SORT_ASC])->asArray()->all(), 'PK_ADMINISTRADORA', 'NOMBRE_ADMINISTRADORA');
$datosCatUbicaciones = ArrayHelper::map(TblCatUbicacionRazonSocial::find()->orderBy(['DESC_UBICACION'=>SORT_ASC])->asArray()->all(), 'PK_UBICACION_RAZON_SOCIAL', 'DESC_UBICACION');
$datosUnidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
$datosUbicacionFisica = ArrayHelper::map(TblCatUbicaciones::find()->where(['=','PROPIA_CLIENTE','Propia'])->asArray()->all(), 'PK_UBICACION', 'DESC_UBICACION');
if(isset(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!empty(user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'])&&!is_super_admin()){
    $unidadesNegocioValidas = user_info()['EXCEPCION_BTN_REGLA_UNIDAD_NEGOCIO'];
    $datosUnidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->where(['PK_UNIDAD_NEGOCIO'=>$unidadesNegocioValidas])->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
} else {
    $datosUnidadNegocio = ArrayHelper::map(TblCatUnidadesNegocio::find()->orderBy(['DESC_UNIDAD_NEGOCIO'=>SORT_ASC])->asArray()->all(), 'PK_UNIDAD_NEGOCIO', 'DESC_UNIDAD_NEGOCIO');
}
$this->title = ' Empleados Pendientes de CV Original';
$this->params['breadcrumbs'][] = $this->title;
?>
<script>
    var ajaxUrl =           '<?php echo Url::to(["$modelo_cargado/pendientes_cvoriginal"]); ?>';
    var _csrfToken =        '<?=Yii::$app->request->getCsrfToken()?>';
    var url_view =          '<?php echo Url::to(["$modelo_cargado/view",'id'=>'lorem']); ?>';
    var url_pendientes =    '<?php echo Url::to(["$modelo_cargado/create",'id'=>'lorem']); ?>';
    var url_update =        '<?php echo Url::to(["$modelo_cargado/update",'id'=>'lorem']); ?>';
    var url_delete =        '<?php echo Url::to(["$modelo_cargado/delete",'id'=>'lorem']); ?>';
    var historial = 'empleados/pendientes_cvoriginal';
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
    function crear_elemento(data){
        mensaje= 'No existen elementos que cuenten con los criterios especificados para los parámetros de búsqueda';
        divFechaBaja = '';
        if (data.FK_ESTATUS_RECURSO=='4'||data.FK_ESTATUS_RECURSO=='6') {
            divFechaBaja = '<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12 margin-card">'+
                                '<label class="campos-label">Fecha de Baja</label>'+
                                '<p class="font-regular"> '+data.FECHA_BAJA+' </p>'+
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
                                    divFechaBaja+
                                '</div>'+
                             '</div>'+
                        '</div>'+
                    '<div class="estatus-item estatus-empleado-'+data.FK_ESTATUS_RECURSO+'">'+data.DESC_ESTATUS_RECURSO+'</div>'+
                '</div>';
        return html; 
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
