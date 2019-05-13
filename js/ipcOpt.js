var opt = {
    querySpcfnc: function(action) {

        $.post(ipcDispatch, 
        {
            api: 'ipcOpt',
            act:  action,
            user: $("#main_currentUser").text()
        }, 
        
        function(data, status) {
            var obj = JSON.parse(data);
            if (obj.rslt == "fail") {
                alert(obj.reason)
            }
            else {
                var  a = [];
                for (var i = 0; i < obj.rows.length; i++) {  
        
                    a.push('<option>' + obj.rows[i].spcfnc + '</option>')
                    
                }
                fac.spcfnc.empty();
                fac.spcfnc.html(a.join(""));
                facModal.spcfnc.empty();
                facModal.spcfnc.html(a.join(""));
            }
        })
    },

    queryOrt: function(action) {

        $.post(ipcDispatch, 
        {
            api: 'ipcOpt',
            act:  action,
            user: $("#main_currentUser").text()
        }, 
        
        function(data, status) {
            var obj = JSON.parse(data);
            if (obj.rslt == "fail") {
                alert(obj.reason)
            }
            else {
                var  a = [];
                for (var i = 0; i < obj.rows.length; i++) {  
                
                a.push('<option>' + obj.rows[i].ort + '</option>')
                    
                }
                fac.ort.empty();
                fac.ort.html(a.join(""));
                facModal.ort.empty();
                facModal.ort.html(a.join(""));
            }
        })
    },


    queryFtyp: function(action) {

        $.post(ipcDispatch, 
        {
            api: 'ipcOpt',
            act:  action,
            user: $("#main_currentUser").text()
        }, 
        
        function(data, status) {
            var obj = JSON.parse(data);
            if (obj.rslt == "fail") {
                alert(obj.reason)
            }
            else {
                var  a = [];
                for (var i = 0; i < obj.rows.length; i++) {  
        
                    a.push('<option>' + obj.rows[i].ftyp + '</option>')
                    
                }
                fac.ftyp.empty();
                fac.ftyp.html(a.join(""));
                facModal.ftyp.empty();
                facModal.ftyp.html(a.join(""));
            }
        })
    },

    query: function(action) {

        $.post(ipcDispatch, 
        {
            api:  'ipcOpt',
            act:  action,
            user: $("#main_currentUser").text()

            //user: "ninh"
        }, 
        
        function(data, status) {
            var obj = JSON.parse(data);
            if (obj.rslt == "fail") {
                alert(obj.reason)
            }
            else {
                var  optVals = [];
                for (var i = 0; i < obj.rows.length; i++) {  
                    var tabName = obj.rows[i]['fnc'];
                    var selName = obj.rows[i]['opt'];
                    var optVal  = obj.rows[i]['optval'];
        
                    if  (!(tabName in optVals)) {
                        optVals[tabName] = [];
                    }
                    if  (!(selName in optVals[tabName])) {
                        optVals[tabName][selName] = [];
                    }
                    // if (tabName == "PORTMAP" && selName == "ACTION") {
                    //     n = evtlog.fnc_portmap.length;
                    //     evtlog.fnc_portmap[n] = optVal;
                    // }
                    // else if (tabName == "SVCCONN" && selName == "ACTION") {
                    //     n = evtlog.fnc_svc.length;
                    //     evtlog.fnc_svc[n] = optVal;
                    // }
                    // else if (tabName == "USERS" && selName == "ACTION") {
                    //     n = evtlog.fnc_users.length;
                    //     evtlog.fnc_users[n] = optVal;
                    // }
                    // else if (tabName == "WC" && selName == "ACTION") {
                    //     n = evtlog.fnc_wc.length;
                    //     evtlog.fnc_wc[n] = optVal;
                    // }
                    // else if (tabName == "ALMADM" && selName == "ACTION") {
                    //     n = evtlog.fnc_almadm.length;
                    //     evtlog.fnc_almadm[n] = optVal;
                    // }
                    // else if (tabName == "PATHADM" && selName == "ACTION") {
                    //     n = evtlog.fnc_pathadm.length;
                    //     evtlog.fnc_pathadm[n] = optVal;
                    // }
                    // else if (tabName == "MXC" && selName == "ACTION") {
                    //     n = evtlog.fnc_mxc.length;
                    //     evtlog.fnc_mxc[n] = optVal;
                    // }
                    // else if (tabName == "MAINT" && selName == "ACTION") {
                    //     n = evtlog.fnc_maint.length;
                    //     evtlog.fnc_maint[n] = optVal;
                    // }
                    // else if (tabName == "BRDCST" && selName == "ACTION") {
                    //     n = evtlog.fnc_brdcst.length;
                    //     evtlog.fnc_brdcst[n] = optVal;
                    // }
                    // else if (tabName == "FACILITY" && selName == "ACTION") {
                    //     n = evtlog.fnc_fac.length;
                    //     evtlog.fnc_fac[n] = optVal;
                    // }

                    if (optVals[tabName][selName].length == "") {
                        optVals[tabName][selName].push('<option></option>');
                    }
    
                    optVals[tabName][selName].push('<option>' + optVal + '</option>')
                    
                }
    
                fac.act.html(optVals["FACILITY"]["ACTION"].join("")) ;
    
                pm.act.html(optVals["PORTMAP"]["ACTION"].join(""));
                pm.ptyp.html(optVals["PORTMAP"]["PTYP"].join(""));
                pm.psta.html(optVals["PORTMAP"]["PSTA"].join(""));
                
                pmModal.ptyp.html(optVals["PORTMAP"]["PTYP"].join(""));
                pmModal.psta.html(optVals["PORTMAP"]["PSTA"].join(""));
    
                //svc.act.html(optVals["SVCCONN"]["ACTION"].join(""));
                svc.cls.html(optVals["SVCCONN"]["CLS"].join(""));
                svc.adsr.html(optVals["SVCCONN"]["ADSR"].join(""));
                svc.prot.html(optVals["SVCCONN"]["PROT"].join(""));
                svc.mlo.html(optVals["SVCCONN"]["MLO"].join(""));
    
                svcModal.cls.html(optVals["SVCCONN"]["CLS"].join(""));
                svcModal.adsr.html(optVals["SVCCONN"]["ADSR"].join(""));
                svcModal.prot.html(optVals["SVCCONN"]["PROT"].join(""));
                svcModal.mlo.html(optVals["SVCCONN"]["MLO"].join(""));
                
                //maint.act.html(optVals["MAINT"]["ACTION"].join(""));
                maint.cls.html(optVals["SVCCONN"]["CLS"].join(""));
                maint.adsr.html(optVals["SVCCONN"]["ADSR"].join(""));
                maint.prot.html(optVals["SVCCONN"]["PROT"].join(""));
                maint.mlo.html(optVals["SVCCONN"]["MLO"].join(""));
    
                maintModal.cls.html(optVals["SVCCONN"]["CLS"].join(""));
                maintModal.adsr.html(optVals["SVCCONN"]["ADSR"].join(""));
                maintModal.prot.html(optVals["SVCCONN"]["PROT"].join(""));
                maintModal.mlo.html(optVals["SVCCONN"]["MLO"].join(""));
    
                // wc.tzone.html(optVals["WC"]["TZONE"].join(""));
                wc.stat.html(optVals["WC"]["STAT"].join(""));
                wc.state.html(optVals["WC"]["STATE"].join(""));
                wc.act.html(optVals["WC"]["ACTION"].join(""));
                               
                // wcModal.tzone.html(optVals["WC"]["TZONE"].join(""));
                wcModal.stat.html(optVals["WC"]["STAT"].join(""));
                wcModal.state.html(optVals["WC"]["STATE"].join(""));
                
                alm.act.html(optVals["ALMADM"]["ACTION"].join(""));

                brdcst.act.html(optVals["BRDCST"]["ACTION"].join(""));

            }
        })
    }
    
}
