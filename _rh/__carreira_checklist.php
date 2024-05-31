<?
require_once("conexao.php");
if (pode_algum("rh", $_SESSION["permissao"])) {
	if ($_GET["id_carreira"]!="") $id_carreira= $_GET["id_carreira"];
	if ($_POST["id_carreira"]!="") $id_carreira= $_POST["id_carreira"];
	
	$result= mysql_query("select * from rh_carreiras
						 	where id_empresa = '". $_SESSION["id_empresa"] ."'
							and   id_carreira = '". $id_carreira ."'
							");
	$rs= mysql_fetch_object($result);
?>
	
    <div class="div_status_funcionario">
	    (<?= ativo_inativo(pega_status_funcionario($rs->id_funcionario)); ?>)
    </div>
    
    <div id="tela_mensagens2">
        <? include("__tratamento_msgs.php"); ?>
    </div>
    
    <fieldset>
    	<legend>Checklist de <?= pega_acao_carreira2($rs->id_acao_carreira); ?></legend>
        
        <form action="<?= AJAX_FORM; ?>formCarreiraChecklist" method="post" name="formCarreiraChecklist" id="formCarreiraChecklist" onsubmit="return ajaxForm('conteudo_interno', 'formCarreiraChecklist');">
            
            <input class="escondido" type="hidden" id="id_carreira" name="id_carreira" value="<?= $id_carreira; ?>" />
            
            <input class="escondido" type="hidden" id="id_acao_carreira" name="id_acao_carreira" value="<?= $rs->id_acao_carreira; ?>" />
            <input class="escondido" type="hidden" id="id_funcionario" name="id_funcionario" value="<?= $rs->id_funcionario; ?>" />
            
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <th width="50%" align="left">Item</th>
                    <th align="left" width="50%">
                        Checklist
                    </th>
                </tr>
                <?
				if ($rs->id_acao_carreira==1) $vetor= pega_checklist_admissao('l');
				elseif ($rs->id_acao_carreira==2) $vetor= pega_checklist_demissao('l');
				
				$i=1;
				while ($vetor[$i]) {
					$result_checado= mysql_query("select * from rh_carreiras_checklist
												 	where id_acao_carreira = '". $rs->id_acao_carreira ."'
													and   id_funcionario = '". $rs->id_funcionario ."'
													and   id_item = '$i'
													");
					$linhas_checado= mysql_num_rows($result_checado);
				?>
                <tr>
                    <td align="left" class="td_dia_semana">
                        <strong><?= $vetor[$i]; ?></strong>
                    </td>
                    <td align="center">
                        <input class="escondido" type="hidden" name="id_item[<?=$i;?>]" value="<?= $i; ?>" />
                        <input class="tamanho20" type="checkbox" name="checado[<?=$i;?>]" value="1" <? if ($linhas_checado>0) echo "checked=\"checked\""; ?> />
                    </td>
                </tr>
                <? $i++; } ?>
            </table>
        	
            <br /><br />
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        
        </form>
    </fieldset>
<? } ?>