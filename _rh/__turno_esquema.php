<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
?>
<h2>Turnos</h2>

<div class="div_abas" id="aba_opcoes_turnos">
    <ul class="abas">
        <li id="aba_turno_dados" class="atual"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_turno_dados', 'aba_opcoes_turnos'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/turno&amp;acao=e&amp;id_turno=<?= $_GET["id_turno"]; ?>');">Dados do turno</a></li>
        <li id="aba_turno_intervalos"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_turno_intervalos', 'aba_opcoes_turnos'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/turno_intervalo&amp;acao=i&amp;id_turno=<?= $_GET["id_turno"]; ?>');">Intervalos</a></li>
    </ul>
</div>

<div id="conteudo_interno">
	<? require_once("_rh/__turno.php"); ?>
</div>
<? } ?>