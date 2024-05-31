<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	
	if ($_GET["tipo_nota"]!="") $tipo_nota= $_GET["tipo_nota"];
	else $tipo_nota= $_POST["tipo_nota"];
	
	if ($_GET["id_nota"]!="") $id_nota= $_GET["id_nota"];
	if ($_POST["id_nota"]!="") $id_nota= $_POST["id_nota"];
	
	$result= mysql_query("select *
							from  fi_notas
							where id_nota = '". $id_nota ."'
							and   id_empresa = '". $_SESSION["id_empresa"] ."'
							") or die(mysql_error());
	
	$rs= mysql_fetch_object($result);
	$tipo_nota= $rs->tipo_nota;
		
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
<? if ($acao=='i') { ?>
<h2><?= $tit1; ?> de duplicata <?= $tit2; ?></h2>
<? } ?>

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
		$valor_total_nota= pega_valor_total_nota($rs->id_nota);
		$valor_pago= pega_valor_total_pagamento_nota($rs->id_nota);
		?>
        
        <label>Valor total da nota:</label>
        R$ <?= fnum($valor_total_nota); ?>
        <br />
        
        <label>Valor <?= $tit3; ?>:</label>
        R$ <?= fnum($valor_pago); ?>
        <br />
        
        <?
        if (($rs->status_nota==0) && ($valor_total_nota>$valor_pago))
			$saldo_nota= $valor_total_nota-$valor_pago;
		else
			$saldo_nota= 0;
		?>
        <label>Saldo <?= $tit2; ?>:</label>
        R$ <?= fnum($saldo_nota); ?>
        <br />
    </div>
    <? /*
    <div class="parte50">
        <label>Data de vencimento:</label>
        <?= desformata_data($rs->data_vencimento); ?>
        <br />
        
        <label>Valor total:</label>
        R$ <?= fnum($rs->valor_total); ?>
        <br />
    </div>
	*/ ?>
</fieldset>

<form action="<?= AJAX_FORM; ?>formNotaPagamento" method="post" name="formNotaPagamento" id="formNotaPagamento" onsubmit="return ajaxForm('conteudo', 'formNotaPagamento', 'validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_nota@vazio" />
    <input name="id_nota" class="escondido" type="hidden" id="id_nota" value="<?= $rs->id_nota; ?>" />
    
	<?
	$result_parcelas= mysql_query("select * from fi_notas_parcelas
									where id_nota = '". $rs->id_nota ."'
									order by data_vencimento asc
									") or die(mysql_error());
	$linhas_parcelas= mysql_num_rows($result_parcelas);
	?>
        
	<?
    $i=0;
    $a_compensar=0;
    
    while ($rs_parcelas= mysql_fetch_object($result_parcelas)) {
        $j= $i+1;
		
		if ($linhas_parcelas==1) $tit_parcela= "única";
		else $tit_parcela= $j;
    ?>
    <fieldset>
    	<legend>Parcela <?=$tit_parcela;?></legend>
        
        <label>Data de vencimento:</label>
        <?= desformata_data($rs_parcelas->data_vencimento); ?>
        <br />
        
        <label>Valor da parcela:</label>
        R$ <?= fnum($rs_parcelas->valor); ?>
        <br />
		
		<?
		if ($rs_parcelas->status_parcela!=1) {
        ?>
        <input type="hidden" name="id_parcela[]" class="escondido" value="<?= $rs_parcelas->id_parcela; ?>" />
        
        <label for="data_pagamento_<?=$i;?>">Data de <?= $tit4; ?>:</label>
        <input name="data_pagamento[]" id="data_pagamento_<?=$i;?>" class="tamanho25p" onkeyup="formataData(this);" maxlength="10" value="" />
        <br />
        
        <label for="valor_pago_<?=$i;?>">Valor  <?= $tit3; ?>:</label>
        <input name="valor_pago[]" id="valor_pago_<?=$i;?>" onkeydown="formataValor(this,event);" class="tamanho25p" value="" />
        <br />
        
        <label>&nbsp;</label>
        <input type="checkbox" name="integral[]" id="integral_<?=$i;?>" class="tamanho30" value="1" checked="checked" />
        <label for="integral_<?=$i;?>" class="tamanho150 alinhar_esquerda nao_negrito"><?=ucfirst($tit4);?> integral</label>
        <br />
        
        <? } ?>
        
        <fieldset>
            <legend><?=ucfirst($tit4);?>s desta parcela</legend>
            
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
	
				<a href="javascript:ajaxLink('conteudo', 'notaPagamentoExcluir&amp;id_parcela_pagamento=<?= $rs_parcelas_pagamentos->id_parcela_pagamento; ?>&amp;id_nota=<?=$rs->id_nota;?>');" onclick="return confirm('Tem certeza que deseja excluir este pagamento?');">
					<img border="0" src="images/ico_lixeira.png" alt="Status" />
				</a>)
                </strong>
				<br />
				<? } ?>
			<? } ?>            
        </fieldset>
        
    </fieldset>
    <? $i++; } ?>
        

    <br />
    <? if ($rs->status_nota==0) { ?>
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    <? } ?>
</form>
<? } ?>