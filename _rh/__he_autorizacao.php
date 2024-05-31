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
    <legend>Autorização de hora-extra</legend>
    
    <div class="parte50">
        <fieldset>
            <legend>Formulário de cadastro</legend>
            <div id="conteudo_form">
                <? require_once("_rh/__he_autorizacao_form.php"); ?>
            </div>
        </fieldset>
    </div>
    <div class="parte50">
        <fieldset>
            <legend>Autorizações de hora extra</legend>
                
                <table cellspacing="0" width="100%">
                <tr>
                  <th width="27%" align="left" valign="bottom">Funcionário</th>
                  <th width="22%" align="left" valign="bottom">Data/hora</th>
                  <th width="14%" align="left" valign="bottom">Compensação</th>
                  <th width="15%" align="left" valign="bottom">Motivo</th>
                  <th width="13%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
                </tr>
                <?
                $result_he_autorizacao= mysql_query("select * from rh_he_autorizacao
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_funcionario = '". $id_funcionario ."'
													order by  data_he desc, hora_he desc
													") or die(mysql_error());
                $linhas_he_autorizacao= mysql_num_rows($result_he_autorizacao);
                
                $i=0;
                while ($rs_he_autorizacao= mysql_fetch_object($result_he_autorizacao)) {
                ?>
                <tr <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
                    <td><?= pega_funcionario($rs_he_autorizacao->id_funcionario); ?></td>
                    <td><?= desformata_data($rs_he_autorizacao->data_he) ." ". $rs_he_autorizacao->hora_he; ?></td>
                    <td><?= desformata_data($rs_he_autorizacao->data_compensacao); ?></td>
                    <td><?= $rs_he_autorizacao->motivo; ?></td>
                    <td>
                        <a onmouseover="Tip('Autorização de HE');" href="index2.php?pagina=rh/documento&amp;tipo=17&amp;id_he_autorizacao=<?= $rs_he_autorizacao->id_he_autorizacao; ?>" target="_blank">
                            <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                        </a>
                        |
                        <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form', 'carregaPagina&amp;pagina=rh/he_autorizacao_form&amp;acao=e&amp;id_he_autorizacao=<?= $rs_he_autorizacao->id_he_autorizacao; ?>');">
                            <img border="0" src="images/ico_lapis.png" alt="Editar" />
                        </a>
                        |
                        <a href="javascript:ajaxLink('conteudo_interno', 'HEAutorizacaoExcluir&amp;id_he_autorizacao=<?= $rs_he_autorizacao->id_he_autorizacao; ?>&amp;id_funcionario=<?= $rs_he_autorizacao->id_funcionario; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
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