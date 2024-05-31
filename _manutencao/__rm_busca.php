<?
require_once("conexao.php");
if (pode_algum("kj", $_SESSION["permissao"])) {
?>

<? if ($_GET["geral"]=="1") { ?>
<h2>Relatório de RM</h2>
<? } else { ?>
<h2>Busca de RM</h2>
<? } ?>

<div id="conteudo_interno">
   <? if ($_GET["geral"]=="") { ?>
   <div class="parte50">
        <fieldset>
            <legend>Busca</legend>
            
            <form action="./?pagina=manutencao/rm_listar" method="post">
            
                <input class="escondido" type="hidden" id="buscando" name="buscando" value="1" />
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label for="id_rm">Número da RM:</label>
                <input name="id_rm" id="id_rm" class="tamanho25p espaco_dir" value="" title="Número da RM" />
                <br />
                
                <label for="data">Data:</label>
                <input name="data" id="data" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="" title="Data" />
                <br />

				<label for="id_usuario">Aberta por:</label>
                <select name="id_usuario" id="id_usuario" title="Funcionário">
                    <option value="">- TODOS -</option>
					<?
                    $result_fun= mysql_query("select distinct(usuarios.id_usuario) as id_usuario
                                                from  pessoas, rh_funcionarios, man_rms, usuarios
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
												and   rh_funcionarios.status_funcionario <> '2'
												and   rh_funcionarios.id_funcionario = usuarios.id_funcionario
												and   usuarios.id_usuario = man_rms.id_usuario
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_usuario; ?>"<? if ($rs_fun->id_usuario==$_GET["id_usuario"]) echo "selected=\"selected\""; ?>><?= pega_nome_pelo_id_usuario($rs_fun->id_usuario); ?></option>
                    <? $i++; } ?>
                </select>
                <br />

                <label for="tipo_rm">Tipo:</label>
                <select name="tipo_rm" id="tipo_rm" title="Tipo" onchange="alteraTipoRM(this.value);">
                    <option value="">- SELECIONE -</option>
                    <option class="cor_sim" value="e" <? if (($rs->tipo_rm=='e') || (($acao=='i') && ($_GET["id_equipamento"]!=""))) echo "selected=\"selected\""; ?>>Equipamento</option>
                    <option value="p" <? if ($rs->tipo_rm=='p') echo "selected=\"selected\""; ?>>Predial</option>
                </select>
                <br />
                
                <div id="div_id_equipamento" class="escondido">
                    <label for="id_equipamento">Equipamento:</label>
                    <select name="id_equipamento" id="id_equipamento" title="Equipamento">
                        <option value="">- TODOS -</option>
                        
                        <?
                        $result_eq= mysql_query("select * from op_equipamentos
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    order by tipo_equipamento asc,
                                                    equipamento asc
                                                    ") or die(mysql_error());
                        $i=0;
                        while ($rs_eq = mysql_fetch_object($result_eq)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_eq->id_equipamento; ?>" <? if (($rs_eq->id_equipamento==$rs->id_equipamento) || ($rs_eq->id_equipamento==$_GET["id_equipamento"])) echo "selected=\"selected\""; ?>><?= $rs_eq->equipamento; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br />
                </div>
                <br />
                
                <label for="id_situacao">Situação:</label>
                <select name="id_situacao" id="id_situacao" title="Situação">
                    <option value="">- SELECIONE -</option>
                    <option value="1">Am aberto</option>
                    <option value="2">Finalizadas</option>
                </select>
                <br />
                
                <label for="id_tecnico_preferencial">Técnico preferencial:</label>
	            <select id="id_tecnico_preferencial" name="id_tecnico_preferencial" title="Técnico preferencial">
					
	                <option value="">-</option>
	                
					<?
	                $j=0;
	                $result_fun= mysql_query("select *
	                                            from  pessoas, rh_funcionarios, rh_carreiras
	                                            where pessoas.id_pessoa = rh_funcionarios.id_pessoa
	                                            and   pessoas.tipo = 'f'
	                                            and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
	                                            and   rh_carreiras.atual = '1'
	                                            and   rh_carreiras.id_departamento = '8'
												and   rh_funcionarios.afastado <> '1'
	                                            and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
	                                            and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
	                                            order by pessoas.nome_rz asc
	                                            ") or die(mysql_error());
	                while ($rs_fun= mysql_fetch_object($result_fun)) {
	                ?>
	                <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_tecnico_preferencial) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
	                <? $j++; } ?>
	            </select>
	            <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    <? } ?>
    
    <? if ($_GET["geral"]=="1") { ?>
    <div class="parte50">
        <fieldset>
            <legend>Relatório geral de RM</legend>
            
            <form action="index2.php?pagina=manutencao/rm_relatorio" target="_blank" method="post" name="formRMBuscar" id="formRMBuscar" onsubmit="return validaFormNormal('validacoes');">
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
               
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y')) as data_remessa2 from man_rms, man_rms_andamento
                    where man_rms.id_rm = man_rms_andamento.id_rm
                    and   man_rms_andamento.id_situacao = '1'
                    and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
                    order by man_rms_andamento.data_rm_andamento desc ");
                    
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_remessa= explode('/', $rs_per->data_remessa2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_remessa2; ?>"><?= traduz_mes($data_remessa[0]) .'/'. $data_remessa[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    <? /*
    <div class="parte50">
        <fieldset>
            <legend>Quantitativo de requisições por departamento</legend>
            
            <form action="index2.php?pagina=manutencao/rm_relatorio" target="_blank" method="post" name="formRMBuscar" id="formRMBuscar" onsubmit="return validaFormNormal('validacoes');">
            
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
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    
    <div class="parte50">
        <fieldset>
            <legend>Quantitativo de execuções por funcionário</legend>
            
            <form action="index2.php?pagina=manutencao/rm_relatorio" target="_blank" method="post" name="formRMBuscar" id="formRMBuscar" onsubmit="return validaFormNormal('validacoes');">
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="f" />
               
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
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    
    <div class="parte50">
        <fieldset>
            <legend>Quantitativo de requisições abertas x finalizadas</legend>
            
            <form action="index2.php?pagina=manutencao/rm_relatorio" target="_blank" method="post" name="formRMBuscar" id="formRMBuscar" onsubmit="return validaFormNormal('validacoes');">
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="a" />
               
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
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    
    <div class="parte50">
        <fieldset>
            <legend>Quantitativo de requisições por equipamento</legend>
            
            <form action="index2.php?pagina=manutencao/rm_relatorio" target="_blank" method="post" name="formRMBuscar" id="formRMBuscar" onsubmit="return validaFormNormal('validacoes');">
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="e" />
               
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
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    
    <div class="parte50">
        <fieldset>
            <legend>Quantitativo de requisições predial x equipamento</legend>
            
            <form action="index2.php?pagina=manutencao/rm_relatorio" target="_blank" method="post" name="formRMBuscar" id="formRMBuscar" onsubmit="return validaFormNormal('validacoes');">
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="p" />
               
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
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    <? */ }  ?>
</div>

<script language="javascript" type="text/javascript">
	daFoco("id_rm");
</script>

<? } ?>