<?
if (pode_algum("rm", $_SESSION["permissao"])) {
	
	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	else $periodo= date("m/Y");
	
	if ($_POST["id_cliente"]!="") $str= " and   id_cliente= '". $_POST["id_cliente"] ."' ";
	
	$periodo= explode("/", $periodo);
?>

<h2>Produção da Área Limpa</h2>

<? if ( ($_POST["geral"]=="1") && (pode('p', $_SESSION["permissao"])) ) { ?>
<ul class="recuo1">
    <li><a target="_blank" href="index2.php?pagina=op/producao_relatorio&amp;periodo=<?= $_POST["periodo"]; ?>&amp;id_cliente=<?= $_POST["id_cliente"]; ?>">gerar relatório mensal</a></li>
</ul>
<? } } ?>