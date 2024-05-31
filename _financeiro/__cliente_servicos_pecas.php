<?
require_once("conexao.php");
if (pode_algum("i12", $_SESSION["permissao"])) {
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	$acao="i";
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
    
    <form action="<?= AJAX_FORM; ?>formClienteServicosPecas&amp;acao=<?= $acao; ?>" method="post" name="formClienteServicosPecas" id="formClienteServicosPecas" onsubmit="return ajaxForm('conteudo_interno', 'formClienteServicosPecas', 'validacoes');">
    	
        <input class="escondido" type="hidden" id="validacoes" value="id_cliente@vazio" />
        
        <input type="hidden" class="escondido" name="id_cliente" id="id_cliente" value="<?=$id_cliente;?>" />
        
        <? if ($acao=="e") { ?>
        <input type="hidden" class="escondido" name="id_cliente_servico_geral" id="id_cliente_servico_geral" value="<?=$rs->id_cliente_servico_geral;?>" />
        <? } ?>
        
        <fieldset>
            <legend>Preço por peça</legend>
            
            <table cellspacing="0" cellpadding="0">
            	<tr>
                	<th align="left">Tipo de roupa</th>
                    <th align="left">Passadoria</th>
                    <th align="left">Costura</th>
                    <th align="left">Enxoval</th>
                </tr>
					<?
                    $result_pecas= mysql_query("select * from op_limpa_pecas, fi_clientes_pecas
                                                where fi_clientes_pecas.id_empresa = '". $_SESSION["id_empresa"] ."' 
                                                and   fi_clientes_pecas.id_peca = op_limpa_pecas.id_peca
                                                and   fi_clientes_pecas.id_cliente = '". $id_cliente ."'
                                                order by op_limpa_pecas.peca asc
                                                ");
                    $i=0;
                    while ($rs_pecas= mysql_fetch_object($result_pecas)) {
                        if (($i%2)==1) $classe= "odd";
                        else $classe= "even";
						
						$result_cliente_pecas= mysql_query("select * from fi_clientes_servicos_pecas
															where id_empresa = '". $_SESSION["id_empresa"] ."' 
															and   id_cliente = '". $id_cliente ."'
															and   id_peca = '". $rs_pecas->id_peca ."'
															");
						$rs_cliente_pecas= mysql_fetch_object($result_cliente_pecas);
                    ?>
                    <tr class="linha_baixo <?=$classe;?>">
                        <td>
                            <input type="hidden" class="escondido" name="nada[<?=$i;?>]" value="1" />
                            <input type="hidden" class="escondido" name="id_peca[<?=$i;?>]" value="<?=$rs_pecas->id_peca;?>" />
                            <?= $rs_pecas->peca;?>
                        </td>
						<td>
                            <input class="tamanho15p" title="Passadoria" name="passadoria_preco[<?=$i;?>]" id="passadoria_preco_<?=$i;?>" onkeydown="formataValor(this,event);" value="<?= fnumf_naozero($rs_cliente_pecas->passadoria_preco); ?>" />
                        </td>
                        <td>
                        	<input class="tamanho15p" title="Costura" name="costura_preco[<?=$i;?>]" id="costura_preco_<?=$i;?>" onkeydown="formataValor(this,event);" value="<?= fnumf_naozero($rs_cliente_pecas->costura_preco); ?>" />
                        </td>
                        <td>
                        	<input class="tamanho15p" title="Enxoval" name="enxoval_preco[<?=$i;?>]" id="enxoval_preco_<?=$i;?>" onkeydown="formataValor(this,event);" value="<?= fnumf_naozero($rs_cliente_pecas->enxoval_preco); ?>" />
                        </td>
                    </tr>
                    <?
                        $i++;
                    }
                    ?>
                </tr>
            </table>
            
        </fieldset>
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
        
    </form>
</div>

<? } ?>