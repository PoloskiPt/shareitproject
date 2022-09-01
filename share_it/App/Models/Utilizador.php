<?php

namespace App\Models;

use MF\Model\Model;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

class Utilizador extends Model {

	private $id;
	private $nome;
	private $email;
	private $password;

	public function __get($atributo){
		return $this->$atributo;
	}

	public function __set($atributo, $valor){
		$this->$atributo = $valor;
	}

	public function guardar(){
		
		$query = "insert into utilizadores(nome, email, password) values (? , ? , ?)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('nome'));
		$stmt->bindValue(2, $this->__get('email'));
		$stmt->bindValue(3, $this->__get('password')); //md5() -> hash 32 caracteres
		$stmt->execute();
		
		return $this;
	}

	public function enviarMail(){
	$auxEmail = $this->__get('email');
	$mail = new PHPMailer;
	$message = "isto é um teste de registo";
	$subject = "Registo teste";
	$mail->IsSMTP();   
	$mail->setLanguage('pt');                                   // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';                 // Specify main and backup server
	$mail->Port = 587;                                    // Set the SMTP port
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'share.it.teste@gmail.com';                // SMTP username
	$mail->Password = 'Polosky!2011';                  // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

	$mail->From = 'no-reply-share_it_teste@gmail.com';
	$mail->FromName = "Share It";
	$mail->AddAddress($this->__get('email'), $this->__get('nome'));  // Add a recipient 
	$mail->Subject = $subject;
	$mail->Body = "<h1>Ative a sua conta!</h1><br/><h1>Clique no link abaixo!</h1><a href=\"https://shareitcollegeproject.000webhostapp.com/ativar_conta?email=$auxEmail\">Ativar a conta</a>";
	$mail->IsHTML(true);


	if(!$mail->Send()) {
   		echo 'Message could not be sent.';
   		echo 'Mailer Error: ' . $mail->ErrorInfo;
   		exit;
	}
			
	}

	//validar se registo é feito corretamente
	// a ser melhorado!

	public function ativarUtilizador(){

		$query = "update utilizadores SET ativo = 1 where email = ?";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('email'));
		$stmt->execute();
		
		return $this;

	}
	
	public function alterarPassword(){
			
		$query = "update utilizadores
				SET password = ?
						WHERE id = ?;";
		
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('password'));
		$stmt->bindValue(2, $this->__get('id'));
		$stmt->execute();

		return true;
	}

	public function alterarNome(){

		$query = "update utilizadores
				SET nome = ?
						WHERE id = ?;";
		
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('nome'));
		$stmt->bindValue(2, $this->__get('id'));
		$stmt->execute();

		return true;

	}

	public function	deleteAllLikesFromUser(){

		$query = "delete from likes			
						WHERE id_utilizador = ?";
		
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->execute();

		return true;

	}

	public function deleteAllPostsFromUser(){

		$query = "delete from posts			
						WHERE id_utilizador = ?";
		
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->execute();

		return true;	

	}

	public function deleteAllCommentsFromUser(){

		$query = "delete from comentarios			
						WHERE id_utilizador = ?";
		
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->execute();

		return true;	

	}

	public function deleteAllFollowingFromUser(){

		$query = "delete from lista_seguidores			
						WHERE id_utilizador = ? OR id_utilizador_followed = ?";
		
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->bindValue(2, $this->__get('id'));
		$stmt->execute();

		return true;	

	}

	public function eliminarConta(){
		$this->deleteAllLikesFromUser();
		$this->deleteAllPostsFromUser();
		$this->deleteAllCommentsFromUser();
		$this->deleteAllFollowingFromUser();	

		$query = "delete from utilizadores				
						WHERE id = ?";
		
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		if($stmt->execute()){
				return true;
		}

		else{
			return false;
		}

	}


	public function validarRegisto(){
		$valido = true;

		if(strlen($this->__get('nome')) < 3){
			$valido = false;
		}

		if(strlen($this->__get('email')) < 3){
			$valido = false;
		}

		if(strlen($this->__get('password')) < 3){
			$valido = false;
		}

		return $valido;
	}

	// recuperar um utilizador por e-mail

	public function getUtilizadorPorEmail(){
		$query = 'select nome, email from utilizadores where email = ?';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('email'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}


	public function autenticar(){

		$query = "select id, nome, email from utilizadores where email = ? and password = ? and ativo = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('email'));
		$stmt->bindValue(2, $this->__get('password'));
		$stmt->execute();

		$utilizador = $stmt->fetch(\PDO::FETCH_ASSOC);

		if(!empty($utilizador['id']) && !empty($utilizador['nome'])){
			$this->__set('id', $utilizador['id']);
			$this->__set('nome', $utilizador['nome']);
		}
/* dá warning -> Trying to access array offset on value of type in C -> erro ocorre pois não existe nenhum match na db
		if($utilizador['id'] != '' && $utilizador['nome'] != ''){
			$this->__set('id', $utilizador['id']);
			$this->__set('nome', $utilizador['nome']);
		}
*/
		return $this;

	}

	public function getAllFollowers(){

		$query = "select 
			u.id, 
			u.nome, 
			u.email,
			(
				select 
					count(*)
				from
					lista_seguidores as ls
				where 
					ls.id_utilizador = ? and ls.id_utilizador_followed = u.id
			) as following 
		from 
			utilizadores as u
		where 
			u.nome like ? and u.id != ?";

	}

	public function getAll(){
		// like para retornar todos os que contenham aquele nome e não correspondencia exata.
		$query = "
		select 
			u.id, 
			u.nome, 
			u.email,
			(
				select 
					count(*)
				from
					lista_seguidores as ls
				where 
					ls.id_utilizador = ? and ls.id_utilizador_followed = u.id
			) as following 
		from 
			utilizadores as u
		where 
			u.nome like ? and u.id != ?
		";
		$stmt = $this->db->prepare($query);
		//utilizar wildcards antes e depois para o operador 'like' ter efeito.
		
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->bindValue(2, '%'.$this->__get('nome').'%');
		$stmt->bindValue(3, $this->__get('id'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	//recebe o id por parametro no controlador(AppController)
	public function followUtilizador($id_utilizador_follow){
		$query = "insert into lista_seguidores(id_utilizador, id_utilizador_followed)
			values(?, ?)";
		
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->bindValue(2, $id_utilizador_follow);
		$stmt->execute();

		return true;
	}

	public function unfollowUtilizador($id_utilizador_follow){
		$query = "delete from lista_seguidores where id_utilizador = ? 
		and id_utilizador_followed = ?";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->bindValue(2, $id_utilizador_follow);
		$stmt->execute();

		return true;
	}

	//INFO USER
	public function getUserInfo(){
		$query = "select nome from utilizadores where id = ?";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1,$this->__get('id'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);		
	}
	
	//TOTAL POSTS

	public function getTotalPosts(){
		$query = "select Count(*) as total_posts from posts where id_utilizador = ?";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	//TOTAL UTILIZADORES A SEGUIR

	public function getTotalSeguir(){
		$query = "select Count(*) as total_a_seguir from lista_seguidores where id_utilizador = ?";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	//TOTAL SEGUIDORES

	public function getTotalSeguidores(){
		$query = "select Count(*) as total_seguidores from `lista_seguidores` where id_utilizador_followed = ?";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}


}


?>