<?
require_once("conexao.php");
if (pode("rhv412", $_SESSION["permissao"])) {
	if ($_GET["aba_atual"]!="") $aba_atual= $_GET["aba_atual"];
	if ($_POST["aba_atual"]!="") $aba_atual= $_POST["aba_atual"];
	
	if ($_GET["pagina_inclui"]!="") $pagina_inclui= $_GET["pagina_inclui"];
	if ($_POST["pagina_inclui"]!="") $pagina_inclui= $_POST["pagina_inclui"];
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<?
$result_resumo= mysql_query("select * from pessoas
								where id_pessoa = '". $_GET["id_cliente"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
$rs_resumo= mysql_fetch_object($result_resumo);
?>

<h2>Cliente - <?= $rs_resumo->apelido_fantasia; ?></h2>

<? if (pode("v12", $_SESSION["permissao"])) { ?>
<div class="div_abas screen" id="aba_opcoes_clientes">
    <ul class="abas">
        <? if (pode("v1", $_SESSION["permissao"])) { ?>
        <li id="aba_clientes_dados" <? if ($aba_atual!="parametros") { ?> class="atual" <? } ?>><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_clientes_dados', 'aba_opcoes_clientes'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/pessoa&amp;acao=e&amp;id_cliente=<?= $_GET["id_cliente"]; ?>&amp;tipo_pessoa=c&amp;esquema=1');">Dados</a></li>
        <? } ?>
        <? if (pode("v12", $_SESSION["permissao"])) { ?>
        <li id="aba_clientes_contatos"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_clientes_contatos', 'aba_opcoes_clientes'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/ad_listar&amp;id_cliente=<?= $_GET["id_cliente"]; ?>&amp;esquema=1');">Contatos</a></li>
        <? if ($rs_resumo->status_pessoa!=3) { ?>
        <li id="aba_clientes_servicos"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_clientes_servicos', 'aba_opcoes_clientes'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_servicos_geral&amp;id_cliente=<?= $_GET["id_cliente"]; ?>&amp;esquema=1');">Serviços contratados</a></li>
        <li id="aba_clientes_parametros" <? if ($aba_atual=="parametros") { ?> class="atual" <? } ?>><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_clientes_parametros', 'aba_opcoes_clientes'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pecas&amp;id_cliente=<?= $_GET["id_cliente"]; ?>&amp;esquema=1');">Parâmetros</a></li>
        <li id="aba_clientes_pesquisa"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_clientes_pesquisa', 'aba_opcoes_clientes'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pesquisa&amp;id_cliente=<?= $_GET["id_cliente"]; ?>&amp;esquema=1');">Pesquisa de satisfação</a></li>
        <? } ?>
        <li id="aba_clientes_historico"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_clientes_historico', 'aba_opcoes_clientes'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_historico&amp;id_cliente=<?= $_GET["id_cliente"]; ?>&amp;esquema=1');">Histórico</a></li>
        <? } ?>
    </ul>
</div>
<? } ?>

<div id="conteudo_interno">
	<?
	$acao="e";
	$esquema=1;
	
    if ($pagina_inclui=="") $pagina_inclui= "_financeiro/__pessoa";
	
	require_once($pagina_inclui .".php");
	?>
</div>
<? } ?>