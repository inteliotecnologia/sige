<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	
	if ($_GET["id_intervalo"]!="") $id_intervalo= $_GET["id_intervalo"];
	else $id_intervalo= $_POST["id_intervalo"];
	
	$id_turno= pega_id_turno_do_id_intervalo($id_intervalo);
	$id_departamento= pega_id_departamento_do_id_turno($id_turno);
?>
    <div id="tela_mensagens2">
        <? include("__tratamento_msgs.php"); ?>
    </div>

    <div class="parte50">
        <fieldset>
            <legend>Dados do turno/intervalo</legend>
        	
            <label>Departamento:</label>
			<?= pega_departamento($id_departamento); ?>
            <br />
            
            <label>Turno:</label>
			<?= pega_turno($id_turno); ?>
            <br />
            
            <label>Intervalo:</label>
            <?= pega_intervalo($id_intervalo); ?>
            <br /><br />
            
        </fieldset>
    </div>
    <div class="parte50">
		<fieldset>
            <legend>Tabela de horários</legend>
            
            <form action="<?= AJAX_FORM; ?>formIntervaloHorario" method="post" name="formIntervaloHorario" id="formIntervaloHorario" onsubmit="return ajaxForm('conteudo_interno', 'formIntervaloHorario', 'validacoes', true);">
            
            	<input class="escondido" type="hidden" id="validacoes" value="intervalo_apos@vazio|intervalo_duracao@vazio" />
                <input type="hidden" class="escondido" name="id_intervalo" id="id_intervalo" value="<?=$id_intervalo;?>" />
                <input type="hidden" class="escondido" name="id_turno" id="id_turno" value="<?=$id_turno;?>" />
                
                <table cellspacing="0" width="100%">
                    <tr>
                      <th width="20%" align="left" valign="bottom">Dia</th>
                      <th width="10%" align="left" valign="bottom">Auto</th>
                      <th width="15%" align="left" valign="bottom">Entrada</th>
                      <th width="17%" align="left" valign="bottom">Ap&oacute;s</th>
                      <th width="15%" align="left" valign="bottom">Dura&ccedil;&atilde;o</th>
                      <th width="13%" align="left" valign="bottom">Saída</th>
                      <th width="10%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
                  </tr>
                    <?
                    $result= mysql_query("select * from rh_turnos, rh_turnos_horarios, rh_turnos_intervalos, rh_turnos_intervalos_horarios
                                            where rh_turnos.id_turno = rh_turnos_horarios.id_turno
                                            and   rh_turnos.id_turno = rh_turnos_intervalos.id_turno
                                            and   rh_turnos_intervalos.id_intervalo = rh_turnos_intervalos_horarios.id_intervalo
                                            and   rh_turnos_intervalos.id_intervalo = '". $id_intervalo ."'
                                            and   rh_turnos_horarios.id_dia = rh_turnos_intervalos_horarios.id_dia
											order by rh_turnos_intervalos_horarios.id_dia, rh_turnos_intervalos_horarios.automatico asc
                                            ");
                    while ($rs= mysql_fetch_object($result)) {
                    ?>
                    <tr>
                      <td><?= traduz_dia($rs->id_dia); ?></td>
                      <td><?= sim_nao($rs->automatico); ?></td>
                      <td><?= $rs->entrada; ?></td>
                      <td><?= $rs->intervalo_apos; ?></td>
                      <td><?= $rs->intervalo_duracao; ?></td>
                      <td><?= $rs->saida; ?></td>
                      <td>
                      <a href="javascript:ajaxLink('conteudo_interno', 'intervaloHorarioExcluir&amp;id_intervalo_horario=<?= $rs->id_intervalo_horario; ?>&amp;id_intervalo=<?= $rs->id_intervalo; ?>');" onclick="return confirm('Tem certeza que deseja excluir este horário de intervalo?');">
                        <img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a>
                      </td>
                    </tr>
                    <? } ?>
                    <tr>
                        <td>
                            <select name="id_dia" id="id_dia" onchange="pegaHorario('<?= $id_turno; ?>', 'entrada'); pegaHorario('<?= $id_turno; ?>', 'saida');">
                                <? for ($i=0; $i<=6; $i++) { ?>
                                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>"><?= traduz_dia($i); ?></option>
                                <? } ?>
                            </select>
                        </td>
                        <td>
                        	<input class="tamanho20" type="checkbox" name="automatico" value="1" />
                        </td>
                        <td><div id="horario_entrada_atualiza"></div></td>
                        <td><input title="Intervalo após ? horas" name="intervalo_apos" id="intervalo_apos" onkeyup="formataHora(this);" maxlength="8" /></td>
                        <td><input title="Duração do intervalo" name="intervalo_duracao" id="intervalo_duracao" onkeyup="formataHora(this);" maxlength="8" /></td>
                        <td><div id="horario_saida_atualiza"></div></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            	
            	<script language="javascript">
					pegaHorario('<?= $id_turno; ?>', 'entrada'); pegaHorario('<?= $id_turno; ?>', 'saida');
				</script>
                
                <br />
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
                
			</form>
        </fieldset>
    </div>
	
</form>
<? } ?>