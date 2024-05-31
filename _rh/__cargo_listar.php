<?
if (pode("r", $_SESSION["permissao"])) {
	if ($_SESSION["id_empresa"]!="")
		$str= "and   rh_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."' ";
	
	$result= mysql_query("select * from rh_cargos, rh_departamentos
							where rh_cargos.id_departamento = rh_departamentos.id_departamento
							". $str ."
							order by rh_departamentos.departamento asc,
									 rh_cargos.cargo asc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Cargos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/cargo&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="8%">Cód.</th>
		<th width="29%" align="left">Empresa</th>
        <th width="29%" align="left">Departamento</th>
		<th width="20%" align="left">Cargo</th>
		<th width="20%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		if ($rs->status_cargo==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_cargo; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
        <td><?= $rs->departamento; ?></td>
		<td><?= $rs->cargo; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/cargo&amp;acao=e&amp;id_cargo=<?= $rs->id_cargo; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'cargoStatus&amp;id_cargo=<?= $rs->id_cargo; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
            |
			<a href="javascript:ajaxLink('conteudo', 'cargoExcluir&amp;id_cargo=<?= $rs->id_cargo; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>