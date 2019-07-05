var refOptions = [
	{
		title:		  "MAXIMUM PASSWORD AGE",
		selectId:	  "reference-page-pwage",
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
		title: 			"PASSWORD REUSE",
		selectId: 	"reference-page-pwreuse",
		ref_id:     "pw_reuse",
		options: 		[
			{
				value: 	"0",
				text: 	"N/A"
			},
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
		title: 			"PASSWORD REPEAT",
		selectId: 	"reference-page-pwrepeat",
		ref_id:     "pw_repeat",
		options: 		[
      {
        value:  "0",
        text:   "N/A"
      },
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
				text: 	"DELETE AFTER 5 DAYS"
			},
			{
				value: 	"7",
				text: 	"DELETE AFTER 7 DAYS"
			},
			{
				value: 	"14",
				text: 	"DELETE AFTER 14 DAYS"
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
				value:	"120",
				text: 	"DISABLE AFTER 120 DAYS"
			},
			{
				value: 	"180",
				text: 	"DISABLE AFTER 180 DAYS"
			},
			{
				value: 	"240",
				text: 	"DISABLE AFTER 240 DAYS"
			},
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
				text: 	"TIMEOUT AFTER 30 MINUTES"
			},
			{
				value: 	"45",
				text: 	"TIMEOUT AFTER 45 MINUTES"
			},
			{
				value: 	"60",
				text: 	"TIMEOUT AFTER 60 MINUTES"
			},
		]
	},
	{
		title: 			"ALARM REPORT ARCHIVE",
		selectId: 	"reference-page-almarchive",
		ref_id:     "alm_archv",
		options: 		[
			{
				value: 	"60",
				text: 	"ARCHIVE AFTER 60 DAYS"
			},
			{
				value: 	"90",
				text: 	"ARCHIVE AFTER 90 DAYS"
			},
			{
				value: 	"120",
				text: 	"ARCHIVE AFTER 120 DAYS"
			},
		]
  },
	{
		title: 			"ALARM REPORT DELETE",
		selectId: 	"reference-page-almdelete",
		ref_id:     "alm_del",
		options: 		[
			{
				value: 	"340",
				text: 	"DELETE AFTER 340 DAYS"
			},
			{
				value: 	"390",
				text: 	"DELETE AFTER 390 DAYS"
			},
			{
				value: 	"440",
				text: 	"DELETE AFTER 440 DAYS"
			}
		]
	},
	{
		title: 			"CONFIGURATION REPORT ARCHIVE",
		selectId: 	"reference-page-cfgarchive",
		ref_id:     "cfg_archv",
		options: 		[
			{
				value: 	"30",
				text: 	"ARCHIVE AFTER 30 DAYS"
			},
			{
				value: 	"45",
				text: 	"ARCHIVE AFTER 45 DAYS"
			},
			{
				value: 	"60",
				text: 	"ARCHIVE AFTER 60 DAYS"
			}
		]
	},
	{
		title: 			"CONFIGURATION REPORT DELETE",
		selectId: 	"reference-page-cfgdelete",
		ref_id:     "cfg_del",
		options: 		[
			{
				value: 	"130",
				text: 	"DELETE AFTER 130 DAYS"
			},
			{
				value: 	"180",
				text: 	"DELETE AFTER 180 DAYS"
			},
			{
				value: 	"230",
				text: 	"DELETE AFTER 230 DAYS"
			}
		]
	},
	{
		title: 			"PROVISIONING REPORT ARCHIVE",
		selectId: 	"reference-page-provarchive",
		ref_id:     "prov_archv",
		options: 		[
			{
				value: 	"60",
				text: 	"ARCHIVE AFTER 60 DAYS"
			},
			{
				value: 	"90",
				text: 	"ARCHIVE AFTER 90 DAYS"
			},
			{
				value: 	"120",
				text: 	"ARCHIVE AFTER 120 DAYS"
			},
		]
	},
	{
		title: 			"PROVISIONING REPORT DELETE",
		selectId: 	"reference-page-provdelete",
		ref_id:     "prov_del",
		options: 		[
			{
				value: 	"340",
				text: 	"DELETE AFTER 340 DAYS"
			},
			{
				value: 	"390",
				text: 	"DELETE AFTER 390 DAYS"
			},
			{
				value: 	"440",
				text: 	"DELETE AFTER 440 DAYS"
			},
		]
	},
	{
		title: 			"MAINTENANCE REPORT ARCHIVE",
		selectId: 	"reference-page-maintarchive",
		ref_id:     "maint_archv",
		options: 		[
			{
				value: 	"30",
				text: 	"ARCHIVE AFTER 30 DAYS"
			},
			{
				value: 	"45",
				text: 	"ARCHIVE AFTER 45 DAYS"
			},
			{
				value: 	"60",
				text: 	"ARCHIVE AFTER 60 DAYS"
			},
		]
	},
  {
		title: 			"MAINTENANCE REPORT DELETE",
		selectId: 	"reference-page-maintdelete",
		ref_id:     "maint_del",
		options: 		[
			{
				value: 	"130",
				text: 	"DELETE AFTER 230 DAYS"
			},
			{
				value: 	"180",
				text: 	"DELETE AFTER 180 DAYS"
			},
			{
				value: 	"230",
				text: 	"DELETE AFTER 230 DAYS"
			},
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
		title: 			"MTC RESTORE",
		selectId: 	"reference-page-mtcrestore",
		ref_id:     "mtc_restore",
		options: 		[
			{
				value: 	"15",
				text: 	"RESTORE AFTER 15 MINUTES"
			},
			{
				value: 	"30",
				text: 	"RESTORE AFTER 30 MINUTES"
			},
			{
				value: 	"45",
				text: 	"RESTORE AFTER 45 MINUTES"
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
				text: 	"70C OR 158F"
			},
			{
				value: 	"75",
				text: 	"75C OR 167F"
			},
			{
				value: 	"80",
				text: 	"80C OR 176F"
			}
		]
	},
	{
		title: 			"VOLTAGE RANGE",
		selectId: 	"reference-page-voltrange",
		ref_id:     "volt_range",
		options: 		[
			{
				value:	"35-50",
				text: 	"35-50V"
			},
			{
				value: 	"40-50",
				text: 	"40-50V"
			},
			{
				value: 	"40-55",
				text: 	"40-55V"
			}
		]
	},
	{
		title: 			"TEMPERATURE FORMAT",
		selectId: 	"reference-page-tempformat",
		ref_id:     "temp_format",
		options: 		[
			{
				value:	"F",
				text: 	"F - FAHRENHEIT"
			},
			{
				value: 	"C",
				text: 	"C - CELSIUS"
			},
		]
	},
]