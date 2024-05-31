<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	
	if ($_GET["tipo_nota"]!="") $tipo_nota= $_GET["tipo_nota"];
	if ($_POST["tipo_nota"]!="") $tipo_nota= $_POST["tipo_nota"];
	
	if ($_GET["id_nota"]!="") $id_nota= $_GET["id_nota"];
	if ($_POST["id_nota"]!="") $id_nota= $_POST["id_nota"];
	
	if (($acao=="") && ($_GET["acao"]!="")) $acao= $_GET["acao"];
	
	if ($acao=='e') {
		if ($_GET["id_nota"]!="") $id_nota= $_GET["id_nota"];
		if ($_POST["id_nota"]!="") $id_nota= $_POST["id_nota"];
	
		$result= mysql_query("select * from  fi_notas
								where id_nota = '". $id_nota ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		$tipo_nota= $rs->tipo_nota;
		$tit1= "Edição";
		
		$valor_total_nota= pega_valor_total_nota($rs->id_nota);
		$valor_pago= pega_valor_total_pagamento_nota($rs->id_nota);
		
		if (($rs->status_nota==0) && ($valor_total_nota>$valor_pago))
			$saldo_nota= $valor_total_nota-$valor_pago;
		else
			$saldo_nota= 0;
	}
	else
		$tit1= "Cadastro";
	
	if ($tipo_nota=='p') {
		$tit2= "à pagar";
		$tit3= "pago";
		$tit4= "pagamento";
		$txt_cedente= "Fornecedor";
		$tipo_cedente= "f";
	}
	else {
		$tit2= "à receber";
		$tit3= "recebido";
		$tit4= "recebimento";
		$txt_cedente= "Cliente";
		$tipo_cedente= "c";
	}
?>

<div id="item_cadastro" class="telinha1 screen">
    <a href="javascript:void(0);" onclick="fechaDiv('item_cadastro');" class="fechar">x</a>
    
    <h2>Cadastro de produto</h2>
    
    <div id="item_cadastro3">
    </div>

    <label for="item">Produto:</label>
    <input name="item" id="item" onkeyup="if (event.keyCode==13) itemCadastroOk();" />
    <br />
    
    <label for="tipo_apres">Apresentação:</label>
    <select name="tipo_apres" id="tipo_apres">
        <?
        $vetor= pega_tipo_apres('l');
        
        $i=0; $j=0;
        while ($vetor[$i][$j]) {
        ?>
        <option <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> value="<?= $vetor[$i][0]; ?>"><?= $vetor[$i][1]; ?></option>
        <? $i++; } ?>
    </select>
    <br />
    
    <label for="id_centro_custo_tipo">* Tipo:</label>
    <select name="id_centro_custo_tipo" id="id_centro_custo_tipo2" title="Centro de custo">
        <? if ($acao=='i') { ?>
        <option value="">---</option>
        <? } ?>
        <?
        $result_cc= mysql_query("select * from fi_centro_custos
                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                    order by centro_custo asc
                                    ") or die(mysql_error());
        while ($rs_cc= mysql_fetch_object($result_cc)) {
        ?>
        <optgroup label="<?= $rs_cc->centro_custo; ?>">
            <?
            $result_cc2= mysql_query("select * from  fi_centro_custos_tipos, fi_cc_ct
                                        where fi_centro_custos_tipos.id_empresa = '". $_SESSION["id_empresa"] ."'
                                        and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
                                        and   fi_cc_ct.id_centro_custo = '". $rs_cc->id_centro_custo ."'
                                        order by fi_centro_custos_tipos.centro_custo_tipo asc
                                        ") or die(mysql_error());
            $i=0;
            while ($rs_cc2= mysql_fetch_object($result_cc2)) {
            ?>
            <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cc2->id_centro_custo_tipo; ?>"<? if ($rs->id_centro_custo_tipo==$rs_cc2->id_centro_custo_tipo) echo "selected=\"selected\""; ?>><?= $rs_cc2->centro_custo_tipo; ?></option>
            <? $i++; } ?>
        </optgroup>
        <? } ?>
    </select>
    <br />
    
    <br /><br />
    <label>&nbsp;</label>
    <button type="button" onclick="itemCadastroOk();">Adicionar &raquo;</button>
</div>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<? if ($acao=='i') { ?>
<h2><?= $tit1; ?> de duplicata <?= $tit2; ?></h2>
<? } ?>

<? /*
<fieldset>
    <legend>Dados da duplicata</legend>
    
    <div class="parte50">
        <label><?= $txt_cedente; ?>:</label>
        <?= pega_pessoa($rs->id_cedente); ?>
        <br />
        
        <label>Número da nota:</label>
        <?= $rs->num_nota; ?>
        <br />
        
        <label>Data de emissão:</label>
        <?= desformata_data($rs->data_emissao); ?>
        <br />
        
    </div>
    <div class="parte50">
    	<?
		
		?>
        
        <label>Valor total da nota:</label>
        R$ <?= fnum($valor_total_nota); ?>
        <br />
        
        <label>Valor <?= $tit3; ?>:</label>
        R$ <?= fnum($valor_pago); ?>
        <br />
        
        <?
        
		?>
        <label>Saldo <?= $tit2; ?>:</label>
        R$ <?= fnum($saldo_nota); ?>
        <br />
    </div>
</fieldset>
*/ ?>


<form action="<?= AJAX_FORM; ?>formNotaDetalhamento&amp;acao=<?= $acao; ?>" method="post" name="formNotaDetalhamento" id="formNotaDetalhamento" onsubmit="return ajaxForm('conteudo', 'formNotaDetalhamento', 'validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="tipo_nota@vazio|id_cedente@vazio|num_nota@vazio|data_emissao@data" />
    
	<? if ($acao=='e') { ?>
    <input name="id_nota" class="escondido" type="hidden" id="id_nota" value="<?= $rs->id_nota; ?>" />
    <? } ?>
    
    <input name="tipo_nota" class="escondido" type="hidden" id="tipo_nota" value="<?= $tipo_nota; ?>" />
    
    <fieldset>
        <legend>Dados da duplicata</legend>
        
        <div class="parte50">
        	<? if ($_GET["acao"]=="e") { ?>
        	<label for="id_cedente">ID da nota:</label>
        	<?= $rs->id_nota; ?>
        	<br /><br />
        	<? } ?>
        	
            <label for="id_cedente">* <?= $txt_cedente; ?>:</label>
            <select name="id_cedente" id="id_cedente" title="<?= $txt_cedente; ?>">
            	<? if ($acao=='i') { ?>
                <option value="">- TODOS -</option>
                <? } ?>
                
                <?
                $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos
                                            where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                            and   pessoas_tipos.tipo_pessoa = '$tipo_cedente'
                                            order by
                                            pessoas.nome_rz asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_ced = mysql_fetch_object($result_ced)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if ($rs_ced->id_cedente==$rs->id_cedente) echo "selected=\"selected\""; ?>><?= $rs_ced->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="num_nota">* Número da nota:</label>
            <input title="Número da nota" name="num_nota" value="<?= $rs->num_nota; ?>" id="num_nota" />
            <br />
            
            <?
			if ($acao=='i') $data_emissao= date("d/m/Y");
			else $data_emissao= desformata_data($rs->data_emissao);
			?>
            <label for="data_emissao">* Data de emissão:</label>
            <input id="data_emissao" name="data_emissao" class="tamanho25p" value="<?= $data_emissao; ?>" title="Data de emissão" onkeyup="formataData(this);" maxlength="10" />
            <br />
            
        </div>
        
        <div class="parte50">
        	
            <? /* if ($tipo_nota=="r") { ?>
            <label for="valor">* Valor da nota:</label>
            <? if ($rs->valor=="") $valor= ""; else $valor= fnum($rs->valor); ?>
            <input id="valor" name="valor" class="tamanho15p" value="<?= $valor; ?>" onkeydown="formataValor(this,event);" title="Valor" />
            <br />
            
            <label for="imposto_iss_percent">* Imposto ISS (%):</label>
            <? if ($rs->imposto_iss_percent=="") $imposto_iss_percent= ""; else $imposto_iss_percent= fnum($rs->imposto_iss_percent); ?>
            <input id="imposto_iss_percent" name="imposto_iss_percent" class="tamanho15p espaco_dir" value="<?= $imposto_iss_percent; ?>" onkeydown="formataValor(this,event);" title="Imposto ISS %" />
            
            <label for="imposto_iss_valor">* Imposto ISS (R$):</label>
            <? if ($rs->imposto_iss_valor=="") $imposto_iss_valor= ""; else $imposto_iss_valor= fnum($rs->imposto_iss_valor); ?>
            <input id="imposto_iss_valor" name="imposto_iss_valor" class="tamanho15p espaco_dir" value="<?= $imposto_iss_valor; ?>" onkeydown="formataValor(this,event);" title="Imposto ISS R$" />
            <br />
            
            <label for="imposto_outros_percent">* Outros impostos (%):</label>
            <? if ($rs->imposto_outros_percent=="") $imposto_outros_percent= ""; else $imposto_outros_percent= fnum($rs->imposto_outros_percent); ?>
            <input id="imposto_outros_percent" name="imposto_outros_percent" class="tamanho15p espaco_dir" value="<?= $imposto_outros_percent; ?>" onkeydown="formataValor(this,event);" title="Outros impostos %" />
            
            <label for="imposto_outros_valor">* Outros impostos (R$):</label>
            <? if ($rs->imposto_outros_valor=="") $imposto_outros_valor= ""; else $imposto_outros_valor= fnum($rs->imposto_outros_valor); ?>
            <input id="imposto_outros_valor" name="imposto_outros_valor" class="tamanho15p espaco_dir" value="<?= $imposto_outros_valor; ?>" onkeydown="formataValor(this,event);" title="Outros impostos R$" />
            <br />
            <? } */ ?>
            
            <? if ($acao=='i') { ?>
            <div id="div_nota_pagar" class="mostra">
                <label>&nbsp;</label>
                <input type="checkbox" class="tamanho20" name="pagar" id="pagar" value="1" />
                <label for="pagar" class="nao_negrito alinhar_esquerda">Marcar esta duplicata como paga integralmente.</label>
            </div>
			<? } ?>
			
        </div>
    </fieldset>
    
    <div class="parte50">
        <fieldset>
            <legend>Parcelas</legend>
            
            <? if ($acao=="e") { ?>
            <label>Valor das parcelas:</label>
            R$ <?= fnum(pega_valor_parcelas($rs->id_nota)); ?>
            <br /><br />
            <? } ?>
            
            <ul class="recuo1">
                <li><a href="javascript:void(0);" onclick="criaEspacoNotaParcela();">nova parcela &raquo;</a></li>
            </ul>
            
            <div id="parcelas">
                <?
                $result_parcelas= mysql_query("select * from fi_notas_parcelas
                                                where id_nota = '". $rs->id_nota ."'
                                                order by data_vencimento asc
                                                ") or die(mysql_error());
                $linhas_parcelas= mysql_num_rows($result_parcelas);
                
                $k=1;
                while ($rs_parcelas= mysql_fetch_object($result_parcelas)) {
                ?>
                    <div id="div_parcela_<?=$k;?>">
                        <code class="escondido"></code>
                
                        <fieldset>
                            <legend>Parcela <?= $k;?></legend>
                        
                            <? if ($rs_parcelas->status_parcela==1) { ?>
                            
                            <label>Data de vencimento:</label>
                            <?=desformata_data($rs_parcelas->data_vencimento);?>
                            <br />
                            
                            <label>Valor:</label>
                            <?= fnum($rs_parcelas->valor); ?>
                            <br />
                            
                            <?
                            $result_parcelas_pagamentos= mysql_query("select * from fi_notas_parcelas_pagamentos
                                                                        where id_nota = '". $rs->id_nota ."'
                                                                        and   id_parcela = '". $rs_parcelas->id_parcela ."'
                                                                        ");
                            if (mysql_num_rows($result_parcelas_pagamentos)==0)
                                echo "Nada para esta parcela.";
                            else {
                                while ($rs_parcelas_pagamentos= mysql_fetch_object($result_parcelas_pagamentos)) {
                                ?>
                                
                                <label>Data de <?=$tit4;?>:</label>
                                <?= desformata_data($rs_parcelas_pagamentos->data_pagamento); ?>
                                <br />
                                
                                <label>Valor <?=$tit3;?>:</label>
                                R$ <?= fnum($rs_parcelas_pagamentos->valor_pago); ?>
                                
                                <strong>
                                (<?=ucfirst($tit4);?>
                                <?
                                if ($rs_parcelas_pagamentos->integral==1) echo "integral";
                                else echo "parcial";
                                ?>
                                </strong>
                                )
                                <br />
                                
                                <? /*
                                <label>&nbsp;</label>
                                <a href="javascript:ajaxLink('conteudo', 'notaPagamentoExcluir&amp;id_parcela_pagamento=<?= $rs_parcelas_pagamentos->id_parcela_pagamento; ?>&amp;id_nota=<?=$rs->id_nota;?>');" onclick="return confirm('Tem certeza que deseja excluir este pagamento?');">
                                    <img border="0" src="images/ico_lixeira.png" alt="Status" />
                                </a>
                                <br />
                                */ ?>
                                
                                <? } ?>
                            <? } ?>    
                            
                            <?
                            }
                            //se status_parcela==0
                            else {
                            ?>
                                <label for="data_vencimento_<?=$k;?>">Data de vencimento:</label>
                                <input class="tamanho25p" title="Data de vencimento" name="data_vencimento[]" id="data_vencimento_<?=$k;?>" value="<?=desformata_data($rs_parcelas->data_vencimento);?>" onkeyup="formataData(this);" maxlength="10" />
                                <br />
                                
                                <label for="valor_<?=$k;?>">Valor:</label>
                                <input class="tamanho25p" name="valor[]" id="valor_<?=$k;?>" title="Valor da parcela"  onkeydown="formataValor(this,event);" value="<?= fnum($rs_parcelas->valor); ?>" />
                                <br />
                                
                                <label>&nbsp;</label>
                                <a href="javascript:void(0);" onclick="removeDiv('parcelas', 'div_parcela_<?=$k;?>');">remover</a>
                                <br />
                            <?
                            }//fim else
                            $k++;
                        ?>
                        </fieldset>
                    </div>
                <? } ?>
                
                
            </div>
        
            
            <? if ($saldo_nota>0) { ?>
            <ul class="recuo1">
                <li><a href="javascript:void(0);" onclick="criaEspacoNotaParcela();">nova parcela &raquo;</a></li>
            </ul>
            <? } ?>
        
        </fieldset>
    </div>
    
    <? if ($rs->tipo_nota=='p') { ?>
    <div class="parte50">
        <fieldset>
            <legend>Itens</legend>
            
            <label>Valor dos itens:</label>
            R$ <?= fnum(pega_valor_itens($rs->id_nota)); ?>
            <br />
            
            <div id="itens">
            	
                <ul class="recuo1">
                    <li><a href="javascript:void(0);" onclick="criaEspacoNotaItem('<?=$rs->id_nota;?>');">novo item &raquo;</a></li>
                    <li><a href="javascript:void(0);" onclick="abreDiv('item_cadastro'); daFoco('item');">cadastrar produto &raquo;</a></li>
                </ul>
                
				<?
                $result_itens= mysql_query("select * from fi_notas_itens
                                                where id_nota = '". $rs->id_nota ."'
                                                order by id_nota_item asc
                                                ") or die(mysql_error());
                $linhas_itens= mysql_num_rows($result_itens);
                
                $k=1;
                while ($rs_itens= mysql_fetch_object($result_itens)) {
                ?>
                <div id="div_item_<?=$k;?>">
                    <code class="escondido"></code>
            
                    <fieldset>
                        <legend>Item <?= $k;?></legend>
                        
                        <input type="hidden" class="escondido" name="nada[]" value="1" />
						
						<label for="destinacao_<?=$k;?>">Destinação:</label>
						<select name="destinacao[]" id="destinacao_<?=$k;?>" onchange="alteraDestinacao(<?=$k;?>, <?=$rs->id_nota;?>);">
						<option value="1" <? if ($rs_itens->destinacao==1) echo "selected=\"selected\""; ?>>Estoque</option>
						<option value="2" <? if ($rs_itens->destinacao==2) echo "selected=\"selected\""; ?> class="cor_sim">Centro de custo</option>
						</select>
						<br />
						
						<div id="destinacao_atualiza_<?=$k;?>">
							
                            <? if ($rs_itens->destinacao==1) { ?>
                            <label for="item_<?=$k;?>">Pesquisa:</label>
                            <input title="Item" name="item[]" id="item_<?=$k;?>" value="" class="tamanho25p espaco_dir" onkeyup="itemBusca(<?=$k;?>);" />
                            <br />
                            
                            <label for="id_item_<?=$k;?>">Item:</label>
                            <div id="item_atualiza_<?=$k;?>">
                            <select name="id_item[]" id="id_item_<?=$k;?>" onchange="processaDecimal('<?=$k;?>');">
                                <?
                                if ($rs_itens->id_item!=0) {
                                    $result_item2= mysql_query("select * from fi_itens
                                                                where id_item = '". $rs_itens->id_item ."'
                                                                ");
                                    while ($rs_item2= mysql_fetch_object($result_item2)) {
                                    ?>
                                    <option value="<?= $rs_item2->id_item; ?>"><?= $rs_item2->item; ?></option>
                                    <? } ?>
                                <? } else { ?>
                                <option value="">---</option>
                                <? } ?>
                            </select>
                            </div>
                            <br />
                            <? } ?>
                            
                            <div id="cc_atualiza_<?=$k;?>" <? if ($rs_itens->destinacao==1) { ?> class="escondido" <? } ?>>
                            </div>
						</div>
                        
						<? //if ($rs_itens->destinacao==2) { ?>
                        <script language="javascript">
                            ajaxLink("cc_atualiza_<?=$k;?>", "alteraNotaCentroCusto&cont=<?=$k;?>&id_nota=<?=$rs->id_nota;?>&id_nota_item=<?=$rs_itens->id_nota_item;?>&id_centro_custo_tipo=<?=$rs_itens->id_centro_custo_tipo;?>");
                        </script>
                        <? //} ?>
                        
						<label for="valor_unitario_<?=$k;?>">Valor unitário:</label>
						<input onkeydown="formataValor(this,event);" class="tamanho25p" title="Valor unitário" name="valor_unitario[]" id="valor_unitario_<?=$k;?>" value="<?=fnum($rs_itens->valor_unitario);?>" />
						<br />
						
						<label for="qtde_<?=$k;?>">Quantidade:</label>
						<input class="tamanho25p" title="Quantidade" name="qtde[]" id="qtde_<?=$k;?>" value="<?=fnumf($rs_itens->qtde);?>" <? if (eh_decimal($rs_itens->qtde)) { ?> onkeydown="formataValor(this,event);" <? } ?> onblur="calculaValorTotalItemNota(<?=$k;?>);" />
						<br />
						
						<label for="valor_total_<?=$k;?>">Valor total:</label>
						<input class="tamanho25p" title="Valor total" name="valor_total[]" id="valor_total_<?=$k;?>" value="<?=fnum($rs_itens->valor_total);?>" onkeydown="formataValor(this,event);" />
						<br />
						
						<label>&nbsp;</label>
						<a href="javascript:void(0);" onclick="removeDiv('itens', 'div_item_<?=$k;?>');">remover</a><br /><br />
                        
	                </fieldset>
                </div>
                <? $k++; } ?>
            </div>
            
            <br /><br />
            <ul class="recuo1">
                <li><a href="javascript:void(0);" onclick="criaEspacoNotaItem('<?=$rs->id_nota;?>');">novo item &raquo;</a></li>
                <li><a href="javascript:void(0);" onclick="abreDiv('item_cadastro'); daFoco('item');">cadastrar produto &raquo;</a></li>
            </ul>
            <br />
            
        </fieldset>
    </div>
    <? } ?>
    
    <br /><br /><br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
	
</form>

<? } ?>