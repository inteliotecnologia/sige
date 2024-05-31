<?
require_once("conexao.php");
if (pode("rhv4", $_SESSION["permissao"])) {
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Funcionário(a) - <?= pega_funcionario($_GET["id_funcionario"]); ?></h2>

<? if (pode("rhv4", $_SESSION["permissao"])) { ?>
<div class="div_abas screen" id="aba_opcoes_funcionario">
    <ul class="abas">
        <? if (pode("rv4", $_SESSION["permissao"])) { ?>
        <li id="aba_funcionario_dados" class="atual"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_dados', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/funcionario&amp;acao=e&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>');">Dados</a></li>
        <? } ?>
        <? if (pode("rv", $_SESSION["permissao"])) { ?>
        <li id="aba_funcionario_carreira"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_carreira', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/carreira&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>');">Carreira</a></li>
        <? } ?>
        <? if (pode("rvh4", $_SESSION["permissao"])) { ?>
        <li id="aba_funcionario_espelho"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_espelho', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/espelho_busca&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>');">Folha ponto</a></li>
        <li id="aba_funcionario_banco"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_banco', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/banco&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>');">BH</a></li>
        <? } ?>
        <? if (pode("rv", $_SESSION["permissao"])) { ?>
        <li id="aba_funcionario_historico"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_historico', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/historico_listar&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>');">Histórico</a></li>
        <? } ?>
        <? if (pode("r", $_SESSION["permissao"])) { ?>
        <li id="aba_funcionario_descontos"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_descontos', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/desconto_busca&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>');">Descontos</a></li>
        <? } ?>
        <? if (pode("rv", $_SESSION["permissao"])) { ?>
        <li id="aba_funcionario_atestado"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_atestado', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/afastamento&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>&amp;tipo_afastamento=a&amp;esquema=1');">Atestados</a></li>
        <li id="aba_funcionario_pericia"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_pericia', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/afastamento&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>&amp;tipo_afastamento=p&amp;esquema=1');">Perícias</a></li>
        <li id="aba_funcionario_ferias"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_ferias', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/afastamento&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>&amp;tipo_afastamento=f&amp;esquema=1');">Férias</a></li>
        <li id="aba_funcionario_outros"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_outros', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/afastamento&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>&amp;tipo_afastamento=o&amp;esquema=1');">Outros abonos</a></li>
        <? } ?>
        <? if (pode("rhv", $_SESSION["permissao"])) { ?>
        <li id="aba_funcionario_he_autorizacao"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_he_autorizacao', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/he_autorizacao&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>');">A. HE</a></li>
        <li id="aba_funcionario_substituicao"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_substituicao', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/substituicao_funcao&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>');">Subs.</a></li>
        <? } ?>
        <? if (pode("rvh4", $_SESSION["permissao"])) { ?>
        <li id="aba_funcionario_advertencias"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_advertencias', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/afastamento&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>&amp;tipo_afastamento=d&amp;esquema=1');">Advertência</a></li>
        <li id="aba_funcionario_suspensao"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_suspensao', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/afastamento&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>&amp;tipo_afastamento=s&amp;esquema=1');">Suspensão</a></li>
        <? } ?>
        <? if (pode("r", $_SESSION["permissao"])) { ?>
        <li id="aba_funcionario_vt"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_vt', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/vt&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>&amp;esquema=1');">VT</a></li>
        <? } ?>
        <? if (pode("r", $_SESSION["permissao"])) { ?>
        <li id="aba_funcionario_insalubridade"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_insalubridade', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/insalubridade&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>&amp;esquema=1');">Insal.</a></li>
        <? } ?>
        <? if ($_SESSION["tipo_usuario"]=='a') { ?>
        <li id="aba_funcionario_departamento_permissao"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_funcionario_departamento_permissao', 'aba_opcoes_funcionario'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/carreira_departamento_permissao&amp;id_funcionario=<?= $_GET["id_funcionario"]; ?>&amp;esquema=1');">Livro</a></li>
        <? } ?>
    </ul>
</div>
<? } ?>

<div id="conteudo_interno">
	<?
    if ($pagina_inclui=="") $pagina_inclui= "_rh/__funcionario.php";
	require_once($pagina_inclui);
	?>
</div>
<? } ?>