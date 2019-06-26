<script>
  // Interval loop for querying systme information
  var systemInfoInterval = 0;

  // Current Software Version
  var swVer = {
    version: '',
    description: ''
  }

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

  // Wire Center Information
  var wcInfo = {};

  // Port data
  var portX = [];
  var portY = [];

  // First load boolean
  var firstload = true;

  // store key used to decrypt and encrypt password
  var keyId = "";
 
  // store port infor need to be highlight
  var portHighLight = []
</script>