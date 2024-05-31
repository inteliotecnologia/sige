<?
if (pode("e", $_SESSION["permissao"])) {
	$result= mysql_query("select *
							from  tr_cronograma
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							order by id_cliente asc,
							id_dia asc,
							tipo asc,
							hora_cronograma asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Cronograma de entrega/coleta</h2>

<ul class="recuo1">
	<li><a href="./?pagina=transporte/cronograma&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
	  <th width="7%">Cód.</th>
	  <th width="46%" align="left">Cliente</th>
	  <th width="13%">Dia</th>
	  <th width="11%">Tipo</th>
      <th width="11%">Hora</th>
	  <th width="12%">Ações</th>
  </tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_cronograma; ?></td>
		<td><?= pega_pessoa($rs->id_cliente); ?></td>
		<td align="center"><?= traduz_dia($rs->id_dia); ?></td>
		<td align="center"><?= pega_coleta_entrega($rs->tipo); ?></td>
        <td align="center"><?= substr($rs->hora_cronograma, 0, 5); ?></td>
        <td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=transporte/cronograma&amp;acao=e&amp;id_cronograma=<?= $rs->id_cronograma; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
            |
			<a href="javascript:ajaxLink('conteudo', 'transpCronomgramaExcluir&amp;id_cronograma=<?= $rs->id_cronograma; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a>
            </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>