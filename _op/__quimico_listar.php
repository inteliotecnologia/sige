<?
if (pode_algum("ps", $_SESSION["permissao"])) {
	
	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	if ($periodo!="") $str.= " and   DATE_FORMAT(data_troca, '%m/%Y') = '". $periodo ."' ";
	
	if ($_POST["data"]!="") $data2= $_POST["data"];
	if ($_GET["data"]!="") $data2= $_GET["data"];
	if ($data2!="") $str .= " and   data_troca = '". formata_data_hifen($data2) ."' ";
	
	if ($_POST["id_quimico"]!="") $id_quimico= $_POST["id_quimico"];
	if ($_GET["id_quimico"]!="") $id_quimico= $_GET["id_quimico"];
	if ($id_quimico!="") $str .= " and   id_quimico= '". $id_quimico ."' ";
	
	$result= mysql_query("select * from op_suja_quimicos_trocas
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by data_troca desc, hora_troca desc
							");
	
	$num= 50;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select * from op_suja_quimicos_trocas
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by data_troca desc, hora_troca desc
							limit $inicio, $num
							");
	
	$periodo= explode("/", $periodo);
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Área suja - Troca de químicos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=op/quimico&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="7%">Cód.</th>
		<th width="16%">Data da troca</th>
        <th width="19%" align="left">Galão/químico</th>
        <th width="14%">Litros</th>
        <th width="26%">Responsável</th>
      <th width="18%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_troca_quimico; ?></td>
		<td align="center"><?= desformata_data($rs->data_troca); ?></td>
		<td><?= $rs->num_galao ."/". pega_quimico($rs->id_quimico); ?></td>
		<td align="center"><?= fnum($rs->qtde); ?></td>
        <td align="center"><?= primeira_palavra(pega_funcionario($rs->id_funcionario)); ?></td>
		<td align="center">
            <a href="./?pagina=op/quimico&amp;acao=e&amp;id_troca_quimico=<?= $rs->id_troca_quimico; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'trocaQuimicoExcluir&amp;id_troca_quimico=<?= $rs->id_troca_quimico; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
       </td>
	</tr>
	<? $i++; } ?>
</table>

<?
if ($num_paginas > 1) {
	echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=op/quimico_listar&amp;data=". $data2 ."&amp;periodo=". $periodo ."id_quimico=". $id_quimico ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}
?>

<? } ?>