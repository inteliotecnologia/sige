<?
require_once("conexao.php");
if (pode_algum("rhv4&", $_SESSION["permissao"])) {
	
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
    
    <? /*
    <p>Registros entre <strong><?= $data1f; ?></strong> e <strong><?= $data2f; ?></strong>.</p>
    <br />*/ ?>
    
    <? /*
    <ul class="recuo1">
        <li><a href="javascript:void(0);" onclick="checarTudo('tudo');">checar/deschecar tudo</a></li>
    </ul>
    */ ?>
    
    <form action="<?= AJAX_FORM; ?>formAcompanhamento" method="post" name="formAcompanhamento" id="formAcompanhamento" onsubmit="return ajaxForm('conteudo_interno', 'formAcompanhamento', 'validacoes', true);">
    	
        <input class="escondido" type="hidden" id="validacoes" />
        
        <input class="escondido" type="hidden" id="data1" name="data1" value="<?= $data1f; ?>" />
        <input class="escondido" type="hidden" id="data2" name="data2" value="<?= $data2f; ?>" />
        
        <?
		$i=0;
		
		$z=1;
		
		$result_acompanhamento_item= mysql_query("select distinct(op_acompanhamento_itens.id_acompanhamento_item) as id_acompanhamento_item, op_acompanhamento_itens.acompanhamento_item
													from op_acompanhamento_itens, rh_acompanhamento_atividades
													where rh_acompanhamento_atividades.id_acompanhamento = op_acompanhamento_itens.id_acompanhamento_item
													order by op_acompanhamento_itens.id_acompanhamento_item asc
													") or die(mysql_error());
		//$vetor= pega_acompanhamento_atividades('l');
		
		//while ($vetor[$z]) {
		while ($rs_acompanhamento_item= mysql_fetch_object($result_acompanhamento_item)) {
		?>
        <fieldset class="fescala">
            <legend><?= $rs_acompanhamento_item->acompanhamento_item; ?></legend>
                
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="18%" align="left" class="normal">Turno</th>
                    <?
                    $diferenca_tit= date("d", $data2_mk-$data1_mk);
                    
                    //repetir todos os dias do intervalo
                    for ($t=0; $t<=$diferenca_tit; $t++) {
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
                </tr>
                <?
                for ($p=1; $p<5; $p++) {
                ?>
                <tr id="periodo_<?= $z; ?>_<?= $p; ?>">
                    <th align="left" class="td_dia_semana normal">
                        <? /*<a href="javascript:void(0);" onclick="checarTudo('periodo_<?= $z; ?>_<?= $p; ?>');"><?= pega_periodo_turno($p); ?></a> */ ?>
                        
                        <?= pega_periodo_turno($p); ?>
                        
                    </th>
                    <?
                    $diferenca= date("d", $data2_mk-$data1_mk);
                    
                    //repetir todos os dias do intervalo
                    for ($d=0; $d<=$diferenca; $d++) {
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
                        
                        $result_ac= mysql_query("select * from rh_acompanhamento_atividades
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_acompanhamento = '". $rs_acompanhamento_item->id_acompanhamento_item ."'
                                                    and   data_acompanhamento = '". $vale_dia ."'
													and   periodo = '$p'
                                                    ") or die(mysql_error());
                        $rs_ac= mysql_fetch_object($result_ac);
                    ?>
                    <td align="center">
                        <input class="escondido" type="hidden" name="id_acompanhamento[<?=$i;?>]" value="<?= $rs_acompanhamento_item->id_acompanhamento_item; ?>" />
                        <input class="escondido" type="hidden" name="data_acompanhamento[<?=$i;?>]" value="<?= $vale_dia; ?>" />
                        <input class="escondido" type="hidden" name="periodo[<?=$i;?>]" value="<?= $p; ?>" />
                        
                        <? if (($vale_dia_mk==$limite_dia_mk) || (($vale_dia_anterior_mk==$limite_dia_mk) && (date("H")<7))) { ?>
                        <input class="tamanho20" type="checkbox" name="valor[<?=$i;?>]" value="1" <? if ($rs_ac->valor==1) echo "checked=\"checked\""; ?> />
                        <? } else { ?>
                        <input class="tamanho20" type="checkbox" name="valor_falso[<?=$i;?>]" value="1" disabled="disabled" <? if ($rs_ac->valor==1) echo "checked=\"checked\""; ?> />
                        <input type="hidden" class="escondido" name="valor[<?=$i;?>]" value="<?= $rs_ac->valor; ?>" />
						<? } ?>
                    </td>
                    <? $i++; } ?>
                </tr>
                <? } //fim for ?>
            </table>
            
        </fieldset>
        <? $z++; } ?>
	
        <br /><br />
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    
	</form>
<? } ?>