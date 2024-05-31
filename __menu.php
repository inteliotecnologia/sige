<?
/*$result3= mysql_query("select * from fi_centro_custos_tipos");

while ($rs= mysql_fetch_object($result3)) {
	$result2= mysql_query("insert into fi_cc_ct
						  	(id_empresa, id_centro_custo, id_centro_custo_tipo)
							values
							('1', '". $rs->id_centro_custo ."', '". $rs->id_centro_custo_tipo ."')
							");
}*/
?>

<? if ($_SESSION["id_usuario"]!="") { ?>

<? if ($_SESSION["id_departamento_sessao"]!="") { ?>

<? if (pode('l', $_SESSION["permissao"])) { ?>
	<script language="javascript" type="text/javascript">
        shortcut.add("Alt+1",function() { abreDiv("nav1"); });
		shortcut.add("Alt+2",function() { abreDiv("nav2"); });
		shortcut.add("Alt+3",function() { abreDiv("nav3"); });
		
		shortcut.add("Alt+P",function() { window.top.location.href="./?pagina=op/pesagem_limpa&acao=i"; });
		shortcut.add("Alt+X",function() { window.top.location.href="./?pagina=op/pesagem_limpa&acao=i&extra=1"; });
		shortcut.add("Alt+L",function() { window.top.location.href="./?pagina=op/pesagem_limpa_listar"; });
		shortcut.add("Alt+B",function() { window.top.location.href="./?pagina=op/pesagem_limpa_busca"; });
		
		shortcut.add("Alt+C",function() { window.top.location.href="./?pagina=op/costura&acao=i"; });
		shortcut.add("Alt+T",function() { window.top.location.href="./?pagina=op/costura_listar"; });
		shortcut.add("Alt+S",function() { window.top.location.href="./?pagina=op/costura_busca"; });
		
		shortcut.add("Alt+G",function() { window.top.location.href="./?pagina=op/goma_listar"; });
		shortcut.add("Alt+R",function() { window.top.location.href="./?pagina=op/goma_busca"; });
		shortcut.add("Alt+H",function() { window.top.location.href="./?pagina=qualidade/peca_cliente_busca"; });
    </script>
    
    <ul id="menu_principal">
    	<li id="menu1" class="menu_vertical">
        	<a class="linkzao" href="javascript:void(0);">Pesagem (1)</a>
            <ul id="nav1" class="menu">
            	<li class="sem_submenu"><a href="./?pagina=op/pesagem_limpa&amp;acao=i">Inserir (P)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/pesagem_limpa&amp;acao=i&amp;extra=1">Inserir Extra (X)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/pesagem_limpa_listar">Listar (L)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/pesagem_limpa_busca">Buscar (B)</a></li>
                <li class="sem_submenu"><a href="./?pagina=qualidade/peca_cliente_busca">Consulta de peças (H)</a></li>
            </ul>
        </li>
        
        <li id="menu2" class="menu_vertical">
        	<a class="linkzao" href="javascript:void(0);">Costura (2)</a>
            <ul id="nav2" class="menu">
            	<li class="sem_submenu"><a href="./?pagina=op/costura&amp;acao=i">Inserir (C)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/costura_listar">Listar (T)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/costura_busca">Buscar (S)</a></li>
            </ul>
        </li>
        
        <li id="menu3" class="menu_vertical">
        	<a class="linkzao" href="javascript:void(0);">Goma (3)</a>
            <ul id="nav3" class="menu">
                <li class="sem_submenu"><a href="./?pagina=op/goma_listar">Listar (G)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/goma_busca">Buscar (R)</a></li>
            </ul>
        </li>
        
        <?
		if ($_SESSION["id_turno_sessao"]=="-3") {
			if (pode('t)', $_SESSION["permissao"])) {
		?>
		<li id="menu4" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Agenda (4)</a>
			<ul id="nav4" class="menu">
                <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=1&amp;letra=a">Fornecedores</a></li>
                <li class="submenu">
                    <a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=2&amp;letra=a&amp;status_funcionario=1">Funcionários</a>
                    <ul>
                        <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=2&amp;letra=a&amp;status_funcionario=1">Ativos</a></li>
                        <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=2&amp;letra=a&amp;status_funcionario=0">Inativos</a></li>
                        <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=2&amp;letra=a&amp;status_funcionario=-2">Outros</a></li>
                    </ul>
                </li>
                <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=4&amp;letra=a">Clientes</a></li>	
                <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=3&amp;letra=a">Outros</a></li>	
            </ul>
		</li>
		<? } } ?>
        
        
        
    </ul>
<? }//fim limpa ?>

<? if (pode('s', $_SESSION["permissao"])) { ?>
	<script language="javascript" type="text/javascript">
        shortcut.add("Alt+1",function() { abreDiv("nav1"); });
		shortcut.add("Alt+2",function() { abreDiv("nav2"); });
		shortcut.add("Alt+3",function() { abreDiv("nav3"); });
		shortcut.add("Alt+4",function() { window.top.location.href="./?pagina=op/separacao_listar"; });
		shortcut.add("Alt+5",function() { abreDiv("nav5"); });
		shortcut.add("Alt+6",function() { abreDiv("nav6"); });
		shortcut.add("Alt+7",function() { abreDiv("nav7"); });
		
		shortcut.add("Alt+R",function() { window.top.location.href="./?pagina=op/remessa&acao=i"; });
		shortcut.add("Alt+M",function() { window.top.location.href="./?pagina=op/remessa_listar"; });
		shortcut.add("Alt+B",function() { window.top.location.href="./?pagina=op/remessa_busca"; });
		
		shortcut.add("Alt+G",function() { window.top.location.href="./?pagina=op/goma&acao=i"; });
		shortcut.add("Alt+H",function() { window.top.location.href="./?pagina=op/goma_listar"; });
		shortcut.add("Alt+W",function() { window.top.location.href="./?pagina=op/goma_busca"; });
		
		shortcut.add("Alt+P",function() { window.top.location.href="./?pagina=op/pesagem_suja&acao=i"; });
		shortcut.add("Alt+T",function() { window.top.location.href="./?pagina=op/pesagem_suja_listar"; });
		shortcut.add("Alt+S",function() { window.top.location.href="./?pagina=op/pesagem_suja_busca"; });
		
		shortcut.add("Alt+L",function() { window.top.location.href="./?pagina=op/lavagem&acao=i"; });
		shortcut.add("Alt+V",function() { window.top.location.href="./?pagina=op/lavagem_listar"; });
		shortcut.add("Alt+K",function() { window.top.location.href="./?pagina=op/lavagem_busca"; });
		
		shortcut.add("Alt+D",function() { window.top.location.href="./?pagina=op/devolucao&acao=i"; });
		shortcut.add("Alt+F",function() { window.top.location.href="./?pagina=op/devolucao_listar"; });
		shortcut.add("Alt+Y",function() { window.top.location.href="./?pagina=op/devolucao_busca"; });
		
		shortcut.add("Alt+W",function() { window.top.location.href="./?pagina=op/quimico&acao=i"; });
		shortcut.add("Alt+Y",function() { window.top.location.href="./?pagina=op/quimico_listar"; });
		shortcut.add("Alt+J",function() { window.top.location.href="./?pagina=op/quimico_busca"; });
		
    </script>
    
    <ul id="menu_principal">
        
        <li id="menu1" class="menu_vertical">
        	<a class="linkzao" href="javascript:void(0);">Remessa (1)</a>
            <ul id="nav1" class="menu">
            	<li class="sem_submenu"><a href="./?pagina=op/remessa&amp;acao=i">Inserir (R)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/remessa_listar">Listar (M)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/remessa_busca">Buscar (B)</a></li>
            </ul>
        </li>
        
        <li id="menu2" class="menu_vertical">
        	<a class="linkzao" href="javascript:void(0);">Goma (2)</a>
            <ul id="nav2" class="menu">
            	<li class="sem_submenu"><a href="./?pagina=op/goma&amp;acao=i">Inserir (G)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/goma_listar">Listar (H)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/goma_busca">Busca (W)</a></li>
            </ul>
        </li>
        
        <li id="menu3" class="menu_vertical">
        	<a class="linkzao" href="javascript:void(0);">Pesagem (3)</a>
            <ul id="nav3" class="menu">
            	<li class="sem_submenu"><a href="./?pagina=op/pesagem_suja&amp;acao=i">Inserir (P)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/pesagem_suja_listar">Listar (T)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/pesagem_suja_busca">Busca (S)</a></li>
            </ul>
        </li>
        
        <li id="menu4" class="menu_vertical">
        	<a class="linkzao" href="./?pagina=op/separacao_listar">Separação (4)</a>
        </li>
        
        <li id="menu5" class="menu_vertical">
        	<a class="linkzao" href="javascript:void(0);">Lavagem (5)</a>
            <ul id="nav5" class="menu">
            	<li class="sem_submenu"><a href="./?pagina=op/lavagem&amp;acao=i">Inserir (L)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/lavagem_listar">Listar (V)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/lavagem_busca">Busca (K)</a></li>
            </ul>
        </li>
        
        <li id="menu6" class="menu_vertical">
        	<a class="linkzao" href="javascript:void(0);">Devolução (6)</a>
            <ul id="nav6" class="menu">
            	<li class="sem_submenu"><a href="./?pagina=op/devolucao&amp;acao=i">Inserir (D)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/devolucao_listar">Listar (F)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/devolucao_busca">Busca (Y)</a></li>
            </ul>
        </li>
        
        <li id="menu7" class="menu_vertical">
            <a class="linkzao" href="./?pagina=op/quimico&amp;acao=i">Químicos (7)</a>
            <ul id="nav7" class="menu">
                <li class="sem_submenu"><a href="./?pagina=op/quimico&amp;acao=i">Inserir (W)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/quimico_listar">Listar todas (Y)</a></li>
                <li class="sem_submenu"><a href="./?pagina=op/quimico_busca">Buscar (J)</a></li>
            </ul>
        </li>
        
    </ul>
<? }//fim suja ?>

<? } else { ?>
<ul id="menu_principal">
    <? if ($_SESSION["tipo_usuario"]=="a") { ?>
    <li id="menu1" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Administrativo</a>
        <ul id="nav" class="menu">
            <li class="submenu"><a href="javascript:void(0);">Empresas</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa&amp;tipo_pessoa=a&amp;acao=i">Inserir</a></li>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=a">Listar todas</a></li>
                </ul>
            </li>
            <li class="sem_submenu"><a href="./?pagina=acesso/usuario_listar">Usuários</a></li>
            <li class="sem_submenu"><a href="./?pagina=acesso/acessos">Acessos</a></li>
            <li class="sem_submenu"><a href="index2.php?pagina=acesso/backup" target="_blank" onclick="return confirm('Tem certeza que deseja fazer um backup agora?');">Fazer backup</a>
            <? /*
            <li class="submenu">
            	<a href="javascript:void(0);">Backup</a>
            	<ul>
                    <li class="sem_submenu"><a href="index2.php?pagina=acesso/backup" target="_blank" onclick="return confirm('Tem certeza que deseja fazer um backup agora?');">Fazer backup</a>
                    <li class="sem_submenu"><a href="./includes/backup/backups/" target="_blank">Salvar backup</a></li>
                </ul>
            </li>*/ ?>
        </ul>
    </li>
    <? } ?>
    <?
    if ($_SESSION["id_empresa"]!="")  {
		if (pode('uizq|', $_SESSION["permissao"])) {
	?>
    <li id="menu2" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Financeiro</a>
        <ul id="nav" class="menu">
        	<? if (pode('i', $_SESSION["permissao"])) { ?>
            <li class="submenu">
               <a href="javascript:void(0);">Duplicatas</a>
               <ul>
                    <li class="submenu">
                    	<a href="./?pagina=financeiro/nota_listar&amp;tipo_nota=r&amp;status_nota=0">À receber</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_detalhamento&amp;tipo_nota=r&acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_listar&amp;tipo_nota=r&amp;status_nota=0">Listar</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_busca&amp;tipo_nota=r&amp;status_nota=0">Buscar</a></li>
                       </ul>
                    </li>
                    
                    <li class="submenu">
                    	<a href="./?pagina=financeiro/nota_listar&amp;tipo_nota=p&amp;status_nota=0">À pagar</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_detalhamento&amp;tipo_nota=p&acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_listar&amp;tipo_nota=p&amp;status_nota=0">Listar todos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_busca&amp;tipo_nota=p&amp;status_nota=0">Buscar</a></li>
                       </ul>
                    </li>
                    
                    <li class="submenu">
                    	<a href="./?pagina=financeiro/nota_listar&amp;tipo_nota=r&amp;status_nota=1">Recebidas</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_listar&amp;tipo_nota=r&amp;status_nota=1">Listar</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_busca&amp;tipo_nota=r&amp;status_nota=1">Buscar</a></li>
                       </ul>
                    </li>
                    <li class="submenu">
                    	<a href="./?pagina=financeiro/nota_listar&amp;tipo_nota=p&amp;status_nota=1">Pagas</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_listar&amp;tipo_nota=p&amp;status_nota=1">Listar</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_busca&amp;tipo_nota=p&amp;status_nota=1">Buscar</a></li>
                       </ul>
                    </li>
                    
                    <li class="submenu">
                    	<a href="javascript:void(0);">Relatórios</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_cedente_busca&amp;tipo_nota=p">Por fornecedor</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_cedente_busca&amp;tipo_nota=r">Por cliente</a></li>
                            
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_busca&amp;tipo_nota=p&amp;status_nota=0&amp;geral=1">À pagar</a></li>
							<li class="sem_submenu"><a href="./?pagina=financeiro/nota_busca&amp;tipo_nota=r&amp;status_nota=0&amp;geral=1">À receber</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_busca&amp;tipo_nota=p&amp;status_nota=1&amp;geral=1">Pagas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_busca_cc&amp;tipo_nota=p&amp;status_nota=1&amp;geral=1">Pagas (CC fornecedor)</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/nota_busca&amp;tipo_nota=r&amp;status_nota=1&amp;geral=1">Recebidas</a></li>
                       </ul>
                    </li>
                    
                </ul>
            </li>
            <? } ?>
            <? if (pode('iz', $_SESSION["permissao"])) { ?>
            <li class="submenu">
               <a href="javascript:void(0);">Clientes</a>
               <ul>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa&amp;tipo_pessoa=c&amp;acao=i">Inserir</a></li>
                    <li class="submenu">
                    	<a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=1">Listar todos</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=1">Ativos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=0">Inativos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=3">Em vista</a></li>
                       </ul>
                    </li>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_busca&amp;tipo_pessoa=c">Buscar</a></li>
                    <li class="submenu">
                    	<a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=1">Relatórios</a>
                        <ul>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=financeiro/pessoa_situacao_relatorio&amp;tipo_pessoa=c&amp;status_pessoa=1">Ativos</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=financeiro/pessoa_situacao_relatorio&amp;tipo_pessoa=c&amp;status_pessoa=0">Inativos</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=financeiro/pessoa_situacao_relatorio&amp;tipo_pessoa=c&amp;status_pessoa=2">Geral</a></li>
                       </ul>
                    </li>
               </ul>
            </li>
            <? } ?>
            <? if (pode('iq', $_SESSION["permissao"])) { ?>
            <li class="submenu">
               <a href="javascript:void(0);">Fornecedores</a>
               <ul>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa&amp;tipo_pessoa=f&amp;acao=i">Inserir</a></li>
                    <li class="submenu">
                    	<a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=f&amp;status_pessoa=1">Listar todos</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=f&amp;status_pessoa=1">Ativos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=f&amp;status_pessoa=0">Inativos</a></li>
                       </ul>
                    </li>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_busca&amp;tipo_pessoa=f">Buscar</a></li>
                    <li class="submenu">
                    	<a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=1">Relatórios</a>
                        <ul>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=financeiro/pessoa_situacao_relatorio&amp;tipo_pessoa=f&amp;status_pessoa=1">Ativos</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=financeiro/pessoa_situacao_relatorio&amp;tipo_pessoa=f&amp;status_pessoa=0">Inativos</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=financeiro/pessoa_situacao_relatorio&amp;tipo_pessoa=f&amp;status_pessoa=2">Geral</a></li>
                       </ul>
                    </li>
                </ul>
            </li>
            <? } ?>
            <? if (pode('iq|', $_SESSION["permissao"])) { ?>
            <li class="submenu">
               <a href="javascript:void(0);">Estoque</a>
               <ul>
               		<? if (pode('iq', $_SESSION["permissao"])) { ?>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/estoque_entrada_inserir">Entrada</a></li>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/estoque_listar">Listar estoque</a></li>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/estoque_saida_inserir">Saída</a></li>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/estoque_preco">Consulta de preço</a></li>
                    <? } ?>
                    <li class="submenu">
                        <a href="javascript:void(0);">Depósitos</a>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);">Listar estoque</a>
                                <ul>
                                    <?
                                    $result_dep= mysql_query("select * from fi_depositos
                                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                                order by deposito asc ");
                                    while ($rs_dep= mysql_fetch_object($result_dep)) {
                                    ?>
                                    <li class="sem_submenu"><a href="./?pagina=financeiro/estoque_deposito_listar&amp;id_deposito=<?=$rs_dep->id_deposito;?>"><?=$rs_dep->deposito;?></a></li>
                                    <? } ?>
                               </ul>
                            </li>
                            <li class="submenu">
                                <a href="javascript:void(0);">Saída</a>
                                <ul>
                                    <?
                                    $result_dep= mysql_query("select * from fi_depositos
                                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                                order by deposito asc ");
                                    while ($rs_dep= mysql_fetch_object($result_dep)) {
                                    ?>
                                    <li class="sem_submenu"><a href="./?pagina=financeiro/estoque_deposito_saida_inserir&amp;id_deposito=<?=$rs_dep->id_deposito;?>"><?=$rs_dep->deposito;?></a></li>
                                    <? } ?>
                               </ul>
                            </li>
                        </ul>
                    </li>
                    <? if (pode('iq', $_SESSION["permissao"])) { ?>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Relatórios</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/estoque_balanco_busca">Balanço geral</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/cc_busca">Distribuição de custo</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/estoque_cc_busca">Estoque por centro de custo</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/compras_busca">Relatório de compras</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=financeiro/estoque_minimo_relatorio">Estoque mínimo</a></li>
                            
                            <!--<li class="sem_submenu"><a href="./?pagina=financeiro/estoque_relatorio2">Movimentações</a></li>-->
                       </ul>
                    </li>
                    <? } ?>
                </ul>
            </li>
			<? } ?>
            <? if (pode('iqz', $_SESSION["permissao"])) { ?>
            <li class="submenu">
               <a href="javascript:void(0);">Cadastros</a>
               <ul>
               		<? if (pode('i', $_SESSION["permissao"])) { ?>
                    <li class="submenu">
                        <a href="javascript:void(0);">Centro de custo</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/centro_custo_listar">Centro de custo</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/centro_custo_tipo_listar">Tipos</a></li>
                        </ul>
                    </li>
                    <? } ?>
                    <? if (pode('iz', $_SESSION["permissao"])) { ?>
                    <li class="submenu">
                        <a href="javascript:void(0);">Clientes</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/ad_listar&amp;tipo_pessoa=c">Aniversários</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/cliente_tipo_listar">Tipos de clientes</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="javascript:void(0);">Fornecedores</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/ad_listar&amp;tipo_pessoa=f">Aniversários</a></li>
                        </ul>
                    </li>
                    <? } ?>
                    <? if (pode('i', $_SESSION["permissao"])) { ?>
                    <li class="submenu">
                        <a href="javascript:void(0);">Motivos</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/motivo_listar&amp;tipo_motivo=q">Estoque</a></li>
                        </ul>
                    </li>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/deposito_listar">Depósitos</a></li>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/contrato_listar">Contratos</a></li>
					<? } ?>
                    <? if (pode('iq', $_SESSION["permissao"])) { ?>
                    <li class="sem_submenu"><a href="./?pagina=financeiro/item_esquema&amp;letra=a">Produtos</a></li>
                    <? } ?>
                </ul>
            </li>
            <? } ?>
            
            <? if (pode('u', $_SESSION["permissao"])) { ?>
            <li class="submenu"><a href="javascript:void(0);">Autorizações</a>
                <ul id="nav" class="menu">
                   <li class="submenu">
                       <a href="javascript:void(0);">Abastecimento</a>
                       <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/abastecimento&amp;acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/abastecimento_busca">Buscar</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/abastecimento_listar">Listar todos</a></li>	
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=financeiro/abastecimento_branco_requisicao">Vias em branco</a></li>	
                        </ul>
                   </li>
                   <li class="submenu">
                       <a href="javascript:void(0);">Refeições</a>
                       <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/refeicao&amp;acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/refeicao_busca">Buscar</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/refeicao_listar">Listar todos</a></li>		
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=financeiro/refeicao_branco_requisicao">Vias em branco</a></li>	
                        </ul>
                   </li>
                   <li class="submenu">
                       <a href="javascript:void(0);">Relatórios</a>
                       <ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/abastecimento_busca&amp;geral=1">Abastecimentos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/refeicao_busca&amp;geral=1">Refeições</a></li>
                       </ul>
                    </li>
                   <li class="submenu">
                       <a href="javascript:void(0);">Cadastros</a>
                       <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/veiculo_listar">Veículos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/motivo_listar&amp;tipo_motivo=r">Motivos p/ refeições</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <? } ?>
        </ul>
    </li>
    <? } } ?>
	<?
    if ($_SESSION["id_empresa"]!="") {
		if (pode('rv4w', $_SESSION["permissao"])) {
	?>
    <li id="menu3" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">R. Humanos</a>
        <ul id="nav" class="menu">
        	<? if (pode('rv4', $_SESSION["permissao"])) { ?>
            <li class="submenu"><a href="javascript:void(0);">Funcionários</a>
                <ul>
                    <? if (pode('rv', $_SESSION["permissao"])) { ?>
                    <li class="sem_submenu"><a href="./?pagina=rh/funcionario&amp;acao=i">Inserir</a></li>
                    <? } ?>
                    
						<? if (pode('rv4', $_SESSION["permissao"])) { ?>
                        <li class="submenu"><a href="./?pagina=rh/funcionario_listar&amp;status_funcionario=1">Listar todos</a>
                            <ul>
                                <li class="sem_submenu"><a href="./?pagina=rh/funcionario_listar&amp;status_funcionario=1">Ativos</a>
                                <li class="sem_submenu"><a href="./?pagina=rh/funcionario_listar&amp;status_funcionario=0">Inativos</a></li>
                                <li class="sem_submenu"><a href="./?pagina=rh/funcionario_listar&amp;status_funcionario=-1">Em espera</a></li>
                                <!--<li class="sem_submenu"><a href="./?pagina=rh/funcionario_listar&amp;temp=1">Ilhados</a></li>-->
                            </ul>
                        </li>
                        <? } ?>
                    <? if (pode('r', $_SESSION["permissao"])) { ?>
                    <li class="sem_submenu"><a href="./?pagina=rh/cartao_listar">Cartões</a></li>
                    <li class="sem_submenu"><a href="./?pagina=rh/ponto_log_listar">Ponto (log)</a></li>
                    <li class="sem_submenu"><a href="./?pagina=rh/desconto_busca">Descontos</a></li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Histórico interno</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/historico&amp;acao=i">Inserir</a></li>
							<li class="sem_submenu"><a href="./?pagina=rh/historico_listar">Listar todos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/historico_busca">Buscar</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Troca de escala</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/escala_troca&amp;acao=i">Inserir</a></li>
							<li class="sem_submenu"><a href="./?pagina=rh/escala_troca_listar">Listar todos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/escala_troca_busca">Buscar</a></li>
                        </ul>
                    </li>
                    <li class="submenu"><a href="javascript:void(0);">Afastamentos</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/afastamento_listar&amp;tipo_afastamento=a">Atestados</a></li>
							<li class="sem_submenu"><a href="./?pagina=rh/afastamento_listar&amp;tipo_afastamento=p">Perícias</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/afastamento_listar&amp;tipo_afastamento=f">Férias</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/afastamento_listar&amp;tipo_afastamento=o">Outros abonos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/afastamento_listar&amp;tipo_afastamento=d">Advertências</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/afastamento_listar&amp;tipo_afastamento=s">Suspensões</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/afastamento_listar&amp;tipo_afastamento=b">Abandono</a></li>
                        </ul>
                    </li>
                    <? } ?>
                </ul>	
            </li>
            <? } ?>
            <? if (pode('rw', $_SESSION["permissao"])) { ?>
            <li class="sem_submenu"><a href="./?pagina=rh/escala_busca">Escala</a></li>
            <? } ?>
            <? if (pode('r', $_SESSION["permissao"])) { ?>
            <li class="sem_submenu"><a href="./?pagina=rh/oficio_gera">Documento</a></li>
            <li class="submenu"><a href="javascript:void(0);">Cadastros</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=rh/departamento_listar">Departamentos</a></li>
                    <li class="sem_submenu"><a href="./?pagina=rh/turno_listar">Turnos</a></li>
                    <li class="sem_submenu"><a href="./?pagina=rh/cargo_listar">Cargos</a></li>
                    <li class="sem_submenu"><a href="./?pagina=rh/feriado_listar">Feriados</a></li>
                    <li class="submenu">
                        <a href="javascript:void(0);">Treinamentos</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/treinamento_listar&amp;tipo_treinamento=1">Internos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/treinamento_listar&amp;tipo_treinamento=2">Externos</a></li>
                        </ul>
                        
                    </li>
                    <li class="submenu">
                        <a href="javascript:void(0);">Vale-transporte</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/vt_linha_listar">Linhas</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="javascript:void(0);">Motivos</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/motivo_listar&amp;tipo_motivo=o">Outros abonos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/motivo_listar&amp;tipo_motivo=s">Suspensões</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/motivo_listar&amp;tipo_motivo=d">Advertências</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/motivo_listar&amp;tipo_motivo=p">Alteração no ponto</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/motivo_listar&amp;tipo_motivo=t">Descontos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/motivo_listar&amp;tipo_motivo=r">Refeições</a></li>
                        </ul>
                    </li>
                    <!--
                    <li class="submenu">
                        <a href="javascript:void(0);">Gratificações</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/item&amp;tipo_item=g&amp;acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/item_listar&amp;tipo_item=g">Listar todos</a></li>			
                        </ul>
                    </li>
                    -->
                </ul>
        	</li>
            <? } ?>
            <? if (pode('rw4', $_SESSION["permissao"])) { ?>
            <li class="submenu"><a href="javascript:void(0);">Relatórios</a>
                <ul>
                    <? if (pode('r', $_SESSION["permissao"])) { ?>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Funcionários</a>
                    	<ul>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=rh/funcionario_resumido_relatorio">Lista resumida</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=rh/funcionario_experiencia_relatorio">Período de experiência</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/funcionario_situacao_busca">Ativos/inativos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/funcionario_situacao_busca&amp;ativo_periodo=1">Ativos por período</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/idade_busca">Por faixa de idade</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=rh/funcionario_bairro_relatorio">Por bairro</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=rh/funcionario_escolaridade_relatorio">Por escolaridade</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/ferias_busca">F&eacute;rias</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/aniversariantes_busca">Aniversariantes</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/funcionario_comunicacao_busca">Filhos</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=rh/funcionario_cpf_relatorio">Nome/CPF</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=rh/funcionario_cargo_relatorio">Nome/Cargo</a></li>
                        </ul>
                    </li>
                    <li class="sem_submenu"><a href="./?pagina=rh/espelho_busca&amp;geral=1">Ponto (geral)</a></li>
                    <li class="sem_submenu"><a href="./?pagina=rh/desconto_busca&amp;geral=1">Folha</a></li>
                	<li class="sem_submenu"><a href="./?pagina=rh/afastamento_busca">Afastamentos por período</a></li>
                    <li class="sem_submenu"><a href="./?pagina=rh/atestado_busca">Atestados (quantitativo)</a></li>
                    <li class="sem_submenu"><a href="./?pagina=rh/relatorio_busca">Central de relatorios</a></li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Ponto</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/nao_faltantes_busca">Não faltantes</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/ponto_consecutivo_relatorio">Por batida consecutiva</a></li>
                        </ul>
                    </li>
                    <? } ?>
                    <? if (pode('rw4', $_SESSION["permissao"])) { ?>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Escala</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/escala_busca_relatorio">Escala</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/escala_relacao_busca_relatorio">Relação de escala</a></li>
                        </ul>
                    </li>
                    <? } ?>
                    <? if (pode('r', $_SESSION["permissao"])) { ?>
                    <li class="sem_submenu"><a href="./?pagina=rh/acompanhamento_busca_relatorio">Acompanhamento (impressão)</a></li>
                    <li class="sem_submenu"><a href="./?pagina=rh/vt_busca">Vale-transporte</a></li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Turnos</a>
                        <ul>
                            <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=rh/turno_relatorio">Relação de horários</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/turno_resumido_busca">Listagem atual</a></li>
                        </ul>
                    </li>
                    <? } ?>
                </ul>
        	</li>
            <? } ?>
        </ul>
    </li>
    <? } //fim pode rh ?>
    <?
    if ($_SESSION["id_empresa"]!="") {
		if (pode('dc35', $_SESSION["permissao"])) {
	?>
    <li id="menu3" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Documentos</a>
        <ul id="nav" class="menu">
           <? if (pode('c', $_SESSION["permissao"])) { ?>
           <li class="submenu">
               <a href="javascript:void(0);">Emissão (ADM)</a>
               <ul>
               	<?
				$vetor= pega_tipo_documento_emissao("l", 0);
				$i=2;
				while ($vetor[$i]) {
					if ($i==7) {
				?>
                    <li class="submenu">
                    	<a href="javascript:void(0);"><?= $vetor[$i]; ?></a>
                        <ul>
                        	<?
							$vetor2= pega_metodo_documento_assinado("l");
							$k=1;
							while ($vetor2[$k]) {
							?>
                            <li class="sem_submenu"><a href="./?pagina=dc/documento_emissao_listar&amp;tipo=<?= $i; ?>&amp;tipo_documento=1&amp;metodo=<?= $k; ?>"><?= $vetor2[$k]; ?></a></li>
                            <? $k++; } ?>
                       </ul>
                    </li>
                <?
					}
					else {
				?>
                   	<li class="sem_submenu"><a href="./?pagina=dc/documento_emissao_listar&amp;tipo=<?= $i; ?>&amp;tipo_documento=1"><?= $vetor[$i]; ?></a></li>
                <?
                    }
                	$i++;
				}
				?>
                </ul>
           </li>
           <? } ?>
           
		   <? if (pode('3', $_SESSION["permissao"])) { ?>
           <li class="submenu">
               <a href="javascript:void(0);">Emissão (OP)</a>
               <ul>
                   	<li class="sem_submenu"><a href="./?pagina=dc/documento_emissao_listar&amp;tipo=2&amp;tipo_documento=2">Memorando</a></li>
                    <li class="sem_submenu"><a href="./?pagina=dc/documento_emissao_listar&amp;tipo=3&amp;tipo_documento=2">CI</a></li>
                    <li class="sem_submenu"><a href="./?pagina=dc/documento_emissao_listar&amp;tipo=8&amp;tipo_documento=2">Pauta de reunião</a></li>
                    <li class="sem_submenu"><a href="./?pagina=dc/documento_emissao_listar&amp;tipo=9&amp;tipo_documento=2">Protocolo</a></li>
                    <li class="sem_submenu"><a href="./?pagina=dc/documento_emissao_listar&amp;tipo=6&amp;tipo_documento=2">Fax</a></li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Doc. assinado</a>
                        <ul>
                        	<?
							$vetor2= pega_metodo_documento_assinado("l");
							$k=1;
							while ($vetor2[$k]) {
							?>
                            <li class="sem_submenu"><a href="./?pagina=dc/documento_emissao_listar&amp;tipo=7&amp;tipo_documento=2&amp;metodo=<?= $k; ?>"><?= $vetor2[$k]; ?></a></li>
                            <? $k++; } ?>
                       </ul>
                    </li>
                </ul>
           </li>
           <? } ?>
           
		   <? if (pode('d5', $_SESSION["permissao"])) { ?>
           <li class="submenu">
                <a href="javascript:void(0);">Arquivo</a>
                
                <ul>
                   <li class="submenu">
                   		<a href="./?pagina=dc/documento_pasta_listar">Pastas</a>
                        <ul>
                            <? if (pode('d', $_SESSION["permissao"])) { ?>
                            <li class="sem_submenu"><a href="./?pagina=dc/documento_pasta&amp;acao=i">Inserir</a></li>
                            <? } ?>
                            <li class="sem_submenu"><a href="./?pagina=dc/documento_pasta_listar">Listar pastas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=dc/documento_busca">Buscar</a></li>
                        </ul>
                   </li>
                   <li class="submenu">
                   		<a href="javascript:void(0);">Documentos</a>
                        <ul>
                            <? if (pode('d', $_SESSION["permissao"])) { ?>
                            <li class="sem_submenu"><a href="./?pagina=dc/documento&amp;acao=i">Inserir</a></li>
                            <? } ?>
                            <li class="sem_submenu"><a href="./?pagina=dc/documento_busca">Buscar</a></li>
                        </ul>
                   </li>
                   <li class="sem_submenu"><a href="./?pagina=dc/documento_busca&amp;geral=1">Relatórios</a></li>
                </ul>
           </li>
           <? if (pode('d', $_SESSION["permissao"])) { ?>
           <li class="submenu">
               <a href="javascript:void(0);">Cadastros</a>
               <ul>
                    <li class="sem_submenu"><a href="./?pagina=dc/documento_pasta_listar">Pastas (arquivo)</a></li>
                </ul>
           </li>
           <? } ?>
           <? } ?>
        </ul>
    </li>
    <? } } ?>
	<?
    if ($_SESSION["id_empresa"]!="") {
		if (pode('mno', $_SESSION["permissao"])) {
	?>
    <li id="menu4" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Comunicação</a>
        <ul id="nav" class="menu">
           <? if (pode('m', $_SESSION["permissao"])) { ?>
           <li class="submenu">
               <a href="javascript:void(0);">Relatórios</a>
               <ul>
                    <li class="submenu">
                       <a href="javascript:void(0);">Funcionários</a>
                       <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/aniversariantes_busca">Aniversariantes</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/funcionario_comunicacao_busca">Relatório geral</a></li>
                       </ul>
                    </li>
                    <li class="submenu">
                       <a href="javascript:void(0);">Clientes</a>
                       <ul>
                            <li class="sem_submenu"><a href="./?pagina=qualidade/pessoa_aniversariantes_busca&amp;tipo_pessoa=c">Aniversariantes</a></li>
                       </ul>
                    </li>
                    <li class="submenu">
                       <a href="javascript:void(0);">Fornecedores</a>
                       <ul>
                            <li class="sem_submenu"><a href="./?pagina=qualidade/pessoa_aniversariantes_busca&amp;tipo_pessoa=f">Aniversariantes</a></li>
                       </ul>
                    </li>
               </ul>
           </li>
           <? } ?>
           <? if (pode('n', $_SESSION["permissao"])) { ?>
           <li class="submenu">
               <a href="javascript:void(0);">Mensagens</a>
               <ul>
                    <? /*<li class="sem_submenu"><a href="./?pagina=com/mensagem&acao=i">Nova mensagem</a></li>*/ ?>
                    <li class="sem_submenu"><a href="./?pagina=com/mensagem_listar&amp;tipo=r">Caixa de entrada</a></li>
                    <? /*<li class="sem_submenu"><a href="./?pagina=com/mensagem_listar&amp;tipo=e">Caixa de saída</a></li>*/ ?>
                </ul>
           </li>
           <? } ?>
           <? if (pode('o', $_SESSION["permissao"])) { ?>
           <li class="sem_submenu"><a href="./?pagina=com/livro">Livro</a></li>
           <? } ?>
           <? if (pode('mn', $_SESSION["permissao"])) { ?>
           <li class="sem_submenu"><a href="./?pagina=qualidade/reclamacao_nc_linha_tempo&amp;motivo=r">Linha do tempo de R/NC</a></li>
           <li class="sem_submenu"><a href="index2.php?pagina=acesso/backup" target="_blank" onclick="return confirm('Tem certeza que deseja fazer um backup agora?');">Fazer backup</a>
           <? } ?>
        </ul>
    </li>
    <? } } ?>
	<?
    if ($_SESSION["id_empresa"]!="") {
		if (pode('t)', $_SESSION["permissao"])) {
	?>
    <li id="menu5" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Contatos</a>
        <ul id="nav" class="menu">
           <? if (pode('t', $_SESSION["permissao"])) { ?>
           <li class="submenu">
               <a href="javascript:void(0);">Agenda de telefones</a>
               <ul>
                    <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=1&amp;letra=a">Fornecedores</a></li>
                    
                    <li class="submenu">
                    	<a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=2&amp;letra=a&amp;status_funcionario=1">Funcionários</a>
                    	<ul>
                            <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=2&amp;letra=a&amp;status_funcionario=1">Ativos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=2&amp;letra=a&amp;status_funcionario=0">Inativos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=2&amp;letra=a&amp;status_funcionario=-2">Outros</a></li>
                        </ul>
                    </li>
                    
                    <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=4&amp;letra=a">Clientes</a></li>	
                    <li class="sem_submenu"><a href="./?pagina=contatos/contato_esquema&amp;tipo_contato=3&amp;letra=a">Outros</a></li>	
                </ul>
           </li>
           <li class="sem_submenu"><a href="./?pagina=contatos/ligacao_listar">Controle de ligações</a></li>
           <li class="submenu">
               <a href="javascript:void(0);">Relatório</a>
               <ul>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Fornecedores</a>
                        <ul>
                            <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=1">Geral</a></li>
                            <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=1&amp;rel=s">Para supervisores</a></li>	
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Clientes</a>
                        <ul>
                            <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=4">Geral</a></li>
                            <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=4&amp;rel=s">Para supervisores</a></li>	
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Funcionários</a>
                        <ul>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=2&amp;status_funcionario=1">Ativos</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=2&amp;status_funcionario=0">Inativos</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=2&amp;status_funcionario=-2">Outros</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Outros</a>
                        <ul>
                            <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=3">Geral</a></li>
                            <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=3&amp;rel=s">Para supervisores</a></li>	
                        </ul>
                    </li>
                </ul>
           </li>
           <? } ?>
           <? if (pode(')', $_SESSION["permissao"])) { ?>
           <li class="submenu">
               <a href="javascript:void(0);">Relatório</a>
               <ul>
                    <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=1&amp;rel=s">Fornecedores</a></li>	
                    <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=4&amp;rel=s">Clientes</a></li>	
                    <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=2&amp;rel=s">Funcionários</a></li>	
                    <li class="sem_submenu"><a target="_blank" href="./index2.php?pagina=contatos/contato_relatorio&amp;tipo_contato=3&amp;rel=s">Outros</a></li>	
                </ul>
           </li>
           <? } ?>
        </ul>
    </li>
    <? } } ?>
	<?
    if ($_SESSION["id_empresa"]!="") {
		if (pode('psl', $_SESSION["permissao"])) {
	?>
    <li id="menu6" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Operacional</a>
        <ul id="nav" class="menu">
			<? if (pode('s', $_SESSION["permissao"])) { ?>
            
            <script language="javascript" type="text/javascript">
				shortcut.add("F8",function() { window.top.location.href="./?pagina=op/remessa&acao=i"; });
				shortcut.add("F9",function() { window.top.location.href="./?pagina=op/pesagem_suja&acao=i"; });
				shortcut.add("F10",function() { window.top.location.href="./?pagina=op/lavagem&acao=i"; });
			</script>
            
            <li class="submenu">
               <a href="javascript:void(0);">Área suja</a>
               <ul>
                    <li class="submenu">
                        <a href="./?pagina=op/remessa&amp;acao=i" accesskey="r">Remessa</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/remessa&amp;acao=i" accesskey="d">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/remessa_listar">Listar todas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/remessa_busca">Buscar</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="./?pagina=op/pesagem_suja&amp;acao=i">Pesagem</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/pesagem_suja&amp;acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/pesagem_suja_listar">Listar todas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/pesagem_suja_busca">Buscar</a></li>
                        </ul>
                    </li>
                    <li class="sem_submenu">
                        <a href="./?pagina=op/separacao_listar">Separação</a>
                    </li>
                    <li class="submenu">
                    	<a href="./?pagina=op/lavagem&amp;acao=i">Lavagem</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/lavagem&amp;acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/lavagem_listar">Listar todas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/lavagem_busca">Buscar</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="./?pagina=op/devolucao&amp;acao=i">Devolução</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/devolucao&amp;acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/devolucao_listar">Listar todas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/devolucao_busca">Buscar</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="./?pagina=op/quimico&amp;acao=i">Troca de químicos</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/quimico&amp;acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/quimico_listar">Listar todas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/quimico_busca">Buscar</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="./?pagina=op/goma&amp;acao=i">Gomas</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/goma&amp;acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/goma_listar">Listar todas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/goma_busca">Buscar</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <? } ?>
            <? if (pode('l', $_SESSION["permissao"])) { ?>
            
            <script language="javascript" type="text/javascript">
				//shortcut.add("F8",function() { window.top.location.href="./?pagina=op/pesagem_limpa&acao=i"; });
				//shortcut.add("F9",function() { window.top.location.href="./?pagina=op/costura&acao=i"; });
				//shortcut.add("F10",function() { window.top.location.href="./?pagina=op/lavagem&acao=i"; });
			</script>
            
            <li class="submenu">
               <a href="javascript:void(0);">Área limpa</a>
               <ul>
                    <li class="submenu">
                    	<a href="./?pagina=op/pesagem_limpa&amp;acao=i">Pesagem</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/pesagem_limpa&amp;acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/pesagem_limpa&amp;acao=i&amp;extra=1">Inserir (extra)</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/pesagem_limpa_listar">Listar todas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/pesagem_limpa_busca">Buscar</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="./?pagina=op/costura&amp;acao=i">Costura</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/costura&amp;acao=i">Inserir</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/costura_listar">Listar todas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/costura_busca">Buscar</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Gomas</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/goma_listar">Listar todas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/goma_busca">Buscar</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <? } ?>
            <? if (pode('pslw1', $_SESSION["permissao"])) { ?>
            <? if (pode('pw1', $_SESSION["permissao"])) { ?>
            <li class="submenu">
               <a href="javascript:void(0);">Cadastros</a>
               <ul>
                    <? /*<li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=1">Clientes</a></li>*/ ?>
                    <? if (pode('w', $_SESSION["permissao"])) { ?>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Escala</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=rh/escala_busca">Escala</a></li>
                            <li class="sem_submenu"><a href="./?pagina=rh/escala_relacao_busca_relatorio">Relação de escala</a></li>
                        </ul>
                    </li>
                    <? } ?>
                    <li class="sem_submenu"><a href="./?pagina=op/veiculo_listar">Veículos</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/equipamento_listar">Equipamentos</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/equipamento_tipo_listar">Equipamentos (Tipos)</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/processo_listar">Processos</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/peca_listar">Peças de roupa</a></li>
                    <? /*if (pode('1', $_SESSION["permissao"])) { ?>
                    <li class="sem_submenu"><a href="./?pagina=op/acompanhamento_item_listar">Itens - Checklist (acompanhamento)</a></li>
                    <? } */ ?>
                </ul>
            </li>
            <? } ?>
            <? } ?>
			<? if (pode('psl', $_SESSION["permissao"])) { ?>
            <li class="submenu">
               <a href="javascript:void(0);">Relatórios</a>
               <ul>
                    
                    <!--<li class="sem_submenu"><a href="./?pagina=op/coleta_busca">Formulário de coleta</a></li>-->
                    <li class="submenu">
                    	<a href="./?pagina=op/entrega_busca&amp;tipo=2">Rel. de entrega</a>
                        <ul>
                            <? /*<li class="sem_submenu"><a href="./?pagina=op/entrega_busca&amp;tipo=2">Gerar</a></li>*/ ?>
                            <li class="sem_submenu"><a href="./?pagina=op/entrega_busca5&amp;tipo=2">Gerar</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/entrega_listar">Listar todos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/entrega_busca2">Buscar</a></li>
                        </ul>
                    </li>
                    <li class="sem_submenu"><a href="./?pagina=op/entrega_coleta_busca">Entrega/coleta</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/entrega_extra_busca">Entrega extra</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/movimentacao_busca">Movimentação</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/levantamento_pecas_busca">Lev. de peças</a></li>
                    <!--<li class="sem_submenu"><a href="./?pagina=op/pedido_busca&amp;tipo=1">Pedido de lavanderia</a></li>-->
                    
                    <li class="submenu">
                    	<a href="javascript:void(0);">Área suja</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/pesagem_suja_busca&amp;geral=1">Pesagem</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/pesagem_suja_busca&amp;geral=2">Pesagem (Excel)</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/producao_suja_busca&amp;geral=1">Produção</a></li>
                            <li class="submenu">
                                <a href="javascript:void(0);">Lavagem</a>
                            	<ul>
                                	<li class="sem_submenu"><a href="./?pagina=op/lavagem_busca&amp;geral=1">Por peso</a></li>
                                    <li class="sem_submenu"><a href="./?pagina=op/lavagem_processo_busca&amp;geral=1">Por processo</a></li>
                                    <li class="sem_submenu"><a href="./?pagina=op/lavagem_maquina_parada_busca&amp;geral=1">Máquina parada</a></li>
                                    <li class="sem_submenu"><a href="./?pagina=op/lavagem_processo_perda_busca&amp;geral=1">Perda de processo</a></li>
                                </ul>
                            </li>
                            <li class="sem_submenu"><a href="./?pagina=op/devolucao_busca&amp;geral=1">Devoluções</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/quimico_busca&amp;geral=1">Troca de químicos</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/goma_busca&amp;geral=1">Gomas</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/hamper_busca&amp;geral=1">Hampers</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Área limpa</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=op/pesagem_limpa_busca&amp;geral=1">Pesagem</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/producao_busca&amp;geral=1">Produção</a></li>
                            <li class="sem_submenu"><a href="./?pagina=op/costura_busca&amp;geral=1">Costura</a></li>
                        </ul>
                    </li>
                    <li class="sem_submenu"><a href="./?pagina=op/sujidade_busca">Queda de sujidade</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/atraso_busca">Atrasos</a></li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Diversos</a>
                        <ul>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=op/tabela_cliente_relatorio&amp;status_pessoa=1">Clientes ativos</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=op/tabela_cliente_relatorio&amp;status_pessoa=0">Clientes inativos</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=op/tabela_veiculo_relatorio">Veículos</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=op/tabela_equipamento_relatorio">Máquinas</a></li>
                            <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=op/tabela_processo_relatorio">Processos</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <? } ?>
        </ul>
    </li>
    <? } } ?>
    <?
    if ($_SESSION["id_empresa"]!="") {
		if (pode('ey', $_SESSION["permissao"])) {
	?>
    <li id="menu7" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Transporte</a>
        <ul id="nav" class="menu">
           <li class="submenu">
                <a href="./?pagina=transporte/percurso&acao=i">Percursos</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=transporte/percurso&acao=i">Inserir</a></li>
                    <li class="sem_submenu"><a href="./?pagina=transporte/percurso_listar">Listar</a></li>
                    <li class="sem_submenu"><a href="./?pagina=transporte/percurso_busca">Buscar</a></li>
                </ul>
           </li>
           <? /*
           <li class="submenu">
                <a href="./?pagina=transporte/percurso&acao=i">Registro de peso (coleta)</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=transporte/remessa_cliente_peso&amp;tipo=c&amp;acao=i">Inserir</a></li>
                    <li class="sem_submenu"><a href="./?pagina=transporte/remessa_cliente_peso_listar&amp;tipo=c">Listar</a></li>
                    <li class="sem_submenu"><a href="./?pagina=transporte/remessa_cliente_peso_busca&amp;tipo=c">Buscar</a></li>
                </ul>
           </li>
           <li class="submenu">
                <a href="./?pagina=transporte/percurso&acao=i">Registro de peso (entrega)</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=transporte/remessa_cliente_peso&amp;tipo=e&amp;acao=i">Inserir</a></li>
                    <li class="sem_submenu"><a href="./?pagina=transporte/remessa_cliente_peso_listar&amp;tipo=e">Listar</a></li>
                    <li class="sem_submenu"><a href="./?pagina=transporte/remessa_cliente_peso_busca&amp;tipo=e">Buscar</a></li>
                </ul>
           </li> */ ?>
           <li class="submenu">
                <a href="./?pagina=transporte/percurso&acao=i">Vistoria</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=transporte/vistoria&acao=i">Inserir</a></li>
                    <li class="sem_submenu"><a href="./?pagina=transporte/vistoria_listar">Listar</a></li>
                    <li class="sem_submenu"><a href="./?pagina=transporte/vistoria_busca">Buscar</a></li>
                </ul>
           </li>
           <? if (pode('e', $_SESSION["permissao"])) { ?>
           <li class="submenu">
                <a href="javascript:void(0);">Cadastros</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=transporte/cronograma_listar">Cronograma</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/veiculo_listar">Veículos</a></li>
                    <li class="sem_submenu"><a href="./?pagina=transporte/vistoria_item_listar">Itens de vistoria</a></li>
                </ul>
           </li>
           <? } ?>
           <li class="submenu">
               <a href="javascript:void(0);">Relatórios</a>
               <ul>
                   <li class="sem_submenu"><a href="./?pagina=transporte/cronograma_busca&amp;geral=1">Cronograma</a></li>
                   <li class="sem_submenu"><a href="./?pagina=transporte/percurso_busca&amp;geral=1">Percursos</a></li>
                   <!--<li class="sem_submenu"><a href="./?pagina=transporte/vistoria_busca">Vistorias</a></li>-->
               </ul>
           </li>
        </ul>
    </li>
    <? } } ?>
    
    <?
    if ($_SESSION["id_empresa"]!="") {
		if (pode('kj', $_SESSION["permissao"])) {
	?>
    <li id="menu8" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Manutenção</a>
        <ul id="nav" class="menu">
           <li class="submenu">
                <a href="./?pagina=manutencao/rm_listar&acao=i">RM</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/rm&amp;acao=i">Inserir</a></li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Listar</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=manutencao/rm_listar">Ordem</a></li>
                            <!--<li class="sem_submenu"><a href="./?pagina=manutencao/rm_listar&amp;seleciona=1">Equipamento</a></li>-->
                        </ul>
                    </li>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/rm_busca">Buscar</a></li>
                </ul>
           </li>
           <? if (pode('kj', $_SESSION["permissao"])) { ?>
           <li class="submenu">
                <a href="./?pagina=manutencao/os_listar&acao=i">OS</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/os&amp;acao=i">Inserir</a></li>
                    <li class="submenu">
                    	<a href="javascript:void(0);">Listar</a>
                        <ul>
                            <li class="sem_submenu"><a href="./?pagina=manutencao/os_listar">Ordem</a></li>
                            <!--<li class="sem_submenu"><a href="./?pagina=manutencao/os_listar&amp;seleciona=1">Equipamento</a></li>-->
                        </ul>
                    </li>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/os_busca">Buscar</a></li>
                </ul>
           </li>
           <li class="submenu">
               <a href="./?pagina=manutencao/checklist_busca">Checklist</a>
               <ul>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/checklist_busca">Preencher</a></li>
                </ul>
           </li>
           <li class="submenu">
                <a href="javascript:void(0);">Cadastros</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/tecnico_listar">Técnicos</a></li>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/servico_tipo_listar">Serviços (Tipos)</a></li>
                    <!--<li class="sem_submenu"><a href="./?pagina=op/equipamento_listar">Equipamentos</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/equipamento_tipo_listar">Equipamentos (Tipos)</a></li>-->
                    <li class="sem_submenu"><a href="./?pagina=op/acompanhamento_item_listar">Itens de checklist</a></li>
                </ul>
           </li>
           <li class="submenu">
                <a href="javascript:void(0);">Relatórios</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/rm_busca&amp;geral=1">RM</a></li>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/os_busca&amp;geral=1">OS</a></li>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/os_rm_busca&amp;geral=1">OS x RM</a></li>
                    <li class="sem_submenu"><a href="./?pagina=manutencao/checklist_busca&amp;geral=1">Checklist</a></li>
                    <? /*<li class="sem_submenu"><a href="./?pagina=manutencao/graficos">Gráficos</a></li>*/ ?>
                </ul>
           </li>
           <? } ?>
        </ul>
    </li>
    <? } } ?>
    
    <?
    if ($_SESSION["id_empresa"]!="") {
		if (pode('12', $_SESSION["permissao"])) {
	?>
    <li id="menu8" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Qualidade</a>
        <ul id="nav" class="menu">
           <li class="submenu">
                <a href="javascript:void(0);">Clientes</a>
                <ul>
                	<li class="sem_submenu"><a href="./?pagina=financeiro/pessoa&amp;tipo_pessoa=c&amp;status_pessoa=3&amp;acao=i">Inserir</a></li>
                    <li class="submenu">
                    	<a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c">Listar</a>
						<ul>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=1">Ativos</a></li>
                            <!--<li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=0">Inativos</a></li>-->
                            <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=3">Em vista</a></li>
                       </ul>
                       <? /*
                        <ul>
							<?
                            $result_contrato= mysql_query("select * from fi_contratos
                                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                            order by id_contrato asc ");
                            $i=0;
                            while ($rs_contrato = mysql_fetch_object($result_contrato)) {
                            ?>
                            <li class="sem_submenu"><a href="./?pagina=financeiro/pessoa_listar&amp;tipo_pessoa=c&amp;status_pessoa=1&amp;id_contrato=<?=$rs_contrato->id_contrato;?>"><?=$rs_contrato->contrato;?></a></li>
                            <? $i++; } ?>
                        </ul>*/ ?>
                    </li>
                </ul>
           </li>
           <li class="submenu">
                <a href="javascript:void(0);">Costura</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/costura_conserto&amp;acao=i">Inserir</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/costura_conserto_listar">Listar</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/costura_conserto_busca">Buscar</a></li>
                </ul>
           </li>
           <li class="submenu">
                <a href="javascript:void(0);">Recl./NC</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/reclamacao_listar">Listar</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/reclamacao_busca">Buscar</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/reclamacao_nc_linha_tempo">Linha do tempo</a></li>
                </ul>
           </li>
           <li class="sem_submenu"><a href="./?pagina=qualidade/peca_cliente_busca">Peças (consulta)</a></li>
           <? /*
           <li class="submenu">
                <a href="javascript:void(0);">NC</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/reclamacao_listar&amp;motivo=n">Listar</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/reclamacao_busca&amp;motivo=n">Buscar</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/reclamacao_nc_linha_tempo&amp;motivo=n">Linha do tempo</a></li>
                </ul>
           </li>
           */ ?>
		   <li class="submenu">
                <a href="javascript:void(0);">Relatórios</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/costura_conserto_busca&amp;geral=1">Costura</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/reclamacao_nc_busca&amp;geral=1">NC/Reclamações</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/cliente_pesquisa_busca&amp;geral=1">Pesquisas</a></li>
                    <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=op/tabela_cliente_relatorio&amp;status_pessoa=1">Clientes ativos</a></li>
                    <li class="sem_submenu"><a target="_blank" href="index2.php?pagina=op/tabela_cliente_relatorio&amp;status_pessoa=3">Clientes em vista</a></li>
                </ul>
           </li>
           <? if (pode('1', $_SESSION["permissao"])) { ?>
           <li class="submenu">
                <a href="javascript:void(0);">Cadastros</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/causa_listar">Causas</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/pesquisa_categoria_listar">Pesquisa - categorias</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/pesquisa_item_listar">Pesquisa - itens</a></li>
                </ul>
           </li>
           <? } ?>
        </ul>
    </li>
    <? } } ?>
    
    <?
    if ($_SESSION["id_empresa"]!="") {
		if (pode('(', $_SESSION["permissao"])) {
	?>
    <li id="menu8" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Costura</a>
        <ul id="nav" class="menu">
           
           <li class="submenu">
                <a href="./?pagina=op/costura&amp;acao=i">Área limpa</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=op/costura&amp;acao=i">Inserir</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/costura_listar">Listar todas</a></li>
                    <li class="sem_submenu"><a href="./?pagina=op/costura_busca">Buscar</a></li>
                </ul>
            </li>
           
           <li class="submenu">
                <a href="javascript:void(0);">Costura</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/costura_conserto&amp;acao=i">Inserir</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/costura_conserto_listar">Listar</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/costura_conserto_busca">Buscar</a></li>
                </ul>
           </li>
           
           <li class="submenu">
                <a href="javascript:void(0);">Relatórios</a>
                <ul>
                    <li class="sem_submenu"><a href="./?pagina=op/costura_busca&amp;geral=1">Área limpa</a></li>
                    <li class="sem_submenu"><a href="./?pagina=qualidade/costura_conserto_busca&amp;geral=1">Costura - consertos</a></li>

                </ul>
           </li>
        </ul>
    </li>
    <? } } ?>
    
	<? /*
    <li id="menu4" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Documentos</a>
        <ul id="nav" class="menu">
           <li class="sem_submenu"><a href="#">ATA - Reunião</a></li>
           <li class="sem_submenu"><a href="#">Comunicação Interna (CI)</a></li>
           <li class="sem_submenu"><a href="#">Memorando (MEM)</a></li>
           <li class="sem_submenu"><a href="#">Ofício (OFI)</a></li>
           <li class="sem_submenu"><a href="#">FAX</a></li>
        </ul>
    </li>
    <li id="menu5" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Relatórios</a>
        <ul id="nav" class="menu">
           <li class="sem_submenu"><a href="#">Espelho do Cartão</a></li>
           <li class="sem_submenu"><a href="#">Funcionários Ativos</a></li>
           <li class="sem_submenu"><a href="#">Atestados por Período</a></li>
           <li class="sem_submenu"><a href="#">Turnos</a></li>
           <li class="sem_submenu"><a href="#">Relatório de funcionários</a></li>
           <li class="submenu">
            <a href="#">Funcionários por data de admissão</a>
            	<ul>
                    <li class="sem_submenu"><a href="./?esq=lateral&pagina=relatorio_data_admissao&status=1">Ativos</a></li>
                    <li class="sem_submenu"><a href="./?esq=lateral&pagina=relatorio_data_admissao&status=0">Inativos</a></li>	
                </ul>
           
           </li>
        </ul>
    </li>
	*/ ?>
    <?
	/*
    if (pode_algum("ogf", $_SESSION["permissao"])) {
		if ($_SESSION["tipo_usuario"]=='a') $acao_tipo= "buscar";
		else $acao_tipo= "listar";
	?>
    <li id="menu6" class="menu_vertical"><a class="linkzao" href="javascript:void(0);">Acompanhamentos</a>
        <ul id="nav" class="menu">
           <li class="submenu"><a href="javascript:void(0);">Ordens de serviço</a>
           		<ul>
                    <li class="sem_submenu"><a href="./?pagina=os/os_inserir">Nova ordem de serviço</a></li>
                    <li class="sem_submenu"><a href="./?pagina=os/os_listar">Listar ordens de serviço</a></li>
                </ul>
           </li>
           <li class="submenu"><a href="javascript:void(0);">Água</a>
           		<ul>
                    <li class="sem_submenu"><a href="./?pagina=agua/cp_<?= $acao_tipo; ?>">Checklist parcial</a></li>
                    <li class="sem_submenu"><a href="./?pagina=agua/ct_<?= $acao_tipo; ?>">Checklist total</a></li>
                    <li class="sem_submenu"><a href="./?pagina=agua/mce_<?= $acao_tipo; ?>">Mapa de coleta</a></li>
                    <li class="sem_submenu"><a href="./?pagina=agua/ml_<?= $acao_tipo; ?>">Mapa de limpeza</a></li>
                    <li class="sem_submenu"><a href="./?pagina=agua/acl_<?= $acao_tipo; ?>">Acompanhamento de limpeza química</a></li>
                    <li class="sem_submenu"><a href="./?pagina=agua/vt_<?= $acao_tipo; ?>">Visitas técnicas</a></li>
                </ul>
           </li>
           <li class="sem_submenu"><a href="./?pagina=fluxo/fluxo_<?= $acao_tipo; ?>">Fluxo laminar</a></li>
        </ul>
    </li>
    <? } //fim ogf */ ?>
	<? } //fim id_empresa ?>
</ul>
<? } ?>

<script language="javascript" type="text/javascript">
	shortcut.add("Alt+O",function() { window.top.location.href="./?pagina=com/livro"; });
	shortcut.add("Alt+Q",function() { window.top.location.href="./index2.php?pagina=logout"; });
</script>

<?
}
else {
	$erro_a= 3;
	include("__erro_acesso.php");
}
?>