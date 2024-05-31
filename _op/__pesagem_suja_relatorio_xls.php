<?
require_once("conexao.php");
require_once("funcoes.php");
require_once("funcoes_espelho.php");

if (pode("pls", $_SESSION["permissao"])) {
	
	// definimos o tipo de arquivo
	header("Content-type: application/msexcel");
	// Como será gravado o arquivo
	header("Content-Disposition: attachment; filename=pesagem_suja_relatorio_xls_". date("d-m-Y_H:i:s") .".xls");

	$periodo2= explode('/', $_POST["periodo"]);
	
	$data1_mk= mktime(0, 0, 0, $periodo2[0]-1, 26, $periodo2[1]);
	$data2_mk= mktime(0, 0, 0, $periodo2[0], 25, $periodo2[1]);
	
	$data1= date("d/m/Y", $data1_mk);
	$data2= date("d/m/Y", $data2_mk);
	
			
	echo "<h1>RELATÓRIO PARA PESAGEM MENSAL</h1>";
	
	echo "<h2>". traduz_mes($periodo2[0]) ."/". $periodo2[1] ."</h2>";
	
	// ------------- tabela
	
	echo '<table border="1" width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <th align="left">Data</th>
    <th align="left">Peso (manhã)</th>
    <th align="left">Primeira remessa</th>
    <th align="left">Qtde funcionários</th>
    <th align="left">Peso (tarde)</th>
    <th align="left">Primeira remessa</th>
    <th align="left">Qtde funcionários</th>
	<th align="left">Peso (total)</th>
  </tr>';  
  
  $periodo_mk= mktime(0, 0, 0, $periodo2[0], 1, $periodo2[1]);
  $total_dias_mes= date("t", $periodo_mk);
  
   for ($t=1; $t<=$total_dias_mes; $t++) {
		
		$result_total1= mysql_query("select sum(peso) as total_manha from op_suja_pesagem, op_suja_remessas
									where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
									and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
									and   op_suja_remessas.data_remessa= '". $periodo2[1] .'-'. $periodo2[0] .'-'. formata_saida($t, 2) ."'
									and   DATE_FORMAT(op_suja_remessas.hora_chegada, '%H') < '14'
									order by data_pesagem asc, hora_pesagem asc
									");
		$rs_total1= mysql_fetch_object($result_total1);
		
		$result_remessa1= mysql_query("select hora_chegada from op_suja_remessas
									where op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."' 
									and   op_suja_remessas.data_remessa= '". $periodo2[1] .'-'. $periodo2[0] .'-'. formata_saida($t, 2) ."'
									and   DATE_FORMAT(op_suja_remessas.hora_chegada, '%H') < '14'
									order by hora_chegada asc limit 1
									");
		$rs_remessa1= mysql_fetch_object($result_remessa1);
		
		$result_total2= mysql_query("select sum(peso) as total_tarde from op_suja_pesagem, op_suja_remessas
									where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
									and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
									and   op_suja_remessas.data_remessa= '". $periodo2[1] .'-'. $periodo2[0] .'-'. formata_saida($t, 2) ."'
									and   DATE_FORMAT(op_suja_remessas.hora_chegada, '%H') >= '14'
									order by data_pesagem asc, hora_pesagem asc
									");
		$rs_total2= mysql_fetch_object($result_total2);
		
		$result_remessa2= mysql_query("select hora_chegada from op_suja_remessas
									where op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."' 
									and   op_suja_remessas.data_remessa= '". $periodo2[1] .'-'. $periodo2[0] .'-'. formata_saida($t, 2) ."'
									and   DATE_FORMAT(op_suja_remessas.hora_chegada, '%H') >= '14'
									order by hora_chegada asc limit 1
									");
		$rs_remessa2= mysql_fetch_object($result_remessa2);
		
		$result_total= mysql_query("select sum(peso) as total from op_suja_pesagem, op_suja_remessas
									where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
									and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
									and   op_suja_remessas.data_remessa= '". $periodo2[1] .'-'. $periodo2[0] .'-'. formata_saida($t, 2) ."'
									order by data_pesagem asc, hora_pesagem asc
									");
		$rs_total= mysql_fetch_object($result_total);
		
		echo '<tr>
		<td align="left">'. formata_saida($t, 2) .'/'. $_POST["periodo"] .'</td>
		<td align="left">'. fnum($rs_total1->total_manha) .'</td>
		<td align="left">'. substr($rs_remessa1->hora_chegada, 0, 5) .'</td>
		<td align="left">'. $xxx .'</td>
		<td align="left">'. fnum($rs_total2->total_tarde) .'</td>
		<td align="left">'. substr($rs_remessa2->hora_chegada, 0, 5) .'</td>
		<td align="left">'. $xxx .'</td>
		<td align="left">'. fnum($rs_total->total) .'</td>
	  </tr>';
	}
  
	echo '</table>';
}
?>