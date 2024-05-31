<?
require_once("conexao.php");
if (pode_algum("u", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Busca de refeição</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca</legend>
        
        <form action="<?= AJAX_FORM; ?>formRefeicaoBuscar" method="post" name="formRefeicaoBuscar" id="formRefeicaoBuscar" onsubmit="return ajaxForm('conteudo', 'formRefeicaoBuscar', 'validacoes');">

            <div class="parte50">
                
                <input class="escondido" type="hidden" id="validacoes" value="data@data" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <? if ($_GET["geral"]=="") { ?>
                <label for="id_refeicao">Requisição:</label>
                <input name="id_refeicao" id="id_refeicao" class="tamanho25p espaco_dir" value="" title="Requisição" />
                <br /><br />
                <? } ?>
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
					<?
					$i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data, '%m/%Y')) as data2
												from fi_refeicoes order by data desc ");
					
                    while ($rs_per= mysql_fetch_object($result_per)) {
						$data= explode('/', $rs_per->data2);
						if ((date("d")>=25) && ($i==0)) {
							$proximo_mes= date("m/Y", mktime(0, 0, 0, $data[0]+1, 1, $data[1]));
							$data2= explode('/', $proximo_mes);
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $proximo_mes; ?>"><?= traduz_mes($data2[0]) .'/'. $data2[1]; ?></option>
                    <? } ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data2; ?>"><?= traduz_mes($data[0]) .'/'. $data[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br />
                
                <? /*
                <label for="id_funcionario">Funcionário:</label>
                <select name="id_funcionario" id="id_funcionario" title="Motorista">
                    <option value="">- TODOS -</option>
                    <?
                    $result_fun= mysql_query("select *
                                                from  pessoas, rh_funcionarios, rh_carreiras
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
                                                and   rh_funcionarios.status_funcionario = '1'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                */ ?>
                
                <label for="id_motivo">Motivo:</label>
                <select name="id_motivo" id="id_motivo" title="Motivo">
                    <option value="">- TODOS -</option>
                    <?
                    $result_mot= mysql_query("select * from  rh_motivos
                                                where rh_motivos.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   rh_motivos.tipo_motivo = 'r'
                                                order by rh_motivos.motivo asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_mot= mysql_fetch_object($result_mot)) {
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_mot->id_motivo; ?>" <? if ($rs_mot->id_motivo==$rs->id_motivo) echo "selected=\"selected\""; ?>><?= $rs_mot->motivo; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '1');">
                    <option value="">- TODOS -</option>
                    <?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza_1">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '". $rs->id_departamento ."'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                
                <label for="id_usuario_at">Autorizado por:</label>
                <select name="id_usuario_at" id="id_usuario_at" title="Autorizado por">
                    <option value="">- TODOS -</option>
                    <?
                    $result_fun= mysql_query("select *
                                                from  pessoas, rh_funcionarios, rh_carreiras
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
                                                and   rh_funcionarios.status_funcionario = '1'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
                                                and   rh_carreiras.id_departamento = '3'
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$rs->id_usuario_at) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
            </div>
            <br /><br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>

<script language="javascript" type="text/javascript">
	daFoco("id_refeicao");
</script>

<? } ?>