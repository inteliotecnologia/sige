<?
require_once("conexao.php");
if (pode("12(", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_limpa_costura_consertos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_costura_conserto = '". $_GET["id_costura_conserto"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<div id="retorno" class="escondido">
</div>
    
<h2>Costura - Conserto de peças</h2>


<form action="<?= AJAX_FORM; ?>formCosturaConserto&amp;acao=<?= $acao; ?>" method="post" name="formCosturaConserto" id="formCosturaConserto" onsubmit="return validaFormNormal('validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="data_chegada@data|id_cliente@vazio@cliente" />
    <? if ($acao=='e') { ?>
    <input name="id_costura_conserto" class="escondido" type="hidden" id="id_costura_conserto" value="<?= $rs->id_costura_conserto; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte30 gigantex">
            <label class="tamanho25p" for="data_chegada">* Data recebimento:</label>
            <input id="data_chegada" name="data_chegada" class="tamanho35p" value="<?= desformata_data($rs->data_chegada); ?>" title="Data recebimento" onkeyup="formataData(this);" maxlength="10" />
            <br />
            
            <? if ($acao=='e') { ?>
            <label class="tamanho25p">* Data entrega (prevista):</label>
            <?= desformata_data(soma_data($rs->data_chegada, 7, 0, 0)); ?>
            <br /><br />
            <? } ?>
            
            <?
            $codigo_cliente= pega_codigo_do_cliente($rs->id_cliente)
            ?>
            <label class="tamanho25p" for="cliente">* Cliente:</label>
            <input id="cliente" name="cliente" value="<?= $codigo_cliente; ?>" class="tamanho35p espaco_dir" onblur="pegaCliente(this.value);" />
            
            <br />
            <div id="cliente_atualiza">
                <input id="id_cliente" name="id_cliente" value="<?= $rs->id_cliente; ?>" title="Cliente" class="escondido" />
            </div>
            <br /><br />
            
            <? if ($acao=='e') { ?>
            <fieldset>
            	<legend>Entrega</legend>
                
                <label class="tamanho25p" for="data_entrega">* Data:</label>
                <input id="data_entrega" name="data_entrega" class="tamanho35p" value="<?= desformata_data($rs->data_entrega); ?>" title="Data entrega" onkeyup="formataData(this);" maxlength="10" />
                <br />
                
                <label class="tamanho25p" for="hora_entrega">* Hora:</label>
                <input id="hora_entrega" name="hora_entrega" class="tamanho35p" value="<? if ($rs->hora_entrega!="00:00:00") echo $rs->hora_entrega; ?>" title="Hora entrega" onkeyup="formataHora(this);" maxlength="8" />
                <br />
                
                <label class="tamanho25p" for="id_veiculo">Veículo:</label>
                <select name="id_veiculo" class="tamanho160" id="id_veiculo" title="Veículo">
                    <option value="">-</option>
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
                <br />
                
                <label class="tamanho25p" for="peso_entregue">* Peso entregue:</label>
                <input id="peso_entregue" name="peso_entregue" class="espaco_dir tamanho15p" value="<?= fnumf($rs->peso_entregue); ?>" onkeydown="formataValor(this,event);" title="Peso conserto" /> kg
                <br />
            </fieldset>
            <? } ?>
        </div>
        <div class="parte70 gigantex">
            
            <fieldset>
                <legend>Peças de roupa</legend>
                
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <td align="left" class="tamanho160"><strong>Tipo de roupa</strong></td>
                        <td align="left" class="tamanho100"><strong>Recebido</strong></td>
                        <td align="left" class="tamanho90"><strong>Consertado</strong></td>
                        <td align="left" class="tamanho90"><strong>Substituído</strong></td>
                        <td align="left" class="tamanho100"><strong>Baixa</strong></td>
                        <td align="left" class="tamanho70"><strong>Devolvido</strong></td>
                        <td align="left" class="tamanho120"><strong>Motivo</strong></td>
                        <td>&nbsp;</td>
                    </thead>
                </table>
                <br />
                
                <div id="pesagens">
                    <?
                    if ($acao=='i') {
                        for ($i=1; $i<=10; $i++) {
                            $tab= $i+7;
                        ?>
                        <div id="div_pesagem_<?=$i;?>">
                            <code class="escondido"></code>
                            
                            <div id="div_tipo_roupa_<?=$i;?>">
                                <select class="tamanho160 espaco_dir" id="id_tipo_roupa_<?=$i;?>" name="id_tipo_roupa[]" title="Tipo de roupa">
                                    <option value="">-</option>
                                    <?
                                    $result_pecas= mysql_query("select * from op_limpa_pecas
                                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                                and   status_peca = '1'
                                                                $str_aqui
                                                                order by peca asc
                                                                ");
                                    $j=1;
                                    while ($rs_peca= mysql_fetch_object($result_pecas)) {
                                    ?>
                                    <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_peca->id_peca; ?>" <? if ($rs_peca->id_peca==$rs->id_tipo_roupa) echo "selected=\"selected\""; ?>><?= $rs_peca->peca; ?></option>
                                    <? $j++; } ?>
                                </select>
                            </div>
                
                            <input id="qtde_recebido_<?=$i;?>" name="qtde_recebido[]" class="espaco_dir tamanho60" value="<?= $rs_costura_conserto->qtde_recebido; ?>" title="Qtde recebido" />
                            <input id="qtde_consertado_<?=$i;?>" name="qtde_consertado[]" class="espaco_dir tamanho60" value="<?= $rs_costura_conserto->qtde_consertado; ?>" title="Qtde consertado" />
                            <input id="qtde_substituido_<?=$i;?>" name="qtde_substituido[]" class="espaco_dir tamanho60" value="<?= $rs_costura_conserto->qtde_substituido; ?>" title="Qtde substituído" />
                            <input id="qtde_baixa_<?=$i;?>" name="qtde_baixa[]" class="espaco_dir tamanho60" value="<?= $rs_costura_conserto->qtde_baixa; ?>" title="Qtde baixa" />
                            <input id="qtde_devolvido_<?=$i;?>" name="qtde_devolvido[]" class="espaco_dir tamanho60" value="<?= $rs_costura_conserto->qtde_devolvido; ?>" title="Qtde devolvido" />
                            
                            <select class="tamanho120 espaco_dir" name="id_motivo_costura[]" id="id_motivo_costura_<?=$i;?>" title="Motivo">
                                <option value="">-</option> 
                                <?
                                $vetor= pega_motivo_costura('l');
                                $c=1;
                                while ($vetor[$c]) {
                                ?>
                                <option <? if ($c%2==0) echo "class=\"cor_sim\""; ?> value="<?=$c;?>" <? if ($rs->estado_civil==$i) echo "selected=\"selected\""; ?>><?= $vetor[$c]; ?></option>
                                <? $c++; } ?>
                            </select>
                            
                            <a href="javascript:void(0);" onclick="removeDiv('pesagens', 'div_pesagem_<?=$i;?>');">remover</a>
                            <br />
                        </div>
                    <?
                        }
                    }
                    else {
                        
                        $result_costura_conserto= mysql_query("select * from op_limpa_costura_consertos_pecas
                                                                where id_costura_conserto = '". $rs->id_costura_conserto ."'
                                                                order by id_costura_conserto_peca asc
                                                                ") or die(mysql_error());
                        $i=1;
                        while ($rs_costura_conserto= mysql_fetch_object($result_costura_conserto)) {
                        ?>
                        <div id="div_pesagem_<?=$i;?>">
                            <code class="escondido"></code>
                            
                            <div id="div_tipo_roupa_<?=$i;?>">
                                <select class="tamanho160 espaco_dir" id="id_tipo_roupa_<?=$i;?>" name="id_tipo_roupa[]" title="Tipo de roupa">
                                    <option value="">-</option>
                                    <?
                                    $result_pecas= mysql_query("select * from op_limpa_pecas
                                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                                and   status_peca = '1'
                                                                order by peca asc
                                                                ");
                                    $j=1;
                                    while ($rs_peca= mysql_fetch_object($result_pecas)) {
                                    ?>
                                    <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_peca->id_peca; ?>" <? if ($rs_peca->id_peca==$rs_costura_conserto->id_tipo_roupa) echo "selected=\"selected\""; ?>><?= $rs_peca->peca; ?></option>
                                    <? $j++; } ?>
                                </select>
                            </div>
                
                            <input id="qtde_recebido_<?=$i;?>" name="qtde_recebido[]" class="espaco_dir tamanho80" value="<?= $rs_costura_conserto->qtde_recebido; ?>" title="Qtde recebido" />
                            <input id="qtde_consertado_<?=$i;?>" name="qtde_consertado[]" class="espaco_dir tamanho80" value="<?= $rs_costura_conserto->qtde_consertado; ?>" title="Qtde consertado" />
                            <input id="qtde_substituido_<?=$i;?>" name="qtde_substituido[]" class="espaco_dir tamanho80" value="<?= $rs_costura_conserto->qtde_substituido; ?>" title="Qtde substituído" />
                            <input id="qtde_baixa_<?=$i;?>" name="qtde_baixa[]" class="espaco_dir tamanho80" value="<?= $rs_costura_conserto->qtde_baixa; ?>" title="Qtde baixa" />
                            <input id="qtde_devolvido_<?=$i;?>" name="qtde_devolvido[]" class="espaco_dir tamanho60" value="<?= $rs_costura_conserto->qtde_devolvido; ?>" title="Qtde devolvido" />
                            
                            <select class="tamanho120 espaco_dir" name="id_motivo_costura[]" id="id_motivo_costura_<?=$i;?>" title="Motivo">
                                <option value="">-</option> 
                                <?
                                $vetor= pega_motivo_costura('l');
                                $c=1;
                                while ($vetor[$c]) {
                                ?>
                                <option <? if ($c%2==0) echo "class=\"cor_sim\""; ?> value="<?=$c;?>" <? if ($rs_costura_conserto->id_motivo_costura==$c) echo "selected=\"selected\""; ?>><?= $vetor[$c]; ?></option>
                                <? $c++; } ?>
                            </select>
                            
                            <a href="javascript:void(0);" onclick="removeDiv('pesagens', 'div_pesagem_<?=$i;?>');">remover</a>
                            <br />
                        </div>
                        <? $i++; } ?>
                    <? } ?>
                </div>
                <br />
                
                <button type="button" onclick="criaEspacoCosturaConserto();">+ peça</button>
                <br />
                
            </fieldset>
            
            <br /><br />
            
            <label for="obs" class="tamanho15p">Observações:</label>
            <textarea name="obs" id="obs" title="Observações"><?=$rs->obs;?></textarea>
            <br /><br />
            
        </div>
        <br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>

<script language="javascript" type="text/javascript">
	daFoco("data_chegada");
	<? if ($acao=='e') { ?>
	pegaCliente("<?= $codigo_cliente; ?>");
	<? } ?>
</script>
<? } ?>