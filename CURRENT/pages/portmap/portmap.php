<div id="portmap-page" class="content-page" style="display:none">
    <div class="container-fluid">
        <section class="content-header">
            <h1>SETUP PORT MAPPING</h1>

            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Setup Port Mapping</li>
            </ol>
        </section>

        <!-- Port Mapping Forms -->
        <?php include __DIR__ . '/portmap-form.html'; ?>

        <!-- Port Mapping Table -->
        <?php include __DIR__ . '/portmap-table.html'; ?>

        <!-- Port Mapping Modals -->
        <?php include __DIR__ . '/portmap-modals.html'; ?>


    </div>

</div>

<script type="text/javascript">

var portmapFirstLoad = true;

$('.menu-item[page_id="portmap-page"]').click(function() {
    if (portmapFirstLoad != true) {
        return;
    }

    loadFacOptions("queryFtyp", "ftyp", createPortOptions);
    loadFacOptions("queryOrt", "ort", createPortOptions);
    loadFacOptions("querySpcfnc", "spcfnc", createPortOptions);
    clearErrors();

    portmapFirstLoad = false;
})

function createPortOptions(data, type) {
    var a = [];
    a.push('<option value=""></option>');
    
    data.forEach(function(option) {
      let html = `<option value="${option[type]}">${option[type]}</option>`;
      a.push(html);
    });
    
    $('#portmap-modal-'+type).html(a.join(''));

  }
</script>

