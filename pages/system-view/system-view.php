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
            <div class="tab-pane active" id="nodex1">
              <div class="container-fluid">
                <div class="row">
                  <div class="btn-group">
                    <button type="button" class="btn btn-default active">MIOX-1</button>
                    <button type="button" class="btn btn-default">MIOX-2</button>
                    <button type="button" class="btn btn-default">MIOX-3</button>
                    <button type="button" class="btn btn-default">MIOX-4</button>
                    <button type="button" class="btn btn-default">MIOX-5</button>
                    <button type="button" class="btn btn-default">MIOX-6</button>
                    <button type="button" class="btn btn-default">MIOX-7</button>
                    <button type="button" class="btn btn-default">MIOX-8</button>
                    <button type="button" class="btn btn-default">MIOX-9</button>
                    <button type="button" class="btn btn-default">MIOX-10</button>
                  </div>
                </div>
                <div class="row">
                  <div class="btn-group">
                    <button type="button" class="btn btn-default active">1-25</button>
                    <button type="button" class="btn btn-default">26-50</button>
                  </div>
                </div>
                <div id="x-port-grid" class="row port-grid">
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
            <div class="tab-pane active" id="nodey1">
              <div class="container-fluid">
                <div class="row">
                  <div class="btn-group">
                    <button type="button" class="btn btn-default active">MIOY-1</button>
                    <button type="button" class="btn btn-default">MIOY-2</button>
                    <button type="button" class="btn btn-default">MIOY-3</button>
                    <button type="button" class="btn btn-default">MIOY-4</button>
                    <button type="button" class="btn btn-default">MIOY-5</button>
                    <button type="button" class="btn btn-default">MIOY-6</button>
                    <button type="button" class="btn btn-default">MIOY-7</button>
                    <button type="button" class="btn btn-default">MIOY-8</button>
                    <button type="button" class="btn btn-default">MIOY-9</button>
                    <button type="button" class="btn btn-default">MIOY-10</button>
                  </div>
                </div>
                <div class="row">
                  <div class="btn-group">
                    <button type="button" class="btn btn-default active">1-25</button>
                    <button type="button" class="btn btn-default">26-50</button>
                  </div>
                </div>
                <div id="y-port-grid" class="row port-grid">
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
    nodeInfo.forEach(function(node) {
      if ($('#node-x-table .node-tab[node_id="'+node.node+'"]').length === 0) {
        let nodeXTab = createNodeTabs(node, 'x');
        $('#node-x-tabs').append(nodeXTab);
      }

      if ($('#node-y-table .node-tab[node_id="'+node.node+'"]').length === 0) {
        let nodeYTab = createNodeTabs(node, 'y');
        $('#node-y-tabs').append(nodeYTab);
      }
    });

    if ($('#node-x-tabs').children().length > 0) {
      $('#node-x-table .node-tab[node_id="1"]').addClass('active');
    }
    if ($('#node-y-tabs').children().length > 0) {
      $('#node-y-table .node-tab[node_id="1"]').addClass('active');
    }

    for (let i = 1; i <= 25; i++) {
      let portBox = '<div class="info-box bg-gray-active disabled" grid_num="'+i+'">' +
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

      $('.port-grid').append(portBox);
    }
  }

  function createNodeTabs(node, ptyp) {
    // HTML template for node tab
    let nodeTab = '<li class="node-tab" node_id="'+node.node+'">' +
                    '<a href="#node'+ptyp+'" data-toggle="tab">Node '+node.node+'</a>' +
                  '</li>';

    // Return html string
    return nodeTab;
  }

  $(document).ready(function() {
  });
</script>