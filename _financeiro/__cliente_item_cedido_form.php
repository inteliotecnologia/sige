<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	if ($_GET["id_item_cedido"]!="") $id_item_cedido= $_GET["id_item_cedido"];
	if ($_POST["id_item_cedido"]!="") $id_item_cedido= $_POST["id_item_cedido"];
	
	if ($_GET["tipo_item_cedido"]!="") $tipo_item_cedido= $_GET["tipo_item_cedido"];
	if ($_POST["tipo_item_cedido"]!="") $tipo_item_cedido= $_POST["tipo_item_cedido"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from fi_clientes_itens_cedidos
							 	where id_item_cedido = '". $id_item_cedido ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_cliente= $rs->id_cliente;
		$tipo_item_cedido= $rs->tipo_item_cedido;
	}
?>
<form action="<?= AJAX_FORM; ?>formClienteItemCedido&amp;acao=<?= $acao; ?>" method="post" name="formClienteItemCedido" id="formClienteItemCedido" onsubmit="return ajaxForm('conteudo_interno', 'formClienteItemCedido', 'validacoes_cedido', true);">

    <input class="escondido" type="hidden" id="validacoes_cedido" value="id_cliente@vazio|data_entrega@data|data_valida@data" />
    <input name="id_cliente" class="escondido" type="hidden" id="id_cliente" value="<?= $id_cliente; ?>" />
    
    <input name="tipo_item_cedido" class="escondido" type="hidden" id="tipo_item_cedido" value="<?= $tipo_item_cedido; ?>" />
    
    <? if ($acao=="e") { ?>
    <input name="id_item_cedido" class="escondido" type="hidden" id="id_item_cedido" value="<?= $rs->id_item_cedido; ?>" />
    <? } ?>
    
    
    <label for="id_cliente">Cliente:</label>
    <select name="id_cliente" id="id_cliente" title="Cliente">
        <? if ($acao=='i') { ?>
        <option value="">-</option>
        <? } ?>
        <?
        $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos
                                    where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                    and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                    and   pessoas_tipos.tipo_pessoa = 'c'
                                    order by
                                    pessoas.apelido_fantasia asc
                                    ") or die(mysql_error());
        $k=0;
        while ($rs_ced = mysql_fetch_object($result_ced)) {
        ?>
        <option  <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if (($rs_ced->id_cedente==$rs->id_cliente) || ($rs_ced->id_cedente==$id_cliente)) echo "selected=\"selected\""; ?>><?= $rs_ced->apelido_fantasia; ?></option>
        <? $k++; } ?>
    </select>
    <br />

    <label for="data_entrega">Data de entrega:</label>
    <input name="data_entrega" id="data_entrega" class="tamanho15p" value="<?=desformata_data($rs->data_entrega);?>" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" />
    <br />
    
    <label for="data_valida">Vale para:</label>
    <input name="data_valida" id="data_valida" class="tamanho15p" value="<?=desformata_data($rs->data_valida);?>" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" />
    <br />
    
    <label for="qtde">Quantidade (enviada):</label>
    <input name="qtde" id="qtde" class="tamanho15p" value="<?=$rs->qtde;?>" />
    <br />
    
    <? /* if ($tipo_item_cedido==1) { ?>
    <label for="qtde_padrao">Quantidade (padrão):</label>
    <input name="qtde_padrao" id="qtde_padrao" class="tamanho15p" value="<?=$rs->qtde_padrao;?>" />
    <br />
    
    <label for="qtde_debito">Quantidade (débito):</label>
    <input name="qtde_debito" id="qtde_debito" class="tamanho15p" value="<?=$rs->qtde_debito;?>" />
    <br />
    <? //} */ ?>
    
    <? /*
    <label for="qtde_padrao">Quantidade padrão:</label>
    <input name="qtde_padrao" id="qtde_padrao" class="tamanho15p" value="<?=$rs->qtde;?>" />
    <br />
    
    <label for="qtde">Quantidade:</label>
    <input name="qtde" id="qtde" class="tamanho15p" value="<?=$rs->qtde;?>" />
    <br />
    
    <label for="qtde">Quantidade:</label>
    <input name="qtde" id="qtde" class="tamanho15p" value="<?=$rs->qtde;?>" />
    <br />
    */ ?>
    
    <label for="recebido_por">Recebido por:</label>
    <input name="recebido_por" id="recebido_por" value="<?=$rs->recebido_por;?>" />
    <br />
    <br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>