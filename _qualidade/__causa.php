<?
require_once("conexao.php");
if (pode_algum("p", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from qual_reclamacoes_causas
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_causa = '". $_GET["id_causa"] ."'
								") or die(mysql_error());
		
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Causas de reclamações</h2>

<form action="<?= AJAX_FORM; ?>formReclamacaoCausa&amp;acao=<?= $acao; ?>" method="post" name="formReclamacaoCausa" id="formReclamacaoCausa" onsubmit="return ajaxForm('conteudo', 'formReclamacaoCausa', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="causa@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_causa" class="escondido" type="hidden" id="id_causa" value="<?= $rs->id_causa; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="causa">* Causa:</label>
            <input title="Causa" name="causa" value="<?= $rs->causa; ?>" id="causa" />
            <br />
            
        </div>
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>
<? } ?>