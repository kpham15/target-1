<div id="event-report-page" class="content-page" style="display:none;">
  <div class="container-fluid">    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Event Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Event Report</li>
      </ol>
    </section>
    <!-- Maint Report Forms -->
    <?php include __DIR__ . '/event-report-forms.html'; ?>
    <!-- Maint Report Table -->
    <?php include __DIR__ . '/event-report-tables.html'; ?>
    
  </div>
</div>

<script type="text/javascript">

  var eventReportFirstLoad = true;
  $(".menu-item[page_id='event-report-page']").click(async function() {
    if (eventReportFirstLoad != true) {
      return;
    }
    // load event log table upon visiting page
    eventReportQueryEventlog(eventReportStartDate, eventReportEndDate);

    eventReportFirstLoad = false;
  });
  
</script>