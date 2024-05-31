<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		if ($_SESSION["id_empresa"]!="")
			$str= "and   id_empresa = '". $_SESSION["id_empresa"] ."' ";
	
		$result= mysql_query("select *, DATE_FORMAT(data_feriado, '%d/%m/%Y') as data_feriado2 from rh_feriados
								where rh_feriados.id_feriado = '". $_GET["id_feriado"] ."'
								$str
								");
							
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Feriados</h2>

<form action="<?= AJAX_FORM; ?>formFeriado&amp;acao=<?= $acao; ?>" method="post" name="formFeriado" id="formFeriado" onsubmit="return validaFormNormal('validacoes', 0, 1);">
    
    <input class="escondido" type="hidden" id="validacoes" value="data_feriado@data|feriado@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_feriado" class="escondido" type="hidden" id="id_feriado" value="<?= $rs->id_feriado; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">		
            
            <label for="data_feriado">* Data feriado:</label>
            <input name="data_feriado" id="data_feriado" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= $rs->data_feriado2; ?>" title="Data do feriado" />
            <br />
            
            <label for="feriado">* Feriado:</label>
            <input title="Feriado" name="feriado" value="<?= $rs->feriado; ?>" id="feriado" />
            <br />
        </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>