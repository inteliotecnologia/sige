<?
require_once("conexao.php");
if (pode("p", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	if ($_GET["id_departamento"]!="") $id_departamento= $_GET["id_departamento"];
	if ($_POST["id_departamento"]!="") $id_departamento= $_POST["id_departamento"];
	
	if ( ($_POST["data1"]!="") ) {
		$data1= $_POST["data1"];
	}
	else {
		if ( ($_GET["data1"]!="") ) {
			$data1= $_GET["data1"];
		}
	}
	
	$data2= $data1;
	
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
    
    <br />
    
    <form action="<?= AJAX_FORM; ?>formProducaoAjusteFuncionarios" method="post" name="formProducaoAjuste" id="formProducaoAjuste">
        
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
			$data_min= date("Ymd", $calculo_data);
			
			$total_dia=0;
			$media_dia=0;
			$media_turno_aqui=0;
			
		?>
        <fieldset>
        	<legend><?= $data_formatada ." - ". traduz_dia($id_dia); ?></legend>
            
            <? if ($id_departamento=="1") { ?>
            
				<?
                if (($id_dia!=0) && ($id_dia!=6)) {
                        
                    for ($t=1; $t<5; $t++) {
						
						$result_desc= mysql_query("select * from rh_ponto_producao_desconsiderados
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_departamento = '". $id_departamento ."'
													and   vale_dia = '". $data_mesmo ."'
													and   desconsiderado = '1'
													and   id_turno_index = '". $t ."'
													");
						$linhas_desc= mysql_fetch_object($result_desc);
						
                ?>
                        <h3><?= pega_turno_padrao($t); ?>:</h3>
                        
                        <input <? if ($linhas_desc>0) { ?>checked="checked"<? } ?> type="checkbox" name="desconsiderar" id="desconsiderar_<?=$i;?>_<?=$t;?>" onclick="desconsideraProducaoDia(this, '<?=$i;?>', '<?=$t;?>', '<?= $data_mesmo; ?>', '<?= $_POST["id_departamento"]; ?>');" class="tamanho30" />
                        <label class="tamanho80 nao_negrito" for="desconsiderar_<?=$i;?>_<?=$t;?>">Desconsiderar</label>
                        <br /><br />
                        
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                     <? for ($f=6; $f>0; $f--) { ?>
                                     <td align="left"><strong><?=$f;?> hora(s)</strong></td>
                                     <? } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?
                                    for ($f=6; $f>0; $f--) {
                                    
                                        $result_des= mysql_query("select * from op_limpa_producao_funcionarios
                                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                                    and   id_departamento = '". $id_departamento ."'
                                                                    and   data = '". $data_mesmo ."'
                                                                    and   turno = '". $t ."'
                                                                    and   qtde_horas = '". $f ."'
                                                                    ");
                                        $rs_des= mysql_fetch_object($result_des);
                                    
                                    ?>
                                    <td>
                                        <input class="escondido" type="hidden" name="indexador[]" value="1" />
                                        <input class="escondido" type="hidden" name="data[]" value="<?= $data_mesmo; ?>" />
                                        <input class="escondido" type="hidden" name="turno[]" value="<?= $t; ?>" />
                                        <input class="escondido" type="hidden" name="qtde_horas[]" value="<?= $f; ?>" />
                                        
                                        <input class="tamanho100" type="text" name="qtde_funcionarios[]" id="qtde_funcionarios_<?=$data_min;?>_<?=$t;?>_<?=$f;?>" value="<?=$rs_des->qtde_funcionarios;?>" />
                                    </td>
                                    <? } ?>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div id="div_desconsiderar_<?=$i;?>_<?=$t;?>">
                        </div>
                        
                        <br /><br />
					<? } ?>
					<br /><br />
			<?
            }
			else {
				$result_desc= mysql_query("select * from rh_ponto_producao_desconsiderados
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_departamento = '". $id_departamento ."'
												and   vale_dia = '". $data_mesmo ."'
												and   desconsiderado = '1'
												and   id_turno_index = '0'
												");
					$linhas_desc= mysql_fetch_object($result_desc);
			?>
            
            <h3>Plantão <?= traduz_dia($id_dia); ?>:</h3>
                        
            <input <? if ($linhas_desc>0) { ?>checked="checked"<? } ?> type="checkbox" name="desconsiderar" id="desconsiderar_<?=$i;?>_<?=$t;?>" onclick="desconsideraProducaoDia(this, '<?=$i;?>', '0', '<?= $data_mesmo; ?>', '<?= $_POST["id_departamento"]; ?>');" class="tamanho30" />
            <label class="tamanho80 nao_negrito" for="desconsiderar_<?=$i;?>_<?=$t;?>">Desconsiderar
            <br /><br />
                    
            <div id="div_desconsiderar_<?=$i;?>_0">
            </div>
                        
            <table width="100%" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                         <? for ($f=12; $f>0; $f--) { ?>
                         <td align="left"><strong><?=$f;?> hora(s)</strong></td>
                         <? } ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?
                        for ($f=12; $f>0; $f--) {
                        
                            $result_des= mysql_query("select * from op_limpa_producao_funcionarios
                                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                        and   id_departamento = '". $id_departamento ."'
                                                        and   data = '". $data_mesmo ."'
                                                        and   turno = '0'
                                                        and   qtde_horas = '". $f ."'
                                                        ");
                            $rs_des= mysql_fetch_object($result_des);
                        
                        ?>
                        <td>
                            <input class="escondido" type="hidden" name="indexador[]" value="1" />
                            <input class="escondido" type="hidden" name="data[]" value="<?= $data_mesmo; ?>" />
                            <input class="escondido" type="hidden" name="turno[]" value="0" />
                            <input class="escondido" type="hidden" name="qtde_horas[]" value="<?= $f; ?>" />
                            
                            <input class="tamanho60" type="text" name="qtde_funcionarios[]" id="qtde_funcionarios_<?=$data_min;?>_<?=$t;?>_<?=$f;?>" value="<?=$rs_des->qtde_funcionarios;?>" />
                        </td>
                        <? } ?>
                    </tr>
                </tbody>
            </table>
            
            <br /><br />
            
            <? } ?>
                    
			<? } ?>
            
            
            
        </fieldset>
		<? } ?>
    <br /><br />
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
	</form>
<? } ?>