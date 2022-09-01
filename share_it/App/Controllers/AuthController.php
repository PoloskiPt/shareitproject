<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {


	public function autenticar(){
	
		$utilizador = Container::getModel('Utilizador');

		$utilizador->__set('email', $_POST['email']);
		$utilizador->__set('password', md5($_POST['password']));

		$utilizador->autenticar();

		if($utilizador->__get('id') != '' && $utilizador->__get('nome')){
			
			session_start();

			$_SESSION['id'] = $utilizador->__get('id');
			$_SESSION['nome'] = $utilizador->__get('nome');

			header('Location: /timeline');

		}else{
			
			header('Location: /?login=erro');
		}
		
	}

	public function sair(){

		session_start();
		session_destroy();
		header('Location: /');

	}

	public function validaAuth(){

		session_start();

		if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == ''){
			header('Location: /?login=erro');
		}
				
	}

	public function eliminarConta(){
			$this->validaAuth();
			$this->view->eliminar = true;
			$utilizador = Container::getModel('Utilizador');
			$utilizador->__set('id', $_SESSION['id']);
			try{
				if($utilizador->eliminarConta()){
				$this->sair();
				$this->view->eliminar = true;
			}else{
				$this->view->eliminar = false;
				$this->render('settings');
			}
			}catch(Exception $e){
				$this->view->eliminar = false;
				$this->render('settings');
			}
					
			
	}

	public function alterarDados(){
		
		$this->validaAuth();	
		
		$this->view->valido = true;	

		$utilizador = Container::getModel('Utilizador');
		$utilizador->__set('id', $_SESSION['id']);
		
		
		if(isset($_POST['nome'])){		
			if(strlen($_POST['nome']) >= 3){
				$utilizador->__set('nome', $_POST['nome']);
			    $utilizador->alterarNome();
			}						
		}

		if(isset($_POST['password'])){
			
			if(strlen($_POST['password']) >= 3){
					$utilizador->__set('password', md5($_POST['password']));
					$utilizador->alterarPassword();
					$this->sair();
			}	
			
		}

		header('Location: /timeline');

		

		}	
		

	}




?>