<div id="ftOrders-page" class="content-page" style="display:none;">
  <div class="container-fluid">
    
    <!-- Content Header (Page header) -->
    <section class="content-header" style="padding:2px;">
      <h1>
        FLOW-THROUGH ORDERS
      </h1>
      <ol class="breadcrumb" style="padding-top: 0px">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Flow-Through Orders</li>
      </ol>
    </section>

    <!-- ORD -->
    <div class="col-md-12">
      <div class="row">
        <div class="col-sm-5" style="width:46%">
  
          <!-- ORD FORM -->
          <?php include __DIR__ . '/forms/ftOrders-ordForm.html'; ?>
  
        </div>
        <div class="col-sm-7" style="width:54%">
          
          <!-- ORD TABLE -->
          <?php include __DIR__ . '/tables/ftOrders-ordTable.html'; ?>
  
        </div>
      </div>
    </div>

    <br>

    <!-- CKT -->
    <div class="col-md-12">
      <div class="row">
        <div class="col-sm-5 ftOrders-form" style="width:46%">
  
          <!-- CKT FORM -->
          <?php include __DIR__ . '/forms/ftOrders-cktForm.html'; ?>
  
        </div>
        <div class="col-sm-7" style="width:54%">
          
          <!-- CKT TABLE -->
          <?php include __DIR__ . '/tables/ftOrders-cktTable.html'; ?>
  
        </div>
      </div>
    </div>

    <br>

    <!-- FAC -->
    <div class="col-md-12">
      <div class="row">
        <div class="col-sm-5 ftOrders-form" style="width:46%">

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
</div>

<script type="text/javascript">
  // modify padding for input fields in cktForm
  $(".ftOrders-cktForm-input").parent().parent().css('padding-right', "0px");

  // FtOrders menu item click event
  var ftOrdersFirstLoad = true;
  $(".menu-item[page_id='ftOrders-page']").click(function() {
    if (ftOrdersFirstLoad != true) {
      return;
    }
    // load ord table upon visiting page
    queryFtOrdersOrd();
    ftOrdersFirstLoad = false;
  });
</script>