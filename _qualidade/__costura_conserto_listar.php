<?
if (pode_algum("12(", $_SESSION["permissao"])) {
	

	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	if ($periodo!="") $str.= " and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". $periodo ."' ";
	
	if ($_POST["data_chegada"]!="") $data_chegada= $_POST["data_chegada"];
	if ($_GET["data_chegada"]!="") $data_chegada= $_GET["data_chegada"];
	if ($data_chegada!="") $str .= " and   data_chegada= '". formata_data_hifen($data_chegada) ."' ";
	
	if ($_POST["data_entrega"]!="") $data_entrega= $_POST["data_entrega"];
	if ($_GET["data_entrega"]!="") $data_entrega= $_GET["data_entrega"];
	if ($data_entrega!="") $str .= " and   data_entrega= '". formata_data_hifen($data_entrega) ."' ";
	
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($id_cliente!="") $str .= " and   id_cliente= '". $id_cliente ."' ";
	
	$result= mysql_query("select * from op_limpa_costura_consertos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by data_chegada desc
							");
	
	$num= 50;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select * from op_limpa_costura_consertos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by data_chegada desc
							limit $inicio, $num
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Costura - Conserto de peças</h2>

<ul class="recuo1">
	<li><a href="./?pagina=qualidade/costura_conserto&amp;acao=i" id="link_inserir">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="8%" align="left">Data chegada</th>
	    <th width="14%" align="left">Cliente</th>
	    <th width="8%">Data entrega</th>
	    <th width="12%">Total recebido</th>
		<th width="12%">Total consertado</th>
		<th width="9%">Total substitu&iacute;do</th>
		<th width="10%">Total baixa</th>
        <th width="10%">Total devolvido</th>
      <th width="17%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		$result_costura= mysql_query("select sum(qtde_recebido) as qtde_recebido, sum(qtde_consertado) as qtde_consertado,
										sum(qtde_substituido) as qtde_substituido, sum(qtde_baixa) as qtde_baixa, sum(qtde_devolvido) as qtde_devolvido
										from op_limpa_costura_consertos_pecas
										where id_costura_conserto = '". $rs->id_costura_conserto ."'
										");
		$rs_costura= mysql_fetch_object($result_costura);
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td><?= desformata_data($rs->data_chegada); ?></td>
		<td><?= pega_sigla_pessoa($rs->id_cliente); ?></td>
		<td align="center">
			<? if ((trim($rs->data_entrega)=="") || (trim($rs->data_entrega)=="0000-00-00")) echo "Consertando"; else echo desformata_data($rs->data_entrega); ?>
        </td>
		<td align="center"><?= $rs_costura->qtde_recebido; ?></td>
		<td align="center"><?= $rs_costura->qtde_consertado; ?></td>
		<td align="center"><?= $rs_costura->qtde_substituido; ?></td>
		<td align="center"><?= $rs_costura->qtde_baixa; ?></td>
        <td align="center"><?= $rs_costura->qtde_devolvido; ?></td>
		<td align="center">
            <a onmouseover="Tip('Gerar relatório');" href="index2.php?pagina=qualidade/costura_conserto_relatorio&amp;id_costura_conserto=<?= $rs->id_costura_conserto; ?>" target="_blank">
                <img border="0" src="images/ico_pdf.png" alt="Relatório" />
            </a> |
                    
            <a id="link_edita<?=$i;?>" href="./?pagina=qualidade/costura_conserto&amp;acao=e&amp;id_costura_conserto=<?= $rs->id_costura_conserto; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'costuraConsertoExcluir&amp;id_costura_conserto=<?= $rs->id_costura_conserto; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
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
			echo " <a href=\"./?pagina=qualidade/costura_conserto_listar&amp;data=". $data2 ."&amp;periodo=". $periodo ."&amp;id_cliente=". $id_cliente ."&amp;id_tipo_roupa=". $id_tipo_roupa ."&amp;id_turno=". $id_turno ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}
?>

<script type="text/javascript">
	daFoco("link_edita0");
</script>

<? } ?>