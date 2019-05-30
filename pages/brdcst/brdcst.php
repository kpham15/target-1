<div id="brdcst-page" class="content-page" style="display:none;">
  <div class="container-fluid">
    
    <!-- Brdcst Forms -->
    <?php include __DIR__ . '/brdcst-forms.html'; ?>

    <!-- Brdcst Table -->
    <?php include __DIR__ . '/brdcst-table.html'; ?>

    <!-- Brdcst Modals -->
    <?php include __DIR__ . '/brdcst-modals.html'; ?>
  </div>
</div>

<script type="text/javascript">
  function loadOwners() {
    $.ajax({
      type: 'POST',
      url: ipcDispatch,
      data: {
        "api": "ipcUser",
        "act": "queryBrdcstOwner",
        "user": user.uname
      },
      dataType: 'json'
    }).done(function(data) {
      let res = data.rows;
      let modal = {
        title: data.rslt,
        body: data.reason
      }

      if (data.rslt === 'fail') {
        modal.type = "danger",
        modalHandler(modal);
      } else {
        createMsgOwners(res);
      }
    });
  }

  function createMsgOwners(data) {
    var a = [];
    a.push('<option value=""></option>');
    
    data.forEach(function(owner) {
      let html =  '<option value="'+owner.uname+'">' +
                    owner.fname + ' ' + owner.mi + ' ' + owner.lname +
                  '</option>';

      a.push(html);
    });

    $('#brdcst-modal-msgowner').html(a.join(''));
  }
  
  function clearErrors() {
    $('.help-block').text('');
    $('.form-group').removeClass('has-error');
  }
</script>