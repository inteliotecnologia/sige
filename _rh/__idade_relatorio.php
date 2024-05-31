<?
if (pode("r", $_SESSION["permissao"])) {
	$periodo1= date("Y-m-d", mktime(0, 0, 0, date('m'), date('d'), date('Y')-$_POST["p1"]));
	$periodo2= date("Y-m-d", mktime(0, 0, 0, date('m'), date('d'), date('Y')-($_POST["p2"]+1)));
		
	$result = mysql_query("select *, DATE_FORMAT(pessoas.data, '%d/%m/%Y') as data_nasc2
									from rh_funcionarios, pessoas, rh_departamentos, rh_carreiras
									where rh_carreiras.id_departamento = rh_departamentos.id_departamento
									and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
									and   rh_carreiras.atual = '1'
									and   rh_funcionarios.status_funcionario = '1'
									and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
									and   pessoas.data <= '$periodo1'
									and   pessoas.data >= '$periodo2'
									order by pessoas.data desc, pessoas.nome_rz asc
									") or die(mysql_error());
?>

<p>Encontrados <strong><?= mysql_num_rows($result); ?></strong> funcionários entre <strong><?= $_POST["p1"]; ?></strong> e <strong><?= $_POST["p2"]; ?></strong> anos.</p>

<br /><br />

<table cellspacing="0" width="100%">
	<tr>
		<th width="7%">Cód.</th>
        <th width="19%" align="left">Empresa</th>
        <th width="16%" align="left">Departamento</th>
        <th width="33%" align="left">Nome</th>
        <th width="16%" align="left">Data de nascimento</th>
        <th width="9%" align="left">Idade</th>
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
        <td><?= $rs->data_nasc2; ?></td>
		<td><?= $idade; ?></td>
	</tr>
	<? $j++; } ?>
</table>

<? } ?>