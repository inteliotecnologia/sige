<?
require_once("conexao.php");
if (pode_algum("r", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
	
	$result= mysql_query("select * from rh_carreiras_departamentos
						 	where id_funcionario = '$id_funcionario'
							order by id_carreira_departamento desc limit 1
							");
	$rs= mysql_fetch_object($result);
	
	$id_departamento_principal= pega_dado_carreira("id_departamento", $id_funcionario);
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<div id="conteudo_interno">

    <form action="<?= AJAX_FORM; ?>formDepartamentoPermissao" method="post" name="formDepartamentoPermissao" id="formDepartamentoPermissao" onsubmit="return ajaxForm('conteudo_interno', 'formDepartamentoPermissao');">
    	
        <input type="hidden" class="escondido" name="id_funcionario" id="id_funcionario" value="<?=$id_funcionario;?>" />
        
        <div class="parte50x">
            <fieldset class="">
                <legend>Departamentos</legend>
                
                <label class="tamanho100" for="valido">Modo:</label>
                <select class="tamanho300" name="valido" id="valido">
                	<option value="0" <? if (($rs->valido=="0") || ($rs->valido=="")) echo "selected=\"selected\""; ?>>Somente tem acesso aos departamentos</option>
                    <option value="1" <? if ($rs->valido=="1") echo "selected=\"selected\""; ?>>Responde inteiramente pelos departamentos</option>
                </select>
                <br /><br /><br />
                
                <?
				$id_departamento_funcionario= pega_dado_carreira("id_departamento", $id_funcionario);
				
                $result_dep= mysql_query("select * from rh_departamentos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."' 
											/* and   id_departamento <> '$id_departamento_principal' */
                                            and   status_departamento = '1' 
                                            order by departamento asc
                                            ");
                $i=1;
                while ($rs_dep= mysql_fetch_object($result_dep)) {
                    $result_permissao= mysql_query("select * from rh_carreiras_departamentos
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    and   id_funcionario = '". $id_funcionario ."'
                                                    and   id_departamento = '". $rs_dep->id_departamento ."'
                                                    ");
                    $linhas_permissao= mysql_num_rows($result_permissao);
                ?>
                <input <? if ( ($linhas_permissao>0) || ($id_departamento_funcionario==$rs_dep->id_departamento) ) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="id_departamento[]" id="id_departamento_<?= $rs_dep->id_departamento;?>" value="<?= $rs_dep->id_departamento;?>" />
                <label for="id_departamento_<?= $rs_dep->id_departamento;?>" class="alinhar_esquerda menor tamanho180"><?= $rs_dep->departamento;?></label>
                
                <?
                    if (($i%4)==0) echo "<br /><br />";
                    
                    $i++;
                }
                ?>
                
                <br /><br />
                
            </fieldset>
        </div>
                
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </form>

<? } ?>