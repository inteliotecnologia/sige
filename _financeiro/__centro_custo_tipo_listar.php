<?
if (pode("i", $_SESSION["permissao"])) {
	
	if ($_GET["id_centro_custo"]!="")
		$result= mysql_query("select * from fi_centro_custos_tipos, fi_cc_ct
								where fi_centro_custos_tipos.id_empresa = '". $_SESSION["id_empresa"] ."' 
								and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
								and   fi_cc_ct.id_centro_custo = '". $_GET["id_centro_custo"] ."'
								order by fi_centro_custos_tipos.id_centro_custo_tipo asc
								") or die(mysql_error());
	else
		$result= mysql_query("select * from fi_centro_custos_tipos
								where fi_centro_custos_tipos.id_empresa = '". $_SESSION["id_empresa"] ."' 
								order by fi_centro_custos_tipos.id_centro_custo_tipo asc
								") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Centros de custos - Tipos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=financeiro/centro_custo_tipo&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="12%">Cód.</th>
		<th width="30%" align="left">Tipo</th>
		<th width="38%" align="left">Centro de custo</th>
		<th width="20%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		/*
		if ($rs->status_centro_custo==1) $status= 0;
		else $status= 1;*/
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center" valign="top"><?= $rs->id_centro_custo_tipo; ?></td>
		<td valign="top"><?= $rs->centro_custo_tipo; ?></td>
		<td valign="top">
		<?
        $result3= mysql_query("select * from fi_centro_custos, fi_cc_ct
							  	where fi_centro_custos.id_centro_custo = fi_cc_ct.id_centro_custo
								and   fi_cc_ct.id_centro_custo_tipo = '". $rs->id_centro_custo_tipo ."'
								order by fi_centro_custos.centro_custo asc
								");

		while ($rs3= mysql_fetch_object($result3)) {
			echo $rs3->id_centro_custo .") ". $rs3->centro_custo ."<br />";
		}
		?>
        </td>
		<td align="center" valign="top">
			<a href="./?pagina=financeiro/centro_custo_tipo&amp;acao=e&amp;id_centro_custo_tipo=<?= $rs->id_centro_custo_tipo; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'tipoCentroCustoExcluir&amp;id_centro_custo_tipo=<?= $rs->id_centro_custo_tipo; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>