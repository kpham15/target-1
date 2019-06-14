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
                      '<li><a>Item 1</a></li>' +
                      '<li><a>Item 2</a></li>' +
                      '<li><a>Item 3</a></li>' +
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

  $(document).ready(function() {
    // Click event for Node Tabs
    $(document).on('click', '.node-tab', function() {
      let ptyp = $(this).attr('ptyp');
      $('.mio-btn[ptyp="'+ptyp+'"]').first().trigger('click');
    });

    // Click event for MIO buttons
    $(document).on('click', '.mio-btn', function() {
      // new code
      let ptyp = $(this).attr('ptyp');
      if ($(this).hasClass('active')) {

      }
      else {        
        $('.mio-btn.active[ptyp="'+ptyp+'"]').button('toggle');
        $(this).button('toggle');
        $('.port-range-btn[ptyp="'+ptyp+'"]').first().trigger('click');
      }

    });

    // MIO dropdown menu lock card
    $(document).on('click',".dropdown-menu-lock-card", function() {
      console.log($(this).parent().parent().parent().children('button').attr('ptyp'));
      console.log($(this).parent().parent().parent().children('button').attr('slot'));
      let ptyp = $(this).parent().parent().parent().children('button').attr('ptyp');
      let slot = $(this).parent().parent().parent().children('button').attr('slot');
      let node = $(".node-tab.active[ptyp='" + ptyp + "']").attr('node_id');
      let shelf = "";
      if (ptyp == "x") {
        shelf = "1";
      } else if (ptyp == "y") {
        shelf = "2";
      }
      console.log(`node: ${node}; shelf: ${shelf}; slot: ${slot}; ptyp: ${ptyp}`);

      $("#matrix-modal-node").val(node);
      $("#matrix-modal-shelf").val(shelf);
      $("#matrix-modal-slot").val(slot);
      $("#matrix-modal-type").val(ptyp);
      $("#matrix-modal-action").val("LCK");
      $("#matrix-modal").modal('show');
      // popup modal
      // Set node, shelf, slot, type values
      // Display modal populated with values

    });

    // MIO dropdown menu unlock card
    $(document).on('click',".dropdown-menu-unlock-card", function() {
      console.log($(this).parent().parent().parent().children('button').attr('ptyp'));
      // Set node, shelf, slot, type values
      // Display modal populated with values
    });

    // Click event for Port range buttons
    $(document).on('click', '.port-range-btn', function() {
      let ptyp = $(this).attr('ptyp');
      let node = $('.node-tab.active[ptyp="'+ptyp+'"]').attr('node_id');
      let slot = $('.mio-btn.active[ptyp="'+ptyp+'"]').attr('slot');

      $('.port-range-btn.active[ptyp="'+ptyp+'"]').button('toggle');
      $(this).button('toggle');

      queryAndUpdatePorts(node, slot, ptyp);
    });

    // Click events for Port Box Dropdowns
    // $(document).on('click', '.port-box button', function() {
    //   $(this).dropdown('toggle');
    // });
  });
</script>