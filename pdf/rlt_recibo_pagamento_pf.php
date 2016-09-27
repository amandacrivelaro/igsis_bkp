<?php 
   
      
   // INSTALAÇÃO DA CLASSE NA PASTA FPDF.
	require_once("../include/lib/fpdf/fpdf.php");
	
   //require '../include/';
   require_once("../funcoes/funcoesConecta.php");
   require_once("../funcoes/funcoesGerais.php");
   require_once("../funcoes/funcoesSiscontrat.php");

   //CONEXÃO COM BANCO DE DADOS 
   $conexao = bancoMysqli();

   
class PDF extends FPDF
{
// Page header
function Header()
{
	session_start();
	$inst = recuperaDados("ig_instituicao",$_SESSION['idInstituicao'],"idInstituicao");
	$logo = "../visual/img/".$inst['logo']; 
    // Logo
    $this->Image($logo,20,20,50);
    // Move to the right
    $this->Cell(80);
    $this->Image('../visual/img/logo_smc.jpg',170,10);
    // Line break
    $this->Ln(20);
}

}


//CONSULTA 
$id_ped=$_GET['id'];

$pedido = siscontrat($id_ped);
$pessoa = siscontratDocs($pedido['IdProponente'],1);

$id = $pedido['idEvento'];
$Objeto = $pedido["Objeto"];
$Periodo = $pedido["Periodo"];
$Duracao = $pedido["Duracao"];
$CargaHoraria = $pedido["CargaHoraria"];
$Local = $pedido["Local"];
$ValorGlobal = dinheiroParaBr($pedido["ValorGlobal"]);
$ValorPorExtenso = valorPorExtenso($pedido["ValorGlobal"]);
$FormaPagamento = $pedido["FormaPagamento"];
$Justificativa = $pedido["Justificativa"];
$Fiscal = $pedido["Fiscal"];
$Suplente = $pedido["Suplente"];

$Nome = $pessoa["Nome"];
$NomeArtistico = $pessoa["NomeArtistico"];
$EstadoCivil = $pessoa["EstadoCivil"];
$Nacionalidade = $pessoa["Nacionalidade"];
$DataNascimento = exibirDataBr($pessoa["DataNascimento"]);
$RG = $pessoa["RG"];
$CPF = $pessoa["CPF"];
$CCM = $pessoa["CCM"];
$OMB = $pessoa["OMB"];
$DRT = $pessoa["DRT"];
$cbo = $pessoa["cbo"];
$Funcao = $pessoa["Funcao"];
$Endereco = $pessoa["Endereco"];
$Telefones = $pessoa["Telefones"];
$Email = $pessoa["Email"];
$INSS = $pessoa["INSS"];

$ano=date('Y');


// GERANDO O PDF:
$pdf = new PDF('P','mm','A4'); //CRIA UM NOVO ARQUIVO PDF NO TAMANHO A4
$pdf->AliasNbPages();
$pdf->AddPage();

   
$x=20;
$l=7; //DEFINE A ALTURA DA LINHA   
   
   $pdf->SetXY( $x , 45 );// SetXY - DEFINE O X (largura) E O Y (altura) NA PÁGINA

   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 14);
   $pdf->Cell(180,5,utf8_decode("RECIBO DE PAGAMENTO"),0,1,'C');
   
   $pdf->Ln();
   $pdf->Ln();
   $pdf->Ln();
   $pdf->Ln();
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','', 11.5);
   $pdf->MultiCell(180,$l,utf8_decode("Recebi da Prefeitura de São Paulo - Secretaria Municipal de Cultura a importância de R$ ".$ValorGlobal." (".$ValorPorExtenso." ) referente à serviços prestados ".$Periodo."."));
   
   $pdf->Ln();
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(12,$l,'Nome:',0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(168,$l,utf8_decode($Nome));
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(28,$l,utf8_decode('Nome Artístico:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(152,$l,utf8_decode($NomeArtistico));
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(23,$l,utf8_decode('Estado Civil:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(35,$l,utf8_decode($EstadoCivil),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(28,$l,utf8_decode('Nacionalidade:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(35,$l,utf8_decode($Nacionalidade),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(10,$l,utf8_decode('CCM:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(45,$l,utf8_decode($CCM),0,1,'L');
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(8,$l,utf8_decode('RG:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(50,$l,utf8_decode($RG),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(10,$l,utf8_decode('CPF:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(53,$l,utf8_decode($CPF),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(12,$l,utf8_decode('OMB:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(45,$l,utf8_decode($OMB),0,1,'L');
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(10,$l,utf8_decode('DRT:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(40,$l,utf8_decode($DRT),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(15,$l,utf8_decode('C.B.O.:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(30,$l,utf8_decode($cbo),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(15,$l,utf8_decode('Função:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(45,$l,utf8_decode($Funcao),0,1,'L');
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(20,$l,utf8_decode('Endereço:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(160,$l,utf8_decode($Endereco));
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(17,$l,utf8_decode('Telefone:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(87,$l,utf8_decode($Telefones),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(13,$l,utf8_decode('E-mail:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(53,$l,utf8_decode($Email),0,1,'L');
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(64,$l,utf8_decode('Inscrição no INSS ou nº PIS / PASEP:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(50,$l,utf8_decode($INSS),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(36,$l,utf8_decode('Data de Nascimento:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(45,$l,utf8_decode($DataNascimento),0,1,'L');
   
   $pdf->Ln();
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(31,$l,utf8_decode('Serviço Prestado:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(149,$l,utf8_decode($Objeto));
  
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(27,$l,utf8_decode('Data / Período:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(153,$l,utf8_decode($Periodo));
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(16,$l,utf8_decode('Duração:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(164,$l,utf8_decode($Duracao."utos"));
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(12,$l,'Local:',0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(168,$l,utf8_decode($Local));
   
   $pdf->Ln();
   $pdf->Ln();
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(180,$l,utf8_decode("São Paulo, _______ de ________________________ de ".$ano."."));
   
   
//RODAPÉ PERSONALIZADO
   $pdf->SetXY($x,262);
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(100,$l,utf8_decode($Nome),'T',1,'L');
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(100,$l,"RG: ".$RG,0,0,'L');   
   
$pdf->Output();


?>