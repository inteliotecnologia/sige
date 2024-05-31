<?
class PDF extends FPDF {
		//Page header
		function Header() {
			$this->Line(85, 0, 85, 210);
			$this->Line(170, 0, 170, 210);
			$this->Line(0, 55, 297, 55);
			$this->Line(0, 110, 297, 110);
		} 
	}
?>