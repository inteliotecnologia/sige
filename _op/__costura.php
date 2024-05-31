<?
require_once("conexao.php");
if (pode("pl(", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_limpa_costura
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_costura = '". $_GET["id_costura"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<h2>Área limpa - Costura</h2>

<form action="<?= AJAX_FORM; ?>formCostura&amp;acao=<?= $acao; ?>" method="post" name="formCostura" id="formCostura" onsubmit="return validaFormNormal('validacoes', false, 1);">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_remessa@vazio@data_remessa|data_costura@data|id_cliente@vazio@cliente" />
    <? if ($acao=='e') { ?>
    <input name="id_costura" class="escondido" type="hidden" id="id_costura" value="<?= $rs->id_costura; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50 gigante">
            <?
			/*$result_remessa= mysql_query("select * from op_suja_remessas
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											order by data_remessa desc, hora_chegada desc limit 1
											");
			$rs_remessa= mysql_fetch_object($result_remessa);
			
			//if ($acao=='i') $id_remessa= $rs_remessa->id_remessa;
			//else $id_remessa= $rs->id_remessa;
			?>
            <label for="data_remessa">* Data/nº remessa:</label>
            <input id="data_remessa" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="data_remessa" class="tamanho25p" value="<?= desformata_data(pega_dado_remessa("data_remessa", $rs->id_remessa)); ?>" title="Data da remessa" onkeyup="formataData(this);" maxlength="10" onblur="pegaRemessa();" />
            
            <input id="num_remessa" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="num_remessa" class="tamanho15p" value="<?= pega_dado_remessa("num_remessa", $rs->id_remessa); ?>" title="Número da remessa" onblur="pegaRemessa();" />
            
            <div id="remessa_atualiza" class="gigante_textox">
                <input id="id_remessa" name="id_remessa" value="<?= $rs->id_remessa; ?>" title="Remessa" class="escondido" />
            </div>
            <br /><br />
            
            <?
			*/
			
			if ($acao=='i') $data_costura= date("d/m/Y");
			else $data_costura= desformata_data($rs->data_costura);
			?>
            <label for="data_costura">* Data:</label>
            <input id="data_costura" name="data_costura" class="tamanho25p" value="<?= $data_costura; ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
			<br />
            
            <?
			$codigo_cliente= pega_codigo_do_cliente($rs->id_cliente)
			?>
        	<label for="cliente">* Cliente:</label>
            <input id="cliente" name="cliente" value="<?= $codigo_cliente; ?>" class="tamanho25p espaco_dir" onblur="pegaCliente(this.value);" />
            <div id="cliente_atualiza">
            	<input id="id_cliente" name="id_cliente" value="<?= $rs->id_cliente; ?>" title="Cliente" class="escondido" />
            </div>
            <br />
            
            <? /*
            <label for="obs">Observações:</label>
            <textarea name="obs" id="obs" title="Observações"><?=$rs->obs;?></textarea>
            <br />
            */ ?>
            
        </div>
        <div class="parte50 gigante">
        	
			<?
			if ($acao=='i') {
				$result_pecas= mysql_query("select * from op_limpa_pecas
										   	where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   status_peca = '1'
											order by peca asc
											");
				$j=1;
				while ($rs_peca= mysql_fetch_object($result_pecas)) {
				?>
				
                <input name="id_peca[]" class="escondido" type="hidden" value="<?= $rs_peca->id_peca; ?>" />
                
				<label for="qtde_<?= $rs_peca->id_peca; ?>">* <?= $rs_peca->peca; ?>:</label>
				<input id="qtde_<?= $rs_peca->id_peca; ?>" name="qtde[]" class="espaco_dir tamanho25p" value="" title="Quantidade" /> un.
				<br />
				
            <?
            		$j++;
				}
			?>
            </select>
            <br />
            <? } else { ?>
            
            <label for="id_peca">* Roupa:</label>
            <select id="id_peca" name="id_peca" size="5">
            	<?
				$result_pecas= mysql_query("select * from op_limpa_pecas
										   	where id_empresa = '". $_SESSION["id_empresa"] ."'
											order by peca asc
											");
				$j=1;
				while ($rs_peca= mysql_fetch_object($result_pecas)) {
				?>
                <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_peca->id_peca; ?>" <? if ($rs_peca->id_peca==$rs->id_peca) echo "selected=\"selected\""; ?>><?= $rs_peca->peca; ?></option>
                <? $j++; } ?>
            </select>
            <br />
            
            <label for="qtde">* Quantidade:</label>
            <input id="qtde" name="qtde" class="espaco_dir tamanho25p" value="<?= $rs->qtde; ?>" title="Quantidade" /> un.
            <br />
            
            <? } ?>
            
            <br /><br />
        </div>
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>

<script language="javascript" type="text/javascript">
	//pegaRemessa(<?= $id_remessa; ?>);
	daFoco("cliente");
	
	<? if ($acao=='e') { ?>
	pegaCliente(<?= $codigo_cliente; ?>);
	<? } ?>
</script>
<? } ?>