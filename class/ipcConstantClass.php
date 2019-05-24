<?php
/* 
    Filename: ipcConstantClass.php  
*/
    // Common
    const SVC_ACTION_LST = array("query","queryCkid","queryOrd","queryCkt","queryCktcon","queryFacX","queryFacY","provNewCkt","provConnect","provDisconnect","QUERY-CKT","QUERY-CKID","QUERY-ORD","QUERY-CKTCON","CONNECT","DISCONNECT","NEW-CKT");
    const CLS_LST  = array("BUS","RES");
    const PROT_LST = array("","SSM","SSP");
    const ADSR_LST = array("N","Y");
    const MLO_LST = array("N","Y");
    const CTYP_LST = array("GEN","MLPT","LPBK");

    const SUCCESS = "success";
    const FAIL = "fail";
    const INVALID_ACTION = "Invalid ACTION";
    const INVALID_CLS = "Invalid or Missing CLS";
    const INVALID_ADSR = "Invalid or Missing ADSR";
    const INVALID_PROT = "Invalid or Missing PROT";
    const INVALID_ORDNO = "Invalid or Missing ORDNO";
    const INVALID_MLO = "Invalid or Missing MLO";
    const INVALID_CKTCON = "Invalid or Missing CKTCON";

    // CKT
    const INVALID_CTID = "Invalid or Missing CTID";
    const QUERY_MATCHED = "Query Matched";
    const QUERY_NOT_MATCHED = "Query Not Matched";
    
    const CKT_CONSTRUCTED = "CKT is constructed successfully";
    const CKT_UPDATED = "CKT is updated successfully";
    const CKT_NOT_UPDATED = "CKT is not updated successfully";
    const CKT_NOT_ADDED = "CKT is not added successfully";
    const CKT_ADDED = "CKT is added successfully";
    const CKT_DELETED = "CKT is deleted successfully";

    //for ipcPortClas
    const INVALID_PORT = "PORT ID DOES NOT EXIST";
    const PORT_CONSTRUCTED = "PORT IS CONSTRUCTED SUCCESSFULLY";
    const PORT_UPDATED = "PORT IS UPDATED SUCCESSFULLY";
    const INVALID_PSTA = "Invalid or Missing PSTA";
    const INVALID_SSTA = "Invalid or Missing SSTA";
    const INVALID_SUBSTA = "Invalid or Missing SUBSTA";

    // for ipcCktconClass.php
    const CKTCON_CONSTRUCTED = "CKTCON IS CONSTRUCTED SUCCESSFULLY";
    const INVALID_CON = "CKTCON DOES NOT EXIST";

    const CKTCON_ADDED = "CKTCON IS ADDED SUCCESSFULLY";
    const CKTCCON_IDX__ADDED = "CKTCON IDX IS ADDED SUCCESSFULLY";
    const CKTCON_IDX_DELETED = "CKTCON IDX IS REMOVED SUCCESSFULLY";


    // for ipcStgClass.php
    const STG_CONSTRUCTED = "STG is constructed successfully";
    const INVALID_STG = "Invalid or Missing STG";
    const STG_UPDATED = "STG is updated successfully";
    const STG_NOT_UPDATED = "STG is not updated successfully";
    const INVALID_STG_X = "X is not valid";

    // for ipcBrdcstClass.php
    const BRDCST_CONSTRUCTED = "Brdcst is constructed successfully";
    const INVALID_USER= "Invalid or missing user";
    const INVALID_OWNER= "Invalid or missing owner";
    const INVALID_BRDCST= "Invalid or missing Brdcst";

    // for ipcFacClass.php
    const FAC_CONSTRUCTED = "Fac is constructed successfully";
    const INVALID_FAC = "Invalid or missing Fac";
    
    // USERS
    const USER_LOGIN = "USER_LOGIN";
    const USER_LOGOUT = "USER_LOGOUT";
    const LOGOUT_UPDATED = "LOGOUT_UPDATED";
    const QUERY_TIMEOUT_FAIL = "QUERY_TIMEOUT_FAIL";
    
    // LOGIN
    const LOGIN_UPDATED = "LOGIN_UPDATED";

    // for ipcCktClass.php
    const CKT_QUERIED = "CKT_QUERIED";

    // for ipcCktconClass.php
    const CKTCON = "CKTCON";

    // for ipcCktconClass.php
    const CKTCON_IDX_ADDED = "CKTCON_IDX_ADDED";
    
    /**
     * For MaintDiscon.php
     */
    const CKTCON_UPDATED = "CKTCON_UPDATED";
    const MTC_DISCON_COMPLETED = "MTC_DISCON_COMPLETED";

    
    function result($obj) {
        $result["rslt"] = $obj->rslt;
        $result["reason"] = $obj->reason;
        $result["rows"] = $obj->rows;
        echo json_encode($result);
    }

    // Constants for posting through UDP
    const nOpe = "ipcNodeOpe";
    
    const nAdm = "ipcNodeAdmin";

    
    const apiAndActArray = [
        "CPS" => [
            "API"   => "ipcNodeOpe",
            "DCV"   => "DISCOVER",
            "DCVD"  => "DISCOVERED",
            "STOP"  => "STOP",
            "ON"    => "CPS_ON",
            "OFF"   => "CPS_OFF",
        ],
        "NADM" => [
            "API"   => "ipcNodeAdmin",
            "UPDC"  => "updateCpsCom",
            "UNDS"  => "updateNodeDevicesStatus",
        ]
    ];

    

?>