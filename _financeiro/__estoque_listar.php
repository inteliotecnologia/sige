<?
if (pode("iq|", $_SESSION["permissao"])) {
	
	if ($_POST["item"]!="") $item= $_POST["item"];
	if ($_GET["item"]!="") $item= $_GET["item"];
	if ($item!="") $str2= " and   fi_itens.item like '%". $item ."%' ";
	
	$result= mysql_query("select * from fi_estoque, fi_itens
							where fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   fi_estoque.id_item = fi_itens.id_item
							$str2
							order by fi_itens.item asc
							") or die(mysql_error());
?>

<div id="tela_aux" class="telinha1 screen">
</div>

<div id="tela_mensagens">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Estoque</h2>

<div class="parte50">
    <p>Foram encontrados <strong><?= mysql_num_rows($result); ?></strong> registro(s).</p>
    <br />
</div>
<div class="parte50">
	<fieldset>
    	<legend>Busca rápida</legend>
        
            <form action="./?pagina=financeiro/estoque_listar" method="post" onsubmit="return validaFormNormal('validacoes_estoque', false, 1);">
            
                <input class="escondido" type="hidden" id="validacoes_estoque" value="nome_rz@vazio" />
                <input class="escondido" type="hidden" id="busca_geral" value="1" />
               
                <label for="item">Item:</label>
                <input name="item" id="item" value="<?=$item;?>" class="" title="Nome" />
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </form>
        
    </fieldset>
</div>
<br />

<table cellspacing="0" id="tabela" class="sortable">
  <tr>
        <th width="75%" align="left">Item</th>
        <th width="25%" align="right">Qtde (apresentação)</th>
    </tr>
    <?
	while ($rs= mysql_fetch_object($result)) {
        $result_min= mysql_query("select * from fi_estoque_minimo
                                    where id_item = '". $rs->id_item ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
                                    ");
        $rs_min= mysql_fetch_object($result_min);
		
		if ( (($rs_min->qtde_minima!="0") && ($rs_min->qtde_minima!="")) || (($rs_min->provisionamento!="0") && ($rs_min->provisionamento!="")) ) $min= "_azul";
        else $min= "";
        
        $rs_min= mysql_fetch_object($result_min);
    ?>
    <tr class="corzinha">
        <td>

        <a href="javascript:void(0);" class="link_folder<?=$min;?>" onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=financeiro/estoque_minimo&amp;id_item=<?= $rs->id_item; ?>');" onmouseover="Tip('Estoque mínimo e provisionamento para este item.');">estoque mínimo/provisionamento</a>
        
        <?
		//pegar fornecedores deste produto...
		
		$result_for= mysql_query("select distinct(fi_notas.id_cedente) as id_cedente from fi_notas, fi_notas_itens
									where fi_notas_itens.id_item = '". $rs->id_item ."'
									and   fi_notas.id_nota = fi_notas_itens.id_nota
									");
		$fornecedores= "";
		while ($rs_for= mysql_fetch_object($result_for)) {
			$fornecedores .= "<li>". pega_pessoa($rs_for->id_cedente) ."</li>";
		}
		
		?>
        
        <a href="./?pagina=financeiro/estoque_extrato&amp;id_item=<?= $rs->id_item; ?>"><?= $rs->item; ?></a>
        
        <? if ($fornecedores!="") { ?>
        <a onmouseover="Tip('<strong>Fornecedores:</strong><br /><br /><ul><?= $fornecedores; ?></ul>');" class="menor">fornecedores</a>
        <? } ?>
        
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