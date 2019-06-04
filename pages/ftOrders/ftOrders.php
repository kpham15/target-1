<div id="ftOrders-page" class="content-page" style="display:none;">
  <div class="container-fluid">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        FLOW-THROUGH ORDERS
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Flow-Through Orders</li>
      </ol>
    </section>

    <!-- ORD -->
    <div class="col-md-12">
      <div class="col-sm-5" style="width:46%">

        <!-- ORD FORM -->
        <?php include __DIR__ . '/forms/ftOrders-ordForm.html'; ?>

      </div>
      <div class="col-sm-7" style="width:54%">
        
        <!-- ORD TABLE -->
        <?php include __DIR__ . '/tables/ftOrders-ordTable.html'; ?>

      </div>
    </div>

    <br>

    <!-- CKT -->
    <div class="col-md-12">
      <div class="col-sm-5" style="width:46%">

        <!-- CKT FORM -->
        <?php include __DIR__ . '/forms/ftOrders-cktForm.html'; ?>

      </div>
      <div class="col-sm-7" style="width:54%">
        
        <!-- CKT TABLE -->
        <?php include __DIR__ . '/tables/ftOrders-cktTable.html'; ?>

      </div>
    </div>

    <br>

    <!-- FAC -->
    <div class="col-md-12">
      <div class="col-sm-5" style="width:46%">

        <!-- FAC FORM -->
        <?php include __DIR__ . '/forms/ftOrders-facForm.html'; ?>

      </div>
      <div class="col-sm-7" style="width:54%">
        
        <!-- FAC TABLE -->
        <?php include __DIR__ . '/tables/ftOrders-facTable.html'; ?>

      </div>
    </div>



  </div>
</div>

<script type="text/javascript">
  $(".ftOrders-cktForm-input").parent().parent().css('padding-right', "0px");
</script>