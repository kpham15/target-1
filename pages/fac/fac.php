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

  function loadFtyps() {
    $.ajax({
      type: 'POST',
      url: "./em/ipcDispatch.php",
      data: {
        api: "ipcOpt",
        act: "queryFtyp",
        user: user.uname,
      },
      dataType: 'json'
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
        createFtypOptions(res);
      }
    });
  }

  function createFtypOptions(data) {
    var a = [];
    a.push('<option value=""></option>');

    data.forEach(function(ftyp) {
      let html = `<option value="${ftyp.alias}">${ftyp.alias}</option>`;
      a.push(html);
    });

    $("#fac-form-ftyp").html(a.join(''));
    $("#fac-modal-ftyp").html(a.join(''));
  }
</script>