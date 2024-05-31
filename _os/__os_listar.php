<?
if ($_SESSION["id_empresa"]!="") {
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2 class="titulos">Ordens de serviço cadastradas</h2>

<?
if ($_SESSION["tipo_empresa"]=="a") {
	$result= mysql_query("select oss.*, DATE_FORMAT(oss.data_os, '%d/%m/%Y') as data_os,
							servicos.servico, empresas.nome_fantasia from oss, servicos, empresas
							where oss.id_servico = servicos.id_servico
							and   oss.id_empresa = empresas.id_empresa
							order by oss.id_os desc
							");

	if (mysql_num_rows($result)==0)
		echo "<p>Nenhuma ordem de serviço cadastrada até o momento.</p>";
	else {
?>
<p>Na lista abaixo estão solicitações cadastradas:</p>

<table cellspacing="0">
	<tr>
		<th width="10%">Cód.</th>
		<th width="15%">Data</th>
		<th width="25%" align="left">Empresa</th>
		<th width="35%" align="left">Serviço</th>
		<th width="15%">Prioridade</th>
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
	<tr onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=_os/os_ver&amp;id_os=<?= $rs->id_os ?>');" class="<?= $classe; ?> corzinha maozinha">
		<td align="center"><?= $rs->id_os; ?></td>
		<td align="center"><?= $rs->data_os; ?></td>
		<td><?= $rs->nome_fantasia; ?></td>
		<td><?= $rs->servico; ?></td>
		<td align="center"><?= pega_prioridade($rs->prioridade); ?></td>

	</tr>
	<? $i++; } ?>
</table>
<?
	}
}
elseif ($_SESSION["tipo_empresa"]=="c") {
	$result= mysql_query("select oss.*, DATE_FORMAT(oss.data_os, '%d/%m/%Y') as data_os, servicos.servico from oss, servicos
							where oss.id_empresa= '". $_SESSION["id_empresa"] ."'
							and   oss.id_servico = servicos.id_servico
							order by oss.id_os desc
							");
	if (mysql_num_rows($result)==0)
		echo "<p>Nenhuma ordem de serviço cadastrada até o momento.</p>";
	else {
?>
<p>Na lista abaixo estão solicitações cadastradas:</p>

<table cellspacing="0">
	<tr>
		<th width="10%">Cód.</th>
		<th width="15%">Data</th>
		<th width="50%" align="left">Serviço</th>
		<th width="25%">Prioridade</th>
	</tr>
	<?
	$i= 0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0)
			$classe= "cor_sim";
		else
			$classe= "cor_nao";
	?>
	<tr onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=_os/os_ver&amp;id_os=<?= $rs->id_os ?>');" class="<?= $classe; ?> corzinha maozinha">
		<td align="center"><?= $rs->id_os; ?></td>
		<td align="center"><?= $rs->data_os; ?></td>
		<td><?= $rs->servico; ?></td>
		<td align="center"><?= pega_prioridade($rs->prioridade); ?></td>
	</tr>
	<? $i++; } ?>
</table>
<? } } } ?>