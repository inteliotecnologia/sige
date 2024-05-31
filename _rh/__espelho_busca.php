<?
require_once("conexao.php");
if (pode_algum("rvh4", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<? if ($_GET["geral"]==1) { ?>
<h2>Cartão ponto geral</h2>

<div id="conteudo_interno">
<? } ?>

<fieldset>
    <legend>Formulário de busca do período</legend>
    	
        <div class="parte50">
        	<form action="<?= AJAX_FORM; ?>formEspelhoBuscar" method="post" name="formEspelhoBuscar" id="formEspelhoBuscar" onsubmit="return ajaxForm('conteudo_interno', 'formEspelhoBuscar');">
                
                <input class="escondido" type="hidden" name="geral" id="geral" value="<?=$_GET["geral"];?>" />
                
                <? if ($_GET["geral"]!=1) { ?>
                <label for="lista">Lista:</label>
                <select name="lista" id="lista" onchange="alteraFuncionariosAtivosInativos(this.value);">
                	<option value="1">Ativos</option>
                    <option value="0" class="cor_sim">Inativos</option>
                    <option value="-1">Em espera</option>
                </select>
                <br />
                
                <label for="id_funcionario">Funcionário:</label>
                <div id="id_funcionario_atualiza">
                    <select name="id_funcionario" id="id_funcionario" title="Funcionário">
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
                            /*if ($rs_fun->status_funcionario==1) $classe= "cor_sim";
                            else $classe= "cor_nao";*/
							
							if (($i%2)==0) $classe= "cor_sim";
							else $classe= "cor_nao";
                        ?>
                        <option class="<?= $classe; ?>" value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$_GET["id_funcionario"]) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                <? } else { ?>
                <label for="id_departamento">Departamento:</label>
                <div id="id_departamento_atualiza">
                    <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSoh();">
                        <option value="">- DEPARTAMENTO -</option>
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
                </div>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza">
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
                <? } ?>
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
					<?
					$i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%m/%Y')) as data_batida2 from rh_ponto order by data_batida desc ");
					
                    while ($rs_per= mysql_fetch_object($result_per)) {
						$data_batida= explode('/', $rs_per->data_batida2);
						if ((date("d")>=25) && (date("m")==$data_batida[0]) && (date("Y")==$data_batida[1]) ) {
							$proximo_mes= date("m/Y", mktime(0, 0, 0, $data_batida[0]+1, 1, $data_batida[1]));
							$data_batida2= explode('/', $proximo_mes);
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $proximo_mes; ?>"><?= traduz_mes($data_batida2[0]) .'/'. $data_batida2[1]; ?></option>
                    <? } ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_batida2; ?>"><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
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
                
                <br /><br />
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
                
            </form>
        </div>
	
</fieldset>

<? if ($_GET["geral"]==1) { ?>
</div>
<? } ?>

<? } ?>