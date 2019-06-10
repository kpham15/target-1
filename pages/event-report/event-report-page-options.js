var eventReportFunctionOptions = {
  configuration:      [
    "SETUP WIRE CENTER", 
    "PORT MAPPING", 
    "SETUP FACILITY",
  ],

  provisioning:       [
    "SETUP SERVICE CONNECTION", 
    "BATCH EXECUTION", 
    "FT ADMIN REPORT", 
    "FLOW-THROUGH RELEASE TABLE", 
    "FLOW-THROUGH", 
    "FOMS/FUSA",
  ],
  
  maintenance:        [
    "SETUP MAINTENANCE CONNECTION",
    "LOCK/UNLOCK MATRIX CARD",
    "LOCK/UNLOCK MATRIX NODE",
    "ALARM ADMINISTRATION",
    "PATH ADMINISTRATION",
  ],

  userManagement:     [
    "SETUP USER",
    "USER ACCESS",
    "USER SEARCH",
    "SET/RESET PASSWORD",
    "BROADCAST NOTIFICATION",
  ],

  ipcAdministration:  [
    "IPC REFERENCE DATA",
    "SYSTEM SHUTDOWN",
    "RESTORE SYSTEM",
    "BACKUP DATABASE",
    "UPDATE SOFTWARE RELEASES",
    "NODE ADMINISTRATION",
  ],
}

var eventReportTaskOptions = {
  setupWireCenter: [
      "VIEW",
      "UPDATE",
      "RESET",
      "UPDATE_NETWORK",
      "TURN_UP",
      "HOLD"
  ],
  
  portMapping: [
      "MAP",
      "UNMAP",
  ],
  
  setupServiceConnection: [
      "CONNECT",
      "DISCONNECT",
      "DIP CONNECT",
      "UPDATE_CKT",
  ],

  ftAdminReport: [

  ],

  fomsFusa: [

  ],

  batchExecution: [
      "EXECUTE BATCH FILES",
  ],

  setupMaintenanceConnection: [
      "MTC_CONN",
      "MTC_DISCON",
      "RESTORE_MTCD",
      "MTC_RESTORE",
      "MTC_LPBK_TEST",
  ],

  lockUnlockMatrixCard: [
      "LOCK_CARD",
      "UNLOCK_CARD",
      "REFRESH_CARD",
  ],

  lockUnlockMatrixNode: [
      "LOCK_NODE",
      "UNLOCK_NODE",
  ],

  alarmAdministration: [
      "ACK",
      "UN-ACK",
      "CLR ALARM",
  ],

  pathAdministration: [
      "REPLACE DEF PATH",
      "RELEASE DEF PATH",
      "REPEAT CONNECT",
  ],

  broadcastNotification: [
      "UPDATE MSG",
      "DELETE MSG",
      "ADD MSG",
  ],

  setupUser: [
      "ADD USER",
      "UPDATE USER",
      "LOCK USER",
      "UNLOCK USER",
      "UPDATE USER GROUP",
      "DELETE USER",
      "CHANGE PASSWORD",
      "RESET PW",
  ],

  userAccess: [

  ],

  userSearch: [

  ],

  setResetPassword: [

  ],

  backupDatabase: [
      "BACKUP DB",
      "RESTORE BACKUP DB",
  ],

  restoreSystem: [

  ],

  updateSoftwareReleases: [
      "UPDATE SOFTWARE",
      "ROLLBACK SOFTWARE",
  ],

  nodeAdministration: [

  ],

  systemShutdown: [
      "LOCK IPC",
      "UNLOCK IPC",
      "SHUTDOWN IPC",
  ],

  ipcReferenceData: [
      "UPDATE",
      "RESET"
  ],

  flowThrough: [

  ],

  flowThroughReleaseTable: [

  ],
  
  setupFacility: [
      "VIEW",
      "ADD",
      "UPDATE",
      "DELETE",
  ],

}