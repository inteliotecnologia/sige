<?
require_once("conexao.php");
if (pode("rhv", $_SESSION["permissao"])) {
	$acao= 'i'; //$_GET["acao"];
	
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<fieldset>
    <legend>Substituição temporária de função</legend>
    
    <div class="parte50">
        <fieldset>
            <legend>Formulário de cadastro</legend>
            <div id="conteudo_form">
                <? require_once("_rh/__substituicao_funcao_form.php"); ?>
            </div>
        </fieldset>
    </div>
    <div class="parte50">
        <fieldset>
            <legend>Substituições</legend>
                
                <table cellspacing="0" width="100%">
                <tr>
                  <th width="57%" align="left" valign="bottom">Funcionário</th>
                  <th width="21%" align="left" valign="bottom">Data</th>
                  <th width="22%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
                </tr>
                <?
                $result_substituicao_funcao= mysql_query("select * from rh_substituicao_funcao
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_funcionario = '". $id_funcionario ."'
													order by  data_substituicao desc
													") or die(mysql_error());
                $linhas_substituicao_funcao= mysql_num_rows($result_substituicao_funcao);
                
                $i=0;
                while ($rs_substituicao_funcao= mysql_fetch_object($result_substituicao_funcao)) {
                ?>
                <tr <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
                    <td><?= pega_funcionario($rs_substituicao_funcao->id_funcionario); ?></td>
                    <td><?= desformata_data($rs_substituicao_funcao->data_substituicao) ." ". $rs_substituicao_funcao->hora_he; ?></td>
                    <td>
                        <a onmouseover="Tip('Termo de substituição temporária de função');" href="index2.php?pagina=rh/documento&amp;tipo=18&amp;id_substituicao_funcao=<?= $rs_substituicao_funcao->id_substituicao_funcao; ?>" target="_blank">
                            <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                        </a>
                        |
                        <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form', 'carregaPagina&amp;pagina=rh/substituicao_funcao_form&amp;acao=e&amp;id_substituicao_funcao=<?= $rs_substituicao_funcao->id_substituicao_funcao; ?>');">
                            <img border="0" src="images/ico_lapis.png" alt="Editar" />
                        </a>
                        |
                        <a href="javascript:ajaxLink('conteudo_interno', 'substituicaoFuncaoExcluir&amp;id_substituicao_funcao=<?= $rs_substituicao_funcao->id_substituicao_funcao; ?>&amp;id_funcionario=<?= $rs_substituicao_funcao->id_funcionario; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
                            <img border="0" src="images/ico_lixeira.png" alt="Status" />
                        </a>
                    </td>
                </tr>
                <?
                    $i++;
                }
                ?>
            </table>
            
        </fieldset>
    </div>
</fieldset>

<br />
<? } ?>