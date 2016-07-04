<?php

$id_ped = $_GET['id_ped'];
$server = "http://".$_SERVER['SERVER_NAME']."/igsis/";
$http = $server."/pdf/";
$link1=$http."rlt_emissao_nf.php";

$_SESSION['idPedido'] = $_GET['id_ped'];
$pedido = recuperaDados("igsis_pedido_contratacao",$_GET['id_ped'],"idPedidoContratacao");



	$con = bancoMysqli();
if(isset($_POST['atualizar'])){ // atualiza o pedido
	$ped = $_GET['id_ped'];
	$notaFiscal=$_POST['notaFiscal'];
	$descricaoNF= $_POST['descricaoNF'];

	$sql_atualiza_pedido = "UPDATE igsis_pedido_contratacao SET 
			notaFiscal = '$notaFiscal',
			descricaoNF = '$descricaoNF',			
			estado = 10
			WHERE idPedidoContratacao = '$id_ped' ";
	if(mysqli_query($con,$sql_atualiza_pedido))
	{
		$mensagem = "
			<div class='col-md-offset-2 col-md-8'>
				<a href='$link1?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Emissão de N.F.</a>
			</div>	 
			<div class='col-md-offset-2 col-md-8'>
				<br/>
			</div>
		";	
	}
	else
	{
		$mensagem = "Erro ao atualizar! Tente novamente.";
	}
		
}

	$pedido = recuperaDados("igsis_pedido_contratacao",$_GET['id_ped'],"idPedidoContratacao");
?>	


<?php include 'includes/menu.php';?>
		
	  
	 <!-- Contact -->
	  <section id="contact" class="home-section bg-white">
	  	<div class="container">
			  <div class="form-group">
					<div class="sub-title"><h2>CADASTRO DE NOTA FISCAL</h2></div>
					<div><?php if(isset($mensagem)){ echo $mensagem; } ?></div>
			  </div>

	  		<div class="row">
	  			<div class="col-md-offset-1 col-md-10">

				<form class="form-horizontal" role="form" action="?perfil=pagamento&p=frm_cadastra_emissao_nf&id_ped=<?php echo $id_ped; ?>" method="post">
				  
				<div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Código do Pedido de Contratação:</strong>
					  <input type="text" class="form-control" disabled id="Id_PedidoContratacao"  name="Id_PedidoContratacao" <?php echo " value='$id_ped' ";?>>
					</div>
				</div>
				 
                <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Nota Fiscal nº:</strong>
					  <input type="text" class="form-control" name="notaFiscal" placeholder="Número da Nota Fiscal" value="<?php echo $pedido['notaFiscal']; ?>">
					</div>
				</div>
                  
                <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Descrição:</strong>
					  <input type="text" class="form-control" name="descricaoNF" placeholder="Descrição" value="<?php echo $pedido['descricaoNF']; ?>">
					</div>
				</div>
					
				<div class="form-group">
					<div class="col-md-offset-2 col-md-8">
					 <input type="submit" name="atualizar" value="GRAVAR" class="btn btn-theme btn-lg btn-block">
					</div>
				</div>
                  
				</form>
	
	  			</div>
			
				
	  		</div>
			

	  	</div>
	  </section>  
