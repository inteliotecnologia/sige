<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		if ($_SESSION["id_empresa"]!="")
			$str= "and   rh_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."' ";
	
		$result= mysql_query("select * from rh_turnos, rh_departamentos
								where rh_turnos.id_departamento = rh_departamentos.id_departamento
								and   rh_turnos.id_turno = '". $_GET["id_turno"] ."'
								". $str ."
								order by rh_departamentos.departamento asc,
										 rh_turnos.turno asc
								");
							
		$rs= mysql_fetch_object($result);
	}
?>
<? if ($acao=='i') { ?>
<h2>Cadastro de turno</h2>
<? } ?>

<form action="<?= AJAX_FORM; ?>formTurno&amp;acao=<?= $acao; ?>" method="post" name="formTurno" id="formTurno" onsubmit="return ajaxForm('conteudo', 'formTurno', 'validacoes', true);">

    <input class="escondido" type="hidden" id="validacoes" value="id_departamento@vazio|turno@vazio|id_regime@vazio|dias_trabalhados_semana@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_turno" class="escondido" type="hidden" id="id_turno" value="<?= $rs->id_turno; ?>" />
    <? } ?>

    <div class="parte50">
        <fieldset>
            <legend>Dados do turno</legend>
        
            <label for="id_departamento">* Departamento:</label>
            <div id="id_departamento_atualiza">
                <select name="id_departamento" id="id_departamento" title="Departamento">
                    <option value="">- DEPARTAMENTO -</option>
                    <?
                    if ($acao=='i')
                        $id_empresa= $_SESSION["id_empresa"];
                    else
                        $id_empresa= $rs->id_empresa;
                    
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $id_empresa ."'
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <br />
            
            <label for="turno">* Turno:</label>
            <input title="Turno" name="turno" value="<?= $rs->turno; ?>" id="turno" />
            <br />
            
            <label for="id_regime">* Regime:</label>
            <select name="id_regime" id="id_regime" title="Regime">
                <option value="">- SELECIONE -</option>
                <?
                $vetor= pega_regime_turno('l');
                $i=1;
                
                while ($vetor[$i]) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $i; ?>" <? if ($i==$rs->id_regime) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="dias_trabalhados_semana">* Dias/semana:</label>
            <input title="Dias/semana" name="dias_trabalhados_semana" value="<?= $rs->dias_trabalhados_semana; ?>" id="dias_trabalhados_semana" />
            <br />
            
        </fieldset>
    </div>
    <div class="parte50">
		<fieldset>
            <legend>Tabela de horários</legend>
            
            <table cellspacing="0" width="100%">
                <tr>
                  <th width="21%" align="left" valign="bottom">Dia</th>
                  <th width="23%" align="left" valign="bottom">Entrada</th>
                  <th width="21%" align="left" valign="bottom">Intervalo(s)</th>
                  <th width="24%" align="left" valign="bottom">Saída</th>
                  <th width="11%" align="left" valign="bottom">HL</th>
              </tr>
                <?
                for ($i=0; $i<=6; $i++) {
                    if ($acao=='e') {
                        $result_dia= mysql_query("select * from rh_turnos_horarios
                                                    where id_turno = '". $rs->id_turno ."'
                                                    and   id_dia = '$i'
                                                    ");
                        $rs_dia= mysql_fetch_object($result_dia);
                    }
                ?>
                <tr>
                    <td valign="top"><?= traduz_dia($i); ?></td>
                    <td valign="top">
                    <input type="hidden" class="escondido" name="id_dia[<?=$i;?>]" id="id_dia_<?=$i;?>" value="<?=$i;?>" />
                        <input name="entrada[<?=$i;?>]" id="entrada_<?=$i;?>" value="<?=$rs_dia->entrada;?>" onkeyup="formataHora(this);" maxlength="8" <? /* if ($acao=='e') { ?> onblur="atualizaHorarioTurno(this.value, 'entrada', '<?= $rs_dia->id_turno_horario; ?>');" <?  } */ ?> />                    </td>
                  <td>
                    	<?
						if ($acao=='e') {
							$result_int= mysql_query("select rh_turnos_intervalos.id_intervalo, rh_turnos_intervalos.intervalo, rh_turnos_intervalos_horarios.automatico
														from rh_turnos_intervalos, rh_turnos_intervalos_horarios
														where rh_turnos_intervalos.id_turno = '". $rs->id_turno ."'
														and   rh_turnos_intervalos.id_intervalo = rh_turnos_intervalos_horarios.id_intervalo
														and   rh_turnos_intervalos_horarios.id_dia = '$i'
														");
							
							if (mysql_num_rows($result_int)==0)
								echo "Nenhum";
							else {
								while ($rs_int= mysql_fetch_object($result_int)) {
									if ($rs_int->automatico==1)
										echo "<strong class=\"verde\">DESCANÇO</strong><br />";
									else
										echo "<strong>". $rs_int->intervalo ."</strong><br />";
									echo pega_detalhes_intervalo($rs_int->id_intervalo, $i, $rs_int->automatico);
									//echo 
									//pega_intervalos_do_dia($rs->id_turno, $rs_dia->id_dia);
								}
							}
						}
						else
							echo "Cadastrar depois";
						?></td>
                    <td valign="top">
                        <input name="saida[<?=$i;?>]" id="saida_<?=$i;?>" value="<?=$rs_dia->saida;?>" onkeyup="formataHora(this);" maxlength="8"  <? /*if ($acao=='e') { ?> onblur="atualizaHorarioTurno(this.value, 'saida', '<?= $rs_dia->id_turno_horario; ?>');" <? } */ ?> />                    </td>
                    <td valign="top">
                    	<input type="checkbox" <? if($rs_dia->hl==1) echo "checked=\"checked\""; ?> class="tamanho30" name="hl[<?=$i;?>]" id="hl_<?=$i;?>" value="1" />
                    </td>
              </tr>
                <? } ?>
            </table>
      </fieldset>
    </div>
	
    <br /><br />
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>

</form>
<? } ?>