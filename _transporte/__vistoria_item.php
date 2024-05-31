<?
require_once("conexao.php");
if (pode_algum("pue", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  tr_vistorias_itens
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_item = '". $_GET["id_item"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Vistoria - Itens</h2>

<form action="<?= AJAX_FORM; ?>formVistoriaItem&amp;acao=<?= $acao; ?>" method="post" name="formVistoriaItem" id="formVistoriaItem" onsubmit="return validaFormNormal('validacoes', false, 1);">
    
    <input class="escondido" type="hidden" id="validacoes" value="item@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_item" class="escondido" type="hidden" id="id_item" value="<?= $rs->id_item; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="item">* Item:</label>
            <input title="Item" name="item" value="<?= $rs->item; ?>" id="item" />
            <br />
            
            <label for="ordem">* Ordem:</label>
            <input title="Ordem" name="ordem" value="<?= $rs->ordem; ?>" id="ordem" />
            <br />
        </div>
        
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>
<? } ?>