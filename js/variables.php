<script>
  // Interval loop for querying systme information
  var systemInfoInterval = 0;

  // Current User Information
  var user = {
    uname: '',
    fname: '',
    mi: '',
    lname: '',
    grp: 0,
    ugrp: '',
    loginTime: '',
    idle_to: ''
  }
  
  // Node information for System View
  var nodeInfo = [];

  // Flag for first startup
  var firstLoad = true;
</script>