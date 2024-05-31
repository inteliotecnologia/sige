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

	<ul class="recuo1">
        <li class="flutuar_esquerda tamanho130"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pecas&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">tipos de roupa</a></li>
        <li class="flutuar_esquerda tamanho200"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pecas_selecao&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">seleção dos tipos de roupa</a></li>
        <li class="flutuar_esquerda tamanho80"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_setor&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">setores</a></li>
    </ul>
    <br /><br />
    
    <fieldset>
    	<legend>Setores</legend>
        
        <div class="parte50">
            <fieldset>
                <legend>Formulário de cadastro</legend>
                <div id="conteudo_form">
                    <? require_once("_financeiro/__cliente_setor_form.php"); ?>
                </div>
            </fieldset>
    	</div>
        <br />
        
        <fieldset>
            <legend>Setores cadastrados</legend>
                
                <table cellspacing="0" width="100%">
                <tr>
                  <th width="14%" align="left" valign="bottom">C&oacute;d.</th>
                  <th width="41%" align="left" valign="bottom">Cliente</th>
                  <th width="27%" align="left" valign="bottom">Setor</th>
                  <th width="18%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
                </tr>
                <?
                $result_setor= mysql_query("select * from fi_clientes_setores
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   id_cliente = '". $id_cliente ."'
                                            order by setor asc
                                            ") or die(mysql_error());
                $linhas_setor= mysql_num_rows($result_setor);
                
                $i=0;
                while ($rs_setor= mysql_fetch_object($result_setor)) {
                ?>
                <tr id="linha_<?=$i;?>" <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
                  <td><?= $rs_setor->id_cliente_setor; ?></td>
                    <td><?= pega_pessoa($rs_setor->id_cliente); ?></td>
                    <td><?= $rs_setor->setor; ?></td>
                    <td>
                        <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form', 'carregaPagina&amp;pagina=financeiro/cliente_setor_form&amp;acao=e&amp;id_cliente_setor=<?= $rs_setor->id_cliente_setor; ?>');">
                            <img border="0" src="images/ico_lapis.png" alt="Editar" />
                        </a>
                        |
                        <a href="javascript:ajaxLink('linha_<?=$i;?>', 'clienteSetorExcluir&amp;id_cliente_setor=<?= $rs_setor->id_cliente_setor; ?>&amp;id_cliente=<?= $rs_setor->id_cliente; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
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