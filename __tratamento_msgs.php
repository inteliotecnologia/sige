<?
if ($_SESSION["id_usuario"]!="") {
	
	if ($_GET["msg"]!="") $msg= $_GET["msg"];
	
	if (isset($msg)) {
		if ($msg_str!="") {
			echo "<div class=\"atencao2\">". $msg_str ."</div>";
		}
		elseif (strlen($msg)>5) {
			echo "<div class=\"atencao2\">". $msg ."</div>";
		}
		else {
			if ($msg==0) echo "<div class=\"atencao\">Opera��o realizada com sucesso!</div>";
			else echo "<div class=\"atencao2\">N�o foi poss�vel completar a opera��o!</div>";
		}
	}
}
?>