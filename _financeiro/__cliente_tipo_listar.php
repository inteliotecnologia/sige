<?
if (pode("i", $_SESSION["permissao"])) {
	$result= mysql_query("select * from fi_clientes_tipos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   status_cliente_tipo = '1'
							order by cliente_tipo asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Tipos de clientes</h2>

<ul class="recuo1">
	<li><a href="./?pagina=financeiro/cliente_tipo&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="9%">Cód.</th>
		<th width="27%" align="left">Empresa</th>
		<th width="29%" align="left">Tipo de cliente</th>
		<th width="21%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_cliente_tipo; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
		<td><?= $rs->cliente_tipo; ?></td>
		<td align="center">
			<a href="./?pagina=financeiro/cliente_tipo&amp;acao=e&amp;id_cliente_tipo=<?= $rs->id_cliente_tipo; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'clienteTipoExcluir&amp;id_cliente_tipo=<?= $rs->id_cliente_tipo; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>