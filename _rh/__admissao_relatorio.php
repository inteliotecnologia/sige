<?
if (pode("r", $_SESSION["permissao"])) {
	if($_GET["status"]==1) $tit= " ativos";
	else $tit= " inativos";
	
	$result = mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%d/%m/%Y') as data_admissao
										from rh_funcionarios, pessoas, rh_carreiras
										where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.id_acao_carreira = '1'
										and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
										and   rh_funcionarios.status_funcionario = '". $_GET["status"] ."'
										order by rh_carreiras.data asc, pessoas.nome_rz asc
										") or die(mysql_error());
?>

<h2>Funcionários <?= $tit; ?> por data de admissão</h2>

<table cellspacing="0" width="100%">
	<tr>
		<th width="7%">Cód.</th>
        <th width="19%" align="left">Empresa</th>
        <th width="16%" align="left">Departamento</th>
        <th width="33%" align="left">Nome</th>
        <th width="16%" align="left">Data</th>
  </tr>
	<?
	$j=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($j%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";

		$idade= calcula_idade($rs->data_nasc2);
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_funcionario; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
        <td><?= pega_departamento($rs->id_departamento); ?></td>
        <td><?= $rs->nome_rz; ?></td>
        <td><?= $rs->data_admissao; ?></td>
	</tr>
	<? $j++; } ?>
</table>

<? } ?>