<?
if (pode("r", $_SESSION["permissao"])) {
	if ($_SESSION["id_empresa"]!="")
		$str= "and   rh_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."' ";
	
	$result= mysql_query("select * from rh_turnos, rh_departamentos
							where rh_turnos.id_departamento = rh_departamentos.id_departamento
							". $str ."
							order by rh_departamentos.departamento asc,
									 rh_turnos.turno asc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Turnos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/turno&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="6%">Cód.</th>
		<th width="23%" align="left">Empresa</th>
        <th width="19%" align="left">Departamento</th>
		<th width="17%" align="left">Turno</th>
	  <th width="19%" align="left">Intervalos</th>
		<th width="16%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		if ($rs->status_turno==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_turno; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
        <td><?= $rs->departamento; ?></td>
		<td><a href="./?pagina=rh/turno_esquema&amp;acao=e&amp;id_turno=<?= $rs->id_turno; ?>"><?= $rs->turno; ?></a></td>
		<td align="left">
        	<?= pega_intervalos($rs->id_turno); ?>
        </td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'turnoStatus&amp;id_turno=<?= $rs->id_turno; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
            |
            <a href="./?pagina=rh/turno_esquema&amp;acao=e&amp;id_turno=<?= $rs->id_turno; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
            |
			<a href="javascript:ajaxLink('conteudo', 'turnoExcluir&amp;id_turno=<?= $rs->id_turno; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>