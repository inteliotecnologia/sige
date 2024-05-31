<?
require_once("conexao.php");
if (pode_algum("pkj", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from op_servicos_tipos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_servico_tipo = '". $_GET["id_servico_tipo"] ."'
								") or die(mysql_error());
		
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Serviços (Tipo)</h2>

<form action="<?= AJAX_FORM; ?>formServicoTipo&amp;acao=<?= $acao; ?>" method="post" name="formServicoTipo" id="formServicoTipo" onsubmit="return ajaxForm('conteudo', 'formServicoTipo', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="servico_tipo@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_servico_tipo" class="escondido" type="hidden" id="id_servico_tipo" value="<?= $rs->id_servico_tipo; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="servico_tipo">* Tipo:</label>
            <input title="servico" name="servico_tipo" value="<?= $rs->servico_tipo; ?>" id="servico_tipo" />
            <br />
            
        </div>
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>
<? } ?>