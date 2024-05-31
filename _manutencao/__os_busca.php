<?
require_once("conexao.php");
if (pode_algum("kj", $_SESSION["permissao"])) {
?>

<h2>Relatório de Ordens de serviço</h2>

<div id="conteudo_interno">
   <? if ($_GET["geral"]=="") { ?>
   <div class="parte50">
        <fieldset>
            <legend>Busca</legend>
            
            <form action="./?pagina=manutencao/os_listar" method="post">
            
            	<input class="escondido" type="hidden" id="buscando" name="buscando" value="1" />
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label for="id_os">Número da OS:</label>
                <input name="id_os" id="id_os" class="tamanho25p espaco_dir" value="" title="Número da os" />
                <br />
                
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
            <legend>Relatório geral de OS</legend>
            
            <form action="index2.php?pagina=manutencao/os_relatorio" target="_blank" method="post" name="formOSBuscar" id="formOSBuscar" onsubmit="return validaFormNormal('validacoes');">
            
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