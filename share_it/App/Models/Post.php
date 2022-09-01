<?php


namespace App\Models;

use MF\Model\Model;

class Post extends Model {

	private $id;
	private $id_utilizador;
	private $post;
	private $data;

	public function __get($atributo){
		return $this->$atributo;
	}

	public function __set($atributo, $valor){
		 $this->$atributo = $valor;
	}

	//guardar


	public function guardar(){

		$query = "insert into posts(id_utilizador, content) values (?, ?)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id_utilizador'));
		$stmt->bindValue(2, $this->__get('post'));
		$stmt->execute();

		return $this;
	}

	public function clearListaSeguidores(){

		$query = "delete from likes where id_post = ?";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->execute();
		return true;

	}

	public function alterarPost(){

		$query = "update posts set content = ? where id = ?";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('post'));
		$stmt->bindValue(2, $this->__get('id'));
		$stmt->execute();
		return true;

	}

	public function removerPost(){

		$this->clearListaSeguidores();

		$query = "delete from posts where id = ? and id_utilizador = ?";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id'));
		$stmt->bindValue(2, $this->__get('id_utilizador'));
		$stmt->execute();
		return true;

	}

	public function listarPosts(){

		$query = "

			select 
				p.id, 
				p.id_utilizador, 
				u.nome, 
				p.content, 
				DATE_FORMAT(p.data, '%d/%m/%Y %H:%i') as data
			from 
				posts as p 
				left join utilizadores as u on (p.id_utilizador = u.id)
			where 
				p.id_utilizador = ?
			order by
				p.data desc
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id_utilizador'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	public function renderLike(){

		$query = "

			select 
				id_post, liked, id_utilizador
			from 
				likes
			where 
				id_utilizador = ?
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1 , $this->__get('id_utilizador'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function gerirLike(){
		
		$total = $this->existingLike()['total'];
		$status = $this->statusLike()['liked'];
			
		if($total == 0){
			$this->like();
		}

		else if($status == 1){
				$this->atribuiLikeDislike(0);
		}
		else if($status == 0){
				$this->atribuiLikeDislike(1);
			}
		
		
		}
	
	public function atribuiLikeDislike($status){

		$query = "update likes SET liked = ? where id_utilizador = ? and id_post = ?";
			$stmt =$this->db->prepare($query);
			$stmt->bindValue(1, $status);
			$stmt->bindValue(2, $this->__get('id_utilizador'));
			$stmt->bindValue(3, $this->__get('id'));		
			$stmt->execute();

			return $this;

	}

	public function like(){

		$query = "insert into likes(id_utilizador, id_post, liked) values (? , ? , ?)";
			$stmt =$this->db->prepare($query);
			$stmt->bindValue(1, $this->__get('id_utilizador'));
			$stmt->bindValue(2, $this->__get('id'));
			$stmt->bindValue(3, 1);
			$stmt->execute();

			return $this;

	}

	//verifica se o like ja existe na db
	public function existingLike(){
		
		$query = "select count(*) as total
					from likes
					where id_utilizador = ? and id_post = ?		
		";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id_utilizador'));
		$stmt->bindValue(2, $this->__get('id'));
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}

	public function statusLike(){
		
		$query = "select liked
					from likes
					where id_utilizador = ? and id_post = ?		
		";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1, $this->__get('id_utilizador'));
		$stmt->bindValue(2, $this->__get('id'));
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}

	//recuperar
	public function getAll(){

		$query = "

			select 
				p.id, 
				p.id_utilizador, 
				u.nome, 
				p.content, 
				DATE_FORMAT(p.data, '%d/%m/%Y %H:%i') as data
			from 
				posts as p 
				left join utilizadores as u on (p.id_utilizador = u.id)
			where 
				p.id_utilizador = ?
				or p.id_utilizador in(select id_utilizador_followed from lista_seguidores where id_utilizador = ?)
			order by
				p.data desc
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1 , $this->__get('id_utilizador'));
		$stmt->bindValue(2 , $this->__get('id_utilizador'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	//get likes

	public function getLikes(){

		$query = "select id_post, count(*) as likes from likes where liked = 1 GROUP BY id_post;";


			
		

		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	//recuperar com paginação
	public function getPorPagina($limit, $offset){

		$query = "

			select   
				p.id, 
				p.id_utilizador, 
				u.nome, 
				p.content, 
				DATE_FORMAT(p.data, '%d/%m/%Y %H:%i') as data
			from 
				posts as p 
				left join utilizadores as u on (p.id_utilizador = u.id)		
			where 
				p.id_utilizador = ?
				or p.id_utilizador in(select id_utilizador_followed from lista_seguidores where id_utilizador = ?)
			order by
				p.data desc
			limit
				$limit
			offset
				$offset
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1 , $this->__get('id_utilizador'));
		$stmt->bindValue(2 , $this->__get('id_utilizador'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	//recuperar total de posts
	public function getTotalRegistos(){

		$query = "

			select 
				count(*) as total
			from 
				posts as p 
				left join utilizadores as u on (p.id_utilizador = u.id)
			where 
				p.id_utilizador = ?
				or p.id_utilizador in(select id_utilizador_followed from lista_seguidores where id_utilizador = ?)
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(1 , $this->__get('id_utilizador'));
		$stmt->bindValue(2 , $this->__get('id_utilizador'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
}

?>