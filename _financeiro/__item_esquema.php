<?
if (pode("tqi", $_SESSION["permissao"])) {
	if ($_GET["letra"]!="") $letra= $_GET["letra"];
	if ($_POST["letra"]!="") $letra= $_POST["letra"];
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Produtos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=financeiro/item&amp;acao=i">inserir</a></li>
    <!-- <li><a href="./?pagina=financeiro/item_buscar">buscar</a></li> -->
</ul>

<? if ($_POST["geral"]!="1") { ?>
<div class="div_abas screen" id="aba_letras">
    <ul class="abas">
        <? for ($i='a'; $i!="aa"; $i++) { ?>
        <li id="aba_letra_<?=$i;?>" <? if ($i==$letra) echo "class=\"atual\""; ?>><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_letra_<?=$i;?>', 'aba_letras'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/item_listar&amp;letra=<?= $i; ?>');"><?= strtoupper($i); ?></a></li>
        <? } ?>
        <li id="aba_letra_<?=$i;?>" <? if ($i==$letra) echo "class=\"atual\""; ?>><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_letra_outros', 'aba_letras'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/item_listar&amp;letra=.');">...</a></li>
    </ul>
</div>
<? } ?>

<br />
<div id="conteudo_interno">
    <? require_once("_financeiro/__item_listar.php"); ?>
</div>

<? } ?>