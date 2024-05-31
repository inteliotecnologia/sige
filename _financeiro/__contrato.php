<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from fi_contratos
								where id_contrato= '". $_GET["id_contrato"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Contrato</h2>

<form action="<?= AJAX_FORM; ?>formContrato&amp;acao=<?= $acao; ?>" method="post" name="formContrato" id="formContrato" onsubmit="return ajaxForm('conteudo', 'formContrato', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="contrato@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_contrato" class="escondido" type="hidden" id="id_contrato" value="<?= $rs->id_contrato; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>

        <div class="parte50">
            <label for="contrato">* Contrato:</label>
            <input title="Contrato" name="contrato" value="<?= $rs->contrato; ?>" id="contrato" />
            <br />
            
            <label for="data_contrato">Data:</label>
            <input name="data_contrato" id="data_contrato" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data" />
            <br />
            
            <? /*
            <label for="pesagem_cliente">Cliente pesa?:</label>
            <input name="pesagem_cliente" id="pesagem_cliente" type="checkbox" class="tamanho20" value="1" />
            <br /><br />
            */ ?>
        </div>
        <div class="parte50">
        	
            <label for="tipo_contrato" class="tamanho80">Tipo:</label>
            <select name="tipo_contrato" id="tipo_contrato" title="Tipo">
                <? if ($acao=="i") { ?>
                <option value="">-</option>
                <? } ?>
                
                <?
                $i=0;
                
                $vetor= pega_tipo_contrato('l');
                while ($vetor[$i]) {
                ?>
                <option value="<?=$i;?>" <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> <? if ($i==$rs->tipo_contrato) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
        </div>
        <br /><br /><br /><br />
        
        <center>
        	<button type="submit" id="enviar">Enviar &raquo;</button>
    	</center>
	    <br />
    </fieldset>
</form>
<? } ?>