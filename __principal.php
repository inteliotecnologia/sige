<? if ($_SESSION["id_usuario"]!="") { ?>
<h2 class="titulos">Página principal</h2>

<p>Seja bem vindo(a) ao <em><? include("titulo.php"); ?></em>.</p>

<br />

<?
/*
require_once("funcoes_espelho.php");

$retorno= pega_dados_rh($_SESSION["id_empresa"], 0, 0, 103, "17/01/2010", "17/01/2010");
$novo= explode("@", $retorno);

echo(calcula_total_horas($novo[3]));
*/
}
else {
	$erro_a= 3;
	include("__erro_acesso.php");
}
?>