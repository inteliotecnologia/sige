<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode_algum("i12", $_SESSION["permissao"])) {
?>

<img src="index2.php?pagina=mini&amp;foto=<?= $_GET["foto"]; ?>&amp;l=780" alt="" width="780" />

<? } ?>