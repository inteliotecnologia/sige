<?
require_once("conexao.php");
if (pode_algum("i12", $_SESSION["permissao"])) {
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($_GET["id_peca"]!="") $id_peca= $_GET["id_peca"];
	if ($_POST["id_peca"]!="") $id_peca= $_POST["id_peca"];
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<? if ($_GET["esquema"]=="2") { ?>
<h2>Especificação de dobras - Fotos</h2>
<? } ?>

<div id="conteudo_interno">

    <div class="parte50x">
        <ul class="recuo1">
            <li class="flutuar_esquerda tamanho130"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pecas&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">tipos de roupa</a></li>
            <li class="flutuar_esquerda tamanho200"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_pecas_selecao&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">seleção dos tipos de roupa</a></li>
            <li class="flutuar_esquerda tamanho80"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_setor&amp;esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>');">setores</a></li>
        </ul>
        <br /><br />
        
        <fieldset>
            <legend>Especificação de dobra</legend>
            
            <div class="parte50">
                <fieldset>
                    <legend>Formulário de cadastro</legend>
                    <div id="conteudo_form">
                        <?
                        $acao="i";
                        require_once("_financeiro/__cliente_pecas_dobra_form.php");
                        ?>
                    </div>
                </fieldset>
            </div>
            <br />
            
            <fieldset>
                <legend>Especificações de dobras cadastradas</legend>
                    
                    <table cellspacing="0" width="100%">
                    <tr>
                      <th width="9%" align="left" valign="bottom">C&oacute;d.</th>
                      <th width="17%" align="left" valign="bottom">Cliente</th>
                      <th width="14%" align="left" valign="bottom">Peça</th>
                      <th width="48%" align="left" valign="bottom">Legenda</th>
                      <th width="12%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
                    </tr>
                    <?
                    $result_peca= mysql_query("select * from fi_clientes_pecas_dobra
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   id_cliente = '". $id_cliente ."'
                                                and   id_peca = '". $id_peca ."'
                                                ") or die(mysql_error());
                    $linhas_peca= mysql_num_rows($result_peca);
                    
                    $i=0;
                    while ($rs_peca= mysql_fetch_object($result_peca)) {
                    ?>
                    <tr id="linha_<?=$i;?>" <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
                        <td valign="top"><?= $rs_peca->id_cliente_peca_dobra; ?></td>
                        <td valign="top"><?= pega_sigla_pessoa($rs_peca->id_cliente); ?></td>
                        <td valign="top"><?= pega_pecas_roupa($rs_peca->id_peca); ?></td>
                        <td valign="top">
                            <?
                            if (file_exists(CAMINHO . "cliente_peca_dobra_". $rs_peca->id_cliente_peca_dobra .".jpg")) {
                            ?>
                            <a href="javascript:void(0);" onclick="window.open('index2.php?pagina=financeiro/cliente_pecas_dobra_foto&amp;foto=cliente_peca_dobra_<?= $rs_peca->id_cliente_peca_dobra; ?>', 'cliente_peca_dobra_foto', 'width=830,height=630');">
                            	<img src="index2.php?pagina=mini&amp;foto=cliente_peca_dobra_<?= $rs_peca->id_cliente_peca_dobra; ?>&amp;l=250" alt="" width="250" />
                            </a>
                            <br /><br />
                            <? } ?>
                            <?= $rs_peca->legenda_foto; ?>
                        </td>
                        <td valign="top">
                            <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form', 'carregaPagina&amp;pagina=financeiro/cliente_pecas_dobra_form&amp;acao=e&amp;id_cliente_peca_dobra=<?= $rs_peca->id_cliente_peca_dobra; ?>');">
                                <img border="0" src="images/ico_lapis.png" alt="Editar" />
                            </a>
                            |
                            <a href="javascript:ajaxLink('linha_<?=$i;?>', 'clientePecaDobraExcluir&amp;id_cliente_peca_dobra=<?= $rs_peca->id_cliente_peca_dobra; ?>&amp;id_cliente=<?= $rs_peca->id_cliente; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
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
</div>

<? } ?>