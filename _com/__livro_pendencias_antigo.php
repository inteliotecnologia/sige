<?
require_once("conexao.php");
if (pode("o", $_SESSION["permissao"])) {
	
	if ($_SESSION["id_funcionario_sessao"]!="") $id_departamento_usuario2= pega_dado_carreira("id_departamento", $_SESSION["id_funcionario_sessao"]);
	else $id_departamento_usuario2= $_SESSION["id_departamento_sessao"];
	
	switch ($_GET["parte_pendencia"]) {
		
		case 1:
		case 5:
			
			if ($_GET["tipo_pendencia"]==1) {
				if ($_GET["parte_pendencia"]==1) {
					$str_livro.= " and   ( id_departamento_principal = '". $id_departamento_usuario2 ."' ";
					
					//pegar todos os deptos do qual o usuário é responsável
					$result_permissao2= mysql_query("select * from rh_carreiras_departamentos
														where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
														and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
														and   rh_carreiras_departamentos.valido = '1'
													") or die(mysql_error());
					while ($rs_permissao2= mysql_fetch_object($result_permissao2)) {
						$str_livro.= " or   id_departamento_principal = '". $rs_permissao2->id_departamento ."' ";
					}
					//$linhas_permissao2= mysql_num_rows($result_permissao2);
					
					$str_livro.= " ) ";
				}
				else $str_livro= " and   de = '". $_SESSION["id_funcionario_sessao"] ."' and  tipo_de= 'f' ";
			}
			else {
				
				//$str_livro.= " and   ( id_departamento_principal = '". $id_departamento_usuario2 ."' ";
				//$str_livro .= " and   ( 1 = 1 ";
				//pegar todos os deptos do qual o usuário é responsável
				$result_permissao2= mysql_query("select * from rh_carreiras_departamentos
													where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
													and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   rh_carreiras_departamentos.valido = '0'
												") or die(mysql_error());
				$p=0;
				$q=0;
				
				while ($rs_permissao2= mysql_fetch_object($result_permissao2)) {
					if ($p==0) $inicio_str= "and ( ";
					else $inicio_str= " or ";
					
					if ($_GET["parte_pendencia"]==1) {
						$str_livro.= $inicio_str . " id_departamento_principal = '". $rs_permissao2->id_departamento ."' ";
					}
					else {
						$result_funcionarios_deptos= mysql_query("select rh_funcionarios.id_funcionario from rh_funcionarios, rh_carreiras, usuarios
																 	where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
																	and   rh_carreiras.atual = '1'
																	and   rh_carreiras.id_departamento = '". $rs_permissao2->id_departamento ."'
																	and   rh_funcionarios.id_funcionario = usuarios.id_funcionario
																	");
						while ($rs_funcionarios_deptos= mysql_fetch_object($result_funcionarios_deptos)) {
							if ($q==0) $inicio_str= "and ( ";
							else $inicio_str= " or ";
						
							$str_livro.= $inicio_str. " de = '". $rs_funcionarios_deptos->id_funcionario ."' ";
							
							$q++;
						}
					}
					
					$p++;
				}
				//$linhas_permissao2= mysql_num_rows($result_permissao2);
				
				if ($str_livro!="") $str_livro.= " ) ";
					
			}
			
			$result_pendencias_livro= mysql_query("select *, DATE_FORMAT(data_livro, '%Y') as ano from com_livro
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   resposta_requerida = '1'
													$str_livro
													and   id_motivo <> '34'
													and   id_motivo <> '37'
													and   id_motivo <> '41'
													and   id_motivo <> '42'
													order by  id_livro desc
													") or die(mysql_error());
			
			$linhas_pendencias_livro= mysql_num_rows($result_pendencias_livro);
			
			if ($linhas_pendencias_livro>0) { ?>
            
                <table cellspacing="0" width="100%">
                    <tr>
                      <th width="13%" align="left" valign="bottom">Data</th>
                      <th width="22%" align="left" valign="bottom">Identifica&ccedil;&atilde;o</th>
                      <th width="54%" align="left" valign="bottom">Mensagem</th>
                      <th width="11%" align="left" valign="bottom">&nbsp;</th>
                    </tr>
                    <?
                    $i=0;
                    while ($rs_pendencias_livro= mysql_fetch_object($result_pendencias_livro)) {
                        
                        $result_permissao= mysql_query("select * from com_livro_permissoes
                                                        where id_livro = '". $rs_pendencias_livro->id_livro ."'
                                                        and   id_departamento = '". $id_departamento_usuario2 ."'
                                                        ");
                        $linhas_permissao= mysql_num_rows($result_permissao);
                        
                        if ($_SESSION["id_funcionario_sessao"]!="") {
                            $result_permissao2= mysql_query("select * from rh_carreiras_departamentos, com_livro_permissoes
                                                                where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
                                                                and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                                and   rh_carreiras_departamentos.id_departamento = com_livro_permissoes.id_departamento
                                                                and   com_livro_permissoes.id_livro = '". $rs_pendencias_livro->id_livro ."'
                                                            ") or die(mysql_error());
                            $linhas_permissao2= mysql_num_rows($result_permissao2);
                        }
                        else $linhas_permissao2=0;
                        
                        
                        //se for admin, se for quem mandou ou se for um destinatário
                        if (($_SESSION["tipo_usuario"]=="a") || (($id_departamento_usuario2==$rs_pendencias_livro->de) && ($rs_pendencias_livro->tipo_de=="d")) || (($_SESSION["id_funcionario_sessao"]==$rs_pendencias_livro->de) && ($rs_pendencias_livro->tipo_de=="f")) || ($linhas_permissao>0) || ($linhas_permissao2>0)) {
                            
                            $result_lida= mysql_query("select * from com_livro_lidas
                                                        where id_livro = '". $rs_pendencias_livro->id_livro ."'
                                                        and   id_usuario = '". $_SESSION["id_usuario"] ."'
                                                        ");
                            if (mysql_num_rows($result_lida)==0) {
                                $novo="<br /><blink><span class=\"vermelho menor\"><strong>NOVO</strong></span></blink>";
                                
                                $result_ler= mysql_query("insert into com_livro_lidas
                                                    (id_empresa, id_livro, data_leitura, hora_leitura, id_usuario)
                                                    values
                                                    ('". $_SESSION["id_empresa"] ."', '". $rs_pendencias_livro->id_livro ."',
                                                     '". date("Ymd") ."', '". date("H:i:s") ."', '". $_SESSION["id_usuario"] ."')
                                                    ");
                            }
                            else $novo= "";
                            
                            if ($id_departamento_usuario2==$rs_pendencias_livro->id_departamento_principal) $classe= "cor_sim_destaque";
                            elseif ($i%2==0) $classe= "cor_sim";
                            else $classe= "cor_nao";
                    ?>
                    
                    <tr class="<?= $classe; ?>">
                        <td valign="top">
                            <?= desformata_data($rs_pendencias_livro->data_livro) ." ". $rs_pendencias_livro->hora_livro . $novo; ?>
                        </td>
                        <td valign="top">
                        <strong>De:</strong>
                        <?
                        if ($rs_pendencias_livro->tipo_de=="f") {
                            if (($rs_pendencias_livro->id_outro_departamento!="") && ($rs_pendencias_livro->id_outro_departamento!="0")) $id_departamento= $rs_pendencias_livro->id_outro_departamento;	
                            else $id_departamento= pega_dado_carreira("id_departamento", $rs_pendencias_livro->de);	
                            
                            if ($rs_pendencias_livro->de==0) echo "Sistema SiGE<br />";
                            else echo pega_funcionario($rs_pendencias_livro->de) ."<br />";
                            
                            $id_deixou= $rs_pendencias_livro->de;
                            $id_agora= $_SESSION["id_funcionario_sessao"];
                        }
                        else {
                            $id_departamento= $rs_pendencias_livro->de;
                            $id_deixou= $rs_pendencias_livro->de;
                            $id_agora= $_SESSION["id_departamento_sessao"];
                        }
                        
                        echo "<strong>". pega_departamento($id_departamento) ."</strong>";
                        ?>
                        
                        <?
                        $result_para= mysql_query("select * from com_livro_permissoes
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    and   id_livro = '". $rs_pendencias_livro->id_livro ."'
                                                    ");
                        $para= "";
                        while ($rs_para= mysql_fetch_object($result_para)) {
                            if ($rs_para->id_departamento==$rs_pendencias_livro->id_departamento_principal)
                                $para.= "- <strong><u>". pega_departamento($rs_para->id_departamento) ."</u></strong>; <br />";
                            else
                                $para.= "- ". pega_departamento($rs_para->id_departamento) ."; <br />";
                        }
                        ?>
                        <br /><br />
                        <strong>Para:</strong> <a href="javascript:void(0);" onmouseover="Tip('<?= $para; ?>');">veja a lista</a><br />
                        
                        <?
                        if ($_SESSION["tipo_usuario"]=="a") {
                            $result_quemleu= mysql_query("select * from com_livro_lidas
                                                            where id_livro = '". $rs_pendencias_livro->id_livro ."' 
                                                            order by data_leitura asc, hora_leitura asc
                                                            ");
                            $quem_leu="";
                            
                            while ($rs_quemleu= mysql_fetch_object($result_quemleu))
                                $quem_leu.= "- <strong>". pega_nome_pelo_id_usuario($rs_quemleu->id_usuario) ."</strong> em <strong>". desformata_data($rs_quemleu->data_leitura) ."</strong> às <strong>". $rs_quemleu->hora_leitura ."</strong>;<br />";
                        ?>
                        <strong>Quem leu:</strong> <a href="javascript:void(0);" onmouseover="Tip('<?= $quem_leu; ?>');">veja a lista</a><br />
                        <? } ?>
                        
                        </td>
                        <td valign="top">
                            <div id="livro_<?= $rs_pendencias_livro->id_livro; ?>">
                                <a name="livro_<?= $rs_pendencias_livro->id_livro; ?>"></a>
                                <?= $rs_pendencias_livro->mensagem; ?>
                                
                                <?
                                if (($rs_pendencias_livro->resposta!="0") && ($rs_pendencias_livro->resposta!="")) {
                                    if ($rs_pendencias_livro->tipo_resposta=="f") {
                                        $em_resposta= pega_funcionario($rs_pendencias_livro->resposta);
                                        $id_departamento_resposta= pega_dado_carreira("id_departamento", $rs_pendencias_livro->resposta);
                                        $departamento_resposta= " (". pega_departamento($id_departamento_resposta) .")";
                                    }
                                    else {
                                        $em_resposta= pega_departamento($rs_pendencias_livro->resposta);
                                        $departamento_resposta="";
                                    }
                                    
                                    $livro_original= addslashes(strip_tags(pega_livro($rs_pendencias_livro->resposta_id_livro)));
                                    //$livro_original= substr($livro_original, 0, -8);
                                ?>
                                <br /><br />
                                <span class="menor"><? if ($livro_original!="") { ?><a href="javascript:void(0);" class="contexto">Em resposta<span><?= $livro_original; ?></span></a><? } else { ?>Em resposta<? } ?> a <strong><?= $em_resposta; ?></strong> <?= $departamento_resposta; ?></span>
                                |
                                <a href="javascript:void(0);" onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=com/livro_conversa&amp;id_livro=<?= $rs_pendencias_livro->id_livro; ?>&amp;resposta_id_livro=<?= $rs_pendencias_livro->resposta_id_livro; ?>');">ver conversa</a>
                                <? } ?>
                                
                                <? if ($_SESSION["tipo_usuario"]=="a") { ?>
                                <a href="link.php?livroExcluir&amp;id_livro=<?= $rs_pendencias_livro->id_livro; ?>&amp;data=<?= $data; ?>" onclick="return confirm('Tem certeza que deseja excluir?');">
                                    <img border="0" src="images/ico_lixeira.png" alt="Status" />
                                </a>
                                <? } ?>
                                <br /><br />
                                
                                <?
                                //if ( ((($rs_pendencias_livro->id_motivo==34) || ($rs_pendencias_livro->id_motivo==37)) && ($_SESSION["tipo_usuario"]=="a")) || ( (($rs_pendencias_livro->id_motivo==34) || ($rs_pendencias_livro->id_motivo==37)) && ($id_departamento_usuario2==$rs_pendencias_livro->id_departamento_principal) ) ) {
                                if (($rs_pendencias_livro->id_motivo==34) || ($rs_pendencias_livro->id_motivo==37) || ($rs_pendencias_livro->id_motivo==41) || ($rs_pendencias_livro->id_motivo==42)) {
                                    if (($rs_pendencias_livro->reclamacao_original_id_livro!="0") && ($rs_pendencias_livro->reclamacao_original_id_livro!="")) $id_livro_aqui= $rs_pendencias_livro->reclamacao_original_id_livro;
                                    else $id_livro_aqui= $rs_pendencias_livro->id_livro;
                                    
                                    $id_situacao_atual= pega_situacao_atual_reclamacao($id_livro_aqui);
                                    
                                    if ($id_situacao_atual==0) $situacao_atual= "NENHUMA RESPOSTA";
                                    else $situacao_atual= pega_situacao_reclamacao($id_situacao_atual);
                                ?>
                                <br />
                                
                                <? if (($rs_pendencias_livro->id_motivo==34) && ($rs_pendencias_livro->reclamacao_id_cliente!=0)) { ?>
                                <span class="menor"><strong>CLIENTE:</strong> <?= pega_pessoa($rs_pendencias_livro->reclamacao_id_cliente); ?></span> <br />
                                <span class="menor caps"><strong>CAUSA:</strong> <?= pega_reclamacao_causa($rs_pendencias_livro->id_causa); ?></span> <br /><br />
                                <? } ?>
                                
                                <span class="menor"><strong>SITUAÇÃO ATUAL:</strong> <?= strtoupper($situacao_atual); ?></span> <br /><br />
                                
                                <a class="menor vermelho" href="./?pagina=qualidade/reclamacao&amp;acao=e&amp;id_livro=<?=$id_livro_aqui;?>&amp;origem=l&amp;data=<?=$data;?>"><strong>ACESSAR</strong></a>
                                <br />
                                
                                <? } elseif ($id_agora!=$id_deixou) { ?>
                                <div id="livro_resposta_<?=$rs_pendencias_livro->id_livro;?>">
                                    <a href="javascript:void(0);" onclick="respondeLivro('<?= $rs_pendencias_livro->id_livro; ?>', '<?= $id_departamento; ?>', '<?=$rs_pendencias_livro->tipo_de;?>', '<?=$rs_pendencias_livro->de;?>', '<?=$minhas;?>', '<?=$id_funcionario;?>', '<?=$parte;?>', '<?=$id_motivo;?>', '<?=$resposta_requerida;?>', '<?=desformata_data($data1);?>', '<?=desformata_data($data2);?>', '<?=$data;?>', '<?=$depto_para;?>');">responder</a>
                                </div>
                                <? } ?>
                            </div>
                        </td>
                        <td valign="top" class="menor">
                            <?
                            //só mostra aguardando resposta quando não é NC ou reclamação
                            if (($rs_pendencias_livro->id_motivo!=34) && ($rs_pendencias_livro->id_motivo!=37) && ($rs_pendencias_livro->id_motivo!=41) && ($rs_pendencias_livro->id_motivo!=42)) { ?>
                            
                                <? if ($rs_pendencias_livro->resposta_requerida==1) { ?>
                                <blink><strong class="vermelho">AGUARDANDO RESPOSTA</strong></blink><br /><br />
                                <? } elseif ($rs_pendencias_livro->resposta_requerida==2) { ?>
                                <strong class="azul">RESPONDIDO</strong><br /><br />
                                <? } ?>
                                
                            <? } ?>
                            
                            <strong><?= pega_motivo($rs_pendencias_livro->id_motivo); ?> Nº <?= fnumi($rs_pendencias_livro->num_livro)."/".$rs_pendencias_livro->ano; ?></strong><br />
                            
                            
                            <?
                            if (($rs_pendencias_livro->prioridade_dias!="") && ($rs_pendencias_livro->prioridade_dias!="0"))
                                echo "<strong>PRAZO:</strong> ". $rs_pendencias_livro->prioridade_dias ." dia(s)";
                            ?>
                        </td>
                    </tr>
                    <? $i++; } } ?>
                </table>
                
                <br /><br />
            
            <?
            }
			
			break;
			
		case 2:
			
			if ($_GET["tipo_pendencia"]=="1") {
				$str_livro2= " and   ( id_departamento_principal = '". $id_departamento_usuario2 ."' ";
				
				//pegar todos os deptos do qual o usuário é responsável
				$result_permissao2= mysql_query("select * from rh_carreiras_departamentos
													where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
													and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   rh_carreiras_departamentos.valido = '1'
												") or die(mysql_error());
				while ($rs_permissao2= mysql_fetch_object($result_permissao2)) {
					$str_livro2.= " or   id_departamento_principal = '". $rs_permissao2->id_departamento ."' ";
				}
				//$linhas_permissao2= mysql_num_rows($result_permissao2);
				
				if ($str_livro2!="") $str_livro2.= " ) ";
			}
			else {
				
				//$str_livro.= " and   ( id_departamento_principal = '". $id_departamento_usuario2 ."' ";
				//$str_livro2 .= " and   ( 1 = 1 ";
				//pegar todos os deptos do qual o usuário tem acesso
				$result_permissao2= mysql_query("select * from rh_carreiras_departamentos
													where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
													and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   rh_carreiras_departamentos.valido = '0'
												") or die(mysql_error());
				$p=0;
				while ($rs_permissao2= mysql_fetch_object($result_permissao2)) {
					if ($p==0) $inicio_str= "and ( ";
					else $inicio_str= " or ";
					
					$str_livro2.= $inicio_str. " id_departamento_principal = '". $rs_permissao2->id_departamento ."' ";	
					
					$p++;
				}
				//$linhas_permissao2= mysql_num_rows($result_permissao2);
				
				if ($str_livro2!="") $str_livro2.= " ) ";
			}
			
			
			$result_pendencias_reclamacoes= mysql_query("select *, DATE_FORMAT(data_livro, '%Y') as ano from com_livro
														where id_empresa = '". $_SESSION["id_empresa"] ."'
														$str_livro2
														and   (id_motivo = '34' or id_motivo = '37')
														and   reclamacao_original = '1'
														and   restrito <> '1'
														order by  id_livro desc
														") or die(mysql_error());
			
			$linhas_pendencias_reclamacoes= mysql_num_rows($result_pendencias_reclamacoes);
			
			if ($linhas_pendencias_reclamacoes>0) { ?>
			
			
				<table cellspacing="0" width="100%" id="tabela" class="sortable">
					<tr>
						<th width="7%" align="left">Data/hora</th>
						<th width="15%" align="left">Reclamante</th>
						<th width="15%">Depto reclamado</th>
						<th width="35%" align="left" class="unsortable">Mensagem</th>
						<th width="7%">Prazo</th>
						<th width="11%" class="unsortable">Situa&ccedil;&atilde;o</th>
					  <th width="10%" class="unsortable">Ações</th>
					</tr>
					<?
					$i=0;
					while ($rs_pendencias_reclamacoes= mysql_fetch_object($result_pendencias_reclamacoes)) {
						if (($i%2)==0) $classe= "cor_sim";
						else $classe= "cor_nao";
						
						$result_reclamacoes_acoes= mysql_query("select * from qual_reclamacoes_andamento
																where id_livro = '". $rs_pendencias_reclamacoes->id_livro ."'
																and   id_empresa = '". $_SESSION["id_empresa"] ."'
																");
						$linhas_reclamacoes_acoes= mysql_num_rows($result_reclamacoes_acoes);
						
						$result_reclamacoes_acoes_ultima= mysql_query("select * from qual_reclamacoes_andamento
																		where id_livro = '". $rs_pendencias_reclamacoes->id_livro ."'
																		and   id_empresa = '". $_SESSION["id_empresa"] ."'
																		order by id_reclamacao_andamento desc limit 1
																		");
						$rs_reclamacoes_acoes_ultima= mysql_fetch_object($result_reclamacoes_acoes_ultima);
						$linhas_reclamacoes_acoes_ultima= mysql_num_rows($result_reclamacoes_acoes_ultima);
						
						if ($rs_reclamacoes_acoes_ultima->id_situacao!=6) {
					?>
					<tr class="<?= $classe; ?> corzinha">
						<td valign="top">
							<span class="escondido"><?= $rs_pendencias_reclamacoes->data_livro ." ". $rs_pendencias_reclamacoes->hora_livro; ?></span><?= desformata_data($rs_pendencias_reclamacoes->data_livro) ."<br />". $rs_pendencias_reclamacoes->hora_livro; ?>
							<br /><br />
							<strong>Nº <?= fnumi($rs_pendencias_reclamacoes->num_livro)."/".$rs_pendencias_reclamacoes->ano; ?></strong>
							
						</td>
						<td valign="top">
						<?
						if ($rs_pendencias_reclamacoes->tipo_de=="f") {
							if (($rs_pendencias_reclamacoes->id_outro_departamento!="") && ($rs_pendencias_reclamacoes->id_outro_departamento!="0")) $id_departamento= $rs_pendencias_reclamacoes->id_outro_departamento;	
							else $id_departamento= pega_dado_carreira("id_departamento", $rs_pendencias_reclamacoes->de);	
							
							echo pega_funcionario($rs_pendencias_reclamacoes->de);
							
							$id_deixou= $rs_pendencias_reclamacoes->de;
							$id_agora= $_SESSION["id_funcionario_sessao"];
						}
						else {
							$id_departamento= $rs_pendencias_reclamacoes->de;
							$id_deixou= $rs_pendencias_reclamacoes->de;
							$id_agora= $_SESSION["id_departamento_sessao"];
						}
						?>
						<br /><br />
				
						<span class="menor"><strong><?= pega_motivo($rs_pendencias_reclamacoes->id_motivo); ?></strong></span>
						</td>
						<td valign="top" align="center">
							<?
							if ($rs_pendencias_reclamacoes->id_departamento_principal!="") echo pega_departamento($rs_pendencias_reclamacoes->id_departamento_principal);
							else echo "-";
							?>
						</td>
						<td valign="top">
							<div id="reclamacao_<?= $rs_pendencias_reclamacoes->id_livro; ?>">
								<?= $rs_pendencias_reclamacoes->mensagem; ?>
								<br />
								
								<? if (($rs_pendencias_reclamacoes->id_motivo==34) && ($rs_pendencias_reclamacoes->reclamacao_id_cliente!=0)) { ?>
								<span class="menor"><strong>CLIENTE:</strong> <?= pega_pessoa($rs_pendencias_reclamacoes->reclamacao_id_cliente); ?></span> <br />
								<span class="menor caps"><strong>CAUSA:</strong> <?= pega_reclamacao_causa($rs_pendencias_reclamacoes->id_causa); ?></span> <br />
								<? } ?>
				
							</div>
						</td>
						<td valign="top" align="center">
							<?
							if (($rs_pendencias_reclamacoes->prioridade_dias!="") && ($rs_pendencias_reclamacoes->prioridade_dias!="0")) {
								
								echo $rs_pendencias_reclamacoes->prioridade_dias ." dia(s)";
								
								switch ($rs_reclamacoes_acoes_ultima->id_situacao) {
									case 1:
									case 2:
									case 3:
									case 4:
										$data_mk_abertura= faz_mk_data($rs_pendencias_reclamacoes->data_livro);
										$data_mk_atual= faz_mk_data(date("Y-m-d"));
										$diferenca= $data_mk_atual-$data_mk_abertura;
										$dias= round(($diferenca/60/60/24));
										
										if ($dias>$rs_pendencias_reclamacoes->prioridade_dias) echo "<br /><br /><span class=\"vermelho menor\"><strong>ATRASADO</strong></span>";
									break;
								}
								
							}
							else echo "-";
							?>
						</td>
						<td valign="top" align="center">
							<?
							if ($rs_reclamacoes_acoes_ultima->id_situacao!="")
								echo pega_situacao_reclamacao($rs_reclamacoes_acoes_ultima->id_situacao) ."<br />";
							
							if ($rs_reclamacoes_acoes_ultima->nota!="") {
								$descricao_nota= pega_descricao_nota($rs_reclamacoes_acoes_ultima->nota);
								$descricao_nota= explode("@", $descricao_nota);
											
								echo " Nota ". $rs_reclamacoes_acoes_ultima->nota ." <span class=\"menor ". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span><br /><br />";
							}
							else echo "<br />";
							?>
							
							<span class="menor caps"><strong>
							<?
							if ($linhas_reclamacoes_acoes==0) echo "Nenhuma ação";
							elseif ($linhas_reclamacoes_acoes==1) echo $linhas_reclamacoes_acoes ." ação tomada";
							else echo $linhas_reclamacoes_acoes ." ações tomadas";
							?></strong>
							</span>
						</td>
						<td valign="top" align="center">
							<a id="link_edita<?=$i;?>" target="_blank" href="./?pagina=qualidade/reclamacao&amp;acao=e&amp;id_livro=<?= $rs_pendencias_reclamacoes->id_livro; ?>&amp;num_pagina=<?= $num_pagina; ?>">
								<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
								
							<? if ($_SESSION["tipo_usuario"]=="a") { ?>
							|
							<a href="link.php?livroExcluir&amp;id_livro=<?= $rs_pendencias_reclamacoes->id_livro; ?>&amp;retorno=r" onclick="return confirm('Tem certeza que deseja excluir?');">
								<img border="0" src="images/ico_lixeira.png" alt="Status" />
							</a>
							<? } ?>
									
							<? /*|
							<a href="javascript:ajaxLink('conteudo', 'livroExcluir&amp;id_livro=<?= $rs_pendencias_reclamacoes->id_livro; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
								<img border="0" src="images/ico_lixeira.png" alt="Status" />
							</a>*/ ?>
						</td>
					</tr>
					<? $i++; } } ?>
				</table>
				
				<br /><br />
			
			<? }
		break;
			
		case 3:
			if ($_GET["tipo_pendencia"]=="1") {
				$str_livro3= " and   ( id_departamento_principal = '". $id_departamento_usuario2 ."' ";
				
				//pegar todos os deptos do qual o usuário é responsável
				$result_permissao2= mysql_query("select * from rh_carreiras_departamentos
													where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
													and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   rh_carreiras_departamentos.valido = '1'
												") or die(mysql_error());
				while ($rs_permissao2= mysql_fetch_object($result_permissao2)) {
					$str_livro3.= " or   id_departamento_principal = '". $rs_permissao2->id_departamento ."' ";
				}
				//$linhas_permissao2= mysql_num_rows($result_permissao2);
				
				if ($str_livro3!="") $str_livro3.=" ) ";
			}
			else {
				//$str_livro.= " and   ( id_departamento_principal = '". $id_departamento_usuario2 ."' ";
				//$str_livro3 .= " and   ( 1 = 1 ";
				//pegar todos os deptos do qual o usuário tem acesso
				$result_permissao2= mysql_query("select * from rh_carreiras_departamentos
													where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
													and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   rh_carreiras_departamentos.valido = '0'
												") or die(mysql_error());
				$p=0;
				while ($rs_permissao2= mysql_fetch_object($result_permissao2)) {
					if ($p==0) $inicio_str= "and ( ";
					else $inicio_str= " or ";
					
					$str_livro3.= $inicio_str . " id_departamento_principal = '". $rs_permissao2->id_departamento ."' ";	
					
					$p++;
				}
				//$linhas_permissao2= mysql_num_rows($result_permissao2);
				
				if ($str_livro3!="") $str_livro3.= " ) ";
			}
			
			
			$result_pendencias_nc= mysql_query("select *, DATE_FORMAT(data_livro, '%Y') as ano from com_livro
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												$str_livro3
												and   (id_motivo = '41' or id_motivo = '42')
												and   reclamacao_original = '1'
												and   restrito <> '1'
												order by  id_livro desc
												") or die(mysql_error());
			$linhas_pendencias_nc= mysql_num_rows($result_pendencias_nc);
			
			if ($linhas_pendencias_nc>0) { ?>
			
				<table cellspacing="0" width="100%" id="tabela" class="sortable">
					<tr>
						<th width="7%" align="left">Data/hora</th>
						<th width="15%" align="left">Reclamante</th>
						<th width="15%">Depto reclamado</th>
						<th width="35%" align="left" class="unsortable">Mensagem</th>
						<th width="7%">Prazo</th>
						<th width="11%" class="unsortable">Situa&ccedil;&atilde;o</th>
					  <th width="10%" class="unsortable">Ações</th>
					</tr>
					<?
					$i=0;
					while ($rs_pendencias_nc= mysql_fetch_object($result_pendencias_nc)) {
						if (($i%2)==0) $classe= "cor_sim";
						else $classe= "cor_nao";
						
						$result_reclamacoes_acoes= mysql_query("select * from qual_reclamacoes_andamento
																where id_livro = '". $rs_pendencias_nc->id_livro ."'
																and   id_empresa = '". $_SESSION["id_empresa"] ."'
																");
						$linhas_reclamacoes_acoes= mysql_num_rows($result_reclamacoes_acoes);
						
						$result_reclamacoes_acoes_ultima= mysql_query("select * from qual_reclamacoes_andamento
																		where id_livro = '". $rs_pendencias_nc->id_livro ."'
																		and   id_empresa = '". $_SESSION["id_empresa"] ."'
																		order by id_reclamacao_andamento desc limit 1
																		");
						$rs_reclamacoes_acoes_ultima= mysql_fetch_object($result_reclamacoes_acoes_ultima);
						$linhas_reclamacoes_acoes_ultima= mysql_num_rows($result_reclamacoes_acoes_ultima);
						
						if ($rs_reclamacoes_acoes_ultima->id_situacao!=6) {
					?>
					<tr class="<?= $classe; ?> corzinha">
						<td valign="top">
							<span class="escondido"><?= $rs_pendencias_nc->data_livro ." ". $rs_pendencias_nc->hora_livro; ?></span><?= desformata_data($rs_pendencias_nc->data_livro) ."<br />". $rs_pendencias_nc->hora_livro; ?>
							<br /><br />
							<strong>Nº <?= fnumi($rs_pendencias_nc->num_livro)."/".$rs_pendencias_nc->ano; ?></strong>
							
						</td>
						<td valign="top">
						<?
						if ($rs_pendencias_nc->tipo_de=="f") {
							if (($rs_pendencias_nc->id_outro_departamento!="") && ($rs_pendencias_nc->id_outro_departamento!="0")) $id_departamento= $rs_pendencias_nc->id_outro_departamento;	
							else $id_departamento= pega_dado_carreira("id_departamento", $rs_pendencias_nc->de);	
							
							echo pega_funcionario($rs_pendencias_nc->de);
							
							$id_deixou= $rs_pendencias_nc->de;
							$id_agora= $_SESSION["id_funcionario_sessao"];
						}
						else {
							$id_departamento= $rs_pendencias_nc->de;
							$id_deixou= $rs_pendencias_nc->de;
							$id_agora= $_SESSION["id_departamento_sessao"];
						}
						?>
						<br /><br />
				
						<span class="menor"><strong><?= pega_motivo($rs_pendencias_nc->id_motivo); ?></strong></span>
						</td>
						<td valign="top" align="center">
							<?
							if ($rs_pendencias_nc->id_departamento_principal!="") echo pega_departamento($rs_pendencias_nc->id_departamento_principal);
							else echo "-";
							?>
						</td>
						<td valign="top">
							<div id="reclamacao_<?= $rs_pendencias_nc->id_livro; ?>">
								<?= $rs_pendencias_nc->mensagem; ?>
								<br />
								
								<? if (($rs_pendencias_nc->id_motivo==34) && ($rs_pendencias_nc->reclamacao_id_cliente!=0)) { ?>
								<span class="menor"><strong>CLIENTE:</strong> <?= pega_pessoa($rs_pendencias_nc->reclamacao_id_cliente); ?></span> <br />
								<span class="menor caps"><strong>CAUSA:</strong> <?= pega_reclamacao_causa($rs_pendencias_nc->id_causa); ?></span> <br />
								<? } ?>
				
							</div>
						</td>
						<td valign="top" align="center">
							<?
							if (($rs_pendencias_nc->prioridade_dias!="") && ($rs_pendencias_nc->prioridade_dias!="0")) {
								
								echo $rs_pendencias_nc->prioridade_dias ." dia(s)";
								
								switch ($rs_reclamacoes_acoes_ultima->id_situacao) {
									case 1:
									case 2:
									case 3:
									case 4:
										$data_mk_abertura= faz_mk_data($rs_pendencias_nc->data_livro);
										$data_mk_atual= faz_mk_data(date("Y-m-d"));
										$diferenca= $data_mk_atual-$data_mk_abertura;
										$dias= round(($diferenca/60/60/24));
										
										if ($dias>$rs_pendencias_nc->prioridade_dias) echo "<br /><br /><span class=\"vermelho menor\"><strong>ATRASADO</strong></span>";
									break;
								}
								
							}
							else echo "-";
							?>
						</td>
						<td valign="top" align="center">
							<?
							if ($rs_reclamacoes_acoes_ultima->id_situacao!="")
								echo pega_situacao_reclamacao($rs_reclamacoes_acoes_ultima->id_situacao) ."<br />";
							
							if ($rs_reclamacoes_acoes_ultima->nota!="") {
								$descricao_nota= pega_descricao_nota($rs_reclamacoes_acoes_ultima->nota);
								$descricao_nota= explode("@", $descricao_nota);
											
								echo " Nota ". $rs_reclamacoes_acoes_ultima->nota ." <span class=\"menor ". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span><br /><br />";
							}
							else echo "<br />";
							?>
							
							<span class="menor caps"><strong>
							<?
							if ($linhas_reclamacoes_acoes==0) echo "Nenhuma ação";
							elseif ($linhas_reclamacoes_acoes==1) echo $linhas_reclamacoes_acoes ." ação tomada";
							else echo $linhas_reclamacoes_acoes ." ações tomadas";
							?></strong>
							</span>
						</td>
						<td valign="top" align="center">
							<a id="link_edita<?=$i;?>" target="_blank" href="./?pagina=qualidade/reclamacao&amp;acao=e&amp;id_livro=<?= $rs_pendencias_nc->id_livro; ?>&amp;num_pagina=<?= $num_pagina; ?>">
								<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
								
							<? if ($_SESSION["tipo_usuario"]=="a") { ?>
							|
							<a href="link.php?livroExcluir&amp;id_livro=<?= $rs_pendencias_nc->id_livro; ?>&amp;retorno=r" onclick="return confirm('Tem certeza que deseja excluir?');">
								<img border="0" src="images/ico_lixeira.png" alt="Status" />
							</a>
							<? } ?>
									
							<? /*|
							<a href="javascript:ajaxLink('conteudo', 'livroExcluir&amp;id_livro=<?= $rs_pendencias_nc->id_livro; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
								<img border="0" src="images/ico_lixeira.png" alt="Status" />
							</a>*/ ?>
						</td>
					</tr>
					<? $i++; } } ?>
				</table>
				<br /><br />
			
			<?
			}
		break;
		
		//rms abertas
		case 4:
			
			if ($_GET["tipo_pendencia"]=="1") {
				$str_livro4= " and   man_rms.id_usuario = '". $_SESSION["id_usuario"] ."' ";
			}
			else {
				
				//$str_livro.= " and   ( id_departamento_principal = '". $id_departamento_usuario2 ."' ";
				//$str_livro4 .= " and   ( 1 = 1 ";
				//pegar todos os deptos do qual o usuário é responsável
				$result_permissao2= mysql_query("select * from rh_carreiras_departamentos
													where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
													and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   rh_carreiras_departamentos.valido = '0'
												") or die(mysql_error());
				$p=0;
				while ($rs_permissao2= mysql_fetch_object($result_permissao2)) {
					$result_funcionarios_deptos= mysql_query("select usuarios.id_usuario from rh_funcionarios, rh_carreiras, usuarios
																where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
																and   rh_carreiras.atual = '1'
																and   rh_carreiras.id_departamento = '". $rs_permissao2->id_departamento ."'
																and   rh_funcionarios.id_funcionario = usuarios.id_funcionario
																");
					while ($rs_funcionarios_deptos= mysql_fetch_object($result_funcionarios_deptos)) {
						if ($p==0) $inicio_str= "and ( ";
						else $inicio_str= " or ";
					
						$str_livro4.= $inicio_str ." man_rms.id_usuario = '". $rs_funcionarios_deptos->id_usuario ."' ";
						
						$p++;
					}
				}
				//$linhas_permissao2= mysql_num_rows($result_permissao2);
				
				if ($str_livro4!="") $str_livro4.= " ) ";
					
			}
			
			$hoje= date("d/m/Y");
			
			$data_limite_rm= soma_data($hoje, -5, 0, 0);
			
			$result_rms= mysql_query("select *
										from  man_rms, man_rms_andamento
										where man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '1'
										$str_livro4
										/* and   man_rms_andamento.data_rm_andamento <= '". formata_data($data_limite_rm) ."' */
										order by man_rms_andamento.data_rm_andamento desc, man_rms_andamento.hora_rm_andamento desc
										") or die(mysql_error());
			$linhas_rms= mysql_num_rows($result_rms);
			
			if ($linhas_rms==0) echo "Nada encontrado";
			else {
			?>
				<table cellspacing="0" width="100%" id="tabela" class="sortable">
                    <tr>
                      <th width="7%">Núm.</th>
                      <th width="18%" align="left">Data/hora</th>
                      <th width="12%" align="left">Tipo</th>
                      <th width="16%">Solicitante</th>
                      <th width="12%">Prioridade</th>
                      <th width="22%">Situa&ccedil;&atilde;o atual</th>
                      <th width="13%" class="unsortable">Ações</th>
                  </tr>
                    <?
                    $i=0;
                    while ($rs= mysql_fetch_object($result_rms)) {
                        
						$result_rm_acoes_ultima= mysql_query("select * from man_rms_andamento
																		where id_rm = '". $rs->id_rm ."'
																		and   id_empresa = '". $_SESSION["id_empresa"] ."'
																		order by id_rm_andamento desc limit 1
																		");
						$rs_rm_acoes_ultima= mysql_fetch_object($result_rm_acoes_ultima);
						$linhas_rm_acoes_ultima= mysql_num_rows($result_rm_acoes_ultima);
						
						if (($rs_rm_acoes_ultima->id_situacao==5) && (($rs_rm_acoes_ultima->nota=="0") || ($rs_rm_acoes_ultima->nota==""))) {
						
							$j= $i+1;
							
							/*$result_at= mysql_query("update man_rms
														set num_rm= '$j'
														where id_rm= '". $rs->id_rm ."'
														");*/
							
							if (($i%2)==0) $classe= "odd";
							else $classe= "even";
							
							if ( ($rs->id_usuario!=$_SESSION["id_usuario"]) && ($_SESSION["tipo_usuario"]!="a") ) {
								//$classe2= "maozinha";
								$link=1;
							}
							else $link= 0;
							
							$result_lida= mysql_query("select * from man_rms_lidas
														where id_rm = '". $rs->id_rm ."'
														and   id_usuario = '". $_SESSION["id_usuario"] ."'
														");
							if (mysql_num_rows($result_lida)==0) $novo="<span class=\"vermelho menor\"><strong>NOVO</strong></span>";
							else $novo= "";
						?>
						<tr class="<?= $classe; ?> corzinha <?=$classe2;?>" <? /*if ($link) echo "onclick=\"window.top.location.href='./?pagina=manutencao/rm&amp;acao=e&amp;id_rm=". $rs->id_rm ."';\"";*/ ?>>
							<td align="center"><?= $rs->num_rm; ?></td>
							<td><?= desformata_data($rs->data_rm_andamento) ." ". $rs->hora_rm_andamento; ?> <? if (pode("j", $_SESSION["permissao"])) echo $novo; ?></td>
							<?
							$detalhes= "<strong>Finalidade:</strong> ". pega_finalidade_rm($rs->finalidade_rm) ."<br />";
							
							if ($rs->tipo_rm=="p") $detalhes.= "<strong>Item:</strong> ". $rs->item ."<br />";
							else $detalhes.= "<strong>Equipamento:</strong> ". pega_equipamento($rs->id_equipamento) ."<br />";
							
							$detalhes.= "<strong>Área:</strong> ". $rs->area ."<br />";
							$detalhes.= "<strong>Problema:</strong> ". nl2br($rs->problema) ."<br />";
							?>
							<td><a class="contexto" href="javascript:void(0);"><?= pega_tipo_rm($rs->tipo_rm); ?><span><?= $detalhes; ?></span></a></td>
							<td align="center"><?= primeira_palavra(pega_nome_pelo_id_usuario($rs->id_usuario)); ?></td>
							<td align="center"><?= pega_prioridade_rm($rs->prioridade_dias); ?></td>
							<td align="center">
							<?
								$result_andamento_atual= mysql_query("select * from man_rms_andamento
																		where id_rm= '". $rs->id_rm ."'
																		order by id_rm_andamento
																		desc limit 1");
								$rs_andamento_atual= mysql_fetch_object($result_andamento_atual);
								
								$situacao_atual= $rs_andamento_atual->id_situacao;
								
								if ($situacao_atual==5) {
									if ((pega_nota_situacao_atual_rm($rs->id_rm)==0) || (pega_nota_situacao_atual_rm($rs->id_rm)=="")) echo "Aguardando avaliação";
									else echo pega_situacao_rm($situacao_atual);
								}
								else echo pega_situacao_rm($situacao_atual);
								
								switch ($situacao_atual) {
									case 1:
									case 2:
									case 3:
									case 6:
										//$data_abertura_rm= pega_data_abertura_rm($rs->id_rm);
										
										$data_mk_abertura= faz_mk_data($rs->data_rm_andamento);
										$data_mk_atual= faz_mk_data(date("Y-m-d"));
										$diferenca= $data_mk_atual-$data_mk_abertura;
										$dias= round(($diferenca/60/60/24));
										
										if ($dias>$rs->prioridade_dias) echo " <span class=\"vermelho menor\"><strong>ATRASADO</strong></span>";
									break;
								}
								
								
								
								if ($rs_andamento_atual->nota!="") {
									$descricao_nota= pega_descricao_nota($rs_andamento_atual->nota);
									$descricao_nota= explode("@", $descricao_nota);
												
									echo " | Nota ". $rs_andamento_atual->nota ." <span class=\"menor ". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span>";
								}
							?>
							</td>
							<td align="center">
								<? if ( (pode("j", $_SESSION["permissao"])) || ($rs->id_usuario==$_SESSION["id_usuario"])) { ?>
								<!--
								<a href="index2.php?pagina=manutencao/rm_relatorio&amp;id_rm=<?= $rs->id_rm; ?>" target="_blank">
									<img border="0" src="images/ico_pdf.png" alt="Relatório" />
								</a>
								|
								-->
								<a target="_blank" href="./?pagina=manutencao/rm&amp;acao=e&amp;id_rm=<?= $rs->id_rm; ?>">
									<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
								<? if ($_SESSION["tipo_usuario"]=="a") { ?>
								|
								<a href="javascript:ajaxLink('conteudo', 'rmExcluir&amp;id_rm=<?= $rs->id_rm; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
									<img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a>
								<? } } else echo "-"; ?>
							</td>
						</tr>
						<?
                        $i++;
					}
				}
				?>
                </table>
                <br /><br />

			
			<?
			}
		break;
	}//fim switch

}//fim pode
?>