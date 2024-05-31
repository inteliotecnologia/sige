<?
if (pode("i", $_SESSION["permissao"])) {
	$result= mysql_query("select * from fi_contratos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							order by id_contrato asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Contratos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=financeiro/contrato&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="7%">Cód.</th>
		<th width="29%" align="left">Contrato</th>
        <th width="19%" align="left">Data</th>
		<th width="26%" align="left">Tipo</th>
		<th width="19%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		if ($rs->status_contrato==1) $status= 0;
		else $status= 1;
	?>
	<tr id="linha_<?=$i;?>" class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_contrato; ?></td>
		<td><?= $rs->contrato; ?></td>
        <td><?= desformata_data($rs->data_contrato); ?></td>
		<td><?= pega_tipo_contrato($rs->tipo_contrato); ?></a></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=financeiro/contrato&amp;acao=e&amp;id_contrato=<?= $rs->id_contrato; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'contratoStatus&amp;id_contrato=<?= $rs->id_contrato; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'contratoExcluir&amp;id_contrato=<?= $rs->id_contrato; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>