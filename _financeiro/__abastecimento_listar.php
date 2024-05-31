<?
if (pode("u", $_SESSION["permissao"])) {
	
	if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
		$data1= $_POST["data1"];
		$data2= $_POST["data2"];
	}
	else {
		if ( ($_GET["data1"]!="") && ($_GET["data2"]!="") ) {
			$data1= $_GET["data1"];
			$data2= $_GET["data2"];
		}
	}
	
	if ( ($data1!="") && ($data2!="") ) {
		$data1f= $data1; $data1= formata_data_hifen($data1);
		$data2f= $data2; $data2= formata_data_hifen($data2);
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	elseif ($_POST["periodo"]!="") {
		$periodo= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(0, 0, 0, $periodo[0]-1, 26, $periodo[1]);
		$data2_mk= mktime(0, 0, 0, $periodo[0], 25, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	
	if (($_POST["id_abastecimento"]!="") && (isset($_POST["geral"])) ) {
		$acao= 'e';
		require_once("_financeiro/__abastecimento.php");
		die();
	}
	
	if ($data1!="") $str .= " and   fi_abastecimentos.data >= '$data1' ";
	if ($data2!="") $str .= " and   fi_abastecimentos.data <= '$data2' ";
	
	if (($_POST["id_veiculo"]!="") && (isset($_POST["geral"]))) $str .= " and   fi_abastecimentos.id_veiculo= '". $_POST["id_veiculo"] ."' ";
	if (($_POST["id_funcionario"]!="") && (isset($_POST["geral"]))) $str .= " and   fi_abastecimentos.id_funcionario= '". $_POST["id_funcionario"] ."' ";
	if (($_POST["id_departamento"]!="") && (isset($_POST["geral"]))) $str .= " and   rh_carreiras.id_departamento= '". $_POST["id_departamento"] ."' ";
	if (($_POST["id_usuario_at"]!="") && (isset($_POST["geral"]))) $str .= " and   fi_abastecimentos.id_usuario_at= '". $_POST["id_usuario_at"] ."' ";
	
	$sql .= "select fi_abastecimentos.* from fi_abastecimentos, rh_carreiras
									where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_funcionario = fi_abastecimentos.id_funcionario
									and   rh_carreiras.atual = '1'
									". $str ."
									order by fi_abastecimentos.data desc
									";
	//echo $sql;
	
	$result= mysql_query($sql) or die(mysql_error());
	
	$total_antes= mysql_num_rows($result);
		
	/*if ($tudo!=1) {
		$num= 30;
		$total_linhas = mysql_num_rows($result);
		$num_paginas = ceil($total_linhas/$num);
		if (!isset($num_pagina))
			$num_pagina = 0;
		$comeco = $num_pagina*$num;
		
		$result= mysql_query($sql ." limit $comeco, $num") or die(mysql_error());
	}*/
	
	//echo $sql;

?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Controle de abastecimentos</h2>

<? if ( ($_POST["geral"]=="1") && (pode('u', $_SESSION["permissao"])) ) { ?>
<ul class="recuo1">
    <li><a target="_blank" href="index2.php?pagina=financeiro/abastecimento_relatorio&amp;data1=<?= $data1; ?>&amp;data2=<?= $data2; ?>&amp;id_funcionario=<?= $_POST["id_funcionario"]; ?>&amp;id_veiculo=<?= $_POST["id_veiculo"]; ?>&amp;id_departamento=<?= $_POST["id_departamento"]; ?>&amp;id_usuario_at=<?= $_POST["id_usuario_at"]; ?>">gerar relatório com as informações referentes</a></li>
</ul>
<? } else { ?>
<ul class="recuo1">
	<li><a href="./?pagina=financeiro/abastecimento&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
	  <th width="7%">Cód.</th>
	  <th width="15%" align="left">Data</th>
      <th width="24%" align="left">Veículo</th>
	  <th width="32%" align="left">Motorista</th>
      <th width="10%" align="left">Valor</th>
	  <th width="12%" class="unsortable">Ações</th>
  </tr>
	<?
	$j=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($j%2)==0) $classe= "odd";
		else $classe= "even";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_abastecimento; ?></td>
		<td><?= desformata_data($rs->data); ?></td>
        <td><?= pega_veiculo($rs->id_veiculo); ?></td>
        <td><?= pega_funcionario($rs->id_funcionario); ?></td>
        <td>R$ <?= fnum($rs->valor_total); ?></td>
		<td align="center">
			<? /*<a href="index2.php?pagina=financeiro/abastecimento_requisicao_relatorio&amp;id_abastecimento=<?= $rs->id_abastecimento; ?>" target="_blank">
                    <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                </a>
            |*/ ?>
            
            <a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=financeiro/abastecimento&amp;acao=e&amp;id_abastecimento=<?= $rs->id_abastecimento; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
                |
			<a href="javascript:ajaxLink('conteudo', 'abastecimentoExcluir&amp;id_abastecimento=<?= $rs->id_abastecimento; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
                </a>
        </td>
	</tr>
	<? $j++; } ?>
</table>

<?
if ($total_linhas>0) {
	if ($num_paginas > 1) {
		$texto_url= "carregaPagina&amp;pagina=financeiro/abastecimento_listar&amp;num_pagina=";
		
		$texto_paginacao .= "<div id=\"paginacao\">
				<ul>";
		if ($num_pagina > 0) {
			$menos = $num_pagina - 1;
			$texto_paginacao .=  "<li><a class=\"maior\" href=\"javascript:void(0);\" onclick=\"ajaxLink('conteudo', '". $texto_url . $menos ."')\">&laquo; Anterior</a></li>";
		}

		for ($i=0; $i<$num_paginas; $i++) {
			$link = $i + 1;
			if ($num_pagina==$i)
				$texto_paginacao .= "<li class=\"paginacao_atual\">". $link ."</li>";
			else
				$texto_paginacao .=  "<li><a href=\"javascript:void(0);\" onclick=\"ajaxLink('conteudo', '". $texto_url . $i ."')\">". $link ."</a></li>";
		}
	
		if ($num_pagina < ($num_paginas - 1)) {
			$mais = $num_pagina + 1;
			$texto_paginacao .=  "<li><a class=\"maior\" href=\"javascript:void(0);\" onclick=\"ajaxLink('conteudo', '". $texto_url . $mais ."')\">Pr&oacute;xima &raquo;</a></li>";
		}
		$texto_paginacao .=  "</ul>
			</div>";

		echo $texto_paginacao;
	}
}
?>

<? } } ?>