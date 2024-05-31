<?
if (pode_algum("rhvwpsu", $_SESSION["permissao"])) {
	
	if (($_POST["id_funcionario_solicitante"]!="") && ($_POST["validacoes"]=="")) $str .= " and   id_funcionario_solicitante= '". $_POST["id_funcionario_solicitante"] ."' ";
	if (($_POST["id_funcionario_assume"]!="") && ($_POST["validacoes"]=="")) $str .= " and   id_funcionario_assume= '". $_POST["id_funcionario_assume"] ."' ";
	if (($_POST["data_escala_troca"]!="") && ($_POST["validacoes"]=="")) $str .= " and   data_escala_troca= '". formata_data($_POST["data_escala_troca"]) ."' ";
	
	$result= mysql_query("select * from rh_escala_troca
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							$str
							order by data_escala_troca desc, id_escala_troca desc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Trocas de escala</h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/escala_troca&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="7%">Cód.</th>
		<th width="19%" align="left">Funcionário (solicitante)</th>
		<th width="21%" align="left">Funcionário (assume)</th>
		<th width="12%">Data</th>
		<th width="26%" align="left">Justificativa</th>
		<th width="15%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center" valign="top"><?= $rs->id_escala_troca; ?></td>
		<td valign="top"><?= pega_funcionario($rs->id_funcionario_solicitante); ?></td>
		<td valign="top"><?= pega_funcionario($rs->id_funcionario_assume); ?></td>
		<td valign="top" align="center"><?= desformata_data($rs->data_escala_troca); ?></td>
		<td valign="top"><?= $rs->justificativa; ?></td>
		<td valign="top" align="center">
            <a href="index2.php?pagina=rh/documento&amp;tipo=7&amp;id_escala_troca=<?= $rs->id_escala_troca; ?>" target="_blank">
                <img border="0" src="images/ico_pdf.png" alt="Relatório" />
            </a>
			|
            <a href="./?pagina=rh/escala_troca&amp;acao=e&amp;id_escala_troca=<?= $rs->id_escala_troca; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
            |
			<a href="javascript:ajaxLink('conteudo', 'escalaTrocaExcluir&amp;id_escala_troca=<?= $rs->id_escala_troca; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
       </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>