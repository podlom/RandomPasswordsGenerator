<?php
/**
 * @author Taras Shkodenko <podlom@gmail.com>
 */

if (!defined('GOOGLE_GTAG_ID')) {
    define('GOOGLE_GTAG_ID', 'G-5THCH12P6M');
}

$minPassLen = 1;
$maxPassLen = 42;
$lenMsg = '';

$passwordLen = filter_input(INPUT_POST, 'len', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => $minPassLen, 'max_range' => $maxPassLen]
]) ?: 16;

if (!isset($_POST['len'])) {
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

    if ($passwordLen > $maxPassLen) {
        $passwordLen = $maxPassLen;
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

    $sNewPassword = substr(str_shuffle(str_repeat($sAlphabet, ceil($passwordLen / strlen($sAlphabet)))), 0, $passwordLen);

    return $sNewPassword;
}
	
$randomPassword = getPassword($passLen);

?>
<!DOCTYPE html>
<html lang="uk">
<head>
		<!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?=GOOGLE_GTAG_ID?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?=GOOGLE_GTAG_ID?>');
    </script>

    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="output.css" rel="stylesheet">
    <title><?=$randomPassword?> - generated password (<?=$passLen?>)</title>
</head>

<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 id="passwordHeader" class="text-2xl font-bold text-center break-all"><?=$randomPassword?></h1>
        </div>

        <form id="f1" method="post" action="/<?= pathinfo(__FILE__, PATHINFO_BASENAME); ?>">
            <div class="bg-blue-500 text-white p-4 rounded-t-lg">
                <h2 class="text-xl font-semibold">Password Generator Config</h2>
            </div>
            
            <div class="bg-white p-6 rounded-b-lg shadow-lg mb-8">
                <div class="mb-6">
                    <label for="len" class="block text-gray-700 font-medium mb-2">Password Length</label>
                    <div class="flex items-center">
                        <span class="material-icons text-gray-500 mr-2">note_add</span>
                        <input type="text" id="len" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               name="len" value="<?=$passLen?>" minlength="1" maxlength="2" required>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="lower" class="h-5 w-5 text-blue-500 rounded focus:ring-blue-500" 
                               name="lower" value="1" checked="checked">
                        <label for="lower" class="ml-2 text-gray-700">Lowercase</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="upper" class="h-5 w-5 text-blue-500 rounded focus:ring-blue-500" 
                               name="upper" value="1" checked="checked">
                        <label for="upper" class="ml-2 text-gray-700">Uppercase</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="punctuation" class="h-5 w-5 text-blue-500 rounded focus:ring-blue-500" 
                               name="punctuation" value="1" checked="checked">
                        <label for="punctuation" class="ml-2 text-gray-700">Punctuation</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="digit" class="h-5 w-5 text-blue-500 rounded focus:ring-blue-500" 
                               name="digit" value="1" checked="checked">
                        <label for="digit" class="ml-2 text-gray-700">Digits</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="special" class="h-5 w-5 text-blue-500 rounded focus:ring-blue-500" 
                               name="special" value="1" checked="checked">
                        <label for="special" class="ml-2 text-gray-700">Special</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="bracket" class="h-5 w-5 text-blue-500 rounded focus:ring-blue-500" 
                               name="bracket" value="1" checked="checked">
                        <label for="bracket" class="ml-2 text-gray-700">Bracket</label>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" id="generate-password" 
                            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded flex-1 flex items-center justify-center cursor-pointer">
                        <span class="material-icons mr-2">refresh</span> Get New Password
                    </button>
                    
                    <button type="button" id="copy-password" data-clipboard-action="copy" data-clipboard-target="#passwordHeader"
                            class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded flex-1 flex items-center justify-center cursor-pointer">
                        <span class="material-icons mr-2">content_copy</span> Copy to Clipboard
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script src="js/jquery-3.4.0.min.js"></script>
    <script src="js/clipboard.js"></script>
    <script>
        new ClipboardJS('#copy-password');
        
        $(document).ready(function() {
            $('#f1').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: '<?= pathinfo(__FILE__, PATHINFO_BASENAME) ?>',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        $('#passwordHeader').text(response.randomPassword);
                        document.title = response.randomPassword + ' - generated password (' + $('#len').val() + ')';
                    },
                    error: function(xhr, status, error) {
                        console.error('Error generating password:', error);
                    }
                });
            });
        });
    </script>
</body>
</html>
