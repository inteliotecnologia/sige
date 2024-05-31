<?
if (pode("r", $_SESSION["permissao"])) {
	
	if ($_GET["tipo_treinamento"]!="") $tipo_treinamento= $_GET["tipo_treinamento"];
	if ($_POST["tipo_treinamento"]!="") $tipo_treinamento= $_POST["tipo_treinamento"];
	
	if ($tipo_treinamento=='') $tipo_treinamento=1;
	
	if ($ano=="") $ano= date("Y");
	
	$result= mysql_query("select *, DATE_FORMAT(data_treinamento, '%d/%m/%Y') as data_treinamento2
								from  rh_treinamentos
								where rh_treinamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   tipo_treinamento = '". $tipo_treinamento ."'
								order by rh_treinamentos.data_treinamento desc
								") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Treinamentos <?= pega_tipo_treinamento($tipo_treinamento); ?>s</h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/treinamento&amp;acao=i&amp;tipo_treinamento=<?=$tipo_treinamento;?>">inserir</a></li>
    <li><a href="./?pagina=rh/treinamento_busca&amp;geral=1">treinamentos por funcionário</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
	  <th width="6%">Cód.</th>
	  <th width="15%" align="left">Data</th>
      <th width="41%" align="left">Treinamento</th>
      <th width="26%" align="left">Participantes</th>
	  <th width="12%">Ações</th>
  </tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha" id="linha_<?=$i;?>">
		<td align="center"><?= $rs->id_treinamento; ?></td>
		<td><?= $rs->data_treinamento2; ?></td>
        <td><?= $rs->treinamento; ?></td>
        <td>
        <?
        if ($rs->tipo_treinamento==1) {
		$result_participantes= mysql_query("select id_funcionario from rh_treinamentos_funcionarios
										   	where id_treinamento = '". $rs->id_treinamento ."'
											");
		$linhas_participantes= mysql_num_rows($result_participantes);
		
		echo $linhas_participantes;
		}
		else echo "<small>CONSULTE LISTAGEM</small>";
		?>
        </td>
        <td align="center">
			<a onmouseover="Tip('Relatório deste treinamento');" href="index2.php?pagina=rh/treinamento_relatorio&amp;tipo_relatorio=1&amp;id_treinamento=<?= $rs->id_treinamento; ?>" target="_blank">
                <img border="0" src="images/ico_pdf.png" alt="Relatório" /></a>
             |
            <a href="./?pagina=rh/treinamento&amp;acao=e&amp;id_treinamento=<?= $rs->id_treinamento; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'treinamentoExcluir&amp;id_treinamento=<?= $rs->id_treinamento; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>