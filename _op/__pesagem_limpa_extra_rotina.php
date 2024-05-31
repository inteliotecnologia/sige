<?
if (pode("psl", $_SESSION["permissao"])) {
	$result= mysql_query("select * from op_limpa_pesagem_pecas
								where id_pesagem_peca = '". $_GET["id_pesagem_peca"] ."'
								") or die(mysql_error());
	$rs= mysql_fetch_object($result);
	
	$result_pesagem_pecas= mysql_query("select count(*) as total from op_limpa_pesagem_pecas
								where id_pesagem = '". $rs->id_pesagem ."'
								") or die(mysql_error());
	$rs_pesagem_pecas= mysql_fetch_object($result_pesagem_pecas);
	
	$result_pesagem= mysql_query("select * from op_limpa_pesagem
								where id_pesagem = '". $rs->id_pesagem ."'
								") or die(mysql_error());
	$rs_pesagem= mysql_fetch_object($result_pesagem);
	
?>

<script language="javascript" type="text/javascript">
    shortcut.add("Esc",function() { fechaDiv("tela_aux"); });
</script>


<h2>Pesagem para entrega extra</h2>

<a href="javascript:void(0);" onclick="fechaDiv('tela_aux');" class="fechar">x</a>

<div id="formulario" class="gigante">
    <form action="<?= AJAX_FORM; ?>formPesagemLimpaExtraRotina" method="post" id="formPesagemLimpaExtraRotina" name="formPesagemLimpaExtraRotina" onsubmit="return ajaxForm('formulario', 'formPesagemLimpaExtraRotina', 'validacoes', true);">
        <input name="id_pesagem_peca" id="id_pesagem_peca" class="escondido" type="hidden" value="<?= $rs->id_pesagem_peca; ?>" />
        <input name="extra" id="extra" class="escondido" type="hidden" value="<?= $_GET[extra]; ?>" />
        
        <input id="validacoes" class="escondido" type="hidden" value="id_pesagem_peca@vazio|peso@vazio|num_pacotes@vazio|pacotes_sobra@vazio|qtde_pecas_sobra@vazio" />
        
        <label class="tamanho60">Destino:</label>
        <? if ($_GET[extra]=="1") { ?>
        Mesa => Extra
        <? } else { ?>
        Extra => Mesa
        <? } ?>
        <br /><br />
        
        <label class="tamanho60">Data/hora:</label>
        <?= desformata_data($rs_pesagem->data_pesagem) ." às ". substr($rs_pesagem->hora_pesagem, 0, 5); ?>
        <br /><br />
        
        <label class="tamanho60">Peça:</label>
        <?= pega_pecas_roupa($rs->id_tipo_roupa); ?>
        <br /><br />
    	
    	<table cellpadding="0" cellspacing="0">
    		<tr>
	    		<th width="40%">&nbsp;</th>
	    		<th width="20%">Pacotes completos</th>
	    		<th width="20%">Pacotes c/ sobra</th>
	    		<th width="20%">Peças (sobra)</th>
    		</tr>
    		<tr>
    			<td>Original:</td>
    			<td class="maior">
    				<?=$rs->num_pacotes;?>
    			</td>
    			<td class="maior">
    				<?=$rs->pacotes_sobra;?>
    			</td>
    			<td class="maior">
    				<?=$rs->qtde_pecas_sobra;?>
    			</td>
    		</tr>
    		
    		<tr>
    			<td>Novo:</td>
    			<td>
    				<input name="num_pacotes" id="num_pacotes" class="tamanho50" value="<?=$rs->num_pacotes;?>" title="Pacotes completos" />
    			</td>
    			<td>
    				<input name="pacotes_sobra" id="pacotes_sobra" class="tamanho50" value="<?=$rs->pacotes_sobra;?>" title="Pacotes c/ sobra" />
    			</td>
    			<td>
    				<input name="qtde_pecas_sobra" id="qtde_pecas_sobra" class="tamanho50" value="<?=$rs->qtde_pecas_sobra;?>" title="Qtde sobras" />
    			</td>
    		</tr>
    	</table>
    	<br /><br />
    	
    	<?
        if ($rs_pesagem_pecas->total==1) $peso= number_format($rs_pesagem->peso, 2, ',', '.');
        else $peso="";
        ?>
        <label class="tamanho60" for="peso">Peso:</label>
        <input name="peso" id="peso" class="tamanho100" value="<?= $peso; ?>" onkeydown="formataValor(this,event);" title="Peso" />
        <br /><br />
    	
        <label class="tamanho60">&nbsp;</label>
        <button>Atualizar</button>
    </form>
</div>
        
<script language="javascript" type="text/javascript">daFoco('num_pacotes');</script>
<?
}
else {
	$erro_a= 1;
	include("__erro_acesso.php");
}
?>