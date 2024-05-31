<?
require_once("conexao.php");
if (pode_algum("pl", $_SESSION["permissao"])) {
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Pesquisas de satisfação</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Por período</legend>
            
            <form action="index2.php?pagina=qualidade/cliente_pesquisa_relatorio" target="_blank" method="post">
    
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="a" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_pesquisa, '%m/%Y')) as data_percurso2
                                                from  qual_pesquisa
                                                where DATE_FORMAT(data_pesquisa, '%m/%Y') <> '00/0000'
                                                order by data_pesquisa desc ");
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
                
                <label for="data1">Datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br /><br />
                
                <?
				$result_clientes= mysql_query("select * from pessoas, pessoas_tipos
												where pessoas.id_pessoa = pessoas_tipos.id_pessoa
												and   pessoas_tipos.tipo_pessoa = 'c'
												and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   pessoas.status_pessoa = '1'
												and   pessoas.id_cliente_tipo = '1'
												order by 
												pessoas.apelido_fantasia asc
												") or die(mysql_error());
				?>
				<label>Clientes:</label>
                <div id="clientes_lista">
					<?
                    $i=1;
                    while ($rs_clientes= mysql_fetch_object($result_clientes)) {
                    ?>
                    
                    <input <? if ($linhas_permissao>0) echo "checked=\"checked\""; ?> class="tamanho15 espaco_dir" type="checkbox" name="id_cliente[]" id="id_cliente_<?= $rs_clientes->id_pessoa;?>" value="<?= $rs_clientes->id_pessoa;?>" />
                    <label for="id_cliente_<?= $rs_clientes->id_pessoa;?>" class="alinhar_esquerda menor2 nao_negrito tamanho70"><?= $rs_clientes->codigo .". ". $rs_clientes->sigla;?></label>
                    
                    <?
                        if (($i%3)==0) echo "<br /> <label>&nbsp;</label>";
                        
                        $i++;
                    }
                    ?>
				</div>
				<br />
                <label>&nbsp;</label>
                <a href="javascript:void(0);" class="menor" onclick="checarTudo('clientes_lista');">inverter seleção</a>
                <br /><br />
                
                <center>
                	<button type="submit" id="enviar">Enviar &raquo;</button>
            	</center>
        	</form>
        </fieldset>
    </div>
</div>

<? } ?>