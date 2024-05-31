<?
if (pode("r", $_SESSION["permissao"])) {
	
	if ($ano=="") $ano= date("Y");
	
	$result= mysql_query("select *, DATE_FORMAT(data_feriado, '%d/%m/%Y') as data_feriado2
								from  rh_feriados
								where rh_feriados.id_empresa = '". $_SESSION["id_empresa"] ."'
								/* and   DATE_FORMAT(data_feriado, '%Y') = '". $ano ."' */
								order by rh_feriados.data_feriado desc
								") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Feriados</h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/feriado&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
	  <th width="9%">Cód.</th>
	  <th width="27%" align="left">Data</th>
      <th width="49%" align="left">Feriado</th>
	  <th width="15%">Ações</th>
  </tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha" id="linha_<?=$i;?>">
		<td align="center"><?= $rs->id_feriado; ?></td>
		<td><?= $rs->data_feriado2; ?></td>
        <td><?= $rs->feriado; ?></td>
        <td align="center">
			<a href="./?pagina=rh/feriado&amp;acao=e&amp;id_feriado=<?= $rs->id_feriado; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'feriadoExcluir&amp;id_feriado=<?= $rs->id_feriado; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>