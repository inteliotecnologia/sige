<?
if (pode("rv", $_SESSION["permissao"])) {
	
	if ($_POST["id_funcionario"]!="") { $id_funcionario_aqui= $_POST["id_funcionario"]; $str .= " and   id_funcionario= '". $_POST["id_funcionario"] ."' "; }
	if ($_GET["id_funcionario"]!="") { $id_funcionario_aqui= $_GET["id_funcionario"]; $str .= " and   id_funcionario= '". $_GET["id_funcionario"] ."' "; }
	
	if (($_POST["data_historico"]!="") && ($_POST["validacoes"]=="")) $str .= " and   data_historico= '". formata_data($_POST["data_historico"]) ."' ";
	
	$result= mysql_query("select * from rh_historico
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							$str
							order by data_historico desc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Hist&oacute;rico interno</h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/historico&amp;acao=i&amp;id_funcionario=<?= $id_funcionario_aqui; ?>">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="9%">Cód.</th>
		<th width="22%" align="left">Funcionário</th>
		<th width="12%" align="left">Data</th>
		<th width="42%" align="left">Histórico</th>
		<th width="15%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center" valign="top"><?= $rs->id_historico; ?></td>
		<td valign="top"><?= pega_funcionario($rs->id_funcionario); ?></td>
		<td valign="top"><?= desformata_data($rs->data_historico); ?></td>
		<td valign="top"><?= $rs->historico; ?></td>
		<td valign="top" align="center">
			<a href="./?pagina=rh/historico&amp;acao=e&amp;id_historico=<?= $rs->id_historico; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
            |
			<a href="javascript:ajaxLink('conteudo', 'historicoExcluir&amp;id_historico=<?= $rs->id_historico; ?>&amp;id_funcionario=<?= $id_funcionario_aqui; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
       </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>