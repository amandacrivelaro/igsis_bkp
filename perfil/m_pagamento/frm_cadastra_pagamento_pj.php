<?php

$id_ped = $_GET['id_ped'];
$server = "http://".$_SERVER['SERVER_NAME']."/igsis/";
$http = $server."/pdf/";
$link1=$http."rlt_pagamento_integral_1rep_pj.php";
$link2=$http."rlt_pagamento_integral_2rep_pj.php";
$link3=$http."rlt_pagamento_parcelado_1rep_pj.php";
$link4=$http."rlt_pagamento_parcelado_2rep_pj.php";
$link5=$http."rlt_recibo_pagamento_1rep_pj.php";
$link6=$http."rlt_recibo_pagamento_2rep_pj.php";
$link7=$http."rlt_declaracao_simples_pj.php";
$link8=$http."rlt_recibo_pagamento_parcelado_1rep_pj.php";
$link9=$http."rlt_recibo_pagamento_parcelado_2rep_pj.php";
$data = date('Y-m-d H:i:s');



	$con = bancoMysqli();
if(isset($_POST['atualizar'])){ // atualiza o pedido
	$ped = $_GET['id_ped'];

	$sql_atualiza_pedido = "UPDATE igsis_pedido_contratacao SET
		estado = 14,
		DataPagamento = '$data'
		WHERE idPedidoContratacao = '$id_ped'";
	if(mysqli_query($con,$sql_atualiza_pedido)){
			$mensagem = "
			<div class='form-group'>
    		  <div class='col-md-offset-2 col-md-8'><hr/></div>
			</div><br/>
			<h5>Qual documento deseja gerar?</h5>
			<div class='col-md-offset-1 col-md-10'>
			<div class='form-group'>
    		  <div class='col-md-offset-2 col-md-2'><h6>01<br/>Representante</h6></div>
			  <div class='col-md-2'>
				<a href='$link1?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Integral</a><br/></div>
			  <div class='col-md-2'>
				<a href='$link3?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Parcelado</a><br/></div>
			  <div class='col-md-2'>
				<a href='$link5?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Recibo</a><br/></div>
				<div class='col-md-2'>
				<a href='$link7?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Declaração</a><br/></div>
			</div>
			
			<div class='form-group'>
    		  <div class='col-md-offset-2 col-md-8'><br/></div>
			</div>
			
			<div class='form-group'>
    		  <div class='col-md-offset-2 col-md-2'><h6>02<br/>Representantes</h6></div>
			  <div class='col-md-2'>
				<a href='$link2?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Integral</a><br/></div>
			  <div class='col-md-2'>
				<a href='$link4?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Parcelado</a><br/></div>
			  <div class='col-md-2'>
				<a href='$link6?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Recibo</a><br/></div>
				<div class='col-md-2'>
				<a href='$link7?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Declaração</a><br/></div>
			</div>
			
			
			<div class='form-group'>
    		  <div class='col-md-offset-2 col-md-8'><hr/></div>
			</div>
	
	
	";	
		}else{
			$mensagem = "Erro ao atualizar! Tente novamente.";
		}
		
	}


$ano=date('Y');
$id_ped = $_GET['id_ped'];	
$linha_tabelas = siscontrat($id_ped);
$pj = siscontratDocs($linha_tabelas['IdProponente'],2);
$parcelamento = retornaParcelaPagamento($id_ped);

?>


<!-- MENU -->	
<?php include 'includes/menu.php';?>
	
    
  	
	  
	 <!-- Contact -->
<section id="contact" class="home-section bg-white">
	<div class="container">
		<div class="form-group"><h2>CONTRATAÇÃO DE PESSOA JURÍDICA</h2>
        <h4><?php if(isset($mensagem)){ echo $mensagem; } ?></h4></div>
		<div class="row">
	  	<div class="col-md-offset-1 col-md-10">
            			
			<div class="col-md-offset-2 col-md-6"><p><strong>Código do pedido de contratação:</strong><br/><?php echo $ano."-".$id_ped; ?></p>
			</div>
			<div class="col-md-6"><p><strong>Número do Processo:</strong><br/><?php echo $linha_tabelas['NumeroProcesso'];?></p>
			</div>
			
		
			<div class="col-md-offset-2 col-md-8"><p><strong>Setor:</strong> <?php echo $linha_tabelas['Setor'];?></p>
			</div>

			<div class="col-md-offset-2 col-md-8"><p><strong>Proponente:</strong><br/><?php echo $pj['Nome'];?></p>
			</div>
				
			<div class="col-md-offset-2 col-md-8"><p><strong>Objeto:</strong><br/><?php echo $linha_tabelas['Objeto'];?></p>
			</div>

			<div class="col-md-offset-2 col-md-8"><p><strong>Local:</strong><br/><?php echo $linha_tabelas['Local'];?></p>				
			</div>
			
			<div class="col-md-offset-2 col-md-6"><p><strong>Data/Período:</strong><br/><?php echo $linha_tabelas['Periodo'];?></p>
			</div>
			<div class="col-md-6"><p><strong>Valor:</strong> R$ <?php echo dinheiroParaBr($linha_tabelas["ValorGlobal"]);?><br/>
			<strong>Duração:</strong> <?php echo $linha_tabelas['Duracao'];?>utos</p>
			</div>
			
			<div class="col-md-offset-2 col-md-6"><p><strong>Fiscal:</strong><br/><?php echo $linha_tabelas['Fiscal'];?></p>
			</div>
			<div class="col-md-6"><p><strong>Suplente:</strong><br/><?php echo $linha_tabelas['Suplente'];?></p>
			</div>
			
			<div class="col-md-offset-2 col-md-8"><p><strong>Observação:</strong> <?php echo $linha_tabelas['Observacao'];?></p>
			</div>
			
			
			</div>                
			
        </div>			
        </div>
    </div>

     

<?php if($linha_tabelas['parcelas'] > 1){ ?> 		  
		
 	<section id="list_items">	
		<div class="container">
		<div class="col-md-offset-1 col-md-20">
            
				<div class="table-responsive list_info">
				<table class="table table-condensed">
					<thead>
						<tr class="list_menu">
							<td>Nº Parcela</td>
							<td>Valor</td>
                            <td>Data</td>
							<td colspan="2">PAGAMENTO</td>
							<td colspan="2">RECIBO</td>
							<td colspan="2">DECLARAÇÃO</td>
						</tr>
					</thead>
					<tbody>
					
<?php				
for($i = 1; $i < count($parcelamento); $i++)
 {
	echo '<tr><td class="list_description">'.$i.'</td> ';
	echo '<td class="list_description">R$ '.$parcelamento[$i]['valor'].'</td> ';
	echo '<td class="list_description">'.$parcelamento[$i]['pagamento'].'</td>';
	echo '<td class="list_description"><a target="_blank" href='.$link3.'?id='.$id_ped.'&parcela='.$i.'>1 Representante</a></td>';
	echo '<td class="list_description"><a target="_blank" href='.$link4.'?id='.$id_ped.'&parcela='.$i.'>2 Representantes</a></td>';
	echo '<td class="list_description"><a target="_blank" href='.$link8.'?id='.$id_ped.'&parcela='.$i.'>1 Representante</a></td>';
	echo '<td class="list_description"><a target="_blank" href='.$link9.'?id='.$id_ped.'&parcela='.$i.'>2 Representantes</a></td>';
	echo '<td class="list_description"><a target="_blank" href='.$link7.'?id='.$id_ped.'&parcela='.$i.'>1 Representante</a></td>';
	echo '<td class="list_description"><a target="_blank" href='.$link7.'?id='.$id_ped.'&parcela='.$i.'>2 Representantes</a></td>';
} ?>	

					</tbody>
				</table>
				
             
            </div>	     
		</div>			 			
		</div>	
	</section>
	
<?php }else{ ?>	
<div class="col-md-offset-2 col-md-8">
<form class="form-horizontal" role="form" action="?perfil=pagamento&p=frm_cadastra_pagamento_pj&id_ped=<?php echo $id_ped; ?>" method="post">
				<div class="col-md-offset-2 col-md-8">
					 <input type="submit" name="atualizar" class="btn btn-theme btn-lg btn-block" value="Confirmar">
				</div>
            </form> 
 </div>
<?php } ?>   
</section>     