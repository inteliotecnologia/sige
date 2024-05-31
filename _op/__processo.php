<?
require_once("conexao.php");
if (pode_algum("p", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_equipamentos_processos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_processo = '". $_GET["id_processo"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Processos de lavagem</h2>

<form action="<?= AJAX_FORM; ?>formProcesso&amp;acao=<?= $acao; ?>" method="post" name="formProcesso" id="formProcesso" onsubmit="return ajaxForm('conteudo', 'formProcesso', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="codigo@vazio|processo@vazio|tempo@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_processo" class="escondido" type="hidden" id="id_processo" value="<?= $rs->id_processo; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="codigo">* Código:</label>
            <input title="Código" name="codigo" value="<?= $rs->codigo; ?>" id="codigo" />
            <br />
            
            <label for="processo">* Processo:</label>
            <input title="Processo" name="processo" value="<?= $rs->processo; ?>" id="processo" />
            <br />
            
            <label for="tempo">* Tempo:</label>
            <input title="Tempo" name="tempo" onkeyup="formataHora(this);" maxlength="8" value="<?= $rs->tempo; ?>" id="tempo" />
            <br />
            
            <label for="carga_maxima">* Carga máxima:</label>
            <input title="Carga máxima" onkeydown="formataValor(this,event);" name="carga_maxima" value="<?= number_format($rs->carga_maxima, 2, ',', '.'); ?>" id="carga_maxima" />
            <br />
            
            <label for="relave">Relave:</label>
            <input type="checkbox" class="tamanho30" value="1" name="relave" id="relave" <? if ($rs->relave==1) echo "checked=\"checked\""; ?> />
            <br />
        </div>
    	<br /><br />
    </fieldset>
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>