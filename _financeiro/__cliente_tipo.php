<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  fi_clientes_tipos
								where id_cliente_tipo = '". $_GET["id_cliente_tipo"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Tipos de clientes</h2>

<form action="<?= AJAX_FORM; ?>formClienteTipo&amp;acao=<?= $acao; ?>" method="post" name="formClienteTipo" id="formClienteTipo" onsubmit="return ajaxFormNormal('validacoes', false, true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="cliente_tipo@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_cliente_tipo" class="escondido" type="hidden" id="id_cliente_tipo" value="<?= $rs->id_cliente_tipo; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="cliente_tipo">* Tipo de cliente:</label>
            <input title="Centro de custo" name="cliente_tipo" value="<?= $rs->cliente_tipo; ?>" id="cliente_tipo" />
            <br />
        </div>
        <br /><br /><br />
        
        <center>
        	<button type="submit" id="enviar">Enviar &raquo;</button>
    	</center>
	    <br />
    </fieldset>
            
</form>
<? } ?>