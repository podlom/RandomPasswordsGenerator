<?php
/**
 * @author Taras Shkodenko <taras@shkodenko.com>
 */

$minPassLen = 1;
$maxPassLen = 42;
$lenMsg = '';
if (!isset($_POST['len']) 
	|| (!is_numeric($_POST['len']))
	|| (strlen($_POST['len']) == 0)
	|| ($_POST['len'] > $maxPassLen) 
	|| ($_POST['len'] < $minPassLen) 
) {
	$passLen = 16;
	$lenMsg = '<div>'.
		'Generated password using default length value: <b>' . $passLen . '</b>. '.
		'Min length: <b>' . $minPassLen . '</b>. '.
		'Max length: <b>' . $maxPassLen . '</b>. '.
		'</div>';
} else {
	$passwordLen = intval($_POST['len']);

	$passwordConfig = array();
	if ($passwordLen) {
		$passwordConfig['len'] = $passwordLen;
	} else {
		$passwordConfig['len'] = rand($minPassLen, $maxPassLen);
	}
	$nF = 0;
	$passParamName = array('lower', 'upper', 'digit', 'special', 'bracket', 'punctuation');
	foreach ($passParamName as $par1) {
		if (isset($_POST[$par1])) {
			$passwordConfig[$par1] = true;
		} else {
			$passwordConfig[$par1] = false;
			$nF ++;
		}
	}
	if ($nF == count($passParamName)) {
		foreach ($passParamName as $par2) {
			$passwordConfig[$par2] = true;
		}
	}

	$randomPassword = getPassword($passwordLen, $passwordConfig);
	header('Content-type: application/json; charset=UTF-8');
	echo json_encode(array('randomPassword' => $randomPassword, 'msg' => '<div>'.
		'Generated password length: <b>' . $passwordLen . '</b>. '.
		'Min length: <b>' . $minPassLen . '</b>. '.
		'Max length: <b>' . $maxPassLen . '</b>. '.
		'</div>'));
	exit;
}	

//
function getPassword($passwordLen = 16, $passwordConfig = array()) {
	if (empty($passwordConfig)) {
		$passParamName = array('lower', 'upper', 'digit', 'special', 'bracket', 'punctuation');
		foreach ($passParamName as $par1) {
			$passwordConfig[$par1] = true;
		}
	}

	$sAlphabet = '';
	if ($passwordConfig['lower']) {
		$sAlphabet .= 'abcdefghijklmnopqrtsuvwxyz';
	}
	if ($passwordConfig['upper']) {
		$sAlphabet .= 'ABCDEFGHIJKLMNOPQERSTUVXYZ';
	}
	if ($passwordConfig['digit']) {
		$sAlphabet .= '0123456789';
	}
	if ($passwordConfig['special']) {
        // Note, do not use :/@?& to get correct values for Symfony DATABASE_URL .env variables like shown in example below:
        // DATABASE_URL="mysql://dbUser:dbPass@127.0.0.1:3306/dbName?serverVersion=5.7&charset=utf8mb4"
		$sAlphabet .= '*%_=';
	}
    if ($passwordConfig['punctuation']) {
        $sAlphabet .= '.,!?';
    }
	if ($passwordConfig['bracket']) {
		$sAlphabet .= '{}[]()';
	}
	
	$aLen = strlen($sAlphabet) - 1;
	
	$sNewPassword = '';
	
	for ($i = 0; $i < $passwordLen; ++ $i) {
		$sNewPassword .= $sAlphabet[rand(0, $aLen)];
	}
	
	return $sNewPassword;
}
//
	
$randomPassword = getPassword($passLen);

	
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-E7041R64JF"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-E7041R64JF');
</script>

<meta charset="UTF-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css">
<title><?=$randomPassword?> - generated password (<?=$passLen?>)</title>
<!-- PHP version 8.1+ -->
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>

<body>

	<h1 id="passwordHeader"><?=$randomPassword?></h1>

	<div class="row">
		<form id="f1" method="post" action="/<?= pathinfo(__FILE__, PATHINFO_BASENAME); ?>">
			<div class="row">
				<div class="col s12">Setup your password</div>
	    	</div>

	    	<div class="row">
	    		<div class="input-field col s2">
		          <i class="material-icons prefix">note_add</i>
		          <input type="text" id="len" class="materialize-textarea" name="len" value="<?=$passLen?>" minlength="1" maxlength="2" required>
		          <label for="len">Length</label>
		        </div>
            </div>

            <div class="row">
		        <div class="input-field col s2">
		          <input type="checkbox" id="lower" class="materialize-textarea" name="lower" value="1" checked="checked">
		          <label for="lower">Lowercase</label>
		        </div>

		        <div class="input-field col s2">
		          <input type="checkbox" id="upper" class="materialize-textarea" name="upper" value="1" checked="checked">
		          <label for="upper">Uppercase</label>
		        </div>

                <div class="input-field col s2">
                    <input type="checkbox" id="punctuation" class="materialize-textarea" name="punctuation" value="1" checked="checked">
                    <label for="punctuation">Punctuation</label>
                </div>
            </div>

            <div class="row">
		        <div class="input-field col s2">
		          <input type="checkbox" id="digit" class="materialize-textarea" name="digit" value="1" checked="checked">
		          <label for="digit">Digits</label>
		        </div>

		        <div class="input-field col s2">
		          <input type="checkbox" id="special" class="materialize-textarea" name="special" value="1" checked="checked">
		          <label for="special">Speacial</label>
		        </div>

		        <div class="input-field col s2">
		          <input type="checkbox" id="bracket" class="materialize-textarea" name="bracket" value="1" checked="checked">
		          <label for="bracket">Bracket</label>
		        </div>
	        </div>

	        <div class="row">
				<div class="input-field col s2">
		          <button class="btn waves-effect waves-light" type="submit" name="action">
                      <i class="material-icons right"></i> get new password
		          </button>
		        </div>
	    	</div>

            <div class="row">
                <div class="input-field col s2">
                    <button class="btn btn1" data-clipboard-action="copy" data-clipboard-target="#passwordHeader">
                        <i class="fa fa-clipboard"></i> copy to clipboard
                    </button>
                </div>
            </div>
		</form>

		<div class="row">
			<div class="col s12" id="passwordText"><?= $lenMsg ?></div>
		</div>
	</div>

<script
        src="https://code.jquery.com/jquery-3.4.0.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="/js/jquery-3.4.0.min.js">\x3C/script>')</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="/js/clipboard.min.js"></script>

<script>

    jQuery( document ).ready(function() {
        jQuery.post("/proxy.php?type=1", function(data) {
            console.log( data );
        });
    });

	jQuery('#f1').submit(function(){
        jQuery.post("/proxy.php?type=1", function(data) {
            console.log( data );
        });

		jQuery.post("/<?= pathinfo(__FILE__, PATHINFO_BASENAME); ?>", $("#f1").serialize(), "json").done(function(dt) {
	    	jQuery("#passwordText").text("").append(dt.msg);
	    	jQuery("#passwordHeader").text("").append(dt.randomPassword);
	    	document.title = dt.randomPassword;
		});

		return false;
	});

    jQuery('.btn1').click(function(e1){
        jQuery.post("/proxy.php?type=2", function(data) {
            console.log( data );
        });

        var clipboard = new ClipboardJS('.btn1');
        clipboard.on('success', function(e) {
            console.log(e);
        });
        clipboard.on('error', function(e) {
            console.log(e);
        });
        e1.preventDefault();
        // return false;
    });

</script>

</body>
</html>
