<?
if (pode("i", $_SESSION["permissao"])) {
	$result= mysql_query("select * from fi_centro_custos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							order by centro_custo asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Centros de custos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=financeiro/centro_custo&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="9%">Cód.</th>
		<th width="27%" align="left">Empresa</th>
		<th width="29%" align="left">Centro de custo</th>
		<th width="21%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		if ($rs->status_centro_custo==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_centro_custo; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
		<td><a href="./?pagina=financeiro/centro_custo_tipo_listar&amp;id_centro_custo=<?= $rs->id_centro_custo; ?>"><?= $rs->centro_custo; ?></a></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=financeiro/centro_custo&amp;acao=e&amp;id_centro_custo=<?= $rs->id_centro_custo; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'centroCustoStatus&amp;id_centro_custo=<?= $rs->id_centro_custo; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
            |
			<a href="javascript:ajaxLink('conteudo', 'centroCustoExcluir&amp;id_centro_custo=<?= $rs->id_centro_custo; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>