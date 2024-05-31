<?
if (pode("i12", $_SESSION["permissao"])) {
	$result= mysql_query("select * from qual_pesquisa_categorias
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							order by id_pesquisa_categoria asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Pesquisa de safisfação - categorias</h2>

<ul class="recuo1">
	<li><a href="./?pagina=qualidade/pesquisa_categoria&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="9%">Cód.</th>
		<th width="29%" align="left">Categoria</th>
		<th width="21%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
	?>
	<tr id="linha_<?=$i;?>" class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_pesquisa_categoria; ?></td>
		<td><a href="./?pagina=qualidade/pesquisa_item_listar&amp;id_pesquisa_categoria=<?= $rs->id_pesquisa_categoria; ?>"><?= $rs->pesquisa_categoria; ?></a></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=qualidade/pesquisa_categoria&amp;acao=e&amp;id_pesquisa_categoria=<?= $rs->id_pesquisa_categoria; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'pesquisaCategoriaExcluir&amp;id_pesquisa_categoria=<?= $rs->id_pesquisa_categoria; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>