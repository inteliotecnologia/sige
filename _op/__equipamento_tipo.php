<?
require_once("conexao.php");
if (pode_algum("p", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from op_equipamentos_tipos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_equipamento_tipo = '". $_GET["id_equipamento_tipo"] ."'
								") or die(mysql_error());
		
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Equipamentos (tipo)</h2>

<form action="<?= AJAX_FORM; ?>formEquipamentoTipo&amp;acao=<?= $acao; ?>" method="post" name="formEquipamentoTipo" id="formEquipamentoTipo" onsubmit="return ajaxForm('conteudo', 'formEquipamentoTipo', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="equipamento_tipo@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_equipamento_tipo" class="escondido" type="hidden" id="id_equipamento_tipo" value="<?= $rs->id_equipamento_tipo; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="equipamento_tipo">* Tipo:</label>
            <input title="Equipamento" name="equipamento_tipo" value="<?= $rs->equipamento_tipo; ?>" id="equipamento_tipo" />
            <br />
            
        </div>
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>
<? } ?>