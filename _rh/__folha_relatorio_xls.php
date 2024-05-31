<?
require_once("conexao.php");
require_once("funcoes.php");
require_once("funcoes_espelho.php");

if (pode_algum("rh", $_SESSION["permissao"])) {
	
	// definimos o tipo de arquivo
	header("Content-type: application/msexcel");
	// Como será gravado o arquivo
	header("Content-Disposition: attachment; filename=contabilidade_relatorio_". date("d-m-Y_H:i:s") .".xls");

	$periodo2= explode('/', $_GET["periodo"]);
	
	$data1_mk= mktime(0, 0, 0, $periodo2[0]-1, 26, $periodo2[1]);
	$data2_mk= mktime(0, 0, 0, $periodo2[0], 25, $periodo2[1]);
	
	$data1= date("d/m/Y", $data1_mk);
	$data2= date("d/m/Y", $data2_mk);
	
			
	echo "<h1>RELATÓRIO PARA CONTABILIDADE</h1>";
	
	echo "<h2>". traduz_mes($periodo2[0]) ."/". $periodo2[1] ."</h2>";
	
	// ------------- tabela
	
	$result_des= mysql_query("select * from rh_motivos
								where tipo_motivo = 't'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								order by motivo asc");
	
	$linhas_desconto= (mysql_num_rows($result_des)+1);
	
	echo '<table border="1" width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <th width="14%" rowspan="2">Funcion&aacute;rios</th>
    <th width="9%" rowspan="2">Insal.</th>
    <th width="7%" rowspan="2">Horas not.</th>
    <th width="9%" rowspan="2">Horas red.</th>
    <th colspan="2">HE Diurno</th>
    <th colspan="2">HE Noturno</th>
    <th colspan="'. $linhas_desconto .'">Descontos</th>
    <th rowspan="2" width="19%">OBS</th>
  </tr>
  <tr>
    <th width="5%">60%</th>
    <th width="5%">100%</th>
    <th width="6%">60%</th>
    <th width="6%">100%</th>';
	
	while ($rs_des= mysql_fetch_object($result_des)) {
		echo '<th width="5%">'. $rs_des->motivo .'</th>';
	}
	
    echo '<th width="5%">Faltas</th>
  </tr>';
  
  
  $j=0;
	$result_fun= mysql_query("select *
								from  pessoas, rh_funcionarios, rh_carreiras
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   pessoas.tipo = 'f'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   rh_funcionarios.status_funcionario = '1'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
	while ($rs_fun= mysql_fetch_object($result_fun)) {
		
		$retorno= pega_dados_rh($_SESSION["id_empresa"], 0, 0, $rs_fun->id_funcionario, $data1, $data2);
		$novo= explode("@", $retorno);
		
		if ($rs_fun->insalubridade!=0) $insalubridade= $rs_fun->insalubridade ."%";
		else $insalubridade= "";
		
		$horas_noturnas= $novo[2];
		
		if (($j%2)==0) $str= "bgcolor='#EFEFEF'";
		else $str= "";
		
		echo '<tr '. $str .'>
		<td>'. $rs_fun->nome_rz .'</td>
		<td>'. $insalubridade .'</td>
		<td>'. calcula_total_horas_ss($horas_noturnas) .'</td>
		<td>'. calcula_total_horas_ss(floor((($horas_noturnas*60)/52.3)-$horas_noturnas)) .'</td>
		<td>'. (calcula_total_horas_ss($novo[4])) .'</td>
		<td>'. (calcula_total_horas_ss($novo[5])) .'</td>
		<td>'. (calcula_total_horas_ss($novo[6])) .'</td>
		<td>'. (calcula_total_horas_ss($novo[7])) .'</td>';
		
		$result_des= mysql_query("select * from rh_motivos
									where tipo_motivo = 't'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									order by motivo asc");
		
		$linhas_desconto= mysql_num_rows($result_des);
		
		$largura= (5/($linhas_desconto+1));
		
		while ($rs_des= mysql_fetch_object($result_des)) {
			$result= mysql_query("select * from rh_descontos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_funcionario = '". $rs_fun->id_funcionario ."'
									and   mes = '". $periodo2[0] ."'
									and   ano = '". $periodo2[1] ."'
									and   id_motivo = '". $rs_des->id_motivo ."'
									");
			$rs= mysql_fetch_object($result);
			
			if ($rs_des->qtde_dias==0) $valor= sim_nao_pdf($rs->valor);
			elseif ($rs->valor!=0) $valor= fnum($rs->valor);
			else $valor= "";
			
			echo '<td>'. $valor .'</td>';
		}

		
		
		echo '
		<td>'. calcula_total_horas_ss($novo[3]) .'</td>
		<td></td>
	  </tr>';
		
		$j++;
	}
  
	echo '</table>';
}
?>