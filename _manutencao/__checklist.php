<?
require_once("conexao.php");
if (pode_algum("j", $_SESSION["permissao"])) {
	
	if ($_POST["id_tecnico"]!="") $id_tecnico= $_POST["id_tecnico"];
	if ($_GET["id_tecnico"]!="") $id_tecnico= $_GET["id_tecnico"];
	
	if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
		$data1= formata_data_hifen($_POST["data1"]); $data1f= $_POST["data1"];
		$data2= formata_data_hifen($_POST["data2"]); $data2f= $_POST["data2"];
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	else {
		$periodo= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(14, 0, 0, $periodo[0], 1, $periodo[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo[0], $dias_mes, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
?>

    <div id="tela_mensagens2">
        <? include("__tratamento_msgs.php"); ?>
    </div>
    
    <strong>Técnico:</strong> <?= pega_manutencao_tecnico($id_tecnico); ?><br /><br />
    
    <form action="<?= AJAX_FORM; ?>formManutencaoChecklist" method="post" name="formManutencaoChecklist" id="formManutencaoChecklist" onsubmit="return ajaxForm('conteudo_interno', 'formManutencaoChecklist', 'validacoes', true);">
    	
        <input class="escondido" type="hidden" id="validacoes" />
        
        <input class="escondido" type="hidden" id="data1" name="data1" value="<?= $data1f; ?>" />
        <input class="escondido" type="hidden" id="data2" name="data2" value="<?= $data2f; ?>" />
        
        <input class="escondido" type="hidden" id="id_tecnico" name="id_tecnico" value="<?= $id_tecnico; ?>" />
        
        <?
		$i=0;
		
		$z=1;
		$result= mysql_query("select * from op_equipamentos
				where id_empresa = '". $_SESSION["id_empresa"] ."' 
				". $str ."
				order by tipo_equipamento asc, equipamento asc
				");
				
			
		
        $i=0;
        while ($rs= mysql_fetch_object($result)) {
            
			if (($_POST["id_categoria"]=="") || (($_POST["id_categoria"]!="") && ($_POST["id_categoria"]==$z)) ) {
		?>
        <fieldset class="fescala">
            <legend><?= pega_equipamento($rs->id_equipamento); ?></legend>
                
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="12%" align="left" class="normal">&nbsp;</th>
                    <?
                    $diferenca_tit= date("d", $data2_mk-$data1_mk);
                    
                    //repetir todos os dias do intervalo
                    for ($t=0; $t<$diferenca_tit; $t++) {
                      $calculo_data_tit= $data1_mk+(86400*$t);
                      
                      $dia_tit= date("d", $calculo_data_tit);
                      $dia_semana_tit= date("w", $calculo_data_tit);
                      $vale_dia_tit= date("Y-m-d", $calculo_data_tit);
                      
                    ?>
                    <th align="left">
                        <?= $dia_tit; ?> <br />
                        <?= traduz_dia_resumido($dia_semana_tit); ?>
                    </th>
                    <? } ?>
                    <th class="normal">
                    	Per.
                    </th>
                </tr>
                <?
				$result_manutencao_itens= mysql_query("select * from op_acompanhamento_itens
													  	where id_empresa = '". $_SESSION["id_empresa"] ."'
														");
				
				while ($rs_manutencao_itens= mysql_fetch_object($result_manutencao_itens)) {
                ?>
                <tr id="periodo_<?= $z; ?>_<?= $p; ?>">
                    <td align="left" class="td_dia_semana normal">
                        <? /*<a href="javascript:void(0);" onclick="checarTudo('periodo_<?= $z; ?>_<?= $p; ?>');"><?= pega_periodo_turno($p); ?></a> */ ?>
                        
                        <?= $rs_manutencao_itens->acompanhamento_item; ?>
                        
                    </td>
                    <?
                    $diferenca= date("d", $data2_mk-$data1_mk);
                    
                    //repetir todos os dias do intervalo
                    for ($d=0; $d<$diferenca; $d++) {
                        $calculo_data= $data1_mk+(86400*$d);
                        $dia_semana= date("w", $calculo_data);
                        $vale_dia= date("Y-m-d", $calculo_data);
						$vale_dia_mk= faz_mk_data($vale_dia);
						
						$vale_dia_anterior= soma_data($vale_dia, 1, 0, 0);
						$vale_dia_anterior_mk= faz_mk_data($vale_dia_anterior);
						
						$limite_dia= date("Y-m-d");
						$limite_dia_mk= faz_mk_data($limite_dia);
						
						//$limite_hora= mktime(6, 10, 0, date("m"), date("d")-1, date("Y")));
						
						//limite = 21-07-2009 06:10:00
						//aqui   = 21-07-2009 15:36:00
						
                        $mes= date("m", $calculo_data);
                        
						$result_ac_outro= mysql_query("select * from man_checklist
														where id_empresa = '". $_SESSION["id_empresa"] ."'
														and   id_tecnico <> '". $id_tecnico ."'
														and   data_checklist = '". $vale_dia ."'
														and   id_checklist_item = '". $rs_manutencao_itens->id_acompanhamento_item ."'
														") or die(mysql_error());
                        $quem_fez= "";
						
						while ($rs_ac_outro= mysql_fetch_object($result_ac_outro))
							$quem_fez.= pega_funcionario($rs_ac_outro->id_funcionario) ."<br />";
							
						$linhas_ac_outro= mysql_num_rows($result_ac_outro);
						
                        $result_ac= mysql_query("select * from man_checklist
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_tecnico = '". $id_tecnico ."'
                                                    and   data_checklist = '". $vale_dia ."'
													and   id_checklist_item = '". $rs_manutencao_itens->id_acompanhamento_item ."'
                                                    ") or die(mysql_error());
                        $rs_ac= mysql_fetch_object($result_ac);
                    ?>
                    <td align="center" <? if ($linhas_ac_outro>0) { ?> class="cor_cont" onmouseover="Tip('<?= $quem_fez; ?>');" <? } ?>>
                        <input class="escondido" type="hidden" name="id_checklist_item[<?=$i;?>]" value="<?= $rs_manutencao_itens->id_acompanhamento_item; ?>" />
                        <input class="escondido" type="hidden" name="data_checklist[<?=$i;?>]" value="<?= $vale_dia; ?>" />
                        
                        <? //if (($vale_dia_mk==$limite_dia_mk) || (($vale_dia_anterior_mk==$limite_dia_mk) && (date("H")<7))) { ?>
                        <input class="tamanho20" type="checkbox" name="valor[<?=$i;?>]" value="1" <? if ($rs_ac->valor==1) echo "checked=\"checked\""; ?> />
                        <? /*} else { ?>
                        <input class="tamanho20" type="checkbox" name="valor_falso[<?=$i;?>]" value="1" disabled="disabled" <? if ($rs_ac->valor==1) echo "checked=\"checked\""; ?> />
                        <input type="hidden" class="escondido" name="valor[<?=$i;?>]" value="<?= $rs_ac->valor; ?>" />
						<? } */ ?>
                    </td>
                    <? $i++; } ?>
                    <td>
                    	<span class="menor"><? //traduz_periodicidade($rs_manutencao_itens->periodicidade); ?></span>
                    </td>
                </tr>
                <? } //fim while itens ?>
            </table>
            
        </fieldset>
        <?
			}
			$z++;
		}
		?>
	
        <br /><br />
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    
	</form>
<? } ?>