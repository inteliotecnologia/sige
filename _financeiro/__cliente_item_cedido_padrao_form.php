<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	if ($_GET["id_item_cedido"]!="") $id_item_cedido= $_GET["id_item_cedido"];
	if ($_POST["id_item_cedido"]!="") $id_item_cedido= $_POST["id_item_cedido"];
	
	if ($_GET["tipo_item_cedido"]!="") $tipo_item_cedido= $_GET["tipo_item_cedido"];
	if ($_POST["tipo_item_cedido"]!="") $tipo_item_cedido= $_POST["tipo_item_cedido"];
	
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($periodo=="") $periodo= date("m/Y");
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($acao=="") $acao= $_GET["acao"];
	
	$periodo2= explode("/", $periodo);
	$data_qtde_padrao= $periodo2[1] ."-". $periodo2[0] ."-01";
	
	$result= mysql_query("select * from fi_clientes_itens_cedidos_padrao
							where tipo_item_cedido = '". $tipo_item_cedido ."'
							and   id_empresa = '". $_SESSION["id_empresa"] ."'
							and   id_cliente = '". $id_cliente ."'
							and   DATE_FORMAT(data_qtde_padrao, '%m/%Y') = '". $periodo ."'
							order by data_qtde_padrao asc, id_item_cedido_padrao desc limit 1
							") or die("1:". mysql_error());
	$linhas= mysql_num_rows($result);
	
	if ($linhas>0) {
		$rs= mysql_fetch_object($result);
		
		$id_cliente= $rs->id_cliente;
		$tipo_item_cedido= $rs->tipo_item_cedido;
	}
?>
<form action="<?= AJAX_FORM; ?>formClienteItemCedidoPadrao" method="post" name="formClienteItemCedidoPadrao" id="formClienteItemCedidoPadrao" onsubmit="return ajaxForm('conteudo_interno', 'formClienteItemCedidoPadrao', 'validacoes_cedido_padrao', true);">

    <input class="escondido" type="hidden" id="validacoes_cedido_padrao" value="id_cliente2@vazio|qtde_padrao@vazio" />
    
    <input name="tipo_item_cedido" class="escondido" type="hidden" id="tipo_item_cedido2" value="<?= $tipo_item_cedido; ?>" />
    <input name="data_qtde_padrao" class="escondido" type="hidden" id="data_qtde_padrao" value="<?= $data_qtde_padrao; ?>" />
    
    <label for="id_cliente2">Cliente:</label>
    <select name="id_cliente" id="id_cliente2" title="Cliente">
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
    
    <label>Período:</label>
    <?
	$periodo2= explode("/", $periodo);
	echo traduz_mes($periodo2[0]) ."/". $periodo2[1];
	?>
    <br /><br />
    
    <label for="qtde_padrao">Quantidade (padrão):</label>
    <input name="qtde_padrao" id="qtde_padrao" class="tamanho15p" value="<?=$rs->qtde_padrao;?>" />
    <br />
    
    <br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>

<? if ($acao=="e") { ?>
<script language="javascript">
	daFoco("qtde_padrao");
</script>
<? } ?>

<? } ?>