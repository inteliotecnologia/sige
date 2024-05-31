<?
require_once("conexao.php");
if (pode("i12", $_SESSION["permissao"])) {
	$acao= 'i'; //$_GET["acao"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($_GET["tipo_item_cedido"]!="") $tipo_item_cedido= $_GET["tipo_item_cedido"];
	if ($_POST["tipo_item_cedido"]!="") $tipo_item_cedido= $_POST["tipo_item_cedido"];
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<div id="conteudo_interno">

	<ul class="recuo1">
        <li class="flutuar_esquerda tamanho160"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_servicos_geral&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">lavação/transporte</a></li>
        <li class="flutuar_esquerda tamanho200"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_servicos_pecas&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">passadoria/costura/enxoval</a></li>
        <li class="flutuar_esquerda tamanho100"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_item_cedido&amp;tipo_item_cedido=1&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">hampers</a></li>
        <li class="flutuar_esquerda tamanho160"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_item_cedido&amp;tipo_item_cedido=2&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">formulário de coleta</a></li>
        <li class="flutuar_esquerda tamanho80"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=op/equipamento_listar&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">equipamentos</a></li>
    </ul>
    <br /><br />
    
    <fieldset>
    	<legend><?= pega_item_cedido($tipo_item_cedido); ?></legend>
        
        <div class="parte50">
            <fieldset>
                <legend>Formulário de lançamento</legend>
                <div id="conteudo_form">
                    <? require_once("_financeiro/__cliente_item_cedido_form.php"); ?>
                </div>
            </fieldset>
    	</div>
        <div class="parte50">
            <fieldset>
                <legend>Quantidade padrão para este cliente</legend>
                <div id="conteudo_form2">
                    <? require_once("_financeiro/__cliente_item_cedido_padrao_form.php"); ?>
                </div>
            </fieldset>
    	</div>
        <br />
        
        <fieldset>
        	<legend>Próximo envio:</legend>
            
            <?
			$result_item_cedido_periodo_ultimo= mysql_query("select data_valida from fi_clientes_itens_cedidos
															where id_empresa = '". $_SESSION["id_empresa"] ."'
															and   id_cliente = '". $id_cliente ."'
															and   tipo_item_cedido = '". $tipo_item_cedido ."'
															order by data_valida desc limit 1
															") or die(mysql_error());
			$rs_item_cedido_periodo_ultimo= mysql_fetch_object($result_item_cedido_periodo_ultimo);
			?>
            
            <?= desformata_data(soma_data($rs_item_cedido_periodo_ultimo->data_valida, 60, 0, 0)); ?>
            
        </fieldset>
        
        <fieldset>
            <legend>Itens entregues</legend>
                
            <table cellspacing="0" width="100%">
                <tr>
                  <th width="13%" align="left" valign="bottom">Período</th>
                  <th width="11%" align="left" valign="bottom">Qtde. padrão</th>
                  <th width="12%" align="left" valign="bottom">Cliente</th>
                  <th width="17%" align="left" valign="bottom">Data da entrega</th>
                  <th width="14%" align="left" valign="bottom">Qtde enviada</th>
                  <th width="12%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
                  <th width="11%" align="left" valign="bottom">D&eacute;bito</th>
                  <th width="10%" align="left" valign="bottom">&nbsp;</th>
                </tr>
                <?
                $result_item_cedido_periodo= mysql_query("select DISTINCT(DATE_FORMAT(data_valida, '%m/%Y'))  as periodo_valido from fi_clientes_itens_cedidos
															where id_empresa = '". $_SESSION["id_empresa"] ."'
															and   id_cliente = '". $id_cliente ."'
															and   tipo_item_cedido = '". $tipo_item_cedido ."'
															order by data_valida desc
															") or die(mysql_error());
				
				//$total_debito=0;
				//$total_enviado=0;
				
                $i=0;
                while ($rs_item_cedido_periodo= mysql_fetch_object($result_item_cedido_periodo)) {
					$qtde_padrao_aqui= pega_qtde_padrao_item_cedido($tipo_item_cedido, $id_cliente, $rs_item_cedido_periodo->periodo_valido);
					
					$total_supostamente_para_enviar+= $qtde_padrao_aqui;
					
					$periodo2= explode("/", $rs_item_cedido_periodo->periodo_valido);
				?>
                <tr id="linha_<?=$i;?>" <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
                	<td valign="top"><?= traduz_mes($periodo2[0]) ."/". $periodo2[1]; ?></td>
                    <td valign="top" align="left">
						<?= fnumi($qtde_padrao_aqui); ?>
                        
                        <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form2', 'carregaPagina&amp;pagina=financeiro/cliente_item_cedido_padrao_form&amp;acao=e&amp;id_cliente=<?= $id_cliente; ?>&amp;tipo_item_cedido=<?=$tipo_item_cedido;?>&amp;periodo=<?=$rs_item_cedido_periodo->periodo_valido;?>');">
                            <img border="0" src="images/ico_lapis.png" alt="Editar" />
                        </a>
                    </td>
                    <td valign="top"><?= pega_sigla_pessoa($id_cliente); ?></td>
                    <td valign="top" colspan="3">
                    
                    	<?
						$result_item_cedido= mysql_query("select * from fi_clientes_itens_cedidos
															where id_empresa = '". $_SESSION["id_empresa"] ."'
															and   id_cliente = '". $id_cliente ."'
															and   tipo_item_cedido = '". $tipo_item_cedido ."'
															and   DATE_FORMAT(data_valida, '%m/%Y') = '". $rs_item_cedido_periodo->periodo_valido ."'
															order by data_entrega desc
															") or die(mysql_error());
						$linhas_item_cedido= mysql_num_rows($result_item_cedido);
						
						?>
						<table cellspacing="0" width="100%">
                            <?
							$j=0;
							while ($rs_item_cedido= mysql_fetch_object($result_item_cedido)) {
								$total_enviado[$i]+= $rs_item_cedido->qtde;
								$total_enviado_geral+= $rs_item_cedido->qtde;
							?>
                            <tr id="linha_<?=$i;?>" <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
                                <td width="39%"><?= desformata_data($rs_item_cedido->data_entrega); ?></td>
                                <td width="32%"><?= fnumi($rs_item_cedido->qtde); ?></td>
                                <td width="29%">
                                    <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form', 'carregaPagina&amp;pagina=financeiro/cliente_item_cedido_form&amp;acao=e&amp;id_item_cedido=<?= $rs_item_cedido->id_item_cedido; ?>');">
                                        <img border="0" src="images/ico_lapis.png" alt="Editar" />
                                    </a>
                                    |
                                    <a href="javascript:ajaxLink('linha_<?=$i;?>', 'clienteItemCedidoExcluir&amp;id_item_cedido=<?= $rs_item_cedido->id_item_cedido; ?>&amp;tipo_item_cedido=<?= $rs_item_cedido->tipo_item_cedido; ?>&amp;id_cliente=<?= $rs_item_cedido->id_cliente; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
                                        <img border="0" src="images/ico_lixeira.png" alt="Status" />
                                    </a>
                                </td>
                            </tr>
							<?
                                $j++;
                            }
							
							$total_debito[$i]= $qtde_padrao_aqui-$total_enviado[$i];
							
                            ?>
                            <tr>
                                <td width="39%">&nbsp;</td>
                                <td width="32%"><strong><?= fnumi($total_enviado[$i]); ?></strong></td>
                                <td width="29%">&nbsp;</td>
                            </tr>
                    	</table>
                    </td>
                    
                    <td valign="top"><strong><?= fnumi($total_debito[$i]); ?></strong></td>
                    <td valign="top">&nbsp;
                    	<?
						//$result_aenviar= mysql_query("select ");
						?>
                    </td>
                </tr>
                <?
                	$i++;
				}
				
				$total_debito_geral= $total_supostamente_para_enviar-$total_enviado_geral;
				?>
                
                <tr>
                  <th width="13%" align="left" valign="bottom">&nbsp;</th>
                  <th width="11%" align="left" valign="bottom"><strong><?= fnumi($total_supostamente_para_enviar); ?></strong></th>
                  <th width="12%" align="left" valign="bottom">&nbsp;</th>
                  <th width="17%" align="left" valign="bottom">&nbsp;</th>
                  <th width="14%" align="left" valign="bottom"><strong><?= fnumi($total_enviado_geral); ?></strong></th>
                  <th width="12%" align="left" valign="bottom">&nbsp;</th>
                  <th width="11%" align="left" valign="bottom"><strong><?= fnumi($total_debito_geral); ?></strong></th>
                  <th width="10%" align="left" valign="bottom">&nbsp;</th>
                </tr>
            </table>
            
        </fieldset>
    </fieldset>
</div>

<br />
<? } ?>