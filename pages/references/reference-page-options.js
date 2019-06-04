var refOptions = [
	{
		title:			"MAXIMUM PASSWORD AGE",
		selectId:		"reference-page-pwage",
		ref_id:     "pw_expire",
		options: 
		[
			{
        value:	"0",
        text: 	"NEVER EXPIRES"
			},
			{
				value: 	"30",
				text: 	"EXPIRES IN 30 DAYS"
			},
			{
				value: 	"60",
				text: 	"EXPIRES IN 60 DAYS"
			},	
			{
				value: 	"90",
				text: 	"EXPIRES IN 90 DAYS"
			}	
		]
	},
	{
		title: 			"ALARM REPORT ARCHIVE",
		selectId: 	"reference-page-almarchive",
		ref_id:     "alm_archv",
		options: 		[
			{
				value:	"30",
				text: 	"30 Days"
			},
			{
				value: 	"60",
				text: 	"60 Days"
			},
			{
				value: 	"90",
				text: 	"90 Days"
			},
			{
				value: 	"120",
				text: 	"120 Days"
			},
		]
  },
  {
		title: 			"MAINTENANCE REPORT DELETE",
		selectId: 	"reference-page-maintdelete",
		ref_id:     "maint_del",
		options: 		[
			{
				value:	"60",
				text: 	"DELETE AFTER 30 DAYS"
			},
			{
				value: 	"60",
				text: 	"DELETE AFTER 60 DAYS"
			},
			{
				value: 	"90",
				text: 	"DELETE AFTER 90 DAYS"
			},
			{
				value: 	"120",
				text: 	"DELETE AFTER 120 DAYS"
			},
		]
	},
	{
		title: 			"PASSWORD EXPIRY PROMPT",
		selectId: 	"reference-page-pwalert",
		ref_id:     "pw_alert",
		options: 		[
			{
				value:	"0",
				text: 	"NEVER PROMPT"
			},
			{
				value: 	"3",
				text: 	"PROMPT BEFORE 3 DAYS"
			},
			{
				value: 	"5",
				text: 	"PROMPT BEFORE 5 DAYS"
			},
			{
				value: 	"7",
				text: 	"PROMPT BEFORE 7 DAYS"
			}
		]
	},
	{
		title: 			"ALARM REPORT DELETE",
		selectId: 	"reference-page-almdelete",
		ref_id:     "alm_del",
		options: 		[
			{
				value:	"250",
				text: 	"250 Days"
			},
			{
				value: 	"300",
				text: 	"300 Days"
			},
			{
				value: 	"390",
				text: 	"390 Days"
			},
			{
				value: 	"400",
				text: 	"400 Days"
			}
		]
	},
	{
		title: 			"AUTO CKID",
		selectId: 	"reference-page-autockid",
		ref_id:     "auto_ckid",
		options: 		[
			{
				value:	"Y",
				text: 	"Y"
			},
			{
				value: 	"N",
				text: 	"N"
			},
		]
	},
	{
		title: 			"PASSWORD REUSE",
		selectId: 	"reference-page-pwreuse",
		ref_id:     "pw_reuse",
		options: 		[
			{
				value: 	"2",
				text: 	"NO REUSE LAST 2 PW"
			},
			{
				value: 	"3",
				text: 	"NO REUSE LAST 3 PW"
			},
			{
				value: 	"4",
				text: 	"NO REUSE LAST 4 PW"
			}
		]
	},
	{
		title: 			"CONFIGURATION REPORT ARCHIVE",
		selectId: 	"reference-page-cfgarchive",
		ref_id:     "cfg_archv",
		options: 		[
			{
				value:	"15",
				text: 	"15 Days"
			},
			{
				value: 	"30",
				text: 	"30 Days"
			},
			{
				value: 	"45",
				text: 	"45 Days"
			},
			{
				value: 	"60",
				text: 	"60 Days"
			}
		]
	},
	{
		title: 			"AUTO ORDER NUMBER",
		selectId: 	"reference-page-autoordno",
		ref_id:     "auto_ordno",
		options: 		[
			{
				value:	"Y",
				text: 	"Y"
			},
			{
				value: 	"N",
				text: 	"N"
			},
		]
	},
	{
		title: 			"PASSWORD REPEAT",
		selectId: 	"reference-page-pwrepeat",
		ref_id:     "pw_repeat",
		options: 		[
			{
				value:	"180",
				text: 	"DO NOT REPEAT WITHIN 180 DAYS"
			},
			{
				value: 	"240",
				text: 	"DO NOT REPEAT WITHIN 240 DAYS"
			},
			{
				value: 	"365",
				text: 	"DO NOT REPEAT WITHIN 365 DAYS"
			},
		]
	},
	{
		title: 			"CONFIGURATION REPORT DELETE",
		selectId: 	"reference-page-cfgdelete",
		ref_id:     "cfg_del",
		options: 		[
			{
				value:	"60",
				text: 	"60 Days"
			},
			{
				value: 	"120",
				text: 	"120 Days"
			},
			{
				value: 	"180",
				text: 	"180 Days"
			},
			{
				value: 	"240",
				text: 	"240 Days"
			}
		]
	},
	{
		title: 			"DATE FORMAT",
		selectId: 	"reference-page-dateformat",
		ref_id:     "date_format",
		options: 		[
			{
				value:	"YYYY-MM-DD",
				text: 	"YYYY-MM-DD"
			},
			{
				value: 	"MM-DD-YYYY",
				text: 	"MM-DD-YYYY"
			},
			{
				value: 	"MM-DD-YY",
				text: 	"MM-DD-YY"
			}
		]
	},
	{
		title: 			"BROADCAST MESSAGE RETENTION",
		selectId: 	"reference-page-brdcstdel",
		ref_id:     "brdcst_del",
		options: 		[
			{
				value:	"0",
				text: 	"DO NOT AUTO DELETE"
			},
			{
				value: 	"5",
				text: 	"5 Days"
			},
			{
				value: 	"7",
				text: 	"7 Days"
			},
			{
				value: 	"14",
				text: 	"14 Days"
			},
		]
	},
	{
		title: 			"PROVISIONING REPORT ARCHIVE",
		selectId: 	"reference-page-provarchive",
		ref_id:     "prov_archv",
		options: 		[
			{
				value:	"30",
				text: 	"30 Days"
			},
			{
				value: 	"60",
				text: 	"60 Days"
			},
			{
				value: 	"90",
				text: 	"90 Days"
			},
			{
				value: 	"120",
				text: 	"120 Days"
			},
		]
	},
	{
		title: 			"MTC RESTORE",
		selectId: 	"reference-page-mtcrestore",
		ref_id:     "mtc_restore",
		options: 		[
			{
				value:	"10",
				text: 	"10 Minutes"
			},
			{
				value: 	"20",
				text: 	"20 Minutes"
			},
			{
				value: 	"30",
				text: 	"30 Minutes"
			},
			{
				value: 	"40",
				text: 	"40 Minutes"
			},
		]
	},
	{
		title: 			"DISABLE INACTIVE USER",
		selectId: 	"reference-page-userdisable",
		ref_id:     "user_disable",
		options: 		[
      {
        value:  "0",
        text:   "NEVER DISABLE"
      },
			{
				value:	"90",
				text: 	"90 Days"
			},
			{
				value: 	"180",
				text: 	"180 Days"
			},
			{
				value: 	"240",
				text: 	"240 Days"
			},
			{
				value: 	"365",
				text: 	"365 Days"
			},
		]
	},
	{
		title: 			"PROVISIONING REPORT DELETE",
		selectId: 	"reference-page-provdelete",
		ref_id:     "prov_del",
		options: 		[
			{
				value:	"250",
				text: 	"250 Days"
			},
			{
				value: 	"300",
				text: 	"300 Days"
			},
			{
				value: 	"390",
				text: 	"390 Days"
			},
			{
				value: 	"400",
				text: 	"400 Days"
			},
		]
	},
	{
		title: 			"MAXIMUM TEMPERATURE",
		selectId: 	"reference-page-maxtemp",
		ref_id:     "temp_max",
		options: 		[
			{
				value:	"70",
				text: 	"70C"
			},
			{
				value: 	"75",
				text: 	"75C"
			},
			{
				value: 	"80",
				text: 	"80C"
			}
		]
	},
	{
		title: 			"USER IDLE TIMEOUT",
		selectId: 	"reference-page-useridleto",
		ref_id:     "user_idle_to",
		options: 		[
			{
				value:	"0",
				text: 	"DO NOT TIMEOUT"
			},
			{
				value: 	"30",
				text: 	"30 Minutes"
			},
			{
				value: 	"45",
				text: 	"45 Minutes"
			},
			{
				value: 	"60",
				text: 	"60 Minutes"
			},
		]
	},
	{
		title: 			"MAINTENANCE REPORT ARCHIVE",
		selectId: 	"reference-page-maintarchive",
		ref_id:     "maint_archv",
		options: 		[
			{
				value:	"15",
				text: 	"15 Days"
			},
			{
				value: 	"30",
				text: 	"30 Days"
			},
			{
				value: 	"45",
				text: 	"45 Days"
			},
			{
				value: 	"60",
				text: 	"60 Days"
			},
		]
	},
	{
		title: 			"VOLTAGE RANGE",
		selectId: 	"reference-page-voltrange",
		ref_id:     "volt_range",
		options: 		[
			{
				value:	"35-50",
				text: 	"35-50"
			},
			{
				value: 	"40-50",
				text: 	"40-50"
			},
			{
				value: 	"40-55",
				text: 	"40-55"
			}
		]
	},
]