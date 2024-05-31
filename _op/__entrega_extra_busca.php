<?
require_once("conexao.php");
if (pode_algum("pl", $_SESSION["permissao"])) {
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Relatório de entregas extras</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Por ano</legend>
            
            <form action="index2.php?pagina=op/entrega_extra_relatorio" target="_blank" method="post" name="formEERelatorio" id="formEERelatorio">
    
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="a" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_percurso, '%Y')) as data_percurso2
                                                from  tr_percursos_passos
                                                where DATE_FORMAT(data_percurso, '%m/%Y') <> '00/0000'
                                                order by data_percurso desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_percurso= $rs_per->data_percurso2;
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_percurso2; ?>"><?= $data_percurso; ?></option>
                    <? $i++; } ?>
                </select>
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
                <div id="clientes_lista_1">
                    <?
                    $i=1;
                    while ($rs_clientes= mysql_fetch_object($result_clientes)) {
                    ?>
                    
                    <input checked="checked" class="tamanho15 espaco_dir" type="checkbox" name="id_cliente[]" id="id_cliente_<?= $rs_clientes->id_pessoa;?>_1" value="<?= $rs_clientes->id_pessoa;?>" />
                    <label for="id_cliente_<?= $rs_clientes->id_pessoa;?>_1" class="alinhar_esquerda menor2 nao_negrito tamanho70"><?= $rs_clientes->codigo .". ". $rs_clientes->sigla;?></label>
                    
                    <?
                        if (($i%3)==0) echo "<br /> <label>&nbsp;</label>";
                        
                        $i++;
                    }
                    ?>
                </div>
                <br />
                <label>&nbsp;</label>
                <a href="javascript:void(0);" class="menor" onclick="checarTudo('clientes_lista_1');">inverter seleção</a>
                <br /><br />
                
                <center>
                	<button type="submit" id="enviar">Enviar &raquo;</button>
            	</center>
        	</form>
        </fieldset>
    </div>
  
</div>

<? } ?>