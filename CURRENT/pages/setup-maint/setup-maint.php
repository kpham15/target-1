<div id="setup-maint-page" class="content-page" style="display:none;">
  <div class="container-fluid">
    
     <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Setup Maintenance Connection
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Setup Maintenance Connection</li>
        </ol>
      </section>

      <!-- Setup Maint Forms -->
      <?php include __DIR__ . '/setup-maint-forms.html'; ?>

      <!-- Setup Maint Table Ckts -->
      <?php include __DIR__ . '/setup-maint-table-ckts.html';?>

      
    </div>
  </div>
  <!-- Setup Maint Modal -->
  <?php include __DIR__ . '/setup-maint-modal.html';?>

<script type="text/javascript">

  function maintLoadTestPort(fac, side) {
    $.ajax({
      type:       'POST',
      url:        ipcDispatch,
      data:       {
        "api":    "ipcFacilities",
        "act":    "queryTestFac",
        "fac":    fac,
        "user":   user.uname
      },
      dataType:   'json',
    }).done(function(data) {
      let res = data.rows;
      
      if (data.rslt == 'fail') {
        $('#setup-maint-forms-post-response-text').css('color','red').text(data.rslt + ' - ' + data.reason);
      }
      else {
        var a = [];
        a.push('<option value=""></option>');
        for (let i=0; i< res.length; i++) {
          a.push('<option>' + res[i].fac + '</option>');
        }

        if (side == 'X') {
          $('#setup-maint-modal-test-port1').empty();
          $('#setup-maint-modal-test-port1').html(a.join(""));
        }
        if (side == 'Y') {
          $('#setup-maint-modal-test-port2').empty();
          $('#setup-maint-modal-test-port2').html(a.join(""));
        }
      }
    })

  }

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

      if (data.rslt == 'fail') {
        $('#setup-maint-forms-post-response-text').css('color','red').text(data.rslt + ' - ' + data.reason);
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

      if (data.rslt == 'fail') {
        $('#setup-maint-forms-post-response-text').css('color','red').text(data.rslt + ' - ' + data.reason);
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