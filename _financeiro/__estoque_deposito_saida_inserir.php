<?
if (pode("iq|", $_SESSION["permissao"])) {
	if ($_GET["id_deposito"]!="") $id_deposito= $_GET["id_deposito"];
	if ($_POST["id_deposito"]!="") $id_deposito= $_POST["id_deposito"];
	
?>

<div id="tela_mensagens2">
<? include("__tratamento_msgs.php"); ?>
</div>

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
        $result_cc= mysql_query("select *
                                    from  fi_centro_custos
                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                    order by centro_custo asc
                                    ") or die(mysql_error());
        while ($rs_cc= mysql_fetch_object($result_cc)) {
        ?>
        <optgroup label="<?= $rs_cc->centro_custo; ?>">
            <?
            $result_cc2= mysql_query("select *
                                        from  fi_centro_custos_tipos, fi_cc_ct
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

<h2 class="titulos">Sa&iacute;da no dep&oacute;sito - <?= pega_deposito($id_deposito); ?></h2>

<div class="parte50 cm">
	<fieldset>
		<legend>Pesquisa de produto</legend>
		
		<label for="pesquisa">Produto:</label>
		<input id="pesquisa" name="pesquisa" class="tamanho80" onkeyup="itemDepositoPesquisar('<?= $id_deposito; ?>', 's');" />
		<? /*<button type="button" class="tamanho30" onclick="itemPesquisar('s');">ok</button>*/ ?>
		<br />
			
		<div id="item_atualiza">
		</div>
        <br />
        
        <!--<label>&nbsp;</label>
        <ul class="recuo1">
        	<li><a href="javascript:void(0);" onclick="abreDiv('item_cadastro'); daFoco('item');">cadastrar produto &raquo;</a></li>
        </ul>-->
	</fieldset>
</div>

<div class="parte50">
	
	<fieldset>
		<legend>Formulário de saída</legend>
	
		<form action="<?= AJAX_FORM; ?>formEstoqueDepositoSaida" method="post" id="formEstoqueDepositoSaida" name="formEstoqueDepositoSaida" onsubmit="return ajaxForm('conteudo', 'formEstoqueDepositoSaida', 'validacoes', true);">
			
            <input id="validacoes" type="hidden" class="escondido" value="id_item@vazio|id_ccts@vazio|qtde@vazio" />
            <input name="id_item" id="id_item" type="hidden" class="escondido" title="Produto" />
            
            <input name="id_deposito" id="id_item" type="hidden" class="escondido" title="Produto" value="<?=$id_deposito;?>" />
            
			<label>Produto:</label>
			<input name="tit_item" id="tit_item" disabled="disabled" class="tamanho200" />
			<br />
			
			<label>Qtde atual:</label>
			<input name="tit_qtde" id="tit_qtde" disabled="disabled" class="tamanho80" />
            <input name="tit_apres" id="tit_apres" disabled="disabled" class="tamanho80" />
			<br />
			
			<label for="qtde">Quantidade:</label>
			<input name="qtde" id="qtde" class="tamanho80" disabled="disabled" title="Quantidade" />
			<br />
            
            <label for="id_motivo">Motivo:</label>
            <select name="id_motivo" id="id_motivo" title="Motivo" disabled="disabled">	  		
                <option value="">---</option>
                <option value="0" class="cor_sim">CONSUMO</option>
				<?
                $i=0;
                $result_mot= mysql_query("select * from rh_motivos where tipo_motivo = 'q' order by motivo asc ");
                while ($rs_mot= mysql_fetch_object($result_mot)) {
                ?>
                <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_mot->id_motivo; ?>"><?= $rs_mot->motivo; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
			<label for="observacoes">Observações:</label>
			<textarea name="observacoes" id="observacoes" disabled="disabled"></textarea>
			<br />
			
			<label>&nbsp;</label>
			<button type="submit" id="enviar" disabled="disabled">Inserir</button>
		</form>
	</fieldset>
</div>
<script language="javascript" type="text/javascript">daFoco('pesquisa');</script>
<? } ?>