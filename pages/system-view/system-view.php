<div id="system-view-page">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      System View
      <!-- <small>Preview page</small> -->
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">System View</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Node Status Section -->
    <?php include __DIR__ . "/node-status.html"; ?>

    <!-- =========================================================== -->

    <div class="row">
      <div class="col-md-6">
        <div id="node-x-table" class="nav-tabs-custom">
          <ul id="node-x-tabs" class="nav nav-tabs node-tabs">
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
                  <div class="btn-group">
                    <button type="button" class="btn btn-default active">1-25</button>
                    <button type="button" class="btn btn-default">26-50</button>
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
          <ul id="node-y-tabs" class="nav nav-tabs node-tabs">
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
                  <div class="btn-group">
                    <button type="button" class="btn btn-default active">1-25</button>
                    <button type="button" class="btn btn-default">26-50</button>
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
    nodeInfo.forEach(function(node) {
      let nodeTab = createNodeTabs(node);
      $('.node-tabs').append(nodeTab);
    });
    $('.node-tab[node_id="1"]').addClass('active');

    // @TODO
    // Might need to add this bit of code to updates of nodes
    // if node information can change and affect tabs
    $('#node-x-tabs>li>a').attr('href','#nodex');
    $('#node-y-tabs>li>a').attr('href','#nodey');

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
      }
    });
  }

  function updatePortRangeBtns(ptyp) {
    console.log(ptyp);
    console.log(portX);
    return;
  }

  function createPortBox(gridNum) {
    let portBox = '<div class="info-box bg-gray-active disabled" grid_num="'+gridNum+'">' +
                    '<div class="info-box-text">' +
                      '-' +
                      '<span class="pull-right">-</span>' +
                    '</div>' +
                    '<div class="info-box-text">' +
                      '-' +
                      '<span class="pull-right">-</span>' +
                    '</div>' +
                    '<div class="info-box-text text-center">' +
                      '-' +
                    '</div>'
                  '</div>';

    return portBox;
  }

  function createMioBtn(psta, index, ptyp) {
    let slot = index + 1;
    let mioBtn = '<button type="button" class="mio-btn btn btn-default" slot="'+slot+'" ptyp="'+ptyp+'"><p>MIO'+ptyp.toUpperCase()+'-'+slot+'<br/><span class="mio-psta">'+psta+'</p></button>';

    return mioBtn;
  }

  function createNodeTabs(node) {
    // HTML template for node tab
    let nodeTab = '<li class="node-tab" node_id="'+node.node+'">' +
                    '<a href="#" data-toggle="tab">Node '+node.node+'</a>' +
                  '</li>';

    // Return html string
    return nodeTab;
  }

  $(document).ready(function() {
    // Click event for MIO buttons
    $(document).on('click', '.mio-btn' , function() {
      let ptyp = $(this).attr('ptyp');

      $('.mio-btn.active[ptyp="'+ptyp+'"]').button('toggle');
      $(this).button('toggle');
    });
  });
</script>