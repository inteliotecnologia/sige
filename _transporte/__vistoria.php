<?
require_once("conexao.php");
if (pode("ey", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from tr_vistorias
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_vistoria = '". $_GET["id_vistoria"] ."'
								");
							
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Vistorias</h2>

<form action="<?= AJAX_FORM; ?>formVistoria&amp;acao=<?= $acao; ?>" method="post" name="formVistoria" id="formVistoria" onsubmit="return validaFormNormal('validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_veiculo@vazio|km@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_vistoria" class="escondido" type="hidden" id="id_vistoria" value="<?= $rs->id_vistoria; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="id_veiculo">Veículo:</label>
            <select name="id_veiculo" id="id_veiculo" title="Veículo" onchange="pegaDadosVeiculo(this.value);">
            	<? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
                
                <?
                $result_vei= mysql_query("select * from op_veiculos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
                                            order by veiculo asc,
                                            placa asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_vei = mysql_fetch_object($result_vei)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_vei->id_veiculo; ?>" <? if ($rs_vei->id_veiculo==$rs->id_veiculo) echo "selected=\"selected\""; ?>><?= $rs_vei->veiculo ." ". $rs_vei->placa; ?></option>
                <? $i++; } ?>
            </select>
            <br /><br />
            
            <div id="div_veiculo">
            </div>
            <br />
            
            <label for="km">* Kilometragem:</label>
            <input name="km" id="km" value="<?= fnum($rs->km); ?>" title="Kilometragem" onkeydown="formataValor(this,event);" class="tamanho25p" />
            <br />
            
            <?
			if ($acao=='i') $data_vistoria= date("d/m/Y");
			else $data_vistoria= desformata_data($rs->data_vistoria);
			?>
			<label for="data_vistoria">Data:</label>
			<input name="data_vistoria" id="data_vistoria" class="tamanho25p" onkeyup="formataData(this);" maxlength="10" value="<?= $data_vistoria; ?>" title="Data" />
			<br />
            
			<?
			if ($acao=='i') $hora_vistoria= date("H:i:s");
			else $hora_vistoria= $rs->hora_vistoria;
			?>
			<label for="hora_vistoria">Hora:</label>
			<input name="hora_vistoria" id="hora_vistoria" class="tamanho25p" onkeyup="formataHora(this);" maxlength="5" value="<?= $hora_vistoria; ?>" title="Hora" />
            <br /><br />
            
            <label for="obs">Observações:</label>
            <textarea name="obs" id="obs" title="Observações"><?=$rs->obs;?></textarea>
            <br />
            
        </div>
        <div class="parte50">
            <fieldset>
                <legend>Checklist</legend>
                
                <?
				$result_itens= mysql_query("select * from tr_vistorias_itens
										   	where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   status_item <> '2'
											order by ordem asc
											");
				
				$i=1;
				
				while ($rs_itens= mysql_fetch_object($result_itens)) {
					$result_che= mysql_query("select * from tr_vistorias_itens_checklist
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_vistoria = '". $rs->id_vistoria ."'
												and   id_item = '". $rs_itens->id_item ."'
												");
					$rs_che= mysql_fetch_object($result_che);
				?>
                <input name="id_item[]" type="hidden" class="escondido" value="<?= $rs_itens->id_item; ?>" />
                
				<label for="valor_<?=$i;?>"><?= $rs_itens->item; ?>:</label>
				<input name="valor[]" id="valor_<?=$i;?>" value="<?= $rs_che->valor; ?>" />
				<br />
				
				<? $i++; } ?>
                
			</fieldset>
        </div>
        
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>

<? } ?>