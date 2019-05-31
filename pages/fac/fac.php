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

    <!-- Forms -->
    <div class="col-md-5">
      <form id="fac-form">
        <!-- Row 1 -->
        <div class="row">
          <!-- FAC_ID -->
          <div class="col-md-7">
            <div class="form-group">
              <label>FAC_ID</label>
              <input id="fac-form-facId" type="text" class="form-control fac-form-input">
            </div>
          </div>

          <!-- PSTA -->
          <div class="col-md-3">
            <div class="form-group">
              <label>PSTA</label>
              <select id="fac-form-psta" class="form-control fac-form-input">
                <option value=""></option>
                <option value="AVAIL">AVAIL</option>
                <option value="SF">SF</option>
                <option value="CONN">CONN</option>
                <option value="MTCD">MTCD</option>
                <option value="DEF">DEF</option>
              </select>
            </div>
          </div>

          <!-- VIEW BUTTON 1 -->
          <div class="col-md-2">
            <button id="fac-form-view-1" type="button" class="btn btn-primary">VIEW</button>
          </div>
        </div>

        <!-- Row 2 -->
        <div class="row">
          <!-- FAC_TYP -->
          <div class="col-md-2">
            <label>FAC_TYP</label>
            <select id="fac-form-ftyp" class="form-control fac-form-input">
              <option value=""></option>
              <!-- TBC -->
              <option value="CP">CP</option>
            </select>
          </div>

          <!-- ORT-->
          <div class="col-md-2">
            <label>ORT</label>
            <select id="fac-form-ort" class="form-control fac-form-input">
              <option value=""></option>
              <!-- TBC -->
              <option value="CP">CP</option>
            </select>
          </div>

          <!-- SPCFNC-->
          <div class="col-md-2">
            <label>SPCFNC</label>
            <select id="fac-form-spcfnc" class="form-control fac-form-input">
              <option value=""></option>
              <!-- TBC -->
              <option value="SP">SP</option>
            </select>
          </div>

          <!-- PORT INFORMATION (HIDDEN) -->
          <div class="col-md-3">
            <input id="fac-form-portInfo" type="hidden" class="form-control fac-form-input">
          </div>

          <!-- VIEW BUTTON 2 -->
          <div class="col-md-2">
            <button id="fac-form-view-2" type="button" class="btn btn-primary">VIEW</button>
          </div>
        </div>

        <!-- Row 3 -->
        <div class="row">
          <!-- Action Dropdown -->
          <div class="col-md-3">
            <div class="form-group">
              <label>ACTION</label>
              <select id="fac-form-action" class="form-control fac-form-input">
                <option value=""></option>
                <option value="add">ADD</option>
                <option value="update">UPDATE</option>
                <option value="delete">DELETE</option>
              </select>
              <span class="help-block"></span>
            </div>
          </div>

          <!-- VIEW BUTTON 2 -->
          <div class="col-md-2">
            <button id="fac-form-clear" type="button" class="btn btn-primary">CLEAR</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
