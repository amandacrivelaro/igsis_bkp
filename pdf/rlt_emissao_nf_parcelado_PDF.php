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
	/*
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
*/
}


//CONSULTA 
$id_ped=$_GET['id'];
$ano=date('Y');

dataPagamento($id_ped);

$pedido = siscontrat($id_ped);
$pj = siscontratDocs($pedido['IdProponente'],2);
$ex = siscontratDocs($pedido['IdExecutante'],1);
$rep01 = siscontratDocs($pj['Representante01'],3);
$rep02 = siscontratDocs($pj['Representante02'],3);
$parcelamento = retornaParcelaPagamento($id_ped);

$id_parcela = $_GET['parcela'];

$valorParcela = $parcelamento[$id_parcela]['valor'];
$ValorPorExtenso = valorPorExtenso(($parcelamento[$id_parcela]['valor']));


$id = $pedido['idEvento'];
$Objeto = $pedido["Objeto"];
$Periodo = $pedido["Periodo"];
$ValorGlobal = dinheiroParaBr($pedido["ValorGlobal"]);
$FormaPagamento = $pedido["FormaPagamento"];
$notaFiscal = $pedido["notaFiscal"];
$descricaoNF = $pedido["descricaoNF"];
$notaempenho = $pedido["NotaEmpenho"];
$data_entrega_empenho = exibirDataBr($pedido['EntregaNE']);

//PessoaJuridica

$pjRazaoSocial = $pj["Nome"];
$pjCNPJ = $pj['CNPJ'];


// Executante

$exNome = $ex["Nome"];
$exRG = $ex["RG"];
$exCPF = $ex["CPF"];


// Representante01

$rep01Nome = $rep01["Nome"];
$rep01EstadoCivil = $rep01["EstadoCivil"];
$rep01Nacionalidade = $rep01["Nacionalidade"];
$rep01RG = $rep01["RG"];
$rep01CPF = $rep01["CPF"];


// Representante02

$rep02Nome = $rep02["Nome"];
$rep02EstadoCivil = $rep02["EstadoCivil"];
$rep02Nacionalidade = $rep02["Nacionalidade"];
$rep02RG = $rep02["RG"];
$rep02CPF = $rep02["CPF"];


// GERANDO O PDF:
$pdf = new PDF('P','mm','A4'); //CRIA UM NOVO ARQUIVO PDF NO TAMANHO A4
$pdf->AliasNbPages();
$pdf->AddPage();

   
$x=20;
$l=6; //DEFINE A ALTURA DA LINHA   
   
   $pdf->SetXY( $x , 30 );// SetXY - DEFINE O X (largura) E O Y (altura) NA PÁGINA

   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 14);
   $pdf->Cell(180,5,utf8_decode($pjRazaoSocial),0,1,'L');
   
   $pdf->Ln();
   $pdf->Ln();
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(180,$l,utf8_decode("Segue abaixo os dados para emissão da Nota Fiscal:"),0,1,'L');
   
   $pdf->Ln();
   $pdf->Ln();
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(17,$l,utf8_decode("Sacado:"),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(180,$l,utf8_decode("Secretaria Municipal de Cultura"),0,1,'L');
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(12,$l,utf8_decode('CNPJ:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(65,$l,utf8_decode("49.269.244/0001-63"),0,1,'L');
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(20,$l,utf8_decode('Endereço:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(180,$l,utf8_decode("Av. São João, 473 - 11º andar - CEP: 01035-000"),0,1,'L');  
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(20,$l,utf8_decode('Município:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(50,$l,utf8_decode("São Paulo"),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(15,$l,utf8_decode('Estado:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(50,$l,utf8_decode("São Paulo"),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(17,$l,utf8_decode('I. Est. Nº:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(50,$l,utf8_decode("Isento"),0,0,'L');       
      
   $pdf->Ln();
   $pdf->Ln();
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(22,$l,'Nota Fiscal:',0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(65,$l,utf8_decode($notaFiscal),0,1,'L');
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(12,$l,'Valor:',0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(168,$l,utf8_decode("R$ $valorParcela"."  "."($ValorPorExtenso )"));
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(21,$l,utf8_decode("Descrição:"),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(159,$l,utf8_decode($descricaoNF));
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(40,$l,utf8_decode("Pagamento referente ao"),0,0,'L');
   $pdf->SetFont('Arial','B', 10);
   $pdf->MultiCell(140,$l,utf8_decode($Objeto));
   
   $pdf->Ln();
   
   $ocor = listaOcorrenciasContrato($id);

   for($i = 0; $i < $ocor['numero']; $i++){
	
   $tipo = $ocor[$i]['tipo'];
   $dia = $ocor[$i]['data'];
   $hour = $ocor[$i]['hora'];
   $lugar = $ocor[$i]['espaco'];
     
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(11,$l,utf8_decode('Tipo:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(158,$l,utf8_decode($tipo));
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(22,$l,utf8_decode('Data/Perído:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(148,$l,utf8_decode($dia));
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(15,$l,utf8_decode('Horário:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(155,$l,utf8_decode($hour));
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(12,$l,utf8_decode('Local:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->MultiCell(158,$l,utf8_decode($lugar));
   
   $pdf->Ln(); 
	}
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(37,$l,utf8_decode('Nota de Empenho nº:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(65,$l,utf8_decode($notaempenho),0,1,'L');
   
   $pdf->SetX($x);
   $pdf->SetFont('Arial','B', 10);
   $pdf->Cell(22,$l,utf8_decode('Emitida em:'),0,0,'L');
   $pdf->SetFont('Arial','', 10);
   $pdf->Cell(65,$l,utf8_decode($data_entrega_empenho),0,1,'L');
   
        
   
$pdf->Output();


?>