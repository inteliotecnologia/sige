<?
require_once("conexao.php");
if (pode_algum("kj", $_SESSION["permissao"])) {
?>

<h2>Relatório de Ordens de serviço</h2>

<div id="conteudo_interno">
   <? if ($_GET["geral"]=="") { ?>
   <div class="parte50">
        <fieldset>
            <legend>Diário</legend>
            
            <form action="./?pagina=manutencao/os_rm_listar" method="post">
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label for="data">Data:</label>
                <input name="data" id="data" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="" title="Data" />
                <br />

				<label for="id_usuario">Aberta por:</label>
                <select name="id_usuario" id="id_usuario" title="Funcionário">
                    <option value="">- TODOS -</option>
					<?
                    $result_fun= mysql_query("select distinct(usuarios.id_usuario) as id_usuario
                                                from  pessoas, rh_funcionarios, man_oss, usuarios
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
												and   rh_funcionarios.status_funcionario <> '2'
												and   rh_funcionarios.id_funcionario = usuarios.id_funcionario
												and   usuarios.id_usuario = man_oss.id_usuario
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

                <label for="tipo_os">Tipo:</label>
                <select name="tipo_os" id="tipo_os" title="Tipo" onchange="alteraTipoRM(this.value);">
                    <option value="">- SELECIONE -</option>
                    <option class="cor_sim" value="e" <? if (($rs->tipo_os=='e') || (($acao=='i') && ($_GET["id_equipamento"]!=""))) echo "selected=\"selected\""; ?>>Equipamento</option>
                    <option value="p" <? if ($rs->tipo_os=='p') echo "selected=\"selected\""; ?>>Predial</option>
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
                
                <? /*
                <label for="id_situacao">Situação:</label>
                <select name="id_situacao" id="id_situacao" title="Situação">
                    <option value="">- SELECIONE -</option>
                    <option value="1">Am aberto</option>
                    <option value="2">Finalizadas</option>
                </select>
                <br />
                */ ?>
                
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
            <legend>Relatório geral de OS</legend>
            
            <form action="index2.php?pagina=manutencao/os_rm_relatorio" target="_blank" method="post" name="formOSBuscar" id="formOSBuscar" onsubmit="return validaFormNormal('validacoes');">
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
               
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_os, '%m/%Y')) as data_remessa2 from man_oss order by data_os desc ");
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
    <? } ?>
</div>

<? } ?>