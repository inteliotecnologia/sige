<?
require_once("conexao.php");
if (pode_algum("rvh4", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
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
	else {
		if ($_POST["periodo"]!="") {
			$periodo= explode('/', $_POST["periodo"]);
			
			$data1_mk= mktime(0, 0, 0, $periodo[0]-1, 26, $periodo[1]);
			$data2_mk= mktime(0, 0, 0, $periodo[0], 25, $periodo[1]);
			
			$data1= date("Y-m-d", $data1_mk);
			$data2= date("Y-m-d", $data2_mk);
			
			$data1f= desformata_data($data1);
			$data2f= desformata_data($data2);
		}
	}
	
	$result_pre= mysql_query("select * from rh_carreiras, rh_turnos
								where rh_carreiras.id_funcionario = '$id_funcionario'
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_carreiras.atual = '1'
								order by rh_carreiras.id_carreira asc limit 1
								");
	$rs_pre= mysql_fetch_object($result_pre);
	
	$id_departamento= $rs_pre->id_departamento;
	$id_turno= $rs_pre->id_turno;
	$id_intervalo= $rs_pre->id_intervalo;
	$id_regime= $rs_pre->id_regime;
?>

<div id="tela_banco_horas" class="telinha1 screen">
	teste
</div>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<fieldset>
    <legend>Banco de horas</legend>
    
    <? if (pode("rv", $_SESSION["permissao"])) { ?>
    <div class="parte50">
        <fieldset>
        	<legend>Dados do funcionário</legend>

            <label>Funcionário:</label>
            <? //pega_funcionario($id_funcionario); ?>
            <select name="id_funcionario" id="id_funcionario" title="Funcionário" onchange="alteraBancoHorasFuncionario(this.value, '<?= $data1f; ?>', '<?= $data2f; ?>');">
                <?
                $result_fun= mysql_query("select *
                                            from  pessoas, rh_funcionarios, rh_carreiras
                                            where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                            and   pessoas.tipo = 'f'
                                            and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                            and   rh_carreiras.atual = '1'
                                            and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            order by pessoas.nome_rz asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_fun= mysql_fetch_object($result_fun)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label>Departamento:</label>
            <?= pega_departamento($id_departamento); ?>
            <br />
            
            <label>Turno:</label>
            <?= pega_turno($id_turno); ?>
            <br />
    	</fieldset>
    </div>
    <div class="parte50">
    	<fieldset>
        	<legend>Inserção de dados</legend>
            
	        <? require_once("_rh/__banco_form.php"); ?>
            
        </fieldset>
    </div>
    <? } ?>
    
    <br />
    
    <fieldset>
        <legend>Todas as operações e saldo</legend>
        
        <table width="100%" cellspacing="0">
          <tr>
            <th align="left" width="13%">Referente à</th>
            <th width="24%">Horas</th>
            <th width="16%">Tipo</th>
            <th width="33%" align="left">Observa&ccedil;&atilde;o</th>
            <th width="11%">A&ccedil;&atilde;o</th>
          </tr>
          <?
		  $total_diruno=0;
		  $total_noturno=0;
		  
		  $d=0;
		  $result= mysql_query("select *, DATE_FORMAT(data_he, '%d/%m/%Y') as data_he2 from rh_ponto_banco
								where id_funcionario = '". $id_funcionario ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								order by data_he asc
								");
		  while ($rs= mysql_fetch_object($result)) {
		  	if (($d%2)==0) $classe= "cor_nao";
			else $classe= "cor_sim";
						
			if ($rs->operacao==0) $classe2="vermelho";
			else $classe2="azul";
								
		  	/*if ($rs->tipo_he==0) {
				$hed= $rs->he; $hen= 0;
				
				if ($rs->operacao==0) $total_diurno-=$hed;
				else $total_diurno+=$hed;
			}
			else {
				$hed= 0; $hen= $rs->he;
				
				if ($rs->operacao==0) $total_noturno-=$hen;
				else $total_noturno+=$hen;
			}*/
			
			$he= $rs->he;				
			if ($rs->operacao==0) $total-=$he;
			else $total+=$he;
			
			
		  ?>
          <tr class="<?= $classe ." ". $classe2; ?>">
          	<td><?= $rs->data_he2; ?></td>
            <td align="center"><?= calcula_total_horas($he); ?></td>
            <td align="center"><?= pega_operacao_debito($rs->operacao, $rs->operacao_debito); ?></td>
            <td><?= $rs->obs; ?></td>
            <td width="3%" align="center">
	            <a href="javascript:ajaxLink('conteudo_interno', 'bancoHorasExcluir&amp;id_banco=<?= $rs->id_banco; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
                    <img border="0" src="images/ico_lixeira.png" alt="Status" />
                </a>
            </td>
          </tr>
          <? $d++; } ?>
          <tr>
          	<td align="right"><strong>Saldo atual:</strong></td>
            <td align="center"><?= calcula_total_horas($total); ?></td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
          </tr>
        </table>
    </fieldset>

        
</fieldset>
<? } ?>