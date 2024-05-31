<?
require_once("conexao.php");
if (pode_algum("psl", $_SESSION["permissao"])) {
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Identificação de peças/clientes</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Peça de roupa:</legend>
            
            <form action="./?pagina=qualidade/peca_cliente_busca" method="post">
    
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="a" />
                
                <label for="id_peca">Peça:</label>
                <select name="id_peca" id="id_peca" title="Peça">	  		
                    <? if ($_POST["id_peca"]=="") { ?>
                    <option selected="selected">- selecione -</option>
                    <? } ?>
                    
					<?
                    $result_pecas= mysql_query("select distinct(op_limpa_pecas.id_peca) as id_peca, peca from op_limpa_pecas, fi_clientes_pecas
                                                where op_limpa_pecas.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   op_limpa_pecas.id_peca = fi_clientes_pecas.id_peca
												and   fi_clientes_pecas.status_cliente_peca = '1'
                                                order by peca asc
                                                ");
                    $i=0;
                    while ($rs_pecas= mysql_fetch_object($result_pecas)) {
                        if (($i%2)==1) $classe= "odd";
                        else $classe= "even";
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> <? if ($_POST["id_peca"]==$rs_pecas->id_peca) echo "selected=\"selected\""; ?> value="<?= $rs_pecas->id_peca; ?>"><?= $rs_pecas->peca; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="status_pessoa">Clientes:</label>
                <select name="status_pessoa" id="status_pessoa" title="Status">	  
                	<option value="1" selected="selected" <? if ($_POST["status_pessoa"]=="1") echo "selected=\"selected\""; ?>>Ativos</option>
                    <option value="2" class="cor_sim" <? if ($_POST["status_pessoa"]=="2") echo "selected=\"selected\""; ?>>Ativos + inativos</option>
                    <option value="0" <? if ($_POST["status_pessoa"]=="0") echo "selected=\"selected\""; ?>>Inativos</option>
                </select>
                <br /><br />
                
                <center>
                	<button type="submit" id="enviar">Buscar &raquo;</button>
            	</center>
        	</form>
        </fieldset>
    </div>
    
    <? if ($_POST["id_peca"]!="") { ?>
    <div class="parte50">
    	<fieldset>
            <legend>Clientes que possuem a peça em seu enxoval:</legend>
            
            <ul class="recuo1">
            <?
			$status_pessoa= $_POST["status_pessoa"];
			
			if (($status_pessoa!="") && ($status_pessoa!="2")) {
				$str= " and   pessoas.status_pessoa = '". $status_pessoa ."' ";
			}
			
			$result_cliente= mysql_query("select distinct(fi_clientes_pecas.id_cliente) as id_cliente, pessoas.apelido_fantasia from fi_clientes_pecas, pessoas
											where fi_clientes_pecas.id_peca = '". $_POST["id_peca"] ."'
											and   fi_clientes_pecas.status_cliente_peca = '1'
											and   pessoas.id_pessoa = fi_clientes_pecas.id_cliente
											$str
											order by pessoas.apelido_fantasia asc
											");
			$i=0;
			while ($rs_cliente= mysql_fetch_object($result_cliente)) {
            ?>
            	<li>
                	<?= $rs_cliente->apelido_fantasia; ?>
                    <br /><br />
                    
                    <?
					$result_peca= mysql_query("select * from fi_clientes_pecas_dobra
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   id_cliente = '". $rs_cliente->id_cliente ."'
                                                and   id_peca = '". $_POST["id_peca"] ."'
                                                ") or die(mysql_error());
                    $linhas_peca= mysql_num_rows($result_peca);
					
					if ($linhas_peca>0) {
                    ?>
                    
                    <fieldset>
                    	<legend>Fotos - <?= $rs_cliente->apelido_fantasia; ?></legend>
                      	
                        <?
						$i=0;
	                    while ($rs_peca= mysql_fetch_object($result_peca)) {
                            if (file_exists(CAMINHO . "cliente_peca_dobra_". $rs_peca->id_cliente_peca_dobra .".jpg")) {
                            ?>
                            <img src="includes/phpthumb/phpThumb.php?src=cliente_peca_dobra_<?= $rs_peca->id_cliente_peca_dobra; ?>.jpg&amp;w=500&amp;zc=1&amp;far=T" alt="" width="500" />
                            <br /><br />
                            <? } ?>
                        <? $i++; } ?>
                        
                    </fieldset>
                    <? } ?>
                    
                    <br />
                </li>
            <? } ?>
            </ul>
        </fieldset>
    </div>
    <? } ?>
</div>

<script language="javascript">
	daFoco("id_peca");
</script>

<? } ?>