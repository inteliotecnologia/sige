<?
if (pode_algum("p", $_SESSION["permissao"])) {
	
	$result= mysql_query("select * from qual_reclamacoes_causas
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							order by causa asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Causas de reclamação</h2>

<ul class="recuo1">
	<li><a href="./?pagina=qualidade/causa&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="13%">ID</th>
		<th width="67%" align="left">Causa</th>
		<th width="20%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr id="linha_<?=$i;?>" class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_causa; ?></td>
		<td><?= $rs->causa; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=qualidade/causa&amp;acao=e&amp;id_causa=<?= $rs->id_causa; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			
	        <? //if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'reclamacaoCausaExcluir&amp;id_causa=<?= $rs->id_causa; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? //} ?>
            </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>