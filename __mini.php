<?
require_once("conexao.php");
require_once("funcoes.php");
if (pode_algum("qwertyuiopasdlkjhfgzxcvbnm12", $_SESSION["permissao"])) {
	
	$foto= $_GET["foto"];
	$l= $_GET["l"];
	
	if ($l<1000) {
		$qualidade = 90;
		$foto= CAMINHO . $foto .".jpg";
	
		$originalimage = imagecreatefromjpeg($foto);
		$l_original= imagesx($originalimage);
		$a_original= imagesy($originalimage);
		
		if ($l==0) {
			$l= $l_original;
			$a= $a_original;
		}
		elseif ($a==0) {
			$a_nova= floor(($l*$a_original)/$l_original);
			$a= $a_nova+1;
			
			//if ($a>80) { $a_nova=80; $scr_y= 60; }
			//else $a_nova=$a;
			//$a++;
		}
		else {
			$a_nova= $a;
		}
		
		//cria um quadrado preto com as dimensoes especificadas
		$thumbnail = imagecreatetruecolor($l, $a_nova);
		//poe a imagem resultante no quadrado preto acima
		imagecopyresampled($thumbnail, $originalimage, 0, 0, 0, 0, $l+1, $a+1, 
		imagesx($originalimage), imagesy($originalimage)); 
		header("Content-Type: image/jpeg"); 
		imagejpeg($thumbnail,'',$qualidade); 
		imagedestroy($thumbnail); 
	}
}

?>