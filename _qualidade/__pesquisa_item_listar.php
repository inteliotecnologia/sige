<?
if (pode("i12", $_SESSION["permissao"])) {
	
	if ($_GET["id_pesquisa_categoria"]) $id_pesquisa_categoria= $_GET["id_pesquisa_categoria"];
	if ($_POST["id_pesquisa_categoria"]) $id_pesquisa_categoria= $_POST["id_pesquisa_categoria"];
	
	if ($id_pesquisa_categoria!="") $str= " and   qual_pesquisa_itens.id_pesquisa_categoria = '". $id_pesquisa_categoria ."'";
	
	$result= mysql_query("select * from qual_pesquisa_itens, qual_pesquisa_categorias
							where qual_pesquisa_categorias.id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   qual_pesquisa_categorias.id_pesquisa_categoria = qual_pesquisa_itens.id_pesquisa_categoria
							$str
							order by qual_pesquisa_categorias.id_pesquisa_categoria asc, qual_pesquisa_itens.id_pesquisa_item asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Pesquisa de satisfação - Itens</h2>

<ul class="recuo1">
	<li><a href="./?pagina=qualidade/pesquisa_item&amp;acao=i&amp;id_pesquisa_categoria=<?=$id_pesquisa_categoria;?>">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="12%">Cód.</th>
		<th width="30%" align="left">Categoria</th>
		<th width="38%" align="left">Item</th>
		<th width="20%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		if ($rs->status_item==1) $status= 0;
		else $status= 1;
	?>
	<tr id="linha_<?=$i;?>" class="<?= $classe; ?> corzinha">
		<td align="center" valign="top"><?= $rs->id_pesquisa_item; ?></td>
		<td valign="top"><?= $rs->pesquisa_categoria; ?></td>
		<td valign="top"><?= $rs->pesquisa_item; ?></td>
		<td align="center" valign="top">
			<a href="./?pagina=qualidade/pesquisa_item&amp;acao=e&amp;id_pesquisa_item=<?= $rs->id_pesquisa_item; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
            <a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'pesquisaItemStatus&amp;id_pesquisa_item=<?= $rs->id_pesquisa_item; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'pesquisaItemExcluir&amp;id_pesquisa_item=<?= $rs->id_pesquisa_item; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>