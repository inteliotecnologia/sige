<?
require_once("conexao.php");
if (pode("ps", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_suja_pesagem
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_pesagem = '". $_GET["id_pesagem"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<h2>Pesagem de roupa suja</h2>

<div class="parte80">
    <form action="<?= AJAX_FORM; ?>formPesagemSuja&amp;acao=<?= $acao; ?>" method="post" name="formPesagemSuja" id="formPesagemSuja" onsubmit="return validaFormNormal('validacoes');">
        
        <input class="escondido" type="hidden" id="validacoes" value="id_remessa@vazio@data_remessa|id_cliente@vazio<? if ($acao=='e') { ?>|peso@vazio<? } ?>" />
        <? if ($acao=='e') { ?>
        <input name="id_pesagem" class="escondido" type="hidden" id="id_pesagem" value="<?= $rs->id_pesagem; ?>" />
        <? } ?>
        
        <fieldset>
            <legend>Dados</legend>
            
            <div class="parte50 gigante">
                <?
                $result_remessa= mysql_query("select * from op_suja_remessas
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by data_remessa desc, hora_chegada desc limit 1
                                                ");
                $rs_remessa= mysql_fetch_object($result_remessa);
                
                if ($acao=='i') $id_remessa= $rs_remessa->id_remessa;
                else $id_remessa= $rs->id_remessa;
                ?>
                <label for="data_remessa">* Data/nº remessa:</label>
                <input id="data_remessa" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="data_remessa" class="tamanho25p" value="<?= desformata_data(pega_dado_remessa("data_remessa", $id_remessa)); ?>" title="Data da remessa" onkeyup="formataData(this);" maxlength="10" onblur="pegaRemessa(); buscaRemessasDoDia();" />
                
                <input id="num_remessa" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="num_remessa" class="tamanho15p" value="<?= pega_dado_remessa("num_remessa", $id_remessa); ?>" title="Número da remessa" onblur="pegaRemessa();" />
                <br />
                
                <label>&nbsp;</label>
                <div id="remessa_atualiza" class="gigante_textox">
                    <input id="id_remessa" name="id_remessa" value="<?= $rs->id_remessa; ?>" title="Remessa" class="escondido" />
                </div>
                <br /><br />
                
                <?
                if ($acao=='i') $data_pesagem= date("d/m/Y");
                else $data_pesagem= desformata_data($rs->data_pesagem);
                ?>
                <label for="data_pesagem">* Data/hora:</label>
                <input id="data_pesagem" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="data_pesagem" class="tamanho25p" value="<?= $data_pesagem; ?>" title="Data da pesagem" onkeyup="formataData(this);" maxlength="10" />
                
                <?
                if ($acao=='i') $hora_pesagem= date("H:i:s");
                else $hora_pesagem= $rs->hora_pesagem;
                ?>
                <input id="hora_pesagem" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="hora_pesagem" class="tamanho25p" value="<?= $hora_pesagem; ?>" title="Hora da pesagem"  onkeyup="formataHora(this);" maxlength="8" />
                <br /><br />
                
                <label for="goma">Goma:</label>
                <input type="checkbox" class="tamanho30" value="1" name="goma" id="goma" <? if ($rs->goma==1) echo "checked=\"checked\""; ?> />
                <br />
            </div>
            <div class="parte50 gigante">
                <?
                $codigo_cliente= pega_codigo_do_cliente($rs->id_cliente)
                ?>
                <label for="cliente">* Cliente:</label>
                <input id="cliente" name="cliente" value="<?= $codigo_cliente; ?>" class="tamanho15p espaco_dir" onblur="pegaCliente(this.value);" />
                <div id="cliente_atualiza">
                    <input id="id_cliente" name="id_cliente" value="<?= $rs->id_cliente; ?>" title="Cliente" class="escondido" />
                </div>
                <br />
                
                <? if ($acao=="e") { ?>
                <label for="peso">* Peso:</label>
                <? if ($acao=='i') $peso= ""; else $peso= fnum($rs->peso); ?>
                <input id="peso" name="peso" class="espaco_dir tamanho15p" value="<?= $peso; ?>" onkeydown="formataValor(this,event);" title="Peso do carrinho" /> kg
                <br />
                
                <label for="hampers">* Hampers:</label>
                <? if ($acao=='i') $hampers= ""; else $hampers= $rs->hampers; ?>
                <input id="hampers" name="hampers" class="espaco_dir tamanho15p" value="<?= $hampers; ?>" title="Hampers" />
                <br />
                
                <? } else { ?>
                <fieldset>
                    <legend>Carrinhos</legend>
                    
                    <div id="pesagens">
                        <?
                        for ($i=1; $i<6; $i++) {
                            $tab= $i+7;
                        ?>
                        <div id="div_pesagem_<?=$i;?>">
                            <code class="escondido"></code>
                            
                            <label for="peso_<?=$i;?>"><?=$i;?> (peso/hampers):</label>
                            <? $peso= ""; ?>
                            <input  id="peso_<?=$i;?>" name="peso[]" class="espaco_dir tamanho15p" value="<?= $peso; ?>" onkeydown="formataValor(this,event);" title="Peso do carrinho" />
                            <input  id="hampers_<?=$i;?>" name="hampers[]" class="espaco_dir tamanho15p" value="<?= $hampers; ?>" title="Hampers" />
                            <a href="javascript:void(0);" onclick="removeDiv('pesagens', 'div_pesagem_<?=$i;?>');">remover</a>
                            <br />
                        </div>
                        <? } ?>
                    </div>
                    
                    <label>&nbsp;</label>
                    <button type="button" onclick="criaEspacoSujaPesagem();">+ carrinho</button>
                    <br />
                </fieldset>
                <br />
                <? } ?>
                
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
	daFoco("cliente");
	pegaRemessa();
	buscaRemessasDoDia();
	
	<? if ($acao=='e') { ?>
	pegaCliente(<?= $codigo_cliente; ?>);
	<? } ?>
</script>
<? } ?>