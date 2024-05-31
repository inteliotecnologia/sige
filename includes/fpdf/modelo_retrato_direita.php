<?

class PDF extends FPDF {
	
	function WriteText($text) {
		
		if ($_SESSION["id_empresa_atendente"]=="") $_SESSION["id_empresa_atendente"]= $_SESSION["id_empresa"];
		
		$intPosIni = 0;
		$intPosFim = 0;
		
		$alturaLinha= 0.6;
		
		if (strpos($text,'<')!==false && strpos($text,'[')!==false)
		{
			if (strpos($text,'<')<strpos($text,'['))
			{
				$this->Write($alturaLinha,substr($text,0,strpos($text,'<')));
				$intPosIni = strpos($text,'<');
				$intPosFim = strpos($text,'>');
				$this->SetFont('ARIAL_N_NEGRITO','');
				$this->Write($alturaLinha,substr($text,$intPosIni+1,$intPosFim-$intPosIni-1));
				$this->SetFont('ARIALNARROW','');
				$this->WriteText(substr($text,$intPosFim+1,strlen($text)));
			}
			else
			{
				$this->Write($alturaLinha,substr($text,0,strpos($text,'[')));
				$intPosIni = strpos($text,'[');
				$intPosFim = strpos($text,']');
				$w=$this->GetStringWidth('a')*($intPosFim-$intPosIni-1);
				$this->Cell($w,$this->FontSize+0.75,substr($text,$intPosIni+1,$intPosFim-$intPosIni-1),1,0,'');
				$this->WriteText(substr($text,$intPosFim+1,strlen($text)));
			}
		}
		else
		{
			if (strpos($text,'<')!==false)
			{
				$this->Write($alturaLinha,substr($text,0,strpos($text,'<')));
				$intPosIni = strpos($text,'<');
				$intPosFim = strpos($text,'>');
				$this->SetFont('ARIAL_N_NEGRITO','');
				$this->WriteText(substr($text,$intPosIni+1,$intPosFim-$intPosIni-1));
				$this->SetFont('ARIALNARROW','');
				$this->WriteText(substr($text,$intPosFim+1,strlen($text)));
			}
			elseif (strpos($text,'[')!==false)
			{
				$this->Write($alturaLinha,substr($text,0,strpos($text,'[')));
				$intPosIni = strpos($text,'[');
				$intPosFim = strpos($text,']');
				$w=$this->GetStringWidth('a')*($intPosFim-$intPosIni-1);
				$this->Cell($w,$this->FontSize+0.75,substr($text,$intPosIni+1,$intPosFim-$intPosIni-1),1,0,'');
				$this->WriteText(substr($text,$intPosFim+1,strlen($text)));
			}
			else
			{
				$this->Write($alturaLinha,$text);
			}
	
		}
	}

	
	//Page header
	function Header() {
		if ($_SESSION["id_empresa_atendente"]=="") $_SESSION["id_empresa_atendente"]= $_SESSION["id_empresa"];
		/*$result_empresa= mysql_query("select * from  pessoas, rh_enderecos, empresas, cidades, ufs
								where empresas.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   empresas.id_pessoa = pessoas.id_pessoa
								and   pessoas.id_pessoa = rh_enderecos.id_pessoa
								and   rh_enderecos.id_cidade = cidades.id_cidade
								and   cidades.id_uf = ufs.id_uf
								") or die(mysql_error());
		$rs_empresa= mysql_fetch_object($result_empresa);*/
		
		if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente"] .".jpg"))
			$this->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente"] .".jpg", 14, 1.3, 5, 1.9287);
		
		/*$this->SetXY(7,1.6);
		$this->SetFont('ARIAL_N_NEGRITO', '', 10);
		$this->Cell(2, 0.5, $rs_empresa->nome_rz , 0 , 1);
		
		$this->SetX(7);
		$this->SetFont('ARIALNARROW', '', 9);
		$this->Cell(2.9, 0.5, $rs_empresa->rua, 0 , 0);
		$this->Cell(0.6, 0.5, $rs_empresa->numero, 0 , 0);
	
		$this->Cell(3.3, 0.5, $rs_empresa->complemento, 0 , 0);
		$this->Cell(2, 0.5, "BAIRRO ". $rs_empresa->bairro, 0 , 1);
		
		$this->SetX(7);
		
		$this->Cell(1.3, 0.45, $rs_empresa->cidade .'/'. $rs_empresa->uf, 0 , 0);
	
		$this->SetX(14.3);
		$this->Cell(1, 0.45, $rs_empresa->cep, 0 , 0);
		*/
		
		$this->SetY(4);
	} 

	//Page footer
	function Footer() {
		if ($_SESSION["id_empresa_atendente"]=="") $_SESSION["id_empresa_atendente"]= $_SESSION["id_empresa"];
		
		$result_empresa= mysql_query("select * from  pessoas, rh_enderecos, empresas, cidades, ufs
								where empresas.id_empresa = '". $_SESSION["id_empresa_atendente"] ."'
								and   empresas.id_pessoa = pessoas.id_pessoa
								and   pessoas.id_pessoa = rh_enderecos.id_pessoa
								and   rh_enderecos.id_cidade = cidades.id_cidade
								and   cidades.id_uf = ufs.id_uf
								") or die(mysql_error());
		$rs_empresa= mysql_fetch_object($result_empresa);

		$this->SetXY(2,27.5);
		$this->SetFont('ARIALNARROW', '', 6);
		$this->Cell(0, 0.1, "", 'T', 1, 'C');
		$this->Cell(0, 0.35, $rs_empresa->nome_rz ." | CNPJ: ". $rs_empresa->cpf_cnpj ." | ". $rs_empresa->rua ." ". $rs_empresa->numero ." ". $rs_empresa->complemento, 0, 1, 'C');
		$this->Cell(0, 0.35, "BAIRRO ". $rs_empresa->bairro .". CEP ". $rs_empresa->cep . ". ". $rs_empresa->cidade .'/'. $rs_empresa->uf ." | ". "FONE: ". $rs_empresa->tel_res ." | SITE: ". $rs_empresa->site ." | E-MAIL: ". $rs_empresa->email, 0, 1, 'C');
		$this->SetFont('ARIAL_N_ITALICO', '', 5);
		$this->Cell(0, 0.4, "RELAT�RIO CRIADO POR ". VERSAO .", GERADO POR ". $_SESSION["nome"] ." EM ". date("d/m/Y"), 0, 1, 'C');
		
		$this->SetXY(17.5,28.3);
		$this->SetFont('ARIALNARROW', '', 9);
		
		$this->Cell(1.9,0.4,''.$this->PageNo().'/{nb}',0, 0, 'R');
		
		$_SESSION["id_empresa_atendente"]="";
	}
}
?>