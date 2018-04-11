<div id="modal" class="modal fade" role="dialog"> 
  <div class="modal-dialog"> 
    <div class="modal-content"> 
      <div class="modal-header"> 
        <h4 class="modal-title">Datos de la tarjeta</h4> 
      </div> 
        <div class="modal-body"> 
         <form action="" method="POST" id="card-form">
             <div class="row">
               <div class="col-md-12 input-group">
                 <label for="">Nombre del propietario</label> 
                 <input type="text" class="form-control col-md-6" data-conekta="card[name]"> 
               </div>
             </div>
             <div class="row form-group">
               <div class="col-md-12 input-group">
                 <label for="">Numero de tarjeta</label> 
                <input type="text" class="form-control" data-conekta="card[number]"> 
               </div>
             </div>
             <div class="row form-group">
               <div class="col-md-4">
                  <label for="">CVC</label> 
                  <input type="text" class="form-control" data-conekta="card[cvc]" placeholder="123">
               </div>
               <div class="col-md-4">
                  <label for="fecha">Mes de expiración</label>
                  <select name="" id="fecha" class="form-control" data-conekta="card[exp_month]">
                    <option value="" selected disabled>Mes</option>
                    <option value="01">01</option>
                    <option value="02">02</option>
                    <option value="03">03</option>
                    <option value="04">04</option>
                    <option value="05">05</option>
                    <option value="06">06</option>
                    <option value="07">07</option>
                    <option value="08">08</option>
                    <option value="09">09</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                  </select>
               </div>
               <div class="col-md-4">
                  <label for="fecha">Año de expiración</label>
                  <input id="fecha" type="text" placeholder="YYYY" class="form-control" data-conekta="card[exp_year]"> 
               </div>
             </div>
             <div class="row form-group">
               <div class="col md-12 input-group">
                  <span class="input-group-addon">$</span>
                  <input type="text" class="form-control" placeholder="Cantidad en pesos MXN">
                  <span class="input-group-addon">.00</span>
               </div>
             </div>
             <button type="submit" class="btn btn-success">Donar!</button> 
             <span class="card-errors text-danger text-center"></span>
           </form> 
             

           </div>
            <div class="modal-footer">
              
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
       
            
</div>
</div>
</div>