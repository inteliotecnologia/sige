<?
require_once("conexao.php");
if (pode_algum("pl", $_SESSION["permissao"])) {
?>

<h2>Levantamento de peças</h2>

<div>
    <fieldset>
        <legend>Formulário de busca</legend>
        
        <form action="index2.php?pagina=op/levantamento_pecas_relatorio" method="post" target="_blank" onsubmit="return validaFormNormal('validacoes1');">

            <input class="escondido" type="hidden" id="validacoes1" value="" />
            
            <div class="parte50">
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_percurso, '%m/%Y')) as data_percurso2
                                                from  tr_percursos_passos
                                                where DATE_FORMAT(data_percurso, '%m/%Y') <> '00/0000'
                                                order by data_percurso desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_percurso= explode('/', $rs_per->data_percurso2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_percurso2; ?>"><?= traduz_mes($data_percurso[0]) .'/'. $data_percurso[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Entre datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br /><br />
                
                <label for="id_cliente">Cliente:</label>
                <select name="id_cliente" id="id_cliente" title="Cliente" <? /*onchange="procuraPedidos();" */ ?>>
                    <option value="">- SELECIONE -</option>
                    <?
                    $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                                where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'c'
                                                and   pessoas.status_pessoa = '1'
												and   pessoas.id_cliente_tipo = '1'
                                                order by 
                                                pessoas.apelido_fantasia asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_cli = mysql_fetch_object($result_cli)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="modo">Modo:</label>
                <select name="modo" id="modo" title="Modo" <? /*onchange="procuraPedidos();" */ ?>>
                    <option value="1">Peças + goma</option>
                    <option value="2">Somente peças</option>
                    <option value="3">Somente goma</option>
                </select>
                <br />
            </div>
            <div id="tudo" class="parte50">
            	<fieldset>
                	<legend>Peças</legend>
                    
                    <?
					$result_pecas= mysql_query("select * from op_limpa_pecas
												where id_empresa = '". $_SESSION["id_empresa"] ."' 
												order by peca asc
												");
					$i=1;
					while ($rs_pecas= mysql_fetch_object($result_pecas)) {
						/* $result_permissao= mysql_query("select * from fi_clientes_pecas
                                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                        and   id_cliente = '". $id_cliente ."'
                                                        and   id_peca = '". $rs_pecas->id_peca ."'
                                                        ");
                        $linhas_permissao= mysql_num_rows($result_permissao);
                        $rs_permissao= mysql_fetch_object($result_permissao); */
					?>
					
					<input checked="checked" class="tamanho15 espaco_dir" type="checkbox" name="id_peca[]" id="id_peca_<?= $rs_pecas->id_peca;?>" value="<?= $rs_pecas->id_peca;?>" />
					<label for="id_peca_<?= $rs_pecas->id_peca;?>" class="alinhar_esquerda menor nao_negrito tamanho110"><?= $rs_pecas->peca;?></label>
					
					<?
						if (($i%3)==0) echo "<br />";
						
						$i++;
					}
					?>
                    <br /><br />
                    
                    <a href="javascript:void(0);" class="menor" onclick="checarTudo('tudo');">checar/deschecar tudo</a>
                </fieldset>
            </div>
            
            <? /*
            
            <label for="extra">Tipo de entrega:</label>
            <select name="extra" id="extra" onchange="processaEntrega(this.value);">
                <option value="0">Entrega normal</option>
                <option value="1" class="cor_sim">Entrega extra</option>
            </select>
            <br />
            */ ?>
            
            <? /*
            <label for="tipo">Tipo:</label>
            <select name="tipo" id="tipo" title="tipo">
                <option value="1">Roupa limpa</option>
                <option value="2" class="cor_sim">Roupa suja</option>
            </select>
            <br />
            */ ?>
            <br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>

<? } ?>