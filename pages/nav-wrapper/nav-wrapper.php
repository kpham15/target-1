<div id="nav-wrapper" class="wrapper" style="display: none;">

  <!-- Main Header -->
  <?php include __DIR__ . "/header-navbar.html"; ?>

  <!-- Left side column. contains the logo and sidebar -->
  <?php include __DIR__ . "/sidebar-nav.html"; ?>

  <!-- Content Wrapper. Contains page content -->
  <div id="main-section-content" class="content-wrapper">

    <!-- Node Status Section -->
    <?php include __DIR__ . "/node-status.html"; ?>

    <hr class="content-page-divider">
    
    <!-- Includes for all the content pages -->
    <?php include __DIR__ . '/../system-view/system-view.php'; ?>
    <?php include __DIR__ . '/../alarmreport/alarmreport.php'; ?>
    <?php include __DIR__ . '/../brdcst/brdcst.php'; ?>
    <?php include __DIR__ . '/../references/references.php'; ?>
    <?php include __DIR__ . '/../svc/svc.php'; ?>
    <?php include __DIR__ . '/../prov-report/prov-report.php'; ?>
    <?php include __DIR__ . '/../maint-report/maint-report.php'; ?>
    <?php include __DIR__ . '/../config-report/config-report.php'; ?>
    <?php include __DIR__ . '/../ftOrders/ftOrders.php'; ?>
    <?php include __DIR__ . '/../fac/fac.php'; ?>
    <?php include __DIR__ . '/../portmap/portmap.php'; ?>
    <?php include __DIR__ . '/../node-operation/node-operation.php'; ?>
    <?php include __DIR__ . '/../alarm-admin/alarm-admin.php'; ?>
<<<<<<< HEAD
    <?php include __DIR__ . '/../batch-exec/batch-exec.php'; ?>
    <?php include __DIR__ . '/../wire-center/wire-center.php'; ?>


=======
    <?php include __DIR__ . '/../database-backup/database-backup.php'; ?>
    <?php include __DIR__ . '/../batch-exec/batch-exec.php'; ?>
    <?php include __DIR__ . '/../wire-center/wire-center.php'; ?>
    <?php include __DIR__ . '/../path-admin/path-admin.php'; ?>
>>>>>>> ipcv2


    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include __DIR__ . "/footer-nav.html"; ?>

  <!-- Control Sidebar -->
  <?php //include __DIR__ . "/control-sidebar.html"; ?>
</div>
<!-- ./wrapper -->