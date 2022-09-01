<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {
	 		$this->verificaAuth();
			// se não existir reencaminha para a pagina inicial
			
			$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
			$this->render('index');
			

	}

	public function verificaAuth(){

		session_start();
			// se já existir uma sessão reencaminha para a timeline
			if(!empty($_SESSION['id'])){
				header('Location: /timeline');
			}

	}

	public function inscreverse() {
		
		$this->verificaAuth();

		$this->view->utilizador = array(
				'nome' => '',
				'email' => '',
				'password' => '',
			);

		$this->view->erroRegisto = false;

		$this->render('inscreverse');

			}
	
	public function ativarConta(){

		$utilizador = Container::getModel('Utilizador');
		$utilizador->__set('email', $_GET['email']);

		$utilizador->ativarUtilizador();
	    header('Location: /');

	}		


	public function registar(){

		$this->verificaAuth();

		//receber os dados do formulário e settar no objeto
		$utilizador = Container::getModel('Utilizador');

		$utilizador->__set('nome', $_POST['nome']);
		$utilizador->__set('email', $_POST['email']);
		$utilizador->__set('password', md5($_POST['password']));

		if($utilizador->validarRegisto() && count($utilizador->getUtilizadorPorEmail()) == 0){
				
				$utilizador->guardar();	
				$utilizador->enviarMail();
				$this->render('registo');		
				
		} else {

			$this->view->utilizador = array(
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'password' => $_POST['password'],
			);

			$this->view->erroRegisto = true;
			
			$this->render('inscreverse');

		}
		

	}

}


?>