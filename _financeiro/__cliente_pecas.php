<?
require_once("conexao.php");
if (pode_algum("i12", $_SESSION["permissao"])) {
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<div id="conteudo_interno">

    <form action="<?= AJAX_FORM; ?>formClientePecas" method="post" name="formClientePecas" id="formClientePecas" onsubmit="return ajaxForm('conteudo_interno', 'formClientePecas');">
    	
        <input type="hidden" class="escondido" name="id_cliente" id="id_cliente" value="<?=$id_cliente;?>" />
        
        <div class="parte50x">
            <ul class="recuo1">
                <li class="flutuar_esquerda tamanho130"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pecas&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">tipos de roupa</a></li>
                <li class="flutuar_esquerda tamanho200"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pecas_selecao&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">seleção dos tipos de roupa</a></li>
                <li class="flutuar_esquerda tamanho80"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_setor&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">setores</a></li>
            </ul>
            <br /><br />
            
            <fieldset>
            	<legend>Observações gerais</legend>
                
                <?
				$result_pre= mysql_query("select * from pessoas
											where id_pessoa = '". $id_cliente ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
				$rs_pre= mysql_fetch_object($result_pre);
				?>
                
                <div class="parte50">
                    <label for="obs_gerais">OBS gerais:</label>
                    <textarea name="obs_gerais" id="obs_gerais" class="tamanho300 altura80"><?= $rs_pre->obs_gerais; ?></textarea>
                    <br />
                </div>
                
            </fieldset>
            
            <fieldset>
            	<legend>Tipos de roupa</legend>
            
                <table cellspacing="0" width="100%">
                	<tr>
                    	<th width="23%" align="left">Tipo</th>
                        <th width="51%" align="left">Orientações gerais</th>
                        <th width="26%" align="left">Especificações de dobra</th>
                    </tr>
					<?
                    $result_pecas= mysql_query("select * from op_limpa_pecas, fi_clientes_pecas
                                                where op_limpa_pecas.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   op_limpa_pecas.id_peca = fi_clientes_pecas.id_peca
												and   fi_clientes_pecas.id_cliente = '". $id_cliente ."'
												and   fi_clientes_pecas.status_cliente_peca = '1'
                                                order by peca asc
                                                ");
                    $i=0;
                    while ($rs_pecas= mysql_fetch_object($result_pecas)) {
                        if (($i%2)==1) $classe= "odd";
                        else $classe= "even";
                    ?>
                    <tr class="linha_baixo <?=$classe;?>">
                        <td valign="top">
                            <input type="hidden" class="escondido" name="nada[<?=$i;?>]" value="1" />
                            
                            <input type="hidden" class="escondido" name="id_peca[<?=$i;?>]" id="id_peca_<?= $rs_pecas->id_peca;?>" value="<?= $rs_pecas->id_peca;?>" />
                            <label for="id_peca_<?= $rs_pecas->id_peca;?>" class="alinhar_esquerda maior tamanho120"><?= $rs_pecas->peca;?></label>
                        </td>
                        <td valign="top">
                            <textarea id="acabamento_orientacoes_<?= $rs_pecas->id_peca;?>" name="acabamento_orientacoes[<?=$i;?>]" class="altura30"><?=$rs_pecas->acabamento_orientacoes;?></textarea>
                        </td>
                        <td valign="top">
                            <?
                            $result_especificacao= mysql_query("select * from fi_clientes_pecas_dobra
                                                                where id_cliente = '". $id_cliente ."'
                                                                and   id_peca = '". $rs_pecas->id_peca ."'
                                                                ");
                            $linhas_especificacao= mysql_num_rows($result_especificacao);
                            ?>
                            
                            <a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pecas_dobra&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>&id_peca=<?=$rs_pecas->id_peca;?>');"><?= $linhas_especificacao; ?> foto(s).</a>
                        </td>
                    </tr>
                    <?
                        $i++;
                    }
                    ?>
                </table>
                
                <br /><br />
            </fieldset>
        </div>
                
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </form>
</div>

<? } ?>