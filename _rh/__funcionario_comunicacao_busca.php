<?
require_once("conexao.php");
if (pode("rhm", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Relatório de funcionários</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form action="index2.php?pagina=rh/funcionario_comunicacao_relatorio" method="post" target="_blank" name="formHistoricoBuscar" id="formHistoricoBuscar" onsubmit="return validaFormNormal('validacoes');">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="" />
                    
                    <? /*
                    <label for="id_departamento">Departamento:</label>
                    <div id="id_departamento_atualiza">
                        <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnos();">
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
                    */ ?>
                    
                    <label for="situacao">Situação:</label>
                    <select name="situacao" id="situacao" title="Situação">	  		
                        <option value="1" class="cor_sim">Somente funcionários ativos presentes</option>
                        <option value="2">Todos os funcionários ativos</option>
                    </select>
                    <br />
                    
                    <? /*
                    <fieldset>
                    	<legend>Idade:</legend>
                        
                        <label for="p1">* Entre:</label>
                        <input name="p1" id="p1" class="tamanho25p" />
                        <br />
                        
                        <label for="p2">* E:</label>
                        <input name="p2" id="p2" class="tamanho25p" />
                        <br />
                    
                    </fieldset>
                    */ ?>
					
                    <label for="sexo">Sexo:</label>
                    <select name="sexo" id="sexo" title="Sexo">	  		
                        <option value="">-</option>
                        <option value="m">Masculino</option>
                        <option value="f" class="cor_sim">Feminino</option>
                    </select>
                    <br />
                    
                    <label for="ordenacao">Ordenar por:</label>
                    <select name="ordenacao" id="ordenacao" title="Ordenar por">	  		
                        <option value="pessoas.nome_rz">Nome</option>
                        <option value="rh_departamentos.departamento asc, rh_carreiras.id_turno" class="cor_sim">Departamento</option>
                    </select>
                    <br />
                    
                    <label>&nbsp;</label>
                    <input type="checkbox" class="tamanho30" id="filhos" name="filhos" value="1" />
                    <label for="filhos" class="alinhar_esquerda nao_negrito tamanho300">Tenha filhos</label>
                    <br />
                    
                    <label>&nbsp;</label>
                    <input type="checkbox" class="tamanho30" id="filhos18" name="filhos18" value="1" />
                    <label for="filhos18" class="alinhar_esquerda nao_negrito tamanho300" onclick="checaCampo('filhos');">Filhos menores de 18 anos</label>
                    <br />
                    
                    <label>&nbsp;</label>
                    <input type="checkbox" class="tamanho30" id="mostrar_filhos" name="mostrar_filhos" value="1" />
                    <label for="mostrar_filhos" class="alinhar_esquerda nao_negrito tamanho300">Mostrar dados dos filhos</label>
                    <br /><br />
                    
                    <label>&nbsp;</label>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                    <br />
                
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>