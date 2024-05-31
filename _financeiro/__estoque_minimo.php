<?
if (pode("iq|", $_SESSION["permissao"])) {
	$result_em= mysql_query("select * from fi_itens
								where id_item = '". $_GET["id_item"] ."'
								");
	$rs_em= mysql_fetch_object($result_em);
	
	$result_emin= mysql_query("select * from fi_estoque_minimo
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_item = '". $_GET["id_item"] ."'
								");

	$rs_emin= mysql_fetch_object($result_emin);
?>


<h2>Produto</h2>

<a href="javascript:void(0);" onclick="fechaDiv('tela_aux');" class="fechar">x</a>

<div id="formulario">
    <form action="<?= AJAX_FORM; ?>formEstoqueMinimo" method="post" id="formEstoqueMinimo" name="formEstoqueMinimo" onsubmit="return ajaxForm('formulario', 'formEstoqueMinimo', 'validacoes');">
        <input name="id_item" id="id_item" class="escondido" type="hidden" value="<?= $rs_em->id_item; ?>" />
        <input id="validacoes" class="escondido" type="hidden" value="id_item@vazio|qtde_minina@numeros" />
        
        <label class="tamanho120">Produto:</label>
        <?= $rs_em->item; ?>
        <br /><br />
        
        <label class="tamanho120" for="qtde_minima">Qtde mínima:</label>
        <input name="qtde_minima" id="qtde_minima" class="tamanho50" value="<?= fnumf($rs_emin->qtde_minima); ?>" />
        <br />
        
        
        <label class="tamanho120" for="provisionamento">Provisionamento:</label>
        <input name="provisionamento" id="provisionamento" class="tamanho50" value="<?= fnumf($rs_emin->provisionamento); ?>" />
        <br /><br />
    
        <label>&nbsp;</label>
        <button>Atualizar</button>
    </form>
</div>
        
<script language="javascript" type="text/javascript">daFoco('qtde_minima');</script>
<?
}
else {
	$erro_a= 1;
	include("__erro_acesso.php");
}
?>