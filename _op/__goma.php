<?
require_once("conexao.php");
if (pode("psl", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_suja_gomas
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_goma = '". $_GET["id_goma"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<h2>Área suja - Goma</h2>

<?
$result_teste= mysql_query("select * from op_suja_gomas_correcoes
							where id_goma = '". $rs->id_goma ."'
							and   id_empresa = '". $_SESSION["id_empresa"] ."'
							order by id_correcao asc
							");
$linhas_teste= mysql_num_rows($result_teste);

if ($linhas_teste>0) {
?>

<fieldset>
	<legend>Alterações</legend>
    
    <ul class="recuo1">
	    <?
        while ($rs_teste= mysql_fetch_object($result_teste)) {
			if ($rs_teste->qtde!="") {
		?>
    	<li><?= "<strong>". pega_nome_pelo_id_usuario($rs_teste->id_usuario) ."</strong> alterou a quantidade de <strong>". $rs_teste->qtde_anterior ."</strong> para <strong>". $rs_teste->qtde ."</strong> em <strong>". desformata_data($rs_teste->data) ."</strong> às <strong>". $rs_teste->hora ."</strong>."; ?></li>
	    <?
        	}//fim if
			if ($rs_teste->peso!="") {
		?>
    	<li><?= "<strong>". pega_nome_pelo_id_usuario($rs_teste->id_usuario) ."</strong> alterou o peso de <strong>". fnumf($rs_teste->peso_anterior) ." kg</strong> para <strong>". fnumf($rs_teste->peso) ." kg</strong> em <strong>". desformata_data($rs_teste->data) ."</strong> às <strong>". $rs_teste->hora ."</strong>."; ?></li>
	    <?
        	}//fim if
		}//fim while
		?>
    </ul>

</fieldset>

<? } ?>

<form action="<?= AJAX_FORM; ?>formGoma&amp;acao=<?= $acao; ?>" method="post" name="formGoma" id="formGoma" onsubmit="return validaFormNormal('validacoes', false, 1);">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_remessa@vazio@data_remessa|data_goma@data|id_cliente@vazio@cliente" />
    <? if ($acao=='e') { ?>
    <input name="id_goma" class="escondido" type="hidden" id="id_goma" value="<?= $rs->id_goma; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50 gigante">
            <?
			/*$result_remessa= mysql_query("select * from op_suja_remessas
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											order by data_remessa desc, hora_chegada desc limit 1
											");
			$rs_remessa= mysql_fetch_object($result_remessa);*/
			
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
			if ($acao=='i') $data_goma= date("d/m/Y");
			else $data_goma= desformata_data($rs->data_goma);
			?>
            <label for="data_goma">* Data:</label>
            <input id="data_goma" name="data_goma" class="tamanho25p" value="<?= $data_goma; ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
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
            
        </div>
        <div class="parte50 gigante">
        	
			<?
			if ($acao=='i') {
				$j=1;
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
				<input id="qtde_<?= $rs_peca->id_peca; ?>" name="qtde[]" class="espaco_dir tamanho15p" value="" title="Quantidade" /> <div class="flutuar_esquerda espaco_dir">un.</div>
                
            	<input id="peso_<?= $rs_peca->id_peca; ?>" name="peso[]" class="espaco_dir tamanho15p" onkeydown="formataValor(this,event);" value="" title="Peso" /> <div class="flutuar_esquerda">kg.</div>
            
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
											and   status_peca = '1'
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

            <label for="peso">* Peso:</label>
            <input id="peso" name="peso" class="espaco_dir tamanho25p" onkeydown="formataValor(this,event);" value="<?= fnumf_naozero($rs->peso); ?>" title="Peso" /> kg.
            
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
	daFoco("data_remessa");
	
	<? if ($acao=='e') { ?>
	pegaCliente(<?= $codigo_cliente; ?>);
	<? } ?>
</script>
<? } ?>