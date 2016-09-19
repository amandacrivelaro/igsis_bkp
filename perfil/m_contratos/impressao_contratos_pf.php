<?php
include 'includes/menu.php';
$conexao = bancoMysqli();
$_SESSION['idPedido'] = $_GET['id_ped'];
$id_ped = $_GET['id_ped'];
$id = $id_ped;
$server = "http://".$_SERVER['SERVER_NAME']."/igsis/"; //mudar para pasta do igsis
$http = $server."/pdf/";
$link0 = $http."rlt_pedido_contratacao_pf.php"."?id=".$id_ped;
$link1 = $http."rlt_proposta_padrao_pf.php";
$link2 = $http."rlt_proposta_artistico_pf.php";
$link3 = $http."rlt_proposta_eventoexterno_pf.php";
$link4 = $http."rlt_proposta_oficina_pf.php";
$link5 = $http."rlt_fac_pf.php";
$link6 = $http."rlt_evento_pf.php";
$link7 = $http."rlt_pedido_reserva_nova_pf.php";
$link8 = $http."rlt_pedido_reserva_fepac_pf.php";
$link9 = $http."rlt_pedido_reserva_cooperativa_pf.php";
$link10 = $http."rlt_pedido_reserva_vocacional_pf.php";
$link11 = $http."rlt_recibo_ne_pf.php";
$link12 = $http."rlt_declaracao_naoservidor_pf.php";
$link13 = $http."rlt_declaracao_iss_pf.php";
$link14 = $http."rlt_parecer_pf.php";
$link15 = $http."rlt_direitos_conexos.php";
$link16 = $http."rlt_proposta_reversaolonga_pf.php"."?id=".$id_ped."&penal=18"; 
$link17 = $http."rlt_proposta_reversaocurta_pf.php"."?id=".$id_ped."&penal=16";
$link18 = $http."rlt_proposta_doacao_pf.php"."?id=".$id_ped."&penal=6";
$link19 = $http."rlt_ordemservico_pf.php";
$link20 = $http."rlt_declaracao_exclusividade_pf.php";
$link21 = $http."rlt_proposta_cinema_pf.php"."?id=".$id_ped."&penal=3";
$link22 = $http."rlt_proposta_comissaojulgadora_pf.php"."?id=".$id_ped."&penal=5";
$link23 = $http."rlt_proposta_exposicao_pf.php"."?id=".$id_ped."&penal=10";
$link24 = $http."rlt_proposta_oficinassemedital_pf.php"."?id=".$id_ped."&penal=12";
$link25 = $http."rlt_proposta_palestra_pf.php"."?id=".$id_ped."&penal=15";
$link26 = $http."rlt_termo_doacaoobradearte_pf.php";
$link27 = $http."rlt_termo_doacaoservico_pf.php";
$link28 = $http."rlt_termo_parceria_pf.php";
$link29 = $http."rlt_proposta_formacao.php"."?id=".$id_ped."&penal=20";
$link30 = $http."rlt_proposta_pia_pf.php"."?id=".$id_ped."&penal=21";
$link31 = $http."rlt_pedido_reserva_existente_pf.php";
$link32 = $http."rlt_pedido_reserva_chefiagabinete_pf.php";
$link33 = $http."rlt_pedido_contratacao_pf_formacao.php";
$link34 = $http."rlt_pedido_reserva_transferencia_pf.php";
$link35 = $http."rlt_pedido_reserva_transferencia_virada_pf.php";
$link36 = $http."rlt_pedido_reserva_transferencia_virada_centro_pf.php";
$link37 = $http."rlt_normas_internas_teatros_pf.php"."?id=".$id_ped;
$link38 = $http."rlt_proposta_exposicao_edital_pf.php"."?id=".$id_ped."&penal=23";
$link39 = $http."rlt_proposta_mediacao_edital_pf.php"."?id=".$id_ped."&penal=25";
$link40 = $http."rlt_proposta_contadores_edital_pf.php"."?id=".$id_ped."&penal=26";
$link41 = $http."rlt_proposta_galadeballet_edital_pf.php"."?id=".$id_ped."&penal=29";
$link42 = $http."rlt_pedido_reserva_oficinaseditalcasa_pf.php";


 
 
	 $last_id = mysqli_insert_id($conexao);
	 echo "
	 
<section id='list_items' class='home-section bg-white'><h6>Qual modelo de documento deseja imprimir?</h6>
<div class='container'>
<div class='col-md-offset-2 col-md-8'>	
	<div class='table-responsive list_info'>
	
	<table class='table table-condensed'>					
	   <tbody>
		<tr class='list_menu'> 
			<td colspan='2'><strong>PEDIDO</strong></td>
		</tr>
		
		<tr>
			<td width='50%'><a href='$link0?id=$id_ped' target='_blank'><strong>Pedido de Contratação</strong></a></td>
			<td><a href='$link33?id=$id_ped' target='_blank'><strong>Pedido de Contratação - Formação</strong></a></td>
		</tr>
		
		<tr><td class='list_description'><br/></td></tr>
		
		<tr class='list_menu'><td colspan='2'><strong>PROPOSTA</strong></td></tr>
		<!--
		<tr>
			<td><a href='$link1?id=$id_ped' target='_blank'><strong>Padrão</strong></a></td>
			<td><a href='$link2?id=$id_ped' target='_blank'><strong>Artístico</strong></a></td>
		</tr>
		
		<tr>
			<td><a href='$link3?id=$id_ped' target='_blank'><strong>Evento Externo</strong></a></td>
			<td><a href='$link4?id=$id_ped' target='_blank'><strong>Oficinas</strong></a></td>
		</tr>
		-->
		
		<tr>
			<td><a href='$link21' target='_blank'><strong>Cinema</strong></a></td>
			<td><a href='$link22?id=$id_ped' target='_blank'><strong>Comissão Julgadora</strong></a></td>
		</tr>
		
		<tr>
			<td><a href='$link18?id=$id_ped' target='_blank'><strong>Doação de Serviços</strong></a></td>
			<td><a href='$link23?id=$id_ped' target='_blank'><strong>Exposição | Outros</strong></a></td>
		</tr>
		
		<tr>
			<td><a href='$link24?id=$id_ped' target='_blank'><strong>Oficinas sem Edital</strong></a></td>
			<td><a href='$link25?id=$id_ped' target='_blank'><strong>Palestra | Debate | Workshop</strong></a></td>
		</tr>
		
		<tr>
			<td><a href='$link17?id=$id_ped' target='_blank'><strong>Reversão Curta Temporada</strong></a></td>
			<td><a href='$link16?id=$id_ped' target='_blank'><strong>Reversão Longa Temporada</strong></a></td>
		</tr>
		
		<tr>
			<td><a href='$link29' target='_blank'><strong>Vocacional</strong></a></td>
			<td><a href='$link30?id=$id_ped' target='_blank'><strong>PIÁ</strong></a></td>
		</tr>
		
		<tr>
			<td><a href='$link38?id=$id_ped' target='_blank'><strong>Edital - Exposição</strong></a></td>
			<td><a href='$link39?id=$id_ped' target='_blank'><strong>Edital - Mediação</strong></a></td>
		</tr>
		
		<tr>
			<td><a href='$link40?id=$id_ped' target='_blank'><strong>Edital - Contação de Histórias</strong></a></td>
		    <td><a href='$link37' target='_blank'><strong>NORMAS INTERNAS - Teatro Distrital</strong></a></td>

		</tr>	
		
		<tr>
			<td><a href='$link41?id=$id_ped' target='_blank'><strong>Edital - Gala de Balé</strong></a></td>
		</tr>
		
		<tr><td class='list_description'><br/></td></tr>
		
		<tr class='list_menu'><td colspan='2'><strong>DECLARAÇÃO</strong></td></tr>
		
		<tr>
			<td><a href='$link12?id=$id_ped' target='_blank'><strong>Não Servidor</strong></a></td>
			<td><a href='$link13?id=$id_ped' target='_blank'><strong>ISS</strong></a></td>
		</tr>
		<tr>
			<td><a href='$link15?id=$id_ped' target='_blank'><strong>Direitos Conexos</strong></a></td>
			<td><a href='$link20?id=$id_ped' target='_blank'><strong>Exclusividade</strong></a></td>
		</tr>
		
		<tr><td class='list_description'><br/></td></tr>
		
		<tr class='list_menu'><td colspan='2'><strong>OUTROS</strong></td></tr>
		
		<tr>
			<td><a href='$link5?id=$id_ped' target='_blank'><strong>FACC</strong></a></td>
			<td><a href='$link14?id=$id_ped' target='_blank'><strong>Parecer da Comissão</strong></a></td>
		</tr>
		<tr>
			<td><a href='$link19?id=$id_ped' target='_blank'><strong>Ordem de serviço</strong></a></td>
			<td><a href='$link26?id=$id_ped' target='_blank'><strong>Termo de Doação de Obra de Arte</strong></a></td>
		</tr>
		<tr>
			<td><a href='$link27?id=$id_ped' target='_blank'><strong>Termo de Doação de Serviço</strong></a></td>
			<td><a href='$link28?id=$id_ped' target='_blank'><strong>Termo de Parceria</strong></a></td>
		</tr>
		
		<tr><td class='list_description'><br/></td></tr>
		
		<tr class='list_menu'><td colspan='2'><strong>PEDIDO DE RESERVA</strong></td></tr>
		
		<tr>
			<td><a href='$link7?id=$id_ped' target='_blank'><strong>Reserva Nova</strong></a></td>
			<td><a href='$link9?id=$id_ped' target='_blank'><strong>Cooperativa</strong></a></td>
		</tr>
		<tr>
			<td><a href='$link8?id=$id_ped' target='_blank'><strong>FEPAC</strong></a></td>
			<td><a href='$link10?id=$id_ped' target='_blank'><strong>Formação</strong></a></td>
		</tr>
		<tr>
			<td><a href='$link31?id=$id_ped' target='_blank'><strong>Reserva Existente</strong></a></td>
			<td><a href='$link32?id=$id_ped' target='_blank'><strong>Formação - Gabinete</strong></a></td>
		</tr>
		<tr>
			<td><a href='$link34?id=$id_ped' target='_blank'><strong>Reserva de Verba com Transferência</strong></a></td>
			<td><a href='$link35?id=$id_ped' target='_blank'><strong>Reserva de Verba com Transferência - VIRADA</strong></a></td>
		</tr>
		
		<tr>
			<td><a href='$link42?id=$id_ped' target='_blank'><strong>Reserva de Oficinas - Edital Casas de Cultura</strong></a></td>
		</tr>
		
		
		
		
	  </tbody>
	  </table>
		
	</div>	
</div>
</div>
</section>";
?>