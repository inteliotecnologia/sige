<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  fi_depositos
								where id_deposito = '". $_GET["id_deposito"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Depósitos</h2>

<form action="<?= AJAX_FORM; ?>formDeposito&amp;acao=<?= $acao; ?>" method="post" name="formDeposito" id="formDeposito" onsubmit="return ajaxForm('conteudo', 'formDeposito', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="deposito@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_deposito" class="escondido" type="hidden" id="id_deposito" value="<?= $rs->id_deposito; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="deposito">* Depósito:</label>
            <input title="Centro de custo" name="deposito" value="<?= $rs->deposito; ?>" id="deposito" />
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