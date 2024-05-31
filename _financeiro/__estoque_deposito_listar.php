<?
if (pode("iq|", $_SESSION["permissao"])) {
	
	if ($_GET["id_deposito"]!="") $id_deposito= $_GET["id_deposito"];
	if ($_POST["id_deposito"]!="") $id_deposito= $_POST["id_deposito"];
	
	$result= mysql_query("select * from fi_estoque_deposito, fi_itens
							where fi_estoque_deposito.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   fi_estoque_deposito.id_item = fi_itens.id_item
							and   fi_estoque_deposito.id_deposito= '". $id_deposito ."'
							order by fi_itens.item asc
							") or die(mysql_error());
?>

<div id="tela_aux" class="telinha1 screen">
</div>

<div id="tela_mensagens">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Estoque de depósito - <?= pega_deposito($id_deposito); ?></h2>

<p>Foram encontrados <strong><?= mysql_num_rows($result); ?></strong> registro(s).</p>
<br />

<table cellspacing="0" id="tabela" class="sortable">
  <tr>
        <th width="75%" align="left">Produto</th>
        <th width="25%" align="right">Qtde (apresentação)</th>
    </tr>
    <?
	while ($rs= mysql_fetch_object($result)) {
        $result_min= mysql_query("select * from fi_estoque_minimo
                                    where id_item = '". $rs->id_item ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
                                    ");
        if (mysql_num_rows($result_min)==0)
            $min= "";
        else
            $min= "_azul";
        
        $rs_min= mysql_fetch_object($result_min);
    ?>
    <tr class="corzinha">
        <td>
            <? /*<a href="javascript:void(0);" class="link_folder" onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=financeiro/estoque_minimo&amp;id_item=<?= $rs->id_item; ?>');" onmouseover="Tip('Clique para adicionar/atualizar o estoque mínimo deste item.<br />Caso o estoque mínimo seja atingido, um \'!\' aparecerá ao lado da quantidade.');">estoque mínimo</a>*/?>
            <a href="./?pagina=financeiro/estoque_deposito_extrato&amp;id_deposito=<?= $rs->id_deposito; ?>&amp;id_item=<?= $rs->id_item; ?>"><?= $rs->item; ?></a>
        </td>
        <td align="right">
            <?
            if ($rs_min->qtde_minima>=$rs->qtde_atual) echo "<img src=\"images/ico_atencao.gif\" alt=\"\" />&nbsp;";
            
            echo fnumf($rs->qtde_atual) ." ". pega_tipo_apres($rs->tipo_apres);
			?>
        </td>
    </tr>
    <? } //} ?>
</table>
<?
}
else {
	$erro_a= 3;
	include("__erro_acesso.php");
}
?>