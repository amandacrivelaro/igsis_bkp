<?php

$id_ped = $_GET['id_ped'];
$server = "http://".$_SERVER['SERVER_NAME']."/igsis/";
$http = $server."/pdf/";
$link1=$http."rlt_pagamento_integral_pf.php";
$link2=$http."rlt_pagamento_parcelado_pf.php?id=".$id_ped."";
$link3=$http."rlt_recibo_pagamento_pf.php?id=".$id_ped."";
$link4=$http."rlt_recibo_pagamento_parcelado_pf.php?id=".$id_ped."";
$data = date('Y-m-d H:i:s');

	$con = bancoMysqli();
if(isset($_POST['atualizar'])){ // atualiza o pedido
	$ped = $_GET['id_ped'];

	$sql_atualiza_pedido = "UPDATE igsis_pedido_contratacao SET
		estado = 14,
		DataPagamento = '$data'
		WHERE idPedidoContratacao = '$id_ped'";
	if(mysqli_query($con,$sql_atualiza_pedido)){
			$mensagem = "<h5>Qual documento deseja gerar?</h5>
			<div class='col-md-offset-1 col-md-10'>
			<div class='form-group'>
    		  <div class='col-md-offset-2 col-md-6'>
				<a href='$link1?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Pagamento Integral</a></div>
			  <div class='col-md-6'>
				<a href='$link3?id=$id_ped' class='btn btn-theme btn-lg btn-block' target='_blank'>Recibo de Pagamento</a><br/></div>
			</div>
			
			<div class='form-group'>
    		  <div class='col-md-offset-2 col-md-8'><br/></div>
			</div>
	
	
	";	
		}else{
			$mensagem = "Erro ao atualizar! Tente novamente.";
		}
		
	}


$ano=date('Y');
$id_ped = $_GET['id_ped'];	
$linha_tabelas = siscontrat($id_ped);
$fisico = siscontratDocs($linha_tabelas['IdProponente'],1);	
$parcelamento = retornaParcelaPagamento($id_ped);

?>


<!-- MENU -->	
<?php include 'includes/menu.php';?>
	
    
  	
	  
	 <!-- Contact -->
<section id="contact" class="home-section bg-white">
	<div class="container">
		<div class="form-group"><h2>CONTRATAÇÃO DE PESSOA FÍSICA</h2>
        <h4><?php if(isset($mensagem)){ echo $mensagem; } ?></h4></div>
		<div class="row">
	  	<div class="col-md-offset-1 col-md-10">
           
			<div class="col-md-offset-2 col-md-6"><p><strong>Código do pedido de contratação:</strong><br/><?php echo $ano."-".$id_ped; ?></p>
			</div>
			<div class="col-md-6"><p><strong>Número do Processo:</strong><br/><?php echo $linha_tabelas['NumeroProcesso'];?></p>
			</div>
			
		
			<div class="col-md-offset-2 col-md-8"><p><strong>Setor:</strong> <?php echo $linha_tabelas['Setor'];?></p>
			</div>

			<div class="col-md-offset-2 col-md-8"><p><strong>Proponente:</strong><br/><?php echo $fisico['Nome'];?></p>
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
	
	
<?php if($linha_tabelas['parcelas'] > 1){ ?> 		  
		
 	<section id="list_items">	
		<div class="container">
		<div class="col-md-offset-1 col-md-10">
            
				<div class="table-responsive list_info">
				<table class="table table-condensed">
					<thead>
						<tr class="list_menu">
							<td>Nº Parcela</td>
							<td>Valor</td>
                            <td>Data</td>
							<td></td>
							<td></td>
						</tr>
					</thead>
					<tbody>
					
<?php				
for($i = 1; $i < count($parcelamento); $i++)
 {
	echo '<tr><td class="list_description">'.$i.'</td> ';
	echo '<td class="list_description">R$ '.$parcelamento[$i]['valor'].'</td> ';
	echo '<td class="list_description">'.$parcelamento[$i]['pagamento'].'</td>';
	echo '<td class="list_description"><a target="_blank" href='.$link2.'&parcela='.$i.'>Pagamento</a></td>';
	echo '<td class="list_description"><a target="_blank" href='.$link4.'&parcela='.$i.'>Recibo</a></td></tr>';
} ?>	

					</tbody>
				</table>
				
             
            </div>	     
		</div>			 			
		</div>	
	</section>
	
<?php }else{ ?>	

<div class="col-md-offset-2 col-md-8">
<form class="form-horizontal" role="form" action="?perfil=pagamento&p=frm_cadastra_pagamento_pf&id_ped=<?php echo $id_ped; ?>" method="post">
	<div class="col-md-offset-2 col-md-8">
		<input type="submit" name="atualizar" class="btn btn-theme btn-lg btn-block" value="Confirmar">
	</div>
</form> 
 </div>
<?php } ?>	
	
</section>         