<?
require_once("conexao.php");
if (pode_algum("p", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from op_acompanhamento_itens
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_acompanhamento_item = '". $_GET["id_acompanhamento_item"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Checklist</h2>

<form action="<?= AJAX_FORM; ?>formAcompanhamentoItem&amp;acao=<?= $acao; ?>" method="post" name="formAcompanhamentoItem" id="formAcompanhamentoItem" onsubmit="return ajaxForm('conteudo', 'formAcompanhamentoItem', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="acompanhamento_item@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_acompanhamento_item" class="escondido" type="hidden" id="id_acompanhamento_item" value="<?= $rs->id_acompanhamento_item; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="Item">* Item:</label>
            <input title="Item" name="acompanhamento_item" value="<?= $rs->acompanhamento_item; ?>" id="acompanhamento_item" />
            <br />
            
        </div>
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>
<? } ?>