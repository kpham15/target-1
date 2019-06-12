<div id="matrix-page" class="content-page" style="display:none;">
  <div class="container-fluid">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Lock/Unlock Matrix Cards and Nodes
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Lock/Unlock Matrix Cards and Nodes</li>
      </ol>
    </section>

    <div class="col-md-5">
      <!-- MATRIX FORM -->
      <?php include __DIR__ . '/matrix-forms.html'; ?>

      <!-- VIEW PORT TABLE -->
      <?php include __DIR__ . '/tables/matrix-viewport-table.html'; ?>

    </div>

    <div class="col-md-7">
      <!-- MATRIX CARDS TABLE -->
      <?php include __DIR__ . '/tables/matrix-table.html'; ?>

    </div>

    <!-- MATRIX MODAL -->
    <?php include __DIR__ . '/matrix-modals.html'; ?>

  </div>
</div>

<script type="text/javascript">

  // A flag to check if it is first time loading, primary use is for click event for matrix menu item
  var matrixFirstLoad = true;

  // FAC menu item click event
  $(".menu-item[page_id='matrix-page']").click(async function() {
    if (matrixFirstLoad != true) {
      return;
    }

    // load matrix table upon visiting page
    queryFac();

    matrixFirstLoad = false;
  });
  
</script>