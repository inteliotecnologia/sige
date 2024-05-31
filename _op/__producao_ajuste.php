<?
require_once("conexao.php");
if (pode("[", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	if ($_GET["id_departamento"]!="") $id_departamento= $_GET["id_departamento"];
	if ($_POST["id_departamento"]!="") $id_departamento= $_POST["id_departamento"];
	
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
		
		$data1= soma_data($data1, -1, 0, 0);
		$data2= soma_data($data2, -1, 0, 0);
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
		
		//$primeiro_dia_mes_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
		$primeiro_dia_mes_mk= $data1_mk;
		
		$id_dia_primeiro_dia= date("w", $primeiro_dia_mes_mk);
		
		$i_inicio= 1;
		
		// 10/10/2010
		
		$primeiro_dia_periodo_mk= $primeiro_dia_mes_mk;
		
		//echo substr($data1f, 6, 4);//date("d/m/Y", $primeiro_dia_periodo_mk); die();
		
		$ultimo_dia_periodo_mk= $data2_mk+(86400*2);
	}
	else {
		$periodo= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(20, 0, 0, $periodo[0], 1, $periodo[1]);
		
		$total_dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo[0], $total_dias_mes, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		//$primeiro_dia_mes_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
		$primeiro_dia_mes_mk= $data1_mk;
		
		$id_dia_primeiro_dia= date("w", $primeiro_dia_mes_mk);
		
		$i_inicio= 1;
		
		//echo $id_dia_primeiro_dia; die();
		
		switch ($id_dia_primeiro_dia) {
			case 1: $i_inicio=0; break;
			case 2: $i_inicio=-1; break;
			case 3: $i_inicio=-2; break;
			case 4: $i_inicio=-3; break;
			case 5: $i_inicio=-4; break;
			case 6: $i_inicio=-5; break;
			case 0: $i_inicio=-6; break;
		}
		
		// 10/10/2010
		
		$primeiro_dia_periodo_mk= mktime(0, 0, 0, substr($data1f, 3, 2), $i_inicio, substr($data1f, 6, 4));
		
		//echo substr($data1f, 6, 4);//date("d/m/Y", $primeiro_dia_periodo_mk); die();
		
		$ultimo_dia_periodo_mk= mktime(0, 0, 0, substr($data2f, 3, 2), substr($data2f, 0, 2), substr($data2f, 6, 4));
	}
	
	$total_dias_considerados=0;
	$total_dias_considerados_semana=0;
	
	$vale_dia_inicio= date("Y-m-d", $primeiro_dia_periodo_mk);
	$vale_dia_fim= date("Y-m-d", $ultimo_dia_periodo_mk);
	
	//echo "inicio: ". $vale_dia_inicio ." fim: ". $data2f;
?>

    <div id="tela_mensagens2">
        <? include("__tratamento_msgs.php"); ?>
    </div>
    
    <h2>Ajuste de produção - <?= pega_departamento($id_departamento); ?></h2>
    
    <p>Registros entre <strong><?= $data1f; ?></strong> e <strong><?= $data2f; ?></strong>.</p>
    
    <ul class="recuo1">
        <li><a href="javascript:void(0);" onclick="checarTudo('tudo');">checar/deschecar tudo</a></li>
    </ul>
    
    <form action="<?= AJAX_FORM; ?>formProducaoAjuste" method="post" name="formProducaoAjuste" id="formProducaoAjuste">
        
        <input class="escondido" type="hidden" id="data1" name="data1" value="<?= $data1f; ?>" />
        <input class="escondido" type="hidden" id="data2" name="data2" value="<?= $data2f; ?>" />
        <input class="escondido" type="hidden" name="id_departamento" value="<?= $id_departamento; ?>" />
        
        <?
		$k=0;
	
		$diferenca = ceil(($ultimo_dia_periodo_mk-$primeiro_dia_periodo_mk)/86400);
		
		for ($i=$i_inicio; $i<$diferenca; $i++) {
			//if ($i>0) $k++;
		
			$calculo_data= $data1_mk+(86400*$i);
			
			$amanha_mk= mktime(0, 0, 0, $periodo[0], $i+1, $periodo[1]);
			
			$id_dia= date("w", $calculo_data);
			
			if (($i%2)==0) $fill=0;
			else $fill= 1;
			
			$data_formatada= date("d/m/Y", $calculo_data);
			$data= date("Y-m-d", $calculo_data);
			$amanha= soma_data($data, 1, 0, 0);
			
			//if (($data=="2010-09-20") || ($data=="2010-09-21")) {
			
			$data_mesmo= date("Y-m-d", $calculo_data);
			
			$total_dia=0;
			$media_dia=0;
			$media_turno_aqui=0;
			
		?>
        <fieldset>
        	<legend><?= $data_formatada; ?></legend>
            
            <?
			if ($id_departamento=="1") {
				if (($id_dia!=0) && ($id_dia!=6)) {
					
					for ($t=1; $t<5; $t++) {
						$result_des= mysql_query("select count(*) as linhas from rh_ponto_producao_desconsiderados
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_departamento = '". $id_departamento ."'
													and   id_turno_index = '". $t ."'
													and   vale_dia = '". $data_mesmo ."'
													and   desconsiderado = '1'
													");
						$rs_des= mysql_fetch_object($result_des);
				?>
					
						<label class="tamanho200" for="desconsiderar_<?=$i;?>_<?=$t;?>">Desconsiderar - <?= pega_turno_padrao($t); ?>:</label>
						<input <? if ($rs_des->linhas>0) { ?>checked="checked"<? } ?> type="checkbox" name="desconsiderar" id="desconsiderar_<?=$i;?>_<?=$t;?>" onclick="desconsideraProducaoDia(this, '<?=$i;?>', '<?=$t;?>', '<?= $data_mesmo; ?>', '<?= $id_departamento; ?>');" class="tamanho30" />
						<div id="div_desconsiderar_<?=$i;?>_<?=$t;?>">
						</div>
						
						<br />
					<? } ?>
					<br /><br />
            <? } } ?>
            
            <?
			$result_fun_teste= mysql_query("select count(id_funcionario) as total from rh_ponto_producao_funcionarios
											where vale_dia = '". $data ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_departamento = '". $id_departamento ."'
											");
			$rs_fun_teste= mysql_fetch_object($result_fun_teste);
			
			if ($rs_fun_teste->total>0) {
			?>
            
            <table width="100%" cellspacing="0" class="contraste">
                <tr>
                    <th width="14%" align="left">Funcionário</th>
                    <?
                    for ($hi=6; $hi<30; $hi++) {
						if ($hi<24) $h=$hi;
						else $h= $hi-24;
                    ?>
                    <th align="left">
                        <?= $h; ?>h
                    </th>
                    <? } ?>
                </tr>
                <?
				$result_fun= mysql_query("select distinct(id_funcionario) as id_funcionario from rh_ponto_producao_funcionarios
											where vale_dia = '". $data ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_departamento = '". $id_departamento ."'
											");
				$t=0;
				while ($rs_fun= mysql_fetch_object($result_fun)) {
				?>
                <tr id="funcionario_<?= $rs_fun->id_funcionario; ?>">
                    <td align="left" class="menor">
                        <strong><?= pega_funcionario($rs_fun->id_funcionario); ?></strong>
                    </td>
                    <?
                    for ($hi=6; $hi<30; $hi++) {
						
						if ($hi<24) $h=$hi;
						else $h= $hi-24;
						
						if (($id_dia==0) || ($id_dia==6)) $id_turno_index=0;
						else {
							switch ($h) {
								case 0:
								case 1:
								case 2:
								case 3:
								case 4:
								case 5:
								$id_turno_index=4;
								break;
								case 6:
								case 7:
								case 8:
								case 9:
								case 10:
								case 11:
								$id_turno_index=1;
								break;
								case 12:
								case 13:
								case 14:
								case 15:
								case 16:
								case 17:
								$id_turno_index=2;
								break;
								case 18:
								case 19:
								case 20:
								case 21:
								case 22:
								case 23:
								$id_turno_index=3;
								break;	
							}
						}
						
						$result_fun_check= mysql_query("select * from rh_ponto_producao_funcionarios
														where vale_dia = '". $data ."'
														and   id_empresa = '". $_SESSION["id_empresa"] ."'
														and   id_funcionario = '". $rs_fun->id_funcionario ."'
														and   hora_base = '". $h ."'
														and   id_departamento = '". $id_departamento ."'
														");
						$rs_fun_check= mysql_fetch_object($result_fun_check);
                    ?>
                    <td <? if (($h%2)==0) { ?> class="cor_sim" <? } ?> align="center">
                        <input class="escondido" type="hidden" name="id_funcionario[<?=$k;?>]" value="<?= $rs_fun->id_funcionario; ?>" />
                        <input class="escondido" type="hidden" name="vale_dia[<?=$k;?>]" value="<?= $data; ?>" />
                        <input class="escondido" type="hidden" name="h[<?=$k;?>]" value="<?= $h; ?>" />
                        <input class="escondido" type="hidden" name="id_turno_index[<?=$k;?>]" value="<?= $id_turno_index; ?>" />
                        
                        <input class="tamanho20" type="checkbox" name="trabalhou[<?=$k;?>]" value="1" <? if ($rs_fun_check->trabalhou==1) echo "checked=\"checked\""; ?> />
                    </td>
                    <? $k++; } ?>
                </tr>
                <?
	                $t++;
				
					if (($t%10)==0) {
						?>
                        <tr>
                            <th width="14%" align="left">Funcionário</th>
                            <?
                            for ($hi=6; $hi<30; $hi++) {
								if ($hi<24) $h=$hi;
								else $h= $hi-24;
                            ?>
                            <th <? if (($h%2)==0) { ?> class="cor_sim" <? } ?> align="left">
                                <?= $h; ?>h
                            </th>
                            <? } ?>
                        </tr>
                        <?
					}
				}
				?>
            </table>
        	<? } else echo "<p>Média ainda não gerada para este dia.</p>"; ?>
            
        </fieldset>
		<? } ?>
    <br /><br />
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
	</form>
<? } ?>