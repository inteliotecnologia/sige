<?
if (pode("iqt", $_SESSION["permissao"])) {
	
	if ($_GET["letra"]!="") $letra= $_GET["letra"];
	if ($_POST["letra"]!="") $letra= $_POST["letra"];
	
	if ($letra==".") {
		for ($i='a'; $i!="aa"; $i++)
			$str .= " and item not like '". $i ."%' ";
	}
	else if ($letra!="") $str .= " and   item like '". $letra ."%' ";
	
	$result= mysql_query("select * from fi_itens
							where 1=1
							$str
							/* and   id_empresa = '". $_SESSION["id_empresa"] ."' */
							order by item asc
							") or die(mysql_error());
?>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="10%">Cód.</th>
		<th width="37%" align="left">Produto</th>
		<th width="12%" align="left">Apresentação</th>
		<th width="25%" align="left">Tipo</th>
		<th width="16%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_item; ?></td>
		<td><?= $rs->item; ?></td>
		<td><?= pega_tipo_apres($rs->tipo_apres); ?></td>
		<td><?= pega_centro_custo_tipo($rs->id_centro_custo_tipo); ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=financeiro/item&amp;acao=e&amp;id_item=<?= $rs->id_item; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'itemExcluir&amp;id_item=<?= $rs->id_item; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>