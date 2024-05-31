<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	$acao= 'i'; //$_GET["acao"];
	
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<div class="parte50">
    <fieldset>
    	<legend>Cadastro de vale-transporte</legend>
        
        <fieldset>
            <legend>Formulário de cadastro</legend>
            <div id="conteudo_form">
                <? require_once("_rh/__vt_form.php"); ?>
            </div>
        </fieldset>
    
        <fieldset>
            <legend>Vale-transportes recebidos</legend>
                
                <table cellspacing="0" width="100%">
                <tr>
                  <th width="9%" align="left" valign="bottom">C&oacute;d.</th>
                  <th width="27%" align="left" valign="bottom">Funcionário</th>
                  <th width="22%" align="left" valign="bottom">Linha</th>
                  <th width="14%" align="left" valign="bottom">Trajeto</th>
                  <th width="15%" align="left" valign="bottom">Valor da passagem</th>
                  <th width="13%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
                </tr>
                <?
                $result_vt= mysql_query("select * from rh_vt, rh_vt_linhas
                                            where rh_vt.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   rh_vt.id_linha = rh_vt_linhas.id_linha
                                            and   rh_vt.id_funcionario = '". $id_funcionario ."'
                                            order by  rh_vt.trajeto desc, rh_vt_linhas.id_linha asc
                                            ") or die(mysql_error());
                $linhas_vt= mysql_num_rows($result_vt);
                
                $i=0;
                while ($rs_vt= mysql_fetch_object($result_vt)) {
                ?>
                <tr <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
                  <td><?= $rs_vt->id_vt; ?></td>
                    <td><?= pega_funcionario($rs_vt->id_funcionario); ?></td>
                    <td><?= $rs_vt->linha; ?></td>
                    <td><?= pega_trajeto($rs_vt->trajeto); ?></td>
                    <td>R$ <?= fnum($rs_vt->valor); ?></td>
                    <td>
                        <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form', 'carregaPagina&amp;pagina=rh/vt_form&amp;acao=e&amp;id_vt=<?= $rs_vt->id_vt; ?>');">
                            <img border="0" src="images/ico_lapis.png" alt="Editar" />
                        </a>
                        |
                        <a href="javascript:ajaxLink('conteudo_interno', 'VTExcluir&amp;id_vt=<?= $rs_vt->id_vt; ?>&amp;id_funcionario=<?= $rs_vt->id_funcionario; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
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
    </fieldset>
</div>
<div class="parte50">
	<fieldset>
    	<legend>Cadastro de desconto/período</legend>
        
        <fieldset>
            <legend>Formulário de cadastro</legend>
            <div id="conteudo_form2">
                <? require_once("_rh/__vt_desconto_form.php"); ?>
            </div>
        </fieldset>
        
        <br />
        
      <fieldset>
        <legend>Descontos de vale-transporte cadastrados</legend>
                
          <table cellspacing="0" width="100%">
              <tr>
                <th width="28%" align="left" valign="bottom">Funcionário</th>
                <th width="24%" align="left" valign="bottom">Período</th>
                  <th width="17%" align="left" valign="bottom">Data de entrega</th>
                <th width="16%" align="left" valign="bottom">Descontado</th>
                <th width="15%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
              </tr>
              <?
                $result_vt_desconto= mysql_query("select * from rh_vt_descontos
													where rh_vt_descontos.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   rh_vt_descontos.id_funcionario = '". $id_funcionario ."'
													order by  ano desc, mes desc
													") or die(mysql_error());
                $linhas_vt_desconto= mysql_num_rows($result_vt_desconto);
                
                $i=0;
                while ($rs_vt_desconto= mysql_fetch_object($result_vt_desconto)) {
                ?>
              <tr <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
                  <td><?= pega_funcionario($rs_vt_desconto->id_funcionario); ?></td>
                  <td><?= traduz_mes($rs_vt_desconto->mes) ."/". $rs_vt_desconto->ano; ?></td>
                    <td><?= desformata_data($rs_vt_desconto->data_entrega); ?></td>
                  <td><?= $rs_vt_desconto->qtde; ?></td>
                  <td>
                      <a onmouseover="Tip('Entrega de vale transporte');" href="index2.php?pagina=rh/documento&amp;tipo=12&amp;id_vt_desconto=<?= $rs_vt_desconto->id_vt_desconto; ?>" target="_blank">
                          <img border="0" src="images/ico_pdf.png" alt="Relatório" />
                      </a>
                      |
                      <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form2', 'carregaPagina&amp;pagina=rh/vt_desconto_form&amp;acao=e&amp;id_vt_desconto=<?= $rs_vt_desconto->id_vt_desconto; ?>');">
                          <img border="0" src="images/ico_lapis.png" alt="Editar" />
                      </a>
                      |
                      <a href="javascript:ajaxLink('conteudo_interno', 'VTDescontoExcluir&amp;id_vt_desconto=<?= $rs_vt_desconto->id_vt_desconto; ?>&amp;id_funcionario=<?= $rs_vt_desconto->id_funcionario; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
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
        
    </fieldset>
</div>

<br />
<? } ?>