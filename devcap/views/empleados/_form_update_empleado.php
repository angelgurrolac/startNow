<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;

use app\models\tblcatpaises;
use app\models\tblcatestados;
use app\models\tblcatmunicipios;
use app\models\tbldomicilios;
use app\models\tblcatgenero;
use app\models\TblCatRazonSocial;
use app\models\TblCatUbicacionRazonSocial;
use app\models\TblCatAdministradoras;
use app\models\TblCatDuracionTipoServicios;
use app\models\TblCatTipoContrato;
use app\models\TblCatTipoServicios;
use app\models\TblCatAreas;
use app\models\TblCatPuestos;
use app\models\TblCatRankTecnico;
/* @var $this yii\web\View */
/* @var $model app\models\tblempleados */
/* @var $form yii\widgets\ActiveForm */

use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;

$url_paises = \yii\helpers\Url::to(['site/paises']);
$url_estados = \yii\helpers\Url::to(['site/estados']);
$url_municipios = \yii\helpers\Url::to(['site/municipios']);

$datosDomicilios = ArrayHelper::map(tbldomicilios::find()->asArray()->all(), 'PK_DOMICILIO','COLONIA');
$datosGenero = ArrayHelper::map(tblcatgenero::find()->asArray()->all(), 'PK_GENERO','DESC_GENERO');
$datosCatPaises = ArrayHelper::map(tblcatpaises::find()->asArray()->all(), 'PK_PAIS', 'DESC_PAIS');
$datosCatEstados = ArrayHelper::map(tblcatestados::find()->asArray()->all(), 'PK_ESTADO', 'DESC_ESTADO');
$datosCatMunicipios = ArrayHelper::map(tblcatmunicipios::find()->asArray()->all(), 'PK_MUNICIPIO', 'DESC_MUNICIPIO');
$datosRazonSocial = ArrayHelper::map(TblCatRazonSocial::find()->asArray()->all(), 'PK_RAZON_SOCIAL', 'DESC_RAZON_SOCIAL');
$datosUbicacion = ArrayHelper::map(TblCatUbicacionRazonSocial::find()->asArray()->all(), 'PK_UBICACION_RAZON_SOCIAL', 'DESC_UBICACION');
$datosAdministradora = ArrayHelper::map(TblCatAdministradoras::find()->asArray()->all(), 'PK_ADMINISTRADORA', 'NOMBRE_ADMINISTRADORA');
$datosDuracionTipoServicio = ArrayHelper::map(TblCatDuracionTipoServicios::find()->asArray()->all(), 'PK_DURACION', 'DESC_DURACION');
$datosTipoContrato = ArrayHelper::map(TblCatTipoContrato::find()->asArray()->all(), 'PK_TIPO_CONTRATO', 'DESC_TIPO_CONTRATO');
$datosTipoServicios = ArrayHelper::map(TblCatTipoServicios::find()->asArray()->all(), 'PK_TIPO_SERVICIO', 'DESC_TIPO_SERVICIO');
$datosAreas = ArrayHelper::map(TblCatAreas::find()->asArray()->all(), 'PK_AREA', 'DESC_AREA');
$datosPuestos = ArrayHelper::map(TblCatPuestos::find()->asArray()->all(), 'PK_PUESTO', 'DESC_PUESTO');
$datosRankTecnico = ArrayHelper::map(TblCatRankTecnico::find()->asArray()->all(), 'PK_RANK_TECNICO', 'DESC_RANK_TECNICO');
$colums='col-xs-12 col-sm-6 col-md-3 col-lg-3';
$colums2='col-xs-6 col-sm-2 col-md-2 col-lg-2';
$colums3='col-xs-6 col-sm-2 col-md-2 col-lg-6';
$colums4='col-xs-6 col-sm-6 col-md-6 col-lg-12';
$colums5='col-xs-6 col-sm-12 col-md-12 col-lg-12';
?>

<div class="tblempleados-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'form_empleados']]); ?>

        <div class="row form-container"> <!-- Form captura TBLEMPLEADO -->
           
            <h3 class="campos-title font-bold">
                <div class='circle-row izq'>1</div>Datos personales
            </h3>
            <div class="clear"></div>

            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-3'>
                <?= $form->field($modelSubirFotoEmpleado, '[1]file',
                [
                    'template' => ' <div class="'.$colums5.' text-center">{input}<label class="control-label">Foto de perfil</label>{error}{hint}<div class="clear"></div></div>',
                ]
                )->fileInput(['data-file'=>($model->FOTO_EMP&&$model->FOTO_EMP!='defoult')?'../../..'.$model->FOTO_EMP:'']) ?>

                <?= $form->field($model, 'FOTO_EMP')->hiddenInput()->label(false); ?>

            </div>
            
            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-9'>
                <div class="row">
                    <?= $form->field($model, 'NOMBRE_EMP',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'class'=>'form-control', 'onblur'=>"this.value=this.value.toUpperCase()"]) ?>
                    
                    <?= $form->field($model, 'APELLIDO_PAT_EMP', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onblur'=>"this.value=this.value.toUpperCase()"]) ?>

                    <?= $form->field($model, 'APELLIDO_MAT_EMP', 
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onblur'=>"this.value=this.value.toUpperCase()"]) ?>
                </div>

                <div class="row">

                    <?= $form->field($model, 'CURP_EMP',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onblur'=>"javascript: errorCurp();this.value=this.value.toUpperCase()"]) ?>

                        <?= $form->field($model, 'RFC_EMP',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onblur'=>"this.value=this.value.toUpperCase()"]) ?>

                        <?= $form->field($model, 'NSS_EMP',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 numeric">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true]) ?>


                    <?= $form->field($model, 'FECHA_NAC_EMP',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'class' => 'form-control datepicker datepicker-upa', 'onchange'=>'calcular_anios(this);', 'onblur'=>'calcular_anios(this);','placeholder'=>'DD/MM/AAAA'])
                    /*->widget(\yii\jui\DatePicker::classname(), [
                        //'language' => 'ru',
                            'dateFormat' => 'yyyy-MM-dd',
                                'clientOptions' => [
                                    'yearRange' => '-115:+0',
                                    'changeYear' => true,
                                    'changeMonth' => true],
                                'options' => [
                                    'class' => 'form-control datepicker',
                                    'onchange'=>'calcular_anios(this);',
                                    'onblur'=>'calcular_anios(this);',
                                    'placeholder'=>'AAAA/MM/DD',
                                    'autocomplete'=>'off'
                                    //'readonly' => 'readonly',
                            ],
                        ]) */
                    ?>

                    <?=
                        $form->field($model, 'FK_GENERO_EMP',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->radioList($datosGenero);
                    ?>
                </div>
          
                <div class="row">
                    <?=
                        $form->field($model, 'TIPO_SANGRE',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true, 'onblur'=>"this.value=this.value.toUpperCase()"])
                    ?>

                    <?= $form->field($model, 'PAIS', 
                    [
                        'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                    ])->widget(Select2::classname(), [
                    'data' => $datosCatPaises,
                    'options' => ['placeholder' => ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    ]); ?>
                <?= $form->field($model, 'ESTADO', 
                    [
                        'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                    ])->widget(Select2::classname(), [
                    'initValueText'=>($extra['DESC_ESTADO'] != ''?$extra['DESC_ESTADO']->DESC_ESTADO:''),
                    // 'data' => $datosCatEstados,
                    'options' => ['placeholder' => ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => $url_estados,
                            'dataType' => 'json',
                            'delay' => 250,
                            'data' => new JsExpression('function(params) { return {q:params.term,p:$("#tblempleados-pais").val()}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(data) { return data.text; }'),
                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                    ],
                    ]); ?>

                <?= $form->field($model, 'MUNICIPIO', 
                    [
                        'template' => ' <div class="'.$colums.'">{label}{input}{error}{hint}<div class="clear"></div></div>',
                    ])->widget(Select2::classname(), [
                    'initValueText'=>($extra['DESC_MUNICIPIO'] != ''?$extra['DESC_MUNICIPIO']->DESC_MUNICIPIO:''),
                    // 'data' => $datosCatMunicipios,
                    'options' => ['placeholder' => ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => $url_municipios,
                            'dataType' => 'json',
                            'delay' => 250,
                            'data' => new JsExpression('function(params) { return {q:params.term,p:$("#tblempleados-pais").val(),e:$("#tblempleados-estado").val()}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(data) { return data.text; }'),
                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                    ],
                    ]); ?>

                </div>

                <div class="row">
                    <?= $form->field($model, 'EMAIL_EMP',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'EMAIL_INTERNO',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'EMAIL_ASIGNADO',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'CELULAR',
                        [
                            'template' => ' <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">{label}{input}{error}{hint}<div class="clear"></div></div>',
                        ]
                        )->textInput(['maxlength' => true]) ?>
                </div>
                </div>
                <div class="row">


                <?= $form->field($model, 'FK_DOMICILIO', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput()->label(false); ?>

                <?= $form->field($model, 'PK_EMPLEADO', 
                    [
                        'template' => '{input}',
                    ]
                    )->hiddenInput()->label(false); ?>

            </div>
            <div class="form-group der">
                <br><br><br><br>
                <input type="hidden" id="datosRepetidos" value="false"/>
                <?= Html::a('Cancelar', Url::to(['empleados/view', 'id' => $model->PK_EMPLEADO]),['class'=>'btn btn-cancel btn-cancel-form']) ?>
                <?= Html::submitButton($model->isNewRecord ? 'GUARDAR' : 'MODIFICAR', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success', 'id'=>'botonGuardar']) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<div class="modal fade" id="fecha-empleado" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p style="text-align: center; font-weight: bold; display:block">
                    El empleado debe ser mayor a 18 años
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-success" data-dismiss="modal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="datos-repetidos" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p style="text-align: center; font-weight: bold; display:block; padding: 20px 10px;" >
                        Los siguientes datos ya han sido utilizados en otros empleados:
                    </p></br>
                    <ul style="list-style-type: square" id="msjRepetidos">

                    </ul>
                    <p style="text-align: center; font-weight: bold; display:block; padding: 20px 10px;" >
                        * Se restablecerán los valores
                    </p>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-success" data-dismiss="modal" id="ocultarModal">ACEPTAR</button>
                    </div>
                </div>
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
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />

                    <p id="mensaje" style="text-align: center; font-weight: bold;">
                    ¿Esta seguro que desea salir sin </br>
                    guardar sus cambios?
                    </p>

                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-cancel">CANCELAR</button>
                    <button type="button" class="btn btn-success cancelar-cambios">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-conexion" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content" style='border-radius: 0px;'>
            <div class="modal-body" style="padding: 0px;">
                <div>
                    <img src="/erteisei_devcap_DES/web/iconos/linea - modal - cancelacion.png" width="100%" height="8px" />
                    <p style="text-align: center; font-weight: bold; padding: 20px 10px;" >
                        La petición no puede ser procesada en este momento, favor de verificar su conexión de internet y contactar a su administrador</br>
                    </p>
                    <div class="row" style="margin: 0px; padding: 0px; text-align: center;">
                    <button type="button" class="btn btn-success" data-dismiss="modal" id="ocultarModal">ACEPTAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

//funcion para calcular edad
function calcular_edad($FECHA_NAC_EMP){
    $dias = explode("-", $FECHA_NAC_EMP, 3);
    $dias = mktime(0,0,0,$dias[1],$dias[0],$dias[2]);
    $edad = (int)((time()-$dias)/31556926 );
    return $edad;
}
// Formato: dd-mm-yy
//echo calcular_edad("01-10-1989"); // Resultado: 21
//fin de funcion

function validarDatos(curp, nss, rfc){
    var validacion = false;
        $.ajax({
            url: '<?php echo Yii::$app->request->baseUrl. '/empleados/validar_campos' ?>',
            type: 'post',
            async: false,
            data: {
                curp: curp , 
                nss: nss,
                rfc: rfc,
                pk_empleado: '<?= $model->PK_EMPLEADO ?>',
                _csrf : '<?=Yii::$app->request->getCsrfToken()?>'
            },
            success: function (data) {
                console.log(data);
                validacion = data;
            },
            error: function(data){
                validacion = 'error';
            }
        });
    return validacion;
}

function errorCurp(){
    var post = false;
    var curp = $("#tblempleados-curp_emp").val().toUpperCase();
    
    var paterno1st = $("#tblempleados-apellido_pat_emp").val();
        paterno1st = paterno1st.replace("LAS ","");
        paterno1st = paterno1st.replace("DEL ","");
        
    var paterno  = paterno1st.replace("LA ","");
        paterno = paterno.replace("DE ","");
        paterno = paterno.replace("Y ","");   
     
    while(paterno[0] == " "){
        paterno = paterno.substr(1, paterno.length - 1);
    }
    
    var materno1st = $("#tblempleados-apellido_mat_emp").val();
    var materno1st = materno1st.replace("LAS ","");
        materno1st = materno1st.replace("DEL ","");
        materno1st = materno1st.replace("DE ","");
        
    var materno  = materno1st.replace("LA ","");
        materno = materno.replace("Y ","");
                
    while(materno[0] == " "){
        materno = materno.substr(1, materno.length - 1);
    }
    
    var nombre = $("#tblempleados-nombre_emp").val();
    var op_paterno = paterno.length;
    var vocales = /^[aeiou]/i;
    var alfabeto = /^[abcdefghijklmnoñpqrstuvwxyz]/i;
    
    var s1 = '';
    var s2 = '';

    var i = 0;
    var x= true;
    var z = true;

    while(i < op_paterno){
        if((alfabeto.test(paterno[i]) == true) & (x != false)){
            s1 = s1 + paterno[i];
            paterno = paterno.replace(paterno[i],"");
            x=false;
        }
        
        if((vocales.test(paterno[i]) == true) & (z != false)){
            s2 = s2 + paterno[i];
            paterno = paterno.replace(paterno[i],"");
            z=false;
        }
        i++;
    }

    var s3 = materno[0];
    var s4 = nombre[0];

    /*Cuando el nombre sea compuesto (formado por dos o más palabras), la clave se construye con la letra inicial de la primera
    palabra, siempre que no sea MARIA, MA., MA, o JOSE, J, J. en cuyo caso se utilizará la segunda palabra.
    Ejemplos: MARIA LUISA PEREZ HERNANDEZ PEHL
              LUIS ENRIQUE ROMERO PALAZUELOS ROPL*/
    var segundoNombre = '';
    var validNombreCompuesto = nombre.split(" ");

    if(validNombreCompuesto.length > 1){
        segundoNombre = 
            validNombreCompuesto[0] == "JOSE" ||  validNombreCompuesto[0] == "J" || validNombreCompuesto[0] == "J." || 
            validNombreCompuesto[0] == "MARIA" || validNombreCompuesto[0] == "MA" || validNombreCompuesto[0] == "MA." ? 
            validNombreCompuesto[1] : validNombreCompuesto[0];
        
        s4 = segundoNombre[0];
    }
    
    var nomCurp = curp.substring(0,4);
    var nomComp = s1+s2+s3+s4;

    //Validación para detectar que campo CURP no este vacio.
    if(curp == ''){
        $(".field-tblempleados-curp_emp").addClass('has-error').find('.help-block').html('C.U.R.P. no puede estar vacío.');
        post = false;
    }else{
        post = true;
        //Validación para detectar que campo CURP tenga un máximo de 18 caracteres.
        if(curp.length !== 18){
        $(".field-tblempleados-curp_emp").addClass('has-error').find('.help-block').html('La longitud del campo "CURP" es incorrecto.');
        post = false;
        }else{
            post = true;
            //Validación para detectar que campo CURP tenga la estructructrau correcta.
            var er = /^([A-Z][AEIOUX][A-Z]{2}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[hHmM]{1}[a-zA-Z]{5}([0-9A-Z]{2}))$/,
            validado = curp.match(er);
            if(!validado){
                $(".field-tblempleados-curp_emp").addClass('has-error').find('.help-block').html('El formato del campo "CURP" es incorrecto.');
                post = false;
            }else{
                post = true;
                //Validación para detectar que CURP ingresado correesponda con el nombre y apellidos capturados.
                if(nomComp !== nomCurp){
                    $(".field-tblempleados-curp_emp").addClass('has-error').find('.help-block').html('El CURP no corresponde al nombre capturado');
                    post = false;
                }else{
                    post = true;
                    
                }
            }
        }
    }
    if(post == true){
        $(".field-tblempleados-curp_emp").removeClass('has-error').find('.help-block').html('');
        $(".field-tblempleados-curp_emp").addClass('has-success');
    }
    return post;
}

$("#botonGuardar").click(function (){
    var curp = $("#tblempleados-curp_emp").val();
    var nss = $("#tblempleados-nss_emp").val();
    var rfc = $("#tblempleados-rfc_emp").val();

    //Datos originales
    var curp_original = "<?= $model->CURP_EMP ?>";
    var nss_original = "<?= $model->NSS_EMP ?>";
    var rfc_original = "<?= str_replace('-','',$model->RFC_EMP) ?>";

    $("#datosRepetidos").val('false');
    $("#msjRepetidos").html('');

    var post = true;
    var errorCurp = '<li style="font-weight: bold;">CURP</li>';
    var errorNss = '<li style="font-weight: bold;">NSS</li>';
    var errorRfc = '<li style="font-weight: bold;">RFC</li>';    

    curp = (curp==curp_original?'':curp);
    nss = (nss==nss_original?'':nss);
    rfc = (rfc==rfc_original?'':rfc);

    var validador = validarDatos(curp,nss,rfc);
    console.log(validador);
    if(validador!='error'){//Valida si se tiene conexión a internet
        //Valida CURP
        if(validador.curpRepetido == true){
        $("#tblempleados-curp_emp").val(curp_original);
        $("#msjRepetidos").append(errorCurp);
        $("#datosRepetidos").val('true');
    }

    //Valida NSS
    if(validador.nssRepetido == true){
        $("#tblempleados-nss_emp").val(nss_original);
        $("#msjRepetidos").append(errorNss);
        $("#datosRepetidos").val('true');
    }

    //Valida RFC
    if(validador.rfcRepetido == true){
        $("#tblempleados-rfc_emp").val(rfc_original);
        $("#msjRepetidos").append(errorRfc);
        $("#datosRepetidos").val('true');
    }

        if($("#datosRepetidos").val() == 'true'){
            post = false;
            $("#datos-repetidos").modal('show');
        }
    } else {
        post = false;
        $("#modal-conexion").modal('show');
    }   
    return post;
});


    jQuery(document).ready(function(){
        $(".field-tblempleados-curp_emp").addClass('required');
        $("#tblempleados-curp_emp").prop("maxlength","18");
        //var todoVacio = true;
        //habilitarGuardar();
        $("#tblempleados-rfc_emp").inputmask("aaaa-999999-***");
        
        $('#form_empleados input').keyup(function(){
            guardarActivar(form_empleados());
        });
        $('#form_empleados input').blur(function(){
            guardarActivar(form_empleados());
        });
        $('#form_empleados select').on('change', function(){
            guardarActivar(form_empleados());
        });
        if($('#form_empleados').length>0){
            guardarActivar(todoVacio);
            
        }

        $('#tblempleados-pais').on('change', function(){

            if($('#tblempleados-pais').val() == '' || $('#tblempleados-pais').val() == null){

                $("#tblempleados-estado").select2("val", "");
                $("#tblempleados-municipio").select2("val", "");
            }else{
                $("#tblempleados-estado").select2("val", "");
                $("#tblempleados-municipio").select2("val", "");
            }
            
        });

        $('#tblempleados-estado').on('change', function(){

            if($('#tblempleados-estado').val() == '' || $('#tblempleados-estado').val() == null){

                $("#tblempleados-municipio").select2("val", "");
            }else{
                $("#tblempleados-municipio").select2("val", "");
            }
            
        });
      
    });


    var form_empleados = function(){
                
        var elementos = jQuery('#form_empleados input, #form_empleados select');
        
        jQuery(elementos).each(function(index, el){
            var elemento=$(el)
            if(elemento.attr('name')!='_csrf'){
                if($.trim(elemento.val())==''){
                    todoVacio=true;
                    return todoVacio;
                }else{
                    todoVacio=false;
                    return todoVacio;
                }
            }
            //
            
            
        })
        return todoVacio        

    }
</script>