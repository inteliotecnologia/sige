<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	$acao= 'i'; //$_GET["acao"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<div id="conteudo_interno">

    <fieldset>
    	<legend>Histórico</legend>
        
        <fieldset>
            <legend>Formulário de cadastro</legend>
            <div id="conteudo_form">
                <? require_once("_financeiro/__cliente_historico_form.php"); ?>
            </div>
        </fieldset>
        <br />
        
        <fieldset>
            <legend>Histórico do cliente</legend>
                
                <strong>Relatório em PDF:</strong> <a target="_blank" href="./index2.php?pagina=financeiro/cliente_historico_relatorio&amp;id_cliente=<?= $id_cliente ?>"><img src="images/ico_pdf.png" alt="" border="0" /></a>
                <br /><br />
                
                <table cellspacing="0" width="100%">
                <tr>
                  <th width="12%" align="left" valign="bottom">Data/hora</th>
                  <th width="12%" align="left" valign="bottom">Cliente</th>
                  <th width="45%" align="left" valign="bottom">Histórico</th>
                  <th width="14%" align="left" valign="bottom">Lan&ccedil;ado por</th>
                  <th width="17%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
                </tr>
                <?
                $result_historico= mysql_query("select * from com_livro
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   reclamacao_id_cliente = '". $id_cliente ."'
												order by data_livro desc, hora_livro desc
												") or die(mysql_error());
                $linhas_historico= mysql_num_rows($result_historico);
                
                $i=0;
                while ($rs_historico= mysql_fetch_object($result_historico)) {
                ?>
                <tr id="linha_<?=$i;?>" <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
                    <td valign="top"><?= desformata_data($rs_historico->data_livro) ." ". $rs_historico->hora_livro; ?></td>
                    <td valign="top"><?= pega_sigla_pessoa($rs_historico->reclamacao_id_cliente); ?></td>
                    <td valign="top">
						<?
                        echo $rs_historico->mensagem ."<br />";
						
						if ($rs_historico->id_motivo!="") echo "<span class=\"menor\"><strong>". pega_motivo($rs_historico->id_motivo) ."</strong></span><br />";
						
						//reclamação de cliente
						if (($rs_historico->id_motivo==34) || ($rs_historico->id_motivo==42)) {
						?>
                        <a class="menor vermelho" href="./?pagina=qualidade/reclamacao&amp;acao=e&amp;id_livro=<?=$rs_historico->id_livro;?>&amp;origem=h" target="_blank"><strong>ACESSAR</strong></a>
                        <? } ?>
                    </td>
                    <td valign="top" class="menor">
						<?
						if ($rs_historico->de!="") echo pega_funcionario($rs_historico->de);
						else echo pega_nome_pelo_id_usuario($rs_historico->id_usuario);
						?>
                    </td>
                    <td valign="top">
                        <? if ($rs_historico->id_motivo=="") { ?>
                            <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form', 'carregaPagina&amp;pagina=financeiro/cliente_historico_form&amp;acao=e&amp;id_livro=<?= $rs_historico->id_livro; ?>&amp;id_cliente=<?= $rs_historico->reclamacao_id_cliente; ?>');">
                                <img border="0" src="images/ico_lapis.png" alt="Editar" />
                            </a>
                            <? if ($_SESSION["tipo_usuario"]=="a") { ?>
                            |
                            <a href="javascript:ajaxLink('linha_<?=$i;?>', 'clienteHistoricoExcluir&amp;id_livro=<?= $rs_historico->id_livro; ?>&amp;id_cliente=<?= $rs_historico->reclamacao_id_cliente; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
                                <img border="0" src="images/ico_lixeira.png" alt="Status" />
                            </a>
                            <? } ?>
                        <? } else echo "-"; ?>
                    </td>
                </tr>
                <?
                    $i++;
                }
                ?>
            </table>
            
        </fieldset>
    </fieldset>
</div>

<br />
<? } ?>