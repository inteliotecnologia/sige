<? if ($_SESSION["id_usuario"]!="") { ?>
<h2 class="titulos">Fechar produção</h2>

<?
$data1_mk= mktime();

$calculo_data= $data1_mk;
		
$ontem_mk= mktime(14, 0, 0, date("m"), date("d")-1, date("Y"));
$amanha_mk= mktime(14, 0, 0, date("m"), date("d")+1, date("Y"));

$id_dia= date("w", $calculo_data);

$data_formatada= date("d/m/Y", $calculo_data);

$ontem= date("Y-m-d", $ontem_mk);
$data= date("Y-m-d", $calculo_data);
$amanha= soma_data($data, 1, 0, 0);

$data_mesmo= date("Y-m-d", $calculo_data);

$total_dia=0;
$media_dia=0;
$media_turno_aqui=0;

if (($_SESSION["id_turno_sessao"]=="-2") || ($_SESSION["id_turno_sessao"]=="-1")) {
			
	$result_soma= mysql_query("select sum(peso) as soma from op_limpa_pesagem
								where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
								and   op_limpa_pesagem.extra = '0'
								and   op_limpa_pesagem.id_turno = '". $_SESSION["id_turno_sessao"] ."'
								and   ( op_limpa_pesagem.data_pesagem = '". $ontem ."' or  op_limpa_pesagem.data_pesagem = '". $data ."' )
								") or die(mysql_error());
	$rs_soma= mysql_fetch_object($result_soma);
	
	$soma= $rs_soma->soma;
	$total_dia= $soma;
	
	//$funcionarios_neste_plantao= pega_funcionarios_trabalhando_retroativo_plantao(1, $data_mesmo);
	
	if (date("H")<7) {
		$data_referencia= $ontem;
		$data_referencia_amanha= $data;
	}
	else {
		$data_referencia= $data;
		$data_referencia_amanha= $amanha;
	}
	
	$sql_pre="";
					
	$result_pre= mysql_query("select * from rh_ponto_producao
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   vale_dia = '". $data_referencia ."'
								");
	$linhas_pre= mysql_num_rows($result_pre);
	
	//if (($_POST["denovo"]=="1") || ($linhas_pre==0)) {
		//$funcionarios_neste_plantao= pega_funcionarios_trabalhando_retroativo($_SESSION["id_empresa"], 1, 0, $data_referencia, $data_referencia ." 06:00:00", $data_referencia_amanha ." 06:00:00", 0, 24);
		$funcionarios_neste_plantao= pega_funcionarios_trabalhando_manual($_SESSION["id_empresa"], 1, 0, $data_mesmo);
	//}
	
	/*
	if ($linhas_pre>0) {
		
		$rs_pre= mysql_fetch_object($result_pre);
		
		if ($_POST["denovo"]=="1") {
			
			$sql_pre="update rh_ponto_producao
						set media = '". $funcionarios_neste_plantao ."'
						where id_empresa = '". $_SESSION["id_empresa"] ."'
						and   vale_dia = '". $data_referencia ."'
						";
		}
		else {
			$funcionarios_neste_plantao= $rs_pre->media;
		}
		
	}
	else {				
		$sql_pre="insert into rh_ponto_producao
				(id_empresa, vale_dia, id_turno_index, media, id_usuario)
				values
				('". $_SESSION["id_empresa"] ."', '". $data_referencia ."', '0', 
				'". $funcionarios_neste_plantao ."', '". $_SESSION["id_usuario"] ."')
				";
	}
	
	if ($sql_pre!="") {
		$result_fixa= mysql_query($sql_pre) or die(mysql_error());
	}
	*/
	
	if ($funcionarios_neste_plantao>0) $media_plantao= ($soma/$funcionarios_neste_plantao);
	else $media_plantao= 0;
	
	$funcionarios_trabalhando= $funcionarios_neste_plantao;
	$media_funcionarios= ($media_plantao/20)*6;
}
else {
	
	$h=pega_turno_padrao_pelo_id_turno($_SESSION["id_turno_sessao"]);
		
	$soma= 0;
	
	$ontem= soma_data($data, -1, 0, 0);
	$amanha= soma_data($data, 1, 0, 0);
	
	//$str= " and   op_limpa_pesagem.id_turno = '". $h ."' ";
	
	/*if ($data=="2010-02-11") echo "select sum(peso) as soma from op_limpa_pesagem, rh_turnos
								where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
								and   op_limpa_pesagem.id_turno = rh_turnos.id_turno
								and   rh_turnos.id_turno_index = '". $h ."'
								and   op_limpa_pesagem.extra = '0'
								and   op_limpa_pesagem.data_pesagem = '". $data ."'
								<br /><br />
								";
		*/
	
	$str_add="";
	
	if ($h==4) {
		$data_referencia= $ontem;
		$data_referencia_amanha= $data;
	}
	else {
		if (date("H")<4) {
			$data_referencia= $ontem;
			$data_referencia_amanha= $data;
		}
		else {
			$data_referencia= $data;
			$data_referencia_amanha= $amanha;
		}
	}
	
	//noite
	if ($h==3) {
		$str_add= " and  ( op_limpa_pesagem.data_pesagem = '". $data_referencia ."'
							or op_limpa_pesagem.data_pesagem = '". $data_referencia_amanha ."' )
					and   op_limpa_pesagem.data_hora_pesagem < '". $data_referencia_amanha ." 04:00:00'
					and   op_limpa_pesagem.data_hora_pesagem > '". $data_referencia ." 14:00:00'
					";
	}
	//madrugada
	elseif ($h==4) {
		//$str_add= " and   op_limpa_pesagem.data_pesagem = '". $amanha ."' ";
		
		$str_add= " and  ( op_limpa_pesagem.data_pesagem = '". $data_referencia ."'
							or op_limpa_pesagem.data_pesagem = '". $data_referencia_amanha ."' )
					and   op_limpa_pesagem.data_hora_pesagem > '". $data_referencia ." 20:00:00'
					and   op_limpa_pesagem.data_hora_pesagem < '". $data_referencia_amanha ." 10:00:00'
					";
		
	}
	//manhã e tarde
	else {
		$str_add= " and   op_limpa_pesagem.data_pesagem = '". $data ."' ";
	}
	
	$result_soma= mysql_query("select sum(peso) as soma from op_limpa_pesagem, rh_turnos
								where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
								and   op_limpa_pesagem.id_turno = rh_turnos.id_turno
								and   rh_turnos.id_turno_index = '". $h ."'
								and   op_limpa_pesagem.extra = '0'
								$str_add
								") or die(mysql_error());
	$rs_soma= mysql_fetch_object($result_soma);
	
	/*
	$result_soma= mysql_query("select avg(qtde_funcionarios) as media from op_limpa_pesagem
								where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
								and   data_pesagem = '". $data ."'
								". $str ."
								order by data_pesagem desc, hora_pesagem desc
								");
	$rs_soma= mysql_fetch_object($result_soma);
	*/
	
	$result_dia= mysql_query("select rh_turnos_horarios.* from rh_turnos, rh_turnos_horarios
								where rh_turnos.id_turno = rh_turnos_horarios.id_turno
								and   rh_turnos.id_turno_index = '". $h ."'
								and   rh_turnos_horarios.id_dia = '$id_dia'
								");
	$rs_dia= mysql_fetch_object($result_dia);
	
	$sql_pre="";
				
	$result_pre= mysql_query("select * from rh_ponto_producao
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   vale_dia = '". $data ."'
								and   id_turno_index = '". $h ."'
								and   id_departamento = '1'
								");
	$linhas_pre= mysql_num_rows($result_pre);
	
	//if (($_POST["denovo"]=="1") || ($linhas_pre==0)) {
		//$funcionarios_neste_turno_neste_dia= pega_funcionarios_trabalhando_retroativo($_SESSION["id_empresa"], 1, $h, $data_referencia, $data_referencia ." ". $rs_dia->entrada, $data_referencia ." ". $rs_dia->saida, 0, 6);
		$funcionarios_neste_turno_neste_dia= pega_funcionarios_trabalhando_manual($_SESSION["id_empresa"], 1, $h, $data_mesmo);
	//}
	
	/*
	if ($linhas_pre>0) {
		
		$rs_pre= mysql_fetch_object($result_pre);
		
		//if ($_POST["denovo"]=="1") {
			
			$sql_pre="update rh_ponto_producao
						set media = '". $funcionarios_neste_turno_neste_dia ."'
						where id_empresa = '". $_SESSION["id_empresa"] ."'
						and   vale_dia = '". $data_referencia ."'
						and   id_turno_index = '". $h ."'
						and   id_departamento = '1'
						";
		//}
		//else {
		//	$funcionarios_neste_turno_neste_dia= $rs_pre->media;
		//}
		
	}
	else {				
		$sql_pre="insert into rh_ponto_producao
				(id_empresa, id_departamento, vale_dia, id_turno_index, media, id_usuario)
				values
				('". $_SESSION["id_empresa"] ."', '1', '". $data_referencia ."', '". $h ."', 
				'". $funcionarios_neste_turno_neste_dia ."', '". $_SESSION["id_usuario"] ."')
				";
	}
	
	if ($sql_pre!="") {
		$result_fixa= mysql_query($sql_pre) or die(mysql_error());
	}
	*/
	
	$soma= $rs_soma->soma;
	
	$funcionarios_trabalhando= $funcionarios_neste_turno_neste_dia;
	
	if ($funcionarios_trabalhando>0) $media_funcionarios= ($soma/$funcionarios_trabalhando);
	else $media_funcionarios=0;
	
}//fim else

$data_turno= date("Y-m-d H:i:s");

$ano= substr($data_turno, 0, 4);
$mes= substr($data_turno, 5, 2);
$dia= substr($data_turno, 8, 2);
$hora= substr($data_turno, 11, 2);
$minuto= substr($data_turno, 14, 2);
$segundo= substr($data_turno, 17, 2);

$calculo_data_turno= mktime($hora-1, $minuto, $segundo, $mes, $dia, $ano);

?>

<table cellspacing="0">
	<tr>
    	<th align="left">Data</th>
    	<th align="left">Turno</th>
        <th>Peso turno</th>
        <th>Média de funcionários</th>
        <th>Média individual</th>
    </tr>
    <tr>
    	<td><?= date("d/m/Y", $calculo_data_turno); ?></td>
        <td><?= pega_turno(pega_turno_pelo_horario(date("Y-m-d H:i:s", $calculo_data_turno))); ?></td>
        <td align="center"><?= fnum($soma) ." kg"; ?></td>
        <td align="center"><?= fnum($funcionarios_trabalhando); ?></td>
        <td align="center"><?= fnum($media_funcionarios) ." kg/funcionário"; ?></td>
    </tr>
</table>

<br /><br /><br /><br /><br /><br /><br /><br /><br />

<center>
	<button id="sair" onclick="window.top.location.href='index2.php?pagina=logout';">sair do sistema</button>
</center>


<script language="javascript">
	daFoco("sair");
</script>
<?
}
?>