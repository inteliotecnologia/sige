<?
if (pode("i", $_SESSION["permissao"])) {
	$result= mysql_query("select * from fi_depositos
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							and   status_deposito = '1'
							order by deposito asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Depósitos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=financeiro/deposito&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="9%">Cód.</th>
		<th width="27%" align="left">Empresa</th>
		<th width="29%" align="left">Depósito</th>
		<th width="21%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_deposito; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
		<td><?= $rs->deposito; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=financeiro/deposito&amp;acao=e&amp;id_deposito=<?= $rs->id_deposito; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'depositoExcluir&amp;id_deposito=<?= $rs->id_deposito; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>