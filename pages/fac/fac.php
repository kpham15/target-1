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

  function loadOptions(action, type, element) {
    $.ajax({
      type: 'POST',
      url: "./em/ipcDispatch.php",
      data: {
        api: "ipcOpt",
        act: action,
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
        createFtypOptions(res, type, element);
      }
    });
  }

  function createFtypOptions(data, type, element) {
    var a = [];
    a.push('<option value=""></option>');

    data.forEach(function(option) {
      let html = `<option value="${option[type]}">${option[type]}</option>`;
      console.log(html);
      a.push(html);
    });

    element.html(a.join(''));
  }
</script>