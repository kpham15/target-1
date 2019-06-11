<div id="setup-maint-page" class="content-page" style="display:none;">
  <div class="container-fluid">
    
     <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Setup Maintenance
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Setup Maintenance</li>
        </ol>
      </section>

      <!-- Setup Maint Forms -->
      <?php include __DIR__ . '/setup-maint-forms.html'; ?>

      <!-- Setup Maint Table Ckts -->
      <?php include __DIR__ . '/setup-maint-table-ckts.html';?>

      <!-- Setup Maint Modal -->
      <?php include __DIR__ . '/setup-maint-modal.html';?>

  </div>
</div>

<script type="text/javascript">
  function maintLoadFacY() {
    $.ajax({
      type:     'POST',
      url:      ipcDispatch,
      data:     {
        "api":  "ipcProv",
        "act":   "queryFacY",
        "user":  user.uname,
      },
      dataType:  'json'
    }).done(function(data) {
      let res = data.rows;
      let modal = {
        header: data.rslt,
        body:   data.reason,
      }

      if (data.rslt == 'fail') {
        modal.type = 'danger';
        modalHandler(modal);
      }
      else {
        let a = [];
        a.push('<option value=""></option>');

        for (let i=0; i<res.length; i++) {
          a.push('<option>' + data.rows[i].fac + '</option>');
        }

        $('#setup-maint-modal-conn1-tfac').empty();
        $('#setup-maint-modal-conn1-tfac').html(a.join(""));


      }
    })
  }

  function maintLoadFacX() {
    $.ajax({
      type:       'POST',
      url:        ipcDispatch,
      data:       {
        "api":    "ipcProv",
        "act":    "queryFacX",
        "user":   user.uname,
      },
      dataType:   'json'
    }).done(function(data) {
      let res = data.rows;
      let modal = {
        header:   data.rslt,
        body:     data.reason,
      }

      if (data.rslt == 'fail') {
        modal.type = 'danger';
        modalHandler(modal);
      }
      else {
        let a = [];
        a.push('<option value=""></option>');

        for (let i=0; i<res.length; i++) {
          a.push('<option>' + data.rows[i].fac + '</option>');
        }

        $('#setup-maint-modal-conn2-ffac').empty();
        $('#setup-maint-modal-conn2-ffac').html(a.join(""));
      }
    })
  }
</script>