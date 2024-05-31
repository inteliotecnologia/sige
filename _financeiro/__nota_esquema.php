<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	if ($_GET["id_nota"]!="") $id_nota= $_GET["id_nota"];
	if ($_POST["id_nota"]!="") $id_nota= $_POST["id_nota"];
	
	$tipo_nota= pega_tipo_nota($id_nota);
	
	if ($tipo_nota=='p') $tit6= "Pagamento";
	else $tit6= "Recebimento";
	
	if ($pagina_inclui=="") $pagina_inclui="_financeiro/__nota_detalhamento.php";
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Duplicatas</h2>

<div class="div_abas screen" id="aba_opcoes_nota">
    <ul class="abas">
        <? /*<li <? if ($pagina_inclui=="") { ?> class="atual" <? } ?> id="aba_nota_geral"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_nota_geral', 'aba_opcoes_nota'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/nota&amp;id_nota=<?= $id_nota; ?>&amp;acao=e');">Ddetalhamento</a></li>*/ ?>
        <li <? if ($pagina_inclui=="_financeiro/__nota_detalhamento.php") { ?> class="atual" <? } ?> id="aba_nota_detalhamento"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_nota_detalhamento', 'aba_opcoes_nota'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/nota_detalhamento&amp;acao=e&amp;id_nota=<?= $id_nota; ?>');">Detalhamento</a></li>
        <li <? if ($pagina_inclui=="_financeiro/__nota_pagamento.php") { ?> class="atual" <? } ?> id="aba_nota_pagamento"><a href="javascript:void(0);" onclick="atribuiAbaAtual('aba_nota_pagamento', 'aba_opcoes_nota'); ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/nota_pagamento&amp;acao=e&amp;id_nota=<?= $id_nota; ?>');"><?= $tit6; ?></a></li>
    </ul>
</div>

<div id="conteudo_interno">
	<?
	require_once($pagina_inclui);
	?>
</div>
<? } ?>