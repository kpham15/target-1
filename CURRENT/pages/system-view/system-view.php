<div id="system-view-page" class="content-page active-page" style="display:none;">
  <!-- Content Header (Page header) -->
  <!-- <section class="content-header">
    <h1>
      System View -->
      <!-- <small>Preview page</small> -->
    <!-- </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">System View</li>
    </ol>
  </section> -->

  <!-- Main content -->
  <section class="content">
    <!-- Find CKID Section -->
    <?php include __DIR__ . "/find-ckid.html"; ?>
    <?php include __DIR__ . "/find-fac.html"; ?>
    <?php include __DIR__ . "/find-conn.html"; ?>
    <?php include __DIR__ . '/view-path.html'; ?>





    <!-- =========================================================== -->

    <div class="row">
      <div class="col-md-6">
        <div id="node-x-table" class="nav-tabs-custom">
          <ul id="node-x-tabs" class="nav nav-tabs node-tabs" ptyp="x">
            <!-- Node tabs for X side created dynamically -->
          </ul>
          <div id="mio-x-table" class="tab-content mio-tabs">
            <div class="tab-pane active" id="nodex">
              <div class="container-fluid">
                <div class="row">
                  <div id="miox-btn-group" class="mio-btn-group btn-group">
                    <!-- MIO buttons created dynamically -->
                  </div>
                </div>
                <div class="row">
                  <div class="btn-group port-range-btns" ptyp="x">
                    <!-- Port range buttons created dynamically -->
                  </div>
                </div>
                <div id="x-port-grid" class="row port-grid" ptyp="x">
                  <!-- Port boxes created dynamically -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div id="node-y-table" class="nav-tabs-custom">
          <ul id="node-y-tabs" class="nav nav-tabs node-tabs" ptyp="y">
            <!-- Node tabs for Y side created dynamically -->
          </ul>
          <div id="mio-y-table" class="tab-content mio-tabs">
            <div class="tab-pane active" id="nodey">
              <div class="container-fluid">
                <div class="row">
                  <div id="mioy-btn-group" class="mio-btn-group btn-group">
                    <!-- MIO buttons created dynamically -->
                  </div>
                </div>
                <div class="row">
                  <div class="btn-group port-range-btns" ptyp="y">
                    <!-- Port range buttons created dynamically -->
                  </div>
                </div>
                <div id="y-port-grid" class="row port-grid" ptyp="y">
                  <!-- Port boxes created dynamically -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>

<script type="text/javascript">

  // UPON LOAD PAGE FIRST TIME
  var sysViewFirstLoad = true;
  $(".menu-item[page_id='system-view-page']").click(async function() {
    if (sysViewFirstLoad != true) {
      return;
    }
    $("#header-bulletinBoard-modal").modal('show');
    sysViewFirstLoad = false;
  });

  function sysviewStartup() {
    // Create the Node tabs according to amount of nodes
    // @TODO
    // Might need to add this bit of code to updates of nodes
    // if node information can change and affect tabs
    nodeInfo.forEach(function(node) {
      let nodeXTab = createNodeTabs(node, 'x');
      let nodeYTab = createNodeTabs(node, 'y');
      $('#node-x-tabs').append(nodeXTab);
      $('#node-y-tabs').append(nodeYTab);
    });
    $('.node-tab[node_id="1"]').addClass('active');

    // Create MIO buttons according to data from first node (the initial active node)
    let node1 = nodeInfo.findIndex(node => node.node === '1');
    nodeInfo[node1].MIOX.forEach(function(psta, i) {
      let mioBtn = createMioBtn(psta, i, 'x');
      $('#miox-btn-group').append(mioBtn);
    });
    nodeInfo[node1].MIOY.forEach(function(psta, i) {
      let mioBtn = createMioBtn(psta, i, 'y');
      $('#mioy-btn-group').append(mioBtn);
    });
    $('.mio-btn[slot="1"]').addClass('active');
    
    // Create port grid template
    for (let i = 1; i <= 25; i++) {
      let portBox = createPortBox(i);
      $('.port-grid').append(portBox);
    }

    // Query for initial X & Y port info
    queryAndUpdatePorts(1, 1, 'x');
    queryAndUpdatePorts(1, 1, 'y');
  }

  function queryAndUpdatePorts(node, slot, ptyp) {
    $.ajax({
      type: 'POST',
      url: ipcDispatch,
      data: {
        "api":      "ipcPortmap",
        "act":      "QUERYMIO",
        "user":     "SYSTEM",
        "node":     node,
        "slot":     slot,
        "ptyp":     ptyp
      },
      dataType: 'json'
    }).done(function(data) {
      let res = data.rows;
      let modal = {};

      if (data.rslt == "fail") {
        modal.title = data.rslt;
        modal.body = data.reason;
        modal.type = 'danger';
        modalHandler(modal);
      } else {
        if (ptyp === 'x') {
          portX = res;
        } else if (ptyp === 'y') {
          portY = res;
        }

        updatePortRangeBtns(ptyp);
        updatePortGrid(ptyp);

        //highlight the found ports
        highlightPorts(ptyp, portHighLight);
        
      }
    });
  }

  function updatePortGrid(ptyp) {
    let grid = $('.port-grid[ptyp="'+ptyp+'"]');
    let index = $('.port-range-btn.active[ptyp="'+ptyp+'"]').attr('index');
    let calculated = 25*index;
    let portArray = [];
    let color = '';

    if (ptyp === 'x') {
      portArray = portX.filter(function(port) {
        if (port.pnum >= 1+calculated && port.pnum <= 25+calculated) {
          return true;
        } else {
          return false;
        }
      });
    } else if (ptyp === 'y') {
      portArray = portY.filter(function(port) {
        if (port.pnum >= 1+calculated && port.pnum <= 25+calculated) {
          return true;
        } else {
          return false;
        }
      });
    }
    
    portArray.forEach(function(port) {
      let gridNum = port.pnum - calculated;
      let selector = '.port-box[grid_num="'+gridNum+'"]';
      
      switch(port.psta) {
        case "SF":
          color = 'bg-aqua';
          break;
        case "LCK":
          color = 'bg-critical';
          break;
        case "CONN":
          color = 'bg-green';
          break;
        case "MTCD":
          color = 'bg-minor';
          break;
        case "MAINT":
          color = 'bg-major';
          break;
        default:
          color = 'bg-gray-active';
      }

      grid.find(selector).removeClass(function(i, className) {
        return (className.match (/(^|\s)bg-\S+/g) || []).join(' ');
      });
      grid.find(selector).removeClass('disabled');
      grid.find(selector).addClass(color);
      grid.find(selector+' .port-num').text(port.port === '' ? '-' : port.port);
      grid.find(selector+' .port-psta').text(port.psta === '' ? '-' : port.psta);
      grid.find(selector+' .fac-num').text(port.fac === '' ? '-' : port.fac);
      grid.find(selector+' .fac-type').text(port.ftyp === '' ? '-' : port.ftyp);
      grid.find(selector+' .port-ckid').text(port.ckid === '' ? '-' : port.ckid);
    });
    
  }

  function updatePortRangeBtns(ptyp) {
    let amount = Math.ceil(portX.length / 25);
    let portBtns = $('.port-range-btns[ptyp="'+ptyp+'"]');
    let html = '';

    if (portBtns.children().length > 0) {
      if (portBtns.children().length > amount) {
        amount = portBtns.children().length - amount;
        for (let i = 0; i < amount; i++) {
          portBtns.children().last().remove();
        }
      } else if (portBtns.children().length < amount) {
        for (let i = portBtns.children().length-1; i < amount; i++) {
          let calculated = 25*i;
          html = '<button type="button" class="btn btn-default port-range-btn" ptyp="'+ptyp+'" index="'+i+'">'+
                    (1+calculated) + '-' + (25+calculated) +
                  '</button>';
          
          portBtns.append(html);
        }
      }
    } else {
      for (let i = 0; i < amount; i++) {
        let calculated = 25*i;
        html = '<button type="button" class="btn btn-default port-range-btn" ptyp="'+ptyp+'" index="'+i+'">'+
                    (1+calculated) + '-' + (25+calculated) +
                  '</button>';

        portBtns.append(html);
      }

      $('.port-range-btn[index="0"]').addClass('active');
    }
    
    return;
  }

  function createPortBox(gridNum) {
    let portBox = '<div class="dropdown port-box info-box bg-gray-active disabled" grid_num="'+gridNum+'">' +
                    '<button data-toggle="dropdown" id="dropdown'+gridNum+'">' +
                      '<div class="info-box-text">' +
                        '<span class="port-num">-</span>' +
                        '<span class="port-psta pull-right">-</span>' +
                      '</div>' +
                      '<div class="info-box-text">' +
                        '<span class="fac-num">-</span>' +
                        '<span class="fac-type pull-right">-</span>' +
                      '</div>' +
                      '<div class="info-box-text text-center">' +
                        '<span class="port-ckid">-</span>' +
                      '</div>' +
                    '</button>' +
                    '<ul class="dropdown-menu" aria-labelledby="dropdown'+gridNum+'">' +
                      '<li class="mt-disconnect"><a>MT_DISCONNECT</a></li>' +
                      '<li class="mt-restore"><a>MT_RESTORE</a></li>' +
                      '<li class="restore-mtcd"><a>RESTORE_MTCD</a></li>' +
                      '<li class="mt-test"><a>MT_TEST</a></li>' +
                    '</ul>' +
                  '</div>';

    return portBox;
  }

  function createMioBtn(psta, index, ptyp) {
    let slot = index + 1;
    let mioBtn = `<div class="dropdown" style="float: left">
                    <button type="button" class="mio-btn btn btn-default" data-toggle="dropdown" slot="${slot}" ptyp="${ptyp}">
                      <p>MIO${ptyp.toUpperCase()}-${slot}<br/><span class="mio-psta">${psta}</p>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-menu-lock-card">LOCK-CARD</a></li>
                      <li><a class="dropdown-menu-unlock-card">UNLOCK-CARD</a></li>
                      <li><a class="dropdown-menu-view-path">VIEW PATH</a></li>
                    </ul>
                  </div>`;

    return mioBtn;
  }

  function createNodeTabs(node, ptyp) {
    // HTML template for node tab
    let nodeTab = '<li class="node-tab" node_id="'+node.node+'" ptyp="'+ptyp+'">' +
                    '<a href="#node'+ptyp+'" data-toggle="tab">Node '+node.node+'</a>' +
                  '</li>';

    // Return html string
    return nodeTab;
  }

  function updateMxcInfo() {
    let nodeX = $(".node-tab.active[ptyp='x']").attr("node_id");
    let slotX = $(".mio-btn.active[ptyp='x']").attr("slot");
    let nodeY = $(".node-tab.active[ptyp='y']").attr("node_id");
    let slotY = $(".mio-btn.active[ptyp='y']").attr("slot");
    //if sys-view not ready, don't do anything
    if(nodeX == undefined || slotX == undefined || nodeY == undefined || slotY == undefined)
      return;
    //update miox
    nodeInfo.filter(function(item) {
      return item.node == nodeX
    })[0].MIOX.forEach(function(psta,i){
      let slotId = i +1;
      $(".mio-btn[slot='"+slotId+"'][ptyp='x']").find('span').html(psta);

    });
    //update mioy
    nodeInfo.filter(function(item) {
      return item.node == nodeY
    })[0].MIOY.forEach(function(psta,i){
      let slotId = i +1;
      $(".mio-btn[slot='"+slotId+"'][ptyp='y']").find('span').html(psta);
    })
    
    //update ports
    queryAndUpdatePorts(nodeX,slotX,'x')
    queryAndUpdatePorts(nodeY,slotY,'y')


  }

  function highlightPorts(portType, portHighLight) {
    for(let i=0; i<portHighLight.length; i++) {
      let port = portHighLight[i];
      let portExtract = port.split('-');
      let ptyp = portExtract[2].toLowerCase();
      // if not in the same displayed side, return
      if(ptyp !== portType)
        return;

      let node = portExtract[0];
      let slot = portExtract[1];
      let pnum = portExtract[3];
      let index = Math.floor((pnum-1)/25);
      if(pnum > 25) 
          portGrid_id = pnum - 25;
      else 
          portGrid_id = pnum;
      
      if($(".node-tab.active[ptyp='"+ptyp+"']").attr('node_id') == node &&
        $(".mio-btn.active[ptyp='"+ptyp+"']").attr('slot') == slot && 
        $(".port-range-btn.active[ptyp='"+ptyp+"']").attr('index') == index
        ) {
          $(".port-grid[ptyp='"+ptyp+"'] > .port-box").removeClass('addBorder') 
          $(".port-grid[ptyp='"+ptyp+"'] > .port-box[grid_num='"+portGrid_id+"']").addClass('addBorder')
          //empty the portHighLight data
          portHighLight.splice(i,1);
        } 
    }
  }

  function sysviewMtcPopulateModal(ckid, mtcAction) {
    $.ajax ({
      type:       'POST',
      url:        ipcDispatch,
      data:       {
        "api":    "ipcProv",
        "act":    "queryCktconByCkid",
        "user":   user.uname,
        "ckid":   ckid
      },
      dataType:   'json',
    }).done(function(data) {
      let res = data.rows;

      if (mtcAction == 'MTC_DISCON' || 
          mtcAction == 'MTC_RESTORE') {
        $('#setup-maint-modal-ckid').val(ckid);
        $('#setup-maint-modal-ckid-tn').val(ckid);
        $('#setup-maint-modal-cls').val(res[0].cls);
        $('#setup-maint-modal-adsr').val(res[0].adsr);
        $('#setup-maint-modal-prot').val(res[0].prot);
        $('#setup-maint-modal-mlo').val(res[0].mlo);
        $('#setup-maint-modal-contyp').val(res[0].ctyp);
        $('#setup-maint-modal-ffac').val(res[0].ffac);
        $('#setup-maint-modal-tfac').val(res[0].tfac);
      }
      else if (mtcAction == 'RESTORE_MTCD') {
        $('#setup-maint-modal-ckid').val(ckid);
        $('#setup-maint-modal-ckid-tn').val(ckid);
        $('#setup-maint-modal-cls').val(res[1].cls);
        $('#setup-maint-modal-adsr').val(res[1].adsr);
        $('#setup-maint-modal-prot').val(res[1].prot);
        $('#setup-maint-modal-mlo').val(res[1].mlo);
        $('#setup-maint-modal-contyp').val(res[1].ctyp);
        $('#setup-maint-modal-ffac').val(res[1].ffac);
        $('#setup-maint-modal-tfac').val(res[1].tfac);
      }
      else if (mtcAction == 'MTC_TEST') {
        $('#setup-maint-modal-ckid').val(ckid);
        $('#setup-maint-modal-ckid-tn').val(ckid);
        $('#setup-maint-modal-cls').val(res[0].cls);
        $('#setup-maint-modal-adsr').val(res[0].adsr);
        $('#setup-maint-modal-prot').val(res[0].prot);
        $('#setup-maint-modal-mlo').val(res[0].mlo);
        $('#setup-maint-modal-test-ctyp1').val(res[0].ctyp);
        $('#setup-maint-modal-test-ctyp2').val(res[0].ctyp);
        $('#setup-maint-modal-test-ffac').val(res[0].ffac);
        $('#setup-maint-modal-test-tfac').val(res[0].tfac);

        maintLoadTestPort(res[0].ffac, 'X');
        maintLoadTestPort(res[0].tfac, 'Y');

        maintTestForms();
      }
    })
  }
    
  

  $(document).ready(function() {

    // Click event Port Box -> MT_DISCONNECT
    $(document).on('click', '.mt-disconnect', function() {
      let ckid = $(this).closest('.port-box').find('span.port-ckid').text();
      clearErrors();
      $('#setup-maint-modal-post-response-text').text('');
      $('.setup-maint-modal-input').val('');
      maintDefaultForms();
      sysviewMtcPopulateModal(ckid, 'MTC_DISCON');
      $('#setup-maint-modal-action').val('MTC_DISCON');
      $('#setup-maint-modal').modal('show');
    });

    // Click event Port Box Menu -> RESTORE_MTCD
    $(document).on('click', '.restore-mtcd', function() {
      let ckid = $(this).closest('.port-box').find('span.port-ckid').text();
      clearErrors();
      $('#setup-maint-modal-post-response-text').text('');
      $('.setup-maint-modal-input').val('');
      maintDefaultForms();
      sysviewMtcPopulateModal(ckid, 'RESTORE_MTCD');
      $('#setup-maint-modal-action').val('RESTORE_MTCD');
      $('#setup-maint-modal').modal('show');
    });

    // Click even Port Box Menu -> MT_RESTORE
    $(document).on('click', '.mt-restore', function() {
      let ckid = $(this).closest('.port-box').find('span.port-ckid').text();
      clearErrors();
      $('#setup-maint-modal-post-response-text').text('');
      $('.setup-maint-modal-input').val('');
      maintDefaultForms();
      sysviewMtcPopulateModal(ckid, 'MTC_RESTORE');
      $('#setup-maint-modal-action').val('MTC_RESTORE');
      $('#setup-maint-modal').modal('show');
    });

    // Click even Port Box Menu -> MT_TEST
    $(document).on('click', '.mt-test', function() {
      let ckid = $(this).closest('.port-box').find('span.port-ckid').text();

      // clear modal
      clearErrors();
      $('#setup-maint-modal-post-response-text').text('');
      $('.setup-maint-modal-input').val('');
      // select correct divs to hide/show
      maintTestForms();
      // set defaults to radio and selection box
      $('#setup-maint-modal-test-radio1').prop('checked', true);
      $('#setup-maint-modal-test-radio2').prop('checked', false);
      $('#setup-maint-modal-test-port1').prop('disabled', false);
      $('#setup-maint-modal-test-port2').prop('disabled', true);
      sysviewMtcPopulateModal(ckid, 'MTC_TEST');
      $('#setup-maint-modal-action').val('MTC_TEST');
      $('#setup-maint-modal').modal('show');
    });

    // Click event for Node Tabs
    $(document).on('click', '.node-tab', function() {
      let ptyp = $(this).attr('ptyp');
      $('.mio-btn.active[ptyp="'+ptyp+'"]').button('toggle');
      $('.mio-btn[ptyp="'+ptyp+'"]').first().trigger('click');
    });

    // Click event for MIO buttons
    $(document).on('click', '.mio-btn', function() {
      // new code
      let ptyp = $(this).attr('ptyp');
      if ($(this).hasClass('active')) {
        return;
      }
      else {        
        $('.mio-btn.active[ptyp="'+ptyp+'"]').button('toggle');
        $(this).button('toggle');
        $('.port-range-btn[ptyp="'+ptyp+'"]').first().trigger('click');
      }

    });

    // MIO dropdown menu lock card
    $(document).on('click',".dropdown-menu-lock-card", function() {
      let ptyp = $(this).closest('.dropdown-menu').siblings('button').attr('ptyp');
      let slot = $(this).closest('.dropdown-menu').siblings('button').attr('slot');
      let node = $(".node-tab.active[ptyp='" + ptyp + "']").attr('node_id');
      let shelf = "";
      if (ptyp == "x") {
        shelf = "1";
        ptyp = "MIOX";
      } else if (ptyp == "y") {
        shelf = "2";
        ptyp = "MIOY";
      }

      $("#matrix-modal-node").val(node);
      $("#matrix-modal-shelf").val(shelf);
      $("#matrix-modal-slot").val(slot);
      $("#matrix-modal-type").val(ptyp);
      $("#matrix-modal-action").val("LCK").attr('action', 'lck');
      $("#matrix-modal").modal();

    });

    // MIO dropdown menu unlock card
    $(document).on('click',".dropdown-menu-unlock-card", function() {
      let ptyp = $(this).closest('.dropdown-menu').siblings('button').attr('ptyp');
      let slot = $(this).closest('.dropdown-menu').siblings('button').attr('slot');
      let node = $(".node-tab.active[ptyp='" + ptyp + "']").attr('node_id');
      let shelf = "";
      if (ptyp == "x") {
        shelf = "1";
        ptyp = "MIOX";
      } else if (ptyp == "y") {
        shelf = "2";
        ptyp = "MIOY";
      }

      $("#matrix-modal-node").val(node);
      $("#matrix-modal-shelf").val(shelf);
      $("#matrix-modal-slot").val(slot);
      $("#matrix-modal-type").val(ptyp);
      $("#matrix-modal-action").val("UN-LCK").attr('action', 'unlck');
      $("#matrix-modal").modal();
    });

    // MIO dropdown menu action: VIEW PATH
    $(document).on('click', ".dropdown-menu-view-path", function() {

      // Save values for node and slot
      let ptyp = $(this).closest('.dropdown-menu').siblings('button').attr('ptyp');
      let slot = $(this).closest('.dropdown-menu').siblings('button').attr('slot');
      let node = $(".node-tab.active[ptyp='" + ptyp + "']").attr('node_id');

      // Display Modal containing table
      $("#sysview_viewPath_modal").modal("show");
  
      $(document).off('shown.bs.modal', '#sysview_viewPath_modal');
      $(document).on('shown.bs.modal', '#sysview_viewPath_modal', function(e) {
        sysview_viewPath(node, slot);
      });
    });


    // Click event for Port range buttons
    $(document).on('click', '.port-range-btn', function() {
      let ptyp = $(this).attr('ptyp');
      let node = $('.node-tab.active[ptyp="'+ptyp+'"]').attr('node_id');
      let slot = $('.mio-btn.active[ptyp="'+ptyp+'"]').attr('slot');
      $('.port-range-btn.active[ptyp="'+ptyp+'"]').button('toggle');
      $(this).button('toggle');

      //remove previous highlighted port
      $(".port-grid[ptyp='"+ptyp+"'] > .port-box").removeClass('addBorder') 
      queryAndUpdatePorts(node, slot, ptyp);
    });

    // Click event for Port Box
    $(document).on('click', '.port-box button', function() {
      let stat = $(this).parent().attr('class');
      let portPsta = $(this).find('span.port-psta').text();
      let mtDisconnect = 'disabled';
      let mtRestore = 'disabled';
      let restoreMtcd = 'disabled';
      let mtTest = 'disabled';

      if (stat.includes('bg-green')) {
        mtDisconnect = '';
      } else if (stat.includes('bg-minor')) {
        mtRestore = '';
        mtTest = '';
      } else if (stat.includes('bg-major')) {
        restoreMtcd = '';
      }

      $(this).siblings('ul')
        .children('.mt-disconnect')
        .attr('class', 'mt-disconnect ' + mtDisconnect);
      $(this).siblings('ul')
        .children('.mt-restore')
        .attr('class', 'mt-restore ' + mtRestore);
      $(this).siblings('ul')
        .children('.restore-mtcd')
        .attr('class', 'restore-mtcd ' + restoreMtcd);
      $(this).siblings('ul')
        .children('.mt-test')
        .attr('class', 'mt-test ' + mtTest);
    })
  });
</script>