<?
require_once("conexao.php");
if (pode_algum("ps", $_SESSION["permissao"])) {
?>

<h2>Relatório de lavagem - Área Suja</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Diário</legend>
            
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/lavagem_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/lavagem_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="d" />
               
                <label for="data">Data:</label>
                <input name="data" id="data" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data 1" />
                <br />
                
                
                <label for="lista">Lista:</label>
                <select name="lista" id="lista" onchange="alteraClientesAtivosInativos(this.value, 'id_cliente_atualiza1');">
                    <option value="1">Ativos</option>
                    <option value="0" class="cor_sim">Inativos</option>
                </select>
                <br />
                
                <label for="id_cliente">Cliente:</label>
                <div id="id_cliente_atualiza1">
                    <select name="id_cliente" id="id_cliente" title="Cliente">
                        <option value="">- TODOS -</option>
                        <?
                        $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                                    where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                    and   pessoas_tipos.tipo_pessoa = 'c'
                                                    and   pessoas.id_cliente_tipo = '1'
													and   pessoas.status_pessoa = '1'
                                                    order by 
                                                    pessoas.apelido_fantasia asc
                                                    ") or die(mysql_error());
                        $i=0;
                        while ($rs_cli = mysql_fetch_object($result_cli)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                

                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TODOS -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '2'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                
                <label for="id_equipamento">Máquina:</label>
                <select name="id_equipamento" id="id_equipamento" title="Máquina">	  		
                    <option value="">- TODOS -</option>
                    <?
                    $result_equi= mysql_query("select * from op_equipamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   tipo_equipamento = '1'
                                                order by equipamento asc
                                                 ");
                    $i=0;
                    while ($rs_equi = mysql_fetch_object($result_equi)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_equi->id_equipamento; ?>" <? if ($rs_equi->id_equipamento==$rs->id_equipamento) echo "selected=\"selected\""; ?>><?= $rs_equi->equipamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />

                <label for="id_processo">Processo:</label>
                <select name="id_processo" id="id_processo" title="Processo">	  		
                    <option value="">- TODOS -</option>
                    <?
                    $result_proc= mysql_query("select * from op_equipamentos_processos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by codigo asc
                                                 ");
                    $i=0;
                    while ($rs_proc = mysql_fetch_object($result_proc)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_proc->id_processo; ?>" <? if ($rs_proc->id_processo==$rs->id_processo) echo "selected=\"selected\""; ?>><?= $rs_proc->codigo .". ". $rs_proc->processo; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_funcionario">Lavador:</label>
                <select id="id_funcionario" name="id_funcionario">
                    <option value="">- TODOS -</option>
                    <?
                    $j=0;
                    $result_fun= mysql_query("select *
                                                from  pessoas, rh_funcionarios, rh_carreiras
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
                                                and   rh_carreiras.id_departamento = '2'
                                                /* and   rh_carreiras.id_cargo = '4' */
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   rh_funcionarios.status_funcionario = '1'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= primeira_palavra($rs_fun->nome_rz); ?></option>
                    <? $j++; } ?>
                </select>
                <br />
                
                <br /><br /><br /><br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    <div class="parte50">
        <fieldset>
            <legend>Mensal</legend>
            
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/lavagem_relatorio" target="_blank" method="post" name="formLavagemBuscar" id="formLavagemBuscar" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/lavagem_listar" method="post" name="formLavagemBuscar" id="formLavagemBuscar" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="m" />
               
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_remessa, '%m/%Y')) as data_remessa2 from op_suja_remessas order by data_remessa desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_remessa= explode('/', $rs_per->data_remessa2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_remessa2; ?>"><?= traduz_mes($data_remessa[0]) .'/'. $data_remessa[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Entre datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br /><br />
                
                <label for="lista">Lista:</label>
                <select name="lista" id="lista" onchange="alteraClientesAtivosInativos(this.value, 'id_cliente_atualiza2');">
                    <option value="1">Ativos</option>
                    <option value="0" class="cor_sim">Inativos</option>
                </select>
                <br />
                
                <label for="id_cliente">Cliente:</label>
                <div id="id_cliente_atualiza2">
                    <select name="id_cliente" id="id_cliente" title="Cliente">
                        <option value="">- TODOS -</option>
                        <?
                        $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                                    where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                    and   pessoas_tipos.tipo_pessoa = 'c'
                                                    and   pessoas.id_cliente_tipo = '1'
													and   pessoas.status_pessoa = '1'
                                                    order by 
                                                    pessoas.apelido_fantasia asc
                                                    ") or die(mysql_error());
                        $i=0;
                        while ($rs_cli = mysql_fetch_object($result_cli)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TODOS -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '2'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                
                <label for="id_equipamento">Máquina:</label>
                <select name="id_equipamento" id="id_equipamento" title="Máquina">	  		
                    <option value="">- TODOS -</option>
                    <?
                    $result_equi= mysql_query("select * from op_equipamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   tipo_equipamento = '1'
                                                order by equipamento asc
                                                 ");
                    $i=0;
                    while ($rs_equi = mysql_fetch_object($result_equi)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_equi->id_equipamento; ?>" <? if ($rs_equi->id_equipamento==$rs->id_equipamento) echo "selected=\"selected\""; ?>><?= $rs_equi->equipamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />

                <label for="id_processo">Processo:</label>
                <select name="id_processo" id="id_processo" title="Processo">	  		
                    <option value="">- TODOS -</option>
                    <?
                    $result_proc= mysql_query("select * from op_equipamentos_processos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by codigo asc
                                                 ");
                    $i=0;
                    while ($rs_proc = mysql_fetch_object($result_proc)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_proc->id_processo; ?>" <? if ($rs_proc->id_processo==$rs->id_processo) echo "selected=\"selected\""; ?>><?= $rs_proc->codigo .". ". $rs_proc->processo; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_funcionario">Lavador:</label>
                <select id="id_funcionario" name="id_funcionario">
                    <option value="">- TODOS -</option>
                    <?
                    $j=0;
                    $result_fun= mysql_query("select *
                                                from  pessoas, rh_funcionarios, rh_carreiras
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
                                                and   rh_carreiras.id_departamento = '2'
                                                /* and   rh_carreiras.id_cargo = '4' */
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   rh_funcionarios.status_funcionario = '1'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= primeira_palavra($rs_fun->nome_rz); ?></option>
                    <? $j++; } ?>
                </select>
                <br />
                
                <br /><br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
</div>

<script language="javascript" type="text/javascript">
	daFoco("data");
</script>

<? } ?>