<?php
if(!isset($_GET['pag'])){
	header("Location: main.php?pag=1");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Page Title</title>
</head>
<body>
<h3>MOVIES</h3>

<?php 
$content = connectAndCreateCache(); //array
$count =  count($content); //45
$p = $count / 2; //22.5
$nr = ceil($p); //23 (pages)

addLinks($nr); //create links
 
// PAGINATE	
	for($j = 1; $j < $count; $j++){ //iterez prin array
		$g = ($j * 2) - 2;    // din 2 in 2 (0, 2, 4, 6) GENEREAZA POZITIILE DE LA CARE SA INCEP ARRAYUL
		if(isset($_GET['pag']) && $_GET['pag'] == $j){ //la fiecare pagina
		    if (!isset($content[$g+1])) {
                 $content[$g+1] = null;
				 ob1($content[$g]);
                  } else{  
                    echo("<div><br>===========Obiect 1============<br>");	
                    ob1($content[$g]);
			        echo("</div><br>=========Obiect 2===================<br><div>");
			        ob2($content[$g+1]);
                    echo("</div>");			
				  }			
		}
	}
?>
</body>
</html>

<?php
function connectAndCreateCache(){
    $url 			= "https://mgtechtest.blob.core.windows.net/files/showcase.json"; // json source
    $cache 			= "json.cache"; // make this file in same dir
    $force_refresh	 	= false; // dev
    $refresh		= 60*60; // once an hour

    if ($force_refresh || ((time() - filectime($cache)) > ($refresh) || 0 == filesize($cache))) {
        echo "I'm going after URL<br>";
        $ch = curl_init($url) or die("curl issue");
        $curl_options = array(
        CURLOPT_RETURNTRANSFER	=> true,
        CURLOPT_HEADER 		=> false,
        CURLOPT_FOLLOWLOCATION	=> false,
        CURLOPT_ENCODING	=> "",
        CURLOPT_AUTOREFERER 	=> true,
        CURLOPT_CONNECTTIMEOUT 	=> 7, #timeout for the connect phase
        CURLOPT_TIMEOUT 	=> 7, #maximum time the request is allowed to take
        CURLOPT_MAXREDIRS 	=> 3,
        CURLOPT_SSL_VERIFYHOST	=> false,
        CURLOPT_USERAGENT	=> "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13"
        );
        curl_setopt_array($ch, $curl_options);
        $curlcontent = curl_exec($ch);
        curl_close($ch);

        # Write everything into json.cache 
        $handle = fopen($cache, 'wb') or die('no fopen');
        $json_cache = $curlcontent;
        fwrite($handle, $json_cache);
        fclose($handle);

        return json_decode(utf8_encode($curlcontent));

    } else {
        echo "I'm in locally<br>";
        $page = file_get_contents($cache); //locally
        return json_decode(utf8_encode($page));
    }
} // end connect


function addLinks($nr){
	echo "<div>";
for($i = 1; $i < $nr+1; $i++){ //count($_SESSION['baz']) / 2
	echo "| <a href='main.php?pag=$i'>Pagina$i</a> |"; //creez 9 linkuri ORI count(array) si impart la 2 sau cate rezultate vreau sa afisez per page => NR DE PAGINI 10/2=5
}
echo "</div>";
}

function ob1($a){
	echo (strlen($a->body)<1)?"<b>Body is empty</b>":"<b>Body: </b>".$a->body; // BODY	
}

//=============================================OBJECT 2=====================================
function ob2($b){
	echo (strlen($b->body)<1)?"<b>Body is empty</b>":"<b>Body</b>".$b->body; //BODY
}


?>