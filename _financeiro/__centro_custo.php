<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  fi_centro_custos
								where id_centro_custo = '". $_GET["id_centro_custo"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Centro de custo</h2>

<p>* Campos de preenchimento obrigatório.</p>

<form action="<?= AJAX_FORM; ?>formCentroCusto&amp;acao=<?= $acao; ?>" method="post" name="formCentroCusto" id="formCentroCusto" onsubmit="return ajaxForm('conteudo', 'formCentroCusto', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="centro_custo@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_centro_custo" class="escondido" type="hidden" id="id_centro_custo" value="<?= $rs->id_centro_custo; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="centro_custo">* Centro de custo:</label>
            <input title="Centro de custo" name="centro_custo" value="<?= $rs->centro_custo; ?>" id="centro_custo" />
            <br /><br /><br />
        
        <center>
        	<button type="submit" id="enviar">Enviar &raquo;</button>
    	</center>
	    <br />
    </fieldset>
</form>
<? } ?>