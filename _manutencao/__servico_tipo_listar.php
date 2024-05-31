<?
if (pode_algum("pkj", $_SESSION["permissao"])) {
	
	$result= mysql_query("select * from man_servicos_tipos
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							order by servico_tipo asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Serviços (tipos)</h2>

<ul class="recuo1">
	<li><a href="./?pagina=manutencao/servico_tipo&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="13%">ID</th>
		<th width="67%" align="left">Tipo</th>
		<th width="20%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_servico_tipo; ?></td>
		<td><?= $rs->servico_tipo; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=man/servico_tipo&amp;acao=e&amp;id_servico_tipo=<?= $rs->id_servico_tipo; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			
	        <? //if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'servicoTipoExcluir&amp;id_servico_tipo=<?= $rs->id_servico_tipo; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? //} ?>
            </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>