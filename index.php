<?php

$url = getUrl();

if($url !=="") {
    $urlStr = "Location:".$url;
    header($urlStr);
    exit;
}
else {
    echo "CONFIGURATION FILE IS BROKEN";
    return;
}


function getUrl() {
    $url = "";
    $file = fopen("bhd.cfg", "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            // process the line read.
            $lineExtract = explode(":", $line);
            if($lineExtract[0] == "RUNNING") {
                $url = str_replace(' ','',$lineExtract[1]);
                break;
            }

        }

        fclose($file);

<<<<<<< HEAD
	if (user.uname == '')
	{
		$('#login-page').show();
	}

	FastClick.attach(document.body);

});

</script>
=======
    }
    return $url;
>>>>>>> a27e9ea26fc2bbaa3e4c343c26f62e8c6d73e42a

}

?>
