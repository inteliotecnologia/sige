<?
if (pode("t", $_SESSION["permissao"])) {
	if ($_GET["letra"]!="") $letra= $_GET["letra"];
	if ($_POST["letra"]!="") $letra= $_POST["letra"];
	
	if ($_GET["tipo_contato"]!="") $tipo_contato= $_GET["tipo_contato"];
	if ($_POST["tipo_contato"]!="") $tipo_contato= $_POST["tipo_contato"];
	
	if ($_GET["status_funcionario"]!="") $status_funcionario= $_GET["status_funcionario"];
	if ($_POST["status_funcionario"]!="") $status_funcionario= $_POST["status_funcionario"];
	
	if ($tipo_contato==2) $tit_add= ativo_inativo($status_funcionario);
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Contatos telefônicos - <?= pega_tipo_contato($tipo_contato); ?> <?= strtolower(strip_tags($tit_add)) ."s"; ?></h2>

<ul class="recuo1">
	<li><a href="./?pagina=contatos/contato&amp;acao=i&amp;tipo_contato=<?=$tipo_contato;?>">inserir</a></li>
    <li><a href="./?pagina=contatos/contato_buscar&amp;tipo_contato=<?=$tipo_contato;?>">buscar</a></li>
</ul>

<div id="legenda">
	<ul>
    	<li class="preto">Residencial</li>
        <li class="azul">Comercial</li>
        <li class="verde">Celular</li>
        <li class="vermelho">Fax</li>
        <li class="cinza">Outros</li>
    </ul>
</div>

<? if ($_POST["geral"]!="1") { ?>
<div class="div_abas screen" id="aba_letras">
    <ul class="abas">
        <? for ($i='a'; $i!="aa"; $i++) { ?>
        <li id="aba_letra_<?=$i;?>" <? if ($i==$letra) echo "class=\"atual\""; ?>><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_letra_<?=$i;?>', 'aba_letras'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=contatos/contato_listar&amp;tipo_contato=<?= $tipo_contato; ?>&amp;status_funcionario=<?= $status_funcionario; ?>&amp;letra=<?= $i; ?>');"><?= strtoupper($i); ?></a></li>
        <? } ?>
    </ul>
</div>
<? } ?>

<div id="conteudo_interno">
    <? require_once("_contatos/__contato_listar.php"); ?>
</div>

<? } ?>