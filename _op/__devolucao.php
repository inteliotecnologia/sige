<?
require_once("conexao.php");
if (pode("psl", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_suja_devolucao
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_devolucao = '". $_GET["id_devolucao"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<h2>&Aacute;rea suja - Devolução de resíduos</h2>

<div class="parte80">
    <form action="<?= AJAX_FORM; ?>formDevolucao&amp;acao=<?= $acao; ?>" method="post" name="formDevolucao" id="formDevolucao" onsubmit="return validaFormNormal('validacoes', true, 1);">
        
      <input class="escondido" type="hidden" id="validacoes" value="id_remessa@vazio@data_remessa|id_cliente@vazio|peso@vazio" />
        <? if ($acao=='e') { ?>
        <input name="id_devolucao" class="escondido" type="hidden" id="id_devolucao" value="<?= $rs->id_devolucao; ?>" />
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
                */
                
                //if ($acao=='i') $id_remessa= $rs_remessa->id_remessa;
                //else $id_remessa= $rs->id_remessa;
                ?>
                <label for="data_remessa">* Data/nº remessa:</label>
                <input id="data_remessa" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="data_remessa" class="tamanho25p" value="<?= desformata_data(pega_dado_remessa("data_remessa", $rs->id_remessa)); ?>" title="Data da remessa" onkeyup="formataData(this);" maxlength="10" onblur="pegaRemessa(); buscaRemessasDoDia();" />
                
                <input id="num_remessa" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="num_remessa" class="tamanho15p" value="<?= pega_dado_remessa("num_remessa", $rs->id_remessa); ?>" title="Número da remessa" onblur="pegaRemessa();" />
                <br />
                
                <label>&nbsp;</label>
                <div id="remessa_atualiza" class="gigante_textox">
                    <input id="id_remessa" name="id_remessa" value="<?= $rs->id_remessa; ?>" title="Remessa" class="escondido" />
                </div>
                <br />     
                
                <?
                if ($acao=='i') $data_devolucao= date("d/m/Y");
                else $data_devolucao= desformata_data($rs->data_devolucao);
                ?>
                <label for="data_devolucao">* Data/hora:</label>
                <input id="data_devolucao" name="data_devolucao" class="tamanho25p" value="<?= $data_devolucao; ?>" title="Data da devolucao" onkeyup="formataData(this);" maxlength="10" />
                
                <?
                if ($acao=='i') $hora_devolucao= date("H:i:s");
                else $hora_devolucao= $rs->hora_devolucao;
                ?>
                <input id="hora_devolucao" name="hora_devolucao" class="tamanho25p" value="<?= $hora_devolucao; ?>" title="Hora da devolucao"  onkeyup="formataHora(this);" maxlength="8" />
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
                
                <label for="pacotes">* Pacotes:</label>
                <? if ($acao=='i') $pacotes= ""; else $pacotes= $rs->pacotes; ?>
                <input id="pacotes" name="pacotes" class="espaco_dir tamanho25p" value="<?= $pacotes; ?>" title="Pacotes" /> 
                <br />
                
            </div>
            <div class="parte50 gigante">
                <?
                $i= 1;
                $vetor= pega_itens_devolucao('l');
                
                while ($vetor[$i]) {
                    $result_dev= mysql_query("select * from op_suja_devolucao_itens
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   id_devolucao = '". $rs->id_devolucao ."'
                                                and   id_item = '". $i ."'
                                                ");
                    $rs_dev= mysql_fetch_object($result_dev);
                    
                    if ($i==1) {
                        if ($acao=='i') $peso_qtde= "";
                        else $peso_qtde= fnum($rs_dev->peso_qtde);
                        $medida= "kg";
                    }
                    else {
                        if ($acao=='i') $peso_qtde= "";
                        else $peso_qtde= number_format($rs_dev->peso_qtde, 0);
                        $medida= "un.";
                    }
                    
                ?>
                <label for="peso_<?=$i;?>">* <?= $vetor[$i]; ?>:</label>
                <input name="id_item[]" class="escondido" type="hidden" id="id_item_<?=$i;?>" value="<?= $i; ?>" />
                <input id="peso_qtde_<?=$i;?>" name="peso_qtde[]" class="espaco_dir tamanho25p" value="<?= $peso_qtde; ?>" <? if ($i==1) { ?> onkeydown="formataValor(this,event);" <? } ?> title="Peso" /> <?= $medida; ?>
                <br />
                
                <? $i++; } ?>
                
                <br /><br />
                
                <label for="peso">* Peso total:</label>
                <? if ($acao=='i') $peso= ""; else $peso= fnum($rs->peso); ?>
                <input id="peso" name="peso" class="espaco_dir tamanho25p" value="<?= $peso; ?>" onkeydown="formataValor(this,event);" title="Peso total" /> kg
                <br />
                
            </div>
            <br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </fieldset>
    </form>
</div>
<div class="parte20">
	<fieldset>
    	<legend>Remessas do dia</legend>
        
        <div id="div_remessa_lista">
        	Digite a data para buscar as remessas.
        </div>
        <br />

    </fieldset>
</div>

<script language="javascript" type="text/javascript">
	//pegaRemessa(<?= $id_remessa; ?>);
	daFoco("data_remessa");
	
	<? if ($acao=='e') { ?>
	pegaCliente(<?= $codigo_cliente; ?>);
	buscaRemessasDoDia();
	<? } ?>
</script>
<? } ?>