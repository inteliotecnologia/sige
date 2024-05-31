<?
require_once("conexao.php");
if (pode_algum("i12", $_SESSION["permissao"])) {
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	$result= mysql_query("select * from fi_clientes_servicos_geral
							where id_cliente = '". $id_cliente ."'
							and   id_empresa = '". $_SESSION["id_empresa"] ."'
							") or die(mysql_error());
	$linhas= mysql_num_rows($result);
	$rs= mysql_fetch_object($result);
	
	if ($linhas>0) {
		$id_cliente= $rs->id_cliente;
		$acao="e";
	}
	else $acao="i";
	//}
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<div id="conteudo_interno">

    <ul class="recuo1">
        <li class="flutuar_esquerda tamanho160"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_servicos_geral&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">lavação/transporte</a></li>
        <li class="flutuar_esquerda tamanho200"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_servicos_pecas&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">passadoria/costura/enxoval</a></li>
        <li class="flutuar_esquerda tamanho100"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_item_cedido&amp;tipo_item_cedido=1&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">hampers</a></li>
        <li class="flutuar_esquerda tamanho160"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_item_cedido&amp;tipo_item_cedido=2&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">formulário de coleta</a></li>
        <li class="flutuar_esquerda tamanho80"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=op/equipamento_listar&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">equipamentos</a></li>
    </ul>
    <br /><br />
    
    <form action="<?= AJAX_FORM; ?>formClienteServicosGeral&amp;acao=<?= $acao; ?>" method="post" name="formClienteServicosGeral" id="formClienteServicosGeral" onsubmit="return ajaxForm('conteudo_interno', 'formClienteServicosGeral', 'validacoes');">
    	
        <input class="escondido" type="hidden" id="validacoes" value="id_cliente@vazio" />
        
        <input type="hidden" class="escondido" name="id_cliente" id="id_cliente" value="<?=$id_cliente;?>" />
        
        <? if ($acao=="e") { ?>
        <input type="hidden" class="escondido" name="id_cliente_servico_geral" id="id_cliente_servico_geral" value="<?=$rs->id_cliente_servico_geral;?>" />
        <? } ?>
        
        <fieldset>
            <legend>Lavação</legend>
            
            <div class="parte50">
            	<label for="lavacao_peso_minimo_mes">Peso mínimo:</label>
                <input title="Peso mínimo" name="lavacao_peso_minimo_mes" id="lavacao_peso_minimo_mes" class="tamanho15p" onkeydown="formataValor(this,event);" value="<?= fnum($rs->lavacao_peso_minimo_mes); ?>" />
                <br />
            
            </div>
            <div class="parte50">
            	<label for="lavacao_preco_kg">Preço/kg:</label>
                <input title="Preço/kg" name="lavacao_preco_kg" id="lavacao_preco_kg" class="tamanho15p" onkeydown="formataValor(this,event);" value="<?= fnum($rs->lavacao_preco_kg); ?>" />
                <br />
            </div>
            
        </fieldset>
        
        <fieldset>
            <legend>Transporte</legend>
            
            <? for ($i=1; $i<3; $i++) { ?>
            <div class="parte50">
            	<fieldset>
                	<legend><?= pega_coleta_entrega($i); ?></legend>
                    
                    <? for ($j=0; $j<7; $j++) { ?>
                    	<label class="alinhar_esquerda"><?= traduz_dia($j);?>:</label>
	                    <br />
                        
						<?
                        for ($k=0; $k<4; $k++) {
							$result_cronograma= mysql_query("select * from  tr_cronograma
															where id_empresa = '". $_SESSION["id_empresa"] ."'
															and   id_cliente = '". $id_cliente ."'
															and   tipo = '". $i ."'
															and   id_dia = '". $j ."'
															order by hora_cronograma asc
															limit $k, 1
															") or die(mysql_error());
							
							$rs_cronograma= mysql_fetch_object($result_cronograma);
						?>
                        <input type="hidden" class="escondido" name="nada[]" value="1" />
                        
                        <input type="hidden" class="escondido" name="tipo[]" value="<?= $i; ?>" />
                        <input type="hidden" class="escondido" name="id_dia[]" value="<?= $j; ?>" />
                        
                        <input class="tamanho15p espaco_dir" title="Hora" name="hora_cronograma[]" onkeyup="formataHora(this);" maxlength="5" value="<?= substr($rs_cronograma->hora_cronograma, 0, 5); ?>" id="hora_cronograma_<?=$k;?>" />
                        <? } ?>
                    
                    <br /><br />
                    <? } ?>
                    
                </fieldset>
            </div>
            <? } ?>
            
        </fieldset>
        
        <fieldset>
            <legend>Coleta/entrega extra</legend>
            
            <div class="parte50">
                <label for="extra_mes">Total mês:</label>
                <input title="Peso mínimo" name="extra_mes" id="extra_mes" class="tamanho15p" value="<?= ($rs->extra_mes); ?>" />
                <br />
            </div>
            <div class="parte50">
            	<label for="extra_adicional_preco">Preço adicional:</label>
                <input title="Preço adicional" name="extra_adicional_preco" id="extra_adicional_preco" class="tamanho15p" onkeydown="formataValor(this,event);" value="<?= fnum($rs->extra_adicional_preco); ?>" />
                <br />
            </div>
            
        </fieldset>
        
        <fieldset>
            <legend>Costura</legend>
            
            <div class="parte50">
            	<label for="costura_recebimento_id_dia">* Recebimento:</label>
                <select name="costura_recebimento_id_dia" id="costura_recebimento_id_dia" title="Recebimento">
                    <? if ($acao=='i') { ?>
                    <option value="">-</option>
                    <? } ?>
                    
                    <?
                    for ($i=0; $i<7; $i++) {
                    ?>
                    <option <? if ($i%2!=0) echo "class=\"cor_sim\""; ?> value="<?= $i; ?>" <? if (($acao=='e') && ($i==$rs->costura_recebimento_id_dia)) echo "selected=\"selected\""; ?>><?= traduz_dia($i); ?></option>
                    <? } ?>
                </select>
                <br />
            
            </div>
            <div class="parte50">
            	<label for="costura_entrega_id_dia">* Entrega:</label>
                <select name="costura_entrega_id_dia" id="costura_entrega_id_dia" title="Entrega">
                    <? if ($acao=='i') { ?>
                    <option value="">-</option>
                    <? } ?>
                    
                    <?
                    for ($i=0; $i<7; $i++) {
                    ?>
                    <option <? if ($i%2!=0) echo "class=\"cor_sim\""; ?> value="<?= $i; ?>" <? if (($acao=='e') && ($i==$rs->costura_entrega_id_dia)) echo "selected=\"selected\""; ?>><?= traduz_dia($i); ?></option>
                    <? } ?>
                </select>
                <br />
            </div>
            
        </fieldset>
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
        
    </form>
</div>

<? } ?>