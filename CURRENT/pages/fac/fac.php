<div id="fac-page" class="content-page" style="display:none;">
  <div class="container-fluid">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        SETUP FACILITIES
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Setup Facilities</li>
      </ol>
    </section>

    <!-- Fac Forms -->
    <?php include __DIR__ . '/fac-forms.html'; ?>

    <!-- Fac Table -->
    <?php include __DIR__ . '/fac-table.html'; ?>

    <!-- Fac Modal -->
    <?php include __DIR__ . '/fac-modals.html'; ?>

  </div>
</div>

<script type="text/javascript">

  // A flag to check if it is first time loading, primary use is for click event for fac menu item
  var facFirstLoad = true;

  // FAC menu item click event
  $(".menu-item[page_id='fac-page']").click(function() {
    $("#fac-page select").css('-webkit-appearance', 'menulist');
    $("#fac-page select:disabled").css('-webkit-appearance', 'none');
    if (facFirstLoad != true) {
      return;
    }
    // loads options for ftyp, ort, spcfnc selection fields in setup facility
    loadFacOptions("queryFtyp", "ftyp", createFacOptions);
    loadFacOptions("queryOrt", "ort", createFacOptions);
    loadFacOptions("querySpcfnc", "spcfnc", createFacOptions);

    // load fac table upon visiting page
    queryFac();

    facFirstLoad = false;
  });
  
  function loadFacOptions(action, type, callback) {
    $.ajax({
      type: 'POST',
      url: "./em/ipcDispatch.php",
      data: {
        api: "ipcOpt",
        act: action,
        user: user.uname,
      },
      dataType: 'json',
    }).done(function(data) {
      let res = data.rows;
      let modal = {
        title: data.rslt,
        body: data.reason
      }

      if (data.rslt === "fail") {
        modal.type = 'danger',
        modalHandler(modal);
      } else {
        callback(res, type);
      }
    });
  }

  function createFacOptions(data, type) {
    var a = [];
    a.push('<option value=""></option>');
    
    data.forEach(function(option) {
      let html = `<option value="${option[type]}">${option[type]}</option>`;
      a.push(html);
    });
    
    $('#fac-form-'+type).html(a.join(''));
    $('#fac-modal-'+type).html(a.join(''));
  }
</script>