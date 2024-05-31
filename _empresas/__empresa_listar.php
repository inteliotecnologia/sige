<?
if ($_SESSION["tipo_empresa"]=="a") {
	$result= mysql_query("select * from empresas
							where tipo_empresa <> 'a'
							order by nome_fantasia asc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2 class="titulos">Empresas cadastradas</h2>

<p>Na lista abaixo estão as empresas cadastradas no sistema:</p>

<table cellspacing="0">
	<tr>
		<th width="10%">Cód.</th>
		<th width="40%" align="left">Empresa</th>
		<th width="15%">CNPJ</th>
		<th width="20%">Equipamentos</th>
		<th width="15%">Ações</th>
	</tr>
	<?
	$i= 0;
	while ($rs= mysql_fetch_object($result)) {
		
		if (($i%2)==0)
			$classe= "cor_sim";
		else
			$classe= "cor_nao";
		
		if ($rs->status_empresa==1)
			$status= 0;
		else
			$status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_empresa; ?></td>
		<td><?= $rs->nome_fantasia; ?></td>
		<td align="center"><?= formata_cnpj($rs->cnpj); ?></td>
		<td align="center"><a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=_empresas/eq_listar&amp;id_empresa=<?= $rs->id_empresa; ?>');">listar</a></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=_empresas/empresa_editar&amp;id_empresa=<?= $rs->id_empresa; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'empresaStatus&amp;id_empresa=<?= $rs->id_empresa; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a></td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>