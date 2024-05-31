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

    <form action="<?= AJAX_FORM; ?>formClientePecasSelecao" method="post" name="formClientePecasSelecao" id="formClientePecasSelecao" onsubmit="return ajaxForm('conteudo_interno', 'formClientePecasSelecao');">
    	
        <input type="hidden" class="escondido" name="id_cliente" id="id_cliente" value="<?=$id_cliente;?>" />
        
        <div class="parte50x">
            <ul class="recuo1">
                <li class="flutuar_esquerda tamanho130"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pecas&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">tipos de roupa</a></li>
                <li class="flutuar_esquerda tamanho200"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pecas_selecao&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">seleção dos tipos de roupa</a></li>
                <li class="flutuar_esquerda tamanho80"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_setor&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">setores</a></li>
            </ul>
            <br /><br />
            
            <fieldset>
            	<legend>Tipos de roupa</legend>
            
                
					<?
                    $result_pecas= mysql_query("select * from op_limpa_pecas
                                                where id_empresa = '". $_SESSION["id_empresa"] ."' 
												and status_peca = '1'
                                                order by peca asc
                                                ");
                    $i=0;
                    while ($rs_pecas= mysql_fetch_object($result_pecas)) {
                        $result_permissao= mysql_query("select * from fi_clientes_pecas
                                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                        and   id_cliente = '". $id_cliente ."'
                                                        and   id_peca = '". $rs_pecas->id_peca ."'
														and   status_cliente_peca = '1'
                                                        ");
                        $linhas_permissao= mysql_num_rows($result_permissao);
                        $rs_permissao= mysql_fetch_object($result_permissao);
                        
                        if (($i%2)==1) $classe= "odd";
                        else $classe= "even";
						
						$j=$i+1;
                    ?>
                    
                    <input type="hidden" class="escondido" name="nada[<?=$i;?>]" value="1" />
                    
                    <input <? if ($linhas_permissao>0) echo "checked=\"checked\""; ?> class="tamanho30" type="checkbox" name="id_peca[<?=$i;?>]" id="id_peca_<?= $rs_pecas->id_peca;?>" value="<?= $rs_pecas->id_peca;?>" />
                    <label for="id_peca_<?= $rs_pecas->id_peca;?>" class="alinhar_esquerda nao_negrito tamanho250"><?= $rs_pecas->peca;?></label>
                    
                    <?
						if (($j%3)==0) echo "<br /><br />";
						
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