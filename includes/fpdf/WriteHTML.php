<?
require('fpdf.php');

if ($_SESSION["id_empresa_atendente2"]=="") $_SESSION["id_empresa_atendente2"]= $_SESSION["id_empresa"];

class PDF_HTML extends FPDF
{
	var $B=0;
	var $I=0;
	var $U=0;
	var $HREF='';
	var $ALIGN='';
	
	function Header() {
		$this->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
		$this->SetY(4);
	} 

	function Footer() {
		$result_empresa= mysql_query("select * from  pessoas, rh_enderecos, empresas, cidades, ufs
								where empresas.id_empresa = '". $_SESSION["id_empresa_atendente2"] ."'
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
		$this->Cell(0, 0.4, "RELATÓRIO CRIADO POR ". VERSAO .", GERADO POR ". $_SESSION["nome"], 0, 1, 'C');
		
		$this->SetXY(17.5,28.3);
		$this->SetFont('ARIALNARROW', '', 9);
		
		$this->Cell(1.9,0.4,''.$this->PageNo().'/{nb}',0, 0, 'R');
	}

	function WriteHTML($html)
	{
		//HTML parser
		$html=str_replace("\n",' ',$html);
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				//Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				elseif($this->ALIGN == 'center')
					$this->Cell(0,0.7,$e,0,1,'C');
				else
					$this->Write(0.7,$e);
			}
			else
			{
				//Tag
				if($e{0}=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					//Extract properties
					$a2=split(' ',$e);
					$tag=strtoupper(array_shift($a2));
					$prop=array();
					foreach($a2 as $v)
						if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
							$prop[strtoupper($a3[1])]=$a3[2];
					$this->OpenTag($tag,$prop);
				}
			}
		}
	}

	function OpenTag($tag,$prop)
	{
		//Opening tag
		if($tag=='B' or $tag=='I' or $tag=='U' or $tag=='STRONG' or $tag=='EM')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF=$prop['HREF'];
		if($tag=='BR')
			$this->Ln(5);
		if($tag=='P')
		//	$this->ALIGN=$prop['ALIGN'];
			$this->Ln();
		if($tag=='HR')
		{
			if( $prop['WIDTH'] != '' )
				$Width = $prop['WIDTH'];
			else
				$Width = $this->w - $this->lMargin-$this->rMargin;
			$this->Ln(2);
			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetLineWidth(0.4);
			$this->Line($x,$y,$x+$Width,$y);
			$this->SetLineWidth(0.2);
			$this->Ln(2);
		}
	}

	function CloseTag($tag)
	{
		//Closing tag
		if (($tag=="B") || ($tag=="I") || ($tag=="STRONG") || ($tag=="EM"))
			$this->SetStyle($tag,false);
		if($tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF='';
		if($tag=='P')
			$this->ALIGN='';
	}

	function SetStyle($tag,$enable)
	{
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s)
			if($this->$s>0)
				$style.=$s;
		
		if ($enable) {
			if (($tag=="B") || ($tag=="STRONG")) $this->SetFont("ARIAL_N_NEGRITO");
			elseif (($tag=="I") || ($tag=="EM")) $this->SetFont("ARIAL_N_ITALICO");
			else $this->SetFont('ARIALNARROW','');
		}
		else {
			if (($tag=="B") || ($tag=="STRONG")) $this->SetFont("ARIALNARROW");
			elseif (($tag=="I") || ($tag=="EM")) $this->SetFont("ARIALNARROW");
			else $this->SetFont('ARIALNARROW','');
		}
		
	}

	function PutLink($URL,$txt)
	{
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
}
?>
