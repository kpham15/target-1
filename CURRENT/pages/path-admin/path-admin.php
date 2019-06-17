<div id="path-admin-page" class="content-page" style="display:none;">
  <div class="container-fluid">    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Path Administration
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Path Administration</li>
      </ol>
    </section>
    <!-- Maint Report Forms -->
    <?php include __DIR__ . '/path-admin-forms.html'; ?>
    <!-- Maint Report Table -->
    <?php include __DIR__ . '/path-admin-tables.html'; ?>
    
  </div>
</div>

<?php include __DIR__ . '/path-admin-modal-view-path.html'; ?>